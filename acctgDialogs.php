<?php 
	$iq = dbquery("select branch_code, branch_name from options_branches;");
	$bOPT = "<option value=''>- Consolidated -</option>";
	while(list($optbid,$optbname) = mysql_fetch_array($iq)) {
		$bOPT .= "<option value='$optbid'>$optbname</option>";
	}
?>

<div id="jsched" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Book :</span></td>
			<td>
				<select name="js_type" id="js_type" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value="SI">Sales Journal (SI)</option>
					<option value="POS">Sales Journal (POS)</option>
					<option value="CR">Cash Receipts Journal</option>
					<option value="CV">Disbursement Journal</option>
					<option value="AP">Vouchers Payable Journal</option>
					<option value="DA">Debit/Credit Advise Journal</option>
					<option value="JV">General Journal</option>
					<option value="APB">AP - Beginning</option>
					<option value="ARB">AR - Beginning</option>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Date Range :</span></td>
			<td>
				<input type="text" id="js_dtf" class="gridInput" style="width: 80%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="js_dt2" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Report Type :</span></td>
			<td>
				<select name="js_rtype" id="js_rtype" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value="1">Detailed</option>
					<option value="2">Summary</option>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">Account Code :</span></td>
			<td>
				<input type="text" id="js_acct" class="gridInput" style="width: 80%;" onkeyup="javascript: acctLookupReport(this.value,this.id);" /><br/>
				<input type="checkbox" id="js_conso">&nbsp;<span class="spandix-l"><i>Consolidated Schedule</i></span>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateSchedule();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report in PDF</button>
				<button onClick="generateScheduleXLS();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate in Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="tbalance" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=30%><span class="spandix-l">Branch :</span></td>
			<td>
				<select id="tb_branch" style="width: 80%; font-size: 11px;" class="gridInput">
					<?php
						echo $bOPT;
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Date Range :</span></td>
			<td>
				<input type="text" id="tb_dtf" class="gridInput" style="width: 80%;" value="<?php echo date('m/01/2018'); ?>" readonly />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="tb_dt2" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" /><br/>
				<!--input type="checkbox" id="tb_conso">&nbsp;<span class="spandix-l"><i>Consolidated Trial Balance</i></span-->
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateTB();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report in PDF</button>
				<button onClick="generateTBXLS();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate in Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="glsched" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=30%><span class="spandix-l">Branch :</span></td>
			<td>
				<select id="gls_branch" style="width: 80%; font-size: 11px;" class="gridInput">
					<?php
						echo $bOPT;
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Covered Period :</span></td>
			<td>
				<input type="text" id="gls_dtf" class="gridInput" style="width: 80%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="gls_dt2" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">Account Code :</span></td>
			<td>
				<input type="text" id="gls_acct" class="gridInput" style="width: 80%;" onkeyup="javascript: acctLookupReport(this.value,this.id);" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateGL(1);" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate in PDF</button>
				<button onClick="generateGL(2);" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate in Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="cashflow" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Account :</span></td>
			<td>
				<select id="cf_source" style="width: 80%; font-size: 11px;" class="gridInput" <?php echo $isDisabled; ?> />
					<?php
						$_j = dbquery("SELECT acct_code, description FROM acctg_accounts WHERE acct_grp = '1000' AND acct_code != '1001' AND company = '$_SESSION[company]';");
						while(list($a,$b) = mysql_fetch_array($_j)) {
							echo "<option value='$a'>($a) $b</option>";
							}
						?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Date Range :</span></td>
			<td>
				<input type="text" id="cf_dtf" class="gridInput" style="width: 80%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="cf_dt2" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateCashFlow();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="checks" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Account :</span></td>
			<td>
				<select id="ic_source" style="width: 80%; font-size: 11px;" class="gridInput" <?php echo $isDisabled; ?> />
					<?php
						$_j = dbquery("SELECT acct_code, description FROM acctg_accounts WHERE acct_grp = '1000' AND acct_code not in ('1001','1002') AND company = '$_SESSION[company]';");
						while(list($a,$b) = mysql_fetch_array($_j)) {
							echo "<option value='$a'>($a) $b</option>";
							}
						?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Date Range :</span></td>
			<td>
				<input type="text" id="ic_dtf" class="gridInput" style="width: 80%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="ic_dt2" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateChecks();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="ExpSched" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35% valign=top><span class="spandix-l">Branch :</span></td>
			<td>
				<select id="expsched_branch" style="width: 80%; font-size: 11px;" class="gridInput">
					<option value = "">- Consolidated -</option>
					<?php
						echo $bOPT;
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Expense Account :</span></td>
			<td>
				<select id="expsched_acct" style="width: 80%; font-size: 11px;" class="gridInput" <?php echo $isDisabled; ?> />
					<?php
						$_jdf = dbquery("SELECT acct_code, description FROM acctg_accounts WHERE acct_grp in ('7000','1650') AND company = '$_SESSION[company]' order by acct_code asc;");
						while(list($adf,$bdf) = mysql_fetch_array($_jdf)) {
							echo "<option value='$adf'>($adf) $bdf</option>";
							}
						?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Calendar Year :</span></td>
			<td>
				<input type="text" id="expsched_year" class="gridInput" style="width: 80%;" value="<?php echo date('Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateExpSched();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="purchases" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Report Type :</span></td>
			<td>
				<select id="po_type" style="width: 80%; font-size: 11px;" class="gridInput" />
					<option value="">- All Purchases -</option>
					<option value="1">- Unserved Purchase Orders -</option>
					<option value="2">- Partially Served Purchase Orders -</option>
					<option value="3">- Fully Served Purchase Orders -</option>
					<option value="4">- Partial & Fully Served P.Os -</option>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Supplier :</span></td>
			<td>
				<input type="hidden" id="po_sid"><input type="text" id="po_sname" class="gridInput" style="width: 80%;" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Date Range :</span></td>
			<td>
				<input type="text" id="po_dtf" class="gridInput" style="width: 80%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="po_dt2" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generatePurchases();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
				<button onClick="generatePurchasesX();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export to Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="accountbalance" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Customer :</span></td>
			<td>
				<input type="hidden" id="cab_sid"><input type="text" id="cab_sname" class="gridInput" style="width: 80%;" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">As Of :</span></td>
			<td>
				<input type="text" id="cab_asof" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" /><br/>
				<input type="checkbox" id = "overdue_only" >&nbsp;<span class="spandix-l" valign=top><i>Show overdue accounts only</i></span><br/>
				<input type="checkbox" id = "with_soa_num" >&nbsp;<span class="spandix-l" valign=top><i>Assign SOA No. and save result for future reference</i></span>
				
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateSOA();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="outstandingInvoices" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Customer :</span></td>
			<td>
				<select name="coi_cust" id="coi_cust" class="gridInput" style="width: 80%; font-size: 11px;" /></select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">As Of :</span></td>
			<td>
				<input type="text" id="coi_asof" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" /><br/>
				<input type="checkbox" id="coi_isoverdue">&nbsp;<span class="spandix-l"><i>Check to display overdue accounts only</i></span>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateOutstanding();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="subledger" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=40%><span class="spandix-l">Customer/Supplier :</span></td>
			<td>
				<input type="hidden" id="subledger_sid"><input type="text" id="subledger_sname" class="gridInput" style="width: 80%;" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=40% valign=top><span class="spandix-l">Account Code :</span></td>
			<td>
				<input type="text" id="subledger_acct" class="gridInput" style="width: 80%;" /><br/>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=40% valign=top><span class="spandix-l">Covered Period :</span></td>
			<td>
				<input type="text" id="subledger_dtf" class="gridInput" style="width: 80%;" value="<?php echo date('m/01/Y'); ?>" /><br/>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="subledger_dt2" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" /><br/>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateSubLedger();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="aras" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35% valign=top><span class="spandix-l">As Of :</span></td>
			<td>
				<input type="text" id="aras_asof" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateARAS();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="apas" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35% valign=top><span class="spandix-l">As Of :</span></td>
			<td>
				<input type="text" id="apas_asof" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" /><br/>
				<input type="checkbox" id="apas_conso">&nbsp;<span class="spandix-l"><i>Consolidated Schedule</i></span>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateAPAS();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="incomestatement" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35% valign=top><span class="spandix-l">Branch :</span></td>
			<td>
				<select id="is_branch" style="width: 80%; font-size: 11px;" class="gridInput">
					<?php
						echo $bOPT;
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">Month of :</span></td>
			<td>
				<select id="is_month" style="width: 80%; font-size: 11px;" class="gridInput">
					<option value="01">January</option>
					<option value="02">February</option>
					<option value="03">March</option>
					<option value="04">April</option>
					<option value="05">May</option>
					<option value="06">June</option>
					<option value="07">July</option>
					<option value="08">August</option>
					<option value="09">September</option>
					<option value="10">October</option>
					<option value="11">November</option>
					<option value="12">December</option>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">Year :</span></td>
			<td>
				<select id="is_year" class="gridInput" style="width: 50%; font-size: 11px;">
					<?php
						$cy = date('Y');
						for($x=$cy;$x>=2015;$x--){
							echo "<option value='$x'>$x</option>";
						}
						
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateIS();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
				<button onClick="generateISEX();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export to Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="transLock" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35% valign=top><span class="spandix-l">Month :</span></td>
			<td>
				<select id="lock_month" style="width: 80%; font-size: 11px;" class="gridInput">
					<option value="01">January</option>
					<option value="02">February</option>
					<option value="03">March</option>
					<option value="04">April</option>
					<option value="05">May</option>
					<option value="06">June</option>
					<option value="07">July</option>
					<option value="08">August</option>
					<option value="09">September</option>
					<option value="10">October</option>
					<option value="11">November</option>
					<option value="12">December</option>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">Year :</span></td>
			<td>
				<select id="lock_year" class="gridInput" style="width: 50%; font-size: 11px;">
					<?php
						$cy = date('Y');
						for($x=$cy;$x>=2015;$x--){
							echo "<option value='$x'>$x</option>";
						}
						
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">Memo :</span></td>
			<td>
				<textarea id="lock_memo" class="gridInput" style="width: 80%; font-size: 11px;" rows=2 /></textarea>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="lockTransactions();" class="buttonding" style="font-size: 11px;"><img src="images/icons/lock.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Finalize & Lock Transactions</button>
			</td>
		</tr>
	</table>
