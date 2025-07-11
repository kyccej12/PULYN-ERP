<?php
	session_start();
	include("includes/dbUSE.php");
	include("functions/adj.displayDetails.fnc.php");

	@mysql("START TRANSACTION");
	function updateHeaderAmt($doc_no) {
		list($amt) = getArray("select sum(amount) from adj_details where doc_no = '$doc_no' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
		dbquery("update ignore adj_header set amount = '$amt' where doc_no = '$doc_no' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
	}

	switch($_POST['mod']) {
		case "saveHeader":
			list($isE) = getArray("select count(*) from adj_header where doc_no = '$_POST[doc_no]' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
			if($isE > 0) {
				$s = "update ignore adj_header set cid = '$_POST[cid]', cname = '".mysql_real_escape_string(htmlentities($_POST['cname']))."', caddr = '".mysql_real_escape_string(htmlentities($_POST['addr']))."', doc_date = '".formatDate($_POST['doc_date'])."', adjustment_type = '$_POST[adj_type]', requested_by='".mysql_real_escape_string(htmlentities($_POST['requested_by']))."', ref_type = '$_POST[ref_type]', ref_no='$_POST[ref_no]', ref_date='".formatDate($_POST['ref_date'])."', remarks = '".mysql_real_escape_string(htmlentities($_POST['remarks']))."', updated_by = '$_SESSION[userid]', updated_on = now() where doc_no = '$_POST[doc_no]' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';";
			} else {
				$s = "insert ignore into adj_header (company, branch, doc_no, doc_date, cid, cname, caddr, adjustment_type, requested_by, ref_type, ref_no, ref_date, remarks, created_by, created_on) values ('$_SESSION[company]','$_SESSION[branchid]','$_POST[doc_no]','".formatDate($_POST['doc_date'])."','$_POST[cid]','".mysql_real_escape_string(htmlentities($_POST['cname']))."','".mysql_real_escape_string(htmlentities($_POST['addr']))."','$_POST[adj_type]','".mysql_real_escape_string(htmlentities($_POST['requested_by']))."','$_POST[ref_type]','$_POST[ref_no]','".formatDate($_POST['ref_date'])."','".mysql_real_escape_string(htmlentities($_POST['remarks']))."','$_SESSION[userid]',now());";
			}
			echo $s;
			dbquery($s);
		break;
		case "insertDetail":
			dbquery("insert ignore into adj_details (company,branch,doc_no,item_code,description,qty,unit,cost,amount) values ('$_SESSION[company]','$_SESSION[branchid]','$_POST[doc_no]','$_POST[icode]','".mysql_real_escape_string($_POST['desc'])."','".formatDigit($_POST['qty'])."','$_POST[unit]','".formatDigit($_POST['price'])."','".formatDigit($_POST['amount'])."');");
			updateHeaderAmt($_POST['doc_no']);
			ADJDETAILS($_POST['doc_no']);
		break;
		case "deleteDetails":
			dbquery("delete from adj_details where line_id = '$_POST[lid]';");
			updateHeaderAmt($_POST['doc_no']);
			ADJDETAILS($_POST['doc_no']);
		break;
		case "usabQty":
			$amt = ROUND(formatDigit($_POST['price']) * formatDigit($_POST['val']),2);
			dbquery("update adj_details set qty = '".formatDigit($_POST['val'])."', amount = '$amt' where line_id = '$_POST[lid]';");
			updateHeaderAmt($_POST['doc_no']);

			list($amtGT) = getArray("select sum(amount) from adj_details where doc_no = '$_POST[doc_no]' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
			echo json_encode(array('amt1' => number_format($amt,2), 'amt2' => number_format($amtGT,2)));
		break;
		case "check4print":
			list($a) = getArray("select count(*) from adj_header where doc_no = '$_POST[doc_no]' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
			list($b) = getArray("select count(*) from adj_details where doc_no = '$_POST[doc_no]' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
			
			if($a == 0 && $b > 0) { echo "head"; }
			if($b == 0 && $a > 0) { echo "det"; }
			if($a == 0 && $b == 0) { echo "both"; }
			if($a > 0 && $b > 0) { echo "noerror"; }
		break;
		case "finalizeADJ":
			dbquery("update ignore adj_header set status = 'Finalized', updated_by = '$_SESSION[userid]', updated_on = now() where doc_no ='$_POST[doc_no]' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
			
		break;
		case "reopenADJ":
			dbquery("update ignore adj_header set status = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where doc_no = '$_POST[doc_no]' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
		break;
		case "cancel":
			dbquery("update ignore adj_header set status = 'Cancelled', updated_by = '$_SESSION[userid]', updated_on = now() where doc_no = '$_POST[doc_no]' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
		break;
	}
	@mysql_query("COMMIT");
	mysql_close($con);

?>