<?php
	ini_set('max_execution_time',0);
	ini_set('memory_limit',-1);
	
	include("../handlers/initDB.php");
	$con = new myDB;
	$data = array();

	$datares = $con->dbquery("SELECT record_id AS id, item_code, IF(barcode=0,'',barcode) AS barcode, tradename as supplier, a.brand, a.description, a.rack_no, a.level, generic_name, c.group, b.description AS unit, a.srp, '' AS qty_onhand, '' AS sold FROM pharma_master a LEFT JOIN options_units b ON a.unit = b.unit LEFT JOIN pharma_mgroup c ON a.category = c.id LEFT JOIN contact_info d ON a.supplier = d.file_id WHERE a.file_status != 'Deleted' ORDER BY generic_name;");
	while($row = $datares->fetch_array()){

		/* GET INVENTORY */
		list($beg,$begdate) = $con->getArray("select begqty,begdate from pharma_master where `item_code` = '$row[item_code]';");
		list($sold) = $con->getArray("SELECT SUM(b.qty) FROM pharma_so_header a LEFT JOIN pharma_so_details b ON a.so_no = b.so_no WHERE a.status = 'Finalized' AND a.so_date >= '$begdate' AND b.code = '$row[item_code]';");

		list($purchases) = $con->getArray("SELECT SUM(purchases) FROM ibook WHERE item_code = '$row[item_code]' AND doc_type LIKE '%PHARMA%' and doc_date >= '$begdate';");
		list($pullouts) = $con->getArray("SELECT SUM(pullouts) FROM ibook WHERE item_code = '$row[item_code]' AND doc_type LIKE '%PHARMA%' and doc_date >= '$begdate';");

		$row['qty_onhand'] = $beg + $purchases - $pullouts - $sold;
		
		$row['sold'] = $sold;

	 	$data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>