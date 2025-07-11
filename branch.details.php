<?php
	session_start();
	include("includes/dbUSE.php");
	if(isset($_GET['code']) && $_GET['code'] != "") { $res = getArray("select * from options_branches where branch_code = '$_GET[code]' and company = '1';"); }
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>

<script>
	function saveBranch(fid) {
		var msg = "";
		if($("#branch_name").val() == "") { msg = msg + "- You did not specify customer/supplier name/trade name.<br/>"; }
		if($("#province").val() == "") { msg = msg + "- You did not specify Provincial Address for this customer/supplier<br/>"; }
		if($("#city").val() == "") { msg = msg + "- You did not specify City/Municipal Address for this customer/supplier<br/>"; }

		if(msg!="") {
			parent.sendErrorMessage(msg);
		} else {
			var url = $(document.contactinfo).serialize();
			$.ajax({
			type: "POST",
			async: false,
			url: "src/sjerp.php",
			data: "mod=saveBInfo&"+url,
			success: function() { 
					alert("Record Successfully Saved!");
					parent.closeDialog("#customerdetails");
					parent.showBranches();
				}
			});
		}
	}
	
	function deleteCust(fid) {
		if(confirm("It is not advisable to delete any record from this module to keep track of historical transactions. Are you sure you want to delete this record?") == true) {
			$.post("src/sjerp.php", { mod: "deleteBranch", fid: fid, sid: Math.random() }, function(){ "Branch Successfully Deleted!"; parent.closeDialog("#customerdetails"); parent.showCust(); });
		}	
	}

	function getCities(pid) {
		$.post("src/sjerp.php", { mod: "getCities", pid: pid, sid: Math.random() }, function(data) {
			$("#city").html(data);
		},"html");
	}
	
