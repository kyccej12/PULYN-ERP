<?php 
	session_start(); 

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>$co[company_name] ERP System Version 1.0b</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript">	
		function checkFields() {
			var uname = document.loginForm.txtname.value;
			var pass = document.loginForm.txtpass.value;
			
			if(uname=='' || pass=='') {
				alert("ERROR: One or more information are needed. Please check the credentials you supplied...");
				return false;
			} else {
				document.loginForm.submit();
			}
		}
		
	</script>
</head>
<body bgcolor="#000000" leftmargin=0 rightmargin=0 bottommargin=0 topmargin=0 onLoad="document.loginForm.txtname.focus();">
<table height="15%"><tr><td></td></tr></table>
<table width=550 border="0" cellpadding="0" cellspacing="0" align=center class="login">
	<form name=loginForm method=POST action="authenticate.php">
	<tr><td height=12></td></tr>
	<tr><td width=30></td> <td class="header-quicksilver" height=60>&nbsp;</td></tr>
	<tr><td height=12></td></tr>
	<tr>
		<td colspan=2 class=loginText>Username<br>
			<input type="text" name="txtname" id="txtname" class="gridInput" style="width:90%">	
		</td>
	</tr>
	<tr><td height=4></td></tr>
	<tr>
		<td colspan=2 class=loginText>Password<br>
			<input type="password" name="txtpass" id="txtpass" class="gridInput" style="width:90%">
		</td>
	</tr>
	<tr><td height=4></td></tr>
	<tr>
		<td colspan=2 class=loginText>Company<br>
			<select name="company" id="company" class="gridInput" style="width:90%" onChange="javascript: getBranchList(this.value);">
				<option value = "">- Select Company -</option>
				<?php
					include("includes/dbUSE.php");
					$c = dbquery("select company_id, company_name from companies order by company_id;");
					while(list($cid,$cname) = mysql_fetch_array($c)) {
						echo "<option value='$cid'>$cname</option>";
					}
					mysql_close($con);
				?>
			</select>	
		</td>
	</tr>
	<tr><td height=4></td></tr>
	<tr>
		<td colspan=2 class=loginText>Branch<br>
			<select name="branch" id="branch" class="gridInput" style="width:90%">
				<option value = "">- Select Branch -</option>
			</select>
		</td>
	</tr>
	<tr><td height=4></td></tr>
	<tr>
		<td colspan=2 style="padding-left: 40px; padding-top: 10px;" valign=top>
			<button type=button onclick="checkFields();"><img src="images/icons/key.png" height=16 width=16 align=absmiddle />&nbsp;Login</button>
		</td>
	</tr>
	<tr>
		<td colspan=2 style="padding: 45px 20px 20px 20px; color: #4a4a4a;">
			<p style='text-align: justify; font-size: 11px;'><font style='font-weight: bold; font-size: 11px;'>NOTICE:</font> Use of this network, its equipment, and resources is monitored at all times and requires explicit permission from the network administrator.</p>
		</td>
	</tr>
	</form>
</table>
<table width=450 align=center cellspacing=0 cellpadding=0 bgcolor="#000000" border=0 style="padding-top:5px;">
	<tr><td valign="top" align=center height=20 style="font-size: 9px; color: #ffffff">&copy; Developed by PORT80 Business Solutions for Se&ntilde;or $co[company_name] and its conglomerate</td></tr>
</table>
<table height="25%"><tr><td></td></tr></table>
</body>
</html>
<?php unset($_SESSION['logerror']); ?>