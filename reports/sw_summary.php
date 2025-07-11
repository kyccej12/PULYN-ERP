<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");
	ini_set("memory_limit","1024M");
	ini_set("max_execution_time","0");

	$mpdf=new mPDF('win-1252','folio','','',15,15,32,20,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
		$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
		
		if($_GET['type'] != "") { $f1 = "and a.ref_type = '$_GET[type]' "; }
		switch($_GET['type']) { case "OS": $wtype = "General Office Supplies"; break; case "MS": $wtype = "General Maintenance"; break; case "SP": $wtype = "Stocks Pullout (For Retazor/Fabrication)"; break; case "OTH": $wtype = "Other General Purposes"; break; default: $wtype = "All Withdrawals"; break; }
		$query = mysql_query("SELECT a.sw_no AS doc_no, DATE_FORMAT(sw_date,'%m/%d/%Y') AS doc_date, a.remarks, b.item_code, b.description, b.unit,b.qty FROM sw_header a LEFT JOIN sw_details b ON a.sw_no = b.sw_no AND a.branch = b.branch WHERE a.branch = '$_SESSION[branchid]' AND a.sw_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' AND a.status = 'Finalized' $f1 ORDER BY a.sw_date ASC, a.sw_no ASC;");
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
			<span style="font-weight: bold; font-size: 8pt; color: #000000;"><br/>Summary of Goods Witdhrawn</span><br /><span style="font-size: 6pt; font-style: italic;">Covered Period : ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
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
<table width=100% cellpadding=3 style="font-size: 8pt;">
	<tr><td align=left><b>Widthdrawal Type : </b>'.$wtype.'</td></tr>
</table>
<table class="items" width="100%" align=center style="font-size: 7pt; border-collapse: collapse;" cellpadding="3">
<thead>
	<tr>
		<td width="10%" align=left><b>DOC #</b></td>
		<td width="10%" align=center><b>DOC DATE</b></td>
		<td width="30%" align=left><b>DOC REMARKS</b></td>
		<td width="10%" align=left><b>ITEM CODE</b></td>
		<td width="25%" align=left><b>DESCRIPTION</b></td>
		<td width="5%" align=center><b>UNIT</b></td>
		<td width="10%" align=right><b>QTY</b></td>
	</tr>
</thead>
<tbody>';

while($row = mysql_fetch_array($query)) {
	if($row['doc_no'] != $o) { $dno = $row['doc_no']; $ddate = $row['doc_date']; $rem = $row['remarks']; } else { $dno = ""; $ddate = ""; $rem = ""; }

	$html = $html . '<tr>
		<td align=left>' . $dno . '</td>
		<td align=left>' . $ddate . '</td>
		<td align=left>' . $rem . '</td>
		<td align=left>' . $row['item_code'] . '</td>
		<td align=left>' . $row['description'] . '</td>
		<td align=center>' . $row['unit'] . '</td>
		<td align=right>' . number_format($row['qty'],2) . '</td>
		<td align=right>' . number_format($row['amount'],2) . '</td>
	</tr>'; $amtGT+=$row['amount']; $o = $row['doc_no'];
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