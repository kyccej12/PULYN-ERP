<?php
	include("includes/dbUSE.php");
	session_start();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Ozian Realty Development</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link href="style/jquery-ui.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/jquery-ui.js"></script>
<script language="javascript" src="js/date.js"></script>
<script>
	function add() {
		var msg = "";
		
		if($("#fname").val() == "") { msg = msg + "You did not specify user's full name.<br/>"; }
		if($("#uname").val() == "") { msg = msg + "You did not specify username for this user.<br/>"; }
		if($("#pass1").val() == "" || $("#pass2").val() == "") { msg = msg + "The system cannot accept empty password.<br/>"; }
		if($("#pass1").val() != $("#pass2").val()) { msg = msg + "Passwords do not match.<br/>"; }
		if($("#changePass").attr("checked") == 'checked') { var cPass = "Y"; } else { var cPass = "N"; }
		
		
		if(msg!="") {
			parent.sendErrorMessage("<b>Unable to continue due to the following error(s):</b><br/><br/>" + msg);
		} else {
			$.post("geck.datacontrol.php", {mod: "checkUname", uname: $("#uname").val(), sid: Math.random() }, function(data) {
				if(data > 0) {
					parent.sendErrorMessage("<b>Unable to add user. Username has already been used by another user.</b>");
				} else {
					$.post("geck.datacontrol.php", { mod: "addUser", fname: $("#fname").val(), uname: $("#uname").val(), pass: $("#pass1").val(), utype: $("#utype").val(), email: $("#uemail").val(), changePass: cPass, sid: Math.random() },function() {
						alert("User successfully added to the system");
						parent.close_div2();
						parent.showUsers()
					});
				}
			},"html");
		}
		
	}


</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<input type="hidden" id = "file_id" value="<?php echo $_GET['fileID']; ?>">
	<input type="hidden" id = "rsv_id" value="<?php echo $lot['rsv_id']; ?>">
	<tr>
		<td style="padding:0px;" valign=top>
			<table width=100% border=0 cellspacing=2 cellpadding=0>
				<tr>
					<td valign=top width="90%" class="td_content" style="padding: 10px;">		
						<table border="0" cellpadding="0" cellspacing="0" width=100%>
							<tr>
								<td width=35%><span class="spandix-l">Full Name :</span></td>
								<td>
									<input type="text" id="fname" class="nInput" style="width: 80%;" value="<?php echo $res['tel_no']; ?>" />
								</td>
							</tr>
							<tr>
								<td width=35%><span class="spandix-l">Username :</span></td>
								<td>
									<input type="text" id="uname" class="nInput" style="width: 80%;" value="<?php echo $res['tel_no']; ?>" />
								</td>
							</tr>
							<tr>
								<td width=35%><span class="spandix-l">Password :</span></td>
								<td>
									<input type="password" id="pass1" class="nInput" style="width: 80%;" value="<?php echo $res['tel_no']; ?>" />
								</td>
							</tr>
							<tr>
								<td width=35%><span class="spandix-l">Confirm Password :</span></td>
								<td>
									<input type="password" id="pass2" class="nInput" style="width: 80%;" value="<?php echo $res['tel_no']; ?>" />
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td width=35%><span class="spandix-l">User Type :</span></td>
								<td>
									<select id="utype" style="width: 80%;" class="nInput" />
										<option value="user">Limited User</option>
										<option value="admin">Super User</option>
									</select>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td width=35%><span class="spandix-l">Email Address :</span></td>
								<td colspan=3>
									<input type="text" id="uemail" class="nInput" style="width: 80%;" value="<?php echo $res['email']; ?>" />
								</td>
							</tr>
							<tr>
								<td width=35%></td>
								<td>
									<input type="checkbox" id="changePass">&nbsp;<span class="spandix"><i>Let user change his/her password on first login</i></span>
								</td>
							</tr>
							<tr><td colspan=2><hr></hr></td></tr>
							<tr>
								<td align=center colspan=2>
									<button onClick="add();" class="buttonding"><img src="images/icons/adduser.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Add System User</b></button>
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