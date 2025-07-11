<?php
	session_start();
    //ini_set("display_errors","On");
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
	$_ihead = $con->getArray("SELECT *, LPAD(so_no,6,0) AS sono, LPAD(csi_no,6,0) AS csi_no, DATE_FORMAT(so_date,'%m/%d/%Y') AS d8, date_format(so_date,'%M %d') as date1, date_format(so_date,'%Y') as date2, IF(customer_code!=0,CONCAT('[',LPAD(customer_code,6,'0'),'] ',customer_name),CONCAT(customer_name,' WALK-IN CUSTOMER')) AS cname, customer_address, b.description AS terms_desc, c.fullname AS created_by FROM pharma_so_header a LEFT JOIN options_terms b ON a.terms = b.terms_id LEFT JOIN user_info c ON a.created_by = c.emp_id WHERE so_no = '$_REQUEST[so_no]' and branch = '$_SESSION[branchid]';");
	$_idetails = $con->dbquery("SELECT a.`code`,IF(qty>1,CONCAT(a.description,' (x',qty,')'),a.description) AS particulars,a.qty,a.unit_price,a.discount as disc,a.amount as total_amount,a.amount_due as discounted_amount, date_format(b.expiry_d8,'%m/%d/%Y') as expiry_d8 FROM pharma_so_details a left join pharma_master b on a.code = b.item_code WHERE so_no = '$_REQUEST[so_no]' and branch = '$_SESSION[branchid]';");
	
	list($nos,$stin,$isVat) = $con->getArray("select tel_no, tin_no, vatable from contact_info where file_id = '$_ihead[customer_code]';");
	
    list($tin,$bizstyle) = $con->getArray("select tin_no, bizstyle from contact_info where file_id = '$_ihead[customer_code]';");
	list($uname) = $con->getArray("select fullname from user_info where emp_id = '$_ihead[created_by]';");
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

$mpdf=new mPDF('win-1252','FOLIO-H','','',10,10,95,5,24,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
//$mpdf->SetProtection(array('print'));
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
        td { 
            vertical-align: top; 
        }
        .row {
            font-size:9pt;
            padding-left:65px;
        }
        .rowS {
            font-size:9pt;
            padding-left:30px;
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
    <table width="100%" cellpadding=0 cellspacing=0>';
	if($_ihead['csi_no'] != '') {
$html .='<tr>
            <td colspan=4 align=right class=row>REFERENCE NO. : &nbsp;'.$_ihead['csi_no'].'</td>
        </tr>';
	}else {
$html .='<tr>
            <td colspan=4 align=right class=row>&nbsp;</td>
        </tr>';
	}
$html .='<tr><td height=42></td></tr>
        <tr>';

		if($_ihead['patient_name'] != '') {

			$html .= '<td colspan=3 align=left class=row>'.$_ihead['customer_name'].' - '.$_ihead['patient_name'].'</td>
            <td align=right class=right-fix row-right>'.$_ihead['date1'].',&nbsp;'.$_ihead['date2'].'</td>';
		}else {

		$html .= '
            <td colspan=3 align=left class=row>'.$_ihead['customer_name'].'</td>
            <td align=right class=right-fix row-right>'.$_ihead['date1'].',&nbsp;'.$_ihead['date2'].'</td>';
		}
       $html .=' </tr>
        <tr><td height=5></td></tr>
        <tr>
            <td colspan=3 align=left class=row>'.$_ihead['customer_address'].'</td>
            <td align=right class=right-fix row-right>'.$_ihead['terms_desc'].'</td>
        </tr>
        <tr><td height=5></td></tr>
        <tr>
            <td colspan=3 align=left class=row>'.$tin.'</td>
            <td align=right class=right-fix row-right>'.$bizstyle.'</td>
        </tr>
        <tr><td height=40></td></tr>
    </table>
    <table class="items" width="100%" style="font-size: 10px; margin-top:10px; border-collapse: collapse;" cellpadding="3">
		<tbody>';
			$i = 0;
			while($row = $_idetails->fetch_array()) {
				$html .= '<tr>
						<td align=left width="10%">'.$row['code'].'</td>
						<td align=left>' . $row['particulars'] . '</td>
						<td align=right width="10%">' .$row['qty'] . '</td>
						<td align=right width="10%">' .$row['unit_price'] . '</td>
						<td align=right width="10%">' .$row['disc'] . '</td>
						<td align=right width="10%">' . number_format($row['total_amount'],2) . '</td>
						<td align=right width="10%">' . number_format($row['discounted_amount'],2) . '</td>
						</tr>'; $i++; $amtGT+=$row['amount_due'];
			}

			if($_ihead['scpwd_id'] != '' && $_p['age'] >= 60) {

				$scpwd = ROUND($amtGT * 0.20,2);
				$html .= '<tr>
								<td align=left colspan="2"><b>Less: Senior Citizen/PWD Discount</b></td>
								<td align=right>('.number_format($scpwd,2).')</td>
						  </tr>';
			} else { $scpwd = 0; }


			$html .= '<tr><td colspan=5 align=center>****************************** NOTHING FOLLOWS ******************************</td></tr>';
			for($i; $i <=3; $i++) {
				$html .= '<tr><td colspan=3>&nbsp;</td></tr>';
			}
			
			$html = $html .  '<tr>
						
					</tr>
		</tbody>
	</table>

</htmlpageheader>
<htmlpagefooter name="myfooter">
			<table width=100% height=68>
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
					<td align=left>&nbsp;</td>
					<td align=right>&nbsp;</td>
					<td align=right>&nbsp;</td>
				</tr>
				<tr>
					<td width=33% align=left>'.$_ihead['created_by'].'</td>
					<td align=center>'.$_ihead['scpwd_id'].'</td>
					<td align=right>&nbsp;</td>
					<td align=right>&nbsp;</td>
				</tr>
				<tr><td height=90></td></tr>
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