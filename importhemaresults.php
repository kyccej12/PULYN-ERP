<html>
<head>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript">
		function importData() {
			var file = document.frmImportResults.userfile.value;
           
            if(file == '') {
                parent.sendErrorMessage("Nothing to to processed!");
            } else {
           
               

                var tfile = file.split('.');


                if(tfile[1] == 'csv') {
                   
                    document.frmImportResults.submit();
                } else {
                    parent.sendErrorMessage("Invalid file format detected!");
                }
            }
		}
		
	</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
	<form enctype="multipart/form-data" name="frmImportResults" method=post action="hemaimporter.php" target="_blank">
		<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
		<table width=90% align=center>
			<tr><td height=8></td></tr>
			<tr><td class=bareThin width=50% align=right style="padding-right: 15px;">File to Import (CSV)&nbsp;:</td>
				<td align=left><input type=file id="userfile" name="userfile" style="width:90%"></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
		<hr style="width:80%;" align=center>
		<table align=center>
			<tr><td height=8></td></tr>
			<tr><td></td>
				<td>
					<button class="buttonding" onclick="importData();" style="height: 30px;" type="button"><img src="images/icons/csv-icon.png" border=0 width="16" height="16" align=absmiddle>&nbsp;&nbsp;Import Hema Batch Result</button>
				</td>
			</tr>
		</table>
	</form>
</body>
</html>
<?
