<?php
	session_start();
	
	require_once "../lib/mpdf6/mpdf.php";
	require_once "../handlers/_apfunct.php";
	
	$p = new myAP;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $p->getArray("select * from companies where company_id = '$_SESSION[company]';");
	$_ihead = $p->getArray("select apv_no, lpad(apv_no,6,0) as apv, date_format(apv_date,'%m/%d/%Y') as d8, supplier, supplier_name, supplier_addr, amount, vat, ewt_amount, remarks from apv_header where apv_no='$_REQUEST[apv_no]' and branch = '$_SESSION[branchid]';");
	$_idetails = $p->dbquery("select if(cost_center != '',concat(acct,'-',cost_center),acct) as acct, acct_desc, sum(debit) as debit, sum(credit) as credit from apv_details where apv_no = '$_REQUEST[apv_no]' and branch = '$_SESSION[branchid]' group by cost_center, acct order by debit, acct_desc;");
	list($nos, $stin) = $p->getArray("select tel_no, tin_no from contact_info where file_id = '$_ihead[supplier]';");
	$bcode = STR_PAD($_REQUEST[user],2,'0',STR_PAD_LEFT)."-AP".$_ihead['apv_no']."-".date('Ymd');
	

/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','letter','','',10,10,35,30,15,15);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
if($_GET['rePrint'] == "Y") {
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
	background-color: #EEEEEE;
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
<table width="100%"><tr>
<td style="color:#000000; padding-top: 15px;">
	<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$bit['tin_no'].'</span>
</td>
<td width="40%" align=right>
	<span style="font-weight: bold; font-size: 11pt; color: #000000;">ACCTS PAYABLE VOUCHER&nbsp;&nbsp;</span><br />
	<barcode size=0.8 code="'.substr($bcode,0,10).'" type="C128A">
</td>
</tr>
</table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100%><tr><td align=left width=25%><b>TRANSACTION REMARKS :</b><td align=left>'.$_ihead['remarks'].'</td></tr></table>
<table width=100% cellpadding=5 style="border: 1px solid #000000;">
<tr>
	<td width=33% align=center><b>PREPARED BY:</b><br><br>'.$p->getUname($_REQUEST[user]).'<br></td>
	<td width=33% align=center><b>CHECKED BY:</b><br><br>__________________________<br><font size=3>Print Name & Signature</font></td>
	<td width=34% align=center><b>APPROVED BY:</b><br><br>__________________________<br><font size=3>Print Name & Signature</font></td>
</tr>
</table>
<table width=100%>
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Run Date: '.date('m/d/Y h:i:s a').'</td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table width="100%" cellspacing=0 cellpadding=0>
<tr>
<td class="billto" width=60% rowspan="6"><b>PAYEE :</b><br /><br /><b>('.$_ihead['supplier'].') '.$_ihead['supplier_name'].'</b><br /><i>'.$_ihead['supplier_addr'].'<br><b>T-I-N: </b>'.$stin.'<br/><b>Contact #: </b>'.$nos.'</i></td>
<td class="td-l-top"><b>APV No.</b></td>
<td class="td-r-top">' . $_ihead['apv'] . '</td>
</tr>
<tr>
<td class="td-l-head"><b>APV Date</b></td>
<td class="td-r-head">' . $_ihead['d8'] . '</td>
</tr>
<tr>
<td class="td-l-head"><b>Gross Amount </b></td>
<td class="td-r-head">&#8369;' . number_format(($_ihead['ewt_amount']+$_ihead['amount']),2) . '</td>
</tr>
<tr>
<td class="td-l-head"><b>Input Taxes</b></td>
<td class="td-r-head">&#8369;' . number_format($_ihead['vat'],2) . '</td>
</tr>
<tr>
<td class="td-l-head"><b>Tax Withheld </b></td>
<td class="td-r-head">&#8369;' . number_format($_ihead['ewt_amount'],2) . '</td>
</tr>
<tr>
<td class="td-l-head-bottom"><b>Net Payable</b></td>
<td class="td-r-head-bottom"><b>&#8369;' . number_format($_ihead['amount'],2) . '</b></td>
</tr>
</table>
<table><tr><td height=20></td></tr></table>
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse;" cellpadding="3">
<thead>
<tr>
<td width="20%" align=left><b>ACCT CODE</b></td>
<td width="40%" align=left><b>ACCT DESCRIPTION</b></td>
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
		<td align="right">' . number_format($row['debit'],2) . '</td>
		<td align="right">' . number_format($row['credit'],2) . '</td>
		</tr>'; $dbGT+=$row['debit']; $crGT+=$row['credit'];
	}
	$html = $html . '<tr>
		<td align=left colspan=2 style=" border-top: 0.1mm solid #000000;"></td>
		<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($dbGT,2) . '</b></td>
		<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($crGT,2) . '</b></td>
		</tr>
	  </tbody>
	</table>';

	list($sh) = $p->getArray("select count(*) from apv_subheader where apv_no = '$_REQUEST[apv_no]' and branch = '$_SESSION[branchid]';");
	if($sh > 0) {
		$html = $html . '<table width=100% cellpadding=8 cellspacing=0>
			<tr>
				<td width="20%" align=left class="subdetail"><b>INV #</b></td>
				<td width="20%" align=left class="subdetail"><b>INV DATE</b></td>
				<td width="20%" align=right class="subdetail"><b>INV. AMOUNT</b></td>
				<td width="20%" align=right class="subdetail"><b>INPUT VAT</b></td>
				<td width="20%" align=right class="subdetail"><b>EWT</b></td>
				<td width="20%" align=right class="subdetail"><b>NET PAYABLE</b></td>
			</tr>';

		$ss = $p->dbquery("select invoice_no, date_format(invoice_date,'%m/%d/%Y') as inv_date, net_payable, ROUND(net_payable+ewt_amount,2) as gross, input_vat, ewt_amount, net_payable from apv_subheader where apv_no = '$_REQUEST[apv_no]' and branch = '$_SESSION[branchid]' order by invoice_date asc;");
		while($_s = $ss->fetch_array(MYSQLI_BOTH)) {
			$html = $html . '
				<tr>
				<td width="20%" align=left>'.$_s['invoice_no'].'</td>
				<td width="20%" align=left>'.$_s['inv_date'].'</td>
				<td width="20%" align=right>'.number_format($_s['gross'],2).'</td>
				<td width="20%" align=right>'.number_format($_s['input_vat'],2).'</td>
				<td width="20%" align=right>'.number_format($_s['ewt_amount'],2).'</td>
				<td width="20%" align=right>'.number_format($_s['net_payable'],2).'</td>
				</tr>
			'; $gGT+=$_s['gross']; $iGT+=$_s['input_vat']; $eGT+=$_s['ewt_amount']; $nGT+=$_s['net_payable'];
		}
			$html = $html . '<tr>
				<td style=" border-top: 0.1mm solid #000000;"></td>
				<td style=" border-top: 0.1mm solid #000000;"></td>
				<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($gGT,2) . '</b></td>
				<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($iGT,2) . '</b></td>
				<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($eGT,2) . '</b></td>
				<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($nGT,2) . '</b></td>
		</tr>
		<tr><td colspan=5>&nbsp;</td></tr>';
	} else {
		
		list($isE) = $p->getArray("select count(*) from apv_details where ref_no != '' and apv_no = '$_REQUEST[apv_no]' and branch = '$_SESSION[branchid]';");
		if($isE > 0) {
			$_ig = $p->dbquery("SELECT DISTINCT ref_no, date_format(ref_date,'%m/%d/%y') as ref_date FROM apv_details WHERE apv_no  = '$_REQUEST[apv_no]' and branch = '$_SESSION[branchid]' AND ref_no != '';");
			$html = $html . '<table width=100% cellpadding=8 cellspacing=0>
				<tr>
					<td width="10%" align=left class="subdetail"><b>INV #</b></td>
					<td width="10%" align=center class="subdetail"><b>INV DATE</b></td>
					<td width="8%" align=left class="subdetail"><b>RR #</b></td>
					<td width="8%" align=center class="subdetail"><b>RR DATE</b></td>
					<td width="8%" align=left class="subdetail"><b>PO #</b></td>
					<td width="8%" align=center class="subdetail"><b>PO DATE</b></td>
					<td width="12%" align=right class="subdetail"><b>AMOUNT</b></td>
					<td width="12%" align=right class="subdetail"><b>INPUT VAT</b></td>
					<td width="12%" align=right class="subdetail"><b>EWT</b></td>
					<td width="12%" align=center class="subdetail"><b>NET PAYABLE</b></td>
				</tr>';

			while(list($xap,$xdate) = $_ig->fetch_array(MYSQLI_BOTH)) {
				list($ewt) = $p->getArray("SELECT SUM(credit-debit) FROM apv_details WHERE apv_no = '$_REQUEST[apv_no]' and branch = '$_SESSION[branchid]' and ref_no = '$xap' AND acct IN ('2012') GROUP BY apv_no, cy;");
				list($input) = $p->getArray("SELECT SUM(debit-credit) FROM apv_details WHERE apv_no = '$_REQUEST[apv_no]' and branch = '$_SESSION[branchid]' and ref_no = '$xap' AND acct IN ('1401') GROUP BY apv_no, cy;");
				list($ap) = $p->getArray("SELECT SUM(credit-debit) FROM apv_details WHERE apv_no = '$_REQUEST[apv_no]' and branch = '$_SESSION[branchid]' and ref_no = '$xap' AND acct IN ('2001','2002') GROUP BY apv_no, cy;");
				list($rr_no,$rr_date,$po_no,$po_date) = $p->getArray("select distinct a.rr_no, date_format(b.rr_date,'%m/%d/%y') as rr_date, po_no, date_format(po_date,'%m/%d/%y') as po_date from rr_details a left join rr_header b on a.rr_no = b.rr_no and a.branch=b.branch where a.branch = '$_SESSION[branchid]' and b.invoice_no = '$xap' and supplier = '$_ihead[supplier]';");

				$html = $html . '
					<tr>
					<td align=left>'.$xap.'</td>
					<td align=center>'.$xdate.'</td>
					<td align=left>'.$rr_no.'</td>
					<td align=center>'.$rr_date.'</td>
					<td align=left>'.$po_no.'</td>
					<td align=center>'.$po_date.'</td>
					<td align=right>'.number_format(($ewt+$ap),2).'</td>
					<td align=right>'.number_format($input,2).'</td>
					<td align=right>'.number_format($ewt,2).'</td>
					<td align=right>'.number_format($ap,2).'</td>
					</tr>
				'; $ewtGT+=$ewt; $inputGT+=$input; $apGT+=$ap;
			}
			
			$html = $html . '<tr>
					<td style=" border-top: 0.1mm solid #000000;" colspan=6></td>
					<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format(($ewtGT+$apGT),2) . '</b></td>
					<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($inputGT,2) . '</b></td>
					<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($ewtGT,2) . '</b></td>
					<td align="right" style=" border-top: 0.1mm solid #000000;"><b>' . number_format($apGT,2) . '</b></td>
			</tr>
			<tr><td colspan=10>&nbsp;</td></tr>';
		}
	}

$html = $html . '</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>