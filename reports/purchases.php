<?php
	session_start();
	require_once "../lib/mpdf6/mpdf.php";
	require_once "../handlers/_generics.php";
	
	
	$mpdf=new mPDF('win-1252','folio-l','','',10,10,32,15,10,10);
	$mpdf->use_embeddedfonts_1252 = true;    // false is default
	$mpdf->SetProtection(array('print'));
	$mpdf->SetAuthor("PORT80 Business Solutions");
	$mpdf->SetDisplayMode(75);
	
	$mydb = new _init;
	
	switch($_GET['type']) {
		case "1": $lbl = "Unserved Purchase Orders"; $f1 = " and qty_dld = 0"; break;
		case "2": $lbl = "Partially Served Purchase Orders"; $f1 = " and qty_dld > 0 and qty_dld < qty"; break;
		case "3": $lbl = "Fully Served Purchases Orders"; $f1 = " and qty_dld >= qty";	break;
		case "4": $lbl = "Partial/Fully Served P.Os"; $f1 = " and qty_dld > 0";	break;
		default: $lbl = "All Purchases"; $f1 = "";	break;
	}

	if($_GET['supplier'] != "") {
		$f0 = " and supplier = trim(leading '0' from '$_GET[supplier]') ";
	}

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
		$query = $mydb->dbquery("select * from (select lpad(a.po_no,6,0) as po, date_format(po_date,'%m/%d/%y') as pd8, po_date, concat('(',supplier,') ',supplier_name) as supp, b.item_code, b.description, b.qty, b.unit, (b.cost-b.discount) as cost, ROUND(b.qty * (b.cost-b.discount),2) as amount, b.qty_dld from po_header a left join po_details b on a.po_no=b.po_no and a.branch = b.branch where a.po_date between '".formatDate($_GET['dtf'])."' and '".formatDate($_GET['dt2'])."' and a.status='Finalized' $f0) a where 1=1 $f1 order by po_date desc;");
	/* END OF SQL QUERIES */

$html = '
<html>
<head>
<style>
body {font-family: sans-serif;
    font-size: 8pt;
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
<table width="100%">
	<tr>
		<td style="color:#000000; padding-top: 15px;">
			<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
		</td>
		<td width="40%" align=right>
			<span style="font-weight: bold; font-size: 9pt; color: #000000;">Summary of Purchases</span><br /><span style="font-size: 6pt; font-style: italic;"><b>('.$lbl.') '.$adesc.'</b><br/>Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
		</td>
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
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table class="items" width="100%" align=center style="font-size: 8pt; border-collapse: collapse;">
<thead>
	<tr>
		<td width="5%" align=center><b>PO #</b></td>
		<td width="5%" align=center><b>DATE</b></td>
		<td width="23%" align=left><b>SUPPLIER</b></td>
		<td width="30%" align=left><b>ITEM</b></td>
		<td width="10%" align=center><b>QTY</b></td>
		<td width="5%" align=center><b>UNIT</b></td>
		<td width="5%" align=right><b>COST</b></td>
		<td width="10%" align=right><b>AMOUNT</b></td>
		<td width="7%" align=right><b>QTY DEL\'D</b></td>
	</tr>
</thead>
<tbody>
<tr><td colspan=4></td></tr>';

while($row = $query->fetch_array(MYSQLI_BOTH)) {
	if($row['po'] != $oldPO) { $po = $row['po']; $date = $row['pd8']; $supp = $row['supp']; } else { $po = ""; $date = ""; $supp = ""; }
	$html = $html . '<tr>
		<td align=center>' . $po . '</td>
		<td align=center>' . $date . '</td>
		<td align=left>' . $supp . '</td>
		<td align=left>('.$row['item_code'].') '.$row['description'].'</td>
		<td align=center>'. $row['qty'].'</td>
		<td align=center>'. $row['unit'].'</td>
		<td align=right>' . number_format($row['cost'],2) . '</td>
		<td align=right>' . number_format($row['amount'],2) . '</td>
		<td align=right>' . number_format($row['qty_dld'],2) . '</td>
	</tr>'; $oldPO = $row['po']; $amtGT+=$row['amount'];
}

$html = $html . '<tr>
					<td colspan=7></td>
					 <td align=right>-----------------------<br/><b>'.number_format($amtGT,2).'</b><br/>==========</td>
					 <td></td>
			     </tr>';
$html = $html . '</tbody></table>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;
?>