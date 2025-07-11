<?php
	session_start();
	include("../handlers/initDB.php");
	$con = new myDB;
	
	$data = array();
	
	$datares = $con->dbquery("SELECT a.record_id, a.acct_code, a.description as acct_desc, parent_acct, '' AS parent_title, a.acct_grp, b.description AS grp_desc FROM acctg_accounts a LEFT JOIN acctg_accountgrps b ON a.acct_grp = b.acct_grp WHERE a.file_status = 'Active' AND parent != 'Y';");
	while($row = $datares->fetch_array()){
	  
		list($parent) = $con->getArray("select description from acctg_accounts where acct_code = '$row[parent_acct]';");
		$row['parent_title'] = $parent;
	  
	  
		$data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>