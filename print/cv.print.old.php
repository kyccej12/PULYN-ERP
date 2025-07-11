<?php
	session_start();
	ini_set("display_errors","On");

	include ("../lib/mpdf6/mpdf.php");
	include ("../handlers/_cvfunct.php");
	
	$p = new myCV;
	
	
	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = $p->getArray("select * from companies where company_id = '$_SESSION[company]';");
		$_ihead = $p->getArray("select cv_no, lpad(cv_no,6,0) as rr, date_format(cv_date,'%m/%d/%Y') as d8, payee, payee_name, payee_addr, amount,vat,ewt_amount,ROUND(amount+ewt_amount,2) as gross, check_no, if(check_date != '0000-00-00',date_format(check_date,'%m/%d/%Y'),'') as ckd8, remarks, b.tin_no, b.tel_no,if(check_no!='','CHECK','CASH') as xtitle from cv_header a left join contact_info b on a.payee=b.file_id where cv_no='$_REQUEST[cv_no]' and branch = '$_SESSION[branchid]';");
		$_idetails = $p->dbquery("SELECT IF(acct_branch!=branch,CONCAT(acct,'-',LPAD(acct_branch,2,0)),acct) AS acct, acct_desc,cost_center, SUM(debit) AS debit, SUM(credit) AS credit FROM cv_details WHERE cv_no = '$_GET[cv_no]' AND branch = '$_SESSION[branchid]' GROUP BY acct, cost_center, acct_branch order by debit, acct_desc;");
		$bcode = STR_PAD($_REQUEST[user],2,'0',STR_PAD_LEFT)."-CV".$_ihead['cv_no']."-".date('Ymd');

		list($digs,$fracs) = explode(".",$_ihead['amount']);
		if($fracs != '00') { $fracs = " & $fracs/100"; } else { $fracs = ""; }
		$word = $p->inWords($digs);
		
	/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','letter','','',8,8,80,80,15,15);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");

if($_REQUEST['rePrint'] == 'Y') {
	$mpdf->SetWatermarkText('Reprinted Copy');
	$mpdf->showWatermarkText = true;
}

$mpdf->SetDisplayMode(60);

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
	background-color: #EEEEEE;
    text-align: center;
}

.subdetail { 
	border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
    text-align: center;
}

