<?php
	session_start();
	include("includes/dbUSE.php");
	if(isset($_GET['rid']) && $_GET['rid'] != "") { $res = getArray("select * from products_master where record_id='$_GET[rid]';"); }
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link href="style/jquery-ui.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script>
	function savePInfo(rid) {
		var msg = "";
		
		if($("#item_code").val() == "") { msg = msg + "- You did not assign Item Code for this merchandise..<br/>"; }
		if($("#description").val() == "") { msg = msg + "- You did not specify item's description..<br/>"; }
		if(isNaN(parent.stripComma($("#item_unitcost").val())) == true || $("#item_unitcost").val() == "" || $("#item_unitcost").val() == "0.00") { msg = msg + "- Invalid Unit Cost..<br/>"; }
		if(isNaN(parent.stripComma($("#item_unitprice").val())) == true || $("#item_unitprice").val() == "" || $("#item_unitprice").val() == "0.00") { msg = msg + "- Invalid Unit Price..<br/>"; }

		if(msg!="") {
			parent.sendErrorMessage(msg);
		} else {
			$.post("src/sjerp.php", { mod: "checkDupCode", rid: rid, item_code: $("#item_code").val(), barcode: $("#item_barcode").val(), sid: Math.random() }, function(data) {
				if(data == "NODUPLICATE") {
					var url = $(document.merchandise).serialize();
						url = "mod=savePInfo&"+url;
					$.post("src/sjerp.php", url);
					alert("Merchandise Successfuly Saved!"); 
					parent.closeDialog("#itemdetails");	
					parent.showItems();
				} else { parent.sendErrorMessage("Duplicate Item Code or Barcode has been detected!"); }
			},"html");	
		}
	}
	function deletePro(rid) {
			if(confirm("Are you sure you want to delete this record?") == true) {
				$.post("src/sjerp.php", { mod: "deletePro", rid: rid, sid: Math.random() }, function() { 
					alert("Merchandise Record Successfully Deleted!"); 
					parent.closeDialog("#itemdetails");	
					parent.showItems();
				});
			}
		}
