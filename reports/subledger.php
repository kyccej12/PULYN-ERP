<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");
	
	
	$mpdf=new mPDF('win-1252','legal-l','','',10,10,32,25,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);
	
	$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
	$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");

	$prefixAcct = substr($_GET['acct_code'],0,3);
	
	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$adesc = getAcctDesc($_GET['acct'],$_SESSION['company']);
		list($cname) = getArray("select tradename from contact_info where file_id = trim(LEADING '0' from '$_GET[cid]');");
		list($run) = getArray("SELECT SUM(debit-credit) FROM acctg_gl WHERE contact_id = trim(leading '0' from '$_GET[cid]') and acct = '$_GET[acct]' AND doc_date < '".formatDate($_GET['dtf'])."' group by acct;");
		$query = mysql_query("SELECT cy, doc_no, LPAD(doc_no,6,0) AS dno, DATE_FORMAT(doc_date,'%m/%d/%Y') AS dd8, doc_type, debit, credit, ref_no, date_format(ref_date,'%m/%d/%Y') as ref_date, ref_type, debit-credit AS xamt, doc_remarks AS remarks FROM acctg_gl WHERE contact_id = trim(leading '0' from '$_GET[cid]') AND doc_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' AND acct = '$_GET[acct_code]' ORDER BY doc_date ASC;");
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
			<span style="font-weight: bold; font-size: 9pt; color: #000000;">ACCOUNT SUBLEDGER</span><br /><span style="font-size: 6pt; font-style: italic;"><b>('.$_GET['acct'].') '.$adesc.'</b><br/>Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'<br/><br><b>('.$_GET['cid'].') '.$cname.'</b></span>
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
		<td width="5%" align=left><b>DOC #</b></td>
		<td width="10%" align=center><b>DATE</b></td>
		<td width="10%" align=center><b>TYPE</b></td>
		<td width="10%" align=right><b>DEBIT</b></td>
		<td width="10%" align=right><b>CREDIT</b></td>
		<td width="10%" align=right><b>RUNNING BALANCE</b></td>
		<td width="10%" align=center><b>REF #</b></td>
		<td width="10%" align=center><b>REF DATE</b></td>
		<td width="25%" align=left><b>MEMO</b></td>
	</tr>
</thead>
<tbody>
<tr><td colspan=4></td></tr>';

$html = $html . '<tr>
		<td align=left colspan=5><b>BALANCE FORWARDED FROM PREVIOUS PERIOD</b></td>
		<td align=right><b>' . number_format($run,2) . '</b></td>
		<td align=left><i>' . $memo . '</i></td>
	</tr>';

while($row = mysql_fetch_array($query)) {
	$run+=$row['xamt'];
	
	switch($prefixAcct) {
		case "200":
			if($row['debit'] > 0) {
				$ref_no = $row['ref_type'] . "-" . $row['ref_no'];
				$ref_date = $row['ref_date'];
			}
		break;
		default:
			$ref_no = "";
			$ref_date = "";
		break;
	}
	
	
	
	$html = $html . '<tr>
		<td align=left>' . $row['dno'] . '</td>
		<td align=center>' . $row['dd8'] . '</td>
		<td align=center>' . $row['doc_type'] . '</td>
		<td align=right>' . number_format($row['debit'],2) . '</td>
		<td align=right>' . number_format($row['credit'],2) . '</td>
		<td align=right>' . number_format($run,2) . '</td>
		<td align=center>' .  $ref_no . '</td>
		<td align=center>' .  $ref_date . '</td>
		<td align=left><i>' . $row['remarks'] . '</i></td>
	</tr>';
	$ref_no = ''; $ref_date = '';
}

$html = $html . '<tr>
					<td colspan=5></td>
					 <td align=right>-----------------------<br/><b>'.number_format($run,2).'</b><br/>==========</td>
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