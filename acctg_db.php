<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	include("handlers/_generics.php");
	$con = new _init;

	$now = date("m/d/Y h:i a");
	$dtf = "$_GET[month]/01/$_GET[year]";
	list($dt2,$month) = $con->getArray("select date_format(last_day('$_GET[year]-$_GET[month]-01'),'%m/%d/%Y'), date_format('$_GET[year]-$_GET[month]-01','Month of %M %Y');");

	list($lm_dtf) = $con->getArray("select date_sub('".$con->formatDate($dtf)."', INTERVAL 1 MONTH);"); list($ly_dtf) = $con->getArray("select date_sub('".$con->formatDate($dtf)."', INTERVAL 1 YEAR);");
	list($lm_dt2) = $con->getArray("select date_sub('".$con->formatDate($dt2)."', INTERVAL 1 MONTH);"); list($ly_dt2) = $con->getArray("select date_sub('".$con->formatDate($dt2)."', INTERVAL 1 YEAR);");
	$year = $_GET[year];
	$ly = $year - 1; 
	$ly_dt2 = date("m/d/$ly");

	/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	
	/* Year Comparison */
	list($ytd) = $con->getArray("select sum(credit-debit) from acctg_gl a inner join acctg_accounts b ON a.acct = b.acct_code where doc_date between '$year-01-01' and '".$con->formatDate($dt2)."' AND a.branch = '$_SESSION[branchid]' and b.acct_grp in ('9','10');");
	list($lytd) = $con->getArray("select sum(credit-debit) from acctg_gl a inner join acctg_accounts b on a.acct = b.acct_code where doc_date between '$ly-01-01' and '".$con->formatDate($ly_dt2)."' AND a.branch = '$_SESSION[branchid]' and b.acct_grp in ('9','10');");
	$yvariance = ROUND((($ytd - $lytd) / $ytd) * 100);

	/* Month Comparison */
	list($cm) = $con->getArray("select sum(credit-debit) from acctg_gl a inner join acctg_accounts b ON a.acct = b.acct_code where doc_date between '".$con->formatDate($dtf)."' and '".$con->formatDate($dt2)."' and b.acct_grp in ('9','10') AND a.branch = '$_SESSION[branchid]';");
	list($lm) = $con->getArray("select sum(credit-debit) from acctg_gl a inner join acctg_accounts b ON a.acct = b.acct_code where doc_date between '$lm_dtf' and '$lm_dt2' and b.acct_grp in ('9','10') AND a.branch = '$_SESSION[branchid]';");
	list($ym) = $con->getArray("select sum(credit-debit) from acctg_gl a inner join acctg_accounts b ON a.acct = b.acct_code where doc_date between '$ly_dtf' and '$ly_dt2' and b.acct_grp in ('9','10') AND a.branch = '$_SESSION[branchid]';");
	
	$mvariance = ROUND((($cm - $lm) / $cm) * 100);
	$ymvariance = ROUND((($cm - $ym) / $cm) * 100);


	/* YTD NET INCOME */
	list($ytd_i) = $con->getArray("SELECT SUM(credit-debit) FROM acctg_gl a INNER JOIN acctg_accounts b ON a.acct = b.acct_code WHERE doc_date BETWEEN '$_GET[year]-01-01' AND '".$con->formatDate($dt2)."' AND a.branch = '$_SESSION[branchid]' AND b.acct_grp IN ('9','10','11','12','13','14');");
	list($lytd_i) = $con->getArray("SELECT SUM(credit-debit) FROM acctg_gl a INNER JOIN acctg_accounts b ON a.acct = b.acct_code WHERE doc_date BETWEEN '$ly-01-01' AND '".$con->formatDate($ly_dt2)."' AND a.branch = '$_SESSION[branchid]' AND b.acct_grp IN ('9','10','11','12','13','14');");
	$yvariance_i = ROUND((($ytd_i - $lytd_i) / $ytd) * 100);
	/* END OF SQL QUERIES */

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
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
								<tr><td class="dgridhead" width=100%>Y-T-D Net Income Ending <?php echo $month; ?></td></tr>
								<tr><td align=center style="padding-top: 25px; padding-bottom: 2px; font-size: 50px; font-weight: bold; color: #4a4a4a;">&#8369; <?php echo $con->convert2Short($ytd_i); ?></td></tr>
								<tr><td align=center style="padding-bottom: 25px; font-size: 12px; color: #4a4a4a;"><?php if($yvariance_i > 0) { echo "<img src='images/icons/uptrend.png' width=14 height=14 align=absmiddle />&nbsp;Up by $yvariance% vs. Last Year of Same Period (&#8369;".number_format($lytd,2).")"; } else { echo "<img src='images/icons/downtrend.png' width=12 height=12 align=absmiddle />&nbsp;Down by ".abs($yvariance_i)."% vs. Last Year of Same Period"; }?></td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width=100%>
							<table width=100% style="border: 1px solid #cdcdcd;">
								<tr><td class="dgridhead" width=100%>Y-T-D Revenue Ending <?php echo $month; ?></td></tr>
								<tr><td align=center style="padding-top: 25px; padding-bottom: 2px; font-size: 50px; font-weight: bold; color: #4a4a4a;">&#8369; <?php echo $con->convert2Short($ytd); ?></td></tr>
								<tr><td align=center style="padding-bottom: 25px; font-size: 12px; color: #4a4a4a;"><?php if($yvariance > 0) { echo "<img src='images/icons/uptrend.png' width=14 height=14 align=absmiddle />&nbsp;Up by $yvariance% vs. Last Year of Same Period (&#8369;".number_format($lytd,2).")"; } else { echo "<img src='images/icons/downtrend.png' width=12 height=12 align=absmiddle />&nbsp;Down by ".abs($yvariance)."% vs. Last Year of Same Period"; }?></td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width=100%>
							<table width=100% style="border: 1px solid #cdcdcd;">
								<tr><td class="dgridhead" width=100%>Total Revenue (<?php echo $month; ?>)</td></tr>
								<tr><td align=center style="padding-top: 25px; padding-bottom: 2px; font-size: 50px; font-weight: bold; color: #4a4a4a;">&#8369; <?php echo $con->convert2Short($cm); ?></td></tr>
								<tr><td align=center style="font-size: 12px; color: #4a4a4a;"><?php if($mvariance > 0) { echo "<img src='images/icons/uptrend.png' width=14 height=14 align=absmiddle />&nbsp;Up by $mvariance% vs. Last Month (&#8369;".number_format($lm,2).")"; } else { echo "<img src='images/icons/downtrend.png' width=12 height=12 align=absmiddle />&nbsp;Down by ".abs($mvariance)."% vs. Last Month (&#8369;".number_format($lm,2).")"; }?></td></tr>
								<tr><td align=center style="padding-bottom: 20px; font-size: 12px; color: #4a4a4a;"><?php if($ymvariance > 0) { echo "<img src='images/icons/uptrend.png' width=14 height=14 align=absmiddle />&nbsp;Up by $ymvariance% vs. Last Year of Same Month (&#8369;".number_format($ym,2).")"; } else { echo "<img src='images/icons/downtrend.png' width=12 height=12 align=absmiddle />&nbsp;Down by ".abs($ymvariance)."% vs. Last Year of Same Month (&#8369;".number_format($lm,2).")"; }?></td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width=100%>
							<table width=100% style="border: 1px solid #cdcdcd;">
								<tr><td class="dgridhead" width=100% colspan=2>Top 10 Expenses (<?php echo $month; ?>)</td></tr>
								<?php
									$z = $con->dbquery("SELECT * FROM (SELECT CONCAT('(',a.acct,') ',b.description), ROUND(SUM(debit-credit),2) AS amt FROM acctg_gl a INNER JOIN acctg_accounts b ON a.acct = b.acct_code WHERE doc_date BETWEEN '".$con->formatDate($dtf)."' AND '".$con->formatDate($dt2)."' AND b.acct_grp in ('12') GROUP BY a.acct) a ORDER BY amt DESC LIMIT 10;");
									$i = 1;
									while($y = $z->fetch_array()) {
										if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
										echo "<tr bgcolor=\"$bgC\"><td class=grid width=70% valign=top>$i. $y[0]</td><td class=grid valign=top align=right style='padding-right: 5px;'>&#8369; ".number_format($y[1],2)."</td></tr>";
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
								<tr><td width=100% class="dgridhead" width=100%>Comparative Yearly Revenue (vs. Last Year)</td></tr>
								<tr><td width=100% align=center><?php echo "<img src=\"reports/graphs/comparativerevenue-graph.php?dtf=$dtf&dt2=$dt2\" />"; ?></td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="border: 1px solid #cdcdcd;" width=50% valign=top>
							<table width=100% style="border: 1px solid #cdcdcd;">
								<tr><td class="dgridhead" width=100%>Y-T-D Revenue Breakdown Ending <?php echo $month; ?></td></tr>
								<tr><td><?php echo "<img src=\"reports/graphs/revenuecontrib-graph.php?dtf=01/01/$_GET[year]&dt2=$dt2\" width=95% height=95% />"; ?></td></tr>
							</table>
						</td>
						<td style="border: 1px solid #cdcdcd;" width=50% valign=top>
							<table>
								<tr><td class="dgridhead">Revenue Breakdown for the <?php echo $month; ?></td></tr>
								<tr><td><?php echo "<img src=\"reports/graphs/revenuecontrib-graph.php?dtf=$dtf&dt2=$dt2\" width=95% height=95% />"; ?></td></tr>
							</table>
						</td>
					</tr>
					<tr><td colspan=2 style="border: 1px solid #cdcdcd;" width=100%>
							<table width=100%>
								<tr><td width=100% class="dgridhead" width=100%>Comparative Consolidated Expenses for Calendar Year <?php echo $_GET[year]; ?></td></tr>
								<tr><td width=100% align=center><?php echo "<img src=\"reports/graphs/comparative-expenses.php?dtf=$dtf&dt2=$dt2\" />"; ?></td></tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>