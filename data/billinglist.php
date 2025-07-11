<?php
	session_start();
	include("../includes/dbUSEi.php");
	
	$datares = $con->query("SELECT recordID,billingNo,DATE_FORMAT(billingDate,'%m/%d/%Y') AS billDate,acctName,concat('Tower ',tower,' - ',unit) as towerUnit, balanceDue,`status` FROM billing;");
	
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
		$data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>