<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../handlers/_generics.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");

	$_ihead = $con->getArray("SELECT lpad(b.so_no,6,0) as myso, DATE_FORMAT(result_date,'%m/%d/%Y') AS rdate, b.so_date as xorderdate, c.birthdate as xbday, b.patient_name, b.patient_address, IF(c.gender='M','Male','Female') AS gender, FLOOR(ROUND(DATEDIFF(b.so_date,c.birthdate) / 364.25,2)) AS age, b.physician,d.patientstatus,a.serialno,a.created_by, b.trace_no, b.customer_name, b.customer_address FROM lab_audiometry a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id left join options_patientstat d on b.patient_stat = d.id WHERE a.so_no = '$_REQUEST[so_no]' AND serialno = '$_REQUEST[serialno]'  AND a.branch = '$_SESSION[branchid]';");
    $b = $con->getArray("SELECT *,date_format(result_date,'%m/%d/%Y') as rdate, prepared_by, performed_by, verified_by, verified FROM lab_audiometry WHERE so_no = '$_ihead[myso]' AND serialno = '$_ihead[serialno]' AND branch = '$_SESSION[branchid]';");	
	
	$con->calculateAge2($_ihead['xorderdate'],$_ihead['xbday']);

	if($b['performed_by'] != '') {
        list($radtechSignature,$radtechFullname,$radtechRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, role from user_info where emp_id = '$b[performed_by]';");
    }		
	if($b['prepared_by'] != '') {
        list($encoderSignature,$encoderFullname,$encoderRole) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature, fullname, role from user_info where emp_id = '$b[prepared_by]';");
    }		

	list($brgy) = $con->getArray("SELECT brgyDesc FROM options_brgy WHERE brgyCode = '$_ihead[brgy]';");
    list($ct) = $con->getArray("SELECT citymunDesc FROM options_cities WHERE cityMunCode = '$_ihead[city]';");
    list($prov) = $con->getArray("SELECT provDesc FROM options_provinces WHERE provCode = '$_ihead[province]';");

    if($_ihead['street'] != '') { $myaddress.=$_ihead['street'].", "; }
    if($brgy != "") { $myaddress .= $brgy.", "; }
    if($ct != "") { $myaddress .= $ct.", "; }
    if($prov != "")  { $myaddress .= $prov.", "; }
    $myaddress = substr($myaddress,0,-2);

/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','Letter','','',10,10,55,10,5,5);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->useGraphs = true;

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

        #items {
			font-family: Arial, Helvetica, sans-serif;
			border-collapse: collapse;
		  }
		  
		  #items td, #items th {
			border: 1px solid #ddd;
			text-align: center;
		  }
		  
		  #items tr:nth-child(even){background-color: #f2f2f2;}
		  
		  #items tr:hover {background-color: #ddd;}
		  
		  #items th {
			padding-top: 5px;
			padding-bottom: 5px;
			text-align: left;
			background-color: #04AA6D;
			color: white;
			text-align: center;
		  }

		  #items .indent {
			padding-left:5px;
		  }

		  .red {
			color: red;
		  }

		  .blue {
			color: blue;
		  }

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
<table width=100% cellpadding=2 cellspacing=0 style="font-size: 9pt;margin-top:5px;">
	<tr>
		<td width=100% colspan=4 style="background-color: #cdcdcd; border-top: 1px solid black; border-bottom: 1px solid black;" align=center><b>PATIENT INFORMATION</b></td>
	</tr>
	<tr>
		<td width=25%><b>CASE NO.</b></td>
		<td width=40%>:&nbsp;&nbsp;'.$_ihead['serialno'].'</td>
		<td width=20%><b>DATE</b></td>
		<td width=15%>:&nbsp;&nbsp;'.$b['rdate'].'</td>
	</tr>
	<tr>
		<td><b>PATIENT NAME</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['patient_name'].'</td>
		<td><b>GENDER</b></td>
		<td>:&nbsp;&nbsp;'.$_ihead['gender'].'</td>
	</tr>
	<tr>
		<td><b>PATIENT ADDRESS</b></td>
		<td>:&nbsp;&nbsp;' . $myaddress . '</td>
		<td><b>AGE</b></td>
		<td>:&nbsp;&nbsp;'.$con->ageDisplay.'</td>
	</tr>
</table>

</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100% cellpadding=5>
		<tr>
        	<td align="left" colspan=4 style="border-bottom: 1px solid black; margin-top: 20px;"><b>Remarks:</b> '.$b['remarks'].'</td>
    	</tr>
</table>
<table width=100% cellpadding=5 style="margin-bottom: 10px;">
	<tr>
		<td align=center valign=top>'.$radtechSignature.'<br/><b>'.$radtechFullname.'<br/>___________________________________________<br>PERFORMED BY</b></td>
        <td align=center valign=top>'.$encoderSignature.'<br/><b>'.$encoderFullname.'<br/>___________________________________________<br>PREPARED BY</b></td>
	</tr>
