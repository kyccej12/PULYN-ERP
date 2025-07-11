<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$o = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $o->getArray("select * from companies where company_id = '$_SESSION[company]';");

	$_ihead = $o->getArray("SELECT lpad(b.so_no,6,0) as myso, DATE_FORMAT(result_date,'%m/%d/%Y') AS rdate, b.patient_name, b.patient_address, IF(c.gender='M','Male','Female') AS gender, FLOOR(ROUND(DATEDIFF(so_date,c.birthdate) / 364.25,2)) AS age, b.physician,d.patientstatus,a.serialno,a.created_by, b.trace_no, b.so_date as xorderdate, c.birthdate as xbday FROM lab_uaresult a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id left join options_patientstat d on b.patient_stat = d.id WHERE a.so_no = '$_REQUEST[so_no]' AND serialno = '$_REQUEST[serialno]'  AND a.branch = '$_SESSION[branchid]';");
    $b = $o->getArray("select * from lab_uaresult where so_no = '$_ihead[myso]' and serialno = '$_ihead[serialno]' and branch = '$_SESSION[branchid]';");
    

    $o->calculateAge2($_ihead['xorderdate'],$_ihead['xbday']);

    list($encSignature,$encBy,$encByLicense,$encByRole) = $o->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[created_by]';");

    if($b['verified_by'] != '') {
        list($cbySignature,$cby,$cbyLicense,$cbyRole) = $o->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[verified_by]';");
    }
    

    if($_ihead['physician'] != '') {
        list($docSignature,$docFullName,$docprefix,$docSpec) = $o->getArray("SELECT IF(signature_file != '',CONCAT('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') AS signature, fullname, concat(', ',prefix), specialization FROM options_doctors WHERE id = '$_ihead[physician]';");
    }
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','letter','','',10,10,40,30,10,10);
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
<table width=100% cellpadding=2 cellspacing=0 style="font-size: 10pt; margin-top:100px;">
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
		<td>:&nbsp;&nbsp;'.$o->escapeString(htmlentities($_ihead['patient_name'])).'</td>
		<td><b>GENDER</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['gender'].'</td>
	</tr>
	<tr>
		<td><b>PATIENT ADDRESS</b></td>
		<td>:&nbsp;&nbsp;' . $_ihead['patient_address'] . '</td>
		<td><b>AGE</b></td>
		<td>:&nbsp;&nbsp;'.$o->ageDisplay.'</td>
	</tr>
	<tr>
		<td><b>REQUESTING PHYSICIAN</b></td>
        <td>:&nbsp;&nbsp;'.$docFullName.''.$docprefix .'</td>
		<td></td>
		<td></td>
	</tr>
    <tr>
        <td width="100%" colspan=4 style="padding-top: 5px;" align=center>
         <span style="font-weight: bold; font-size: 12pt; color: #000000; text-decoration: underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;URINALYSIS (UA)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </td>
    </tr>
</table>
<table width=80% cellpadding=0 cellspacing=3 align=center style="margin-left: 60px;">
<tr>
    <td align="left" colspan=3  style="padding-left: 15px;"><b>PHYSICAL&nbsp;:</b></td>
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Color</td>
    <td align=center style="border-bottom: 1px solid black;vertical-align: top;">'. $b['color'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Appearance</td>
    <td align=center style="border-bottom: 1px solid black;vertical-align: top;">'. $b['appearance'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">pH</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['ph'] . '</td>
    <td align="left" style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Specific Gravity</td>
    <td align=center style="border-bottom: 1px solid black;">'. number_format($b['gravity'],3) . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" colspan=3  style="padding-left: 15px;"><b>MICROSCOPIC&nbsp;:</b></td>
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">WBC / hpf&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['wbc_hpf'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">RBC / hpf&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['rbc_hpf'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Yeast&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['yeast'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Bacteria&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['bacteria'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Mucus Threads&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['mucus_thread'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Amorphous&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['amorphous_urates'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" colspan=3  style="padding-left: 15px;"><b>CRYSTALS&nbsp;:</b></td>
</tr>';
    if($b['crystal1'] != '') {

    $html .= '<tr>
                <td align="left" style="padding-left: 35px;">&nbsp;</td>
                <td align=center style="border-bottom: 1px solid black;">'. $b['crystal1'] . '</td>
                <td align="left" colspan=2 style="padding-left: 15px;"></td>	
            </tr>';
    } else {
        $html .= '<tr>
                <td align="left" style="padding-left: 35px;">&nbsp;</td>
                <td align=center style="border-bottom: 1px solid black;">&nbsp;</td>
                <td align="left" colspan=2 style="padding-left: 15px;"></td>	
            </tr>';
    }

    if($b['crystal2'] != '') {
   $html .= '<tr>
                <td align="left" style="padding-left: 35px;">&nbsp;</td>
                <td align=center style="border-bottom: 1px solid black;">'. $b['crystal2'] . '</td>
                <td align="left" colspan=2 style="padding-left: 15px;"></td>	
            </tr>';
    }

    if($b['crystal3'] != '') {
   $html .= '<tr>
                <td align="left" style="padding-left: 35px;">&nbsp;</td>
                <td align=center style="border-bottom: 1px solid black;">'. $b['crystal3'] . '</td>
                <td align="left" colspan=2 style="padding-left: 15px;"></td>	
            </tr>';
    }

    if($b['crystal4'] != '') {
   $html .= '<tr>
                <td align="left" style="padding-left: 35px;">&nbsp;</td>
                <td align=center style="border-bottom: 1px solid black;">'. $b['crystal4'] . '</td>
                <td align="left" colspan=2 style="padding-left: 15px;"></td>	
            </tr>';
    }


$html .='

<tr>
    <td align="left" colspan=3  style="padding-left: 15px;"><b>Chemical&nbsp;:</b></td>
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Blood&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['blood'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Bilirubin&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['bilirubin'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Urobilinogen&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['urobilinogen'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Ketone&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['ketone'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Protein&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['protein'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Nitrite&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['nitrite'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Glucose&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['glucose'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Leukocyte&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['leukocyte'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" colspan=3  style="padding-left: 15px;"><b>Epithelial Cells&nbsp;:</b></td>
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Squamous&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['squamous'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Bladder&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['bladder'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">Renal&nbsp;:</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['renal'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" colspan=3  style="padding-left: 15px;"><b>CASTS&nbsp;:</b></td>
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">&nbsp;</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['hyaline'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">&nbsp;</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['coarse_granular'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">&nbsp;</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['casts_wbc'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr>
    <td align="left" style="padding-left: 35px;">&nbsp;</td>
    <td align=center style="border-bottom: 1px solid black;">'. $b['casts_rbc'] . '</td>
    <td align="left" colspan=2 style="padding-left: 15px;"></td>	
</tr>
<tr><td height=3>&nbsp;</td></tr>
<tr>
    <td align="left" style="padding-left: 15px;" valign=top><b>Note&nbsp;:</b></td>
    <td align=left colspan=2 style="border-bottom: 1px solid black;">'. $b['remarks'] . '</td>
    <td width=25%></td>
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