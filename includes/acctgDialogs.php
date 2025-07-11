<div id="jsched" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Book :</span></td>
			<td>
				<select name="js_type" id="js_type" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value="SI">Sales Journal</option>
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
				<input type="text" id="js_acct" class="gridInput" style="width: 80%;" onkeyup="javascript: acctLookupReport(this.value,this.id);" />
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
			<td width=35%><span class="spandix-l">Date As Of :</span></td>
			<td>
				<input type="text" id="tb_asof" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" readonly />
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
		<?php
			//$o->_structAccount('Account Code','35%','gls_acct','gridInput','width: 80%; font-size: 11px;','Y','',$exclude='');
			$o->_structInput('Acct. Code','35%','gls_acct','gridInput','width: 80%;','');
			$o->_structInput('Client Code','35%','gls_client','gridInput','width: 80%;','');
			$o->_structInput('From','35%','gls_dtf','gridInput','width: 80%;',date('m/01/Y'));
			$o->_structInput('To','35%','gls_dt2','gridInput','width: 80%;',date('m/d/Y'));
			
		?>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateGL(1);" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
				<button onClick="generateGL(2);" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate in Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="cashflow" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<?php 
			$o->_structAccount('Account','35%','cf_source','gridInput','width: 80%; font-size: 11px;','N','1',$exclude=''); 
			$o->_structInput('From','35%','cf_dtf','gridInput','width: 80%;',date('m/01/Y'));
			$o->_structInput('To','35%','cf_dt2','gridInput','width: 80%;',date('m/d/Y'));
		?>
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
		<?php 
				$o->_structAccount('Account','35%','ic_source','gridInput','width: 80%; font-size: 11px;','N','1',$exclude = "'10100'");
				$o->_structInput('From','35%','ic_dtf','gridInput','width: 80%;',date('m/01/Y'));
				$o->_structInput('To','35%','ic_dt2','gridInput','width: 80%;',date('m/d/Y')); 
		?>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateChecks();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
				<button onClick="generateChecksXLS();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export to Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="ExpSched" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<?php
			$o->_structMonths('expsched_month','width: 80%; font-size: 11px;','gridInput');
			$o->_structYear("Calendar Year","35%","expsched_year","gridInput","style='width:80%; font-size: 11px;'");
		?>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateExpSched(1);" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
				<button onClick="generateExpSched(2);" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export to Excel</button>
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
		<?php
			$o->_structInput('Supplier','35%','po_sid','gridInput','width: 80%;','');
			$o->_structInput('From','35%','po_dtf','gridInput','width: 80%;',date('m/01/Y')); 			
			$o->_structInput('To','35%','po_dt2','gridInput','width: 80%;',date('m/d/Y')); 
		?>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generatePurchases();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
				<button onClick="generatePurchasesX();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export to Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="expsum" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<?php
			$o->_structInput('Covered Period','35%','exs_dtf','gridInput','width: 80%;',date('m/01/Y')); 
			$o->_structInput('','35%','exs_dt2','gridInput','width: 80%;',date('m/d/Y')); 
		?>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateSummaryExpenses();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export Report to Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="subledger" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<?php 
			$o->_structInput('Client Code','35%','subledger_sid','inputSearch2','width: 80%; padding-left: 22px;','');
			$o->_structInput('Subledger Acct.','35%','subledger_acct','inputSearch2','width: 80%; padding-left: 22px;','');
			$o->_structInput('Date as Of','35%','subledger_asof','gridInput','width: 80%;',date('m/d/Y')); 
		?>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateSubLedger();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="apas" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<?php $o->_structInput('Date as Of','35%','apas_asof','gridInput','width: 80%;',date('m/d/Y')); ?>
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
			<td width=35%><span class="spandix-l">Cost Center :</span></td>
			<td>
				<select id="is_cc" style="width: 80%; font-size: 11px;" class="gridInput" />
					<?php
						$uLoop = $o->dbquery("SELECT proj_id,proj_code from options_project;");
						echo "<option value=''>- Consolidated -</option>";
						while(list($pid,$pname) = $uLoop->fetch_array(MYSQLI_BOTH)) {
							$option = $option ."<option value='$pid'>$pname</option>";
						}
						echo $option;
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<?php
			$o->_structMonths('is_month','width: 80%; font-size: 11px;','gridInput');
			$o->_structYear("Calendar Year","35%","is_year","gridInput","style='width:80%; font-size: 11px;'");
		?>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateIS();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
				<button onClick="generateISEX();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export to Excel</button>
			</td>
		</tr>
	</table>
