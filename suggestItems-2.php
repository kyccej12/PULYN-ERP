<?php
	session_start();
	include("includes/dbUSEi.php");
	
	$term = trim(strip_tags($_GET['term']));
	
	//list($pl) = getArray("SELECT price_level FROM contact_info WHERE file_id = '$_REQUEST[customer]';");
	
	
	$term = trim(strip_tags($_GET['term']));
	
	if($_REQUEST['customer'] != "") {
		$datares = $con->query("SELECT item_code, description, unit, CASE 
			WHEN a.price_level = '1' THEN if(b.unit_price1=0,b.unit_price6,b.unit_price1)
				WHEN a.price_level = '2' THEN if(b.unit_price2=0,b.unit_price6,b.unit_price2)
				WHEN a.price_level = '3' THEN if(b.unit_price3=0,b.unit_price6,b.unit_price3)
				WHEN a.price_level = '4' THEN if(b.unit_price4=0,b.unit_price6,b.unit_price4)
				WHEN a.price_level = '5' THEN if(b.unit_price5=0,b.unit_price6,b.unit_price5)
				WHEN a.price_level = '6' THEN if(b.unit_price6=0,walkin_price,b.unit_price6)
				WHEN a.price_level = '7' THEN if(b.unit_price7=0,b.unit_price6,b.unit_price7)
				WHEN a.price_level = '8' THEN if(b.unit_price8=0,b.unit_price6,b.unit_price8)
			ELSE if(walkin_price=0,srp,walkin_price)
		END AS price, indcode FROM cebuglass.contact_info a, cebuglass.products_master b WHERE (LOCATE('$term',description) > 0 OR LOCATE('$term',full_description ) OR LOCATE('$term',item_code) OR LOCATE('$term',indcode)) AND b.file_status = 'Active' and b.active = 'Y' and a.file_id = TRIM(LEADING '0' FROM '$_GET[customer]') limit 500");
	} else {
		$datares = $con->query("SELECT item_code, description, unit, if(unit_price6=0,walkin_price,unit_price6) as walkin_price, indcode FROM cebuglass.products_master WHERE (LOCATE('$term',description) > 0 OR LOCATE('$term',full_description) > 0 OR LOCATE('$term',item_code) > 0 OR LOCATE('$term',indcode)> 0)  AND file_status = 'Active' and `active` = 'Y' limit 500");
	}
	while($row = $datares->fetch_array(MYSQLI_ASSOC)) {
	  $pi = getArray("select ifnull(sum(b.qty),0) from phy_header a inner join phy_details b on a.doc_no = b.doc_no and a.branch=b.branch where a.branch = '$_SESSION[branchid]' and b.item_code = '$row[item_code]' and a.status = 'Finalized' and a.doc_date = '2018-05-26';");
	  $run = getArray("select ifnull(sum(purchases+inbound-outbound-pullouts-sold),0) as run from ibook where doc_date >= '2018-05-26' and doc_date <= now() and item_code = '$row[item_code]' and doc_branch = '$_SESSION[branchid]';");
	  $oh = $pi[0]+$run[0];
	
	  array_push($row,$oh);
	  $data[] = array_map('utf8_encode',$row);
	}


	echo json_encode($data);
	@mysqli_close($con);
?>