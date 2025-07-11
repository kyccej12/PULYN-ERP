<?php
	ini_set("memory_limit","1024M");
	set_time_limit(0);
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");

	
	if(isset($_REQUEST['customer']) && $_REQUEST['customer'] != ''){
		$fs1 = " and a.customer = TRIM(LEADING 0 FROM '$_REQUEST[customer]') ";
	}

	
/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = getArray("select *,lcase(short_name) as bname from companies where company_id = '$_SESSION[company]';");
	$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
	$docs = dbquery("SELECT LPAD(a.so_no,5,0) AS xso, a.customer_name, DATE_FORMAT(a.so_date,'%m/%d/%y') AS sd8, b.item_code, b.description, b.unit, (qty-qty_dld) AS undld FROM so_header a INNER JOIN so_details b ON a.so_no = b.so_no AND a.branch = b.branch WHERE a.so_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' AND a.status = 'Finalized' $fs1;");
		
	
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','FOLIO','','',10,10,35,13,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(60);
//background-color: #EEEEEE;
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
<table width="100%"><tr>
<td style="color:#000000;" width=80><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
<td style="color:#000000; padding-top: 15px;">
	<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
</td>
<td width="40%" align=right><span style="font-size: 10px;">List of Unserved Sales Order<br/>Covered Period : ' .$_GET['dtf'] . ' - ' . $_GET['dt2'] . '</span></td>
</tr>
</table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100%  style="font-size: 7pt;">
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Run Date: '.date('m/d/Y h:i:s a').'</td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table  width="100%" style="font-size: 7pt; border-collapse: collapse;" cellpadding="3">
<thead>
<tr>
	<td width="10%" align=left><b>S.O. No.</b></td>
	<td width="10%" align=center><b>Date</b></td>
	<td width="25%" align=center><b>Customer</b></td>
	<td width="10%" align=center><b>Item</b></td>
	<td width="30%" align=center><b>Description</b></td>
	<td width="5%" align=center><b>UoM</b></td>
	<td width="10%" align=center><b>Unserved Qty</b></td>
</tr>
</thead>
<tbody>';
	$temp_branch =''; $temp_so = ''; $temp_date = ''; $temp_cust = '';
	while($row = mysql_fetch_array($docs)){

		$html .= '
		<tr>
			<td align=center>'.$row['xso'].'</td>
			<td align=center>'.$row['sd8'].'</td>
			<td align=left>'. $row['customer_name'] .'</td>
			<td align=left>'.$row['item_code'].'</td>
			<td align=left>'.$row['description'].'</td>
			<td align=left>'.$row['unit'].'</td>
			<td align=right>'.$row['qty'].'</td>
			</tr>
		';
	}
	
$html = $html . '
</tbody>
</table>
'.$ary.'

</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>