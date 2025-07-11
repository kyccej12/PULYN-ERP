<?php
	include("../handlers/_generics.php");
	$con = new _init;

	function formatPriorityNo($no,$station) {
		if($no != '') {
			switch($callStation) {
				case "MED. CLINIC":
					$priority = "M" . str_pad($no,3,'0',STR_PAD_LEFT);
				break;
				case "DENTAL CLINIC":
					$priority = "D" . str_pad($no,3,'0',STR_PAD_LEFT);
				break;
				default:
					$priority = str_pad($no,4,'0',STR_PAD_LEFT);
				break;
			}
		}

		return $priority;

	}

	$data = array();
	$datares = $con->dbquery("SELECT record_id as id, priority_no, '' as priority, lpad(so_no,6,0) as so, patient_name, gender, calling_station AS station, DATE_FORMAT(time_queued,'%m/%d/%Y %h:%i %p') AS timequeued, IF(picked='Y',DATE_FORMAT(time_picked,'%m/%d/%Y %h:%i %p'),'') AS timepicked, b.fullname AS pickedby FROM queueing a LEFT JOIN user_info b ON a.queued_by = b.emp_id WHERE date_queued = '".date('Y-m-d')."';");

    while($row = $datares->fetch_array()){
       
		$row['priority'] = formatPriorityNo($row['priority_no'],$row['station']);
      
        $data[] = array_map('utf8_encode',$row);

	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);

?>