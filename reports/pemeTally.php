<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	
	//ini_set("display_errors","On");
	require_once "../lib/mpdf6/mpdf.php";
	require_once "../handlers/_generics.php";
	
	$con = new _init;
	
	//include("../includes/dbUSE.php");
	
	
	$mpdf=new mPDF('win-1252','letter','','',15,15,68,15,15,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);
	
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
	$fs = '';
	
	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$dtf = $con->formatDate($_GET['dtf']);
		$dt2 = $con->formatDate($_GET['dt2']);

		$query = $con->dbquery("SELECT DISTINCT compname FROM peme a WHERE (a.examined_by = '$_GET[cid]' OR a.evaluated_by = '$_GET[cid]') AND (DATE(a.evaluated_on) BETWEEN '$dtf' AND '$dt2' OR DATE(a.examined_on) BETWEEN '$dtf' AND '$dt2');");

		list($doctorsName,$prefix,$specs,$licno) = $con->getArray("SELECT fullname,prefix,specialization,license_no from options_doctors where id = '$_GET[cid]';");
		
		/* END OF SQL QUERIES */

$html = '
<html>
<head>
<style>
body {
	font-family: calibri;
    font-size: 8pt;
}
p {    margin: 0pt;
}
td { vertical-align: top; }

table thead td {
	border: 0.1mm solid #000000;
	background-color: #cdcdcd;
}

.borders {
    border: 0.1mm solid #000000;
}
.border-right { 
	border-right: 0.1mm solid #000000;
}
.border-left {
	border-left: 0.1mm solid #000000;
}
.border-top {
	border-top: 0.1mm solid #000000;
}
.border-bottom {
	border-bottom: 0.1mm solid #000000;
}



</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%" cellpadding=0 cellspading=0>
	<tr><td align=center><img src="../images/doc-header.jpg" /></td></tr>
	<tr><td height=10></td></tr>
	<tr>
		<td width="100%" align=center><span style="font-weight: bold; font-size: 14pt; color: #000000;">PEME TALLY REPORT</span></td>
	</tr>
	<tr>
		<td width="100%" align=center><span style="font-weight: bold; font-size: 11pt; color: #000000;">&nbsp;</span></td>
	</tr>
	<tr><td height=15></td></tr>
</table>
<table width=100% cellspacing=0 cellpadding=0>
	<tr>
		<td width=50%>
			<span style="font-size:14px;font-weight:bold;">Dr.&nbsp;'.$doctorsName.',&nbsp;'.$prefix.'</span><br/>
			<span style="font-size:11px;">'.$specs.'</span><br/>
			<span style="font-size:11px;">License No.&nbsp;'.$licno.'</span><br/>
		</td>
		<td align=right valign=top><span style="font-size:12px;">Covered Period&nbsp;:&nbsp;'.$_GET['dtf'].' - '.$_GET['dt2'].'</span></td>
	</tr>
</table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table style="border-top: 1px solid #000000; font-size: 8pt; width: 100%">
<tr>
<td width="50%" align="left">Page {PAGENO} of {nb}</td>
<td width="50%" align="right" style="font-size:8pt; font-color: #cdcdcd;">Run Date: ' . $now . '</td>
</tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table class="items" width="100%" style="font-size: 12px; border-collapse: collapse;" cellpadding="2">
	<thead>
		<tr>
			<td align=center ><b>COMPANY</b></td>
			<td align=center ><b>EXAMINED</b></td>
			<td align=center ><b>EVALUATED</b></td>
			<td align=center ><b>PAPSMEAR</b></td>
		</tr>
	</thead>
 <tbody>';

$totalexamin = 0; $totaleval = 0; $totalpap = 0;  $i = 0;
$perExamin = 35; $perEval = 15; $papExam = 150;

while($row = $query->fetch_array()) {

	list($examintotal) = $con->getArray("SELECT COUNT(DISTINCT so_no) AS pidcount FROM peme WHERE date(examined_on) BETWEEN '$dtf' AND '$dt2' AND examined_by = '$_GET[cid]' and compname = '$row[compname]';");
	list($evaltotal) = $con->getArray("SELECT COUNT(DISTINCT so_no) AS pidcount FROM peme WHERE date(evaluated_on) BETWEEN '$dtf' AND '$dt2' AND evaluated_by = '$_GET[cid]' and compname = '$row[compname]';");
	list($paptotal) = $con->getArray("SELECT COUNT(DISTINCT so_no) AS pidcount FROM peme WHERE date(evaluated_on) BETWEEN '$dtf' AND '$dt2' AND evaluated_by = '$_GET[cid]' and compname = '$row[compname]' and pap_normal= 'Y';");

	$etotal =''; $vtotal = ''; $ptotal = '';
	if($row['compname'] == '') { $row['compname'] = 'Walkin Customer'; }

	if($examintotal != $paymaya) { $vtotal = number_format($examintotal,2); $vtotal= ''; $totalexamin += $examintotal; }
	if($evaltotal != $paymaya) { $etotal = number_format($evaltotal,2); $etotal= ''; $totaleval += $evaltotal; }
	if($paptotal != $paymaya) { $ptotal = number_format($paptotal,2); $ptotal= ''; $totalpap += $paptotal; }

	$html = $html . '<tr bgcolor="'.$con->initBackground($i).'">
		<td class="borders" width=60% align=left style="padding-left: 15px;">' . $row['compname'] . '</td>
		<td class="borders" width=20% align=center>'.$examintotal.'</td>
		<td class="borders" width=20% align=center>'.$evaltotal.'</td>
		<td class="borders" width=20% align=center>'.$paptotal.'</td>
		
	</tr>'; $paymaya = $row['compname']; $i++;
}

$totalAmtExamin = $totalexamin * $perExamin;
$totalAmtEval = $totaleval * $perEval;
$totalAmtPap = $totalpap * $papExam;
$totalAmt = $totalAmtExamin + $totalAmtEval + $totalAmtPap;


$html = $html . '<tr bgcolor="'.$con->initBackground($i).'">
				<td class="border-left border-right" align=right>Grand Total</td>
				<td class="border-left border-right" align=center>' . number_format($totalexamin) . '</td>
				<td class="border-left border-right" align=center>' . number_format($totaleval) . '</td>
				<td class="border-left border-right" align=center>' . number_format($totalpap) . '</td>
				</tr>';

$html = $html . '<tr>
				<td class="border-left border-right" align=right>Rate Per Patient</td>
				<td class="border-left border-right" align=center>' . number_format($perExamin,2) . '</td>
				<td class="border-left border-right" align=center>' . number_format($perEval,2) . '</td>
				<td class="border-left border-right" align=center>' . number_format($papExam,2).'</td>
				</tr>
				<tr>
				<td class="border-left border-right border-bottom" align=right>Total</td>
				<td class="border-left border-right border-bottom" align=center>' . number_format($totalAmtExamin,2) . '</td>
				<td class="border-left border-right border-bottom" align=center>' . number_format($totalAmtEval,2) . '</td>
				<td class="border-left border-right border-bottom" align=center>' . number_format($totalAmtPap,2) . '</td>
				</tr>';

$html = $html . '<tr style="background-color: #ededed;">
				<td class="borders" align=right><b>AMOUNT TOTAL</b></td>
				<td class="borders" align=center colspan=3><b>' . number_format($totalAmt,2) . '</b></td>
				</tr>';
$html = $html . '
</tbody>
</table>
</body>
</html>
';

$html = html_entity_decode($html);
$mpdf->WriteHTML($html);
$mpdf->Output(); 
exit;

mysql_close($con);
?>