<?php
	session_start();
	include("includes/dbUSE.php");

	$date_opt = dbquery("SELECT period_id,CONCAT(DATE_FORMAT(`start`,'%m/%d/%Y'),' - ',DATE_FORMAT(`end`,'%m/%d/%Y')) AS label FROM cg_hrd.e_payperiods ;");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script>
		$(function() { $("#dtf").datepicker(); $("#dt2").datepicker(); });
	</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<form enctype="multipart/form-data" action="payroll.importdtr.php" method="POST">
	<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<select class="nInput" id = "dtr_period" name = "dtr_period">
					<?php
						while($rowp=mysql_fetch_array($date_opt)){
							echo "<option value='$rowp[period_id]'> $rowp[label] </option>";
						}
					?>
				</select>
			</td>
			</tr>
				<tr><td height=4></td></tr>
		<tr><td height=4></td></tr>
		<tr>
			<td align="left" style="padding:5px; font-size:9pt;">Choose a file to upload: </td>
			<td><input name="uploadedfile" type="file" /></td>
		</tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button type="submit" class="buttonding"><img src="images/icons/dtr2.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Upload & Process Raw DTR Logs</b></button>
			</td>
		</tr>
	</table>
</form>
</body>
</html>
<?php mysql_close($con);