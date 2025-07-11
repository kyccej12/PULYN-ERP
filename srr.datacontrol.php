<?php
	session_start();
	require_once 'handlers/_ifunct.php';
	$mydb = new imod;

	function updateAmount($srr_no) {
		global $mydb;
		list($total) = $mydb->getArray("select ifnull(sum(qty*cost),0) from srr_details where srr_no = '$srr_no' and branch = '$_SESSION[branchid]';");
		$mydb->dbquery("update srr_header set amount = '$total' where srr_no = '$srr_no' and branch = '$_SESSION[branchid]';");
		echo number_format($total,2);
	}

	switch($_REQUEST['mod']) {
		case "saveHeader":
			
			if($_POST['srr_no'] != "") {
				$s = "update ignore srr_header set received_by = '".$mydb->escapeString(htmlentities($_POST['by']))."', received_from = '".$mydb->escapeString(htmlentities($_POST['from']))."', srr_date = '".$mydb->formatDate($_POST['srr_date'])."', ref_no = '$_POST[ref_no]', ref_date='".$mydb->formatDate($_POST['ref_date'])."', ref_type='$_POST[ref_type]', remarks = '".$mydb->escapeString(htmlentities($_POST['remarks']))."', updated_by = '$_SESSION[userid]', updated_on = now() where srr_no = '$_POST[srr_no]' and branch = '$_SESSION[branchid]';";
				$srr_no = $_POST['srr_no'];
			} else {
				list($srr_no) = $mydb->getArray("select ifnull(max(srr_no),0)+1 from srr_header where branch = '$_SESSION[branchid]';"); 
				$s = "insert ignore into srr_header (branch,srr_no,srr_date,received_from,received_by,ref_type,ref_no,ref_date,trace_no,remarks,created_by,created_on) values ('$_SESSION[branchid]','$srr_no','".$mydb->formatDate($_POST['srr_date'])."','".$mydb->escapeString(htmlentities($_POST['from']))."','".$mydb->escapeString(htmlentities($_POST['by']))."','$_POST[ref_type]','$_POST[ref_no]','".$mydb->formatDate($_POST['ref_date'])."','$_POST[trace_no]','".$mydb->escapeString(htmlentities($_POST['remarks']))."','$_SESSION[userid]',now());";
			}
			$mydb->dbquery($s);
			echo str_pad($srr_no,6,'0',STR_PAD_LEFT);
		break;

		case "addItem":
			list($isE) = $mydb->getArray("select count(*) from srr_details where trace_no = '$_POST[trace_no]' and item_code = '$_POST[item]' and branch = '$_SESSION[branchid]';");
			if($isE > 0) {
				$s = "update ignore srr_details set qty = qty + ".$mydb->formatDigit($_POST['qty']).", amount = amount + ".$mydb->formatDigit($_POST['amount'])." where trace_no = '$_POST[trace_no]' and item_code = '$_POST[item]' and branch = '$_SESSION[branchid]';";
			} else {
				$s = "insert ignore into srr_details (branch,srr_no,item_code,description,qty,unit,cost,amount,trace_no) values ('$_SESSION[branchid]','$_POST[srr_no]','$_POST[item]','".$mydb->escapeString(htmlentities($_POST['description']))."','".$mydb->formatDigit($_POST['qty'])."','$_POST[unit]','".$mydb->formatDigit($_POST['cost'])."','".$mydb->formatDigit($_POST['amount'])."','$_POST[trace_no]');";
			}
		
			$mydb->dbquery($s);
			updateAmount($_POST['srr_no']);
		break;
		
		case "deleteLine":
			$mydb->dbquery("delete from srr_details where line_id = '$_POST[lid]';");
			updateAmount($_POST['srr_no']);
		break;
	
		case "retrieveLine":
			echo json_encode($mydb->getArray("select *,format(cost,2) as ucost, format(amount,2) as amt from srr_details where line_id = '$_POST[lid]';"));
		break;

		case "updateItem":
			$mydb->dbquery("update srr_details set item_code = '$_POST[item]', description = '".$mydb->escapeString(htmlentities($_POST['description']))."', qty = '".$mydb->formatDigit($_POST['qty'])."', unit = '$_POST[unit]',cost = '".$mydb->formatDigit($_POST['cost'])."', amount = '".$mydb->formatDigit($_POST['amount'])."' where line_id = '$_POST[lid]';");
			updateAmount($_POST['srr_no']);
		break;

		case "check4print":
			list($a) = $mydb->getArray("select count(*) from srr_header where srr_no = '$_POST[srr_no]' and branch = '$_SESSION[branchid]';");
			list($b) = $mydb->getArray("select count(*) from srr_details where srr_no = '$_POST[srr_no]' and branch = '$_SESSION[branchid]';");
			
			if($a == 0 && $b > 0) { echo "head"; }
			if($b == 0 && $a > 0) { echo "det"; }
			if($a == 0 && $b == 0) { echo "both"; }
			if($a > 0 && $b > 0) { echo "noerror"; }
		break;
		case "finalizeSRR":
			$mydb->dbquery("update srr_header set status = 'Finalized', updated_by = '$_SESSION[userid]', updated_on = now() where srr_no ='$_POST[srr_no]' and branch = '$_SESSION[branchid]';");
				
			$iquery = $mydb->dbquery("SELECT a.branch, a.srr_no AS doc_no, a.srr_date AS doc_date, received_from AS customer, b.item_code, b.unit, b.qty FROM srr_header a INNER JOIN srr_details b ON a.srr_no = b.srr_no AND a.branch = b.branch WHERE a.srr_no = '$_POST[srr_no]' AND a.branch = '$_SESSION[branchid]';");
			while($ibook = $iquery->fetch_array(MYSQLI_BOTH)) {
				$mydb->dbquery("INSERT IGNORE INTO ibook (doc_no,doc_date,doc_type,doc_branch,cname,item_code,uom,inbound,posted_by,posted_on) VALUES ('$ibook[doc_no]','$ibook[doc_date]','SRR','$ibook[branch]','".$mydb->escapeString($ibook['customer'])."','$ibook[item_code]','$ibook[unit]','$ibook[qty]','$_SESSION[userid]',now());");
			}
			
		break;
		case "reopenSRR":
			$mydb->dbquery("update srr_header set status = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where srr_no = '$_POST[srr_no]' and branch = '$_SESSION[branchid]';");
			$mydb->dbquery("delete from ibook where doc_no = '$_POST[srr_no]' and doc_branch = '$_SESSION[branchid]' and doc_type = 'SRR';");
		break;
		case "cancel":
			$mydb->dbquery("update srr_header set status = 'Cancelled', updated_by = '$_SESSION[userid]', updated_on = now() where srr_no = '$_POST[srr_no]' and branch = '$_SESSION[branchid]';");
		break;
		case "getTotals":
			list($amt) = $mydb->getArray("select sum(ROUND(qty*cost,2)) as amount from srr_details where srr_no = '$_POST[srr_no]' and branch = '$_SESSION[branchid]';");
			echo json_encode(array("total"=>number_format($amt,2)));
		break;
		case "retrieve":
			$data = array();
	
			$srrd = $mydb->dbquery("SELECT line_id as id, description, item_code, unit, qty, cost, amount FROM srr_details where trace_no = '$_REQUEST[trace_no]';");
			while($row = $srrd->fetch_array()) {

				$data[] = array_map('utf8_encode',$row);
			}
			
			$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
			echo json_encode($results);	
		break;
	}
?>