<?php
	ini_set("max_execution_time","0");
	include("includes/dbUSE.php");
	
	$a = dbquery("select ws_no, customer, customer_name, amount_due from ws_slip a left join contact_info b on a.customer = b.file_id where status = 'Finalized' and b.type != 'EMPLOYEE' and a.terms > 0;");
	
	while(list($ws_no,$cid,$cname,$amt) = mysql_fetch_array($a)) {
		list($applied) = getArray("SELECT ifnull(SUM(amount_paid),0) FROM cr_header a LEFT JOIN cr_details b ON a.trans_no = b.trans_no WHERE a.status = 'Finalized' AND b.doc_no = '$ws_no' AND doc_type = 'FUEL' GROUP BY b.doc_no;");
		echo "TRANS NO: $ws_no<br/>CUSTOMER: ($cid) $cname)<br/>AMOUNT DUE: $amt<br/>AMOUNT PAID: $applied<br/><br/>";
		dbquery("update ws_slip set applied_amount = 0$applied, balance = amount_due - 0$applied where ws_no = '$ws_no';");
		$applied = 0;
	}
	
	$b = dbquery("SELECT trans_id, customer, customer_name, amount, balance FROM pos_header WHERE `status` = 'Finalized' AND terms > 0 AND customer NOT IN (SELECT file_id FROM contact_info WHERE `type` = 'EMPLOYEE') ORDER BY trans_id;");
	while(list($tid,$cid,$cname,$amt,$bal) = mysql_fetch_array($b)) {
		list($applied) = getArray("SELECT IFNULL(amount_paid,0) FROM cr_details WHERE doc_no = '$tid' AND doc_type = 'POS';");
		echo "TRANS NO: C$tid<br/>CUSTOMER: ($cid) $cname)<br/>AMOUNT DUE: $amt<br/>AMOUNT PAID: $applied<br/><br/>";
		dbquery("update pos_header set applied_amount = 0$applied, balance = amount - 0$applied where trans_id = '$tid';");
		$applied = 0;
	}
	
	mysql_close($con);
?>