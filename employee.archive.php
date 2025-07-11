<?php
	session_start();
	include("includes/dbUSE.php");	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Geck Distributors</title>
<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link href="style/dropMenu.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
<script language="javascript" src="js/tableH.js"></script>
<script language="javascript" src="js/jquery.dialogextend.js"></script>
<script>

	var eid = "";
	
	function selectFID(obj) {
		gObj = obj;
		$(obj).closest("tr").siblings().removeClass("diffcolor");
		$(obj).toggleClass("diffcolor");
		tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); eid = tmp_obj[1];
	}
	
	function getEE() {
		if(eid == "") {
			parent.sendErrorMessage("Unable to retrieve record. Please select a record from the list, and once highlighted, press  \"<b><i>View Employee Info</i></b>\" button again...");
		} else {
			parent.showEInfo(eid);
		}
	}
	
	function show201() {
		if(eid == "") {
			parent.sendErrorMessage("Unable to retrieve record. Please select a record from the list, and once highlighted, press  \"<b><i>Employee 201 File</i></b>\" button again...");
		} else {
	
			$.post("payroll.datacontrol.php", { mod: "getEmpName", record_id: eid, sid: Math.random() }, function(data) {
				$("#employee201").dialog({title: "Employee 201 File ("+data+")", width: 480, height: 320 }).dialogExtend({
					"closable" : true,
					"maximizable" : false,
					"minimizable" : true
				});
			});
		}
	}
	function restoreEE() {
			if(eid!=''){
				if(confirm("Are you sure you want to restore this file from archives?") == true) {
					$.post("payroll.datacontrol.php", { mod: "restoreEE", eid: eid, sid: Math.random() }, function() { 
						alert("Employee Record Successfully Restored!"); 
						parent.showZipEmp(1);
				});
			}
		}else{
			parent.sendErrorMessage("Unable to retrieve record. Please select a record from the list.");
		}
			
	}
	
	function showEdu() { parent.showEdu(eid); }
	function showFam() { parent.showFam(eid); }
	function showErecord() { parent.showErecord(eid); }
	function showErecord2() { parent.showErecord2(eid); }
	function showCert() { parent.showCert(eid); }
	
