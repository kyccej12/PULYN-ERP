<?php
	session_start();
	include("includes/dbUSE.php");
	if(isset($_GET['eid']) && $_GET['eid'] != "") { 
		$res = getArray("select *,if(birthdate != '0000-00-00',date_format(birthdate,'%m/%d/%Y'),'') as bdate,if(hired_date != '0000-00-00',date_format(hired_date,'%m/%d/%Y'),'') as hdate, if(date_regularized != '0000-00-00',date_format(date_regularized,'%m/%d/%Y'),'') as dreg, if(ojt_from != '0000-00-00',date_format(ojt_from,'%m/%d/%Y'),'') as ojtf, if(ojt_to != '0000-00-00',date_format(ojt_to,'%m/%d/%Y'),'') as ojt2, if(probi_from != '0000-00-00',date_format(probi_from,'%m/%d/%Y'),'') as pbf, if(probi_to != '0000-00-00',date_format(probi_to,'%m/%d/%Y'),'') as pb2, if(date_exited!='0000-00-00',date_format(date_exited,'%m/%d/%Y'),'') as dexit, if(contract_from != '0000-00-00',date_format(contract_from,'%m/%d/%Y'),'') as cdtf, if(contract_to != '0000-00-00',date_format(contract_to,'%m/%d/%Y'),'') as cdt2 from hris.e_master where record_id = '$_GET[eid]';"); 
		list($next) = getArray("select record_id from hris.e_master where company='$_SESSION[company]' and record_id > $_GET[eid] order by record_id asc limit 1;");
		list($prev) = getArray("select record_id from hris.e_master where company='$_SESSION[company]' and record_id < $_GET[eid] order by record_id desc limit 1;");
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
	<script language="javascript" src="js/jquery.dialogextend.js"></script>
	<script language="javascript" src="js/hrd.js"></script>
	<script>
		function saveEInfo(eid) {
			var msg = "";
			
			if($("#ee_id").val() == "") { msg = msg + "- You did not specify Employee's ID Number<br/>"; }
			if($("#ee_lname").val() == "") { msg = msg + "- You did not specify Employee's Last Name<br/>"; }
			if($("#ee_lname").val() == "") { msg = msg + "- You did not specify Employee's First Name<br/>"; }
			if($("#ee_mname").val() == "") { msg = msg + "- You did not specify Employee's Full Middle Name<br/>"; }
			if($("#ee_hired").val() == "") { msg = msg + "- You did not specify the date Employee was hired<br/>"; }
			if($("#ee_company").val() == "") { msg = msg + "- You did not specify the company where the employee is assigned<br/>"; }
			if($("#ee_branch").val() == "") { msg = msg + "- You did not specify the branch where the employee is assigned<br/>"; }
			if($("#ee_desg").val() == "") { msg = msg + "- You did not specify employee's designation<br/>"; }
			
			if(msg!="") {
				parent.sendErrorMessage(msg);
			} else {
				$.post("payroll.datacontrol.php", { mod: "checkDupID", eid: eid, id_no: $("#ee_id").val(), sid: Math.random() }, function(data) {
					if(data == "NODUPLICATE") {
						var url = $(document.frmemployee).serialize();
							url = "mod=saveEInfo&"+url;
						$.post("payroll.datacontrol.php", url);
						alert("Employee Info Successfuly Saved!"); 
						parent.closeDialog("#empdetails");
						parent.showEmployees();
					} else { parent.sendErrorMessage("Unable to save this information. A duplicate Employee ID No. has been detected!"); }
				},"html");	
			}
		}
		
		function deleteEE(eid) {
			if(confirm("Are you sure you want to put this file to archives?") == true) {
				$.post("payroll.datacontrol.php", { mod: "deleteEE", eid: eid, sid: Math.random() }, function() { 
					alert("Employee Record Successfully Archived!"); 
					parent.closeDialog("#empdetails");
					parent.showEmployees(1);
				});
			}
		}

		function getBranchList(company) {
			$.post("src/sjerp.php", { mod: "getBranchList", company: company, sid: Math.random() },function(data) {
				$("#ee_branch").html(data);
			},"html");
		}

		function show201(eid) {
			$.post("payroll.datacontrol.php", { mod: "getEmpName", record_id: eid, sid: Math.random() }, function(data) {
				$("#employee201").dialog({title: "Employee 201 File ("+data+")", width: 480, height: 320 }).dialogExtend({
					"closable" : true,
					"maximizable" : false,
					"minimizable" : true
				});
			});
		}
		
		function computeTotalSalary() {
			if($("#ee_paytype").val() == "SEMI") {
			    var monthly = parseFloat(parent.stripComma($("#ee_salary_monthly").val()));
				var rice = parseFloat(parent.stripComma($("#rice_subsidy").val()));
				var clothing = parseFloat(parent.stripComma($("#clothing").val()));
				var laundry = parseFloat(parent.stripComma($("#laundry").val()));
				var others = parseFloat(parent.stripComma($("#other_non_tax").val()));
				var insurance = parseFloat(parent.stripComma($("#insurance").val()));

				
				var total = (+monthly + +rice + +clothing + +laundry + +others + +insurance);
					total = total.toFixed(2);
				$("#ee_salary_total").val(parent.kSeparator(total));
			}
		}
		
		$(function() { 
			$("#ee_bday").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear() });
			$("#ee_hired").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear() }); 
			$("#ojt_from").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear()}); 
			$("#ojt_to").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear()}); 
			$("#probi_from").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear()}); 
			$("#probi_to").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear()}); 
			$("#date_regularized").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear()}); 
			$("#date_exited").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear()}); 
			$("#contract_from").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + new Date().getFullYear()}); 
			$("#contract_to").datepicker({ changeMonth: true, changeYear: true, yearRange: '1920:' + ((new Date().getFullYear()) + 3) }); 
		});
		
		function getSections(dept) {
			$.post("src/sjerp.php", { mod: "getSections", dept: dept, sid: Math.random() }, function(data) {
				$("#ee_section").html(data);
			},"html");
		}
		
	</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #d6d6d6; border: 1px solid #7f7f7f; border-right: 2px solid #7f7f7f;" >
	<form name="frmemployee" id="frmemployee">
		<input type="hidden" id = "eid" name="eid" value="<?php echo $_GET['eid']; ?>">
		<tr>
			<td valign=top width="50%" style="padding: 5px;" >
				<table border="0" cellpadding="0" cellspacing="0" width=100%>
					<tr><td class="spandix-l" width=35%>Last Name :</td>
						<td align=left>
							<input type="text" name="ee_lname" id="ee_lname" style="width: 90%;" class=nInput value="<?php echo $res['lname']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>First Name :</td>
						<td align=left>
							<input type="text" name="ee_fname" id="ee_fname" style="width: 90%;" class=nInput value="<?php echo $res['fname']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>Middle Name :</td>
						<td align=left>
							<input type="text" name="ee_mname" id="ee_mname" style="width: 90%;" class=nInput value="<?php echo $res['mname']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" valign=top width=35%>Address :</td>
						<td align=left>
							<textarea name="ee_address" id="ee_address" rows=1 style="width: 90%;"><?php echo $res['address']; ?></textarea>
						</td>
					</tr>
					<tr><td height=4></td></tr>
					<tr><td class="spandix-l" width=35%>In case of Emergency :</td>
						<td align=left>
							<input type="text" name="emergency_contact" id="emergency_contact" style="width: 90%;" class=nInput value="<?php echo $res['emergency_contact']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>Birth Date :</td>
						<td align=left>
							<input type="text" name="ee_bday" id="ee_bday" style="width: 60%;" class=nInput value="<?php echo $res['bdate']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Email :</td>
						<td align=left>
							<input type="text" name="ee_email" id="ee_email" style="width: 60%;" class=nInput value="<?php echo $res['email']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Contact Nos. :</td>
						<td align=left>
							<input type="text" name="ee_contactnos" id="ee_contactnos" style="width: 60%;" class=nInput value="<?php echo $res['contact_no']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Gender :</td>
						<td align=left>
							<select name="ee_gender" id="ee_gender" style="width: 70%;" class=nInput>
								<option value="M" <?php if($res['gender'] == 'M') { echo "selected"; } ?>>Male</option>
								<option value="F" <?php if($res['gender'] == 'F') { echo "selected"; } ?>>Female</option>
							</select>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Blood Type :</td>
						<td align=left>
							<select name="ee_bloodtype" id="ee_bloodtype" style="width: 70%;" class=nInput>
								<option value="A+" <?php if($res['bloodtype'] == 'A+') { echo "selected"; } ?>>A+</option>
								<option value="A-" <?php if($res['bloodtype'] == 'A-') { echo "selected"; } ?>>A-</option>
								<option value="B+" <?php if($res['bloodtype'] == 'B+') { echo "selected"; } ?>>B+</option>
								<option value="B-" <?php if($res['bloodtype'] == 'B-') { echo "selected"; } ?>>B-</option>
								<option value="O+" <?php if($res['bloodtype'] == 'O+') { echo "selected"; } ?>>O+</option>
								<option value="O-" <?php if($res['bloodtype'] == 'O-') { echo "selected"; } ?>>O-</option>
								<option value="AB+" <?php if($res['bloodtype'] == 'AB+') { echo "selected"; } ?>>AB+</option>
								<option value="AB-" <?php if($res['bloodtype'] == 'AB-') { echo "selected"; } ?>>AB-</option>
							</select>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Civil Status :</td>
						<td align=left>
							<select name="ee_cstatus" id="ee_cstatus" style="width: 70%;" class=nInput>
								<option value="Single" <?php if($res['cstatus'] == 'Single') { echo "selected"; } ?>>Single</option>
								<option value="Married" <?php if($res['cstatus'] == 'Married') { echo "selected"; } ?>>Married</option>
								<option value="Widow" <?php if($res['cstatus'] == 'Widow') { echo "selected"; } ?>>Widow/Widower</option>
								<option value="Separated" <?php if($res['cstatus'] == 'Separated') { echo "selected"; } ?>>Legally Separated</option>
								<option value="Live-in" <?php if($res['cstatus'] == 'Live-in') { echo "selected"; } ?>>Living-in With Partner</option>
							</select>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr>
						<td class="spandix-l" width="35%">Status :</td>
						<td align="left">
							<select name="ee_status" id="ee_status" style="width: 70%;" class=nInput>
								<option value="REGULAR" <?php if($res['status'] == 'REGULAR') { echo "selected"; } ?>>Regular</option>
								<option value="PROBATIONARY" <?php if($res['status'] == 'PROBATIONARY') { echo "selected"; } ?>>Probationary</option>
								<option value="CONTRACTUAL" <?php if($res['status'] == 'CONTRACTUAL') { echo "selected"; } ?>>Contractual</option>
								<option value="SEASONAL" <?php if($res['status'] == 'SEASONAL') { echo "selected"; } ?>>Seasonal</option>
								<option value="PROJECT" <?php if($res['status'] == 'PROJECT') { echo "selected"; } ?>>Project Based</option>
							</select>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr>
						<td class="spandix-l" width="35%">Employee Type :</td>
						<td align="left">
							<select name="ee_type" id="ee_type" style="width: 70%;" class=nInput>
								<option value="RANK" <?php if($res['etype'] == 'RANK') { echo "selected"; } ?>>Rank-in-File</option>
								<option value="SUP" <?php if($res['etype'] == 'SUP') { echo "selected"; } ?>>Supervisory</option>
								<option value="MAN" <?php if($res['etype'] == 'MAN') { echo "selected"; } ?>>Managerial</option>
								<option value="EXEC" <?php if($res['etype'] == 'EXEC') { echo "selected"; } ?>>Executive Level</option>
							</select>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr>
						<td class="spandix-l" width="35%">Pay Class :</td>
						<td align="left">
							<select name="ee_paytype" id="ee_paytype" style="width: 70%;" class=nInput>
								<option value="SEMI" <?php if($res['pay_type'] == 'SEMI') { echo "selected"; } ?>>Semi-Monthly</option>
								<option value="WEEKLY" <?php if($res['pay_type'] == 'WEEKLY') { echo "selected"; } ?>>Weekly</option>
								<option value="DAILIES" <?php if($res['pay_type'] == 'DAILIES') { echo "selected"; } ?>>Daily</option>
								<option value="HOURLY" <?php if($res['pay_type'] == 'HOURLY') { echo "selected"; } ?>>Hourly</option>
							</select>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>ID No. :</td>
						<td align=left>
							<input type="text" name="ee_id" id="ee_id" style="width: 70%;" class=nInput value="<?php echo $res['id_no']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>Biometrics ID :</td>
						<td align=left>
							<input type="text" name="bio_id" id="bio_id" style="width: 70%;" class=nInput value="<?php echo $res['bio_id']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>Payroll Acct # :</td>
						<td align=left>
							<input type="text" name="bank_acct" id="bank_acct" style="width: 70%;" class=nInput value="<?php echo $res['bank_acct']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr>
						<td class="spandix-l" width="35%">Company :</td>
						<td align="left">
							<select name="ee_company" id="ee_company" style="width: 70%;" class=nInput onChange="javascript: getBranchList(this.value);">
								<option value = "">- Select Company -</option>
								<?php
									$tt = dbquery("select company_id, short_name from companies;");
									while(list($uu,$vv) = mysql_fetch_array($tt)) {
										echo "<option value='$uu' ";
										if($res['company'] == "$uu") { echo "selected"; }
										echo ">$vv</option>";
									}
								?>
								
							</select>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr>
						<td class="spandix-l" width="35%">Branch Assigned :</td>
						<td align="left">
							<select name="ee_branch" id="ee_branch" style="width: 70%;" class=nInput>
								<?php
									$t = dbquery("select branch_code, branch_name from options_branches where company = '$res[company]' order by branch_code;");
									while(list($u,$v) = mysql_fetch_array($t)) {
										echo "<option value='$u' ";
										if($res['branch'] == "$u") { echo "selected"; }
										echo ">$v</option>";
									}
								?>
							</select>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Department :</td>
						<td align=left>
							<select name="ee_dept" id="ee_dept" style="width: 70%;" class=nInput onchange="javascript: getSections(this.value);">
								<?php
									$pp = dbquery("select dept_code,dept_name from options_dept;");
									while(list($_1, $_2) = mysql_fetch_array($pp)) {
										echo "<option value='$_1' ";
										if($res['department'] == $_1) { echo "selected"; }
										echo ">$_2</option>";
									}
								?>
							</select>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Section :</td>
						<td align=left>
							<select name="ee_section" id="ee_section" style="width: 70%;" class=nInput>
								<option value="">- N/A -</option>
								<?php
									$g = dbquery("select section_code, section_name from options_sections where parent_dept = '$res[department]';");
									while(list($n,$m) = mysql_fetch_array($g)) {
										echo "<option value='$n' ";
										if($res['section'] == "$n") { echo "selected"; }
										echo ">$m</option>";
									}
								?>
							</select>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Designation :</td>
						<td align=left>
							<input type="text" id="ee_desg" name="ee_desg" style="width: 70%;" class=nInput value="<?php echo $res['designation']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">HMO :</td>
						<td align=left>
							<input type="text" id="ee_hmo" name="ee_hmo" style="width: 70%;" class=nInput value="<?php echo $res['hmo']; ?>">
						</td>
					</tr>
				</table>
			</td>
			<td valign=top width="50%" style="padding: 5px;">
				<table width=100% cellpadding=0 cellspacing=0>
					<tr><td class="spandix-l" width="35%">Reports To :</td>
						<td align=left>
							<input type="text" id="reports_to" name="reports_to" style="width: 70%;" class=nInput value="<?php echo $res['reports_to']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>Date Hired :</td>
						<td align=left>
							<input type="text" name="ee_hired" id="ee_hired" style="width: 70%;" class=nInput value="<?php echo $res['hdate']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
						<tr><td class="spandix-l" width=35%>OJT Period <i>(OJT Only)</i> :</td>
						<td align=left>
							<input type="text" name="ojt_from" id="ojt_from" style="width: 85px;" class=nInput value="<?php echo $res['ojtf']; ?>"> <span class="spandix-l">to</span> <input type="text" name="ojt_to" id="ojt_to" style="width: 85px;" class=nInput value="<?php echo $res['ojt2']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>Contractual Period :</td>
						<td align=left>
							<input type="text" name="contract_from" id="contract_from" style="width: 85px;" class=nInput value="<?php echo $res['cdtf']; ?>"> <span class="spandix-l">to</span> <input type="text" name="contract_to" id="contract_to" style="width: 85px;" class=nInput value="<?php echo $res['cdt2']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>Probitionary Period :</td>
						<td align=left>
							<input type="text" name="probi_from" id="probi_from" style="width: 85px;" class=nInput value="<?php echo $res['pbf']; ?>"> <span class="spandix-l">to</span> <input type="text" name="probi_to" id="probi_to" style="width: 85px;" class=nInput value="<?php echo $res['pb2']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>Date Regularized :</td>
						<td align=left>
							<input type="text" name="date_regularized" id="date_regularized" style="width: 70%;" class=nInput value="<?php echo $res['dreg']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr>
						<td class="spandix-l" width="35%">Exit Status :</td>
						<td align="left">
							<select name="ee_exit_status" id="ee_exit_status" style="width: 70%;" class=nInput>
								<option value="">- N/A -</option>
								<option value="RESIGNED" <?php if($res['exit_status'] == 'RESIGNED') { echo "selected"; } ?>>Voluntary Resignation</option>
								<option value="TERMINATED" <?php if($res['exit_status'] == 'TERMINATED') { echo "selected"; } ?>>Terminated w/ Cause</option>
								<option value="EOC" <?php if($res['exit_status'] == 'EOC') { echo "selected"; } ?>>End of Contract</option>
								<option value="AWOL" <?php if($res['exit_status'] == 'AWOL') { echo "selected"; } ?>>AWOL</option>
								<option value="DEATH" <?php if($res['exit_status'] == 'DEATH') { echo "selected"; } ?>>Death</option>
							</select>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>Date of Exit :</td>
						<td align=left>
							<input type="text" name="date_exited" id="date_exited" style="width: 70%;" class=nInput value="<?php echo $res['dexit']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>TIN # :</td>
						<td align=left>
							<input type="text" name="ee_tin" id="ee_tin" style="width: 70%;" class=nInput value="<?php echo $res['tin_no']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>SSS ID No. :</td>
						<td align=left>
							<input type="text" name="ee_sss" id="ee_sss" style="width: 70%;" class=nInput value="<?php echo $res['sss_id']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>PAG-IBIG ID No. :</td>
						<td align=left>
							<input type="text" name="ee_pagibig" id="ee_pagibig" style="width: 70%;" class=nInput value="<?php echo $res['pagibig_id']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width=35%>PhilHealth ID No. :</td>
						<td align=left>
							<input type="text" name="ee_philhealth" id="ee_philhealth" style="width: 70%;" class=nInput value="<?php echo $res['philhealth_id']; ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Leave Incentives :</td>
						<td align=left>
							<input type="text" name="ee_sil" id="ee_sil" style="width: 40%;" class=nInput value="<?php echo number_format($res['leave_incentives']); ?>"> <span class="spandix-l">(in Days)</span>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
						<tr>
						<td class="spandix-l" width="35%">Default DTR Sched :</td>
						<td align="left">
							<select name="ee_paysched" id="ee_paysched" style="width: 40%;" class=nInput>
								<option value="1" <?php if($res['paysched'] == '1') { echo "selected"; } ?> title='1pm - 10pm - 7am - 4pm - 1am'>Schedule 1</option>
								<option value="2" <?php if($res['paysched'] == '2') { echo "selected"; } ?> title='2pm - 11pm - 8am - 5pm - 2am'>Schedule 2</option>
								<option value="3" <?php if($res['paysched'] == '3') { echo "selected"; } ?> title='Office/CSU: 7am - 4pm'>Schedule 3</option>
								<option value="4" <?php if($res['paysched'] == '4') { echo "selected"; } ?> title='Office: 8am - 5pm'>Schedule 4</option>
								<option value="5" <?php if($res['paysched'] == '5') { echo "selected"; } ?> title='Office: 9am - 6pm'>Schedule 5</option>
							</select>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Daily Rate :</td>
						<td align=left>
							<input type="text" name="ee_salary_daily" id="ee_salary_daily" style="width: 40%;" class=nInput value="<?php echo number_format($res['daily_rate'],2); ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Monthly Rate :</td>
						<td align=left>
							<input type="text" name="ee_salary_monthly" id="ee_salary_monthly" style="width: 40%;" class=nInput value="<?php echo number_format($res['monthly_rate'],2); ?>" onchange="javascript: computeTotalSalary();">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Rice Subsidy :</td>
						<td align=left>
							<input type="text" name="rice_subsidy" id="rice_subsidy" style="width: 40%;" class=nInput value="<?php echo number_format($res['rice_subsidy'],2); ?>" onchange="javascript: computeTotalSalary();">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Uniform Allowance :</td>
						<td align=left>
							<input type="text" name="clothing" id="clothing" style="width: 40%;" class=nInput value="<?php echo number_format($res['clothing'],2); ?>" onchange="javascript: computeTotalSalary();">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Laundry Allowance :</td>
						<td align=left>
							<input type="text" name="laundry" id="laundry" style="width: 40%;" class=nInput value="<?php echo number_format($res['laundry'],2); ?>" onchange="javascript: computeTotalSalary();">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Insurance :</td>
						<td align=left>
							<input type="text" name="insurance" id="insurance" style="width: 40%;" class=nInput value="<?php echo number_format($res['insurance'],2); ?>" onchange="javascript: computeTotalSalary();">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Others (Non-Taxable) :</td>
						<td align=left>
							<input type="text" name="other_non_tax" id="other_non_tax" style="width: 40%;" class=nInput value="<?php echo number_format($res['other_non_tax'],2); ?>" onchange="javascript: computeTotalSalary();">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Total Salary :</td>
						<td align=left>
							<input type="text" name="ee_salary_total" id="ee_salary_total" style="width: 40%;" class=nInput value="<?php echo number_format($res['total_salary'],2); ?>">
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l">Tax Bracket :</td>
						<td align=left>
							<select class=nInput style="width: 40%" name="ee_taxcode" id="ee_taxcode">
								<option value="" <?php if($res['tax_bracket'] == '') { echo "selected"; } ?>>- N/A -</option>
								<option value="Z" <?php if($res['tax_bracket'] == 'Z') { echo "selected"; } ?> title="">Z</option>
								<option value="S" <?php if($res['tax_bracket'] == 'S') { echo "selected"; } ?> title="Single w/out quilified dependent">S</option>
								<option value="S1" <?php if($res['tax_bracket'] == 'S1') { echo "selected"; } ?> title="Single w/ one quilified dependent">S1</option>
								<option value="S2" <?php if($res['tax_bracket'] == 'S2') { echo "selected"; } ?> title="Single w/ two quilified dependents">S2</option>
								<option value="S3" <?php if($res['tax_bracket'] == 'S3') { echo "selected"; } ?> title="Single w/ three quilified dependents">S3</option>
								<option value="S4" <?php if($res['tax_bracket'] == 'S4') { echo "selected"; } ?> title="Single w/ four quilified dependents">S4</option>
								<option value="ME" <?php if($res['tax_bracket'] == 'ME') { echo "selected"; } ?> title="Married w/out quilified dependent">ME</option>
								<option value="ME1" <?php if($res['tax_bracket'] == 'ME1') { echo "selected"; } ?> title="Married w/ one quilified dependent">ME 1</option>
								<option value="ME2" <?php if($res['tax_bracket'] == 'ME2') { echo "selected"; } ?> title="Married w/ two quilified dependents">ME 2</option>
								<option value="ME3" <?php if($res['tax_bracket'] == 'ME3') { echo "selected"; } ?> title="Married w/ three quilified dependents">ME 3</option>
								<option value="ME4" <?php if($res['tax_bracket'] == 'ME4') { echo "selected"; } ?> title="Married w/ four quilified dependents">ME 4</option>
							</select>
						</td>
					</tr>
					<tr><td height=4 colspan="2"></td></tr>
					<tr><td class="spandix-l" width="35%">Withholding Tax :</td>
						<td align=left>
							<input type="text" name="ee_wtax" id="ee_wtax" style="width: 40%;" class=nInput value="<?php echo number_format($res['wtax'],2); ?>">
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan=2><hr style="width:95%"></hr></td></tr>
		<tr>
			<td colspan=2 align=center>
				<?php if($prev) { ?>
					<button type="button" onClick="parent.showEInfo(<?php echo $prev; ?>);" class="buttonding"><img src="images/icons/previous.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Previous Record</button>
				<?php } ?>
				<button type="button" onClick="saveEInfo(<?php echo $_GET['eid']; ?>);" class="buttonding"><img src="images/icons/floppy.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Save Employee Info.</button>
				<?php if(isset($_GET['eid']) && $_GET['eid'] != "") { ?>
					<button type="button" onClick="javascript: show201(<?php echo $_GET['eid']; ?>);" class="buttonding"><img src="images/icons/customerinfo.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Employee 201 File</b></button>
					<button type="button" onClick="deleteEE('<?php echo $_GET['eid']; ?>');" class="buttonding"><img src="images/icons/archive.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Archive This File</button>
				<?php } ?>
				<?php if($next) { ?>
					<button type="button" onClick="parent.showEInfo(<?php echo $next; ?>);" class="buttonding"><img src="images/icons/forward.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Next Record</button>
				<?php } ?>
			</td>
		</tr>
		<tr><td height=4></td></tr>
	 </form>
 </table>
 <div id="employee201" style="display: none;">
	<div style="padding: 20px;">
		<div class="fileObjects"><a href="#" onclick="parent.showIDs(<?php echo $_GET['eid']; ?>);"><img src="images/icons/camera.png" width=60 height=60 /><br/><br/>ID Pictures</a></div>
		<div class="fileObjects"><a href="#" onclick="parent.showFam(<?php echo $_GET['eid']; ?>);"><img src="images/icons/family.png" width=60 height=60 /><br/><br/>Family Background</a></div>
		<div class="fileObjects"><a href="#" onclick="parent.showEdu(<?php echo $_GET['eid']; ?>);"><img src="images/icons/education.png" width=60 height=60 /><br/><br/>Educational Background</a></div>
		<div class="fileObjects"><a href="#" onclick="parent.showErecord(<?php echo $_GET['eid']; ?>);"><img src="images/icons/employment.png" width=60 height=60 /><br/><br/>Work Experience (External)</a></div>
		<div class="fileObjects"><a href="#" onclick="parent.showErecord2(<?php echo $_GET['eid']; ?>);"><img src="images/icons/employment.png" width=60 height=60 /><br/><br/>Work Experience (Internal)</a></div>
		<div class="fileObjects"><a href="#" onclick="parent.showCert(<?php echo $_GET['eid']; ?>);"><img src="images/icons/certificates.png" width=60 height=60 /><br/><br/>Memos, Certificates & Clearances</a></div>
	</div>
</div>
</body>
</html>
<?php mysql_close($con);