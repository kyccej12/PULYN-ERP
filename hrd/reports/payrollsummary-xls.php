<?php
	require_once '../../handlers/initDB.php';
	$con = new myDB;
	
	list($wom) = $con->getArray("select weekOfMonth from omdcpayroll.pay_periods where period_id = '$_GET[cutoff]';");
	
	if($wom == 1) {
		header("Location: payrollsummary-xls-15.php?cutoff=".$_GET['cutoff']."&proj=".$_GET['proj']);
	} else {
		header("Location: payrollsummary-xls-30.php?cutoff=".$_GET['cutoff']."&proj=".$_GET['proj']);
	}
	

?>