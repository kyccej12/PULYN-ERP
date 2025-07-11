<?php
	session_start();
	include("../includes/dbUSEi.php");
	
	$data = array();
	
	$datares = $con->query("SELECT a.record_id,id_no,lname,fname,mname,`designation` FROM cg_hrd.e_master a ORDER BY lname, fname, mname;");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>