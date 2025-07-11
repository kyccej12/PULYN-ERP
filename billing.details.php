<?php	
	/* UNSET QUED FOR DELETION */
	session_start();
	unset($_SESSION['ques']);
	
	include("functions/cv.displayDetails.fnc.php");
	include("includes/dbUSE.php");
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['rid']) && $_REQUEST['rid']!='0' && $_REQUEST['rid'] != '') { 
		$res = getArray("select *, lpad(acctID,5,0) as xacct, date_format(billingDate,'%m/%d/%Y') as billD8, date_format(periodFrom,'%m/%d/%Y') as pfrom, date_format(periodTo,'%m/%d/%Y') as pto from billing where recordID = '$_REQUEST[rid]';");
		$billingNo = $res['billingNo'];	$cSelected = "Y"; $sSelected = "Y";
	} else {  
		$res['status'] = "Active"; $dS = "1"; $cSelected = "N"; $sSelected = "N"; $withSI = 'N'; $si_no = 0;
	}
		
	function setHeaderControls($status,$uid,$rid,$dS) {
		list($urights) = getArray("select user_type from user_info where emp_id='$uid'");
		switch($status) {
			case "Active": default:
				$headerControls = "<a href=\"#\" class=\"topClickers\" onclick=\"saveHeader();\"><img src=\"images/save.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Save Changes</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"postBilling();\"><img src=\"images/icons/rr.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Post & Finalize Billing</a>&nbsp;&nbsp;";
				if($urights == "admin" && $dS != 1) {
					$headerControls .= "<a href=\"#\" class=\"topClickers\" onclick=\"javascript:cancelBilling('$rid');\"><img src=\"images/icons/cancel.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Cancel this Document</a>";
				}
			break;
			case "Finalized":
				$headerControls = "<a href=\"#\" class=\"topClickers\" onclick=\"reopen($rid);\"><img src=\"images/edit.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Make Changes to this Posted Record</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"parent.printBilling($rid);\"><img src=\"images/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Print Billing to Looseleaf</a>&nbsp;&nbsp;";
			break;
			case "Cancelled":
				list($cBy,$cOn) = getArray("select b.fullname, date_format(cancelledOn,'%m/%d/%Y %r') from billing a left join user_info b on a.cancelledBy = b.emp_id where a.recordID = '$rid';");
				echo "<span class=\"bareGray\" style=\"font-size: 11px;\">Document Cancelled By: <b>$cBy</b> On <b>$cOn</b></span>";
			break;
		}
		echo $headerControls;
	}
	
	function setNavButtons($rid) {
		list($fwd) = getArray("select recordID from billing where recordID > $rid limit 1;");
		list($prev) = getArray("select recordID from billing where recordID < $rid order by billingNo desc limit 1;");
		list($last) = getArray("select recordID from billing order by recordID desc limit 1;");
		list($first) = getArray("select recordID from billing order by recordID asc limit 1;");
		if($prev)
			$nav = $nav . "<a href=# onclick=\"parent.viewBillingDetails('$prev');\"><img src='images/resultset_previous.png'  title='Previous Record' /></a>";
		if($fwd) 
			$nav = $nav . "<a href=# onclick=\"parent.viewBillingDetails('$fwd');\"><img src='images/resultset_next.png' 'title='Next Record' /></a>";
		echo "<a href=# onclick=\"parent.viewBillingDetails('$first');\"><img src='images/resultset_first.png' title='First Record' /><a>" . $nav . "<a href=# onclick=\"parent.viewBillingDetails('$last');\"><img src='images/resultset_last.png' title='Last Record' /></a>";
	}
	if($res['status'] != "Active") { $isReadOnly = "readonly"; $isDisabled = "disabled"; }
	
