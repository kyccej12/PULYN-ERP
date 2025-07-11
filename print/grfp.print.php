<?php
	
	session_start();
	require_once("../lib/mpdf6/mpdf.php");
	require_once("../handlers/_generics.php");
	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$_ihead = $con->getArray("SELECT a.grfp_no,a.emp_id AS employee_id,DATE_FORMAT(date_needed,'%m/%d/%Y') date_needed,a.costcenter,a.remarks,a.payee,a.emp_name AS name,a.department,a.payment_for,a.amount,a.status,a.payee_code,DATE_FORMAT(grfp_date,'%m/%d/%Y') grfp_date FROM grfp a WHERE a.branch = '$_SESSION[branchid]' AND a.grfp_no = '$_GET[grfp_no]';");	
	
	
	$costcenter = $con->identCostCenter($_ihead['costcenter']);
	$user = $con->getUname($_SESSION['userid']);

	list($digs,$fracs) = explode(".",$_ihead['amount']);
	$word = $con->inWords($digs);
	if($digs>0) { if($digs>1) { $p = 'PESOS'; } else { $p = 'PESO'; } }
	if($fracs>0) { if($fracs>1){ $c = 'CENTAVOS'; }else{ $c = 'CENTAVO'; }}
	if($fracs != '00') { $fracs = " & $fracs/100"; } else {	$fracs =''; }
	
	
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252',array(216,150),'','',10,10,100,20,3,3);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->setAutoTopMargin='stretch';
$mpdf->setAutoBottomMargin='stretch';
$mpdf->use_kwt = true;
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(40);

if($_REQUEST['reprint'] == 'Y') {
	$mpdf->SetWatermarkText('REPRINTED COPY');
	$mpdf->showWatermarkText = true;
}

$html = '
<html>
<head>
<style>
body {font-family: sans-serif; font-size: 9pt; }
td { vertical-align: top; }

table thead td { 
	border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
	/* background-color: #EEEEEE; */
    text-align: center;
}

