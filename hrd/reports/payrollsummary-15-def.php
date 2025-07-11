<?php
session_start();
require_once '../../handlers/initDB.php';
require_once '../../lib/mpdf6/mpdf.php';
ini_set("memory_limit","1024M");
ini_set("max_execution_time",0);
ini_set("display_errors","On");

$pay = new myDB;

$mpdf=new mPDF('win-1252','A3-L','','',8,8,25,25,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetTitle("Payroll Summary");
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(75);

	/* MYSQL QUERIES SECTION */
		$cutoff = $_GET['cutoff'];
		if($_GET['proj'] != '') { $f1 = " and proj = '$_GET[proj]' "; }
		
		$now = date("m/d/Y h:i a");
		$co = $pay->getArray("select * from companies where company_id = '$_SESSION[company]';");
		$fDates = $pay->getArray("select date_format(period_start,'%m/%d/%Y') as dtf, date_format(period_end,'%m/%d/%Y') as dt2 from omdcpayroll.pay_periods where period_id = '$_GET[cutoff]';");
	
	/* END OF SQL QUERIES */

$html = '
<html>
<head>
<style>
body {
	font-family: sans-serif;
    font-size: 7pt;
}
p {    margin: 0pt;
}
td { vertical-align: top; }

table thead td {
    text-align: center;
    border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
}
.items td.blanktotal {
    background-color: #FFFFFF;
    border: 0mm none #000000;
    border-top: 0.1mm solid #000000;
    border-right: 0.1mm solid #000000;
}
.items td.totals {
    text-align: right;
    border: 0.1mm solid #000000;
}

.items td {
	border: 0.1mm solid black;
}
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
	<tr>
		<td style="color:#000000;">
			<span style="font-size: 7pt;"><b>'.strtoupper($co['company_name']).'</b><br/>'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'</span>
		</td>
		<td align=right><span style="font-size: 7pt;"><b>PAYROLL REGISTER</b><br />Cutoff Period: '. $fDates[0] . '-' . $fDates[1] . '</span></td>
	</tr>
</table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table style="border-top: 1px solid #000000; font-size: 7pt; width: 100%">
<tr>
<td width="50%" align="left">Page {PAGENO} of {nb}</td>
<td width="50%" align="right" style="font-size:7pt; font-color: #cdcdcd;">Run Date: ' . $now . '</td>
</tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table class="items" width="100%" style="font-size: 5pt; border-collapse: collapse;" cellpadding="2">
<thead>
	<tr style="background-color: #cdcdcd; font-weight: bold;">
		<td align=center><b>ID #</b></td>
		<td align=center><b>EMPLOYEE</b></td>
		<td align=center><b>ACCT #</b></td>
		<td align=center><b>DESIGNATION</b></td>
		<td align=center><b>NO. OF<br/>DAYS</b></td>
		<td align=center><b>BASIC<br/>PAY</b></td>
		<td align=center><b>LESS:<br/>ABSENT</b></td>
		<td align=center><b>LESS:<br/>LATE</b></td>
		<td align=center><b>LESS:<br/>UT</b></td>
		<td align=center><b>COLA</b></td>
		<td align=center><b>S.I.L</b></td>
		<td align=center><b>OTHER<br/>LEAVES</b></td>
		<td align=center><b>RD HOL</b></td>
		<td align=center><b>LGL HOL</b></td>
		<td align=center><b>SP HOL</b></td>
		<td align=center><b>OT<br/>REG</b></td>
		<td align=center><b>OT-RD<br/>(REG+EX)</b></td>
		<td align=center><b>OT LH<br/>(REG+EX)</b></td>
		<td align=center><b>OT SH<br/>(REG+EX)</b></td>
		<td align=center><b>N-PREM</b></td>
		<td align=center><b>ALLOW<br/>(TAX)</b></td>
		<td align=center><b>ALLOW<br/>(NON-TAX)</b></td>
		<td align=center><b>SAL<br/>ADJ</b></td>
		<td align=center><b>GROSS<br/>PAY</b></td>
		<td align=center><b>HDMF<br/>PREM</b></td>
		<td align=center><b>PHIC</b></td>
		<td align=center><b>WTAX</b></td>
		<td align=center><b>HDMF<br/>LOAN</b></td>
		<td align=center><b>C.A</b></td>
		<td align=center><b>COOP<br/>LOAN</b></td>
		<td align=center><b>HEALTH<br/>INS.</b></td>
		<td align=center><b>OTHER<br/>LOANS</b></td>
		<td align=center><b>OTHER<br/>DED.</b></td>
		<td align=center><b>TOT<br/>DED.</b></td>
		<td align=center><b>NET<br/>PAY</b></td>
		<td align=center><b>ON<br/>HOLD</b></td>
		<td align=center><b>CASH</b></td>
		<td align=center><b>ON ATM</b></td>
	</tr>
</thead>
<tbody>';

	$a = $pay->dbquery("select * from omdcpayroll.emp_payslip where period_id = '$cutoff' $f1 order by dept, emp_name;");
	while($row = $a->fetch_array(MYSQLI_BOTH)) {

		list($type,$desg) = $pay->getArray("select ATM_BANK,DESG from omdcpayroll.emp_masterfile where emp_id = '$row[emp_id]';");
		if($row['on_hold'] != 'Y') {
			
			if($type == 0) { $cash = $row['net_pay']; $atm = 0; } else { $cash = 0; $atm = $row['net_pay']; }
		} else { $cash = 0; $atm = 0; }
	
		$otRD = $row['ot_sunday'] + $row['ot_sundayex'];
		$otLH = $row['ot_legalholiday'] + $row['ot_legalholidayex'];
		$otSH = $row['ot_specialholiday'] + $row['ot_specialholidayex'];
		$basicPay = $row['basic_pay']+$row['absences']+$row['late']+$row['undertime'];
		$dedTotal = $row['pagibig_premium'] + $row['philhealth_premium'] + $row['wtax'] + $row['hdmf_loan'] + $row['cash_adv'] + $row['equicom'] + $row['coop_loan'] + $row['health_ins'] + $row['other_loans'] + $row['others_total'];
		
		$html = $html . '<tr>
			<td align="left">' . $row['emp_id'] . '</td>
			<td align="left" width=150>' . $row['emp_name'] . '</td>
			<td align="left">' . $row['acct_no'] . '</td>
			<td align="left" width=100>' . $desg . '</td>
			<td align="right" width=40>' . number_format($row['basic_day'],2) . '</td>
			<td align="right" width=40>' . number_format($basicPay,2) . '</td>
			<td align="right" width=40>(' . number_format($row['absences'],2) . ')</td>
			<td align="right" width=40>(' . number_format($row['late'],2) . ')</td>
			<td align="right" width=40>(' . number_format($row['undertime'],2) . ')</td>
			<td align="right" width=40>' . number_format($row['cola'],2) . '</td>
			<td align="right" width=40>' . number_format($row['vacation_leave'],2) . '</td>
			<td align="right" width=40>' . number_format($row['other_leaves'],2) . '</td>
			<td align="right" width=40>' . number_format($row['holiday_on_restday'],2) . '</td>
			<td align="right" width=40>' . number_format($row['legal_holiday'],2) . '</td>
			<td align="right" width=40>' . number_format($row['special_holiday'],2) . '</td>
			<td align="right" width=40>' . number_format($row['ot_regular'],2) . '</td>
			<td align="right" width=40>' . number_format($otRD,2) . '</td>
			<td align="right" width=40>' . number_format($otLH,2) . '</td>
			<td align="right" width=40>' . number_format($otSH,2) . '</td>
			<td align="right" width=40>' . number_format($row['night_premium'],2) . '</td>
			<td align="right" width=40>' . number_format($row['allowance'],2) . '</td>
			<td align="right" width=40>' . number_format($row['nontax_allowance'],2) . '</td>
			<td align="right" width=40>' . number_format($row['adjustments'],2) . '</td>
			<td align="right" width=40>' . number_format($row['gross_pay'],2) . '</td>
			<td align="right" width=40>' . number_format($row['pagibig_premium'],2) . '</td>
			<td align="right" width=40>' . number_format($row['philhealth_premium'],2) . '</td>
			<td align="right" width=40>' . number_format($row['wtax'],2) . '</td>
			<td align="right" width=40>' . number_format($row['hdmf_loan'],2) . '</td>
			<td align="right" width=40>' . number_format($row['cash_adv'],2) . '</td>
			<td align="right" width=40>' . number_format($row['coop_loan'],2) . '</td>
			<td align="right" width=40>' . number_format($row['health_ins'],2) . '</td>
			<td align="right" width=40>' . number_format($row['other_loans'],2) . '</td>
			<td align="right" width=40>' . number_format($row['others_total'],2) . '</td>
			<td align="right" width=40>' . number_format($dedTotal,2) . '</td>
			<td align="right" width=40>' . number_format($row['net_pay'],2) . '</td>
			<td align=center width=40>'.$row['on_hold'].'</td>
			<td align="right" width=40>' . number_format($cash,2) . '</td>
			<td align="right" width=40>' . number_format($atm,2) . '</td>
		</tr>';
			
			$basicPayGT+=$row['basic_pay'];
			$absTotal+=$row['absences'];
			$lateTotal+=$row['late'];
			$utTotal+=$row['undertime'];
			$colaGT+=$row['cola'];
			$vlGT+=$row['vacation_leave'];
			$silGT+=$row['other_leaves'];
			$rdhGT+=$row['holiday_on_restday'];
			$lgGT+=$row['legal_holiday']; 
			$spGT+=$row['special_holiday'];
			$otGT+=$row['ot_regular']; 
			$otRDGT+=$otRD;
			$otSHGT=$otSH;
			$otLHGT+=$otLH;
			$npGT+=$row['night_premium']; 
			$altGT+=$row['allowance']; 
			$alntGT+=$row['nontax_allowance'];
			$mealGT+=$row['meal_allowance'];
			$transpoGT+=$row['transpo_allowance'];
			$adjGT+=$row['adjustments'];
			$grossGT+=$row['gross_pay'];
			$hdmfGT+=$row['pagibig_premium'];
			$phicGT+=$row['philhealth_premium'];
			$wtaxGT+=$row['wtax'];
			$hdloanGT+=$row['pagibig_loan'];
			$caGT+=$row['cash_adv'];
			$equicomGT+=$row['equicom'];
			$cooploanGT+=$row['coop_loan'];
			$hinsGT+=$row['health_ins'];
			$otherLoansGT+=$row['other_loans'];
			$othersGT+=$row['others_total'];
			$dedTotalGT+=$dedTotal;
			$netGT+=$row['net_pay'];
			$cashGT+=$cash;
			$atmGT+=$atm;
	}
	
	$html .= '<tr style="background-color: #cdcdcd; font-weight: bold;">
		<td align="left" colspan=5 style="font-weight: bold;">GRAND TOTAL</td>
		<td align="right" style="font-weight: bold;">' . number_format($basicPayGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($absTotal,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($lateTotal,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($utTotal,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($colaGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($vlGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($silGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($rdhGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($lgGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($spGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($otGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($otRDGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($otLHGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($otSHGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($npGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($altGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($alntGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($adjGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($grossGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($hdmfGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($phicGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($wtaxGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($hdloan,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($caGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($cooploanGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($hinsGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($otherLoansGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($othersGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($dedTotalGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($netGT,2) . '</td>
		<td align=center>'.$row['on_hold'].'</td>
		<td align="right" style="font-weight: bold;">' . number_format($cashGT,2) . '</td>
		<td align="right" style="font-weight: bold;">' . number_format($atmGT,2) . '</td>
	</tr>';
	
	
$html = $html . '
</tbody>
</table>';

	
	$html .= '<table style="font-size: 5pt; margin-top: 50px;" cellpadding=20 cellspacing=0 align=left>
		<tr>
			<td>Prepared By:  ______________________________________________</td>
			<td>Checked By:  ______________________________________________</td>
			<td>Approved By:  ______________________________________________</td>
		<tr>
	</table>';
	


$html .= '</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>