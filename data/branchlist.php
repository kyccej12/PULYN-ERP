<?php
	include("../includes/dbUSEi.php");
	$datares = $con->query("SELECT LPAD(branch_code,3,0) AS bcode, branch_name, CONCAT(address,', ',b.city,', ',c.province) AS address, a.tel_no FROM options_branches a LEFT JOIN options_cities b ON a.city=b.city_id LEFT JOIN options_provinces c ON a.province = c.province_id LEFT JOIN companies d ON a.company = d.company_id WHERE company = '1';");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysql_close($con);
?>