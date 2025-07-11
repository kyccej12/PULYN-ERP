<?php
	session_start();
	include("includes/dbUSE.php");	
	$emp_idno = $_GET[eid];
	
	/* UPLOAD FILE SECTION */
	if(isset($_POST['upload'])) {
		$uploadDir = "certificates/";
		
		$doc_type = $_POST['doc_type'];
		$doc_date = $_POST['doc_date'];
		$remarks = $_POST['remarks'];
		
		$fileName = $_FILES['userfile']['name'];
		$tmpName = $_FILES['userfile']['tmp_name'];
		$fileSize = $_FILES['userfile']['size'];
		$fileType = $_FILES['userfile']['type'];
		
		/* CHANGE UNIQUE FILENAME TO PREVENT DUPLICATION */
		$ext = substr(strrchr($fileName, "."), 1);
		$randName = md5(rand() * time());
		$newFileName = $randName . "." . $ext;

		$filePath = $uploadDir . $newFileName;
		$result = move_uploaded_file($tmpName, $filePath);
		if (!$result) {
			$error = 1;
		exit;
		}
		
		if(!get_magic_quotes_gpc())
		{
			$fileName = addslashes($fileName);
			$filePath = addslashes($filePath);
		}
		
		$query = "insert ignore into hris.emp_certificates (emp_id, doc_title, doc_date, doc_description, filename, filetype, filesize, filepath, date_uploaded) values ('$emp_idno','$_POST[doc_title]','".formatDate($_POST['doc_date'])."','".mysql_real_escape_string($_POST['doc_description'])."','$newFileName','$fileType','$fileSize','$filePath',now());";
		mysql_query($query);
	
	}
	
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
		$(document.frmCertificates)[0].reset();
		$("#rid").val('');
		$("#workExperience").dialog({
				title: "Upload 201 File", 
				width: 440, 
				height: 340, 
				resizable: false, 
					buttons: {
					"Add New Record":  function() { saveRecord(); }
				}
		});	
	}

	function saveRecord() {
		var msg = "";
		if($("#doc_title").val() == "") { msg = msg + "1"; }
		if($("#doc_date").val() == "") { msg = msg + "1"; }
		if($("#doc_description").val() == "") { msg = msg + "1"; }
		if($("#userfile").val() == "") { msg = msg + "1"; }

		if(msg!='') {
			parent.sendErrorMessage("All fields in this form must be filled up. Please check your inputs and try saving this document again");
		} else {
			$(document.frmCertificates).submit();
		}
	}
	
	function editFile(rid) {
		$.post("payroll.datacontrol.php", { mod: "viewCertificate", rid: rid, sid: Math.random() }, function(data) {
			$("#xrid").val(data['record_id']);
			$("#xdoc_title").val(data['doc_title'])
			$("#xdoc_date").val(data['xd8']);
			$("#xdoc_description").val(data['doc_description']);
			$("#xfilename").val(data['filepath']);
			$("#xview").dialog({
				title: "Update File Information", 
				width: 440, 
				height: 340, 
				resizable: false, 
					buttons: {
					"Update File Info.":  function() { updateFileInfo(); }
				}
		});	
		},"json");
	}
	
	function updateFileInfo() {
		if(confirm("Are you sure you want to update this record?") == true) {
			var url = $(document.viewCertificate).serialize();
		        url = url + "&mod=updateCertificate&sid="+Math.random()+"";
			$.post("payroll.datacontrol.php", url, function(data) {
				alert("Record Successfully Updated");
				$("#details").html(data);
				$(document.viewCertificate)[0].reset();
				$("#xview").dialog("close");
			},"html");
		}
	}
	
	function viewFile(record_id) {
		document.readFile.fileid.value = record_id;
		document.readFile.submit();
	}
	
	function delFile(rid) {
		if(confirm("Are you sure you want to delete this record?") == true) {
			$.post("payroll.datacontrol.php", { mod: "deleteCertFile", rid: rid, emp_idno: $("#emp_idno").val(), sid: Math.random() }, function(data) {
				$("#details").html(data);
			},"html");
		}
	}

	$(function() { 
		$("#doc_date").datepicker({ changeMonth: true, changeYear: true, yearRange: '1960:' + new Date().getFullYear()});
		$("#xdoc_date").datepicker({ changeMonth: true, changeYear: true, yearRange: '1960:' + new Date().getFullYear()});
		if('<?php echo $error; ?>' == 1) { parent.sendErrorMessage("The system failed to upload the file you submitted. Please contact Systems Administrator to resolve this issue."); }		
	});
	
