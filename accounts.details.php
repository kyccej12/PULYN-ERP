<?php
	session_start();
	include("includes/dbUSE.php");
	

	if(isset($_GET['code']) && $_GET['code'] != "") {
		$res = getArray("select * from acctg_accounts where acct_code = '$_GET[code]';");
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script>
	function saveRecord(id) {
		var msg = "";
		
		if($("#acct_code").val() == "") { msg = msg + "- You did not specify account code to add...<br/>"; }
		if($("#acct_description").val() == "") { msg = msg + "- You did not specify account description...<br/>"; }
		if($("#gl_acct").val() == "") { msg = msg + "- Account Description must not be empty...<br/>"; }
		
		
		if(msg!="") {
			parent.sendErrorMessage(msg);
		} else {

			$.post("src/sjerp.php", { mod: "saveAccount", aid: id, acct_code: $("#acct_code").val(), acct_desc: $("#acct_description").val(), acct_grp: $("#acct_grp").val(), sid: Math.random() },function(data) {
				if(data == "DUPLICATE") {
					parent.sendErrorMessage("Duplicate Account Code detected...")
				} else {
					alert("Bank Account Successfully Saved!");
					parent.closeDialog("#accountdetails");
					parent.showAccounts(); 
				}
			});
		}
	}
</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<input type="hidden" id = "bank_id" value="<?php echo $_GET['bid']; ?>">
	<tr>
		<td style="padding:0px;" valign=top>
			<table width=100% border=0 cellspacing=2 cellpadding=0>
				<tr>
					<td valign=top width="90%" class="td_content" style="padding: 10px;">		
						<table border="0" cellpadding="0" cellspacing="0" width=100%>
							<tr>
								<td width=35%><span class="spandix-l">Account Code :</span></td>
								<td>
									<input type="text" id="acct_code" class="nInput" style="width: 80%;" value="<?php echo $res['acct_code']; ?>" />
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td width=35%><span class="spandix-l">Account Description :</span></td>
								<td>
									<input type="text" id="acct_description" class="nInput" style="width: 80%;" value="<?php echo $res['description']; ?>" rows=1/>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td width=35%><span class="spandix-l">Account Group:</span></td>
								<td>
									<select id="acct_grp" style="width: 80%;" class="nInput" />
									<?php
										$agq = dbquery("select acct_grp,description from acctg_accountgrps");
										while($x = mysql_fetch_array($agq)) {
											echo "<option value='$x[acct_grp]' ";
											if($res['acct_grp'] == $x['acct_grp']) { echo "selected"; }
											echo ">($x[acct_grp]) $x[description]</ioption>";
										}
									?>
									</select>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr><td colspan=2><hr></hr></td></tr>
							<tr>
								<td align=center colspan=2>
									<button onClick="saveRecord(<?php echo $_GET['bid']; ?>);" class="buttonding" id="btn_rsv" style="width: 180px;"><img src="images/icons/floppy.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Save/Update Record</b></button>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
 </table>
</body>
</html>
<?php mysql_close($con);