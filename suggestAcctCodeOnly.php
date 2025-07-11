<?php
	session_start();
	include("handlers/initDB.php");

	$con = new myDB;

	unset($my_arr);
	unset($my_arr_row);
	
	$term = trim(strip_tags($_GET['term'])); 
	$r = $con->dbquery("select acct_code, concat(description, ' [',acct_code,']') as description from acctg_accounts where file_status != 'Deleted' and parent != 'Y' and LOCATE('$term', description) > 0 LIMIT 10");
	$my_arr = array();
	$my_arr_row = array();
	if($r) {
		while($row = $r->fetch_array()) {
			$my_arr_row['value'] = $row['acct_code'];
			$my_arr_row['label'] = $row['description'];
			array_push($my_arr,$my_arr_row);
		}
	}

	echo json_encode($my_arr);
	//mysql_close($con);
?>