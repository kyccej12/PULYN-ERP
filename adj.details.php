
<?php	
	/* UNSET QUED FOR DELETION */
	session_start();
	include("functions/adj.displayDetails.fnc.php");
	include("includes/dbUSE.php");
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['doc_no']) && $_REQUEST['doc_no'] != '') { 
		$doc_no = $_REQUEST['doc_no']; 
		$res = getArray("select *, lpad(cid,6,0) as ccode, date_format(doc_date,'%m/%d/%Y') as d8, if(ref_date='0000-00-00','',date_format(ref_date,'%m/%d/%Y')) as rd8 from adj_header where doc_no='$doc_no' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
		$cSelected = "Y";
	} else {  
		list($doc_no) = getArray("select ifnull(max(doc_no),0)+1 from adj_header where company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';"); 
		$res['status'] = "Active"; $dS = "1"; $cSelected = "N";
	}
		
	function setHeaderControls($status,$lock,$doc_no,$uid,$dS) {
		list($urights) = getArray("select user_type from user_info where emp_id='$uid'");
		if($lock != 'Y') {
			switch($status) {
				case "Finalized":
					list($posted_by,$posted_on) = getArray("select fullname as name, date_format(updated_on,'%m/%d/%Y %p') as date_posted from adj_header a left join user_info b on a.updated_by=b.emp_id where a.doc_no='$doc_no';");
					if($urights == "admin") {
						$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript: reopenADJ('$doc_no');\"><img src='images/icons/edit.png' align=absmiddle width=16 height=16 />&nbsp;Set this Document to Active Status</a>&nbsp;";
					}
					$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onClick=\"javascript:parent.printADJ('$doc_no','$_SESSION[userid]','N');\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Print Inventory Adjustment</a>&nbsp;";
				break;
				case "Cancelled":
					if($urights == "admin") {
						$headerControls = $headerControls . "<a href=\"#\" class=\"topClickers\" onclick=\"javascript:reuseADJ('$doc_no');\" style=\"padding: 5px;\"><img src=\"images/icons/refresh.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Recycle this Document</a>&nbsp;";	
					}
				break;
				case "Active": default:
					$headerControls = "<a href=\"#\" class=\"topClickers\" onClick=\"javascript:printADJ('$doc_no','$_SESSION[userid]');\"><img src=\"images/icons/ok.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Finalize Inventory Adjustment</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:saveAdjHeader();\"><img src=\"images/save.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Save Changes</a>&nbsp;";
					if($urights == "admin" && $dS != 1) {
						$headerControls = $headerControls . "<a href=\"#\" class=\"topClickers\" onclick=\"javascript:cancelADJ('$doc_no');\"><img src=\"images/icons/cancel.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Cancel this Document</a>&nbsp;";
					}
				break;
			}
		} else {
			$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onClick=\"javascript:parent.printADJ('$doc_no','$_SESSION[userid]','Y');\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Re-Print Inventory Adjustment</a>&nbsp;";
		}
		echo $headerControls;
	}
	
	function setNavButtons($doc_no) {
		list($fwd) = getArray("select doc_no from adj_header where doc_no > $doc_no and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]' limit 1;");
		list($prev) = getArray("select doc_no from adj_header where doc_no < $doc_no and company = '$_SESSION[company]'and branch = '$_SESSION[branchid]' order by doc_no desc limit 1;");
		list($last) = getArray("select doc_no from adj_header where company = '$_SESSION[company]' and branch = '$_SESSION[branchid]' order by doc_no desc limit 1;");
		list($first) = getArray("select doc_no from adj_header where company = '$_SESSION[company]' and branch = '$_SESSION[branchid]' order by doc_no asc limit 1;");
		if($prev)
			$nav = $nav . "<a href=# onclick=\"parent.viewPO('$prev');\"><img src='images/resultset_previous.png'  title='Previous Record' /></a>";
		if($fwd) 
			$nav = $nav . "<a href=# onclick=\"parent.viewPO('$fwd');\"><img src='images/resultset_next.png' 'title='Next Record' /></a>";
		echo "<a href=# onclick=\"parent.viewPO('$first');\"><img src='images/resultset_first.png' title='First Record' /><a>" . $nav . "<a href=# onclick=\"parent.viewPO('$last');\"><img src='images/resultset_last.png' title='Last Record' /></a>";
	}
	if($res['status'] != "Active") { $isReadOnly = "readonly"; $isDisabled = "disabled"; }
	
?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="js/jquery.dialogextend.js"></script>
	<script language="javascript" src="js/adj.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>
		$('html').click(function(){ $("#suggestions").fadeOut(200); });
		$(function() { $("#doc_date").datepicker(); $("#ref_date").datepicker(); 
			$('#qty').bind('keypress', function(e) { if(e.keyCode ==13){ addDetails(); } });
			$('#amount').bind('keypress', function(e) { if(e.keyCode ==13){ addDetails(); } });
		});
	</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div>
	<table width=100% border=0 cellpadding=0 cellspacing=0 align=center>
		<input type=hidden id="doc_no" name="doc_no" value="<?php echo $doc_no; ?>">
		<input type="hidden" name="cSelected" id="cSelected" value="<?php echo $cSelected; ?>">
		<input type=hidden name="prev_doc_date" id="prev_doc_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>">
		<tr>
			<td valign=top >
				<table width=98% cellpadding=0 cellspacing=0 border=0 align=center>
					<tr>
						<td class="upper_menus" align=left>
							<?php setHeaderControls($res['status'],$res['locked'],$doc_no,$_SESSION['userid'],$dS); ?>
						</td>
						<td width=30% align=right style='padding-right: 5px;'><?php if($doc_no) { setNavButtons($doc_no); } ?></td>
					</tr>
					<tr><td height=2></td></tr>
				</table>
				<table width=98% cellpadding="0" cellspacing="0" align=center style="border: 1px solid #436178;">
				<tr>
					<td align=left class="gridHead">INVENTORY ADJUSTMENT FORM</td>
					<td align=right class="gridHead">DOCUMENT NO. <?php echo str_pad($_SESSION['branchid'],3,'0',STR_PAD_LEFT) . '-' . str_pad($doc_no,10,'0',STR_PAD_LEFT); ?></td>
				</tr>
				<tr>
					<td width="100%" colspan=2>
						<table border="0" cellpadding="0" cellspacing="1" width=100%>
							<tr>
								<td width=50% valign=top>
									<table width=100% style="padding:0px 0px 0px 0px;">
										<tr><td height=2></td></tr>
										<tr>
											<td class="bareBold" align=right valign=top width=30% style="padding-right: 5px;">Customer/Supplier :</td>
											<td align="left">
												<table cellspacing=0 cellpadding=0 border=0 width=100%>
													<tr>
														<td width=25%><input type="text" id="customer_id" name="customer_id" value="<?php echo $res['ccode']?>" class="inputSearch2" style="padding-left: 22px;" <?php echo $isReadOnly; ?>></td>
														<td width=75% align=right colspan=2><input class="gridInput" type="text" name="customer_name" id="customer_name" autocomplete="off" value="<?php echo $res['cname']; ?>" style="width: 100%;" readonly></td>
													</tr>
													<tr>
														<td style="font-size: 9px; padding-left: 5px;">Code</td><td colspan=2 style="font-size: 9px; padding-left: 20px;">Name/Trade Name</td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td width=100% colspan=2><input class="gridInput" type="text" id="cust_address" name="cust_address" value="<?php echo $res['caddr']?>" style="width: 100%;" readonly></td>
													</tr>
													<tr>
														<td colspan=2 style="font-size: 9px; padding-left: 5px;" colspan=2 >Address</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr><td height=2></td></tr>
										<tr>
											<td align="right" class="bareBold" style="padding-right: 5px;">Type of Adjustment :</td>
											<td align=left>
												<select class="gridInput" style="width:70%; font-size: 11px;" name="adjustment_type" id="adjustment_type" <?php echo $isDisabled; ?>>
													<option value="SR" <?php if($res['adjustment_type'] == "SR") { echo "selected"; } ?>>Customer Sales Return</option>
													<option value="PR" <?php if($res['adjustment_type'] == "PR") { echo "selected"; } ?>>Purchase Return</option>
													<option value="OD" <?php if($res['adjustment_type'] == "OD") { echo "selected"; } ?>>Cash Sales Overage</option>
													<option value="UD" <?php if($res['adjustment_type'] == "UD") { echo "selected"; } ?>>Cash Sales Underage</option>
													<option value="GA" <?php if($res['adjustment_type'] == "GA") { echo "selected"; } ?>>General Inventory Adjustment</option>
												</select>
											</td>				
										</tr>
										<tr><td height=2></td></tr>
										<tr>
											<td align="right" class="bareBold" style="padding-right: 5px;">Requested By :</td>
											<td align=left>
												<input class="gridInput" style="width:70%;" type=text name="requested_by" id="requested_by" value="<?php echo $res['requested_by']; ?>" <?php echo $isReadOnly; ?> >
											</td>				
										</tr>
										<tr><td height=2></td></tr>
									</table>
								</td>
								<td valign=top>
									<table border="0" cellpadding="0" cellspacing="1" width=100%>
										<tr><td height=2></td></tr>
										<tr>
											<td align="right" width="30%" class="bareBold" style="padding-right: 5px;">Doc. Date&nbsp;:</td>
											<td align=left>
												<input class="gridInput" style="width:50%;" type=text name="doc_date" id="doc_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>" <?php echo $isDisabled; ?> onChange = "javascript: checkLockDate(this.id,this.value,$('#prev_doc_date').val());" >
											</td>				
										</tr>
										<tr><td height=2></td></tr>
										<tr>
											<td align="right" width="30%" class="bareBold" style="padding-right: 5px;">Ref. Type&nbsp;:</td>
											<td align=left>
												<select class="gridInput" style="width:50%; font-size: 11px;" name="ref_type" id="ref_type" <?php echo $isDisabled; ?>>
													<option value="SI" <?php if($res['ref_type'] == "SI") { echo "selected"; } ?>>Sales Invoice</option>
													<option value="RR" <?php if($res['ref_type'] == "PO") { echo "selected"; } ?>>Receiving Report</option>
													<option value="SRR" <?php if($res['ref_type'] == "SEE") { echo "selected"; } ?>>Stocks Receiving</option>
													<option value="STR" <?php if($res['ref_type'] == "STR") { echo "selected"; } ?>>Stocks Transfer</option>
													<option value="SW" <?php if($res['ref_type'] == "SW") { echo "selected"; } ?>>Stocks Withdrawal</option>
												</select>
											</td>				
										</tr>
										<tr><td height=2></td></tr>
										<tr>
											<td align="right" width="30%" class="bareBold" style="padding-right: 5px;">Ref. No.&nbsp;:</td>
											<td align=left>
												<input class="gridInput" style="width:50%;" type=text name="ref_no" id="ref_no" value="<?php  echo $res['ref_no']; ?>" <?php echo $isReadOnly; ?> >
											</td>				
										</tr>
										<tr><td height=2></td></tr>
										<tr>
											<td align="right" width="30%" class="bareBold" style="padding-right: 5px;">Ref. Date&nbsp;:</td>
											<td align=left>
												<input class="gridInput" style="width:50%;" type=text name="ref_date" id="ref_date" value="<?php echo $res['rd8']; ?>" <?php echo $isDisabled; ?> >
											</td>				
										</tr>
									</table>
								</td>
								</tr>
								<tr>
									<td width=100% colspan=2 valign=top class="inner_border_bottom">
										<table width=100% cellspacing=0 cellpadding=0 border=0>
											<tr>
												<td align=right width=15% valign="top" class=bareBold style="padding-right: 5px;">Remarks/Memo :</td>
												<td align=left>&nbsp;<textarea class="gridInput" type="text" id="remarks" style="width:80%;" <?php echo $isReadOnly; ?> onChange = "javascript: saveAdjHeader();"><?php echo $res['remarks']; ?></textarea></td>
											</tr>
											<tr><td height=4></td></tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					</table>
					<table><tr><td height=2></td></tr></table>
					<table width=98% cellpadding="0" cellspacing="0" align=center>
					<tr>
						<td colspan=2>
							<table cellspacing=0 cellpadding=0 border=0 width=100%>
								<tr bgcolor="#887e6e">
									<td align=left class="gridHead" width="5%">&nbsp;</td>
									<td align=left class="gridHead" width="10%" style="padding-left: 10px;">ITEM CODE</td>
									<td align=left class="gridHead" width="35%">DESCRIPTION</td>
									<td align=center class="gridHead" width="10%">UNIT</td>
									<td align=center class="gridHead" width="10%">UNIT COST</td>
									<td align=center class="gridHead" width="10%">QTY</td>
									<td align=center class="gridHead" width="15%">AMOUNT</td>
									<td align=center class="gridHead" width="5%">&nbsp;</td>
								</tr>
								<?php
									if(($res['status'] == "Active" || $res['status'] == "") && $res['locked'] != 'Y') {
									echo '<tr bgcolor="'.initBackground($i).'">
											<td align=center class="grid" width="5%" align=center><img src="images/icons/green_arrow.png" style="vertical-align: middle;" /></td>
											<td align=center class="grid" idth="45%" colspan=2><input type="hidden" id="product_code" /><input type=text class="inputSearch" style="padding-left: 22px;" id="description" style="width: 95%;" /></td>
											<td align=center class="grid" width="10%">'.constructUnit().'</td>
											<td align=center class="grid" width="10%"><input class="gridInput" type=text id="unit_price"style="width: 90%; text-align: right;" onchange="computeAmount();"/></td>
											<td align=center class="grid" width="10%"><input class="gridInput" type=text id="qty" style="width: 90%; text-align: right;" onblur="computeAmount();" /></td>
											<td align=center class="grid" width="15%"><input class="gridInput" type=text id="amount" style="width: 90%;text-align: right;" readonly/></td>
											<td align=center class="grid" width="5%"><a href="#" onclick="javascript: addDetails();" title="Add Item"><img src="images/icons/add-2.png" width=18 height=18 style="vertical-align: middle;" /></a></td>
										</tr>';
									$i++;
								}
								?>
							</table>
							<table cellpadding=0 cellspacing=0 border=0 width=100% id="podetails">
								<?php ADJDETAILS($doc_no); ?>
							</table>
						</td>
					</tr>
				</table>
				<table><tr><td height=8></td></tr></table>
			</td>
		</tr>
	</table>
</div>
<div class="suggestionsBox" id="suggestions" style="display: none;">
	<div class="suggestionList" id="autoSuggestionsList">&nbsp;</div>
</div>
<div id="loading_popout" style="display:none;" align=center>
	<!--<img style="display:block;margin-left:auto;margin-right:auto;" src="images/ajax-loader.gif" width=128 height=128 align=absmiddle /> -->
	<progress id='progess_trick' value='40' max ='100' width='220px'></progress> <br>
	Please wait while the server is processing your request.
</div>
<div id="loaderMessage" title="Processing..." style="display: none;">
	<p><span style="float:left; margin:0 7px 20px 0;"><img src="images/loader.gif" /></span>Please wait while the system is processing your request...</p>
</div>
</body>
</html>