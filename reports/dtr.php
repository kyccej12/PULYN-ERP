<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");

	$mpdf=new mPDF('win-1252','letter','','',10,10,32,20,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);

	/* MYSQL QUERIES SECTION */

		$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
		$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");

		$now = date("m/d/Y h:i a");
		if($_GET['id_no'] != "") { $f1 = " and a.emp_id = '$_GET[id_no]' "; }
		if($_GET['dept'] != '') { $f2 = " and b.department = '$_GET[dept]' "; }
		$query = mysql_query("SELECT DISTINCT CONCAT('(',id_no,') ',lname,', ',fname,' ',mname) AS employee, a.emp_id, b.department FROM hris.e_dtr a LEFT JOIN hris.e_master b ON a.emp_id = b.id_no WHERE `date` BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' AND emp_id != '' and company = '$_SESSION[company]' $f1 $f2 ORDER BY b.lname ASC;");
	
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
		<td style="color:#000000;" width=150><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
		<td style="color:#000000; padding-top: 15px;">
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
		</td>
		<td align=right><span style="font-weight: bold; font-size: 8pt;">Daily Time Record<br />Date Covered: '. $_GET['dtf'] . ' - ' . $_GET['dt2'] . '</span></td>
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
<table class="items" width="100%" align=center style="font-size: 8pt; border-collapse: collapse;" cellpadding="4">
<thead>
	<tr>
		<td width="30%" align=left><b>EMPLOYEE</b></td>
		<td width="10%" align=center><b>DEPT</b></td>
		<td width="12%" align=center><b>DATE</b></td>
		<td width="10%" align=center><b>TIME IN</b></td>
		<td width="10%" align=center><b>TIME OUT</b></td>
		<td width="10%" align=center><b>REG. HRS</b></td>
		<td width="10%" align=center><b>LATE</b></td>
		<td width="8%" align=center><b>O.T</b></td>
	</tr>
</thead>
<tbody>';

while($row = mysql_fetch_array($query)) {
	$a = dbquery("select date_format(`date`,'%a %m/%d/%y') as xday, t_in, t_out, hrs, late, ot, ot_approved from hris.e_dtr where emp_id = '$row[emp_id]' and `date` between '".formatDate($_GET['dtf'])."' and '".formatDate($_GET['dt2'])."' order by `date` asc;");
	$hGT = 0; $lGT = 0; $otGT = 0;
	while($b = mysql_fetch_array($a)) {
		if($row['emp_id'] != $oldbio) { $emp = $row['employee']; $dept = $row['department']; } else { $emp = ""; $dept = "";}
		if($b['ot'] > 0) { $ota = "($b[ot_approved])"; } else { $ota = ""; }
		$html = $html . '<tr>
			<td width="30%" align=left>'.$emp.'</td>
			<td width="10%" align=center>'.$dept .'</td>
			<td width="12%" align=center>'.$b['xday'].'</td>
			<td width="10%" align=center>'.$b['t_in'].'</td>
			<td width="10%" align=center>'.$b['t_out'].'</td>
			<td width="10%" align=center>'.$b['hrs'].'</td>
			<td width="10%" align=center>'.$b['late'].'</td>
			<td width="8%" align=center>'.$b['ot'].' '.$ota.'</td>
		</tr>';
		$oldbio = $row['emp_id']; $hGT += $b['hrs']; $lGT+=$b['late']; 

		if($b['ot_approved'] == 'Y') { $otGT+=$b['ot']; }
	}
	$html = $html . '<tr>
			<td colspan=5></td>
			<td style="border-top: 0.1mm solid black; border-bottom:  0.1mm solid black;" align=center>'.number_format($hGT,2).'</td>
			<td style="border-top: 0.1mm solid black; border-bottom:  0.1mm solid black;" align=center>'.number_format($lGT,2).'</td>
			<td style="border-top: 0.1mm solid black; border-bottom:  0.1mm solid black;" align=center>'.number_format($otGT,2).'</td>
	</tr>';
}
$html = $html . '</tbody></table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>