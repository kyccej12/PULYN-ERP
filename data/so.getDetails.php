<?php
	session_start();
	include("../includes/dbUSEi.php");
	$datares = $con->query("SELECT line_id, item_code, description, qty, unit, cost AS unit_price, discount as disc, amount FROM so_details WHERE company = '$_SESSION[company]' AND branch = '$_SESSION[branchid]' AND trace_no = '$_REQUEST[traceno]' union all SELECT '9999999999' as line_id, '' as item_code, '' as description, '' as qty, '' as unit, '' AS unit_price, '' as disc, '' as amount;");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  $data[] = array_map('utf8_encode',$row);
	}
	
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>