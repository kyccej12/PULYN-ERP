<?php
	include("handlers/initDB.php");
	session_start();

	$con = new myDB;

	unset($my_arr);
	unset($my_arr_row);

	$term = trim(strip_tags($_GET['term'])); 
	$r = $con->dbquery("SELECT tradename, a.address, a.billing_address, a.brgy, a.city, a.province, a.tin_no FROM contact_info a WHERE LOCATE('$term',tradename) > 0 and a.record_status != 'Deleted';");
	
	$my_arr = array();
	$my_arr_row = array();

	if($r) {
		while($row = $r->fetch_array()) {
	
			$myaddress = "";
			if($row['type'] != "FSUPPLIER") {
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
	
			$cname = html_entity_decode($row['tradename']);
			$addr = html_entity_decode($myaddress);
			$label = html_entity_decode($row['label']);

			$my_arr_row['tin_no'] = $row['tin_no'];
			$my_arr_row['addr'] = $addr;
			$my_arr_row['label'] = $cname;
			$my_arr_row['value'] = $cname;

			array_push($my_arr,$my_arr_row);
		}
	}

	echo json_encode($my_arr);

?>