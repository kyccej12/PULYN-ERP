<?php
	session_start();
	include("includes/dbUSE.php");	
	$emp_idno = $_GET['eid'];
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
				height: 360, 
				resizable: false, 
					buttons: {
					"Add New Record":  function() { saveRecord(); }
				}
		});	
	}

	function viewRecord(rid) {
		$.post("payroll.datacontrol.php", { mod: "viewExpRecord", rid: rid, sid: Math.random() }, function(data) {
			$("#rid").val(data['record_id']);
			$("#company").val(data['company']);
			$("#address").val(data['address']);
			$("#telno").val(data['tel_no']);
			$("#title").val(data['job_title']);
			$("#responsibility").val(data['job_responsibility']);
			$("#from").val(data['dtf']);
			$("#to").val(data['dt2']);
			$("#workExperience").dialog({
				title: "Work Experience", 
				width: 440, 
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
		if($("#company").val() == "") { msg = msg + "- Please specify the company name.<br/>"; }
		if($("#address").val() == "") { msg = msg + "- Please specify the company's address.<br/>"; }
		if($("#job_title").val() == "") { msg = msg + "- Please specify your job title on this work experience.<br/>"; }
		if($("#job_responsibility").val() == "") { msg = msg + "- Please specify your job responsibilities<br/>"; }
		if($("#from").val() == "") { msg = msg + "- Please specify the day you started working on this work experience<br/>"; }
		if($("#to").val() == "") { msg = msg + "- Please specify your last day of work in this work experience<br/>"; }

		if(msg!='') {
			parent.sendErrorMessage(msg);
		} else {
			var url = $(document.frmWorkEx).serialize() + "&mod=saveWorkExp&sid=" + Math.random();
			$.post("payroll.datacontrol.php",url,function(data) {
				$("#details").html(data);
				$(document.frmWorkEx)[0].reset();
				$("#workExperience").dialog("close");
			},"html");
		}

	}

	function deleteRecord() {
		if(confirm("Are you sure you want to delete this record?") == true) {
			$.post("payroll.datacontrol.php", { mod: "deleteExpRecord", rid: $("#rid").val(), emp_idno: $("#emp_idno").val(), sid: Math.random() }, function(data) {
				$("#details").html(data);
				$(document.frmWorkEx)[0].reset();
				$("#workExperience").dialog("close");
			},"html");
		}
	}

	$(function() { 
			$("#from").datepicker({ changeMonth: true, changeYear: true, yearRange: '1960:' + new Date().getFullYear()});
			$("#to").datepicker({ changeMonth: true, changeYear: true, yearRange: '1960:' + new Date().getFullYear()}); 
	});
</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
			<table border="0" cellpadding="0" cellspacing="0" width=100%>
				<tr>
					<td align=left class="dgridhead" width="25%">COMPANY</td>
					<td align=left class="dgridhead" width="20%">COVERED PERIOD</td>
					<td align=left class="dgridhead" width="15%">JOB TITLE</td>
					<td align=left class="dgridhead">JOB RESPONSIBILITIES</td>
					<td align=center class="dgridhead" width="18">&nbsp;</td>
				</tr>
			</table>
			<div id="details" style="height:400px; overflow: auto;">
				<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">
				<?php
					$i = 0;
					
					$getRec = dbquery("select record_id,company,address,job_title,job_responsibility,concat(date_format(datefrom,'%m/%d/%Y'),' - ',date_format(dateto,'%m/%d/%Y')) as covered_period from hris.emp_emphistory where emp_id = '$emp_idno';");
					while($row = mysql_fetch_array($getRec)) {
						if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
						$jresp = explode(";",$row['job_responsibility']);
						echo "<tr bgcolor=\"$bgC\" onmouseover=\"highlightTableRowVersionA(this, '#95f0e8');\" onclick='javascript: viewRecord(".$row['record_id'].");'>
								<td class=dgridbox align=left width=\"25%\" valign=top>&nbsp;&nbsp;$row[company]</td>
								<td class=dgridbox align=left width=\"20%\" valign=top>$row[covered_period]</td>
								<td class=dgridbox align=left width=\"15%\" valign=top>$row[job_title]</td>
								<td class=dgridbox align=left>";
									$z = 1;
									foreach($jresp as $responsibilities) {
										print "&bull; " . $responsibilities . "<br>";
									}
								"</td>
							</tr>"; $i++; 
						}
					if($i < 20) {
						for($i; $i <= 20; $i++) {
							if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
							echo "<tr bgcolor='$bgC'><td colspan='6'>&nbsp;</td></tr>";
						}
					}
				?>
				</table>
			</div>
			<table width="100%"  cellspacing="0" cellpadding="0" style="padding-left: 5px;">
				<tr><td height=8></td></tr>
				<tr>
					<td>
						<button onClick="addRecord();" class="buttonding" id="btn_rsv"><img src="images/icons/add.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Add New Record</button>
					</td>
				</tr>
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
			<tr><td class=bareBold width=30% align=right style="padding-right: 15px;">Company&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="company" name="company" style="width:100%"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=30% align=right style="padding-right: 15px;" valign=top>Address&nbsp;:</td>
				<td align=left><textarea style="width: 100%; font-size: 10px;" rows=1 id="address" name="address" ></textarea></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=30% align=right style="padding-right: 15px;">Tel No.&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="telno" name="telno" style="width:100%"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=30% align=right style="padding-right: 15px;">Job Title&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="title" name="title" style="width:100%" value="<?php echo $res['job_title']; ?>"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=30% align=right style="padding-right: 15px;" valign="top">Responsibilities&nbsp;:</td>
				<td align=left><textarea name="responsibility" id="responsibility" rows="3" style="width: 100%; font-size: 10px;"></textarea></td>
			</tr>
			<tr><td></td><td class=bareGray>Note: Please separate every job responsibility with a semi-colon.</td></tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=30% align=right style="padding-right: 15px;">From&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="from" name="from" style="width:80%"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=30% align=right style="padding-right: 15px;">To&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="to" name="to" style="width:80%"></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
	</form>
 </div>
</body>
</html>
<?php mysql_close($con);