<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit','2048M');
include("../lib/mpdf6/mpdf.php");
include("../includes/dbUSE.php");

$mpdf=new mPDF('win-1252','letter','','',15,15,35,10,10,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(60);

if($_GET['type'] != "") {
	if($_GET['type'] == 1) {
		$fs = " and current_balance <= 0 ";
		$label = "Employee Paid Loans";
	} else {
		$fs = " and current_balance > 0 ";
		$label = "Employee Outstanding Long Term Loans";
	}
} else { $label = "Employee Loans (Paid & Unpaid)"; }

$now = date("m/d/Y h:i a");
$ee_name = getArray("select concat('(',id_no,') ',lname,', ',fname,' ',mname) from e_master where id_no = '$_REQUEST[id_no]';");
	/* END OF SQL QUERIES */

$html = '
<html>
<head>
<style>
body {font-family: sans-serif;
      font-size: 10pt;
}
p {    margin: 0pt;
}
td { vertical-align: top; }

table thead td {
    text-align: center;
    border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
}
.items td.blanktotal {
    background-color: #FFFFFF;
    border: 0mm none #000000;
    border-top: 0.1mm solid #000000;
    border-right: 0.1mm solid #000000;
}

.items td.totals {
    text-align: right;
    border: 0.1mm solid #000000;
}

.items td.lowertotals {
	border: 0mm none #000000;
    border-top: 0.1mm solid #000000;
	border-bottom: 0.1mm solid #000000;
}

</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%"><tr>
<td width="100%" style="color:#000000;" align=left><span style="font-weight: bold; font-size: 9pt;">Geck Distributors Inc.</span><br /><span style="font-size: 8pt;">KM. 3, Brgy. Luna, Surigao City, Philippines</span>
<br /><br /><span style="font-weight: bold; font-size: 8pt;">'.$label.'</span></td>
</tr></table>
<table width="100%" style="color:#000000;" align=left>
<tr><td width="15%" style="font-size: 8pt;">EMPLOYEE :</td>
	<td style="font-size: 8pt;">' . $ee_name[0] . '</td>
</tr>
</table>
</htmlpageheader>

<htmlpagefooter name="myfooter">
<table style="border-top: 1px solid #000000; font-size: 7pt; width: 100%">
<tr>
<td width="50%" align="left">Page {PAGENO} of {nb}</td>
<td width="50%" align="right" style="font-size:7pt; font-color: #cdcdcd;">Run Date: ' . $now . '</td>
</tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="off" />
mpdf-->
<table class="items" width="100%" style="font-size: 7pt; border-collapse: collapse;" cellpadding="1">
<thead>
<tr>
<td width="15%"><b>TRANS ID</b></td>
<td width="15%"><b>LOAN DATE</b></td>
<td width="20%"><b>LOAN AMOUNT</b></td>
<td width="20%" align=right><b>PAYMENTS MADE</b></td>
<td width="30%" align=left><b>REFERENCE/MEMO</b></td>
</tr>
</thead>
<tbody>
<tr><td colspan=6></td></tr>';
	$_idetails = dbquery("select * from  (SELECT file_id AS lid, date_availed as xdate,  DATE_FORMAT(date_availed,'%m/%d/%Y') AS deyt, amount AS amt, balance, remarks, 'LOAN' as `type` FROM e_loans WHERE id_no = '$_REQUEST[id_no]' union all select ws_no as lid, trans_date as xdate,  date_format(trans_date,'%m/%d/%Y') as deyt, amount_due as amt, balance, '' as remarks, 'FUEL' as `type` from ws_slip a left join contact_info b on a.customer=b.file_id where b.emp_idno = '$_REQUEST[id_no]' and a.status = 'Finalized' union all select trans_id as lid, trans_date as xdate, date_format(trans_date,'%m/%d/%Y') as deyt, amount as amt, balance, '' as remarks, 'POS' as `type` from pos_header a left join contact_info b on a.customer=b.file_id where b.emp_idno = '$_REQUEST[id_no]' and a.status = 'Finalized') a order by xdate asc");
	while($row = mysql_fetch_array($_idetails)) {
		if($row['type'] == 'LOAN') { $xtype = 'CA'; } else { $xtype = 'PO'; }
		$applied = 0;
		$html = $html . '<tr>
			<td align=center>' . $xtype . '-' . $row['lid'] . '</td>
			<td align=center>' . $row['deyt'] . '</td>
			<td align=center>' . number_format($row['amt'],2) . '</td>
			<td></td><td>'. $row['remarks'] . '</td>
		</tr>';
		$_xdetails = dbquery("SELECT amount as amount_paid,CONCAT('Payroll: ',DATE_FORMAT(dtf,'%m/%d'),'-',DATE_FORMAT(dt2,'%m/%d'),', ',DATE_FORMAT(dt2,'%Y')) AS reference FROM e_paydeductions WHERE loan_id = '$row[lid]' and `type` = '$row[type]';");
		while($irow = mysql_fetch_array($_xdetails)) {
			$html = $html . '<tr>
				<td colspan=3></td>
				<td align=right>(' . number_format($irow['amount_paid'],2) . ')</td>
				<td align=left>' . $irow['reference'] . '</td>
			</tr>';
			$applied+=$irow['amount_paid'];
			$appliedGT+=$irow['amount_paid'];
		}
		$balance = $row['amt'] - $applied;
		$html = $html . "<tr><td colspan=2></td><td align=right><br><b>BALANCE &raquo;</b></td><td align=right>-------------------------<br/>" . number_format($balance,2) . "<br>===========</td><td></td></tr>";
		$amtGT+=$row['amt'];
		$balGT+=$balance;
		
	}
	$html = $html . "<tr><td colspan=2></td><td align=right><br><b>TOTAL DEDUCTION &raquo;</b></td><td align=right>-------------------------<br/>" . number_format($appliedGT,2) . "<br>===========</td><td></td></tr>";
$html = $html . "<tr><td colspan=2></td><td align=right><br><b>TOTAL BALANCE &raquo;</b></td><td align=right>-------------------------<br/>" . number_format($balGT,2) . "<br>===========</td><td></td></tr>";		
$html = $html . '</tbody>
</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>