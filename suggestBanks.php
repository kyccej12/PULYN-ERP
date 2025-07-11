<?php
	session_start();
	include("handlers/initDB.php");
	$con = new myDB;

	unset($my_arr);
	unset($my_arr_row);

	$term = trim(strip_tags($_GET['term'])); 
	$r = $con->dbquery("SELECT bank_name FROM options_banks WHERE LOCATE('$term',bank_name) > 0");
	$my_arr = array();
	$my_arr_row = array();

	if($r) {
		while($row = $r->fetch_array()) {
			$my_arr_row['value'] = $row['bank_name'];
			$my_arr_row['label'] = $row['bank_name'];
			array_push($my_arr,$my_arr_row);
		}
	}

	echo json_encode($my_arr);
?>