<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	include("../includes/dbUSE.php");
	

	$now = date("m/d/Y h:i a");
	$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
	$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]';");
		
	if($_GET['type'] != '') { $fs1 = " and c.group = '$_GET[type]' "; }
	if($_GET['branch'] != 'undefined') { 
		if($_GET['branch'] != '') {
			$fs2 = " and a.branch = '$_GET[branch]' "; 
			list($myBranch) = getArray("select branch_name from options_branches where branch_code = '$_GET[branch]';"); 
		} else { $myBranch = "Consolidated"; }
	} else { $myBranch = $bit['branch_name']; $fs2 = " and a.branch = '$_SESSION[branchid]' "; }
	$query = dbquery("SELECT b.item_code, b.description, c.indcode as stock_code, b.unit, SUM(qty) AS qty, SUM(ROUND(qty * (cost-b.discount),2)) AS amount FROM invoice_header a LEFT JOIN invoice_details b ON a.doc_no = b.doc_no AND a.branch = b.branch LEFT JOIN products_master c ON b.item_code = c.item_code WHERE a.invoice_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' and a.customer = trim(leading '0' from '$_GET[cid]') $fs1 $fs2 AND a.status = 'Finalized' ORDER BY b.description;");
	list($ndays) = getArray("select datediff('".formatDate($_GET['dt2'])."','".formatDate($_GET['dtf'])."');");
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="../ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#ffffff" leftmargin="10" bottommargin="100" rightmargin="20" topmargin="10" width="215">	
	<?php echo '<table width="100%">
		<tr>
			<td style="color:#000000; padding-top: 15px;">
				<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
			</td>
			<td width="40%" align=right>
				<span style="font-weight: bold; font-size: 9pt; color: #000000;">Sales Summary Report</span><br /><span style="font-size: 6pt; font-style: italic;"><b>'.$myBranch.'</b><br/>Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
			</td>
		</tr>
	</table>';
	?>
	<table cellspacing=0 cellpadding=0 border=0 width=100% cellspacing=5>	
		<tr bgcolor="#887e6e">
			<td width="15%" align=left class="gridHead"><b>ITEM CODE</b></td>
			<td width="15%" align=left class="gridHead"><b>STOCK CODE</b></td>
			<td width="30%" align=left class="gridHead"><b>DESCRIPTION</b></td>
			<td width="10%" align=center class="gridHead"><b>UNIT</b></td>
			<td width="10%" align=right class="gridHead"><b>QTY SOLD</b></td>
			<td width="10%" align=right class="gridHead"><b>AMOUNT</b></td>
			<td width="10%" align=right class="gridHead"><b>AVG. PRICE</b></td>
		</tr>
		<?php
			while($row = mysql_fetch_array($query)) {
				$avg = ROUND($row['amount'] / $row['qty'],2);
				echo '<tr>
					<td align=left valign=top class="grid"><b>' . $row['item_code'] . '</b></td>
					<td align=left  valign=top class="grid"><b>' . $row['stock_code'] . '</b></td>
					<td align=left  valign=top class="grid"><b>' . $row['description'] . '</b></td>
					<td align=center  valign=top class="grid">' . $row['unit'] . '</td>
					<td align=right  valign=top class="grid">' . number_format($row['qty'],2) . '</td>
					<td align=right  valign=top class="grid">' . number_format($row['amount'],2) . '</td>
					<td align=right  valign=top class="grid">' . number_format($avg,2) . '</td>
				</tr>'; $amtGT+=$row['amount']; $avg = 0;
			}
			echo '<tr>
					<td align=left valign=top class="grid" colspan=5 style="border-top: 1px solid #4a4a4a; border-bottom: 1px solid #4a4a4a;"><b>GRAND TOTAL &raquo;</b></td>
					<td align=right  valign=top class="grid" style="border-top: 1px solid #4a4a4a; border-bottom: 1px solid #4a4a4a;"><b>' . number_format($amtGT,2) . '</b></td>
					<td align=right  valign=top class="grid" style="border-top: 1px solid #4a4a4a; border-bottom: 1px solid #4a4a4a;">&nbsp;</td>
				</tr>';
		?>
	</table>
	<?php if($_GET['type'] == '') { ?>
		<table><tr><td height=8></td></tr></table>
		<table align=left cellpadding=0 cellspacing=0 border=0 width=400>	
			<tr bgcolor="#887e6e">
				<td width="40%" align=left class="gridHead"><b>SALES GROUP</b></td>
				<td width="20%" align=right class="gridHead"><b>QTY SOLD</b></td>
				<td width="20%" align=right class="gridHead"><b>AMOUNT</b></td>
				<td width="20%" align=right class="gridHead"><b>AVG. DAILY</b></td>
			</tr>
			
			<?php
			
					$q = dbquery("select d.group_description as igroup, sum(qty), sum(ROUND(qty * (cost-b.discount),2)) as amount from invoice_header a inner join invoice_details b on a.doc_no = b.doc_no and a.branch = b.branch left join products_master c on b.item_code = c.item_code left join options_igroup d on c.group = d.group where a.invoice_date between '".formatDate($_GET['dtf'])."' and '".formatDate($_GET['dt2'])."' $fs2 group by c.group;");
					while($xrow = mysql_fetch_array($q)) {
						echo '<tr>
							<td align=left  valign=top class="grid">' . $xrow['igroup'] . '</td>
							<td align=right  valign=top class="grid">' . number_format($xrow['qty'],2) . '</td>
							<td align=right  valign=top class="grid">' . number_format($xrow['amount'],2) . '</td>
							<td align=right  valign=top class="grid">' . number_format(ROUND($xrow['amount']/$ndays,2),2) . '</td>
						</tr>'; $qTot+=$xrow['qty']; $qAmt+=$xrow['amount'];
					}
					
					echo '<tr >
							<td colspan=2 align=left valign=top class="grid" style="border-top: 1px solid #4a4a4a; border-bottom: 1px solid #4a4a4a;"><b>GRAND TOTAL &raquo;</b></td>
							<td align=right  valign=top class="grid" style="border-top: 1px solid #4a4a4a; border-bottom: 1px solid #4a4a4a;"><b>' . number_format($qAmt,2) . '</b></td>
							<td align=left valign=top class="grid" style="border-top: 1px solid #4a4a4a; border-bottom: 1px solid #4a4a4a;">&nbsp;</b></td>
						</tr>';
			?>
		</table>
	<?php } ?>
	<table><tr><td height=8></td></tr></table>
</body>
</html>