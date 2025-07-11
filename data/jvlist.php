<?php
	session_start();
	include("../handlers/initDB.php");
	$con = new myDB;

	$data = array();
	$datares = $con->dbquery("select lpad(j_no,6,0) as jno, date_format(j_date,'%m/%d/%Y') as jd8, explanation, `status` from journal_header where branch = '1' order by j_date desc, j_no desc;");
	while($row = $datares->fetch_array()){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>