<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");

	$_ihead = $con->getArray("SELECT DATE_FORMAT(result_date,'%m/%d/%Y') AS rdate, b.patient_name, b.patient_address, IF(c.gender='M','Male','Female') AS gender, c.gender as xgender, c.birthdate, b.physician, a.serialno, a.procedure, a.created_by, b.trace_no, a.code FROM lab_egfr a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id WHERE a.so_no = '$_REQUEST[so_no]' AND `code` = '$_REQUEST[code]' AND serialno = '$_REQUEST[serialno]' AND a.branch = '$_SESSION[branchid]';");
    $b = $con->getArray("SELECT * FROM lab_egfr WHERE so_no = '$_REQUEST[so_no]' and branch = '$_SESSION[branchid]' and code = '$_REQUEST[code]' and serialno = '$_REQUEST[serialno]';");	
	$c = $con->getArray("SELECT CONCAT(min_value,' - ',`max_value`,`unit`) as limits FROM lab_testvalues WHERE `code` = '$_ihead[code]';");		


	switch($_ihead['code']) {
		case "L121": /* eGFR */
			$limits = "mL/min/1.73m2";
		break;
		default:
			$limits = $con->getAttribute($_ihead['code'],$con->calculateAge($_ihead['birthdate']),$_ihead['xgender']);
		break;


	}
	

	if($b['verified_by'] != '') {
        list($medtechSignature,$medtechFullname,$medtechLicense,$medtechRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[verified_by]';");
    }

	if($_ihead['physician'] != '') {
        list($docSignature,$docFullName,$docprefix,$docSpec) = $con->getArray("SELECT IF(signature_file != '',CONCAT('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') AS signature, fullname, concat(', ',prefix), specialization FROM options_doctors WHERE id = '$_ihead[physician]';");
    }


/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','FOLIO-H','','',10,10,65,30,5,5);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");

if($b['verified'] != 'Y') {
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

        .itemResult {
            padding:10px;border:1px solid black;text-align: center;
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
		<td width="100%" style="padding-top: 5px;" align=center>
			<span style="font-weight: bold; font-size: 12pt; color: #000000;">LABORATORY DEPARTMENT</span>
		</td>
	</tr>

</table>
<table width=100% cellpadding=2 cellspacing=0 style="font-size: 10pt;margin-top:5px;">
		<tr>
			<td width=100% colspan=4 style="background-color: #cdcdcd; border-top: 1px solid black; border-bottom: 1px solid black;" align=center><b>PATIENT INFORMATION</b></td>
		</tr>
		<tr>
			<td width=25%><b>CASE NO.</b></td>
			<td width=40%>:&nbsp;&nbsp;'.$_ihead['serialno'].'</td>
			<td width=20%><b>DATE</b></td>
			<td width=15%>:&nbsp;&nbsp;'.$_ihead['rdate'].'</td>
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
			<td>:&nbsp;&nbsp;'.$con->calculateAge($_ihead['birthdate']).'</td>
		</tr>
		<tr>
			<td><b>REQUESTING PHYSICIAN</b></td>
			<td>:&nbsp;&nbsp;'.$docFullName.''.$docprefix .'</td>
			<td></td>
			<td></td>
		</tr>
</table>

</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100% cellpadding=5 style="margin-bottom: 25px;">
	<tr>
		<td align=center valign=top>'.$medtechSignature.'<br/><b>'.$medtechFullname.'<br/>___________________________________________<br>'.$medtechRole.'<br/>License No. '.$medtechLicense.'</b></td>
		<td align=center valign=top><img src="../images/signatures/zamora_signature.png" align=absmidddle /><br/><b>THEODORE ZAMORA, MD<br/>____________________________________________________________<br><b>PATHOLOGIST - LIC NO. 0093576</b></td>
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
        <tr><td align=center><span style="font-size: 12pt; font-weight: bold;">'.$b['procedure'].'</span></td></tr>
    </table>

	<table width=80% cellpadding=0 cellspacing=0 align=center style="border-collapse: collapse; font-size: 15px;">
            <tr>
                <td class="itemHeader">TEST</td>
                <td class="itemHeader">RESULT</td>
                <td class="itemHeader">REFERENCE INTERVAL</td>
            </tr>
            <tr>
                <td class="itemResult">ESTIMATED GFR</td>
                <td class="itemResult">'.$b['egfr'].'</td>
                <td class="itemResult">'.$limits.'</td>
            </tr>
            <tr>
                <td class="itemResult">Creatinine</td>
                <td class="itemResult">'.$b['crea'].'</td>
                <td class="itemResult">'.$con->getAttribute('L020',$con->calculateAge($_ihead['birthdate']),$_ihead['xgender']).'</td>
            </tr>

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