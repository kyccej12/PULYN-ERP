<?php
	session_start();
	include("includes/dbUSE.php");
	$emp_idno = $_GET[eid];
	$_yres = getArray("select * from hris.emp_edubackground where emp_id = '$emp_idno';");
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="style/dropMenu.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script language="javascript" src="js/jquery.dialogextend.js"></script>
	<script>

		jQuery.fn.mySerialize = function() {
			  var returning = '';
			  $('input, select, textarea', this).each(function() {
			      if (this.value !== "") // check this to avoid && in returning string
			          returning += '&' + this.id + "="  + this.value;
			  });
			  return returning.substring(1);

			};

		function save_edubackground() {
			if(confirm("Are you sure you want to save the changes you've made on this record?") == true) {
				var valid=0;

				 $("#frmEdu").find('input[type=text]').each(function(){
					 if($(this).val() != "") valid+=1; 
					});

				 if(!valid) {
				 	parent.sendErrorMessage("You must fill up at least one field on the form for this record to be served...");
				 } else {
				 	var url = $("#frmEdu").mySerialize();
				 	    url = url + "&mod=saveEdu&sid=" + Math.random();
				 	$.post("payroll.datacontrol.php", url);
				 }

			}
		}

		function clearEHistory(eid) {
			if(confirm("Are you sure you want to clear employee's educational background?") == true) {
				$.post("payroll.datacontrol.php",{ mod: "clearEHistory", eid: eid, sid: Math.random() }, function() {
					alert("Employee's Educational Bacgkround Successfully Cleared!");
					parent.closeDialog("#empedu")
				});
			}
		}

	</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
	<form name="frmEdu" id="frmEdu">	
		<input type = "hidden" id="emp_idno" name="emp_idno" value="<?php echo $emp_idno; ?>">
			<table border=0 cellpadding="0" cellspacing="0" width=100%>
				<tr>
					<td valign=top align=center>
						<table cellpadding=0 cellspacing=1 width=100% border=0 align=center>
							<tr style="background-color :#cccccc">
								<td align=center class=bareBold align="center" style="padding-top: 4px; padding-bottom: 4px;">POST GRADUATE STUDIES / MASTER'S DEGREE</i></td>
							</tr>
						</table>
									<table width=100% cellpadding=0 cellspacing=1>
										<tr>
											<td width=50% valign=top style="border-left: thin solid #ccc; border-top: thin solid #ccc; border-bottom: thin solid #ccc; padding-left: 10px">
												<table width="100%" border="0">
													<tr><td height=2></td>
													<tr>
														<td class=bareBold valign="top" style="width : 120px;">Specialization :</td>
														<td class=bare>
															<select id="pg_specialization" style="width: 98%;" class="nInput">
															<option value="">- Select Specialization -</option>
															<?php
																$spz = mysql_query("select id, specialization from hris.emp_specialization order by specialization;");
																while($opt = mysql_fetch_array($spz)) {
																	print "<option value=$opt[0] ";
																	   if($opt[0] == $_yres['pg_specialization']) { print "selected"; }
																	print ">$opt[1]</option>";
																}
															?>
															</select>
														</td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold valign="top" style="width : 50px">Course/Major :</td>
														<td class=bare><input type="text" class="nInput" type="text" style="width: 98%" id="pg_major" value="<?php print $_yres['pg_major']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold valign="top" style="width : 50px">School/Institution :</td>
														<td class=bare><input type="text" class="nInput" type="text" style="width: 98%" id="pg_school" value="<?php print $_yres['pg_school']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold valign="top" style="width : 50px">Address :</td>
														<td class=bare><textarea id="pg_address" rows="1" style="width:98%"><?php print $_yres['pg_address']; ?></textarea></td>
													</tr>
												</table>
											</td>
											<td  style="border-right: thin solid #ccc; border-top: thin solid #ccc; border-bottom: thin solid #ccc;" valign=top>
												<table border="0" cellpadding="0" cellspacing="1" width=100%>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold align="right">School Years Attended :&nbsp;&nbsp;</td>
														<td class=bare><input type="text" class="nInput" type="text" id="pg_years" style="width : 140px;" align="right" value="<?php print $_yres['pg_years']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold align="right">School Year Graduated :&nbsp;&nbsp;</td>
														<td class=bare><input type="text" class="nInput" type="text" id="pg_graduated" style="width : 140px;" align="right" value="<?php print $_yres['pg_graduated']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold align="right" valign=top>Awards Received :&nbsp;&nbsp;</td>
														<td class=bare><textarea type="text" id="pg_awards" rows="2" style="width: 90%;" align=right><?php print $_yres['pg_awards']; ?></textarea></td>
													</tr>
													<tr><td colspan="9" height="5"></td></tr>
												</table>
											</td>
										</tr>	
									</table>
									<table cellpadding=0 cellspacing=1 width=100% border=0 align=center>
										<tr style="background-color :#cccccc">
											<td align=center class=bareBold align="center" style="padding-top: 4px; padding-bottom: 4px;">COLLEGE EDUCATION</i></td>
										</tr>
									</table>
									<table width=100% cellpadding=0 cellspacing=1>
										<tr>
											<td width=50% valign=top style="border-left: thin solid #ccc; border-top: thin solid #ccc; border-bottom: thin solid #ccc; padding-left: 10px">
												<table width="100%" border="0">
													<tr><td height=2></td>
													<tr>
														<td class=bareBold valign="top" style="width : 120px">Specialization :</td>
														<td class=bare>
															<select id="co_specialization" style="width: 98%;" class="nInput">
															<option value="">- Select Specialization -</option>
															<?php
																$spz1 = mysql_query("select id, specialization from hris.emp_specialization order by specialization;");
																while($opt1 = mysql_fetch_array($spz1)) {
																	print "<option value=$opt1[0] ";
																	   if($opt1[0] == $_yres['co_specialization']) { print "selected"; }
																	print ">$opt1[1]</option>";
																}
															?>
															</select>
														</td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold valign="top" style="width : 50px">Course/Major :</td>
														<td class=bare><input type="text" class="nInput" type="text" style="width: 98%" id="co_major" value="<?php print $_yres['co_major']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold valign="top" style="width : 50px">School/Institution :</td>
														<td class=bare><input type="text" class="nInput" type="text" style="width: 98%" id="co_school" value="<?php print $_yres['co_school']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold valign="top" style="width : 50px">Address :</td>
														<td class=bare><textarea id="co_address" rows="1" style="width:98%"><?php echo $_yres['co_address']; ?></textarea></td>
													</tr>
												</table>
											</td>
											<td  style="border-right: thin solid #ccc; border-top: thin solid #ccc; border-bottom: thin solid #ccc;" valign=top>
												<table border="0" cellpadding="0" cellspacing="1" width=100%>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold align="right">School Years Attended :&nbsp;&nbsp;</td>
														<td class=bare><input type="text" class="nInput" type="text" id="co_years" style="width : 140px;" align="right" value="<?php print $_yres['co_years']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold align="right">School Year Graduated :&nbsp;&nbsp;</td>
														<td class=bare><input type="text" class="nInput" type="text" id="co_graduated" style="width : 140px;" align="right" value="<?php print $_yres['co_graduated']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold align="right" valign=top>Awards Received :&nbsp;&nbsp;</td>
														<td class=bare><textarea type="text" id="co_awards" rows="2" style="width: 90%;" align=right><?php print $_yres['co_awards']; ?></textarea></td>
													</tr>
													<tr><td colspan="9" height="5"></td></tr>
												</table>
											</td>
										</tr>	
									</table>
									<table cellpadding=0 cellspacing=1 width=100% border=0 align=center>
										<tr style="background-color :#cccccc">
											<td align=center class=bareBold align="center" style="padding-top: 4px; padding-bottom: 4px;">HIGH SCHOOL EDUCATION</i></td>
										</tr>
									</table>
									<table width=100% cellpadding=0 cellspacing=1>
										<tr>
											<td width=50% valign=top style="border-left: thin solid #ccc; border-top: thin solid #ccc; border-bottom: thin solid #ccc; padding-left: 10px">
												<table width="100%" border="0">
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold valign="top" style="width : 120px">School/Institution :</td>
														<td class=bare><input type="text" class="nInput" type="text" style="width: 98%" id="hs_school" value="<?php print $_yres['hs_school']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold valign="top" style="width : 50px">Address :</td>
														<td class=bare><textarea id="hs_address" rows="1" style="width:98%"><?php echo $_yres['hs_address']; ?></textarea></td>
													</tr>
												</table>
											</td>
											<td  style="border-right: thin solid #ccc; border-top: thin solid #ccc; border-bottom: thin solid #ccc;" valign=top>
												<table border="0" cellpadding="0" cellspacing="1" width=100%>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold align="right">School Years Attended :&nbsp;&nbsp;</td>
														<td class=bare><input type="text" class="nInput" type="text" id="hs_years" style="width : 140px;" align="right" value="<?php print $_yres['hs_years']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold align="right">School Year Graduated :&nbsp;&nbsp;</td>
														<td class=bare><input type="text" class="nInput" type="text" id="hs_graduated" style="width : 140px;" align="right" value="<?php print $_yres['hs_graduated']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold align="right" valign=top>Awards Received :&nbsp;&nbsp;</td>
														<td class=bare><textarea type="text" id="hs_awards" rows="2" style="width: 90%;" align=right><?php print $_yres['hs_awards']; ?></textarea></td>
													</tr>
													<tr><td colspan="9" height="5"></td></tr>
												</table>
											</td>
										</tr>	
									</table>
									<table cellpadding=0 cellspacing=1 width=100% border=0 align=center>
										<tr style="background-color :#cccccc">
											<td align=center class=bareBold align="center" style="padding-top: 4px; padding-bottom: 4px;">ELEMENTARY EDUCATION</i></td>
										</tr>
									</table>
									<table width=100% cellpadding=0 cellspacing=1>
										<tr>
											<td width=50% valign=top style="border-left: thin solid #ccc; border-top: thin solid #ccc; border-bottom: thin solid #ccc; padding-left: 10px">
												<table width="100%" border="0">
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold valign="top" style="width : 120px">School/Institution :</td>
														<td class=bare><input type="text" class="nInput" type="text" style="width: 98%" id="elem_school" value="<?php print $_yres['elem_school']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold valign="top" style="width : 50px">Address :</td>
														<td class=bare><textarea id="elem_address" rows="1" style="width:98%"><?php print $_yres['elem_address']; ?></textarea></td>
													</tr>
												</table>
											</td>
											<td  style="border-right: thin solid #ccc; border-top: thin solid #ccc; border-bottom: thin solid #ccc;" valign=top>
												<table border="0" cellpadding="0" cellspacing="1" width=100%>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold align="right">School Years Attended :&nbsp;&nbsp;</td>
														<td class=bare><input type="text" class="nInput" type="text" id="elem_years" style="width : 140px;" align="right" value="<?php print $_yres['elem_years']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold align="right">School Year Graduated :&nbsp;&nbsp;</td>
														<td class=bare><input type="text" class="nInput" type="text" id="elem_graduated" style="width : 140px;" align="right" value="<?php print $_yres['elem_graduated']; ?>"></td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td class=bareBold align="right" valign=top>Awards Received :&nbsp;&nbsp;</td>
														<td class=bare><textarea type="text" id="elem_awards" rows="2" style="width: 90%;" align=right ><?php print $_yres['elem_awards']; ?></textarea></td>
													</tr>
													<tr><td colspan="9" height="5"></td></tr>
												</table>
											</td>
										</tr>	
									</table>
									<table border="0" cellpadding="0" cellspacing="1" width=100%>
										<tr><td height=16></td></tr>
										<tr><td align=center>
												<button type=button  onclick="save_edubackground();"><img src="images/icons/save.png" width=22 height=22 align=absmiddle />&nbsp;&nbsp;Save Changes</button>
												<button type=button  onclick="clearEHistory(<?php echo $emp_idno; ?>);"><img src="images/delete.png" width=22 height=22 align=absmiddle />&nbsp;&nbsp;Clear Employee's Educational Background</button>
							
											</td>
										</tr>
										<tr><td height=8></td></tr>
									</table>
								</td>
							</tr>
						</table>
	</form>
</body>
</html>
<?php mysql_close($con);