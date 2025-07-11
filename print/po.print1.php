<?php
	session_start();
	ini_set("display_errors","On");
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");
	
	$p = new _init;


/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $p->getArray("select * from companies where company_id = '$_SESSION[company]';");
	$_ihead = $p->getArray("select po_no, po_no as rr, date_format(po_date,'%m/%d/%Y') as d8, if(date_needed!='0000-00-00',date_format(date_needed,'%m/%d/%Y'),'') as nd8, delivery_address, supplier, supplier_name, supplier_addr, requested_by, amount, if(discount>0,'DISC. per UoM','') as disc_label, if(discount>0,'15%','2%') as twidth, net, remarks, proj from po_header where po_no='$_REQUEST[po_no]' and branch = '$_SESSION[branchid]';");
	$_idetails = $p->dbquery("SELECT a.item_code, if(a.custom_description!='',concat('(',a.item_code,') ',a.custom_description),if(b.indcode!='',concat('(',b.indcode,') ',a.description),concat('(',a.item_code,') ',a.description))) as xdesc, a.description, qty, a.unit, a.cost, ROUND(discount*qty) AS discount, a.amount, if(a.discount_percent!=0,concat(round(a.discount_percent),'%'),'') as pct FROM po_details a INNER JOIN products_master b ON a.item_code = b.item_code WHERE po_no = '$_REQUEST[po_no]' AND branch = '$_SESSION[branchid]';");
	
	//list($icount) = $p->getArray("select count(*) from po_details where po_no = '$_REQUEST[po_no]' and branch = '$_SESSION[branchid]';");
	//if($icount > 7) { $paper = "letter"; } else { $paper = "HALF-FOLIO"; }
	
	$bcode = STR_PAD($_REQUEST['user'],2,'0',STR_PAD_LEFT)."-PO".$_ihead['po_no']."-".date('Ymd');
	list($nos, $stin) = $p->getArray("select tel_no, tin_no from contact_info where file_id = '$_ihead[supplier]';");
	
	$projName = $p->identCostCenter($_ihead['proj']);
	
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','letter','','',15,15,75,45,9,3);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(40);

if($_REQUEST['rePrint'] == 'Y') {
	$mpdf->SetWatermarkText('Reprinted Copy');
	$mpdf->showWatermarkText = true;
}

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
	<table width="100%" cellpadding=0 cellspaing=0>
		<tr>
			<td style="color:#000000;"><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
			<td width="40%" align=right>
				<span style="font-weight: bold; font-size: 13pt; color: #000000;">PURCHASE ORDER&nbsp;&nbsp;</span><br />
				<barcode size=0.8 code="'.substr($bcode,0,10).'" type="C128A">
			</td>
		</tr>
	</table>
	<table width="100%" cellspacing=0 cellpadding=0>
	<tr>
	<td class="billto" width=60% rowspan="6">
	<b>SUPPLIER :</b><br /><br /><span style="font-weight: bold; font-size: 16pt;">'.$_ihead['supplier_name'].'</span><br /><i>'.$_ihead['supplier_addr'].'<br/><b>Contact Nos: </b>'.$nos.'<br/><b>T-I-N #: '.$stin.'</b></i><br/><br><i>'.$daddr.'</i></td>
	<td class="td-l-top"><b>Page</b></td>
	<td class="td-r-top"><b>{PAGENO} of {nb}</b></td>
	</tr>
	<tr>
	<td class="td-l-head"><b>Doc No</b></td>
	<td class="td-r-head"><b>' . $_REQUEST['po_no'] . '</b></td>
	</tr>
	<tr>
	<td class="td-l-head"><b>Doc Date</b></td>
	<td class="td-r-head"><b>' . $_ihead['d8'] . '</b></td>
	</tr>
		<tr>
	<td class="td-l-head"><b>Proj/Cost Center</b></td>
	<td class="td-r-head"><b>' . $projName . '</b></td>
	</tr>
	<tr>
	<td class="td-l-head"><b>Expected Delivery Date</b></td>
	<td class="td-r-head"><b>' . $_ihead['nd8'] . '</b></td>
	</tr>
	<tr>
	<td class="td-l-head-bottom"><b>Amount</b></td>
	<td class="td-r-head-bottom"><b>&#8369;' . number_format($_ihead['net'],2) . '</b></td>
	</tr>
	</table>
	</htmlpageheader>

<htmlpagefooter name="myfooter">
	<table width=100% cellpadding=5 style="border-top: 1px solid black;">
		<tr><td width=12%><b>Remarks :</b></td><td align=left>'.$_ihead['remarks'].'</td></tr>
	</table>
	<table width=100% cellpadding=5 style="border: 1px solid #000000;">
	<tr>
		<td width=33% align=center><b>PREPARED BY:</b><br><br>'. $p->getUname($_SESSION['userid']) . '<br></td>
		<td width=33% align=center><b>CHECKED BY:</b><br><br>_________________________________<br><font size=3>Signature over Printed Name</font></td>
		<td width=33% align=center><b>APPROVED BY:</b><br><br>_________________________________<br><font size=3>Signature over Printed Name</font></td>
	</tr>
	</table>
	<table>
		<tr><td height=20></td></tr>
		<tr><td>Date & Time Sent: ______________________________</td></tr>
		<tr><td>P.O Received By: &nbsp;&nbsp;______________________________</td></tr>
	</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />

<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->

<table class="items" width="100%" style="font-size: 8pt; border-collapse: collapse;" cellpadding="3">
<thead>
<tr>
<td align=left><b>(ITEM CODE) PARTICULARS</b></td>
<td width="8%" align=right><b>QTY</b></td>
<td width="8%"><b>UoM</b></td>
<td width="10%" align=right><b>UNIT COST</b></td>
<td width="'.$_ihead['twidth'].'" align=center><b>'.$_ihead['disc_label'].'&nbsp;</b></td>
<td width="12%" align=right><b>AMOUNT</b></td>
</tr>
</thead>
<tbody>';
	$i = 0;
	while($row = $_idetails->fetch_assoc()) {
		$html = $html . '<tr>
		<td align=left>'. $row['xdesc'] . '</td>
		<td align="right">' . number_format($row['qty'],2) . '</td>
		<td align="center">' . $row['unit'] . '</td>
		<td align="right">' . number_format($row['cost'],2) . '</td>
		<td align="center">' . $row['pct'] . '</td>
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
?>