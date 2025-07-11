<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");

	$_ihead = $con->getArray("SELECT DATE_FORMAT(result_date,'%m/%d/%Y') AS rdate, b.patient_name, b.patient_address, IF(c.gender='M','Male','Female') AS gender, FLOOR(DATEDIFF(b.so_date,c.birthdate)/365.25) AS age, a.physician, d.fullname as consultant, d.prefix, d.license_no, d.specialization, d.signature_file, a.serialno, a.procedure, a.impression, a.created_by, b.trace_no, a.verified, a.verified_by, e.role, e.signature_file as encodersignature, c.employer, a.serialno FROM lab_descriptive a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id left join options_doctors d on a.consultant = d.id left join user_info e on a.created_by = e.emp_id WHERE a.so_no = '$_REQUEST[so_no]' AND `code` = '$_REQUEST[code]' AND serialno = '$_REQUEST[serialno]' AND a.branch = '$_SESSION[branchid]';");
	list($lotno) = $con->getArray("select lotno from lab_samples where serialno = '$_ihead[serialno]';");

	if($lotno == '') { $lotno = "SO-".$_REQUEST['so_no']; }

	if($_ihead['signature_file'] != '') {
		$consultantSignature = "<img src='../images/signatures/$_ihead[signature_file]' align=absmiddle />";
	} else {
		$consultantSignature = "<img src='../images/signatures/blank.png' align=absmiddle />";	
	}		

	if($_ihead['verified_by'] != '') {
        list($medtechSignature,$medtechFullname,$medtechLicense,$medtechRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$_ihead[verified_by]';");
    }

	if($_ihead['physician'] != '') {
        list($docSignature,$docFullName,$docprefix,$docSpec) = $con->getArray("SELECT IF(signature_file != '',CONCAT('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') AS signature, fullname, concat(', ',prefix), specialization FROM options_doctors WHERE id = '$_ihead[physician]';");
    }

	switch($_REQUEST['code']) {
	case "U012":
	case "U020":
	case "U023":
	case "U004":
	case "U015":
	case "U005":
	case "U024":
	case "U009":
	case "U011":
	case "X055":
	case "U008":
	case "U001":
	case "U003":
	case "U006":
	case "U007":
	case "U010":
	case "U019":
	case "U021":
	case "U022":
	case "U027":
	case "U028":
	case "U029":
	case "U030":
	case "U031":
		$title = "ULTRASOUND REPORT";
	break;
	default:
		$title = "XRAY REPORT";
	break;
	}

/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','LETTER','','',15,15,85,30,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");

if($_ihead['verified'] != 'Y') {
	$mpdf->SetWatermarkText('FOR VALIDATION');
	$mpdf->showWatermarkText = true;
}

$mpdf->SetDisplayMode(50);

$html = '
<html>
<head>
	<style>
		body { font-family: "Times New Roman", Times, serif; font-size: 11pt; }
	</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%" cellpadding=0 cellspaing=0>
	<tr><td align=center><img src="../images/doc-header.jpg" /></td></tr>
</table>
<table width=100% cellpadding=2 cellspacing=0 style="font-size: 9pt;margin-top:20px;">
	<tr>
		<td width=100% colspan=4 style="background-color: #cdcdcd; border-top: 1px solid black; border-bottom: 1px solid black;" align=center><b>PATIENT INFORMATION</b></td>
	</tr>
	<tr>
		<td width=20%><b>CASE NO.</b></td>
		<td width=45%>:&nbsp;&nbsp;'.$lotno.'</td>
		<td width=15%><b>DATE</b></td>
		<td width=20%>:&nbsp;&nbsp;'.$_ihead['rdate'].'</td>
	</tr>
	<tr>
		<td><b>PATIENT NAME</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['patient_name'].'</td>
		<td><b>GENDER</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['gender'].'</td>
	</tr>
	<tr>
		<td><b>PATIENT ADDRESS</b></td>
		<td>:&nbsp;&nbsp;' . $_ihead['patient_address'] . '</td>
		<td><b>AGE</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['age'].'</td>
	</tr>
	<tr>
		<td><b>REQUESTING PHYSICIAN</b></td>
        <td>:&nbsp;&nbsp;'.$docFullName.''.$docprefix .'</td>
		<td><b>EXAMINATION</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['procedure'].'</td>
	</tr>
	<tr>
		<td width="100%" colspan=4 style="padding-top: 30px;" align=center>
			<span style="font-weight: bold; font-size: 14pt; color: #000000;">'.$title.'</span>
		</td>
	</tr>
</table>

</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100% cellpadding=5>
	<tr>
		<td width=50% align=center valign=top>'.$medtechSignature.'<br/>'.$medtechFullname.'<br>_________________________________________<br/><b>'.$medtechRole.'</td>
		<td width=50% align=center valign=top>'.$consultantSignature.'<br/>'.$_ihead['consultant'].', '. $_ihead['prefix'] .'<br/>_________________________________________<br><b>'.$_ihead['specialization'].' </b></td>
	</tr>
</table>
<table width=100%>
	<tr>
		<td align=center style="font-size:8pt;"><i>*** This radiologic interpretation is only a part of the overall assessment of a patients condition. It must becorrelated with the clinical, laboratory and other ancillary parameters for a comprehensive analysis. Therefore, radiology reports are best explained by the attending physician to the patient. ***<i/></td>
	</tr>
</table>
<table width=100%>
	<tr><td align=left><barcode size=0.8 code="'.substr($_ihead['trace_no'],0,10).'" type="C128A"></td><td align=right>Run Date: '.date('m/d/Y h:i:s a').'</td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<div id="main">'.$_ihead['impression'].'</div>
</body>
</html>
';

$html = html_entity_decode($html);
$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>