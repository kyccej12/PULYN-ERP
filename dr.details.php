<?php	
	/* UNSET QUED FOR DELETION */
	session_start();
	
	include("functions/dr.displayDetails.fnc.php");
	include("includes/dbUSE.php");
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['dr_no']) && $_REQUEST['dr_no']!='0') { 
		$dr_no = $_REQUEST['dr_no']; 
		$res = getArray("select *, lpad(customer,6,0) as ccode, date_format(dr_date,'%m/%d/%Y') as d8 from dr_header where dr_no  = '$dr_no';");
		$cSelected = "Y";
	} else {  
		list($dr_no) = getArray("select ifnull(max(dr_no),0)+1 from dr_header;"); 
		$res['status'] = "Active"; $dS = "1"; $cSelected = "N";
	}
		
	function setHeaderControls($status,$lock,$dr_no,$uid,$dS) {
		list($urights) = getArray("select user_type from user_info where emp_id='$uid'");
		if($lock != 'Y') {
			switch($status) {
				case "Finalized":
					list($posted_by,$posted_on) = getArray("select fullname as name, date_format(updated_on,'%m/%d/%Y %p') as date_posted from dr_header a left join user_info b on a.updated_by=b.emp_id where a.dr_no='$dr_no';");
					if($urights == "admin") {
						$headerControls = "<button type=button onclick=\"javascript: reopenDR('$dr_no');\"><img src='images/icons/edit.png' align=absmiddle width=16 height=16 />&nbsp;Set this Document to Active Status</button>&nbsp;<button  onClick=\"javascript:reprintDR('$dr_no','$_SESSION[userid]');\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Reprint Delivery Receipt</button>";
					} else { echo "<b>Posted By:</b> $posted_by  <b>::  Posted On:</b> $posted_on"; }
				break;
				case "Cancelled":
					if($urights == "admin") {
						$headerControls = $headerControls . "<button type=button onclick=\"javascript:reuseRR('$dr_no');\" style=\"padding: 5px;\"><img src=\"images/icons/refresh.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Recycle this Document</button>";	
					}
				break;
				case "Active": default:
					$headerControls = "<button  onClick=\"javascript:printDR('$dr_no','$_SESSION[userid]');\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Print & Finalize Delivery Receipt</button>&nbsp;<button onclick=\"javascript:saveDRHeader();\"><img src=\"images/save.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Save Changes</button>&nbsp;";
					if($urights == "admin" && $dS != 1) {
						$headerControls = $headerControls . "<button onclick=\"javascript:cancelDR('$dr_no');\"><img src=\"images/icons/cancel.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Cancel this Document</button>";
					}
				break;
			}
		}
		echo $headerControls . "&nbsp;<button type=button onclick=\"javascript: parent.showDRList();\" style=\"padding: 5px;\"><img src='images/icons/home-icon.png' align=absmiddle width=16 height=16 />&nbsp;Go Back to Main</button>&nbsp;<button type=button onclick=\"javascript: parent.close_div();\" style=\"padding: 5px;\"><img src='images/icons/cancelled.png' align=absmiddle width=16 height=16 />&nbsp;Close Window</button>";
	}
	
	function setNavButtons($dr_no) {
		list($fwd) = getArray("select dr_no from dr_header where dr_no > $dr_no limit 1;");
		list($prev) = getArray("select dr_no from dr_header where dr_no < $dr_no order by dr_no desc limit 1;");
		list($last) = getArray("select dr_no from dr_header order by dr_no desc limit 1;");
		list($first) = getArray("select dr_no from dr_header order by dr_no asc limit 1;");
		if($prev)
			$nav = $nav . "<a href=# onclick=\"parent.viewRR('$prev');\"><img src='images/resultset_previous.png'  title='Previous Record' /></a>";
		if($fwd) 
			$nav = $nav . "<a href=# onclick=\"parent.viewRR('$fwd');\"><img src='images/resultset_next.png' 'title='Next Record' /></a>";
		echo "<a href=# onclick=\"parent.viewRR('$first');\"><img src='images/resultset_first.png' title='First Record' /><a>" . $nav . "<a href=# onclick=\"parent.viewRR('$last');\"><img src='images/resultset_last.png' title='Last Record' /></a>";
	}
	if($res['status'] != "Active") { $isReadOnly = "readonly"; $isDisabled = "disabled"; }
	
