<?php

	session_start();
	include("../../lib/mpdf6/mpdf.php");
	include("../../handlers/initDB.php");
	ini_set("memory_limit","1024M");
	ini_set("max_execution_time",0);
	ini_set("display_errors","On");
	
	$con = new myDB;
	
	if(isset($_REQUEST['area']) && $_REQUEST['area'] !=''){
   		$myDSG = " AND area = '$_REQUEST[area]' ";
	}
	$q = $con->dbquery("SELECT `name` AS emp,`bank`,`amount` FROM omdcpayroll.thirteenth_month where `year` = '$_REQUEST[year]' $myDSG order by `name` asc;");

			
$mpdf=new mPDF('win-1252','Folio','','',15,15,5,5,5,5);
$mpdf -> use_kwt = true;
$mpdf->use_embeddedfonts_1252 = true;    // false is default
//$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(40);


	
	
$html = '
<html>
<head>
<style>
body {font-family: Arial; font-size: 8pt; }
td { vertical-align: top; }
.e_info { border-top: 0.05mm solid #000000; }
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">

</htmlpageheader>

<sethtmlpageheader name="myheader" value="off" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="off" />
mpdf-->';
while($res = $q->fetch_array()) {
		$html = $html . '<table width="100%" >
			<tr>
				<td width="50%" align=left><span style="font-style: italic; font-size: 7pt;">*** THIS IS A SYSTEM GENERATED PAYSLIP</span></td>
				<td width="50%" align=right>
					<span style="font-weight: bold; font-size: 16pt; color: #000000;">PAYSLIP
				</td>
			</tr>
		</table>
		<table width="100%" cellspacing=0 cellpadding=0 class=e_info>
			<tr><td colspan=6 class=e_info>&nbsp;</td>
			<tr >
				<td width=15%>EMPLOYEE NAME</td>
				<td>:</td>
				<td width=35% style="padding-left: 5px;">'. strtoupper(iconv("UTF-8", "ISO-8859-1//IGNORE", $res['emp'])). '</td>
				<td width=20%>Pay Date</td>
				<td>:</td>
				<td width=30% style="padding-left: 5px;">12-10-'.$_REQUEST['year'].'</td>
			</tr>
			<tr>
				<td width=15%>ATM ACCT #</td><td>:</td>
				<td width=35% style="padding-left: 5px;">'. $res['bank'] . '</td>
				<td width=20%></td>
				<td></td>
				<td width=30% style="padding-left: 5px;"></td>
			</tr>
		</table>
		
		<div width=100% >
			<div width=49.9% height=238.9px style="float:left;border:1px solid black;font-family: Arial;font-size:12pt;font-weight:bold">'; 
			$html .= '<table style="border-collapse:collapse;font-size:7pt;" width=100%>
							<tr> 
								<td width=75% style="border-bottom:1.5px solid black;padding-left:5px;padding-right:5px;"> <b>&nbsp; </b> </td>
								<td width=25% style="border-bottom:1.5px solid black;padding-right:15px;padding-left:5px;padding-right:20px;" align=right><b> &nbsp; </b> </td>
							</tr>';
						
						$html .= '<tr> 
								<td width=75% style="padding-left:10px;font-size:9pt;padding-top:10px;"> 13th Month Pay </td>
								<td width=25% align=right style="padding-right:10px;font-size:9pt;padding-top:10px;"> '.number_format($res['amount'],2).'</td>
					  </tr>';
			
			$html .= '</table>';

			$html.='</div>
			<div width=49.4% height=238.9px style="float:left;border:1px solid black">';
				$html .= '<table style="border-collapse:collapse;font-size:7pt;" width=100%>
							<tr> 
								<td width=60% style="border-bottom:1.5px solid black;padding-left:5px;padding-right:5px;"> &nbsp;  </td>
								<td width=40% style="border-bottom:1.5px solid black;padding-right:15px;padding-left:5px;padding-right:20px;" align=right><b> &nbsp; </b> </td>
							</tr>';
			
			

				$html.='</table>
				</div>
			</div>
		
			<div width=100% style="border:1px solid black;" >
				<div width=48.5% style="float:left;padding-left:5px;padding-right:5px;">'; 
				$html .= '<table style="border-collapse:collapse;" width=100%>
							<tr> 
								<td width=60%> &nbsp; </td>
								<td width=40%> &nbsp; </td>
							</tr>';
				$html .= '</table>';

			$html.='</div>
			<div width=48% style="float:left;padding-left:5px;padding-right:5px;">'; 
			$html .= '<table style="border-collapse:collapse;" width=99%>
							<tr> 
								<td width=60%>&nbsp; <b>NET PAY </b> </td>
								<td width=40% align=right style="padding-right:0px"><b> '.number_format($res['amount'],2).' </b> </td>
							</tr>';
			$html .= '</table>';

			$html.='</div>
		</div>
		<table><tr><td height=60 valign=middle>-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</td></tr></table>';
		
	//}
}
$html = $html.'</body>
</html>
';

$html = utf8_encode($html);
$mpdf->WriteHTML($html);
$mpdf->Output(); 
exit;