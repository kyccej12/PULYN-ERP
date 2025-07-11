<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	
	//ini_set("display_errors","On");
	require_once "../lib/mpdf6/mpdf.php";
	require_once "../handlers/_generics.php";
	
	$mydb = new _init;
	
	//include("../includes/dbUSE.php");
	
	
	$mpdf=new mPDF('win-1252','folio-l','','',10,10,32,25,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);
	
	$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
	$fs = '';
	
	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$dtf = $mydb->formatDate($_GET['dtf']);
		$dt2 = $mydb->formatDate($_GET['dt2']);


		//$query = $mydb->dbquery("SELECT * FROM (SELECT 'so' AS `type`, a.so_no, DATE_FORMAT(b.so_date, '%m/%d/%Y') AS so_date, '' AS si_no, '' AS sidate, b.customer_code, b.customer_name, b.patient_name, c.description AS xterms, a.code, a.description, a.amount, a.discount, ROUND(a.amount-a.discount,2) AS charge_sales, '0' AS cash_sales, '0' AS cc_sales FROM pharma_so_details a LEFT JOIN pharma_so_header b ON a.trace_no = b.trace_no LEFT JOIN options_terms c ON b.terms = c.terms_id WHERE b.status = 'Finalized' AND b.terms NOT IN ('0','100') AND b.so_date BETWEEN '$dtf' AND '$dt2' $fs UNION ALL SELECT 'so' AS `type`, a.so_no, DATE_FORMAT(b.so_date, '%m/%d/%Y') AS so_date, '' AS si_no, '' AS sidate, b.customer_code, b.customer_name, b.patient_name, c.description AS xterms, a.code, a.description, a.amount, a.discount, '0' AS charge_sales, '0' AS cash_sales, '0' AS cc_sales FROM pharma_so_details a LEFT JOIN pharma_so_header b ON a.trace_no = b.trace_no LEFT JOIN options_terms c ON b.terms = c.terms_id WHERE b.status= 'Finalized' AND b.terms IN ('100') AND b.so_date BETWEEN '$dtf' AND '$dt2' $fs UNION ALL SELECT 'si' AS `type`, a.so_no, DATE_FORMAT(b.doc_date,'%m/%d/%Y') AS so_date, b.si_no AS si_no, DATE_FORMAT(b.doc_date,'%m/%d/%Y') AS sidate, b.customer_code, b.customer_name, b.patient_name, 'CASH' AS terms, a.code, a.description, a.amount AS amount, a.discount, '0' AS charge_sales, ROUND(a.amount-a.discount,2) AS cash_sales, '0' AS cc_sales FROM pharma_si_details a LEFT JOIN pharma_si_header b ON a.trace_no = b.trace_no WHERE b.status= 'Finalized' AND b.doc_date BETWEEN '$dtf' AND '$dt2' $fs) a ORDER BY so_no, si_no;");
		$query = $mydb->dbquery("SELECT * FROM (SELECT 'so' AS `type`, a.so_no, DATE_FORMAT(b.so_date, '%m/%d/%Y') AS so_date, '' AS si_no, '' AS sidate, b.customer_code, b.customer_name, b.patient_name, c.description AS xterms, a.code, a.description, a.unit_price, a.qty, a.amount, a.discount, ROUND(a.amount-a.discount,2) AS charge_sales, '0' AS cash_sales FROM pharma_so_details a LEFT JOIN pharma_so_header b ON a.trace_no = b.trace_no LEFT JOIN options_terms c ON b.terms = c.terms_id WHERE b.status = 'Finalized' AND b.terms NOT IN ('0','100') AND b.so_date BETWEEN '$dtf' AND '$dt2' UNION ALL SELECT 'so' AS `type`, a.so_no, DATE_FORMAT(b.so_date, '%m/%d/%Y') AS so_date, '' AS si_no, '' AS sidate, b.customer_code, b.customer_name, b.patient_name, c.description AS xterms, a.code, a.description, a.unit_price, a.qty, a.amount, a.discount, '0' AS charge_sales, '0' AS cash_sales FROM pharma_so_details a LEFT JOIN pharma_so_header b ON a.trace_no = b.trace_no LEFT JOIN options_terms c ON b.terms = c.terms_id WHERE b.status= 'Finalized' AND b.terms IN ('100') AND b.so_date BETWEEN '$dtf' AND '$dt2' UNION ALL SELECT 'si' AS `type`, a.so_no, DATE_FORMAT(b.doc_date,'%m/%d/%Y') AS so_date, b.si_no AS si_no, DATE_FORMAT(b.doc_date,'%m/%d/%Y') AS sidate, b.customer_code, b.customer_name, b.patient_name, 'CASH' AS terms, a.code, a.description, a.unit_price, a.qty,  a.amount AS amount, a.discount, '0' AS charge_sales, ROUND(a.amount-a.discount,2) AS cash_sales FROM pharma_si_details a LEFT JOIN pharma_si_header b ON a.trace_no = b.trace_no WHERE b.status= 'Finalized' AND b.doc_date BETWEEN '$dtf' AND '$dt2') a ORDER BY so_no, si_no;");

	/* END OF SQL QUERIES */

