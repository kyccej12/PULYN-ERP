<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
	$_ihead = $con->getArray("select trace_no, doc_no, lpad(doc_no,2,0) as rr, date_format(posting_date,'%m/%d/%Y') as d8, conducted_by, amount, remarks from phy_header where doc_no = '$_REQUEST[doc_no]' and branch = '$_SESSION[branchid]';");
	$_idetails = $con->dbquery("select item_code, description, qty, unit, cost, amount from phy_details where doc_no = '$_REQUEST[doc_no]' and branch = '$_SESSION[branchid]';");
	$bcode = $_ihead['trace_no'];
	
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','letter','','',15,15,60,15,10,10);
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
<table width="100%" cellpadding=0 cellspaing=0><tr>
<td style="color:#000000; padding-top: 15px;">
	<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
</td>
<td width="40%" align=right>
	<span style="font-weight: bold; font-size: 13pt; color: #000000;">PHYSICAL INVENTORY FORM&nbsp;&nbsp;</span><br />
	<barcode size=0.8 code="'.substr($bcode,0,10).'" type="C128A">
</td>
</tr>
</table>
<table width="100%" cellspacing=0 cellpadding=0>
<tr>
<td class="billto" width=60% rowspan="6">
<b>Inventory Conducted By :</b><br/><br/><i>'.$_ihead['conducted_by'].'</i></td>
<td class="td-l-top"><b>Doc No</b></td>
<td class="td-r-top"><b>' .$_ihead['doc_no']. '</b></td>
</tr>
<tr>
<td class="td-l-head"><b>Posting Date</b></td>
<td class="td-r-head"><b>' . $_ihead['d8'] . '</b></td>
</tr>
<tr>
<td class="td-l-head-bottom"><b>Amount</b></td>
<td class="td-r-head-bottom"><b>&#8369;' . number_format($_ihead['amount'],2) . '</b></td>
</tr>
</table>
</htmlpageheader>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="off" />
mpdf-->
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse;" cellpadding="3">
<thead>
	<tr>
		<td width="15%" align=left>CODE</td>
		<td width="40%" align=left>DESCRIPTION</td>
		<td width="10%" align=center><b>UNIT</b></td>
		<td width="10%" align=right><b>QTY</b></td>
		<td width="10%" align=right><b>COST</b></td>
		<td width="15%" align=right><b>AMOUNT</b></td>
	</tr>
</thead>
<tbody>';
$i = 0;
while($row = $_idetails->fetch_array()) {
	
	$html = $html . '<tr>
	<td align=left>' .$row['item_code']. '</td>
	<td align=left>' . $row['description'] . '</td>
	<td align="center">' . $row['unit'] . '</td>
	<td align="right">' . number_format($row['qty'],2) . '</td>
	<td align="right">' .number_format($row['cost'],2) . '</td>
	<td align="right">' .number_format($row['amount'],2) . '</td>
	</tr>'; $i++; $amtGT+=$row['amount'];
}

$html = $html .  '<tr><td colspan=6>&nbsp;</td></tr>
				  <tr>
				  	<td colspan=5 align=right style="padding-right: 20px;"><b>TOTAL &raquo;</b></td>
					<td align=right style="border-top: 1px solid black;border-bottom: 1px solid black;"><b>'.number_format($amtGT,2).'</b></td>
				  </tr> 
</tbody>
</table>
<table width=100% cellpadding=5>
	<tr><td width=12%><b>Remarks :</b></td><td align=left>'.$_ihead['remarks'].'</td></tr>
</table>
<table width=100% cellpadding=5 style="border: 1px solid #000000;">
<tr>
	<td width=33% align=center><b>PREPARED BY:</b><br><br>________________________________<br></td>
	<td width=33% align=center><b>CHECKED BY:</b><br><br>_________________________________<br><font size=3>Printed Name over Signature</font></td>
	<td width=34% align=center><b>APPROVED BY:</b><br><br>_________________________________<br><font size=3>Printed Name over Signature</font></td>
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