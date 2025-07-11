<?php
	session_start();
	//ini_set("display_errors","On");
	require_once("../lib/mpdf6/mpdf.php");
	require_once("../handlers/_generics.php");
	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
	
	$_ihead = $con->getArray("select cv_no, lpad(cv_no,8,0) as rr, date_format(cv_date,'%m/%d/%Y') as d8, payee, payee_name, payee_addr, ROUND(amount,2) as amount,vat,ewt_amount,ROUND(amount+ewt_amount,2) as gross, check_no, if(check_date != '0000-00-00',date_format(check_date,'%m/%d/%Y'),'') as ckd8, remarks, b.tin_no, b.tel_no,if(check_no!=0,'CHECK','CASH') as xtitle, source, a.created_by from cv_header a left join contact_info b on a.payee = b.file_id where cv_no='$_REQUEST[cv_no]' and branch = '$_SESSION[branchid]';");
	$_idetails = $con->dbquery("SELECT ref_no as doc_no,ref_type,CONCAT(ref_type,'-',LPAD(ref_no,6,0)) AS ref_no,IF(cost_center!='',concat(acct,'-',cost_center),acct) AS acct, acct_desc, SUM(debit) AS debit, SUM(credit) AS credit,cost_center FROM cv_details WHERE cv_no = '$_GET[cv_no]' AND branch = '$_SESSION[branchid]' GROUP BY ref_no,acct,acct_branch order by debit desc,credit desc;");
	$bcode = STR_PAD($_REQUEST[user],2,'0',STR_PAD_LEFT)."-".$_ihead['cv_no']."-".date('Ymd');
	
	list($digs,$fracs) = explode(".",$_ihead['amount']);
	list($bank) = $con->getArray("SELECT description FROM acctg_accounts WHERE acct_code = '$_ihead[source]';");
	if($fracs != '00') { $fracs = " & $fracs/100"; } else {	$fracs ='';}
	$word = $con->inWords($digs);
	
	$cheques = $con->dbquery("SELECT check_no,DATE_FORMAT(check_date,'%m/%d/%Y') check_date, amount FROM cv_header WHERE cv_no = '$_ihead[cv_no]' AND branch = '$_SESSION[branchid]';");
	
	list($uname) = $con->getArray("select fullname from user_info where emp_id = '$_ihead[created_by]';");

	
	switch($_GET['size']) {
		case "2": $paper = "letter"; break;
		case "3": $paper = "folio"; break;
		default: $paper = array(216,140);
	}

	$mpdf=new mPDF('win-1252',$paper,'','',10,10,45,40,2,2);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Solutions");
	$mpdf->shrink_tables_to_fit=1;
	if($_REQUEST['reprint'] == 'Y') {
		$mpdf->SetWatermarkText('REPRINTED COPY');
		$mpdf->showWatermarkText = true;
	}

	$mpdf->SetDisplayMode(100);

	$html = '
	<html>
	<head>
	<style>
	body {
		font-family: arial;
		font-size: 8pt;
	 }
	td { vertical-align: top; }

	table thead td { 
		border-top: 0.1mm solid #000000;
		border-bottom: 0.1mm solid #000000;
		/*  background-color: #EEEEEE; */
		text-align: center;
	}

	.subdetail { 
		border-top: 0.1mm solid #000000;
		border-bottom: 0.1mm solid #000000;
		/*  background-color: #EEEEEE; */
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
		border-left: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;  /*  background-color: #EEEEEE; */
	}
	.items td.tdTotals-r {
		text-align: right; font-weight: bold;
		border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000; /*  background-color: #EEEEEE; */
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
			/*  background-color: #EEEEEE; */ padding: 2px;
			text-align: left;
			border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000;
			border-top: 0.1mm solid #000000;
		}
	.td-r-top { 
		text-align: right; padding: 2px;
		border-right: 0.1mm solid #000000;
		border-top: 0.1mm solid #000000;
	}

	.td-l-head {
		text-align: left; font-weight: bold; padding: 2px; padding-left:5px;
		border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; /*  background-color: #EEEEEE; */
	}

	.td-r-head {
		text-align: right; padding: 2px;
		border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000;
	}
	.td-l-head-bottom {
		text-align: left; padding: 2px; padding-left:5px;
		border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; /*  background-color: #EEEEEE; */ border-bottom: 0.1mm solid #000000;
	}

	.td-r-head-bottom {
		text-align: right; padding: 2px;
		border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
	}

	.billto {
		font-size: 12px; vertical-align: top; padding: 5px;
	}
	</style>
	</head>
	<body>

	<!--mpdf
	<htmlpageheader name="myheader">
	<table width="100%">
		<tr>
			<td style="color:#000000; padding-top: 15px;">
				<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$bit['tin_no'].'</span>
			</td>
			<td width="40%" align=right>
				<span style="font-weight: bold; font-size: 11pt; color: #000000;">CHECK VOUCHER&nbsp;&nbsp;</span><br />
				<barcode size=0.8 code="'.substr($bcode,0,10).'" type="C128A">
			</td>
		</tr>
	</table>
	<table width="100%" cellspacing=0 cellpadding=0>
		<tr>
			<td class="billto" width=60% rowspan="6"><b>PAYEE :</b><br /><br/><span style="font-weight: bold; font-size: 14pt;"><b> '.utf8_decode(html_entity_decode($_ihead['payee_name'])).'</b></span><br />'.utf8_decode(html_entity_decode($_ihead['payee_addr'])).'</td>
			<td class="td-l-head" colspan=2><b>CV No.</b></td>
			<td class="td-r-head">' . $_ihead['rr'] . '</td>
		</tr>
		<tr>
			<td class="td-l-head" colspan=2><b>CV Date</b></td>
			<td class="td-r-head">' . $_ihead['d8'] . '</td>
		</tr>
		<tr>
			<td class="td-l-head-bottom" colspan=2><b>AMOUNT</b></td>
			<td class="td-r-head-bottom"><b>***' . number_format($_ihead['amount'],2) . '</b></td>
		</tr>
		';

	$html.='	
	</table>
	</htmlpageheader>
	<htmlpagefooter name="myfooter">
	
	<table width=100% cellpadding=5 style="border: 1px solid #000000;">
		<tr>
			<td width=33% align=center><b>PREPARED BY:</b><br><br>'.$uname.'<br></td>
			<td width=33% align=center><b>NOTED BY:</b><br><br><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br><font size=3>DR. JEREMY S. NIELO (PRESIDENT)</font></td>
			<td width=33% align=center><b>APPROVED BY:</b><br><br><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br><font size=3>ECM/DAL/CDI</font></td>
			
		</tr>
	</table>
	<table width=100% cellpadding=5 style="margin-top: 5px;">
		<tr><td style="text-align: justify;">Received the amount <u><b>'.$word.' PESOS ' . $fracs . ' ('.number_format($_ihead['amount'],2).')</b></u></td></tr>
		
		<tr><td>Received By: ________________________________________<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature over Printed Name</td></tr>
	</table>
	<table width=100%>
		<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Print Date: '.date('m/d/Y h:i:s a').'</td></tr>
	</table>
	</htmlpagefooter>

	<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
	<sethtmlpagefooter name="myfooter" value="on" />
	mpdf-->

	<div width="64%" style="float:left;">
		<table class="items" width="100%" style="font-size: 7pt; border-collapse: collapse;" cellpadding="5" border=1  autosize=1>
			<thead>
				<tr>
					<td width="15%" align=left><b>REF. NO.</b></td>
					<td width="20%" align=left><b>ACCT CODE</b></td>
					<td width="30%" align=left><b>ACCT DESCRIPTION</b></td>
					<td width="15%" align=right><b>DEBIT</b></td>
					<td width="15%" align=right><b>CREDIT</b></td>
				</tr>
			</thead>
		<tbody>';
			$i = 0;
			while($row = $_idetails->fetch_array()) {
				if($row['ref_no'] != "") {
						if($oldref != $row['ref_no']) { $ref_no = $row['ref_no']; $ref_date = $row['ref_date'];  } else { $ref_no = ""; $ref_date = ""; }
				}

				$html = $html . '<tr>
				<td align=left>' . $row['ref_no'] . '</td>
				<td align=left>' . $row['acct'] . '</td>
				<td align=left>' . $row['acct_desc'] . '</td>
				<td align="right">' . number_format($row['debit'],2) . '</td>
				<td align="right">' . number_format($row['credit'],2) . '</td>
				</tr>'; $dbGT+=$row['debit']; $crGT+=$row['credit'];
			}
			$html = $html . '
			  </tbody>
			</table>';
		$html = $html . '
	</div>
	<div width=1% style="float:left;">&nbsp;</div>
	<div width="35%"> 
		<table class="items" width="100%" style="font-size: 8pt; border-collapse: collapse;" cellpadding="5" border=1 autosize=1>
			<thead>
				<tr>
					<td width="30%" align=center><b>Check No</b></td>
					<td width="30%" align=center><b>Check Date</b></td>
					<td width="40%" align=center><b>Amount</b></td>
				</tr>
			</thead>
			<tbody>';
			while($indxCheck = $cheques->fetch_array()){
				$html.='<tr>
							<td >'.$indxCheck['check_no'].'</td>
							<td align="center">'.$indxCheck['check_date'].'</td>
							<td align="right" >'.number_format($indxCheck['amount'],2).'</td>
						</tr>';
			}		
			$html.='</tbody>
			</table>	
	</div>
	<div style="clear:both">
		<table width=100% style="margin-top: 5px;">
			<tr><td align=left><b>MEMO : </b><u>'.$_ihead['remarks'].'</u></td>
			</tr>
		</table>
	</div>	
	';

	$html = $html . '</body>
	</html>
	';

	$html = utf8_encode(utf8_decode($html));
	$mpdf->WriteHTML($html);
	$mpdf->Output(); exit;
	exit;
?>