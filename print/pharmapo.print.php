<?php
	//ini_set("display_errors","On");
	session_start();
	//ini_set("display_errors","On");
	require_once("../lib/mpdf6/mpdf.php");
	require_once("../handlers/_generics.php");
	
	$con = new _init();


/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");

	$_ihead = $con->getArray("select po_no, lpad(po_no,8,0) as rr, date_format(po_date,'%m/%d/%Y') as d8, if(date_needed!='0000-00-00',date_format(date_needed,'%m/%d/%Y'),'') as nd8, delivery_address, supplier, supplier_name, supplier_addr, requested_by, amount, remarks, mrs_no,c.description as terms FROM pharma_po_header a LEFT JOIN contact_info b ON a.supplier = b.file_id LEFT JOIN options_terms c ON a.terms = c.terms_id where po_no='$_REQUEST[po_no]' and branch = '$_SESSION[branchid]';");
	$_idetails = $con->dbquery("select item_code, description, sum(qty) as qty, unit, cost, ROUND(sum(qty*cost),2) amount from pharma_po_details where po_no = '$_REQUEST[po_no]' and branch = '$_SESSION[branchid]' group by po_no,item_code;");
	$bcode = STR_PAD($_REQUEST['user'],2,'0',STR_PAD_LEFT)."-".$_ihead['po_no']."-".date('Ymd');
	
	// list($dscnt) = $con->getArray("select sum(amount) from pharma_po_details where po_no = '$_REQUEST[po_no]' and branch = '$_SESSION[branchid]' and item_code = 'DSCT-001-001';");
	// list($dl_charge) = $con->getArray("select sum(amount) from pharma_po_details where po_no = '$_REQUEST[po_no]' and branch = '$_SESSION[branchid]' and item_code = 'DLCH-001-001';");
	// list($lbr_charge) = $con->getArray("select sum(amount) from pharma_po_details where po_no = '$_REQUEST[po_no]' and branch = '$_SESSION[branchid]' and item_code = 'LABR-001-001';");
	// list($frgt_charge) = $con->getArray("select sum(amount) from pharma_po_details where po_no = '$_REQUEST[po_no]' and branch = '$_SESSION[branchid]' and item_code = 'FRGHT-001-001';");

	list($nos, $stin) = $con->getArray("select tel_no, tin_no from contact_info where file_id = '$_ihead[supplier]';");
	
	if($_ihead['delivery_address'] != "") {
		$daddr = "<b>Delivery Information: </b><br/>c/o ".utf8_decode($_ihead['requested_by'])."<br/>".$_ihead['delivery_address'];
	} else {
		$daddr = "<b>Delivery Information: </b><br/>";
	}
	
	$projname = $con->identCostCenter($_ihead['proj']);
	list($cperson) = $con->getArray("select cperson from contact_info where file_id = '$_ihead[supplier]';");

	$approveDiv = '
	<table width=100% cellpadding=5 style="border: 1px solid #000000;">
		<tr>
			<td width=50% align=center><b>PREPARED BY:</b><br><br><br/>_________________________________<br/><font size=5>Purchaser</font></td>
			
			<td width=50% align=center><b>NOTED BY:</b><br><br><br>_________________________________<br><font size=5>Dr. Jeremy Nielo</font></td>
			<td width=50% align=center><b>APPROVED BY:</b><br><br><br>_________________________________<br><font size=5>ECM/DAL/CDI</font></td>
		</tr>
	</table>';
	
	
	
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','letter','','',15,15,100,75,10,10);
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
<tr><td align=center><img src="../images/doc-header.jpg" /></td></tr>
<tr>
	<td width="100%" align=center>
		<span style="font-weight: bold; font-size: 16pt; color: #000000; padding-top: 10px;">PURCHASE ORDER (PHARMACY)</span>
	</td>
</tr>
</table>
<br/><br/>
<table width="100%" cellspacing=0 cellpadding=0 style = "font-size:15px;" >
<tr>
<td class="billto" width=55% rowspan="4">
	<b>SUPPLIER :</b><br /><br /><span style="font-weight: bold; font-size: 16pt;"><b>'.$_ihead['supplier_name'].'</b></span><br /><i>'.$_ihead['supplier_addr'].'<br/><b>Contact Person: </b>'.$cperson.'<br/><b>Contact Nos: </b>'.$nos.'<br/><b>T-I-N #: '.$stin.'</b></i><br/><br><i>'.$daddr.'</i>
