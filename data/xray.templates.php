<?php
	session_start();
	include("../handlers/initDB.php");
	$con = new myDB;

	$data = array();

	$datares = $con->dbquery("SELECT a.id, a.template_category, title, if(xray_type=1,'Upper Extremities','Lower Extremities') as xray_type, c.fullname as template_owner, DATE_FORMAT(a.created_on, '%m/%d/%Y %r') AS created, DATE_FORMAT(a.updated_on, '%m/%d/%Y %r') AS updated, b.fullname as uby, a.status FROM xray_templates a LEFT JOIN user_info b ON a.created_by = b.emp_id left join options_doctors c on a.template_owner = c.id ORDER BY c.fullname, title;");
	while($row = $datares->fetch_array()){
		$data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
?>