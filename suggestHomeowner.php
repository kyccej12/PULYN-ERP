<?php
	session_start();
	include("includes/dbUSEi.php");
	
	$term = trim(strip_tags($_GET['term']));
	$datares = $con->query("SELECT * FROM (SELECT LPAD(record_id,5,0) AS acctID, IF(fname = '',lname,CONCAT(lname,', ',fname)) AS `name`, tower, tower_unit FROM homeowners) a WHERE `name` LIKE '%$term%';"); 

	while($row = $datares->fetch_array(MYSQLI_ASSOC)) {
		$data[] = array_map('utf8_encode',$row);
	}


	echo json_encode($data);
	@mysqli_close($con);
?>