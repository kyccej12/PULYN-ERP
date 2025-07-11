<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");
	ini_set("memory_limit","2048M");
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
		list($cname) = getArray("select tradename from contact_info where file_id = '$_REQUEST[cid]';");
		if($_GET['cid'] != "") { $f1 = " and a.customer = '$_GET[cid]' "; }
		
		if($_SESSION['company'] == 1) {
			$query = mysql_query("SELECT 'SI' as xtype, a.invoice_date, date_format(invoice_date,'%m/%d/%y') as id8, a.invoice_no, b.item_code, b.description, b.unit, b.qty AS qty, cost as price, ROUND(b.qty * cost,2) AS amount FROM invoice_header a LEFT JOIN invoice_details b ON a.invoice_no = b.invoice_no AND a.branch = b.branch AND a.company=b.company WHERE a.invoice_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' AND a.status = 'Finalized' AND a.company = '$_SESSION[company]' AND a.branch='$_SESSION[branchid]' $f1;");
		} else {
			$query = mysql_query("SELECT * FROM (SELECT 'SI' AS xtype, a.invoice_date, DATE_FORMAT(invoice_date,'%m/%d/%y') AS id8, a.invoice_no, b.item_code, b.description, b.unit, b.qty AS qty, cost as price, ROUND(b.qty * cost,2) AS amount FROM invoice_header a LEFT JOIN invoice_details b ON a.invoice_no = b.invoice_no AND a.branch = b.branch AND a.company=b.company WHERE a.invoice_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' AND a.status = 'Finalized' AND a.company = '$_SESSION[company]' AND a.branch = '$_SESSION[branchid]' UNION ALL SELECT 'POS', a.trans_date AS invoice_date, DATE_FORMAT(trans_date,'%m/%d/%y') AS id8, a.trans_id AS invoice_no, b.item_code, b.description, '' AS unit, b.qty AS qty, price, ROUND(b.qty * price,2) AS amount FROM pos_header a LEFT JOIN pos_details b ON a.tmpfileid = b.tmpfileid WHERE a.trans_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' AND a.status = 'Finalized' AND a.company = '$_SESSION[company]' AND a.branch='$_SESSION[branchid]' ORDER BY invoice_date ASC, invoice_no ASC) a WHERE 1=1 $f1;");
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
		<td style="color:#000000; padding-top: 15px;">
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
		</td>
		<td width="40%" align=right>
			<span style="font-weight: bold; font-size: 9pt; color: #000000;">Customer Detailed Sales Report</span><br />('.$_REQUEST['cid'].') '.$cname.'<span style="font-size: 6pt; font-style: italic;">Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
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
		<td width="12%" align=left><b>TRANS #</b></td>
		<td width="12%" align=left><b>DATE</b></td>
		<td width="10%" align=left><b>ITEM CODE</b></td>
		<td width="25%" align=left><b>DESCRIPTION</b></td>
		<td width="5%" align=center><b>UNIT</b></td>
		<td width="12%" align=right><b>QTY</b></td>
		<td width="12%" align=right><b>PRICE</b></td>
		<td width="12%" align=right><b>AMOUNT</b></td>
	</tr>
</thead>
<tbody>';

while($row = mysql_fetch_array($query)) {
	if($row['invoice_no']!=$xno) { $ino = $row['xtype'].'-'.$row['invoice_no']; $idate = $row['id8']; $cname = '('.$row['customer'].') ' . $row['customer_name']; } else { $ino = ""; $idate =''; $cname = ""; }
	if($row['qty'] > 0) {
		if($row['unit'] == "") {
			list($unit) = getArray("select unit from products_master where item_code = '$row[item_code]' and company = '$_SESSION[company]';");
			$row['unit'] = $unit;
		}
		
		$html = $html . '<tr>
			<td align=left>'. $ino . '</td>
			<td align=left>' . $idate . '</td>
			<td align=left>' . $row['item_code'] . '</td>
			<td align=left>' . $row['description'] . '</td>
			<td align=center>' . $row['unit'] . '</td>
			<td align=right>' . number_format($row['qty'],2) . '</td>
			<td align=right>' . number_format($row['price'],2) . '</td>
			<td align=right>' . number_format($row['amount'],2) . '</td>
		</tr>'; $amtGT+=$row['amount']; $xno = $row['invoice_no'];
	}
}

$html = $html . '<tr>
					<td colspan=7></td>
					 <td align=right>--------------------<br/><b>'.number_format($amtGT,2).'</b><br/>=========</td>
					 <td></td>
			     </tr>
	</tbody>
</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>