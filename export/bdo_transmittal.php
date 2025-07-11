<?php
	//ini_set("display_errors","On");
	ini_set('memory_limit','1024M');
	set_time_limit(0);
	
	require_once '../handlers/_generics.php';
	date_default_timezone_set('Asia/Manila');
	
	session_start();
	
	$con = new _init();
	$cutoff = $_REQUEST['cutoff'];
	$proj = $_REQUEST['proj'];
	
	if($proj != '') { $f1 = " and a.proj = '$proj' "; } else { $f1 = ''; }
	$q = $con->dbquery("SELECT TRIM(BOTH '' FROM LPAD(a.acct_no,12,0)) as bank_acct,net_pay FROM redglobalhris.emp_payslip a left join redglobalhris.emp_masterfile b on a.emp_id = b.emp_id WHERE a.period_id = '$cutoff' AND net_pay > 0 and b.atm_bank = 1 $f1;");
	
	list($batchCode) = $con->getArray("SELECT LPAD('$_REQUEST[batch]','2','0');");
    list($fileName) = $con->getArray("SELECT DATE_FORMAT('".$con->formatDate($_REQUEST['date'])."','%m%d%y');");
	
	$fundAcct = '4190246088';
	$compCode = 'D5K';
	$creditDate = $con->formatDate($_REQUEST['date']);
	
	while($res = $q->fetch_array()){
		echo "$res[bank_acct]\t".number_format($res['net_pay'],2,".","")."\r"."\n";
	}
    header("Content-type: text/plain");
    header("Content-Disposition: attachment; filename=".$compCode."".$fileName."".$batchCode.".txt");

	
?>