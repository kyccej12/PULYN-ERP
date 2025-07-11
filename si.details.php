<?php	
	/* UNSET QUED FOR DELETION */
	session_start();
	unset($_SESSION['ques']);
	include("functions/si.displayDetails.fnc.php");
	include("includes/dbUSE.php");
	list($urights) = getArray("select user_type from user_info where emp_id='$_SESSION[userid]';");
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['docno']) && $_REQUEST['docno'] != '') { 
		$docno = $_REQUEST['docno']; 
		$res = getArray("select *, lpad(customer,6,0) as sup_code, date_format(invoice_date,'%m/%d/%Y') as d8, if(posting_date='0000-00-00','',date_format(posting_date,'%m/%d/%Y')) as pd8 from invoice_header where doc_no='$docno' and branch = '$_SESSION[branchid]';");
		if($res[terms]==0){ $res[applied_amount]=0; } 
		$cSelected = "Y"; $trace_no = $res['trace_no']; $status = $res['status']; $lock = $res['lock'];
	} else {  
		$trace_no = genTraceno();
		$status = "Active"; $dS = "1"; $cSelected = "N"; $lock = "N";
	}
	
	function genTraceno(){
		$flag = true;
		while($flag){
			list($trace_no) = getArray("SELECT LEFT(MD5(RAND()),32) trace_no;");
			list($traceCount) = getArray("SELECT COUNT(trace_no) FROM invoice_header a WHERE a.trace_no = '$trace_no';");
			if($traceCount>0){
				$flag = true;
			}else{
				$flag = false;
			}
		}
		return $trace_no;
	}
	
	function setHeaderControls($status,$lock,$docno,$uid,$dS,$urights) {
		
		if($lock != 'Y') {
			switch($status) {
				case "Finalized":
					if($urights == "admin") {
						$headerControls = "<a href=\"#\" class=\"topClickers\" onclick=\"javascript: reopenSI('$docno');\"><img src='images/icons/edit.png' align=absmiddle width=16 height=16 />&nbsp;Set this Document to Active Status</a>&nbsp;";
					}
					$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onClick=\"javascript:printSI('N');\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Print Sales Invoice</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onClick=\"javascript:printPackingList();\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Print Packing List (D.R)</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onClick=\"javascript:viewDoc();\"><img src=\"images/icons/docinfo.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;View Doc. Info</a>";
				break;
				case "Cancelled":
					if($urights == "admin") {
						$headerControls = $headerControls . "<a href=\"#\" class=\"topClickers\" onclick=\"javascript:reuseSI();\" style=\"padding: 5px;\"><img src=\"images/icons/refresh.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Recycle this Document</a>";	
					}
				break;
				case "Active": default:
					$headerControls = "<a href=\"#\" class=\"topClickers\" onClick=\"javascript:finalizeSI();\"><img src=\"images/icons/ok.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Checkout & Finalize Transaction</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:saveInvHeader();\"><img src=\"images/save.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Save Changes</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:downloadPO();\"><img src=\"images/icons/copy.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Copy from Customer's Outstanding S.O</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:downloadPayRef();\"><img src=\"images/icons/copy.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Copy from Sales Order Ref. No.</a>&nbsp;";
					if($urights == "admin" && $dS != 1) {
						$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:cancelSI();\"><img src=\"images/icons/cancel.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Cancel this Document</a>";
					}
				break;
			} 
		} else { 
			$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\"  onClick=\"javascript:printSI('Y');\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Re-Print Sales Invoice</a>";
		}
		echo $headerControls;
	}
	
	function setNavButtons($docno) {
		list($fwd) = getArray("select doc_no from invoice_header where doc_no > $docno and branch = '$_SESSION[branchid]' limit 1;");
		list($prev) = getArray("select doc_no from invoice_header where doc_no < $docno and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]' order by doc_no desc limit 1;");
		list($last) = getArray("select doc_no from invoice_header where branch = '$_SESSION[branchid]' order by doc_no desc limit 1;");
		list($first) = getArray("select doc_no from invoice_header where branch = '$_SESSION[branchid]' order by doc_no asc limit 1;");
		if($prev)
			$nav = $nav . "<a href=# onclick=\"parent.viewSI('$prev');\"><img src='images/resultset_previous.png'  title='Previous Record' /></a>";
		if($fwd) 
			$nav = $nav . "<a href=# onclick=\"parent.viewSI('$fwd');\"><img src='images/resultset_next.png' 'title='Next Record' /></a>";
		echo "<a href=# onclick=\"parent.viewSI('$first');\"><img src='images/resultset_first.png' title='First Record' /><a>" . $nav . "<a href=# onclick=\"parent.viewSI('$last');\"><img src='images/resultset_last.png' title='Last Record' /></a>";
	}
	
