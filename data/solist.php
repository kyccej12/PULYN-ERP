<?php
	session_start();
	include("../handlers/initDB.php");
	$con = new myDB;

	$data = array();
	
	$datares = $con->dbquery("SELECT so_no, soa_no, LPAD(so_no,6,0) AS myso, DATE_FORMAT(so_date,'%m/%d/%Y') AS sdate, patient_name, IF(customer_code=0,concat(patient_name, '(Patient)'),customer_name) AS customer, b.description AS terms_desc, remarks, FORMAT(amount,2) AS amount,`status`,c.sostatus AS cstat FROM so_header a LEFT JOIN options_terms b ON a.terms = b.terms_id LEFT JOIN options_sostatus c ON a.cstatus = c.id WHERE branch = '$_SESSION[branchid]';");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>