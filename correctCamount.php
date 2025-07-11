<?php
	ini_set("max_execution_time","0");
	//ini_set("memory_limit",-1);
	include("includes/dbUSE.php");
	
	$a = dbquery("SELECT tmpfileid FROM pos_header WHERE `status` = 'Finalized' AND tmpfileid IN (SELECT DISTINCT tmpfileid FROM pos_details WHERE item_code = '--') ORDER BY trans_id ASC limit 0,100;");
	while(list($tid) = mysql_fetch_array($a)) {
		list($amt) = getArray("select IFNULL(ROUND(SUM(IF(item_code='--','1',qty)*IF(item_code='--',amount,price)),2),0) from pos_details where tmpfileid = '$tid';");
		
		dbquery("update pos_header set amount = 0$amt, balance = 0$amt - applied_amount where tmpfileid = '$tid';");
		echo "Update Transaction ID: $tid, Amount = $amt<br/>";
	}
	
?>