?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/tautocomplete.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="js/jquery.dialogextend.js"></script>
	<script language="javascript" src="js/tautocomplete.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script language="javascript" src="js/si.js"></script>
	<script>
		
		var SLid;
		
		function selectSLid(obj,lid) {
			gObj = obj;
			$(obj).closest("tr").siblings().removeClass("diffcolor");
			$(obj).toggleClass("diffcolor");
			SLid = lid;
		}
	
		$(document).ready(function($) {
			<?php if($status == 'Finalized' || $status == 'Cancelled') {	echo "$(\"#xform :input\").prop('disabled',true);"; } ?>
			$("#posting_date").datepicker(); $("#ref_date").datepicker(); $("#cheq_date").datepicker(); $("#invoice_date").datepicker(); 
			$('#qty').bind('keypress', function(e) { if(e.keyCode ==13){ addDetails(); } });
			$('#amount').bind('keypress', function(e) { if(e.keyCode ==13){ addDetails(); } });
			
			$('#customer_id').autocomplete({
				source:'suggestContacts.php', 
				minLength:3,
				select: function(event,ui) {
					$("#cSelected").val('Y');
					$("#customer_id").val(ui.item.cid);
					$("#customer_name").val(decodeURIComponent(ui.item.cname));
					$("#cust_address").val(decodeURIComponent(ui.item.addr));
					$("#terms").val(ui.item.terms);
					//saveInvHeader();
				}
			});
			
			var myProduct = $("#description").tautocomplete({
				width: "720px",
				columns: ['QTY ON-HAND','Item Code','Description','Unit','Unit Price'],
				hide: false,
				ajax: {
					url:  "suggestItems-2.php",
					type: "GET",
					data:function() {var x = { term: myProduct.searchdata(), customer : $("#customer_id").val() }; return x; },
					success: function (data) {
						var filterData = [];
						var searchData = eval("/" + myProduct.searchdata() + "/gi");
						$.each(data, function (i,v) {
							if (v.description.search(new RegExp(searchData)) != -1) {
								filterData.push(v);
							}
						});
						return filterData;
					}
				},
				onchange: function () {
					var cellData = myProduct.all();
					$("#product_code").val(cellData['Item Code']);
					$("#description").val(cellData['Description']);
					$("#unit").val(cellData['Unit']);
					$("#unit_price").val(cellData['Unit Price']);
					$("#qty").focus();
				}
			  });
		
			
		});
		
		function deleteLine() {
			if(SLid == "") {
				parent.sendErrorMessage("- You have not selected any record that you wish to remove.");
			} else {
				if(confirm("Are you sure you want to remove this entry?") == true) {
					$.post("si.datacontrol.php", { mod: "deleteLine", lid: SLid, trace_no: $("#trace_no").val(), sid: Math.random() }, function(ret) {
						$("#details").html(ret); SLid = ""; getTotals();
					},"html");
				}
			}
		}

		function clearItems() {
			if(confirm("Are you sure you want to clear all item entries loaded into this Invoice?") == true) {
				$.post("si.datacontrol.php", { mod: "clearItems", trace_no: $("#trace_no").val(), sid: Math.random() }, function(ret) {
					$("#details").html(ret); getTotals();
				},"html");
			}
		}		
	</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div style="padding: 10px;">
	<form name="xform" id="xform">
		<input type=hidden id="trace_no" value="<?php echo $trace_no; ?>">
		<input type="hidden" name="cSelected" id="cSelected" value="<?php echo $cSelected; ?>">
		<input type=hidden name="prev_invoice_date" id="prev_invoice_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>">
		<table width=100% cellpadding=0 cellspacing=0 border=0 align=center>
			<tr>
				<td class="upper_menus" align=left id="uppermenus">
					<?php setHeaderControls($res['status'],$res['locked'],$docno,$_SESSION['userid'],$dS,$urights); ?>
				</td>
				<td width=30% align=right style='padding-right: 5px;'><?php if($docno) { setNavButtons($docno); } ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
		<table width=100% cellpadding="0" cellspacing="0" align=center class="tableRounder">
			<tr>
				<td align=left class="gridHead-left"></td>
				<td align=right class="gridHead-right"></td>
			</tr>
			<tr>
				<td width="100%" colspan=2>
					<table border="0" cellpadding="0" cellspacing="1" width=100%>
						<tr>
							<td width=50% valign=top>
								<table width=100% style="padding:0px 0px 0px 0px;">
									<tr><td height=2></td></tr>
									<tr>
										<td class="bareBold" align=right valign=top width=25% style="padding-right: 5px;">Customer&nbsp;:</td>
										<td align="left">
											<table cellspacing=0 cellpadding=0 border=0 width=100%>
												<tr>
													<td width=25%><input type="text" id="customer_id" name="customer_id" value="<?php echo $res['sup_code']?>" class="inputSearch2" style="padding-left: 22px;"></td>
													<td width=75% align=right colspan=2><input type="text" name="customer_name" id="customer_name" autocomplete="off" value="<?php echo $res['customer_name']; ?>" style="width: 100%;" readonly></td>
												</tr>
												<tr>
													<td style="font-size: 9px; padding-left: 5px;">Code</td><td colspan=2 style="font-size: 9px; padding-left: 20px;">Customer Name</td>
												</tr>
												<tr><td height=2></td></tr>
												<tr>
													<td width=100% colspan=2><input type="text" class="nInput" id="cust_address" name="cust_address" value="<?php echo $res['customer_addr']?>" style="width: 100%;" readonly></td>
												</tr>
												<tr>
													<td colspan=2 style="font-size: 9px; padding-left: 5px;" colspan=2 >Address</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td class="bareBold" align=right width=25% style="padding-right: 5px;">Credit Term&nbsp;:</td>
										<td align="left">
											<select id="terms" name="terms" style="width: 150px;" <?php if($urights!='admin') { echo "disabled"; } ?> />
												<?php
													$tq = dbquery("select terms_id, description from options_terms order by terms_id;");
													while(list($tid,$td) = mysql_fetch_array($tq)) {
														echo "<option value='$tid' ";
														if($res['terms'] == $tid) { echo "selected"; }
														echo ">$td</option>";
													}
												?>
											</select>
										</td>
									</tr>
								</table>
							</td>
							<td valign=top>
								<table border="0" cellpadding="0" cellspacing="1" width=100%>
									<tr><td height=2></td></tr>
									<tr>
										<td align="left" width="40%" class="bareBold" style="padding-left: 20%;">Doc No.&nbsp;
											<select id="docno_type" name="docno_type" style="width: 70px;" />
												<option value="sec" <?php if($res['invoice_type'] == 'sec') { echo "selected"; } ?>>Secondary</option>		
												<option value="pri" <?php if($res['invoice_type'] == 'pri') { echo "selected"; } ?>>Primary</option>
											</select> :</td>
										<td align=left>
											<input style="width:40%;" type=text name="doc_no" id="doc_no" value="<?php echo $docno; ?>" readonly>
										</td>				
									</tr>
									<tr>
										<td align="left" width="40%" class="bareBold" style="padding-left: 20%;">S.I No.&nbsp;:</td>
										<td align=left>
											<input style="width:40%;" type=text name="invoice_no" id="invoice_no" value="<?php echo $res['invoice_no']; ?>">
										</td>				
									</tr>
									<tr>
										<td align="left" width="40%" class="bareBold" style="padding-left: 20%;">Document Date&nbsp;:</td>
										<td align=left>
											<input style="width:40%;" type=text name="invoice_date" id="invoice_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>" >
										</td>				
									</tr>
									<tr>
										<td align="left" width="40%" class="bareBold" style="padding-left: 20%;">Posting Date&nbsp;:</td>
										<td align=left>
											<input style="width:40%;" type=text name="posting_date" id="posting_date" value="<?php if(!$res['pd8']) { echo date('m/d/Y'); } else { echo $res['pd8']; }?>" onChange = "javascript: checkLockDate(this.id,this.value,$('#prev_invoice_date').val());" >
										</td>				
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table><tr><td height=2></td></tr></table>
		<table cellspacing=0 cellpadding=0 border=0 width=100%>
			<tr>
				<td align=center class="ui-state-default" style="padding: 5px;" width="6%">SO #</td>
				<td align=center class="ui-state-default" style="padding: 5px;" width="8%">S.O DATE</td>
				<td align=left class="ui-state-default" style="padding: 5px 5px 5px 25px;" width="10%">ITEM CODE</td>
				<td align=left class="ui-state-default" style="padding: 5px;" width="21%">DESCRIPTION</td>
				<td align=center class="ui-state-default" style="padding: 5px;" width="5%">UNIT</td>
				<td align=center class="ui-state-default" style="padding: 5px;" width="6%">QTY</td>
				<td align=center class="ui-state-default" style="padding: 5px;" width="8%">UNIT PRICE</td>
				<td align=center class="ui-state-default" style="padding: 5px;" width="8%">DISC</td>
				<td align=center class="ui-state-default" style="padding: 5px;" width="8%">DISC. PRICE</td>
				<td align=center class="ui-state-default" style="padding: 5px;" width="8%">COMMISSION</td>
				<td align=center class="ui-state-default" style="padding: 5px;" width="10%">AMOUNT</td>
			</tr>
			<?php
				if($status == "Active" || $status == "") {
					/*
					echo '<tr>
							<td align=center class="grid"><input class="gridInput" type=text id="so_no" style="width: 98%; text-align: right;" /></td>
							<td align=center class="grid"><input class="gridInput" type=text id="so_date" style="width: 98%; text-align: right;" /></td>
							<td align=center class="grid" colspan=2><input type="hidden" id="product_code" /><input type=text class="inputSearch" style="padding-left: 22px; width: 98%" id="description" /></td>
							<td align=center class="grid"><input class="gridInput" type=text id="unit" style="width: 98%; text-align: center;" readonly /></td>
							<td align=center class="grid"><input class="gridInput" type=text id="qty" style="width: 98%; text-align: right;" onblur="computeAmount();" /></td>
							<td align=center class="grid"><input class="gridInput" type=text id="unit_price"style="width: 98%; text-align: right;" onchange="computeAmount();"/></td>
							<td align=center class="grid"><input class="gridInput" type=text id="discount" style="width: 98%;text-align: right;" readonly/></td>
							<td align=center class="grid"><input class="gridInput" type=text id="linegross" style="width: 98%; text-align: right;" readonly/></td>
							<td align=center class="grid"><input class="gridInput" type=text id="icomm" style="width: 98%; text-align: right;" readonly/></td>
							<td align=center class="grid"><input class="gridInput" type=text id="amount" style="width: 80%;text-align: right;" readonly/>&nbsp;&nbsp;<a href="#" onclick="javascript: addDetails();" title="Add Item"><img src="images/icons/add-2.png" width=18 height=18 style="vertical-align: middle;" /></a></td>
						</tr>';
					$i++;
					*/
					
				}
			?>
		</table>
		<div id="details" style="height: 130px; overflow-x: auto; border-bottom: 3px solid #4297d7;">
			<?php IDETAILS($trace_no,$status,$lock) ?>
		</div>
		<table width=100%>
			<tr>
				<td width=50%>
					Sales Representative:<br/>
					<select id="sales_rep" name="sales_rep" style="width: 200px;" />
						<option value="0" <?php if($res['sales_rep'] == 0) { echo "selected"; } ?>>- None -</option>
						<?php
							$sr = dbquery("select record_id,sales_rep from options_salesrep order by sales_rep;");
							while($srx = mysql_fetch_array($sr)) {
								echo "<option value='$srx[0]' ";
								if($res['sales_rep'] == $srx[0]) { echo "selected"; }
								echo ">$srx[1]</option>";

							}
						?>
					</select>
				<br/><br/>
					Transaction Remarks: <br/>
					<textarea type="text" id="remarks" style="width:83%;"><?php echo $res['remarks']; ?></textarea>
				</td>
				<td align=right width=50%>
					<table border="0" cellpadding="0" cellspacing="1" width=100%>
						<tr><td height=2></td></tr>
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 40%;">Total Before Discount & Commission&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="amount_b4_discount" id="amount_b4_discount" value="<?php echo number_format(($res['amount']+$res['discount']+$res['commission']),2); ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 40%;">Less: Discount&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="discount_in_peso" id="discount_in_peso" value="<?php echo number_format($res['discount'],2); ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 40%;">Less: Commission&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="commission" id="commission" value="<?php echo number_format($res['commission'],2); ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 40%;">Amount Due&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="total_due" id="total_due" value="<?php echo number_format($res['amount'],2); ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 40%;">Amount Applied&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="amount_applied" id="amount_applied" value="<?php echo number_format($res['applied_amount'],2); ?>">
							</td>				
						</tr>
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 40%;">Balance Due&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="balance_due" id="balance_due" value="<?php echo number_format($res['balance'],2); ?>" readonly>
							</td>				
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align=left colspan=2 style="padding-top: 15px;">
					<?php if($status == 'Active') { ?>
						<a href="#" class="topClickers" onClick="javascript:deleteLine();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Line Entries</a>&nbsp;&nbsp;<a href="#" class="topClickers" onClick="javascript:applyDiscount();"><img src="images/icons/discount-icon.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Apply Discount</a>&nbsp;&nbsp;<a href="#" class="topClickers" onClick="javascript:clearItems();"><img src="images/icons/bin.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Clear Item Details</a>
					<?php } ?>
				</td>
			</tr>
		</table>
	</form>
