<?php
	//ini_set("display_errors", "on");
	include("handlers/initDB.php");
	$con = new myDB;
	$pid = $_GET['pid'];	
	
	$res = array();
	if($pid != '') {
		$res = $con->getArray("select *,photo_path,date_format(birthdate,'%m/%d/%Y') as pbday, if(spouse_birthdate!='0000-00-00',date_format(spouse_birthdate,'%m/%d/%Y'),'') as sbday from patient_info where patient_id = '$pid';");
	}

	session_start();
	if(isset($_POST['submit'])){
		//echo var_dump($_POST);
		$temp = explode(".",$_FILES["uploadedfile"]["name"]);
		$filename =  $temp[0] . "." . end($temp);

		$path = "household_pic/$filename";
		$imageFileType = pathinfo($path,PATHINFO_EXTENSION);


		// Check file size
		if ($_FILES["uploadedfile"]["size"] > 2000000) {
		    echo ">> Sorry, your file is too large.<br/>";
		    $error = 1;
		}

		// Allow certain file formats
		if($imageFileType != "JPG" && $imageFileType != "jpg" && $imageFileType != "JPEG" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "PNG") {
		    $error = 1;
		}else{
			 move_uploaded_file($_FILES["uploadedfile"]["tmp_name"],$path); 
			$con->dbquery("UPDATE citylights.household SET h_pic = '$filename' WHERE record_id = '$_POST[hh_rid]';");
		}
	}

	if($res['photo_path'] != '') {
		$photo = "<img src='".$res['photo_path']."' align=absmiddle width=120 />";
	} else { $photo = "<img src=\"./images/id/main.png\" align=absmiddle width=120 />"; }
	
	function getMod($def,$mod) { if($def == $mod) { echo "class=\"float2\""; } }

?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>OMDC Prime Medical Diagnostics Corp.</title>
<link rel="stylesheet" type="text/css" href="style/style.css" />
<link rel="stylesheet" type="text/css" href="ui-assets/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
<script type="text/javascript" charset="utf8" src="js/jquery.dialogExtend.js"></script>
<script>
	$(document).ready(function() {
		<?php if($error == 1) { echo "parent.sendErrorMessage(\"Invalid Image Format. Please have convert this file into JPEG or PNG format.\")"; } ?>
		 $("#p_bday").datepicker({changeMonth: true, changeYear: true, yearRange: "-90:+00"}); $("#s_bday").datepicker({changeMonth: true, changeYear: true,  yearRange: "-90:+00"});
		<?php 
			switch($_GET['mod']) {
				case "2":
					echo "$('#splist').dataTable({
						\"keys\": true,
						\"scrollY\":  \"200px\",
						\"select\":	\"single\",
						\"pagingType\": \"full_numbers\",
						\"bProcessing\": false,
						\"serverSide\": true,
						\"sAjaxSource\": \"data/household.php?owner_id=".$ownerid."\",
						\"aoColumns\": [
						  { mData: 'h_lname' },
						  { mData: 'h_fname' },
						  { mData: 'h_mname' },
						  { mData: 'h_date' },
						  { mData: 'h_relationship' }
						],
						\"aoColumnDefs\": [
							{className: \"dt-body-center\", \"targets\": [3]},
							{ \"targets\": [0], \"visible\": true }
						]
					});";
				break;
				case "3":
					echo "$('#ilist').dataTable({
						\"keys\": true,
						\"scrollY\":  \"200px\",
						\"select\":	\"single\",
						\"pagingType\": \"full_numbers\",
						\"bProcessing\": false,
						\"serverSide\": true,
						\"sAjaxSource\": \"data/vehicles.php?owner_id=".$ownerid."\",
						\"aoColumns\": [
						  { mData: 'plate_no' },
						  { mData: 'v_type' },
						  { mData: 'v_brand' },
						  { mData: 'v_model' },
						  { mData: 'v_parking' }
						],
						\"aoColumnDefs\": [
							{className: \"dt-body-center\", \"targets\": [4]},
							{ \"targets\": [0], \"visible\": true }
						]
					});";
				break;
			}
		?>
	});

	function saveCInfo(fid) {
		if(confirm("Are you sure you want to save changes made to this file?") == true) {
			var msg = "";
			

			if(msg!="") {
				parent.sendErrorMessage(msg);
			} else {
				var url = $(document.frmPatientInfo).serialize();
				url = "mod=savePatientInfo&"+url;
				$.post("src/sjerp.php", url);
				alert("Record Successfuly Saved!")
			}
		}
	}

	function takePhoto(fid) {
		var txtHTML = "<iframe id='frmCamera' frameborder=0 width='100%' height='100%' src='maniniyot.php?pid="+fid+"&sid="+Math.random()+"'></iframe>";
		$("#cameraFrame").html(txtHTML);
		$("#cameraFrame").dialog({
			title: "Capture Photo",
			width: 400,
			height: 420,
			resizeable: false,
			modal: false
		}); 
	}
	
	function deleteCust(fid) {
		if(confirm("Are you sure you want to delete this record?") == true) {
			$.post("homeowner.datacontrol.php", { mod: "deleteFile", fid: fid, sid: Math.random() }, function(){ "Customer Record Successfully Deleted!"; parent.closeDialog("#customerdetails"); parent.showHomeOwner(''); });
		}	
	}

	function getCities(pid,selbox) {
		$.post("src/sjerp.php", { mod: "getCities", pid: pid, sid: Math.random() }, function(data) {
			document.getElementById(selbox).innerHTML = data;
		},"html");
	}
	
	function getBrgy(city,selbox) {
		$.post("src/sjerp.php", { mod: "getBrgy", city: city, sid: Math.random() }, function(data) {
			document.getElementById(selbox).innerHTML = data;
		},"html");
	}
	
	function changeMod(mod) {
		document.changeModPage.mod.value = mod;
		document.changeModPage.submit();
	}

	
