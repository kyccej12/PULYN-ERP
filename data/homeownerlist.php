<?php
	ini_set("memory_limit",-1);
	include("../includes/dbUSE.php");
	
	//switch($_GET['mod']) { case "H": $fx = " and record_type in ('H') "; break; case "T": $fx = " and record_type
	
	$datares = dbquery("SELECT record_id, concat('T',tower,' - ',`tower_unit`) as myunit, CONCAT(`lname`,', ',`fname`,' ', `mname`) AS owner_name, `tower`, `tower_unit`, `assigned_parking`, `floor_no`, `tel_no`, `nationality`, '' as `nat`, 
						CASE 
							WHEN `record_type` = 'H' THEN 'Homeowner & Resident'
							WHEN `record_type` = 'OBR' THEN 'Owned But Not Residing'
							WHEN `record_type` = 'ONL' THEN 'Owned But Leased to Others'
							WHEN `record_type` = 'T' THEN 'Tennant/Leasee'
						END AS rtype
					FROM `citylights`.`homeowners`;");
	while($row = mysql_fetch_array($datares)){
		$pk = dbquery("SELECT parking_no FROM parking WHERE owner_id = '$row[record_id]';"); 
		$i = 0; $xpark = '';
		
		if($row['nationality'] != '' || $row['nationality'] != 0) { $nat = getArray("SELECT nation_desc FROM `nationality` WHERE line_id = '$row[nationality]';"); $row['nat'] = $nat[0]; } else { $row['nat'] = ''; }
		
		while($pkrow = mysql_fetch_array($pk)) {  $xpark .= $pkrow[0].","; if($t >= 2) { $xpark .= "<br/>"; $t = 0; } else { $t++; }} 
			$row['assigned_parking'] = substr($xpark,0,-1);
			$data[] = array_map('utf8_encode',$row);
	}
	
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>