</div>

<div id="uLock" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35% valign=top><span class="spandix-l">Month :</span></td>
			<td>
				<select id="ulock_month" style="width: 80%; font-size: 11px;" class="gridInput">
					<option value="01">January</option>
					<option value="02">February</option>
					<option value="03">March</option>
					<option value="04">April</option>
					<option value="05">May</option>
					<option value="06">June</option>
					<option value="07">July</option>
					<option value="08">August</option>
					<option value="09">September</option>
					<option value="10">October</option>
					<option value="11">November</option>
					<option value="12">December</option>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">Year :</span></td>
			<td>
				<select id="ulock_year" class="gridInput" style="width: 50%; font-size: 11px;">
					<?php
						$cy = date('Y');
						for($x=$cy;$x>=2015;$x--){
							echo "<option value='$x'>$x</option>";
						}
						
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="unlockTransactions();" class="buttonding" style="font-size: 11px;"><img src="images/icons/key.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Unlock Transactions</button>
			</td>
		</tr>
	</table>
</div>
<div id="balanceSheet" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=30%><span class="spandix-l">Period Ending :</span></td>
			<td>
				<select id="bs_month" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value="01"> January </option>
					<option value="02"> February </option>
					<option value="03"> March </option>
					<option value="04"> April </option>
					<option value="05"> May </option>
					<option value="06"> June </option>
					<option value="07"> July </option>
					<option value="08"> August </option>
					<option value="09"> September </option>
					<option value="10"> October </option>
					<option value="11"> November </option>
					<option value="12"> December </option>
				</select> 
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=30%></td>
			<td>
				<select id="bs_year" class="gridInput" style="width: 50%; font-size: 11px;">
					<?php
						$cy = date('Y');
						for($x=$cy;$x>=2015;$x--){
							echo "<option value='$x'>$x</option>";
						}
						
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateBalanceSheet();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
		
	</table>
