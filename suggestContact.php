<?php
	include("includes/dbUSE.php");
	session_start();

	unset($my_arr);
	unset($my_arr_row);

	$term = trim(strip_tags($_GET['term'])); 
	$r = mysql_query("SELECT CONCAT('(',LPAD(file_id,6,0),') ',tradename) AS label, LPAD(file_id,6,0) AS cid,tradename,CONCAT(`address`,', ',d.brgyDesc,', ',b.citymunDesc,', ',c.provDesc) AS address,terms FROM contact_info a LEFT JOIN options_cities b ON a.city = b.citymunCode LEFT JOIN options_provinces c ON a.province = c.provCode LEFT JOIN options_brgy d ON a.brgy = d.brgyCode WHERE LOCATE('$terms',tradename) > 0;");
	
	$my_arr = array();
	$my_arr_row = array();

	if($r) {
		while($row = mysql_fetch_array($r)) {
	
			$cname = html_entity_decode($row['tradename']);
			$addr = html_entity_decode($row['address']);
			$label = html_entity_decode($row['label']);

			$my_arr_row['cid'] = $row['cid'];
			$my_arr_row['cname'] = $cname;
			$my_arr_row['addr'] = $addr;
			$my_arr_row['terms'] = $row['terms'];
			$my_arr_row['label'] = $label;
			$my_arr_row['value'] = $row['cid'];

			array_push($my_arr,$my_arr_row);
		}
	}

	echo json_encode($my_arr);
	mysql_close($con);
?>