
<div id="printDTR" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Department :</span></td>
			<td>
				<select id="dtr_dept" style="width: 80%; font-size: 11px;" class="gridInput" />
					<option value="">- All Departments -</option>
					<?php
						$_b = dbquery("SELECT dept_code, dept_name FROM options_dept;");
						while(list($p,$pp) = mysql_fetch_array($_b)) {
							echo "<option value='$p'>($p) $pp</option>";
							}
						?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<input class="gridInput" type="text" id="dtr_dtf" name="dtr_dtf" style="width: 80%; font-size: 11px;"  value="" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l"></span></td>
			<td>
				<input class="gridInput" type="text" id="dtr_dt2" name="dtr_dt2" style="width: 80%; font-size: 11px;" value="" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr style="width: 95%;"></hr></td></tr>
		<tr>
		    <td></td>
			<td align=left>
				<button class="buttonding" style="font-size: 11px;" onclick="printDTR();"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Print Daily Time Record</b></button>
			</td>
		</tr>
	</table>
</div>
<div id="edtr" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Employee :</span></td>
			<td>
				<select id="e_eid" name="e_eid" style="width: 80%; font-size: 11px;" class="gridInput" /></select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<select name="dtr_period" id="dtr_period" class="gridInput" style="width:80%; font-size: 8pt;font-family:arial;">
					<option value=''> SELECT </option>
						<?php
							$payperiod = dbquery("SELECT period_id,CONCAT(DATE_FORMAT(period_start,'%m/%d/%Y'),' - ',DATE_FORMAT(period_end,'%m/%d/%Y')) AS label FROM redglobalhris.pay_periods a ORDER BY period_start DESC;");
							while($iperiod = mysql_fetch_array($payperiod)){
								$opt_period .= "<option value='".$iperiod['period_id']."'> ".$iperiod['label']." </option>";
							}
							echo $opt_period;
						?>
					</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Department :</span></td>
			<td>
				<select name="dept_id" id="dept_id" class="gridInput" style="width:80%; font-size: 8pt;font-family:arial;">
					<option value=''> SELECT </option>
						<?php
							$dept_opt = dbquery("SELECT dept_code,dept_name FROM options_dept;");
							while($iperiod = mysql_fetch_array($dept_opt)){
								$dept .= "<option value='".$iperiod['dept_code']."'> ".$iperiod['dept_name']." </option>";
							}
							echo $dept;
						?>
					</select>
			</td>
		</tr>
		<tr><td colspan=2><hr style="width: 95%;"></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button class="buttonding" style="font-size: 11px;" onclick="manageEDTR();"><img src="images/icons/dtr.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;View & Manage Employee DTR</b></button>
			</td>
		</tr>
	</table>
</div>
<div id="paySlip" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<select name="payslip_period" id="payslip_period" class="gridInput" style="width:80%; font-size: 8pt;font-family:arial;">
					<option value=''> SELECT </option>
						<?php
							$payperiod = dbquery("SELECT period_id,CONCAT(DATE_FORMAT(period_start,'%m/%d/%Y'),' - ',DATE_FORMAT(period_end,'%m/%d/%Y')) AS label FROM hris.pay_periods a ORDER BY period_start DESC;");
							while($iperiod = mysql_fetch_array($payperiod)){
								$opt_period2 .= "<option value='".$iperiod['period_id']."'> ".$iperiod['label']." </option>";
							}
							echo $opt_period2;
						?>
					</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Department :</span></td>
			<td>
				<select name="payslip_dept" id="payslip_dept" class="gridInput" style="width:80%; font-size: 8pt;font-family:arial;">
					<option value=''> SELECT </option>
						<?php
							$dept_opt = dbquery("SELECT dept_code,dept_name FROM options_dept;");
							while($iperiod = mysql_fetch_array($dept_opt)){
								$dept .= "<option value='".$iperiod['dept_code']."'> ".$iperiod['dept_name']." </option>";
							}
							echo $dept;
						?>
					</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr style="width: 95%;"></hr></td></tr>
		<tr>
			<td align=center>
				<button class="buttonding" style="font-size: 11px;" onclick="generatePaySlip();"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Payslip</b></button>
			</td>
			<td align=center>
				<button class="buttonding" style="font-size: 11px;" onclick="generatePaySlipEX();"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Summary</b></button>
			</td>
		</tr>
	</table>
</div>
<div id="paySum" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Payroll Period :</span></td>
			<td>
				<select name="paysum_period" id="paysum_period" class="gridInput" style="width:80%; font-size: 8pt;font-family:arial;">
					<option value=''> SELECT </option>
						<?php
							$payperiod = dbquery("SELECT period_id,CONCAT(DATE_FORMAT(period_start,'%m/%d/%Y'),' - ',DATE_FORMAT(period_end,'%m/%d/%Y')) AS label FROM hris.pay_periods a ORDER BY period_start DESC;");
							while($iperiod = mysql_fetch_array($payperiod)){
								$opt_period3 .= "<option value='".$iperiod['period_id']."'> ".$iperiod['label']." </option>";
							}
							echo $opt_period3;
						?>
					</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Department :</span></td>
			<td>
				<select name="pay_sumdept" id="pay_sumdept" class="gridInput" style="width:80%; font-size: 8pt;font-family:arial;">
					<option value=''> SELECT </option>
						<?php
							$dept_opt = dbquery("SELECT dept_code,dept_name FROM options_dept;");
							while($iperiod = mysql_fetch_array($dept_opt)){
								$dept .= "<option value='".$iperiod['dept_code']."'> ".$iperiod['dept_name']." </option>";
							}
							echo $dept;
						?>
					</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr style="width: 95%;"></hr></td></tr>
		<tr>
			<td align=center colspan=2>
			<input type=checkbox id="finalizePayroll"> <span style="font-size: 11px;"> Finalize Payroll </span> 
		</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td align=center colspan=2>
				<button class="buttonding" style="font-size: 11px;" onclick="generatePaySlipEX();"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Summary</b></button>
			</td>
		</tr>
	</table>
</div>