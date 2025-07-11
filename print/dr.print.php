<?php
	include("../lib/mpdflib/mpdf.php");
	include("../includes/dbUSE.php");


/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$_ihead = getArray("select dr_no, lpad(dr_no,6,0) as rr, date_format(dr_date,'%m/%d/%Y') as d8, dr_stub_no, customer, customer_name, customer_addr, amount from dr_header where dr_no = '$_REQUEST[dr_no]';");
	$_idetails = dbquery("select description, qty, unit, cost, amount from dr_details where dr_no = '$_REQUEST[dr_no]';");
	$bcode = 'DR'.STR_PAD($_REQUEST['user'],2,'0',STR_PAD_LEFT)."-".$_ihead['dr_no']."-".date('Ymd');
	
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','letter','','',15,15,28,15,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");

if($_REQUEST['reprint'] == 'Y') {
	$mpdf->SetWatermarkText('REPRINTED COPY');
	$mpdf->showWatermarkText = true;
}

$mpdf->SetDisplayMode(60);

$html = '
<html>
<head>
<style>
body {font-family: sans-serif; font-size: 9pt; }
td { vertical-align: top; }

table thead td { 
	border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
	background-color: #EEEEEE;
    text-align: center;
}

.td-l { border-left: 0.1mm solid #000000; }
.td-r { border-right: 0.1mm solid #000000; }
.empty { border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; }

.items td.blanktotal {
    background-color: #FFFFFF;
    border: 0.1mm solid #000000;
}
.items td.totals-l-top {
    text-align: right; font-weight: bold;
    border-left: 0.1mm solid #000000;
	border-top: 0.1mm solid #000000;
}
.items td.totals-r-top {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000;
	border-top: 0.1mm solid #000000;
}
.items td.totals-l {
    text-align: right; font-weight: bold;
    border-left: 0.1mm solid #000000;
}
.items td.totals-r {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000;
}

.items td.tdTotals-l {
    text-align: left; font-weight: bold;
    border-left: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;  background-color: #EEEEEE;
}
.items td.tdTotals-r {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000; background-color: #EEEEEE;
}

.items td.tdTotals-l-1 {
    text-align: left;
    border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}
.items td.tdTotals-r-1 {
    text-align: right;
    border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}

.td-l-top { 	
		background-color: #EEEEEE; padding: 3px;
		text-align: left; font-weight: bold;
		border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000;
		border-top: 0.1mm solid #000000;
	}
.td-r-top { 
	text-align: right; font-weight: bold; padding: 3px;
    border-right: 0.1mm solid #000000;
	border-top: 0.1mm solid #000000;
}

.td-l-head {
	text-align: left; font-weight: bold; padding: 3px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; background-color: #EEEEEE;
}

.td-r-head {
	text-align: right; font-weight: bold; padding: 3px;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000;
}
.td-l-head-bottom {
	text-align: left; font-weight: bold; padding: 3px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; background-color: #EEEEEE; border-bottom: 0.1mm solid #000000;
}

.td-r-head-bottom {
	text-align: right; font-weight: bold; padding: 3px;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}

.billto {
	font-size: 12px; vertical-align: top; padding: 3px;
}
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%"><tr>
<td width="70%" style="color:#000000;">
	<img src="../images/geck-small-logo.jpg" /><br /><span style="color: #3b3b3b; font-size: 8pt;">KM. 3, Brgy. Luna, Surigao City, Philippines</span>
</td>
<td width="30%" align=right>
	<span style="font-weight: bold; font-size: 13pt; color: #000000;">DELIVERY RECEIPT&nbsp;&nbsp;</span><br />
	<barcode code="'.substr($bcode,0,10).'" type="C128A">
</td>
</tr>
</table>
</htmlpageheader>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="off" />
mpdf-->
<table width="100%" cellspacing=0 cellpadding=0>
<tr>
<td class="billto" width=50% rowspan="4">
<b>CUSTOMER :</b><br /><br /><b>('.$_ihead['customer'].') '.$_ihead['customer_name'].'</b><br /><i>'.$_ihead['customer_addr'].'</i></td>
<td class="td-l-top"><b>Doc No.</b></td>
<td class="td-r-top"><b>' . $_ihead['rr'] . '</b></td>
</tr>
<tr>
<td class="td-l-head-bottom"><b>DR Loose Leaf #</b></td>
<td class="td-r-head-bottom"><b>' . $_ihead['dr_stub_no'] . ' </b></td>
</tr>
<tr>
<td class="td-l-head"><b>Doc Date</b></td>
<td class="td-r-head"><b>' . $_ihead['d8'] . '</b></td>
</tr>
<tr>
<td class="td-l-head-bottom"><b>Amount</b></td>
<td class="td-r-head-bottom"><b>' . number_format($_ihead['amount'],2) . ' PHP</b></td>
</tr>
</table>
<table><tr><td height=40></td></tr></table>
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse;" cellpadding="3">
<thead>
<tr>
<td width="45%" align=left><b>PARTICULARS</b></td>
<td width="15%" align=right><b>QTY</b></td>
<td width="10%"><b>UNIT</b></td>
<td width="15%" align=right><b>COST</b></td>
<td width="15%" align=right><b>AMOUNT</b></td>
</tr>
</thead>
<tbody>';
	$i = 0;
	while($row = mysql_fetch_array($_idetails)) {
		
		$html = $html . '<tr>
		<td align=left>' . $row['description'] . '</td>
		<td align="right">' . number_format($row['qty'],2) . '</td>
		<td align="center">' . identUnit($row['unit']) . '</td>
		<td align="right">' . number_format($row['cost'],2) . '</td>
		<td align="right">' . number_format($row['amount'],2) . '</td>
		</tr>'; $i++;
	}

	for($i; $i < 16; $i++) { $html = $html . "<tr><td colspan=6></td></tr>"; }
	
$html = $html .  '<tr><td align=left><b>REMARKS :</b><td colspan=7>'.$_ihead['remarks'].'</td></tr>
</tbody>
</table>
<table width=100% cellpadding=5 style="border: 1px solid #000000;">
<tr>
	<td width=33%><b>PREPARED BY:</b><br><br>'.getUname($_REQUEST['user']).'<br></td>
	<td width=33%><b>APPROVED BY:</b><br><br>_________________________________<br><font size=3>Signature over Printed Name</font></td>
	<td width=34%><b>RECEIVED BY:</b><br><br>_________________________________<br><font size=3>Signature over Printed Name</font></td>
</tr>
</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>