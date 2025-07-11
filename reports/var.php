<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	require_once "../handlers/_generics.php";

	$mydb = new _init;

	$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
	if($_GET['acct'] != '') { $f1 = " and a.acct = '$_GET[acct]' "; }
	$now = date("m/d/Y h:i a");
	$ly = $_GET['year'] - 1;
	$xdtf = "$_GET[year]-01-01";
	$lydtf = "$_GET[$ly]-01-01";
	list($looper) = $mydb->getArray("SELECT DATE_FORMAT(doc_date,'%m') FROM acctg_gl WHERE DATE_FORMAT(doc_date,'%Y') = '$_GET[year]' ORDER BY doc_date DESC LIMIT 1");
	if($_GET['type'] == 'Exp') { 
		$lbl = "Expenses"; $subLbl = "Budget";
		$acctGroup = "'12','13'";
		$val = "sum(debit-credit) as amt";
	} else { $lbl = "Revenue"; $subLbl = "Target"; $acctGroup = "'10','11'"; $val = "sum(credit-debit) as amt"; }
	
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link href="../ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" charset="utf8" src="../ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="../ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script>
		function modifyBudget (val,el,acct,year) {
			if(confirm("Are you sure you want to make changes on this account?") == true) {
				var budget = parent.stripComma(val);
				if(isNaN(budget) == true) {
					parent.sendErrorMessage("Invalid Value Specified!");
					document.getElementById(el).value = "0.00";
				} else {
					$.post("../src/sjerp.php", { mod: "modifyBudget", acct: acct, year: year, budget: budget, sid: Math.random() },function() { 
						if(confirm("Budget Successfully Modified. The system requires you to reload/refresh this report to display its modified values. Should you wish to do it now, click \"Ok\", otherwise click \"Cancel\" to do it on a later time.") == true) {
							window.location.reload();
						}
					});
				}
			}
		}
	
	</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="10" width="215">	
	<?php echo '<table width="100%">
		<tr>
			<td style="color:#000000; padding-top: 15px;">
				<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
			</td>
			<td width="40%" align=right>
				<span style="font-weight: bold; font-size: 9pt; color: #000000;">Variance Analysis ('.$lbl.') Report For CY '.$_GET[year].'</span></span>
			</td>
		</tr>
	</table>';
	?>
	<table width=100% style="border-collapse: collapse;">	
	<tr>
		<td class="gridHead" width="20%" align=left><b>Account Title</b></td>
		<td class="gridHead" align=right><b>Annual <?php echo $subLbl; ?></b></td>
		<?php
			for($i=1; $i <= $looper; $i++) {
				$month = str_pad($i,2,'0',STR_PAD_LEFT);
				list($myMonth) = $mydb->getArray("select date_format('$_GET[year]-$month-01','%b %Y');");
				echo "<td class=\"gridHead\" align=center><b>$myMonth</b></td>";
			}
			echo "<td class=\"gridHead\" align=center><b>Y-T-D ".$lbl."</b></td><td class=\"gridHead\" align=center><b>% to Annual $subLbl</b></td></tr>";
			$outQuery = $mydb->dbquery("select acct,b.description,$val from acctg_gl a left join acctg_accounts b on a.acct = b.acct_code where b.acct_grp in ($acctGroup) and date_format(doc_date,'%Y') = '$_GET[year]' group by acct;");
			while($outRow = $outQuery->fetch_array(MYSQLI_BOTH)) {
				list($ab,$moab) = $mydb->getArray("select budget, ROUND(budget/12,2) from budgets where acct = '$outRow[acct]' and `year` = '$_GET[year]';");
				echo "<tr><td class=\"grid2\">($outRow[acct]) $outRow[description]</td><td class=\"grid2\" align=right>".number_format($ab,2)."&nbsp;".$mydb->getPerformanceValueVsLastYear($val1=$ab,$val2=$ly_ab,$mod=$_GET['type'])."</td>";
				
				$mAmtGT = 0;
				for($i=0; $i <= ($looper-1); $i++) {
					list($dtf,$dt2) = $mydb->getArray("select date_add('$xdtf',INTERVAL $i MONTH),last_day(date_add('$xdtf',INTERVAL $i MONTH));");
					list($ly_dtf,$ly_dt2) = $mydb->getArray("select date_add('$xdtf',INTERVAL $i MONTH),last_day(date_add('$xdtf',INTERVAL $i MONTH));");					
					list($mAmt) = $mydb->getArray("select $val from acctg_gl where acct = '$outRow[acct]' and doc_date between '$dtf' and '$dt2';");
					list($ly_mAmt) = $mydb->getArray("select $val from acctg_gl where acct = '$outRow[acct]' and doc_date between '$ly_dtf' and '$ly_dt2';");
					
					if($mAmt > 0) {
						$moVariance = $mAmt - $moab;
						$moLyVariance = $mAmt  - $ly_amt;
						$moPct = ROUND(($moVariance/$mAmt) * 100);
						$moLyPct = ROUND(($moLyVariance/$mAmt) * 100);
						if($moVariance > 0) { $moTitle = "Up by &#8369;".number_format($moVariance,2)." or $moPct% vs. Budget"; } else { $moTitle = "Down by &#8369;".number_format(abs($moVariance),2)."' or ".abs($moPct)."% vs. Budget"; }
						if($moLyVariance > 0) { $moTitle2 = "Up by &#8369;".number_format($moLyVariance,2)." or $moLyPct% vs. Actual Last Year"; } else { $moTitle2 = "Down by &#8369;".number_format(abs($moLyVariance),2)."' or ".abs($moLyPct)."% vs. Actual Last Year"; }
						
						$dspAmt = "<span title=\"$moTitle. $moTitle2\">".$mydb->formatNumber($mAmt,2)."</span>"; 
					} else { $dspAmt = ''; }
					echo "<td class=\"grid2\" align=right>".$dspAmt."</td>";
					$mAmtGT += $mAmt;
				}
				
				$abGT += $ab;
				echo "<td class=\"grid2\" align=right><b>".$mydb->formatNumber($mAmtGT,2)."</b></td><td class=\"grid2\" align=center>".ROUND(($mAmtGT/$ab) * 100)."%</td></tr>";
				$gt+= $mAmtGT;
			}
			
			/* Grand Total */
			echo "<tr><td class=\"gridHead\" width=\"20%\" align=left><b>GRAND TOTAL</b></td>
				  <td class=\"gridHead\" align=right><b>".number_format($abGT,2)."</b></td>";
			
			for($i = 0; $i <= ($looper-1); $i++) {
				list($dtf,$dt2) = $mydb->getArray("select date_add('$xdtf',INTERVAL $i MONTH),last_day(date_add('$xdtf',INTERVAL $i MONTH));");
				list($colAmt) = $mydb->getArray("select $val from acctg_gl a left join acctg_accounts b on a.acct = b.acct_code where b.acct_grp in ($acctGroup) and doc_date between '$dtf' and '$dt2';");
				echo "<td class=\"gridHead\" align=right><b>".number_format($colAmt,2)."</b></td>";
				
			}
		
			echo "
				 <td class=\"gridHead\" align=right><b>".$mydb->formatNumber($gt,2)."</b></td><td class=\"gridHead\"></td>
			</tr>";
			
		?>
	</table>
</body>
</html>