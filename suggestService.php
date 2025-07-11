<?php
	session_start();
	include("handlers/initDB.php");
    $con = new myDB;


	unset($my_arr);
	unset($my_arr_row);

	$term = trim(strip_tags($_GET['term'])); 
	$r = $con->dbquery("select concat('(',code,') ',description) as item, code, description, unit, format(unit_price,2) as unit_price, format(unit_price,2) as specialprice, 'N' as sprice from services_master where (locate('$term',description) > 0 or locate('$term',barcode) or locate('$term',code)) and file_status = 'Active' limit 25;");
	$my_arr = array();
	$my_arr_row = array();

	if($r) {
		while($row = $r->fetch_array()) {
			
			if($_REQUEST['cid'] != 0 or $_REQUEST['cid'] != '') {
				list($sprice) = $con->getArray("SELECT special_price FROM contact_sprice WHERE `code` = '$row[code]' AND contact_id = '$_REQUEST[cid]' AND (with_validity = 'N' OR valid_until >= NOW());");
				if($sprice > 0 && $sprice != '') { 
					$row['specialprice'] = $sprice; 
					$row['sprice'] = 'Y'; 
				}
			} 
			
			$my_arr_row['specialprice'] = $row['specialprice']; 
			$my_arr_row['sprice'] = $row['sprice']; 

			$my_arr_row['price'] = $row['unit_price']; 
			$my_arr_row['code'] = $row['code'];
			$my_arr_row['value'] = $row['description'];
			$my_arr_row['unit'] = $row['unit'];
			$my_arr_row['label'] = $row['item'];

			array_push($my_arr,$my_arr_row);
		}
	}
	
    echo json_encode($my_arr);

?>