?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Citylights Towers 3 & 4 Condo Corp.</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="style/tautocomplete.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="js/tautocomplete.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>

	
		function getTotal() {
			var assocDues = parseFloat(parent.stripComma($("#assoc_dues").val()));
			var waterBill = parseFloat(parent.stripComma($("#water_bill").val()));
			var stp = parseFloat(parent.stripComma($("#stp_charge").val()));
			var phase3 = parseFloat(parent.stripComma($("#phase3").val()));
			var insurance = parseFloat(parent.stripComma($("#insurance").val()));
			var parkingDues = parseFloat(parent.stripComma($("#parking_dues").val()));
			var otherCharges = parseFloat(parent.stripComma($("#other_charges").val()));
			
			var total = assocDues + waterBill + stp + phase3 + insurance + parkingDues + otherCharges;
				total = total.toFixed(2);
			
			$("#total").val(parent.addCommas(total));
		
		}
	
		function reopen(rid) {
			if(confirm("Are you sure you want to make changes to this record?") == true) {
				$.post("billing.datacontrol.php", { mod: "reopen", rid: rid, sid: Math.random() }, function() {
					parent.viewBillingDetails(rid);
				});
			}
		}
	
		function computeWaterBill(){
			var msg = "";
			
			if(isNaN($("#previous_reading").val()) == true) { msg = msg + "- Invalid Previous Reading Specified!<br/>"; }
			if(isNaN($("#current_reading").val()) == true) { msg = msg + "- Invalid Current Reading Specified!<br/>"; }
		
			if(msg != "") {
				parent.sendErrorMessage(msg);
			} else {
				var prev = parseFloat($("#previous_reading").val());
				var cur = parseFloat($("#current_reading").val());
				
				if(cur > prev) {
					$.post("billing.datacontrol.php", { mod: "computeWaterBill", prev: prev, cur: cur, sid: Math.random() }, function(data) {
						$("#water_bill").val(data['wbill']);
						$("#stp_charge").val(data['stp']);
						getTotal();
					},"json");
				}
			}
		

		
		}
	
		$(function() { 
			<?php if($res['status'] == 'Finalized' || $res['status'] == 'Cancelled') {	echo "$(\"#frmBilling :input\").prop('disabled',true);"; } ?>
			<?php if ($res['status'] == 'Active') { ?> 
				$("#billing_date").datepicker(); 
				$("#period_from").datepicker();
				$("#period_to").datepicker();
				
				var myHomeOwner = $("#homeOwner").tautocomplete({
					width: "720px",
					columns: ['Acct ID','Homeowner','Tower','Unit'],
					hide: false,
					ajax: {
						url:  "suggestHomeowner.php",
						type: "GET",
						data:function() {var x = { term: myHomeOwner.searchdata() }; return x; },
						success: function (data) {
							var filterData = [];
							var searchData = eval("/" + myHomeOwner.searchdata() + "/gi");
							$.each(data, function (i,v) {
								if (v.name.search(new RegExp(searchData)) != -1) {
									filterData.push(v);
								}
							});
							return filterData;
						}
					},
					onchange: function () {
						var cellData = myHomeOwner.all();
						//var itype = cellData['Item Code'];
						
						$("#tower").val(cellData['Tower']);
						$("#unit").val(cellData['Unit']);
						$("#acct").val(cellData['Acct ID']);
						$("#acctName").val(cellData['Homeowner']);
						
						$.post("billing.datacontrol.php", { mod: "getHOInfo", acct: cellData['Acct ID'], sid: Math.random() }, function(hDetails) {
							$("#company").val(hDetails['company']);
							$("#contact_no").val(hDetails['owner_contactno']);
							$("#assoc_dues").val(hDetails['assoc_dues']);
							$("#phase3").val(hDetails['phase3']);
							$("#insurance").val(hDetails['insurance']);
							
							getTotal();
						},"json");
						
					}
				});
			
			<?php } ?>
		});

		function saveHeader() {
			var msg = "";
			
			if($("#bill_no").val() == '') { msg = msg + "- Invalid Billing No. Please use the series no. indicated on the loose leaf were these data are to be printed.<br/>"; }
			if($("#acctName").val() == '') { msg = msg + "- Invalid Homeowner Name Specified!<br/>"; }
			if($("#acct").val() == "") { msg = msg + "- Invalid Account ID!<br/>"; }
			if($("#previous_reading").val() == "" || isNaN($("#previous_reading").val() == true)) { msg = msg + "- Invalid Previous Reading!<br/>"; }
			if($("#current_reading").val() == "" || isNaN($("#current_reading").val() == true)) { msg = msg + "- Invalid Previous Reading!<br/>"; }
			
			if(msg != '') {
				parent.sendErrorMessage(msg);
			} else {
				
				$.post("billing.datacontrol.php", { mod: "checkBillNo", billNo: $("#bill_no").val(), rid: $("#rid").val(), sid: Math.random() }, function(ret) {
					if(ret == "ok") {
						var url = $(document.frmBilling).serialize();
						url = "mod=saveBilling&"+url;
						$.post("billing.datacontrol.php", url, function(dat) {
							if(dat != '') { $("#rid").val(dat); }
							alert("Record Successfully Saved!");
						});
						
					} else {
						parent.sendErrorMessage("Billing No. has already been used previously...");
					}
					
				});
			}
		}
		
		function postBilling() {
			if(confirm("Are you sure you want to finalize & post billing to General Ledger?") == true) {
				$.post("billing.datacontrol.php", { mod: "postBilling", rid: $("#rid").val(), sid: Math.random() }, function(data) {
					alert("Billing Successfully Posted & Finalized");
					parent.viewBillingDetails($("#rid").val());
				});
			}
		}
		
		function cancelBilling() {
			if($("#rid").val() != "") {
				if(confirm("Are you sure you want to cancel this billing record?") == true) {
					$.post("billing.datacontrol.php", { mod: "cancel", rid: $("#rid").val(), sid: Math.random() }, function() { parent.viewBillingDetails($("#rid").val()); });
				}
			} else { parent.sendErrorMessage("There is nothing to cancel as this record has yet to be saved!"); }
		}
		
	</script>
	<style>
		input[type="text"]:disabled, TEXTAREA:disabled {
		  background: none; border: none;
		}
	</style>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div style="padding: 5px;">

	<form name = "frmBilling" id = "frmBilling">

	<table width=100% border=0 cellpadding=0 cellspacing=0 align=center>
		<input type="hidden" name="rid" id="rid" value="<?php echo $_REQUEST['rid']; ?>">
		<tr>
			<td valign=top >
				<table width=100% cellpadding=0 cellspacing=0 border=0 align=center>
					<tr>
						<td class="upper_menus" align=left>
							<?php setHeaderControls($res['status'],$_SESSION['userid'],$res['recordID'],$dS); ?>
						</td>
						<td align=right style='padding-right: 5px;'><?php if($_REQUEST['rid']) { setNavButtons($_REQUEST['rid']); } ?></td>
					</tr>
					<tr><td height=2></td></tr>
				</table>
				<table width=100% cellpadding="0" cellspacing="0" align=center style="border: 1px solid #436178;">
					<tr>
						<td width=100%>
							<table width=100% cellpadding=0 cellspacing=0 style="padding: 10px;">
								<tr>
									<?php if($res['status'] == 'Active' || $res['status'] == '') { ?>
									<td class="waybill-box" colspan=2>Search Acct Name Here :&nbsp;&nbsp;<input type="text" name="homeOwner" id="homeOwner" style="width:70%; border: none;" value="" /></td>
									<?php } else { ?>
									<td class="waybill-box" colspan=2>Record ID :&nbsp;&nbsp;<input type="hidden" name="homeOwner" id="homeOwner" value = "<?php echo $res['acctName']; ?>" ><input type="text" name="seqNo" id="seqNo" style="width:70%; border: none;" value="<?php echo str_pad($res['recordID'],6,0,STR_PAD_LEFT); ?>" /></td>	
									<?php } ?>
									<td  width=20% rowspan=13 class="waybill-box-right" style="background-color: #cdcdcd;" valign=top>
										<table cellpadding=0 cellspacing=0 width=100%>
											<tr>
												<td colspan=2 align=center>
													<i>Billing No.</i>&nbsp;<input type="text" name="bill_no" id="bill_no" class="nInput-huge-dark" value="<?php echo $res['billingNo']; ?>" style="width:90%" />
												</td>
											</tr>
											<tr><td colspan=2 class=gridHead>Summary of Charges</td></tr>
											<tr>
												<td class="waybill-box" width=60%>Assoc. Dues</td>
												<td class="waybill-box-right"><input type="text" name="assoc_dues" id="assoc_dues" class="nInput-borderless-dark" style="width: 90%;" value="<?php echo number_format($res['assocDues'],2); ?>" onfocus="if(this.value == '0.00') { this.value = ''; }" onblur="if(this.value == '') { this.value = '0.00'; } else { getTotal(); }" /></td>
											</tr>
											<tr>
												<td class="waybill-box" width=60%>Water Bill</td>
												<td class="waybill-box-right"><input type="text" name="water_bill" id="water_bill" class="nInput-borderless-dark" style="width: 90%;" value="<?php echo number_format($res['waterBill'],2); ?>" onfocus="if(this.value == '0.00') { this.value = ''; }" onblur="if(this.value == '') { this.value = '0.00'; } else { getTotal(); }" /></td>
											</tr>
											<tr>
												<td class="waybill-box">STP Charges</td>
												<td class="waybill-box-right"><input type="text" name="stp_charge" id="stp_charge" class="nInput-borderless-dark" style="width: 90%;" value="<?php echo number_format($res['stpCharges'],2); ?>" onfocus="if(this.value == '0.00') { this.value = ''; }" onblur="if(this.value == '') { this.value = '0.00'; } else { getTotal(); }" /></td>
											</tr>
											<tr>
												<td class="waybill-box">Phase 3</td>
												<td class="waybill-box-right"><input type="text" name="phase3" id="phase3" class="nInput-borderless-dark" style="width: 90%;" value="<?php echo number_format($res['phase3'],2); ?>" onfocus="if(this.value == '0.00') { this.value = ''; }" onblur="if(this.value == '') { this.value = '0.00'; } else { getTotal(); }" /></td>
											</tr>
											<tr>
												<td class="waybill-box">Insurance</td>
												<td class="waybill-box-right"><input type="text" name="insurance" id="insurance" class="nInput-borderless-dark" style="width: 90%;" value="<?php echo number_format($res['insurance'],2); ?>" onfocus="if(this.value == '0.00') { this.value = ''; }" onblur="if(this.value == '') { this.value = '0.00'; } else { getTotal(); }" /></td>
											</tr>
											<tr>
												<td class="waybill-box">Parking Dues</td>
												<td class="waybill-box-right"><input type="text" name="parking_dues" id="parking_dues" class="nInput-borderless-dark" style="width: 90%;" value="<?php echo number_format($res['parkingDues'],2); ?>" onfocus="if(this.value == '0.00') { this.value = ''; }" onblur="if(this.value == '') { this.value = '0.00'; } else { getTotal(); }" /></td>
											</tr>
											<tr>
												<td class="waybill-box">Penalties & Other Charges</td>
												<td class="waybill-box-right"><input type="text" name="other_charges" id="other_charges" class="nInput-borderless-dark" style="width: 90%;" value="<?php echo number_format($res['otherCharges'],2); ?>" onfocus="if(this.value == '0.00') { this.value = ''; }" onblur="if(this.value == '') { this.value = '0.00'; } else { getTotal(); }" /></td>
											</tr>
											<tr>
												<td class="waybill-box-bottom-left"><b>Total</b></td>
												<td class="waybill-box-bottom-right"><input type="text" name="total" id="total" class="nInput-borderless-dark" style="width: 90%; font-weight: bold;" value="<?php echo number_format($res['balanceDue'],2); ?>" readonly /></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td class="waybill-box" colspan=2>Homeowner/Tennant :&nbsp;&nbsp;<input type="text" name="acctName" id="acctName" style="width:70%; border: none;" value="<?php echo $res['acctName']; ?>" /></td>
								</tr>
								<tr>
									<td class="waybill-box" colspan=2>Billing Date :&nbsp;&nbsp;<input type="text" name="billing_date" id="billing_date" style="width:70%; border: none;" value="<?php if($res['billD8'] == '' || $res['billD8'] == '00/00/0000') { echo date('m/d/Y'); } else { echo $res['billD8']; } ?>" /></td>
								</tr>
								<tr>
									<td class="waybill-box" width=20%>Tower :&nbsp;&nbsp;<input type="text" name="tower" id="tower" style="width: 60%; border: none;" value="<?php echo $res['tower']; ?>" readonly /></td>
									<td class="waybill-box" width=20%>Unit :&nbsp;&nbsp;<input type="text" name="unit" id="unit" style="width: 60%; border: none;" value="<?php echo $res['unit']; ?>" readonly /></td>
								</tr>
								<tr>
									<td class="waybill-box" width=20%>Account ID :&nbsp;&nbsp;<input type="text" name="acct" id="acct" style="width: 40%; border: none;" value="<?php echo $res['acctID']; ?>" readonly /></td>
									<td class="waybill-box">Tel/Mobile # :&nbsp;&nbsp;<input type="text" name="contact_no" id="contact_no" style="width: 40%; border: none;" value="<?php echo $res['contact_no']; ?>"  /></td>
								</tr>
								<tr>
									<td class="waybill-box" colspan=2>Company :&nbsp;&nbsp;<input type="text" name="company" id="company" style="width: 70%; border: none;" value="<?php echo $res['company']; ?>" /></td>	
								</tr>
								<tr>
									<td class="waybill-box" colspan=2>Street/Brgy/Village :&nbsp;&nbsp;<input type="text" name="add1" id="add1" style="width: 70%; border: none;" value="<?php echo $res['add1']; ?>" /></td>
								</tr>
								<tr>
									<td class="waybill-box" colspan=2>City/Province :&nbsp;&nbsp;<input type="text" name="province" id="province" style="width: 70%; border: none;" value="<?php echo $res['add2']; ?>" /></td>
								</tr>
								<tr>
									<td class="waybill-box">Previous Reading :&nbsp;&nbsp;<input type="text" name="previous_reading" id="previous_reading" style="width: 80px; border: none;" value="<?php echo $res['prevReading']; ?>" onchange="javascript: computeWaterBill();" /></td>
									<td class="waybill-box">Current Reading :&nbsp;&nbsp;<input type="text" name="current_reading" id="current_reading" style="width: 80px; border: none;" value="<?php echo $res['curReading']; ?>" onchange="javascript: computeWaterBill();" /></td>
								</tr>
								<tr>
									<td class="waybill-box">Period From :&nbsp;&nbsp;<input type="text" name="period_from" id="period_from" style="width: 80px; border: none;" value="<?php if($res['pfrom'] == '' || $res['pfrom'] == '00/00/00') { echo date('m/d/Y'); } else { echo $res['pfrom']; }; ?>" onchange="javascript: computeWaterBill();" /></td>
									<td class="waybill-box">To :&nbsp;&nbsp;<input type="text" name="period_to" id="period_to" style="width: 80px; border: none;" value="<?php if($res['pto'] == '' || $res['pto'] == '00/00/00') { echo date('m/d/Y'); } else { echo $res['pto']; }; ?>" onchange="javascript: computeWaterBill();" /></td>
								</tr>
								<tr>
									<td class="waybill-box-bottom-left" colspan=2 rowspan=3 valign=top><b>Billing Remarks :</b><br/>
										<textarea type="text" name="remarks" id="remarks" style="border: none; width: 95%; resize: none;" rows=3><?php echo $res['remarks']; ?></textarea>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	
	</form>
</div>
<div class="suggestionsBox" id="suggestions" style="display: none;">
	<div class="suggestionList" id="autoSuggestionsList">&nbsp;</div>
</div>
<?php include("includes/applydiv.php"); ?>
</body>
</html>