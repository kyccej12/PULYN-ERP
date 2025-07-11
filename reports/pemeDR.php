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
        if($_GET['cid'] != '') { $fs .= " and b.customer_code = '$_GET[cid]' "; }


		$query = $mydb->dbquery("SELECT 'so' AS `type`,a.so_no, a.branch, DATE_FORMAT(a.so_date,'%m/%d/%Y') AS so_date, a.clinic AS clinic_no, a.procedure, b.customer_code, b.customer_name, a.pid AS patient_id, b.patient_name, CONCAT(b.terms,' ','Days') AS terms, a.code, a.examined_by, CONCAT(c.fullname,', ',c.prefix) AS examined_by, a.evaluated_by, CONCAT(d.fullname,', ',d.prefix) AS evaluated_by, e.description, e.unit_price FROM peme a LEFT JOIN so_header b ON a.so_no = b.so_no LEFT JOIN options_doctors c ON a.examined_by = c.id LEFT JOIN options_doctors d ON a.evaluated_by = d.id LEFT JOIN services_master e ON a.code = e.code WHERE 1=1 AND a.examined_by > 0 AND evaluated_by > 0 AND a.so_date BETWEEN '$dtf' AND '$dt2' $fs ORDER BY so_no;");
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
		<td style="color:#000000;">
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 8pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
		</td>
		<td width="40%" align=right>
			<span style="font-weight: bold; font-size: 8pt; color: #000000;">PEME DETAILED REPORT</span><br/>Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
		</td>
	</tr>
</table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table style="border-top: 1px solid #000000; font-size: 8pt; width: 100%">
<tr>
<td width="50%" align="left">Page {PAGENO} of {nb}</td>
<td width="50%" align="right" style="font-size:8pt; font-color: #cdcdcd;">Run Date: ' . $now . '</td>
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
			<td align=center><b>CLINIC#</b></td>
			<td align=center><b>BILLED TO</b></td>
			<td align=center><b>PATIENT NAME</b></td>
			<td align=center><b>PROCEDURE</b></td>
			<td align=center><b>TERMS</b></td>
			<td align=center><b>CODE</b></td>
			<td align=center><b>EXAMINED BY</b></td>
			<td align=center><b>EVALUATED BY</b></td>
			<td align=center><b>AMOUNT</b></td>
		</tr>
	</thead>
<tbody>';

$total= 0; $amountGT = 0; $i = 1;
while($row = $query->fetch_array()) {

	$ptotal =''; $atotal = '';

	if($row['customer_code'] == 0) { $row['customer_name'] = 'Walk-in Customer'; }

	if($row['so_no'] != $paymaya) { $ptotal = number_format($row['branch']); $ptotal=''; $total += $row['branch']; }
	if($row['unit_price'] != $shopee) { $atotal = number_format($row['unit_price'],2); $atotal= ''; $amountGT += $row['unit_price']; }

	if($row['terms'] == 0) { $row['terms'] = 'Cash'; }

	$html = $html . '<tr bgcolor="'.$mydb->initBackground($i).'">
		<td align=center>' . $row['so_no'] . '</td>
		<td align=center>' . $row['so_date'] . '</td>
		<td align=center>' . $row['clinic_no'] . '</td>
		<td align=left width=15%>' . $row['customer_name'] . '</td>
		<td align=left width=15%>'. $row['patient_name'] .'</td>
		<td align=center>'. $row['procedure'] .'</td>
		<td align=center>'. $row['terms'] .'</td>
		<td align=center>'. $row['code'] .'</td>
		<td align=center>'. $row['examined_by'] .'</td>
		<td align=center>'. $row['evaluated_by'] .'</td>
		<td align=center>'. $row['unit_price'] .'</td>
		
	</tr>'; $paymaya = $row['branch']; $shopee = $row['so_no']; $i++;
}

$html = $html . '<tr bgcolor="'.$mydb->initBackground($i).'">
					<td colspan=10 align=left><b>AMOUNT GRAND TOTAL</b></td>
				<td align=right><b>' . number_format($amountGT,2) . '</b></td>
				</tr>

				<tr bgcolor="'.$mydb->initBackground($i).'">
					<td colspan=11 align=left><b>TOTAL PATIENT EVALUATED:</b>&nbsp;&nbsp;<b>'. number_format($total) .'</b></td>
				</tr>';
$html = $html . '</tbody></table>
</body>
</html>
';

$html = html_entity_decode($html);
$mpdf->WriteHTML($html);
$mpdf->Output(); 
exit;

mysql_close($con);
?>