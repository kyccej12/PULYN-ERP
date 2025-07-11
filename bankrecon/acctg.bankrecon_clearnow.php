<?php
	session_start();
	include("../includes/dbUSE.php");
	
	list($trace_no) = getArray("SELECT LEFT(MD5(RAND()),32) trace_no;");
	dbquery("INSERT IGNORE INTO bankrecon_details (traceNo,docNo,docDate,docType,payee,remarks,debit,credit) SELECT '$trace_no' AS traceNo, doc_no AS docNo, doc_date AS docDate, doc_type AS docType, contact_id AS payee, doc_remarks AS remarks, debit, credit FROM acctg_gl WHERE tmp_cleared = 'Y' and acct = '$_POST[acct_code]' and branch = '1';");
	dbquery("INSERT IGNORE INTO bankrecon_header (traceNo,bankAcct,dateReconciled,openingBalance,endingBalance,reconciledBy,reconciledOn) VALUES ('$trace_no','$_POST[acct_code]','".formatDate($_POST['date'])."','".formatDigit($_POST['balOpen'])."','".formatDigit($_POST['balend'])."','$_SESSION[userid]',now());");
	
	
	mysql_query("update acctg_gl set cleared = 'Y',cleared_on=now(), cleared_by = '$_SESSION[userid]' where tmp_cleared = 'Y' and acct = '$_POST[acct_code]' and branch = '1';");
	mysql_query("update acctg_gl set tmp_cleared = 'N' where tmp_cleared = 'Y' and acct = '$_POST[acct_code]' and branch = '1';");
	
	echo $trace_no;
	
	mysql_close($con);
?>