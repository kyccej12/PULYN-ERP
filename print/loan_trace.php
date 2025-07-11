<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");


/* MYSQL QUERIES SECTION */
$_ihead = getArray("SELECT a.amount,dedu_amount,c.description,CONCAT(b.lname,', ',b.fname) AS emp_name 
					FROM hris.e_loans a LEFT JOIN hris.e_master b ON a.id_no = b.id_no LEFT JOIN hris.e_loantype c ON a.loan_type = c.type 
					WHERE a.file_id = '$_REQUEST[rid]';");	
$_idetails = dbquery("SELECT CONCAT(DATE_FORMAT(`period_start`,'%m/%d/%Y'),' - ',DATE_FORMAT(`period_end`,'%m/%d/%Y')) period,amount 
						FROM hris.e_loanposted a INNER JOIN hris.pay_periods b ON a.pay_period = b.period_id 
						WHERE a.loan_id = '$_REQUEST[rid]';");
$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
	
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','letter','','',15,15,75,75,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->setAutoTopMargin='stretch';
$mpdf->setAutoBottomMargin='stretch';
$mpdf->use_kwt = true;
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(40);

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
		/* background-color: #EEEEEE; */ padding: 3px;
		text-align: left; font-weight: bold;
		border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000;
		border-top: 0.1mm solid #000000;
	}
.td-r-top { 
	text-align: right; font-weight: bold; padding: 3px;
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
	font-size: 12px; vertical-align: top; padding: 3px;
}
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%" cellpadding=0 cellspaing=0>
<tr><td align=center><img src="../images/logosmall.jpg" /></td></tr>
<tr>
	<td width="100%" align=center>
		<span style="font-weight: bold; font-size: 16pt; color: #000000;"></span>
	</td>
</tr>
</table>
<br/><br/>
<table width="100%" cellpadding=0 cellspaing=0>
	<tr>
		<td width="15%">Employee : </td> <td align = left > '.$_ihead[emp_name].' </td>
	</tr>
	<tr>
		<td width="15%">Loan Type : </td> <td align = left > '.$_ihead[description].' </td>
	</tr>
	<tr>
		<td width="15%">Amount : </td> <td align = left > '.number_format($_ihead[amount],2).' </td>
	</tr>
</table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100%>
	<tr><td align=left>Page {PAGENO} of {nb}</td><td align=right>Print Date: '.date('m/d/Y h:i:s a',strtotime('8 hours')).'</td></tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->

<table class="items" width="100%" style="font-size: 11pt; border-collapse: collapse;" cellpadding="5" >
<thead>
	<tr>	
		<td width="60%"> Payroll Period</td>
		<td> Deduction </td>
		<td> Balance </td>
	</tr>
</thead>
<tbody>';
$style = 'style = "font-size:10pt;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;"';
	$i = 0; $total = 0;
	$rb = $_ihead['amount'];
	while($row = mysql_fetch_array($_idetails)) {
		$rb = $rb - $_ihead['dedu_amount'];
		$html .= "<tr>
						<td align=center> ".$row[period]."</td>
						<td align=center> ".$_ihead['dedu_amount']."</td>
						<td align=right style='padding-right:10px' >".number_format($rb,2)."</td>
				  </tr>";
		//$total += $row[amount];
	}
	$html .= "
			  <tr>
					<td align=center></td> <td align=center></td>
					<td align=right style='padding-right:10px' >============</td>
			  </tr>
			  ";
	/*
	$html .= "
			  <tr>
					<td align=center></td> <td align=center></td>
					<td align=right style='padding-right:10px' >============</td>
			  </tr>
			  <tr>
					<td align=center></td> <td align=right>Balance >> </td> 
					<td align=right style='padding-right:10px' >".number_format($_ihead[amount] - $total,2)."</td>
			  </tr>
			  ";
	*/
$html = $html . '
</tbody>
</table>
<br><br><br> 
<table>
	<tr>
		<td align=center>  Prepared By : <br><br><br> '.getUName($_SESSION['userid']).'</td>
	</tr>
</table>

</body>
</html>
';
$html = utf8_encode($html);
$mpdf->WriteHTML($html);

$mpdf->Output(); exit;

mysql_close($con);
?>