<?php
	ini_set("memory_limit","1024M");
	set_time_limit(0);
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");


	switch($_REQUEST['status']){
		case '1':
			$ss = " and status = 'Cancelled' ";
		break;
		case '2':
			$ss = " and status = 'Active' ";
		break;
		case '3':
			$ss = " and status in ('Active','Posted') ";
		break;
		default:
		
		break;
	} 
	
	$dtf = formatDate($_REQUEST['dtf']);
	$dt2 = formatDate($_REQUEST['dt2']);
	
	$xd = " between '$dtf' and '$dt2' ";
/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = getArray("select *,lcase(short_name) as bname from companies where company_id = '$_SESSION[company]';");
	$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
	//$bcode = STR_PAD($_REQUEST['user'],2,'0',STR_PAD_LEFT)."-SI".$_ihead['invoice_no']."-".date('Ymd');
	
		switch($_REQUEST['doc_type']){
		case '1': 	
			$docs = dbquery("SELECT UCASE(b.branch_name) AS bname,branch,apv_no as doc_no,apv_date as doc_date,`status` FROM apv_header a LEFT JOIN options_branches b 
							 ON a.company = b.company AND a.branch = b.branch_code where a.branch = '$_SESSION[branchid]' and a.company = '$_SESSION[company]' and apv_date $xd $ss order by apv_no");
			$dtype = "A.P. VOUCHER";
		break;
		case '2': 
			$docs = dbquery("SELECT UCASE(b.branch_name) AS bname,branch,cv_no as doc_no,cv_date as doc_date,`status` FROM sjpi.cv_header a LEFT JOIN options_branches b 
							 ON a.company = b.company AND a.branch = b.branch_code where a.branch = '$_SESSION[branchid]' and a.company = '$_SESSION[company]' and cv_date $xd $ss order by cv_no;");
			$dtype = "CHECK VOUCHER";
		break;
		case '3': 
			$docs = dbquery("SELECT UCASE(b.branch_name) AS bname,branch,j_no AS doc_no,j_date as doc_date,`status` FROM sjpi.journal_header a LEFT JOIN options_branches b 
							 ON a.company = b.company AND a.branch = b.branch_code where a.branch = '$_SESSION[branchid]' and a.company = '$_SESSION[company]' and j_date $xd $ss order by j_no;");
			$dtype = "JOURNAL VOUCHER";
		break;
		case '4': 
			$docs = dbquery("SELECT UCASE(b.branch_name) AS bname,invoice_no AS dc_no,invoice_date AS doc_date,STATUS FROM invoice_header a LEFT JOIN options_branches b 
							 ON a.company = b.company AND a.branch = b.branch_code where a.branch = '$_SESSION[branchid]' and a.company = '$_SESSION[company]' and invoice_date $xd $ss order by invoice_no;");
			$dtype = "SALES INVOICE";
		break;
		case '5': 
			$docs = dbquery("SELECT UCASE(b.branch_name) AS bname,cr_no AS doc_no,cr_date AS doc_date,STATUS FROM cr_header a LEFT JOIN options_branches b 
							 ON a.company = b.company AND a.branch = b.branch_code WHERE a.branch = '$_SESSION[branchid]' AND a.company = '$_SESSION[company]' and cr_date $xd $ss ORDER BY cr_no;");
			$dtype = "COLLECTION RECEIPT";
		break;
		case '6': 	
			$docs = dbquery("SELECT UCASE(b.branch_name) AS bname,rr_no AS doc_no,rr_date AS doc_date,STATUS FROM rr_header a LEFT JOIN options_branches b 
							ON a.company = b.company AND a.branch = b.branch_code WHERE a.branch = '$_SESSION[branchid]' AND a.company = '$_SESSION[company]' and rr_date $xd $ss ORDER BY rr_no;");
			$dtype = "RECEIVING REPORT";
		break;
		case '7': 	
			$docs = dbquery("SELECT UCASE(b.branch_name) AS bname,sw_no AS doc_no,sw_date AS doc_date,STATUS FROM sw_header a LEFT JOIN options_branches b 
							ON a.company = b.company AND a.branch = b.branch_code WHERE a.branch = '$_SESSION[branchid]' AND a.company = '$_SESSION[company]' and sw_date $xd $ss ORDER BY sw_no ;");
			$dtype = "STOCKS WITHDRAWAL";
		break;
		case '8': 	
			$docs = dbquery("SELECT UCASE(b.branch_name) AS bname,str_no AS doc_no,str_date AS doc_date,STATUS FROM str_header a LEFT JOIN options_branches b 
							ON a.company = b.company AND a.branch = b.branch_code WHERE a.branch = '$_SESSION[branchid]' AND a.company = '$_SESSION[company]' and str_date $xd $ss ORDER BY str_no;");
			$dtype = "STOCKS TRANSFER";
		break;
		case '9': 	
			$docs = dbquery("SELECT UCASE(b.branch_name) AS bname,srr_no AS doc_no,srr_date AS doc_date,STATUS FROM srr_header a LEFT JOIN options_branches b 
							ON a.company = b.company AND a.branch = b.branch_code WHERE a.branch = '$_SESSION[branchid]' AND a.company = '$_SESSION[company]' and srr_date $xd $ss ORDER BY srr_no;");
			$dtype = "STOCKS RECEIVING";
		break;
	}
	
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','FOLIO','','',15,15,25,13,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");

$mpdf->SetDisplayMode(60);
//background-color: #EEEEEE;
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
	
</td>
</tr>
</table>
<br>
<div style="font-size:12pt;font-weight:bold;" align=center>LIST OF ISSUED DOCUMENTS ('.$dtype.')</div>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100%>
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Run Date: '.date('m/d/Y h:i:s a').'</td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table  width="100%" style="font-size: 9pt; border-collapse: collapse;" cellpadding="3">
<thead>
<tr>
<td width="20%" align=left><b>BRANCH</b></td>
<td width="20%" align=center><b>DOC. NO</b></td>
<td width="20%" align=center><b>DOC. DATE</b></td>
<td width="20%" align=center><b></b></td>
<td width="20%" align=center><b>STATUS</b></td>
</tr>
</thead>
<tbody>';


	
	
	
	while($row = mysql_fetch_array($docs)){
		$html .= '
		<tr>
			<td width="20%" align=left>'.$row[bname].'</td>
			<td width="20%" align=center>'.$row[doc_no].'</td>
			<td width="20%" align=center>'.$row[doc_date].'</td>
			<td width="20%" align=center></td>
			<td width="20%" align=center>'.$row[status].'</td>
			</tr>
		';
	}
	
$html = $html . '
</tbody>
</table>


</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>