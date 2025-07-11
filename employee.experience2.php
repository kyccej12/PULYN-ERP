<?php
	session_start();
	include("includes/dbUSE.php");	
	$emp_idno = $_GET[eid];
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

	function addRecord() {
		$(document.frmWorkEx)[0].reset();
		$("#rid").val('');
		$("#workExperience").dialog({
				title: "Work Experience", 
				width: 440, 
				height: 420, 
				resizable: false, 
					buttons: {
					"Add New Record":  function() { saveRecord(); }
				}
		});	
	}

	function viewRecord(rid) {
		$.post("payroll.datacontrol.php", { mod: "viewExpRecord2", rid: rid, sid: Math.random() }, function(data) {
			$("#rid").val(data['record_id']);
			$("#previous_title").val(data['previous_title']);
			$("#previous_start").val(data['pstart']);
			$("#previous_end").val(data['pend']);
			$("#previous_rate").val(parent.kSeparator(data['previous_rate']));
			$("#new_title").val(data['new_title']);
			$("#new_start").val(data['nstart']);
			$("#new_responsibilities").val(data['new_responsibilities']);
			$("#previous_responsibilities").val(data['previous_responsibilities']);
			$("#new_rate").val(parent.kSeparator(data['new_rate']));
			$("#workExperience").dialog({
				title: "Work Experience", 
				width: 440, 
				height: 420, 
				resizable: false, 
					buttons: {
					"Update Record":  function() { saveRecord(); },
					"Delete Record":  function() { deleteRecord(); }
				}
		});	
		},"json");

	}

	function saveRecord() {
		var msg = "";
		if($("#previous_title").val() == "") { msg = msg + "1"; }
		if($("#previous_start").val() == "") { msg = msg + "1"; }
		if($("#previous_end").val() == "") { msg = msg + "1"; }
		if($("#previous_responsibilities").val() == "") { msg = msg + "1"; }
		if($("#new_title").val() == "") { msg = msg + "1"; }
		if($("#new_start").val() == "") { msg = msg + "1"; }
		if($("#new_responsibilies").val() == "") { msg = msg + "1"; }

		if(msg!='') {
			parent.sendErrorMessage("All fields in this form must be filled up. Please check your inputs and try saving this document again");
		} else {
			var url = $(document.frmWorkEx).serialize() + "&mod=saveWorkExpInternal&sid=" + Math.random();
			$.post("payroll.datacontrol.php",url,function(data) {
				$("#details").html(data);
				$(document.frmWorkEx)[0].reset();
				$("#workExperience").dialog("close");
			},"html");
		}

	}

	function deleteRecord() {
		if(confirm("Are you sure you want to delete this record?") == true) {
			$.post("payroll.datacontrol.php", { mod: "deleteExpRecordInternal", rid: $("#rid").val(), emp_idno: $("#emp_idno").val(), sid: Math.random() }, function(data) {
				$("#details").html(data);
				$(document.frmWorkEx)[0].reset();
				$("#workExperience").dialog("close");
			},"html");
		}
	}

	$(function() { 
			$("#previous_start").datepicker({ changeMonth: true, changeYear: true, yearRange: '1960:' + new Date().getFullYear()});
			$("#previous_end").datepicker({ changeMonth: true, changeYear: true, yearRange: '1960:' + new Date().getFullYear()}); 
			$("#new_start").datepicker({ changeMonth: true, changeYear: true, yearRange: '1960:' + new Date().getFullYear()}); 
	});
