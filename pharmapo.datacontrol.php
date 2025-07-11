<?php
	session_start();
	require_once "handlers/_pharmapofunct.php";
	$p = new myPO;
	$bid = $_SESSION['branchid'];
	$uid = $_SESSION['userid'];


	switch($_POST['mod']) {
		case "saveHeader":
			if($_POST['po_no'] != '') {
				$pono = $_POST['po_no'];
				$s = "update ignore pharma_po_header set supplier = '$_POST[cid]', requested_by = '".$p->escapeString(htmlentities($_POST['requested_by']))."', delivery_address = '".$p->escapeString(htmlentities($_POST['del_addr']))."', date_needed = '".$p->formatDate($_POST['date_needed'])."', supplier_name = '".$p->escapeString(htmlentities($_POST['cname']))."', supplier_addr = '".$p->escapeString(htmlentities($_POST['addr']))."', po_date = '".$p->formatDate($_POST['po_date'])."', terms='$_POST[terms]', mrs_no = '$_POST[mrs]', remarks = '".$p->escapeString($_POST['remarks'])."', updated_by = '$uid', updated_on = now() where po_no = '$_POST[po_no]' and branch = '$bid';";
			} else {
				list($pono) = $p->getArray("select lpad((ifnull(max(po_no),0)+1),6,0) from pharma_po_header where branch = '$bid';"); 
				$s = "insert ignore into pharma_po_header (branch, po_no, po_date, terms, requested_by, mrs_no, delivery_address, date_needed, supplier, supplier_name, supplier_addr, remarks, created_by, created_on, trace_no) values ('$bid','$pono','".$p->formatDate($_POST['po_date'])."','$_POST[terms]','".$p->escapeString(htmlentities($_POST['requested_by']))."','$_POST[mrs]','".$p->escapeString(htmlentities($_POST['del_addr']))."','".$p->formatDate($_POST['date_needed'])."','$_POST[cid]','".$p->escapeString(htmlentities($_POST['cname']))."','".$p->escapeString($_POST['addr'])."','".$p->escapeString(htmlentities($_POST['remarks']))."','$uid',now(),'$_POST[trace_no]');";
			}
			echo $pono;
			$p->dbquery($s);
		break;

		case "addItem":
			$p->dbquery("INSERT INTO pharma_po_details (branch,po_no,costcenter,item_code,description,qty,unit,cost,amount,trace_no) VALUES ('$bid','$_POST[po_no]','$_POST[costcenter]','$_POST[item]','".$p->escapeString(htmlentities($_POST['description']))."','".$p->formatDigit($_POST['qty'])."','$_POST[unit]','".$p->formatDigit($_POST['cost'])."','".$p->formatDigit($_POST['amount'])."','$_POST[trace_no]');");
		break;

		case "updateItem":
			$p->dbquery("UPDATE pharma_po_details set costcenter = '$_POST[costcenter]',item_code = '$_POST[item]',description='".$p->escapeString(htmlentities($_POST['description']))."',qty = '".$p->formatDigit($_POST['qty'])."',unit = '$_POST[unit]',cost = '".$p->formatDigit($_POST['cost'])."',amount = '".$p->formatDigit($_POST['amount'])."' where line_id = '$_POST[lid]';");
			$p->updateHeaderAmt($_POST['po_no'],$bid);
		break;

		case "deleteLine":
			$p->dbquery("delete from pharma_po_details where line_id = '$_POST[lid]';");
			$p->updateHeaderAmt($_POST['po_no'],$bid);
		break;

		case "check4print":
			list($a) = $p->getArray("select count(*) from pharma_po_header where po_no = '$_POST[po_no]' and branch = '$bid';");
			list($b) = $p->getArray("select count(*) from pharma_po_details where po_no = '$_POST[po_no]' and branch = '$bid';");
			
			if($a == 0 && $b > 0) { echo "head"; }
			if($b == 0 && $a > 0) { echo "det"; }
			if($a == 0 && $b == 0) { echo "both"; }
			if($a > 0 && $b > 0) { echo "noerror"; }
		break;

		case "finalizePO":
			$p->dbquery("update pharma_po_header set status = 'Finalized', updated_by = '$uid', updated_on = now() where po_no ='$_POST[po_no]' and branch = '$bid';");
			$p->updateHeaderAmt($_POST['po_no'],$bid);
		break;

		case "reopenPO":
			$p->dbquery("update pharma_po_header set status = 'Active', updated_by = '$uid', updated_on = now() where po_no = '$_POST[po_no]' and branch = '$bid';");
		break;

		case "cancel":
			$p->dbquery("update pharma_po_header set status = 'Cancelled', updated_by = '$uid', updated_on = now() where po_no = '$_POST[po_no]' and branch = '$bid';");
		break;

		case "getDocInfo":
			$m = $p->getArray("select a.status,if(a.status='Cancelled','Cancelled By',if(a.status='Finalized','Finalized By','Last Updated By')) as lbl, a.status,if(a.status='Cancelled','Cancelled On',if(a.status='Finalized','Finalized On','Last Updated On')) as lbl2,b.fullname as cby, date_format(a.created_on,'%m/%d/%Y %r') as con, c.fullname as uby, date_format(updated_on,'%m/%d/%Y %r') as uon from pharma_po_header a left join user_info b on a.created_by=b.emp_id left join user_info c on a.updated_by=c.emp_id where po_no = '$_POST[po_no]' and a.branch = '$bid';");
			$n = $p->dbquery("SELECT a.rr_no,with_ap,IF(with_ap = 'Y',CONCAT('AP-',a.apv_no),IF(with_cv='Y',CONCAT('CV-',a.cv_no),'')) AS doc_no, IF(with_ap='Y',DATE_FORMAT(c.apv_date,'%m/%d/%y'),IF(with_cv='Y',DATE_FORMAT(d.cv_date,'%m/%d/%y'),'')) AS doc_date,  DATE_FORMAT(a.rr_date,'%m/%d/%y') AS rd8 FROM pharma_rr_header a LEFT JOIN pharma_rr_details b ON a.rr_no=b.rr_no AND a.branch=b.branch LEFT JOIN apv_header c ON a.apv_no=c.apv_no AND a.branch=c.branch LEFT JOIN cv_header d ON a.cv_no=d.cv_no AND a.branch=d.branch WHERE b.po_no = '$_POST[po_no]' AND a.branch = '$bid' GROUP BY a.rr_no;");
			while(list($o,$p,$u,$v,$w) = $n->fetch_array(MYSQLI_BOTH)) {
				if($o != "") { $q = $q . " RR # $o Dtd. $w;"; }
				if($u != "" && $u != $ou) { $z = $z . " $u Dtd. $v;"; }

				if($p == "Y") {
					$doc = explode("-",$u);
					$f = $p->dbquery("select a.branch,a.cv_no, date_format(a.cv_date,'%m/%d/%y') as cd8 from cv_header a left join cv_details b on a.cv_no=b.cv_no and a.company=b.company and a.branch=b.branch where b.ref_no = '$doc[1]' and b.ref_type='AP' and b.acct_branch='1' and b.company='$_SESSION[company]';");
					while(list($l,$g,$h) = $f->fetch_array(MYSQLI_BOTH)) {
						$t = $t . "CV # $l-$g Dtd. $h;";					}
				}
				$ou = $u;
			}

			if($q == "") { $q = "None "; }
			if($t == "") { $t = "None "; }
			if($z == "") { $z = "None "; }

			echo "<table width=100% cellpadding=2 cellspacing=0 style='font-size: 11px;'>
					<tr>
						<td width='30%'>Created By</td>
						<td width=5>:</td>
						<td style='padding-left:10px;'>$m[cby]</td>
					</tr>
					<tr>
						<td>Created On</td>
						<td width=5>:</td>
						<td style='padding-left:10px;'>$m[con]</td>
					</tr>
					<tr>
						<td>$m[lbl]</td>
						<td width=5>:</td>
						<td style='padding-left:10px;'>$m[uby]</td>
					</tr>
					<tr>
						<td>$m[lbl2]</td>
						<td width=5>:</td>
						<td style='padding-left:10px;'>$m[uon]</td>
					</tr>
					<tr>
						<td valign=top>RR Reference</td>
						<td valign=top width=5 valign=top>:</td>
						<td style='padding-left:10px;' valign=top>".substr_replace($q, "", -1)."</td>
					</tr>
					<tr>
						<td valign=top>AP Reference</td>
						<td valign=top width=5>:</td>
						<td style='padding-left:10px;'>".substr_replace($z, "", -1)."</td>
					</tr>
					<tr>
						<td valign=top>CV Reference</td>
						<td valign=top width=5>:</td>
						<td style='padding-left:10px;'>".substr_replace($t, "", -1)."</td>
					</tr>
				  </table>";

		break;

		case "getTotals":
			list($gross) = $p->getArray("SELECT SUM(ROUND(qty*cost,2)) AS gross FROM pharma_po_details WHERE po_no = '$_POST[po_no]' AND branch = '$bid';");
			if($gross == "") { $gross = "0.00"; }
			echo json_encode(array("gross"=>$gross));
		break;

		case "retrieve":
			$data = array();
	
			$srrd = $p->dbquery("SELECT line_id AS id, a.costcenter, b.costcenter AS cc, description, item_code, unit, qty, cost, amount FROM pharma_po_details a LEFT JOIN options_costcenter b ON a.costcenter = b.unitcode WHERE trace_no = '$_REQUEST[trace_no]';");
			while($row = $srrd->fetch_array()) {

				$data[] = array_map('utf8_encode',$row);
			}
			
			$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
			echo json_encode($results);	
		break;

	}

	//$p->@mysqli::close();

?>