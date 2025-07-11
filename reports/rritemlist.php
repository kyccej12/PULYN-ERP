<?php
	include("../lib/mpdflib/mpdf.php");
	include("../includes/dbUSE.php");
	
	$mpdf=new mPDF('win-1252','letter','','',15,15,28,15,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);

	/* MYSQL QUERIES SECTION */	
		$now = date("m/d/Y h:i a");
		$query = mysql_query("select lpad(a.rr_no,2,0) as rr, date_format(rr_date,'%m/%d/%Y') as rdate, a.supplier, a.supplier_name, sum(b.qty) as qty from rr_header a left join rr_details b on a.rr_no = b.rr_no where a.status = 'Finalized' and a.rr_date between '".formatDate($_GET['dtf'])."' and '".formatDate($_GET['dt2'])."' and b.item_code = '".$_GET['item_code']."' group by a.rr_no;");
		list($desc) = getArray("select description from products_master where item_code = '".$_GET['item_code']."';");
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

table thead td {
    text-align: center;
    border: 0.1mm solid #000000;
	background-color:#ededed;
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
		<td width="50%" style="color:#000000;" align=left><img src="../images/geck-small-logo.jpg" /><br /><span style="color: #3b3b3b; font-size: 8pt;">KM. 3, Brgy. Luna, Surigao City, Philippines</span></td>
		<td width="50%" align=right><span style="font-weight: bold; font-size: 8pt;">RECEIVING REPORT SUMMARY <br/>ITEM: ('.$_REQUEST['item_code'].') '.$desc.'<br />Covered Period : ' . $_REQUEST['dtf'] . ' - '. $_REQUEST['dt2'] .'</span></td>
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
<table class="items" width="100%" align=center style="font-size: 8pt; border-collapse: collapse;" cellpadding="8">
<thead>
	<tr>
		<td width="15%"><b>RR #</b></td>
		<td width="15%"><b>RR DATE</b></td>
		<td width="45%" align=left><b>SUPPLIER/ORIGIN</b></td>
		<td width="15%"><b>QTY</b></td>
	</tr>
</thead>
<tbody>';

while($x = mysql_fetch_array($query)) {
	$html = $html . '<tr>
		<td align=center>'.$x['rr'].'</td>
		<td align=center>'.$x['rdate'].'</td>
		<td align=left>('.$x['supplier'].') '.$x['supplier_name'].'</td>
		<td align=center>'.number_format($x['qty'],2).'</td>
	</tr>'; $qtyGT+=$x['qty'];
}
$html = $html . '<tr>
		<td align=center colspan=3 align=right><b>TOTAL QUANTITY >>></b></td>
		<td align=center><b>'.number_format($qtyGT,2).'</b></td>
	</tr>
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