</script>
<style>
	
	.dataTables_wrapper {
		display: inline-block;
		font-size: 11px; padding: 3px;
		width: 99%; 
	}
	
	table.dataTable tr.odd { background-color: #f5f5f5;  }
	table.dataTable tr.even { background-color: white; }
	.dataTables_filter input { width: 250px; }
	</style>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<form name="frmPatientInfo" id="frmPatientInfo">
	<input type="hidden" name="pid" id="pid" value="<?php echo $pid; ?>">
	<table width="100%" cellspacing="0" cellpadding="5" style="border-bottom: 1px solid black; margin-bottom: 5px;">
		<tr>
			<td align=left>
				<?php 
					if($_GET['mod'] == 1) { 

						echo '<button type = "button" name = "setActive" class="ui-button ui-widget ui-corner-all" onClick="saveCInfo(\''. $pid . '\');">
							<span class="ui-icon ui-icon-disk"></span> Save Changes Made
						</button>';	
						if($pid != '') {
							echo '<button type = "button" name = "setActive" class="ui-button ui-widget ui-corner-all" onClick="deleteCust(\'' . $pid . '\');">
									<span class="ui-icon ui-icon-closethick"></span> Delete Record
							 	  </button>';
						}
						echo '<button type = "button" name = "savePhoto" class="ui-button ui-widget ui-corner-all" onClick="takePhoto(\''. $pid . '\');">
							<span class="ui-icon ui-icon-contact"></span> Take Photo
						</button>';	
					}
				?>
			</td>
		</tr>
	</table>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" >
		<tr>
			<td width="30%" align=center class="spandix-l" rowspan=9>
				<?php echo $photo; ?>
				<?php
					if($pid != '') { echo "<br/><span>Patient ID: <b>" . str_pad($pid,6,'0',STR_PAD_LEFT). "</b></span></br/><br/>"; }

				?>

					<input type="text" id="p_badgeno" name="p_badgeno" class="gridInput" style="width: 60%; text-align: center; font-weight:bold; font-size: 14px;" value="<?php echo $res['badge_no']; ?>" />
					<br/><span style="font-size:7pt;color:grey;padding-left:1%;" >BADGE No.</span>
			</td>
			<td width="70%" align=left>
				<table width=100% cellpadding=0 cellspacing=2>
					<tr>
						<td>
							<input type="text" id="p_lname" name="p_lname" class="gridInput" style="width: 100%;" value="<?php echo $res['lname']; ?>" />
							<br/><span style="font-size:7pt;color:grey;padding-left:1%;" >Last Name</span>
						</td>
						<td>
							<input type="text" id="p_fname" name="p_fname" class="gridInput" style="width: 100%;" value="<?php echo $res['fname']; ?>" />
							<br/><span style="font-size:7pt;color:grey;padding-left:1%;" >First Name</span>
						</td>
					
						<td>	
							<input type="text" id="p_mname" name="p_mname" class="gridInput" style="width: 100%;" value="<?php echo $res['mname']; ?>" />
							<br/><span style="font-size:7pt;color:grey;padding-left:1%;" >Full Middle Name</span>
						</td>
						<td width=10%>	
							<select id="p_suffix" name="p_suffix" class="gridInput" style="width: 100%;">
								<option value="">- NA -</option>
								<option value="JR" <?php if($res['suffix'] == 'JR') { echo "selected"; } ?>>JR</option>
								<option value="SR" <?php if($res['suffix'] == 'SR') { echo "selected"; } ?>>SR</option>
							</select>
							<br/><span style="font-size:7pt;color:grey;padding-left:1%;" >Suffix</span>
						</td>	
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width="70%" align=left>
				<select id="p_gender" name="p_gender" class="gridInput" style="width: 150px;">
					<option value="M" <?php if($res['gender'] == 'M') { echo "selected"; } ?>>Male</option>
					<option value="F" <?php if($res['gender'] == 'F') { echo "selected"; } ?>>Female</option>	
				</select>
				<br/><span style="font-size:7pt;color:grey;" >Gender</span>			
			</td>		
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width="70%" align=left>
				<input type="text" id="p_bday" name="p_bday" class="gridInput" style="width: 150px;" value="<?php echo $res['pbday']; ?>" />
				<br/><span style="font-size:7pt;color:grey;" >Birthdate</span>			
			</td>			
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width="80%" align=left>
				<select id="p_cstat" name="p_cstat" class="gridInput" style="width: 150px;">
					<?php
						$csQuery = $con->dbquery("select csid,civil_status from omdcpayroll.options_civilstatus");
						while($csRow = $csQuery->fetch_array()) {
							echo "<option value='$csRow[0]' ";
							if($csRow[0] == $res['cstat']) { echo "selected"; }
							echo ">$csRow[1]</option>";
						}
					?>
				</select>
				<br/><span style="font-size:7pt;color:grey;">Civil Status</span>			
			</td>		
		</tr>
		<tr><td height=4></td></tr>
		<td width=50% align=left>
			<table width=100% cellpadding=0 cellspacing=2>
				<td>		
					<tr>
						<td align=left width=20%>
							<input type="text" id="p_phic" name="p_phic" class="gridInput" style="width: 150px;" value="<?php echo $res['phic_no']; ?>" />
							<br/><span style="font-size:7pt;color:grey;">Philhealth ID No. (If Available)</span>			
						</td>	
						<td align=left>
							<input type="text" id="p_mid" name="p_mid" class="gridInput" style="width: 150px;" value="<?php echo $res['mid_no']; ?>" />
							<br/><span style="font-size:7pt;color:grey;">PAG-IBIG MID No. (If Available)</span>			
						</td>
					</tr>	
				</td>
			</table>
		</td>
	</table>
	<table cellspacing=0 cellpadding=0 width=100% align=center style="margin-top: 20px;">
		<tr>
			<td style="padding: 0px 0px 1px 0px;">
				<div id="custmenu" align=left class="ddcolortabs">
					<ul class=float2>
						<li><a href="#" <?php getMod("1",$_GET[mod]); ?> onclick="javascript: changeMod(1);"><span id="tbbalance1">General Info</span></a></li>						
						<?php
							if($pid != '') {
								echo '<li><a href="#" ' . getMod("2",$_GET['mod']) . ' onclick="javascript: changeMod(2);"><span id="tbbalance2">Transaction History</span></a></li>
								<li><a href="#" ' . getMod("5",$_GET['mod']) . ' onclick="javascript: changeMod(5);"><span id="tbbalance5">Medical History</span></a></li>';
							}
						?>	
					</ul>
				</div>
			</td>
		</tr>
	</table>
	<?php switch($_GET['mod']) {  case "1": default: ?>
	<table width="100%" cellpadding=0 cellspacing=1 class="td_content" style="padding:10px;" border=0>
	<tr>
			<td width=20% class="spandix-l" valign=top>Address:</td>
			<td width=80%>
				<table width=100% cellpadding=0 cellspacing=1>
					<tr>
						<td width=40%>
							<input type="text" id="p_street" name="p_street" class="gridInput" style="width: 100%" value="<?php echo $res['street']; ?>" />
							<br/><span style="font-size:7pt;color:grey;" >House #,Street,Village</span>
						</td>
						<td width=30%>
							<select id="p_brgy" name="p_brgy" class="gridInput" style="width: 99%;">
								<?php
									if($res['city'] != '') {
										$brgyQuery = $con->dbquery("select brgyCode, brgyDesc from options_brgy where citymunCode = '$res[city]' order by brgyDesc asc;");
										while($brgyRow = $brgyQuery->fetch_array()) {
											echo "<option value='$brgyRow[0]' "; if($brgyRow[0] == $res['brgy']) { echo "selected"; }
											echo ">$brgyRow[1]</option>";
										}
									}
								?>
							</select>
							<br/><span style="font-size:7pt;color:grey;" >Barangay</span>
						</td>
						<td>
							<select id="p_city" name="p_city" class="gridInput" style="width: 99%;" onchange = "getBrgy(this.value,'p_brgy');">
								<?php
									if($res['province'] != '') {
										$cityQuery = $con->dbquery("select citymunCode, citymunDesc from options_cities where provCode = '$res[province]' order by citymunDesc asc;");
										while($cityRow = $cityQuery->fetch_array()) {
											print "<option value='$cityRow[0]' "; if($cityRow[0] == $res['city']) { echo "selected"; }
											print ">$cityRow[1]</option>";
										}
									}
								?>
							</select>
							<br/><span style="font-size:7pt;color:grey;" >City or Municipality</span>
						</td>
					</tr>
					<tr>
						<td width=40%>
							<select id="p_province" name="p_province" class="gridInput" style="width: 100%;" onchange="getCities(this.value,'p_city');">
								<option value="">- Select Province -</option>
								<?php
									$provQuery = $con->dbquery("select provCode, provDesc from options_provinces order by provDesc asc;");
									while($provRow = $provQuery->fetch_array()) {
										print "<option value='$provRow[0]' "; if($provRow[0] == $res['province']) { echo "selected"; }
										print ">$provRow[1]</option>";
									}
								?>
							</select>
							<br/><span style="font-size:7pt;color:grey;" >Province</span>
						</td>
					<tr/>
				</table>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=20% class="spandix-l">Birthplace  :</td>
			<td width=80%>
				<input type="text" id="p_birthplace" name="p_birthplace" class="gridInput" style="width: 40%" value="<?php echo $res['birthplace']; ?>" />
				
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=20% class="spandix-l">Guardian's Full Name  :</td>
			<td width=80%>
				<input type="text" id="p_guardian" name="p_guardian" class="gridInput" style="width: 40%" value="<?php echo $res['guardian']; ?>" />
				<br/><span style="font-size:7pt;color:grey;" >For Patients accompanied by their guardians (eg. Infants, Toddlers, Senior Citizens, PWDs, etc.)</span>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=20% class="spandix-l">Mobile No. :</td>
			<td width=80%>
				<input type="text" id="p_mobileno" name="p_mobileno" class="gridInput" style="width: 40%" value="<?php echo $res['mobile_no']; ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=20% class="spandix-l">Telephone No. :</td>
			<td width=80%>
				<input type="text" id="p_telephone" name="p_telephone" class="gridInput" style="width: 40%" value="<?php echo $res['tel_no']; ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=20% class="spandix-l">Email Address :</td>
			<td width=80%>
				<input type="text" id="p_email" name="p_email" class="gridInput" style="width: 40%" value="<?php echo $res['email_add']; ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=20% class="spandix-l">Nationality :</td>
			<td width=80%>
				<select id="p_nation" name="p_nation" style="width: 40%;" class="gridInput" />
					<option value="66">Filipino</option>
					<?php
						$q0 = $con->dbquery("SELECT line_id,nation_desc FROM nationality order by nation_desc;");
						while($_0 = $q0->fetch_array()) {
							print "<option value='$_0[0]' "; if($_0[0] == $res['nationality']) { echo "selected"; }
							print ">$_0[1]</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=20% class="spandix-l" valign=top>Spouse (If Married):</td>
			<td width=80%>
				<table width=100% cellpadding=0 cellspacing=1>
					<tr>
						<td width=35%>
							<input type="text" id="s_lname" name="s_lname" class="gridInput" style="width: 95%" value="<?php echo $res['spouse_lname']; ?>" />
							<br/><span style="font-size:7pt;color:grey;" >Last Name</span>
						</td>
						<td width=30%>
							<input type="text" id="s_fname" name="s_fname" class="gridInput" style="width: 95%" value="<?php echo $res['spouse_fname']; ?>" />
							<br/><span style="font-size:7pt;color:grey;" >First Name</span>
						</td>
						<td>
							<input type="text" id="s_mname" name="s_mname" class="gridInput" style="width: 95%" value="<?php echo $res['spouse_mname']; ?>" />
							<br/><span style="font-size:7pt;color:grey;" >Full Middle Name</span>
						</td>
						<td width=10%>	
							<select id="s_suffix" name="s_suffix" class="gridInput" style="width: 100%;">
								<option value="">- NA -</option>
								<option value="JR" <?php if($res['s_suffix'] == 'JR') { echo "selected"; } ?>>JR</option>
								<option value="SR" <?php if($res['s_suffix'] == 'SR') { echo "selected"; } ?>>SR</option>
							</select>
							<br/><span style="font-size:7pt;color:grey;padding-left:1%;" >Suffix</span>
						</td>
					</tr>
					<tr>
					<td width=35%>
							<input type="text" id="s_bday" name="s_bday" class="gridInput" style="width: 60%" value="<?php echo $res['sbday']; ?>" />
							<br/><span style="font-size:7pt;color:grey;" >Spouse's Birthdate</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=20% class="spandix-l">Occupation :</td>
			<td width=80%>
				<input type="text" id="p_occupation" name="p_occupation" style="width: 40%;" class="gridInput" value = "<?php echo $res['occupation']; ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=20% class="spandix-l">Employer :</td>
			<td width=80%>
				<input type="text" id="p_employer" name="p_employer" style="width: 100%;" class="gridInput" value = "<?php echo $res['employer']; ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=20% class="spandix-l" valign=top>Employer's Address :</td>
			<td width=80%>
				<table width=100% cellpadding=0 cellspacing=1>
					<tr>
						<td width=40%>
							<input type="text" id="e_street" name="e_street" class="gridInput" style="width: 100%" value="<?php echo $res['emp_street']; ?>" />
							<br/><span style="font-size:7pt;color:grey;" >House #,Street,Bldg.,Village</span>
						</td>
						<td width=30%>
							<select id="e_brgy" name="e_brgy" class="gridInput" style="width: 99%;">
								<?php
									if($res['emp_city'] != '') {
										$brgyQuery = $con->dbquery("select brgyCode, brgyDesc from options_brgy where citymunCode='$res[emp_city]' order by brgyDesc asc;");
										while($brgyRow = $brgyQuery->fetch_array()) {
											echo "<option value='$brgyRow[0]' "; if($brgyRow[0] == $res['brgy']) { echo "selected"; }
											echo ">$brgyRow[1]</option>";
										}
									}
								?>
							</select>
							<br/><span style="font-size:7pt;color:grey;" >Barangay</span>
						</td>
						<td>
							<select id="e_city" name="e_city" class="gridInput" style="width: 100%;" onchange = "getBrgy(this.value,'e_brgy');">
								<?php
									if($res['emp_province'] != '') {
										$cityQuery = $con->dbquery("select citymunCode, citymunDesc from options_cities where provCode = '$res[emp_province]' order by citymunDesc asc;");
										while($cityRow = $cityQuery->fetch_array()) {
											print "<option value='$cityRow[0]' "; if($cityRow[0] == $res['city']) { echo "selected"; }
											print ">$cityRow[1]</option>";
										}
									}
								?>
							</select>
							<br/><span style="font-size:7pt;color:grey;" >City or Municipality</span>
						</td>
					</tr>
					<tr>
						<td width=40%>
							<select id="e_province" name="e_province" class="gridInput" style="width: 100%;" onchange="getCities(this.value,'e_city');">
								<option value="">- Select Province -</option>
								<?php
									$provQuery = $con->dbquery("select provCode, provDesc from options_provinces order by provDesc asc;");
									while($provRow = $provQuery->fetch_array()) {
										print "<option value='$provRow[0]' "; if($provRow[0] == $res['emp_province']) { echo "selected"; }
										print ">$provRow[1]</option>";
									}
								?>
							</select>
							<br/><span style="font-size:7pt;color:grey;" >Province</span>
						</td>
					<tr/>
					<tr>
						<td width=40%>
							<input type="text" id="e_telno" name="e_telno" class="gridInput" style="width: 100%" value="<?php echo $res['emp_telno']; ?>" />
							<br/><span style="font-size:7pt;color:grey;" >Employer's Tel. No.</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height=4></td></tr>
	</table>
	<?php break; case "2": ?>
	<table id="splist" style="font-size:11px;">
		<thead>
			<tr>
				<th width=20%>LAST NAME</th>
				<th width=20%>FIRST NAME</th>
				<th width=20%>MIDDLE NAME</th>
				<th width=20%>BIRTHDATE</th>
				<th width=20%>RELATIONSHIP</th>
			</tr>
		</thead>
	</table>
	<table>
		<tr>
			<td align=left colspan=2 style="padding-top:5px;">
				<a href="#" class="topClickers" onClick="javascript:newHouseHold();"><img src="images/icons/add-2.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Add Entry</a>&nbsp;&nbsp;
				<a href="#" class="topClickers" onClick="javascript:editHH();"><img src="images/icons/tests256.png" width=16 height=16 border=0 align="absmiddle">&nbsp;View Entry</a>&nbsp;&nbsp;
				<a href="#" class="topClickers" onClick="javascript:deleteHousehold();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Entry</a>
			</td>
		</tr>
	</table>
	<?php break; case "3": ?>
	<table id="ilist" style="font-size:11px;">
		<thead>
			<tr>
				<th width=20%>PLATE NO.</th>
				<th width=20%>CATEGORY</th>
				<th width=20%>MAKE/BRAND</th>
				<th width=20%>MODEL</th>
				<th width=20%>DESIGNATED PARKING</th>
			</tr>
		</thead>
	</table>
	<table>
	<tr>
		<td align=left colspan=2 style="padding-top:5px;">
			<a href="#" class="topClickers" onClick="javascript:newV();"><img src="images/icons/add-2.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Add Entry</a>&nbsp;&nbsp;
			<a href="#" class="topClickers" onClick="javascript:editV();"><img src="images/icons/tests256.png" width=16 height=16 border=0 align="absmiddle">&nbsp;View Entry</a>&nbsp;&nbsp;
			<a href="#" class="topClickers" onClick="javascript:deleteVehicle();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Entry</a>
		</td>
	</tr>
	</table>
	<?php break; } ?>
</form>

<form name="changeModPage" id="changeModPage" action="homeowner.details.php" method="GET" >
	<input type="hidden" name="pid" id="pid" value="<?php echo $_GET['pid']; ?>">
	<input type="hidden" name="mod" id="mod">
</form>
<div id="cameraFrame" style="display: none;"></div>

</body>
</html>