</div>
<div id="ColPerf" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35% valign=top><span class="spandix-l">Branch :</span></td>
			<td>
				<select id="colperf_branch" style="width: 80%; font-size: 11px;" class="gridInput">
					<?php
						echo $bOPT;
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=30%><span class="spandix-l">Month :</span></td>
			<td>
				<select id="colperf_month" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value="01"> January </option>
					<option value="02"> February </option>
					<option value="03"> March </option>
					<option value="04"> April </option>
					<option value="05"> May </option>
					<option value="06"> June </option>
					<option value="07"> July </option>
					<option value="08"> August </option>
					<option value="09"> September </option>
					<option value="10"> October </option>
					<option value="11"> November </option>
					<option value="12"> December </option>
				</select> 
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Year :</span></td>
			<td>
				<select id="colperf_year" class="gridInput" style="width: 50%; font-size: 11px;">
					<?php
						$cy = date('Y');
						for($x=$cy;$x>=2015;$x--){
							echo "<option value='$x'>$x</option>";
						}
						
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateColPerf();" class="buttonding" style="font-size: 11px;"><img src="images/icons/chart.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="dsper" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Branch :</span></td>
			<td>
				<select id="dsper_branch" style="width: 90%; font-size: 11px;" class="gridInput">
					<?php
						echo $bOPT;
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Date :</span></td>
			<td>
				<input type="text" id="dsper_dtf" class="gridInput" style="width: 90%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="showDSPER();" class="buttonding" style="font-size: 11px;"><img src="images/icons/processraw.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Daily Sales Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="listofdoc" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Document Type :</span></td>
			<td>
				<select id="listofdoc_type" style="width: 90%; font-size: 11px;" class="gridInput">
				
				<?php
					$doclist_op = mysql_query("SELECT line_id,doc_type FROM doctypes WHERE rp= 'Y';");
					while(list($dlid,$dltype) = mysql_fetch_array($doclist_op)) {
						echo "<option value='$dlid'>$dltype</option>";
					}
				?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Date From :</span></td>
			<td>
				<input type="text" id="listofdoc_dtf" class="gridInput" style="width: 90%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Date To :</span></td>
			<td>
				<input type="text" id="listofdoc_dt2" class="gridInput" style="width: 90%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Doc.Status :</span></td>
			<td>
				<select id="listofdoc_status" style="width: 90%; font-size: 11px;" class="gridInput">
					<option value="1" > Cancelled </option>
					<option value="2" > Active </option>
					<option value="3" > Finalized </option>
				</select>
			</td>
		</tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="genDocList();" class="buttonding" style="font-size: 11px;"><img src="images/icons/processraw.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<!-- Bank Recon -->