</td>
<td class="td-l-head"><b>Purchase Order No</b></td>
<td class="td-r-head"><b>' . $_ihead['rr'] . '</b></td>
</tr>
<tr>
<td class="td-l-head"><b>Material Request No.</b></td>
<td class="td-r-head"><b>' . $_ihead['mrs_no'] . '</b></td>
</tr>
<tr>
<td class="td-l-head"><b>Purchase Order Date</b></td>
<td class="td-r-head"><b>' . $_ihead['d8'] . '</b></td>
</tr>
<tr>
<td class="td-l-head-bottom"><b>Terms</b></td>
<td class="td-r-head-bottom"><b><span style="font-size:10pt;">'.$_ihead['terms'].'</span></b></td>
</tr>
</table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100% cellpadding=5>
	<tr><td width=12%><b>Remarks :</b></td><td align=left>'.$_ihead['remarks'].'</td></tr>
</table>
'.$approveDiv.'
<table width=100%>
	<tr><td height=20></td><td rowspan=3 align=right><br/><barcode code="'.substr($bcode,0,10).'" type="C128A"></td></tr>
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

<table class="items" width="100%" style="font-size: 11pt; border-collapse: collapse;" cellpadding="5">
<thead>
<tr>
<td width="15%" align=center><b>CODE</b></td>
<td width="35%" align=center><b>PARTICULARS</b></td>
<td width="10%" align=center><b>QTY</b></td>
<td width="10%" align=center><b>UNIT</b></td>
<td width="15%" align=right><b>UNIT COST</b></td>
<td width="15%" align=right><b>AMOUNT</b></td>
</tr>
</thead>
<tbody>';
$style = 'style = "font-size:10pt;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;"';
	$i = 0;
	while($row = $_idetails->fetch_array(MYSQLI_ASSOC)) {
		$html = $html . '<tr>
		<td align="center" '.$style.'>' . $row['item_code'] . '</td>
		<td align=left '.$style.'>' . $row['description'] . '</td>
		<td align="right" '.$style.'>' . number_format($row['qty'],2) . '</td>
		<td align="center" '.$style.'>' . $row['unit'] . '</td>
		<td align="right" '.$style.'>' . number_format($row['cost'],2) . '</td>
		<td align="right" '.$style.'>' . number_format($row['amount'],2) . '</td>
		</tr>'; $i++;
	}
	$html = $html . "<tr><td colspan=6 align=center ".$style."><b>*** NOTHING FOLLOWS ***</b></td></tr>";$i++;
	
$html = $html .  '
</tbody>
</table>

</body>
</html>
';
$html = utf8_encode($html);
$mpdf->WriteHTML($html);
$nfooter = '
<table width=100% cellpadding=5 border=0 style = "font-size: 14pt;">';

if($frgt_charge>0){ $nfooter .= '<tr><td width=75% align=right style = "font-size: 10pt;"><b>Freight Charge  Charge :</b></td><td align=right style = "font-size: 10pt;"><b>'. number_format($frgt_charge,2) .' PHP</b></td></tr>'; }	
if($lbr_charge>0){ $nfooter .= '<tr><td width=75% align=right style = "font-size: 10pt;"><b>Labor  Charge :</b></td><td align=right style = "font-size: 10pt;"><b>'. number_format($lbr_charge,2) .' PHP</b></td></tr>'; }	
if($dl_charge>0){ $nfooter .= '<tr><td width=75% align=right style = "font-size: 10pt;"><b>Delivery Charge :</b></td><td align=right style = "font-size: 10pt;"><b>'. number_format($dl_charge,2) .' PHP</b></td></tr>'; }	
if($dscnt>0){ $nfooter .= '<tr><td width=75% align=right style = "font-size: 10pt;"><b>Discount :</b></td><td align=right style = "font-size: 10pt;"><b>'. number_format($dscnt,2).' PHP</b></td></tr>';}	
$nfooter.='<tr><td width=70% align=right><b>TOTAL AMOUNT OF PURCHASE :</b></td><td align=right><b>'. number_format($_ihead['amount'],2) .' PHP</b></td></tr>
</table>
<table width=100% cellpadding=5>
	<tr><td width=12%><b>Remarks :</b></td><td align=left>'.$_ihead['remarks'].'</td></tr>
</table>
'.$approveDiv.'
<table width=100%>
	<tr><td height=20></td><td rowspan=3 align=right><br/><barcode code="'.substr($bcode,0,10).'" type="C128A"></td></tr>
	<tr><td>Date & Time Sent: ______________________________</td></tr>
	<tr><td>P.O Received By: &nbsp;&nbsp;______________________________</td></tr>
</table>
<table width=100%>
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Print Date: '.date('m/d/Y h:i:s a',strtotime('7 hours')).'</td></tr>
</table>';
$mpdf->setHTMLFooter($nfooter);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>