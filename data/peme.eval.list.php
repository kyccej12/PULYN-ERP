<?php
	session_start();
	include("../handlers/_generics.php");
	$con = new _init;
	$searchString = '';

	if($_SESSION['so_no'] != '') { $searchString .= " and so_no = '$_SESSION[so_no]' "; }
	//if(isset($_SESSION['clinic_no']) && $_SESSION['clinic_no'] != '') { $searchString .= " and a.clinic = '$_SESSION[clinic_no]' "; }

	$data = array();
	$datares = $con->dbquery("SELECT LPAD(prio,6,0) AS prio, LPAD(so_no,6,0) AS so, DATE_FORMAT(so_date,'%m/%d/%Y') AS sodate, `code`, `procedure`, CONCAT(b.lname,', ',b.fname,', ',b.mname) AS pname, b.gender, DATE_FORMAT(b.birthdate,'%m/%d/%Y') AS bday, FLOOR(DATEDIFF(so_date,b.birthdate)/364.25) AS age, compname, a.clinic, a.so_date,prio AS `priority`,b.birthdate, a.pid, CONCAT(c.fullname,', ',c.prefix) AS ex_by, CONCAT(d.fullname,', ',d.prefix) AS ev_by FROM peme a LEFT JOIN patient_info b ON a.pid = b.patient_id LEFT JOIN options_doctors c ON a.examined_by = c.id LEFT JOIN options_doctors d ON a.evaluated_by = d.id WHERE 1=1 AND a.examined_by > 0 AND a.evaluated_by IS NULL;");

    while($row = $datares->fetch_array(MYSQLI_ASSOC)){
        //$row['age'] =  $con->calculateAge($row['so_date'],$row['birthdate']);
		$row['compname'] = html_entity_decode($row['compname']);
		$row['pname'] = html_entity_decode($row['pname']);
        $data[] = array_map('utf8_encode',$row);

	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>