</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">

 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<form name="merchandise" id="merchandise">
		<input type="hidden" id = "rid" name="rid" value="<?php echo $_GET['rid']; ?>">
		<tr>
			<td style="padding:0px;" valign=top>
				<table width=100% border=0 cellspacing=2 cellpadding=0>
					<tr>
						<td valign=top width="90%" class="td_content" style="padding: 10px;">		
							<table border="0" cellpadding="0" cellspacing="0" width=100%>
								<tr><td class="spandix-l" width=35%>Highest Educational Attainment :</td>
									<td align=left>
										<select name="highest_edu" id="highest_edu" style="width: 50%;" class="nInput">
											<option value="PG">Post Graduate</option>
											<option value="CG">College Graduate</option>
											<option value="CL">College Level</option>
											<option value="HG">High School Graduate</option>
											<option value="EG">Elementary Graduate</option>
										</select>
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td class="spandix-l" width=35%>Item Code :</td>
									<td align=left>
										<input type="text" name="item_code" id="item_code" style="width: 138px;" class="nInput" value="<?php echo $res['item_code']; ?>">
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td class="spandix-l" width=35%>Barcode :</td>
									<td align=left>
										<input type="text" name="item_barcode" id="item_barcode" style="width: 138px;" class="nInput" value="<?php echo $res['barcode']; ?>">
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td class="spandix-l" width=35%>Item Description :</td>
									<td align=left>
										<input type="text" name="item_description" id="item_description" style="width: 90%;" class="nInput" value="<?php echo htmlentities($res['description']); ?>">
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr>
									<td class="spandix-l" width="35%">Unit :</td>
									<td align="left">
										<select name="item_unit" id="item_unit" style="width: 50%;" class="nInput">
											<?php
												$iun = mysql_query("select unit, description from options_units;");
												while(list($u,$uu) = mysql_fetch_array($iun)) {
													echo "<option value='$u' ";
														if($res['unit'] == $u) { echo "selected"; }
													echo ">$uu</option>";
												}
											?>
										</select>
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td class="spandix-l" width=35%>Size (If Applicable) :</td>
									<td align=left>
										<input type="text" name="item_size" id="item_size" style="width: 30%;" class="nInput" value="<?php echo $res['size']; ?>">
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td class="spandix-l" width=35%>Color (If Applicable) :</td>
									<td align=left>
										<input type="text" name="item_color" id="item_color" style="width: 30%;" class="nInput" value="<?php echo $res['color']; ?>">
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td class="spandix-l" width=35%>Unit Cost :</td>
									<td align=left>
										<input type="text" name="item_unitcost" id="item_unitcost" style="width: 30%;" value='<?php echo number_format($res['unit_cost'],2); ?>' class="nInput">
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td class="spandix-l" width=35%>Unit Price <?php if($_SESSION['company'] == '1') { echo "(Level 1)"; } ?> :</td>
									<td align=left>
										<input type="text" name="item_unitprice" id="item_unitprice" style="width: 30%;" value='<?php echo number_format($res['unit_price'],2); ?>' class="nInput">
									</td>
								</tr>
								<?php if($_SESSION['company'] == 1) { ?>
								<tr>
									<td colspan=2>
										<table width=100% cellpadding=0 cellspacing=0>
											<tr><td height=4 colspan="2"></td></tr>
											<tr><td class="spandix-l" width=35%>Unit Price (Level 2) :</td>
												<td align=left width=20%>
													<input type="text" name="item_unitprice2" id="item_unitprice2" style="width: 95%;" value='<?php echo number_format($res['unit_price2'],2); ?>' class="nInput">
												</td>
												<td class="spandix-l" width=30%>Unit Price (Level 3) :</td>
												<td align=left width=15%>
													<input type="text" name="item_unitprice3" id="item_unitprice3" style="width: 95%;" value='<?php echo number_format($res['unit_price3'],2); ?>' class="nInput">
												</td>
											</tr>
											<tr><td height=4 colspan="2"></td></tr>
											<tr><td class="spandix-l" width=35%>Unit Price (Level 4) :</td>
												<td align=left width=20%>
													<input type="text" name="item_unitprice4" id="item_unitprice4" style="width: 95%;" value='<?php echo number_format($res['unit_price4'],2); ?>' class="nInput">
												</td>
												<td class="spandix-l" width=30%>Unit Price (Level 5) :</td>
												<td align=left width=15%>
													<input type="text" name="item_unitprice5" id="item_unitprice5" style="width: 95%;" value='<?php echo number_format($res['unit_price5'],2); ?>' class="nInput">
												</td>
											</tr>
											<tr><td height=4 colspan="2"></td></tr>
											<tr><td class="spandix-l" width=35%>Unit Price (Level 6) :</td>
												<td align=lef width=20%>
													<input type="text" name="item_unitprice6" id="item_unitprice6" style="width: 95%;" value='<?php echo number_format($res['unit_price6'],2); ?>' class="nInput">
												</td>
												<td class="spandix-l" width=30%>Unit Price (Level 7) :</td>
												<td align=left width=15%>
													<input type="text" name="item_unitprice7" id="item_unitprice7" style="width: 95%;" value='<?php echo number_format($res['unit_price7'],2); ?>' class="nInput">
												</td>
											</tr>
											<tr><td height=4 colspan="2"></td></tr>
											<tr><td class="spandix-l" width=35%>Unit Price (Level 8) :</td>
												<td align=left  width=20%>
													<input type="text" name="item_unitprice8" id="item_unitprice8" style="width: 95%;" value='<?php echo number_format($res['unit_price8'],2); ?>' class="nInput">
												</td>
												<td class="spandix-l" width=30%>Unit Price (Level 9) :</td>
												<td align=left width=15%>
													<input type="text" name="item_unitprice9" id="item_unitprice9" style="width: 95%;" value='<?php echo number_format($res['unit_price9'],2); ?>' class="nInput">
												</td>
											</tr>
											<tr><td height=4 colspan="2"></td></tr>
											<tr><td class="spandix-l" width=35%>Unit Price (Level 10) :</td>
												<td align=left width=20%>
													<input type="text" name="item_unitprice10" id="item_unitprice10" style="width: 95%;" value='<?php echo number_format($res['unit_price10'],2); ?>' class="nInput">
												</td>
												<td width=30%></td><td width=15%></td>
											</tr>
										</table>
									</td>
								</tr>
								<?php } else { ?>
									<input type="hidden" name="item_unitprice2" id="item_unitprice2" value="<?php echo $res['unit_price2']; ?>">
									<input type="hidden" name="item_unitprice3" id="item_unitprice3" value="<?php echo $res['unit_price3']; ?>">
									<input type="hidden" name="item_unitprice2" id="item_unitprice2" value="<?php echo $res['unit_price4']; ?>">
									<input type="hidden" name="item_unitprice3" id="item_unitprice3" value="<?php echo $res['unit_price5']; ?>">
									<input type="hidden" name="item_unitprice2" id="item_unitprice2" value="<?php echo $res['unit_price6']; ?>">
									<input type="hidden" name="item_unitprice3" id="item_unitprice3" value="<?php echo $res['unit_price7']; ?>">
									<input type="hidden" name="item_unitprice2" id="item_unitprice2" value="<?php echo $res['unit_price8']; ?>">
									<input type="hidden" name="item_unitprice3" id="item_unitprice3" value="<?php echo $res['unit_price9']; ?>">
									<input type="hidden" name="item_unitprice2" id="item_unitprice2" value="<?php echo $res['unit_price10']; ?>">
								<?php } ?>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td class="spandix-l" width=35%>Min. Inventory Level :</td>
									<td align=left>
										<input type="text" name="item_mininv" id="item_mininv" style="width: 30%;" class="nInput" value="<?php echo number_format($res['minimum_level'],2); ?>">
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr>
									<td class="spandix-l" width="35%">VAT Exempted :</td>
									<td align="left">
										<select name="vat_exempt" id="vat_exempt" style="width: 30%;" class="nInput">
											<option value = "N" <?php if($res['vat_exempt'] == 'N') { echo "selected"; } ?>>- No -</option>
											<option value = "Y" <?php if($res['vat_exempt'] == 'Y') { echo "selected"; } ?>>- Yes -</option>
										</select>
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr>
									<td class="spandix-l" width="35%">Inventory Group :</td>
									<td align="left">
										<select name="item_group" id="item_group" style="width: 90%;" class="nInput">
											<?php
												$iut = mysql_query("select `group`,group_description from options_igroup;");
												while(list($t,$tt) = mysql_fetch_array($iut)) {
													echo "<option value='$t' ";
														if($res['group'] == $t) { echo "selected"; }
													echo ">$tt</option>";
												}
											?>
										</select>
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr>
									<td class="spandix-l" width="35%">Income Account :</td>
									<td align="left">
										<select name="rev_acct" id="rev_acct" style="width: 90%;" class="nInput">
											<option value="">- NA -</option>
											<?php
												$iun = mysql_query("select acct_code,description from acctg_accounts where acct_grp='4000' and company = '$_SESSION[company]' order by acct_code;");
												while(list($aa,$ab) = mysql_fetch_array($iun)) {
													echo "<option value='$aa' ";
													if($res['rev_acct'] == $aa) { echo "selected"; }
													echo ">$ab</option>";
												}
											?>
										</select>
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr>
									<td class="spandix-l" width="35%">COGS Acct :</td>
									<td align="left">
										<select name="cogs_acct" id="cogs_acct" style="width: 90%;" class="nInput">
											<option value="">- NA -</option>
											<?php
												$iun = mysql_query("select acct_code,description from acctg_accounts where acct_grp in ('6000') and company = '$_SESSION[company]' order by acct_code;");
												while(list($bb,$bc) = mysql_fetch_array($iun)) {
													echo "<option value='$bb' ";
													if($res['cogs_acct'] == $bb) { echo "selected"; }
													echo ">$bc</option>";
												}
											?>
										</select>
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr>
									<td class="spandix-l" width="35%">Purchases/Expense Acct :</td>
									<td align="left">
										<select name="exp_acct" id="exp_acct" style="width: 90%;" class="nInput">
											<option value="">- NA -</option>
											<?php
												$iun = mysql_query("select acct_code,description from acctg_accounts where acct_grp in ('7000') and company = '$_SESSION[company]' order by acct_code;");
												while(list($bb,$bc) = mysql_fetch_array($iun)) {
													echo "<option value='$bb' ";
													if($res['exp_acct'] == $bb) { echo "selected"; }
													echo ">$bc</option>";
												}
											?>
										</select>
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr>
									<td class="spandix-l" width="35%">Asset Account :</td>
									<td align="left">
										<select name="asset_acct" id="asset_acct" style="width: 90%;" class="nInput">
											<option value="">- NA -</option>
											<?php
												$iun = mysql_query("select acct_code,description from acctg_accounts where acct_grp in ('1200','1500','1600') and company = '$_SESSION[company]' order by acct_code;");
												while(list($cc,$cd) = mysql_fetch_array($iun)) {
													echo "<option value='$cc' ";
													if($res['asset_acct'] == $cc) { echo "selected"; }
													echo ">$cd</option>";
												}
											?>
										</select>
									</td>
								</tr>
								<tr><td height=4></td></tr>
								<tr><td colspan=2><hr></hr></td></tr>
								<tr><td height=4></td></tr>
								<tr>
									<td align=center colspan=2>
										<button type="button" onClick="savePInfo(<?php echo $_GET['rid']; ?>);" class="buttonding"><img src="images/icons/floppy.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Save Merchandise Info.</b></button>
										<?php if(isset($_GET['rid']) && $_GET['rid'] != "") { ?>
										<button type="button" onClick="deletePro('<?php echo $_GET['rid']; ?>');" class="buttonding"><img src="images/icons/delete.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Delete Record</b></button>
										<?php } ?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	 </form>
 </table>

</body>
</html>
<?php mysql_close($con);