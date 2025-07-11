<?php
	session_start();
	ini_set("max_execution_time",0);
	require_once "../lib/mpdf6/mpdf.php";
	require_once "../handlers/_generics.php";
	
	$mydb = new _init;
	
	$mpdf=new mPDF('win-1252','folio-l','','',10,10,32,25,10,10);
	$mpdf->use_embeddedfonts_1252 = true;
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$adesc = $mydb->getAcctDesc($_GET['source'],$_SESSION['company']);
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
		$query = $mydb->dbquery("SELECT CONCAT(cy,'-',LPAD(cv_no,6,0)) AS dno, DATE_FORMAT(cv_date,'%m/%d/%y') AS dd8, CONCAT('(',payee,') ',payee_name) AS payee, check_no, DATE_FORMAT(check_date,'%m/%d/%Y') AS check_date, remarks, amount FROM cv_header WHERE branch = '$_SESSION[branchid]' AND cv_date between '".$mydb->formatDate($_GET['dtf'])."' and '".$mydb->formatDate($_GET['dt2'])."' and `status` = 'Posted' AND source = '$_GET[source]';");
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
			<span style="font-weight: bold; font-size: 9pt; color: #000000;">Summary of Issued Checks</span><br /><span style="font-size: 6pt; font-style: italic;"><b>('.$_GET['source'].') '.$adesc.'</b><br/>Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
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
		<td width="12%" align=left><b>CV #</b></td>
		<td width="8%" align=center><b>DATE</b></td>
		<td width="20%" align=left><b>PAYEE</b></td>
		<td width="12%" align=left><b>CHECK #</b></td>
		<td width="12%" align=center><b>CHECK DATE</b></td>
		<td width="21%" align=left><b>MEMO</b></td>
		<td width="15%" align=right><b>AMOUNT</b></td>
	</tr>
</thead>
<tbody>
<tr><td colspan=4></td></tr>';
while($row = $query->fetch_array(MYSQLI_BOTH)) {
	$html = $html . '<tr>
		<td align=left>' . $row['dno'] . '</td>
		<td align=center>' . $row['dd8'] . '</td>
		<td align=left>' . $row['payee'] . '</td>
		<td align=left>'. $row['check_no'].'</td>
		<td align=center>'. $row['check_date'].'</td>
		<td align=left>' . $row['remarks'] . '</td>
		<td align=right>' . number_format($row['amount'],2) . '</td>
	</tr>'; $amtGT+=$row['amount'];
}
$html = $html . "<tr><td colspan=6 align=right><br/><b>TOTAL &raquo;</b></td><td align=right>===========<br/>".number_format($amtGT,2)."<br/>--------------------------</td></tr>";
$html = $html . '</tbody></table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>