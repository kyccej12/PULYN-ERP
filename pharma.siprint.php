<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$_ihead = $con->getArray("SELECT *, LPAD(doc_no,6,0) AS docno, DATE_FORMAT(doc_date,'%m/%d/%Y') AS d8, date_format(doc_date,'%M %d') as date1, date_format(doc_date,'%Y') as date2, IF(customer_code!=0,CONCAT('[',LPAD(customer_code,6,'0'),'] ',customer_name),CONCAT(customer_name,' WALK-IN CUSTOMER')) AS cname, customer_address, b.description AS terms_desc FROM pharma_si_header a LEFT JOIN options_terms b ON a.terms = b.terms_id WHERE doc_no = '$_REQUEST[doc_no]' and branch = '$_SESSION[branchid]';");
	$_idetails = $con->dbquery("SELECT `code`,IF(qty>1,CONCAT(description),description) AS particulars,qty,unit_price,discount,amount_due,amount FROM pharma_si_details WHERE doc_no = '$_REQUEST[doc_no]' and branch = '$_SESSION[branchid]';");
	
	list($tin,$bizstyle) = $con->getArray("select tin_no, bizstyle from contact_info where file_id = '$_ihead[customer_code]';");
	
	/* AUDIT TRAIL PURPOSES */
	$con->dbquery("insert into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SI','SALES INVOICE # $_REQUEST[doc_no] WAS PRINTED BY USER','$_REQUEST[doc_no]');");
			
	/* Summary of Charges */

		$vatable = 0;
		$vat = 0;

		$amountDue = $_ihead['amount_due'];
		$scPwdDiscount = $_ihead['discount'];
		$gross = $_ihead['gross'];
		$vatable = ROUND($gross/1.12,2);
		$vat = ROUND($vatable * 0.12,2);

		if($_ihead['scpwd_id'] != '' && $age >= 60) {
			list($scdiscount) = $con->getArray("SELECT SUM(discount) FROM pharma_si_header WHERE doc_no = '$_REQUEST[doc_no]';");
			$scdiscount = ROUND($vatable * 0.20,2);
		}


/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','FOLIO-H','','',10,10,90,5,24,10);
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
.row {
	font-size:9pt;
	padding-left:60px;
}
.row-right {
	font-size:9pt;
	padding-right: 30px;
}
.right-fix {
	font-size:9pt;
}
.nside {
 font-size:7pt;
}
.nside-row {
	margin-right:10px;
}
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%" cellpadding=0 cellspacing=0>
	<tr>
		<td colspan=4 align=right class=row>REFERENCE NO. :'.$_ihead['docno'].'</td>
	</tr>
	<tr><td height=30></td></tr>
	<tr>
		<td colspan=2 align=left class=row>'.$_ihead['customer_name'].'</td>
		<td align=center class=right-fix>'.$_ihead['date1'].',&nbsp;'.$_ihead['date2'].'</td>
	</tr>
	<tr><td height=5></td></tr>
	<tr>
		<td width=60% class=row>'.$_ihead['customer_address'].'</td>
		<td width=25% align=left class=right-fix>'.$tin.'</td>
		<td align=right class=right-fix>'.$bizstyle.'</td>
	</tr>
	<tr><td height=40></td></tr>
</table>

<table class="items" width="100%" style="font-size: 11px; margin-top:10px; border-collapse: collapse;" cellpadding="3">
		<tbody>';
			$i = 0;
			while($row = $_idetails->fetch_array()) {
				$html .= '<tr>
							<td width=10% align=left>'.$row['code'].'</td>
							<td width=40% align=left>' . $row['particulars'] . '</td>
							<td width=10% align=right>' .$row['qty'] . '</td>
							<td width=10% align=right>' .$row['unit_price'] . '</td>
							<td width=10% align=right>' . number_format($row['discount'],2) . '</td>
							<td width=10% align=right>' .$row['amount'] . '</td>
							<td width=10% align=right>' .$row['discount'] . '</td>
						<td></td>
						</tr>'; $i++;
			}

			if($_ihead['scpwd_id'] != '' && $_p['age'] >= 60) {

				$scpwd = ROUND($amtGT * 0.20,2);
				$html .= '<tr>
								<td align=left colspan="2"><b>Less: Senior Citizen/PWD Discount</b></td>
								<td align=right>('.number_format($scpwd,2).')</td>
						  </tr>';
			} else { $scpwd = 0; }


			$html .= '<tr><td colspan=3 align=center class=nside>****************************** NOTHING FOLLOWS ******************************</td></tr>';
			for($i; $i <=3; $i++) {
				$html .= '<tr><td colspan=3>&nbsp;</td></tr>';
			}
			
			$html = $html .  '<tr>
						
					</tr>
		</tbody>
	</table>

</htmlpageheader>

			<htmlpagefooter name="myfooter">
			<table width=100% height=70>
				<tr>
					<td width=33.3%>&nbsp;</td>
					<td width=33.3%>&nbsp;</td>
					<td width=33.3% align=right>'.number_format($_ihead['net'],2).'</td>
				</tr>
				<tr>
					<td width=33.3% align=left style="padding-left:120px;">'.number_format($vatable,2).'</td>
					<td width=33.3% align=right>'.number_format($vat,2).'</td>
					<td width=33.3% align=right>'.number_format($_ihead['discount'],2).'</td>
				</tr>
				<tr>
					<td width=33.3% align=left style="padding-left:120px;">'.number_format($_ihead['gross'],2).'</td>
					<td width=33.3% align=right>'.number_format($_ihead['gross'],2).'</td>
					<td width=33.3% align=right>'.number_format($_ihead['amount_due'],2).'</td>
				</tr>
				<tr>
					<td width=33.3% align=left style="padding-left:120px;">'.number_format($_ihead['zero_rated'],2).'</td>
					<td width=33.3% align=right>0.00</td>
					<td width=33.3% align=right>0.00</td>
				</tr>
				<tr>
					<td width=33.3%>&nbsp;</td>
					<td width=33.3%>&nbsp;</td>
					<td width=33.3% align=right>&nbsp;</td>
				</tr>
				<tr><td></td></tr>
				<tr>
					<td align=left>&nbsp;</td>
					<td align=right>&nbsp;</td>
					<td align=right>&nbsp;</td>
				</tr>
				<tr>
					<td width=33% align=left>'.$con->getUname($_SESSION['userid']).'</td>
					<td align=center>'.$_ihead['scpwd_id'].'</td>
					<td align=right>&nbsp;</td>
					<td align=right>&nbsp;</td>
				</tr>
				<tr><td height=90></td></tr>
				<tr><td height=12></td></tr>
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