<?php
	session_start();
	include("includes/dbUSE.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="js/jquery.dialogextend.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>
	var sPO;
		$(function() { $("#hol_date").datepicker(); });
		function selectFile(obj) {
			$(obj).closest("tr").siblings().removeClass("diffcolor");
			$(obj).toggleClass("diffcolor");
			tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); sPO = tmp_obj[1];
		}

		function viewFile(){
			if(sPO==''){
				parent.sendErrorMessage("Please select record to view.");
			}else{
				parent.viewHoliday(sPO);
			}
		}
		
		function newRecord(){
			parent.viewHoliday('');
		}
	
	</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table width='100%' class="tgrid" cellpadding=0 cellspacing=0>
	<tr>
		<td class="dgridHead" align=left width=30%><b>DATE</b></td>
		<td class="dgridHead" align=left width=30%><b>TYPE</b></td>
		<td class="dgridHead" align=left><b>DESCRIPTION</b></td>
		<td class="dgridHead" width=16>&nbsp;</td>
	</tr>
</table>
<div id="details" style="height:450px; overflow: auto;">
<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">
<?php
	$_i = dbquery("select record_id, date_format(`date`,'%W %M %d, %Y') as myday, `type`, if(`type`='REG','Regular Holiday',if(`type`='SP','Special Holiday','Company Holiday')) as htype, description from hris.e_holidays order by `date` desc;");
	while($row = mysql_fetch_array($_i)) {
		if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
		echo "<tr bgcolor=\"$bgC\" id='obj_$row[record_id]' onMouseOver=\"javascript: highlightTableRowVersionA(this, '#e6f0fa');\" onclick=\"javascript: selectFile(this);\">
				<td class=\"grid\" valign=top align=left width=30% style=\"padding-left: 5px;\">".$row['myday']."</td>
				<td class=\"grid\" valign=top align=left width=30% style=\"padding-left: 10px;\">".$row['htype']."</td>
				<td class=\"grid\" valign=top align=left width=40% style=\"padding-left: 10px;\">".$row['description']."</td>
		</tr>"; $i++;
	}
	if($i < 22) {	
		for($i; $i <= 22; $i++) {	if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
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
			<button onClick="newRecord();" class="buttonding" id="btn_rsv"><img src="images/icons/add.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;New Holiday File</b></button>
			<button onClick="viewFile();" class="buttonding" id="btn_rsv"><img src="images/icons/bill.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;View File Details</b></button>
			<button type="button" class="buttonding" style="font-size: 11px;" onclick="parent.showHolidays();"><img src="images/icons/refresh.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Refresh List</b></button>
		</td>
	</tr>
 </table>
 <div id="holidayDetails" style="padding: 10px; display: none;">
	
</div>
</body>
</html>
<?php mysql_close($con);