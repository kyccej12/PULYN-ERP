  <?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");


function getFullDesc($icode) {
	list($desc,$fdesc) = getArray("select description, full_description from products_master where item_code = '$icode';");
	if($fdesc != '') { return $fdesc; } else { return $desc; }
}

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
	$bit = getArray("select lpad(branch_code,2,0), branch_name, tin_no from options_branches where branch_code = '$_SESSION[branchid]';");
	$_ihead = getArray("select doc_no, doc_no as rr, date_format(invoice_date,'%m/%d/%Y') as d8, customer, customer_name, customer_addr, ROUND(amount+commission,2) as amount, b.description as terms, remarks, sales_rep from invoice_header a left join options_terms b on a.terms = b.terms_id where doc_no ='$_REQUEST[docno]' and branch = '$_SESSION[branchid]';");
	$_idetails = dbquery("select if(so_no='','--',so_no) as so_no, if(so_date='0000-00-00','--',date_format(so_date,'%m/%d/%Y')) as so_date, item_code, description, custom_description, qty, unit, cost, discount, (amount+(comm*qty)) as amount, comm from invoice_details where doc_no = '$_REQUEST[docno]' and branch = '$_SESSION[branchid]';");
	$bcode = STR_PAD($_REQUEST['user'],2,'0',STR_PAD_LEFT)."-SI".$_ihead['doc_no']."-".date('Ymd');
	list($nos, $stin, $isVat) = getArray("select tel_no, tin_no, vatable from contact_info where file_id = '$_ihead[customer]';");
	list($srep) = getArray("select sales_rep from options_salesrep where record_id = '$_ihead[sales_rep]';");
	//list($mySO) = getArray("select so_no from invoice_details where doc_no = '$_REQUEST[docno]' and branch = '$_SESSION[branchid]' limit 1;");
	///list($soCreator) = getArray("SELECT created_by FROM so_header WHERE so_no = '$mySO' AND branch = '$_SESSION[branchid]';");

	/* AUDIT TRAIL PURPOSES */
	dbquery("insert into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SI','SALES INVOICE # $_POST[docno] PRINTED BY USER','$_POST[docno]');");
			
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','FOLIO','','',8,8,70,8,8,8);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(60);
if($_GET['rePrint'] == "Y") {
	$mpdf->SetWatermarkText('Reprinted Copy');
	$mpdf->showWatermarkText = true;
}

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
<td style="color:#000000;" width=80><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
<td style="color:#000000; padding-top: 15px;">
	<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$bit['tin_no'].'</span>
</td>
<td width="40%" align=right>
	<span style="font-weight: bold; font-size: 13pt; color: #000000;">PACKING LIST&nbsp;&nbsp;</span><br />
	<barcode size=0.8 code="'.substr($bcode,0,10).'" type="C128A">
</td>
</tr>
</table>
<table width="100%" cellspacing=0 cellpadding=0>
<tr>
<td class="billto" width=60% rowspan="4">
<b>CUSTOMER:</b><br /><br /><b>('.$_ihead['customer'].') '.$_ihead['customer_name'].'</b><br /><i>'.$_ihead['customer_addr'].'<br/><b>Contact Nos: </b>'.$nos.'<br/><b>T-I-N #: </b>'.$stin.'</i></td>
<td class="td-l-top"><b>Page</b></td>
<td class="td-r-top"><b>{PAGENO} of {nb}</b></td>
</tr>
<tr>
<td class="td-l-head"><b>Doc No</b></td>
<td class="td-r-head"><b>' . str_pad($_SESSION['branchid'],2,0,STR_PAD_LEFT) . '-' . $_REQUEST['docno'] . 'PL</b></td>
</tr>
<tr>
<td class="td-l-head"><b>Doc Date</b></td>
<td class="td-r-head"><b>' . $_ihead['d8'] . '</b></td>
</tr>
<tr>
<td class="td-l-head-bottom"><b>Amount</b></td>
<td class="td-r-head-bottom"><b>&#8369;' . number_format($_ihead['amount'],2) . '</b></td>
</tr>
</table>
</htmlpageheader>
<htmlpagefooter name="myfooter">
<table width=100%>
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Run Date: '.date('m/d/Y h:i:s a').'</td></tr>
</table>
</htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse;" cellpadding="3">
<thead>
<tr>
<td align=left><b>PARTICULARS</b></td>
<td width="10%"><b>UoM</b></td>
<td width="20%" align=right><b>QTY</b></td>
</tr>
</thead>
<tbody>';
	$i = 0;
	while($row = mysql_fetch_array($_idetails)) {
		if($old_po != $row['so_no']) { $so_no = $row['so_no']; $so_date = $row['so_date']; } else { $so_no = ""; $so_date = ""; }
		if($row['custom_description'] != '') { $iDesc = $row['custom_description']; } else { $iDesc = strtoupper(getFullDesc($row['item_code'])); }
		$html = $html . '<tr>
		<td align=left>' . $iDesc . '</td>
		<td align="center">' . identUnit($row['unit']) . '</td>
		<td align="right">' . number_format($row['qty'],2) . '</td>
		</tr>'; $i++; $old_po = $row['so_no']; 
	}

	for($i; $i < 5; $i++) { $html = $html . "<tr><td colspan=3></td></tr>"; }

$html = $html . '
</table>
<table><tr><td height=20></td></tr></table>
<table width=100% cellpadding=5>
	<tr>
		<td width=25%><b>Transaction Remarks:</b></td>
		<td align=left>'.$_ihead['remarks'].'</td>
	</tr>
</table>
<table width=100% cellpadding=5 style="border: 1px solid #000000;">
<tr>
	<td width=25% align=center><b>SALES REP:</b><br><br><u>&nbsp;&nbsp;'.$srep.'&nbsp;&nbsp;</u><br></td>
	<td width=25% align=center><b>CHECKED BY:</b><br><br>___________________________<br><font size=3>Dispatching Officer</font></td>
	<td width=25% align=center><b>RECEIVED BY:</b><br><br>___________________________<br><font size=3>Signature Over Printed Name</font></td>
	<td width=25% align=center><b>DATE RECEIVED:</b><br><br>_____________________</td>
</tr>
</table>
<table width=100%>
	<tr><td height=20></td></tr>
	<tr><td style="text-align: justify;"><p><b>NOTICE: </b>By signing the "RECEIVED BY" portion of this Order Slip, you hereby acknowledged that you have received the goods in <b>GOOD CONDITION</b>. Any <b>DEFECTIVE GOODS</b> discovered right after receipt, in the presence of the driver and loader, immediately be communicated/reported to the office. Any <b>DEFECTIVE GOODS</b> reported after the driver and loader had already left the establishment should no longer be entertained. Return/Exchange of items should be within seven (7) days from the date of purchase.</p></td></tr>
</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>