$html = '
<html>
<head>
<style>
body {
	font-family: calibri;
    font-size: 8pt;
}
p {    margin: 0pt;
}
td { vertical-align: top; }

table thead td {
	border: 0.1mm solid #000000;
	background-color: #cdcdcd;
}

.items td {
    border: 0.1mm solid #000000;
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
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
		</td>
		<td width="40%" align=right>
			<span style="font-weight: bold; font-size: 8pt; color: #000000;">PHARMACY DETAILED SALES REPORT</span><br/>Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
		</td>
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
<table class="items" width="100%" align=center style="font-size: 8pt; border-collapse: collapse;" cellpadding="3">
	<thead>
		<tr>
			<td align=center><b>SO #</b></td>
			<td align=center><b>SO DATE</b></td>
			<td align=center><b>SI #</b></td>
			<td align=center><b>SI DATE</b></td>
			<td align=center><b>BILLED TO</b></td>
			<td align=center><b>PATIENT NAME</b></td>
			<td align=center><b>TERMS</b></td>
			<td  align=center><b>CODE</b></td>
			<td align=center><b>DESCRIPTION</b></td>
			<td align=center><b>UNIT PRICE</b></td>
			<td align=center><b>QTY</b></td>
			<td align=center><b>AMOUNT</b></td>
			<td align=center><b>DISCOUNT</b></td>
			<td align=center><b>CHARGE SALES</b></td>
			<td align=center><b>CASH SALES</b></td>
		</tr>
	</thead>
<tbody>';

$cashGT = 0; $chargeGT = 0; $i = 1;
while($row = $query->fetch_array()) {
	
	$charge = ''; $cash  = '';

	switch($row['type']) {
		case "so":
			if($row['so_no'] != $xso) {
				$charge = number_format($row['charge_sales'],2); $cash = ''; $chargeGT += $row['charge_sales'];
			}
		break;
		case "si":
			if($row['si_no'] != $xor) {
				$cash = number_format($row['cash_sales'],2); $charge = ''; $cashGT += $row['cash_sales'];
			}
		break;
	}

	if($row['customer_code'] == 0) { $billedto = $row['customer_name']; } else { $billedto = 'WALK-IN CUSTOMER'; }

	$html = $html . '<tr bgcolor="'.$mydb->initBackground($i).'">
		<td align=center>' . $row['so_no'] . '</td>
		<td align=center>' . $row['so_date'] . '</td>
		<td align=center>' . $row['si_no'] . '</td>
		<td align=center>' . $row['sidate'] . '</td>
		<td align=left width=15%>' . $row['customer_name'] . '</td>
		<td align=left width=10%>'. $row['patient_name'] .'</td>
		<td align=center>'. $row['xterms'] .'</td>
		<td align=center>'. $row['code'] .'</td>
		<td align=left width=15%>'. $row['description'] .'</td>
		<td align=right>' . number_format($row['unit_price'],2) . '</td>
		<td align=right>' . number_format($row['qty'],2) . '</td>
		<td align=right>' . number_format($row['amount'],2) . '</td>
		<td align=right>' . number_format($row['discount'],2) . '</td>
		<td align=right width=8%>' . $charge . '</td>
		<td align=right>' .$cash . '</td>
	</tr>'; $xso = $row['si_no']; $xor = $row['so_no']; $i++;
}

$html = $html . '<tr bgcolor="'.$mydb->initBackground($i).'">
					<td colspan=13 align=left><b>GRAND TOTAL</b></td>
					<td align=right><b>' . number_format($chargeGT,2) . '</b></td>
					<td align=right><b>' .number_format($cashGT,2) . '</b></td>
			     </tr>';
$html = $html . '</tbody></table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>