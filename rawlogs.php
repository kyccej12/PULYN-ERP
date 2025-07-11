<?php
	session_start();
	include("includes/dbUSE.php");
	
	ini_set("display_errors","On");

	$p = dbquery("SELECT emp_id,`date`,punch_in,punch_type FROM hris.rawlogs  where emp_id = '1371'  and `date` >= '2017-01-10' order by emp_id,`date`,punch_in ;");

	echo "<table border=1>";


	while($row = mysql_fetch_array($p)){

	//list($wh_range,$sch_in,$sch_out) = getArray("SELECT TIME_TO_SEC('09:00'),TIME_TO_SEC(LEFT(sched,5))%86400 AS s_in,TIME_TO_SEC(RIGHT(sched,5))%86400 AS s_out FROM hris.e_schedule WHERE id_no = '$row[emp_id]' AND `date` = DATE_SUB('$row[date]',INTERVAL 1 DAY );");
	//list($wh_range,$sch_in,$sch_out) = getArray("SELECT TIME_TO_SEC('09:00'),TIME_TO_SEC(LEFT(sched,5))%86400 AS s_in,TIME_TO_SEC(RIGHT(sched,5))%86400 AS s_out FROM hris.e_schedule WHERE id_no = '$row[emp_id]' AND `date` = '$row[date]';");
	
	
	switch($row['punch_type']){
		case 'C/In':
			list($wh_range,$sch_in,$sch_out) = getArray("SELECT TIME_TO_SEC('09:00'),TIME_TO_SEC(LEFT(sched,5))%86400 AS s_in,TIME_TO_SEC(RIGHT(sched,5))%86400 AS s_out FROM hris.e_schedule WHERE id_no = '$row[emp_id]' AND `date` = DATE_SUB('$row[date]',INTERVAL 1 DAY );");
			list($actual_punch) = getArray("SELECT TIME_TO_SEC('$row[punch_in]');");
			if($sch_in>$sch_out){
				//$flag = 'has Yesterday';
				if($actual_punch>$sch_out){
					//current day
					$flag = "first in";
				}else{
					//previuos day
					$flag = "second in";
				}
			}else{
				//current day
				//$flag = "first in";
				list($wh_range,$sch_in,$sch_out) = getArray("SELECT TIME_TO_SEC('09:00'),TIME_TO_SEC(LEFT(sched,5))%86400 AS s_in,TIME_TO_SEC(RIGHT(sched,5))%86400 AS s_out FROM hris.e_schedule WHERE id_no = '$row[emp_id]' AND `date` = '$row[date]';");
				list($actual_punch) = getArray("SELECT TIME_TO_SEC('$row[punch_in]');");
				if($sch_in>$sch_out){
					//$flag = 'has Yesterday';
					if($actual_punch>$sch_out){
						//current day
						$flag = "first in";
					}else{
						//previuos day
						//$flag = "second in";
					}
				}else{
					//current day
					//$flag = "first in";
				}
			}
		break;

		case 'C/Out':
			list($actual_punch) = getArray("SELECT TIME_TO_SEC('$row[punch_in]');");
			list($wh_range,$sch_in,$sch_out) = getArray("SELECT TIME_TO_SEC('09:00'),TIME_TO_SEC(LEFT(sched,5))%86400 AS s_in,TIME_TO_SEC(RIGHT(sched,5))%86400 AS s_out FROM hris.e_schedule WHERE id_no = '$row[emp_id]' AND `date` = DATE_SUB('$row[date]',INTERVAL 1 DAY );");
	
			if($sch_in>$sch_out){
				if($actual_punch<= $sch_in+$wh_range && $actual_punch < $sch_out){
					$flag = "break out";
				}else{
					$flag = "final out";
				}
			}else{
				list($wh_range,$sch_in,$sch_out) = getArray("SELECT TIME_TO_SEC('09:00'),TIME_TO_SEC(LEFT(sched,5))%86400 AS s_in,TIME_TO_SEC(RIGHT(sched,5))%86400 AS s_out FROM hris.e_schedule WHERE id_no = '$row[emp_id]' AND `date` = '$row[date]';");
				if($actual_punch>$sch_in){
					if($actual_punch<$sch_in+$wh_range){
						$flag = "break out";
					}else{
						$flag = "final out";
					}
				}else{
					//does not belong to this day
					//$flag = "final out";
				}
			}
				

		break;
	}
	


	/*
	if($sch_in>$sch_out){
		$flag = 'has Yesterday';

	}else{
		$flag = 'disregard';
	}
	*/
	
	//$yesterdaySched = 
		echo "<tr>";
			echo "<td width=100px> $row[emp_id] </td>";
			echo "<td width=100px> $row[date] </td>";
			echo "<td width=100px> $row[punch_in] </td>";
			echo "<td width=100px> $row[punch_type] </td>";
			echo "<td width=100px> $flag </td>";
		echo "</tr>";
	}

	echo "</table>";


?>

