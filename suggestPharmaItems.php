<?php
	session_start();
	include("handlers/initDB.php");
    $con = new myDB;


	unset($my_arr);
	unset($my_arr_row);

	$term = trim(strip_tags($_GET['term'])); 
	$r = $con->dbquery("select concat('(',item_code,') ',description) as item, item_code, description, unit, srp, unit_cost, rack_no, begqty, begdate from pharma_master where (locate('$term',description) > 0 or locate('$term',barcode) or locate('$term',item_code) or locate('$term',generic_name)) AND file_status != 'Deleted' limit 25");
	$my_arr = array();
	$my_arr_row = array();

	if($r) {
		while($row = $r->fetch_array()) {


			list($sold) = $con->getArray("select sum(b.qty) from pharma_si_header a left join pharma_si_details b on a.doc_no = b.doc_no where a.status = 'Finalized' and a.doc_date >= '$row[begdate]' and b.code = '$row[item_code]';");
			$oh = $row['begqty']-$sold;

			$description = $row['description'] . " [RACK NO " . $row['rack_no'] . " =>  " . number_format($oh,2) . "]"; 
			
			$my_arr_row['price'] = $row['srp']; 
			$my_arr_row['cost'] = $row['unit_cost']; 
			$my_arr_row['code'] = $row['item_code'];
			$my_arr_row['value'] = $row['description'];
			$my_arr_row['unit'] = $row['unit'];
			$my_arr_row['label'] = $description;

			array_push($my_arr,$my_arr_row);
		}
	}
	
    echo json_encode($my_arr);

?>