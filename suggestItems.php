<?php
	session_start();
	include("handlers/initDB.php");
	$con = new myDB;
	

	$term = trim(strip_tags($_GET['term'])); 
	$r = $con->dbquery("select concat('(',item_code,') ',description) as item, item_code,description,walkin_price,unit from products_master where (locate('$term',description) > 0 or locate('$term',barcode) or locate('$term',item_code)) and file_status = 'Active' and `active` = 'N' limit 10");
	$my_arr = array();
	$my_arr_row = array();

	if($r) {
		while($row = $r->fetch_array()) {
			$my_arr_row['item_code'] = $row['item_code'];
			$my_arr_row['value'] = $row['description'];
			$my_arr_row['unit_price'] = $row['unit_price'];
			$my_arr_row['unit'] = $row['unit'];
			$my_arr_row['label'] = $row['item'];

			array_push($my_arr,$my_arr_row);
		}
	}

	echo json_encode($my_arr);
?>