<div id="bankrecondiv" style="display: none; background-color: #f5f5f5;">
	<table width=100% align=center style="padding:10px;">
		<tr>
			<td style="padding-right: 20px; font-weight: bold;font-size:11px;" align=right width=100>Bank Account :</td>
			<td align=left>
				<select class="gridInput" name="acct_code" id="acct_code" style="width: 300px; font-size: 11px;" onchange="javascript: bank_getData();">
					<option value="">- Select Bank Account -</option>
					<?php
						$q = mysql_query("select acct_code,concat('[',acct_code,']', description) as bank from acctg_accounts where acct_grp = '1000' and acct_code not in ('1001','1002') order by acct_code;");
						while(list($acct,$bank) = mysql_fetch_array($q)) {
							echo "<option value=\"$acct\">$bank</option>";
						}
					?>
				</select>
			</td>
			<td style="padding-right: 20px; font-weight: bold;font-size:11px;" align=right width=80>Date As Of :</td>
			<td align=left>
				<input type="text" name="tmp_date" id="tmp_date" style="width: 170px;" onchange="javascript: bank_getData();" value="<?php echo date("m/d/Y"); ?>"></td>
			</td>
		</tr>
		<tr>
			<td style="padding-right: 20px; font-weight: bold;font-size:11px;" align=right width=100>Opening Balance :</td>
			<td align=left>
				<input type="text" id="balanceopen" style="text-align: right; font-weight: bold;" readonly>
			</td>
			<td style="padding-right: 20px; font-weight: bold;font-size:11px;" align=right width=80>Balance End :</td>
			<td align=left>
				<input type="text" name="balend" id="balend" style="width: 170px; text-align: right; font-weight: bold;" onchange="javascript: passbalend(this.value);"></td>
			</td>
		</tr>
		<tr><td colspan=4><hr style="width:95%;" align=center /></td></tr>
		<tr><td height=10></td></tr>
		<tr>
			<td colspan=4 width=100%>
				<table>
					<tr>
						<td width=85% >
							<table width=100%>
								<tr>
									<td colspan=4 style="padding-left: 20px; font-weight: bold;">
										Deposits & Other Credits
									</td>
								</tr>
								<tr>
									<td colspan=4>
										<div style="padding-left: 20px;">
											<table width=100% cellspacing=0 cellpadding=0 style="margin-left: 20x;">
												<tr>
													<td class="dgridhead" width=10%>DOC #</td>
													<td class="dgridhead" width=10%>DATE</td>
													<td class="dgridhead" width=30%>PAYEE</td>
													<td class="dgridhead" width=30%>MEMO</td>
													<td class="dgridhead" width=10% align=right style="padding-right: 20px;">AMOUNT</td>
													<td class="dgridhead">&nbsp;</td>
													<td class="dgridhead" width=20>&nbsp;</td>
												</tr>
											</table>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan=4 style="padding-left: 20px;">
										<div id="db_transaction" style="border: 1px solid #cdcdcd; height: 120px; width: 100%; overflow-y: scroll;">
										
										</div>
									</td>
								</tr>
								<tr><td height=4></td></tr>
								<tr>
									<td colspan=4 style="padding-left: 20px; font-weight: bold;">
										Checks & Payments
									</td>
								</tr>
								<tr>
									<td colspan=4>
										<div style="padding-left: 20px;">
											<table width=100% cellspacing=0 cellpadding=0 style="margin-left: 20x;">
												<tr>
													<td class="dgridhead" width=10%>DOC #</td>
													<td class="dgridhead" width=10%>DATE</td>
													<td class="dgridhead" width=15%>CHECK #</td>
													<td class="dgridhead" width=15%>PAYEE</td>
													<td class="dgridhead" width=30%>MEMO</td>
													<td class="dgridhead" width=10% align=right style="padding-right: 20px;">AMOUNT</td>
													<td class="dgridhead">&nbsp;</td>
													<td class="dgridhead" width=20>&nbsp;</td>
												</tr>
											</table>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan=4 style="padding-left: 20px;">
										<div id="cr_transaction" style="border: 1px solid #cdcdcd; height: 120px; width: 100%; overflow-y: scroll;">
										
										</div>
									</td>
								</tr>
								<tr><td height=4></td></tr>
								<tr>
									<td style="padding-left: 20px;">
										<fieldset>
											<table cellspacing=0 cellpadding=0 width=100% style="font-weight: bold;">
												<tr>
													<td colspan=2 width=50% style="font-size: 14px;">
														Items you have marked as cleared
													</td>
													<td style="font-size: 14px;"></td>
													<td align=right><!--input type="text" id="balanceend" name="balanceend" style="border: none; background-color: #f5f5f5; text-align: right; font-weight: bold; font-size: 12px;" value="0.00" readonly--></td>
												</tr>
												<tr>
													<td width=10%><input type="text" id="debits" name="debits" style="width: 40px; border: none; background-color: #f5f5f5; text-align: right; font-weight: bold; font-size:12px;" value="0" readonly></td>
													<td style="font-size: 12px;">Deposists and other Credits</td>
													<td style="font-size: 12px;">Cleared Balance :</td>
													<td align=right><input type="text" id="clearedbalance" name="clearedbalance" style="border: none; background-color: #f5f5f5; text-align: right; font-weight: bold; ont-size:12px;" value="0.00" readonly></td>
												</tr>
												<tr>
													<td width=10%><input type="text" id="credits"  name="credits" style="width: 40px; border: none; background-color: #f5f5f5; text-align: right; font-weight: bold; font-size:12px;" value="0" readonly></td>
													<td style="font-size: 12px;">Checks & Payments</td>
													<td style="font-size: 12px;">Difference :</td>
													<td align=right><input type="text" id="diffbalance" name="diffbalance" style="border: none; background-color: #f5f5f5; text-align: right; font-weight: bold; ont-size:12px;" value="0.00" readonly></td>
												</tr>
											</table>
										</fieldset>
									</td>
								</tr>
								<tr><td align=center><hr style="width:99%;" align=center /></td></tr>
							</table>
						</td>
						<td valign=top style="padding-top: 40px;">
							<button style="height: 40px;width:160px;" onclick="clear_selected();"><img src="images/icons/ok.png" border=0 align=absmiddle width=18 height=18 />&nbsp;&nbsp;Done</button><br/>
							<button style="height: 40px;width:160px;" onclick="close_recon();"><img src="images/icons/checkout.png" border=0 align=absmiddle width=18 height=18 />&nbsp;&nbsp;Leave</button><br/><br/><br/><br/><br/><br/>
							<button style="height: 40px;width:160px;" onclick="check_all();"><img src="images/icons/crr.png" border=0 align=absmiddle width=18 height=18 />&nbsp;&nbsp;Check All</button><br/>
							<button style="height: 40px;width:160px;" onclick="uncheck_all();"><img src="images/delete2.png" border=0 align=absmiddle width=16 height=16 />&nbsp;&nbsp;Uncheck All</button><br/>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<div id="collection" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=40%><span class="spandix-l">Customer/Supplier :</span></td>
			<td>
				<input type="hidden" id="colrep_sid"><input type="text" id="colrep_sname" class="gridInput" style="width: 80%;" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=40% valign=top><span class="spandix-l">Report Type :</span></td>
			<td>
				<select id="colrep_type" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value="1">- Detailed -</option>
					<option value="2">- Summary -</option>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=40% valign=top><span class="spandix-l">Covered Period :</span></td>
			<td>
				<input type="text" id="colrep_dtf" class="gridInput" style="width: 80%;" value="<?php echo date('m/01/Y'); ?>" /><br/>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="colrep_dt2" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" /><br/>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateCollectionReport();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="vatRelief" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Month :</span></td>
			<td>
				<select id="vat_month" class="gridInput" style="width: 60%; font-size: 11px;">
					<option value="01"> January </option>
					<option value="02"> February </option>
					<option value="03"> March </option>
					<option value="04"> April </option>
					<option value="05"> May </option>
					<option value="06"> June </option>
					<option value="07"> July </option>
					<option value="08"> August </option>
					<option value="09"> September </option>
					<option value="10"> October </option>
					<option value="11"> November </option>
					<option value="12"> December </option>
				</select> 
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Year :</span></td>
			<td>
				<select id="vat_year" class="gridInput" style="width: 60%; font-size: 11px;">
					<?php
						$cy = date('Y');
						for($x=2017;$x<=$cy;$x++){
							echo "<option value='$x'>$x</option>";
						}
						
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l" >Type :</span></td>
			<td>
				<select id="vat_type" class="gridInput" style="width: 60%; font-size: 11px;">
					<option value="input">Input Tax</option>
					<option value="output">Output Tax</option>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateVAT();" class="buttonding" style="font-size: 11px;"><img src="images/icons/processraw.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate DAT File</button>
			</td>
		</tr>
	</table>
