<?php
session_start();
require_once "../lib/mpdf6/mpdf.php";
require_once "../handlers/_generics.php";

$mydb = new _init;

$mpdf=new mPDF('win-1252','legal-l','','',15,15,35,25,10,10);
$mpdf->use_embeddedfonts_1252 = true;
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(75);

	/* MYSQL QUERIES SECTION */
		$date = $mydb->formatDate($_GET['asof']);
		$now = date("m/d/Y h:i a");

		$lbl = "ACCOUNTS PAYABLE AGING SCHEDULE";
		$co = $mydb->getArray("select * from companies where company_id = '1';");
		$fDates = $mydb->getArray("select date_format('" . $date . "','%W %M %d, %Y') as date;");
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
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
	<tr>
		<td style="color:#000000;" width=80><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
		<td style="color:#000000; padding-top: 15px;">
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
		</td>
		<td align=right><span style="font-weight: bold; font-size: 8pt;">'.$lbl.'<br />Date As Of: '. $fDates[0] . '</span></td>
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
<table class="items" width="100%" style="font-size: 8pt; border-collapse: collapse;" cellpadding="5" border="1">
<thead>
<tr>
<td width="25%" align=center><b>SUPPLIER</b></td>
<td width="5%" align=center><b>TERMS</b></td>
<td width="8%" align=center><b>CREDIT LIMIT</b></td>
<td width="8%" align=center><b>CURRENT</b></td>
<td width="8%" align=center><b>1-30 DAYS</b></td>
<td width="8%" align=center><b>31-60 DAYS</b></td>
<td width="8%" align=center><b>61-90 DAYS</b></td>
<td width="10%" align=center><b>91-120 DAYS</b></td>
<td width="10%" align=center><b>OVER 120 DAYS</b></td>
<td width="10%" align=center><b>OUTSTANDING BALANCE</b></td>
</tr>
</thead>
<tbody>';

	$a = $mydb->dbquery("select supplier, b.tradename, b.credit_limit, c.description as terms, ROUND(sum(cur),2) as cur, ROUND(sum(bal1),2) as bal1, ROUND(sum(bal2),2) as bal2, ROUND(sum(bal3),2) as bal3, ROUND(sum(bal4),2) as bal4, ROUND(sum(bal5),2) as bal5 from (select a.supplier, balance as cur, 0 as bal1, 0 as bal2, 0 as bal3, 0 as bal4, 0 as bal5 from (select a.supplier, if('$date' <= date_add(apv_date,INTERVAL a.terms DAY),'0',datediff('$date',date_add(apv_date,INTERVAL a.terms DAY))) as daysdue, balance from apv_header a left join options_terms b on a.terms=b.terms_id where apv_date <= '$date' and balance > 0 and a.status='Posted' union all select b.customer as supplier, if('$date' <= date_add(invoice_date,INTERVAL c.terms DAY),'0',datediff('$date',date_add(invoice_date,INTERVAL c.terms DAY))) as daysdue, balance from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch left join contact_info c on b.customer=c.file_id left join options_terms d on c.terms=d.terms_id where invoice_date <= '$date' and a.status = 'Posted' and balance > 0 and a.branch = '1') a where daysdue <= 0 union all select a.supplier, 0 as cur, balance as bal1, 0 as bal2, 0 as bal3, 0 as bal4, 0 as bal5 from (select a.supplier, if('$date' <= date_add(apv_date,INTERVAL a.terms DAY),'0',datediff('$date',date_add(apv_date,INTERVAL a.terms DAY))) as daysdue, balance from apv_header a left join options_terms b on a.terms=b.terms_id where apv_date <= '$date' and balance > 0 and a.status='Posted' and branch = '1' union all select b.customer as supplier, if('$date' <= date_add(invoice_date,INTERVAL c.terms DAY),'0',datediff('$date',date_add(invoice_date,INTERVAL c.terms DAY))) as daysdue, balance from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch left join contact_info c on b.customer=c.file_id left join options_terms d on c.terms=d.terms_id where invoice_date <= '$date' and a.status = 'Posted' and balance > 0) a where daysdue between 1 and 30 union all select a.supplier, 0 as cur, 0 as bal1, balance as bal2, 0 as bal3, 0 as bal4, 0 as bal5 from (select a.supplier, if('$date' <= date_add(apv_date,INTERVAL a.terms DAY),'0',datediff('$date',date_add(apv_date,INTERVAL a.terms DAY))) as daysdue, balance from apv_header a left join options_terms b on a.terms=b.terms_id where apv_date <= '$date' and balance > 0 and a.status='Posted' union all select b.customer as supplier, if('$date' <= date_add(invoice_date,INTERVAL c.terms DAY),'0',datediff('$date',date_add(invoice_date,INTERVAL c.terms DAY))) as daysdue, balance from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch left join contact_info c on b.customer=c.file_id left join options_terms d on c.terms=d.terms_id where invoice_date <= '$date' and a.status = 'Posted' and balance > 0) a where daysdue between 31 and 60 union all select a.supplier, 0 as cur, 0 as bal1, 0 as bal2, balance as bal3, 0 as bal4, 0 as bal5 from (select a.supplier, if('$date' <= date_add(apv_date,INTERVAL a.terms DAY),'0',datediff('$date',date_add(apv_date,INTERVAL a.terms DAY))) as daysdue, balance from apv_header a left join options_terms b on a.terms=b.terms_id where apv_date <= '$date' and balance > 0 and a.status='Posted' union all select b.customer as supplier, if('$date' <= date_add(invoice_date,INTERVAL c.terms DAY),'0',datediff('$date',date_add(invoice_date,INTERVAL c.terms DAY))) as daysdue, balance from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch left join contact_info c on b.customer=c.file_id left join options_terms d on c.terms=d.terms_id where invoice_date <= '$date' and a.status = 'Posted' and balance > 0) a where daysdue between 61 and 90 union all select a.supplier, 0 as cur, 0 as bal1, 0 as bal2, 0 as bal3, balance as bal4, 0 as bal5 from (select a.supplier, if('$date' <= date_add(apv_date,INTERVAL a.terms DAY),'0',datediff('$date',date_add(apv_date,INTERVAL a.terms DAY))) as daysdue, balance from apv_header a left join options_terms b on a.terms=b.terms_id where apv_date <= '$date' and balance > 0 and a.status='Posted' union all select b.customer as supplier, if('$date' <= date_add(invoice_date,INTERVAL c.terms DAY),'0',datediff('$date',date_add(invoice_date,INTERVAL c.terms DAY))) as daysdue, balance from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch left join contact_info c on b.customer=c.file_id left join options_terms d on c.terms=d.terms_id where invoice_date <= '$date' and a.status = 'Posted' and balance > 0) a where daysdue between 91 and 120 union all select a.supplier, 0 as cur, 0 as bal1, 0 as bal2, 0 as bal3, 0 as bal4, balance as bal5 from (select a.supplier, if('$date' <= date_add(apv_date,INTERVAL a.terms DAY),'0',datediff('$date',date_add(apv_date,INTERVAL a.terms DAY))) as daysdue, balance from apv_header a left join options_terms b on a.terms=b.terms_id where apv_date <= '$date' and balance > 0 and a.status='Posted' union all select b.customer as supplier, if('$date' <= date_add(invoice_date,INTERVAL c.terms DAY),'0',datediff('$date',date_add(invoice_date,INTERVAL c.terms DAY))) as daysdue, balance from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch left join contact_info c on b.customer=c.file_id left join options_terms d on c.terms=d.terms_id where invoice_date <= '$date' and a.status = 'Posted' and balance > 0 $fs1) a where daysdue > 120) a left join contact_info b on a.supplier = b.file_id left join options_terms c on b.terms = c.terms_id group by supplier order by b.tradename asc;");
	while($row = $a->fetch_array(MYSQLI_BOTH)) {
		$total = ROUND($row['cur'] + $row['bal1'] + $row['bal2'] + $row['bal3'] + $row['bal4'] + $row['bal5'],2);
		$html = $html . '<tr>
		<td align="left">('.$row['supplier'].') ' . $row['tradename'] . '</td>
		<td align="center">' . $row['terms'] . '</td>
		<td align="center">' . number_format($row['credit_limit'],2) . '</td>
		<td align="right">' . number_format($row['cur'],2) . '</td>
		<td align="right">' . number_format($row['bal1'],2) . '</td>
		<td align=right>' . number_format($row['bal2'],2) . '</td>
		<td align="right">' . number_format($row['bal3'],2) . '</td>
		<td align="right">' . number_format($row['bal4'],2) . '</td>
		<td align="right">' . number_format($row['bal5'],2) . '</td>
		<td align="right">' . number_format($total,2) . '</td>
		</tr>'; $curGT+=$row['cur']; $bal1GT+=$row['bal1']; $bal2GT+=$row['bal2']; $bal3GT+=$row['bal3']; $bal4GT+=$row['bal4']; $bal5GT+=$row['bal5']; $totGT+=$total;
	}
	$html = $html . '<tr>
		<td align="left" colspan=3><b>Grand Total &raquo;'.$query.'</b></td>
		<td align="right"><b>' . number_format($curGT,2) . '</b></td>
		<td align="right"><b>' . number_format($bal1GT,2) . '</b></td>
		<td align=right><b>' . number_format($bal2GT,2) . '</b></td>
		<td align="right"><b>' . number_format($bal3GT,2) . '</b></td>
		<td align="right"><b>' . number_format($bal4GT,2) . '</b></td>
		<td align="right"><b>' . number_format($bal5GT,2) . '</b></td>
		<td align="right"><b>' . number_format($totGT,2) . '</b></td>
		</tr>';
		$html = $html . '<tr>
		<td align="left" colspan=3><b>% To Total &raquo;</b></td>
		<td align="right"><b>' . number_format(($curGT/$totGT)*100,2) . '%</b></td>
		<td align="right"><b>' . number_format(($bal1GT/$totGT)*100,2) . '%</b></td>
		<td align=right><b>' . number_format(($bal2GT/$totGT)*100,2) . '%</b></td>
		<td align="right"><b>' . number_format(($bal3GT/$totGT)*100,2) . '%</b></td>
		<td align="right"><b>' . number_format(($bal4GT/$totGT)*100,2) . '%</b></td>
		<td align="right"><b>' . number_format(($bal5GT/$totGT)*100,2) . '%</b></td>
		<td align="right"><b>100%</b></td>
		</tr>';


$html = $html . '
</tbody>
</table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>