<?php
	include("hrReports.php");
	
	$ptypeOption = '';
	$ptQuery = $o->dbquery("SELECT period_id, CONCAT(DATE_FORMAT(period_start,'%m/%d/%y'),'-',DATE_FORMAT(period_end,'%m/%d/%y')) FROM omdcpayroll.pay_periods ORDER BY period_end DESC;");
	while($ptRow = $ptQuery->fetch_array()) {
		$ptypeOption .= "<option value='$ptRow[0]'>$ptRow[1]</option>";	
	}
	
	$batchOption = "<option value=''>- SELECT BATCH -</option>";
	$_sq = $o->dbquery("select batch_id,batch_details from omdcpayroll.pay_batch order by batch_id");
	while($batchrow = $_sq->fetch_array()) {
		$batchOption .= "<option value='$batchrow[0]' title='$batchrow[1]'>$batchrow[1]</option>";
	}

	$periodIdOption = "<option value=''>- SELECT Period -</option>";
	$_q = $o->dbquery("select period_id,concat(date_format(period_start,'%m/%d/%Y'),' - ',date_format(period_end,'%m/%d/%Y')) from omdcpayroll.pay_periods where payroll_batch = '1' order by period_end desc limit 10;");
	while(list($a,$b) =$_q->fetch_array()) {
		$periodIdOption .= "<option value='$a'>$b</option>";
	}
	
	$deptOption = '';
	$doQuery = $o->dbquery("SELECT id, dept_name FROM omdcpayroll.options_dept ORDER BY dept_name asc;");
	while(list($did,$dname) = $doQuery->fetch_array()) {
		$deptOption .= "<option value='$did'>$dname</option>";	
	}
	
?>

<div id="plotSchedules" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Payroll Batch :</span></td>
			<td>
				<select name="plot_batch" id="plot_batch" style="width: 90%; font-size: 11px;" class="gridInput" onchange="javascript: populatePeriods(this.value,'plot_cutoff');" />
					<?php echo $batchOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<select name="plot_cutoff" id="plot_cutoff" style="width: 90%; font-size: 11px;" class="gridInput" />
					<?php echo $periodIdOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td><span class="spandix-l">Department :</span></td>
			<td>
				<select id="plot_dept" name="plot_dept" style="width: 90%; font-size: 11px;" class="gridInput" >
					<?php echo $deptOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="plotSchedules();" class="buttonding" style="font-size: 11px;"><img src="images/icons/dtr.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Plot Employee Schedules</button>
			</td>
		</tr>
	</table>
</div>

<div id="manageDTR" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Payroll Batch :</span></td>
			<td>
				<select name="mdtr_batch" id="mdtr_batch" style="width: 90%; font-size: 11px;" class="gridInput" onchange="javascript: populatePeriods(this.value,'mdtr_cutoff'); populateEmployees(this.value,'mdtr_emp');" />
					<?php echo $batchOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<select name="mdtr_cutoff" id="mdtr_cutoff" style="width: 90%; font-size: 11px;" class="gridInput" />
					<?php echo $periodIdOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td><span class="spandix-l">Employee :</span></td>
			<td>
				<select id="mdtr_emp" name="mdtr_emp" style="width: 90%; font-size: 11px;" class="gridInput" >
					
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="getDTR();" class="buttonding" style="font-size: 11px;"><img src="images/icons/dtr.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;View & Manage DTR</button>
			</td>
		</tr>
	</table>
</div>
<div id="manageOT" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Payroll Batch :</span></td>
			<td>
				<select name="mot_batch" id="mot_batch" style="width: 90%; font-size: 11px;" class="gridInput" onchange="javascript: populatePeriods(this.value,'mot_cutoff'); populateEmployees(this.value,'mdtr_emp');" />
					<?php echo $batchOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<select name="mot_cutoff" id="mot_cutoff" style="width: 90%; font-size: 11px;" class="gridInput">
					<?php echo $empOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="getOT();" class="buttonding" style="font-size: 11px;"><img src="images/icons/dtr.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;View & Manage DTR</button>
			</td>
		</tr>
	</table>
