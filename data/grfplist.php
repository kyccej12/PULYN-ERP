<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	require_once("../handlers/_generics.php");
	$con = new _init;
	
	$data = array();
	
	if($_GET['search'] == 'Y') {
		$searchString = '';
		if($_GET['cust'] != "") { $searchString .= "and payee like '%$_GET[cust]%' "; }   
		if($_GET['dtf'] != '' && $_GET['dt2'] != '') {
			$searchString .= "and grfp_date between '".$con->formatDate($_GET['dtf'])."' and '".$con->formatDate($_GET['dt2'])."' "; 
		} else {
			if($_GET['dtf'] != '') { 
				$searchString .= "and grfp_date = '".$con->formatDate($_GET['dtf'])."' "; 
			} elseif ($_GET['dt2'] != '') { 
				$searchString .= "and grfp_date = '".$con->formatDate($_GET['dt2'])."' "; 
			} else { }
		}
		if($_GET['proj'] != '') { $searchString .= "and proj_name = '$_GET[proj]' "; }
		if($_GET['item'] != '') { $searchString .= "and emp_name like '%$_GET[item]%' "; }
		if($_GET['stat'] != '') { $searchString .= "and `status` = '$_GET[stat]' "; }
	
	} else {
		$searchString = " ORDER BY grfp_no DESC LIMIT 1500";
	}
	

	$datares =$con->dbquery("SELECT grfp_no,with_cv,LPAD(grfp_no,6,0) AS grfp,DATE_FORMAT(grfp_date,'%m/%d/%Y') AS grfpdate, emp_name as requestor,payee,payment_for,amount,status,'' as cv_no FROM grfp where branch = '$_SESSION[branchid]' $searchString;");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
		
			if($row['with_cv']=='Y' && $row['status']=='Finalized' ){ 
				list($cv_no) = $con->getArray("SELECT CONCAT('CV-',LPAD(cv_no,6,0)) AS cv_no FROM cv_details a WHERE a.rfp_type = 'GRFP' AND a.ref_no = '".$row['grfp_no']."' LIMIT 1;");
				$row['cv_no'] = $cv_no;
			}
		
		$data[] = array_map('utf8_encode',$row);
	}
	
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>