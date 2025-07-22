<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
    $now = date("m/d/Y h:i a");
    $co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
    
    $order = $con->getArray("select *, `code` as xmen, date_format(extractdate,'%m/%d/%Y') as exdate from lab_samples where so_no = '$_REQUEST[so_no]' and serialno = '$_REQUEST[serialno]';");
    $_ihead = $con->getArray("SELECT lpad(b.so_no,6,0) as myso, b.so_date as xorderdate, c.birthdate as xbday, DATE_FORMAT(result_date,'%m/%d/%Y') AS rdate, b.patient_name, b.patient_address, IF(c.gender='M','Male','Female') AS gender, FLOOR(ROUND(DATEDIFF(b.so_date,c.birthdate) / 364.25,2)) AS age, b.physician,d.patientstatus,a.serialno,a.created_by, b.trace_no FROM lab_bloodchem a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id left join options_patientstat d on b.patient_stat = d.id WHERE a.so_no = '$_REQUEST[so_no]' AND serialno = '$_REQUEST[serialno]'  AND a.branch = '$_SESSION[branchid]';");
    
    $con->calculateAge2($_ihead['xorderdate'],$_ihead['xbday']);
    $b = $con->getArray("SELECT *, date_format(result_date,'%m/%d/%Y') as xdate FROM lab_bloodchem WHERE so_no = '$_ihead[myso]' AND serialno = '$order[serialno]' AND branch = '1';");

    list($encSignature,$encBy,$encByLicense,$encByRole) = $con->getArray("SELECT if(signature_file != '',concat('<img style=\"position:absolute; top:-10px; z-index: -1;\" src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[created_by]';");

    if($b['verified_by'] != '') {
        list($cbySignature,$cby,$cbyLicense,$cbyRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, license_no, role from user_info where emp_id = '$b[verified_by]';");
    }

    if($_ihead['physician'] != '') {
        list($docSignature,$docFullName,$docprefix,$docSpec) = $con->getArray("SELECT IF(signature_file != '',CONCAT('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') AS signature, fullname, concat(', ',prefix), specialization FROM options_doctors WHERE id = '$_ihead[physician]';");
    }

    switch($order['xmen']) {
        case 'L203':
        case 'L032':
        case 'L033':
            $wolverin = 'IMMUNOLOGY & SEROLOGY';
        break;
        default:
            $wolverin = 'CLINICAL CHEMISTRY';
        break;
    }

/* END OF SQL QUERIES */
function checkTest($code,$serialno) {
    if(in_array($code,$_REQUEST['othercodes'])) { return true; } else { return false; }
}


$mpdf=new mPDF('win-1252','letter','','',10,10,90,30,10,10);
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
		<td width=17%>:&nbsp;&nbsp;'.$b['xdate'].'</td>
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
		<td><b>PATIENT STATUS</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['patientstatus'].'</td>
	</tr>
    <tr>
        <td width="100%" colspan=4 style="padding-top: 30px;" align=center>
         <span style="font-weight: bold; font-size: 12pt; color: #000000; text-decoration: underline;">&nbsp;&nbsp;&nbsp;'.$wolverin.'&nbsp;&nbsp;&nbsp;</span>
        </td>
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

<table width=90% cellpadding=0 cellspacing=0 align=center>
<tr>
    <td align="left" width=25% style="border-bottom: 1px solid black;"><b>TEST</b></td>
    <td align=center width=25% style="border-bottom: 1px solid black;"><b>RESULT</b></td>
    <td align=center width=25% style="border-bottom: 1px solid black;"><b>FLAG</b></td>
    <td align=center width=25% style="padding-left: 15px; border-bottom: 1px solid black;"><b>REFERENCE VALUES</b></td>	
</tr>
<tr><td colspan=4 height=5>&nbsp;</td></tr>';

if(checkTest('L004',$order['serialno']) && $b['uric'] > 0 || checkTest('L209',$order['serialno'])) {
    $html .= '<tr>
        <td align="left" valign=top>Blood Uric Acid (BUA)</td>
        <td align=center style="border-bottom: 1px solid black;vertical-align: top;">'. $b['uric'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L004',$b['uric']).'</td>
        <td align=center valign=top>'.$con->getAttribute('L004',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>
    ';
}