</table>
<table width=100%>
	<tr><td align=left><barcode size=0.6 code="'.substr($_ihead['trace_no'],0,15).'" type="C128A"></td><td align=right>Run Date: '.date('m/d/Y h:i:s a').'</td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<div id="main">
	<table width="100%" cellpadding=0 cellspaing=0>
		<tr>
			<td width="100%" style="padding-top: 5px; padding-bottom: 5px;" align=center>
				<span style="font-weight: bold; font-size: 12pt; color: #000000;">HEARING EVALUATION REPORT</span>
			</td>
		</tr>

	</table>
    <table width=100% cellpadding=0 cellspacing=0 align=center style="border-collapse: collapse;">
            <tr>
				<td align=center>
					<img src="audiometrygraph.php?sn='.$_ihead['serialno'].'&sid='.uniqid().'" />
				</td>
			</tr>
    </table>
	<table width=86% style="border-collapse: collapse;" id="items" align=center style="margin-top: 5px;">
		<tr>
			<td width=60% valign=top style="border: 1px solid #000;">
				<table width=100% cellpadding=0 align=center style="font-size: 9pt;">
					<tr>
						<th>&nbsp;</th>
						<th>250</th>
						<th>500</th>
						<th>1000</th>
						<th>2000</th>
						<th>4000</th>
						<th>6000</th>
						<th>8000</th>
					</tr> 
					<tr>
						<td style="font-weight:bold;" class="blue">L</td>
						<td class="blue">'.$b['250_l'].'</td>
						<td class="blue">'.$b['500_l'].'</td>
						<td class="blue">'.$b['1k_l'].'</td>
						<td class="blue">'.$b['2k_l'].'</td>
						<td class="blue">'.$b['4k_l'].'</td>
						<td class="blue">'.$b['6k_l'].'</td>
						<td class="blue">'.$b['8k_l'].'</td>
						
					</tr>
					<tr>
						<td style="font-weight:bold;" class="red">R</td>
						<td class="red">'.$b['250_r'].'</td>
						<td class="red">'.$b['500_r'].'</td>
						<td class="red">'.$b['1k_r'].'</td>
						<td class="red">'.$b['2k_r'].'</td>
						<td class="red">'.$b['4k_r'].'</td>
						<td class="red">'.$b['6k_r'].'</td>
						<td class="red">'.$b['8k_r'].'</td>
				
					</tr>
				</table>
		 	</td>
			 <td width=40% valign=top style="border: 1px solid #000; margin-left: 4px;">
			 <table width=100% cellpadding=0 align=center style="font-size: 7pt;">
				 <tr>
					 <th>BETTER EAR</th>
					 <th>LESS GOOD EAR</th>						
				 </tr> 
				 <tr>
					 <td>AN AVERAGE OF ATLEAST <br /> 30 decibels</td>
					 <td>AN AVERAGE OF 40 decibels</td>						
				 </tr>
				 <tr>
					 <td align="center" colspan=2>AT FREQUENCIES: 500, 1000, 2000, 3000 Hz</td>
				 </tr>
			 </table>
		  </td>
		</tr>
	</table>
	<table width=86% style="border-collapse: collapse;" id="items" align=center style="margin-top: 5px;">
		<tr>
			<td width=60% valign=top style="border: 1px solid #000;">
				<table width=100% cellpadding=0 align=center style="font-size: 9pt;">
					<tr>
						<th>&nbsp;</th>
						<th>Right Ear</th>
						<th>Left Ear</th>
					</tr> 
					<tr>
						<td align=left class="indent">Pure Tone Average</td>
						<td class="red">'. $b['avg_r'] .'</td>
						<td class="blue">'. $b['avg_l'] .'</td>
					</tr> 
					<tr>
						<td align=left class="indent">Speech Reception Threshold</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr> 
					<tr>
						<td align=left class="indent">Speech Discrimination Score</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr> 
					<tr>
						<td align=left class="indent">Most Comfort Level</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr> 
					<tr>
						<td align=left class="indent">Uncomportable Level</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr> 
				</table>
		 	</td>
			<td width=40% valign=top style="border: 1px solid #000;">
				<table width=100% cellpadding=0 align=center style="font-size: 9pt;">
					<tr>
						<th>Audiograph Key</th>
						<th>Right Ear</th>
						<th>Left Ear</th>
					</tr> 
					<tr>
						<td align=left class="indent">Air Conduction</td>
						<td><img src="../images/icons/circle.png" width=10></td>
						<td><img src="../images/icons/x.png" width=8></td>
					</tr> 
					<tr>
						<td align=left class="indent">AC Mask</td>
						<td style="font-weight:bold;">&#9651;</td>
						<td style="font-weight:bold;">&#9647;</td>
					</tr> 
					<tr>
						<td align=left class="indent">Bone Conduction</td>
						<td style="font-weight:bold;"><</td>
						<td style="font-weight:bold;">></td>
					</tr> 
					<tr>
						<td align=left class="indent">BC Mask</td>
						<td style="font-weight:bold;">&#8969;</td>
						<td style="font-weight:bold;">&#8968;</td>
					</tr> 
					<tr>
						<td align=left class="indent">No Responce</td>
						<td>NR</td>
						<td>NR</td>
					</tr> 
				</table>
		 	</td>
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