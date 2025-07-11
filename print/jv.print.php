<?php
	session_start();
	//ini_set("display_errors","on");
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;
	

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
	// $bit = $con->getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
	$_ihead = $con->getArray("select lpad(j_no,6,0) as jno, date_format(j_date,'%m/%d/%Y') as jd8, explanation from journal_header where j_no = '$_REQUEST[j_no]' and branch = '$_SESSION[branchid]';");
	$_idetails = $con->dbquery("SELECT ref_no, date_format(ref_date,'%m/%d/%y') as ref_date, ref_type, CONCAT('(',`client`,') ',b.tradename) AS cust, acct, acct_desc, cost_center,debit,credit FROM journal_details a LEFT JOIN contact_info b ON a.client=b.file_id WHERE j_no = '$_REQUEST[j_no]' and a.branch='$_SESSION[branchid]' order by debit, acct_desc;");
	$bcode = STR_PAD($_REQUEST['user'],2,'0',STR_PAD_LEFT)."-JV".$_ihead['po_no']."-".date('Ymd');
	
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','letter','','',15,15,28,15,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Business Solutions");

if($_REQUEST['rePrint'] == 'Y') {
	$mpdf->SetWatermarkText('Reprinted Copy');
	$mpdf->showWatermarkText = true;
}

$mpdf->SetDisplayMode(60);

$html = '
<html>
<head>
<style>
body {font-family: sans-serif; font-size: 8pt; }
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
<td style="color:#000000;"><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
<td style="color:#000000; padding-top: 15px;">
	<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
</td>
<td width="40%" align=right>
	<span style="font-weight: bold; font-size: 13pt; color: #000000;">JOURNAL VOUCHER&nbsp;&nbsp;</span><br />
	<barcode size=0.8 code="'.substr($bcode,0,10).'" type="C128A">
</td>
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
<table width="100%" cellspacing=0 cellpadding=0>
<tr>
<td class="billto" width=60% rowspan="3"></td>
<td class="td-l-top"><b>Doc Branch</b></td>
<td class="td-r-top"><b>' . $co['company_id'] . '</b></td>
</tr>
<tr>
<td class="td-l-head"><b>Doc No</b></td>
<td class="td-r-head">' . $_ihead['jno'] . '</td>
</tr>
<tr>
<td class="td-l-head-bottom"><b>Doc Date</b></td>
<td class="td-r-head-bottom"><b>' .$_ihead['jd8'] . '</b></td>
</tr>
</table>
<table width=100% cellspacing=0 cellpadding=0>
	<tr><td height=20></td></tr>
	<tr>
		<td width=12%><b>Explanation :</b></td>
		<td><i>' . $_ihead['explanation'] . '</i></td>
	</tr>
</table>
<table><tr><td height=20></td></tr></table>
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse;" cellpadding="3">
<thead>
<tr>
<td width="10%"><b>REF #</b></td>
<td width="10%"><b>DATE</b></td>
<td width="8%"><b>TYPE</b></td>
<td width="8%"><b>COST CENTER</b></td>
<td width="27%" align=left><b>GL ACCOUNT</b></td>
<td width="11%" align=right><b>DEBIT</b></td>
<td width="11%" align=right><b>CREDIT</b></td>
<td width="23%" align=left style="padding-right: 10px;"><b>CLIENT</b></td>
</tr>
</thead>
<tbody>';
	$i = 0;
	while($row = $_idetails->fetch_array()) {
		
		$html = $html . '<tr>
		<td align=center>' . $row['ref_no'] . '</td>
		<td align=center>' . $row['ref_date'] . '</td>
		<td align=center>' . $row['ref_type'] . '</td>
		<td align=center>' . $row['cost_center'] . '</td>
		<td align=left>(' . $row['acct'] . ') ' . $row['acct_desc'] . '</td>
		<td align="right">' . number_format($row['debit'],2) . '</td>
		<td align="right">' . number_format($row['credit'],2) . '</td>
		<td align="left" style="padding-right: 10px;">' . $row['cust'] . '</td>
		</tr>'; $i++; $dbGT+=$row['debit']; $crGT+= $row['credit'];
	}
	$html = $html . '<tr>
						<td colspan=5 style="border-top: 0.1mm solid #000000;"></td>
						<td align=right style="border-top: 0.1mm solid #000000;"><b>'.number_format($dbGT,2).'</b></td>
						<td align=right style="border-top: 0.1mm solid #000000;"><b>'.number_format($crGT,2).'</b></td>
						<td style="border-top: 0.1mm solid #000000;"></td>
					 </tr>';
	for($i; $i < 12; $i++) { $html = $html . "<tr><td colspan=7></td></tr>"; }
$html = $html . ';
</tbody>
</table>
<table width=100% cellpadding=5 style="border: 1px solid #000000;">
<tr>
	<td width=33%><b>PREPARED BY:</b><br><br>'.$con->getUname($_REQUEST['user']).'<br></td>
	<td width=33%><b>CHECKED BY:</b><br><br>_________________________________<br><font size=3>Signature over Printed Name</font></td>
	<td width=34%><b>APPROVED BY:</b><br><br>_________________________________<br><font size=3>Signature over Printed Name</font></td>
</tr>
</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

?>