<?php
	include("includes/dbUSE.php");
	session_start();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Geck Distributors</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/main.js"></script>
<script language="javascript" src="js/date.js"></script>
<script language="javascript" src="js/tableH.js"></script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;">
			<table align=center width=100% height=8% border=0 cellspacing=0 cellpadding=0 style="border-bottom:2px solid black; background-color:#595959; background-image: url(images/4.jpg); font-weight:bold; color:#ffffff;">
				<tr><td height=2></td></tr>
				<tr>
					<td align="left" style="font-weight: bold; font-size: 11px;">&nbsp;&nbsp;UPDATE SECURITY CREDENTIAL</td>
					<td align=right width="6%" style="padding-right: 5px;" valign=middle>
						<a href="javascript: parent.close_div();"><img src="images/close.png" border=0 width="12" height="12" title="Close"></img></a>
					</td>
				</tr>
				<tr><td height=2></td></tr>
			</table>
			<table width=100% height=92% border=0 cellspacing=5 cellpadding=0>
				<tr>
					<td valign=top width="90%" class="td_content" style="padding: 20px;">		
						<table border="0" cellpadding="0" cellspacing="0" width=100%>
							<tr>
								<td><span style="font-size: 12px; font-weight: bold;">NOTICE:&nbsp;&nbsp;</span><span style="font-size: 12px; text-align; justify;">Before changing your password, please make it sure that you have selected your name on the selection box. You are also required to provide your old password to verify your identity.</span></td>
							</tr>
							<tr><td height=16></td></tr>
							<tr>
								<td width="100%" cellspacing=0 cellpadding=0>
									<select id="uid" class=gridInput style="width: 90%">
										<option value="U1">- SELECT USER -</option>
										<option value="U1">MR. ALVIN LUA</option>
										<option value="U2">MR. WILLIAM FOREST GERDES</option>
										<option value="U3">MR. JOSE MANUEL CUENCO</option>
									</select>
									<br>
									<span class="spandix-l">SELECT YOUR NAME</span>
								</td>
							</tr>
							<tr><td height=8></td></tr>
							<tr>
								<td width="100%" cellspacing=0 cellpadding=0><input type="password" id="old_pass" class="nInput" style="width: 90%;" onchange="checkUSER('sp2',$('#uid').val(),this.value);">&nbsp;<span id="sp2"></span><br><span class="spandix-l">TYPE YOUR OLD PASSWORD</span></td>
							
							</tr>
							<tr><td height=8></td></tr>
							<tr>
								<td width="100%" cellspacing=0 cellpadding=0><input type="password" id="new_pass" class="nInput" style="width: 90%;">&nbsp;<span id="sp3"><br><span class="spandix-l">TYPE YOUR NEW PASSWORD</span></span></td>
							
							</tr>
							<tr><td height=16></td></tr>
							<tr><td align=center><button class="bigButton" onclick="changeMyPassword();"><b>CHANGE YOUR PASSWORD!</b></button>
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