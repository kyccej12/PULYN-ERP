<?php

	//ini_set("display_errors","On");
	ini_set("memory_limit","2056M");
	ini_set("max_execution_time",0);

	/* Importing Data File */
	$uploadDir = "temp/";

	$fileName = $_FILES['userfile']['name'];
	$tmpName = $_FILES['userfile']['tmp_name'];
		
	/* CHANGE UNIQUE FILENAME TO PREVENT DUPLICATION */
	$ext = substr(strrchr($fileName, "."), 1);
	$randName = md5(rand() * time());
	$newFileName = $randName . "." . $ext;
	$filePath = $uploadDir . $newFileName;
	
	$result = move_uploaded_file($tmpName, $filePath);
	if (!$result) {
		echo "Error uploading file";
		exit;
	} else {
	
		require_once '../handlers/_payroll.php';
		require_once '../handlers/_generics.php';
		
		$pay = new payroll($_POST['cutoff']);
		
		$file = "temp/$newFileName";// Your Temp Uploaded file
		$handle = fopen($file, "r"); // Make all conditions to avoid errors
		$read = file_get_contents($file); //read
		$lines = explode("\n", $read);//get
		$i= 0;//initialize

		/* delete existing logs to avoid duplication */
		$pay->dbquery("TRUNCATE TABLE omdcpyaroll.biologs_raw;");
		
		
		foreach($lines as $key => $value) {
			$cols[$i] = explode(",", $value);
			
			$emp_id = $cols[$i][0];
			$date = $cols[$i][1];
			$tmpTime = $cols[$i][2];
		
			$time = substr($tmpTime,0,5) . ":00";

			if($date >= $pay->dtf && $date <= $pay->dt2) {		
				
				/* Get Basic Infor from Employee Profile */
				list($def_shift,$emp_type,$pay_type,$day,$area,$timeSec) = $pay->getArray("select shift, emp_type, payroll_type, `area`, date_format('$date','%a'), TIME_TO_SEC('$time') from omdcpyaroll.emp_masterfile where EMP_ID = '$emp_id';");
				
				/* Check if Schedule is Plotted */
				list($shift) = $pay->getArray("select `SHIFT` from omdcpyaroll.emp_dtrfinal where EMP_ID = '$emp_id' and `DATE` = '$date';");
				
				/* If Shift is not plotted, refer to Employee's default shift in his profile */
				if($shift == '') { $shift = $def_shift; }
				
				/* Check if Clock IN Exists */
				list($inExists,$inStamp) = $pay->getArray("select count(*), TIME_TO_SEC(`time`) from omdcpyaroll.biologs_raw where `date` = '$date' and type = 'I' and emp_id = '$emp_id';");
				if($inExists > 0) {
					$currentOut = $pay->timeToSeconds($time);
					list($previousOut) = $pay->getArray("select TIME_TO_SEC(`time`) from omdcpyaroll.biologs_raw where `date` = '$date' and type = 'O' and emp_id = '$emp_id';");
					
					if($previousOut == '' && ($currentOut-$inStamp) > 0) {
						$pay->dbquery("INSERT IGNORE INTO omdcpyaroll.biologs_raw (emp_id,shift,emp_type,pay_type,`date`,`time`,`period`,`type`) VALUES ('$emp_id','$shift','$emp_type','$pay_type','$date','$time','PM','O');");
					} else {

						if($currentOut > $previousOut) {
							$pay->dbquery("UPDATE IGNORE omdcpyaroll.biologs_raw set `time` = '$time' where `date` = '$date' and emp_id = '$emp_id' and `period` = 'PM' and `type` = 'O';");
						}
					}
				} else {
					$pay->dbquery("INSERT IGNORE INTO omdcpyaroll.biologs_raw (emp_id,shift,emp_type,pay_type,`date`,`time`,`period`,`type`) VALUES ('$emp_id','$shift','$emp_type','$pay_type','$date','$time','AM','I');");
				}
			}
		}

		for($i = 0; $i <= $pay->ndays; $i++) {			
			list($myDay) = $pay->getArray("select date_add('". $pay->dtf . "', INTERVAL $i DAY);");
			$b = $pay->dbquery("select a.emp_id, a.shift, a.emp_type, if(a.pay_type=0,b.payroll_type,a.pay_type) as pay_type, ifnull(b.area,1) as `area` from omdcpyaroll.biologs_raw a left join omdcpyaroll.emp_masterfile b on a.emp_id = b.emp_id where a.date = '$myDay' group by a.emp_id order by emp_id asc;");
			
			while(list($emp_id,$shift,$emp_type,$pay_type,$area) = $b->fetch_array()) {
				
				$pay->checkHoliday($myDay,$shift,$area);

				list($in_am,$ins) = $pay->getArray("SELECT `time`, TIME_TO_SEC(`time`) FROM omdcpyaroll.biologs_raw WHERE emp_id = '$emp_id' AND `date` = '$myDay' AND `period` = 'AM' AND `type` = 'I' LIMIT 1");
				list($out_pm,$ops) = $pay->getArray("SELECT `time`, TIME_TO_SEC(`time`) FROM omdcpyaroll.biologs_raw WHERE emp_id = '$emp_id' AND `date` = '$myDay' AND `period` = 'PM' AND `type` = 'O' LIMIT 1");
			
				list($isE) = $pay->getArray("select count(*) from omdcpyaroll.emp_dtrfinal where EMP_ID = '$emp_id' and `DATE` = '$myDay';");
				if($isE > 0) {
					$pay->dbquery("UPDATE IGNORE omdcpyaroll.emp_dtrfinal set CLOCKIN = '$in_am',CLOCKOUT = '$out_pm', HD_TYPE = '" . $pay->htype . "' where EMP_ID = '$emp_id' and `DATE` = '$myDay';");
				} else {
					$pay->dbquery("INSERT IGNORE INTO omdcpyaroll.emp_dtrfinal (PERIOD_ID,EMP_TYPE,EMP_ID,DEPT,`DATE`,CLOCKIN,CLOCKOUT,HD_TYPE) VALUES ('" . $pay->cutoff . "','$emp_type','$emp_id','$dept','$myDay','$in_am','$out_pm','".$pay->htype."');");
				}
				
				if($ins != 0 && $ops != 0) {
					$pay->computeTimeSheets($emp_id,$myDay,$shift,$ins,$ops);
					$pay->dbquery("update ignore omdcpyaroll.emp_dtrfinal set tot_work = '".$pay->twork."',tot_late = '".$pay->late."', tot_ut = '".$pay->ut."', reg_ot = '".$pay->overtime."', sun_ot = '".$pay->restday."', prem_ot='".$pay->premium."' where emp_id = '$emp_id' and date = '$myDay';");
				}
				
			}
		}

		echo "Logs Successfully Uploaded... Please close this window...";
		
	}
?>