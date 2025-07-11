<?php
	include("includes/dbUSE.php");
	session_start();
	//list($_GET[eid]) = getArray("select EMP_ID from hris.emp_masterfile where EMP_ID = '$_GET[eid]';");
	$_sres = getArray("select *, date_format(bday, '%m/%d/%Y') as sbday from hris.emp_srecord where emp_id = '$_GET[eid]';");
	$_pres = getArray("select *, date_format(mom_bday,'%m/%d/%Y') as mbday, date_format(dad_bday,'%m/%d/%Y') as dbday from hris.emp_precord where emp_id = '$_GET[eid]';");
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
		
		function saveFBackground(eid) {
			if(confirm("Are you sure you want to save the changes you've made on this file?") == true) {
				$.post("payroll.datacontrol.php", { 
					mod: "saveFBackground", 
					eid: eid, 
					s_lname: $("#spouse_lname").val(), 
					s_fname: $("#spouse_fname").val(),
					s_mname: $("#spouse_mname").val(),
					s_address: $("#spouse_address").val(),
					s_bday: $("#spouse_bday").val(),
					s_occupation: $("#spouse_occupation").val(),
					m_lname: $("#mom_lname").val(),
					m_fname: $("#mom_fname").val(),
					m_mname: $("#mom_mname").val(),
					m_bday: $("#mom_bday").val(),
					m_occupation: $("#mom_occupation").val(),
					d_lname: $("#dad_lname").val(),
					d_fname: $("#dad_fname").val(),
					d_mname: $("#dad_mname").val(),
					d_bday: $("#dad_bday").val(),
					d_occupation: $("#dad_occupation").val(),
					mdaddress: $("#mdaddress").val(),
					sid: Math.random() }, function() {
						alert("Record Successfully Saved!");
					}
				);
			}
		}

		function clearFBackground(eid) {
			if(confirm("Are you sure you want to clear employee's family background?") == true) {
				$.post("payroll.datacontrol.php", { mod: "clearFBackground", eid: eid, sid: Math.random() }, function() {
					alert("Employee's family background completey cleared...");
					parent.closeDialog("#empfam");
				});
			}
		}

		function view_crecord(lid) {
			$.post("payroll.datacontrol.php", { mod: "viewCRecord", rid: lid, sid: Math.random() }, function(data) {
				$("#c_lname").val(data['lname']);
				$("#c_fname").val(data['fname']);
				$("#c_mname").val(data['mname']);
				$("#c_bday").val(data['xbday']);
				$("#c_gender").val(data['gender']);
				$("#c_cstat").val(data['status']);
				$("#c_occupation").val(data['occupation']);
				$("#c_rid").val(data['record_id']);
				$("#childinfo").dialog({
					title: "Child Information", 
					width: 380, 
					height: 360, 
					resizable: false, 
						buttons: {
						"Save Changes":  function() { saveCRecord(); },
						"Delete File": function() { deleteCfile(lid); }
					}
				});	
			},"json");
		}

		function newCRecord() {
			$(document.frmChild)[0].reset();
			$("#childinfo").dialog({
					title: "Child Information", 
					width: 380, 
					height: 360, 
					resizable: false, 
						buttons: {
						"Add New Record":  function() { saveCRecord(); }
					}
			});	
		}

		function saveCRecord() {
			var msg = "";
			if($("#c_lname").val() == "") { msg = msg + "- Please speficy your child's Last Name<br/>"; }
			if($("#c_fname").val() == "") { msg = msg + "- Please speficy your child's First Name<br/>"; }
			if($("#c_mname").val() == "") { msg = msg + "- Please speficy your child's Middle Name<br/>"; }
			if($("#c_bday").val() == "") { msg = msg + "- Please speficy your child's Birth Date<br/>"; }
			if(msg != "") {
				parent.sendErrorMessage(msg);
			} else {
				$.post("payroll.datacontrol.php", { mod: "saveCRecord", rid: $("#c_rid").val(), eid: $("#emp_idno").val(), lname: $("#c_lname").val(), fname: $("#c_fname").val(), mname: $("#c_mname").val(), bday: $("#c_bday").val(), gender: $("#c_gender").val(), cstat: $("#c_cstat").val(), occupation: $("#c_occupation").val(), sid: Math.random() }, function(data) {
					alert("Record Successfully Saved...");
					$("#childinfo").dialog("close");
					$(document.frmChild)[0].reset();
					$("#crecords").html(data);
				},"html");
			}
		}

		function deleteCfile(rid) {
			if(confirm("Are you sure you want to delete this record?") == true) {
				$.post("payroll.datacontrol.php", { mod: "deleteCRecord", rid: rid, eid: $("#emp_idno").val(), sid: Math.random() }, function(data) {
					$("#childinfo").dialog("close");
					$(document.frmChild)[0].reset();
					$("#crecords").html(data);
				},"html");
			}
		}



		function view_brecord(lid) {
			$.post("payroll.datacontrol.php", { mod: "viewBRecord", rid: lid, sid: Math.random() }, function(data) {
				$("#b_lname").val(data['lname']);
				$("#b_fname").val(data['fname']);
				$("#b_mname").val(data['mname']);
				$("#b_bday").val(data['xbday']);
				$("#b_gender").val(data['gender']);
				$("#b_cstat").val(data['status']);
				$("#b_occupation").val(data['occupation']);
				$("#b_rid").val(data['record_id']);
				$("#BroSis").dialog({
					title: "Brother/Sister Info", 
					width: 380, 
					height: 360, 
					resizable: false, 
						buttons: {
						"Save Changes":  function() { saveBRecord(); },
						"Delete File": function() { deleteBfile(lid); }
					}
				});	
			},"json");
		}

		function newBRecord() {
			$(document.frmBroSis)[0].reset();
			$("#BroSis").dialog({
					title: "Brother/Sister Info", 
					width: 380, 
					height: 360, 
					resizable: false, 
						buttons: {
						"Add New Record":  function() { saveBRecord(); }
					}
			});	
		}

		function saveBRecord() {
			var msg = "";
			if($("#b_lname").val() == "") { msg = msg + "- Please speficy your child's Last Name<br/>"; }
			if($("#b_fname").val() == "") { msg = msg + "- Please speficy your child's First Name<br/>"; }
			if($("#b_mname").val() == "") { msg = msg + "- Please speficy your child's Middle Name<br/>"; }
			if($("#b_bday").val() == "") { msg = msg + "- Please speficy your child's Birth Date<br/>"; }
			if(msg != "") {
				parent.sendErrorMessage(msg);
			} else {
				$.post("payroll.datacontrol.php", { mod: "saveBRecord", rid: $("#b_rid").val(), eid: $("#emp_idno").val(), lname: $("#b_lname").val(), fname: $("#b_fname").val(), mname: $("#b_mname").val(), bday: $("#b_bday").val(), gender: $("#b_gender").val(), cstat: $("#b_cstat").val(), occupation: $("#b_occupation").val(), sid: Math.random() }, function(data) {
					alert("Record Successfully Saved...");
					$("#BroSis").dialog("close");
					$(document.frmBroSis)[0].reset();
					$("#brecords").html(data);
				},"html");
			}
		}

		function deleteBfile(rid) {
			if(confirm("Are you sure you want to delete this record?") == true) {
				$.post("payroll.datacontrol.php", { mod: "deleteBRecord", rid: rid, eid: $("#emp_idno").val(), sid: Math.random() }, function(data) {
					$("#BroSis").dialog("close");
					$(document.frmBroSis)[0].reset();
					$("#brecords").html(data);
				},"html");
			}
		}

		$(function() { 
			$("#spouse_bday").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear()});
			$("#mom_bday").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear() }); 
			$("#dad_bday").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear() }); 
			$("#c_bday").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear() }); 
			$("#b_bday").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear() }); 
		});

	</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
	<input type = "hidden" id="emp_idno" name="emp_idno" value="<?php echo $_GET[eid]; ?>">
	<table cellpadding=0 cellspacing=1 width=100% border=0 align=center>
		<tr style="background-color :#cccccc">
			<td align=center class=bareBold align="center" style="padding-top: 4px; padding-bottom: 4px; font-size: 12px;"><b>SPOUSE INFORMATION <i>(IF MARRIED)</i></b></td>
		</tr>
	</table>
	<table width=100% cellpadding=0 cellspacing=1>
		<tr>
			<td width=60% valign=top style="border-left: thin solid #ccc; border-top: thin solid #ccc; border-bottom: thin solid #ccc; padding-left: 10px">
				<table border="0" cellpadding="0" cellspacing="1" width=100%>
					<tr><td height=2></td></tr>
					<tr>
						<td class=baregray colspan=3></td>
					</tr>
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareBold width="15%">Name :&nbsp;&nbsp;&nbsp;</td>
						<td class=bare width=30%><input type="text" class="nInput3" id="spouse_lname" name="spouse_lname" style="width:95%" value="<?php print $_sres['lname']; ?>"></td>
						<td class=bare style="width : 30px"></td>
						<td class=bare><input type="text" class="nInput3" id="spouse_fname" name="spouse_fname" style="width:95%" value="<?php print $_sres['fname']; ?>"></td>
						<td class=bare style="width : 30px"></td>
						<td class=bare><input type="text" class="nInput3" id="spouse_mname" name="spouse_mname" style="width:95%" value="<?php print $_sres['mname']; ?>"></td>
					</tr>
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareGray></td>
						<td class=bareGray align="center">(Maiden Last Name)</td>
						<td class=bareGray></td>
						<td class=bareGray align="center">(First Name)</td>
						<td class=bareGray></td>
						<td class=bareGray align="center">(Maiden Middle Name)</td>
					</tr>		
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareBold width="15%" valign=top>Address :&nbsp;&nbsp;&nbsp;</td>
						<td class=bare colspan=5>
							<textarea id="spouse_address" name="spouse_address" rows="1" style="width:98%"><?php print $_sres['address']; ?></textarea>
						</td>
					</tr>
					<tr><td height=2></td></tr>
				</table>
			</td>
			<td  style="border-right: thin solid #ccc; border-top: thin solid #ccc; border-bottom: thin solid #ccc;" valign=top>
				<table border="0" cellpadding="0" cellspacing="1" width=100%>
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareBold align="right">Birth Date :&nbsp;&nbsp;</td>
						<td class=bare><input type="text" class="nInput3" id="spouse_bday" name="spouse_bday" style="width : 140px;" align="right" value="<?php print $_sres['sbday']; ?>"></td>
					</tr>
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareBold align="right">Occupation :&nbsp;&nbsp;</td>
						<td class=bare><input type="text" class="nInput3" id="spouse_occupation" name="spouse_occupation" style="width : 140px" align="right" value="<?php print $_sres['occupation']; ?>"></td>
					</tr>
					<tr><td colspan="9" height="5"></td></tr>
				</table>
			</td>
		</tr>	
	</table>
	<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr style="background-color :#cccccc">
			<td align=center width="15%"></td>
			<td align=center class=bareBold align="center" style="padding-top: 2px; padding-bottom: 2px; font-size: 12px;"><b>CHILDREN <i>(IF EMPLOYEE HAVE ANY)</i></b></td>
			<td align=right width="15%" style="padding-right:5px;"><a style="font-size: 10px;" href="javascript: newCRecord();">[ Add Record ]</a></td>
		</tr>
	</table>
	<div id="crecords">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<?php
				$check_crecord = mysql_fetch_array(mysql_query("select count(*) from hris.emp_crecord where emp_id = '$_GET[eid]';"));
				if($check_crecord[0] > 0) {
					print "<tr>";
					print "<td valign=\"top\">";
					print "<table width=100% cellpadding=0 cellspacing=1 >";
					print "<tr>";
					print "<td width=\"15%\" class=dgridHead align=center ><strong>First Name</strong></td>";
					print "<td width=\"15%\" class=dgridHead align=center ><strong>Middle Name</strong></td>";
					print "<td width=\"15%\" class=dgridHead align=center ><strong>Last Name</strong></td>";
					print "<td width=\"15%\" class=dgridHead align=center ><strong>Birth Date</strong></td>";
					print "<td width=\"10%\" class=dgridHead align=center ><strong>Gender</strong></td>";
					print "<td width=\"10%\" class=dgridHead align=center ><strong>Civil Status</strong></td>";
					print "<td width=\"20%\" class=dgridHead align=center ><strong>Occupation</strong></td>";
					print "</tr>";
					print "<tr bgcolor=\"#000000\" height=1><td colspan=14></td></tr>";
					print "</table>";
					print "</td></tr>";
					print "<tr>";
					print "<td width=100% valign=top style=\"border: thin solid #ccc;\">";
					print "<table width=100% cellspacing='0' cellpadding='0' onMouseOut=\"javascript:highlightTableRowVersionA(0);\">";
					$crecords = mysql_query("select record_id, fname, mname, lname, date_format(bday,'%m/%d/%Y') as bday, bday as bd8, gender, status, occupation from hris.emp_crecord where emp_id = '$_GET[eid]' order by bd8 asc;");
					$x = 1;
					while($_crow = mysql_fetch_array($crecords)) {
						if ($color == "#ffffff") { $mycolor = "#e6e6e6"; } else { $mycolor = "#ffffff"; }
															
						print "<tr bgcolor=\"$mycolor\" onMouseOver=\"javascript:highlightTableRowVersionA(this, '#95f0e8');\" title=\"Click to view or edit this record.\" onclick=\"view_crecord(".$_crow['record_id'].");\">";
						print "<td class=\"grid2\" width=3% align=center>" . $x++ . ".</td>";
						print "<td class=\"grid2\" width=\"12%\" align=left>$_crow[fname]</td>";
						print "<td class=\"grid2\" align=left width=\"15%\" style=\"padding-left: 10px;\">$_crow[mname]</td>";
						print "<td class=\"grid2\" align=left width=\"15%\" style=\"padding-left: 10px;\">$_crow[lname]</td>";
						print "<td align=center class=\"grid2\" width=\"15%\">$_crow[bday]</td>";
						print "<td align=center class=\"grid2\" width=\"10%\">$_crow[gender]</td>";
						print "<td align=left class=\"grid2\" width=\"10%\" style=\"padding-left: 10px;\">$_crow[status]</td>";
						print "<td align=left class=\"grid2\" width=\"20%\" style=\"padding-left: 10px;\">$_crow[occupation]&nbsp;</td>";
						print "</tr>";
						$color = $mycolor;
					}
						print "</table>";
						print "<td></tr>";
				} else { 
					print "<tr><td width=100% valign=top style=\"border: thin solid #ccc; padding-left: 10px\">";
					print "<b>&nbsp;</b>";
					print "</td>";
					print "</tr>";
				}
			?>
		</table>
	</div>
	<table cellpadding=0 cellspacing=1 width=100% border=0 align=center>
		<tr style="background-color :#cccccc">
			<td align=center class=bareBold align="center" style="padding-top: 4px; padding-bottom: 4px; font-size: 12px;"><b>FAMILY INFORMATION</i></b></td>
		</tr>
	</table>
	<table width=100% cellpadding=0 cellspacing=1>
		<tr>
			<td width=60% valign=top style="border-left: thin solid #ccc; border-top: thin solid #ccc; border-bottom: thin solid #ccc; padding-left: 10px">
				<table border="0" cellpadding="0" cellspacing="1" width=100%>
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareBold colspan=3><b>Mother's Maiden Name</b></td>
					</tr>
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareBold width="15%">&nbsp;&nbsp;&nbsp;</td>
						<td class=bare width=30%><input type="text" class="nInput3" id="mom_lname" name="mom_lname" style="width:95%" value="<?php print $_pres['mom_lname']; ?>"></td>
						<td class=bare style="width : 30px"></td>
						<td class=bare><input type="text" class="nInput3" id="mom_fname" name="mom_fname" style="width:90%" value="<?php print $_pres['mom_fname']; ?>"></td>
						<td class=bare style="width : 30px"></td>
						<td class=bare><input type="text" class="nInput3" id="mom_mname" name="mom_mname" style="width:90%" value="<?php print $_pres['mom_mname']; ?>"></td>
					</tr>
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareGray></td>
						<td class=bareGray align="center">(Last Name)</td>
						<td class=bareGray></td>
						<td class=bareGray align="center">(First Name)</td>
						<td class=bareGray></td>
						<td class=bareGray align="center">(Middle Name)</td>
					</tr>		
					<tr><td height=2></td></tr>
				</table>
				<table border="0" cellpadding="0" cellspacing="1" width=100%>
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareBold colspan=3><b>Father's Name</b></td>
					</tr>
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareBold width="15%">&nbsp;&nbsp;&nbsp;</td>
						<td class=bare width=30%><input type="text" class="nInput3" id="dad_lname" name="dad_lname" style="width:95%" value="<?php print $_pres['dad_lname']; ?>"></td>
						<td class=bare style="width : 30px"></td>
						<td class=bare><input type="text" class="nInput3" id="dad_fname" name="dad_fname" style="width:90%" value="<?php print $_pres['dad_fname']; ?>"></td>
						<td class=bare style="width : 30px"></td>
						<td class=bare><input type="text" class="nInput3" id="dad_mname" name="dad_mname" style="width:90%" value="<?php print $_pres['dad_mname']; ?>"></td>
					</tr>
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareGray></td>
						<td class=bareGray align="center">(Last Name)</td>
						<td class=bareGray></td>
						<td class=bareGray>(First Name)</td>
						<td class=bareGray></td>
						<td class=bareGray align="center">(Middle Name)</td>
					</tr>		
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareBold width="15%" valign=top>Address :&nbsp;&nbsp;&nbsp;</td>
						<td class=bare colspan=5>
							<textarea id="mdaddress" name="mdaddress" rows="1" style="width:98%"><?php print $_pres['address']; ?></textarea>
						</td>
					</tr>
				</table>
			</td>
			<td  style="border-right: thin solid #ccc; border-top: thin solid #ccc; border-bottom: thin solid #ccc;" valign=top>
				<table border="0" cellpadding="0" cellspacing="1" width=100%>
					<tr><td height=16></td></tr>
					<tr>
						<td class=bareBold align="right">Birth Date :&nbsp;&nbsp;</td>
						<td class=bare><input type="text" class="nInput3" id="mom_bday" name="mom_bday" style="width : 140px;" align="right" value="<?php print $_pres['mbday']; ?>"></td>
					</tr>
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareBold align="right">Occupation :&nbsp;&nbsp;</td>
						<td class=bare><input type="text" class="nInput3" id="mom_occupation" name="mom_occupation" style="width : 140px;" align="right" value="<?php print $_pres['mom_occupation']; ?>"></td>
					</tr>
					<tr><td colspan="9" height="5"></td></tr>
				</table>
				<table><tr><td height=4></td></tr></table>
				<table border="0" cellpadding="0" cellspacing="1" width=100%>
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareBold align="right">Birth Date :&nbsp;&nbsp;</td>
						<td class=bare><input type="text" class="nInput3" id="dad_bday" name="dad_bday" style="width : 140px;" align="right" value="<?php print $_pres['dbday']; ?>"></td>
					</tr>
					<tr><td height=2></td></tr>
					<tr>
						<td class=bareBold align="right">Occupation :&nbsp;&nbsp;</td>
						<td class=bare><input type="text" class="nInput3" id="dad_occupation" name="dad_occupation" style="width : 140px;" align="right" value="<?php print $_pres['dad_occupation']; ?>"></td>
					</tr>
					<tr><td colspan="9" height="5"></td></tr>
				</table>
			</td>
		</tr>	
	</table>
	<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr style="background-color :#cccccc">
			<td align=center width="15%"></td>
			<td align=center class=bareBold align="center" style="padding-top: 4px; padding-bottom: 4px; font-size: 12px;"><b>BROTHERS & SISTERS <i>(IF EMPLOYEE HAVE ANY)</i></b></td>
			<td align=right width="15%" style="padding-right:5px;"><a style="font-size: 10px;" href="javascript: newBRecord();">[ Add Record ]</a></td>
		</tr>
	</table>
	<div id="brecords">
		<table border="0" cellpadding="0" cellspacing="1" width="100%">
		<?php
			$check_brecord = mysql_fetch_array(mysql_query("select count(*) from hris.emp_brecord where emp_id='$_GET[eid]';"));
			if($check_brecord[0] > 0) {
				print "<tr>";
				print "<td valign=\"top\">";
				print "<table width=100% cellpadding=0 cellspacing=1 onMouseOut=\"javascript:highlightTableRowVersionA(0);\">";
				print "<tr>";
				print "<td width=\"15%\" class=dgridHead align=center ><strong>First Name</strong></td>";
				print "<td width=\"15%\" class=dgridHead bgcolor=\"#cdcdcd\" align=center ><strong>Middle Name</strong></td>";
				print "<td width=\"15%\" class=dgridHead bgcolor=\"#cdcdcd\" align=center ><strong>Last Name</strong></td>";
				print "<td width=\"15%\" class=dgridHead bgcolor=\"#cdcdcd\" align=center ><strong>Birth Date</strong></td>";
				print "<td width=\"10%\" class=dgridHead bgcolor=\"#cdcdcd\" align=center ><strong>Gender</strong></td>";
				print "<td width=\"10%\" class=dgridHead bgcolor=\"#cdcdcd\" align=center ><strong>Civil Status</strong></td>";
				print "<td width=\"20%\" class=dgridHead bgcolor=\"#cdcdcd\" align=center ><strong>Occupation</strong></td>";
				print "</tr>";
				print "<tr bgcolor=\"#000000\" height=1><td colspan=14></td></tr>";
				print "</table>";
				print "</td></tr>";
				print "<tr>";
				print "<td width=100% valign=top style=\"border: thin solid #ccc;\">";
				print "<table width=100% cellspacing='0' cellpadding='0'>";
				$brecords = mysql_query("select record_id, fname, mname, lname, date_format(bday,'%m/%d/%Y') as bday, bday as bd8, gender, status, occupation from hris.emp_brecord where emp_id = '$_GET[eid]' order by bd8 asc;");
				$i = 1;
				while($_brow = mysql_fetch_array($brecords)) {
					if ($color == "#ffffff") { $mycolor = "#e6e6e6";} else {$mycolor = "#ffffff";}
					print "<tr bgcolor=\"$mycolor\" onMouseOver=\"javascript:highlightTableRowVersionA(this, '#95f0e8');\" onclick=\"javascript: view_brecord(".$_brow['record_id'].");\");>";
					print "<td class=\"grid2\" width=3% align=center>" . $i++ . ".</td>";
					print "<td class=\"grid2\" width=\"12%\" align=left>$_brow[fname]</td>";
					print "<td class=\"grid2\" align=left width=\"15%\" style=\"padding-left: 15px;\">$_brow[mname]</td>";
					print "<td class=\"grid2\" align=left width=\"15%\" style=\"padding-left: 15px;\">$_brow[lname]</td>";
					print "<td align=center class=\"grid2\" width=\"15%\">$_brow[bday]</td>";
					print "<td align=center class=\"grid2\" width=\"10%\" style=\"padding-left: 15px;\">$_brow[gender]</td>";
					print "<td align=left class=\"grid2\" width=\"10%\" style=\"padding-left: 15px;\">$_brow[status]</td>";
					print "<td align=left class=\"grid2\" width=\"20%\" style=\"padding-left: 15px;\">$_brow[occupation]&nbsp;</td>";
					print "</tr>";
					$color = $mycolor;
				}
				print "</table>";
				print "<td></tr>";
			} else { 
				print "<tr><td width=100% valign=top style=\"border: thin solid #ccc; padding-left: 10px\">";
				print "<b>&nbsp;</b>";
				print "</td>";
				print "</tr>";
			}
		?>
		</table>
	</div>
	<table width=100%>
		<tr><td height=16></td></tr>
		<tr>
			<td align=left>
				<button type="button" onClick="saveFBackground(<?php echo $_GET[eid]; ?>);" class="buttonding"><img src="images/icons/floppy.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Save Changes</button>
				<button type="button" onClick="clearFBackground(<?php echo $_GET[eid]; ?>);" class="buttonding"><img src="images/delete.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Clear Employee's Family Backgroud</button>
			</td>
	</table>
	<div id="childinfo" style="padding: 10px; display: none;">
		<form name="frmChild" id="frmChild">
			<input type="hidden" id="c_rid" name="c_rid">
			<table align=center border=0 width=100% cellpadding=0 cellspacing=3>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;">Last Name :</td>
					<td align=left>
						<input type=text id="c_lname" name="c_lname" class="gridInput" style="width:80%; font-size: 11px;">
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;">First Name :</td>
					<td align=left>
						<input type=text id="c_fname" name="c_fname" class="gridInput" style="width:80%; font-size: 11px;">
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;">Middle Name :</td>
					<td align=left>
						<input type=text id="c_mname" name="c_mname" class="gridInput" style="width:80%; font-size: 11px;">
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;">Birth Date :</td>
					<td align=left>
						<input type=text id="c_bday" name="c_bday" class="gridInput" style="width:80%; font-size: 11px;">
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;" valign=top>Gender :</td>
					<td align=left>
						<select name="c_gender" id="c_gender" class="gridInput" style="width:80%; font-size: 11px;">
							<option value="M">- Male-</option>
							<option value="F">- Female -</option>
						</select>
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;" valign=top>Civil Status :</td>
					<td align=left>
						<select name="c_cstat" id="c_cstat" class="gridInput" style="width:80%; font-size: 11px;">
							<option value="Single">- Single -</option>
							<option value="Married">- Married -</option>
							<option value="Widow">- Widow/Widower -</option>
						</select>
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;">Occupation :</td>
					<td align=left>
						<input type=text id="c_occupation" name="c_occupation" class="gridInput" style="width:80%; font-size: 11px;">
					</td>
				</tr>
				<tr><td height=2></td></tr>
			</table>
		</form>
	</div>
	<div id="BroSis" style="padding: 10px; display: none;">
		<form name="frmBroSis" id="frmBroSis">
			<input type="hidden" id="b_rid" name="b_rid">
			<table align=center border=0 width=100% cellpadding=0 cellspacing=3>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;">Last Name :</td>
					<td align=left>
						<input type=text id="b_lname" name="b_lname" class="gridInput" style="width:80%; font-size: 11px;">
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;">First Name :</td>
					<td align=left>
						<input type=text id="b_fname" name="b_fname" class="gridInput" style="width:80%; font-size: 11px;">
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;">Middle Name :</td>
					<td align=left>
						<input type=text id="b_mname" name="b_mname" class="gridInput" style="width:80%; font-size: 11px;">
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;">Birth Date :</td>
					<td align=left>
						<input type=text id="b_bday" name="b_bday" class="gridInput" style="width:80%; font-size: 11px;">
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;" valign=top>Gender :</td>
					<td align=left>
						<select name="b_gender" id="b_gender" class="gridInput" style="width:80%; font-size: 11px;">
							<option value="M">- Male-</option>
							<option value="F">- Female -</option>
						</select>
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;" valign=top>Civil Status :</td>
					<td align=left>
						<select name="b_cstat" id="b_cstat" class="gridInput" style="width:80%; font-size: 11px;">
							<option value="Single">- Single -</option>
							<option value="Married">- Married -</option>
							<option value="Widow">- Widow/Widower -</option>
						</select>
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td class=spandix-l align=right style="padding-right: 10px;">Occupation :</td>
					<td align=left>
						<input type=text id="b_occupation" name="b_occupation" class="gridInput" style="width:80%; font-size: 11px;">
					</td>
				</tr>
				<tr><td height=2></td></tr>
			</table>
		</form>
	</div>
</body>
</html>
<?php mysql_close($con);