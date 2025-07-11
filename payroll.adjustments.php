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

		$(function() { $("#date").datepicker();});
		function selectFile(obj) {
			$(obj).closest("tr").siblings().removeClass("diffcolor");
			$(obj).toggleClass("diffcolor");
			tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); sPO = tmp_obj[1];
		}
		
		function viewFile() {
			if(sPO == "") {
				parent.sendErrorMessage("Please select record to view.");
			} else {
				$.post("payroll.datacontrol.php", { mod: "viewAdjustment", fid: sPO, sid: Math.random() }, function(data) {
					$("#fid").val(data['record_id']);
					$("#id_no").val(data['id_no']);
					$("#date").val(data['d8']);
					$("#amount").val(data['amt']);
					$("#remarks").val(data['remarks']);
					$("#leaveDetails").dialog({
						title: "Salary Adjustment", 
						width: 380, 
						height: 280, 
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
				    url = "mod=saveAdjustment&"+url;
				$.post("payroll.datacontrol.php", url, function() { alert("Record Successfully Saved!"); parent.showPayAdjust(); });
			}
		}

		function deleteFile() {
			if($("#fid").val()!="") {
				if(confirm("Are you sure you want delete this holiday file?") == true) {
					$.post("payroll.datacontrol.php", { mod: "deleteAdjust", rid: $("#fid").val(), sid: Math.random() },function() { alert("Record Successfully Delete!"); parent.showPayAdjust(); });
				}
			}
		}

		function newRecord() {
			$(document.frminvoices)[0].reset();
			$("#leaveDetails").dialog({
				title: "New Salary Adjustment", 
				width: 380, 
				height: 280, 
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
		<td class="dgridHead" align=left width=80><b>DATE</b></td>
		<td class="dgridHead" align=center width=80><b>AMOUNT</b></td>
		<td class="dgridHead" align=center><b>REASON</b></td>
		<td class="dgridHead" width=16>&nbsp;</td>
	</tr>
</table>
<div id="details" style="height:350px; overflow: auto;">
<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">
<?php
  $_i = dbquery("select a.record_id, a.id_no, concat(b.lname,', ',fname) as emp, date_format(`date`,'%m/%d/%Y') as d8, amount, remarks from hris.e_adjustments a left join hris.e_master b on a.id_no = b.id_no where b.filestatus = 'Active' order by `date` asc, b.lname;");
	while($row = mysql_fetch_array($_i)) {
		if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
		echo "<tr bgcolor=\"$bgC\" id='obj_$row[record_id]' onMouseOver=\"javascript: highlightTableRowVersionA(this, '#e6f0fa');\" onclick=\"javascript: selectFile(this);\">
				<td class=\"grid\" valign=top align=left width=220>(".$row['id_no'].") ".$row['emp']."</td>
				<td class=\"grid\" valign=top align=left width=80>".$row['d8']. "</td>
				<td class=\"grid\" valign=top align=center width=80>".number_format($row['amount'],2)."</td>
				<td class=\"grid\" valign=top align=center>".$row['remarks']."</td>
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
			<button onClick="newRecord();" class="buttonding" id="btn_rsv"><img src="images/icons/add.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;New Record</b></button>
			<button onClick="viewFile();" class="buttonding" id="btn_rsv"><img src="images/icons/bill.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;View Record Details</b></button>
			<button type="button" class="buttonding" style="font-size: 11px;" onclick="parent.showPayAdjust();"><img src="images/icons/refresh.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Refresh List</b></button>
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
							$a = dbquery("select id_no, concat(lname,', ',fname) from hris.e_master where id_no !='' and filestatus = 'Active' and company = '$_SESSION[company]' order by lname;");
							while(list($id,$emp) = mysql_fetch_array($a)) {
								echo "<option value='$id'>$emp</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Date :</td>
				<td align=left>
					<input type=text id="date" name="date" class="gridInput" style="width:80%; font-size: 11px;">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Amount (+/-) :</td>
				<td align=left>
					<input type=text id="amount" name="amount" class="gridInput" style="width:80%; font-size: 11px;">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;" valign=top>Remarks :</td>
				<td align=left>
					<textarea id="remarks" name="remarks" class="gridInput" rows=2 style="width:80%;font-size: 11px;"></textarea>
				</td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
	</form>
</div>
</body>
</html>
<?php mysql_close($con);