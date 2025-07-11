<?php
	session_start();
	include("includes/dbUSE.php");	
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>$co[company_name] ERP System Ver. 1.0b</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="style/dropMenu.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="js/jquery.dialogextend.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>

		$(function() { $("#dtf").datepicker();  $("#dt2").datepicker();});
		function selectFile(obj) {
			$(obj).closest("tr").siblings().removeClass("diffcolor");
			$(obj).toggleClass("diffcolor");
			tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); sPO = tmp_obj[1];
		}
		
		function viewFile() {
			if(sPO == "") {
				parent.sendErrorMessage("Please select record to view.");
			} else {
				$.post("payroll.datacontrol.php", { mod: "viewLeaveFile", fid: sPO, sid: Math.random() }, function(data) {
					$("#fid").val(data['record_id']);
					$("#id_no").val(data['id_no']);
					$("#dtf").val(data['date1']);
					$("#dt2").val(data['date2']);
					$("#length").val(data['length']);
					$("#leave_type").val(data['type']);
					$("#payable").val(data['with_pay']);
					$("#remarks").val(data['reason']);
					$("#approved_by").val(data['approved_by']);
					$("#leaveDetails").dialog({
						title: "Employee Leave File", 
						width: 380, 
						height: 420, 
						resizable: false, 
							buttons: {
							"Update Record":  function() { saveFile(); },
							"Delete File": function() { deleteFile(); }
						}
					});	
				},"json");
			}
		}

		function saveFile() {
			if(confirm("Are you sure you want to save changes made?") == true) {
				var url = $(document.frminvoices).serialize();
				    url = "mod=saveLeave&"+url;
				$.post("payroll.datacontrol.php", url, function() { alert("Record Successfully Saved!"); parent.showLA(); });
			}
		}

		function deleteFile() {
			if($("#fid").val()!="") {
				if(confirm("Are you sure you want delete this holiday file?") == true) {
					$.post("payroll.datacontrol.php", { mod: "deleteHoliday", rid: $("#fid").val(), sid: Math.random() },function() { alert("Record Successfully Delete!"); parent.showHolidays(); });
				}
			}
		}

		function newRecord() {
			$(document.frminvoices)[0].reset();
			$("#leaveDetails").dialog({
				title: "Employee Leave File", 
				width: 380, 
				height: 420, 
				resizable: false, 
					buttons: {
					"Save Record":  function() { saveFile(); }
				}
			});
		}
	</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table width='100%' class="tgrid" cellpadding=0 cellspacing=0>
	<tr>
		<td class="dgridHead" align=left width=220><b>EMPLOYEE</b></td>
		<td class="dgridHead" align=left width=150><b>DATE OF ABSENCE</b></td>
		<td class="dgridHead" align=center width=50><b>TYPE</b></td>
		<td class="dgridHead" align=center><b>NO. OF DAYS</b></td>
		<td class="dgridHead" width=16>&nbsp;</td>
	</tr>
</table>
<div id="details" style="height:350px; overflow: auto;">
<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">
<?php
	$_i = dbquery("select a.record_id, a.id_no, concat(b.lname,', ',fname) as emp, date_format(dtf,'%m/%d/%Y') as date1, date_format(dt2,'%m/%d/%Y') as date2, `length`, `type` from hris.e_leaves a left join hris.e_master b on a.id_no = b.id_no order by dtf, b.lname;");
	while($row = mysql_fetch_array($_i)) {
		if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
		echo "<tr bgcolor=\"$bgC\" id='obj_$row[record_id]' onMouseOver=\"javascript: highlightTableRowVersionA(this, '#e6f0fa');\" onclick=\"javascript: selectFile(this);\">
				<td class=\"grid\" valign=top align=left width=220>(".$row['id_no'].") ".$row['emp']."</td>
				<td class=\"grid\" valign=top align=left width=150>".$row['date1']. " - " . $row['date2'] . "</td>
				<td class=\"grid\" valign=top align=center width=50>".$row['type']."</td>
				<td class=\"grid\" valign=top align=center>".$row['length']."</td>
		</tr>"; $i++;
	}
	if($i < 17) {	
		for($i; $i <= 17; $i++) {	if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
			echo "<tr bgcolor=\"$bgC\">
					<td class=\"grid\" colspan=9>&nbsp;</td>
				  </tr>";
		}
	}
?>
	</table>
</div>
<table width=100% cellpadding=5 cellspacing=0>
	<tr>
		<td style="padding: 5px;">
			<button onClick="newRecord();" class="buttonding" id="btn_rsv"><img src="images/icons/add.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;New Leave File</b></button>
			<button onClick="viewFile();" class="buttonding" id="btn_rsv"><img src="images/icons/bill.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;View File Details</b></button>
			<button type="button" class="buttonding" style="font-size: 11px;" onclick="parent.showLA();"><img src="images/icons/refresh.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Refresh List</b></button>
		</td>
	</tr>
 </table>
 <div id="leaveDetails" style="padding: 10px; display: none;">
	<form name="frminvoices" id="frminvoices">
		<input type="hidden" id="fid" name="fid">
		<table align=center border=0 width=100% cellpadding=0 cellspacing=3>
			<tr><td class=spandix-l align=right style="padding-right: 10px;" valign=top>Employee :</td>
				<td align=left>
					<select name="id_no" id="id_no" class="gridInput" style="width:80%; font-size: 11px;">
						<?php
							$a = dbquery("select id_no, concat(lname,', ',fname) from hris.e_master where company = '$_SESSION[company]' and `filestatus` = 'Active' and id_no !='' order by lname;");
							while(list($id,$emp) = mysql_fetch_array($a)) {
								echo "<option value='$id'>$emp</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Date From:</td>
				<td align=left>
					<input type=text id="dtf" name="dtf" class="gridInput" style="width:80%; font-size: 11px;">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Date To:</td>
				<td align=left>
					<input type=text id="dt2" name="dt2" class="gridInput" style="width:80%; font-size: 11px;">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Length (in days):</td>
				<td align=left>
					<input type=text id="length" name="length" class="gridInput" style="width:80%; font-size: 11px;">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;" valign=top>Type :</td>
				<td align=left>
					<select name="leave_type" id="leave_type" class="gridInput" style="width:80%; font-size: 11px;">
						<option value="VL">- Vacation Leave -</option>
						<option value="SL">- Sick Leave -</option>
						<option value="EL">- Emergency Leave -</option>
						<option value="ML">- Maternity Leave -</option>
						<option value="PL">- Paternity Leave -</option>
						<option value="OL">- Ordinary Leave -</option>
						<option value="AWOL">- AWOL -</option>
					</select>
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;" valign=top>With Pay :</td>
				<td align=left>
					<select name="payable" id="payable" class="gridInput" style="width:80%; font-size: 11px;">
						<option value="Y">- Yes -</option>
						<option value="N">- No -</option>
					</select>
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;" valign=top>Reasons Stated :</td>
				<td align=left>
					<textarea id="remarks" name="remarks" class="gridInput" rows=2 style="width:80%;font-size: 11px;"></textarea>
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Approved By :</td>
				<td align=left>
					<input type=text id="approved_by" name="approved_by" class="gridInput" style="width:80%; font-size: 11px;">
				</td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
	</form>
</div>
</body>
</html>
<?php mysql_close($con);