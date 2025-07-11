<?php
	ini_set("display_errors","On");
	include("../includes/dbUSE.php");
	
	$_idetails = dbquery("select * from (select b.emp_idno as id_no, ws_no as lid, trans_date as xdate,  date_format(trans_date,'%m/%d/%Y') as deyt, amount_due as amt, balance, '' as remarks, 'FUEL' as `type` from ws_slip a left join contact_info b on a.customer=b.file_id where a.status = 'Finalized' and a.balance > 0 and b.emp_idno != '' union all select b.emp_idno as id_no, trans_id as lid, trans_date as xdate, date_format(trans_date,'%m/%d/%Y') as deyt, amount as amt, balance, '' as remarks, 'POS' as `type` from pos_header a left join contact_info b on a.customer=b.file_id where a.status = 'Finalized' and a.balance > 0 and b.emp_idno != '') a order by xdate asc");
	while($row = mysql_fetch_array($_idetails)) {
		switch($row['type']) {
			case "POS":
				list($applied) = getArray("select ifnull(sum(amount),0) from e_paydeductions where loan_id = '$row[lid]' and id_no = '$row[id_no]' and `type` = 'POS';");
				dbquery("update pos_header set applied_amount = applied_amount + 0$applied, balance = amount - 0$applied where trans_id = '$row[lid]';");
				echo "update pos_header set applied_amount = applied_amount + 0$applied, balance = amount - 0$applied where trans_id = '$row[lid]'<br/>";
			break;
			case "FUEL":
				list($applied) = getArray("select ifnull(sum(amount),0) from e_paydeductions where loan_id = '$row[lid]' and id_no = '$row[id_no]' and `type` = 'FUEL';");
				dbquery("update ws_slip set applied_amount = applied_amount + 0$applied, balance = amount_due - 0$applied where ws_no = '$row[lid]';");
				echo "update ws_slip set applied_amount = applied_amount + 0$applied, balance = amount_due - 0$applied where ws_no = '$row[lid]'<br/>";
			break;
		}
	}
	
?>