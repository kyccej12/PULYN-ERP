<?php
	session_start();
	
	set_time_limit(0);
	require_once "../lib/mpdf6/mpdf.php";
	require_once "../handlers/_generics.php";
	
	$mydb = new _init;

	$mpdf=new mPDF('win-1252','FOLIO-L','','',10,10,32,20,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
		$dtf = $mydb->formatDate($_GET['dtf']); $dt2 = $mydb->formatDate($_GET['dt2']);
		$query = $mydb->dbquery("SELECT * FROM (SELECT CONCAT('CV-',a.cv_no) AS doc_no, a.cv_date AS docdate, DATE_FORMAT(a.cv_date,'%m/%d/%Y') AS dd8, IF(b.supplier_name='',a.payee,b.supplier) AS payee, IF(b.supplier_name='',a.payee_name,b.supplier_name) AS payeename, supplier_address, supplier_tin, invoice_no, DATE_FORMAT(invoice_date,'%m/%d/%Y') AS idate, ROUND(b.net_payable+b.ewt_amount,2) AS gross, b.input_vat, b.ewt_amount AS ewt, ROUND(b.net_payable,2) AS net FROM cv_header a INNER JOIN cv_subheader b ON a.cv_no = b.cv_no AND a.branch = b.branch WHERE a.branch = '1' AND a.cv_date BETWEEN '$dtf' AND '$dt2' AND a.status = 'Posted' UNION ALL SELECT CONCAT('APV-',a.apv_no) AS doc_no, a.apv_date AS docdate, DATE_FORMAT(a.apv_date,'%m/%d/%Y') AS dd8, a.supplier AS payee, a.supplier_name AS payeename, '' AS supplier_address, '' AS supplier_tin, b.invoice_no, DATE_FORMAT(invoice_date,'%m/%d/%Y') AS idate, ROUND(b.net_payable+b.ewt_amount,2) AS gross, b.input_vat, b.ewt_amount AS ewt, ROUND(b.net_payable,2) AS net FROM apv_header a INNER JOIN apv_subheader b ON a.apv_no = b.apv_no AND a.branch = b.branch WHERE a.branch = '1' AND a.apv_date BETWEEN '$dtf' AND '$dt2' AND a.status = 'Posted' UNION ALL SELECT CONCAT('JV-',a.j_no) AS doc_no, a.j_date AS docdate, DATE_FORMAT(a.j_date,'%m/%d/%Y') AS dd8, b.supplier AS payee, b.supplier_name AS payeename, b.supplier_address, b.supplier_tin, invoice_no, DATE_FORMAT(invoice_date,'%m/%d/%Y') AS idate, ROUND(b.net_payable+b.ewt_amount,2) AS gross, b.input_vat, b.ewt_amount AS ewt, ROUND(b.net_payable-b.ewt_amount,2) AS net FROM journal_header a INNER JOIN journal_invoices b ON a.j_no = b.j_no AND a.branch = b.branch WHERE a.branch = '1' AND a.j_date BETWEEN '$dtf' AND '$dt2' AND a.status = 'Posted') a WHERE 1=1 ORDER BY docdate ASC, doc_no ASC;");
		
	/* END OF SQL QUERIES */

$html = '
<html>
<head>
<style>
body {font-family: sans-serif;
    font-size: 8pt;
}
p {    margin: 0pt;
}
td { vertical-align: top; }

table thead td {
    text-align: center;
    border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
}

.lowerHeader {
    text-align: center;
    border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
}

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

.items td.lowertotals {
	border: 0mm none #000000;
    border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
}

</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
	<tr>
		<td style="color:#000000; padding-top: 15px;">
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
		</td>
		<td width="40%" align=right>
			<span style="font-weight: bold; font-size: 9pt; color: #000000;">Summary of Vatable Purchases<br/><span style="font-size: 6pt; font-style: italic;">Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
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
<table class="items" width="100%" align=center style="font-size: 8pt; border-collapse: collapse;" cellpadding="2">
<thead>
	<tr>
		<td width="5%" align=left><b>DOC #</b></td>
		<td width="8%" align=left><b>DOC DATE</b></td>
		<td width="8%" align=left><b>REF #</b></td>
		<td width="8%" align=left><b>REF DATE</b></td>
		<td width="15%" align=left><b>PAYEE/SUPPLIER</b></td>
		<td align=left><b>ADDRESS</b></td>
		<td width="10%" align=center><b>T-I-N #</b></td>
		<td width="8%" align=right><b>GROSS</b></td>
		<td width="8%" align=right><b>V-A-T</b></td>
		<td width="5%" align=right><b>EWT</b></td>
		<td width="8%" align=right><b>NET AMOUNT</b></td>
	</tr>
</thead>
<tbody>';

while($row = $query->fetch_array(MYSQLI_BOTH)) {
		
		if($row['payee'] != 0) {
			$ttt = $mydb->getArray("SELECT tin_no, address, brgy, city, province FROM contact_info WHERE file_id = '$row[payee]';");
			list($brgy) = $mydb->getArray("SELECT brgyDesc FROM options_brgy WHERE brgyCode = '$ttt[brgy]';");
			list($ct) = $mydb->getArray("SELECT cityMunDesc FROM options_cities WHERE cityMunCode = '$ttt[city]';");
			list($prov) = $mydb->getArray("SELECT provDesc FROM options_provinces WHERE provCode = '$ttt[province]';");
			$row['supplier_tin'] = $ttt['tin_no']; $row['supplier_address'] =  $ttt['address'] . ',' . $brgy .','.$ct.','. $prov;
		}
		
		$html = $html . '<tr>
			<td align=left>'. $row['doc_no'] . '</td>
			<td align=left>'. $row['dd8'] . '</td>
			<td align=left>' . $row['invoice_no'] . '</td>
			<td align=left>' . $row['idate'] . '</td>
			<td align=left>' . $row['payeename'] . '</td>
			<td align=left>' . $row['supplier_address'] . '</td>
			<td align=center>' . $row['supplier_tin'] . '</td>
			<td align=right>' . number_format($row['gross'],2) . '</td>
			<td align=right>' . number_format($row['input_vat'],2) . '</td>
			<td align=right>' . number_format($row['ewt'],2) . '</td>
			<td align=right>' . number_format($row['net'],2) . '</td>
		</tr>'; $grossGT+=$row['gross']; $inputGT+=$row['input_vat']; $ewtGT+=$row['ewt']; $netGT+=$row['net']; $brgy = ''; $ct = ''; $prov = '';
}

$html = $html . '<tr>
					<td colspan=7 style="border-top: 1px solid black;"></td>
					<td align=right style="border-top: 1px solid black;"><b>'.number_format($grossGT,2).'</td>
					 <td align=right style="border-top: 1px solid black;"><b>'.number_format($inputGT,2).'</td>
					 <td align=right style="border-top: 1px solid black;"><b>'.number_format($ewtGT,2).'</td>
					 <td align=right style="border-top: 1px solid black;"><b>'.number_format($netGT,2).'</td>
			     </tr>
	</tbody>
</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>