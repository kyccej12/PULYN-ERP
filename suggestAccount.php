<?php
	session_start();
	include("handlers/initDB.php");

	$con = new myDB;

	unset($my_arr);
	unset($my_arr_row);
	
	if(isset($_GET['grp'])) { if(isset($_GET['grp2'])) { $f1 = " and acct_grp in ('$_GET[grp1]','$_GET[grp2]') "; } else { $f1 = " and acct_grp = '$_GET[grp]' "; }}
	$term = trim(strip_tags($_GET['term'])); 
	$r = $con->dbquery("select concat('[',acct_code,'] ',description) as acct, acct_code from acctg_accounts where  file_status != 'Deleted' and parent != 'Y' and LOCATE('$term', description) > 0 $f1 LIMIT 10");
	$my_arr = array();
	$my_arr_row = array();
	if($r) {
		while($row = $r->fetch_array()) {

			$my_arr_row['acct_code'] = $row['acct_code'];
			$my_arr_row['acct'] = htmlentities($row['acct']);
			$my_arr_row['label'] = html_entity_decode($row['acct']);
			array_push($my_arr,$my_arr_row);
		}
	}

	echo json_encode($my_arr);

?>