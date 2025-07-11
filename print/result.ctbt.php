<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");

	$_ihead = $con->getArray("SELECT DATE_FORMAT(result_date,'%m/%d/%Y') AS rdate, b.patient_name, b.patient_address, c.gender as xgender, b.so_date as xorderdate, c.birthdate as xbday, IF(c.gender='M','Male','Female') AS gender, c.gender as xgender, c.birthdate, b.physician, a.serialno,a.created_by,b.trace_no FROM lab_ctbt a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id WHERE a.so_no = '$_REQUEST[so_no]' and a.serialno = '$_REQUEST[serialno]';");  
    $b = $con->getArray("SELECT ct_min,ct_sec,bt_min,bt_sec,remarks,verified_by,verified FROM lab_ctbt WHERE so_no = '$_REQUEST[so_no]' and branch = '$_SESSION[branchid]' and serialno = '$_REQUEST[serialno]';");
	
    $con->calculateAge2($_ihead['xorderdate'],$_ihead['xbday']);

    list($encSignature,$encBy,$encByLicense,$encByRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$_ihead[created_by]';");

	if($b['verified_by'] != '') {
        list($medtechSignature,$medtechFullname,$medtechLicense,$medtechRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[verified_by]';");
    }		

    if($_ihead['physician'] != '') {
        list($docSignature,$docFullName,$docprefix,$docSpec) = $con->getArray("SELECT IF(signature_file != '',CONCAT('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') AS signature, fullname, concat(', ',prefix), specialization FROM options_doctors WHERE id = '$_ihead[physician]';");
    }


/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','LETTER','','',10,10,68,25,5,5);
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
		body {font-family: sans-serif; font-size: 10px; }
        .itemHeader {
            padding:5px;border:1px solid black; text-align: center; font-weight: bold; background-color: #edf2f2;
        }

        .itemResult {
            padding:5px;border:1px solid black;text-align: center; border-top:none;
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
		<td>:&nbsp;&nbsp;'.$con->ageDisplay.'</td>
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
<table width=100% cellpadding=5 style="margin-bottom: 10px;">
	<tr>
        <td width=33% align=center>'.$encSignature.'<br/><b>'.$encBy.'<br/>_______________________________<br/><span>PRC LICENSE NO. '.$encByLicense.'</span><br/><b>REPORTED BY</b></td>
        <td width=33% align=center>'.$medtechSignature.'<br/><b>'.$medtechFullname.'<br/>_______________________________<br/><span>PRC LICENSE NO. '.$medtechLicense.'</span><br/><b>VALIDATED BY</b></td>
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
<div id="main">
    <table width=60% cellpadding=0 cellspacing=0 align=center style="margin: 5px;">
        <tr><td align=center><span style="font-size: 12pt; font-weight: bold;">HEMATOLOGY RESULT</span></td></tr>
    </table>
    <table width=90% cellpadding=0 cellspacing=0 align=center style="border-collapse: collapse; font-size: 14px;">

            <tr>
                <td class="itemHeader">CLOTTING TIME</td>
            </tr>
    </table>
    <table width=90% cellpadding=0 cellspacing=0 align=center style="border-collapse: collapse; font-size: 14px;">';
    if($b['ct_min'] <= 1)  {
        $html .= '<tr>
                    <td width=50% class="itemResult">'.$b['ct_min'].' MINUTE ' .$b['ct_sec'].' SECOND/s</td>
                    <td width=50% class="itemResult" style="font-weight:bold;">NORMAL LIMIT: 3-9 MINUTES</td>
                </tr>';
    }else {      
    
        $html .=  '<tr>
                    <td width=50% class="itemResult">'.$b['ct_min'].' MINUTES ' .$b['ct_sec'].' SECOND/s</td>
                    <td width=50% class="itemResult" style="font-weight:bold;">NORMAL LIMIT: 3-9 MINUTES</td>
                </tr>';
    }
    $html .= '</table>
    <table><tr><td height=15></td></tr></table>
    <table width=90% cellpadding=0 cellspacing=0 align=center style="border-collapse: collapse; font-size: 14px;">

            <tr>
                <td class="itemHeader">BLEEDING TIME</td>
            </tr>
    </table>
    <table width=90% cellpadding=0 cellspacing=0 align=center style="border-collapse: collapse; font-size: 14px;">';
    if($b['bt_min'] <= 1)  {
        $html .= '<tr>
                    <td width=50% class="itemResult">'.$b['bt_min'].' MINUTE ' .$b['bt_sec'].' SECOND/s</td>
                    <td width=50% class="itemResult" style="font-weight:bold;">NORMAL LIMIT: 2-5 MINUTES</td>
                </tr>';
    }else {      
    
        $html .=  '<tr>
                    <td width=50% class="itemResult">'.$b['bt_min'].' MINUTES ' .$b['bt_sec'].' SECOND/s</td>
                    <td width=50% class="itemResult" style="font-weight:bold;">NORMAL LIMIT: 2-5 MINUTES</td>
                </tr>';
    }
    $html .= '</table>
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