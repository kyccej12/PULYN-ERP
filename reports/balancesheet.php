<?php
	
	session_start();
	ini_set("max_execution_time",0);
	require_once "../lib/mpdf6/mpdf.php";
	require_once "../handlers/_generics.php";
	
	$mydb = new _init;
	
	//include("../includes/dbUSE.php");

	$mpdf=new mPDF('win-1252','letter','','',15,15,35,25,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Solutions");
	$mpdf->SetDisplayMode(60);

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$dtf = "2018-12-31";
		list($dt2,$ending) = $mydb->getArray("select last_day('$_GET[year]-$_GET[month]-01'),date_format(last_day('$_GET[year]-$_GET[month]-01'),'%M %Y');");
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
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
		<td align=right><br/><span style="font-weight: bold; font-size: 8pt;">BALANCE SHEET<br/><i>Ending '.$ending.'</i></span></td>
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
	$a = $mydb->dbquery("SELECT b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct = b.acct_code WHERE b.acct_grp = '1' AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	while($b = $a->fetch_array(MYSQLI_BOTH)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$b['description'].'</td><td align=right>'.$mydb->formatNumber($b['amount'],2).'</td></tr>';
		$abGT+=$b['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL CASH & CASH EQUIVALENTS</b></td><td align=right><b>------------------------<br/>'.$mydb->formatNumber($abGT,2).'<br/>============</b></td></tr>';
	
	/* Accounts Receivable*/
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>ACCOUNTS RECEIVABLE</b></td></tr>';
	$c = $mydb->dbquery("SELECT b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp in ('2') AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	while($d = $c->fetch_array(MYSQLI_BOTH)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$d['description'].'</td><td align=right>'.$mydb->formatNumber($d['amount'],2).'</td></tr>';
		$cdGT+=$d['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL ACCOUNTS RECEIVABLE</b></td><td align=right><b>------------------------<br/>'.$mydb->formatNumber($cdGT,2).'<br/>============</b></td></tr>';
	

	/* Properties & Equipment */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>PROPERTIES & EQUIPMENT</b></td></tr>';
	$g = $mydb->dbquery("SELECT b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '3' AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	while($h= $g->fetch_array(MYSQLI_BOTH)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$h['description'].'</td><td align=right>'.$mydb->formatNumber($h['amount'],2).'</td></tr>';
		$ghGT+=$h['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL PROPERTIES & EQUIPMENT</b></td><td align=right><b>------------------------<br/>'.$mydb->formatNumber($ghGT,2).'<br/>============</b></td></tr>';
	
	/* OTHER CURRENT & NON-CURRENT ASSETS */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>OTHER CURRENT & NON-CURRENT ASSETS</b></td></tr>';
	$k = $mydb->dbquery("SELECT b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp in ('4','5') AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	while($l= $k->fetch_array(MYSQLI_BOTH)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$l['description'].'</td><td align=right>'.$mydb->formatNumber($l['amount'],2).'</td></tr>';
		$klGT+=$l['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL OTHER CURRENT & NON-CURRENT ASSETS</b></td><td align=right><b>------------------------<br/>'.$mydb->formatNumber($klGT,2).'<br/>============</b></td></tr>';
	$html = $html . '<tr><td align="left"><br/><b>TOTAL ASSETS</b></td><td align=right><b>------------------------<br/>'.$mydb->formatNumber(($abGT+$cdGT+$efGT+$ghGT+$ijGT+$klGT),2).'<br/>============</b></td></tr>';
	
	/* Liabilities & EQUITIES */
	$html = $html . '<tr><td align="left" colspan=2 style="border-bottom: 0.1mm solid black;"><b>LIABILITIES & EQUITY</b></td></tr>';
	
	/* CURRENT LIABILITIES */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>CURRENT LIABILITIES</b></td></tr>';
	$m = $mydb->dbquery("SELECT b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '6' AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	while($n = $m->fetch_array(MYSQLI_BOTH)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$n['description'].'</td><td align=right>'.$mydb->formatNumber($n['amount'],2).'</td></tr>';
		$mnGT+=$n['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL CURRENT LIABILITIES</b></td><td align=right><b>------------------------<br/>'.$mydb->formatNumber($mnGT,2).'<br/>============</b></td></tr>';
	
	/* OTHER CURRENT LIABILITIES */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>OTHER CURRENT LIABILITIES</b></td></tr>';
	$o = $mydb->dbquery("SELECT b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp in ('7') AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	while($p = $o->fetch_array(MYSQLI_BOTH)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$p['description'].'</td><td align=right>'.$mydb->formatNumber($p['amount'],2).'</td></tr>';
		$opGT+=$p['amount'];
	}
	
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL OTHER CURRENT LIABILITIES</b></td><td align=right><b>------------------------<br/>'.$mydb->formatNumber($opGT,2).'<br/>============</b></td></tr>';
	
	/*ADDITIONAL*/
	$add1 = $mydb->getArray("SELECT ROUND(SUM(credit-debit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp in ('9','10','11','12','13','14') AND doc_date BETWEEN '$dtf' AND '$dt2';");
	$html = $html . '<tr><td align=left style="padding-left: 20px;"><br/><b>INCOME / LOSS FOR THE PERIOD</b></td><td align=right><b>------------------------<br/>'.$mydb->formatNumber($add1[0],2).'<br/>============</b></td></tr>';
		
	
	/* EQUITY */
	$html = $html . '<tr><td align="left" colspan=2 style="padding-left: 10px;"><b>EQUITY</b></td></tr>';
	$s = $mydb->dbquery("SELECT b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '8' AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	while($t = $s->fetch_array(MYSQLI_BOTH)) {
		$html = $html . '<tr><td align=left style="padding-left: 20px;">'.$t['description'].'</td><td align=right>'.$mydb->formatNumber($t['amount'],2).'</td></tr>';
		$stGT+=$t['amount'];
	}
	$html = $html . '<tr><td align="left" style="padding-left: 10px;"><br/><b>TOTAL EQUITY</b></td><td align=right><b>------------------------<br/>'.$mydb->formatNumber($stGT,2).'<br/>============</b></td></tr>';
	$html = $html . '<tr><td align="left"><br/><b>TOTAL LIABILTIES & EQUITIES</b></td><td align=right><b>------------------------<br/>'.$mydb->formatNumber(($mnGT+$opGT+$qrGT+$stGT+$add1[0]),2).'<br/>============</b></td></tr>';
	$html = $html . '
</table>
</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>