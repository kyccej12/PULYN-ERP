<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
	$_ihead = $con->getArray("SELECT *, LPAD(so_no,6,0) AS sono, DATE_FORMAT(so_date,'%m/%d/%Y') AS d8, IF(customer_code!=0,CONCAT('[',LPAD(customer_code,6,'0'),'] ',customer_name),CONCAT(customer_name,' WALK-IN CUSTOMER')) AS cname, customer_address, b.description AS terms_desc, c.fullname AS created_by FROM pharma_so_header a LEFT JOIN options_terms b ON a.terms = b.terms_id LEFT JOIN user_info c ON a.created_by = c.emp_id WHERE so_no = '$_REQUEST[so_no]' and branch = '$_SESSION[branchid]';");
	$_idetails = $con->dbquery("SELECT `code`,IF(qty>1,CONCAT(description,' (x',qty,')'),description) AS particulars,qty,unit_price,amount_due,amount FROM pharma_so_details WHERE so_no = '$_REQUEST[so_no]' and branch = '$_SESSION[branchid]';");
	$bcode = $_ihead['trace_no'];
	
	list($nos,$stin,$isVat) = $con->getArray("select tel_no, tin_no, vatable from contact_info where file_id = '$_ihead[customer_code]';");
	
	/* AUDIT TRAIL PURPOSES */
	$con->dbquery("insert into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SO','SALES ORDER # $_REQUEST[so_no] WAS PRINTED BY USER','$_REQUEST[so_no]');");
			
	/* Summary of Charges */

		$vatable = 0;
		$vat = 0;

		$amountDue = $_ihead['amount_due'];
		$scPwdDiscount = $_ihead['discount'];
		$gross = $_ihead['gross'];
		$vatable = ROUND($gross/1.12,2);
		$vat = ROUND($vatable * 0.12,2);

		if($_ihead['scpwd_id'] != '' && $age >= 60) {
			list($scdiscount) = $con->getArray("select sum(discount) from pharma_so_details where so_no = '$_REQUEST[so_no]';");
			$scdiscount = ROUND($vatable * 0.20,2);
		}


/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','FOLIO-H','','',10,10,90,30,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");

if($_REQUEST['rePrint'] == 'Y') {
	$mpdf->SetWatermarkText('Reprinted Copy');
	$mpdf->showWatermarkText = true;
}

$mpdf->SetDisplayMode(50);

$html = '
<html>
<head>
<style>
body {font-family: sans-serif; font-size: 10px; }
td { vertical-align: top; }

table thead td { 
	border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
	/* background-color: #EEEEEE; */
    text-align: center;
}

