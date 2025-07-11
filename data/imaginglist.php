<?php
	include("../handlers/_generics.php");
	$con = new _init;


	$data = array();
	//$datares = $con->dbquery("SELECT b.line_id as id, LPAD(a.so_no,6,0) AS so, DATE_FORMAT(a.so_date,'%m/%d/%Y') AS sodate, a.patient_name, IF(c.gender='M','Male','Female') AS gender, c.birthdate, '' AS age, b.code, b.description AS particulars, a.physician, e.sample_type FROM so_header a LEFT JOIN so_details b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON a.patient_id = c.patient_id LEFT JOIN services_master d ON b.code = d.code LEFT JOIN options_sampletype e ON d.sample_type = e.id WHERE b.sample_extracted = 'N' AND d.with_specimen = 'Y' AND a.status = 'Finalized' AND d.category IN ('1','2') and b.sample_extracted = 'N' AND a.cstatus IN (2,3,12) ORDER BY a.so_date DESC;");
	$datares = $con->dbquery("SELECT a.record_id AS id, LPAD(b.priority_no,6,0) AS priority, LPAD(a.so_no,6,0) AS so, DATE_FORMAT(b.so_date,'%m/%d/%Y') AS sodate, b.patient_name, IF(c.gender='M','Male','Female') AS gender, DATE_FORMAT(c.birthdate,'%m/%d/%Y') AS birthdate, FLOOR(ROUND(DATEDIFF(b.so_date,c.birthdate) / 364.25,2)) AS age, a.code, a.procedure, a.physician,a.parent_code,b.so_date as sdate FROM lab_samples a LEFT JOIN so_header b ON a.so_no = b.so_no LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN services_master e ON a.code = e.code WHERE extracted = 'N' AND e.category  in ('2','3') and b.so_date > '2022-03-20';");

    while($row = $datares->fetch_array(MYSQLI_ASSOC)){
        $row['age'] =  $con->calculateAge($row['birthdate']);
      
        $data[] = array_map('utf8_encode',$row);

	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>