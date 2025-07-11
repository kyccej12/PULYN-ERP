<?php
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");
	session_start();
	ini_set("memory_limit","1024M");
	ini_set("max_execution_time",0);
	ini_set("display_errors","Off");
	
	$cutoff = $_REQUEST['cutoff'];
	$byear = date('Y');
	if($_REQUEST['ee'] != "") { $x1 = " and a.emp_id='$_REQUEST[ee]' "; }
	
	$dtf = formatDate($_GET['dtf']);
	$dt2 = formatDate($_GET['dt2']);

	if($_GET['dept'] != "") { $fs = " and department = '$_GET[dept]' "; }
	$q = mysql_query("SELECT id_no, CONCAT(lname,', ',fname) AS emp, tax_bracket, designation, department, if(pay_type='SEMI',monthly_rate,daily_rate) as basic_rate, daily_rate, monthly_rate, pay_type, IF(pay_type='SEMI','Monthly','Daily') AS ptype, rice_subsidy, clothing, laundry, insurance, other_non_tax FROM e_master WHERE company = '$_SESSION[company]' and `status` NOT IN ('Terminated','Resigned') AND id_no != '' $fs ORDER BY lname ASC;");
	list($payDate) = mysql_fetch_array(mysql_query("select date_format(date_add('$dt2', INTERVAL 5 DAY),'%m/%d/%Y');"));
	