.td-l { border-left: 0.1mm solid #000000; }
.td-r { border-right: 0.1mm solid #000000; }
.empty { border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; }

.items td.blanktotal {
    /* background-color: #FFFFFF; */
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
    border-left: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;  /* background-color: #EEEEEE; */
}
.items td.tdTotals-r {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000; /* background-color: #EEEEEE; */
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
		/* background-color: #EEEEEE; */ padding: 5px;
		text-align: left; font-weight: bold;
		border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000;
		border-top: 0.1mm solid #000000;
	}
.td-r-top { 
	text-align: right; font-weight: bold; padding: 5px;
    border-right: 0.1mm solid #000000;
	border-top: 0.1mm solid #000000;
}

.td-l-head {
	text-align: left; font-weight: bold; padding: 5px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; /* background-color: #EEEEEE; */
}

.td-r-head {
	text-align: right; font-weight: bold; padding: 5px;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000;
}
.td-l-head-bottom {
	text-align: left; font-weight: bold; padding: 5px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; /* background-color: #EEEEEE; */ border-bottom: 0.1mm solid #000000;
}

.td-r-head-bottom {
	text-align: right; font-weight: bold; padding: 5px;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}

.billto {
	font-size: 12px; vertical-align: top; padding: 3px;
}
.row {
	font-size:10pt;
}
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%" cellpadding=0 cellspaing=0>
	<tr>
		<td width=75><img src="../images/logo-small.png" width=64 height=64 align=absmiddle></td>
		<td style="color:#000000; padding-top: 5px;" valign=top>
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>NON-VAT REG. TIN: '.$bit['tin_no'].'</span>
		</td>
		<td width="40%" align=right>
			<span style="font-weight: bold; font-size: 13pt; color: #000000;">SERVICE ORDER&nbsp;&nbsp;</span><br />
			<barcode size=0.8 code="'.substr($bcode,0,10).'" type="C128A">
		</td>
	</tr>
</table>
<table width="100%" cellspacing=0 cellpadding=0>

	<tr>
		<td class="billto" width=60% rowspan="5">
		<b><br/>SOLD TO :</b><br /><br /><b>'.$_ihead['patient_name'].'</b><br/><span style="font-size: 7pt;"><br>COMPANY NAME&nbsp;:&nbsp;<b>'.$_ihead['customer_name'].'</b><br>ADDRESS&nbsp;:&nbsp;<b>'.$_ihead['patient_address'].'</b><br/>TEL&nbsp;NOS.&nbsp;:&nbsp;<b>'.$nos.'</b><br/>PHYSICIAN&nbsp;:&nbsp;<b>'.$_ihead['physician'].'</b><br />SC/PWD&nbsp;ID&nbsp;:&nbsp;<b>'.$_ihead['scpwd_id'].'</b></span></td>
	</tr>
	<tr>
		<td class="td-l-head"><b>SO No.</b></td>
		<td class="td-r-head"><b>' . $_ihead['sono'] . '</b></td>
	</tr>

	<tr>
		<td class="td-l-head"><b>S.O Date</b></td>
		<td class="td-r-head"><b>' . $_ihead['d8'] . '</b></td>
	</tr>
	<tr>
		<td class="td-l-head"><b>Terms</b></td>
		<td class="td-r-head"><b>' . $_ihead['terms_desc'] . '</b></td>
	</tr>
	<tr>
		<td class="td-l-head-bottom"><b>Amount Due</b></td>
		<td class="td-r-head-bottom"><b>&#8369;' . number_format($_ihead['amount_due'],2) . '</b></td>
	</tr>
</table>

<table class="items" width="100%" style="font-size: 12px; margin-top:10px; border-collapse: collapse;" cellpadding="3">
		<thead>
			<tr>
				<td width="25%" style="background-color: #cdcdcd;" align=left><b>CODE</b></td>
				<td width="70%" style="background-color: #cdcdcd;" align=left><b>PARTICULARS</b></td>
				<td width="15%" style="background-color: #cdcdcd;" align=left><b>QTY</b></td>
				<td width="15%" style="background-color: #cdcdcd;" align=left><b>UNIT&nbsp;PRICE</b></td>
				<td width="15%" style="background-color: #cdcdcd;" align=right><b>AMOUNT</b></td>
			</tr>
		</thead>
		<tbody>';
			$i = 0;
			while($row = $_idetails->fetch_array()) {
				$html .= '<tr>
						<td align=left class="row">'.$row['code'].'</td>
						<td align=left class="row">' . $row['particulars'] . '</td>
						<td align=left class="row">' .$row['qty'] . '</td>
						<td align=left class="row">' .$row['unit_price'] . '</td>
						<td align=right class="row">' . number_format($row['amount'],2) . '</td>
						</tr>'; $i++; $amtGT+=$row['amount_due'];
			}

			if($_ihead['scpwd_id'] != '' && $_p['age'] >= 60) {

				$scpwd = ROUND($amtGT * 0.20,2);
				$html .= '<tr>
								<td align=left colspan="2"><b>Less: Senior Citizen/PWD Discount</b></td>
								<td align=right>('.number_format($scpwd,2).')</td>
						  </tr>';
			} else { $scpwd = 0; }


			$html .= '<tr><td colspan=3 align=center>*********************************** NOTHING FOLLOWS ***********************************</td></tr>';
			for($i; $i <=3; $i++) {
				$html .= '<tr><td colspan=3>&nbsp;</td></tr>';
			}
			
			$html = $html .  '<tr>
						
					</tr>
		</tbody>
	</table>

</htmlpageheader>

<htmlpagefooter name="myfooter">

<table class="items" width="100%" style="font-size: 8px; margin-bottom:5px; border-collapse: collapse;" cellpadding="3">
		<tr>
			<td colspan=2 style="background-color: #cdcdcd; border-bottom: 0.1mm solid #000000;" align=left><b>TOTAL SALES&nbsp;:</b><b>&nbsp;&nbsp;'.number_format($gross,2).'</b></td>
		
			<td colspan=2 style="background-color: #cdcdcd; border-bottom: 0.1mm solid #000000;" align=left><b>VATABLE SALES&nbsp;:</b><b>&nbsp;&nbsp;'.number_format($vatable,2).'</b></td>
			<td colspan=2 style="background-color: #cdcdcd; border-bottom: 0.1mm solid #000000;" align=right><b>V-A-T&nbsp;:</b><b>&nbsp;&nbsp;'.number_format($vat,2).'<b></td>
			<td colspan=2 style="background-color: #cdcdcd; border-bottom: 0.1mm solid #000000;" align=right><b>SC/PWD DISCOUNT&nbsp;:</b><b>&nbsp;&nbsp;'.number_format($scPwdDiscount,2).'</b></td>
			<td colspan=2 style="background-color: #cdcdcd; border-bottom: 0.1mm solid #000000;" align=right><b>AMOUNT DUE&nbsp;:</b><b>&nbsp;&nbsp;'.number_format($amountDue,2).'</b></td>
		</tr>

</table>

<table width=100% cellpadding=5 style="border: 1px solid #000000;">
	<tr>
		<td width=33% align=center><b>PREPARED BY:</b><br><br>'.$_ihead['created_by'].'<br></td>
		<td align=center><b>ACKNOWLEDGED BY:</b><br><br>_______________________<br><font size=2>Signature Over Printed Name</font></td>
	</tr>
</table>
<table width=100%>
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Run Date: '.date('m/d/Y h:i:s a').'</td></tr>
	<tr><td colspan=2 align=center><b> **** THIS DOCUMENT IS NOT VALID FOR INPUT TAX CLAIM ****</b></td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />

	mpdf-->
</body>
</html>
';

$html = html_entity_decode($html);
$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>