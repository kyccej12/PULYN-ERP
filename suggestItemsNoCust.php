<?php
	session_start();
	include("handlers/initDB.php");
	$con = new myDB;
	
	$term = trim(strip_tags($_GET['term']));
	$datares = $con->dbquery("SELECT item_code, description, unit, '' as onhand FROM products_master WHERE (LOCATE('$term',description) > 0 OR LOCATE('$term',full_description) > 0 OR LOCATE('$term',item_code) > 0 OR LOCATE('$term',indcode) > 0) AND file_status = 'Active' and `active` = 'Y' limit 500");
	while($row = $datares->fetch_array()){	
	  $pi = $con->getArray("select ifnull(sum(b.qty),0) from phy_header a inner join phy_details b on a.doc_no = b.doc_no and a.branch=b.branch where a.branch = '$_SESSION[branchid]' and b.item_code = '$row[item_code]' and a.status = 'Finalized' and a.doc_date = '2018-05-26';");
	  $run = $con->getArray("select ifnull(sum(purchases+inbound-outbound-pullouts-sold),0) as run from ibook where doc_date >= '2018-05-26' and doc_date <= now() and item_code = '$row[item_code]' and doc_branch = '$_SESSION[branchid]';");
	  $oh = $pi[0]+$run[0];
	  
	  $row['onhand'] = $oh;
	  $data[] = array_map('utf8_encode',$row);
	}

	echo json_encode($data);
?>