</div>
<div id="otSummary" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<select name="otCutoff" id="otCutoff" style="width: 90%; font-size: 11px;" class="gridInput" />
				<?php echo $periodIdOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td><span class="spandix-l">Department :</span></td>
			<td>
				<select id="otProj" name="otProj" style="width: 90%; font-size: 11px;" class="gridInput">
					<?php echo $projOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="processOT();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="printDTR" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Payroll Batch :</span></td>
			<td>
				<select name="pdtr_batch" id="pdtr_batch" style="width: 90%; font-size: 11px;" class="gridInput" onchange="javascript: populatePeriods(this.value,'pdtr_cutoff');" />
					<?php echo $batchOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<select name="pdtr_cutoff" id="pdtr_cutoff" style="width: 90%; font-size: 11px;" class="gridInput" />
				<?php echo $periodIdOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td><span class="spandix-l">Department :</span></td>
			<td>
				<select id="pdtr_dept" name="pdtr_dept" style="width: 90%; font-size: 11px;" class="gridInput">
					<option value="">- All Departments -</option>
					<?php echo $deptOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="printDTR(1);" class="buttonding" style="font-size: 11px;"><img src="images/icons/print.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Print Daily Time Record</button>
			</td>
		</tr>
	</table>
</div>
<div id="printTardy" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td><span class="spandix-l">Department :</span></td>
			<td>
				<select id="tardyDept" name="tardyDept" style="width: 90%; font-size: 11px;" class="gridInput">
					<?php echo $deptOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<?php
			$o->_structInput('From','35%','tardyDtf','gridInput','width: 90%;',date('m/01/Y')); 
			$o->_structInput('To','35%','tardyDt2','gridInput','width: 90%;',date('m/d/Y')); 
		?>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="printTardy();" class="buttonding" style="font-size: 11px;"><img src="images/icons/print.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Print Tardiness Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="processPay" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Payroll Batch :</span></td>
			<td>
				<select name="payBatch" id="payBatch" style="width: 90%; font-size: 11px;" class="gridInput" onchange="javascript: populatePeriods(this.value,'payCutoff');" />
					<?php echo $batchOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<select name="payCutoff" id="payCutoff" style="width: 90%; font-size: 11px;" class="gridInput" />
				<?php echo $periodIdOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td><span class="spandix-l">Department :</span></td>
			<td>
				<select id="payDept" name="payDept" style="width: 90%; font-size: 11px;" class="gridInput">
					<option value="">- All Departments -</option>
					<?php echo $deptOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="processPay();" class="buttonding" style="font-size: 11px;"><img src="images/icons/processraw.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Process Payroll</button>
			</td>
		</tr>
	</table>
</div>
<div id="printPaySlip" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Batch :</span></td>
			<td>
				<select name="payslipBatch" id="payslipBatch" style="width: 90%; font-size: 11px;" class="gridInput" onchange="javascript: populatePeriods(this.value,'payslipCutoff');" />
					<?php echo $batchOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<select name="payslipCutoff" id="payslipCutoff" style="width: 90%; font-size: 11px;" class="gridInput" />
				<?php echo $periodIdOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td><span class="spandix-l">Department :</span></td>
			<td>
				<select id="payslipDept" name="payslipDept" style="width: 90%; font-size: 11px;" class="gridInput" onchange="javascript: parent.getEmployeesByDept(this.value,$('#payslipBatch').val(),'payslipEmployee');">
					<option value=""> - All Departments -</option>
					<?php
						echo $deptOption;
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Employee :</span></td>
			<td>
				<select name="payslipEmployee" id="payslipEmployee" style="width: 90%; font-size: 11px;" class="gridInput" />
					<option value="">- All Employees -</option>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="printPaySlip();" class="buttonding" style="font-size: 11px;"><img src="images/icons/print.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Print Payslip</button>
			</td>
		</tr>
	</table>
