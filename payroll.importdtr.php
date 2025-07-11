<?php
	include("includes/dbUSE.php");
	ini_set("memory_limit",-1);
	ini_set("max_execution_time",0);
	
	function formatDate2($date) {
		$date = explode("/",$date);
		return $date[2]."-".str_pad($date[0],2,'0',STR_PAD_LEFT)."-".str_pad($date[1],2,'0',STR_PAD_LEFT);
	}

	function formatD($date){
		if($date!=''){
			$month = substr($date,0,2);
			$day = substr($date,2,2);
			$year = substr($date,4,4);

			return $year.'-'.$month.'-'.$day;
		}
		
	}

	function formatTime($time){
		if($time!=''){
			$hrs = substr($time,0,2);
			$mins = substr($time,2,2);
			return $hrs.':'.$mins;	
		}
	}
	
	
	list($tmpfilename) = getArray("select UCASE(left(MD5(RAND()),12)) as trace_no;"); 
	
	$error = 0;

	$temp = explode(".",$_FILES["uploadedfile"]["name"]);
	$filename =  $tmpfilename . "." . end($temp);

	$path = "uploads/$filename";
	$imageFileType = pathinfo($path,PATHINFO_EXTENSION);


	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 2000000) {
	    echo ">> Sorry, your file is too large.<br/>";
	    $error = 1;
	}

	// Allow certain file formats
	//if($imageFileType != "TXT" && $imageFileType != "txt") {
	if($imageFileType == "TXT" || $imageFileType == "txt" || $imageFileType == "LOG" || $imageFileType == "log") {
	    $error = 0;
	}else{
		$error = 1;
		echo ">> Sorry, invalid file format detected.<br/>";
	}

	if ($error == 0 ) {
	    move_uploaded_file($_FILES["uploadedfile"]["tmp_name"],$path); 
		
		$file = "uploads/$filename";
		//$file = "uploads/TIME458.TXT";
		$handle = fopen($file, "r");
		$read = file_get_contents($file);
		$lines = explode("\n", $read);
		
		/* Read Text File And Process Raw Log File */
		foreach($lines as $key => $value){
			 $cols = preg_split("/\s+/", $value);
			 dbquery("INSERT IGNORE INTO citylights.nationality (nation_desc) VALUES ('".mysql_real_escape_string($cols[0])."');");
			}
		}
?>

<?php if($error == 0) { ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>$co[company_name] ERP System Ver 1.0b</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="style/jquery-ui.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="js/date.js"></script>
	<script>
		$(function() { $("#dtf").datepicker(); $("#dt2").datepicker(); });
		function printDTR() {
		window.open("reports/dtr.php?dtf="+$("#dtf").val()+"&dt2="+$("#dt2").val()+"&sid="+Math.random()+"","Daily Time Record","location=1,status=1,scrollbars=1,width=640,height=720");
	}
	</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<input class="nInput" type="text" id="dtf" name="dtf" style="width: 80%;"  value="<?php echo $_POST['dtf']; ?>" />
			</td>
			</tr>
				<tr><td height=4></td></tr>
			<tr>
			<td width=35%><span class="spandix-l"></span></td>
			<td>
				<input class="nInput" type="text" id="dt2" name="dt2" style="width: 80%;" value="<?php echo $_POST['dt2']; ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr><td height=4></td></tr>
		<tr>
			<td>&nbsp;</td>
			<td align=left>
				<button type="button" class="buttonding" style="font-size: 11px;" onclick="printDTR();"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Print Daily Time Record</button>
			</td>
			</td>
		</tr>
		<tr><td height=20></td></tr>
	</table>
</form>
</body>
</html>
<?php 
	} //End of If
	mysql_close($con);
?>

