<?php
	ini_set("memory_limit","1024M");
	set_time_limit(0);
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");


	
	$dtf = formatDate($_REQUEST['dtf']);
	$dt2 = formatDate($_REQUEST['dt2']);
	
	$xd = " between '$dtf' and '$dt2' ";
	
	if(isset($_REQUEST['customer']) && $_REQUEST['customer'] != ''){
		$custFilter = " and a.customer = TRIM(LEADING 0 FROM '$_REQUEST[customer]') ";
	}
	
	if(isset($_REQUEST['dtf']) && $_REQUEST['dtf'] != '' && isset($_REQUEST['dt2']) && $_REQUEST['dt2'] != '' ){
		$dateFilter = " and a.so_date between '$dtf' and '$dt2' ";
	}
	
	
/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = getArray("select *,lcase(short_name) as bname from companies where company_id = '$_SESSION[company]';");
	$ccode = $co['dbase'];
	$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
	//$bcode = STR_PAD($_REQUEST['user'],2,'0',STR_PAD_LEFT)."-SI".$_ihead['invoice_no']."-".date('Ymd');
	//$ary = "SELECT a.branch,CONCAT(a.branch,'-',LPAD(a.so_no,6,0)) as so_no,so_date,a.customer,a.customer_name,b.item_code,b.description,b.qty FROM sjpi.so_header a INNER JOIN sjpi.so_details b ON a.so_no = b.so_no AND a.company = b.company AND a.branch = b.branch WHERE (b.qty - b.qty_dld) >0  $custFilter $dateFilter ORDER BY a.so_no,b.description;";
	//$docs = dbquery("SELECT a.branch,CONCAT(a.branch,'-',LPAD(a.so_no,6,0)) as so_no,so_date,a.customer,a.customer_name,b.item_code,b.description,b.qty FROM $ccode.so_header a INNER JOIN $ccode.so_details b ON a.so_no = b.so_no AND a.company = b.company AND a.branch = b.branch WHERE (b.qty - b.qty_dld) >0  $custFilter $dateFilter ORDER BY a.so_no,b.description;");
	$docs = dbquery("SELECT c.branch_name, lpad(a.so_no,7,0) so_no,so_date,a.customer,a.customer_name,b.item_code,b.description,b.qty FROM $ccode.so_header a INNER JOIN $ccode.so_details b ON a.so_no = b.so_no AND a.company = b.company AND a.branch = b.branch INNER JOIN options_branches c ON a.branch = c.branch_code AND a.company = c.company WHERE (b.qty - b.qty_dld) >0  $custFilter $dateFilter ORDER BY a.so_no,customer,b.description;");
		
	
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','FOLIO','','',15,15,25,13,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");

$mpdf->SetDisplayMode(60);
//background-color: #EEEEEE;
$html = '
<html>
<head>
<style>
body {
	font-family: arial;
	font-size: 10pt;
 }
td { vertical-align: top; }

table thead td { 
	border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
	
    text-align: center;
}

.td-l { border-left: 0.1mm solid #000000; }
.td-r { border-right: 0.1mm solid #000000; }
.empty { border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; }

.items td.blanktotal {
    background-color: #FFFFFF;
    border: 0.1mm solid #000000;
}
.items td.totals-l-top {
    text-align: right; font-weight: bold;
    border-left: 0.1mm solid #000000;
	border-top: 0.1mm solid #000000;
}
.items td.totals-r-top {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000;
	border-top: 0.1mm solid #000000;
}
.items td.totals-l {
    text-align: right; font-weight: bold;
    border-left: 0.1mm solid #000000;
}
.items td.totals-r {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000;
}

.items td.tdTotals-l {
    text-align: left; font-weight: bold;
    border-left: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;  background-color: #EEEEEE;
}
.items td.tdTotals-r {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000; background-color: #EEEEEE;
}

.items td.tdTotals-l-1 {
    text-align: left;
    border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}
.items td.tdTotals-r-1 {
    text-align: right;
    border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}

.td-l-top { 	
		background-color: #EEEEEE; padding: 3px;
		text-align: left; font-weight: bold;
		border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000;
		border-top: 0.1mm solid #000000;
	}
.td-r-top { 
	text-align: right; font-weight: bold; padding: 3px;
    border-right: 0.1mm solid #000000;
	border-top: 0.1mm solid #000000;
}

.td-l-head {
	text-align: left; font-weight: bold; padding: 3px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; background-color: #EEEEEE;
}

.td-r-head {
	text-align: right; font-weight: bold; padding: 3px;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000;
}
.td-l-head-bottom {
	text-align: left; font-weight: bold; padding: 3px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; background-color: #EEEEEE; border-bottom: 0.1mm solid #000000;
}

.td-r-head-bottom {
	text-align: right; font-weight: bold; padding: 3px;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}

.billto {
	font-size: 12px; vertical-align: top; padding: 3px;
}
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%"><tr>
<td style="color:#000000;"><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
<td style="color:#000000; padding-top: 15px;">
	<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
</td>
<td width="40%" align=right>
	
</td>
</tr>
</table>
<br>
<div style="font-size:12pt;font-weight:bold;" align=center>List of Unserved Sales Order</div>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100%  style="font-size: 7pt;">
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Run Date: '.date('m/d/Y h:i:s a').'</td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table  width="100%" style="font-size: 7pt; border-collapse: collapse;" cellpadding="3" border=1>
<thead>
<tr>
<td width="10%" align=center><b>Branch</b></td>
<td width="10%" align=left><b>S.O. No.</b></td>
<td width="10%" align=center><b>Date</b></td>
<td width="25%" align=center><b>Customer</b></td>
<td width="10%" align=center><b>Item</b></td>
<td width="25%" align=center><b>Description</b></td>
<td width="10%" align=center><b>Qty</b></td>
</tr>
</thead>
<tbody>';
	$temp_branch =''; $temp_so = ''; $temp_date = ''; $temp_cust = '';
	while($row = mysql_fetch_array($docs)){
		
		if($temp_branch=='' && $temp_so==''){
				$temp_branch = $row['branch_name'];
				$temp_so = $row['so_no'];
				$temp_date = $row['so_date'];
				$temp_cust = $row['customer'];
				$temp_name = $row['customer_name'];
				
				$mask_branch = $row['branch_name'];
				$mask_so = $row['so_no'];
				$mask_date = $row['so_date'];
				$mask_cust = $row['customer'];
				$mask_name = $row['customer_name'];
		}else{
			if($temp_branch!=$row['branch_name'] || $temp_so!=$row['so_no']){
				$temp_branch = $row['branch_name'];
				$temp_so = $row['so_no'];
				$temp_date = $row['so_date'];
				$temp_cust = $row['customer'];
				$temp_name = $row['customer_name'];
				
				$mask_branch = $row['branch_name'];
				$mask_so = $row['so_no'];
				$mask_date = $row['so_date'];
				$mask_cust = $row['customer'];
				$mask_name = $row['customer_name'];
			}else{
				$mask_branch = '';
				$mask_so = '';
				$mask_date = '';
				$mask_cust = '';
				$mask_name = '';
			}
		}
		
		$html .= '
		<tr>
			<td width="10%" align=left>'.$mask_branch.'</td>
			<td width="10%" align=center>'.$mask_so.'</td>
			<td width="10%" align=center>'.$mask_date.'</td>
			<td width="25%" align=left>'.$mask_cust.' - '.$mask_name.'</td>
			<td width="10%" align=left>'.$row[item_code].'</td>
			<td width="25%" align=left>'.$row[description].'</td>
			<td width="10%" align=right>'.$row[qty].'</td>
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