<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
	$_ihead = $con->getArray("SELECT *, LPAD(so_no,6,0) AS sono, DATE_FORMAT(a.created_on, '%m/%d/%Y %h:%i:%s %p') AS dprocessed, DATE_FORMAT(so_date,'%m/%d/%Y') AS d8, IF(loa_date!='0000-00-00',DATE_FORMAT(loa_date,'%m/%d/%Y'),'') AS load8, IF(hmo_card_expiry!='0000-00-00',DATE_FORMAT(hmo_card_expiry,'%m/%d/%Y'),'') AS exd8, LPAD(patient_id,6,'0') AS pid, a.patient_id, IF(customer_code!=0,CONCAT('[',LPAD(customer_code,6,'0'),'] ',customer_name),CONCAT(patient_name,' (Patient)')) AS cname, IF(customer_code!=0,customer_address,patient_address) AS customer_address, b.description AS terms_desc,c.fullname AS created_by,  a.so_date as xorderdate FROM so_header a LEFT JOIN options_terms b ON a.terms = b.terms_id LEFT JOIN user_info c ON a.created_by = c.emp_id where so_no = '$_REQUEST[so_no]' and branch = '$_SESSION[branchid]';");
	$_idetails = $con->dbquery("SELECT `code`,if(qty>1,concat(description,' (x',qty,')'),description) as particulars,amount_due FROM so_details WHERE so_no = '$_REQUEST[so_no]' AND branch = '$_SESSION[branchid]';");
	$bcode = $_ihead['trace_no'];
	
	list($nos,$stin,$isVat) = $con->getArray("select tel_no, tin_no, vatable from contact_info where file_id = '$_ihead[customer_code]';");
	$_p = $con->getArray("SELECT DATE_FORMAT(birthdate,'%m/%d/%Y') AS bday, birthdate as xbday, birthdate, IF(gender='M','Male','Female') AS gender, b.civil_status, mobile_no, email_add FROM pddmc.patient_info a LEFT JOIN omdcpayroll.options_civilstatus b ON a.cstat = b.csid WHERE a.patient_id = '$_ihead[patient_id]';");
	
	$con->calculateAge2($_ihead['xorderdate'],$_p['xbday']);

	$age = $con->calculateAge($_p['birthdate']);
	list($lvisit) = $con->getArray("select date_format(so_date,'%m/%d/%Y') from so_header where patient_id = '$_ihead[patient_id]' and status = 'Finalized' and so_date < '$_ihead[so_date]' order by so_date desc limit 1;");
	list($pstat) = $con->getArray("select patientstatus from options_patientstat where id = '$_ihead[patient_stat]';");
	

	list($physician) = $con->getArray("select concat(fullname,', ',prefix) from options_doctors where id = '$_ihead[physician]';");

	list($dRows) = $con->getArray("select count(*) from so_details WHERE so_no = '$_REQUEST[so_no]' AND branch = '$_SESSION[branchid]';");
	if($dRows > 6) { $paper = "letter"; } else { $paper = "LETTER-H"; }

	/* AUDIT TRAIL PURPOSES */
	$con->dbquery("insert into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SO','SALES ORDER # $_REQUEST[so_no] WAS PRINTED BY USER','$_REQUEST[so_no]');");
			
	/* Summary of Charges */

		$vatable = 0;
		$vat = 0;

		if($_ihead['scpwd_id'] != '' && $age >= 60) {
			$scdiscount = ROUND($vatable * 0.20,2);
		}

		$amountDue = $_ihead['amount'] - $scdiscount;

/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252',$paper,'','',10,10,90,30,10,10);
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
		/* background-color: #EEEEEE; */ padding: 3px;
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
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; /* background-color: #EEEEEE; */
}

