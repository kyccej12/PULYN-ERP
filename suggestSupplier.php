<?php
	session_start();
	include("includes/dbUSE.php");
	
	unset($my_arr);
	unset($my_arr_row);

	$term = trim(strip_tags($_GET['term'])); 
	//$r = mysql_query("select concat('(',lpad(file_id,6,0),') ',tradename) as label, lpad(file_id,6,0) as cid,tradename,concat(`address`,', ',b.city,', ',c.province) as address,terms from contact_info a left join options_cities b on a.city=b.city_id left join options_provinces c on a.province=c.province_id where locate('$term',tradename) > 0 and company in ('0','$_SESSION[company]') limit 20");
	
	$r = mysql_query("SELECT CONCAT('(',LPAD(file_id,6,0),') ',tradename) AS label, LPAD(file_id,6,0) AS cid,tradename,CONCAT(`address`,', ',d.brgyDesc,', ',b.citymunDesc,', ',c.provDesc) AS address,terms,price_level FROM contact_info a LEFT JOIN options_cities b ON a.city = b.citymunCode LEFT JOIN options_provinces c ON a.province = c.provCode LEFT JOIN options_brgy d ON a.brgy = d.brgyCode WHERE LOCATE('$term',tradename) > 0;");
	
	$my_arr = array();
	$my_arr_row = array();

	if($r) {
		while($row = mysql_fetch_array($r)) {

			$patterns = array();
			$patterns[0] = '/&Ntilde;/';
			$patterns[1] = '/&ntilde;/';
			$replacements = array();
			$replacements[0] = 'Ñ';
			$replacements[1] = 'ñ';

			$label = preg_replace($patterns, $replacements, $row['label']);
			$cname = preg_replace($patterns, $replacements, $row['tradename']);

			$my_arr_row['cid'] = $row['cid'];
			$my_arr_row['cname'] = $cname;
			$my_arr_row['addr'] = $row['address'];
			$my_arr_row['terms'] = $row['terms'];
			$my_arr_row['label'] = $label;

			array_push($my_arr,$my_arr_row);
		}
	}

	echo json_encode($my_arr);
	mysql_close($con);
?>