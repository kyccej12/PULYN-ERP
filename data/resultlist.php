<?php
	session_start();
	include("../handlers/_generics.php");

	ini_set('max_execution_time',0);
	ini_set('memory_limit',-1);
	
	$con = new _init;
	$today = date('Y-m-d');

	list($baseD8) = $con->getArray("SELECT DATE_SUB('$today', INTERVAL 1 MONTH);");

	if($_REQUEST['displayPending'] != 'Y') {
		$f = " and b.so_date >= '$baseD8' ";
	} else { $f = " and b.so_date < '$baseD8' "; }

	$data = array();
	$datares = $con->dbquery("SELECT a.record_id AS id, LPAD(b.priority_no,6,0) AS priority, LPAD(a.so_no,6,0) AS sono, DATE_FORMAT(b.so_date,'%m/%d/%Y') AS sodate, b.patient_name AS pname, '' AS age, IF(c.gender='M','Male','Female') AS gender,a.procedure, IF(a.released='Y','Yes','No') AS released, d.fullname AS rby, IF(release_date IS NOT NULL,DATE_FORMAT(release_date,'%m/%d/%Y'),'') AS rdate, release_mode, released_to,a.code, a.serialno,with_file, file_path, a.is_consolidated, b.so_date as xorderdate, c.birthdate as xbday FROM lab_samples a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN user_info d ON a.released_by = d.emp_id WHERE a.status = '4' AND a.branch = '$_SESSION[branchid]' $f ORDER BY so_date DESC;");
	
    while($row = $datares->fetch_array()){

		$con->calculateAge2($row['xorderdate'],$row['xbday']);
		$row['age'] = $con->ageDisplay;
        $data[] = array_map('utf8_encode',$row);

	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>