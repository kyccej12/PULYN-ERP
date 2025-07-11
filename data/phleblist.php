<?php
	include("../handlers/_generics.php");
	$con = new _init;
	$today = date('Y-m-d');

	list($baseD8) = $con->getArray("SELECT DATE_SUB('$today', INTERVAL 7 DAY);");

	if($_REQUEST['displayPending'] != 'Y') {
		$f = " and b.so_date = '$today' ";
	} else { $f = " and b.so_date < '$today' "; }

	$data = array();
	$datares = $con->dbquery("SELECT a.record_id AS id, LPAD(b.priority_no,6,0) AS priority, LPAD(a.so_no,6,0) AS so, DATE_FORMAT(b.so_date,'%m/%d/%Y') AS sodate, b.patient_name, IF(c.gender='M','Male','Female') AS gender, DATE_FORMAT(c.birthdate,'%m/%d/%Y') AS birthdate, FLOOR(ROUND(DATEDIFF(b.so_date,c.birthdate) / 364.25,2)) AS age, a.code, a.procedure, CONCAT(f.fullname,', ',f.prefix) AS physician, d.sample_type,a.parent_code, b.so_date as sdate, e.subcategory AS subcat FROM lab_samples a LEFT JOIN so_header b ON a.so_no = b.so_no LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_sampletype d ON a.sampletype = d.id LEFT JOIN services_master e ON a.code = e.code LEFT JOIN options_doctors f ON f.id = a.physician WHERE extracted = 'N' AND e.category = '1' $f;");

    while($row = $datares->fetch_array()){
      
        $data[] = array_map('utf8_encode',$row);

	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>