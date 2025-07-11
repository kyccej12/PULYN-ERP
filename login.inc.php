<?php

	function _returnError($code) {
		switch($code) {
			case "1": echo "Invalid Username or Password!";	break;
			case "2": echo "You have been logged out as your session has already expired!"; break;
			case "3": echo "Unable to renew Session ID. Please contact system administrator to correct this problem."; break;
			case "4": echo "Unable to retrieve Session Data. Please contact system administrator to correct this problem."; break;
		}
	}

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title></title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript">	
		
		function checkFields() {
			var uname = document.loginForm.txtname.value;
			//var branch = document.loginForm.branch.value;
			
			if(uname=='' ) {
				alert("ERROR: One or more information are needed. Please check the credentials you supplied...");
				return false;
			} else {
				document.loginForm.submit();
			}
		}

		function getCompany(username) {
			if(username != "") {
				$.post("initAuth.php", { mod: "getCompany", uname:username, sid: Math.random() },function(data) {
					if(data == 'error') { 
						alert("Username \""+username+"\" is not on the database!");
						$("#txtname").val(''); document.getElementById('txtname').focus();
					} else {
						$("#company").html(data);
					}
				},"html");
			}
		}

		function getBranchList(company) {
			var username = $("#txtname").val();
			$.post("initAuth.php", { mod: "getnewbranch", company: company,uname:username, sid: Math.random() },function(data) {
				$("#branch").html(data);
			},"html");
		}

	</script>
</head>
<body bgcolor="#000000" leftmargin=0 rightmargin=0 bottommargin=0 topmargin=0 onLoad="document.loginForm.txtname.focus();">
<table height="25%"><tr><td></td></tr></table>
<table width=450 border="0" cellpadding="0" cellspacing="0" valign="middle" align=center style="background-color: #FFFFFF;">
<form name="loginForm" action="authenticate.php" method="post" onsubmit="javascript: checkFields();">
	<tr>
		<td width="100%" height="100%" align=center valign="middle" style="border: 2px #dfc341 solid;">
			<table align=center width=100% border=0 cellspacing=0 cellpadding=0 style="border-bottom:1px solid #dfc341; background-color:#4c6ea4; font-weight:bold; color:#ffffff;">
				<tr><td height=6></td></tr>
				<tr>
					<td align="left" style="font-weight:bold; font-size: 10px;">&nbsp;&nbsp;USER LOGIN</td>
				</tr>
				<tr><td height=6></td></tr>
			</table>
			<table width=100% align=center border=0 cellspacing=0 cellpadding=0>
				<tr>
					<td width="30%" align=center style="padding-left: 10px;"><img src="images/login2.png"></img></td>
					<td width="70%">
						<table width="100%" border=0 cellspacing=0 cellpadding=0>
							<tr><td height=16></td></tr>
							<tr><td colspan=2 align=left class="loginTextBold"></td></tr>
							<tr><td height=16></td></tr>
							<?php
								if(isset($_REQUEST['exception'])) { ?>
								<tr>
									<td>
										<table width=100% cellpadding=0 cellspacing=0 border=0>
											<tr>
												<td width="18px;" valign=top style="padding-left: 20px;"><img src="images/icons/warning.png" width=20 height=20 /></td>
												<td valign=middle style="font-size: 11px; font-weight: bold; color: red; padding-right: 10px; padding-left: 5px;"><?php _returnError($_REQUEST['exception']); ?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr><td height=8></td></tr>
							<?php } ?>
							<tr>
								<td class=loginText>Username<br>
									<input type="text" name="txtname" id="txtname" style="width:90%" class="gridInput" autocomplete="off" onblur="javascript: getCompany(this.value);" >	
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td class=loginText>Password<br>
									<input type="password" name="txtpass" id="txtpass" class="gridInput" style="width:90%">
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td style="padding: 5px 20px 10px 20px; color: #4a4a4a;">
									<p style='text-align: justify; font-size: 11px;'><font style='font-weight: bold; font-size: 11px;'>NOTICE:</font> Use of this network, its equipment, and resources is monitored at all times and requires explicit permission from the network administrator.</p>
								</td>
							</tr>
						</table>
						<table width="100%" align=center>
							<tr><td height=4></td></tr>
							<tr>
								<td align=left style="padding-left: 20px;"><button type=button onclick="checkFields();"><image src="images/icons/key.png" height=18 width=18 align=absmiddle border=0 />&nbsp;Login</button></td>
							</tr>
							<tr><td height=16></td></tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</form>
</table>
<table width=450 align=center cellspacing=0 cellpadding=0 bgcolor="#000000" border=0 style="padding-top:5px;">
	<tr><td valign="top" align=center height=25 style="font-size: 9px; color: #ffffff">&copy;</td></tr>
</table>
<table height="25%"><tr><td></td></tr></table>
</body>
</html>
<?php unset($_SESSION['logerror']); ?>