?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Geck Distributors</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="style/jquery-ui.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="js/jquery.js"></script>
	<script language="javascript" src="js/dr.js"></script>
	<script language="javascript" src="js/jquery-ui.js"></script>
	<script language="javascript" src="js/jquery.center.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>
		$('html').click(function(){ $("#suggestions").fadeOut(200); });
		$(function() { $("#suggestionBox").centerIt({vertical: false}); $("#rr_date").datepicker(); $("#ref_date").datepicker(); });

		
	</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div style="padding: 10px;">
	<table width=100% border=0 cellpadding=0 cellspacing=0 align=center>
		<input type=hidden id="dr_no" value="<?php echo $dr_no; ?>">
		<input type="hidden" name="cSelected" id="cSelected" value="<?php echo $cSelected; ?>">
		<input type="hidden" name="prev_dr_date" id="prev_dr_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>" >
		<tr>
			<td valign=top >
				<table width=98% cellpadding=0 cellspacing=0 border=0 align=center>
					<tr>
						<td class="upper_menus" align=left>
							<?php setHeaderControls($res['status'],$res['locked'],$dr_no,$_SESSION['userid'],$dS); ?>
						</td>
						<td width=30% align=right style='padding-right: 5px;'><?php if($dr_no) { setNavButtons($dr_no); } ?></td>
					</tr>
					<tr><td height=2></td></tr>
				</table>
				<table width=98% cellpadding="0" cellspacing="0" align=center style="border: 1px solid #436178;">
				<tr>
					<td align=left class="gridHead">DELIVERY RECEIPT</td>
					<td align=right class="gridHead">DOCUMENT NO. <?php echo str_pad($dr_no,10,'0',STR_PAD_LEFT); ?></td>
				</tr>
				<tr>
					<td width="100%" colspan=2>
						<table border="0" cellpadding="0" cellspacing="1" width=100%>
							<tr>
								<td width=60% valign=top>
									<table width=100% style="padding:0px 0px 0px 0px;">
										<tr><td height=2></td></tr>
										<tr>
											<td class="bareBold" align=right width=25% style="padding-right: 5px;">Loose Leaf DR #&nbsp;:</td>
											<td align="left">
												<input type=text id="dr_stub_no" class="gridInput" name="dr_stub_no" value="<?php echo $res['dr_stub_no']; ?>" <?php echo $isReadonly; ?> style="width: 126px;">
											</td>
										</tr>
										<tr><td height=2></td></tr>
										<tr>
											<td class="bareBold" align=right valign=top width=25% style="padding-right: 5px;">Supplier&nbsp;:</td>
											<td align="left">
												<table cellspacing=0 cellpadding=0 border=0 width=100%>
													<tr>
														<td width=25%><input type="text" id="customer_id" name="customer_id" value="<?php echo $res['ccode']?>" <?php if($res['status'] == "Active" or $res['status'] == "") { echo "onkeyup=\"contactlookup(this.value, this.id);\""; } ?> class="inputSearch2" style="padding-left: 22px; padding-bottom: 1px; padding-top: 1px;" <?php echo $isReadOnly; ?> style="width: 112px;"></td>
														<td width=75% align=right colspan=2><input type="text" name="customer_name" id="customer_name" autocomplete="off" class="gridInput" value="<?php echo $res['customer_name']; ?>" style="width: 95%; font-weight: bold;" readonly></td>
													</tr>
													<tr>
														<td style="font-size: 9px; padding-left: 5px;">Code</td><td colspan=2 style="font-size: 9px; padding-left: 20px;">Supplier Name</td>
													</tr>
													<tr><td height=2></td></tr>
													<tr>
														<td width=100% colspan=2><input class="gridInput" type="text" id="cust_address" name="cust_address" value="<?php echo $res['customer_addr']?>" style="font-weight: bold; width: 98%;" readonly></td>
													</tr>
													<tr>
														<td colspan=2 style="font-size: 9px; padding-left: 5px;" colspan=2 >Address</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr><td height=2></td></tr>
									</table>
								</td>
								<td valign=top>
									<table border="0" cellpadding="0" cellspacing="1" width=100%>
										<tr><td height=2></td></tr>
										<tr>
											<td align="right" width="30%" class="bareBold" style="padding-right: 5px;">Trans. Date&nbsp;:</td>
											<td align=left>
												<input class="gridInput" style="width:40%;" type=text name="dr_date" id="dr_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>" <?php echo $isReadonly; ?> onchange="javascript: checkLockdate(this.id,this.value,$('#prev_dr_date').val());">
											</td>				
										</tr>
										<tr><td height=2></td></tr>
									</table>
								</td>
								</tr>
								<tr>
									<td width=100% colspan=2 valign=top class="inner_border_bottom">
										<table width=100% cellspacing=0 cellpadding=0 border=0>
											<tr>
												<td align=right width=15% valign="top" class=bareBold style="padding-right: 5px;">Remarks/Memo :</td>
												<td align=left>&nbsp;<textarea class="gridInput" type="text" id="remarks" style="width:80%;" <?php if($res['status'] == "Active") { echo "onblur=\"saveDRHeader();\" "; } echo $isReadOnly; ?>><?php echo $res['remarks']; ?></textarea></td>
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
							<table cellspacing=0 cellpadding=0 border=0 width=100% style="border: 1px solid #436178;">
								<tr bgcolor="#887e6e">
									<td align=left class="gridHead" width="5%">&nbsp;</td>
									<td align=left class="gridHead" width="10%" style="padding-left: 10px;">ITEM CODE</td>
									<td align=left class="gridHead" width="35%">DESCRIPTION</td>
									<td align=center class="gridHead" width="10%">UNIT</td>
									<td align=center class="gridHead" width="10%">UNIT COST</td>
									<td align=center class="gridHead" width="10%">QTY</td>
									<td align=center class="gridHead" width="10%">AMOUNT</td>
									<td align=center class="gridHead" width="10%">&nbsp;</td>
								</tr>
							</table>
							<table cellpadding=0 cellspacing=0 border=0 width=100% id="rrdetails">
								<?php DRDETAILS($dr_no); ?>
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
</body>
</html>