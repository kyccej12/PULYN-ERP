<?php
	session_start();
	include("../includes/dbUSEi.php");
	
	$datares = $con->query("SELECT record_id, nickname, `type`, color, '' as last_vaccine_date, '' as vaccine_type FROM citylights.pets WHERE owner_id = '$_GET[owner_id]';");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
		$data[] = array_map('html_entity_decode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>