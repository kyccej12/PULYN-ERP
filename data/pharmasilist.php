<?php
	session_start();
	include("../handlers/initDB.php");
	$con = new myDB;

	$data = array();
	
	$datares = $con->dbquery("SELECT LPAD(doc_no,6,0) AS doc_no, DATE_FORMAT(doc_date,'%m/%d/%Y') AS d8, lpad(si_no,6,0) as si_no, customer_name AS cname, b.description AS terms, remarks, amount_due, `status` FROM pharma_si_header a LEFT JOIN options_terms b ON a.terms = b.terms_id;");
	while($row = $datares->fetch_array()){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>