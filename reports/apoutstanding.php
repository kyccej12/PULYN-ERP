<?php
session_start();
ini_set("max_execution_time",0);
ini_set("memory_limit",-1);
require_once "../lib/mpdf6/mpdf.php";
require_once "../handlers/_generics.php";

$mydb = new _init;

$mpdf=new mPDF('win-1252','folio-l','','',15,15,35,25,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(75);

	/* MYSQL QUERIES SECTION */
		$date = $mydb->formatDate($_GET['asof']);
		$now = date("m/d/Y h:i a");
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
		$fDates = $mydb->getArray("select date_format('" . $date . "','%W %M %d, %Y') as date;");
	/* END OF SQL QUERIES */

$html = '
<html>
<head>
<style>
body {font-family: sans-serif;
    font-size: 10pt;
}
p {    margin: 0pt;
}
td { vertical-align: top; }

.items td.blanktotal {
    background-color: #FFFFFF;
    border: 0mm none #000000;
    border-top: 0.1mm solid #000000;
    border-right: 0.1mm solid #000000;
}
.items td.totals {
    text-align: right;
    border: 0.1mm solid #000000;
}
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
	<tr>
		<td style="color:#000000; padding-top: 15px;" width=50%>
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
		</td>
		<td align=right><span style="font-weight: bold; font-size: 8pt;">Customer\'s Outstanding Invoices<br />Date As Of: '. $_GET['asof'] . '</span></td>
	</tr>
</table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table style="border-top: 1px solid #000000; font-size: 7pt; width: 100%">
	<tr>
	<td width="50%" align="left">Page {PAGENO} of {nb}</td>
	<td width="50%" align="right" style="font-size:7pt; font-color: #cdcdcd;">Run Date: ' . $now . '</td>
	</tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table class="items" width="100%" style="font-size: 8pt; border-collapse: collapse;" cellpadding="5" border="1">
<thead>
<tr>
<td align=left><b>SUPPLIER</b></td>
<td width="15%" align=center><b>REF. No.</b></td>
<td width="8%" align=center><b>DATE</b></td>
<td width="8%" align=center><b>TERMS</b></td>
<td width="8%" align=center><b>DUE DATE</b></td>
<td width="8%" align=center><b>DAYS DUE</b></td>
<td width="8%" align=center><b>AMOUNT</b></td>
<td width="10%" align=center><b>AMOUNT PAID</b></td>
<td width="10%" align=center><b>BALANCE DUE&nbsp;</b></td>
</tr>
</thead>
<tbody>';
	if($_GET['cust'] != '') { $f1 = " and a.supplier = '$_GET[cust]' "; $f2 = " and b.customer = '$_GET[cust]' "; }
	if($_GET['od'] == "Y") { $f3 = " and a.daysdue > 0 "; }
	$str = "SELECT * FROM (SELECT CONCAT('APV-',LPAD(a.apv_no,6,0)) AS docno, DATE_FORMAT(apv_date,'%m/%d/%y') docdate, apv_date AS xd8, a.supplier, a.supplier_name, b.description AS termDesc, DATE_FORMAT(DATE_ADD(apv_date,INTERVAL a.terms DAY),'%m/%d/%Y') AS due, IF('$date' <= DATE_ADD(apv_date,INTERVAL a.terms DAY),'',DATEDIFF('$date',DATE_ADD(apv_date,INTERVAL a.terms DAY))) AS daysdue, amount, applied_amount, balance FROM apv_header a LEFT JOIN options_terms b ON a.terms = b.terms_id WHERE a.status = 'Posted' AND a.balance > 0 $f1 UNION ALL SELECT b.invoice_no AS docno, DATE_FORMAT(b.invoice_date,'%m/%d/%y') AS docdate, b.invoice_date AS xd8, b.customer AS supplier, b.customer_name AS supplier_name, e.description AS termDesc, DATE_FORMAT(DATE_ADD(b.invoice_date,INTERVAL c.terms DAY),'%m/%d/%Y') AS due, IF('$date' <= DATE_ADD(invoice_date,INTERVAL c.terms DAY),'',DATEDIFF('$date',DATE_ADD(invoice_date,INTERVAL c.terms DAY))) AS daysdue, amount, applied_amount, balance FROM apbeg_header a LEFT JOIN apbeg_details b ON a.doc_no = b.doc_no AND a.branch = b.branch LEFT JOIN contact_info c ON b.customer = c.file_id LEFT JOIN options_terms e ON c.terms = e.terms_id WHERE a.status = 'Posted' AND b.balance > 0 $f2) a where 1=1 $f3 ORDER BY a.supplier_name ASC;";
	$a = $mydb->dbquery($str);
	while($row = $a->fetch_array(MYSQLI_BOTH)) {
		$html = $html . '<tr>
			<td align="left">('.$row['supplier'].') ' . $row['supplier_name'] . '</td>
			<td align="center">' . $row['docno'] . '</td>
			<td align="center">' . $row['docdate'] . '</td>
			<td align="center">' . $row['termDesc'] . '</td>
			<td align="center">' . $row['due'] . '</td>
			<td align="center">' . $row['daysdue'] . '</td>
			<td align=right>' . number_format($row['gross_amount'],2) . '</td>
			<td align="right">' . number_format($row['app_amount'],2) . '</td>
			<td align="right">' . number_format($row['balance'],2) . '</td>
			</tr>'; $balanceGT+=$row['balance'];
	}

$html = $html . '<tr><td colspan=8 align=right><b>GRAND TOTAL &raquo;</b></td><td align=right><b>'. number_format($balanceGT,2) . '</b></td></tr>';
$html = $html . '
</tbody>
</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>