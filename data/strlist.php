<?php
	session_start();
	include("../includes/dbUSEi.php");
	$datares = $con->query("SELECT LPAD(str_no,6,0) AS str, DATE_FORMAT(str_date,'%m/%d/%Y') AS sdate, b.branch_name, remarks, amount, `status` FROM str_header a LEFT JOIN options_branches b ON a.transferred_to = b.branch_code WHERE a.branch = '$_SESSION[branchid]';");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>