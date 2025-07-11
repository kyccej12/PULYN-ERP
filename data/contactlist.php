<?php

	include("../handlers/initDB.php");
	$con = new myDB;

	$data = array();
	
	$query = $con->dbquery("SELECT LPAD(file_id,6,0) AS cid, `contacttype` AS ctype, tradename AS cname, a.address, a.brgy, a.city, a.province, a.type, '' as caddress, a.billing_address, tel_no AS ctelno, cperson FROM contact_info a left join options_ctype b on a.type = b.id WHERE record_status != 'Deleted';");
	while($row = $query->fetch_array()) {
		
		$myaddress = "";
		if($row['type'] != 7) {
			list($brgy) = $con->getArray("SELECT brgyDesc FROM options_brgy WHERE brgyCode = '$row[brgy]';");
			list($ct) = $con->getArray("SELECT citymunDesc FROM options_cities WHERE cityMunCode = '$row[city]';");
			list($prov) = $con->getArray("SELECT provDesc FROM options_provinces WHERE provCode = '$row[province]';");
		
			if($row['address'] != '') { $myaddress.=$row['address'].", "; }
			if($brgy != "") { $myaddress.=$brgy.", "; }
			if($ct != "") { $myaddress.=$ct.", "; }
			if($prov != "")  { $myaddress.=$prov.", "; }
			$myaddress = substr($myaddress,0,-2);
		} else {
			$myaddress = $row['billing_address'];
		}	
		
		$row['caddress'] = strtoupper($myaddress);
		$data[] = array_map('utf8_encode',$row);
	}
	
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	
?>