</div>
<div id="paySummary" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Batch :</span></td>
			<td>
				<select name="paySBatch" id="paySBatch" style="width: 90%; font-size: 11px;" class="gridInput" onchange="javascript: populatePeriods(this.value,'paySCutoff');" />
					<?php echo $batchOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<select name="paySCutoff" id="paySCutoff" style="width: 90%; font-size: 11px;" class="gridInput" />
				<?php echo $periodIdOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td><span class="spandix-l">Department :</span></td>
			<td>
				<select id="paySDept" name="paySDept" style="width: 90%; font-size: 11px;" class="gridInput">
					<option value=""> - All Departments -</option>
					<?php echo $deptOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="processPaySummary();" class="buttonding" style="font-size: 11px;"><img src="images/icons/customer-report-icon.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Print Payroll Resgister</button>
				<button onClick="processPaySummaryExcel();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export to Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="loanBalances" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td><span class="spandix-l">Employee :</span></td>
			<td>
				<select id="lb_emp" name="lb_emp" style="width: 90%; font-size: 11px;" class="gridInput" >
					<?php echo $empOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="getEmpLoans();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="printStatutory" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Department :</span></td>
			<td align=left>
				<select name="statProj" id="statProj" style="width: 90%; font-size: 11px;" class="gridInput">
					<option value=""> - All Departments -</option>
					<?php echo $projOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<?php
			$o->_structMonths('statMonth','width: 90%; font-size: 11px;','gridInput');
			$o->_structInput('Year','35%','statYear','gridInput','width: 90%;',date('Y')); 
		?>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="printStatutory();" class="buttonding" style="font-size: 11px;"><img src="images/icons/print.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
				<button onClick="exportStatutory();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export to Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="printGrossCompensation" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Department :</span></td>
			<td align=left>
				<select name="grossProj" id="grossProj" style="width: 90%; font-size: 11px;" class="gridInput">
					<option value=""> - All Departments -</option>
					<?php echo $projOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<?php
			$o->_structInput('Year','35%','grossYear','gridInput','width: 100px;',date('Y')); 
		?>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="printGrossCompensation();" class="buttonding" style="font-size: 11px;"><img src="images/icons/print.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="userChangePass" style="display: none;">
	<form name="frmPass" id="frmPass">
		<input type="hidden" name="myUID" id="myUID" value="<?php echo $_SESSION['userid']; ?>">
		<table border="0" cellpadding="0" cellspacing="0" width=100%>
			<tr><td height=4></td></tr>
			<tr>
				<td width=35%><span class="spandix-l">New Password :</span></td>
				<td>
					<input type="password" id="pass1" class="nInput" style="width: 80%;"  />
				</td>
			</tr>
			<tr><td height=4></td></tr>
			<tr>
				<td width=35%><span class="spandix-l">Confirm New Password :</span></td>
				<td>
					<input type="password" id="pass2" class="nInput" style="width: 80%;" />
				</td>
			</tr>
			</table>
	</form>
</div>
<div id="bdoTransmittal" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
			<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<select name="bdo_cutoff" id="bdo_cutoff" style="width: 90%; font-size: 11px;" class="gridInput" />
					<?php echo $ptypeOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td><span class="spandix-l">Location :</span></td>
			<td>
				<select id="bdo_proj" name="bdo_proj" style="width: 90%; font-size: 11px;" class="gridInput">
					<option value=""> - All Projects -</option>
					<?php echo $projOption; ?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Credit Date :</span></td>
			<td>
				<input type="text" name="bdo_creditdate" id="bdo_creditdate" style="width: 90%; font-size: 11px;" class="gridInput" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Batch Code :</span></td>
			<td>
				<input type="text" name="bdo_batchcode" id="bdo_batchcode" style="width: 90%; font-size: 11px;" class="gridInput" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateTransmittal();" class="buttonding" style="font-size: 11px;"><img src="images/icons/processraw.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Create Transmittal</button>
				<button onClick="generateTransmittalX();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Create Transmittal in Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="printThirteenth" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Calendar Year :</span></td>
			<td>
				<select name="13_year" id="13_year" style="width: 90%; font-size: 11px;" class="gridInput" />
					<option value='2020'>2020</option>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Department :</span></td>
			<td>
				<select name="13_area" id="13_area" style="width: 90%; font-size: 11px;" class="gridInput" />
					
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="print13();" class="buttonding" style="font-size: 11px;"><img src="images/icons/dtr.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Print Thirteenth Month Payslip</button>
			</td>
		</tr>
	</table>
</div>

<div id="e_list" style="display: none;"></div>
<div id="e_details" style="display: none;"></div>
<div id="payperiods" style="display: none;"></div>
<div id="holidays" style="display: none;"></div>
<div id="leaves" style="display: none;"></div>
<div id="deductions" style="display: none;"></div>
<div id="loans" style="display: none;"></div>
<div id="adjustments" style="display: none;"></div>
<div id="importdtr" style="display: none;"></div>
<div id="empdtr" style="display: none;"></div>