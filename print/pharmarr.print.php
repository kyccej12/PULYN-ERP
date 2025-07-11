<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");
	$p = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $p->getArray("select * from companies where company_id = '$_SESSION[company]';");
	$_ihead = $p->getArray("select trace_no, lpad(rr_no,6,0) as rr, date_format(rr_date,'%m/%d/%Y') as d8, supplier, supplier_name, supplier_addr, invoice_no, date_format(invoice_date,'%m/%d/%Y') as id8, received_by, amount, remarks from pharma_rr_header where rr_no='$_REQUEST[rr_no]' and branch = '$_SESSION[branchid]';");
	$_idetails = $p->dbquery("select costcenter, if(po_no='','--',lpad(po_no,6,0)) as po_no, if(po_date='0000-00-00','--',date_format(po_date,'%m/%d/%y')) as po_date, item_code, description, qty, unit, cost, amount from pharma_rr_details where rr_no = '$_REQUEST[rr_no]' and branch = '$_SESSION[branchid]';");
	$bcode = $_ihead['trace_no'];
	list($nos, $stin) = $p->getArray("select tel_no, tin_no from contact_info where file_id = '$_ihead[supplier]';");
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','FOLIO-H','','',10,10,30,30,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->setAutoTopMargin='stretch';
$mpdf->setAutoBottomMargin='stretch';
$mpdf->use_kwt = true;

if($_REQUEST['rePrint'] == 'Y') {
	$mpdf->SetWatermarkText('Reprinted Copy');
	$mpdf->showWatermarkText = true;
}

$mpdf->SetDisplayMode(60);

$html = '
<html>
<head>
<style>
body {
	font-family: arial;
	font-size: 8pt;
 }
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
<table width="100%">
	<tr>
        <td width=75><img src="../images/logo-small.png" width=64 height=64 align=absmiddle></td>
		<td style="color:#000000; padding-top: 15px;">
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$bit['tin_no'].'</span>
		</td>
		<td width="60%" align=right>
			<span style="font-weight: bold; font-size: 13pt; color: #000000;">RECEIVING REPORT (PHARMACY)&nbsp;&nbsp;</span><br />
			<barcode size=0.8 code="'.substr($bcode,0,19).'" type="C128A">
		</td>
	</tr>
</table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100% cellpadding=5>
	<tr><td align=left width=15%><b>REMARKS :</b><td width=85% style="text-align: justify;">'.$_ihead['remarks'].'</td></tr>
</table>
<table width=100% cellpadding=5 style="border: 1px solid #000000;">
<tr>
	<td width=25% align=center><b>PREPARED BY:</b><br><br>'.$p->getUname($_SESSION['userid']).'<br></td>
	<td width=25% align=center><b>RECEIVED BY:</b><br><br>'.$_ihead['received_by'].'<br></td>
	<td width=25%  align=center><b>CHECKED & VERIFIED BY:</b><br><br>_________________________<br><font size=3>Signature over Printed Name</font></td>
</tr>
</table>
<table width=100%>
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Run Date: '.date('m/d/Y h:i:s a').'</td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table width="100%" cellspacing=0 cellpadding=0>
<tr>
<td class="billto" width=60% rowspan="5">
<b>RECEIVED FROM :</b><br /><br /><b>('.$_ihead['supplier'].') '.$_ihead['supplier_name'].'</b><br /><i>'.$_ihead['supplier_addr'].'<br/><b>Contact Nos: </b>'.$nos.'<br/><b>T-I-N #: </b>'.$stin.'</i></td>
<td class="td-l-top"><b>RR #</b></td>
<td class="td-r-top"><b>' . $_ihead['rr'] . '</b></td>
</tr>
<tr>
<td class="td-l-head"><b>Doc Date</b></td>
<td class="td-r-head"><b>' . $_ihead['d8'] . '</b></td>
</tr>
<tr>
<td class="td-l-head"><b>Invoice No.</b></td>
<td class="td-r-head"><b>' . $_ihead['invoice_no'] . '</b></td>
</tr>
<tr>
<td class="td-l-head"><b>Invoice Date</b></td>
<td class="td-r-head"><b>' . $_ihead['id8'] . '</b></td>
</tr>
<tr>
<td class="td-l-head-bottom"><b>Amount</b></td>
<td class="td-r-head-bottom"><b>&#8369;' . number_format($_ihead['amount'],2) . '</b></td>
</tr>
</table>
<table><tr><td height=15></td></tr></table>
<table class="items" width="100%" style="font-size: 8pt; border-collapse: collapse;" cellpadding="2">
<thead>
<tr>
<td width="20%" align=left><b>UNIT CODE</b></td>
<td width="10%" align=center><b>PO #</b></td>
<td width="10%" align=center><b>PO DATE</b></td>
<td width="25%" align=left><b>PARTICULARS</b></td>
<td width="10%" align=right><b>QTY</b></td>
<td width="5%"><b>UNIT</b></td>
<td width="10%" align=right><b>UNIT COST</b></td>
<td width="10%" align=right><b>AMOUNT</b></td>
</tr>
</thead>
<tbody>';
	$i = 0;
	while($row = $_idetails->fetch_array(MYSQLI_ASSOC)) {

		$html = $html . '<tr>
		<td align="left">' . $p->identCostCenter($row['costcenter']) . '</td>
		<td align="center" >' . $row['po_no'] . '</td>
		<td align="center">' . $row['po_date'] . '</td>
		<td align=left>(' . $row['item_code'] . ') ' . $row['description'] . '</td>
		<td align="right">' . number_format($row['qty'],2) . '</td>
		<td align="center">' . $row['unit'] . '</td>
		<td align="right">' . number_format($row['cost'],2) . '</td>
		<td align="right">' . number_format($row['amount'],2) . '</td>
		</tr>'; $i++;
	}
$html = $html .  '
</tbody>
</table>
</body>
</html>
';

$html = html_entity_decode($html);
$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>