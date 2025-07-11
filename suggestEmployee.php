<?php
	session_start();
	require_once 'handlers/initDB.php';
	$db = new myDB;

	unset($my_arr);
	unset($my_arr_row);
	
	$term = trim(strip_tags($_GET['term'])); 
	$r = $db->dbquery("SELECT emp_id, fullname, username FROM user_info WHERE (LOCATE('$term', fullname) > 0 OR LOCATE('$term', username) > 0) AND role NOT LIKE '%tech%' AND license_no = '' LIMIT 10;");
	$my_arr = array();
	$my_arr_row = array();
		while($row = $r->fetch_array(MYSQLI_ASSOC)) {
			$my_arr_row['emp_id'] = $row['emp'];
			$my_arr_row['fullname'] = $row['fullname'];
			$my_arr_row['username'] = $row['uname'];
			$my_arr_row['value'] = strtoupper($row['fullname']);
			$my_arr_row['label'] = strtoupper($row['fullname']);
			array_push($my_arr,$my_arr_row);
		}

	echo json_encode($my_arr);
?>