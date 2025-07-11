<?php
	session_start();
	include("handlers/initDB.php");
	$con = new myDB;

	unset($my_arr);
	unset($my_arr_row);

	$term = trim(strip_tags($_GET['term'])); 
	$r = $con->dbquery("select concat('(',item_code,') ',description) as item, item_code,description,unit_cost,unit from products_master where (locate('$term',description) > 0 or locate('$term',item_code)) and file_status = 'Active' limit 30;");
	$my_arr = array();
	$my_arr_row = array();

	if($r) {
		while($row = $r->fetch_array()) {
			$my_arr_row['item_code'] = $row['item_code'];
			$my_arr_row['value'] = $row['description'];
			$my_arr_row['unit_price'] = $row['unit_cost'];
			$my_arr_row['unit'] = $row['unit'];
			$my_arr_row['label'] = $row['item'];

			array_push($my_arr,$my_arr_row);
		}
	}

	echo json_encode($my_arr);

?>