<?php
	session_start();
	include("../includes/dbUSEi.php");
	$datares = $con->query("SELECT LPAD(recordID,6,'0') AS docno, DATE_FORMAT(crDate,'%m/%d/%Y') AS cd8, acctName AS cust, remarks, amountPaid as amount, crNo as cr_no, `status` FROM cr_header ORDER BY crDate DESC, recordID DESC;");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>