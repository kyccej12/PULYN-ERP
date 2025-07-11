<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");
	ini_set("memory_limit","4096M");
	ini_set("max_execution_time","0");

	$mpdf=new mPDF('win-1252','letter-l','','',10,10,32,20,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);
	switch($_GET['type']) { 
		case "CR": $lbl = "Collection Receipts Journal"; $f1 = "and a.doc_type = 'CR' "; break;
		case "SI": $lbl = "Sales Journal (Sales Invoice)"; $f1 = "and a.doc_type = 'SI' "; break;
		case "POS": $lbl = "Sales Journal (Point of Sale)"; $f1 = "and a.doc_type = 'POS' "; break;
		case "CV": $lbl = "Cash/Check Disbursement Journal"; $f1 = "and a.doc_type = 'CV' "; break;
		case "AP": $lbl = "Accounts Payable Journal"; $f1 = "and a.doc_type = 'AP' "; break;
		case "JV": $lbl = "General (JV) Journal"; $f1 = "and a.doc_type = 'JV' "; break;
		case "DA": $lbl = "Debit/Credit Advise Journal"; $f1 = "and a.doc_type = 'JV' "; break;
		case "APB": $lbl = "Accounts Payable - Beginning Balance"; $f1 = "and a.doc_type = 'APB' "; break;
		case "ARB": $lbl = "Accounts Receivable - Beginning Balance"; $f1 = "and a.doc_type = 'ARB' "; break;
	}
	
	if($_GET['acct'] != '') { $f2 = " and a.acct = '$_GET[acct]' "; }

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
		$query = mysql_query("SELECT doc_no, CONCAT(LPAD(branch,2,0),'-',LPAD(doc_no,5,0)) AS dno, DATE_FORMAT(doc_date,'%m/%d/%Y') AS dd8, a.acct, b.description, debit, credit, IF(doc_type IN ('SOA','CR'),CONCAT(d.lname,', ',d.fname,' (',d.tower,'-',d.tower_unit,')'),IFNULL(CONCAT('(',LPAD(contact_id,3,0),') ',c.tradename),'')) AS tradename, contact_id, CONCAT(a.branch,'-',doc_no) AS xdoc, doc_remarks FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code LEFT JOIN contact_info c ON a.contact_id=c.file_id LEFT JOIN homeowners d ON a.contact_id = d.record_id WHERE doc_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' $f1 $f2 $f3 order by doc_no asc;");
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
		<td width="8%" align=left><b>DOC #</b></td>
		<td width="8%" align=center><b>DATE</b></td>
		<td width="20%" align=left><b>CLIENT</b></td>
		<td width="20%" align=left><b>GL ACCOUNT</b></td>
		<td width="12%" align=right><b>DEBIT</b></td>
		<td width="12%" align=right><b>CREDIT</b></td>
		<td width="20%" align=left><b>MEMO</b></td>
	</tr>
</thead>
<tbody>';

while($row = mysql_fetch_array($query)) {
	if($xdoc != $row['xdoc']) { $memo = $row['doc_remarks']; $dno = $row['dno']; $d8 = $row['dd8']; $cust = $row['tradename']; } else { $memo = ""; $dno = ""; $d8 = ""; $cust = ""; }
	$html = $html . '<tr>
		<td align=left><b>' . $dno . '</b></td>
		<td align=center><b>' . $d8 . '</b></td>
		<td align=left><b>' . $cust . '</b></td>
		<td align=left>('.$row['acct'].') ' . $row['description'] . '</td>
		<td align=right>' . number_format($row['debit'],2) . '</td>
		<td align=right>' . number_format($row['credit'],2) . '</td>
		<td align=left><i>' . $memo . '</i></td>
	</tr>'; $dbGT+=$row['debit']; $crGT+=$row['credit']; $xdoc = $row['xdoc'];
}

$html = $html . '<tr>
					<td colspan=4></td>
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