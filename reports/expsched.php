<?php
	//ini_set("display_errors","On");
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	require_once "../handlers/_generics.php";

	$mydb = new _init;


	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
		
		$dtf = "$_GET[year]-$_GET[month]-01";
		$ydtf = "$_GET[year]-01-01";
		$fs1 = '';
		list($dt2,$period) = $mydb->getArray("select last_day('$dtf'), date_format('$dtf','%M %Y');");
		$Title = "Schedule of Expense";
		
	/* END OF SQL QUERIES */

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
				<span style="font-size: 9pt;"><b>'.$co['company_name'].'</b></span><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
			</td>
			<td width="40%" align=right>
				<span style="font-weight: bold; font-size: 9pt; color: #000000;">'.$Title.'</span><br /><span style="font-size: 8pt; font-style: italic;">Covered Period: '.$period.'</span>
			</td>
		</tr>
	</table>
	<table width=100%><tr><td style="border-bottom: 1px solid black;">&nbsp;</td>';
	?>
	<table width=100% cellpadding=0 cellspacing =0 align=center>
		<?php
		
			echo '<tr >
			        <td class="gridHead">EXPENSE ACCOUNT</td>';
			
				$uLoop = $mydb->dbquery("SELECT 'PCC' AS costcenter UNION ALL SELECT costcenter FROM options_costcenter ORDER BY costcenter;");
				while($cc = $uLoop->fetch_array(MYSQLI_BOTH)) {
					echo '<td class="gridHead" align=right>'.$cc[0].'</td>';
				}
					
			echo '<td class="gridHead" align=right with=10%>TOTAL EXPENSE<br/>FOR THE PERIOD</td>
					<td class="gridHead" align=right with=10%>BUDGET</td>
					<td class="gridHead" align=right with=10%>VARIANCE<br/>(AMOUNT)</td>
					<td class="gridHead" align=right with=10%>VARIANCE<br/>(%)</td>
					<td class="gridHead" align=right with=10%>YTD EXPENSES</td>
					<td class="gridHead" align=right with=10%>YTD %<br/>TO ANNUAL</td>
					<td class="gridHead" align=right with=10%>ANNUAL BUDGET</td>
			</tr>';
		
		
			$c = $mydb->dbquery("SELECT DISTINCT acct, b.description FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct = b.acct_code WHERE doc_date between '$dtf' and '$dt2' and b.acct_grp in ('12','13') ORDER BY a.acct $fs1");
			$i = 0; $aeGT = 0; $budgetGT = 0; $annualGT = 0; $ytdeGT = 0; $varianceGT = 0; 
			while(list($acct,$description) = $c->fetch_array(MYSQLI_BOTH)) {
				
				list($ae) = $mydb->getArray("select sum(debit-credit) from acctg_gl where doc_date between '$dtf' and '$dt2' and acct = '$acct' $fs1");
				list($ytde) = $mydb->getArray("select sum(debit-credit) from acctg_gl where doc_date between '$ydtf' and '$dt2' and acct = '$acct' $fs1");
				list($budget,$annual) = $mydb->getArray("select ROUND(budget/12,2), budget from budgets where year = '$_GET[year]' and acct = '$acct';");
				if($budget == '') { $budget = 0; }
				$variance = $ae-$budget;
				if($variance < 0) { $var = '('.number_format(($variance*-1),2).')'; } else { $var = number_format($variance,2); }
				
				$acctPCT = ROUND(($variance/$ae) * 100,2);
				$ytdePCT = ROUND(($ytde/$annual) * 100,2);
				
				echo '<tr bgcolor="'.$mydb->initBackground($i).'">
						<td class=grid>('.$acct.') '.$description.'</td>';
						$uLoop = $mydb->dbquery("SELECT '' as unitcode,'PCC' as costcenter UNION ALL SELECT unitcode,costcenter from options_costcenter order by costcenter;");
						while($cc = $uLoop->fetch_array(MYSQLI_BOTH)) {
							list($ccExpense) = $mydb->getArray("SELECT SUM(debit-credit) AS amount FROM acctg_gl WHERE acct = '$acct' AND doc_date BETWEEN '$dtf' AND '$dt2' and cost_center = '$cc[0]' GROUP BY cost_center;");
							
							echo '<td class="grid" align=right>'.number_format($ccExpense,2).'</td>';
						}
						
				  echo '<td class=grid align=right>'.number_format($ae,2).'</td>
						<td class=grid align=right>'.number_format($budget,2).'</td>
						<td class=grid align=right>'.$var.'</td>
						<td class=grid align=right>'.$acctPCT.'</td>
						<td class=grid align=right>'.number_format($ytde,2).'</td>
						<td class=grid align=right>'.number_format($ytdePCT,2).'</td>
						<td class=grid align=right>'.number_format($annual,2).'</td>
					  </tr>'; $aeGT+=$ae; $budgetGT+=$budget; $annualGT+=$annual; $ytdeGT+=$ytde; $varianceGT += $variance; $i++;
			}
			
				echo '<tr>
			        <td class="gridHead">GRAND TOTAL</td>';
					$uLoop = $mydb->dbquery("SELECT '' as unitcode, 'PCC' as costcenter UNION ALL SELECT unitcode,costcenter from options_costcenter order by costcenter;");
					while($cc = $uLoop->fetch_array(MYSQLI_BOTH)) {
						list($ccExpenseGT) = $mydb->getArray("SELECT SUM(debit-credit) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b on a.acct = b.acct_code WHERE b.acct_grp in ('12','13') AND doc_date BETWEEN '$dtf' AND '$dt2' and cost_center = '$cc[0]' GROUP BY cost_center;");
						echo '<td class="gridHead" align=right>'.number_format($ccExpenseGT,2).'</td>';
					}	
					
					
			  echo '<td class="gridHead" align=right with=10%>'.number_format($aeGT,2).'</td>
					<td class="gridHead" align=right with=10%>'.number_format($budgetGT,2).'</td>
					<td class="gridHead" align=right with=10%>'.number_format($varianceGT,2).'</td>
					<td class="gridHead" align=right with=10%></td>
					<td class="gridHead" align=right with=10%>'.number_format($ytdeGT,2).'</td>
					<td class="gridHead" align=right with=10%></td>
					<td class="gridHead" align=right with=10%>'.number_format($annualGT,2).'</td>
			</tr>';
			
		?>
	</table>
</body>
</html>