if(checkTest('L021',$order['serialno']) && $b['glucose'] > 0 || checkTest('L212',$order['serialno'])) {
    $html .= '<tr>
        <td align="left" valign=top>Fasting Blood Sugar (FBS)</td>
        <td align=center style="border-bottom: 1px solid black;vertical-align: top;">'. $b['glucose'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L021',$b['glucose']).'</td>
        <td align=center valign=top>'.$con->getAttribute('L021',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>
    ';
}

if(checkTest('L009',$order['serialno']) && $b['rbs'] > 0) {
    $html .= '<tr>
        <td align="left" valign=top>Random Blood Sugar (RBS)</td>
        <td align=center style="border-bottom: 1px solid black;vertical-align: top;">'. $b['rbs'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L009',$b['rbs']).'</td>
        <td align=center valign=top>'.$con->getAttribute('L009',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>
    ';
}

if(checkTest('L026',$order['serialno']) && $b['sodium'] > 0) {
    $html .= '<tr>
        <td align="left" valign=top>SODIUM (Na)</td>
        <td align=center style="border-bottom: 1px solid black;vertical-align: top;">'. $b['sodium'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L026',$b['sodium']).'</td>
        <td align=center valign=top>'.$con->getAttribute('L026',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>
    ';
}

if(checkTest('L025',$order['serialno']) && $b['potassium'] > 0) {
    $html .= '<tr>
        <td align="left" valign=top>POTASSIUM (K)</td>
        <td align=center style="border-bottom: 1px solid black;vertical-align: top;">'. $b['potassium'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L025',$b['potassium']).'</td>
        <td align=center valign=top>'.$con->getAttribute('L025',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>
    ';
}

if(checkTest('L029',$order['serialno']) || checkTest('L006',$order['serialno']) && $b['calcium'] > 0 || checkTest('L255',$order['serialno'])) {
    $html .= '<tr>
        <td align="left" valign=top>CALCIUM (Ca)/TOTAL Ca</td>
        <td align=center style="border-bottom: 1px solid black;vertical-align: top;">'. $b['calcium'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L029',$b['calcium']).'</td>
        <td align=center valign=top>'.$con->getAttribute('L029',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>
    ';
}

if((checkTest('L016',$order['serialno']) || checkTest('L252',$order['serialno'])) && $b['bun'] > 0) {
    $html .= '<tr>
        <td align="left">Blood Urea Nitrogen (BUN)</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['bun'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L016',$b['bun']).'</td>
        <td align=center>'.$con->getAttribute('L016',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L018',$order['serialno'])  && $b['total_chol'] > 0) {
    $html .= '<tr>
        <td align="left">Total Cholesterol</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['total_chol'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L018',$b['total_chol']).'</td>
        <td align=center>'.$con->getAttribute('L018',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L020',$order['serialno']) || $b['creatinine'] > 0 || checkTest('L211',$order['serialno'])) {
    $html .= '<tr>
        <td align="left">Creatinine</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['creatinine'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L020',$b['creatinine']).'</td>
        <td align=center>'.$con->getAttribute('L020',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L023',$order['serialno']) && $b['sgot'] > 0) {
    $html .= '<tr>
        <td align="left">SGOT/AST&nbsp;</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['sgot'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L023',$b['sgot']).'</td>
        <td align=center>'.$con->getAttribute('L023',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L022',$order['serialno']) && $b['sgpt'] > 0) {
    $html .= '<tr>
        <td align="left">SGPT/ALT&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['sgpt'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L022',$b['sgpt']).'</td>
        <td align=center>'.$con->getAttribute('L022',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L121',$order['serialno']) && $b['phosphorus'] > 0) {
    $html .= '<tr>
        <td align="left">PHOSPHORUS&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['phosphorus'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L121',$b['phosphorus']).'</td>
        <td align=center>'.$con->getAttribute('L121',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L003',$order['serialno']) || checkTest('L109',$order['serialno'])) {
    $html .= '<tr>
    <td align="left">Total Bilirubin</td>
    <td align=center>'. $b['bilirubin'] . '</td>
    <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L003',$b['bilirubin']).'</td>
    <td align=center colspan=2>'.$con->getAttribute('L003',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';

    $html .= '<tr>
    <td align="left">Direct Bilirubin&nbsp;:</td>
    <td align=center>'. $b['bilirubin_direct'] . '</td>
    <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L109',$b['bilirubin_direct']).'</td>
    <td align=center>'.$con->getAttribute('L109',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';

    $html .= '<tr>
        <td align="left">Indirect Bilirubin</td>
        <td align=center>'. ($b['bilirubin']-$b['bilirubin_direct']) . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L149',$b['bilirubin_indirect']).'</td>
        <td align=center>'.$con->getAttribute('L149',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';

}

