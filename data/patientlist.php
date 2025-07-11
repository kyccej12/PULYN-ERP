<?php

	include("../handlers/initDB.php");
	$con = new myDB;

	$data = array();
	
	$query = $con->dbquery("SELECT patient_id as pid, lpad(patient_id,6,0) as patient, lname, IF(suffix!='',CONCAT(fname,', ',suffix),fname) AS fname, mname, IF(gender='M','MALE','FEMALE') AS gender, a.street, a.brgy, a.city, a.province, '' as paddress, email_add, mobile_no FROM patient_info a;");
	while($row = $query->fetch_array()) {
		
		$myaddress = "";
		
        list($brgy) = $con->getArray("SELECT brgyDesc FROM options_brgy WHERE brgyCode = '$row[brgy]';");
        list($ct) = $con->getArray("SELECT citymunDesc FROM options_cities WHERE cityMunCode = '$row[city]';");
        list($prov) = $con->getArray("SELECT provDesc FROM options_provinces WHERE provCode = '$row[province]';");
    
        if($row['street'] != '') { $myaddress.=$row['street'].", "; }
        if($brgy != "") { $myaddress.=$brgy.", "; }
        if($ct != "") { $myaddress.=$ct.", "; }
        if($prov != "")  { $myaddress.=$prov.", "; }
        $myaddress = substr($myaddress,0,-2);
		
		$row['paddress'] = strtoupper($myaddress);
		$data[] = array_map('utf8_encode',$row);
	}
	
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	
?>