<?php
	session_start();
	ini_set("max_execution_time",0);
	
	//ini_set("display_errors","On");
	require_once "../lib/mpdf6/mpdf.php";
	require_once "../handlers/_generics.php";
	
	$mydb = new _init;
	
	//include("../includes/dbUSE.php");
	
	
	$mpdf=new mPDF('win-1252','folio-l','','',10,10,32,25,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);
	
	$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
	
	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$adesc = $mydb->getAcctDesc($_GET['source'],$_SESSION['company']);
		list($run) = $mydb->getArray("SELECT SUM(debit-credit) FROM acctg_gl WHERE acct = '$_GET[source]' AND doc_date < '".$mydb->formatDate($_GET['dtf'])."' and branch = '$_SESSION[branchid]' group by acct;");
		$query = $mydb->dbquery("SELECT cy, doc_no, CONCAT(cy,'-',LPAD(doc_no,6,0)) AS dno, DATE_FORMAT(doc_date,'%m/%d/%y') AS dd8, doc_type, debit, credit, debit-credit AS xamt, doc_remarks AS remarks FROM acctg_gl WHERE branch = '$_SESSION[branchid]' and doc_date BETWEEN '".$mydb->formatDate($_GET['dtf'])."' AND '".$mydb->formatDate($_GET['dt2'])."' AND acct = '$_GET[source]' ORDER BY doc_date ASC;");
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
			<span style="font-weight: bold; font-size: 9pt; color: #000000;">Cash Position Report</span><br /><span style="font-size: 6pt; font-style: italic;"><b>('.$_GET['source'].') '.$adesc.'</b><br/>Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
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
		<td width="7%" align=left><b>DOC #</b></td>
		<td width="5%" align=center><b>DATE</b></td>
		<td width="5%" align=center><b>TYPE</b></td>
		<td width="22%" align=left><b>PAYEE</b></td>
		<td width="5%" align=left><b>CHECK #</b></td>
		<td width="5%" align=left><b>CHECK DATE</b></td>
		<td width="7%" align=right><b>DEBIT</b></td>
		<td width="7%" align=right><b>CREDIT</b></td>
		<td width="10%" align=right><b>RUNNING BALANCE</b></td>
		<td align=left><b>MEMO</b></td>
	</tr>
</thead>
<tbody>
<tr><td colspan=4></td></tr>';

$html = $html . '<tr>
		<td align=left colspan=8><b>BALANCE FORWARDED FROM PREVIOUS PERIOD ................................................................................................................................</b></td>
		<td align=right><b>' . $mydb->formatNumber($run,2) . '</b></td>
		<td align=left><i>' . $memo . '</i></td>
	</tr>';

while($row = $query->fetch_array(MYSQLI_BOTH)) {
	switch($row['doc_type']) {
		case "CV":
			list($payee,$check_no,$check_date) = $mydb->getArray("select concat('(',payee,') ',payee_name) as payee, check_no, if(check_date!='0000-00-00',date_format(check_date,'%m/%d/%y'),'') as check_date from cv_header where cv_no = '$row[doc_no]' and branch = '$_SESSION[branchid]';");
		break;
		default :
			$payee='';$check_no='';$check_date='';
		break;
	}
	$run+=$row['xamt'];
	$html = $html . '<tr>
		<td align=left><b>' . $row['dno'] . '</b></td>
		<td align=center><b>' . $row['dd8'] . '</b></td>
		<td align=center>' . $row['doc_type'] . '</td>
		<td align=left>' . $payee . '</td>
		<td align=left>'. $check_no.'</td>
		<td align=left>'. $check_date.'</td>
		<td align=right>' . number_format($row['debit'],2) . '</td>
		<td align=right>' . number_format($row['credit'],2) . '</td>
		<td align=right>' . $mydb->formatNumber($run,2) . '</td>
		<td align=left><i>' . $row['remarks'] . '</i></td>
	</tr>';
}

$html = $html . '<tr>
					<td colspan=8></td>
					 <td align=right>-----------------------<br/><b>'.$mydb->formatNumber($run,2).'</b><br/>==========</td>
					 <td></td>
			     </tr>';
$html = $html . '</tbody></table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>