<?php
	include("includes/dbUSE.php");
	session_start();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Citilights Garden - Ballot Registration Form</title>
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
					<td align="left" style="font-weight: bold; font-size: 11px;">&nbsp;&nbsp;SYSTEM LOGIN</td>
				</tr>
				<tr><td height=2></td></tr>
			</table>
			<table width=100% height=92% border=0 cellspacing=5 cellpadding=0>
				<tr>
					<td valign=top width="90%" class="td_content" style="padding: 20px;">		
						<table border="0" cellpadding="0" cellspacing="0" width=100%>
							<tr>
								<td><span style="font-size: 12px; font-weight: bold;">NOTICE:&nbsp;&nbsp;</span><span style="font-size: 12px; text-align; justify;">The system requires that the passwords of the three (3) Election Board Officers must be provided simultaneously.</span></td>
							</tr>
							<tr><td height=16></td></tr>
							<tr>
								<td width="100%" cellspacing=0 cellpadding=0><input type="password" name="U1" class="nInput" style="width: 90%;" onchange="checkUSER('sp1','U1',this.value);">&nbsp;<span id="sp1"></span><br><span class="spandix-l">MR. ALVIN LUA'S PASSWORD</span></td>
							
							</tr>
							<tr><td height=8></td></tr>
							<tr>
								<td width="100%" cellspacing=0 cellpadding=0><input type="password" name="U2" class="nInput" style="width: 90%;" onchange="checkUSER('sp2','U2',this.value);">&nbsp;<span id="sp2"></span><br><span class="spandix-l">MR. WILLIAM FLORES GERDES' PASSWORD</span></td>
							
							</tr>
							<tr><td height=8></td></tr>
							<tr>
								<td width="100%" cellspacing=0 cellpadding=0><input type="password" name="U3" class="nInput" style="width: 90%;" onchange="checkUSER('sp3','U3',this.value);">&nbsp;<span id="sp3"><br><span class="spandix-l">MR. JOSE MANUEL CUENCO'S PASSWORD</span></span></td>
							
							</tr>
							<tr><td height=16></td></tr>
							<tr><td align=center><button class="bigButton" onclick="authenticateMe();"><b>AUTHENTICATE NOW!</b></button>
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