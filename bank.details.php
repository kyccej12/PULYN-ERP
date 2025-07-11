<?php
	session_start();
	include("includes/dbUSE.php");
	if(isset($_GET['bid']) && $_GET['bid'] != "") {
		$res = getArray("select * from acctg_bankaccounts where bank_id = '$_GET[bid]';");
	}

	list($check_no) = getArray("select check_no from cv_header where source = '$res[gl_acct]' and branch = '$_SESSION[branchid]' and company = '$_SESSION[company]' order by cv_date desc limit 1;");

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>

<script>
	function add() {
		var msg = "";
		
		if($("#bank_name").val() == "") { msg = msg + "- You did not specify the CIB Account's Bank Name.<br/>"; }
		if($("#gl_acct").val() == "") { msg = msg + "- You must map this account to a General Ledger Account<br/>"; }
		
		
		if(msg!="") {
			parent.sendErrorMessage(msg);
		} else {
			$.post("src/sjerp.php", { mod: "saveBank", bid: $("#bank_id").val(), bname: $("#bank_name").val(), badd: $("#bank_address").val(), tel_no: $("#tel_no").val(), acct_type: $("#acct_type").val(), acct_no: $("#acct_no").val(), gl_acct: $("#gl_acct").val(), check_no: $("#check_no").val(), sid: Math.random() },function() {
				alert("Bank Account Successfully Saved!");
				parent.closeDialog("#bankdetails");
				parent.showBanks();
			});
		}
	}
	
	function del(bid) {
		if(confirm("Are you sure you want to delete this record?") == true) {
			$.post("src/sjerp.php", {mod: "deleteBank",bid: bid, sid: Math.random()},function(){
				parent.closeDialog("#bankdetails");
				parent.showBanks();
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
								<td width=35%><span class="spandix-l">Bank Name :</span></td>
								<td>
									<input type="text" id="bank_name" class="nInput" style="width: 80%;" value="<?php echo $res['bank_name']; ?>" />
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td width=35%><span class="spandix-l">Address :</span></td>
								<td>
									<input type="text" id="bank_address" class="nInput" style="width: 80%;" value="<?php echo $res['bank_address']; ?>" rows=1/>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td width=35%><span class="spandix-l">Tel # :</span></td>
								<td>
									<input type="text" id="tel_no" class="nInput" style="width: 80%;" value="<?php echo $res['tel_no']; ?>" />
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td width=35%><span class="spandix-l">Account Type :</span></td>
								<td>
									<select id="acct_type" style="width: 80%;" class="nInput" />
										<option value="SA" <?php if($res['acct_type']  == 'SA') { echo "selected"; }?>>Savings Account</option>
										<option value="CA" <?php if($res['acct_type']  == 'CA') { echo "selected"; }?>>Current Checking Account</option>
										<option value="DA" <?php if($res['acct_type']  == 'DA') { echo "selected"; }?>>Dollar Account</option>
									</select>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td width=35%><span class="spandix-l">Account # :</span></td>
								<td colspan=3>
									<input type="text" id="acct_no" class="nInput" style="width: 80%;" value="<?php echo $res['acct_no']; ?>" />
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td width=35%><span class="spandix-l">GL Account :</span></td>
								<td>
									<select id="gl_acct" style="width: 80%;" class="nInput" />
										<option value="">- Select GL Account -</option>
										<?php
											$agrp_query = dbquery("select acct_code, description from acctg_accounts where acct_grp ='100100' and company = '$_SESSION[company]' and acct_code not in ('100101','100102') order by acct_code;");
											while(list($acct_code,$desc) = mysql_fetch_array($agrp_query)) {
												echo "<option value='$acct_code' ";
												if($res['gl_acct'] == $acct_code) { echo "selected"; }
												print ">($acct_code) $desc</option>";
											}
										?>
									</select>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td width=35%><span class="spandix-l">Last Check No. Used :</span></td>
								<td colspan=3>
									<input type="text" id="check_no" class="nInput" style="width: 80%;" value="<?php echo $check_no; ?>" />
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr><td colspan=2><hr></hr></td></tr>
							<tr>
								<td align=center colspan=2>
									<button onClick="add();" class="buttonding"><img src="images/icons/floppy.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Save/Update Record</b></button>
									<?php if($_GET['bid'] !='') { ?>
									<button onClick="del('<?php echo $_GET['bid']; ?>');" class="buttonding"><img src="images/icons/delete.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Delete Record</b></button>
									<?php } ?>
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