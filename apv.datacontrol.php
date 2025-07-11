<?php
	session_start();
	
	//ini_set("display_errors","On");
	require_once "handlers/_apfunct.php";
	$p = new myAP;
	$apv_no = ltrim($_POST['apv_no'],0);
	$bid = $_SESSION['branchid'];
	$uid = $_SESSION['userid'];
	$trace_no = $_POST['trace_no'];

	switch($_POST['mod']) {
		case "saveHeader":
			list($a) = $p->getArray("select `status` from apv_header where apv_no = '$apv_no' and branch = '$bid';");
			if($a != "") {
				if($a == "Active") {
					$p->dbquery("update apv_header set cy='".$p->formatCY($_POST['apv_date'])."', apv_date='".$p->formatDate($_POST['apv_date'])."', supplier='$_POST[cid]', supplier_name='".$p->escapeString(htmlentities($_POST['cname']))."', supplier_addr='".$p->escapeString(htmlentities($_POST['addr']))."',terms='$_POST[terms]',atc_code='$_POST[atc_code]',remarks='".$p->escapeString(htmlentities($_POST['remarks']))."',updated_by='$uid', updated_on=now() where apv_no = '$apv_no' and branch = '$bid';");
				} else {
					echo "error";
				}
			} else {
				$p->dbquery("insert ignore into apv_header (cy,branch,trace_no,apv_no,apv_date,supplier,supplier_name,supplier_addr,terms,atc_code,remarks,created_by,created_on) values ('".$p->formatCY($_POST['apv_date'])."','$bid','$trace_no','$apv_no','".$p->formatDate($_POST['apv_date'])."','$_POST[cid]','".$p->escapeString(htmlentities($_POST['cname']))."','".$p->escapeString(htmlentities($_POST['addr']))."','$_POST[terms]','$_POST[atc_code]','".$p->escapeString(htmlentities($_POST['remarks']))."','$uid',now());");
			}
		break;

		case "getInvoices":
			list($b) = $p->getArray("select count(*) from rr_header where supplier = trim(leading '0' from '$_POST[cid]') and (invoice_no != '' or invoice_no != 0) and (with_ap != 'Y' && with_cv != 'Y') and status = 'Finalized' and branch = '$bid';");
			if($b > 0) {

				echo "<table width=100% cellpadding=2 cellspacing=0>
						<tr>
							<td class='ui-state-default' style='padding: 5px;' align=center width=10%>RR No.</td>
							<td class='ui-state-default' style='padding: 5px;' align=center width=10%>RR Date</td>
							<td class='ui-state-default' style='padding: 5px;' align=center width=10%>Inv. No.</td>
							<td class='ui-state-default' style='padding: 5px;' align=center width=10%>Inv. Date</td>
							<td class='ui-state-default' style='padding: 5px;' width=40%>Transaction Remarks</td>
							<td class='ui-state-default'  style='padding: 5px;'align=center>Amount</td>
							<td class='ui-state-default' style='padding: 5px;' width=10>&nbsp;</td>
						</tr>
					</table>
					";

				$c = $p->dbquery("select lpad(rr_no,6,0) as xrr, rr_no as rr, rr_no, date_format(rr_date,'%m/%d/%Y') as rd8, invoice_no as ino, date_format(invoice_date,'%m/%d/%Y') as id8, remarks, amount from rr_header where supplier = trim(leading '0' from '$_POST[cid]') and (invoice_no != '' or invoice_no != 0) and (with_ap != 'Y' && with_cv != 'Y') and status = 'Finalized' and branch = '$bid';");
				echo "<div style='height: 330px; overflow-x: auto;'>
						<table width=100% cellpadding=2 cellspacing=0>";
						while($d = $c->fetch_array(MYSQLI_BOTH)) {
							$i = 0; $checked = "";
							$needle = $d['rr_no'];
							if(isset($_SESSION['ques'])) {	if(in_array($needle, $_SESSION['ques'])) { $checked = "checked"; }}

							echo "<tr bgcolor='".$p->initBackground($i)."'>
									<td class=grid valign=top align=center width=11%>$d[xrr]</td>
									<td class=grid valign=top align=center width=9%>$d[rd8]</td>
									<td class=grid valign=top align=center width=11%>$d[ino]</td>
									<td class=grid valign=top align=center width=10%>$d[id8]</td>
									<td class=grid valign=top width=40%>$d[remarks]</td>
									<td class=grid valign=top align=right>".number_format($d['amount'],2)."&nbsp;<input type='checkbox' id=$d[rr_no] value='$needle' onclick='tagRR(this.id,this.value);' $checked></td>
								</tr>"; $i++;
						}
						if($I < 20) {
								for($i;$i <=19; $i++) {
									echo "<tr  bgcolor='".$p->initBackground($i)."'><td colspan=6 class=grid>&nbsp;</td></tr>";
								}
							}
						echo "</table>
				</div>";
			}
		break;

		case "tagRR":
			$val = array();
			$push = $_REQUEST['push'];
			array_push($val,$_REQUEST['val']);
			if(!isset($_SESSION['ques'])) { $_SESSION['ques'] = array(); }
			if($push == 'Y') { if(array_search($val[0],$_SESSION['ques'])==0) { array_push($_SESSION['ques'],$val[0]); }
			} else { $_SESSION['ques'] = array_diff($_SESSION['ques'],$val); }
		break;

		case "loadInvoice2AP":
			if(count($_SESSION['ques']) > 0) {
				$payableGT = 0; $inputGT = 0; $ewtGT = 0;
				foreach($_SESSION['ques'] as $rr_no) {
					$netGT = 0; $grossGT = 0;
					list($rrd8,$ino,$id8,$remarks) = $p->getArray("select rr_date, invoice_no, invoice_date, remarks from rr_header where rr_no = '$rr_no' and branch = '$bid';");
					
					if($_POST['isVat'] == "Y") {
						$txt = "SELECT costcenter, SUM(net_amt) AS net_amt, SUM(gross_amt) AS gross_amt, IF(exp_acct='',asset_acct,exp_acct) AS acct FROM (SELECT b.costcenter, IF(d.vatable='Y',IF(c.vat_exempt='Y',round(SUM(b.amount),2),ROUND(SUM(b.amount) / 1.12,2)),ROUND(SUM(b.amount),2)) AS net_amt,  round(SUM(b.amount),2) AS gross_amt, c.exp_acct, c.asset_acct FROM rr_header a LEFT JOIN rr_details b ON a.rr_no = b.rr_no AND a.branch = b.branch LEFT JOIN products_master c ON b.item_code = c.item_code left join contact_info d on a.supplier=d.file_id WHERE a.rr_no = '$rr_no' AND a.supplier = '$_POST[cid]' AND a.status = 'Finalized' AND a.branch = '$bid' GROUP BY b.costcenter,b.item_code) a GROUP BY costcenter,acct";
					} else {
						$txt = "SELECT costcenter, SUM(net_amt) AS net_amt, SUM(gross_amt) AS gross_amt, IF(exp_acct='',asset_acct,exp_acct) AS acct FROM (SELECT b.costcenter, round(SUM(b.amount),2) AS net_amt,  round(SUM(b.amount),2) AS gross_amt, c.exp_acct, c.asset_acct FROM rr_header a LEFT JOIN rr_details b ON a.rr_no = b.rr_no AND a.branch = b.branch LEFT JOIN products_master c ON b.item_code = c.item_code left join contact_info d on a.supplier=d.file_id WHERE a.rr_no = '$rr_no' AND a.supplier = '$_POST[cid]' AND a.status = 'Finalized' AND a.branch = '$bid' GROUP BY b.costcenter,b.item_code) a GROUP BY costcenter,acct";
					}
					
					
					$q = $p->dbquery($txt);
					while($r = $q->fetch_array()) {
						$p->dbquery("insert ignore into apv_details (cy,branch,trace_no,apv_no,ref_no,ref_date,cost_center,acct,acct_desc,debit,balance) values ('".$p->formatCY($_POST['apv_date'])."','$bid','$trace_no','$apv_no','$ino','$id8','$r[costcenter]','$r[acct]','".$p->getAcctDesc($r['acct'],$_SESSION['company'])."','$r[net_amt]','$r[net_amt]');");
						$netGT+=$r['net_amt']; $grossGT+=$r['gross_amt'];
					}
				
					if($_POST['isVat'] == "Y") {
						if($_POST['atc'] != "") {
							list($rate,$erate,$ewtAcct) = $p->getArray("select ROUND(rate/100,2), rate, acct_code from options_atc where atc_code = '$_POST[atc]';");
							$ewt = ROUND($netGT * $rate,2);
							$p->dbquery("insert ignore into apv_details (cy,branch,trace_no,apv_no,ref_no,ref_date,acct,acct_desc,credit,balance) values ('".$p->formatCY($_POST['apv_date'])."','$bid','$trace_no','$apv_no','$ino','$id8','$ewtAcct','".$p->getAcctDesc($ewtAcct,$_SESSION['company'])."','$ewt','$ewt');");
						}
					}

					$payable = $grossGT - $ewt;
					$input = $grossGT - $netGT;
					if($input > 0) { $p->dbquery("insert ignore into apv_details (cy,branch,trace_no,apv_no,ref_no,ref_date,acct,acct_desc,debit,balance) values ('".$p->formatCY($_POST['apv_date'])."','$bid','$trace_no','$apv_no','$ino','$id8','30208','".$p->getAcctDesc("30208",$_SESSION['company'])."','$input','$input');"); }
					$p->dbquery("insert ignore into apv_details (cy,branch,trace_no,apv_no,ref_no,ref_date,acct,acct_desc,credit,balance) values ('".$p->formatCY($_POST['apv_date'])."','$bid','$trace_no','$apv_no','$ino','$id8','30101','".$p->getAcctDesc("30101",$_SESSION['company'])."','$payable','$payable');");
					$p->dbquery("update ignore rr_header set with_ap = 'Y', apv_no = '$apv_no', apv_date='".$p->formatDate($_POST['apv_date'])."' where rr_no = '$rr_no' and branch = '$bid';");
					
					/* INSERT INTO SUBHEADER */
					$p->dbquery("insert ignore into apv_subheader (branch,cy,apv_no,ref_type,rr_no,rr_date,invoice_no,invoice_date,net_payable,input_vat,ewt_code,ewt_rate,ewt_amount,balance,trace_no) values ('$bid','".$p->formatCY($_POST['apv_date'])."','$apv_no','RR','$rr_no','$rrd8','$ino','$id8','$payable','$input','$_POST[atc]','$erate','$ewt','$payable','$trace_no');");	
				}
				$p->updateHeadAmount($apv_no);
				unset($_SESSION['ques']);
			}
		break;

		case "addInvoice":
			if($_POST['isVat'] == "Y") { $vat = ROUND(($_POST['amount'] / 1.12) * 0.12,2); }
			$net = $_POST['amount'] - $vat;
			if($_POST['atc'] != "") { list($ewt,$erate,$ewtAcct) = $p->getArray("select ROUND($net * (rate/100),2) as ewt,rate, acct_code from options_atc where atc_code = '$_POST[atc]';"); }
			$payable = $_POST['amount'] - $ewt;

			if($ewt > 0) { $p->dbquery("insert ignore into apv_details (branch,cy,trace_no,apv_no,ref_no,ref_date,acct,acct_desc,credit) values ('$bid','".$p->formatCY($_POST['apv_date'])."','$trace_no','$apv_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','$ewtAcct','".$p->getAcctDesc($ewtAcct,$_SESSION['company'])."','$ewt');"); }
			if($vat > 0) { $p->dbquery("insert ignore into apv_details (branch,cy,trace_no,apv_no,ref_no,ref_date,acct,acct_desc,debit) values ('$bid','".$p->formatCY($_POST['apv_date'])."','$trace_no','$apv_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','10415','".$p->getAcctDesc("10415",$_SESSION['company'])."','$vat');"); }
			
			$p->dbquery("insert ignore into apv_details (branch,cy,trace_no,apv_no,ref_no,ref_date,acct,acct_desc,credit) values ('$bid','".$p->formatCY($_POST['apv_date'])."','$trace_no','$apv_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','20101','".$p->getAcctDesc("20101",$_SESSION['company'])."','$payable');");
			$p->dbquery("insert ignore into apv_subheader (branch,cy,trace_no,apv_no,invoice_no,invoice_date,net_payable,input_vat,ewt_code,ewt_rate,ewt_amount,balance) values ('$bid','".$p->formatCY($_POST['apv_date'])."','$trace_no','$apv_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','$payable','$vat','$_POST[atc]','$erate','$ewt','$payable');");
			$p->updateHeadAmount($apv_no);

		break;

		case "deleteInvoice":
			$p->dbquery("delete from apv_details where apv_no = '$apv_no' and ref_no = '$_POST[ino]' and branch = '$bid';");
			$p->dbquery("delete from apv_subheader where apv_no = '$apv_no' and invoice_no = '$_POST[ino]' and branch = '$bid';");
			$p->dbquery("update ignore rr_header set with_ap = 'N', apv_no = '', apv_date='' where rr_no = '$_POST[rr_no]' and branch = '$bid';");
			$p->updateHeadAmount($apv_no);
		break;

		case "insertDetail":
			if($_POST['dc'] == "DB") { $db = $p->formatDigit($_POST['amount']); $cr = 0; } else { $cr = $p->formatDigit($_POST['amount']); $db = 0; }
			$p->dbquery("insert ignore into apv_details (cy,branch,apv_no,ref_no,ref_date,acct,acct_desc,debit,credit,cost_center) values ('".$p->formatCY($_POST['apv_date'])."','1','$apv_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','$_POST[acode]','".$p->escapeString($_POST[adesc])."','$db','$cr','$_POST[ccenter]');");
			
			list($crAmt) = $p->getArray("select sum(credit) from apv_details where apv_no = '$apv_no' and acct != '20101';");
			list($dbAmt) = $p->getArray("select sum(debit) from apv_details where apv_no = '$apv_no';");
			
			list($isAP) = $p->getArray("select count(*) from apv_details where apv_no = '$apv_no' and acct = '20101';");
			
			if($isAP > 0) {
				$p->dbquery("update apv_details set credit = 0$dbAmt - 0$crAmt where apv_no = '$apv_no' and acct = '20101';");
			} else {
				$p->dbquery("insert ignore into apv_details (cy,branch,apv_no,ref_no,ref_date,acct,acct_desc,credit,cost_center) values ('".$p->formatCY($_POST['apv_date'])."','1','$apv_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','$_POST[acode]','".$p->escapeString($_POST[adesc])."','".$dbAmt-$crAmt."','$_POST[ccenter]');");
			}
			
			$p->updateHeadAmount($apv_no);
		break;

		case "deleteLine":
			$p->dbquery("delete from apv_details where record_id = '$_POST[lid]';");
			$p->updateHeadAmount($apv_no);
		break;

		case "check4print":
			list($aaa) = $p->getArray("select count(*) from apv_details where apv_no = '$apv_no' and branch = '$bid';");
			if($aaa > 0) {
				list($db,$cr) = $p->getArray("select sum(debit), sum(credit) from apv_details where apv_no = '$apv_no' and branch = '$bid';");
				if($db!=$cr) { echo "DiBalanse"; } else {
					$p->dbquery("insert ignore into acctg_gl (cy,branch,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,debit,credit,cost_center,doc_remarks,posted_by,posted_on) select a.cy,a.branch,a.apv_no as doc_no,a.apv_date as doc_date,'AP' as doc_type, a.supplier as contact_id, '1' as acct_branch, b.acct, sum(debit) as debit, sum(credit) as credit, b.cost_center,a.remarks as doc_remarks,'$uid',now() from apv_header a left join apv_details b on a.apv_no=b.apv_no and a.branch=b.branch where a.apv_no = '$apv_no' and `status` != 'Posted' and a.branch = '$bid' group by b.cost_center,b.acct;");
					$p->dbquery("update ignore apv_header set status = 'Posted', updated_by = '$uid', updated_on = now() where apv_no = '$apv_no' and branch = '$bid';");
					echo "noerror";
				}
			} else { echo "waySulod"; }
		break;

		case "reopenAP":
			$p->dbquery("delete from acctg_gl where doc_no = '$apv_no' and doc_type = 'AP' and branch = '$bid';");
			$p->dbquery("update ignore apv_header set status = 'Active', updated_by = '$uid', updated_on = now() where apv_no = '$apv_no' and branch = '$bid';");
		break;

		case "cancel":
			$p->dbquery("update ignore apv_header set status='Cancelled', updated_by='$uid', updated_on = now() where apv_no ='$apv_no' and branch = '$bid';");
		break;
		case "getTotals":
			list($ap,$input,$ewt,$balance,$applied) = $p->getArray("SELECT amount, vat, ewt_amount, balance, applied_amount FROM apv_header WHERE apv_no = '$apv_no' AND branch = '$bid';");
			if($input>0) { $netOfVat = number_format(($ap+$ewt-$input),2); } else { $netOfVat = "0.00"; }
			echo json_encode(array("gross"=>number_format(ROUND($ap+$ewt,2),2),"netOfVat"=>$netOfVat,"netPayable"=>number_format($ap,2),"vat"=>number_format($input,2),"ewt"=>number_format($ewt,2),"balance"=>number_format($balance,2),"applied"=>number_format($applied,2)));		
		break;
		case "getApplied":
			$a = $p->dbquery("SELECT 'CV' AS `type`, a.cv_no AS doc_no, CONCAT(LPAD(a.branch,2,0),'-',LPAD(a.cv_no,6,0)) AS xdoc, DATE_FORMAT(a.cv_date,'%m/%d/%Y') AS dd8 FROM cv_header a INNER JOIN cv_details b ON a.cv_no = b.cv_no AND a.branch = b.branch WHERE ref_type = 'AP' AND ref_no = '".ltrim($apv_no,'0')."' AND acct_branch = '$bid';");
			$showee = "";
			while(list($type,$docno,$xdoc,$dd8) = $a->fetch_array(MYSQLI_BOTH)) {
				switch($type) { 
					case "CV":
						$showee .= "&nbsp;&nbsp;&nbsp;&raquo; <a href=\"#\" onclick=\"parent.viewCV('$docno');\">Check Voucher No. $xdoc Dtd. $dd8</a><br/>";
					break;
				}
			}
			echo $showee;
		break;

		case "retrieveLedger":
			$data = array();
	
			$srrd = $p->dbquery("SELECT acct, acct_desc, b.costcenter, SUM(debit) AS db, SUM(credit) AS cr FROM apv_details a LEFT JOIN options_costcenter b ON a.cost_center = b.unitcode WHERE trace_no = '$_POST[trace_no]' GROUP BY acct, cost_center order by debit desc, a.cost_center;");
			while($row = $srrd->fetch_array()) {

				$data[] = array_map('utf8_encode',$row);
			}
			
			$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
			echo json_encode($results);	
		break;

		case "retrievePurchases":
			$data = array();
	
			$srrd = $p->dbquery("SELECT record_id AS id, LPAD(rr_no,6,0) AS rrno, DATE_FORMAT(rr_date,'%m/%d/%Y') AS rrdate, invoice_no AS ino, DATE_FORMAT(invoice_date,'%m/%d/%Y') AS idate, ROUND(net_payable+ewt_amount,2) AS gross, ewt_amount, input_vat, net_payable AS net FROM apv_subheader WHERE trace_no = '$_POST[trace_no]';");
			while($row = $srrd->fetch_array()) {

				$data[] = array_map('utf8_encode',$row);
			}
			
			$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
			echo json_encode($results);	
		break;

	}
?>