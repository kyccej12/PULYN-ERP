<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");

	$_ihead = $con->getArray("SELECT DATE_FORMAT(result_date,'%m/%d/%Y') AS rdate,a.patient_stat, b.so_date as xorderdate, c.birthdate as xbday, b.patient_name, b.patient_address, IF(c.gender='M','Male','Female') AS gender, YEAR(so_date)-YEAR(c.birthdate) AS age, DATE_FORMAT(c.birthdate,'%m/%d/%Y') AS dob, b.physician, a.serialno,a.created_by,b.trace_no,a.result,a.sensitivity,a.specificity, a.verified,a.verified_by, d.sample_type AS sample_type FROM lab_antigenresult a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_sampletype d ON a.sampletype = d.id WHERE a.so_no = '$_REQUEST[so_no]' AND `code` = '$_REQUEST[code]' AND serialno = '$_REQUEST[serialno]' AND a.branch = '$_SESSION[branchid]';");
	
	$con->calculateAge2($_ihead['xorderdate'],$_ihead['xbday']);

	$b = $con->getArray("SELECT a.testkit, a.lotno, a.procedure, DATE_FORMAT(a.expiry,'%Y/%m/%d') AS expiry, DATE_FORMAT(extractdate,'%m/%d/%Y') AS extractdate, a.sampletype AS sample_type, TIME_FORMAT(a.extractime,'%h:%i:%s %p') AS xtime FROM lab_samples a WHERE so_no = '$_REQUEST[so_no]' and branch = '$_SESSION[branchid]' and code = '$_REQUEST[code]' and serialno = '$_REQUEST[serialno]';");

	if($_ihead['verified_by'] != '') {
        list($medtechSignature,$medtechFullname,$medtechLicense,$medtechRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$_ihead[verified_by]';");
    }
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','LETTER','','',10,10,80,30,10,10);
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
		body {font-family: sans-serif; font-size: 10px; }
		.itemHeader {
			padding:10px;border:1px solid black; text-align: center; font-weight: bold;
		}

		.itemHeaderRemarks {
			padding:10px;border:1px solid black; text-align: center; font-weight: bold;
		}

		.itemResult {
			padding:20px;border:1px solid black;text-align: center; font-weight: bold;
		}
		.itemRemarks {
			border:1px solid black;text-align: left;padding:8px;
		}

		#items td { border: 1px solid; text-align: center; }
	</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%" cellpadding=0 cellspaing=0>
	<tr><td align=center><img src="../images/doc-header.jpg" /></td></tr>

    <tr>
		<td width="100%" style="padding-top: 30px;" align=center>
			<span style="font-weight: bold; font-size: 12pt; color: #000000;">LABORATORY DEPARTMENT</span>
		</td>
	</tr>

</table>
<table width=100% cellpadding=2 cellspacing=0 style="font-size: 10px;margin-top:20px;">
	<tr>
		<td width=100% colspan=4 style="background-color: #cdcdcd; border-top: 1px solid black; border-bottom: 1px solid black;" align=center><b>PATIENT INFORMATION</b></td>
	</tr>
	<tr>
		<td width=20%><b>CASE NO.</b></td>
		<td width=30%>:&nbsp;&nbsp;'.$_ihead['serialno'].'</td>
		<td width=20%><b>DATE RECEIVED</b></td>
		<td width=30%>:&nbsp;&nbsp;'.$b['extractdate'].'</td>
	</tr>
	<tr>
        <td width=20%><b>NAME</b></td>
        <td width=30%>:&nbsp;&nbsp;'.$_ihead['patient_name'].'</td>
		<td><b>GENDER</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['gender'].'</td>
	</tr>
	<tr>
		<td><b>PATIENT STATUS</b></td>
		<td>:&nbsp;&nbsp;' . $_ihead['patient_stat'] . '</td>
		<td><b>AGE</b></td>
		<td>:&nbsp;&nbsp;'.$con->ageDisplay.'</td>
	</tr>
	<tr>
        <td><b>DATE OF BIRTH</b></td>
        <td>:&nbsp;&nbsp;'.$_ihead['dob'].'</td>
		<td><b>DATE & TIME COLLECTED</b></td>
		<td>:&nbsp;&nbsp;'.$b['extractdate'].'&nbsp;&nbsp;'.$b['xtime'].'</td>
	</tr>
	<tr>
		<td><b>SPECIMEN</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['sample_type'].'</td>
        <td><b>REQUESTING COMPANY</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['company'].'</td>
	</tr>
</table>

</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100% cellpadding=5 style="margin-bottom: 25px;">
	<tr>
		<td align=center valign=top>'.$medtechSignature.'<br/><b>'.$medtechFullname.'<br/>___________________________________________<br>'.$medtechRole.'<br/>License No. '.$medtechLicense.'</b></td>
		<td align=center valign=top><img src="../images/signatures/leyson.png" align=absmidddle /><br/><b>JEREMIAS P. LEYSON, MD, DPSP<br/>____________________________________________________________<br><b>PATHOLOGIST - LIC NO. 0124968</b></td>
	</tr>
</table>
<table width=100%>
	<tr><td align=left><barcode size=0.8 code="'.substr($_ihead['trace_no'],0,10).'" type="C128A"></td><td align=right>Run Date: '.date('m/d/Y h:i:s a').'</td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->

<div id="main">
	<table width=60% cellpadding=0 cellspacing=0 align=center style="margin: 5px;">
        <tr><td align=center><span style="font-size: 12pt; font-weight: bold;">SARS-COV2 RAPID ANTIGEN TEST</span></td></tr>
    </table>

	<table width=80% cellpadding=0 cellspacing=0 align=center style="border-collapse: collapse; font-size: 12px;">
            <tr>
                <td class="itemHeader" width=50%>TEST</td>
                <td class="itemHeader" width=50%>RESULT</td>
            </tr>
            <tr>
                <td class="itemResult" width=50%>SARS-COV2 RAPID ANTIGEN TEST</td>
                <td class="itemResult" width=50%>'.$_ihead['result'].'</td>
            </tr>

    </table>

	<table width=60% cellpadding=0 cellspacing=0 align=center style="margin: 5px; margin-top:15px;">
        <tr><td align=center><span style="font-size: 10pt; font-weight: bold;">REMARKS</span></td></tr>
    </table>

	<table width=80% cellpadding=0 cellspacing=0 align=center style="border-collapse: collapse; font-size: 10px;">
            <tr>
                <td class="itemHeaderRemarks" width=50%>RESULTS</td>
                <td class="itemHeaderRemarks" width=50%>INTERPRETATION</td>
            </tr>
            <tr>
                <td class="itemRemarks" width=50%>Covid 19 Antigen Reactive</td>
                <td class="itemRemarks" width=50%>Suspected Infection</td>
            </tr>
			<tr>
                <td class="itemRemarks" width=50%>Covid 19 Antigen Non- Reactive</td>
                <td class="itemRemarks" width=50%>Needs further assessment by the physician</td>
            </tr>

    </table>

	<table width=100% cellpadding=1 cellspacing=0 align=left style="font-style: italic; margin-top: 50px; font-size: 8pt;">
		<tr><td width=100>Test Kit :</td><td><b>'.$b['testkit'].'</b></td></tr>
		<tr><td width=100>Lot No :</td><td><b>'.$b['lotno'].'</b></td></tr>
		<tr><td width=100>Expiry Date :</td><td><b>'.$b['expiry'].'</b></td></tr>
		<tr><td width=100>Performance :</td><td><b>Sensitivity = &nbsp;&nbsp;&nbsp;'.$_ihead['sensitivity'].'&nbsp;%</b></td></tr>
		<tr><td width=100></td><td><b>Specificity = &nbsp;&nbsp;&nbsp;'.$_ihead['specificity'].'&nbsp;%</b></td></tr>
	</table>

	<table width=100% cellpadding=0 cellspacing=0 align=center style="margin: 5px; margin-top:15px;">
        <tr><td align=left><span style="font-size: 10pt; font-weight: bold;">Note:</span></td></tr>
		<tr><td></td><td>This is a <b><u>SCREENING TEST ONLY.</b></u></td></tr>
		<tr><td></td><td><b><u>PLEASE CORRELATE CLINICALLY.</b></u></td></tr>
    </table>
</div>


</body>
</html>
';

$html = html_entity_decode($html);
$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>