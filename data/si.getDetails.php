<?php
	session_start();
	include("../includes/dbUSEi.php");
	$datares = $con->query("SELECT line_id, so_no, DATE_FORMAT(so_date,'%m/%d/%Y') AS so_date, item_code, description, qty, unit, cost AS unit_price, amount FROM invoice_details WHERE company = '$_SESSION[company]' AND branch = '$_SESSION[branchid]' AND invoice_no = '$_REQUEST[invoice_no]';");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>