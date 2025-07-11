<?php
	session_start();
	include("includes/dbUSE.php");
	include("functions/beg.displayDetails.fnc.php");
	mysql_query("START TRANSACTION");

	switch($_POST['mod']) {
		case "saveARBHeader":
			list($count) = getArray("select count(*) from arbeg_header where company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
			if($count > 0) {
				dbquery("update arbeg_header set cy='".formatCY($_POST['doc_date'])."', doc_date = '".formatDate($_POST['doc_date'])."', explanation = '".mysql_real_escape_string($_POST['remarks'])."', updated_by='$_SESSION[userid]', updated_on=now() where company ='$_SESSION[company]' and branch = '$_SESSION[branchid]';");
			} else {
				dbquery("insert ignore into arbeg_header (company,branch,cy,doc_no,doc_date,explanation,created_by,created_on) values ('$_SESSION[company]','$_SESSION[branchid]','".formatCY($_POST['doc_date'])."','1','".formatDate($_POST['doc_date'])."','".mysql_real_escape_string($_POST['remarks'])."','$_SESSION[userid]',now());");
			}
		break;
		case "saveARDetails":
			dbquery("insert ignore into arbeg_details (company,branch,cy,doc_no,customer,customer_name,invoice_no,invoice_date,po_no,po_date,amount,balance) values ('$_SESSION[company]','$_SESSION[branchid]','".formatCY($_POST['doc_date'])."','1','$_POST[cid]','".mysql_real_escape_string(htmlentities($_POST['cname']))."','$_POST[inv_no]','".formatDate($_POST['inv_date'])."','$_POST[po_no]','".formatDate($_POST['po_date'])."','".formatDigit($_POST['amount'])."','".formatDigit($_POST['amount'])."');");
			ARDETAILS($_SESSION['company'],$_SESSION['branchid']);
		break;
		case "deleteARLine":
			dbquery("delete from arbeg_details where line_id = '$_POST[lid]';");
			ARDETAILS($_SESSION['company'],$_SESSION['branchid']);
		break;
		case "finalizeARB":
			dbquery("insert ignore into acctg_gl (company, branch, cy, doc_no, doc_date, doc_type, contact_id, acct_branch, acct, debit, credit, cost_center, doc_remarks, posted_by, posted_on) select a.company, a.branch, a.cy, a.doc_no, a.doc_date, 'BB' as doc_type, b.customer as contact_id, '$_SESSION[branchid]', '1101' as acct, sum(b.amount) as debit, 0 as credit, '' as cost_center, a.explanation as doc_remarks, '$_SESSION[userid]' as posted_by, now() as posted_on from arbeg_header a left join arbeg_details b on a.doc_no=b.doc_no and a.company=b.company and a.branch=b.branch where a.company='$_SESSION[company]' and a.branch='$_SESSION[branchid]' group by b.customer;");
			dbquery("insert ignore into acctg_gl (company, branch, cy, doc_no, doc_date, doc_type, contact_id, acct_branch, acct, debit, credit, cost_center, doc_remarks, posted_by, posted_on) select a.company, a.branch, a.cy, a.doc_no, a.doc_date, 'BB' as doc_type, '0' as contact_id, '$_SESSION[branchid]', '3001' as acct, 0 as debit, sum(b.amount) as credit, '' as cost_center, a.explanation as doc_remarks, '' as posted_by, now() as posted_on from arbeg_header a left join arbeg_details b on a.doc_no=b.doc_no and a.company=b.company and a.branch=b.branch where a.company='$_SESSION[company]' and a.branch='$_SESSION[branchid]';");
			dbquery("update ignore arbeg_header set `status`='Posted', updated_by = '$_SESSION[userid]', updated_on = now() where company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
		break;
		case "reOPENARB":
			dbquery("delete from acctg_gl where doc_no = '1' and company='$_SESSION[company]' and branch = '$_SESSION[branchid]' and doc_type = 'BB';");
			dbquery("update arbeg_header set `status`='Active', updated_by = '$_SESSION[userid]', updated_on = now() where company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
		break;
	
		/* AP */
		case "saveAPBHeader":
			list($count) = getArray("select count(*) from apbeg_header where company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
			if($count > 0) {
				dbquery("update apbeg_header set cy='".formatCY($_POST['doc_date'])."', doc_date = '".formatDate($_POST['doc_date'])."', explanation = '".mysql_real_escape_string($_POST['remarks'])."', updated_by='$_SESSION[userid]', updated_on=now() where company ='$_SESSION[company]' and branch = '$_SESSION[branchid]';");
			} else {
				dbquery("insert ignore into apbeg_header (company,branch,cy,doc_no,doc_date,explanation,created_by,created_on) values ('$_SESSION[company]','$_SESSION[branchid]','".formatCY($_POST['doc_date'])."','1','".formatDate($_POST['doc_date'])."','".mysql_real_escape_string($_POST['remarks'])."','$_SESSION[userid]',now());");
			}
		break;
		case "saveAPDetails":
			dbquery("insert ignore into apbeg_details (company,branch,cy,doc_no,customer,customer_name,invoice_no,invoice_date,po_no,po_date,amount,balance) values ('$_SESSION[company]','$_SESSION[branchid]','".formatCY($_POST['doc_date'])."','1','$_POST[cid]','".mysql_real_escape_string(htmlentities($_POST['cname']))."','$_POST[inv_no]','".formatDate($_POST['inv_date'])."','$_POST[po_no]','".formatDate($_POST['po_date'])."','".formatDigit($_POST['amount'])."','".formatDigit($_POST['amount'])."');");
			APDETAILS($_SESSION['company'],$_SESSION['branchid']);
		break;
		case "deleteAPLine":
			dbquery("delete from apbeg_details where line_id = '$_POST[lid]';");
			APDETAILS($_SESSION['company'],$_SESSION['branchid']);
		break;
		case "finalizeAPB":
			dbquery("insert ignore into acctg_gl (company, branch, cy, doc_no, doc_date, doc_type, contact_id, acct, debit, credit, cost_center, doc_remarks, posted_by, posted_on) select a.company, a.branch, a.cy, a.doc_no, a.doc_date, 'APB' as doc_type, b.customer as contact_id, '2001' as acct,  0 as debit, sum(b.amount) as credit, '' as cost_center, a.explanation as doc_remarks, '$_SESSION[userid]' as posted_by, now() as posted_on from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.company=b.company and a.branch=b.branch where a.company='$_SESSION[company]' and a.branch='$_SESSION[branchid]' group by b.customer;");
			dbquery("insert ignore into acctg_gl (company, branch, cy, doc_no, doc_date, doc_type, contact_id, acct, debit, credit, cost_center, doc_remarks, posted_by, posted_on) select a.company, a.branch, a.cy, a.doc_no, a.doc_date, 'APB' as doc_type, '0' as contact_id, '3001' as acct, sum(b.amount) as debit, 0 as credit, '' as cost_center, a.explanation as doc_remarks, '' as posted_by, now() as posted_on from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.company=b.company and a.branch=b.branch where a.company='$_SESSION[company]' and a.branch='$_SESSION[branchid]';");
			dbquery("update ignore apbeg_header set `status`='Posted', updated_by = '$_SESSION[userid]', updated_on = now() where company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
		break;
		case "reOPENAPB":
			dbquery("delete from acctg_gl where doc_no = '1' and company='$_SESSION[company]' and branch = '$_SESSION[branchid]' and doc_type = 'APB';");
			dbquery("update apbeg_header set `status`='Active', updated_by = '$_SESSION[userid]', updated_on = now() where company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
		break;

	}
	mysql_query("COMMIT");
	mysql_close($con);
?>