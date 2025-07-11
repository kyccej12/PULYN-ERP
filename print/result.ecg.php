<?php
	session_start();
    //ini_set("display_errors","On");
	//include("../lib/mpdf6//mpdf.php");
	include("../handlers/_generics.php");
    include("../lib/mpdf6/mpdf.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");

    $_ihead = $con->getArray("SELECT DATE_FORMAT(result_date,'%m/%d/%Y') AS rdate, b.patient_name, b.patient_address, IF(c.gender='M','Male','Female') AS gender, FLOOR(DATEDIFF(b.so_date,c.birthdate)/365.25) AS age, d.fullname AS consultant, d.prefix, d.license_no, d.specialization, d.signature_file, a.serialno, a.procedure, a.impression, a.created_by, b.trace_no, a.verified, a.verified_by, e.role, e.signature_file AS encodersignature, c.employer FROM lab_ecgresult a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_doctors d ON a.consultant = d.id LEFT JOIN user_info e ON a.created_by = e.emp_id WHERE a.so_no = '$_REQUEST[so_no]' AND `code` = '$_REQUEST[code]' AND serialno = '$_REQUEST[serialno]' AND a.branch = '$_SESSION[branchid]';");

    list($file) = $con->getArray("select CONCAT('../',file_path) from lab_samples where so_no = '$_REQUEST[so_no]' and `code` = 'X017' and serialno = '$_REQUEST[serialno]';");
    $b = $con->getArray("SELECT * FROM lab_ecgresult WHERE so_no = '$_REQUEST[so_no]' and `code` = 'X017' and serialno = '$_REQUEST[serialno]';");
    
    if($_ihead['signature_file'] != '') {
		$consultantSignature = "<img src='../images/signatures/$_ihead[signature_file]' align=absmiddle />";
	} else {
		$consultantSignature = "<img src='../images/signatures/blank.png' align=absmiddle />";	
	}	

/* END OF SQL QUERIES */

$html = '


<html>
<head>
	<style>
		body {font-family: sans-serif; font-size: 8pt; }
        .itemHeader {
            padding:5px;border:1px solid black; text-align: center; font-weight: bold;
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

</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100% cellpadding=5>
	<tr>
        <td width=71% align=center style="font-size: 14px;">'.$b['impression'] .'</td>
		<td width=29% align=center valign=top>'.$consultantSignature.'<br/>'.$_ihead['consultant'].', '. $_ihead['prefix'] .'&nbsp;&nbsp;'. $_ihead['rdate'] .'<br/>_________________________________________<br><b>'.$_ihead['specialization'].' - Lic. No. '.$_ihead['license_no'].'</b></td>
	</tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<div id="main">
    <table width=60% cellpadding=0 cellspacing=0 align=center style="margin: 5px;">
        <tr><td align=center></td></tr>
    </table>
</div>
</body>
</html>
';

$mpdf=new mPDF('c','A4-L','','',5,5,5,5,5,5);
$mpdf->setDisplaymode(100);
$mpdf->setImportUse();
$mpdf->WriteHTML($html);

$pagecount = $mpdf->setSourceFile($file);
$tplId = $mpdf->importPage($pagecount);
$mpdf->UseTemplate($tplId);

if($_ihead['verified'] != 'Y') {
	$mpdf->SetWatermarkText('FOR VALIDATION');
	$mpdf->showWatermarkText = true;
}

$mpdf->Output();
exit;

?>