</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
			<table border="0" cellpadding="0" cellspacing="0" width=100%>
				<tr>
					<td align=center class="dgridhead" width="150" style='border-right: 1px solid #ededed;'>DOC TITLE</td>
					<td align=center class="dgridhead" width="120" style='border-right: 1px solid #ededed;'>DOC DATE</td>
					<td align=center class="dgridhead" width="340" style='border-right: 1px solid #ededed;'>DOC DESCRIPTION</td>
					<td align=center class="dgridhead" width="140" style='border-right: 1px solid #ededed;'>DATE UPLOADED</td>
					<td align=center class="dgridhead" style='border-right: 1px solid #ededed;'></td>
					<td align=center class="dgridhead" width="8">&nbsp;</td>
				</tr>
				<tr><td height=1></td></tr>
				<tr bgcolor="#000000" height=1><td colspan=7></td></tr>
			</table>
			<div id="details" style="height:390px; overflow: auto;">
				<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">
				<?php
					$i = 0;
					
					$getRec = dbquery("select record_id, doc_title, date_format(doc_date,'%m/%d/%Y') as doc_date, doc_description, date_format(date_uploaded,'%m/%d/%Y %r') as uploaded from hris.emp_certificates where emp_id='$emp_idno';");
					while($row = mysql_fetch_array($getRec)) {
						if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
						$jresp = explode(";",$row['previous_responsibilities']);
						$kresp = explode(";",$row['new_responsibilities']);
						echo "<tr bgcolor=\"$bgC\">
								<td class=dgridbox align=left width=\"150\">$row[doc_title]</td>
								<td class=dgridbox align=center width=\"120\">$row[doc_date]</td>
								<td class=dgridbox align=left width=\"340\">$row[doc_description]</td>
								<td class=dgridbox align=center width=\"140\">$row[uploaded]</td>
								<td class=dgridbox align=center><a href=\"#\" onclick=\"delFile($row[record_id]);\" title='Delete Record'><img src='images/icons/bin.png' width=18 height=18 align=absmiddle /></a>&nbsp;&nbsp;<a href=\"#\" onclick=\"viewFile($row[record_id]);\" title='Download File'><img src='images/icons/xdownload.png' width=18 height=18 align=absmiddle /></a>&nbsp;&nbsp;<a href=\"#\" onclick=\"editFile($row[record_id]);\" title='Edit Record'><img src='images/icons/edit.png' width=18 height=18 align=absmiddle /></a></td>
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
 	<form enctype="multipart/form-data" id="frmCertificates" name="frmCertificates"  action="employee.certificates.php?eid=<?php echo $_GET['eid']; ?>" method="POST">
 		<input type="hidden" name="emp_idno" id="emp_idno" value="<?php echo $emp_idno; ?>">
 		<input type="hidden" name="rid" id="rid">
		<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
		<input type="hidden" name="upload" value="upload">
	 	<table width=80% align=center>
			<tr><td height=16></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;">Doc. Title&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="doc_title" name="doc_title" style="width:100%"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;">Doc Date&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="doc_date" name="doc_date" style="width:100%"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;" valign=top>Document Description&nbsp;:</td>
				<td align=left><textarea style="width: 100%; font-size: 10px;" rows=6 id="doc_description" name="doc_description" ></textarea></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;">File Path :</td>
				<td align=left><input type=file id="userfile" name="userfile" style="width:100%; font-size: 10px;"></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
	</form>
 </div>
 
  <div id="xview" style="display: none;">
 	<form id="viewCertificate" name="viewCertificate">
 		<input type="hidden" name="xemp_idno" id="xemp_idno" value="<?php echo $emp_idno; ?>">
		<input type="hidden" name="xrid" id="xrid">
	 	<table width=80% align=center>
			<tr><td height=16></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;">Doc. Title&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="xdoc_title" name="xdoc_title" style="width:100%"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;">Doc Date&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="xdoc_date" name="xdoc_date" style="width:100%"></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareBold width=40% align=right style="padding-right: 15px;" valign=top>Document Description&nbsp;:</td>
				<td align=left><textarea style="width: 100%; font-size: 10px;" rows=6 id="xdoc_description" name="xdoc_description" ></textarea></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr>
				<td class=bareBold width=40% align=right style="padding-right: 15px;" valign=top>File Location&nbsp;:</td>
				<td align=left><input class="nInput3" type=text id="xfilename" name="xfilename" style="width:100%" readonly></td>
			</tr>
		</table>
	</form>
 </div>
 
 <form name="readFile" action="employee.viewcertificate.php" method="POST"  target="_blank">
	<input type="hidden" name="viewFlag" value=1>
	<input type="hidden" name="fileid">
</form>
</body>
</html>
<?php mysql_close($con);