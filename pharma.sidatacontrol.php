<?php
    session_start();
	require_once 'handlers/_generics.php';
	$con = new _init;
    $bid = $_SESSION['branchid'];

    if($_SESSION['authkey']) {
		$con->updateTstamp($_SESSION['authkey']);
	}

	function updateAmount($doc_no) {
		global $con;
		list($gross,$discount,$net) = $con->getArray("select ifnull(sum(qty*unit_price),0), ifnull(sum(discount),0), ifnull(sum(amount_due),0) from pharma_si_details where doc_no = '$doc_no' and branch = '$_SESSION[branchid]';");
		$con->dbquery("update ignore pharma_si_header set gross = '$gross', discount = '$discount', net = '$net', amount_due = '$net', balance = '$net', amount_paid = '0' where doc_no = '$doc_no' and branch = '$_SESSION[branchid]';");
	}

    switch($_REQUEST['mod']) {
        case "saveHeader":

            list($isCount) = $con->getArray("select count(*) from pharma_si_header where doc_no = '$_POST[doc_no]';");
            if($isCount > 0) {
                $queryString = "UPDATE IGNORE pharma_si_header set doc_date = '".$con->formatDate($_POST['doc_date'])."', si_no = '$_POST[si_no]', customer_code = '$_POST[cid]',customer_name = '".$con->escapeString(htmlentities($_POST['cname']))."',customer_address = '".$con->escapeString(htmlentities($_POST['caddr']))."',terms = '$_POST[terms]', scpwd_id = '$_POST[sc_id]', physician = '".$con->escapeString(htmlentities($_POST['physician']))."', remarks = '".$con->escapeString(htmlentities($_POST['remarks']))."', updated_by = '$_SESSION[userid]' , updated_on = now() where doc_no = '$_POST[doc_no]';";
            } else {
                $queryString = "INSERT IGNORE INTO pharma_si_header (doc_no,branch,doc_date,si_no,customer_code,customer_name,customer_address,terms,scpwd_id,physician,remarks,trace_no,created_by,created_on) VALUES ('$_POST[doc_no]','$bid','".$con->formatDate($_POST['doc_date'])."','$_POST[si_no]','$_POST[cid]','".$con->escapeString(htmlentities($_POST['cname']))."','".$con->escapeString(htmlentities($_POST['caddr']))."','$_POST[terms]','$_POST[sc_id]','".$con->escapeString(htmlentities($_POST['physician']))."','".$con->escapeString(htmlentities($_POST['remarks']))."','$_POST[trace_no]','$_SESSION[userid]',now());";
            }

            $con->dbquery($queryString);

        break;

        case "browseSO":

            if($_POST['cid'] != '') { $f1 = " and a.customer_code = '$_POST[cid]' "; }

            $q = "SELECT so_no, LPAD(so_no,6,0) AS sono, DATE_FORMAT(so_date,'%m/%d/%Y') AS sdate, customer_name as chargeto, amount_due, a.remarks FROM pharma_so_header a WHERE branch = '$bid' AND billed != 'Y' AND so_no NOT IN (SELECT distinct so_no FROM pharma_si_details WHERE doc_no = '$_POST[doc_no]' AND branch = '$bid') AND `status` = 'Finalized' $f1 ORDER BY so_date DESC;";
            
            if($con->countRows($q) > 0) {
                echo "
                    <form name=\"frmFetchedSO\" id=\"frmFetchedSO\">
                    <table width=100% cellspacing=0 cellpadding=0>

                        <tr>
                            <td class=gridHead width=10%>SO #</td>
                            <td class=gridHead width=10%>DATE</td>
                            <td class=gridHead width=20%>CUSTOMER</td>
                            <td class=gridHead >REMARKS</td>
                            <td class=gridHead width=15% align=right>AMOUNT</td>
                            <td class=gridHead width=20>&nbsp;</td>
                        </tr>";

                $soQuery = $con->dbquery($q); $i=0;
                while($soRow = $soQuery->fetch_array()) {

                    echo "<tr bgcolor='".$con->initBackground($i)."'>
                            <td class=grid>".$soRow['sono']."</td>
                            <td class=grid>".$soRow['sdate']."</td>
							<td class=grid>".$soRow['chargeto']."</td>
                            <td class=grid>".$soRow['remarks']."</td>
                            <td class=grid align=right>".number_format($soRow['amount_due'],2)."</td>
                            <td class=grid align=center><input type=radio name=\"so\" id=\"so\" value='".$soRow['so_no']."'></td>
                        </tr>
                    
                    ";
                    $i++;

                }
                 echo "</table>  
                </form>";


            }

        break;

        case "retrieveSO":

			if($_POST['cid'] != '') { $cid = " and customer_code = '$_POST[cid]' "; } else { $cid = ''; }
			$docno = $_POST['docno'];

			$soString = "select billed, scpwd_id from pharma_so_header where status = 'Finalized' and so_no = '$_POST[so]' and branch = '$bid' $cid;";
			if($con->countRows($soString) > 0) {
				$so = $con->getArray($soString);
				if($so['billed'] == 'Y') {
					echo json_encode(array("respond"=>"used"));
				} else {

					$s = $con->getArray("select distinct a.doc_no,lpad(a.doc_no,6,'0') as docno, date_format(doc_date,'%m/%d/%Y') as docdate from pharma_si_header a left join pharma_si_details b on a.doc_no = b.doc_no and a.branch = b.branch where b.so_no = '$_POST[so]' and b.branch = '$bid' and `status` = 'Active';");
					if($s[0] != '') {
						echo json_encode(array("respond"=>"currentActive","doc_no"=>$s['doc_no'],"docno"=>$s['docno'],"date"=>$s['docdate']));
					} else {

						/* INSERT CONTENT OF SO DETAILS TO OR DETAILS */
						$sdQuery = $con->dbquery("select * from pharma_so_details a where a.so_no = '$_POST[so]' and a.branch = '$bid';");
						while($sdRow = $sdQuery->fetch_array()) {
							
                            if($so['scpwd_id'] != '') {
                                $discount = $sdRow['amount'] * 0.20;
                                $adue = $sdRow['amount'] - $discount;
                            } else { $discount = $sdRow['discount']; $adue = $sdRow['amount_due']; }
                            
                            
                            $con->dbquery("INSERT INTO pharma_si_details (doc_no,branch,so_no,`code`,`description`,unit,unit_price,qty,amount,discount,amount_due,trace_no) VALUES ('$docno','$bid','$sdRow[so_no]','$sdRow[code]','$sdRow[description]','$sdRow[unit]','$sdRow[unit_price]','$sdRow[qty]','$sdRow[amount]','$discount','$adue','$_POST[trace_no]');");
						}
						
						$or = array("respond"=>"ok","scpwd_id"=>$so['scpwd_id']);
						echo json_encode($or);
						updateAmount($docno);
					}
				}
			} else {
				echo json_encode(array("respond"=>"notFound"));
			}

		break;

        case "addItem":

            $qty = $con->formatDigit($_POST['qty']);
            $price = $con->formatDigit($_POST['cost']);
            $amt = ROUND($qty*$price,2);
            
            $con->dbquery("INSERT INTO pharma_si_details (doc_no,branch,`code`,`description`,unit,unit_price,qty,amount,trace_no) VALUES ('$_POST[doc_no]','$bid','$_POST[item]','".$con->escapeString(htmlentities($_POST['description']))."','$_POST[unit]','$price','$qty','$amt','$_POST[trace_no]');");
            updateAmount($_POST['doc_no']);
        break;


        case "deleteLine":
            $con->deleteRow($table="pharma_si_details",$arg = "line_id='$_POST[lid]'");
            updateAmount($_POST['doc_no']);
        break;

        case "applyDiscount":
			$d = $con->getArray("select * from pharma_si_details where line_id = '$_POST[lid]';");
			
            $discount = ROUND(($d['amount']) * ($_POST['discPercent']/100),2);
            $adue = $d['amount'] - $discount;      
           
            $con->dbquery("update ignore pharma_si_details set discount = '$discount', disctype='$_POST[discType]', discpercent = '$_POST[discPercent]', amount_due = '$adue' where line_id = '$_POST[lid]';");
			updateAmount($_POST['doc_no']);
		break;

        case "getTotals":
            $t = $con->getArray("SELECT gross, discount, net, amount_due, amount_paid, balance FROM pharma_si_header WHERE doc_no = '$_POST[doc_no]' AND branch = '$bid';");
            
            echo json_encode(array("gross"=>number_format($t['gross'],2), "discount"=>number_format($t['discount'],2), "net"=>number_format($t['net'],2), "due"=>number_format($t['amount_due'],2), "paid"=>number_format($t['amount_paid'],2), "balance"=>number_format($t['balance'],2) ));
        break;

        case "check4print":
			list($a) = $con->getArray("select count(*) from pharma_si_header where doc_no = '$_POST[doc_no]' and branch = '$bid';");
			list($b) = $con->getArray("select count(*) from pharma_si_details where doc_no = '$_POST[doc_no]' and branch = '$bid';");
			
			if($a == 0 && $b > 0) { echo "head"; }
			if($b == 0 && $a > 0) { echo "det"; }
			if($a == 0 && $b == 0) { echo "both"; }
			if($a > 0 && $b > 0) { echo "noerror"; }
		break;

        case "updatePayment":
			$con->dbquery("update ignore pharma_si_header set balance = 0, amount_paid = ".$con->formatDigit($_POST['paid']).", cash_tendered = '".$con->formatDigit($_POST['tendered'])."', change_due = '".$con->formatDigit($_POST['changeDue'])."', updated_by = '$uid', updated_on = now() where doc_no = '$_POST[doc_no]' and branch = '$bid';");
		break;

        case "finalize":
            $con->dbquery("update ignore pharma_si_header set `status` = 'Finalized', updated_by = '$_SESSION[userid]', updated_on = now() where doc_no = '$_POST[doc_no]' and branch = '$bid';");

            $soquery = $con->dbquery("select distinct so_no from pharma_si_details where doc_no = '$_POST[doc_no]' and branch = '$bid';");
            while($sorow = $soquery->fetch_array()) {
                $con->dbquery("update ignore pharma_so_header set billed = 'Y' where so_no = '$sorow[0]' and branch = '$bid';");

            }
        break;

        case "checkBilled":
            if($con->countRows("select doc_no from pharma_si_header where doc_no = '$_POST[doc_no]' and branch = '$bid' and (billed = 'Y' or paid ='Y');") > 0) {
                echo "processed";
            }
        break;

        case "reopen":
            $con->dbquery("update pharma_si_header set status = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where doc_no = '$_POST[doc_no]' and branch = '$bid';");

            $soquery = $con->dbquery("select distinct so_no from pharma_si_details where doc_no = '$_POST[doc_no]' and branch = '$bid';");
            while($sorow = $soquery->fetch_array()) {
                $con->dbquery("update ignore pharma_so_header set billed = 'N' where so_no = '$sorow[0]' and branch = '$bid';");

            }
        break;
        
        case "cancel":
            $con->dbquery("update ignore pharma_si_header set `status` = 'Cancelled', updated_by = '$_SESSION[userid]', updated_on = now() where doc_no = '$_POST[doc_no]' and branch = '$bid';");
        break; 

        case "retrieve":
            $data = array();
			$srrd = $con->dbquery("SELECT line_id as id, lpad(so_no,6,0) as so_no, `code`, description, unit, qty, unit_price, amount, discount, amount_due FROM pharma_si_details WHERE trace_no = '$_REQUEST[trace_no]';");
			while($row = $srrd->fetch_array()) {
				$data[] = array_map('utf8_encode',$row);
			}
			$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
			echo json_encode($results);	
        break;


    }



?>