.td-l { border-left: 0.1mm solid #000000; }
.td-r { border-right: 0.1mm solid #000000; }
.empty { border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; }

.items td.blanktotal {
    /* background-color: #FFFFFF; */
    border: 0.1mm solid #000000;
}
.items td.totals-l-top {
    text-align: right; font-weight: bold;
    border-left: 0.1mm solid #000000;
	border-top: 0.1mm solid #000000;
}
.items td.totals-r-top {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000;
	border-top: 0.1mm solid #000000;
}
.items td.totals-l {
    text-align: right; font-weight: bold;
    border-left: 0.1mm solid #000000;
}
.items td.totals-r {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000;
}

.items td.tdTotals-l {
    text-align: left; font-weight: bold;
    border-left: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000; /* background-color: #EEEEEE; */
}
.items td.tdTotals-r {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000; /* background-color: #EEEEEE; */
}

.items td.tdTotals-l-1 {
    text-align: left;
    border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}
.items td.tdTotals-r-1 {
    text-align: right;
    border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}

.td-l-top { 	
		/* background-color: #EEEEEE; */ padding: 5px;
		text-align: left; font-weight: bold;
		border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000;
		border-top: 0.1mm solid #000000;
	}
.td-r-top { 
	text-align: right; font-weight: bold; padding: 5px;
    border-right: 0.1mm solid #000000;
	border-top: 0.1mm solid #000000;
}

.td-l-head {
	text-align: left; font-weight: bold; padding-left: 5px;vertical-align:middle;padding-right: 5px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; /* background-color: #EEEEEE; */
}

.td-r-head {
	text-align: right; font-weight: bold; padding-left: 5px;vertical-align:middle;padding-right: 5px;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000;
}
.td-l-head-bottom {
	text-align: left; font-weight: bold; padding-left: 5px;vertical-align:middle;padding-right: 5px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; /* background-color: #EEEEEE; */ border-bottom: 0.1mm solid #000000;
}

.td-r-head-bottom {
	text-align: right; font-weight: bold; padding-left: 5px;padding-right: 5px;vertical-align:middle;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}

.billto {
	font-size: 12px; vertical-align: top; padding: 5px;
}
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%" cellpadding=0 cellspaing=0>
<tr><td align=center><img src="../images/doc-header.jpg" height="100" width="100%" /></td></tr>
<tr>
	<td width="100%" align=center>
		<span style="font-weight: bold; font-size: 16pt; color: #000000;">PETTY CASH REQUEST</span>
	</td>
</tr>
</table>

<table width="100%" border=0>
	<tr><td align="right"> RFP No. :</td> <td width="25%" align="right">'.str_pad($_ihead['grfp_no'],6,0,STR_PAD_LEFT).'</td></tr>
	<tr><td align="right"> Unit Code/Cost Center :</td> <td width="25%" align="right">'.$costcenter.'</td></tr>
</table>
<table width="100%" >
		<tr>
			<td width="20%" align="right">Name of Requestor</td><td width="30%" style="border-bottom:1px solid black;padding-left:10px;" >'.utf8_decode($_ihead['name']).'</td>
			<td width="20%" align="right">Date of Request</td><td width="30%" style="border-bottom:1px solid black;padding-left:10px;">'.$_ihead['grfp_date'].'</td>
		</tr>
		<tr>
			<td align="right">Department</td><td width="30%" style="border-bottom:1px solid black;padding-left:10px;" > '.$_ihead['department'].' </td>
			<td align="right">Date Payment Required</td><td width="30%" style="border-bottom:1px solid black;padding-left:10px;" >'.$_ihead['date_needed'].' </td>
		</tr>
</table>
<br>
<div style = "padding-left:5%">May I request for Partial / Full payment of the following : </div> <br>
<div style = "border:1px solid black;">
	<table width="100%">
		<tr><td height="4"></td> </tr>
		<tr>
			<td width="15%"> Payment For :  </td> <td style="border-bottom:1px solid black;padding-left:10px;"> '.$_ihead['payment_for'].' </td>
			
		</tr>
		<tr>
			<td>&nbsp;</td><td style="border-bottom:1px solid black;"> </td>
		</tr>
		<tr>
			<td width="15%"> Remarks :  </td> <td style="border-bottom:1px solid black;padding-left:10px;"> '.$_ihead['remarks'].' </td>
			
		</tr>
		<tr>
			<td>&nbsp;</td><td style="border-bottom:1px solid black;"> </td>
		</tr>
		<tr>
			<td>&nbsp;</td><td> </td>
		</tr>
	</table>
</div>
<br>
<table width = "100%">
	<tr> 
		<td width="10%">Payee</td>
		<td width="50%" style="border-bottom:1px solid black;padding-left:10px;"> '.utf8_decode($_ihead['payee']).'</td>
		<td width="20%" align ="right">Amount</td>
		<td width="20%" style="border-bottom:1px solid black;" align="center"> '.number_format($_ihead['amount'],2).' </td>
	</tr>
</table><br>
<table width = "100%">
	<tr> 
		<td width="20%">AMOUNT IN WORDS</td>
		<td style="border-bottom:1px solid black;padding-left:10px;" colspan="3">'.$word.' '.$p.' '.$fracs.' '.$c.' ONLY</td>
	</tr>
</table><br>
<div style="border:1px solid black;">
<table width=100% cellpadding=5 style="border: 1px solid #000000;">
		<tr>
			<td width=33% align=center><b>PREPARED BY:</b><br><br>'.$con->getUname($_REQUEST['user']).'<br></td>
			<td width=33% align=center><b>CHECKED BY:</b><br><br><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Employe Name Here - &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br><font size=3>Printed Name Over Signature</font></td>
			<td width=33% align=center><b>APPROVED BY:</b><br><br><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jaime A. Gochoco&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br><font size=3>VP - FINANCE</font></td>
			
		</tr>
</table>
</div>
</htmlpageheader>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->';
$style = 'style = "font-size:11pt;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;"';
$html = $html.'
</body>
</html>
';
$html = utf8_encode($html);
$mpdf->WriteHTML($html);

$mpdf->Output(); exit;
exit;
?>