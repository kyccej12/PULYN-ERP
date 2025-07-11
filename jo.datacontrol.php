<?php
	session_start();
	require_once("handlers/_generics.php");
	$con = new _init;
	
	switch($_POST['mod']) {
		
		case "saveDocument":
			list($isE) = $con->getArray("select count(*) from joborder where doc_no = '$_POST[doc_no]';");
			if($isE > 0) {
				$con->dbquery("UPDATE IGNORE joborder set doc_date = '".$con->formatDate($_POST['doc_date'])."',supplier = '$_POST[cid]',supplier_name = '".$con->escapeString(htmlentities($_POST['cname']))."',area = '$_POST[proj]',request_by = '$_POST[request_by]',request_date = '".$con->formatDate($_POST['request_date'])."',request_no = '$_POST[request_no]',scope = '".$con->escapeString(htmlentities($_POST['scope']))."',terms = '$_POST[terms]',amount = '".$con->formatDigit($_POST['amount'])."',expected_date = '".$con->formatDate($_POST['date_needed'])."',updated_by = '$_SESSION[userid]', updated_on = NOW() where doc_no = '$_POST[doc_no]';");
			} else {
				$con->dbquery("INSERT IGNORE INTO joborder (doc_no,doc_date,supplier,supplier_name,area,request_by,request_date,request_no,scope,terms,amount,expected_date,created_by,created_on) VALUES ('$_POST[doc_no]','".$con->formatDate($_POST['doc_date'])."','$_POST[cid]','".$con->escapeString(htmlentities($_POST['cname']))."','$_POST[proj]','$_POST[request_by]','".$con->formatDate($_POST['request_date'])."','$_POST[request_no]','".$con->escapeString(htmlentities($_POST['scope']))."','$_POST[terms]','".$con->formatDigit($_POST['amount'])."','".$con->formatDate($_POST['date_needed'])."','$_SESSION[userid]',NOW());");		
			}
		break;
		
		case "finalize":
			$con->dbquery("UPDATE IGNORE joborder set status = 'Finalized', updated_by = '$_SESSION[userid]', updated_on = now() where doc_no = '$_POST[doc_no]';");
		break;
		
		case "active":
			$con->dbquery("UPDATE IGNORE joborder set status = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where doc_no = '$_POST[doc_no]';");
		break;
		
		case "cancel":
			$con->dbquery("UPDATE IGNORE joborder set status = 'Cancelled', cancelled_by = '$_SESSION[userid]', cancelled_on = now() where doc_no = '$_POST[doc_no]';");
		break;
		
	}
	

?>