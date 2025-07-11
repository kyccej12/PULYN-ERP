<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$o = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $o->getArray("select * from companies where company_id = '$_SESSION[company]';");

	$_ihead = $o->getArray("SELECT lpad(b.so_no,6,0) as myso, DATE_FORMAT(result_date,'%m/%d/%Y') AS rdate, b.patient_name, b.patient_address, IF(c.gender='M','Male','Female') AS gender, FLOOR(DATEDIFF(so_date,c.birthdate)/364.25) AS age, b.physician,d.patientstatus,a.serialno,a.created_by, b.trace_no FROM lab_stoolexam a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id left join options_patientstat d on b.patient_stat = d.id WHERE a.so_no = '$_REQUEST[so_no]' AND serialno = '$_REQUEST[serialno]'  AND a.branch = '$_SESSION[branchid]';");
    $b = $o->getArray("select * from lab_stoolexam where so_no = '$_ihead[myso]' and serialno = '$_ihead[serialno]' and branch = '$_SESSION[branchid]';");
    
    list($encSignature,$encBy,$encByLicense,$encByRole) = $o->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[created_by]';");

    if($b['verified_by'] != '') {
        list($cbySignature,$cby,$cbyLicense,$cbyRole) = $o->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[verified_by]';");
    }

    if($_ihead['physician'] != '') {
        list($docSignature,$docFullName,$docprefix,$docSpec) = $o->getArray("SELECT IF(signature_file != '',CONCAT('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') AS signature, fullname, concat(', ',prefix), specialization FROM options_doctors WHERE id = '$_ihead[physician]';");
    }

/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','letter','','',10,10,90,30,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");

if($b['verified'] != 'Y') {
	$mpdf->SetWatermarkText('FOR VALIDATION');
	$mpdf->showWatermarkText = true;
}else {
	$mpdf->SetWatermarkImage ('../images/logo-small.png',0.1,'F','P');
	$mpdf->showWatermarkImage = true;
}

$mpdf->SetDisplayMode(50);

$html = '
<html>
<head>
	<style>
		body {font-family: sans-serif; font-size: 9pt; }
        .itemHeader {
            padding:5px;border:1px solid black; text-align: center; font-weight: bold;
        }

        .itemResult {
            padding:20px;border:1px solid black;text-align: center;
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
<table width=100% cellpadding=2 cellspacing=0 style="font-size: 9pt; margin-top:20px;">
	<tr>
		<td width=100% colspan=4 style="background-color: #cdcdcd; border-top: 1px solid black; border-bottom: 1px solid black;" align=center><b>PATIENT INFORMATION</b></td>
	</tr>
	<tr>
		<td width=25%><b>SO NO.</b></td>
		<td width=45%>:&nbsp;&nbsp;'.$_ihead['myso'].'</td>
		<td width=15%><b>DATE</b></td>
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
		<td>:&nbsp;&nbsp;'.$_ihead['age'].'</td>
	</tr>
	<tr>
		<td><b>REQUESTING PHYSICIAN</b></td>
        <td>:&nbsp;&nbsp;'.$docFullName.''.$docprefix .'</td>
		<td><b>PATIENT STATUS</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['patientstatus'].'</td>
	</tr>
    <tr>
        <td width="100%" colspan=4 style="padding-top: 25px;" align=center>
         <span style="font-weight: bold; font-size: 12pt; color: #000000; text-decoration: underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;STOOL EXAM (FECALYSIS)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </td>
    </tr>
</table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100% cellpadding=5 style="margin-bottom: 25px;">
	<tr>
        <td width=33% align=center>'.$encSignature.'<br/><b>'.$encBy.'<br/>_______________________________<br/><span>PRC LICENSE NO. '.$encByLicense.'</span><br/><b>REPORTED BY</b></td>
        <td width=33% align=center>'.$cbySignature.'<br/><b>'.$cby.'<br/>_______________________________<br/><span>PRC LICENSE NO. '.$cbyLicense.'</span><br/><b>VALIDATED BY</b></td>
        <td align=center valign=top><img src="../images/signatures/leyson.png" align=absmidddle /><br/><b>JEREMIAS P. LEYSON, MD, DPSP<br/> ____________________________________________________<br><b>PATHOLOGIST - LIC NO. 0124968</b></td>
	</tr>
</table>
<table width=100%>
	<tr><td align=left><barcode size=0.8 code="'.substr($_ihead['trace_no'],0,10).'" type="C128A"></td><td align=right>Run Date: '.date('m/d/Y h:i:s a').'</td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->

<table width=80% cellpadding=0 cellspacing=5 align=center style="padding-left: 105px;">
<tr>
    <td align="left" width=40%></td>
    <td align=center width=40%></td>
</tr>
<tr>
    <td align="left" colspan=2  style="padding-left: 15px;"><b>PHYSICAL CHARACTERISTICS&nbsp;:</b></td>
    <td></td>
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Color</td>
    <td align=center style="border-bottom: 1px solid black;vertical-align: top;">'. $b['color'] . '</td>
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Consistency</td>
    <td align=center style="border-bottom: 1px solid black;vertical-align: top;">'. $b['consistency'] . '</td>
</tr>
<tr>
    <td align="left" colspan=2  style="padding-left: 15px; padding-top: 10px;"><b>MICROSCOPIC&nbsp;:</b></td>
    <td></td>
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">WBC / hpf&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['wbc'] . '</td>
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">RBC / hpf&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['rbc'] . '</td>
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Bacteria&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['bacteria'] . '</td>
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Fat Globules&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['globules'] . '</td>
</tr>
<tr>
    <td align="left"  style="padding-left: 35px;">Yeast Cells&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['yeast_cells'] . '</td>
</tr>
<tr>
    <td align="left"  style="padding-left: 35px;">Occult Blood&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['occult_blood'] . '</td>
</tr>
<tr>
    <td align="left" colspan=2  style="padding-left: 15px; padding-top: 10px;"><b>PARASITES&nbsp;:</b></td>
    <td></td>
</tr>
<tr>
    <td align="left"  style="padding-left: 35px;">Ascaris Lumbricoides</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['ascaris'] . '</td>
</tr>
<tr>
    <td align="left"  style="padding-left: 35px;">Entamoeba Histolytica</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['histolytica'] . '</td>
</tr>
<tr>
    <td align="left"  style="padding-left: 35px;">Entamoeba Coli</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['coli'] . '</td>
</tr>
<tr>
    <td align="left"  style="padding-left: 35px;">Trichuris Trichuria</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['trichuris'] . '</td>
</tr>
<tr>
    <td align="left"  style="padding-left: 35px;">Hook Worm</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['hookworm'] . '</td>
</tr>
<tr>
    <td align="left"  style="padding-left: 35px;">Giardia Lamblia</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['giardia'] . '</td>
</tr>
<tr><td height=5>&nbsp;</td></tr>
<tr>
    <td align="left"  style="padding-left: 15px; font-weight: bold;" valign=top>Remarks&nbsp;:</td>
    <td align=left style="border-bottom: 1px solid black;">'. $b['remarks'] . '</td>
    <td></td>
</tr>
</table>

</body>
</html>
';

$html = html_entity_decode($html);
$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>