</div>
<div id="moTBalance" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=30%><span class="spandix-l">Branch :</span></td>
			<td>
				<select id="motb_branch" style="width: 80%; font-size: 11px;" class="gridInput">
					<?php
						echo $bOPT;
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=30%><span class="spandix-l">Month :</span></td>
			<td>
				<select id="motb_month" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value="01"> January </option>
					<option value="02"> February </option>
					<option value="03"> March </option>
					<option value="04"> April </option>
					<option value="05"> May </option>
					<option value="06"> June </option>
					<option value="07"> July </option>
					<option value="08"> August </option>
					<option value="09"> September </option>
					<option value="10"> October </option>
					<option value="11"> November </option>
					<option value="12"> December </option>
				</select> 
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=30%><span class="spandix-l">Year :</span></td>
			<td>
				<select id="motb_year" class="gridInput" style="width: 50%; font-size: 11px;">
					<?php
						$cy = date('Y');
						for($x=$cy;$x>=2015;$x--){
							echo "<option value='$x'>$x</option>";
						}
						
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateMOTB();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="preboard" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Reporting Period :</span></td>
			<td>
				<select id="dboard_month" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value="01"> January </option>
					<option value="02"> February </option>
					<option value="03"> March </option>
					<option value="04"> April </option>
					<option value="05"> May </option>
					<option value="06"> June </option>
					<option value="07"> July </option>
					<option value="08"> August </option>
					<option value="09"> September </option>
					<option value="10"> October </option>
					<option value="11"> November </option>
					<option value="12"> December </option>
				</select> 
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%></td>
			<td>
				<select id="dboard_year" class="gridInput" style="width: 50%; font-size: 11px;">
					<?php
						$cy = date('Y');
						for($x=$cy;$x>=2015;$x--){
							echo "<option value='$x'>$x</option>";
						}
						
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="fetchDashboard();" class="buttonding" style="font-size: 11px;"><img src="images/icons/chart.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Open Dashboard</button>
			</td>
		</tr>
		
	</table>
