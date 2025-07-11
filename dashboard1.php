<?php
	session_start();
	ini_set("max_execution_time",0);
	include("includes/dbUSE.php");
	$now = date("m/d/Y h:i a");
	$dtf = date("$_GET[year]-$_GET[month]-01");

	$thisMonth = $_GET['month'];
	list($lastMonth,$lastYrIntMonth) = getArray("SELECT date_format(LAST_DAY(DATE_SUB('".$dtf."', INTERVAL 1 MONTH)),'%m'),date_format(LAST_DAY(DATE_SUB('".$dtf."', INTERVAL 1 MONTH)),'%Y');");
	
	$thisYear = $_GET['year']; 
	$lastYear = $thisYear-1;
	
	/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
	$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
	
	/* Year Comparison */
	list($ytd) = getArray("SELECT ROUND(SUM(credit-debit),2) FROM acctg_mo_tbalance a left join acctg_accounts b on a.acct = b.acct_code WHERE `year` = '$thisYear' and `month` <= '$thisMonth' AND b.acct_grp = '4000' AND a.acct NOT IN ('4013','4099');");
	list($lytd) = getArray("SELECT ROUND(SUM(credit-debit),2) FROM acctg_mo_tbalance a left join acctg_accounts b on a.acct = b.acct_code WHERE `year` = '$lastYear' and `month` <= '$thisMonth' AND b.acct_grp = '4000' AND a.acct NOT IN ('4013','4099');");
	$yvariance = ROUND((($ytd - $lytd) / $ytd) * 100);

	/* Month Comparison */
	list($cm) = getArray("SELECT ROUND(SUM(credit-debit),2) FROM acctg_mo_tbalance a left join acctg_accounts b on a.acct = b.acct_code WHERE `year` = '$thisYear' and `month` = '$thisMonth' AND b.acct_grp = '4000' AND a.acct NOT IN ('4013','4099');");
	list($lm) = getArray("SELECT ROUND(SUM(credit-debit),2) FROM acctg_mo_tbalance a left join acctg_accounts b on a.acct = b.acct_code WHERE `year` = '$lastYrIntMonth' and `month` = '$lastMonth' AND b.acct_grp = '4000' AND a.acct NOT IN ('4013','4099');");
	list($ym) = getArray("SELECT ROUND(SUM(credit-debit),2) FROM acctg_mo_tbalance a left join acctg_accounts b on a.acct = b.acct_code WHERE `year` = '$lastYear' and `month` = '$thisMonth' AND b.acct_grp = '4000' AND a.acct NOT IN ('4013','4099');");
	$mvariance = ROUND((($cm - $lm) / $cm) * 100);
	$ymvariance = ROUND((($cm - $ym) / $cm) * 100);

	/* END OF SQL QUERIES */

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>$co[company_name] ERP System Ver. 1.0b</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#ffffff" topmargin="5">	
	<table width=100% cellspacing=0 cellpadding = 0>
		<tr>
			<td width=30% valign=top>
				<table width=100%>
					<tr>
						<td width=100%>
							<table width=100% style="border: 1px solid #cdcdcd;">
								<tr><td class="dgridhead" width=100%>YTD SALES</td></tr>
								<tr><td align=center style="padding-top: 25px; padding-bottom: 2px; font-size: 50px; font-weight: bold; color: #4a4a4a;">&#8369; <?php echo convert2Short(ROUND($ytd)); ?></td></tr>
								<tr><td align=center style="padding-bottom: 25px; font-size: 12px; color: #4a4a4a;"><?php if($yvariance > 0) { echo "<img src='images/icons/uptrend.png' width=14 height=14 align=absmiddle />&nbsp;Up by $yvariance% (".convert2Short(ROUND($lytd)).") vs. Previous Year of Same Period"; } else { echo "<img src='images/icons/downtrend.png' width=12 height=12 align=absmiddle />&nbsp;Down by ".abs($yvariance)."% (".convert2Short(ROUND($lytd)).") vs. Previous Year of Same Period"; }?></td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width=100%>
							<table width=100% style="border: 1px solid #cdcdcd;">
								<tr><td class="dgridhead" width=100%>Net Sales (Current Month)</td></tr>
								<tr><td align=center style="padding-top: 25px; padding-bottom: 2px; font-size: 50px; font-weight: bold; color: #4a4a4a;">&#8369; <?php echo convert2Short($cm); ?></td></tr>
								<tr><td align=center style="font-size: 12px; color: #4a4a4a;"><?php if($mvariance > 0) { echo "<img src='images/icons/uptrend.png' width=14 height=14 align=absmiddle />&nbsp;Up by $mvariance% (".convert2Short(ROUND($lm)).") vs. Previous Month"; } else { echo "<img src='images/icons/downtrend.png' width=12 height=12 align=absmiddle />&nbsp;Down by ".abs($mvariance)."% (".convert2Short(ROUND($lm)).") vs. Previous Month"; }?></td></tr>
								<tr><td align=center style="padding-bottom: 20px; font-size: 12px; color: #4a4a4a;"><?php if($ymvariance > 0) { echo "<img src='images/icons/uptrend.png' width=14 height=14 align=absmiddle />&nbsp;Up by $ymvariance% (".convert2Short(ROUND($ym)).") vs. Previous Year of Same Month"; } else { echo "<img src='images/icons/downtrend.png' width=12 height=12 align=absmiddle />&nbsp;Down by ".abs($ymvariance)."% (".convert2Short(ROUND($ym)).") vs. Previous Year of Same Month"; }?></td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width=100%>
							<table width=100% style="border: 1px solid #cdcdcd;">
								<tr><td class="dgridhead" width=100% colspan=2>TOP 20 SELLING PRODUCTS</td></tr>
								<?php
									$z = dbquery("SELECT `code`, b.description, ROUND(SUM(amount),2) AS amount FROM ijournal a INNER JOIN products_master b ON a.code = b.item_code WHERE `year` = '$thisYear' AND `month` = '$thisMonth' AND sold > 0 GROUP BY item_code ORDER BY SUM(amount) DESC LIMIT 20;");
									$i = 1;
									while($y = mysql_fetch_array($z)) {
										if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
										print "<tr bgcolor=\"$bgC\"><td class=grid>($i) $y[1]</td><td class=grid align=right>&#8369; ".number_format($y[2],2)."</td></tr>";
										$i++;
									}
								?>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<td width=70% valign=top>
				<table width=100% cellspacing=1>
					<tr><td colspan=2 style="border: 1px solid #cdcdcd;" width=100%>
							<table width=100%>
								<tr><td width=100% class="dgridhead" width=100%>Consolidate Sales Contribution Per Product Group (For the Period)</td></tr>
								<tr><td width=100% align=center><?php  echo "<img src=\"reports/mo-salescontrib-graph.php?month=$_GET[month]&year=$_GET[year]&setSize=Y\" width = 80% height=50% />"; ?></td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="border: 1px solid #cdcdcd;" width=50% valign=top>
							<table>
								<tr><td class="dgridhead">Overall Sales Performance for the Calendar Year <?php echo $thisYear; ?></td></tr>
								<tr><td><?php echo "<img src=\"reports/mo-sperf-graph.php?year=$thisYear&setSize=Y\" />"; ?></td></tr>
							</table>
							
						</td>
						<td style="border: 1px solid #cdcdcd;" width=50% valign=top>
							<table width=100% style="border: 1px solid #cdcdcd;">
								<tr><td class="dgridhead" width=100% colspan=2>TOP SALES CONTRIBUTOR</td></tr>
								<?php
									$z = dbquery("SELECT c.branch_name, ROUND(SUM(credit-debit),2) AS amount FROM acctg_mo_tbalance a LEFT JOIN acctg_accounts b ON a.acct = b.acct_code LEFT JOIN options_branches c ON a.branch = c.branch_code WHERE b.acct_grp IN ('4000') AND `month` = '$thisMonth' AND a.acct NOT IN ('4013') AND `year` = '$thisYear' AND c.company = '$_SESSION[company]' GROUP BY branch LIMIT 10;;");
									$i = 1;
									while($y = mysql_fetch_array($z)) {
										if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
										echo "<tr bgcolor=\"$bgC\"><td class=grid>($i) $y[0]</td><td class=grid align=right style=\"padding-right: 10px;\">&#8369; ".number_format($y[1],2)."</td></tr>";
										$i++;
									}
									if($i < 10) {
										for($i; $i<=11; $i++) { 
											if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; } 
											print "<tr bgcolor=\"$bgC\"><td class=grid colspan=2>&nbsp;</td></tr>"; 
										}
									}
								?>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
<?php @mysql_close($con); ?>