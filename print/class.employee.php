<?php
	class Employee{

	public $id_no;
	public $period;
	public $desg;
	public $bank_acct;
	public $empname;
	public $pay_type;
	public $basic_rate;
	public $basic_pay;
	public $payhours;
	public $payamount;
	public $slvl;
	public $ot_hrs;
	public $ot_pay;
	public $sunday;
	public $sunda_pay;
	public $legal_hol;
	public $legal_pay;
	public $legal_hrs;
	public $legal_hrspay;
	public $legal_ot;
	public $legal_otpay;
	public $legal_sunday;
	public $legal_sundaypay;
	public $legalsunday_ot;
	public $legalsunday_otpay;
	public $special_hol;
	public $special_holpay;
	public $special_holhrs;
	public $special_holhrspay;
	public $late_mins;
	public $late_amt;
	public $absent_hrs;
	public $absent_amt;
	public $ut_mins;
	public $ut_amt;
	public $gross;
	public $sss_prem;
	public $phic_prem;
	public $hdmf_prem;
	public $wtax;
	public $ca;
	public $short;
	public $sssloan;
	public $pagibigloan;
	public $bread;
	public $laptop;
	public $productvale;
	public $boarding;
	public $load;
	public $uniform;
	public $nameplate;
	public $oth;
	public $chrg;
	public $nega;
	public $hmo;
	public $totalded;
	public $takehome;
	public $position;

	public $daily_rate;
	public $hourly_rate;
	public $minutely_rate;
	public $company;
	public $branch;
	public $daysworked;
	
	public $clothing;	
	public $laundry;
	public $rsub;
	public $insurance;
	public $ontx;
	public $ddsched;
	public $adjustment;
		public function __construct($id_no,$period){
			$this->id_no = $id_no;
			$this->period = $period;
			$this->desg = $desg;
			
			$deduc_type = getArray("SELECT dedu_type FROM hris.pay_periods a WHERE a.period_id  ='".$this->period."';");
			$this->ddsched = $deduc_type['dedu_type'];
			$res = getArray("SELECT id_no,CONCAT(lname,', ',fname,' ',mname) AS emp_name,clothing,laundry,rice_subsidy,other_non_tax,company,branch,designation,insurance,bank_acct,department,pay_type,IF(pay_type='SEMI',monthly_rate,daily_rate) AS basic_rate,hmo,wtax FROM hris.e_master a WHERE a.id_no = '".$this->id_no."';");
		
			$this->empname = $res['emp_name'];
			$this->pay_type = $res['pay_type'];
			$this->basic_rate = $res['basic_rate'];
			$this->bank_acct = $res['bank_acct'];
			$this->position = $res['designation'];
			$this->company = $res['company'];
			$this->branch = $res['branch'];
			$this->hmo = $res['hmo'];
			$this->wtax= $res['wtax'];
			
			
			
			if($this->daysworked > ($this->absent_hrs/8)){
				$this->clothing = ROUND($res['clothing'] / 2,2);	
				$this->laundry = ROUND($res['laundry'] / 2,2);
				$this->rsub = ROUND($res['rice_subsidy'] / 2,2);
				$this->insurance = ROUND($res['insurance'] / 2,2);
				$this->ontx = ROUND($res['other_non_tax'] / 2,2);
			}else{
				$this->clothing = ROUND($res['clothing'] / 2,2)- (($res['clothing']/13)*($this->absent_hrs/8));	
				$this->laundry = ROUND($res['laundry'] / 2,2)- (($res['laundry']/13)*($this->absent_hrs/8));	
				$this->rsub = ROUND($res['rice_subsidy'] / 2,2)- ((rice_subsidy)*($this->absent_hrs/8));	
				$this->insurance = ROUND($res['insurance'] / 2,2)- (($res['insurance']/13)*($this->absent_hrs/8));	
				$this->ontx = ROUND($res['other_non_tax'] / 2,2)- (($res['other_non_tax']/13)*($this->absent_hrs/8));	
			}
			
			
	
			
			if($this->pay_type=='SEMI'){
				$this->basic_pay = $this->basic_rate / 2;
				$this->daily_rate = $this->basic_pay/13;
			}else{
				$this->basic_pay = $this->basic_rate * $this->daysworked;
				$this->daily_rate = $this->basic_rate;
			}
		
			$this->hourly_rate = $this->daily_rate/8;
			$this->minutely_rate = $this->hourly_rate/60;
			$this->setHoursWorked();
			
			if($this->pay_type=='SEMI'){
				$this->payamount = $this->basic_pay - $this->ut_amt - $this->late_amt - $this->slvl - $this->absent_amt - $this->legal_pay - $this->special_holpay;
				list($ph_premium) = getArray("select ROUND(ee_share/2,2) from philhealth_table where '".$this->basic_rate."' between range1 and range2;");
				list($sss_premium) = getArray("select round(ee_share/2,2) from sss_table where '".$this->basic_rate."' between range1 and range2;");
				$this->sss_prem = $sss_premium;
				$this->phic_prem = $ph_premium;
			}else{
				$this->payamount = $this->payhours * $this->hourly_rate;	
				$dummy_pay = $this->daily_rate * 26;
				list($ph_premium) = getArray("select ROUND(ee_share/2,2) from philhealth_table where '".$dummy_pay."' between range1 and range2;");
				list($sss_premium) = getArray("select round(ee_share/2,2) from sss_table where '".$dummy_pay."' between range1 and range2;");
				$this->sss_prem = $sss_premium;
				$this->phic_prem = $ph_premium;
			}
			$this->hdmf_prem = 50;

			$this->gross = $this->payamount + $this->legal_pay + $this->special_holpay + $this->slvl + $this->legal_hrspay + $this->special_holhrspay + $this->ot_pay + $this->rsub + $this->laundry + $this->insurance + $this->ontx + $this->clothing;
		
			if($deduc_type['dedu_type']=='15'){	
				$this->ca = $this->loan('1','15');
				$this->short = $this->loan('2','15');
				$this->sssloan = $this->loan('3','15');
				$this->pagibigloan = $this->loan('4','15');
				$this->bread = $this->loan('5','15');
				$this->laptop = $this->loan('6','15');
				$this->productvale = $this->loan('7','15');
				$this->boarding = $this->loan('8','15');
				$this->load = $this->loan('9','15');
				$this->uniform = $this->loan('10','15');
				$this->nameplate = $this->loan('11','15');
				$this->oth = $this->loan('12','15');
				$this->chrg = $this->loan('13','15');
				$this->nega = $this->loan('14','15');
				$this->hmo = $this->loan('15','15');
			}else{
				$this->ca = $this->loan('1','30');
				$this->short = $this->loan('2','30');
				$this->sssloan = $this->loan('3','30');
				$this->pagibigloan = $this->loan('4','30');
				$this->bread = $this->loan('5','30');
				$this->laptop = $this->loan('6','30');
				$this->productvale = $this->loan('7','30');
				$this->boarding = $this->loan('8','30');
				$this->load = $this->loan('9','30');
				$this->uniform = $this->loan('10','30');
				$this->nameplate = $this->loan('11','30');
				$this->oth = $this->loan('12','30');
				$this->chrg = $this->loan('13','30');
				$this->nega = $this->loan('14','30');
				$this->hmo = $this->loan('15','30');
			}

			$this->totalded = $this->ca + $this->short + $this->sssloan + $this->pagibigloan + $this->bread + $this->laptop + $this->productvale + $this->boarding + $this->load +
							  $this->uniform + $this->nameplate + $this->oth + $this->chrg + $this->nega + $this->hmo + $this->wtax +  $this->sss_prem + $this->phic_prem + $this->hdmf_prem + $this->adjustment;
			$this->takehome = $this->gross - $this->totalded;

		}

		public function out($qid){
			list($varname) = getArray("SELECT php_var FROM hris.payroll_header WHERE line_id = '$qid';");
			return $this->$varname;
		}

		public function setHoursWorked(){
			list($hw) = getArray("SELECT IFNULL(SUM(hrs),0) FROM hris.pay_periods a INNER JOIN hris.e_dtr b ON b.date BETWEEN a.period_start AND a.period_end WHERE DAYNAME(b.date) != 'Sunday' AND a.period_id = '".$this->period."' AND b.emp_id = '".$this->id_no."' and b.date not in (SELECT `date` FROM hris.e_holidays a INNER JOIN hris.holiday_aoe aa ON a.record_id = aa.hol_fileid AND a.trace_no = aa.trace_no INNER JOIN hris.pay_periods b ON a.date BETWEEN b.period_start AND b.period_end WHERE aa.company = '".$this->company."' AND aa.branch='".$this->branch."' AND b.period_id = '".$this->period."');");
			//dbquery("insert into hris.query_log (qry) values ('".mysql_real_escape_string("SELECT IFNULL(SUM(hrs),0) FROM hris.pay_periods a INNER JOIN hris.e_dtr b ON b.date BETWEEN a.period_start AND a.period_end WHERE DAYNAME(b.date) != 'Sunday' AND a.period_id = '".$this->period."' AND b.emp_id = '".$this->id_no."'' and b.date not in (SELECT `date` FROM hris.e_holidays a INNER JOIN hris.holiday_aoe aa ON a.record_id = aa.hol_fileid AND a.trace_no = aa.trace_no INNER JOIN hris.pay_periods b ON a.date BETWEEN b.period_start AND b.period_end WHERE aa.company = '".$this->company."' AND aa.branch='".$this->branch."' AND b.period_id = '".$this->period."');")."');");
			$this->payhours = $hw;
			$this->daysworked = round($this->payhours/8,2);

			list($ut) = getArray("SELECT IFNULL(SUM(ut),0) FROM hris.pay_periods a INNER JOIN hris.e_dtr b ON b.date BETWEEN a.period_start AND a.period_end WHERE a.period_id = '".$this->period."' AND b.emp_id = '".$this->id_no."';");
			$this->ut_mins = $ut;
			$this->ut_amt =  $this->ut_mins * $this->minutely_rate;

			list($late) = getArray("SELECT IFNULL(SUM(late),0) FROM hris.pay_periods a INNER JOIN hris.e_dtr b ON b.date BETWEEN a.period_start AND a.period_end WHERE a.period_id = '".$this->period."' AND b.emp_id = '".$this->id_no."';");
			$this->late_mins = $late;
			$this->late_amt = $this->late_mins * $this->minutely_rate;

			list($ot) = getArray("SELECT IFNULL(SUM(ot),0) FROM hris.pay_periods a INNER JOIN hris.e_dtr b ON b.date BETWEEN a.period_start AND a.period_end WHERE a.period_id = '".$this->period."' AND b.emp_id = '".$this->id_no."' AND b.ot_approved = 'Y';");
			$this->ot_hrs = $ot;
			$this->ot_pay = $ot * ($this->minutely_rate * 1.25);

			list($sil) = getArray("SELECT SUM(`length`) FROM hris.e_leaves a INNER JOIN hris.pay_periods b ON a.dtf BETWEEN b.period_start AND b.period_end WHERE a.id_no = '".$this->id_no."' AND b.period_id = '".$this->period."' AND with_pay = 'Y' ;"); 
			$this->slvl = $this->hourly_rate * $sil;
			
			list($ol) = getArray("SELECT SUM(`length`) FROM hris.e_leaves a INNER JOIN hris.pay_periods b ON a.dtf BETWEEN b.period_start AND b.period_end WHERE a.id_no = '".$this->id_no."' AND b.period_id = '".$this->period."' AND with_pay = 'N' ;"); 
			$this->absent_hrs = $ol;
			$this->absent_amt = $this->absent_hrs * $this->hourly_rate;

			list($sun_hw) = getArray("SELECT IFNULL(SUM(hrs),0) FROM hris.pay_periods a INNER JOIN hris.e_dtr b ON b.date BETWEEN a.period_start AND a.period_end WHERE DAYNAME(b.date) = 'Sunday' AND a.period_id = '".$this->period."' AND b.emp_id = '".$this->id_no."';");
			$this->sunday = $sun_hw;
			$this->sunda_pay = $this->sunday * ( $this->hourly_rate * 1.3);

			list($hol_count) = getArray("SELECT COUNT(`date`) FROM hris.e_holidays a INNER JOIN hris.holiday_aoe aa ON a.record_id = aa.hol_fileid AND a.trace_no = aa.trace_no INNER JOIN hris.pay_periods b ON a.date BETWEEN b.period_start AND b.period_end WHERE aa.company = '".$this->company."' AND aa.branch='".$this->branch."' AND b.period_id = '".$this->period."' AND a.type = 'REG';");
			$this->legal_hol = $hol_count;
			$this->legal_pay = $this->legal_hol * $this->daily_rate;

			list($hol_hours) = getArray("SELECT IFNULL(SUM(hrs),0) FROM hris.pay_periods a INNER JOIN hris.e_dtr b ON b.date BETWEEN a.period_start AND a.period_end WHERE DAYNAME(b.date) != 'Sunday' AND a.period_id = '".$this->period."' AND b.emp_id = '".$this->id_no."' and b.date in (SELECT `date` FROM hris.e_holidays a INNER JOIN hris.holiday_aoe aa ON a.record_id = aa.hol_fileid AND a.trace_no = aa.trace_no INNER JOIN hris.pay_periods b ON a.date BETWEEN b.period_start AND b.period_end WHERE aa.company = '".$this->company."' AND aa.branch='".$this->branch."' AND b.period_id = '".$this->period."' and a.type='REG');");
			$this->legal_hrs = $hol_hours;
			$this->legal_hrspay = $this->legal_hrs * $this->hourly_rate;

			list($sp_count) = getArray("SELECT COUNT(`date`) FROM hris.e_holidays a INNER JOIN hris.holiday_aoe aa ON a.record_id = aa.hol_fileid AND a.trace_no = aa.trace_no INNER JOIN hris.pay_periods b ON a.date BETWEEN b.period_start AND b.period_end WHERE aa.company = '".$this->company."' AND aa.branch='".$this->branch."' AND b.period_id = '".$this->period."' AND a.type = 'SP';");
			$this->special_hol = $sp_count;
			$this->special_holpay = $this->special_hol * $this->daily_rate;

			list($sp_holhrs) = getArray("SELECT IFNULL(SUM(hrs),0) FROM hris.pay_periods a INNER JOIN hris.e_dtr b ON b.date BETWEEN a.period_start AND a.period_end WHERE DAYNAME(b.date) != 'Sunday' AND a.period_id = '".$this->period."' AND b.emp_id = '".$this->id_no."' and b.date in (SELECT `date` FROM hris.e_holidays a INNER JOIN hris.holiday_aoe aa ON a.record_id = aa.hol_fileid AND a.trace_no = aa.trace_no INNER JOIN hris.pay_periods b ON a.date BETWEEN b.period_start AND b.period_end WHERE aa.company = '".$this->company."' AND aa.branch='".$this->branch."' AND b.period_id = '".$this->period."' and a.type='SP');");
			$this->special_holhrs = $sp_holhrs;
			$this->special_holhrspay = $this->special_holhrs * ($this->hourly_rate * 0.3);
			
			list($adj) = getArray("select sum(amount) FROM hris.e_adjustments a WHERE a.period_id = '".$this->period."' AND id_no = '".$this->id_no."';");
			$this->adjustment = $adj;
			//
		}

		public function loan($loantype,$dedutype){
            list($amt) = getArray("SELECT  sum(a.multiply * IF(a.dedu_amount>a.balance,a.balance,a.dedu_amount)) AS dedu FROM hris.e_loans a WHERE a.balance > 0 AND a.id_no = '".$this->id_no."' AND loan_type = '$loantype' AND include = 'Y' AND a.deduct_day IN ('','$dedutype') AND a.date_availed <= (SELECT `period_end` FROM hris.pay_periods b WHERE b.period_id = '".$this->period."');");
        return $amt * 1;
        }
	
		function postLoan($id_no,$period,$dedu_day){
			$loan_type = dbquery("SELECT `type` FROM hris.e_loantype;");
			while($indx_loan = mysql_fetch_array($loan_type)){
					//dbquery("INSERT INTO hris.query_log (qry) VALUES ('".mysql_real_escape_string("SELECT distinct file_id,loan_type,(a.multiply * IF(a.dedu_amount>a.balance,a.balance,a.dedu_amount)) AS dedu FROM hris.e_loans a INNER JOIN hris.pay_periods b ON a.date_availed <= b.period_end WHERE a.balance > 0 AND a.id_no = '$id_no' AND loan_type = '$indx_loan[type]' AND include = 'Y' and a.multiply > 0 AND a.deduct_day IN ('','$dedu_day') AND a.date_availed <= (SELECT `period_end` FROM hris.pay_periods b WHERE b.period_id = '$period');")."');");
					//$loan = dbquery("SELECT distinct file_id,loan_type,(a.multiply * IF(a.dedu_amount>a.balance,a.balance,a.dedu_amount)) AS dedu FROM hris.e_loans a INNER JOIN hris.pay_periods b ON a.date_availed <= b.period_end WHERE a.balance > 0 AND a.id_no = '$id_no' AND loan_type = '$indx_loan[type]' AND include = 'Y' and a.multiply > 0 AND a.deduct_day IN ('','$dedu_day') AND a.date_availed <= (SELECT `period_end` FROM hris.pay_periods b WHERE b.period_id = '$period');");
					$loan = dbquery("SELECT distinct file_id,loan_type,(a.multiply * IF(a.dedu_amount>a.balance,a.balance,a.dedu_amount)) AS dedu FROM hris.e_loans a INNER JOIN hris.pay_periods b ON a.date_availed <= b.period_end WHERE a.balance > 0 AND a.id_no = '$id_no' AND loan_type = '$indx_loan[type]' AND include = 'Y' and a.multiply > 0 AND a.deduct_day IN ('','$dedu_day') AND a.date_availed <= (SELECT `period_end` FROM hris.pay_periods b WHERE b.period_id = '$period');");
				while($row = mysql_fetch_array($loan)){
					dbquery("INSERT INTO hris.query_log (qry) VALUES ('".mysql_real_escape_string("INSERT INTO hris.e_loanposted (pay_period,loan_id,loan_type,emp_id,amount) VALUES ('$period','$row[file_id]','$row[loan_type]','$id_no','$row[dedu]');")."');");
					
					dbquery("INSERT INTO hris.e_loanposted (pay_period,loan_id,loan_type,emp_id,amount) VALUES ('$period','$row[file_id]','$row[loan_type]','$id_no','$row[dedu]');");
					list($amt) = getArray("SELECT SUM(amount) FROM hris.e_loanposted a WHERE a.loan_id = '$row[file_id]' AND a.emp_id = '$id_no' AND a.loan_type = '$row[loan_type]';");
					dbquery("UPDATE hris.e_loans a SET a.balance = a.amount - '$amt', a.applied_amount =  '$amt' WHERE a.file_id = '$row[file_id]' AND a.id_no = '$id_no';");
					dbquery("UPDATE hris.e_loans a SET a.posted = 'Y' WHERE a.file_id = '$row[file_id]' AND a.id_no = '$id_no';");
				}
			}
			
		}

	}
?>