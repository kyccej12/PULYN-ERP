<?php
	require_once '../handlers/_generics.php';
	$o = new _init;
	
?>
<html>
<head>
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" src="../ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript">
		function importData() {
			if($("#cutoff").val() == "") {
				alert("Error: You must specify cut-off period/log file.");
			} else {
				$.post("hrd.checkrawexists.php", { mod: "checkBio", period: $("#cutoff").val() }, function(data) {
					if(data == "shubet") {
						if(confirm("Error: Cut-off period specified has already been imported into the system. Do you want to process raw logs again?") == true) {
							$.post("checkrawexists.php", { mod: "checkFinal", period: $("#cutoff").val() }, function(data) {
								if(data == "shubet") { 
									alert("Error: Unable to continue. It seems the cut-off period you specified has already been posted for payroll processing."); 
								} else {
									document.frmimportlogs.submit();
								}
							},"html");
						}
					} else {
						document.frmimportlogs.submit();
					}
				},"html");
			}
		}
		
		function getPayperiods(ptype) {
			$.post("misc-data.php", { mod: "populatePeriods", batch: ptype, sid: Math.random() }, function(data) { $("#cutoff").html(data); },"html");
		}
	</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
	<form enctype="multipart/form-data" name="frmimportlogs" method=post action="importlogs.php" target="_blank">
		<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
		<table width=90% align=center>
			<tr><td height=8></td></tr>
				<tr>
				<tr><td class=bareThin width=40% align=right style="padding-right: 15px;">Payroll Type&nbsp;:</td>
				<td>
					<select id="cutoff_type" name="cutoff_type" class="gridInput" style="width : 90%; font-size: 11px;" onchange="javascript: getPayperiods(this.value);">
						<option value="">- Select Payroll Type -</option>
						<?php
							$loop_ptype = "select batch_id,batch_details from omdcpayroll.pay_batch order by batch_id;";
							$_xres1 = $o->dbquery($loop_ptype);
							while($_xrow1 = $_xres1->fetch_array(MYSQLI_NUM))
							{
								print("<option value=\"" . $_xrow1[0] . "\">" . $_xrow1[1] . "</option>");
							}
							unset($_xres1);
						?>
					</select>
				</td>
			</tr>
			<tr><td height=4></td></tr>
			<tr><td class=bareThin width=40% align=right style="padding-right: 15px;">Cut-off Period&nbsp;:</td>
				<td align=left>
					<select name="cutoff" id="cutoff" style="width: 90%; font-size: 11px; padding: 5px;">
						
					</select>
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=bareThin width=30% align=right style="padding-right: 15px;">File to Upload&nbsp;:</td>
				<td align=left><input type=file id="userfile" name="userfile" style="width:90%"></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
		<hr style="width:80%;" align=center>
		<table align=center>
			<tr><td height=8></td></tr>
			<tr><td></td>
				<td>
					<button class="buttonding" onclick="importData();"><img src="../images/icons/download.png" border=0 width="16" height="16" align=absmiddle>&nbsp;&nbsp;Import DTR</button>
				</td>
			</tr>
		</table>
	</form>
</body>
</html>
<?
