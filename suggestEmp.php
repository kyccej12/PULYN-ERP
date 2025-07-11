<?php
	session_start();
	require_once 'handlers/initDB.php';
	$db = new myDB;

	unset($my_arr);
	unset($my_arr_row);
	
	$term = trim(strip_tags($_GET['term'])); 
	$r = $db->dbquery("SELECT emp_id, CONCAT(lname,', ',fname,LEFT(mname,1),'.') AS emp_name, b.dept_name FROM redglobalhris.`emp_masterfile` a LEFT JOIN redglobalhris.`options_dept` b ON a.dept = b.id WHERE (LOCATE('$term', lname) > 0 OR LOCATE('$term',fname) > 0) LIMIT 10");
	$my_arr = array();
	$my_arr_row = array();
	if($r) {
		while($row = $r->fetch_array(MYSQLI_ASSOC)) {
			$my_arr_row['emp_name'] = $row['emp_name'];
			$my_arr_row['dept_name'] = $row['dept_name'];
			$my_arr_row['value'] = $row['emp_id'];
			$my_arr_row['label'] = $row['emp_name'];
			array_push($my_arr,$my_arr_row);
		}
	}

	echo json_encode($my_arr);
?>