<?php
	session_start();
	//ini_set("display_errors","On");
	require_once("../lib/mpdf6/mpdf.php");
	require_once("../handlers/_generics.php");
	
	$con = new _init();

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
	$_ihead = $con->getArray("select  date_format(date_needed,'%m/%d/%Y') as d82,rfp_no, lpad(rfp_no,5,0) as rr, date_format(rfp_date,'%m/%d/%Y') as d8, supplier, ucase(supplier_name) supplier_name, supplier_addr, amount, remarks, b.tin_no, b.tel_no from rfp_header a left join contact_info b on a.supplier=b.file_id where rfp_no='$_REQUEST[rfp_no]';");
	$_idetails = $con->dbquery("select *, date_format(apv_date,'%m/%d/%Y') as ad8, date_format(due_date,'%m/%d/%Y') as dd8, lpad(apv_no,6,0) as ano,if(apv_remarks='0','',apv_remarks) as my_remarks from rfp_details where rfp_no = '$_REQUEST[rfp_no]';");
	list($nos, $stin) = $con->getArray("select tel_no, tin_no from contact_info where file_id = '$_ihead[supplier]';");
	
	$bcode = STR_PAD($_REQUEST['user'],2,'0',STR_PAD_LEFT)."-".$_ihead['rfp_no']."-".date('Ymd');


/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','folio-h','','',10,10,70,30,10,10);
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
body {
	font-family: arial;
	font-size: 10pt;
 }
td { vertical-align: top; }

table thead td { 
	border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
	/*  background-color: #EEEEEE; */
    text-align: center;
}

.subdetail { 
	border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
	/*  background-color: #EEEEEE; */
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
    border-left: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;  /*  background-color: #EEEEEE; */
}
.items td.tdTotals-r {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000; /*  background-color: #EEEEEE; */
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
		/*  background-color: #EEEEEE; */ padding: 5px;
		text-align: left;
		border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000;
		border-top: 0.1mm solid #000000;
	}
.td-r-top { 
	text-align: right; padding: 5px;
    border-right: 0.1mm solid #000000;
	border-top: 0.1mm solid #000000;
}

.td-l-head {
	text-align: left; font-weight: bold; padding: 5px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; /*  background-color: #EEEEEE; */
}

.td-r-head {
	text-align: right; padding: 5px;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000;
}
.td-l-head-bottom {
	text-align: left; padding: 5px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; /*  background-color: #EEEEEE; */ border-bottom: 0.1mm solid #000000;
}

.td-r-head-bottom {
	text-align: right; padding: 5px;
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
<table width="100%">
	<tr>
		<td style="color:#000000; padding-top: 15px;">
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$bit['tin_no'].'</span>
		</td>
		<td width="40%" align=right>
			<span style="font-weight: bold; font-size: 13pt; color: #000000;">REQUEST FOR PAYMENT&nbsp;&nbsp;</span><br />
			<barcode size=0.8 code="'.substr($bcode,0,10).'" type="C128A">
		</td>
	</tr>
</table>
<table width="100%" cellspacing=0 cellpadding=0>
	<tr>
	<td class="billto" width=60% rowspan="7" ><b>PAYEE :</b><br /><span style="font-weight: bold; font-size: 16pt;"><br /><b>'.$_ihead['supplier_name'].'</b></span><br /><i>'.$_ihead['supplier_addr'].'<br><b>T-I-N: </b>'.$stin.'<br/><b>Contact #: </b>'.$nos.'</i></td>
	</tr>
	<tr>
		<td class="td-l-head"><b>RFP NO.</b></td>
		<td class="td-r-head">' . $_ihead['rr'] . '</td>
	</tr>
	<tr>
		<td class="td-l-head"><b>RFP DATE</b></td>
		<td class="td-r-head">' . $_ihead['d8'] . '</td>
	</tr>
	<tr>
		<td class="td-l-head"><b>DATE NEEDED</b></td>
		<td class="td-r-head">' . $_ihead['d82'] . '</td>
	</tr>
	<tr>
		<td class="td-l-head-bottom"><b>AMOUNT</b></td>
		<td class="td-r-head-bottom"><b>' . number_format($_ihead['amount'],2) . '</b></td>
	</tr>
</table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<div style="border:1px solid black;">
<table width="100%">
	<tr>	
		<td width=26% align=center>REQUESTED BY</td>
		<td width=10%> </td>
		<td width=26% align=center>CHECKED & VERIFIED BY</td>
		<td width=10%> </td>
		<td width=26% align=center>APPROVED BY</td>
	 </tr>
	 <tr>	
		<td width=26% align=center style="border-bottom:1px solid black">'.$con->getUname($_REQUEST['user']).'</td>
		<td width=10%> </td>
		<td width=26% align=center style="border-bottom:1px solid black">&nbsp;</td>
		<td width=10%> </td>
		<td width=26% align=center style="border-bottom:1px solid black">&nbsp;</td>
	 </tr>
	 <tr>	
		<td width=26% align=center></td>
		<td width=10%> </td>
		<td width=26% align=center>TRM/DTD</td>
		<td width=10%> </td>
		<td width=26% align=center>ECM</td>
	 </tr>
</table>
</div>
<table width=100%>
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Print Date: '.date('m/d/Y h:i:s a',strtotime('8 hours')).'</td></tr>
	</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table width="100%" style="font-size: 9pt; border-collapse: collapse;" cellpadding="8">
<thead>
<tr>
	<td align=center width="10%"  class="subdetail">APV #</td>
	<td align=center width="15%" class="subdetail">APV DATE</td>
	<td align=left width="45%" class="subdetail">DETAILS</td>
	<td align=center width="10%" class="subdetail">DUE DATE</td>
	<td align=right width="20%" class="subdetail">AMOUNT</td>
</tr>
</thead>
<tbody>';
	$i = 0;

	while($x = $_idetails->fetch_array()) {
		$html = $html . '<tr>
			<td align=center width="10%">'.$x['ano'].'</td>
			<td align=center width="15%">'.$x['ad8'].'</td>
			<td align=left width="45%">'.$x['my_remarks'].'</td>
			<td align=center width="10%">'.$x['dd8'].'</td>
			<td align=right width="20%" style="padding-right: 12px;">'.number_format($x['amount'],2).'</td>
		</tr>'; $amtGT+=$x['amount']; $vatGT+=$x['vat']; $ewtGT+=$x['ewt']; $netGT+=$x['net_payable']; $i++;
	}
	$html = $html . '
	<tr>
		<td align=left colspan=4 style=" border-top: 0.1mm solid #000000;"></td>
		<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($netGT,2) . '</b></td>
		</tr>
	  </tbody>
	</table>';

$html = $html .  '<table width=100%><tr><td align=left width=10%><b>MEMO :</b><td align=left>'.$_ihead['remarks'].'</td></tr></table>

</body>
</html>
';
$html = utf8_encode($html);
$mpdf->WriteHTML($html);
$mpdf->Output(); 
exit;
?>