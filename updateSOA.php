<?php
	ini_set("max_execution_time","0");
	include("includes/dbUSE.php");
	
	$a = dbquery("SELECT soa_no, customer_code AS customer, date_from, date_to, `type` FROM soa ORDER BY soa_no ASC;");
	while($b = mysql_fetch_array($a)) {
		if($b['type'] == 'F') {
			list($adue) = getArray("select sum(amount_due) from ws_slip where customer = '$b[customer]' and `status` = 'Finalized' and trans_date between '$b[date_from]' and '$b[date_to]';");
		} else {
			list($adue) = getArray("select ifnull(ROUND(sum(if(item_code='--','1',qty)*if(item_code='--',b.amount,price)),2),0) from pos_header a left join pos_details b on a.tmpfileid = b.tmpfileid where a.status = 'Finalized' and a.customer = '$b[customer]' and a.trans_date between '$b[date_from]' and '$b[date_to]';");
		}
		
		/* Applied Amount */
		dbquery("update soa set amount = 0$adue where soa_no = $b[soa_no];");
		echo "update soa set amount = 0$adue where soa_no = $b[soa_no];<br/>";
	}
?>