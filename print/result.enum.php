<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");

	$_ihead = $con->getArray("SELECT DATE_FORMAT(result_date,'%m/%d/%Y') AS rdate, b.patient_name, b.patient_address, IF(c.gender='M','Male','Female') AS gender, YEAR(so_date)-YEAR(c.birthdate) AS age, DATE_FORMAT(c.birthdate,'%m/%d/%Y') AS dob, b.physician, a.serialno,a.created_by,b.trace_no,a.result, a.verified,a.verified_by, d.sample_type AS sample_type FROM lab_enumresult a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_sampletype d ON a.sampletype = d.id WHERE a.so_no = '$_REQUEST[so_no]' AND `code` = '$_REQUEST[code]' AND serialno = '$_REQUEST[serialno]' AND a.branch = '$_SESSION[branchid]';");
	$_idetails = $con->getArray("SELECT record_id AS id, LPAD(a.so_no,6,0) AS myso,DATE_FORMAT(b.so_date,'%m/%d/%Y') AS sodate, LPAD(b.patient_id,6,0) AS mypid,b.patient_name AS pname, YEAR(b.so_date) - YEAR(c.birthdate) AS age,IF(c.gender='M','Male','Female') AS gender, DATE_FORMAT(c.birthdate,'%m/%d/%Y') AS bday,e.patientstatus,b.physician,a.code,a.procedure,sampletype,serialno,DATE_FORMAT(extractdate,'%m/%d/%Y') AS exday,TIME_FORMAT(extractime,'%h:%i %p') AS etime,extractby,a.location,a.testkit,a.lotno,DATE_FORMAT(a.expiry,'%m/%d/%Y') AS expiry FROM lab_samples a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_patientstat e ON b.patient_stat = e.id WHERE a.so_no = '$_REQUEST[so_no]' AND `code` = '$_REQUEST[code]' AND serialno = '$_REQUEST[serialno]' AND a.branch = '$_SESSION[branchid]';");
    $b = $con->getArray("SELECT a.testkit, a.lotno, a.procedure, DATE_FORMAT(a.expiry,'%Y/%m/%d') AS expiry, DATE_FORMAT(extractdate,'%m/%d/%Y') AS extractdate, a.sampletype AS sample_type, TIME_FORMAT(a.extractime,'%h:%i:%s') AS xtime FROM lab_samples a WHERE so_no = '$_REQUEST[so_no]' and branch = '$_SESSION[branchid]' and code = '$_REQUEST[code]' and serialno = '$_REQUEST[serialno]';");

	list($encSignature,$encBy,$encByLicense,$encByRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[created_by]';");

    if($b['verified_by'] != '') {
        list($cbySignature,$cby,$cbyLicense,$cbyRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[verified_by]';");
    }

	if($_ihead['physician'] != '') {
        list($docSignature,$docFullName,$docprefix,$docSpec) = $con->getArray("SELECT IF(signature_file != '',CONCAT('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') AS signature, fullname, concat(', ',prefix), specialization FROM options_doctors WHERE id = '$_ihead[physician]';");
    }
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','LETTER','','',10,10,80,30,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");

if($_ihead['verified'] != 'Y') {
	$mpdf->SetWatermarkText('FOR VALIDATION');
	$mpdf->showWatermarkText = true;
} else {
	$mpdf->SetWatermarkImage ('../images/logo-small.png',0.1,'F','P');
	$mpdf->showWatermarkImage = true;
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
			padding:20px;border:1px solid black;text-align: center;
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
<table width=100% cellpadding=2 cellspacing=0 style="font-size: 9px;margin-top:20px;">
	<tr>
		<td width=100% colspan=4 style="background-color: #cdcdcd; border-top: 1px solid black; border-bottom: 1px solid black;" align=center><b>PATIENT INFORMATION</b></td>
	</tr>
	<tr>
		<td width=20%><b>NAME</b></td>
		<td width=30%>:&nbsp;&nbsp;'.$_ihead['patient_name'].'</td>
		<td width=20%><b>DATE RECEIVED</b></td>
		<td width=30%>:&nbsp;&nbsp;'.$b['extractdate'].'</td>
	</tr>
	<tr>
		<td><b>DATE OF BIRTH</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['dob'].'</td>
		<td><b>GENDER</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['gender'].'</td>
	</tr>
	<tr>
		<td><b>PATIENT STATUS</b></td>
		<td>:&nbsp;&nbsp;' . $_idetails['patientstatus'] . '</td>
		<td><b>AGE</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['age'].'&nbsp;y/o</td>
	</tr>
	<tr>
		<td><b>REQUESTING COMPANY</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['company'].'</td>
		<td><b>DATE & TIME COLLECTED</b></td>
		<td>:&nbsp;&nbsp;'.$b['extractdate'].'&nbsp;/&nbsp;'.$b['xtime'].'</td>
	</tr>
	<tr>
		<td><b>SPECIMEN</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['sample_type'].'</td>
	</tr>
</table>

</htmlpageheader>

<htmlpagefooter name="myfooter">
 	<table width=100% cellpadding=5 style="margin-bottom: -55px;">
        <tr>
            <td width=33% align=center style="position:absolute; top:-10px;">'.$encSignature.'</td>
            <td width=33% align=center style="position:absolute; top:-10px;">'.$cbySignature.'</td>
            <td align=center valign=top style="position:absolute; top:-10px;"><img src="../images/signatures/leyson.png" align=absmidddle /></td>
        </tr>
    </table>
    <table width=100% cellpadding=5 style="margin-bottom: 5px; font-size: 8pt;">
        <tr>
            <td width=33% align=center>&nbsp;<br/><b>'.$encBy.'<br/>_______________________________<br/><span>PRC LICENSE NO. '.$encByLicense.'</span><br/><b>REPORTED BY</b></td>
            <td width=33% align=center>&nbsp;<br/><b>'.$cby.'<br/>_______________________________<br/><span>PRC LICENSE NO. '.$cbyLicense.'</span><br/><b>VALIDATED BY</b></td>
            <td align=center valign=top>&nbsp;<br/><b>JEREMIAS P. LEYSON, DPSP MD<br/> ____________________________________________________<br><b>PATHOLOGIST - LIC NO. 0124968</b></td>
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

	<table width=80% cellpadding=1 cellspacing=0 align=left style="font-style: italic; margin-top: 50px; font-size: 7pt;">
		<tr><td width=80><b>Test Kit :</b></td><td>'.$b['testkit'].'</td></tr>
		<tr><td width=80><b>Lot No :</b></td><td>'.$b['lotno'].'</td></tr>
		<tr><td width=80><b>Expiry Date :</b></td><td>'.$b['expiry'].'</td></tr>
		<tr><td width=80><b>Performance :</b></td><td>Sensitivity =</td></tr>
		<tr><td width=80></td><td>Specificity =</td></tr>
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