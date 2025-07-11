<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");
	ini_set("memory_limit","512M");
	ini_set("max_execution_time","0");

	

	$mpdf=new mPDF('win-1252','letter','','',10,10,32,20,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
		$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
		
		if($_GET['type'] != '') { $fs1 = " and b.item_code in (select item_code from products_master where rev_acct = '$_GET[type]' and company='$_SESSION[company]') "; }

		if($_SESSION['company'] == 1) {
			$query = mysql_query("SELECT b.item_code, b.description, b.unit, ROUND(SUM(b.qty),2) AS qty, ROUND(SUM(b.qty * cost),2) AS amount FROM invoice_header a LEFT JOIN invoice_details b ON a.invoice_no = b.invoice_no AND a.branch = b.branch AND a.company=b.company WHERE a.invoice_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' AND a.status = 'Finalized' and a.company = '$_SESSION[company]' and a.branch='$_SESSION[branchid]' $fs1 GROUP BY b.item_code, b.unit ORDER BY item_code ASC;");
		} else {
			$query = mysql_query("SELECT a.item_code, a.description, b.unit, SUM(qty) AS qty, SUM(amount) AS amount FROM (SELECT a.company, b.item_code, b.description, qty, ROUND(b.qty * cost,2) AS amount FROM invoice_header a LEFT JOIN invoice_details b ON a.invoice_no = b.invoice_no AND a.branch = b.branch AND a.company=b.company WHERE a.invoice_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' AND a.status = 'Finalized' AND a.company = '$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT a.company, b.item_code, b.description, qty,ROUND(b.qty * price,2) AS amount FROM pos_header a LEFT JOIN pos_details b ON a.tmpfileid = b.tmpfileid WHERE a.trans_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' AND a.status = 'Finalized' AND a.company = '$_SESSION[company]' AND a.branch='$_SESSION[branchid]') a LEFT JOIN products_master b ON a.company=b.company AND a.item_code=b.item_code $fs1 GROUP BY item_code ORDER BY a.description ASC;");
		}
	
	
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
		<td style="color:#000000;" width=150><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
		<td style="color:#000000; padding-top: 15px;">
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
		</td>
		<td width="40%" align=right>
			<span style="font-weight: bold; font-size: 9pt; color: #000000;">Sales Summary Report</span><br /><span style="font-size: 6pt; font-style: italic;">Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
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
		<td width="15%" align=left><b>ITEM CODE</b></td>
		<td width="40%" align=left><b>DESCRIPTION</b></td>
		<td width="10%" align=center><b>UNIT</b></td>
		<td width="15%" align=right><b>QTY SOLD</b></td>
		<td width="15%" align=right><b>AMOUNT</b></td>
		<td width="15%" align=right><b>AVG. PRICE</b></td>
	</tr>
</thead>
<tbody>';

while($row = mysql_fetch_array($query)) {
	$avg = ROUND($row['amount'] / $row['qty'],2);
	$html = $html . '<tr>
		<td align=left>' . $row['item_code'] . '</td>
		<td align=left>' . $row['description'] . '</td>
		<td align=center>' . $row['unit'] . '</td>
		<td align=right>' . number_format($row['qty'],2) . '</td>
		<td align=right>' . number_format($row['amount'],2) . '</td>
		<td align=right>' . number_format($avg,2) . '</td>
	</tr>'; $amtGT+=$row['amount']; $avg = 0;
}

$html = $html . '<tr>
					<td colspan=4></td>
					 <td align=right>----------------------------<br/><b>'.number_format($amtGT,2).'</b><br/>============</td>
					 <td></td>
			     </tr>
	</tbody>
</table>';

	if($_SESSION['company'] == 1) {
		$a = dbquery("select sum(qty) as sqty, sum(amount) as samount, rev_acct from (select b.item_code, sum(qty) as qty, ROUND(sum(qty*cost),2) as amount from invoice_header a left join invoice_details b on a.invoice_no = b.invoice_no and a.branch = b.branch and a.company = b.company where a.branch = '$_SESSION[branchid]' and a.company = '$_SESSION[company]' and a.status = 'Finalized' and a.invoice_date between '".formatDate($_GET['dtf'])."' and '".formatDate($_GET['dt2'])."' $fs1 group by b.item_code) a left join products_master b on a.item_code = b.item_code and b.company = '$_SESSION[company]' group by rev_acct;");				 
	} else {
		$a = dbquery("SELECT b.rev_acct, SUM(qty) AS sqty, SUM(amount) AS samount FROM (SELECT a.company, b.item_code, b.description, qty, ROUND(b.qty * cost,2) AS amount FROM invoice_header a LEFT JOIN invoice_details b ON a.invoice_no = b.invoice_no AND a.branch = b.branch AND a.company=b.company WHERE a.invoice_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' AND a.status = 'Finalized' AND a.company = '$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT a.company, b.item_code, b.description, qty,ROUND(b.qty * price,2) AS amount FROM pos_header a LEFT JOIN pos_details b ON a.tmpfileid = b.tmpfileid WHERE a.trans_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' AND a.status = 'Finalized' AND a.company = '$_SESSION[company]' AND a.branch='$_SESSION[branchid]') a LEFT JOIN products_master b ON a.company=b.company AND a.item_code=b.item_code GROUP BY b.rev_acct ORDER BY b.rev_acct;");
	}
	
	$html = $html . '<table class="items" width="60%" align=center style="font-size: 8pt; border-collapse: collapse;" cellpadding="3">
		<tr><td colspan=3 height=40></td></tr>
		<tr>
			<td width="30%" align=left class="lowerHeader"><b>SALES GROUP</b></td>
			<td width="15%" align=right class="lowerHeader"><b>QTY SOLD</b></td>
			<td width="15%" align=right class="lowerHeader"><b>AMOUNT</b></td>
		</tr>';
	while($b = mysql_fetch_array($a)) {
		if($b['rev_acct'] == "") { $acct = "UNCLASSIFIED SALES "; } else {
			list($acct) = getArray("select description from acctg_accounts where company = '$_SESSION[company]' and acct_code = '$b[rev_acct]';"); }
		$html = $html . '<tr>
			<td align=left>' . $acct . '</td>
			<td align=right>' . number_format($b['sqty'],2) . '</td>
			<td align=right>' . number_format($b['samount'],2) . '</td>
		</tr>'; $samtGT+=$b['samount'];
	}	
	$html = $html . '<tr>
		<td colspan=2></td>
		<td align=right>----------------------------<br/><b>'.number_format($samtGT,2).'</b><br/>============</td>
	  </tr>		 
</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>