</div>

<div id="balanceSheet" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<?php
			$o->_structMonths('bs_month','width: 80%; font-size: 11px;','gridInput');
			$o->_structYear("Calendar Year","35%","bs_year","gridInput","style='width:80%; font-size: 11px;'");
		?>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateBalanceSheet(1);" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
				<button onClick="generateBalanceSheet(2);" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export to Excel</button>
			</td>
		</tr>
		
	</table>
</div>

<div id="glposting" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		
		<?php
			$o->_structInput('Covered Period','35%','glp_dtf','gridInput','width: 80%;',date('m/01/Y')); 
			$o->_structInput('','35%','glp_dt2','gridInput','width: 80%;',date('m/d/Y')); 
		?>

		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="postRevenuetoGL();" class="buttonding" style="font-size: 11px;"><img src="images/icons/processraw.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Post Data to General Ledger</button>
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
				
				
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<?php
			$o->_structInput('From','35%','listofdoc_dtf','gridInput','width: 80%;',date('m/01/Y')); 
			$o->_structInput('To','35%','listofdoc_dt2','gridInput','width: 80%;',date('m/d/Y')); 
		?>
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
						$q = $o->dbquery("select acct_code,concat('[',acct_code,']', description) as bank from acctg_accounts where acct_grp = '1' and acct_code not in ('10100') order by acct_code;");
						while(list($acct,$bank) = $q->fetch_array(MYSQLI_BOTH)) {
							echo "<option value=\"$acct\">$bank</option>";
						}
						unset($q);
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
				<input type="text" name="balend" id="balend" style="width: 170px; text-align: right; font-weight: bold;" onchange="javascript: if(isNaN(stripComma(this.value)) == true) { this.value = '0.00'; sendErrorMessage('Invalid Ending Balance Specified.'); } else { this.value = kSeparator(this.value); computeDifference(this.value); }"></td>
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
													<td class="dgridhead" width=80>DOC #</td>
													<td class="dgridhead" width=80>DATE</td>
													<td class="dgridhead" width=80>CHECK #</td>
													<td class="dgridhead" width=190>PAYEE</td>
													<td class="dgridhead">MEMO</td>
													<td class="dgridhead" width=150 align=right style="padding-right: 40px;">AMOUNT</td>
													<td class="dgridhead" width=10>&nbsp;</td>
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
													<td align=right><input type="text" id="balanceend" name="balanceend" style="border: none; background-color: #f5f5f5; text-align: right; font-weight: bold; font-size: 12px;" value="0.00" readonly></td>
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
<div id="vatRelief" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<?php
			$o->_structMonths('vat_month','width: 80%; font-size: 11px;','gridInput');
			$o->_structInput('Year','35%','vat_year','gridInput','width: 80%;',''); 
		?>
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
		<?php
			$o->_structMonths('motb_month','width: 80%; font-size: 11px;','gridInput');
			$o->_structYear("Calendar Year","35%","motb_year","gridInput","style='width:80%; font-size: 11px;'");
		?>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateMOTB();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report in PDF</button>
			</td>
		</tr>
	</table>
