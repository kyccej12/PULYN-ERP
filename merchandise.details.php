<?php
	include("includes/dbUSE.php");
	session_start();
	
	if(isset($_GET['rid']) && $_GET['rid'] != "") { $res = getArray("select * from products_master where record_id='$_GET[rid]';"); }
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Geck Distributors</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link href="style/jquery-ui.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/jquery-ui.js"></script>
<script language="javascript" src="js/date.js"></script>
<script language="javascript" src="js/shortcut.js"></script>
<script>

	function init() {
		shortcut.add("F12", function() { savePInfo(<?php echo $_GET['rid']; ?>); });
		shortcut.add("F9", function() { deletePro('<?php echo $_GET['rid']; ?>'); });
	} 

	function savePInfo(rid) {
		var msg = "";
		
		if($("#item_code").val() == "") { msg = msg + "You did not assign Item Code for this merchandise..<br/>"; }
		
		if(msg!="") {
			parent.sendErrorMessage("<b>Unable to continue due to the following error(s):</b><br/><br/>" + msg);
		} else {
			$.post("geck.datacontrol.php", { mod: "checkDupCode", rid: rid, item_code: $("#item_code").val(), barcode: $("#item_barcode").val(), sid: Math.random() }, function(data) {
				if(data == "NODUPLICATE") {
					var url = $(document.merchandise).serialize();
						url = "mod=savePInfo&"+url;
					var obj = "obj_"+rid;
					$.post("geck.datacontrol.php", url);
					alert("Merchandise Successfuly Saved!"); 
					parent.close_div2();
					parent.productSaved(obj,encodeURIComponent(item_code));
				} else { parent.sendErrorMessage("Unable to save this information. A duplicate Item Code or Barcode has been detected!"); }
			},"html");	
		}
	}
	function deletePro(rid) {
		if(confirm("Are you sure you want to delete this record?") == true) {
			$.post("geck.datacontrol.php", { mod: "deletePro", rid: rid, sid: Math.random() }, function() { 
				alert("Merchandise Record Successfully Deleted!"); 
				parent.close_div2();
				parent.showProducts();
			});
		}
	}

	window.onload = init;

</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">

 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<form name="merchandise" id="merchandise">
		<input type="hidden" id = "rid" name="rid" value="<?php echo $_GET['rid']; ?>">
		<tr>
			<td style="padding:0px;" valign=top>
				<table width=100% border=0 cellspacing=2 cellpadding=0>
					<tr>
						<td valign=top width="90%" class="td_content" style="padding: 10px;">		
							<table border="0" cellpadding="0" cellspacing="0" width=100%>
								
								<tr><td class="spandix-l" width=35%>Item Code :</td>
									<td align=left>
										<input type="text" name="item_code" id="item_code" style="width: 138px;" class="nInput" value="<?php echo $res['item_code']; ?>">
									</td>
								</tr>
								<tr><td class="spandix-l" width=35%>Barcode :</td>
									<td align=left>
										<input type="text" name="item_barcode" id="item_barcode" style="width: 138px;" class="nInput" value="<?php echo $res['barcode']; ?>">
									</td>
								</tr>
								<tr><td class="spandix-l" width=35%>Item Description :</td>
									<td align=left>
										<input type="text" name="item_description" id="item_description" style="width: 90%;" class="nInput" value="<?php echo $res['description']; ?>">
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr>
									<td class="spandix-l" width="35%">Category :</td>
									<td align="left">
										<select name="item_category" id="item_category" style="width: 90%;" class="nInput">
											<?php
												$itg = mysql_query("select id,itype from options_mtype;");
												while(list($it,$id) = mysql_fetch_array($itg)) {
													echo "<option value='$it' ";
													if($res['category'] == $it) { echo "selected"; }
													echo ">$id</option>";
												}
											?>
										</select>
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr>
									<td class="spandix-l" width="35%">Unit :</td>
									<td align="left">
										<select name="item_unit" id="item_unit" style="width: 30%;" class="nInput">
											<?php
												$iun = mysql_query("select unit,description from options_unit;");
												while(list($ut,$ud) = mysql_fetch_array($iun)) {
													echo "<option value='$ut' ";
														if($res['unit'] == $ut) { echo "selected"; }
													echo ">$ud</option>";
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
								<tr><td class="spandix-l" width=35%>Color (If Applicable) :</td>
									<td align=left>
										<input type="text" name="item_color" id="item_color" style="width: 30%;" class="nInput" value="<?php echo $res['color']; ?>">
									</td>
								</tr>
								<tr><td class="spandix-l" width=35%>Unit Cost :</td>
									<td align=left>
										<input type="text" name="item_unitcost" id="item_unitcost" style="width: 30%;" value='<?php echo number_format($res['unit_cost'],2); ?>' class="nInput">
									</td>
								</tr>
								<tr><td class="spandix-l" width=35%>Unit Price :</td>
									<td align=left>
										<input type="text" name="item_unitprice" id="item_unitprice" style="width: 30%;" value='<?php echo number_format($res['unit_price'],2); ?>' class="nInput">
									</td>
								</tr>
								<tr><td class="spandix-l" width=35%>Min. Inventory Level :</td>
									<td align=left>
										<input type="text" name="item_mininv" id="item_mininv" style="width: 30%;" class="nInput" value="<?php echo number_format($res['minimum_level'],2); ?>">
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr>
									<td class="spandix-l" width="35%">Revenue Account :</td>
									<td align="left">
										<select name="rev_acct" id="rev_acct" style="width: 90%;" class="nInput">
											<option value="">- NA -</option>
											<?php
												$iun = mysql_query("select acct_code,description from acctg_accounts where acct_grp='4000' order by acct_code;");
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
									<td class="spandix-l" width="35%">Expense/COGS/Purchases :</td>
									<td align="left">
										<select name="exp_acct" id="exp_acct" style="width: 90%;" class="nInput">
											<option value="">- NA -</option>
											<?php
												$iun = mysql_query("select acct_code,description from acctg_accounts where acct_grp='6000' order by acct_code;");
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
												$iun = mysql_query("select acct_code,description from acctg_accounts where acct_grp='1200' order by acct_code;");
												while(list($cc,$cd) = mysql_fetch_array($iun)) {
													echo "<option value='$cc' ";
													if($res['asset_acct'] == $cc) { echo "selected"; }
													echo ">$cd</option>";
												}
											?>
										</select>
									</td>
								</tr>
								<tr><td colspan=2><hr></hr></td></tr>
								<tr><td height=4></td></tr>
								<tr>
									<td align=center colspan=2>
										<button type="button" onClick="savePInfo(<?php echo $_GET['rid']; ?>);" class="buttonding"><img src="images/icons/floppy.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Save Merchandise Info. (F12)</b></button>
										<?php if(isset($_GET['rid']) && $_GET['rid'] != "") { ?>
										<button type="button" onClick="deletePro('<?php echo $_GET['rid']; ?>');" class="buttonding"><img src="images/icons/delete.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Delete Record (F9)</b></button>
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