</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<form name="contactinfo" id="contactinfo">
		<input type="hidden" id = "bid" name="bid" value="<?php echo $res['record_id']; ?>">
		<tr>
			<td style="padding:0px;" valign=top>
				<table width=100% border=0 cellspacing=2 cellpadding=0>
					<tr>
						<td valign=top width="90%" class="td_content" style="padding: 10px;">		
							<table border="0" cellpadding="0" cellspacing="0" width=100%>
								<tr>
									<td width=35%><span class="spandix-l">Branch Name :</span></td>
									<td>
										<input type="text" id="branchname" name="branchname" class="nInput" style="width: 80%;" value="<?php echo $res['branch_name']; ?>" />
									</td>
								</tr>
								<tr><td height=4></td></tr>
								<tr>
									<td width=35% valign=top><span class="spandix-l">Street #/Brgy./Village :</span></td>
									<td>
										<textarea style="width: 80%" cols=2 id="address" name="address"><?php echo $res['address']; ?></textarea>
									</td>
								</tr>
								<tr><td height=4></td></tr>
								<tr>
									<td width=35%><span class="spandix-l">Province :</span></td>
									<td colspan=3>
										<select id="province" name="province" style="width: 80%;" class="nInput" onchange="getCities(this.value);" />
											<option value="">- Select Province -</option>
											<?php
												$q0 = dbquery("select province_id, province from options_provinces order by province asc;");
												while($_0 = mysql_fetch_array($q0)) {
													print "<option value='$_0[0]' "; if($_0[0] == $res['province']) { echo "selected"; }
													print ">$_0[1]</option>";
												}
												
											?>
										</select>
									</td>
								</tr>
								<tr><td height=4></td></tr>
								<tr>
									<td width=35%><span class="spandix-l">City :</span></td>
									<td colspan=3>
										<select id="city" name="city" style="width: 80%;" class="nInput" />
											<option value="">- Select City -</option>
											<?php
												$q1 = dbquery("select city_id, city from options_cities where province_id = '$res[province]' order by city asc;");
												while($_1 = mysql_fetch_array($q1)) {
													print "<option value='$_1[0]' "; if($_1[0] == $res['city']) { echo "selected"; }
													print ">$_1[1]</option>";
												}
											?>
										</select>
									</td>
								</tr>
								<tr><td height=4></td></tr>
								<tr>
									<td width=35%><span class="spandix-l">Contact Nos. :</span></td>
									<td colspan=3>
										<input type="text" id="telno" name="telno" class="nInput" style="width: 80%;" value="<?php echo $res['tel_no']; ?>" />
									</td>
								</tr>
								<tr><td height=4></td></tr>
								<tr>
									<td width=35%><span class="spandix-l">O-I-C :</span></td>
									<td colspan=3>
										<input type="text" id="oic" name="oic" class="nInput" style="width: 80%;" value="<?php echo $res['oic']; ?>" />
									</td>
								</tr>
								<tr><td height=4></td></tr>
								<tr>
									<td width=35%><span class="spandix-l">Sales Quota :</span></td>
									<td colspan=3>
										<input type="text" id="quota" name="quota" class="nInput" style="width: 80%;" value="<?php echo number_format($res['sales_quota'],2); ?>" onfocus="if(this.value == '0.00') { this.value = ''; }" onblur="if(this.value=='') { this.value='0.00'; } if(isNaN(parent.stripComma(this.value)) == true) { parent.sendErrorMessage('Error: Invalid User Input!'); this.value='0.00'; this.focus(); } " />
									</td>
								</tr>
								<tr><td height=4></td></tr>
								<tr>
									<td width=35%><span class="spandix-l">Branch Current Acct :</span></td>
									<td colspan=3>
										<select id="branch_current" name="branch_current" style="width: 80%;" class="nInput" />
											<option value="">- Select Account -</option>
											<?php
												$h = dbquery("SELECT acct_code, description FROM acctg_accounts WHERE description LIKE '%advances from%' AND company='$_SESSION[company]';");
												while($g = mysql_fetch_array($h)) {
													print "<option value='$g[0]' "; if($g[0] == $res['branch_current']) { echo "selected"; }
													print ">[$g[0]] $g[1]</option>";
												}
											?>
										</select>	
									</td>
								</tr>
								<tr><td height=4></td></tr>
								<tr>
									<td width=35%><span class="spandix-l">Office Current Acct :</span></td>
									<td colspan=3>
										<select id="office_current" name="office_current" style="width: 80%;" class="nInput" />
											<option value="">- Select Account -</option>
											<?php
												$m = dbquery("SELECT acct_code, description FROM acctg_accounts WHERE description LIKE '%advances to%' AND company='$_SESSION[company]';");
												while($l = mysql_fetch_array($m)) {
													print "<option value='$l[0]' "; if($l[0] == $res['office_current']) { echo "selected"; }
													print ">[$l[0]] $l[1]</option>";
												}
											?>
										</select>	
									</td>
								</tr>
								<tr><td height=4></td></tr>
								<tr>
									<td width=35%><span class="spandix-l">Client Code (SJFC) :</span></td>
									<td colspan=3>
										<input type="text" id="client_code" name="client_code" class="nInput" style="width: 80%;" value="<?php echo $res['client_code']; ?>" />
									</td>
								</tr>
								<tr><td height=4></td></tr>
								<tr><td colspan=2><hr></hr></td></tr>
								<tr><td height=4></td></tr>
								<tr>
									<td align=center colspan=2>
										<button type="button" onClick="saveBranch(<?php echo $_GET['fid']; ?>);" class="buttonding"><img src="images/icons/floppy.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Save/Update Record.</b></button>
										<?php if(isset($_GET['fid']) && $_GET['fid'] != "") { ?>
											<button type="button" onClick="deleteCust('<?php echo $_GET['fid']; ?>');" class="buttonding"><img src="images/icons/delete.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Delete Record</b></button>
										<?php } ?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	 </form>
 </table>

</body>
</html>
<?php mysql_close($con);