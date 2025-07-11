<?php
	session_start();
	include("handlers/_generics.php");
	
	$con = new _init();
	$bid = $_SESSION['branchid'];
	$uid = $_SESSION['userid'];

	if($_SESSION['authkey']) {
		$con->updateTstamp($_SESSION['authkey']);
	}

	function updateAmount($docno,$cid,$scid) {
		global $con;
		
		list($xgross,$discount) = $con->getArray("select ROUND(sum(unit_price*qty),2), round(sum(qty*discount),2) as discount from or_details where doc_no = '$docno';");
		list($adue) = $con->getArray("select ROUND(SUM(amount_due),2) AS adue FROM or_details WHERE doc_no = '$docno';");
		list($taxcode) = $con->getArray("select tax_code from or_header where doc_no = '$docno';");
		list($midno) = $con->getArray("select mid_no from or_header where doc_no = '$docno';");



		$netdiscount = $xgross - $discount;
		
		if($scid != '') {
			 $scpwd = ROUND($netdiscount * 0.20,2); 
			 $adue = $xgross - $discount - $scpwd; 
		} else { 
			$scpwd = 0; 
			$adue = $xgross - $discount; 
		}


		if($taxcode != '') { 
			$ewt = ROUND($adue * 0.02,2);
			$adue = $adue - $ewt; 
		}

		if($midno != '') {
			$discount = ROUND($xgross * 0.10,2); 
			$adue = $xgross - $discount; 

		}


		$con->dbquery("update ignore or_header set gross = '$xgross', discount = '$discount', sc_discount = '$scpwd', ewt = '$ewt',  amount_due = '$adue', balance = '$adue' where doc_no = '$docno';");
	
	}


	switch($_REQUEST['mod']) {
		
		case "retrieveSO":

			if($_POST['cid'] != '') { $cid = " and customer_code = '$_POST[cid]' "; } else { $cid = ''; }
			$docno = $_POST['docno'];

			$soString = "select *,lpad(so_no,6,0) as sono, lpad(patient_id,6,0) as pid, if(customer_code!=0,lpad(customer_code,6,'0'),'') as cid from so_header where status = 'Finalized' and so_no = '$_POST[so]' and branch = '$bid' $cid;";
			if($con->countRows($soString) > 0) {
				$so = $con->getArray($soString);
				if($so['billed'] == 'Y' or $so['paid'] == 'Y') {
					echo json_encode(array("respond"=>"used"));
				} else {

					$s = $con->getArray("select distinct a.doc_no,lpad(a.doc_no,6,'0') as docno, date_format(doc_date,'%m/%d/%Y') as docdate from or_header a left join or_details b on a.doc_no = b.doc_no and a.branch = b.branch where b.so_no = '$_POST[so]' and b.branch = '$bid' and `status` = 'Active';");
					if($s[0] != '') {
						echo json_encode(array("respond"=>"currentActive","doc_no"=>$s['doc_no'],"docno"=>$s['docno'],"date"=>$s['docdate']));
					} else {
					
						/* Automatically Insert Data from SO Header to OR Header */
						if($docno == '') {
							list($orno) = $con->getArray("select ifnull(max(or_no),0)+1 from or_header where branch = '$bid';"); 
							list($docno) = $con->getArray("select ifnull(max(doc_no),0)+1 from or_header where branch = '$bid';");
							$con->dbquery("INSERT IGNORE INTO or_header (doc_no,branch,doc_date,or_no,customer_code,customer_name,customer_address,scpwd_id,amount_due,amount_paid,balance,trace_no,created_by,created_on) VALUES ('$docno','$bid','".date('Y-m-d')."','$orno','$so[customer_code]','$so[customer_name]','$so[customer_address]','$so[scpwd_id]','$so[amount]','0','$so[amount]','$_POST[trace_no]','$uid',now());");
						}

						/* INSERT CONTENT OF SO DETAILS TO OR DETAILS */
						$sdQuery = $con->dbquery("select a.*,b.patient_id,b.patient_name,b.patient_address,b.so_date from so_details a left join so_header b on a.so_no = b.so_no and a.branch = b.branch where  a.so_no = '$_POST[so]' and a.branch = '$bid';");
						while($sdRow = $sdQuery->fetch_array()) {
							$con->dbquery("INSERT IGNORE INTO or_details (doc_no,branch,so_no,so_date,pid,pname,paddr,`code`,`description`,unit,unit_price,is_special,qty,amount,discount,amount_due,trace_no) VALUES ('$docno','$bid','$sdRow[so_no]','$sdRow[so_date]','$sdRow[patient_id]','".$con->escapeString($sdRow['patient_name'])."','$sdRow[patient_address]','$sdRow[code]','$sdRow[description]','$sdRow[unit]','$sdRow[unit_price]','$sdRow[is_special]','$sdRow[qty]','$sdRow[amount]','$sdRow[discount]','$sdRow[amount_due]','$_POST[trace_no]');");
						}
						
						$or = array("respond"=>"ok","or_no"=>str_pad($orno,6,0,STR_PAD_LEFT),"doc_no"=>str_pad($docno,6,0,STR_PAD_LEFT));
						$result = array_merge($so,$or);
						echo json_encode($result);

						updateAmount($docno,$so['customer_code'],$so['scpwd_id']);
					}
				}
			} else {
				echo json_encode(array("respond"=>"notFound"));
			}

		break;

		case "retrieveSOA":

			$soa = $con->getArray("select customer_code, lpad(customer_code,6,'0') as cid, customer_name as cname, customer_address as caddr, amount, b.vatable from soa_header a left join contact_info b on a.customer_code = b.file_id where soa_no = '$_POST[soa]' and branch = '$bid';");
			
			if($_POST['docno'] == '') {
				list($orno) = $con->getArray("select ifnull(max(or_no),0)+1 from or_header where branch = '$bid';"); 
				list($docno) = $con->getArray("select ifnull(max(doc_no),0)+1 from or_header where branch = '$bid';");
				$con->dbquery("INSERT IGNORE INTO or_header (doc_no,branch,doc_date,or_no,customer_code,customer_name,customer_address,amount_due,amount_paid,balance,trace_no,created_by,created_on) VALUES ('$docno','$bid','".date('Y-m-d')."','$orno','$soa[customer_code]','$soa[cname]','$soa[caddr]','$soa[amount]','0','$soa[amount]','$_POST[trace_no]','$uid',now());");
			} else {
				$orno = $_POST['or_no'];
				$docno = $_POST['docno'];
				$con->dbquery("UPDATE IGNORE or_header SET customer_code = '$soa[customer_code]', customer_name='$soa[cname]',customer_address='$soa[caddr]',amount_due='$soa[amount]',amount_paid=0,balance='$soa[amount]',updated_by='$uid',updated_on = now() where doc_no = '$docno' and branch = '$bid';");
			}
			
			/* INSERT CONTENT OF SO DETAILS TO OR DETAILS */
			$sdQuery = $con->dbquery("SELECT *, unit_price as uprice, amount as amt FROM soa_details WHERE soa_no = '$_POST[soa]' AND branch = '$bid';");
			while($sdRow = $sdQuery->fetch_array()) {
				$con->dbquery("INSERT INTO or_details (doc_no,branch,so_no,so_date,soa_no,pid,pname,paddr,`code`,`description`,unit,unit_price,is_special,qty,amount,amount_due,trace_no) VALUES ('$docno','$bid','$sdRow[so_no]','$sdRow[so_date]','$_POST[soa]','$sdRow[pid]','$sdRow[pname]','$sdRow[paddr]','$sdRow[code]','$sdRow[description]','$sdRow[unit]','$sdRow[uprice]','N','$sdRow[qty]','".ROUND($sdRow['uprice'] * $sdRow['qty'],2)."','$sdRow[amt]','$_POST[trace_no]');");
			}

			$or = array("respond"=>"ok","or_no"=>str_pad($orno,6,0,STR_PAD_LEFT),"doc_no"=>str_pad($docno,6,0,STR_PAD_LEFT));
			$result = array_merge($soa,$or);
			echo json_encode($result);

			updateAmount($docno,$soa['customer_code'],'');
		break;

		case "browseSO":
			list($cutoff) = $con->getArray("SELECT DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 2 DAY),'%Y-%m-%d')");

            $q = "SELECT so_no, LPAD(so_no,6,0) AS sono, DATE_FORMAT(so_date,'%m/%d/%Y') AS sdate, a.patient_name, a.customer_code, if(a.customer_code=0,'Charge to Patient',customer_name) as chargeto, amount, a.remarks FROM so_header a WHERE branch = '$bid' AND billed != 'Y' AND so_no NOT IN (SELECT so_no FROM or_details WHERE doc_no = '$_POST[doc_no]' AND branch = '$bid') AND `status` = 'Finalized' AND cstatus IN ('1') and so_date >= '$cutoff' ORDER BY so_date DESC, soa_no DESC;";
            
            if($con->countRows($q) > 0) {
                echo "
                    <form name=\"frmFetchedSO\" id=\"frmFetchedSO\">
                    <table width=100% cellspacing=0 cellpadding=0>

                        <tr>
                            <td class=gridHead width=10%>SO #</td>
                            <td class=gridHead width=10%>DATE</td>
                            <td class=gridHead width=15%>PATIENT</td>
							<td class=gridHead width=20%>CHARGE TO</td>
                            <td class=gridHead >REMARKS</td>
                            <td class=gridHead width=15% align=right>AMOUNT</td>
                            <td class=gridHead width=20>&nbsp;</td>
                        </tr>";

                $soQuery = $con->dbquery($q); $i=0;
                while($soRow = $soQuery->fetch_array()) {

                    echo "<tr bgcolor='".$con->initBackground($i)."'>
                            <td class=grid>".$soRow['sono']."</td>
                            <td class=grid>".$soRow['sdate']."</td>
                            <td class=grid>".$soRow['patient_name']."</td>
							<td class=grid>".$soRow['chargeto']."</td>
                            <td class=grid>".$soRow['remarks']."</td>
                            <td class=grid align=right>".number_format($soRow['amount'],2)."</td>
                            <td class=grid align=center><input type=radio name=\"so\" id=\"so\" value='".$soRow['so_no']."'></td>
                        </tr>
                    
                    ";
                    $i++;

                }
                 echo "</table>  
                </form>";


            }

        break;

		case "browseSOA":

			if($_POST['cid'] != '') { $fs = " and a.customer_code = '$_POST[cid]' "; }
            $q = "SELECT soa_no, LPAD(soa_no,6,0) AS soano, DATE_FORMAT(soa_date,'%m/%d/%Y') AS sdate, a.customer_code, if(a.customer_code=0,'Charge to Patient',customer_name) as chargeto, amount, a.remarks FROM soa_header a WHERE branch = '$bid' AND balance > 0 AND soa_no NOT IN (SELECT soa_no FROM or_details WHERE doc_no = '$_POST[doc_no]' AND branch = '$bid' and soa_no !='') AND `status` = 'Finalized' $fs ORDER BY soa_date DESC, soa_no DESC;";
            
            if($con->countRows($q) > 0) {
                echo "
                    <form name=\"frmFetchedSO\" id=\"frmFetchedSO\">
                    <table width=100% cellspacing=0 cellpadding=0>

                        <tr>
                            <td class=gridHead width=10%>SOA #</td>
                            <td class=gridHead width=10%>DATE</td>
							<td class=gridHead width=30%>CHARGED TO</td>
                            <td class=gridHead >REMARKS</td>
                            <td class=gridHead width=15% align=right>AMOUNT</td>
                            <td class=gridHead width=20>&nbsp;</td>
                        </tr>";

                $soQuery = $con->dbquery($q); $i=0;
                while($soRow = $soQuery->fetch_array()) {

                    echo "<tr bgcolor='".$con->initBackground($i)."'>
                            <td class=grid>".$soRow['soano']."</td>
                            <td class=grid>".$soRow['sdate']."</td>
							<td class=grid>".$soRow['chargeto']."</td>
                            <td class=grid>".$soRow['remarks']."</td>
                            <td class=grid align=right>".number_format($soRow['amount'],2)."</td>
                            <td class=grid align=center><input type=radio name=\"soa\" id=\"soa\" value='".$soRow['soa_no']."'></td>
                        </tr>
                    
                    ";
                    $i++;

                }
                 echo "</table>  
                </form>";


            }

        break;

		case "saveHeader":
			if($_POST['doc_no'] != '') {
				$con->dbquery("UPDATE IGNORE or_header SET doc_date = '".$con->formatDate($_POST['docdate'])."', or_no='$_POST[or_no]', customer_code='$_POST[cid]', customer_name='".$con->escapeString(htmlentities($_POST['cname']))."', customer_address='".$con->escapeString(htmlentities($_POST['caddress']))."', scpwd_id='$_POST[scid]', mid_no = '$_POST[mid_no]', remarks = '".$con->escapeString(htmlentities($_POST['remarks']))."', cashtype='$_POST[cashtype]', cardtype='$_POST[cc_type]', cardprovider='$_POST[cc_bank]', cardname='$_POST[cc_name]',cardno='$_POST[cc_no]',cardexpiry='$_POST[cc_expiry]',cardapproval='$_POST[cc_approvalno]',checkbank='".$con->escapeString(htmlentities($_POST['ck_bank']))."',checkno='$_POST[ck_no]',checkdate='".$con->formatDate($_POST['ck_date'])."', updated_by='$uid', updated_on=now() WHERE doc_no = '$_POST[doc_no]' and branch = '$bid';");
			} else {
				list($orno) = $con->getArray("select ifnull(max(or_no),0)+1 from or_header where branch = '$bid';"); 
				list($docno) = $con->getArray("select ifnull(max(doc_no),0)+1 from or_header where branch = '$bid';");
				$con->dbquery("INSERT IGNORE INTO or_header (trace_no,doc_no,branch,doc_date,or_no,customer_code,customer_name,customer_address,terms,scpwd_id,mid_no,remarks,cashtype,cardtype,cardprovider,cardname,cardno,cardexpiry,cardapproval,created_by,created_on) values ('$_POST[trace_no]','$docno','$bid','".$con->formatDate($_POST['docdate'])."','$orno','$_POST[cid]','".$con->escapeString(htmlentities($_POST['cname']))."','".$con->escapeString(htmlentities($_POST['caddress']))."','$_POST[terms]','$_POST[scid]','$_POST[mid_no]','".$con->escapeString(htmlentities($_POST['remarks']))."','$_POST[cashtype]','$_POST[cc_type]','$_POST[cc_bank]','$_POST[cc_name]','$_POST[cc_no]','$_POST[cc_expiry]','$_POST[cc_approvalno]','$uid',now());");
				echo json_encode(array("docno"=>str_pad($docno,6,0,STR_PAD_LEFT),"orno"=>str_pad($orno,6,0,STR_PAD_LEFT)));
			}

		break;

		case "addItem":

            $sprice = $con->formatDigit($_POST['sprice']);
            $qty = $con->formatDigit($_POST['qty']);
            $amt = $con->formatDigit($_POST['amount']);
            
            if($sprice != $uprice) { $price = $sprice; } else { $price = $uprice; }
            $con->dbquery("INSERT INTO or_details (doc_no,branch,`code`,`description`,unit,unit_price,is_special,qty,amount,discount,amount_due,trace_no) VALUES ('$_POST[doc_no]','$bid','$_POST[item]','".$con->escapeString(htmlentities($_POST['description']))."','$_POST[unit]','$sprice','$_POST[ispecial]','$qty','$amt','0','$amt','$_POST[trace_no]');");
            updateAmount($_POST['doc_no'],'',$_POST['scid']);
        break;

		case "checkLabSamples":
			list($isE) = $con->getArray("SELECT COUNT(*) FROM lab_samples WHERE so_no = '$_POST[so_no]' AND parent_code = '$_POST[code]' AND extracted = 'Y' and branch = '$bid';");
			if($isE > 0) { echo "notOk"; }
		break;

        case "deleteLine":
            $con->deleteRow($table="or_details",$arg = "line_id = '$_POST[lid]'");
			$con->deleteRow($table="lab_samples",$arg = "so_no = '$_POST[so_no]' and parent_code = '$_POST[code]'");
			
			updateAmount($_POST['doc_no'],'',$_POST['scid']);
        break;

		case "applyDiscount":
			$d = $con->getArray("select * from or_details where line_id = '$_POST[lid]';");
			$adue = ROUND($d['qty'] * ($d['unit_price'] - $con->formatDigit($_POST['discount'])),2);
			$con->dbquery("update ignore or_details set discount = '".$con->formatDigit($_POST['discount'])."', amount_due = '$adue' where line_id = '$_POST[lid]';");
			updateAmount($_POST['doc_no'],'',$_POST['scid']);
		break;

		case "updateEWT":
			$con->dbquery("update ignore or_header set tax_code = '$_POST[ecode]' where doc_no = '$_POST[doc_no]' and branch = '$bid';");
			updateAmount($_POST['doc_no'],'',$_POST['scid']);
		break;

		case "updatePayment":
			$con->dbquery("update or_header set balance = 0, amount_paid = ".$con->formatDigit($_POST['paid']).", cash_tendered = '".$con->formatDigit($_POST['tendered'])."', change_due = '".$con->formatDigit($_POST['changeDue'])."', updated_by = '$uid', updated_on = now() where doc_no = '$_POST[doc_no]' and branch = '$bid';");
		break;

		case "retrieve":
            $data = array();
			$srrd = $con->dbquery("SELECT line_id AS id, LPAD(so_no,6,'0') AS sono, DATE_FORMAT(so_date,'%m/%d/%y') AS sodate, IF(soa_no!='',LPAD(soa_no,6,'0'),'') AS soano, pname, IF(qty > 1,CONCAT(description,' (x',qty,')'),description) AS `procedure`, amount as unit_price, discount, amount_due, `code` FROM or_details WHERE trace_no = '$_REQUEST[trace_no]';");
			while($row = $srrd->fetch_array()) {
				$data[] = array_map('utf8_encode',$row);
			}
			$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
			echo json_encode($results);	
        break;

		case "getTotals":
			echo json_encode($con->getArray("SELECT FORMAT(discount,2) as discount,  FORMAT(gross,2) as gross, FORMAT((gross-discount),2) as subtotal, FORMAT(ewt,2) as ewt, FORMAT(sc_discount,2) AS sc, FORMAT(amount_due,2) AS adue, FORMAT(amount_paid,2) AS paid, FORMAT(balance,2) AS balance FROM or_header WHERE doc_no = '$_POST[doc_no]' and branch = '$bid';"));
		break;

		case "finalize":
			list($isVat) = $con->getArray("select vatable from contact_info where file_id = '$_POST[cid]';");
			$con->dbquery("update ignore or_header set `status` = 'Finalized', updated_by = '$uid', updated_on = now() where doc_no = '$_POST[doc_no]' and branch = '$bid';");
			

			
			/* Charge to Patient */
			$dQuery = $con->dbquery("select distinct so_no from or_details where doc_no = '$_POST[doc_no]' and branch = '$bid' AND (soa_no IS NULL OR soa_no = '');");
			while($dRow = $dQuery->fetch_array()) {
				$con->dbquery("update ignore so_header set cstatus = '2', paid = 'Y' where so_no = '$dRow[so_no]' and branch = '$bid';");
			
				/* Send Lab Request Pending Extraction */
				$so = $con->dbquery("SELECT a.so_no AS so, b.code as parent_code, b.code, b.description AS `procedure`, a.physician, d.sample_type, d.container_type FROM so_header a LEFT JOIN so_details b ON a.trace_no = b.trace_no LEFT JOIN patient_info c ON a.patient_id = c.patient_id LEFT JOIN services_master d ON b.code = d.code WHERE a.status = 'Finalized' AND a.cstatus IN (2,4,12) AND d.with_subtests = 'N' AND d.category IN ('1','2') AND a.so_no = '$dRow[so_no]' UNION SELECT a.so_no AS so, e.parent as parent_code, e.code, e.description AS `procedure`, a.physician, f.sample_type, f.container_type FROM so_header a LEFT JOIN so_details b ON a.trace_no = b.trace_no LEFT JOIN patient_info c ON a.patient_id = c.patient_id LEFT JOIN services_master d ON b.code = d.code LEFT JOIN services_subtests e ON b.code = e.parent LEFT JOIN services_master f ON e.code = f.code WHERE a.status = 'Finalized' AND a.cstatus IN (2,4,12) AND d.with_subtests = 'Y' AND f.category IN ('1','2') AND a.so_no = '$dRow[so_no]';");
				while($eRow = $so->fetch_array()) {
					list($labCount) = $con->getArray("select count(*) from lab_samples where so_no = '$eRow[so]' and parent_code = '$eRow[parent_code]' and code = '$eRow[code]';");
					if($labCount == 0) {
						$con->dbquery("INSERT IGNORE INTO lab_samples (branch,so_no,parent_code,code,`procedure`,sampletype,samplecontainer,physician,created_by,created_on) values ('$bid','$eRow[so]','$eRow[parent_code]','$eRow[code]','$eRow[procedure]','$eRow[sample_type]','$eRow[container_type]','$eRow[physician]','$uid',now());");
					}
				}

				/* Send Request to Nursing Station for PEME */
				$gQuery = $con->dbquery("SELECT a.priority_no as prio, a.so_no, a.so_date, b.code as parentcode, b.code, b.description AS `procedure`, a.patient_id AS pid, c.birthplace, c.occupation AS occu, c. employer AS compname, c.mobile_no AS contactno FROM so_header a LEFT JOIN so_details b ON a.trace_no = b.trace_no LEFT JOIN patient_info c ON a.patient_id = c.patient_id WHERE a.so_no = '$dRow[so_no]' AND b.code IN ('M001') AND a.status IN (2,4,12) UNION ALL SELECT a.priority_no as prio, a.so_no, a.so_date, b.code as parentcode, e.code, e.description AS `procedure`, a.patient_id AS pid, c.birthplace, c.occupation AS occu, c. employer AS compname, c.mobile_no AS contactno FROM so_header a LEFT JOIN so_details b ON a.trace_no = b.trace_no LEFT JOIN patient_info c ON a.patient_id = c.patient_id LEFT JOIN services_master d ON b.code = d.code LEFT JOIN services_subtests e ON e.parent = d.code  WHERE a.so_no = '$dRow[so_no]' AND e.code IN ('M001') AND a.status IN (2,4,12) AND  d.with_subtests = 'Y';");
				while($hRow = $gQuery->fetch_array()) {
					$con->dbquery("INSERT IGNORE INTO peme (so_no,branch,so_date,prio,parentcode,code,`procedure`,pid,pob,occu,compname,contactno) values ('$hRow[so_no]','$bid','$hRow[so_date]','$hRow[prio]','$hRow[parentcode]','$hRow[code]','$hRow[procedure]','$hRow[pid]','" . $con->escapeString(htmlentities($hRow['birthplace'])) . "','$hRow[occu]','" . $con->escapeString(htmlentities($hRow['compname'])) . "','$hRow[contactno]');");
				}
			}

			/* On-Account */
			$sQuery = $con->dbquery("select sum(amount_due) as paid, soa_no from or_details where doc_no = '$_POST[doc_no]' and branch = '$bid' and soa_no > 0 group by soa_no;");
			while($sRow = $sQuery->fetch_array()) {
				$con->dbquery("update ignore soa_header set balance = balance - 0$sRow[paid], amount_paid = amount_paid + 0$sRow[paid] where soa_no = '$sRow[soa_no]' and branch = '$bid';");
			}

		break;

		case "reopen":
			list($isVat) = $con->getArray("select vatable from contact_info where file_id = '$_POST[cid]';");
			$con->dbquery("update or_header set `status` = 'Active', updated_by = '$uid', updated_on = now() where doc_no = '$_POST[doc_no]' and branch = '$bid';");
		
			/* Charge to Patient */
			$dQuery = $con->dbquery("select distinct so_no from or_details where doc_no = '$_POST[doc_no]' and branch = '$bid' and (soa_no is null or soa_no = '');");
			while($dRow = $dQuery->fetch_array()) {
				$con->dbquery("update so_header set cstatus = '1', paid = 'N' where so_no = '$dRow[so_no]' and branch = '$bid';");
			}

			/* On-Account */
			$sQuery = $con->dbquery("select sum(if('$isVat'='Y',ROUND(amount_due * 1.12,2),amount_due)) as paid, soa_no from or_details where doc_no = '$_POST[doc_no]' and branch = '$bid' and soa_no > 0 group by soa_no;");
			while($sRow = $sQuery->fetch_array()) {
				$con->dbquery("update ignore soa_header set balance = balance + 0$sRow[paid], amount_paid = amount_paid - 0$sRow[paid] where soa_no = '$sRow[soa_no]' and branch = '$bid';");
			}

			if($terms == 0) {
				$con->dbquery("update so_header set cstatus = '1' where so_no = '$sono' and branch = '$bid';");
			} else { $con->dbquery("update ignore so_header set cstatus = '10' where so_no = '$sono' and branch = '$bid';"); }
		break;

		case "cancel":
			$con->dbquery("update or_header set `status` = 'Cancelled', updated_by = '$uid', updated_on = now() where doc_no = '$_POST[doc_no]' and branch = '$bid';");
			list($sono,$terms) = $con->getArray("select so_no, terms from or_header where doc_no = '$_POST[doc_no]' and branch = '$bid';");
			$con->dbquery("update ignore so_header set cstatus = '11' where so_no = '$sono' and branch = '$bid';");
		break;

		case "reuse":
			$con->dbquery("update or_header set `status` = 'Active', updated_by = '$uid', updated_on = now() where doc_no = '$_POST[doc_no]' and branch = '$bid';");
			list($sono,$terms) = $con->getArray("select so_no, terms from or_header where doc_no = '$_POST[doc_no]' and branch = '$bid';");
			$con->dbquery("update ignore so_header set cstatus = '1' where so_no = '$sono' and branch = '$bid';");
		break;

	}

?>