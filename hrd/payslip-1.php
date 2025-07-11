<?php
	//ini_set("display_errors","On");
	require_once("../lib/mpdf6/mpdf.php");
	require_once '../handlers/initDB.php';
	
	require_once "../handlers/_payroll.php";
	$pay = new payroll($_REQUEST['cutoff']);
	
	ini_set("max_execution_time",-1);
	ini_set("memory_limit",-1);
	
	$cutoff = $_REQUEST['cutoff'];
	$dept = $_REQUEST['dept'];
	$eid = $_REQUEST['eid'];
	
	if($dept != '') { $f1 = " and a.dept = '$dept' "; } else { $f1 = ""; }
	if($eid != '') { $f2 = " and a.emp_id = '$eid' "; } else { $f2 = ""; }
	$q = $pay->dbquery("select * from omdcpayroll.emp_payslip a where period_id = '$cutoff' $f1 $f2 order by emp_name asc;");
	//list($dtf,$dt2,$paydate) = $pay->getArray("select date_format(period_start,'%m/%d/%Y'), date_format(period_end,'%m/%d/%Y'), date_format(date_add(period_end,INTERVAL 5 DAY),'%m/%d/%Y') from omdcpayroll.pay_periods where period_id = '$cutoff';");
	
	$mpdf=new mPDF('win-1252','FOLIO','','',10,10,12,5,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false s default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Solutions");
	$mpdf->useSubstitutions = false; 
	$mpdf->SetDisplayMode(60);

$html = '
<html>
<head>
<style>
body {font-family: sans-serif; font-size: 6.5pt; }
td { vertical-align: top; font-size: 6.5pt; }
.e_info { border-top: 0.05mm solid #000000; }
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">

</htmlpageheader>

<sethtmlpageheader name="myheader" value="off" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="off" />
mpdf-->';

while($res = $q->fetch_array(MYSQLI_BOTH)) {
	list($desg,$dhired,$dept,$sl_credit,$vl_credit,$brate,$ptype,$basicrate) = $pay->getArray("select ucase(desg) as desg,date_format(date_hired,'%m/%d/%Y'),b.dept_abbrv,vl_credit,a.basic_rate,a.payroll_type,a.basic_rate from omdcpayroll.emp_masterfile a left join omdcpayroll.options_dept b on a.dept=b.id where a.emp_id='$res[emp_id]' and a.file_status != 'DELETED';");
	$pay->getRates($ptype,$basicrate);
	

	if($res['net_pay'] > 0) {

		$html = $html . '<table width="100%">
		<tr>
			<td width="50%" height=32>&nbsp;</td>
			<td width="50%" align=right>
				<span style="font-weight: bold; font-size: 12pt; color: #000000;">PAY SLIP
			</td>
		</tr>
	</table>
	<table width="100%" cellspacing=0 cellpadding=0 class=e_info>
		<tr><td colspan=4 class=e_info>&nbsp;</td>
		<tr>
			<td width=15%>ID NUMBER</td>
			<td width=35% style="padding-left: 5px;">: '.$res['emp_id']. '</td>
			<td width=50% colspan=2></td>
		</tr>
		<tr>
			<td width=15%>NAME</td>
			<td width=35% style="padding-left: 5px;">: '. strtoupper(iconv("UTF-8", "ISO-8859-1//IGNORE", $res['emp_name'])). '</td>
			<td width=20%>DEPARMENT</td>
			<td width=30% style="padding-left: 5px;">: '.$dept.'</td>
			
		</tr>
		<tr>
			<td width=15%>DESIGNATION</td>
			<td width=35% style="padding-left: 5px;">: '.$desg.'</td>
			<td width=20%>PAYROLL CUT OFF DATE</td>
			<td width=30% style="padding-left: 5px;">: '. $pay->dtf . ' to ' . $pay->dt2 .'</td>
		</tr>
		<tr>
			<td width=15%>DATE HIRED</td>
			<td width=35% style="padding-left: 5px;">: '.$dhired.'</td>';
			
			if($pay_type == 2) {
				$html .= '<td width=20%>CREDITED HOURS</td>
						  <td width=30% style="padding-left: 5px;">: '. $base .'</td>';
			} else {
				$html .= '<td width=50% colspan=2></td>';
			}
			
		$html .= '</tr>';
		
	$html .= '</tr>
		</table>
		<table width=100% cellpadding=0 cellspacing=0 style="font-size: 10px; margin-top: 5px;">
			<tr>
				<td width=50% style="border: 0.1em solid black; border-collapse: collapse;">
					<table width=100% cellpadding=0 cellspacing=0>
						<tr>
							<td width="70%" style="border-bottom: 0.1mm solid black; padding-left: 10px;"><b>EARNINGS</b></td>
							<td width="30%" style="border-bottom: 0.1mm solid black; text-align: right; padding-right: 5px;"><b>AMOUNT</b></td>
						</tr>	
						<tr>
							<td width="100%" colspan=2 style="padding-left: 10px;">Basic Income</td>
						</tr>
						
						<tr>
							<td width="70%" style="padding-left: 40px;">Basic Pay</td>
							<td width="30%" style="padding-right: 5px; text-align: right;"><b>'.number_format(($res['basic_pay'] + $res['absences'] + $res['late'] + $res['undertime']),2) .'</b></td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 50px; font-style: italic; font-size: 8px;">*Less: Absences</td>
							<td width="30%" style="padding-right: 5px; text-align: right; font-size: 8px;">('.number_format($res['absences'],2).')</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 50px; font-style: italic; font-size: 8px;">*Less: Late</td>
							<td width="30%" style="padding-right: 5px; text-align: right; font-size: 8px;">('.number_format($res['late'],2).')</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 50px; font-style: italic; font-size: 8px;">*Less: Undertime</td>
							<td width="30%" style="padding-right: 5px; text-align: right; font-size: 8px;">('.number_format($res['undertime'],2).')</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 40px;">('. ROUND($res['holiday_on_restday'] / $res['daily_rate']) .') Holiday on Rest Day</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['holiday_on_restday'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 40px;">('.ROUND($res['legal_holiday'] / $res['daily_rate']) .') Legal Holiday</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['legal_holiday'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 40px;">('. ROUND($res['special_holiday'] / $res['daily_rate']) .') Special Holiday</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['special_holiday'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 40px;">('.  ROUND($res['vacation_leave'] / $res['daily_rate']) .') Service Incentive Leaves</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['vacation_leave'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 40px;">('. ROUND($res['other_leaves'] / $res['daily_rate']) .') Other Paid Leaves (Maternity,Emergency,etc.)</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['other_leaves'],2).'</td>
						</tr>
						
						<tr>
							<td width="70%" style="padding-left: 10px;">COLA</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['cola'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">Overtime</td>
							<td width="30%" style="padding-right: 5px; text-align: right;"></td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 40px;">Regular Overtime</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format(($res['ot_regular']+$res['ot_regular_ex']),2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 40px;">Legal Holiday Overtime</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format(($res['ot_legalholiday']+$res['ot_legalholidayex']),2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 40px;">Special Holiday Overtime</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format(($res['ot_specialholiday']+$res['ot_specialholidayex']),2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 40px;">Rest Day Overtime</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format(($res['ot_sunday']+$res['ot_sundayex']),2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 40px;">Night Differentials</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['night_premium'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">Performance Bonuses/Incentives</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['incentives'],2).'</td>
						</tr>

						<tr>
							<td width="70%" style="padding-left: 10px;">Taxable Allowances</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['allowance'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">Non-taxable Allowances</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['nontax_allowance'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">Adjustments</td>
							<td width="30%" style="padding-right: 5px; text-align: right;"></td>
						</tr>';
						
						$adjQuery = $pay->dbquery("select remarks, amount from omdcpayroll.emp_adjustments where emp_id = '$res[emp_id]' and period_id = '$cutoff';");
						while($adjRow = $adjQuery->fetch_array()) {
							$html .= '<tr>
								<td width="70%" style="padding-left: 40px;">'.strtoupper($adjRow[0]).'</td>
								<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($adjRow[1],2).'</td>
							</tr>';
						}
			
						
						
						$html .= '<tr>
							<td width="70%" style="text-align: left; padding-left: 20px;"><br/><b>G R O S S &nbsp;&nbsp; P A Y &raquo;</b></td>
							<td width="30%" style="padding: 5px; text-align: right;"><b>'.number_format($res['gross_pay'],2).'<br/>========</b></td>
						</tr>	
					</table>
				</td>
				<td width=50% style="border: 0.1em solid black; border-collapse: collapse;">
					<table width=100% cellpadding=0 cellspacing=0>
						<tr>
							<td width="70%" style="border-bottom: 0.1mm solid black; padding-left: 10px;"><b>DEDUCTIONS</b></td>
							<td width="35%" style="border-bottom: 0.1mm solid black; text-align: right; padding-right: 5px;"><b>AMOUNT</b></td>
						</tr>
						<tr>
							<td width="100%" colspan=2 style="padding-left: 10px;">Premium Contributions</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 40px;">Philhealth</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['philhealth_premium'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 40px;">SSS Premium</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['sss_premium'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 40px;">Pagibig/HDMF Premium</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['pagibig_premium'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">Coop Premium</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['coop_premium'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">Retirement Plan</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['retirement_plan'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">Withholding Tax</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['wtax'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">SSS Loan</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['sss_loan'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">Pagibig (HDMF) Loan</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['hdmf_loan'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">JAG Loan</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['jag_loan'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">Company Loan/Cash Advance</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['cash_adv'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">Laboratory</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['laboratory'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">Coop Loan</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['coop_loan'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">Other Loans Total</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['other_loans'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="padding-left: 10px;">Other Deductions Total</td>
							<td width="30%" style="padding-right: 5px; text-align: right;">'.number_format($res['others_total'],2).'</td>
						</tr>
						<tr>
							<td width="70%" style="text-align: left; padding-left:10px;"><br/><b>D E D U C T I O N S &nbsp;&nbsp; T O T A L &raquo;</b></td>
							<td width="30%" style="padding: 5px; text-align: right;"><b>'.number_format(($res['sss_premium']+$res['pagibig_premium']+$res['philhealth_premium']+$res['coop_premium']+$res['retirement_plan']+$res['wtax']+$res['loans_total']+$res['others_total']),2).'<br/>========</b></td>
						</tr>
						<tr>
							<td width="100%" style="border-top: 0.1mm solid black; padding: 5px;" colspan=2><b>NET PAY</b></td>
						</tr>
						<tr><td width=100% style="padding-left: 5px; padding-top: 10px; font-size: 16pt;" align=center colspan=2><b>&#8369; '.number_format($res['net_pay'],2).'</b></td></tr>
					</table>
				</td>
			</tr>
		</table>
		<table><tr><td height=20 valign=middle>&#9986;----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</td></tr></table>';
	}

}
$html = $html . '</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>