<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

	/* MYSQL QUERIES SECTION */
 	$now = date("m/d/Y h:i a");
 	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
	$_ihead = $con->getArray("SELECT a.*, LPAD(c.so_no,6,0) AS sono, DATE_FORMAT(c.so_date,'%m/%d/%Y') AS d8, DATE_FORMAT(a.updated_on, '%m/%d/%Y') AS examin_d8, b.patient_id, fname, b.lname, b.mname,b.suffix,FLOOR(DATEDIFF(c.so_date,b.birthdate)/364.25) AS age,DATE_FORMAT(b.birthdate,'%M %d') AS date1, DATE_FORMAT(b.birthdate,'%Y') AS date2, classification,examined_by,evaluated_by,b.gender,b.cstat,b.brgy,b.city,b.province,b.street,c.customer_name,c.customer_address FROM peme a LEFT JOIN omdc.patient_info b ON a.pid = b.patient_id LEFT JOIN so_header c ON a.so_no = c.so_no AND a.pid = c.patient_id WHERE a.so_no= '$_REQUEST[so_no]' AND a.pid= '$_REQUEST[pid]';");
	$a = $con->dbquery("SELECT LPAD(so_no,6,0) AS sono, pid,CONCAT(c.fname,' ',c.lname) AS pname, pm_history FROM peme a LEFT JOIN options_medicalhistory b ON b.id = a.pid LEFT JOIN patient_info c ON a.pid = c.patient_id WHERE a.so_no= '$_REQUEST[so_no]' AND a.pid= '$_REQUEST[pid]';");
	$b = $con->getArray("SELECT * FROM peme WHERE so_no= '$_REQUEST[so_no]' AND pid= '$_REQUEST[pid]';");
	$c = $con->getArray("SELECT pid,examined_by,DATE_FORMAT(examined_on,'%m/%d/%Y') AS examin_d8,TIME_FORMAT(examined_on,'%h:%m:%s') AS examin_tym,evaluated_by,DATE_FORMAT(evaluated_on,'%m/%d/%Y') AS eval_d8, TIME_FORMAT(evaluated_on,'%h:%m:%s') AS eval_tym FROM peme WHERE so_no = '$_REQUEST[so_no]' and pid = '$_REQUEST[pid]';");
	$d = $con->getArray("select trace_no from so_details where so_no = '$_ihead[sono]';");

	$labresult = $con->getArray("SELECT chest_findings, cbc_findings, cbc_findings, se_findings, dt_findings, pap_findings, antigen_findings, IF(ecg_normal='Y','NORMAL','With Findings') AS ecg_normal, ecg_findings, others1_name, IF(others1_normal='Y','NORMAL','With Findings') AS others1_normal, others1_findings, others2_name, IF(others2_normal='Y','NORMAL','With Findings') AS other2_normal, others2_findings FROM peme WHERE so_no= '$_ihead[so_no]' AND pid= '$_ihead[pid]';");

	if($_ihead['signature_path'] != '') {
		$patient_signature = "<img src='".$_ihead['signature_path']."' align=absmiddle width=123 height=50 />";
	} else { $patient_signature = ''; }

	if($_ihead['examined_by'] != '') {
        list($docsignature,$docfullname,$docprefix,$docrole,$doclicenseno) = $con->getArray("SELECT IF(signature_file != '',CONCAT('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') AS signature, fullname, prefix , specialization, license_no FROM options_doctors WHERE id = '$_ihead[examined_by]';");
    }

	if($_ihead['evaluated_by'] != '') {
        list($doctorevaluator,$doctorfullname,$doctorprefix,$doctorrole,$doctorlicenseno) = $con->getArray("SELECT IF(signature_file != '',CONCAT('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') AS signature, fullname, prefix , specialization, license_no FROM options_doctors WHERE id = '$_ihead[evaluated_by]';");
    }

	list($brgy) = $con->getArray("SELECT brgyDesc FROM options_brgy WHERE brgyCode = '$_ihead[brgy]';");
    list($ct) = $con->getArray("SELECT citymunDesc FROM options_cities WHERE cityMunCode = '$_ihead[city]';");
    list($prov) = $con->getArray("SELECT provDesc FROM options_provinces WHERE provCode = '$_ihead[province]';");

    if($_ihead['street'] != '') { $myaddress.=$_ihead['street'].", "; }
    if($brgy != "") { $myaddress .= $brgy.", "; }
    if($ct != "") { $myaddress .= $ct.", "; }
    if($prov != "")  { $myaddress .= $prov.", "; }
    $myaddress = substr($myaddress,0,-2);



	/* Medical History */
	if($_ihead['pm_history'] != '') {
		$pm = explode(",",$_ihead['pm_history']);
		$pmstring = '';
		foreach($pm as $pmid) {
			list($pmDescription) = $con->getArray("SELECT history FROM options_medicalhistory WHERE id = '$pmid';");
			$pmstring .= $pmDescription . ", ";
		}
		$pmstring = substr($pmstring,0,-2);

	} else { $pmstring = ''; }


	 /* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','letter','','',10,10,57,30,10,5);
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
body {font-family: sans-serif; font-size: 9px; }
thead {
	height: 30%;
	border: 1px solid black;
}

.td-0 {
	border-top: 1px solid #00000;
}
.td-1 {
	border-top: 1px solid black;
	border-left: 1px solid black;
	padding-bottom: 2px;
	text-align:left;
	padding-top:2px;
	padding-left: 5px;
}
.td-2 {
	border-top: 1px solid black;
	padding-bottom: 5px;
	border-left: 1px solid black;
	padding-left: 5px;
	text-align:left;
	padding-top:3px;
}
.td-3 {
	padding-bottom: 5px;
	border-left: 1px solid black;
	padding-left: 5px;
	text-align:left;
	padding-top:3px;
}
.td-4 {
	border-bottom: 1px solid #00000;
}
.td-5 {
	border-left: 1px solid #00000;
	padding-left:5px;
	padding-bottom:2px;
	padding-top:2px;
}
.td-6 {
	border-right: 1px solid #00000;
}
.indent-top {
	padding-top:5px;
	padding-left:5px;
	font-size: 9px;
}
.table-border {
	border-right: 1px solid #00000;
	border-left: 1px solid #00000;
}
tbody {
	height: 50%;
}
.side-left {
	padding-left: 10px;
}
.border-right {
	border-right: 1px solid #00000;
}
.border-left {
	border-left: 1px solid #00000;
}
.border-pe {
	padding-top:2px;
	padding-left:2px;
	border-left: 1px solid black;
	padding-bottom: 2px;
}
.padding-top {
	padding-top:5px;
}
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%" cellpadding=0 cellpadding=0>
	<tr>
		<td align=center><img src="../images/doc-header.jpg" /></td>
	</tr>
	<tr>
		<td width="100%" align=center><span style="font-weight: bold; font-size: 14pt; color: #000000;">PHYSICAL EXAMINATION</span></td>
	</tr>
</table>
<table width="100%" cellspacing=0 cellpadding=0>
<tr><td height=10></td></tr>
</table>
<table width="100%" cellspacing=0 cellpadding=0>
	<tr>
		<td width=15% class="td-1">Last Name</td>
		<td width=17% class="td-0">First Name</td>
		<td width=10% class="td-0">Middle Name</td>
		<td width=5% class="td-0">Suffix</td>
		<td class="td-2 td-6">Present Mailing Address:&nbsp;&nbsp;<b>'.$myaddress.'</b></td>
	</tr>
	<tr>
		<td width=15% class="td-5 td-4"><b>'.$_ihead['lname'].'</b></td>
		<td width=17% class="td-4"><b>'.$_ihead['fname'].'</b></td>
		<td width=10% class="td-4"><b>'.$_ihead['mname'].'</b></td>
		<td width=5% class="td-4"><b>'.$_ihead['suffix'].'</b></td>
		<td class="td-4 td-5 td-6">Cell/Tel.No.:&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$_ihead['mobile_no'].'</b></td>
	</tr>
</table>
<table width="100%" cellspacing=0 cellpadding=0>
	<tr>
		<td width=15% class="td-5">Sex</td>
		<td width=17% class="td-5">Age</td>
		<td width=15% class="td-5">Birth Date</td>
		<td class="td-5">Civil Status</td>
		<td width=40% class="td-5 td-6">Intended Occupation</td>
	</tr>
	<tr>
		<td width=15% class="td-5 td-4"><b>'.$_ihead['gender'].'</b></td>
		<td width=15% class="td-5 td-4"><b>'.$_ihead['age'].'</b></td>
		<td width=17% class="td-5 td-4"><b>'.$_ihead['date1'].', '.$_ihead['date2'].'</b></td>';
		if($_ihead['cstat'] == '5') {
	$html .=	'<td class="td-5 td-4"><b>Living-in with Partner</b></td>';
		}else if($_ihead['cstat'] == '2') {
	$html .=	'<td class="td-5 td-4"><b>MARRIED</b></td>';
		}else if($_ihead['cstat'] == '3') {
	$html .=	'<td class="td-5 td-4"><b>Legally Separated</b></td>';
		}else if($_ihead['cstat'] == '4') {
	$html .=	'<td class="td-5 td-4"><b>Widow/Widower</b></td>';
		}else if($_ihead['cstat'] == '1'){
	$html .=	'<td class="td-5 td-4"><b>SINGLE</b></td>';
		}else {
	$html .=	'<td class="td-5 td-4"><b>&nbsp;</b></td>';
		}
		$html .= '<td width=40% class="td-4 td-5 td-6"><b>'.$_ihead['occupation'].'</b></td>
	</tr>
</table>

</htmlpageheader>


<htmlpagefooter name="myfooter">
<table width=100% cellpadding=5 style="margin-bottom: 5px;">
	<tr>
		<td align=center valign=top>'.$docsignature.'<br/><b>'.$docfullname.',&nbsp;'.$docprefix.'&nbsp;- LIC No. '.$doclicenseno.'&nbsp;&nbsp;&nbsp;'.$c['examin_d8'].'</b><br/>___________________________________________<br/>Examining Physician&nbsp;/&nbsp;Date</td>
		<td align=center valign=top>'.$doctorevaluator.'<br/><b>'.$doctorfullname.'&nbsp;'.$doctorprefix.'&nbsp;- LIC No. '.$doctorlicenseno.'&nbsp;&nbsp;&nbsp;'.$c['eval_d8'].'</b><br/>___________________________________________<br/>Evaluating Physician&nbsp;/&nbsp;Date</td>
	</tr>
</table>
<table width=100%>
	<tr><td align=left><barcode size=0.8 code="'.substr($d['trace_no'],0,10).'" type="C128A"></td><td align=right>Run Date: '.date('m/d/Y h:i:s a').'</td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" show-this-page="1" />

	mpdf-->
	<tbody>
	<table width="100%" cellspacing=0 cellpadding=0 class="border-left border-right">
		<tr>
			<td width=100% height=40% align=left class="indent-top" style="padding-top:5px;padding-bottom:3px;background-color:#ecebeb;"><b>I.&nbsp;&nbsp;Medical History</b></td>
		</tr>
		<tr><td style="padding: 10px;">'.$pmstring.'</td></tr>
		<tr><td height=10>&nbsp;</td></tr>
	</table>
	<table width="100%" cellspacing=0 cellpadding=0 class="border-left border-right">
		<tr>
			<td class="border-left" align=left class="indent-top">Family History:</td>
			<td align=left width=78% style="border-bottom: 1px solid black;"><b>'. $_ihead['fm_history'] . '</b></td>
		</tr>
		<tr>
			<td class="border-left" align=left class="indent-top">Previous Hospitalization:</td>
			<td align=left width=78% style="border-bottom: 1px solid black;"><b>'. $_ihead['pv_hospitalization'] . '</b></td>
		</tr>
		<tr>
			<td class="border-left" align=left class="indent-top">Current Medication:</td>
			<td align=left width=78% style="border-bottom: 1px solid black;"><b>'.$_ihead['current_med'].'</b></td>
		</tr>
	</table>
	<table width="100%" cellspacing=0 cellpadding=0 class="border-left border-right">
		<tr>
			<td width=20% align=left class="border-left" class="indent-top">Parity: &nbsp;&nbsp;&nbsp;<u>&nbsp;&nbsp;&nbsp;<b>'.$_ihead['parity'].'</b>&nbsp;&nbsp;&nbsp;</u></td>
			<td width=25% align=left class="border-left" class="indent-top">Alcoholic Beverage Drinker: &nbsp;&nbsp;&nbsp;<u>&nbsp;&nbsp;&nbsp;<b>'.$_ihead['alcoholic'].'</b>&nbsp;&nbsp;&nbsp;</u></td>
			<td width=20% align=left class="border-left" class="indent-top">Illicit Drug Use: &nbsp;&nbsp;&nbsp;<u>&nbsp;&nbsp;&nbsp;<b>'.$_ihead['drugs'].'</b>&nbsp;&nbsp;&nbsp;</u></td>
			<td width=20% align=left class="border-left" class="indent-top">Pregnant: &nbsp;&nbsp;&nbsp;<u>&nbsp;&nbsp;&nbsp;<b>'.$_ihead['pregnant'].'</b>&nbsp;&nbsp;&nbsp;</u></td>
		</tr>
	</table>
	<table width="100%" cellspacing=0 cellpadding=0 class="border-left border-right">
		<tr>
			<td width=20% align=left class="border-left" class="indent-top">LMP: &nbsp;&nbsp;&nbsp;<u>&nbsp;&nbsp;&nbsp;<b>'.$_ihead['lmp'].'</b>&nbsp;&nbsp;&nbsp;</u></td>
			<td width=25% align=left class="border-left" class="indent-top">Menstrual History: &nbsp;&nbsp;&nbsp;<u>&nbsp;&nbsp;&nbsp;<b>'.$_ihead['mens_history'].'</b>&nbsp;&nbsp;&nbsp;</u></td>
			<td width=20% align=left class="border-left" class="indent-top">Contraceptive Use: &nbsp;&nbsp;&nbsp;<u>&nbsp;&nbsp;&nbsp;<b>'.$_ihead['contraceptives'].'</b>&nbsp;&nbsp;&nbsp;</u></td>

		</tr>
		<tr><td height=10></td></tr>
	</table>
	<table width="100%" cellspacing=0 cellpadding=0 class="border-left border-right">
		<tr>
			<td width=100% class="indent-top" align=left><i>I certify that my medical history contained above is correct and true. I certify that I am the same person being examined whose name appears on this medical record, and that I have truthfully answered the question asked regarding my well-being.</i></td>
		</tr>
	</table>
	<table width=100% cellpadding=5 class="border-left border-right">
		<tr>
			<td align=center valign=top>'.$patient_signature.'<br/>___________________________________________<br><b>SIGNATURE OF EXAMINEE/PATIENT</b></td>
			<td align=center style="padding-top:33px;"><b>' .$_ihead['customer_name']. '</b><br/><br/>____________________________________________________________<br><b>NAME OF EMPLOYER/COMPANY NAME</b></td>
		</tr>
	</table>
	<table width="100%" cellspacing=0 cellpadding=0>
		<tr style="table-border">
			<td width=30% height=40% align=left class="indent-top td-0 border-right border-left" style="padding-top:5px;padding-bottom:3px;background-color:#ecebeb;"><b>II.&nbsp;&nbsp;PHYSICAL EXAMINATION</b>(to be completed by examining physician)</td>
		</tr>
		<tr><td height=5 class="border-right border-left td-4"></td></tr>
	</table>
	<table width="100%" cellspacing=0 cellpadding=0>
		<tr>
			<td width=13% class="border-pe">Height (cm)</td>
			<td width=12% class="border-pe">Weight (kg)</td>
			<td width=15% class="border-pe">Blood Pressure (mmHg)</td>
			<td width=10% class="border-pe">Pulse (beats/min)</td>
			<td width=17% class="border-pe">Respiratory rate (cycles/min)</td>
			<td width=12% class="border-pe">BMI kg/m2</td>
			<td class="border-pe border-right">Body Build</td>
		</tr>
		<tr>
			<td width=13% align=center class="border-pe td-4"><b>'.$_ihead['ht'].' cm</b></td>
			<td width=12% align=center class="border-pe td-4"><b>'.number_format($_ihead['wt']).' kg</b></td>
			<td width=15% align=center class="border-pe td-4"><b>'.$_ihead['bp'].' mmHg</b></td>
			<td width=10% align=center class="border-pe td-4"><b>'.$_ihead['pulse'].'</b></td>
			<td width=17% align=center class="border-pe td-4"><b>'.$_ihead['rr'].'</b></td>
			<td width=12% align=center class="border-pe td-4"><b>'.$_ihead['rr'].'</b></td>
			<td align=center class="border-pe td-4 border-right"><b>&nbsp;</b></td>
		</tr>
	</table>
	<table width="100%" cellspacing=0 cellpadding=0>
		<tr>
			<td width=13% class="border-pe">VISUAL ACUITY</td>
			<td width=12% class="border-pe">with glasses</td>
			<td width=15% class="border-pe">JAEGER TEST</td>
			<td width=10% class="border-pe">with glasses</td>
			<td width=17% class="border-pe">ISHIHARA TEST</td>
			<td width=12% class="border-pe">HEARING TEST</td>
			<td class="border-pe border-right">CLARITY OF SPEECH</td>
		</tr>
		<tr>
			<td width=13% align=center class="border-pe td-4"><b>R<u>&nbsp;'.$_ihead['righteye'].'&nbsp; </u>&nbsp;&nbsp; L<u>&nbsp;'.$_ihead['lefteye'].'&nbsp;</u></b></td>
			<td width=12% align=center class="border-pe td-4"><b>R<u>&nbsp;'.$_ihead['correct_left'].'&nbsp; </u>&nbsp;&nbsp; L<u>&nbsp;'.$_ihead['correct_right'].'&nbsp;</u></b></td>
			<td width=15% align=center class="border-pe td-4"><b>R<u>&nbsp;&nbsp;&nbsp;'.$_ihead['jaegerright'].'&nbsp;&nbsp;&nbsp; </u>&nbsp;&nbsp; L<u>&nbsp;&nbsp;&nbsp;'.$_ihead['jaegerleft'].'&nbsp;&nbsp;&nbsp;</u></b></td>
			<td width=10% align=center class="border-pe td-4"><b>&nbsp;</b></td>
			<td width=17% align=center class="border-pe td-4"><b>&nbsp;'.$_ihead['ishihara'].'</b></td>
			<td width=12% align=center class="border-pe td-4"><b>R<u>&nbsp; 	&nbsp; </u>&nbsp;&nbsp; L<u>&nbsp;	&nbsp;	&nbsp;</u></b></td>
			<td align=center class="border-pe td-4 border-right"><b>&nbsp;</b></td>
		</tr>
	</table>
	<table width=100% align=left cellspacing=0 cellpadding=0 class="border-left border-right">
		<tr>
			<td width=15%>&nbsp;</td>
			<td width=15% align=left style="padding-top: 5px;"><b>STATUS</b></td>
			<td width=20% align=left style="padding-top: 5px;"><b>FINDINGS</b></td>
			<td width=15%>&nbsp;</td>
			<td width=15% align=left style="padding-top: 5px;"><b>STATUS</b></td>
			<td width=20% align=left style="padding-top: 5px;"><b>FINDINGS</b></td>
		</tr>
	</table>
	<table width=100% align=left cellspacing=0 cellpadding=0 class="border-left border-right">
		<tr>
			<td width=15% align=left style="padding-left:10px;padding-top: 5px;">Skin&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width=15% align=left style="padding-top: 2px;"><b>'; if($b['sa_normal'] == '') { $html .= 'N/A'; }else if($b['sa_normal'] == 'Y') { $html .= 'Normal'; }else { $html .= 'With Findings';} $html .='</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$b['sa_findings'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width=15% align=left; style="padding-left:10px;padding-top: 2px;">Abdomen</td>
			<td width=15% align=left style="padding-top: 2px;"><b>'; if($b['abdomen_normal'] == '') { $html .= 'N/A'; }else if($b['abdomen_normal'] == 'Y') { $html .= 'Normal'; }else { $html .= 'With Findings';} $html .='</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$b['abdomen_findings'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>
		<tr>
			<td width=15% style="padding-left:10px;padding-top: 2px;">Head, Scalp</td>
			<td width=15% align=left style="padding-top: 2px;"><b>'; if($b['hs_normal'] == '') { $html .= 'N/A'; }else if($b['hs_normal'] == 'Y') { $html .= 'Normal'; }else { $html .= 'With Findings';} $html .='</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$b['hs_findings'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width=15% style="padding-left:10px;padding-top: 2px;">Back</td>
			<td width=15% align=left style="padding-top: 2px;"><b>Normal</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>
		<tr>
			<td width=15% align=left style="padding-left:10px;padding-top: 2px;">EENT</td>
			<td width=15% align=left style="padding-top: 2px;"><b>'; if($b['ee_normal'] == '') { $html .= 'N/A'; }else if($b['ee_normal'] == 'Y') { $html .= 'Normal'; }else { $html .= 'With Findings';} $html .='</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$b['ee_findings'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width=15% align=left; style="padding-left:10px;padding-top: 2px;">Anus, Rectum</td>
			<td width=15% align=left style="padding-top: 2px;"><b>'; if($b['rect_normal'] == '') { $html .= 'N/A'; }else if($b['rect_normal'] == 'Y') { $html .= 'Normal'; }else { $html .= 'With Findings';} $html .='</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$b['rect_findings'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>
		<tr>
			<td width=15% style="padding-left:10px;padding-top: 2px;">Neck/Thyroid</td> 	neck_normal
			<td width=15% align=left style="padding-top: 2px;"><b>'; if($b['neck_normal'] == '') { $html .= 'N/A'; }else if($b['neck_normal'] == 'Y') { $html .= 'Normal'; }else { $html .= 'With Findings';} $html .='</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$b['neck_findings'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width=15% style="padding-left:10px;padding-top: 2px;">GUT</td>
			<td width=15% align=left style="padding-top: 2px;"><b>Normal</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>
		<tr>
			<td width=15% align=left style="padding-left:10px;padding-top: 5px;">Lungs&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> 
			<td width=15% align=left style="padding-top: 2px;"><b>'; if($b['lungs_normal'] == '') { $html .= 'N/A'; }else if($b['lungs_normal'] == 'Y') { $html .= 'Normal'; }else { $html .= 'With Findings';} $html .='</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$b['lungs_findings'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width=15% align=left; style="padding-left:10px;padding-top: 2px;">Genitals</td>
			<td width=15% align=left style="padding-top: 2px;"><b>'; if($b['genitals_normal'] == '') { $html .= 'N/A'; }else if($b['genitals_normal'] == 'Y') { $html .= 'Normal'; }else { $html .= 'With Findings';} $html .='</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$b['genitals_findings'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>
		<tr>
			<td width=15% style="padding-left:10px;padding-top: 2px;">Heart&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width=15% align=left style="padding-top: 2px;"><b>'; if($b['heart_normal'] == '') { $html .= 'N/A'; }else if($b['heart_normal'] == 'Y') { $html .= 'Normal'; }else { $html .= 'With Findings';} $html .='</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$b['heart_findings'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width=15% style="padding-left:10px;padding-top: 2px;">Reflexes</td>
			<td width=15% align=left style="padding-top: 2px;"><b>'; if($b['ref_normal'] == '') { $html .= 'N/A'; }else if($b['ref_normal'] == 'Y') { $html .= 'Normal'; }else { $html .= 'With Findings';} $html .='</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$b['ref_findings'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>
		<tr>
			<td width=15% style="padding-left:10px;padding-top: 2px;">Breast-Axilla&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width=15% align=left style="padding-top: 2px;"><b>'; if($b['check_normal'] == '') { $html .= 'N/A'; }else if($b['check_normal'] == 'Y') { $html .= 'Normal'; }else { $html .= 'With Findings';} $html .='</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$b['check_findings'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width=15% style="padding-left:10px;padding-top: 2px;">Extremities</td>
			<td width=15% align=left style="padding-top: 2px;"><b>'; if($b['extr_normal'] == '') { $html .= 'N/A'; }else if($b['extr_normal'] == 'Y') { $html .= 'Normal'; }else { $html .= 'With Findings';} $html .='</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$b['extr_findings'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>
		<tr>
			<td width=15% style="padding-left:10px;padding-top: 2px;">Mouth/Teeth/Tongue</td>
			<td width=15% align=left style="padding-top: 2px;"><b>'; if($b['mouth_normal'] == '') { $html .= 'N/A'; }else if($b['mouth_normal'] == 'Y') { $html .= 'Normal'; }else { $html .= 'With Findings';} $html .='</b></td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$b['mouth_findings'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width=15% style="padding-left:10px;padding-top: 2px;">Dental</td>
			<td width=15% align=left style="padding-top: 2px;">&nbsp;</td>
			<td width=20% align=left style="padding-top: 2px;text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>
		<tr><td height=5></td></tr>
	</table>
	<table width="100%" cellspacing=0 cellpadding=0>
		<tr style="table-border">
			<td width=30% height=40% align=left class="indent-top td-0 border-right border-left" style="padding-top:5px;padding-bottom:3px;background-color:#ecebeb;"><b>III.&nbsp;&nbsp;X-RAY, ECG AND LABORATORY EXAMINATION REPORT</b></td>
		</tr>
		<tr><td height=5 class="border-right border-left td-4"></td></tr>
	</table>
	<table width=100% align=left cellspacing=0 cellpadding=0 class="border-left border-right td-4">
		<tr>';
		if($_ihead['chest_normal'] == 'Y') {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">A.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chest X-ray:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>NORMAL</b></td>';
		}else if($_ihead['chest_normal'] == 'N') {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">A.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chest X-ray:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>With Findings</b></td>';
		}else {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">A.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chest X-ray:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>N/A</b></td>';
		}


		if($_ihead['se_normal'] == 'Y') {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">E.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Stool Examination:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>NORMAL</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['se_findings'].'</u></td>';
		}else if($_ihead['se_normal'] == 'N') {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">E.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Stool Examination:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>With Findings</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['se_findings'].'</u></td>';
		}else {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">E.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Stool Examination:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>N/A</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['se_findings'].'</u></td>';
		}

		if($_ihead['ecg_normal'] == 'Y') {
	$html .= '<td style="padding-left:10px;padding-top: 2px;">K.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ECG:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>NORMAL</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['ecg_findings'].'</u></td>';
		}else if($_ihead['ecg_normal'] == 'N') {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">K.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ECG:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>With Findings</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['ecg_findings'].'</u></td>';
		}else {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">K.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ECG:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>N/A</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['ecg_findings'].'</u></td>';
		}
	$html .= '</tr>
		<tr>
			<td width=33% style="padding-left:10px;padding-top: 2px; text-decoration:underline; padding-left:35px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$labresult['chest_findings'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width=33% style="padding-left:10px;padding-top: 2px;">F.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hepatitis A:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$_ihead['hepa_normal'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$_ihead['hepa_findings'].'</u></td>';

			if($_ihead['pap_normal'] == 'Y') {
				$html .= '<td style="padding-left:10px;padding-top: 2px;">L.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Papsmear:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>NORMAL</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['pap_findings'].'</u></td>';
					}else if($_ihead['pap_normal'] == 'N') {
				$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">L.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Papsmear:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>With Findings</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['pap_findings'].'</u></td>';
					}else {
				$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">L.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Papsmear:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>N/A</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['pap_findings'].'</u></td>';
					}
				$html .= '</tr>
		</tr>
		<tr>';
		if($_ihead['cbc_normal'] == 'Y') {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">B.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Complete Blood Count:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>NORMAL</b></td>';
		}else if($_ihead['cbc_normal'] == 'N') {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">B.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Complete Blood Count:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>With Findings</b></td>';
		}else {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">B.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Complete Blood Count:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>N/A</b></td>';
		}
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">G.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pregnancy Test:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$_ihead['pt_normal'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$_ihead['pt_findings'].'</u></td>';

	if($_ihead['antigen_normal'] == 'Y') {
	$html .= '<td style="padding-left:10px;padding-top: 2px;">M.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rapid Antigen Test:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>NORMAL</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['antigen_findings'].'</u></td>';
		}else if($_ihead['antigen_normal'] == 'N') {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">M.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rapid Antigen Test:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>With Findings</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['antigen_findings'].'</u></td>';
		}else {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">M.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rapid Antigen Test:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>N/A</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['antigen_findings'].'</u></td>';
		}
	$html .= '</tr>
		</tr>
		<tr>
			<td width=33% style="padding-left:10px;padding-top: 2px; text-decoration:underline;padding-left:35px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$labresult['cbc_findings'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
			if($_ihead['dt_normal'] == 'Y') {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">H.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Drug Test:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>POSITIVE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['dt_findings'].'</u></td>';
			} else if($_ihead['dt_normal'] == 'N'){
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">H.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Drug Test:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>NEGATIVE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['dt_findings'].'</u></td>';
			}else {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">H.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Drug Test:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>N/A</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$labresult['dt_findings'].'</u></td>';
			}
	$html .='</tr>
		<tr>';
		if($_ihead['ua_normal'] == 'Y') {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">C.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Urinalysis:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>NORMAL</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$_ihead['ua_findings'].'</u></td>';
		}else if($_ihead['ua_normal'] == 'N') {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">C.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Urinalysis:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>With Findings</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$_ihead['ua_findings'].'</u></td>';
		}else {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">C.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Urinalysis:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>N/A</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$_ihead['ua_findings'].'</u></td>';
		}
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">D.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HbsAg/Hep.B:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$_ihead['hbsag_normal'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$_ihead['hbsag_findings'].'</u></td>';

	$html .=	'</tr>
		<tr>';
		
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">D.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Blood Typing:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$_ihead['bt_normal'].'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'.$_ihead['bt_findings'].'</u></td>';
		if($_ihead['others1_name'] != '') {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">J.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Others:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_ihead['others1_name'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_ihead['others1_findings'].'</td>';
		}else if($_ihead['others2_name'] != '') {
	$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">J.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Others:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_ihead['others2_name'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_ihead['others2_findings'].'</td>';
		}else {
			$html .= '<td width=33% style="padding-left:10px;padding-top: 2px;">J.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Others:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
		}
	$html .=	'</tr>
		<tr><td height=5></td></tr>
	</table>
	<table width="100%" cellspacing=0 cellpadding=0 class="border-left border-right">
		<tr>
			<td width=30% height=40% align=left class="indent-top" style="padding-top:5px;padding-bottom:3px;background-color:#ecebeb;"><b>IV.&nbsp;&nbsp;RECOMMENDATION:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
		</tr>
		<tr><td height=5></td></tr>
	</table>
	<table width="100%" cellspacing=0 cellpadding=0 class="border-left border-right">';
		if($_ihead['classification'] == 'A') {
		$html .= '<tr>
				<td style="padding-left:40px;"><b>Class "A"</b>&nbsp;&nbsp;&nbsp;&nbsp; = Physically fit for all types of work.</td>';
		$html .= '</tr>';

		} else if($_ihead['classification'] == 'B') {
		$html .= '<tr>
					<td style="padding-left:40px;"><b>Class "B" </b>&nbsp;&nbsp;&nbsp;&nbsp; = Physically fit for all types of work.<br/>
					Have minor ailments or defect.Easily curable or offers no handicap to job applied.<br/>
					Needs treatment/ correction:<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_ihead['class_b_remarks1'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br>
					Treatment optional for:<u> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_ihead['class_b_remarks2'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>';
		$html .= '</tr>';

		}else if($_ihead['classification'] == 'C') {
	$html .='<tr>
	
			 <td style="padding-left:40px;"><b>Class "C"</b>&nbsp;&nbsp;&nbsp;&nbsp; = Physically fit for less strenuous type of work. Has minor ailment/s or defect/s.<br/>
			 	 Easily curable or offers no handicap to job applied.<br/>
			 	 Needs treatment / correction: <u> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_ihead['class_c_remarks1'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />
			  	 Treatment optional for:<u> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_ihead['class_c_remarks2'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>
			 
			 </tr>';
		} else if($_ihead['classification'] == 'D') {
			$html .= '<tr>
			<td style="padding-left:40px;"><b>Class "D"</b>&nbsp;&nbsp;&nbsp;&nbsp; = Employment at the risk and discretion of the management.</td>';
	$html .= '</tr>';


		}else if($_ihead['classification'] == 'E') {
			$html .= '<tr>
			<td style="padding-left:40px;"><b>Class "E"</b>&nbsp;&nbsp;&nbsp;&nbsp; = Unfit for Employment.</td>';
	$html .= '</tr>';
		}else if($_ihead['classification'] == 'PENDING') {
			$html .= '<tr>
			<td style="padding-left:40px;"><b>Classification: PENDING</b>&nbsp;&nbsp;&nbsp;&nbsp;<br />
			For further evaluation of:<u> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_ihead['pending_remarks'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>';
	$html .= '</tr>';
		}else {
			$html .= '<tr>
			<td style="padding-left:40px;">&nbsp;&nbsp;&nbsp;&nbsp;<br />';
			$html .= '</tr>';
		}
	$html .=	'</tr>

	</table>
	<table width="100%" cellspacing=0 cellpadding=0 class="border-left border-right td-4">
	<tr><td height=5></td></tr>
		<tr>
			<td class="border-left" align=left class="indent-top">Remarks:</td>
			<td align=left width=90% style="border-bottom: 1px solid black;margin-left:10px;"><b>'. $_ihead['overall_remarks'] . '</b></td>
		</tr>
		<tr><td height=15></td></tr>
	</table>
	</tbody>
</body>
</html>
';

$html = html_entity_decode($html);
$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>