</div>
<div class="suggestionsBox" id="suggestions" style="display: none;">
	<div class="suggestionList" id="autoSuggestionsList">&nbsp;</div>
</div>
<div id="invoiceAttachment" style="display: none;"></div>
<div id="loading_popout" style="display:none;" align=center>
	<progress id='progess_trick' value='40' max ='100' width='220px'></progress> <br>
	Please wait while the server is processing our request.
</div>
<div id="loaderMessage" title="Processing..." style="display: none;">
	<p><span style="float:left; margin:0 7px 20px 0;"><img src="images/loader.gif" /></span>Please wait while the system is processing your request...</p>
</div>
<div id="discountDiv" style="display: none;" align=center>
	<table border=0>
		<tr>
			<td>Discount : </td>
			<td>
				<input type='text' class="nInput" id="salesDiscount" style="font-family:Arial; width: 140px;" > / UoM
			</td>
		</tr>
		<tr><td></td>
			<td>
				<input type="radio" name="type" id="type" value="PCT" checked>&nbsp;<span class="baregray" style="font-size: 10px;">Percent</span>&nbsp;
				<input type="radio" name="type" id="type" value="AMT">&nbsp;<span class="baregray" style="font-size: 10px;">Peso Value</span>&nbsp;
			</td>
		</tr>
	</table>
