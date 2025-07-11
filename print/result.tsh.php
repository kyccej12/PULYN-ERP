<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");

	$_ihead = $con->getArray("SELECT c.*, LPAD(b.so_no,6,0) AS myso, DATE_FORMAT(result_date,'%m/%d/%Y') AS rdate, b.patient_name, b.patient_address, IF(c.gender='M','Male','Female') AS gender, c.birthdate, YEAR(so_date)-YEAR(c.birthdate) AS age, b.physician,d.patientstatus,a.serialno,a.created_by, b.trace_no, e.code FROM lab_tsh a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_patientstat d ON b.patient_stat = d.id LEFT JOIN lab_samples e ON a.so_no = e.so_no AND a.serialno = e.serialno WHERE a.so_no = '$_REQUEST[so_no]' AND a.serialno = '$_REQUEST[serialno]' AND a.branch = '$_SESSION[branchid]';");
    $b = $con->getArray("SELECT *,created_by, verified_by FROM lab_tsh WHERE so_no = '$_ihead[myso]' AND serialno = '$_ihead[serialno]' AND branch = '$_SESSION[branchid]';");
    //$con->dbquery("insert into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SO','SALES ORDER # $_REQUEST[so_no] WAS PRINTED BY USER','$_REQUEST[so_no]');");
			
/* END OF SQL QUERIES */
	list($testkit,$lotno,$xpire_d8,$testPrinciple) = $con->getArray("select testkit, lotno, DATE_FORMAT(expiry,'%m/%d/%Y') as expiry_d8, test_principle from lab_samples where so_no = '$_REQUEST[so_no]' and serialno = '$_REQUEST[serialno]';");


    list($procedure) = $con->getArray("SELECT `description` FROM services_master WHERE `code` = '$_REQUEST[code]';");

    list($encSignature,$encBy,$encByLicense,$encByRole) = $con->getArray("SELECT if(signature_file != '',concat('<img style=\"position:absolute; top:-10px; z-index: -1;\" src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[created_by]';");

    if($b['verified_by'] != '') {
        list($cbySignature,$cby,$cbyLicense,$cbyRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[verified_by]';");
    }

    if($_ihead['physician'] != '') {
        list($docSignature,$docFullName,$docprefix,$docSpec) = $con->getArray("SELECT IF(signature_file != '',CONCAT('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') AS signature, fullname, concat(', ',prefix), specialization FROM options_doctors WHERE id = '$_ihead[physician]';");
    }

	list($min,$max,$unit) = $con->getArray("select min_value, max_value, unit from lab_testvalues where `code` = '$_REQUEST[code]';");

    switch($_REQUEST['code']) {
		case "L031":
			$limits = 'Level below '.$min.' '.$unit.' indicative "Hyperthyroidism" <br/> Level above '.$max.' '.$unit.' indicative "Hypothyroidism"';
		break;
		default:
			$limits = $con->getAttribute($_ihead['code'],$con->calculateAge($_ihead['birthdate']),$_ihead['xgender']);
		break;
	}


$mpdf=new mPDF('win-1252','LETTER','','',10,10,90,30,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");

if($b['verified'] != 'Y') {
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
		body {font-family: sans-serif; font-size: 9pt; }
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
		<td width="100%" style="padding-top: 10px;" align=center>
			<span style="font-weight: bold; font-size: 12pt; color: #000000;">LABORATORY DEPARTMENT</span>
		</td>
	</tr>

</table>
<table width=100% cellpadding=2 cellspacing=0 style="font-size: 9pt;margin-top:10px;">
	<tr>
		<td width=100% colspan=4 style="background-color: #cdcdcd; border-top: 1px solid black; border-bottom: 1px solid black;" align=center><b>PATIENT INFORMATION</b></td>
	</tr>
	<tr>
		<td width=25%><b>CASE NO.</b></td>
		<td width=38%>:&nbsp;&nbsp;'.$_ihead['myso'].'</td>
		<td width=20%><b>DATE</b></td>
		<td width=17%>:&nbsp;&nbsp;'.$_ihead['rdate'].'</td>
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
        <td width="100%" colspan=4 style="padding-top: 30px;" align=center>
         <span style="font-weight: bold; font-size: 12pt; color: #000000; text-decoration: underline;">&nbsp;&nbsp;&nbsp;IMMUNOLOGY AND SEROLOGY&nbsp;&nbsp;&nbsp;</span>
        </td>
    </tr>
</table>

</htmlpageheader>

<htmlpagefooter name="myfooter">
 <table width=100% cellpadding=5 style="margin-bottom: -45px;">
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
		<table width=90% cellpadding=0 cellspacing=0 align=center>
           <tr>
				<td align="left" width=30% style="border-bottom: 1px solid black;"><b>TEST</b></td>
				<td align=center width=20% style="border-bottom: 1px solid black;"><b>RESULT</b></td>
				<td align=center width=25% style="border-bottom: 1px solid black;"><b>FLAG</b></td>
				<td align=center width=25% style="padding-left: 15px; border-bottom: 1px solid black;"><b>REFERENCE VALUES</b></td>	
			</tr>
			<tr><td colspan=4 height=5>&nbsp;</td></tr>
			<tr>
				<td align="left" valign=top>TSH (THYROID STIMULATING HORMONES)</td>
				<td align=center style="border-bottom: 1px solid black;vertical-align: top;">'. $b['tsh'] . '</td>
				<td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L031',$b['tsh']).'</td>
				<td align=center valign=top>'.$con->getAttribute('L031',$_ihead['age'],$_ihead['gender']).'</td>	
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td align="center" colspan=4 style="padding-top: 10px;"><b>END OF RESULT. NOTHING FOLLOWS</b></td>
			</tr>
			<tr>
				<td align="left" colspan=4 style="border-top: 1px solid black; margin-top: 20px;"><b>Remarks:</b> '.$b['remarks'].'</td>
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