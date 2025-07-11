<?php
	ini_set("max_execution_time",0);
	session_start();
	include("includes/dbUSE.php");
	
	dbquery("delete from sjpi.acctg_gl where doc_date between '2016-10-01' and '2016-10-12' and company = '2' and branch = '6' and doc_type = 'POS';");
	
	$a = dbquery("select tmpfileid from sjpi.pos_header where trans_date between '2016-10-01' and '2016-10-12' and company = '2' and branch = '6';");
	while(list($tid) = mysql_fetch_array($a)) {
		dbquery("update pos_header set `status` = 'Cancelled' where tmpfileid = '$tid';");
		echo "Cancelling POS TRANSACTION ID $tid<br/>";
	}
	
?>