<?php
	session_start();
	include("../includes/dbUSEi.php");
	
	$datares = $con->query("SELECT record_id, owner_id, h_lname, h_fname, h_mname, h_date, h_dsg FROM citylights.household_workers WHERE owner_id = '$_REQUEST[owner_id]';");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  
	  $data[] = array_map('html_entity_decode',$row);
	 
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>