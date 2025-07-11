<?php
	session_start();
	include("../handlers/initDB.php");
	$con = new myDB;

	$data = array();
	
	//$datares = $con->dbquery("SELECT LPAD(so_no,6,0) AS so, DATE_FORMAT(so_date,'%m/%d/%Y') AS d8, patient_name as pname, customer_name AS cname, b.description AS terms, remarks, amount_due as amount, `status` FROM pharma_so_header a LEFT JOIN options_terms b ON a.terms = b.terms_id;");
	$datares = $con->dbquery("SELECT LPAD(so_no,6,0) AS so, lpad(csi_no,6,0) as csi, DATE_FORMAT(so_date,'%m/%d/%Y') AS d8, patient_name as pname, customer_name AS cname, b.description AS terms, remarks, amount_due as amount, `status` FROM pharma_so_header a LEFT JOIN options_terms b ON a.terms = b.terms_id;");
	while($row = $datares->fetch_array()){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>