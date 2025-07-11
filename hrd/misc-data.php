<?php
	session_start();

	//ini_set("display_errors","On");
	require_once '../handlers/_payroll.php';
	require_once '../handlers/_generics.php';
	
	$mydb = new _init;
	
	switch($_POST['mod']) {
		case "deleteEmployee":
			$mydb->dbquery("UPDATE ignore omdcpayroll.emp_masterfile SET FILE_STATUS = 'DELETED', DELETED_BY = '$_SESSION[userid]', DELETED_ON = NOW() WHERE record_id = '$_POST[rid]';");
		break;
		case "getCutoff":
			echo json_encode($mydb->getArray("SELECT period_id AS rid, payroll_batch as batch,DATE_FORMAT(period_start,'%m/%d/%Y') AS dtf,DATE_FORMAT(period_end,'%m/%d/%Y') AS dt2,reportingMonth,reportingYear,weekOfMonth,remarks FROM omdcpayroll.pay_periods WHERE period_id = '$_POST[id]';"));
		break;
		case "updateCutoff":
			$mydb->dbquery("update ignore omdcpayroll.pay_periods set payroll_batch='$_POST[batch]', period_start='".$mydb->formatDate($_POST[dtf])."', period_end='".$mydb->formatDate($_POST[dt2])."', reportingMonth = '$_POST[month]', reportingYear = '$_POST[year]', weekOfMonth = '$_POST[week]', remarks = '".$mydb->escapeString($_POST[remarks])."' where period_id = '$_POST[rid]';");
		break;
		case "newCutoff":
			$mydb->dbquery("insert ignore into omdcpayroll.pay_periods (payroll_batch,period_start,period_end,reportingMonth,reportingYear,weekOfMonth,remarks) values ('$_POST[batch]','".$mydb->formatDate($_POST[dtf])."','".$mydb->formatDate($_POST[dt2])."','$_POST[month]','$_POST[year]','$_POST[week]','".$mydb->escapeString($_POST[remarks])."');");
		break;
		
		/*
		case "getPeriods":
			$_q = $mydb->dbquery("select period_id,concat(date_format(period_start,'%m/%d/%Y'),' - ',date_format(period_end,'%m/%d/%Y')) from omdcpayroll.pay_periods where payroll_type = '$_POST[type]' order by period_end desc;");
			while(list($a,$b) =$_q->fetch_array()) {
				echo "<option value='$a'>$b</option>";
			}
			unset($q);
		break;
		*/
		
		case "populatePeriods":
			$_q = $mydb->dbquery("select period_id,concat(date_format(period_start,'%m/%d/%Y'),' - ',date_format(period_end,'%m/%d/%Y')) from omdcpayroll.pay_periods where payroll_batch = '$_POST[batch]' order by period_end desc limit 10;");
			while(list($a,$b) =$_q->fetch_array()) {
				echo "<option value='$a'>$b</option>";
			}
			unset($q);
		break;
		
		case "populateEmployees":
			$_q = $mydb->dbquery("SELECT emp_id, CONCAT(lname,', ',fname,' ',LEFT(mname,1),'.') FROM omdcpayroll.emp_masterfile WHERE FILE_STATUS != 'DELETED' AND EMPLOYMENT_STATUS NOT IN (7,8,9,10) AND PAYROLL_BATCH = '$_POST[batch]' order by lname, fname;");
			while(list($a,$b) =$_q->fetch_array()) {
				echo "<option value='$a'>". strtoupper($b) . "</option>";
			}
		
		break;
		
		
		case "getEmployeesByDept":
			if($_POST['dept'] != "") { $f1 = " AND `DEPT` = '$_POST[dept]' "; } else { $f1 = ""; }
			if($_POST['batch'] != "") { $f2 = " AND `PAYROLL_BATCH` = '$_POST[batch]' "; } else { $f2 = ""; }
			$_q = $mydb->dbquery("SELECT emp_id, CONCAT(lname,', ',fname,' ',LEFT(mname,1),'.') FROM omdcpayroll.emp_masterfile WHERE FILE_STATUS != 'DELETED' AND EMPLOYMENT_STATUS NOT IN (7,8,9,10) $f1 $f2 order by lname, fname;");
			echo "<option value=''>- All Employees -</option>";
			while(list($a,$b) =$_q->fetch_array()) {
				echo "<option value='$a'>". strtoupper($b) . "</option>";
			}
		break;
		
		
		case "deleteCutoff":
			$mydb->dbquery("delete from omdcpayroll.pay_periods where period_id = '$_POST[rid]';");
		break;
		
		case "getNatHoliday":
			echo json_encode($mydb->getArray("SELECT id, `type` as xtype, DATE_FORMAT(`date`,'%m/%d/%Y') AS xdate, occasion FROM omdcpayroll.pay_holiday_nat WHERE id = '$_POST[id]';"));
		break;
		case "updateNatHoliday":
			$mydb->dbquery("UPDATE omdcpayroll.pay_holiday_nat SET `type` = '$_POST[type]', `date` = '".$mydb->formatDate($_POST['date'])."', occasion = '".$mydb->escapeString(htmlentities($_POST['occasion']))."' WHERE id = '$_POST[rid]';");
		break;
		case "newNatHoliday":
			$mydb->dbquery("INSERT IGNORE INTO omdcpayroll.pay_holiday_nat (`type`,`date`,occasion) VALUES ('$_POST[type]','".$mydb->formatDate($_POST['date'])."','".$mydb->escapeString(htmlentities($_POST['occasion']))."');");
		break;
		case "deleteNatHoliday":
			$mydb->dbquery("delete from omdcpayroll.pay_holiday_nat where id = '$_POST[rid]';");
		break;
		
		case "getLocHoliday":
			echo json_encode($mydb->getArray("SELECT id, `area`, DATE_FORMAT(`date`,'%m/%d/%Y') AS xdate, occasion FROM omdcpayroll.pay_holiday_local WHERE id = '$_POST[id]';"));
		break;
		case "updateLocHoliday":
			$mydb->dbquery("UPDATE omdcpayroll.pay_holiday_local SET `area` = '$_POST[area]', `date` = '".$mydb->formatDate($_POST['date'])."', occasion = '".$mydb->escapeString(htmlentities($_POST['occasion']))."' WHERE id = '$_POST[rid]';");
		break;
		case "newLocHoliday":
			$mydb->dbquery("INSERT IGNORE INTO omdcpayroll.pay_holiday_local (`area`,`date`,occasion) VALUES ('$_POST[area]','".$mydb->formatDate($_POST['date'])."','".$mydb->escapeString(htmlentities($_POST['occasion']))."');");
		break;
		case "deleteLocHoliday":
			$mydb->dbquery("delete from omdcpayroll.pay_holiday_local where id = '$_POST[rid]';");
		break;
		
		
		case "getLeave":
			echo json_encode($mydb->getArray("SELECT *, date_format(`date`,'%m/%d/%Y') as tdate, if(date_from != '0000-00-00',date_format(date_from,'%m/%d/%Y'),'') as dtf, if(date_to != '0000-00-00',date_format(date_to,'%m/%d/%Y'),'') as dt2 FROM omdcpayroll.pay_loa WHERE trans_id = '$_POST[id]';"));
		break;
		case "updateLeave":
			$mydb->dbquery("UPDATE IGNORE omdcpayroll.pay_loa set emp_id = '$_POST[emp_id]', emp_name='".$mydb->escapeString($_POST['emp_name'])."', `date` = '".$mydb->formatDate($_POST['date'])."', `length` = '$_POST[length]', date_from = '". $mydb->formatDate($_POST['dateFrom']) ."', date_to = '". $mydb->formatDate($_POST['dateTo']) ."',  leave_type = '$_POST[type]', reasons = '".$mydb->escapeString(htmlentities($_POST['reasons']))."', address_on_leave = '".$mydb->escapeString($_POST['address'])."', w_pay = '$_POST[w_pay]', updated_by = '$_SESSION[userid]', updated_on = NOW() WHERE trans_id = '$_POST[rid]';");
		break;
		case "newLeave":
			$mydb->dbquery("INSERT IGNORE INTO omdcpayroll.pay_loa (emp_id,emp_name,`date`,date_from,date_to,`length`,leave_type,reasons,address_on_leave,w_pay) values ('$_POST[emp_id]','".$mydb->escapeString($_POST['emp_name'])."','".$mydb->formatDate($_POST['date'])."','". $mydb->formatDate($_POST['dateFrom']) ."','". $mydb->formatDate($_POST['dateTo']) ."','$_POST[length]','$_POST[type]','".$mydb->escapeString(htmlentities($_POST['reasons']))."','".$mydb->escapeString($_POST['address'])."','$_POST[w_pay]');");
		break;
		case "deleteLeave":
			$mydb->dbquery("update ignore omdcpayroll.pay_loa set file_status = 'Deleted', deleted_by = '$_SESSION[userid]', deleted_on = now() where trans_id = '$_POST[rid]';");
		break;
		case "getDeduction":
			echo json_encode($mydb->getArray("select *, format(amount,2) as amt from omdcpayroll.emp_otherdeductions where record_id = '$_POST[id]';"));
		break;
		case "updateDeduction":
			$mydb->dbquery("UPDATE ignore omdcpayroll.emp_otherdeductions set emp_id = '$_POST[emp_id]', deduction_type = '$_POST[type]', amount = '".$mydb->formatDigit($_POST['amount'])."', period_id = '$_POST[period]', remarks = '".$mydb->escapeString($_POST['remarks'])."', updated_by = '$_SESSION[userid]', updated_on = NOW() WHERE record_id = '$_POST[rid]';");
		break;
		case "newDeduction":
			$mydb->dbquery("insert ignore into omdcpayroll.emp_otherdeductions (emp_id, deduction_type, amount, period_id, remarks, created_by, created_on) values ('$_POST[emp_id]','$_POST[type]','".$mydb->formatDigit($_POST['amount'])."','$_POST[period]','".$mydb->escapeString($_POST['remarks'])."','$_SESSION[userid]',now());");
		break;
		case "deleteDeduction":
			$mydb->dbquery("update ignore omdcpayroll.emp_otherdeductions set file_status = 'Deleted', deleted_by = '$_SESSION[userid]', deleted_on = now() where record_id = '$_POST[rid]';");
		break;
		case "getIncentive":
			echo json_encode($mydb->getArray("select a.*, format(amount,2) as amt, date_format(incentive_date,'%m/%d/%Y') as indate, CONCAT(b.lname,', ',b.fname,' ',LEFT(b.mname,1),'.') AS emp_name from omdcpayroll.emp_incentives a left join omdcpayroll.emp_masterfile b on a.emp_id = b.emp_id where a.record_id = '$_POST[id]';"));
		break;
		case "updateIncentive":
			$mydb->dbquery("UPDATE ignore omdcpayroll.emp_incentives set emp_id = '$_POST[emp_id]', incentive_type = '$_POST[type]', amount = '".$mydb->formatDigit($_POST['amount'])."', incentive_date = '".$mydb->formatDate($_POST['date'])."', remarks = '".$mydb->escapeString($_POST['remarks'])."', updated_by = '$_SESSION[userid]', updated_on = NOW() WHERE record_id = '$_POST[rid]';");
		break;
		case "newIncentive":
			$mydb->dbquery("insert ignore into omdcpayroll.emp_incentives (emp_id,incentive_type,amount,incentive_date,remarks,created_by,created_on) values ('$_POST[emp_id]','$_POST[type]','".$mydb->formatDigit($_POST['amount'])."','".$mydb->formatDate($_POST['date'])."','".$mydb->escapeString($_POST['remarks'])."','$_SESSION[userid]',now());");
		break;
		case "deleteIncentive":
			$mydb->dbquery("update ignore omdcpayroll.emp_incentives set file_status = 'Deleted', deleted_by = '$_SESSION[userid]', deleted_on = now() where record_id = '$_POST[rid]';");
		break;
		case "getLoan":
			echo json_encode($mydb->getArray("SELECT *, DATE_FORMAT(date_loan,'%m/%d/%Y') AS date_loan, DATE_FORMAT(effective_date,'%m/%d/%Y') AS eff, FORMAT(loan_amt,2) AS gamt, FORMAT(semi_amrtz,2) AS amrtz FROM omdcpayroll.emp_loanmasterfile WHERE record_id = '$_POST[id]';"));
		break;
		case "updateLoan":
			$mydb->dbquery("UPDATE ignore omdcpayroll.emp_loanmasterfile SET emp_id = '$_POST[emp_id]', loan_type = '$_POST[type]', date_loan = '".$mydb->formatDate($_POST['loan_date'])."', loan_amt = '".$mydb->formatDigit($_POST['amount'])."', loan_terms = '$_POST[terms]', effective_date = '".$mydb->formatDate($_POST['eff'])."', semi_amrtz = '".$mydb->formatDigit($_POST['amrtz'])."', multiplier = '$_POST[mplier]', monthly_amrtz = '" . $mydb->formatDigit($_POST['monthly_amrtz']) . "', dedu_type = '$_POST[dedu_type]', active = '$_POST[active]', offsetted = '$_POST[offsetted]', amount_offsetted = '".$mydb->formatDigit($_POST['amt_offsetted'])."', remarks = '".$mydb->escapeString($_POST['remarks'])."', updated_by = '$_SESSION[userid]', updated_on = NOW() WHERE record_id = '$_POST[rid]';");
		break;
		case "newLoan":
			$mydb->dbquery("insert ignore into omdcpayroll.emp_loanmasterfile (emp_id,loan_type,date_loan,loan_amt,loan_terms,effective_date,semi_amrtz,multiplier,monthly_amrtz,dedu_type,active,remarks,created_by,created_on) values ('$_POST[emp_id]','$_POST[type]','".$mydb->formatDate($_POST['loan_date'])."','".$mydb->formatDigit($_POST['amount'])."','$_POST[terms]','".$mydb->formatDate($_POST['eff'])."','".$mydb->formatDigit($_POST['amrtz'])."','$_POST[mplier]','" . $mydb->formatDigit($_POST['monthly_amrtz']) . "','$_POST[dedu_type]','$_POST[active]','".$mydb->escapeString($_POST['remarks'])."','$_SESSION[userid]',now());");
		break;
		case "deleteLoan":
			$mydb->dbquery("update ignore omdcpayroll.emp_loanmasterfile set file_status = 'Deleted', deleted_by = '$_SESSION[userid]', deleted_on = now() where record_id = '$_POST[rid]';");
		break;
		case "getAdjustment":
			echo json_encode($mydb->getArray("SELECT a.*, format(amount,2) as amt, date_format(adjustment_date,'%m/%d/%Y') as adjdate,CONCAT(b.lname,', ',b.fname,' ',LEFT(b.mname,1),'.') AS emp_name FROM omdcpayroll.emp_adjustments a left join omdcpayroll.emp_masterfile b on a.emp_id = b.emp_id WHERE a.record_id = '$_POST[id]';"));
		break;
		case "updateAdjustment":
			$mydb->dbquery("UPDATE omdcpayroll.emp_adjustments SET emp_id = '$_POST[emp_id]', adjustment_type = '$_POST[type]', `taxable` = '$_POST[taxable]', amount = '".$mydb->formatDigit($_POST['amount'])."', adjustment_date = '".$mydb->formatDate($_POST['date'])."', remarks = '".$mydb->escapeString($_POST['remarks'])."', updated_by = '$_SESSION[userid]', updated_on = NOW() WHERE record_id = '$_POST[rid]';");
		break;
		case "newAdjustment":
			$mydb->dbquery("insert into omdcpayroll.emp_adjustments (emp_id,adjustment_type,`taxable`,amount,adjustment_date,remarks,created_by,created_on) values ('$_POST[emp_id]','$_POST[type]','$_POST[taxable]','".$mydb->formatDigit($_POST['amount'])."','".$mydb->formatDate($_POST['date'])."','".$mydb->escapeString($_POST['remarks'])."','$_SESSION[userid]',now());");
		break;
		case "deleteAdjustment":
			$mydb->dbquery("update omdcpayroll.emp_adjustments set file_status = 'Deleted', deleted_by = '$_SESSION[userid]', deleted_on = now() where record_id = '$_POST[rid]';");
		break;
		case "getBasic2":
			echo json_encode($mydb->getArray("SELECT *, format(amount,2) as amt FROM omdcpayroll.emp_basic2 WHERE record_id = '$_POST[id]';"));
		break;
		case "updateBasic2":
			$mydb->dbquery("UPDATE omdcpayroll.emp_basic2 SET pay_type='$_POST[ptype]', emp_id = '$_POST[emp_id]', `taxable` = '$_POST[taxable]', amount = '".$mydb->formatDigit($_POST['amount'])."', period_id = '$_POST[period]', remarks = '".$mydb->escapeString($_POST[remarks])."', updated_by = '$_SESSION[userid]', updated_on = NOW() WHERE record_id = '$_POST[rid]';");
		break;
		case "newBasic2":
			$mydb->dbquery("insert ignore into omdcpayroll.emp_basic2 (pay_type,emp_id,`taxable`,amount,period_id,remarks,created_by,created_on) values ('$_POST[ptype]','$_POST[emp_id]','$_POST[taxable]','".$mydb->formatDigit($_POST[amount])."','$_POST[period]','".$mydb->escapeString($_POST[remarks])."','$_SESSION[userid]',now());");
		break;
		case "deleteBasic2":
			$mydb->dbquery("update omdcpayroll.emp_basic2 set file_status = 'Deleted', deleted_by = '$_SESSION[userid]', deleted_on = now() where record_id = '$_POST[rid]';");
		break;
		case "populateEmp":
			$qemp = $mydb->dbquery("select emp_id, concat(lname,', ',fname,' ',left(mname,1),'.') as name from omdcpayroll.emp_masterfile where file_status != 'DELETED' and payroll_type = '$_POST[type]' and employment_status not in (7,8,9,10) order by lname, fname;");
			while($emprow = $qemp->fetch_array(MYSQLI_BOTH)) {
				print "<option value='$emprow[0]'>".strtoupper($emprow[1])."</option>\n";
			}
		break;
		case "getEmpName":
			echo json_encode($mydb->getArray("SELECT CONCAT('(',emp_id,') ',lname,', ',fname,' ',mname) FROM omdcpayroll.emp_masterfile WHERE EMP_ID = '$_POST[eid]';"));
		break;
		
		case "saveSchedule":
			$mypay = new payroll($_POST['period']);
			list($area) = $mydb->getArray("select `area` from omdcpayroll.emp_masterfile where emp_id = '$_POST[eid]';");
			$mypay->checkHoliday($_POST['date'],$_POST['shift'],$area);
		
			list($isLock) = $mydb->getArray("SELECT `SLOCK` from omdcpayroll.emp_dtrfinal where emp_id = '$_POST[eid]' and `date` = '$_POST[date]';");
			if($isLock == '') {
				$mydb->dbquery("INSERT IGNORE INTO omdcpayroll.emp_dtrfinal (EMP_ID,DEPT,PAY_BATCH,PERIOD_ID,`DATE`,SHIFT,HD_TYPE,SCREATED_BY,SCREATED_ON) values ('$_POST[eid]','$_POST[dept]','$_POST[batch]','$_POST[period]','$_POST[date]','$_POST[shift]','".$mypay->htype."','$_SESSION[userid]',now());");
			} else {
				if($isLock == 'N') {
					$mydb->dbquery("UPDATE IGNORE omdcpayroll.emp_dtrfinal set SHIFT = '$_POST[shift]', HD_TYPE='".$mypay->htype."' where EMP_ID = '$_POST[eid]' and `DATE` = '$_POST[date]';");
				}
			}
		break;
		
		case "saveEDTR":
			list($area) = $mydb->getArray("select `area` from omdcpayroll.emp_masterfile where emp_id = '$_POST[eid]';");
			list($isExist) = $mydb->getArray("select count(*) from omdcpayroll.emp_dtrfinal where `DATE` = '$_POST[date]' and EMP_ID = '$_POST[eid]';");
			if($isExist > 0) {
				$mydb->dbquery("update ignore omdcpayroll.emp_dtrfinal set `$_POST[type]` = '$_POST[val]:00' where `DATE` = '$_POST[date]' and EMP_ID = '$_POST[eid]';");
			} else {
				$mydb->dbquery("insert ignore into omdcpayroll.emp_dtrfinal (PERIOD_ID,EMP_ID,EMP_TYPE,DEPT,`DATE`,`$_POST[type]`) values ('$_POST[period]','$_POST[eid]','$_POST[etype]','$_POST[dept]','$_POST[date]','$_POST[val]:00');");
			}

			list($insec,$outsec) = $mydb->getArray("select time_to_sec(CLOCKIN),time_to_sec(CLOCKOUT) from omdcpayroll.emp_dtrfinal where EMP_ID = '$_POST[eid]' and `DATE` = '$_POST[date]';");
			

			if($insec > 0 && $outsec > 0) {
				$mypay = new payroll($_POST['period']);
				$mypay->computeTimeSheets($_POST['eid'],$_POST['date'],$_POST['sched'],$insec,$outsec);
				$mypay->checkHoliday($_POST['date'],$_POST['sched'],$area);
				$mydb->dbquery("update ignore omdcpayroll.emp_dtrfinal set tot_work='".$mypay->twork."',tot_late='".$mypay->late."', tot_ut='".$mypay->ut."', reg_ot = '".$mypay->overtime."', sun_ot = '".$mypay->restday."', prem_ot='".$mypay->premium."',hd_type='". $mypay->htype . "' where emp_id='$_POST[eid]' and date='$_POST[date]';");
			} else { 
				$mydb->dbquery("update ignore omdcpayroll.emp_dtrfinal set tot_work = 0, tot_late = 0, tot_ut = 0, reg_ot = 0, sun_ot = 0, prem_ot = 0 where emp_id = '$_POST[eid]' and `date` = '$_POST[date]';");
			}
		
		break;
		case "otApprove":
			$mydb->dbquery("update ignore omdcpayroll.emp_dtrfinal set OT_APPROVE = 'Y' where record_id = '$_POST[rid]';");
		break;
		case "otDisApprove":
			$mydb->dbquery("update ignore omdcpayroll.emp_dtrfinal set OT_APPROVE = 'N' where record_id = '$_POST[rid]';");
		break;	
	}
	
	@mysql_close($con);
?>