</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
			<table border="0" cellpadding="0" cellspacing="0" width=100%>
				<tr>
					<td align=center class="dgridhead" width="10%" style='border-right: 1px solid #ededed;'>PREV. JOB TITLE</td>
					<td align=center class="dgridhead" width="15%" style='border-right: 1px solid #ededed;'>COVERED PERIOD</td>
					<td align=center class="dgridhead" width="25%" style='border-right: 1px solid #ededed;'>JOB RESPONSIBILITIES</td>
					<td align=center class="dgridhead" width="10%" style='border-right: 1px solid #ededed;'>NEW JOB TITLE</td>
					<td align=center class="dgridhead" width="10%" style='border-right: 1px solid #ededed;'>DATE STARTED</td>
					<td align=center class="dgridhead" style='border-right: 1px solid #ededed;'>NEW JOB RESPONSIBILITIES</td>
					<td align=center class="dgridhead" width="15">&nbsp;</td>
				</tr>
				<tr><td height=1></td></tr>
				<tr bgcolor="#000000" height=1><td colspan=7></td></tr>
			</table>
			<div id="details" style="height:340px; overflow: auto;">
				<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">
				<?php
					$i = 0;
					
					$getRec = dbquery("select record_id,previous_title,concat(date_format(previous_start,'%m/%d/%Y'),' ',date_format(previous_end,'%m/%d/%Y')) as covered_period, previous_responsibilities, new_title, date_format(new_start,'%m/%d/%Y') as xstart, new_responsibilities from hris.emp_internalhistory where emp_id='$emp_idno';");
					while($row = mysql_fetch_array($getRec)) {
						if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
						$jresp = explode(";",$row['previous_responsibilities']);
						$kresp = explode(";",$row['new_responsibilities']);
						echo "<tr bgcolor=\"$bgC\" onmouseover=\"highlightTableRowVersionA(this, '#95f0e8');\" onclick='javascript: viewRecord(".$row['record_id'].");'>
								<td class=dgridbox align=left width=\"10%\" valign=top>$row[previous_title]</td>
								<td class=dgridbox align=center width=\"16%\" valign=top>$row[covered_period]</td>
								<td class=dgridbox align=left width=25%>";
									$z = 1;
									foreach($jresp as $responsibilities) {
										print "&bull; " . $responsibilities . "<br>";
									}
						  echo "</td>
								<td class=dgridbox align=center width=\"10%\" valign=top>$row[new_title]</td>
								<td class=dgridbox align=center width=\"10%\" valign=top>$row[xstart]</td>
								<td class=dgridbox align=left>";
									$z = 1;
									foreach($kresp as $responsibilities2) {
										print "&bull; " . $responsibilities2 . "<br>";
									}
						  echo "</td>
							</tr>"; $i++; 
						}
					if($i < 20) {
						for($i; $i <= 20; $i++) {
							if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
							echo "<tr bgcolor='$bgC'><td colspan='7'>&nbsp;</td></tr>";
						}
					}
				?>
				</table>
			</div>
			<table width="100%"  cellspacing="0" cellpadding="0" style="padding-left: 5px; background-color: #7f7f7f;">
				<tr><td height=8></td></tr>
				<tr>
					<td>
						<button onClick="addRecord();" class="buttonding" id="btn_rsv"><img src="images/icons/add.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Add New Record</button>
					</td>
				</tr>
				<tr><td height=8></td></tr>
			</table>
		</td>
	</tr>
 </table>
 <div id="workExperience" style="display: none;">
 	<form id="frmWorkEx" name="frmWorkEx">
 		<input type="hidden" name="emp_idno" id="emp_idno" value="<?php echo $emp_idno; ?>">
 		<input type="hidden" name="rid" id="rid">
	 	<table width=80% align=center>
			<tr><td height=16></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;">Prev. Job Title&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="previous_title" name="previous_title" style="width:100%"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;">Date From&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="previous_start" name="previous_start" style="width:100%"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;">To&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="previous_end" name="previous_end" style="width:100%"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;">Previous Rate&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="previous_rate" name="previous_rate" style="width:100%"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;" valign=top>Prev. Job Responsibilities&nbsp;:</td>
				<td align=left><textarea style="width: 100%; font-size: 10px;" rows=3 id="previous_responsibilities" name="previous_responsibilities" ></textarea></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;">New Job Title&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="new_title" name="new_title" style="width:100%"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;">Date Started&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="new_start" name="new_start" style="width:100%"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;" valign="top">New Job Responsibilities&nbsp;:</td>
				<td align=left><textarea name="new_responsibilities" id="new_responsibilities" rows="3" style="width: 100%; font-size: 10px;"></textarea></td>
			</tr>
			<tr><td></td><td class=bareGray>Note: Please separate every job responsibility with a semi-colon.</td></tr>
			<tr><td height=2></td></tr>
				<tr><td class=bareBold width=40% align=right style="padding-right: 15px;">New Rate&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="new_rate" name="new_rate" style="width:100%"></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
	</form>
 </div>
</body>
</html>
<?php mysql_close($con);