<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");

	$_ihead = $con->getArray("SELECT DATE_FORMAT(result_date,'%m/%d/%Y') AS rdate, b.patient_name, b.patient_address, c.gender as xgender, IF(c.gender='M','Male','Female') AS gender, c.gender as xgender, c.birthdate, b.physician, a.serialno,a.created_by,b.trace_no, FLOOR(ROUND(DATEDIFF(b.so_date,c.birthdate) / 364.25,2)) AS age FROM lab_lipidpanel a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id WHERE a.so_no = '$_REQUEST[so_no]' and a.serialno = '$_REQUEST[serialno]' AND a.branch = '$_SESSION[branchid]';");
    $b = $con->getArray("SELECT cholesterol,triglycerides,hdl,ldl,vldl,sgpt,remarks,verified_by, verified, created_by FROM lab_lipidpanel WHERE so_no = '$_REQUEST[so_no]' and branch = '$_SESSION[branchid]' and serialno = '$_REQUEST[serialno]';");	
	
	list($encSignature,$encBy,$encByLicense,$encByRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[created_by]';");

    if($b['verified_by'] != '') {
        list($cbySignature,$cby,$cbyLicense,$cbyRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[verified_by]';");
    }	

	if($_ihead['physician'] != '') {
        list($docSignature,$docFullName,$docprefix,$docSpec) = $con->getArray("SELECT IF(signature_file != '',CONCAT('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') AS signature, fullname, concat(', ',prefix), specialization FROM options_doctors WHERE id = '$_ihead[physician]';");
    }

/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','FOLIO-H','','',10,10,72,30,5,5);
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
            padding:5px;border:1px solid black; text-align: center; font-weight: bold;
        }

        .itemResult {
            padding:5px;border:1px solid black;text-align: center;
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
		<td width="100%" style="padding-top: 20px;" align=center>
			<span style="font-weight: bold; font-size: 12pt; color: #000000;">LABORATORY DEPARTMENT</span>
		</td>
	</tr>

</table>
<table width=100% cellpadding=2 cellspacing=0 style="font-size: 10pt;margin-top:10px;">
	<tr>
		<td width=100% colspan=4 style="background-color: #cdcdcd; border-top: 1px solid black; border-bottom: 1px solid black;" align=center><b>PATIENT INFORMATION</b></td>
	</tr>
	<tr>
		<td width=25%><b>SO NO.</b></td>
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
		<td>:&nbsp;&nbsp;'.$con->calculateAge($_ihead['birthdate']).'yo</td>
	</tr>
	<tr>
		<td><b>REQUESTING PHYSICIAN</b></td>
		<td>:&nbsp;&nbsp;'.$docFullName.''.$docprefix .'</td>
		<td><b>EXAMINATION</b></td>
		<td>:&nbsp;&nbsp;Lipid Panel</td>
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
            <td align=center valign=top>&nbsp;<br/><b>JEREMIAS P. LEYSON, MD, DPSP<br/> ____________________________________________________<br><b>PATHOLOGIST - LIC NO. 0124968</b></td>
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
    <table width=80% cellpadding=0 cellspacing=0 align=center style="border-collapse: collapse;">

            <tr>
                <td class="itemHeader">TEST</td>
                <td class="itemHeader">RESULT</td>
				<td class="itemHeader">UNIT</td>
				<td class="itemHeader">FLAG</td>
                <td class="itemHeader">NORMAL REFERENCE</td>
            </tr>
            <tr>
                <td class="itemResult">TOTAL CHOLESTEROL</td>
                <td class="itemResult">'.$b['cholesterol'].'</td>
				<td class="itemResult">mmol/L</td>
        		<td class="itemResult" align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L018',$b['cholesterol']).'</td>
                <td class="itemResult">'.$con->getAttribute('L018',$con->calculateAge($_ihead['birthdate']),$_ihead['xgender']).'</td>
            </tr>
			<tr>
				<td class="itemResult">TRIGLYCERIDES</td>
				<td class="itemResult">'.$b['triglycerides'].'</td>
				<td class="itemResult">mmol/L</td>
        		<td class="itemResult" align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L030',$b['triglycerides']).'</td>
				<td class="itemResult">'.$con->getAttribute('L030',$con->calculateAge($_ihead['birthdate']),$_ihead['xgender']).'</td>
			</tr>
			<tr>
				<td class="itemResult">HDL</td>
				<td class="itemResult">'.$b['hdl'].'</td>
				<td class="itemResult">mmol/L</td>
        		<td class="itemResult" align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L024',$b['hdl']).'</td>
				<td class="itemResult">'.$con->getAttribute('L024',$con->calculateAge($_ihead['birthdate']),$_ihead['xgender']).'</td>
			</tr>
			<tr>
				<td class="itemResult">LDL</td>
				<td class="itemResult">'.$b['ldl'].'</td>
				<td class="itemResult">mmol/L</td>
        		<td class="itemResult" align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L080',$b['ldl']).'</td>
				<td class="itemResult">'.$con->getAttribute('L080',$con->calculateAge($_ihead['birthdate']),$_ihead['xgender']).'</td>
			</tr>
			<tr>
				<td class="itemResult">VLDL</td>
				<td class="itemResult">'.$b['vldl'].'</td>
				<td class="itemResult">mmol/L</td>
        		<td class="itemResult" align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L081',$b['vldl']).'</td>
				<td class="itemResult">'.$con->getAttribute('L081',$con->calculateAge($_ihead['birthdate']),$_ihead['xgender']).'</td>
			</tr>
    </table>
	<table width=60% align=center style="margin-top: 5px; font-size: 9pt; font-style: italic;">
        <tr>
            <td align=left width=18%><b>REMARKS :</b></td>
            <td align=left width=82% style="border-bottom: 1px solid black;">'.$b['remarks'].'</td>
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