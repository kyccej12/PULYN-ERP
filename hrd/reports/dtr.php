<?php

	require_once '../../lib/mpdf6/mpdf.php';
	require_once '../../handlers/_payroll.php';
	
	ini_set("display_errors","on");
	ini_set("max_execution_time",-1);
	ini_set("memory_limit",-1);
	
	$mypayroll = new payroll($_GET['period']);
	
	session_start();

	$mpdf=new mPDF('win-1252','FOLIO-L','','',10,10,32,20,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
	
		if($_GET['dept'] != "") { $f1 = " and a.dept = '$_GET[dept]' "; }
		
		$query = $mypayroll->dbquery("SELECT DISTINCT CONCAT('(',a.EMP_ID,') ',lname,', ',fname,' ',mname) AS employee, a.emp_id FROM omdcpayroll.emp_dtrfinal a LEFT JOIN omdcpayroll.emp_masterfile b ON a.EMP_ID = b.EMP_ID WHERE a.period_id = '$_GET[period]' $f1 ORDER BY b.lname, b.fname;");
		/* END OF SQL QUERIES */

$html = '
<html>
<head>
<style>
body {font-family: sans-serif;
    font-size: 8pt;
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

.items td.lowertotals {
	border: 0mm none #000000;
    border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
}

</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
	<tr>
		<td>
			<span style="font-size: 7pt;"><b>Pulyn Dialysis &amp; Diagnostics Clinic</b><br/>Esperanza Village, Poblacion 3, Carcar City, 6019 Cebu<br/>Tel # (032) 326-1580</span>
		</td>
		<td width="40%" align=right>
			<span style="font-weight: bold; font-size: 9pt; color: #000000;">Daily Time Record</span><br /><span style="font-size: 6pt; font-style: italic;">Cut-off Period ' . $mypayroll->dtf . ' to ' . $mypayroll->dt2 .'</span>
		</td>
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
<table class="items" width="100%" align=center style="font-size: 8pt; border-collapse: collapse;" cellpadding="3">
<thead>
	<tr>
		<td width="15%" align=left><b>EMPLOYEE</b></td>
		<td width="10%" align=center><b>DATE</b></td>
		<td width="10%" align=center><b>IN (AM)</b></td>
		<td width="10%" align=center><b>OUT (PM)</b></td>
		<td align=center><b>LATE (MINS)</b></td>
		<td align=center><b>REG. HRS</b></td>
		<td align=center><b>REG. OT</b></td>
		<td align=center><b>SUN OT</b></td>
		<td align=center><b>PREM OT</b></td>
		<td align=center><b>OT APPROVED</b></td>
		<td align=center><b>REMARKS</b></td>
	</tr>
</thead>
<tbody>';

while($row = $query->fetch_array()) {
	$a = $mypayroll->dbquery("SELECT DISTINCT emp_id, `date` AS yday, shift, DATE_FORMAT(`date`,'%a %m/%d/%y') AS xday, IF(clockin!='00:00:00',LEFT(clockin,LENGTH(clockin)-3),'') AS in_am,IF(clockout!='00:00:00',LEFT(clockout,LENGTH(clockout)-3),'') AS out_pm, IF(TOT_WORK>0,TOT_WORK,'') AS hrs, IF(TOT_LATE>0,TOT_LATE*60,'') AS late, IF(REG_OT>0,REG_OT,'') AS ot, IF(SUN_OT>0,SUN_OT,'') AS sot, IF(PREM_OT>0,PREM_OT,'') AS pot, (REG_OT+SUN_OT+PREM_OT) AS tot, ot_approve FROM omdcpayroll.emp_dtrfinal WHERE emp_id = '$row[emp_id]' AND period_id = '$_GET[period]' ORDER BY `date` ASC;");
	$hGT = 0; $lGT = 0; $otGT = 0;
	while($b = $a->fetch_array()) {
		if($row['emp_id'] != $oldid) { $emp = $row['employee']; } else { $emp = ""; }
		if($b['tot'] > 0) { $ota = "($b[ot_approve])"; } else { $ota = ""; }
		
		$html = $html . '<tr>
			<td align=left>'.$emp.'</td>
			<td align=center>'.$b['xday'].'</td>
			<td align=center>'.$b['in_am'].'</td>
			<td align=center>'.$b['out_pm'].'</td>
			<td align=center>'.$b['late'].'</td>
			<td align=center>'.$b['hrs'].'</td>
			<td align=center>'.$b['ot'].'</td>
			<td align=center>'.$b['sot'].'</td>
			<td align=center>'.$b['pot'].'</td>
			<td align=center>'.$ota.'</td>
		</tr>';
		$oldid = $row['emp_id']; $hGT += $b['hrs']; $lGT+=$b['late']; 

		if($b['ot_approve'] == 'Y') { $otGT+=$b['ot']; $sotGT+=$b['sot']; $potGT+=$b['pot']; }
	}
	$html = $html . '<tr>
			<td colspan=4></td>
			<td style="border-top: 0.1mm solid black; border-bottom:  0.1mm solid black;" align=center>'.ROUND($lGT*60,2).'</td>
			<td style="border-top: 0.1mm solid black; border-bottom:  0.1mm solid black;" align=center>'.number_format($hGT,2).'</td>
			<td style="border-top: 0.1mm solid black; border-bottom:  0.1mm solid black;" align=center>'.number_format($otGT,2).'</td>
			<td style="border-top: 0.1mm solid black; border-bottom:  0.1mm solid black;" align=center>'.number_format($sotGT,2).'</td>
			<td style="border-top: 0.1mm solid black; border-bottom:  0.1mm solid black;" align=center>'.number_format($potGT,2).'</td>
			<td></td>
			<td></td>
	</tr>';
}
$html = $html . '</tbody></table>
</body>
</html>
';

$html = iconv("UTF-8", "ISO-8859-1//IGNORE", $html);
$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

?>