</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
			<table border="0" cellpadding="0" cellspacing="0" width=100%>
				<tr bgcolor="#595959">
					<td align=left class="dgridhead" width="70">ID NO.&nbsp;<a href="#" onclick="javascript: parent.showEmployees(2);"><img src="images/down2.png" border=0 align=absmiddle /></a></td>
					<td align=left class="dgridhead" width="120">LAST NAME&nbsp;<a href="#" onclick="javascript: parent.showEmployees(3);"><img src="images/down2.png" border=0 align=absmiddle /></a></td>
					<td align=left class="dgridhead" width="120">FIRST NAME&nbsp;<a href="#" onclick="javascript: parent.showEmployees(4);"><img src="images/down2.png" border=0 align=absmiddle /></a></td>
					<td align=left class="dgridhead" width="120">MIDDLE NAME&nbsp;<a href="#" onclick="javascript: parent.showEmployees(5);"><img src="images/down2.png" border=0 align=absmiddle /></a></td>
					<td align=left class="dgridhead" width="330" style="padding-left: 10px;">COMPLETE ADDRESS</td>
					<td align=left class="dgridhead" style="padding-left: 5px;">DESIGNATION</td>
					<td align=center class="dgridhead" width="18">&nbsp;</td>
				</tr>
			</table>
			<div id="details" style="height:405px; overflow: auto;">
				<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">
				<?php
					$i = 0;
					
					if(isset($_GET['searchtext']) && $_GET['searchtext'] != '') { 
						$araynako = explode(" ",$_GET['searchtext']);
						foreach($araynako as $sakit) {
							$tunga = $tunga . "lname like '%$sakit%' || fname like '%$sakit%' || mname like '%$sakit%' || id_no = '$sakit' || `address` like '%$sakit%' ||";
						}
						
						$tunga = substr($tunga,0,-3);
						$gipangita = " and ($tunga) ";
					}
					
					switch($_GET['sort']) {
						case "1": $sort = " order by lname, fname, mname "; break;
						case "2": $sort = " order by id_no asc "; break;
						case "3": $sort = " order by lname, fname, mname "; break;
						case "4": $sort = " order by fname "; break;
						case "5": $sort = " order by mname "; break;
						default: $sort = " order by lname, fname, mname "; break;
					}
					

					$getRec = dbquery("select * from hris.e_master where company = '$_SESSION[company]' and filestatus='Archive' $gipangita $sort;");
					while($row = mysql_fetch_array($getRec)) {
						if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
						switch($row['company']) { case "1": $comp = "SSJFC"; break; case "2": $comp = "SJPI"; break; case "3": $comp = "FPFC"; break; case "4": $comp = "NRDC"; break; default: $comp = "SSJFC"; break; }
						echo "<tr bgcolor=\"$bgC\" onmouseover=\"highlightTableRowVersionA(this, '#3399ff');\" id='obj_$row[record_id]' onclick='selectFID(this);'>
								<td class=dgridbox align=left width=\"70\">$row[id_no]</td>
								<td class=dgridbox align=left width=\"120\">$row[lname]</td>
								<td class=dgridbox align=left width=\"120\">$row[fname]</td>
								<td class=dgridbox align=left width=\"120\" style=\"padding-left: 10px;\">$row[mname]</td>
								<td class=dgridbox align=left width=330>$row[address]</td>
								<td class=dgridbox align=left>$row[designation]</td>
							</tr>"; $i++; 
						}
					if($i < 30) {
						for($i; $i <= 30; $i++) {
							if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
							echo "<tr bgcolor='$bgC'><td colspan='7'>&nbsp;</td></tr>";
						}
					}
				?>
				</table>
			</div>
			<table width="100%"  cellspacing="0" cellpadding="0" style="padding-left: 5px;">
				<tr><td height=8></td></tr>
				<tr>
					<td>
						<button onClick="javascript: getEE();" class="buttonding"><img src="images/icons/personalinfo.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;View Employee Info.</b></button>
						<button onClick="javascript: restoreEE();" class="buttonding"><img src="images/icons/customerinfo.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Restore Employee</b></button>
						<button onClick="javascript: parent.showZipEmp('');" class="buttonding"><img src="images/icons/refresh.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Reload List</b></button>
						<button onClick="parent.showSearch('employee');" class="buttonding"><img src="images/icons/search.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Search Record</b></button>
					</td>
				</tr>
			</table>
		</td>
	</tr>
 </table>
 <div id="employee201" style="display: none;">
	<div style="padding: 20px;">
		<div class="fileObjects"><a href="#" onclick="showIDs();"><img src="images/icons/camera.png" width=60 height=60 /><br/><br/>ID Pictures</a></div>
		<div class="fileObjects"><a href="#" onclick="showFam();"><img src="images/icons/family.png" width=60 height=60 /><br/><br/>Family Background</a></div>
		<div class="fileObjects"><a href="#" onclick="showEdu();"><img src="images/icons/education.png" width=60 height=60 /><br/><br/>Educational Background</a></div>
		<div class="fileObjects"><a href="#" onclick="showErecord();"><img src="images/icons/employment.png" width=60 height=60 /><br/><br/>Work Experience (Internal)</a></div>
		<div class="fileObjects"><a href="#" onclick="showErecord2();"><img src="images/icons/employment.png" width=60 height=60 /><br/><br/>Work Experience (External)</a></div>
		<div class="fileObjects"><a href="#" onclick="showCert();"><img src="images/icons/certificates.png" width=60 height=60 /><br/><br/>Memos, Certificates & Clearances</a></div>
	</div>
</div>
</body>
</html>
<?php mysql_close($con);