<?php
//ini_set("display_errors","yes");
session_start();
include("../lib/mpdf6/mpdf.php");
include("../includes/dbUSE.php");

$mpdf=new mPDF('win-1252','letter','','',15,15,60,25,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(75);

	/* MYSQL QUERIES SECTION */
		$date = date("Y-m-d");
		$cust = $_GET['cid'];
		$now = date("m/d/Y h:i a");
		$barcode = $branch.$cust.$_REQUEST['soa_no'];
		
		if($_REQUEST['with_soa_num'] == "Y") { 
			list($soa_number) = getArray("SELECT LPAD((soa_number+1),2,0) FROM soa_series;"); 
			dbquery("update soa_series set soa_number=soa_number+1");
		}
		
		$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
		$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");

		$c = getArray("SELECT tradename, CONCAT(address,', ',b.city,', ',c.province) AS address FROM contact_info a LEFT JOIN options_cities b ON a.city=b.city_id LEFT JOIN erp_commong.options_provinces c ON a.province=c.province_id WHERE a.file_id = '$cust'");
		$fDates = mysql_fetch_array(mysql_query("select date_format('" . $date . "','%W %M %d, %Y') as date;"));
	
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
		<td width="70%" style="color:#000000;">
			<img src="../images/headerlogo.jpg" /><br /><span style="color: #3b3b3b; font-size: 7pt; font-style: italic;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Emilio Yuipco St., Kaskag Village, Surigao City, Philippines<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tel. No.: (086) 826-0367; Fax: (086) 826-2531<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email: cityhomebasic@gmail.com</span>
		</td>
		<td align=right><barcode code="'.$barcode.'" type="C128A"><br/><br/><b>SOA No. :</b> &nbsp;&nbsp;<font color=red><b><i>'.$soa_number.'</i></b></font>&nbsp;&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr><td height=20></td></tr>
	<tr><td colspan=2 align=center><span style="font-weight: bold; font-size: 8pt;">STATEMENT OF ACCOUNT<br />Date As Of: '. $fDates[0] . '</span></td></tr>
	<tr><td height=20></td></tr>
</table>
<table width=100%>
<tr><td width=15%  style="font-weight: bold; font-size: 9pt;">Customer :</td><td  style="font-weight: bold; font-size: 9pt;" width=85%><b>' . '(' . $cust . ') ' .$c['tradename'] . '</b></td></tr>
<tr><td width=15% style="font-weight: bold; font-size: 9pt;">Address  :</td><td style="font-weight: bold; font-size: 9pt;"><b>' . $c['address'] . '</b></td></tr>
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
<table class="items" width="100%" style="font-size: 8pt; border-collapse: collapse;" cellpadding="8" border="1">
<thead>
<tr>
<td width="10%" align=center><b>DOC NO</b></td>
<td width="10%" align=center><b>DATE</b></td>
<td width="10%" align=center><b>TERMS</b></td>
<td width="10%" align=center><b>PO NO</b></td>
<td width="20%" align=center><b>INVOICE AMOUNT</b></td>
<td width="20%" align=right><b>AMOUNT PAID</b></td>
<td width="20%" align=right><b>BALANCE&nbsp;</b></td>
</tr>
</thead>
<tbody>';


	foreach($_SESSION['iques'] as $ino) {
		$row = mysql_fetch_array(mysql_query("select lpad(ci_no,6,'0') as inv_no, date_format(invoice_date,'%m/%d/%Y') as inv_date, customer_name as cust, customer_addr, b.description as terms, amount as gross_amount, balance, applied_amount as app_amount from ci_header a left join options_terms b on a.terms=b.terms_id where invoice_no='$ino' and a.customer='$cust' order by invoice_date desc, invoice_no desc;"));
		if($_REQUEST["with_soa_num"] == "Y") {
			mysql_query("insert into soa (soa_no,soa_date,ccode,customer_name,customer_add,invoice_no,po_no,invoice_date,terms,amount,applied_amount,balance) values ('$soa_number','$date','$cust','".mysql_real_escape_string($row['cust'])."','".mysql_real_escape_string($row['customer_address'])."','$row[inv_no]','$row[po_no]','$row[inv_date]','$row[terms]','$row[gross_amount]','$row[app_amount]','$row[balance]');");
		}
		
		$html = $html . '<tr>
		<td align="center">' . $row['inv_no'] . '</td>
		<td align="center">' . $row['inv_date'] . '</td>
		<td align="center">' . $row['terms'] . '</td>
		<td align="center">' . $row['po_no'] . '</td>
		<td align=right>' . number_format($row['gross_amount'],2) . '</td>
		<td align="right">' . number_format($row['app_amount'],2) . '</td>
		<td align="right">' . number_format($row['balance'],2) . '</td>
		</tr>'; $balanceGT+=$row['balance'];
	}
$html = $html . '<tr><td colspan=6 align=right><b>GRAND TOTAL &raquo;</b></td><td align=right>'. number_format($balanceGT,2) . '</td></tr>';
$html = $html . '
</tbody>
</table>
<table with=100% border=0 cellpadding=0 cellspacing=0>
	<tr><td colspan=2 width=100%><i><b>Note:</b> 3% monthly interest for overdue account</i></td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr><td width=50%><b>Prepared By:</b></td><td style="padding-right: 40px;"><b>Received By:</b></td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr><td>_____________________________________________</td><td style="padding-right: 40px;">_____________________________________________</td></tr>
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