</div>
<div id="audtrail" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<td width=35%><span class="spandix-l">Module :</span></td>
			<td>
				<select id="audType" class="gridInput" style="width: 90%; font-size: 11px;">
				<option value=""> - All Activities - </option>
					<option value="SO"> Sales Order </option>
					<option value="PO"> Purchase Order </option>
					<option value="SI"> Sales Invoice </option>
					<option value="RR"> Receiving Report </option>
					<option value="STR"> Stocks Transfer </option>
					<option value="SRR"> Stocks Receiving </option>
					<option value="SW"> Stocks Withdrawal </option>
					<option value="AP"> Accounts Payable </option>
					<option value="CV"> Check Disbursement </option>
					<option value="JV"> Journal Voucher </option>
					<option value="CR"> Collection Receipt </option>
				</select> 
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<td width=35%><span class="spandix-l">Module :</span></td>
			<td>
				<select id="audUser" class="gridInput" style="width: 90%; font-size: 11px;">
					<option value=""> - All Users - </option>
					<?php
						$uter = dbquery("SELECT emp_id, fullname FROM user_info ORDER BY fullname ASC;");
						while($uterow = mysql_fetch_array($uter)) {
							echo "<option value = '$uterow[0]'>$uterow[1]</option>";
						}
					?>
				</select> 
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Date From :</span></td>
			<td>
				<input type="text" id="audDTF" class="gridInput" style="width: 90%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Date To :</span></td>
			<td>
				<input type="text" id="audDT2" class="gridInput" style="width: 90%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="viewAuditTrail();" class="buttonding" style="font-size: 11px;"><img src="images/icons/audittrail.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;View Audit Trail</button>
			</td>
		</tr>
	</table>
</div>
<div id="inVatSummary" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Supplier/Payee :</span></td>
			<td>
				<select name="ivat_payee" id="ivat_payee" class="gridInput" style="width: 80%; font-size: 11px;" /></select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Date Range :</span></td>
			<td>
				<input type="text" id="ivat_dtf" class="gridInput" style="width: 80%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="ivat_dt2" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateInVatSummaryPDF();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button><button onClick="generateInVatSummaryXLS();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export to Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="overdueAP" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Supplier/Payee :</span></td>
			<td>
				<select name="oap_payee" id="oap_payee" class="gridInput" style="width: 80%; font-size: 11px;" /></select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l"></span></td>
			<td>
				<input type="text" name="oap_asof" id="oap_asof" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" /><br/>
				<input type="checkbox" id="oap_isoverdue">&nbsp;<span class="spandix-l"><i>Check to display overdue payables only</i></span>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateOAP();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="cpdc" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35% valign=top><span class="spandix-l">Date Due:</span></td>
			<td>
				<input type="text" id="cpdc_date" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateCPDC();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>