$mpdf=new mPDF('win-1252','LETTER','','',15,15,7,7,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(40);

$html = '
<html>
<head>
<style>
body {font-family: sans-serif; font-size: 8pt; }
td { vertical-align: top; }
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

while($res = mysql_fetch_array($q)) {
	$barcode = $res['eid'];

	/* Total Days Woked */
	list($twork) = getArray("select ROUND(sum(hrs)/8,2) from e_dtr where emp_id = '$res[id_no]' and `date` between '$dtf' and '$dt2' group by emp_id;");
	
	/* Leaves */
	list($svl) = getArray("select sum(`length`) from e_leaves where id_no = '$res[id_no]' and with_pay = 'Y' and (dtf between '$dtf' and '$dt2' or dt2 between '$dtf' and '$dt2');");
	list($svlwp) = getArray("select sum(`length`) from e_leaves where id_no = '$res[id_no]' and with_pay = 'N' and (dtf between '$dtf' and '$dt2' or dt2 between '$dtf' and '$dt2');");
	
	/* Overtime */
	list($regOT) = getArray("select sum(ot) from e_dtr where emp_id = '$res[id_no]' and `date` between '$dtf' and '$dt2' and `date` not in (select `date` from e_holidays where `date` between '$dtf' and '$dt2') AND DAYNAME(`date`) != 'Sunday' and ot_approved = 'Y' group by emp_id;");
	list($reghOT) = getArray("select sum(ot) from e_dtr where emp_id = '$res[id_no]' and `date` in (select `date` from e_holidays where `date` between '$dtf' and '$dt2' and `type`='REG') and ot_approved = 'Y' group by emp_id;");
	
	/* Holidays */
	list($hol) = getArray("select count(*) from e_holidays where `date` between '$dtf' and '$dt2' AND DAYNAME(`date`) != 'Sunday';");
	
	/* Sunday Overtime */
	list($sundayOT) = getArray("select sum(ot) from e_dtr where emp_id = '$res[id_no]' and `date` between '$dtf' and '$dt2' and `date` not in (select `date` from e_holidays where `date` between '$dtf' and '$dt2') AND DAYNAME(`date`) = 'Sunday' and ot_approved = 'Y' group by emp_id;");
	
	if($res['status']=='Regular'){
		list($sphOT) = getArray("select sum(ot) from e_dtr where emp_id = '$res[id_no]' and `date` in (select `date` from e_holidays where `date` between '$dtf' and '$dt2' and `type`='SP') and ot_approved = 'Y' group by emp_id;");
	} else {
		$sphOT = 0;
	}
	
	if($sundayOT > 0){	$sphOT += $sundayOT; }
	
	if($res['pay_type'] == "SEMI") { 
		
		list($ut) = getArray("select ifnull(sum(8-(hrs+late)),0) from e_dtr where emp_id = '$res[id_no]' and `date` between '$dtf' and '$dt2'");
		list($late) = getArray("select ifnull(sum(late),0) from e_dtr where emp_id = '$res[id_no]' and `date` between '$dtf' and '$dt2'");
		list($deduc_ut) = getArray("SELECT IF(`length`>DATEDIFF('$dt2',dtf)+1,`length`-DATEDIFF('$dt2',dtf)+1,`length`) * 8 AS deduc_ut FROM sjerp.e_leaves a WHERE id_no ='$res[id_no]' and `type` != 'OL';");
		$ut =$ut - $deduc_ut;
		$br = ROUND($res['basic_rate'] / 26,2);
		$hr = ROUND($br/8,2);
		
		if($ut > 0) { $myut = ROUND($hr * $ut,2); }
		if($late > 0) { $mylate = ROUND($hr * $late,2); }
		if($svlwp > 0) { $wopay = ROUND($br * $svlwp,2); }
		
		list($ph_premium) = getArray("select ROUND(ee_share/2,2) from philhealth_table where '$res[basic_rate]' between range1 and range2;");
		list($sss_premium) = getArray("select round(ee_share/2,2) from sss_table where '$res[basic_rate]' between range1 and range2;");
		$holPay = ROUND($hol * $br,2);

		$basic =  ROUND($res['basic_rate']/2,2) - $mylate - $myut - $wopay - $holPay;
		
	} else { 
		$br = $res['daily_rate']; 
		$ph_premium = 56.25;
		$sss_premium = 163.50;
		$basic = ROUND($twork * $br,2);
		
		$holPay = ROUND($hol * $br,2);
		$hr = round($br/8,2);
	}

	$svlPay = ROUND($br * $svl,2);
	$pi_premium = 50;
	
	/* Non Taxable Allowances */
	$clothing = ROUND($res['clothing'] / 2,2);
	$laundry = ROUND($res['laundry'] / 2,2);
	$rsub = ROUND($res['rice_subsidy'] / 2,2);
	$insurance = ROUND($res['insurance'] / 2,2);
	$ontx = ROUND($res['other_non_tax'] / 2,2);
	
	
	
	/* Overtime Computation */
		$regOTPay = ROUND(($regOT * $hr),2);
		$spOTPay = ROUND(($sphOT * $hr),2);
		$reghOTPay = ROUND(($reghOT * $hr),2);
		$ot = $regOTPay + $spOTPay + $reghOTPay;
	
	/* Gross Salary */
	$gross = $basic + $ot + $laundry + $clothing + $rsub + $ontx + $insurance + $holPay + $svlPay;

	/* DEDUCTIONS */
		
		list($sss_loan) = getArray("select dedu_amount from e_loans where id_no = '$res[id_no]' and '$dt2' between date_availed and date_add(date_availed,interval terms/2 MONTH) and loan_type ='3';");
		list($hdmf_loan) = getArray("select dedu_amount from e_loans where id_no = '$res[id_no]' and '$dt2' between date_availed and date_add(date_availed,interval terms/2 MONTH) and loan_type = '4';");
		list($others) = getArray("select sum(dedu_amount) as dedu_amount from e_loans where id_no = '$res[id_no]' and '$dt2' between date_availed and date_add(date_availed,interval terms/2 MONTH) and loan_type not in ('3','4');");
	
	/* With-holding Tax Computation */
	$nbt = $gross - $ph_premium - $sss_premium - $pi_premium - $rsub - $ontx - $clothing - $laundry - $insurance;
	if($res['tax_bracket'] != '') {
		list($wtax) = getArray("select tax_exempt1+ROUND((($nbt-sl_range1) * OOP_status1) / 100,2) as wtax from e_taxtable where '$nbt' between sl_range1 and sl_range2 and tax_id = '$res[tax_bracket]';");
	} else {
		$wtax = 0;
	}

	/* Total Deductions for teh Period */
	$tDeductions = $sss_loan + $hdmf_loan + $others + $sss_premium + $pi_premium + $ph_premium + $wtax;

	//if($basic > 0) {
		$html = $html . '<table width="100%">
			<tr>
				<td width="50%"><barcode code="'.substr($barcode,0,10).'" type="C128A"></td>
				<td width="50%" align=right>
					<span style="font-weight: bold; font-size: 16pt; color: #000000;">PAY SLIP
				</td>
			</tr>
		</table>
		<table width="100%" cellspacing=0 cellpadding=0 class=e_info>
			<tr><td colspan=6 class=e_info>&nbsp;</td>
			<tr >
				<td width=15%>ID NUMBER</td>
				<td>:</td>
				<td width=35% style="padding-left: 5px;">'.$res['id_no']. '</td>
				<td width=20%>PAYROLL CUT-OFF DATE</td>
				<td>:</td>
				<td width=30% style="padding-left: 5px;">'. $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</td>
			</tr>
			<tr>
				<td width=15%>NAME</td><td>:</td>
				<td width=35% style="padding-left: 5px;">'. strtoupper(iconv("UTF-8", "ISO-8859-1//IGNORE", $res['emp'])). '</td>
				<td width=20%>DAYS WORKED</td>
				<td>:</td>
				<td width=30% style="padding-left: 5px;">'.$twork.'</td>
			</tr>
			<tr>
				<td width=15%>DESIGNATION</td><td>:</td>
				<td width=35% style="padding-left: 5px;">'.$res['designation'].'</td>
				<td width=20%>PAY DATE</td>
				<td>:</td>
				<td width=30% style="padding-left: 5px;">'.$payDate.'</td>
			</tr>
			<tr>
				<td width=15%>DEPARTMENT</td><td>:</td>
				<td width=35% style="padding-left: 5px;">'.$res['department'].'</td>
				<td width=20%>BASIC RATE</td>
				<td>:</td>
				<td width=30% style="padding-left: 5px;">'.number_format($res['basic_rate'],2).' '.$res['ptype'] . '</td>
			</tr>
		</table>
		<table style="font-size: 10px;" cellpadding="2" cellspacing=0 width=100%>
			<tr>
				<td width="25%" style="border-top: 0.1mm solid black; border-bottom: 0.1mm solid black; border-left: 0.1mm solid black; padding-left: 10px;"><b>EARNINGS & ADJUSTMENTS</b></td>
				<td width="15%" style="border-top: 0.1mm solid black; border-bottom: 0.1mm solid black;"><b></b></td>
				<td width="15%" style="border-top: 0.1mm solid black; border-bottom: 0.1mm solid black; text-align: right; padding-right: 5px;"><b>AMOUNT</b></td>
				<td width="30%" style="border-top: 0.1mm solid black; border-bottom: 0.1mm solid black; border-left: 0.1mm solid black; padding-left: 10px;"><b>DEDUCTIONS</b></td>
				<td width="15%" style="border-top: 0.1mm solid black; border-bottom: 0.1mm solid black; border-right: 0.1mm solid black; text-align: right; padding-right: 5px;"><b>AMOUNT</b></td>	
			</tr>
			<tr>
				<td width="25%" style="border-left: 0.1mm solid black; padding-left: 10px;">BASIC SALARY</td>
				<td width="15%"></td>
				<td width="15%" style="padding-right: 5px; text-align: right;">'.number_format($basic,2).'</td>
				<td width="30%" style="padding-left: 10px; border-left: 0.1mm solid black;">SSS PREMIUM</td>
				<td width="15%" style="padding-right: 5px; text-align: right; border-right: 0.1mm solid black;">'.number_format($sss_premium,2).'</td>	
			</tr>
			<tr>
				<td width="25%" style="border-left: 0.1mm solid black; padding-left: 10px;">OVERTIME (REGULAR,HOLIDAY)</td>
				<td width="15%"></td>
				<td width="15%" style="padding-right: 5px; text-align: right;">'.number_format($ot,2).'</td>
				<td width="30%" style="padding-left: 10px; border-left: 0.1mm solid black;">PHILHEALTH</td>
				<td width="15%"  style="padding-right: 5px; text-align: right; border-right: 0.1mm solid black;">'.number_format($ph_premium,2).'</td>	
			</tr>
			<tr>
				<td width="25%" style="border-left: 0.1mm solid black; padding-left: 10px;">Holiday Pay</td>
				<td width="15%"></td>
				<td width="15%" style="padding-right: 5px; text-align: right;">'.number_format($holPay,2).'</td>
				<td width="30%" style="padding-left: 10px; border-left: 0.1mm solid black;">PAGIBIG PREMIUM</td>
				<td width="15%"  style="padding-right: 5px; text-align: right; border-right: 0.1mm solid black;">'.number_format($pi_premium,2).'</td>	
			</tr>
			<tr>
				<td width="25%" style="border-left: 0.1mm solid black; padding-left: 10px;">SL/VL Pay</td>
				<td width="15%"></td>
				<td width="15%" style="padding-right: 5px; text-align: right;">'.number_format($svlPay,2).'</td>
				<td width="30%" style="padding-left: 10px; border-left: 0.1mm solid black;">SSS LOAN</td>
				<td width="15%"  style="padding-right: 5px; text-align: right; border-right: 0.1mm solid black;">'.number_format($sss_loan,2).'</td>	
			</tr>
			<tr>
				<td width="25%" style="border-left: 0.1mm solid black; padding-left: 10px;">Clothing</td>
				<td width="15%"></td>
				<td width="15%" style="padding-right: 5px; text-align: right;">'.number_format($clothing,2).'</td>
				<td width="30%" style="padding-left: 10px; border-left: 0.1mm solid black;">PAGIBIG LOAN</td>
				<td width="15%" style="padding-right: 5px; text-align: right; border-right: 0.1mm solid black;">'.number_format($hdmf_loan,2).'</td>	
			</tr>
			<tr>
				<td width="25%" style="border-left: 0.1mm solid black; padding-left: 10px;">Rice Subsidy</td>
				<td width="15%"></td>
				<td width="15%" style="padding-right: 5px; text-align: right;">'.number_format($rsub,2).'</td>
				<td width="30%" style="padding-left: 10px; border-left: 0.1mm solid black;">Other Deductions</td>
				<td width="15%"  style="padding-right: 5px; text-align: right; border-right: 0.1mm solid black;">'.number_format($others,2).'</td>	
			</tr>
			<tr>
				<td width="25%" style="border-left: 0.1mm solid black; padding-left: 10px;">Late ('.($late * 60).')</td>
				<td width="15%"></td>
				<td width="15%" style="padding-right: 5px; text-align: right;">('.number_format($mylate,2).')</td>
				<td width="30%" style="padding-left: 10px; border-left: 0.1mm solid black;"></td>
				<td width="15%" style="padding-right: 5px; text-align: right; border-right: 0.1mm solid black;"></td>	
			</tr>';
			if($ut>0){
				$html.='
					<tr>
						<td width="25%" style="border-left: 0.1mm solid black; padding-left: 10px;">Undertime ('.($ut*60).')</td>
						<td width="15%"></td>
						<td width="15%" style="padding-right: 5px; text-align: right;">('.number_format($myut,2).')</td>
						<td width="30%" style="padding-left: 10px; border-left: 0.1mm solid black;"></td>
						<td width="15%" style="padding-right: 5px; text-align: right; border-right: 0.1mm solid black;"></td>	
					</tr>';
			}

			$html.='
			<tr>
				<td width="25%" style="border-left: 0.1mm solid black; padding-left: 10px;">Laundry</td>
				<td width="15%"></td>
				<td width="15%" style="padding-right: 5px; text-align: right;">'.number_format($laundry,2).'</td>
				<td width="30%" style="padding-left: 10px; border-left: 0.1mm solid black;">Witholding Tax</td>
				<td width="15%" style="padding-right: 5px; text-align: right; border-right: 0.1mm solid black;">'.number_format($wtax,2).'</td>	
			</tr>
			<tr>
				<td width="25%" style="border-left: 0.1mm solid black; padding-left: 10px;">Other Non-Taxable Allowance</td>
				<td width="15%"></td>
				<td width="15%" style="padding-right: 5px; text-align: right;">'.number_format($ontx,2).'</td>
				<td width="30%" style="padding-left: 10px; border-left: 0.1mm solid black;"></td>
				<td width="15%"  style="padding-right: 5px; text-align: right; border-right: 0.1mm solid black;"></td>	
			</tr>
			<tr>
				<td width="25%" style="border-left: 0.1mm solid black; padding-left: 10px;">Insurance</td>
				<td width="15%"></td>
				<td width="15%" style="padding-right: 5px; text-align: right;">'.number_format($insurance,2).'</td>
				<td width="30%" style="padding-left: 10px; border-left: 0.1mm solid black;"></td>
				<td width="15%" style="padding-right: 5px; text-align: right; border-right: 0.1mm solid black;"></td>	
			</tr>
			<tr>
				<td width="40%" style="border-left: 0.1mm solid black; border-top: 0.1mm solid black; padding-left: 10px;" colspan=2><b>GROSS PAY</b></td>
				<td width="15%" style="border-right: 0.1mm solid black; border-top: 0.1mm solid black; padding-right: 5px; text-align: right;"><b>'.number_format($gross,2).'</b></td>
				<td width="30%" style="border-top: 0.1mm solid black; padding-left: 10px;"><b>TOTAL DEDUCTIONS</b></td>
				<td width="15%" style="border-right: 0.1mm solid black; border-top: 0.1mm solid black; padding-right: 5px; text-align: right;"><b>'.number_format($tDeductions,2).'</b></td>	
			</tr>
			<tr>
				<td width="55%" style="border-left: 0.1mm solid black; border-bottom: 0.1mm solid black; border-top: 0.1mm solid black; padding-left: 10px;" colspan=3>&nbsp;</td>
				<td width="30%" style="border-top: 0.1mm solid black; border-bottom: 0.1mm solid black; padding-left: 10px;"><b>NET PAY >></b></td>
				<td width="15%" style="border-right: 0.1mm solid black; border-bottom: 0.1mm solid black; border-top: 0.1mm solid black; padding-right: 5px; text-align: right;"><b>'.number_format(ROUND($gross-$tDeductions-$bu_assist,2),2).'</b></td>	
			</tr>
		</table>
		
		<table><tr><td height=55 valign=middle>-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</td></tr></table>
		';
	//}br->'.ROUND($res['basic_rate']/2,2).' mylate -> '.$mylate.' UT -> '.$myut.' wopay -> '.$wopay.' holiday -> '.$holPay.' ut -> '.$ut.' br->'.$br.' hr-> '.$hr.' late -> '.$late.'
		
}
$html = $html . '</body> 
</html>
';

$html = iconv("UTF-8", "ISO-8859-1//IGNORE", $html);

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>