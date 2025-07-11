<?php
	session_start();
	include("../includes/dbUSEi.php");
	$data = array();
	
	$datares = $con->query("SELECT lpad(cv_no,6,0) as cv, DATE_FORMAT(cv_date,'%m/%d/%Y') AS cd8, CONCAT('(',payee,') ',payee_name) AS payee, ca_refno, remarks, amount, if(source='10100','',check_no) as check_no, IF(check_date='0000-00-00','',DATE_FORMAT(check_date,'%m/%d/%Y')) AS ckdate, `status` FROM cv_header WHERE branch = '1' order by cv_date desc;");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>