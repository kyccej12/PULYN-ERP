<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");


/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");


	
	
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','A4','','',15,15,100,80,10,15);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->setAutoTopMargin='stretch';
$mpdf->setAutoBottomMargin='stretch';
$mpdf->use_kwt = true;
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(40);

if($_REQUEST['reprint'] == 'Y') {
	$mpdf->SetWatermarkText('REPRINTED COPY');
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
	/* background-color: #EEEEEE; */
    text-align: center;
}

.td-l { border-left: 0.1mm solid #000000; }
.td-r { border-right: 0.1mm solid #000000; }
.empty { border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; }

.items td.blanktotal {
    /* background-color: #FFFFFF; */
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
    border-left: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000; /* background-color: #EEEEEE; */
}
.items td.tdTotals-r {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000; /* background-color: #EEEEEE; */
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
		/* background-color: #EEEEEE; */ padding: 5px;
		text-align: left; font-weight: bold;
		border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000;
		border-top: 0.1mm solid #000000;
	}
.td-r-top { 
	text-align: right; font-weight: bold; padding: 5px;
    border-right: 0.1mm solid #000000;
	border-top: 0.1mm solid #000000;
}

.td-l-head {
	text-align: left; font-weight: bold; padding-left: 5px;vertical-align:middle;padding-right: 5px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; /* background-color: #EEEEEE; */
}

.td-r-head {
	text-align: right; font-weight: bold; padding-left: 5px;vertical-align:middle;padding-right: 5px;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000;
}
.td-l-head-bottom {
	text-align: left; font-weight: bold; padding-left: 5px;vertical-align:middle;padding-right: 5px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; /* background-color: #EEEEEE; */ border-bottom: 0.1mm solid #000000;
}

.td-r-head-bottom {
	text-align: right; font-weight: bold; padding-left: 5px;padding-right: 5px;vertical-align:middle;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}

.billto {
	font-size: 12px; vertical-align: top; padding: 5px;
}
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%" cellpadding=0 cellspaing=0>
	<tr>
		<td align=center><img src="../images/doc-header.jpg" /></td>
	</tr>
	<tr>
		<td width="100%" align=center><span style="font-weight: bold; font-size: 16pt; color: #000000;">STATEMENT OF ACCOUNT</span></td>
	</tr>
</table>

<table width=100% cellspacing=0 cellpadding=0>
	<tr>
		<td width=50%>
		
		</td>
		<td>

		</td>
	</tr>
</table>


<table width="100%" cellspacing=0 cellpadding=0 style = "font-size:15px;" >
	<tr>
		<td class="billto" width=55% rowspan="8">
	<b>SUPPLIER :</b><br /><br /><span style="font-weight: bold; font-size: 16pt;"><b>'.$_ihead['supplier_name'].'</b></span><br /><i>'.$_ihead['supplier_addr'].'<br/><b>Contact Person: </b>'.$_ihead['requested_by'].'<br/><b>Contact Nos: </b>'.$nos.'<br/><b>T-I-N #: '.$stin.'</b></i><br/><br><i>'.$daddr.'</i>
	</td>
<td class="td-l-head"><b>Job Order No</b></td>
<td class="td-r-head"><b>' . $_ihead['rr'] . '</b></td>
</tr>
<tr>
<td class="td-l-head"><b>Project Name</b></td>
<td class="td-r-head"><b>' .  $projname . '</b></td>
</tr>
<tr>
<td class="td-l-head"><b>Job Order Date</b></td>
<td class="td-r-head"><b>' . $_ihead['d8'] . '</b></td>
</tr>
<tr>
<td class="td-l-head"><b>W.R. No.</b></td>
<td class="td-r-head"><b>' . $_ihead['mr_no'] . '</b></td>
</tr>
<tr>
<td class="td-l-head-bottom"><b>Terms</b></td>
<td class="td-r-head-bottom"><b><span style="font-size:10pt;">'.$_ihead['terms'].'</span></b></td>
</tr>

<tr>
<td ></td>
<td ></td>
</tr>
<tr>
<td ></td>
<td ></td>
</tr>
<tr>
<td ></td>
<td ></td>
</tr>
<tr>
<td ></td>
<td ></td>
</tr>
</table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
'.$approveDiv.'
<table width=100%>
	<tr><td height=20></td><td rowspan=3 align=right><br/><barcode code="'.$bcode.'" type="C128A"></td></tr>
	<tr><td>Date & Time Sent: ______________________________</td></tr>
	<tr><td>P.O Received By: &nbsp;&nbsp;______________________________</td></tr>
</table>
<table width=100%>
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Print Date: '.date('m/d/Y h:i:s a',strtotime('7 hours')).'</td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table width=100% cellpadding=5>
	<tr><td width=20%><b>Scope of Work :</b></td><td align=left><pre>'.$_ihead['remarks'].'</pre></td></tr>
</table>
<br>
<table class="items" width="100%" style="font-size: 11pt; border-collapse: collapse;" cellpadding="5">
<thead>
<tr>
<td width="10%" align=right><b>QTY</b></td>
<td width="10%" align=center><b>UNIT</b></td>
<td width="50%" align=center><b>PARTICULARS</b></td>
<td width="15%" align=right><b>UNIT COST</b></td>
<td width="15%" align=right><b>AMOUNT</b></td>
</tr>
</thead>
<tbody>';
$style = 'style = "font-size:11pt;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;"';
	$i = 0;
	while($row = mysql_fetch_array($_idetails)) {
		$html = $html . '<tr>
		<td align="right" '.$style.'>' . number_format($row['qty'],2) . '</td>
		<td align="center" '.$style.'>' . $row['unit'] . '</td>
		<td align=left '.$style.'> ' . $row['description'] . '</td>
		<td align="right" '.$style.'>' . number_format($row['cost'],2) . '</td>
		<td align="right" '.$style.'>' . number_format($row['amount'],2) . '</td>
		</tr>'; $i++;
		if($row['item_code']!='DSCT-001-001'){
			$totalAmount += $row['amount'];
		}
		
	}
	$html = $html . "<tr><td colspan=5 align=center ".$style."><b>*** NOTHING FOLLOWS ***</b></td></tr>";$i++;
	
$html = $html .  '
</tbody>
</table>

</body>
</html>
';
$html = utf8_encode($html);
$mpdf->WriteHTML($html);
$mpdf->setHTMLFooter('<table width=100% cellpadding=5 border=0 style = "font-size: 14pt;">
	<tr><td width=75% align=right><b>TOTAL AMOUNT :</b></td><td align=right><b>'. number_format($totalAmount-$dscnt,2) .' PHP</b></td></tr>
</table>
'.$approveDiv.'
<table width=100%>
	<tr><td height=20></td><td rowspan=3 align=right><br/><barcode code="'.substr($bcode,0,10).'" type="C128A"></td></tr>
	<tr><td>Date & Time Sent: ______________________________</td></tr>
	<tr><td>P.O Received By: &nbsp;&nbsp;______________________________</td></tr>
</table>
<table width=100%>
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Print Date: '.date('m/d/Y h:i:s a',strtotime('8 hours')).'</td></tr>
</table>');
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>