.td-r-head {
	text-align: right; font-weight: bold; padding: 3px;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000;
}
.td-l-head-bottom {
	text-align: left; font-weight: bold; padding: 3px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; /* background-color: #EEEEEE; */ border-bottom: 0.1mm solid #000000;
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
		<b><br/>BILL TO :</b><br /><br /><b>'.$_ihead['cname'].'</b><br /><i>'.$_ihead['customer_address'].'<br/>'.$nos.'<br/></i></td>
		<td class="td-l-top"><b>Priority No.</b></td>
		<td class="td-r-top"><b>'.str_pad($_ihead['priority_no'],4,'0',STR_PAD_LEFT).'</b></td>
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
		<td class="td-r-head-bottom"><b>&#8369;' . number_format($_ihead['amount'],2) . '</b></td>
	</tr>
</table>
<table width=100% cellpadding=2 cellspacing=0 style="font-size: 9px;margin-top:10px;">
	<tr>
		<td width=100% colspan=4 style="background-color: #cdcdcd; border-top: 1px solid black; border-bottom: 1px solid black;" align=center><b>PATIENT INFORMATION</b></td>
	</tr>
	<tr>
		<td width=20%><b>PATIENT ID</b></td>
		<td width=30%>:&nbsp;&nbsp;'.$_ihead['pid'].'</td>
		<td width=20%><b>GENDER</b></td>
		<td width=30%>:&nbsp;&nbsp;'.$_p['gender'].'</td>
	</tr>
	<tr>
		<td><b>PATIENT NAME</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['patient_name'].'</td>
		<td><b>BIRTHDATE</b></td>
		<td>:&nbsp;&nbsp;'.$_p['bday'].'</td>
	</tr>
	<tr>
		<td><b>PATIENT ADDRESS</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['patient_address'].'</td>
		<td><b>AGE</b></td>
		<td>:&nbsp;&nbsp;'.$con->ageDisplay.'</td>
	</tr>
	<tr>
		<td><b>MOBILE NO.</b></td>
		<td>:&nbsp;&nbsp;'.$_p['mobile_no'].'</td>
		<td><b>CIVIL STATUS</b></td>
		<td>:&nbsp;&nbsp;'.$_p['civil_status'].'</td>
	</tr>
	<tr>
		<td><b>EMAIL ADDRESS</b></td>
		<td>:&nbsp;&nbsp;'.$_p['email_add'].'</td>
		<td><b>SC/PWD ID</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['scpwd_id'].'</td>
	</tr>
	<tr>
		<td><b>REQUESTING PHYSICIAN</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['physician'].'</td>
		<td><b>HMO CARD NO.</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['hmo_card_no'].'</td>
	</tr>
	<tr>
		<td><b>LAST KNOWN VISIT</b></td>
		<td>:&nbsp;&nbsp;'.$lvisit.'</td>
		<td><b>PATIENT STATUS</b></td>
		<td>:&nbsp;&nbsp;'.$pstat.'</td>
	</tr>
</table>

</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100% cellpadding=0>
	<tr>
		<td align=left><b>REMARKS:&nbsp;&nbsp;</b>'.$_ihead['remarks'].'</td>
	</tr>
</table>
<table width=100% cellpadding=5 style="border: 1px solid #000000;">
	<tr>
		<td width=33% align=center><b>PREPARED BY:</b><br><br>'.$_ihead['created_by'].'<br></td>
		<td align=center><b>ACKNOWLEDGED BY:</b><br><br>_______________________<br><font size=2>Signature Over Printed Name</font></td>
	</tr>
</table>
<table width=100%>
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Run Date: '.$_ihead['dprocessed'].'</td></tr>
	<tr><td colspan=2 align=center><b> **** THIS DOCUMENT IS NOT VALID FOR INPUT TAX CLAIM ****</b></td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->

	<table class="items" width="100%" style="font-size: 9px; border-collapse: collapse;" cellpadding="3">
		<thead>
			<tr>
				<td width="15%" style="background-color: #cdcdcd;" align=left><b>CODE</b></td>
				<td width="70%" style="background-color: #cdcdcd;" align=left><b>PARTICULARS/PROCEDURE</b></td>
				<td width="15%" style="background-color: #cdcdcd;" align=right><b>AMOUNT</b></td>
			</tr>
		</thead>
		<tbody>';
			$i = 0;
			while($row = $_idetails->fetch_array()) {

				list($cat) = $con->getArray("select with_subtests from services_master where `code` = '$row[code]';");
				if($cat == 'Y') { list($subdescription) = $con->getArray("select concat('<br/>&raquo; ',fulldescription) as subdescription from services_master where `code` = '$row[code]';"); } else { $subdescription = ''; }

				$html .= '<tr>
						<td align=left>'.$row['code'].'</td>
						<td align=left>' . $row['particulars'] .  $subdescription .'</td>
						<td align="right">' . number_format($row['amount_due'],2) . '</td>
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
						<td colspan=2 style="background-color: #cdcdcd; border-top: 0.1mm solid #000000;border-bottom: 0.1mm solid #000000;" align=left><b>TOTAL AMOUNT (NON-VAT)</b></td>
						<td width="15%" style="background-color: #cdcdcd; border-top: 0.1mm solid #000000;border-bottom: 0.1mm solid #000000;" align=right><b>'.number_format(($amtGT-$scpwd),2).'</b></td>
					</tr>
		</tbody>
	</table>
		
</body>
</html>
';

$html = html_entity_decode($html);
$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>