if(checkTest('L027',$order['serialno'])) {
    $html .= '<tr>
        <td align="left">Total Protein</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['protein'] . '</td>
        <td align=center></td>
        <td align=center>'.$con->getAttribute('L027',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L001',$order['serialno']) && $b['albumin'] > 0) {
    $html .= '<tr>
        <td align="left">Albumin&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['albumin'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L001',$b['albumin']).'</td>
        <td align=center>'.$con->getAttribute('L001',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L001',$order['serialno']) && checkTest('L110',$order['serialno'])) {
    $html .= '<tr>
        <td align="left">A/G Ratio&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['agratio'] . '</td>
        <td align=center></td>
        <td align=center>1.1 - 1.8</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L119',$order['serialno']) && $b['ion_calcium'] > 0) {
    $html .= '<tr>
        <td align="left">IONIZED CALCIUM&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['ion_calcium'] . '</td>
         <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L119',$b['ion_calcium']).'</td>
        <td align=center>'.$con->getAttribute('L119',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}
if(checkTest('L196',$order['serialno'])) {
    $html .= '<tr>
        <td align="left" colspan=3 ><b>Electrolytes&nbsp;:</b></td>
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';

    if($b['electrolytes_na'] > 0) {
        $html .= '
                <tr>
                    <td align="left" style="padding-left: 35px;">Sodium (Na)&nbsp;:</td>
                    <td align=center style="border-bottom: 1px solid black;">'. $b['electrolytes_na'] . '</td>
                    <td align=center >'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L026',$b['electrolytes_na']).'</td>
                    <td align=center>'.$con->getAttribute('L026',$_ihead['age'],$_ihead['gender']).'</td>	
                </tr>
                <tr><td colspan=4 height=5>&nbsp;</td></tr>
        ';
    }
   
    if($b['electrolytes_k'] > 0) {
        $html .= '
                <tr>
                    <td align="left" style="padding-left: 35px;">Potassium (K)&nbsp;:</td>
                    <td align=center style="border-bottom: 1px solid black;">'. $b['electrolytes_k'] . '</td>
                    <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L025',$b['electrolytes_k']).'</td>
                    <td align=center>'.$con->getAttribute('L025',$_ihead['age'],$_ihead['gender']).'</td>	
                </tr>
                <tr><td colspan=4 height=5>&nbsp;</td></tr>
        ';
    }
    
    if($b['electrolytes_ci'] > 0) {
        $html .= '
            <tr>
                <td align="left" style="padding-left: 35px;">Chloride (CI)&nbsp;:</td>
                <td align=center width=20% style="border-bottom: 1px solid black;">'. $b['electrolytes_ci'] . '</td>
                <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L028',$b['electrolytes_ci']).'</td>
                <td align=center>'.$con->getAttribute('L028',$_ihead['age'],$_ihead['gender']).'</td>	
            </tr>
            <tr><td colspan=4 height=5>&nbsp;</td></tr>

        ';
    }

    if($b['ion_calcium'] > 0) {   
        $html .= '
                <tr>
                    <td align="left" style="padding-left: 35px;">Ionized Calcium&nbsp;:</td>
                    <td align=center style="border-bottom: 1px solid black;">'. $b['ion_calcium'] . '</td>
                    <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L118',$b['ion_calcium']).'</td>
                    <td align=center>'.$con->getAttribute('L118',$_ihead['age'],$_ihead['gender']).'</td>	
                </tr>
                <tr><td colspan=4 height=5>&nbsp;</td></tr>
        ';
    }
    
    if($b['total_calcium'] > 0) {  
        $html .= '
                <tr>
                    <td align="left" style="padding-left: 35px;">Total Calcium&nbsp;:</td>
                    <td align=center style="border-bottom: 1px solid black;">'. $b['total_calcium'] . '</td>
                    <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L029',$b['total_calcium']).'</td>
                    <td align=center>'.$con->getAttribute('L029',$_ihead['age'],$_ihead['gender']).'</td>	
                </tr>
                <tr><td colspan=4 height=5>&nbsp;</td></tr>
        ';
    }
        
}