</div>
 <div id="payment_mode" style="display: none;">
	<table width=100% class="td_content" cellpadding=0 cellspacing=1>
		<tr>
			<td style="padding: 20px;" align=center>
				<button style="width:140px; height: 80px; font-size: 11px; padding: 5px;" onclick="javascript: cashCheckOut('<?php echo $trace_no; ?>');"><img src="images/icons/price-icon.png" width=48 height=48/><br/>Cash Payment</button>
				<button style="width:140px; height: 80px; font-size: 11px; padding: 5px;" onclick="javascript: ccCheckOut('<?php echo $trace_no; ?>');"><img src="images/icons/credit_card.png" width=48 height=48/><br/>Credit Card</button>
				<button style="width:140px; height: 80px; font-size: 11px; padding: 5px;" onclick="javascript: cheqCheckOut('<?php echo $trace_no; ?>');"><img src="images/icons/account_info.png" width=48 height=48 /><br/>Check Payment</button>
			</td>
		</tr>
	</table>
</div>
  <div id="cashCheckOutForm" style="display: none;">
	<table width=80% align=center class="td_content">
		<tr><td height=16></td></tr>
		<tr><td class="spandix-l">Amount Due<br/>
				<input class="nInput4" type=text id="amountDue" name="amountDue" style="width:100%; text-align: center; height: 80px; font-size: 60px;" value="<?php echo number_format($amtGT,2); ?>" readOnly>
			</td>
		</tr>
		<tr><td height=2></td></tr>
		<tr><td class="spandix-l">Amount Tendered<br/>
				<input class="nInput4" type=text id="amountTendered" name="amountTendered" style="width:100%; text-align: center; height: 80px; font-size: 60px;" onChange="computeChange(this.value);">
			</td>
		</tr>
		<tr><td height=2></td></tr>
		<tr><td valign=top class="spandix-l">Change Due<br/>
				<input class="nInput4" type=text id="changeDue" name="changeDue" value="0.00" style="width:100%; text-align: center; height: 80px; font-size: 60px;" readonly >
			</td>
		</tr>
	</table>
 </div>
  <div id="cardCheckOutForm" style="display: none;">
	<table width=80% align=center class="td_content">
		<tr><td height=16></td></tr>
		<tr><td class="spandix-l">CARD TYPE<br/>
				<select class="nInput4" name="cc_type" id="cc_type" style="width: 100%;">
					<option value="MASTERCARD">MASTERCARD</option>
					<option value="VISA">VISA</option>
					<option value="AMEX">AMERICAN EXPRESS</option>
					<option value="JCB">JCB</option>
				</select>
			</td>
		</tr>
		<tr><td height=2></td></tr>
		<tr><td class="spandix-l">ISSUING BANK<br/>
				<select class="nInput4" name="cc_bank" id="cc_bank" style="width: 100%;">
					<option value="BDO">BANCO DE ORO</option>
					<option value="MBTC">METROBANK</option>
					<option value="EW">EASTWEST BANK</option>
					<option value="CHINA">CHINABANK</option>
					<option value="SC">STANDARD CHARTER</option>
					<option value="CTB">CITI BANK</option>
					<option value="HSBC">HSBC</option>
					<option value="UB">UNION BANK</option>
					<option value="RCBC">RCBC</option>
					<option value="PNB">PNB</option>
				</select>
			</td>
		</tr>
		<tr><td height=2></td></tr>
		<tr><td class="spandix-l">CARD HOLDER NAME<br/>
				<input class="nInput4" type=text id="cc_name" name="cc_name" style="width:100%; text-align: center;">
			</td>
		</tr>
		<tr><td height=2></td></tr>
		<tr><td class="spandix-l">CREDIT CARD NO.<br/>
				<input class="nInput4" type=text id="cc_no" name="cc_no" style="width:100%; text-align: center;">
			</td>
		</tr>
		<tr><td height=2></td></tr>
		<tr><td class="spandix-l">CARD EXPIRY (MM/YY)<br/>
				<input class="nInput4" type=text id="cc_expiry" name="cc_expiry" style="width:100%; text-align: center;">
			</td>
		</tr>
		<tr><td height=2></td></tr>
		<tr><td valign=top class="spandix-l">TRANSACTION APPROVAL NO.<br/>
				<input class="nInput4" type=text id="cc_approvalno" name="cc_approvalno" style="width:100%; text-align: center;">
			</td>
		</tr>
		<tr><td height=2></td></tr>
	</table>
 </div>
  <div id="cheqCheckOutForm" style="display: none;">
	<table width=80% align=center class="td_content">
		<tr><td height=8></td></tr>
		
		<tr><td height=2></td></tr>
		<tr><td class="spandix-l">ISSUING BANK<br/>
				<select class="nInput4" name="cheq_bank" id="cheq_bank" style="width: 100%;">
					<?php
						$cb = dbquery("select bank_code, bank_name from options_banks order by bank_name;");
						while($x = mysql_fetch_array($cb)) {
							echo "<option value='$x[bank_code]'>$x[bank_name]</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr><td height=2></td></tr>
		<tr><td class="spandix-l">Check No.<br/>
				<input class="nInput4" type=text id="cheq_no" name="cheq_no" style="width:100%; text-align: center;">
			</td>
		</tr>
		<tr><td height=2></td></tr>
		<tr><td class="spandix-l">Check Date<br/>
				<input class="nInput4" type=text id="cheq_date" name="cheq_date" style="width:100%; text-align: center;">
			</td>
		</tr>

		<tr><td height=2></td></tr>
	</table>
 </div>
<div id="docinfo" style="display: none;">
	<table>
		<tr> <td height=5> </td> </tr>
		<tr> 
			<td> Created By: </td>
			<td> <?php echo $docinfo[creator]; ?> <br>  <?php echo $docinfo[created_on]; ?> </td>
		</tr>
		<tr> <td height=5> </td> </tr>
		<tr> 
			<td> Updated By: </td>
			<td> <?php echo $docinfo[updater]; ?> <br>  <?php echo $docinfo[updated_on]; ?> </td>
		</tr>
		<tr> <td height=5> </td> </tr>
		<tr> 
			<td> Doc. Status </td>
			<td> <?php echo $res[status]; ?> </td>
		</tr>
	</table>
</div>
<div id="payref" style="display: none;">
	<table width=100% cellpadding=0 cellspacing=0>
		<tr> 
			<td width=40%>Sales Order No. : </td>
			<td><input type="text" name="so_cno" id="so_cno" style="width: 90%;"></td>
		</tr>
	</table>
</div>
<div id="SOinfo" style="display: none;">
	<table width=100% cellpadding=0 cellspacing=0>
		<tr> 
			<td width=40%>Customer : </td>
			<td><input type="text" name="so_cname" id="so_cname" style="width: 90%;" readonly></td>
		</tr>
		<tr> 
			<td width=40%>SO Date : </td>
			<td><input type="text" name="so_cdate" id="so_cdate" style="width: 90%;" readonly></td>
		</tr>
		<tr> 
			<td width=40%>Amount : </td>
			<td><input type="text" name="so_camt" id="so_camt" style="width: 90%;" readonly></td>
		</tr>
		<tr> 
			<td width=40%>Sales Rep : </td>
			<td><input type="text" name="so_srep" id="so_srep" style="width: 90%;" readonly></td>
		</tr>
	</table>
</div>
</body>
</html>