</div>
<div id="preboard" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<?php
			$o->_structMonths('dboard_month','width: 80%; font-size: 11px;','gridInput');
			$o->_structYear("Calendar Year","35%","dboard_year","gridInput","style='width:80%; font-size: 11px;'");
		?>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="fetchDashboard();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pie2.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Open Dashboard</button>
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
						$uter = $o->dbquery("SELECT emp_id, fullname FROM user_info ORDER BY fullname ASC;");
						while($uterow = $uter->fetch_array()) {
							echo "<option value = '$uterow[0]'>$uterow[1]</option>";
						}
						unset($uter);
					?>
				</select> 
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<?php
			$o->_structInput('From','35%','audDTF','gridInput','width: 80%;',date('m/01/Y')); 
			$o->_structInput('To','35%','audDT2','gridInput','width: 80%;',date('m/d/Y')); 
		?>
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
		<?php
			$o->_structInput('From','35%','ivat_dtf','gridInput','width: 80%;',date('m/01/Y')); 
			$o->_structInput('To','35%','ivat_dt2','gridInput','width: 80%;',date('m/d/Y')); 
		?>
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
<div id="var" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35% valign=top><span class="spandix-l">Type :</span></td>
			<td>
				<select id="budType" style="width: 80%; font-size: 11px;" class="gridInput">
					<option value="Exp">- Expense Budgets -</option>
					<option value="Rev">- Revenue Targets -</option>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<?php $o->_structYear("Calendar Year","35%","budYear","gridInput","style='width:80%; font-size: 11px;'"); ?>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateVAR();" class="buttonding" style="font-size: 11px;"><img src="images/icons/chart2.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Show Budgets/Targets</button>
			</td>
		</tr>
	</table>
</div>
<div id="bst" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35% valign=top><span class="spandix-l">Type :</span></td>
			<td>
				<select id="bstType" style="width: 80%; font-size: 11px;" class="gridInput">
					<option value="Exp">- Expense Budgets -</option>
					<option value="Rev">- Revenue Targets -</option>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<?php $o->_structYear("Calendar Year","35%","bstYear","gridInput","style='width:80%; font-size: 11px;'"); ?>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="getBST();" class="buttonding" style="font-size: 11px;"><img src="images/icons/inventory.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;View Expense Budgets or Sales Targets</button>
			</td>
		</tr>
	</table>
</div>
<div id="dsr" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Billed/Charged To: </span></td>
			<td>
				<select name="dsr_cid" id="dsr_cid" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value=''>- All Customers -</option>
					<?php
						$custQuery = $o->dbquery("SELECT DISTINCT customer_code, customer_name FROM so_header WHERE customer_code != 0 AND `status` = 'Finalized' ORDER BY customer_name;");
						while($custRow = $custQuery->fetch_array()) {
							echo "<option value='$custRow[0]'>$custRow[1]</option>";

						}
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">Covered Period:</span></td>
			<td>
				<input type="text" id="dsr_dtf" class="dsr_dtd" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="dsr_dt2" class="dsr_dt2" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">Item Description</span></td>
			<td>
				<input type="text" id="dsr_item" class="dsr_item" style="width: 80%;" value="" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateDSR();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
				<button onClick="generateDSRX();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export Report to Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="countlist" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">		
		<tr>
			<td width=35% valign=top><span class="spandix-l">Covered Period:</span></td>
			<td>
				<input type="text" id="countlist_dtf" class="countlist_dtf" style="width: 80%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="countlist_dt2" class="countlist_dt2" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">Item Description</span></td>
			<td>
				<input type="text" id="dsr_item" class="dsr_item" style="width: 80%;" value="" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateCountList();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
				<!-- <button onClick="generateCountListX();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export Report to Excel</button> -->
			</td>
		</tr>
	</table>
</div>
<div id="pharDsr" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 25px;">
		<tr>
			<td width=35% valign=top><span class="spandix-l">Date From:</span></td>
			<td>
				<input type="text" id="phar_dsr_dtf" class="phar_dsr_dtf" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=8></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">Date To:</span></td>
			<td>
				<input type="text" id="phar_dsr_dt2" class="phar_dsr_dt2" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generatePharDSR();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
				<button onClick="generatePharDSRX();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export Report to Excel</button>
			</td>
		</tr>
	</table>
</div>
<div id="soSummary" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Billed/Charged To: </span></td>
			<td>
				<select name="so_cid" id="so_cid" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value=''>- All Customers -</option>
					<?php
						$custQuery = $o->dbquery("SELECT DISTINCT customer_code, customer_name FROM so_header WHERE customer_code != 0 ORDER BY customer_name;");
						while($custRow = $custQuery->fetch_array()) {
							echo "<option value='$custRow[0]'>$custRow[1]</option>";

						}
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l">Covered Period:</span></td>
			<td>
				<input type="text" id="so_dtf" class="so_dtf" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35% valign=top><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="so_dt2" class="so_dt2" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateSoSummary();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
				<!-- <button onClick="generateDSRX();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export Report to Excel</button> -->
			</td>
		</tr>
	</table>
</div>