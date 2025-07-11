<?php
	session_start();
	include("includes/dbUSEi.php");
	
	$term = trim(strip_tags($_GET['term']));
	$datares = $con->query("SELECT acct_code, a.description, b.description AS acctgrp FROM acctg_accounts a INNER JOIN acctg_accountgrps b ON a.acct_grp=b.acct_grp where a.file_status != 'Deleted' and parent != 'Y' and LOCATE('$term', a.description) > 0 LIMIT 15");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  $data[] = array_map('utf8_encode',$row);
	}

	echo json_encode($data);
	@mysqli_close($con);
?>