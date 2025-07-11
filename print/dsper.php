<?php
	session_start();
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");


/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
	$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
	$_ihead = getArray("select sw_no, lpad(sw_no,2,0) as rr, date_format(sw_date,'%m/%d/%Y') as d8, withdrawn_by, requested_by, if(ref_type='PDN','FOR PRODUCTION',if(ref_type='MS','Maintenance Services','General Office Supplies')) as ref, if(request_date!='0000-00-00',date_format(request_date,'%m/%d/%Y'),'') as rd8, amount, remarks from sw_header where sw_no = '$_REQUEST[sw_no]' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
	$_idetails = dbquery("select item_code, description, qty, unit, cost, amount from sw_details where sw_no = '$_REQUEST[sw_no]' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
	$bcode = STR_PAD($_REQUEST['user'],2,'0',STR_PAD_LEFT)."-SW".$_ihead['sw_no']."-".date('Ymd');
	
	
	$date = formatDate($_REQUEST['dtf']);
	list($coke) = getArray("SELECT SUM(b.amount) FROM (SELECT DISTINCT tmpfileid FROM sjpi.pos_header WHERE trans_date = '$date' AND company = '$_SESSION[company]' AND branch='$_REQUEST[branch]') a INNER JOIN sjpi.pos_details b ON a.tmpfileid = b.tmpfileid WHERE  item_code LIKE '%BV-C%';");
	list($pepsi) = getArray("SELECT SUM(b.amount) FROM (SELECT DISTINCT tmpfileid FROM sjpi.pos_header WHERE trans_date = '$date' AND company = '$_SESSION[company]' AND branch='$_REQUEST[branch]') a INNER JOIN sjpi.pos_details b ON a.tmpfileid = b.tmpfileid WHERE  item_code LIKE '%BV-P%';");
	list($non_carbo) = getArray("SELECT SUM(b.amount) FROM (SELECT DISTINCT tmpfileid FROM sjpi.pos_header WHERE trans_date = '$date' AND company = '$_SESSION[company]' AND branch='$_REQUEST[branch]') a INNER JOIN sjpi.pos_details b ON a.tmpfileid = b.tmpfileid WHERE  item_code LIKE '%BV-NC%';");
	list($energy) = getArray("SELECT SUM(b.amount) FROM (SELECT DISTINCT tmpfileid FROM sjpi.pos_header WHERE trans_date = '$date' AND company = '$_SESSION[company]' AND branch='$_REQUEST[branch]') a INNER JOIN sjpi.pos_details b ON a.tmpfileid = b.tmpfileid WHERE  item_code LIKE '%BV-ED%';");
	list($sp) = getArray("SELECT SUM(b.amount) FROM (SELECT DISTINCT tmpfileid FROM sjpi.pos_header WHERE trans_date = '$date' AND company = '$_SESSION[company]' AND branch='$_REQUEST[branch]') a INNER JOIN sjpi.pos_details b ON a.tmpfileid = b.tmpfileid WHERE  item_code LIKE '%SP-%';");
	list($ice_cream) = getArray("SELECT SUM(b.amount) FROM (SELECT DISTINCT tmpfileid FROM sjpi.pos_header WHERE trans_date = '$date' AND company = '$_SESSION[company]' AND branch='$_REQUEST[branch]') a INNER JOIN sjpi.pos_details b ON a.tmpfileid = b.tmpfileid WHERE  item_code LIKE '%IC-%';");
	list($cake) = getArray("SELECT SUM(b.amount) FROM (SELECT DISTINCT tmpfileid FROM sjpi.pos_header WHERE trans_date = '$date' AND company = '$_SESSION[company]' AND branch='$_REQUEST[branch]') a INNER JOIN sjpi.pos_details b ON a.tmpfileid = b.tmpfileid WHERE  item_code LIKE '%CA-%';");

	$tb = $coke + $pepsi + $non_carbo + $energy;
	$t =  $sp + $ice_cream + $cake;
/* END OF SQL QUERIES */

 
$mpdf=new mPDF('win-1252','FOLIO','','',10,10,20,15,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");

if($_REQUEST['reprint'] == 'Y') {
	$mpdf->SetWatermarkText('REPRINTED COPY');
	$mpdf->showWatermarkText = true;
}

$mpdf->SetDisplayMode(60);

$html = '
<html>
<head>
<style>
body {font-family: sans-serif; font-size: 9pt; }
td { vertical-align: top; }

table thead td { 
	border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
    text-align: center;
}

.td-l { border-left: 0.1mm solid #000000; }
.td-r { border-right: 0.1mm solid #000000; }
.empty { border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; }

.items td.blanktotal {
    background-color: #FFFFFF;
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
    border-left: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}
.items td.tdTotals-r {
    text-align: right; font-weight: bold;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
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
		padding: 3px;
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
	text-align: left; font-weight: bold; padding: 3px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000;
}

.td-r-head {
	text-align: right; font-weight: bold; padding: 3px;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000;
}
.td-l-head-bottom {
	text-align: left; font-weight: bold; padding: 3px;
    border-left: 0.1mm solid #000000; border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}

.td-r-head-bottom {
	text-align: right; font-weight: bold; padding: 3px;
    border-right: 0.1mm solid #000000; border-top: 0.1mm solid #000000; border-bottom: 0.1mm solid #000000;
}

.billto {
	font-size: 12px; vertical-align: top; padding: 3px;
}
</style>
</head>
<body>

<!--mpdf
<div style="border:1px solid black;">
<table width="100%" cellpadding=0 cellspaing=0 style="font-size:8pt;border-collapse:collapse;">
<tr>
	<td rowspan="2" style="color:#000000;"><img src="../images/'.$co['headerlogo'].'" height=64 /></td>
	<td colspan=3 style="color:#000000; padding-top: 10px;width:80%;padding-left:10px;">
		<span> <b>DAILY SALES, PRODUCTION AND EXPENSES REPORT</b> </span>
	</td>
</tr>
<tr>
	
	<td style="padding-left:10px;padding-top:10px;border-right:none;">

		<span> Branch : ______________________ </span>
	</td>
	<td style="width:15%;border-left:none;border-right:none;">

	</td>
	<td style="padding-top:10px;border-left:none;">
		<span> Date : '.$_REQUEST['dtf'].'</span>
	</td>
</tr>
</table>
</div>
<table width=100% border=1>
	<tr>
		<td  align=center width="25%"> <b><u>EXPENSES</u></b> </td>
		<td  align=center width="25%"> <b><u>OTHERS SALES</u></b> </td>
		<td  align=center width="25%"> <b><u>BREAD PRODUCTION</u></b> </td>
		<td  align=center width="25%"> <b><u>BREAD NET PRODUCTION SUMMARY</u></b> </td>
	</tr>
	<tr>
		<td> 
			<table border=1 style="border-collapse:collapse;font-size:9pt;" width="100%"> 
					<tr>
						 <td> Particulars </td>
						 <td width=30%> Amount</td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;"><b> Total Expenses :</b></td>
						 <td> </td> 
					</tr>
			</table> 
		</td>

		<td> 
			<table border=1 style="border-collapse:collapse;font-size:9pt;" width="100%"> 
					<tr>
						 <td> Beverages </td>
						 <td width=30%> Amount</td> 
					</tr>
					<tr>
						 <td style="padding-left:10px;font-size:7pt;"> Coca Cola Products</td>
						 <td style="text-align:right;font-size:7pt;"> '.number_format($coke,2).' </td> 
					</tr>
					<tr>
						 <td style="padding-left:10px;font-size:7pt;"> Pepsi Cola Products</td>
						 <td style="text-align:right;font-size:7pt;"> '.number_format($pepsi,2).' </td> 
					</tr>
					<tr>
						 <td style="padding-left:10px;font-size:7pt;"> Non-carbonated Products</td>
						 <td style="text-align:right;font-size:7pt;"> '.number_format($non_carbo,2).' </td> 
					</tr>
					<tr>
						 <td style="padding-left:10px;font-size:7pt;"> Energy Drinks</td>
						 <td style="text-align:right;font-size:7pt;"> '.number_format($energy,2).' </td> 
					</tr>
					<tr>
						 <td style="padding-left:10px;font-size:7pt;"> Powdered Drinks</td>
						 <td style="text-align:right;font-size:7pt;"> </td> 
					</tr>
					<tr>
						 <td style="padding-left:10px;font-size:7pt;"> Bottled Water </td>
						 <td style="text-align:right;font-size:7pt;"> </td> 
					</tr>
					<tr>
						 <td style="padding-left:10px;font-size:7pt;"> Dispenser Drinks</td>
						 <td style="text-align:right;font-size:7pt;"> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;"><b><u>Total Beverages Sales </u></b></td>
						 <td style="text-align:right;font-size:7pt;"> '.number_format($tb,2).' </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;"><b>Cake </b></td>
						 <td style="text-align:right;font-size:7pt;"> '.number_format($cake,2).'</td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;"><b>Piaya </b></td>
						 <td style="text-align:right;font-size:7pt;"> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						<td style="text-align:right;font-size:7pt;"> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;"><b>Ice Crean Products </b></td>
						 <td style="text-align:right;font-size:7pt;"> '.number_format($ice_cream,2).' </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;"><b>Special Products </b></td>
						 <td style="text-align:right;font-size:7pt;"> '.number_format($sp,2).' </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;"><b>Load Sales </b></td>
						 <td style="text-align:right;font-size:7pt;"> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;"><b>Virginia </b></td>
						 <td style="text-align:right;font-size:7pt;"> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td style="text-align:right;font-size:7pt;"> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;"><b>Total Other Sales : </b></td>
						 <td style="text-align:right;font-size:7pt;"> '.number_format($t,2).'  </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td style="text-align:right;font-size:7pt;"> </td> 
					</tr>
			</table> 
		</td>

		<td> 
			<table border=1 style="border-collapse:collapse;font-size:9pt;" width="100%"> 
					<tr>
						 <td>  </td>
						 <td width=30%> Amount</td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">L.O. Previous</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">Production Today</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">Delivery Received</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;"><b>Gross Production</b></td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;"><u> DEDUCTION </u></td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">L.O. Today</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">Pulled Out</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">Bread Consumption</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">Bread Discounts</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">Branch Bread Deliveries</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:10px;font-size:7pt;">Branch : _____________</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:10px;font-size:7pt;">Branch : _____________</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">Bun</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">&nbsp;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">Spoilage</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td style="padding-left:1px;font-size:7pt;">Total Bread <br>Deduction</td>
						 <td> </td> 
					</tr>
			</table> 
		</td>

		<td> 
			<table border=1 style="border-collapse:collapse;font-size:9pt;" width="100%"> 
					<tr>
						 <td>  </td>
						 <td width=30%> Amount</td> 
					</tr>
					<tr>
						 <td height="30" style="padding-left:1px;font-size:7pt;">Gross Production</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td height="30" style="padding-left:1px;font-size:7pt;">Less: Bread Deduction</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td height="30" style="padding-left:1px;font-size:7pt;">Add: Bike Sales</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td height="30" style="padding-left:1px;font-size:7pt;"> <b> Bread Net Production </b> </td>
						 <td> </td> 
					</tr>
					<tr>
						 <td height="30" style="padding-left:1px;font-size:7pt;"><b><u>Total Bread Sales</u></b>;</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td height="30" style="padding-left:1px;font-size:7pt;">Balance (Zero Difference)</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td height="20" style="padding-left:1px;font-size:7pt;">Overage (Positive)</td>
						 <td> </td> 
					</tr>
					<tr>
						 <td height="20" style="padding-left:1px;font-size:7pt;">Shortage (Negative)</td>
						 <td> </td> 
					</tr>
			</table> 
		</td>
	</tr>
</table>

<table width=100% border=1 style="font-size:9pt;font-family:Arial;">
	<tr> 
		<td rowspan=2 width=32% align=center style="vertical-align:middle"> <u><b>SALES ON ACCOUNT</b></u> </td>
		<td colspan=2 align=center> <b>CASH LIQUIDATION</b> </td>
	</tr>
	<tr> 
		<td align=center width=43%> <u>CASH BREAKDOWN</u> </td>
		<td align=center width=57%> <u>DAILY SALES SUMMARY</u> </td>
	</tr>
	<tr> 
		<td> 
			<table width=100% border=1 style="border-collapse:collapse;">
				<tr>
					<td align=center> Employee\'s Name </td>
					<td align=center> Amount </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td>  </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td>  </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td>  </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td>  </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> <u><b>Total Sales on Account:</b></u> </td>
					<td> </td>
				</tr>
			</table>
		</td>

		<td> 
			<table width=100% border=1 style="border-collapse:collapse;">
				<tr>
					<td colspan=2 align=center> Breakdown Schedule</td>
					<td align=center width="33%"> Amount </td>
				</tr>
				<tr>
					<td align=center height="40" style="vertical-align:middle">Time:</td>
					<td width="33%"> </td>
					<td> </td>
				</tr>
				<tr>
					<td align=center height="40" style="vertical-align:middle">Time:</td>
					<td> </td>
					<td> </td>
				</tr>
				<tr>
					<td align=center height="40" style="vertical-align:middle">Time:</td>
					<td> </td>
					<td> </td>
				</tr>
				<tr>
					<td align=center height="40" style="vertical-align:middle">Time:</td>
					<td> </td>
					<td> </td>
				</tr>
				<tr>
					<td align=center height="40" style="vertical-align:middle">Time:</td>
					<td> </td>
					<td> </td>
				</tr>
				<tr>
					<td align=center height="40" style="vertical-align:middle">Time:</td>
					<td> </td>
					<td> </td>
				</tr>
				<tr>
					<td align=center height="40" style="vertical-align:middle">Time:</td>
					<td> </td>
					<td> </td>
				</tr>
				<tr>
					<td align=center height="40" style="vertical-align:middle">Time:</td>
					<td> </td>
					<td> </td>
				</tr>
				<tr>
					<td colspan=2 height="20" align=left style="vertical-align:middle"><b>Virginia</b></td>
					<td> </td>
				</tr>
				<tr>
					<td colspan=2 height="40" align=left valign=middle><b><u>Total</u> Cash Sales</b></td>
					<td> </td>
				</tr>
				<tr>
					<td colspan=2 height="40" align=left valign=middle>Less : Expenses </td>
					<td> </td>
				</tr>
				<tr>
					<td colspan=2 height="40" align=left valign=middle> <u><b>Total Cash Deposit</b></u> </td>
					<td> </td>
				</tr>
				<tr>
					<td colspan=2 height="20" > Less : Total Other Sales </td>
					<td>  </td>
				</tr>
				<tr>
					<td colspan=2 height="20" > <b>Bread Sales</b> </td>
					<td>  </td>
				</tr>
			</table>
		</td>

		<td style="border-bottom:none;">
			<table width=100% border=1 style="border-collapse:collapse;">
				<tr>
					<td height="40" width="10%"> </td>
					<td  width="45%" > </td>
					<td align=center>Amount</td>
				</tr>
				<tr>
					<td height="40"> </td>
					<td style="vertical-align:middle;"> Bread Sales </td>
					<td> </td>
				</tr>
				<tr>
					<td height="40"> </td>
					<td style="vertical-align:middle;"> Add: Total Expenses </td>
					<td> </td>
				</tr>
				<tr>
					<td height="40"> </td>
					<td style="vertical-align:middle;"> <b><u> Total Counter Sales</u></b>  </td>
					<td>  </td>
				</tr>
				<tr>
					<td height="40"> </td>
					<td style="vertical-align:middle;">  Add : Total Sales on Account   </td>
					<td>  </td>
				</tr>
				<tr>
					<td height="40"> </td>
					<td style="vertical-align:middle;"> <b><u> Total Bread Sales</u></b>  </td>
					<td>  </td>
				</tr>
				<tr>
					<td height="40" > </td>
					<td style="vertical-align:middle;">  Add : Total Others Sales </td>
					<td>  </td>
				</tr>
				<tr>
					<td height="40"> </td>
					<td style="vertical-align:middle;"> <b><u> Total Daily Gross Sales</u></b>  </td>
					<td>  </td>
				</tr>
				<tr>
					<td height="72" >  </td>
					<td>  </td>
					<td>  </td>
				</tr>
				<tr>
					<td height="40"> </td>
					<td> <b><u> Total Cash Deposit </u></b>  </td>
					<td>  </td>
				</tr>
				
			</table>
			<table>
				<tr>
					<td> <b>Cash/Check Deposit</b> </td>
				</tr>
					<tr>
					<td> 1st Deposit : ___________________ </td>
				</tr>
					<tr>
					<td> 2nd Deposit : ___________________</td>
				</tr>
					<tr>
					<td> Check Deposit : ___________________ </td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<table border=1 style="border-collapse:collapse;" width=100%>
				<tr>
					<td align=center colspan=3><b>Cashier\'s On Duty</b></td>
				</tr>
				<tr>
					<td width=33% height="20"> &nbsp; </td>
					<td width=33%> </td>
					<td width=33%> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
					<td> </td>
				</tr>
				<tr>
					<td height="20"> &nbsp; </td>
					<td> </td>
					<td> </td>
				</tr>
			</table>
		</td>


		<td style="padding-left:40px;border-top:bottom;">
		 Prepared By: <br> <br> <br>
			 ______________________________<br>
			 Supervisror\'s Name and Signarture
		</td>
	</tr>
</table>
<div align=right style="font-size:5pt;padding-right:20px"> FM-AC-001a Rev. 02 </div>	
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>