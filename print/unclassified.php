<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");


/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = getArray("select *,lcase(short_name) as bname from companies where company_id = '$_SESSION[company]';");
	$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
	//$bcode = STR_PAD($_REQUEST['user'],2,'0',STR_PAD_LEFT)."-SI".$_ihead['invoice_no']."-".date('Ymd');
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','FOLIO','','',15,15,25,10,10,10);
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
<div style="font-size:12pt;font-weight:bold;" align=center>UNCLASSIFIED ACCOUNTS</div>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100%>
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Run Date: '.date('m/d/Y h:i:s a',strtotime('8 hours')).'</td></tr>
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
<td width="20%" align=center><b>DOC. TYPE</b></td>
<td width="20%" align=center><b>AMOUNT</b></td>
</tr>
</thead>
<tbody>';

	switch($_REQUEST['doc_type']){
		case '1': $filter = " and doc_type = 'AP' ";	break;
		case '2': $filter = " and doc_type = 'CV' ";	break;
		case '3': $filter = " and doc_type = 'JV' ";	break;
		case '4': $filter = " and doc_type = 'SI' ";	break;
		case '5': $filter = " and doc_type = 'CR' ";	break;
		case '10': $filter = " and doc_type = 'POS' ";	break;
	}
	
	$docs = dbquery("SELECT DISTINCT branch,b.branch_name,doc_no,doc_date,doc_type,IF(debit!=0,debit,credit) AS amt FROM ".$co[bname].".acctg_gl a 
						LEFT JOIN options_branches b ON a.company = b.company AND a.branch = b.branch_code
						WHERE a.acct = ''  $filter order by doc_no;");
	
	while($row = mysql_fetch_array($docs)){
		$html .= '
		<tr>
			<td width="20%" align=left>'.$row[branch_name].'</td>
			<td width="20%" align=center>'.$row[doc_no].'</td>
			<td width="20%" align=center>'.$row[doc_date].'</td>
			<td width="20%" align=center>'.$row[doc_type].'</td>
			<td width="20%" align=center>'.$row[amt].'</td>
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