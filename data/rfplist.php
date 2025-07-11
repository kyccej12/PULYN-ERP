<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	require_once("../handlers/_generics.php");
	$con = new _init;
	
	$data = array();
	
	if($_GET['search'] == 'Y') {
		$searchString = '';
		if($_GET['cust'] != "") { $searchString .= "and (supplier = '$_GET[cust]' or supplier_name like '%$_GET[cust]%') "; }   
		if($_GET['dtf'] != '' && $_GET['dt2'] != '') {
			$searchString .= "and rfp_date between '".$con->formatDate($_GET['dtf'])."' and '".$con->formatDate($_GET['dt2'])."' "; 
		} else {
			if($_GET['dtf'] != '') { 
				$searchString .= "and rfp_date = '".$con->formatDate($_GET['dtf'])."' "; 
			} elseif ($_GET['dt2'] != '') { 
				$searchString .= "and rfp_date = '".$con->formatDate($_GET['dt2'])."' "; 
			} else { }
		}
		if($_GET['proj'] != '') { $searchString .= "and proj_name = '$_GET[proj]' "; }
		if($_GET['item'] != '') {
			$r = $con->dbquery("select rfp_no from rfp_details where (apv_no like '%$_GET[item]%' or apv_remarks like '%$_GET[item]%')");
			$rfplist = '';
			while(list($xrfp) = $r->fetch_array()) {
				$rfplist .= $xrfp.",";
			}
			$rfplist = substr($rfplist,0,-1);
		
			$searchString .= "and rfp_no in ($rfplist) ";
		
		}
		if($_GET['stat'] != '') { $searchString .= "and `status` = '$_GET[stat]' "; }
	
	} else {
		$searchString = " ORDER BY rfp_no DESC LIMIT 2500";
	}
	

	$datares = $con->dbquery("select rfp_no, lpad(rfp_no,6,0) as rfp, date_format(rfp_date,'%m/%d/%Y') as rd8, concat('[',supplier,'] ',supplier_name) as payee, remarks, amount,`status` from rfp_header where 1=1 $searchString;");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
		
		$data[] = array_map('utf8_encode',$row);
	}
	
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>