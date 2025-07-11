<?php
	session_start();
	include("../handlers/initDB.php");
	$con = new myDB;

	$data = array();
	
	$datares = $con->dbquery("SELECT soa_no, LPAD(soa_no,6,0) AS soano, date_format(soa_date,'%m/%d/%Y') as sd8, concat('(',lpad(customer_code,6,'0'),') ',customer_name) as customer_name, remarks, b.description AS terms_desc, FORMAT(amount,2) AS amount, FORMAT(amount_paid,2) AS paid, FORMAT(balance,2) AS balance, `status` FROM soa_header a LEFT JOIN options_terms b ON a.terms = b.terms_id WHERE branch = '$_SESSION[branchid]';");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>