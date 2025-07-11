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
	switch($_GET['type']) { 
		case "CR": $lbl = "Collection Receipts Journal Summary"; $f1 = "and a.doc_type = 'CR' "; break;
		case "SI": $lbl = "Sales Journal Summary"; $f1 = "and a.doc_type = 'SI' "; break;
		case "SI": $lbl = "Sales Journal (Sales Invoice)"; $f1 = "and a.doc_type = 'SI' "; break;
		case "POS": $lbl = "Sales Journal (Point of Sale)"; $f1 = "and a.doc_type = 'POS' "; break;
		case "CV": $lbl = "Cash/Check Disbursement Journal Summary"; $f1 = "and a.doc_type = 'CV' "; break;
		case "AP": $lbl = "Accounts Payable Journal Summary"; $f1 = "and a.doc_type = 'AP' "; break;
		case "JV": $lbl = "General (JV) Journal Summary"; $f1 = "and a.doc_type = 'JV' "; break;
		case "DA": $lbl = "Debit/Credit Advise Journal Summary"; $f1 = "and a.doc_type = 'JV' "; break;
		case "APB": $lbl = "Accounts Payable - Beginning Balance Summary"; $f1 = "and a.doc_type = 'APB' "; break;
		case "ARB": $lbl = "Accounts Receivable - Beginning Balance Summary"; $f1 = "and a.doc_type = 'ARB' "; break;
	}
	
	if($_GET['acct'] != '') { $f2 = " and a.acct = '$_GET[acct]' "; }
	if($_GET['conso'] != "Y") { $f3 = " and a.branch = '$_SESSION[branchid]' "; }

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
		$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
		$query = mysql_query("select a.acct, b.description, ROUND(sum(debit-credit),2) as amt from acctg_gl a left join acctg_accounts b on a.acct=b.acct_code and a.company=b.company where doc_date between '".formatDate($_GET['dtf'])."' and '".formatDate($_GET['dt2'])."' and a.company='$_SESSION[company]' $f1 $f2 $f3 group by a.acct;");
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
			<span style="font-weight: bold; font-size: 9pt; color: #000000;">'.$lbl.'&nbsp;&nbsp;</span><br /><span style="font-size: 6pt; font-style: italic;">Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
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
<table class="items" width="100%" align=center style="font-size: 8pt; border-collapse: collapse;" cellpadding="5">
<thead>
	<tr>
		<td width="20%" align=left><b>ACCT</b></td>
		<td width="40%" align=left><b>DESCRIPTION</b></td>
		<td width="20%" align=right><b>DEBIT</b></td>
		<td width="20%" align=right><b>CREDIT</b></td>
	</tr>
</thead>
<tbody>';

while($row = mysql_fetch_array($query)) {
	if($row['acct'] == "") { $acct = "--"; $desc = "UNCLASSIFIED ENTRY"; } else { $acct = $row['acct']; $desc = $row['description']; }
	if($row['amt'] > 0) { $db = $row['amt']; $cr = '0'; } else { $db = 0; $cr = abs($row['amt']); }
	$html = $html . '<tr>
		<td align=left>' . $acct . '</td>
		<td align=left>' . $desc . '</td>
		<td align=right>' . number_format($db,2) . '</td>
		<td align=right>' . number_format($cr,2) . '</td>
	</tr>'; $dbGT+=$db; $crGT+=$cr; 
}

$html = $html . '<tr>
					<td colspan=2></td>
					 <td align=right>-----------------------<br/><b>'.number_format($dbGT,2).'</b><br/>==========</td>
					 <td align=right>-----------------------<br/><b>'.number_format($crGT,2).'</b><br/>==========</td>
					 <td></td>
			     </tr>';
$html = $html . '</tbody></table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>