<?php
	session_start();
	include("../includes/dbUSEi.php");
	
	$datares = $con->query("SELECT record_id, parking_no, `status`, tennant, if(contract_start='0000-00-00','',date_format(contract_start,'%m/%d/%Y')) as contract_start, if(contract_end='0000-00-00','',date_format(contract_end,'%m/%d/%Y')) as contract_end FROM citylights.parking WHERE owner_id = '$_GET[owner_id]';");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
		$data[] = array_map('html_entity_decode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>