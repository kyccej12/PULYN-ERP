<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	
	//ini_set("display_errors","On");
	require_once "../lib/mpdf6/mpdf.php";
	require_once "../handlers/_generics.php";
	
	$mydb = new _init;
	
	//include("../includes/dbUSE.php");
	
	
	$mpdf=new mPDF('win-1252','folio','','',10,10,32,25,10,10);
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

		if($_GET['item'] != '') { $fs = " and description like '%$_GET[item]%' "; }

		$query = $mydb->dbquery("SELECT * FROM (SELECT  a.code, a.description, a.unit_price, SUM(a.qty) AS qty, SUM(ROUND(a.amount-a.discount,2)) AS amount FROM pharma_so_details a LEFT JOIN pharma_so_header b ON a.trace_no = b.trace_no WHERE b.status = 'Finalized' AND b.so_date BETWEEN '$dtf' AND '$dt2' $fs GROUP BY a.code) a ORDER BY qty DESC;");

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
			<span style="font-weight: bold; font-size: 8pt; color: #000000;">TOP SELLING PRODUCTS REPORT</span><br/>Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
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
			<td align=center><b>NO</b></td>
			<td align=center><b>CODE</b></td>
			<td align=center width=45%><b>DESCRIPTION</b></td>
			<td align=center><b>UNIT PRICE</b></td>
			<td align=center><b>QTY</b></td>
			<td align=center><b>AMOUNT</b></td>>
		</tr>
	</thead>
<tbody>';

$i = 1;
while($row = $query->fetch_array()) {

	$html = $html . '<tr bgcolor="'.$mydb->initBackground($i).'">
		<td align=center>'. $i .'</td>
		<td align=center>'. $row['code'] .'</td>
		<td align=left width=45%>'. html_entity_decode($row['description']) .'</td>
		<td align=right>' . number_format($row['unit_price'],2) . '</td>
		<td align=right>' . number_format($row['qty'],2) . '</td>
		<td align=right>' . number_format($row['amount'],2) . '</td>
	</tr>'; $i++;
}
$html = $html . '</tbody></table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>