.td-l { border-left: 0.1mm solid #000000; }
.td-r { border-right: 0.1mm solid #000000; }
.empty { border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; }

.items td.blanktotal {
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
    border-left: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}
.items td.tdTotals-r {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
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
		text-align: left;
		border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000;
		border-top: 0.1mm solid #000000;
	}
.td-r-top { 
	text-align: right; padding: 3px;
    border-right: 0.1mm solid #000000;
	border-top: 0.1mm solid #000000;
}

.td-l-head {
	text-align: left; font-weight: bold; padding: 3px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; background-color: #EEEEEE;
}

.td-r-head {
	text-align: right; padding: 3px;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000;
}
.td-l-head-bottom {
	text-align: left; padding: 3px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; background-color: #EEEEEE; border-bottom: 0.1mm solid #000000;
}

.td-r-head-bottom {
	text-align: right; padding: 3px;
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
<table width="100%">
	<tr>
		<td style="color:#000000;" width=80><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
		<td width="40%" align=right>
			<span style="font-weight: bold; font-size: 13pt; color: #000000;">'.$_ihead['xtitle'].' VOUCHER&nbsp;&nbsp;</span><br />
			<barcode size=0.8 code="'.$bcode.'" type="C128A">
		</td>
	</tr>
</table>
<table width="100%" cellspacing=0 cellpadding=0 style="font-size: 9pt;">
	<tr>
		<td class="billto" width=60% rowspan="6"><b>PAYEE :</b><br /><br /><b>('.$_ihead['payee'].') '.$_ihead['payee_name'].'</b><br /><i>'.$_ihead['payee_addr'].'<br><b>T-I-N: </b>'.$_ihead['tin_no'].'<br/><b>Contact #: </b>'.$_ihead['tel_no'].'</i><br/><br/>'.$project.'</td>
		<td class="td-l-top"><b>Page</b></td>
		<td class="td-r-top">{PAGENO} of {nb}</td>
	</tr>
	<tr>
		<td class="td-l-head"><b>CV No.</b></td>
		<td class="td-r-head">' . $_ihead['rr'] . '</td>
	</tr>
	<tr>
		<td class="td-l-head"><b>CV Date</b></td>
		<td class="td-r-head">' . $_ihead['d8'] . '</td>
	</tr>
	<tr>
		<td class="td-l-head"><b>Check No. </b></td>
		<td class="td-r-head">' . $_ihead['check_no'] . '</td>
	</tr>
	<tr>
		<td class="td-l-head"><b>Check Date</b></td>
		<td class="td-r-head">' . $_ihead['ckd8'] . '</td>
	</tr>
	<tr>
		<td class="td-l-head-bottom"><b>AMOUNT</b></td>
		<td class="td-r-head-bottom"><b>&#8369;' . number_format($_ihead['amount'],2) . '</b></td>
	</tr>
</table>
</htmlpageheader>
<htmlpagefooter name="myfooter">
<table width=100%>
	<tr><td height=20></td></tr>
	<tr><td align=left><b>MEMO : </b>'.$_ihead['remarks'].'</td>
	</tr>
</table>
<table width=100% cellpadding=5 style="border: 1px solid #000000;">
<tr>
	<td width=25% align=center><b>PREPARED BY:</b><br><br>'.$p->getUname($_REQUEST[user]).'<br></td>
	<td width=30% align=center><b>CHECKED BY:</b><br><br>_________________________<br><font size=3>Print Name Over Signature</font></td>
	<td width=20%  align=center><b>NOTED BY:</b><br><br>_________________________<br><font size=3>Print Name Over Signature</font></td>
	<td width=25% align=center><b>APPROVED BY:</b><br><br>_________________________<br><font size=3>Print Name Over Signature</font></td>
</tr>
</table>
<table width=100% cellpadding=5>
<tr><td height=20></td></tr>
<tr><td style="text-align: justify;">Received the amount <u><b>'.$word.' PESOS ' . $fracs . ' <i>(&#8369;'.number_format($_ihead['amount'],2).')</i></b></u> only as covered by <b>OR/CR No. </b>____________ dtd. _________________ <br/><br/><b>Received By: </b>_____________________________ <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name over Signature</td></tr>
</table>
<table width=100% style="font-size: 7pt;">
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Run Date: '.date('m/d/Y h:i:s a').'</td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse;" cellpadding="2">
<thead>
<tr>
<td width="15%" align=left><b>ACCT CODE</b></td>
<td width="40%" align=left><b>ACCT DESCRIPTION</b></td>
<td width="15%" align=left><b>COST CENTER</b></td>
<td width="20%" align=right><b>DEBIT</b></td>
<td width="20%" align=right><b>CREDIT</b></td>
</tr>
</thead>
<tbody>';
	$i = 0;
	while($row = $_idetails->fetch_array(MYSQLI_BOTH)) {
		if($row['ref_no'] != "") {
				if($oldref != $row['ref_no']) { $ref_no = $row['ref_no']; $ref_date = $row['ref_date'];  } else { $ref_no = ""; $ref_date = ""; }
		}
		$html = $html . '<tr>
		<td align=left>' . $row['acct'] . '</td>
		<td align=left>' . $row['acct_desc'] . '</td>
		<td align=left>' . $row['cost_center'] . '</td>
		<td align="right">' . number_format($row['debit'],2) . '</td>
		<td align="right">' . number_format($row['credit'],2) . '</td>
		</tr>'; $dbGT+=$row['debit']; $crGT+=$row['credit'];
	}
	$html = $html . '<tr>
		<td align=left colspan=3 style=" border-top: 0.1mm solid #000000;"></td>
		<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($dbGT,2) . '</b></td>
		<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($crGT,2) . '</b></td>
		</tr>
	  </tbody>
	</table>';

	$html = $html . '</table>';
	list($isE) = $p->getArray("SELECT COUNT(*) FROM cv_details WHERE branch = '$_SESSION[branchid]' AND cv_no = '$_GET[cv_no]' AND ref_type in ('AP','AP-BB');");
	if($isE > 0) {
		$html = $html . '<table width=100% style="font-size: 9pt; border-collapse: collapse;" cellpadding="1">
			<tr>
				<td width="10%" align=left class="subdetail"><b>APV #</b></td>
				<td width="10%" align=center class="subdetail"><b>DATE</b></td>
				<td width="25%" align=left class="subdetail"><b>APV REMARKS</b></td>
				<td width="15%" align=right class="subdetail"><b>AMOUNT</b></td>
				<td width="15%" align=right class="subdetail"><b>INPUT VAT</b></td>
				<td width="10%" align=right class="subdetail"><b>EWT</b></td>
				<td width="15%" align=right class="subdetail"><b>NET PAYABLE</b></td>
			</tr>';
		$_ig = $p->dbquery("select ref_no, date_format(ref_date,'%m/%d/%y') as ref_date, acct_branch, ref_type from cv_details where branch = '$_SESSION[branchid]' and cv_no = '$_GET[cv_no]' and ref_type in ('AP','AP-BB');");
		while($t = $_ig->fetch_array(MYSQLI_BOTH)) {
			if($t['ref_type'] == 'AP') {
				list($amount,$vat,$ewt,$net,$rem) = $p->getArray("select ROUND(amount+ewt_amount,2) as amount, vat, ewt_amount, amount as net, remarks from apv_header where apv_no = '$t[ref_no]' and branch = '$t[acct_branch]';");
			} else {
				list($amount,$vat,$ewt,$net,$rem) = $p->getArray("SELECT b.amount, 0 AS vat, 0 AS ewt, b.amount AS net, a.explanation FROM apbeg_header a LEFT JOIN apbeg_details b ON a.doc_no=b.doc_no AND a.branch=b.branch WHERE a.branch = '$t[acct_branch]' AND b.customer='$_ihead[payee]' AND b.invoice_no = '$t[ref_no]'");
			}
			$html = $html . '<tr>
								<td>'.$t['ref_no'].'</td>
								<td align=center>'.$t['ref_date'].'</td>
								<td align=left>'.$rem.'</td>
								<td align=right>'.number_format($amount,2).'</td>
								<td align=right>'.number_format($vat,2).'</td>
								<td align=right>'.number_format($ewt,2).'</td>
								<td align=right>'.number_format($net,2).'</td>
							</tr>'; $amtGT+=$amount; $vatGT+=$vat; $ewtGT+=$ewt; $netGT+=$net;
		}
		$html = $html . '<tr>
				<td style=" border-top: 0.1mm solid #000000;" colspan=3></td>
				<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format(($amtGT),2) . '</b></td>
				<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($vatGT,2) . '</b></td>
				<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($ewtGT,2) . '</b></td>
				<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($netGT,2) . '</b></td>
		</tr>
		<tr><td colspan=10>&nbsp;</td></tr>';
		$html = $html . '</table>';
	}
	
	
	list($isE) = $p->getArray("SELECT COUNT(*) FROM cv_details WHERE branch = '$_SESSION[branchid]' AND cv_no = '$_GET[cv_no]' AND ref_type in ('SI');");
	if($isE > 0) {
		$html = $html . '<table width=100% style="font-size: 9pt; border-collapse: collapse;" cellpadding="1">
			<tr>
				<td align=left class="subdetail"><b>VENDOR/SUPPLIER</b></td>
				<td width=10% align=left class="subdetail"><b>INV. #</b></td>
				<td width="10%" align=center class="subdetail"><b>INV. DATE</b></td>
				<td width="10%" align=right class="subdetail"><b>GROSS</b></td>
				<td width="10%" align=right class="subdetail"><b>INPUT VAT</b></td>
				<td width="10%" align=right class="subdetail"><b>EWT</b></td>
				<td width="15%" align=right class="subdetail"><b>NET AMOUNT</b></td>
			</tr>';
		$xx = $p->dbquery("SELECT supplier_name AS sname, invoice_no, DATE_FORMAT(invoice_date,'%m/%d/%y') AS id8, ROUND(net_payable+ewt_amount,2) AS gross, input_vat AS ivat, ewt_amount AS ewt, net_payable AS net FROM cv_subheader WHERE cv_no = '$_GET[cv_no]' AND branch = '$_SESSION[branchid]';");
		while($xy = $xx->fetch_array(MYSQLI_BOTH)) {
			$html .= '<tr>
								<td>'.$xy['sname'].'</td>
								<td>'.$xy['invoice_no'].'</td>
								<td align=center>'.$xy['id8'].'</td>
								<td align=right>'.number_format($xy['gross'],2).'</td>
								<td align=right>'.number_format($xy['ivat'],2).'</td>
								<td align=right>'.number_format($xy['ewt'],2).'</td>
								<td align=right>'.number_format($xy['net'],2).'</td>
							</tr>
			
			'; $igrossGT+=$xy['gross']; $ivatGT+=$xy['ivat']; $iewtGT+=$xy['ewt']; $inetGT+=$xy['net'];
		}
		
		
		$html = $html . '<tr>
				<td style=" border-top: 0.1mm solid #000000;" colspan=3></td>
				<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format(($igrossGT),2) . '</b></td>
				<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($ivatGT,2) . '</b></td>
				<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($iewtGT,2) . '</b></td>
				<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($inetGT,2) . '</b></td>
		</tr></table>';
	}
	
$html = $html . '</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>