<?php
	session_start();
	ini_set("max_execution_time",0);
	require_once "../handlers/_generics.php";
	$mydb = new _init;


	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$ydtf = "$_GET[year]-01-01";
		$xydtf = "01/01/$_GET[year]";
		$dtf = "$_GET[year]-$_GET[month]-01";
		$xdtf = "$_GET[month]/01/$_GET[year]";
		$fs = '';
		list($dt2,$xdt2,$month) = $mydb->getArray("select last_day('$dtf'), date_format(last_day('$dtf'),'%m/%d/%Y'), date_format('$dtf','%M');");
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
		
		if($_GET['cc'] != '') { $fs = " and cost_center = '$_GET[cc]' "; list($pcode) = $mydb->getArray("select proj_code from options_project where proj_id = '$_GET[cc]';"); } else { $pcode = "Consolidated"; }
		
	/* END OF SQL QUERIES */

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="../ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<style>
		body { margin: 20px; }
		td { font-size: 12px; }
	</style>
	
</head>
<body bgcolor="#ffffff" width="215">	
	<?php echo '<table width="100%">
		<tr>
			<td style="color:#000000; padding-top: 15px;">
				<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
			</td>
			<td width="40%" align=right>
				<span style="font-weight: bold; font-size: 9pt; color: #000000;">Income Statement for the Period '.$month.' '.$_GET['year'].' ['.$pcode.']</span>
			</td>
		</tr>
	</table>';
	?>
	<table cellspacing=0 cellpadding=0 border=0 width=100% cellspacing=5>	
		<tr bgcolor="#887e6e">
			<td width="70%" align=left class="gridHead" colspan=2>&nbsp;</td>
			<td width="15%" align=right class="gridHead"><b>FOR THE PERIOD</b></td>
			<td width="15%" align=right class="gridHead"><b>YEAR-TO-DATE</b></td>
		</tr>
		<tr><td align="left" colspan=4 style="border-bottom: 0.1mm solid black;"><b>REVENUE FOR THE PERIOD</b></td></tr>
		<?php
			/* SALES */
			$a = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '9' AND doc_date BETWEEN '$ydtf' AND '$dt2' $fs GROUP BY a.acct order by b.description asc;");
			while($b = $a->fetch_array(MYSQLI_BOTH)) {
				$bb = $mydb->getArray("select sum(credit-debit) as amount from acctg_gl where acct = '$b[acct]' and doc_date between '$dtf' and '$dt2' $fs group by acct;");
				print '<tr><td align="left" colspan=2>'.$b['description'].'</td><td align="right"><a href="glschedule.php?dtf='.$xdtf.'&dt2='.$xdt2.'&acct='.$b['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.$mydb->formatNumber($bb['amount'],2).'</a></td><td align="right"><a href="glschedule.php?dtf='.$xydtf.'&dt2='.$xdt2.'&acct='.$b['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.$mydb->formatNumber($b['amount'],2).'</a></td></tr>';
				$ytSales+=$b['amount']; $tSales+=$bb['amount'];
			}
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>TOTAL REVENUE</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($tSales,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($ytSales,2).'</b></td></tr>';
			print '<tr><td colspan=8>&nbsp;</td></tr>';
			/* End of Sales */
			
			/* COGS */
			print '<tr><td align="left" colspan=4 style="border-bottom: 0.1mm solid black;"><b>COST OF SALES OR SERVICES</b></td></tr>';
			$c = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE  b.acct_grp = '14' AND doc_date BETWEEN '$ydtf' AND '$dt2' $fs GROUP BY a.acct order by b.description asc;");
			while($d = $c->fetch_array(MYSQLI_BOTH)) {
				$dd = $mydb->getArray("select sum(debit-credit) as amount from acctg_gl where acct = '$d[acct]' and doc_date between '$dtf' and '$dt2' $fs group by acct;");
				print '<tr><td align="left" colspan=2>'.$d['description'].'</td><td align="right"><a href="glschedule.php?dtf='.$xdtf.'&dt2='.$xdt2.'&acct='.$d['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.$mydb->formatNumber($dd['amount'],2).'</a></td><td align="right"><a href="glschedule.php?dtf='.$xydtf.'&dt2='.$xdt2.'&acct='.$d['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.$mydb->formatNumber($d['amount'],2).'</a></td></tr>';
				$ytCOGS+=$d['amount']; $tCOGS+=$dd['amount'];
			}
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>TOTAL COST SALES OR SERVICES</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($tCOGS,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($ytCOGS,2).'</b></td></tr>';
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>GROSS PROFIT</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($tSales - $tCOGS,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($ytSales - $ytCOGS,2).'</b></td></tr>';
			print '<tr><td colspan=8>&nbsp;</td></tr>';
			/* End of COGS */
			
			/* General & Operating Expenses */
			print '<tr><td align="left" width=40% style="border-bottom: 0.1mm solid black;"><b>GENERAL & OPERATING EXPENSES</b></td><td width=30% style="border-bottom: 0.1mm solid black; font-weight: bold;"></td><td colspan=2 style="border-bottom: 0.1mm solid black;">&nbsp;</td></tr>';
			$e = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp IN ('12') AND doc_date BETWEEN '$ydtf' AND '$dt2' $fs GROUP BY a.acct ORDER BY b.description;");
			while($f = $e->fetch_array(MYSQLI_BOTH)) {
				$ff = $mydb->getArray("select sum(debit-credit) as amount from acctg_gl where acct = '$f[acct]' and doc_date between '$dtf' and '$dt2' $fs group by acct;");
				print '<tr><td align="left">'.$f['description'].'</td><td>'.$f['cost_center'].'</td><td align="right"><a href="glschedule.php?dtf='.$xdtf.'&dt2='.$xdt2.'&acct='.$f['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.$mydb->formatNumber($ff['amount'],2).'</a></td><td align="right"><a href="glschedule.php?dtf='.$xydtf.'&dt2='.$xdt2.'&acct='.$f['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.$mydb->formatNumber($f['amount'],2).'</a></td></tr>';
			}
			
			list($tExpenses) = $mydb->getArray("select sum(debit-credit) from acctg_gl a inner join acctg_accounts b on a.acct = b.acct_code WHERE  b.acct_grp in ('12') and doc_date between '$dtf' and '$dt2' $fs;");
			list($ytExpenses) = $mydb->getArray("select sum(debit-credit) from acctg_gl a inner join acctg_accounts b on a.acct = b.acct_code WHERE  b.acct_grp in ('12') and doc_date between '$ydtf' and '$dt2' $fs;");
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>TOTAL OPERATING EXPENSES</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($tExpenses,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($ytExpenses,2).'</b></td></tr>';
			print '<tr><td colspan=4 height=8>&nbsp;</td></tr>';
			/* End of OPERATING EXPENSES */
		
			/* Net Operating Income or Loss */
			list($noi) = $mydb->getArray("select sum(credit-debit) from acctg_gl a inner join acctg_accounts b on a.acct = b.acct_code WHERE  b.acct_grp in ('9','12','14') and doc_date between '$dtf' and '$dt2' $fs;");
			list($ynoi) = $mydb->getArray("select sum(credit-debit) from acctg_gl a inner join acctg_accounts b on a.acct = b.acct_code WHERE  b.acct_grp in ('9','12','14') and doc_date between '$ydtf' and '$dt2' $fs;");
			if($ynoi < 0) { $yion = '('.$mydb->formatNumber(abs($ynoi),2).')'; } else  { $yion = $mydb->formatNumber($ynoi,2); }
			if($noi < 0) { $lbl0 = "NET OPERATING LOSS"; $ion = '('.$mydb->formatNumber(abs($noi),2).')'; } else { $lbl0 = "NET OPERATING INCOME"; $ion = $mydb->formatNumber($noi,2); }
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>'.$lbl0.'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$ion.'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$yion.'</b></td></tr>';
		
			/* Other Income */
			list($isOthIncome) = $mydb->getArray("select count(*) from acctg_gl where acct in (select acct_code from acctg_accounts where acct_grp in ('10','11')) and doc_date between '$ydtf' and '$dt2' $fs;");
			if($isOthIncome > 0) {
				print '<tr><td align="left" colspan=4 style="border-bottom: 0.1mm solid black;"><b>ADD: OTHER INCOME</b></td></tr>';
				$g = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE  b.acct_grp = '11' AND doc_date BETWEEN '$ydtf' AND '$dt2' $fs GROUP BY a.acct order by b.description asc;");
				while($h = $g->fetch_array(MYSQLI_BOTH)) {
					$hh = $mydb->getArray("select sum(credit-debit) as amount from acctg_gl where acct = '$h[acct]' and doc_date between '$dtf' and '$dt2' $fs group by acct;");
					print '<tr><td align="left" colspan=2>'.$h['description'].'</td><td align="right"><a href="glschedule.php?dtf='.$xdtf.'&dt2='.$xdt2.'&acct='.$h['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.$mydb->formatNumber($hh['amount'],2).'</a></td><td align="right"><a href="glschedule.php?dtf='.$xydtf.'&dt2='.$xdt2.'&acct='.$h['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.$mydb->formatNumber($h['amount'],2).'</a></td></tr>';
					$ytOtherIncome+=$h['amount']; $tOtherIncome+=$hh['amount'];
				}
				print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>TOTAL OTHER INCOME</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($tOtherIncome,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($ytOtherIncome,2).'</b></td></tr>';
			}
			
			$CurNetOperating = $noi + $tOtherIncome;
			$YtdNetOperating = $ynoi + $ytOtherIncome;
			if($YtdNetOperating < 0) { $xyion = '('.$mydb->formatNumber(abs($YtdNetOperating),2).')'; } else  { $xyion = $mydb->formatNumber($YtdNetOperating,2); }
			if($CurNetOperating < 0) { $xion = '('.$mydb->formatNumber(abs($CurNetOperating),2).')'; } else { $xion = $mydb->formatNumber($CurNetOperating,2); }
			if($YtdNetOperating < 0 and  $CurNetOperating < 0 ) { $lblS = "NET LOSS BEFORE DEPRECIATION"; } else { $lblS = "NET INCOME or LOSS BEFORE DEPRECIATION"; }
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>'.$lblS.'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$xion.'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$xyion.'</b></td></tr>';
	
			/* DEPRECIATION EXPENSE */
			print '<tr><td colspan=4 height=8>&nbsp;</td></tr>';
			print '<tr><td align="left" style="border-bottom: 0.1mm solid black; font-weight: bold;">DEPRECIATION EXPENSES</td><td align="left" style="border-bottom: 0.1mm solid black; font-weight: bold;">COST CENTER</td><td align="left" style="border-bottom: 0.1mm solid black;" colspan=2>&nbsp;</td></tr>';
			$depQuery = $mydb->dbquery("SELECT a.acct, a.cost_center, b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE  b.acct_grp in ('13') AND doc_date BETWEEN '$ydtf' AND '$dt2' $fs GROUP BY a.acct, a.cost_center order by a.cost_center, b.description;");
			while($depRow = $depQuery->fetch_array(MYSQLI_BOTH)) {
				list($curDep) = $mydb->getArray("select sum(debit-credit) as amount from acctg_gl where acct = '$depRow[acct]' and cost_center = '$depRow[cost_center]' and doc_date between '$dtf' and '$dt2' $fs group by acct;");
				print '<tr><td align="left">'.$depRow['description'].'</td><td>'.$depRow['cost_center'].'</td><td align="right"><a href="glschedule.php?dtf='.$xdtf.'&dt2='.$xdt2.'&acct='.$depRow['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.$mydb->formatNumber($curDep,2).'</a></td><td align="right"><a href="glschedule.php?dtf='.$xydtf.'&dt2='.$xdt2.'&acct='.$depRow['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.$mydb->formatNumber($depRow['amount'],2).'</a></td></tr>';
				$yDepGT+=$depRow['amount']; $curDepGT+=$curDep;
			}
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>TOTAL DEPRECIATION EXPENSES</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($curDepGT,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($yDepGT,2).'</b></td></tr>';
			print '<tr><td colspan=4 height=8></td></tr>';
			
			$ni_adep = $noi + $tOtherIncome - $curDepGT;
			$yni_adep = $ynoi + $ytOtherIncome - $yDepGT;
			if($yni_adep < 0) { $niadep_yion = '('.$mydb->formatNumber(abs($yni_adep),2).')'; } else  { $niadep_yion = $mydb->formatNumber($yni_adep,2); }
			if($ni_adep < 0) { $niadep_ion = '('.$mydb->formatNumber(abs($ni_adep),2).')'; } else { $niadep_ion = $mydb->formatNumber($ni_adep,2); }
			if($ni_adep < 0 and $yni_adep < 0) { $lblT = "TOTAL NET LOSS"; } else { $lblT = "TOTAL NET INCOME or LOSS"; }
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>'.$lblT.'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$niadep_ion.'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$niadep_yion.'</b></td></tr>';

			if($yni_adep > 0) {
				$yitax = ROUND($yni_adep * 0.30,2);
				if($ni_adep > 0 ) { $itax = ROUND($ni_adep * 0.30,2); $nic = ROUND($ni_adep - $itax,2); } else { $itax = 0; $nic = '('.$mydb->formatNumber(abs($ni_adep),2).')'; }
				print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>PROVISION FOR INCOME TAX (30%)</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($itax,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($yitax,2).'</b></td></tr>';
				print '<tr><td colspan=4>&nbsp;</td></tr>';
				print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>NET INCOME AFTER TAX</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($nic,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$mydb->formatNumber($yni_adep-$yitax,2).'</b></td></tr>';
			}
		?>
	</table>
</body>
</html>