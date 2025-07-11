<?php
	session_start();
	include("includes/dbUSE.php");
	
	//$res = getArray("SELECT DATE_FORMAT(`date`,'%m/%d/%Y') `date`,`type`,description FROM hris.e_holidays WHERE record_id = '$_REQUEST[fid]';");
	
	if(isset($_REQUEST['fid']) && $_REQUEST['fid']!=''){
		$res = getArray("SELECT trace_no,DATE_FORMAT(`date`,'%m/%d/%Y') `date`,`type`,description FROM hris.e_holidays WHERE record_id = '$_REQUEST[fid]';");
		$fid = $_REQUEST['fid'];
		$trace_no = $res['trace_no'];
		
	}else{
		list($trace_no) = getArray("SELECT MD5(RAND()) AS trace_no;");
		//list($fid) = getArray("SELECT IFNULL(MAX(record_id),0)+1 AS record_id FROM hris.e_holidays;");
		$_REQUEST['fid']= $fid;
	}
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
		$(function() { 
				$("#hol_date").datepicker();
				
		});
		function insertBranch(bid,cid,e) {
			
			var push;
			if(document.getElementById(e).checked == true) { push = "Y"; } else { push = "N"; }
			if($("#fid").val()!="") {
				$.post("payroll.datacontrol.php", { mod: "insertAOE" , trace_no : $("#trace_no").val() ,bid: bid,cid:cid, push: push, fid:$("#fid").val(), sid: Math.random() });
			}
		}
		
		function saveFile(){
			$.post("payroll.datacontrol.php",{mod:"saveHoliday",trace_no: $("#trace_no").val(),fid: $("#fid").val(), date: $("#hol_date").val() ,type: $("#hol_type").val(),description: $("#hol_description").val() },function(data){
				parent.showHolidays();
				parent.popSaver();
			});
		}
			
	</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
<form name="frminvoices" id="frminvoices">
		<input type="hidden" id="fid" name="fid" value="<?php echo $_REQUEST['fid'];?>">
		<input type="hidden" id="trace_no" name="trace_no" value="<?php echo $trace_no;?>">
		<table align=center border=0 width=100% cellpadding=0 cellspacing=3>
			<tr><td class=spandix-l align=right style="padding-right: 10px;" valign=top>Type :</td>
				<td align=left>
					<select name="hol_type" id="hol_type" class="gridInput" style="width:80%; font-size: 11px;">
						<option value="REG">- Regular Holiday -</option>
						<option value="SP">- Special Holiday -</option>
						<option value="CP">- Company Holiday -</option>
					</select>
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Date :</td>
				<td align=left>
					<input type=text id="hol_date" name="hol_date" class="gridInput" value = "<?php echo $res['date'];?>" style="width:80%; font-size: 11px;">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;" valign=top>Hol. Description :</td>
				<td align=left>
					<textarea id="hol_description" name="hol_description" class="gridInput" rows=2 style="width:80%;font-size: 11px;" value="" ><?php echo $res['description'];?></textarea>
				</td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
		<table width=100% class="td_content">
		<?php
			
				$mtabs = dbquery("select company_id,company_name from companies;"); $i = 0;
				while(list($mid,$mname) = mysql_fetch_array($mtabs)) {
					echo "<tr><td class=spandix width=10% align=\"right\" valign=top style=\"padding-top: 5px;\"> </td><td valign=middle>";
					//$stabs = dbquery("select submenu_id, menu_title from menu_sub where parent_id='$mid';");
					$stabs = dbquery("SELECT branch_code,company,branch_name from options_branches WHERE company='$mid' ORDER BY branch_code;");
					echo "<table width=100%>";
					$tloop = 1; 
					while(list($bid,$cid,$br_name) = mysql_fetch_array($stabs)) {
						if($tloop > 2 ) { echo "<tr>"; $tloop = 1; }
							echo "<td width=10% valign=top class=spandix><input type=checkbox id=\"cbox[$i]\" value=\"SUBMENU|$sid\" onclick=\"javascript: insertBranch($bid,$cid,this.id);\" ";
							 
							$isExistSub = getArray("select count(*) as found from hris.holiday_aoe where company='$cid' and branch='$bid' and hol_fileid='$_REQUEST[fid]';");
							if($isExistSub[0] > 0) { echo "checked"; }
								echo ">&nbsp;$br_name</td>";
								if($tloop > 2 )  { echo "</tr>"; }
									$tloop++; $i++;
					}
					echo "</table>";
					echo "</td><td width=\"2%\"></td></tr>";
					echo "<tr><td class=\"spandix\" align=\"right\"></td><td colspan=2><hr width=90% align=left style=\"border: 1px solid #195977;\" /></td></tr>";	
				}
			
		?>
	</table>
	</form>
	<div align="right">
			<br>
			<button style="color:#5f5f5f" onclick = "saveFile();"> Save Record </button>
	</div>
</body>
</html>
<?php mysql_close($con);