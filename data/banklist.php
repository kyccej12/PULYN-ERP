<?php
	include("../includes/dbUSEi.php");
	
	$data = array();
	
	$datares = $con->query("SELECT LPAD(bank_id,3,0) AS bid, bank_name, bank_address, tel_no, IF(acct_type='SA','Savings Account','Current Account') AS acct_type, CONCAT('(',gl_acct,') ',b.description) AS acct FROM acctg_bankaccounts a LEFT JOIN acctg_accounts b ON a.gl_acct=b.acct_code AND a.company=b.company WHERE a.company = '1';;");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>