if(checkTest('L017',$order['serialno'])) {
    $html .= '<tr>
        <td align="left">Chemical Ionization (CI)&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['electrolytes_ci'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L017',$b['electrolytes_ci']).'</td>
        <td align=center>'.$con->getAttribute('L017',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}


if(checkTest('L131',$order['serialno'])) {

    if($b['troponin'] > 0.04) { $flag = 'H'; }

    $html .= '<tr>
        <td align="left">Troponin I&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['troponin'] . '</td>
        <td align=center>'.$flag.'</td>
        <td align=center>> 0.04 ng/mL</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L133',$order['serialno'])) {

    $html .= '<tr>
        <td align="left">Amylase&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['amylase'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L133',$b['amylase']).'</td>
        <td align=center>'.$con->getAttribute('L133',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L135',$order['serialno'])) {

    $html .= '<tr>
        <td align="left">Lipase&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['lipase'] . '</td>
        <td align=center>'.$flag.'</td>
        <td align=center>'.$con->getAttribute('L135',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L092',$order['serialno'])) {

    $html .= '<tr>
        <td align="left">Alkaline Phosphatase&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['alkaline'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L092',$b['alkaline']).'</td>
        <td align=center>'.$con->getAttribute('L092',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L158',$order['serialno'])) {

    $html .= '<tr>
        <td align="left">Magnesium&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['magnesium'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L158',$b['magnesium']).'</td>
        <td align=center>'.$con->getAttribute('L158',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L159',$order['serialno'])) {

    $html .= '<tr>
        <td align="left">Inorganic Phospharous&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['inorganic_phos'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L159',$b['inorganic_phos']).'</td>
        <td align=center>'.$con->getAttribute('L159',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L161',$order['serialno'])) {

    $html .= '<tr>
        <td align="left">GLUCOSE 2hrs Post Prandial&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['prandial'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L161',$b['prandial']).'</td>
        <td align=center>'.$con->getAttribute('L161',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L031',$order['serialno'])) {

    $html .= '<tr>
        <td align="left">TSH (THYROID STIMULATING HORMONES)&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['tsh'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L031',trim($b['tsh'],'<')).'</td>
        <td align=center>'.$con->getAttribute('L031',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L033',$order['serialno'])) {

    $html .= '<tr>
        <td align="left">FT3&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['ft3'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L033',$b['ft3']).'</td>
        <td align=center>'.$con->getAttribute('L033',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L032',$order['serialno'])) {

    $html .= '<tr>
        <td align="left">FT4 (THYROXINE)&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['ft4'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L032',$b['ft4']).'</td>
        <td align=center>'.$con->getAttribute('L032',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L061',$order['serialno'])) {

    $html .= '<tr>
        <td align="left">T3&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['t3'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L061',$b['t3']).'</td>
        <td align=center>'.$con->getAttribute('L061',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L062',$order['serialno'])) {

    $html .= '<tr>
        <td align="left">T4&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['t4'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L062',$b['t4']).'</td>
        <td align=center>'.$con->getAttribute('L062',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
}

if(checkTest('L030',$order['serialno'])) {
        $html .= '<tr>
            <td align="left">Triglycerides</td>
            <td align=center style="border-bottom: 1px solid black;">'. $b['triglycerides'] . '</td>
            <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L030',$b['triglycerides']).'</td>
            <td align=center>'.$con->getAttribute('L030',$_ihead['age'],$_ihead['gender']).'</td>	
        </tr>
        <tr><td colspan=4 height=5>&nbsp;</td></tr>';
    
}

if(checkTest('L024',$order['serialno'])) {
    $html .= '<tr>
            <td align="left">HIGH DENSITY LIPOPROTEIN (HDL)</td>
            <td align=center style="border-bottom: 1px solid black;">'. $b['hdl'] . '</td>
            <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L024',$b['hdl']).'</td>
            <td align=center>'.$con->getAttribute('L024',$_ihead['age'],$_ihead['gender']).'</td>	
        </tr>';
}
if(checkTest('L203',$order['serialno'])) {
    $html .= '
    <tr><td colspan="5"><b>THYROID PANEL (COMPLETE) </b></td></tr>';

    $html .= '<tr>
        <td align="left">TSH (THYROID STIMULATING HORMONES)&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['tsh'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L031',$b['tsh']).'</td>
        <td align=center>'.$con->getAttribute('L031',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';

    $html .= '<tr>
        <td align="left">FT3&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['ft3'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L033',$b['ft3']).'</td>
        <td align=center>'.$con->getAttribute('L033',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';

    $html .= '<tr>
        <td align="left">FT4 (THYROXINE)&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['ft4'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L032',$b['ft4']).'</td>
        <td align=center>'.$con->getAttribute('L032',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';

    $html .= '<tr>
        <td align="left">T3&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['t3'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L061',$b['t3']).'</td>
        <td align=center>'.$con->getAttribute('L061',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';

    $html .= '<tr>
        <td align="left">T4&nbsp;:</td>
        <td align=center style="border-bottom: 1px solid black;">'. $b['t4'] . '</td>
        <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L062',$b['t4']).'</td>
        <td align=center>'.$con->getAttribute('L062',$_ihead['age'],$_ihead['gender']).'</td>	
    </tr>
    <tr><td colspan=4 height=5>&nbsp;</td></tr>';
    
    }

if(checkTest('L052',$order['serialno']) || checkTest('L206',$order['serialno'])) {
    $html .= '
    <tr><td colspan="5"><b>LIPID PANEL :</b></td></tr>';

    if($b['cholesterol'] > 0) {

        $html .= '<tr><td colspan=4 height=5>&nbsp;</td></tr>
        <tr>
            <td align="left" style="padding-left: 25px;">Total Cholesterol</td>
            <td align=center style="border-bottom: 1px solid black;">'. $b['cholesterol'] . '</td>
            <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L018',$b['cholesterol']).'</td>
            <td align=center>'.$con->getAttribute('L018',$_ihead['age'],$_ihead['gender']).'</td>	
        </tr>';
    }

    if($b['triglycerides'] > 0) {

        $html .= '<tr><td colspan=4 height=5>&nbsp;</td></tr>
        <tr>
            <td align="left" style="padding-left: 25px;">Triglycerides</td>
            <td align=center style="border-bottom: 1px solid black;">'. $b['triglycerides'] . '</td>
            <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L030',$b['triglycerides']).'</td>
            <td align=center>'.$con->getAttribute('L030',$_ihead['age'],$_ihead['gender']).'</td>	
        </tr>';
    }

    if($b['hdl'] > 0) {
        $html .= '<tr><td colspan=4 height=5>&nbsp;</td></tr>
        <tr>
            <td align="left" style="padding-left: 25px;">HDL</td>
            <td align=center style="border-bottom: 1px solid black;">'. $b['hdl'] . '</td>
            <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L024',$b['hdl']).'</td>
            <td align=center>'.$con->getAttribute('L024',$_ihead['age'],$_ihead['gender']).'</td>	
        </tr>';
    }

    if($b['ldl'] > 0) {
        $html .= '<tr><td colspan=4 height=5>&nbsp;</td></tr>
        <tr>
            <td align="left" style="padding-left: 25px;">LDL</td>
            <td align=center style="border-bottom: 1px solid black;">'. $b['ldl'] . '</td>
            <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L080',$b['ldl']).'</td>
            <td align=center>'.$con->getAttribute('L080',$_ihead['age'],$_ihead['gender']).'</td>	
        </tr>';
    }

    if($b['vldl'] > 0) {

        $html .= '<tr><td colspan=4 height=5>&nbsp;</td></tr>
        <tr>
            <td align="left" style="padding-left: 25px;">VLDL</td>
            <td align=center style="border-bottom: 1px solid black;">'. $b['vldl'] . '</td>
            <td align=center>'.$con->checkChemValues($_ihead['age'],$_ihead['gender'],'L081',$b['vldl']).'</td>
            <td align=center>'.$con->getAttribute('L081',$_ihead['age'],$_ihead['gender']).'</td>	
        </tr>
        <tr><td colspan=4 height=5>&nbsp;</td></tr>';
    }
}

$html .= ' <tr>
        <td align="center" colspan=4 style="padding-top: 10px;"><b>END OF RESULT. NOTHING FOLLOWS</b></td>
    </tr>
    <tr>
        <td align="left" colspan=4 style="border-top: 1px solid black; margin-top: 20px;"><b>Remarks:</b> '.$b['remarks'].'</td>
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