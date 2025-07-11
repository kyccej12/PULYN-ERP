<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	//include("../includes/dbUSE.php");

	require_once "../handlers/initDB.php";
	$db = new myDB;


	$co = $db->getArray("select * from companies where company_id = '$_SESSION[company]';");
	if($_GET['acct'] != '') { $f1 = " and a.acct = '$_GET[acct]' "; }
	$now = date("m/d/Y h:i a");
	$ly = $_GET['year'] - 1;
	$xdtf = "$_GET[year]-01-01";
	$lydtf = "$_GET[$ly]-01-01";
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
	<?php echo '<table width="90%" align=center>
		<tr>
			<td width="100%" align=right>
				<span style="font-weight: bold; font-size: 9pt; color: #000000;">'.$lbl.' ' . $subLbl . ' For CY '.$_GET[year].'</span></span>
			</td>
		</tr>
	</table>';
	?>
	<table width=90% style="border-collapse: collapse;" align=center>	
		<tr>
			<td class="gridHead" width="60%" align=left><b>Account Title</b></td>
			<td class="gridHead" width="20%" align=right><b><?php echo $_GET['year']; ?> Budget</b></td>
			<td class="gridHead" width="20%" align=right><b><?php echo $ly; ?> Budget</b></td>
		</tr>
		<?php
			$outQuery = $db->dbquery("select acct_code as acct,description from acctg_accounts where acct_grp in ($acctGroup) order by acct_code;");
			while($outRow = $outQuery->fetch_array(MYSQLI_BOTH)) {
				list($ab) = $db->getArray("select budget from budgets where acct = '$outRow[acct]' and `year` = '$_GET[year]';");
				list($yz) = $db->getArray("select budget from budgets where acct = '$outRow[acct]' and `year` = '$ly';");
				echo "<tr>
							<td class=\"grid2\">($outRow[acct]) $outRow[description]</td>
							<td class=\"grid2\" align=right><input type=text id = \"inp$outRow[acct]\" style=\"border: none; width: 80%; text-align: right; font-weight: bold;\" value = \"".number_format($ab,2)."\" onchange = \"javascript: modifyBudget(this.value,this.id,'$outRow[acct]','$_GET[year]');\"></td>
							<td class=\"grid2\" align=right><b>".number_format($yz,2)."</b></td>
						</tr>";
				$gt+=$ab; $lgt+=$yz;
			}
			
			/* Grand Total */
			echo "<tr><td class=\"gridHead\" align=left><b>GRAND TOTAL</b></td>
				  <td class=\"gridHead\" align=right><b>".number_format($gt,2)."</b></td>
				  <td class=\"gridHead\" align=right><b>".number_format($lgt,2)."</b></td>
				</tr>";
		?>
	</table>
</body>
</html>