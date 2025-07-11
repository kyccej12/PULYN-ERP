<?php
	
	session_start();
	//ini_set("display_errors","On");
	ini_set("max_execution_time",-1);
	require_once "../handlers/_payroll.php";
	
	if($_GET['dept'] != '') { $f1 = " and a.DEPT = '$_GET[dept]' "; $f1a = " and dept = '$_GET[dept] '"; $lk1 = "&dept=$_GET[dept]"; } else { $f1 = ""; $f1a = ""; $lk1 = ""; }
	if($_GET['eid'] != '') { $f2 = " and a.emp_id = '$_GET[eid]' "; $f2a = " and emp_id = '$_GET[eid] ' "; $lk2 = "&eid=$_GET[eid]"; } else { $f2 = ""; $f2_2 =""; $lk2 = ""; }
	
	$pay = new payroll($_REQUEST['cutoff']);
	
		
	/* Delete Similar Records Previously Processed */
	$pay->dbquery("delete from omdcpayroll.emp_payslip where period_id = '". $pay->cutoff ."' $f1a $f2a;");
	$pay->dbquery("delete from omdcpayroll.emp_deductionmaster where period_id = '". $pay->cutoff ."' $f1a $f2a;");
	
	$mainQuery = $pay->dbquery("SELECT emp_id, emp_type, payroll_type, payroll_factor, CONCAT(LNAME,', ',FNAME,' ',LEFT(MNAME,1),'.') AS emp_name, dept, `area`, acct_no, vl_credit, basic_rate, cola, allowance, allowance_type, if(allowance_type='M',ROUND(nontax_allowance/2,2),nontax_allowance) AS ntx, HDMF_PREMIUM AS hdmf, w_tax, emp_tax, coop_premium, retirement_plan, W_SSS, W_PHILHEALTH, W_HDMF, FLEX_TIME, EMPLOYMENT_STATUS as estat FROM omdcpayroll.emp_masterfile a WHERE 1=1 $f1 $f2 AND employment_status NOT IN (7,8,9,10);");
	
	while($mainRow = $mainQuery->fetch_array()) {
	
		$q = $pay->dbquery("SELECT ROUND(SUM(tot_work)/8,2) AS twork, SUM(ROUND(tot_late*60)) AS late, SUM(tot_ut) AS ut, SUM(reg_ot) AS reg_ot, SUM(prem_ot) AS prem_ot, ROUND(sum(sun_ot),2) as sun_ot, ROUND(SUM(tot_late+tot_ut) / 8,2) AS lut FROM omdcpayroll.emp_dtrfinal a WHERE a.date between '" . $pay->dtf . "' and '" . $pay->dt2 . "' and a.emp_id = '$mainRow[emp_id]' and HD_TYPE = 'NA' GROUP BY emp_id;");
		$row = $q->fetch_array();

			
		$base = 0; $vl = 0; $adj = 0; $cola = 0; $basic2 = 0; $basicDeductions = 0;	$nonTaxableAdjustment = 0;	$nonTaxableAdjustmentCurrent = 0; $incentives = 0;
		$netpay = 0; $taxable = 0;	$wtax = 0;	$allowance = 0;	$transpo = 0; $meal = 0; $gross = 0; $basic_pay = 0; $basic_day = 0; $lateAmount = 0; $utAmount = 0;
		$s_rate = 0; $d_rate = 0; $lt = 0;	$ott = 0; $sil_pay = 0; $holiday_on_restday = 0;
	
		
		$pay->checkVL($mainRow['emp_id'],$mainRow['vl_credit']);
		$pay->checkSIL($mainRow['emp_id'],$mainRow['estat']);
		$pay->getRates($mainRow['payroll_type'],$mainRow['basic_rate'],$mainRow['payroll_factor']);
		$pay->getAbsences($mainRow['emp_id'],$mainRow['area']);
		
		if($pay->dtrCount > 0) {
		
			if($mainRow['payroll_type'] == 2) {
				$base = $row['twork'];
				$monthlyRate = ROUND($mainRow['basic_rate'] * 26,2);
				$basic_pay = ROUND($row['twork'] * $mainRow['basic_rate'],2);
				$pay->hrate = ROUND($mainRow['basic_rate']/8,2);
				$pay->vl = 0;
				$pay->absences = 0;
				
				$pay->myPremiums($monthlyRate,$mainRow['emp_id'],$mainRow['emp_type'],$mainRow['hdmf'],$mainRow['W_SSS'],$mainRow['W_PHILHEALTH'],$mainRow['W_HDMF']);
			
			} else {	
			
				if($mainRow['FLEX_TIME'] == 'Y') {
					$base = ROUND($row['twork']/8,2);
					$basic_pay = ROUND($pay->dailyRate * $base,2);
				} else {
					
					$lateAmount = ROUND($pay->minRate * $row['late'],2);
					$utAmount = ROUND($pay->hrate * $row['ut'],2);
					
					$basicDeductions = ROUND(($pay->holidayCount + $pay->vl + $pay->absences + $pay->sil) * $pay->dailyRate,2) + $lateAmount + $utAmount;
					$base = $pay->baseDays - $row['lut'] - $pay->holidayCount - $pay->vl - $pay->sil - $pay->absences;
					$basic_pay = $pay->semiRate - $basicDeductions; 
				}
					
				$pay->myPremiums($mainRow['basic_rate'],$mainRow['emp_id'],$mainRow['emp_type'],$mainRow['hdmf'],$mainRow['W_SSS'],$mainRow['W_PHILHEALTH'],$mainRow['W_HDMF']);
			
			}
			
			/* Coop Premium & Retirement Plan */
			$coop = $mainRow['coop_premium'];
			
			/* COLA Computation */
			if($mainRow['cola'] > 0) {	$cocaCola = 13 - $row['lut'] - $pay->absences; $cola = ROUND($cocaCola * $mainRow['cola'],2); }
		
			/* Allowance-Taxable Computation */
			if($row['allowance'] > 0) {	if($row['allowance_type'] == "D") {	$allowance = ROUND($base * $row['allowance'],2); } else { $allowance = ROUND($row['allowance'] / 2,2);	}}
		
			/* Nontaxable Allowance */
			if($mainRow['ntx'] > 0) {
				if($mainRow['allowance_type'] == 'M') {
					$ntx = ROUND($mainRow['ntx'] - (($mainRow['ntx']/13) * $pay->absences),2);
				} else {
					$ntx = $mainRow['ntx'] * $base;
				}
			}
			
			if($basic_pay > 0) {
				$s = $pay->dbquery("select record_id, deduction_type, amount, created_on from omdcpayroll.emp_otherdeductions where emp_id = '$mainRow[emp_id]' and period_id = '". $pay->cutoff ."' and file_status != 'Deleted';");
				if($s) {
					while(list($did,$dtype,$damt,$dd8) = $s->fetch_array(MYSQLI_BOTH)) {
						$pay->dbquery("insert ignore into omdcpayroll.emp_deductionmaster (period_id,pay_type,emp_type,emp_id,dept,area,type,ref_id,ref_date,ref_type,amount,posted_on,posted_by) values ('". $pay->cutoff ."','". $mainRow['payroll_type'] ."','$mainRow[emp_type]','$mainRow[emp_id]','$mainRow[dept]','$mainRow[area]','O','$did','$dd8','$dtype','$damt',now(),'$_SESSION[userid]');");
					}
				}
				$pay->loadLoans($mainRow['emp_id'],$mainRow['emp_type'],$mainRow['area'],$mainRow['dept'],$mainRow['payroll_type']);
			}
		
			if($row['prem_ot'] > 0) {
				list($npOrdinary,$np1) = $pay->getArray("select ROUND((". $pay->hrate . " * sum(prem_ot)) * 0.10,2), sum(prem_ot) as np1 from omdcpayroll.emp_dtrfinal where  `date` between '". $pay->dtf ."' and '" . $pay->dt2 . "' and emp_id = '$mainRow[emp_id]' and hd_type = 'NA' and OT_APPROVE = 'Y';");
				list($npRegular,$np2) = $pay->getArray("select ROUND(((". $pay->hrate . " * 2) * sum(prem_ot)) * 0.10,2), sum(prem_ot) as np1 from omdcpayroll.emp_dtrfinal where  `date` between '". $pay->dtf ."' and '" . $pay->dt2 . "' and emp_id = '$mainRow[emp_id]' and hd_type = 'LH' and OT_APPROVE = 'Y';");
				list($npSpecial,$np3) = $pay->getArray("select ROUND(((". $pay->hrate . " * 1.3) * sum(prem_ot)) * 0.10,2), sum(prem_ot) as np1 from omdcpayroll.emp_dtrfinal where  `date` between '". $pay->dtf ."' and '" . $pay->dt2 . "' and emp_id = '$mainRow[emp_id]' and hd_type = 'SH' and OT_APPROVE = 'Y';");
				list($npSunday,$np4) = $pay->getArray("select ROUND(((". $pay->hrate . " * 1.3) * sum(prem_ot)) * 0.10,2), sum(prem_ot) as np1 from omdcpayroll.emp_dtrfinal where  `date` between '". $pay->dtf ."' and '" . $pay->dt2 . "' and emp_id = '$mainRow[emp_id]' and hd_type = 'RD' and OT_APPROVE = 'Y';");
				$npremium_pay = $npOrdinary+$npRegular+$npSpecial+$npSunday;
				$npHrs = $np1 + $np2 + $np3 + $np4;
			} else { $npremium_pay = 0; $npHrs = 0; }
			
			/* Regular Overtime */
			$rot_pay = 0; $rot_pay_hrs = 0;
			list($rot_pay,$rot_pay_hrs) = $pay->getArray("select ifnull(ROUND((sum(reg_ot) * ". $pay->hrate .") * 1.25,2),0), ifnull(sum(reg_ot),0) from omdcpayroll.emp_dtrfinal where `date` between '". $pay->dtf ."' and '" . $pay->dt2 . "' and emp_id = '$mainRow[emp_id]' and hd_type = 'NA' and OT_APPROVE = 'Y';");
			
			/* Legal Holiday Overtime Computation */
			$lh_ot_hrs = 0; $lh_ot = 0; $lh_ot_ex_hrs = 0; $lh_ot_ex = 0;  
			$lhQuery = $pay->dbquery("SELECT DISTINCT `date`,date_format(`date`,'%a') FROM omdcpayroll.pay_holiday_nat WHERE `date` between '". $pay->dtf ."' and '" . $pay->dt2 . "' and type = '1';");
			while(list($lhDate,$lhDay) = $lhQuery->fetch_array()) {
				if($lhDay == 'Sun') { $factor = 1.30; } else { $factor = 1; }
				$lhRow = $pay->getArray("select ROUND($factor * (tot_work * " . $pay->hrate . "),2), ROUND(1.30 * (reg_ot * " . $pay->hrate. "),2), sum(tot_work), sum(reg_ot) from omdcpayroll.emp_dtrfinal where `date` = '$lhDate' and emp_id = '$mainRow[emp_id]';");
				$lh_ot += $lhRow[0]; $lh_ot_ex += $lhRow[1]; $lh_ot_hrs += $lhRow[2]; $lh_ot_ex_hrs += $lhRow[3];
			}
			
			$sh_ot_hrs = 0; $sh_ot = 0; $sh_ot_ex_hrs = 0; $sh_ot_ex = 0;
			$shQuery = $pay->dbquery("SELECT DISTINCT `date`,DATE_FORMAT(`date`,'%a') FROM omdcpayroll.pay_holiday_nat WHERE `date` BETWEEN '". $pay->dtf ."' AND '" . $pay->dt2 . "' AND `type` = '2' UNION SELECT DISTINCT `date`,DATE_FORMAT(`date`,'%a') FROM omdcpayroll.pay_holiday_local WHERE `date` BETWEEN '". $pay->dtf ."' AND '" . $pay->dt2 . "' AND area = '$mainRow[area]';");
			while(list($shDate,$shDay) = $shQuery->fetch_array()) {
				if($shDay == 'Sun') { $factor = 0.50; } else { $factor = 0.30; }
				$shRow = $pay->getArray("select ROUND($factor * (tot_work * " . $pay->hrate . "),2), ROUND(1.30 * (reg_ot * " . $pay->hrate. "),2),sum(tot_work), sum(reg_ot) from omdcpayroll.emp_dtrfinal where `date` = '$shDate' and emp_id = '$mainRow[emp_id]';");
				$sh_ot += $shRow[0]; $sh_ot_ex += $shRow[1]; $sh_ot_hrs += $shRow[2]; $sh_ot_ex_hrs += $shRow[3];
			}
			unset($lhRow);
			unset($shRow);
				
			/* Sunday Overtime */
			$sun_ot_hrs = 0; $sun_ot = 0; $sun_otex_hrs = 0; $sun_otex = 0;
			list($sun_ot_hrs,$sun_ot,$sun_otex_hrs,$sun_otex) = $pay->getArray("select ifnull(sum(tot_work),0), ifnull(ROUND((sum(tot_work) * ". $pay->hrate .") * 1.30,2),0), ifnull(ROUND(sum(reg_ot) * ". $pay->hrate ." * 1.69,2),0), sum(reg_ot) from omdcpayroll.emp_dtrfinal where  `date` between '". $pay->dtf ."' and '" . $pay->dt2 . "' and emp_id = '$mainRow[emp_id]' and hd_type='RD' and OT_APPROVE = 'Y';");
			
		} else { 
			$pay->sss_premium = 0; 
			$pay->sss_premium_er = 0;
			$pay->ph_premium = 0; 
			$pay->ph_premium_er = 0;
			$pay->pg_premium = 0;
			$pay->pg_premium_er = 0; 
			$allowance = 0; 
			$cola = 0; 
			$ntx = 0;
			$retplan = 0;
			$coop = 0;
		}
		
		/* Service Incentive Leave */
		$other_leaves = ROUND($pay->sil * $pay->dailyRate,2);
		
		/* Holiday Pay */
		$lholiday_pay = ROUND($pay->lholiday * $pay->dailyRate,2); 
		$sholiday_pay = ROUND($pay->sholiday * $pay->dailyRate,2);
		$holiday_on_restday = ROUND($pay->onRestDayHoliday * $pay->dailyRate,2);
	
		/* Leaves Pay */
		$vl_pay = ROUND($pay->vl * $pay->dailyRate,2); 
		$sl_pay = ROUND($pay->sl * $pay->dailyRate,2);

		/* Incentives */
		$incQ = $pay->dbquery("SELECT SUM(amount) FROM omdcpayroll.emp_incentives WHERE emp_id = '$mainRow[emp_id]' AND incentive_date between '". $pay->dtf ."' and '".$pay->dt2."' and file_status != 'Deleted';");
		if($incQ) { list($incentives) = $incQ->fetch_array(); } 
		
		/* Adjustments */
		$adjQ = $pay->dbquery("SELECT SUM(IF(adjustment_type='CR',(amount*-1),amount)) AS adjustment FROM omdcpayroll.emp_adjustments WHERE emp_id = '$mainRow[emp_id]' AND adjustment_date between '".$pay->dt2."' and '".$pay->dt2."'and file_status != 'Deleted' GROUP BY emp_id;");
		if($adjQ) {	list($adj) = $adjQ->fetch_array(); }
			
		/* Basic2 / Base-Off Pay*/
		$basic2Q = $pay->dbquery("SELECT sum(amount) FROM omdcpayroll.emp_basic2 WHERE emp_id = '$mainRow[emp_id]' AND period_id = '". $pay->cutoff ."' and file_status != 'Deleted' GROUP BY emp_id;");
		if($basic2Q) {	list($basic2) = $basic2Q->fetch_array(); }
		
		/* Compute Total Gross */
		$gross = $basic_pay + $basic2 + $allowance + $ntx + $rot_pay + $npremium_pay + $sl_pay + $vl_pay + $other_leaves + $lholiday_pay + $sholiday_pay + $holiday_on_restday + $lh_ot + $lh_ot_ex + $sh_ot + +$sh_ot_ex + $sun_ot + $sun_otex + $adj + $incentives;
		
		if($mainRow['w_tax'] == 'N') {
			$wtax = 0;
		} else {
			$taxable = $gross - $pay->sss_premium - $pay->ph_premium - $pay->pg_premium - $row['ntx'] - $incentives;
			if($mainRow['emp_tax'] > 0) {
				$wtax = $mainRow['emp_tax'];
			} else {
				if($taxable > 10417) {
					list($wtax) = $pay->getArray("SELECT ROUND(((0$taxable - base) * ex_factor)+base_tax,2) FROM omdcpayroll.pay_newtax WHERE 0$taxable >= base AND 0$taxable <= top;");
				} else { $wtax = 0; }
			}
		}
		
		/* Loans Total */
		$ltQ = $pay->dbquery("select sum(amount) from omdcpayroll.emp_deductionmaster where period_id = '". $pay->cutoff . "' and emp_id = '$mainRow[emp_id]' and type = 'L';");
		$ottQ = $pay->dbquery("select sum(amount) from omdcpayroll.emp_deductionmaster where period_id = '". $pay->cutoff . "' and emp_id = '$mainRow[emp_id]' and type = 'O';");
		
		if($ltQ) { list($lt) = $ltQ->fetch_array(); }
		if($ottQ) {	list($ott) = $ottQ->fetch_array(); }
		
		$netpay = ROUND(($gross - $pay->sss_premium - $pay->ph_premium - $pay->pg_premium - $wtax - $lt - $ott - $coop),2);
		
		if($netpay != 0) {
			$pay->dbquery("insert ignore into omdcpayroll.emp_payslip (period_id,pay_type,proj,dept,acct_no,emp_id,emp_name,monthly_rate,semi_rate,daily_rate,basic_day,absences,late,undertime,basic_pay,cola,basic2_pay,vacation_leave,
						   sick_leave,other_leaves,holiday_on_restday,legal_holiday,special_holiday,ot_regular_hrs,ot_regular,night_premium_hrs,night_premium,ot_sunday_hrs,ot_sunday,ot_sundayex_hrs,ot_sundayex,ot_legalholiday_hrs,ot_legalholiday,ot_legalholidayex_hrs,ot_legalholidayex,
						   ot_specialholiday_hrs,ot_specialholiday,ot_specialholidayex_hrs,ot_specialholidayex,allowance,nontax_allowance,incentives,gross_pay,sss_premium,
						   sss_premium_er,pagibig_premium,pagibig_premium_er,philhealth_premium,philhealth_premium_er,taxable_income,wtax,coop_premium,retirement_plan,sss_loan,hdmf_loan,coop_loan,jag_loan,cash_adv,laboratory,
						   other_loans,loans_total,others_total,adjustments,net_pay,processed_on,processed_by) values ('". $pay->cutoff ."','". $mainRow['payroll_type'] ."','$mainRow[area]','$mainRow[dept]','$mainRow[acct_no]',
						   '$mainRow[emp_id]','$mainRow[emp_name]','$mainRow[basic_rate]','" . $pay->semiRate . "','". $pay->dailyRate . "','$base','" . ROUND($pay->absences * $pay->dailyRate,2) . "','$lateAmount','$utAmount','$basic_pay','$cola','$basic2','$vl_pay','$sl_pay','$other_leaves',
						   '$holiday_on_restday','$lholiday_pay','$sholiday_pay','$rot_pay_hrs','$rot_pay','$npHrs','$npremium_pay','$sun_ot_hrs','$sun_ot','$sun_otex_hrs','$sun_otex','$lh_ot_hrs','$lh_ot','$lh_ot_ex_hrs','$lh_ot_ex','$sh_ot_hrs','$sh_ot',
						   '$sh_ot_ex_hrs','$sh_ot_ex','$allowance','$ntx','$incentives','$gross','". $pay->sss_premium ."','". $pay->sss_premium_er ."',
						   '" . $pay->pg_premium ."','" . $pay->pg_premium_er ."','". $pay->ph_premium ."','". $pay->ph_premium_er ."','$taxable','$wtax','$coop','$retplan','".$pay->getLoans($mainRow['emp_id'],$pay->cutoff,'1')."',
						   '".$pay->getLoans($mainRow['emp_id'],$pay->cutoff,'2')."','".$pay->getLoans($mainRow['emp_id'],$pay->cutoff,'4')."','".$pay->getLoans($mainRow['emp_id'],$pay->cutoff,'7')."','".$pay->getLoans($mainRow['emp_id'],$pay->cutoff,'10')."',
						   '".$pay->getLoans($mainRow['emp_id'],$pay->cutoff,'8')."','".$pay->getOtherLoans($mainRow['emp_id'],$pay->cutoff)."','$lt','$ott','$adj','$netpay',now(),'".$_SESSION['userid']."');");
		}
	}
	
	if($pay->wom == 1) {
		header("Location: reports/payrollsummary-15.php?cutoff=".$pay->cutoff."&dept=".$_GET['dept']);
	} else {
		header("Location: reports/payrollsummary-30.php?cutoff=".$pay->cutoff."&dept=".$_GET['dept']);
	}
	
	
	//header("Location: payslip.php?cutoff=".$pay->cutoff."&pay_type=".$mainRow['payroll_type']."$lk1"."$lk2");
?>