<div id="rrsummary" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Supplier :</span></td>
			<td>
				<select id="rrs_cid" style="width: 80%; font-size: 11px;" class="gridInput"  />
					<option value="">- All Suppliers -</option>
					<?php
						$_b = $o->dbquery("SELECT DISTINCT supplier, LPAD(supplier,2,0) AS cid, supplier_name FROM rr_header where branch = '$_SESSION[branchid]' and `status` = 'Finalized' ORDER BY supplier_name ASC;");
						while(list($_zz,$_za,$_zb) = $_b->fetch_array()) {
							echo "<option value='$_zz'>$_zb [$_za]</option>";
							}
						?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Covered Period :</span></td>
			<td>
				<input type="text" id="rrs_dtf" class="gridInput" style="width: 80%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="rrs_dt2" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateRRSummary();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="sgwsummary" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Type :</span></td>
			<td>
				<select id="sgw_type" style="width: 80%; font-size: 11px;" class="gridInput" />
					<option value="">- All Types -</option>
					<?php
						$tQuery = $o->dbquery("select id, `type` from options_wtype order by `type`;");
						while($tRow = $tQuery->fetch_array()) {
							echo "<option value='$tRow[0]' ";
							if($res['ref_type'] == $tRow[0]) { echo "selected"; }
							echo ">$tRow[1]</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Cost Center :</span></td>
			<td>
				<select id="sgw_type" style="width: 80%; font-size: 11px;" class="gridInput" />
					<option value="">- All -</option>
					<?php
						$uQuery = $o->dbquery("select unitcode, costcenter from options_costcenter order by costcenter;");
						while($uRow = $uQuery->fetch_array()) {
							echo "<option value='$uRow[0]' ";
							if($res['cost_center'] == $tRow[0]) { echo "selected"; }
							echo ">$uRow[1]</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Covered Period :</span></td>
			<td>
				<input type="text" id="sgw_dtf" class="gridInput" style="width: 80%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="sgw_dt2" class="gridInput" style="width: 80%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="generateSGW();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
<div id="inventorybook" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td width=35%><span class="spandix-l">Type :</span></td>
			<td>
				<select id="ibook_group" style="width: 90%; font-size: 11px;" class="gridInput">
				<option value="">- All Inventory Items -</option>
				<?php
					$iut = $o->dbquery("select `mid`,mgroup from options_mgroup;");
					while(list($t,$tt) = $iut->fetch_array()) {
						echo "<option value='$t'>$tt</option>";
					}
				?>
				</select>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l">Covered Period :</span></td>
			<td>
				<input type="text" id="ibook_dtf" class="gridInput" style="width: 90%;" value="<?php echo date('m/01/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr>
			<td width=35%><span class="spandix-l"></span></td>
			<td>
				<input type="text" id="ibook_dt2" class="gridInput" style="width: 90%;" value="<?php echo date('m/d/Y'); ?>" />
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="processInventory();" class="buttonding" style="font-size: 11px;"><img src="images/icons/processraw.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;View Inventory</button>
				<button onClick="exportInventoryNow();" class="buttonding" style="font-size: 11px;"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export to Excel</button>
			</td>
		</tr>
	</table>
</div>