<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	include("../includes/dbUSE.php");

	$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
	$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
	
	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		
		$ydtf = "$_GET[year]-01-01";
		$xydtf = "01/01/$_GET[year]";
		$dtf = "$_GET[year]-$_GET[month]-01";
		$xdtf = "$_GET[month]/01/$_GET[year]";
		list($dt2,$xdt2,$month) = getArray("select last_day('$dtf'), date_format(last_day('$dtf'),'%m/%d/%Y'), date_format('$dtf','%M');");
		$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_GET[branch]' and company = '$_SESSION[company]';");

		
		if($_GET['branch'] != '') { $fs = " and branch = '$_GET[branch]' "; $conso = 'N'; $branch = $bit['branch_name']; } else { $fs = ""; $conso = 'Y'; $branch = "CONSOLIDATED"; }
	
		$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
		$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");

	/* END OF SQL QUERIES */

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>$co[company_name] ERP System Ver. 1.0b</title>
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
				<span style="font-weight: bold; font-size: 9pt; color: #000000;">Income Statement for the Period '.$month.' '.$_GET['year'].'<br/>'.$branch.'</span>
			</td>
		</tr>
	</table>';
	?>
	<table cellspacing=0 cellpadding=0 border=0 width=100% cellspacing=5>	
		<tr bgcolor="#887e6e">
			<td width="70%" align=left class="gridHead" colspan=2>&nbsp;</td>
			<td width="15%" align=center class="gridHead"><b>FOR THE PERIOD</b></td>
			<td width="15%" align=center class="gridHead"><b>YEAR-TO-DATE</b></td>
		</tr>
		<tr><td align="left" colspan=4 style="border-bottom: 0.1mm solid black;"><b>NET SALES FOR THE PERIOD</b></td></tr>
		<?php
			/* SALES */
			$a = dbquery("SELECT a.acct, b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct = b.acct_code WHERE b.acct_grp = '4000' AND `year` = '$_GET[year]' and month <= '$_GET[month]' $fs GROUP BY a.acct order by b.description asc;");
			while($b = mysql_fetch_array($a)) {
				$bb = getArray("select sum(credit-debit) as amount from acctg_mo_tbalance where acct = '$b[acct]' and `month` = '$_GET[month]' and `year` = '$_GET[year]' $fs group by acct;");
				print '<tr><td align="left" colspan=2>'.$b['description'].'</td><td align="right"><a href="glschedule.php?dtf='.$xdtf.'&dt2='.$xdt2.'&acct='.$b['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.number_format($bb['amount'],2).'</a></td><td align="right"><a href="glschedule.php?dtf='.$xydtf.'&dt2='.$xdt2.'&acct='.$b['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.number_format($b['amount'],2).'</a></td></tr>';
				$ytSales+=$b['amount']; $tSales+=$bb['amount'];
			}
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>TOTAL NET SALES</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($tSales,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($ytSales,2).'</b></td></tr>';
			print '<tr><td colspan=8>&nbsp;</td></tr>';
			print '<tr><td align="left" colspan=4 style="border-bottom: 0.1mm solid black;"><b>COST OF GOODS SOLD</b></td></tr>';
			
			/* COGS */
			$c = dbquery("SELECT a.acct, b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '6000' AND `year` = '$_GET[year]' and month <= '$_GET[month]' $fs GROUP BY a.acct order by b.description asc;");
			while($d = mysql_fetch_array($c)) {
				$dd = getArray("select sum(debit-credit) as amount from acctg_mo_tbalance where acct = '$d[acct]' and `month` = '$_GET[month]' and `year` = '$_GET[year]' $fs group by acct;");
				print '<tr><td align="left" colspan=2>'.$d['description'].'</td><td align="right"><a href="glschedule.php?dtf='.$xdtf.'&dt2='.$xdt2.'&acct='.$d['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.number_format($dd['amount'],2).'</a></td><td align="right"><a href="glschedule.php?dtf='.$xydtf.'&dt2='.$xdt2.'&acct='.$d['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.number_format($d['amount'],2).'</a></td></tr>';
				$ytCOGS+=$d['amount']; $tCOGS+=$dd['amount'];
			}
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>TOTAL COST OF GOODS SOLD</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($tCOGS,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($ytCOGS,2).'</b></td></tr>';
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>GROSS PROFIT</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($tSales - $tCOGS,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($ytSales - $ytCOGS,2).'</b></td></tr>';
			print '<tr><td colspan=8>&nbsp;</td></tr>';
			print '<tr><td align="left" width=40% style="border-bottom: 0.1mm solid black;"><b>OPERATING EXPENSES</b></td><td width=30% style="border-bottom: 0.1mm solid black; font-weight: bold;">COST CENTER</td><td colspan=2 style="border-bottom: 0.1mm solid black;">&nbsp;</td></tr>';
			/* End of COGS */
		
			$e = dbquery("SELECT a.acct, a.cost_center, b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp IN ('7000','8000') AND `year` = '$_GET[year]' and month <= '$_GET[month]' GROUP BY a.acct, a.cost_center ORDER BY a.cost_center, b.description;");
			while($f = mysql_fetch_array($e)) {
				$ff = getArray("select sum(debit-credit) as amount from acctg_mo_tbalance where acct = '$f[acct]' and cost_center = '$f[cost_center]' and `month` = '$_GET[month]' and `year` = '$_GET[year]' $fs group by acct;");
				print '<tr><td align="left">'.$f['description'].'</td><td>'.$f['cost_center'].'</td><td align="right"><a href="glschedule.php?dtf='.$xdtf.'&dt2='.$xdt2.'&acct='.$f['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.number_format($ff['amount'],2).'</a></td><td align="right"><a href="glschedule.php?dtf='.$xydtf.'&dt2='.$xdt2.'&acct='.$f['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.number_format($f['amount'],2).'</a></td></tr>';
			}
			
			list($tExpenses) = getArray("select sum(debit-credit) from acctg_mo_tbalance a left join acctg_accounts b on a.acct = b.acct_code where  b.acct_grp in ('7000','8000') and `month` = '$_GET[month]' and `year` = '$_GET[year]' $fs;");
			list($ytExpenses) = getArray("select sum(debit-credit) from acctg_mo_tbalance a left join acctg_accounts b on a.acct = b.acct_code where  b.acct_grp in ('7000','8000') AND `year` = '$_GET[year]' and month <= '$_GET[month]' $fs;");
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>TOTAL OPERATING EXPENSES</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($tExpenses,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($ytExpenses,2).'</b></td></tr>';
			print '<tr><td colspan=4 height=8>&nbsp;</td></tr>';
			/* End of OPERATING EXPENSES */
		
			/* Net Operating Income or Loss */
			list($noi) = getArray("select sum(credit-debit) from acctg_mo_tbalance a left join acctg_accounts b on a.acct = b.acct_code where b.acct_grp in ('4000','6000','7000','8000') and `month` = '$_GET[month]' and `year` = '$_GET[year]' $fs;");
			list($ynoi) = getArray("select sum(credit-debit) from acctg_mo_tbalance a left join acctg_accounts b on a.acct = b.acct_code where b.acct_grp in ('4000','6000','7000','8000') AND `year` = '$_GET[year]' and month <= '$_GET[month]' $fs;");
			if($ynoi < 0) { $yion = '('.number_format(abs($ynoi),2).')'; } else  { $yion = number_format($ynoi,2); }
			if($noi < 0) { $lbl0 = "NET OPERATING LOSS"; $ion = '('.number_format(abs($noi),2).')'; } else { $lbl0 = "NET OPERATING INCOME"; $ion = number_format($noi,2); }
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>'.$lbl0.'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$ion.'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$yion.'</b></td></tr>';
		
			/* Other Income */
			list($isOthIncome) = getArray("select count(*) from acctg_mo_tbalance where acct in (select acct_code from acctg_accounts where acct_grp = '5000') AND `year` = '$_GET[year]' and month <= '$_GET[month]' $fs;");
			if($isOthIncome > 0) {
				print '<tr><td align="left" colspan=4 style="border-bottom: 0.1mm solid black;"><b>ADD: OTHER INCOME</b></td></tr>';
				$g = dbquery("SELECT a.acct, b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE  b.acct_grp = '5000' AND `year` = '$_GET[year]' and month <= '$_GET[month]' $fs GROUP BY a.acct order by b.description asc;");
				while($h = mysql_fetch_array($g)) {
					$hh = getArray("select sum(credit-debit) as amount from acctg_mo_tbalance where acct = '$h[acct]' and `month` = '$_GET[month]' and `year` = '$_GET[year]' $fs group by acct;");
					print '<tr><td align="left" colspan=2>'.$h['description'].'</td><td align="right"><a href="glschedule.php?dtf='.$xdtf.'&dt2='.$xdt2.'&acct='.$h['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.number_format($hh['amount'],2).'</a></td><td align="right"><a href="glschedule.php?dtf='.$xydtf.'&dt2='.$xdt2.'&acct='.$h['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.number_format($h['amount'],2).'</a></td></tr>';
					$ytOtherIncome+=$h['amount']; $tOtherIncome+=$hh['amount'];
				}
				print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>TOTAL OTHER INCOME</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($tOtherIncome,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($ytOtherIncome,2).'</b></td></tr>';
			}
			
			$CurNetOperating = $noi + $tOtherIncome;
			$YtdNetOperating = $ynoi + $ytOtherIncome;
			if($YtdNetOperating < 0) { $xyion = '('.number_format(abs($YtdNetOperating),2).')'; } else  { $xyion = number_format($YtdNetOperating,2); }
			if($CurNetOperating < 0) { $xion = '('.number_format(abs($CurNetOperating),2).')'; } else { $xion = number_format($CurNetOperating,2); }
			if($YtdNetOperating < 0 and  $CurNetOperating < 0 ) { $lblS = "NET LOSS BEFORE DEPRECIATION"; } else { $lblS = "NET INCOME or LOSS BEFORE DEPRECIATION"; }
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>'.$lblS.'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$xion.'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$xyion.'</b></td></tr>';
	
			/* DEPRECIATION EXPENSE */
			print '<tr><td colspan=4 height=8>&nbsp;</td></tr>';
			print '<tr><td align="left" style="border-bottom: 0.1mm solid black; font-weight: bold;">DEPRECIATION EXPENSES</td><td align="left" style="border-bottom: 0.1mm solid black; font-weight: bold;">COST CENTER</td><td align="left" style="border-bottom: 0.1mm solid black;" colspan=2>&nbsp;</td></tr>';

			$depQuery = dbquery("SELECT a.acct, a.cost_center, b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE  b.acct_grp in ('1650') AND `year` = '$_GET[year]' and month <= '$_GET[month]' $fs GROUP BY a.acct, a.cost_center order by a.cost_center, b.description;");
			while($depRow = mysql_fetch_array($depQuery)) {
				list($curDep) = getArray("select sum(debit-credit) as amount from acctg_mo_tbalance where acct = '$depRow[acct]' and cost_center = '$depRow[cost_center]' and `month` = '$_GET[month]' and `year` = '$_GET[year]' $fs group by acct;");
				print '<tr><td align="left">'.$depRow['description'].'</td><td>'.$depRow['cost_center'].'</td><td align="right"><a href="glschedule.php?dtf='.$xdtf.'&dt2='.$xdt2.'&acct='.$depRow['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.number_format($curDep,2).'</a></td><td align="right"><a href="glschedule.php?dtf='.$xydtf.'&dt2='.$xdt2.'&acct='.$depRow['acct'].'&conso='.$conso.'" style="text-decoration: none; color: black;" target="_blank">'.number_format($depRow['amount'],2).'</a></td></tr>';
				$yDepGT+=$depRow['amount']; $curDepGT+=$curDep;
			}
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>TOTAL DEPRECIATION EXPENSES</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($curDepGT,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($yDepGT,2).'</b></td></tr>';
			print '<tr><td colspan=4 height=8></td></tr>';
			
			$ni_adep = $noi + $tOtherIncome - $curDepGT;
			$yni_adep = $ynoi + $ytOtherIncome - $yDepGT;
			if($yni_adep < 0) { $niadep_yion = '('.number_format(abs($yni_adep),2).')'; } else  { $niadep_yion = number_format($yni_adep,2); }
			if($ni_adep < 0) { $niadep_ion = '('.number_format(abs($ni_adep),2).')'; } else { $niadep_ion = number_format($ni_adep,2); }
			if($ni_adep < 0 and $yni_adep < 0) { $lblT = "TOTAL NET LOSS"; } else { $lblT = "TOTAL NET INCOME or LOSS"; }
			print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>'.$lblT.'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$niadep_ion.'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.$niadep_yion.'</b></td></tr>';

			if($yni_adep > 0) {
				$yitax = ROUND($yni_adep * 0.30,2);
				if($ni_adep > 0 ) { $itax = ROUND($ni_adep * 0.30,2); $nic = ROUND($ni_adep - $itax,2); } else { $itax = 0; $nic = '('.number_format(abs($ni_adep),2).')'; }
				print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>PROVISION FOR INCOME TAX (30%)</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($itax,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($yitax,2).'</b></td></tr>';
				print '<tr><td colspan=4>&nbsp;</td></tr>';
				print '<tr><td align="left" style="border-top: 0.1mm solid black;" colspan=2><b>NET INCOME AFTER TAX</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($nic,2).'</b></td><td align="right" style="border-top: 0.1mm solid black;"><b>'.number_format($yni_adep-$yitax,2).'</b></td></tr>';
			}
		?>
	</table>
</body>
</html>