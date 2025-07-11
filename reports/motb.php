<?php
	session_start();
	
	ini_set("display_errors","On");
	require_once "../lib/mpdf6/mpdf.php";
	require_once "../handlers/_generics.php";
	
	$mydb = new _init;
	
	$mpdf=new mPDF('win-1252','letter','','',15,15,35,25,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
		list($period) = $mydb->getArray("select date_format('$_GET[year]-$_GET[month]-01','%M %Y');");
		$query = mysql_query("SELECT acct, sum(debit-credit) as amt, b.description FROM acctg_mo_tbalance a left join acctg_accounts b on a.acct = b.acct_code WHERE `month` = '$_GET[month]' and `year` = '$_GET[year]' $xbranch GROUP BY acct order by acct;");
	/* END OF SQL QUERIES */

$html = '
<html>
<head>
<style>
body {
	font-family: sans-serif;
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
		<td style="color:#000000;" width=22%><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
		<td style="color:#000000; padding-top: 15px;">
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
		</td>
		<td width="40%" align=right>
			<span style="font-weight: bold; font-size: 10pt; color: #000000;">TRIAL BALANCE</span><br /><span style="font-size: 6pt; font-style: italic;"><b>' . $lbl .'</b></span><br /><span style="font-size: 6pt; font-style: italic;">'.$period.'</span>
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
		<td width="20%" align=left><b>ACCOUNT CODE</b></td>
		<td width="40%" align=left><b>ACCOUNT DESCRIPTION</b></td>
		<td width="20%" align=right><b>DEBIT</b></td>
		<td width="20%" align=right><b>CREDIT</b></td>
	</tr>
</thead>
<tbody>';

while($row = $query->fetch_array(MYSQLI_BOTH)) {
		if($row['amt'] > 0) { $db = $row['amt']; $cr = 0; } else { $db = 0; $cr = abs($row['amt']); }
		$html = $html . '<tr>
		<td align=left>' . $row['acct'] . '</td>
		<td align=left>' . $row['description'] . '</td>
		<td align=right>' . number_format($db,2) . '</td>
		<td align=right>' . number_format($cr,2) . '</td>
	</tr>'; $dbGT+=$db; $crGT+=$cr;
}

$html = $html . '<tr>
					<td colspan=2></td>
					 <td align=right>-----------------------<br/><b>'.number_format($dbGT,2).'</b><br/>==========</td>
					 <td align=right>-----------------------<br/><b>'.number_format($crGT,2).'</b><br/>==========</td>
			     </tr>';
$html = $html . '</tbody></table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>