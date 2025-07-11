<?php
	session_start();
	require_once '../handlers/initDB.php';
	$db = new myDB;

	unset($my_arr);
	unset($my_arr_row);
	
	$term = trim(strip_tags($_GET['term'])); 
	$r = $db->dbquery("SELECT position from omdcpayroll.emp_positions WHERE LOCATE('$term', `position`) > 0 LIMIT 20");
	$my_arr = array();
	$my_arr_row = array();
	if($r) {
		while($row = $r->fetch_array(MYSQLI_ASSOC)) {
			$my_arr_row['value'] = $row['position'];
			$my_arr_row['label'] = $row['position'];
			array_push($my_arr,$my_arr_row);
		}
	}

	echo json_encode($my_arr);
?>