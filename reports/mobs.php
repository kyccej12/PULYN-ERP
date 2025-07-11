<?php
	ini_set("max_execution_time",0);
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");

	$mpdf=new mPDF('win-1252','letter','','',15,15,35,25,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Solutions");
	$mpdf->SetDisplayMode(60);

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		
		list($end,$lbl) = getArray("select last_day('$_GET[year]-$_GET[month]-01'), date_format('$_GET[year]-$_GET[month]-01','%M %Y');");
		
		$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
		$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");

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
		<td style="color:#000000; padding-top: 15px;">
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
		</td>
		<td align=right><br/><span style="font-weight: bold; font-size: 8pt;">Balance Sheet <br/> Ending '.$lbl.'</span></td>
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
<sethtmlpagefooter name="myfooter" value="off" />
mpdf-->
<table class="items" width="100%" style="font-size: 8pt; border-collapse: collapse;" cellpadding="3">
<tbody>';

	/* Assets */
	$html = $html . '<tr><td align="left" colspan=2 style="border-bottom: 0.1mm solid black;"><b>ASSETS</b></td></tr>';
	/* Cash & Cash Equivalents */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>CASH & CASH EQUIVALENTS</b></td></tr>';
	$a = dbquery("SELECT b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '1000' AND monthend <= '$end' GROUP BY acct;");
	while($b = mysql_fetch_array($a)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$b['description'].'</td><td align=right>'.formatNumber($b['amount'],2).'</td></tr>';
		$abGT+=$b['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL CASH & CASH EQUIVALENTS</b></td><td align=right><b>------------------------<br/>'.formatNumber($abGT,2).'<br/>============</b></td></tr>';
	
	/* Accounts Receivable*/
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>ACCOUNTS RECEIVABLE</b></td></tr>';
	$c = dbquery("SELECT b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '1100' AND monthend <= '$end' GROUP BY acct;");
	while($d = mysql_fetch_array($c)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$d['description'].'</td><td align=right>'.formatNumber($d['amount'],2).'</td></tr>';
		$cdGT+=$d['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL ACCOUNTS RECEIVABLE</b></td><td align=right><b>------------------------<br/>'.formatNumber($cdGT,2).'<br/>============</b></td></tr>';
	
	/* Inventory Asset */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>INVENTORY ASSET</b></td></tr>';
	$e = dbquery("SELECT b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '1200' AND monthend <= '$end' GROUP BY acct;");
	while($f= mysql_fetch_array($e)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$f['description'].'</td><td align=right>'.formatNumber($f['amount'],2).'</td></tr>';
		$efGT+=$f['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL INVENTORY ASSETS</b></td><td align=right><b>------------------------<br/>'.formatNumber($efGT,2).'<br/>============</b></td></tr>';
	
	/* Properties & Equipment */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>PROPERTIES & EQUIPMENT</b></td></tr>';
	$g = dbquery("SELECT b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '1600' AND monthend <= '$end' GROUP BY acct;");
	while($h= mysql_fetch_array($g)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$h['description'].'</td><td align=right>'.formatNumber($h['amount'],2).'</td></tr>';
		$ghGT+=$h['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL PROPERTIES & EQUIPMENT</b></td><td align=right><b>------------------------<br/>'.formatNumber($ghGT,2).'<br/>============</b></td></tr>';
	
	/* Intangible Asset */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>INTANGIBLE ASSETS</b></td></tr>';
	$i = dbquery("SELECT b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '1700' AND monthend <= '$end' GROUP BY acct;");
	while($j= mysql_fetch_array($i)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$j['description'].'</td><td align=right>'.formatNumber($j['amount'],2).'</td></tr>';
		$ijGT+=$j['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL INTANGIBLE ASSETS</b></td><td align=right><b>------------------------<br/>'.formatNumber($ijGT,2).'<br/>============</b></td></tr>';
	
	/* OTHER CURRENT & NON-CURRENT ASSETS */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>OTHER CURRENT & NON-CURRENT ASSETS</b></td></tr>';
	$k = dbquery("SELECT b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp in ('1300','1400','1500','1800') AND monthend <= '$end' GROUP BY acct;");
	while($l= mysql_fetch_array($k)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$l['description'].'</td><td align=right>'.formatNumber($l['amount'],2).'</td></tr>';
		$klGT+=$l['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL OTHER CURRENT & NON-CURRENT ASSETS</b></td><td align=right><b>------------------------<br/>'.formatNumber($klGT,2).'<br/>============</b></td></tr>';
	$html = $html . '<tr><td align="left"><br/><b>TOTAL ASSETS</b></td><td align=right><b>------------------------<br/>'.formatNumber(($abGT+$cdGT+$efGT+$ghGT+$ijGT+$klGT),2).'<br/>============</b></td></tr>';
	
	/* Liabilities & EQUITIES */
	$html = $html . '<tr><td align="left" colspan=2 style="border-bottom: 0.1mm solid black;"><b>LIABILITIES & EQUITY</b></td></tr>';
	
	/* CURRENT LIABILITIES */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>CURRENT LIABILITIES</b></td></tr>';
	$m = dbquery("SELECT b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '2000' AND monthend <= '$end' GROUP BY acct;");
	while($n = mysql_fetch_array($m)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$n['description'].'</td><td align=right>'.formatNumber($n['amount'],2).'</td></tr>';
		$mnGT+=$n['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL CURRENT LIABILITIES</b></td><td align=right><b>------------------------<br/>'.formatNumber($mnGT,2).'<br/>============</b></td></tr>';
	
	/* OTHER CURRENT LIABILITIES */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>OTHER CURRENT LIABILITIES</b></td></tr>';
	$o = dbquery("SELECT b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '2200' AND monthend <= '$end' GROUP BY acct;");
	while($p = mysql_fetch_array($o)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$p['description'].'</td><td align=right>'.formatNumber($p['amount'],2).'</td></tr>';
		$opGT+=$p['amount'];
	}
	
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL OTHER CURRENT LIABILITIES</b></td><td align=right><b>------------------------<br/>'.formatNumber($opGT,2).'<br/>============</b></td></tr>';
	
	/* LONG TERM LIABILITIES */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>LONG TERM LIABILITIES</b></td></tr>';
	$q = dbquery("SELECT b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '2600' AND monthend <= '$end' GROUP BY acct;");
	while($r = mysql_fetch_array($q)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$r['description'].'</td><td align=right>'.formatNumber($r['amount'],2).'</td></tr>';
		$qrGT+=$r['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL LONG TERM LIABILTIES</b></td><td align=right><b>------------------------<br/>'.formatNumber($qrGT,2).'<br/>============</b></td></tr>';
	
	/*ADDITIONAL*/
	$add1 = getArray("SELECT ROUND(SUM(credit-debit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp in ('1650','4000','5000','6000','7000','8000') AND monthend <= '$end';");
	//$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>INCOME / LOSS FOR THE PERIOD</b></td></tr>';
	$html = $html . '<tr><td align=left style="padding-left: 20px;"><br/><b>INCOME / LOSS FOR THE PERIOD</b></td><td align=right><b>------------------------<br/>'.formatNumber($add1[0],2).'<br/>============</b></td></tr>';
		
	
	/* EQUITY */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>EQUITY</b></td></tr>';
	$s = dbquery("SELECT b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '3000' AND monthend <= '$end' GROUP BY acct;");
	while($t = mysql_fetch_array($s)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$t['description'].'</td><td align=right>'.formatNumber($t['amount'],2).'</td></tr>';
		$stGT+=$t['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL EQUITY</b></td><td align=right><b>------------------------<br/>'.formatNumber($stGT,2).'<br/>============</b></td></tr>';
	$html = $html . '<tr><td align="left"><br/><b>TOTAL LIABILTIES & EQUITY</b></td><td align=right><b>------------------------<br/>'.formatNumber(($mnGT+$opGT+$qrGT+$stGT+$add1[0]),2).'<br/>============</b></td></tr>';
	$html = $html . '
</table>
</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>