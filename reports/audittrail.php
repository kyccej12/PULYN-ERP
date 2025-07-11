<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");
	
	$mpdf=new mPDF('win-1252','folio-l','','',15,15,35,25,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);


	/* MYSQL QUERIES SECTION */	
		if($_GET['user'] != "") { $f1 = " and a.user_id='$_GET[user]' "; }
		if($_GET['module'] != "") { $f2 = " and a.module = '$_GET[module]' "; }
		$now = date("m/d/Y h:i a");
		
		$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
		$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");

		$query = dbquery("SELECT LPAD(a.user_id,3,'0') AS uid, b.fullname, ipaddress, module,DATE_FORMAT(`timestamp`,'%m/%d/%Y %r') AS tstamp, `action` FROM $dbase.traillog a LEFT JOIN user_info b ON a.user_id = b.emp_id WHERE DATE_FORMAT(`timestamp`,'%Y-%m-%d') BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' $f1 $f2;");
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
		<td style="color:#000000;" width=150><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
		<td style="color:#000000; padding-top: 15px;">
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
		</td>
			<br /><br /><span style="font-weight: bold; font-size: 8pt;">SYSTEM AUDIT TRAIL<br />From: ' . $_REQUEST['dtf'] . ' - ' . $_REQUEST['dt2'] . '</span>
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
		<td width="8%" align=left><b>USER ID</b></td>
		<td width="15%"><b>USER</b></td>
		<td width="15%" align=left><b>IP ADDRESS</b></td>
		<td width="15%"><b>MODULE</b></td>
		<td width="15%"><b>TIMESTAMP</b></td>
		<td width="30%" align=left><b>ACTION TAKEN</b></td>
	</tr>
</thead>
<tbody>
<tr><td colspan=6></td></tr>';

while($row = mysql_fetch_array($query)) {
	$html = $html . '<tr>
		<td align=left width=5%>' . $row['uid'] . '</td>
		<td align=center width=15%>' . $row['fullname'] . '</td>
		<td align=left width=10%>' . $row['ipaddress'] . '</td>
		<td align=center width=10%>' . $row['module'] . '</td>
		<td align=center width=10%>' . $row['tstamp'] . '</td>
		<td align=left>' . $row['action'] . '</td>
	</tr>'; $amountGT+=$row['amount'];
}

$html = $html . '</tbody></table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>