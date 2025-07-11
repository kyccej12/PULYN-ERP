<?php
	session_start();
	include("../includes/dbUSEi.php");
	$data = array();
	
	$searchString = '';
	
	if(isset($_GET['sname']) && $_GET['sname'] != '') { $searchString .= " and supplier_name like '%$_GET[cname]%' "; }
	if(isset($_GET['dtf']) && $_GET['dtf']!="") { $searchString .= " and doc_date between '$_GET[dtf]' and '$_GET[dt2]' "; }
	if(isset($_GET['doc_date']) && $_GET['doc_date'] != "") { $searchString .= " and doc_date = '$_GET[doc_date]' "; }
	if(isset($_GET['scope']) && $_GET['scope'] != '') { $searchString .= " and scope like '%$_GET[scope]%' "; }
	
	$datares = $con->query("SELECT doc_no, LPAD(doc_no,6,0) AS docno, DATE_FORMAT(doc_date,'%m/%d/%Y') AS d8, CONCAT('(',supplier,') ',supplier_name) AS supplier, b.proj_name AS area, scope, amount, STATUS AS doc_status, scope_status, doc_date FROM joborder a LEFT JOIN options_project b ON a.area = b.proj_id WHERE 1=1 $searchString;");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
		$data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>