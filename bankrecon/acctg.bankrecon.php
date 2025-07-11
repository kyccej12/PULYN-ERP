<?php
	session_start();
	$branch = $_SESSION['branchid'];
	
	
	if(isset($_GET['dtf'])) { $dtf = $_GET['dtf']; } else { $dtf = date("m/d/Y"); }
	if(isset($_GET['dt2'])) { $dt2 = $_GET['dt2']; } else { $dt2 = date("m/d/Y"); }
	$acct = $_GET['acct_code'];
?>
<html>
<head>
	<link href="css/style.css" rel="stylesheet" type="text/css" />
	<link type="text/css" href="lib/jquery/themes/base/ui.all.css" rel="stylesheet" />
	<script type="text/javascript" src="lib/jquery/jquery-1.3.2.js"></script>
	<script type="text/javascript" src="lib/jquery/ui/ui.core.js"></script>
	<script type="text/javascript" src="lib/jquery/ui/ui.datepicker.js"></script>
	<script language="javascript" src="date.js"></script>
	<script language="javascript">
		function convert_date(){
			var acct_code = document.getElementById("acct_code").value;
			var dtf = parseDate(document.getElementById("tmp_dtf").value);
			var dt2 = parseDate(document.getElementById("tmp_dt2").value);
			if(acct_code == "") {
				alert("Please specify \"Account Code\"!");
				return false;
			} else {
				if(dtf == null || dt2 == null){
					alert('Date doesn\'t match any recognized formats!');
					return false;
				} else {
					document.getElementById("dtf").value = formatDate(dtf,'yyyy-MM-dd');
					document.getElementById("dt2").value = formatDate(dt2,'yyyy-MM-dd');
					document.gl_sched.submit();
				}
			}
		}
		function pass_to_sub() {
			var dtf = document.getElementById("tmp_dtf").value;
			var dt2 = document.getElementById("tmp_dt2").value;
			parent.journal_sub_acct(dtf,dt2,'1');
		}
		
	</script>
	<script type="text/javascript">
	$(function() {
		$("#tmp_date").datepicker();
	});
</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
	<form name="gl_sched" method=post action="acctg.glsched.php" target="_blank">
		<input type=hidden name="branch" id="branch" value="<?php echo $branch ?>">
		<input type=hidden name="dtf" id="dtf">
		<input type=hidden name="dt2" id="dt2">
	<table align=center width=100% border=0 cellspacing=0 cellpadding=0 style="border-bottom:2px solid black; background-color:#595959; font-weight:bold; color:#ffffff;">
	<tr><td height=2></td></tr>
	<tr>
		<td align="left" style="font-weight:bold">&nbsp;&nbsp;Bank Reconciliation</td>
		<td align=right width="6%" style="padding-right: 5px;" valign=middle>
			<a href="javascript: parent.close_div();"><img src="images/close.png" border=0 width="12" height="12" title="Close"></img></a>
		</td>
	</tr>
	<tr><td height=2></td></tr>
	</table>
	<table width=100% align=center>
		<tr>
			<td style="padding-left: 20px; font-weight: bold;" width=100>Bank Account :</td>
			<td align=left style="padding-left: 20px;">
				<select name="acct_code" style="width: 300px;">
					<?php
						include("connect.libp");
						$q = mysql_query("select acct_code,concat('[',acct_code,']', description) as bank from habagat.subsidiary_account where acct_grp='1000' and acct_code not in ('1001','1002') order by acct_code;");
						while(list($acct,$bank) = mysql_fetch_array($q)) {
							echo "<option value=\"$acct\">$bank</option>";
						}
					?>
				</select>
			</td>
			<td style="padding-left: 20px; font-weight: bold;" width=100>Date As Of :</td>
			<td align=left style="padding-left: 20px;">
				<input type="text" name="tmp_date" id="tmp_date" style="width: 170px;"></td>
			</td>
		</tr>
	</table>
	<hr style="width:95%;" align=center>
	<table align=center>
		<tr><td height=8></td></tr>
		<tr><td></td>
			<td><input type=button value="Process Report" onclick="convert_date();">
			&nbsp;
			<input type=button value="Cancel" onclick="javascript:parent.close_div();">
			</td>
		</tr>
	</table>
	<div id="resultset"></div>
</form>	
</body>
</html>
<?php mysql_close($con); ?>
