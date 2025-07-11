<?php
	session_start();
	include("../handlers/initDB.php");
	$con = new myDB;
	
	$data = array();
	$datares = $con->dbquery("select lpad(rr_no,6,0) as rr, date_format(rr_date,'%m/%d/%Y') as rdate, supplier_name, remarks, amount, status, if(apv_no!='',concat('<a href=\"#\" onclick=\"parent.viewAP(',apv_no,');\" style=\"text-decoration: none;\">','AP-',lpad(apv_no,6,0),'</a>'),'') as apv from pharma_rr_header where branch = '1';");
	
	while($row = $datares->fetch_array()){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
?>