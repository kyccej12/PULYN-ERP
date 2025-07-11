<?php
session_start();
ini_set("max_execution_time",0);
ini_set("memory_limit",-1);
include("../lib/mpdf6/mpdf.php");
include("../includes/dbUSE.php");

$mpdf=new mPDF('win-1252','folio-l','','',15,15,35,25,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(75);

	/* MYSQL QUERIES SECTION */
		$date = formatDate($_GET['asof']);
		$now = date("m/d/Y h:i a");

		$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
		$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]';");
		
		$fDates = mysql_fetch_array(mysql_query("select date_format('" . $date . "','%W %M %d, %Y') as date;"));
		$barcode = $branch.$cust.$soa_number;
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
		<td style="color:#000000;" width=80><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
		<td style="color:#000000; padding-top: 15px;" width=50%>
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
		</td>
		<td align=right><span style="font-weight: bold; font-size: 8pt;">Customer\'s Outstanding Invoices<br />Date As Of: '. $_GET['asof'] . '</span></td>
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
<table class="items" width="100%" style="font-size: 8pt; border-collapse: collapse;" cellpadding="5" border="1">
<thead>
<tr>
<td align=left><b>HOMEOWNER/TENNANT</b></td>
<td width="10%" align=center><b>UNIT</b></td>
<td width="8%" align=center><b>BILLING #</b></td>
<td width="8%" align=center><b>DATE</b></td>
<td width="8%" align=center><b>TERMS</b></td>
<td width="8%" align=center><b>DUE DATE</b></td>
<td width="8%" align=center><b>DAYS DUE</b></td>
<td width="8%" align=center><b>AMOUNT</b></td>
<td width="10%" align=center><b>AMOUNT PAID</b></td>
<td width="10%" align=center><b>BALANCE DUE&nbsp;</b></td>
</tr>
</thead>
<tbody>';
	//$str = "select * from (select lpad(a.doc_no,6,'0') as inv_no, invoice_date, date_format(invoice_date,'%m/%d/%Y') as inv_date, date_format(date_add(invoice_date,INTERVAL a.terms DAY),'%m/%d/%Y') as due, if('$date' <= date_add(invoice_date,INTERVAL a.terms DAY),'',datediff('$date',date_add(invoice_date,INTERVAL a.terms DAY))) as daysdue, b.description as terms, amount as gross_amount, balance, applied_amount as app_amount from invoice_header a left join options_terms b on a.terms=b.terms_id where invoice_date <= '$date' and a.customer = trim(leading from '$cust') and balance > 0 and a.status = 'Finalized' union all select lpad(b.invoice_no,6,'0') as inv_no, invoice_date, date_format(invoice_date,'%m/%d/%Y') as inv_date, date_format(date_add(invoice_date,INTERVAL c.terms DAY),'%m/%d/%Y') as due, if('$date' <= date_add(invoice_date,INTERVAL c.terms DAY),'',datediff('$date',date_add(invoice_date,INTERVAL c.terms DAY))) as daysdue, d.description as terms, amount as gross_amount, balance, applied_amount as app_amount from arbeg_header a left join arbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch left join contact_info c on b.customer=c.file_id left join options_terms d on c.terms=d.terms_id where invoice_date <= '$date' and b.customer = trim(leading from '$cust') and balance > 0 and a.status = 'Posted' order by invoice_date desc) a where 1=1 $fs1;";
	
	//$str = "SELECT a.customer, a.customer_name, a.doc_no as xdoc, LPAD(a.doc_no,6,'0') AS inv_no, invoice_date, DATE_FORMAT(invoice_date,'%m/%d/%Y') AS inv_date, DATE_FORMAT(DATE_ADD(invoice_date,INTERVAL a.terms DAY),'%m/%d/%Y') AS due, IF('$date' <= DATE_ADD(invoice_date,INTERVAL a.terms DAY),'',DATEDIFF('$date',DATE_ADD(invoice_date,INTERVAL a.terms DAY))) AS daysdue, b.description AS terms, amount AS gross_amount, balance, applied_amount AS app_amount FROM invoice_header a LEFT JOIN options_terms b ON a.terms=b.terms_id WHERE invoice_date <= '$date' AND balance > 0 AND a.status = 'Finalized' ORDER BY a.doc_no DESC;";
	if($_GET['cust'] != '') { $f1 = " and a.acctID = '$_GET[cust]' "; }
	if($_GET['od'] == 'Y') { $f3 = " and daysOverDue > 0 "; }
	$str = "SELECT * FROM (SELECT acctID, acctName, CONCAT(tower,'-',unit) AS unit, billingNo, billingDate, DATE_FORMAT(billingDate,'%m/%d/%Y') AS billDate, '30 Days' AS terms, DATE_FORMAT(DATE_ADD(billingDate, INTERVAL 30 DAY),'%m/%d/%Y') AS dueDate, DATEDIFF(NOW(),DATE_ADD(billingDate, INTERVAL 30 DAY)) AS daysOverDue, IF(DATEDIFF(NOW(),DATE_ADD(billingDate, INTERVAL 30 DAY)) < 0,'0',DATEDIFF(NOW(),DATE_ADD(billingDate, INTERVAL 30 DAY))) AS xdaysDue, balanceDue, amountPaid, balanceDue-amountPaid AS balance FROM billing WHERE `status` = 'Finalized' AND balanceDue > amountPaid) a WHERE 1=1 $f1 $f3 ORDER BY a.acctName, a.billingDate ASC;";
	
	
	$a = dbquery($str);
	while($row = mysql_fetch_array($a)) {
		
	$html = $html . '<tr>
			<td align="left">('.$row['acctID'].') ' . $row['acctName'] . '</td>
			<td align="center">' . $row['unit'] . '</td>
			<td align="center">' . $row['billingNo'] . '</td>
			<td align="center">' . $row['billDate'] . '</td>
			<td align="center">' . $row['terms'] . '</td>
			<td align="center">' . $row['dueDate'] . '</td>
			<td align="center">' . $row['daysdue'] . '</td>
			<td align=right>' . number_format($row['balanceDue'],2) . '</td>
			<td align="right">' . number_format($row['amountPaid'],2) . '</td>
			<td align="right">' . number_format($row['balance'],2) . '</td>
			</tr>'; $balanceGT+=$row['balance'];
	}

$html = $html . '<tr><td colspan=9 align=right><b>GRAND TOTAL &raquo;</b></td><td align=right><b>'. number_format($balanceGT,2) . '</b></td></tr>';
$html = $html . '
</tbody>
</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
unset($_SESSION['soa_ques']);
mysql_close($con);
?>