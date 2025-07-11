<?php
	include("../handlers/_generics.php");
	$con = new _init;


	$data = array();
	$datares = $con->dbquery("SELECT a.record_id AS id, patient_name, gender, DATE_FORMAT(birthdate,'%m/%d/%Y') AS bday, contact_no, DATE_FORMAT(scheduled_date,'%W, %M %d, %Y') AS `schedule`, c.slot, IF(b.request_category='1','Medical Consultation','Dental Consultation') AS request, b.request_type, preferred_doctor, a.status FROM patient_appointment a LEFT JOIN options_requesttype b ON a.request_category = b.record_id left join options_timeslot c on a.scheduled_slot = c.id;");

    while($row = $datares->fetch_array(MYSQLI_ASSOC)){
      
        $data[] = array_map('utf8_encode',$row);

	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>