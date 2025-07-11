<?php
	require_once '../../handlers/initDB.php';
	$con = new myDB;
	
	list($wom) = $con->getArray("select weekOfMonth from omdcpayroll.pay_periods where period_id = '$_GET[cutoff]';");
	if($wom == 1) {
		header("Location: payrollsummary-15.php?cutoff=".$_GET['cutoff']."&dept=".$_GET['dept']);
	} else {
		header("Location: payrollsummary-30.php?cutoff=".$_GET['cutoff']."&dept=".$_GET['proj']);
	}
	

?>