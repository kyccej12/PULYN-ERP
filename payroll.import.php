<html>
<head>
<style type="text/css">
 
 body { 
	margin:0px;
	font-size:8pt;
	font-family:"ARIAL";
	color:#195977 ;
 }
	
.mainborder { border: 1.5px solid #436477; 
	box-shadow: 2px 2px 1px #608098;
	height: 120px;
	width: 380px;
	background-color:#FFF;	
}
	
</style>
</head>
<body>

<form enctype="multipart/form-data" action="payroll.importdtr.php" method="POST">
	<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />	
	<table class="mainborder" border="0" cellspacing=0 cellpadding=0 align="center" >
		<tr bgcolor="#4682B4"><td colspan=2><span style="font-weight:bold; color:#FFF; font-size:9pt; padding:5px; ">Import DTR from Source File</span></td></tr>
		<tr>
			<td align="left" style="padding:5px; font-size:9pt;">Period ID: </td>
			<td>
				<select name="period_id" id="period_id" style="width: 180px;">
					<?php
						$con1 = mysql_connect('localhost', 'root', '');
						if (!$con1) { die('Could not connect: ' . mysql_error()); }
						mysql_select_db("p80", $con1);
						$piq = mysql_query("select cutoff, concat(date_format(dtf,'%m/%d/%Y'),' - ',date_format(dt2,'%m/%d/%Y')) from payroll_cutoffs order by dt2 desc;");
						while(list($cf,$range) = mysql_fetch_array($piq)) {
							echo "<option value='$cf'>$range</option>";
						}
						mysql_close($con1);
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="left" style="padding:5px; font-size:9pt;">Choose a file to upload: </td>
			<td><input name="uploadedfile" type="file" /></td>
		</tr>
		<tr>
			<td align="right" colspan=2>
				<input style="margin-right:20px;" type="submit" value="Import" />
			</td>
		</tr>
	</table>
</form>


</body>
</html>