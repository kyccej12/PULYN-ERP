<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");

	$_ihead = $con->getArray("SELECT lpad(b.so_no,6,0) as myso, DATE_FORMAT(result_date,'%m/%d/%Y') AS rdate, b.so_date as xorderdate, c.birthdate as xbday, b.patient_name, b.patient_address, IF(c.gender='M','Male','Female') AS gender, FLOOR(ROUND(DATEDIFF(b.so_date,c.birthdate) / 364.25,2)) AS age, b.physician,d.patientstatus,a.serialno,c.gender as xgender,a.created_by, b.trace_no FROM lab_cbcresult a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id left join options_patientstat d on b.patient_stat = d.id WHERE a.so_no = '$_REQUEST[so_no]' AND serialno = '$_REQUEST[serialno]'  AND a.branch = '$_SESSION[branchid]';");
    $b = $con->getArray("SELECT * FROM lab_cbcresult WHERE so_no = '$_ihead[myso]' AND serialno = '$_ihead[serialno]' AND branch = '$_SESSION[branchid]';");
   
    $con->calculateAge2($_ihead['xorderdate'],$_ihead['xbday']);

    list($encSignature,$encBy,$encByLicense,$encByRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[created_by]';");

    if($b['verified_by'] != '') {
        list($cbySignature,$cby,$cbyLicense,$cbyRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[verified_by]';");
    }
  
    if($_ihead['physician'] != '') {
        list($docSignature,$docFullName,$docprefix,$docSpec) = $con->getArray("SELECT IF(signature_file != '',CONCAT('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') AS signature, fullname, concat(', ',prefix), specialization FROM options_doctors WHERE id = '$_ihead[physician]';");
    }
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','letter','','',10,10,85,10,10,10);
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
		<td width="100%" style="padding-top: 10px;" align=center>
			<span style="font-weight: bold; font-size: 12pt; color: #000000;">LABORATORY DEPARTMENT</span>
		</td>
	</tr>

</table>
<table width=100% cellpadding=2 cellspacing=0 style="font-size: 9pt;margin-top:20px;">
	<tr>
		<td width=100% colspan=4 style="background-color: #cdcdcd; border-top: 1px solid black; border-bottom: 1px solid black;" align=center><b>PATIENT INFORMATION</b></td>
	</tr>
	<tr>
		<td width=25%><b>SO NO.</b></td>
		<td width=38%>:&nbsp;&nbsp;'.$_ihead['myso'].'</td>
		<td width=20%><b>DATE</b></td>
		<td width=17%>:&nbsp;&nbsp;'.$_ihead['rdate'].'</td>
	</tr>
	<tr>
		<td><b>PATIENT NAME</b></td>
		<td>:&nbsp;&nbsp;'.$con->escapeString(htmlentities($_ihead['patient_name'])).'</td>
		<td><b>GENDER</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['gender'].'</td>
	</tr>
	<tr>
		<td><b>PATIENT ADDRESS</b></td>
		<td>:&nbsp;&nbsp;' . $_ihead['patient_address'] . '</td>
		<td><b>AGE</b></td>
		<td>:&nbsp;&nbsp;'.$con->ageDisplay.'</td>
	</tr>
	<tr>
		<td><b>REQUESTING PHYSICIAN</b></td>
		<td>:&nbsp;&nbsp;'.$docFullName.''.$docprefix .'</td>
		<td><b>PATIENT STATUS</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['patientstatus'].'</td>
	</tr>
    <tr>
        <td width="100%" colspan=4 style="padding-top: 30px;" align=center>
         <span style="font-weight: bold; font-size: 12pt; color: #000000; text-decoration: underline;">&nbsp;&nbsp;&nbsp;COMPLETE BLOOD COUNT (CBC)&nbsp;&nbsp;&nbsp;</span>
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

<table width=80% cellpadding=0 cellspacing=5 align=center>
<tr>
    <td align="left" width=30%></td>
    <td align=center width=30%></td>
    <td align="center" width=5%></td>	
    <td align="left" width=30%><b>NORMAL VALUES</b></td>	
</tr>
<tr>
    <td align="left"  style="padding-left: 15px; font-weight: bold;">( ) WBC</td>
    <td align=center style="border-bottom: 1px solid black;vertical-align: top;">'. number_format($b['wbc'],2) . '/uL</td>
    <td align=center>'.$con->checkCBCValues($_ihead['age'],$_ihead['xgender'],"WBC",$b['wbc'],$b['machine']).'</td>
    <td align="left"  style="padding-left: 15px;">4.0 - 10.0/x10<sup>9</sup>uL</td>	
</tr>

<tr>
    <td align="left"  style="padding-left: 15px; font-weight: bold;" valign=top>( ) RBC</td>
    <td align=center style="text-decoration: underline;" valign=top>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. number_format($b['rbc'],2) . ' x 10<sup>6</sup>/uL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td align=center valign=top>'.$con->checkCBCValues($_ihead['age'],$_ihead['xgender'],"RBC",$b['rbc'],$b['machine']).'</td>
    <td align="left" style="padding-left: 15px;" valign=top><b>ADULT:</b><br/><b>F:</b> 4.2 - 5.4 x 10<sup>6</sup>/uL<br/><b>M:</b> 4.7 - 6.1 x 10<sup>6</sup>/uL<br><br><b>Pedia:</b><br/><b>F:</b> 4.0 - 5.1 x 10<sup>6</sup>/uL<br/><b>M:</b> 4.0 - 5.3 x 10<sup>6</sup>/uL</td>	
</tr>

<tr>
    <td align="left"  style="padding-left: 15px; font-weight: bold;">( ) Hemoglobin</td>
    <td align=center style="border-bottom: 1px solid black;">'. number_format($b['hemoglobin'],2) . 'gm%</td>
    <td align=center>'.$con->checkCBCValues($_ihead['age'],$_ihead['xgender'],"HEMOGLOBIN",$b['hemoglobin'],$b['machine']).'</td>
    <td align="left" style="padding-left: 15px;"><b>F:</b> 12-15gm%&nbsp;&nbsp;<b>M:</b> 14-17gm%</td>	
</tr>
<tr>
    <td align="left"  style="padding-left: 15px; font-weight: bold;">( ) Hematocrit</td>
    <td align=center style="border-bottom: 1px solid black;">'. number_format($b['hematocrit'],2) . 'vol%</td>
    <td align=center>'.$con->checkCBCValues($_ihead['age'],$_ihead['xgender'],"HEMATOCRIT",$b['hematocrit'],$b['machine']).'</td>
    <td align="left" style="padding-left: 15px;"><b>F:</b> 38-48vol%&nbsp;&nbsp;<b>M:</b> 40-50vol%</td>	
</tr>
<tr><td height=2>&nbsp;</td></tr>
<tr>
    <td align="left" colspan=3  style="padding-left: 15px; font-weight: bold;">Differential Count&nbsp;:</td>
</tr>
<tr>
    <td align="left"  style="padding-left: 35px;">Neutrophils&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['neutrophils'] . '%</td>
    <td align=center>'.$con->checkCBCValues($_ihead['age'],$_ihead['xgender'],"NEUTROPHILS",$b['neutrophils'],$b['machine']).'</td>
    <td align="left" style="padding-left: 15px;">45-65%</td>	
</tr>
<tr>
    <td align="left"  style="padding-left: 35px;">Lymphocytes&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['lymphocytes'] . '%</td>
    <td align=center>'.$con->checkCBCValues($_ihead['age'],$_ihead['xgender'],"LYMPHOCYTES",$b['lymphocytes'],$b['machine']).'</td>
    <td align="left" style="padding-left: 15px;">20-35%</td>	
</tr>
<tr>
    <td align="left"  style="padding-left: 35px;">Monocytes&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['monocytes'] . '%</td>
    <td align=center>'.$con->checkCBCValues($_ihead['age'],$_ihead['xgender'],"MONOCYTES",$b['monocytes'],$b['machine']).'</td>
    <td align="left" style="padding-left: 15px;">2-9%</td>	
</tr>
<tr>
    <td align="left"  style="padding-left: 35px;">Eosinophils&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['eosinophils'] . '%</td>
    <td align=center>'.$con->checkCBCValues($_ihead['age'],$_ihead['xgender'],"EOSINOPHILS",$b['eosinophils'],$b['machine']).'</td>
    <td align="left" style="padding-left: 15px;">0-6%</td>	
</tr>
<tr>
    <td align="left"  style="padding-left: 35px;">Basophils&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['basophils'] . '%</td>
    <td align=center>'.$con->checkCBCValues($_ihead['age'],$_ihead['xgender'],"BASOPHILS",$b['basophils'],$b['machine']).'</td>
    <td align="left" style="padding-left: 15px;">0-2%</td>	
</tr>
<tr>
    <td align="left"  style="padding-left: 15px; font-weight: bold;">Platelet Count&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. number_format($b['platelate']) . '/uL</td>
    <td align=center>'.$con->checkCBCValues($_ihead['age'],$_ihead['xgender'],"PLATELATE",$b['platelate'],$b['machine']).'</td>
    <td align="left" style="padding-left: 15px;">130-440 10x<sup>3</sup>/uL</td>	
</tr>
<tr>
    <td align="left"  style="padding-left: 35px; font-weight: bold;">MCV:</td>
    <td align=center style="border-bottom: 1px solid black;">'. number_format($b['mcv'],2) . ' fL</td>
    <td align=center>'.$con->checkCBCValues($_ihead['age'],$_ihead['xgender'],"MCV",$b['mcv'],$b['machine']).'</td>
    <td align="left" style="padding-left: 15px;"><b>Children:</b> 73.0 - 89.0fL<br/><b>Adult:&nbsp;M/F</b> 76.0 - 96.0fL</td>	
</tr>
<tr>
    <td align="left"  style="padding-left: 35px; font-weight: bold;">MCH:</td>
    <td align=center style="border-bottom: 1px solid black;">'. number_format($b['mch'],2) . ' pg</td>
    <td align=center>'.$con->checkCBCValues($_ihead['age'],$_ihead['xgender'],"MCH",$b['mch'],$b['machine']).'</td>
    <td align="left" style="padding-left: 15px;"><b>Children:</b> 23.0 - 30.0pg<br/><b>Adult:&nbsp;M/F</b> 26.0 - 32.0pg</td>	
</tr>
<tr>
    <td align="left"  style="padding-left: 35px; font-weight: bold;">MCHC:</td>
    <td align=center style="border-bottom: 1px solid black;">'. number_format($b['mchc'],2) . 'g/dL</td>
    <td align=center>'.$con->checkCBCValues($_ihead['age'],$_ihead['xgender'],"MCHC",$b['mchc'],$b['machine']).'</td>
    <td align="left" style="padding-left: 15px;"><b>All ages:</b> 32.0 - 36.0g/dL&nbsp;&nbsp;</td>
</tr>
<tr><td height=2>&nbsp;</td></tr>
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

mysql_close($con);
?>