<?php	
	session_start();
	include("handlers/_generics.php");
	$mydb = new _init;
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['doc_no']) && $_REQUEST['doc_no'] != '') { 
		$res = $mydb->getArray("select *, if(customer_code>0,lpad(customer_code,6,'0'),'') as cid, lpad(doc_no,6,0) as docno, date_format(doc_date,'%m/%d/%Y') as d8, if(cardexpiry!='0000-00-00',date_format(cardexpiry,'%m/%d/%Y'),'') as exd8, if(checkdate!='0000-00-00',date_format(checkdate,'%m/%d/%Y'),'') as ckdate, gross as grossSales, (gross-discount) as subTotal from or_header where doc_no = '$_REQUEST[doc_no]' and branch = '$_SESSION[branchid]';");
		$cSelected = "Y"; $docno = $res['docno']; $status = $res['status']; $traceNo = $res['trace_no'];
	} else {  
		$status = "Active"; $traceNo = $mydb->generateRandomString();
	}

	function setSOClickers($status,$doc_no,$uid,$dS,$urights) {
		global $mydb;
	
		switch($status) {
			case "Finalized":
				list($posted_by,$posted_on) = $mydb->getArray("select fullname as name, date_format(updated_on,'%m/%d/%Y %p') as date_posted from or_header a left join user_info b on a.updated_by = b.emp_id where a.doc_no='$doc_no';");
				if($urights == "admin") {
					$headerControls = '
						<button type = "button" name = "setActive" class="ui-button ui-widget ui-corner-all" onClick="reopen();">
							<span class="ui-icon ui-icon-unlocked"></span> Set this Document to Active Status
						</button>
					';
				}

				$headerControls .= '
					<button type = "button" name = "setPrint" class="ui-button ui-widget ui-corner-all" onClick="javascript: princtOfficialReceipt();">
						<span class="ui-icon ui-icon-print"></span> Print Collection Receipt
					</button>
				';
			break;
			case "Cancelled":
				if($urights == "admin") {
					$headerControls .= '
						<button type = "button" name = "setRecycle" class="ui-button ui-widget ui-corner-all" onClick="javascript: reuse();">
							<span class="ui-icon ui-icon-document-b"></span> Recycle this Document
						</button>
					';	
				}
			break;
			case "Active": default:

				$headerControls = '
						<button type = "button" class="ui-button ui-widget ui-corner-all" onClick="finalize();">
							<span class="ui-icon ui-icon-check"></span> Post & Finalize Collection Receipt
						</button>
						<button type = "button" class="ui-button ui-widget ui-corner-all" onClick="saveHeader();">
							<span class="ui-icon ui-icon-disk"></span> Save Changes Made (Ctrl+S)
						</button>
						<button type = "button" class="ui-button ui-widget ui-corner-all" onClick="receiveCash();">
							<img src="images/icons/collections.png" width=14 height=14 border=0 align="absmiddle"> Receive Payment (Shift+R)
						</button>
				';

				if($urights == "admin" && $dS != 1) {
					$headerControls .= '
						<button type = "button" name = "setRecycle" class="ui-button ui-widget ui-corner-all" onClick="javascript: cancel();">
							<span class="ui-icon ui-icon-cancel"></span> Cancel this Document
						</button>
					';		
				}
			break;
		}
	
		echo $headerControls;
	}
	
	function setSONavs($doc_no) {
		global $mydb;
		list($fwd) = $mydb->getArray("select doc_no from or_header where doc_no > $doc_no and branch = '$_SESSION[branchid]' limit 1;");
		list($prev) = $mydb->getArray("select doc_no from or_header where doc_no < $doc_no and branch = '$_SESSION[branchid]' order by doc_no desc limit 1;");
		list($last) = $mydb->getArray("select doc_no from or_header where branch = '$_SESSION[branchid]' order by doc_no desc limit 1;");
		list($first) = $mydb->getArray("select doc_no from or_header where branch = '$_SESSION[branchid]' order by doc_no asc limit 1;");
		if($prev)
			$nav = $nav . "<a href=# onclick=\"parent.viewSO('$prev');\"><img src='images/resultset_previous.png'  title='Previous Record' /></a>";
		if($fwd) 
			$nav = $nav . "<a href=# onclick=\"parent.viewSO('$fwd');\"><img src='images/resultset_next.png' 'title='Next Record' /></a>";
		echo "<a href=# onclick=\"parent.viewSO('$first');\"><img src='images/resultset_first.png' title='First Record' /><a>" . $nav . "<a href=# onclick=\"parent.viewSO('$last');\"><img src='images/resultset_last.png' title='Last Record' /></a>";
	}

		
?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>OMDC Prime Medical Diagnostics Corp</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/datatables/css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery.md5.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery.hotkeys.js"></script>
	<script language="javascript" src="js/or.js?sid=<?php echo uniqid(); ?>"></script>
	<script>
	
		$(document).ready(function($) {

			$('#itemDescription').autocomplete({
				source:"suggestService.php?cid="+$("#customer_code").val()+"&sid="+Math.random()+"", 
				minLength:3,
				select: function(event,ui) {
					$("#itemCode").val(ui.item.code);
					$("#itemUnit").val(decodeURIComponent(ui.item.unit));
					$("#itemCost").val(decodeURIComponent(ui.item.price));
					$("#itemSpecial").val(decodeURIComponent(ui.item.specialprice));
					$("#is_sprice").val(ui.item.sprice);
					computeItemAmount($("#itemQty").val());
				}
			});

			$('#ck_bank').autocomplete({
				source:"suggestBanks.php", 
				minLength:3
			});

			$('#patient_id').autocomplete({
				source:'suggestPatient.php', 
				minLength:3,
				select: function(event,ui) {
					$("#patient_name").val(ui.item.name);
					$("#patient_address").val(ui.item.addr);

					if(ui.item.mid_no != '') {
						if(confirm("It appears that this patient has an active PAG_IBIG MID NO. Do you wish to use this on this Official Receipt?") == true) {
							$("#mid_no").val(ui.item.mid_no);
						}
					}
				}
			});

			$('#customer_code').autocomplete({
				source:'suggestContacts.php', 
				minLength:3,
				select: function(event,ui) {
					$("#customer_name").val(decodeURIComponent(ui.item.cname));
					$("#customer_address").val(decodeURIComponent(ui.item.addr));
					$("#terms").val(ui.item.terms);
				}
			});

			$('#details').dataTable({
				"ajax": {
					"url": "or.datacontrol.php",
					"data": { trace_no: "<?php echo $traceNo; ?>", mod: "retrieve", sid: Math.random() },
					"method": "POST"	
				},
				"scrollY":  "100",
				"select":	'single',
				"pagingType": "full_numbers",
				"bProcessing": true,
				"searching": false,
				"paging": false,
				"info": false,
				
				"aoColumns": [
					{ mData: 'id' },
					{ mData: 'sono' },
					{ mData: 'sodate' },
					{ mData: 'soano' },
					{ mData: 'pname' },
					{ mData: 'procedure' },
					{ mData: 'unit_price', render: $.fn.dataTable.render.number(',', '.', 2, '') },
					{ mData: 'discount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
					{ mData: 'amount_due', render: $.fn.dataTable.render.number(',', '.', 2, '') },
					{ mData: 'code' }
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [1,2,3,6,7,8]},
					{ "targets": [0,9], "visible": false }
				]
			});
			
			<?php if($status == 'Finalized' || $status == 'Cancelled') {
				echo "$('#xform :input:not([name=setActive], [name=setPrint])').prop('disabled',true);";
			} else { ?>
				$("#so_date").datepicker(); 
				$("#loa_date").datepicker();
				$("#ck_date").datepicker();
				 $("#hmo_expiry_date").datepicker({changeMonth: true, changeYear: true, yearRange: "+00:+05"});
				$(document).bind('keydown.Ctrl_s',function (evt) { saveHeader(); return false; });
				$(document).bind('keydown.Del',function (evt) { deleteItem(); return false; });
				$(document).bind('keydown.Shift_d',function (evt) { passWordCheck(); return false; });
				$(document).bind('keydown.Shift_r',function (evt) { receiveCash(); return false; });
				$(document).bind('keydown.insert',function (evt) { addItem(); return false; });
			
			<?php } ?>
		});
		
		function princtOfficialReceipt() {
		parent.printOR($("#doc_no").val());
	}
	</script>

	<style>
		.dataTables_wrapper {
			display: inline-block;
			font-size: 11px; 
			width: 100%; 
		}
		
		table.dataTable tr.even { background-color: #f5f5f5;  }
		table.dataTable tr.odd { background-color: white; }
		.dataTables_filter input { width: 250px; }
	</style>

</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div>
	<form name="xform" id="xform">
		<input type="hidden" name="cSelected" id="cSelected" value="<?php echo $cSelected; ?>">
		<input type=hidden name="trace_no" id="trace_no" value="<?php echo $traceNo; ?>">
		<table width=100% cellpadding=0 cellspacing=0 border=0 align=center>
			<tr>
				<td class="upper_menus" align=left>
					<?php setSOClickers($status,$doc_no,$uid,$dS,$_SESSION['utype']); ?>
				</td>
				<td width=10% align=right style='padding-right: 5px;'><?php if($doc_no != '') { setSONavs($doc_no); } ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>

		<table border="0" cellpadding="0" cellspacing="1" width=100% class="td_content">
			<tr>
				<td width=60% valign=top>
					<table width=100% style="padding:0px 0px 0px 0px;">
						<tr><td height=2></td></tr>
						<tr>
							<td align="left"  width=25%  class="bareBold" style="padding-left: 35px;">Document No.&nbsp;:</td>
							<td align=left>
							<input class="gridInput" style="width:25%;" type=text name="doc_no" id="doc_no" value="<?php echo $docno; ?>" >
							</td>				
						</tr>
						<tr>
							<td align="left"  width=25%  class="bareBold" style="padding-left: 35px;">Official Receipt No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:25%;" type=text name="or_no" id="or_no" value="<?php echo $res[or_no]; ?>" >
							</td>				
						</tr>
						<tr>
							<td align="left" class="bareBold" style="padding-left: 35px;" valign=top>Charge To :</td>
							<td align=left>
								<table cellspacing=0 cellpadding=0 border=0 width=100%>
									<tr>
										<td width=25%><input type="text" id="customer_code" name="customer_code" value="<?php echo $res['cid']?>" class="inputSearch2" style="padding-left: 22px; width:98%;" placeholder = "0" onchange="javascript: checkClear(this.value);"></td>
										<td width=75% align=right colspan=2><input class="gridInput" type="text" name="customer_name" id="customer_name" autocomplete="off" value="<?php echo $res['customer_name']; ?>" style="width: 100%;" placeholder="Charge to Patient" readonly></td>
									</tr>
									<tr>
										<td style="font-size: 9px; color: gray; padding-left: 5px;">Customer ID</td><td colspan=2 style="font-size: 9px; padding-left: 5px; color: gray;">Customer Name</td>
									</tr>
									<tr>
										<td width=100% colspan=2><input class="gridInput" type="text" id="customer_address" name="customer_address" value="<?php echo $res['customer_address']?>" style="width: 100%;" readonly></td>
									</tr>
									<tr>
										<td colspan=2 style="font-size: 9px; color: gray; padding-left: 5px;" colspan=2 >Billing Address</td>
									</tr>
								</table>
							</td>				
						</tr>
						<tr>
							<td align="left" width="25%" class="bareBold" style="padding-left: 35px;">Cash Payment Type&nbsp;:</td>
							<td align=left>
								<select class="gridInput" style="width:65%;" name="cash_type" id="cash_type" >
									<?php
										$ctQuery = $mydb->dbquery("select * from options_cashtype");
										while($ctRow = $ctQuery->fetch_array()) {
											echo "<option value='$ctRow[0]' ";
											if($res['cashtype'] == $ctRow[0]) { echo "selected"; }
											echo ">$ctRow[1]</option>";
										}
									?>
								</select>
							</td>				
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<fieldset style="width: 60%;">
									<legend>&nbsp;<img src="images/icons/check_icon.png" width=20 height=20 align=absmiddle /> Check Payment Details</legend>
									<table width=100% cellpadding=0 cellspacing=1>
										<tr>
											<td width=30% class="bareBold">Bank :</td>
											<td><input type="text" name="ck_bank" id="ck_bank" value="<?php echo $res['checkbank']; ?>" style="width: 99%"></td>
										</tr>
										<tr>
											<td width=40% class="bareBold">Check No. :</td>
											<td><input type="text" name="ck_no" id="ck_no" value="<?php echo $res['checkno']; ?>" style="width: 99%"></td>
										</tr>
										<tr>
											<td width=40% class="bareBold">Check Date :</td>
											<td><input type="text" name="ck_date" id="ck_date" value="<?php echo $res['ckdate']; ?>" style="width: 99%"></td>
										</tr>
									</table>
								</fieldset>
							</td>
					</table>
				</td>
				<td valign=top>
					<table border="0" cellpadding="0" cellspacing="1" width=100%>
						<tr><td height=2></td></tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Date&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="doc_date" id="doc_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>">
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Senior Citizen/PWD ID&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="scpwd_id" id="scpwd_id" value="<?php echo $res['scpwd_id']; ?>">
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">PAG-IBIG MID No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="mid_no" id="mid_no" value="<?php echo $res['mid_no']; ?>">
							</td>				
						</tr>
						<tr>
							<td style="padding-left: 30px;" colspan=2>
								<fieldset>
									<legend>&nbsp;<img src="images/icons/credit_card.png" width=20 height=20 align=absmiddle /> Credit Card Payment Details </legend>
									<table width=100% cellpadding=0 cellspacing=1>
										<tr>
											<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Credit Card Type&nbsp;:</td>
											<td align=left>
												<select class="gridInput" style="width:70%;" type=text name="cc_type" id="cc_type">
													<option value="">- Not Applicable -</option>
													<?php
														$ctypeQuery = $mydb->dbquery("select * from options_cardtype");
														while($ctypeRow = $ctypeQuery->fetch_array()) {
															echo "<option value='$ctypeRow[0]' ";
															if($res['cardtype'] == $ctypeRow[0]) { echo "selected"; }
															echo ">$ctypeRow[1]</option>";
														}
													?>
												</select>
											</td>				
										</tr>
										<tr>
											<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Issuing Bank&nbsp;:</td>
											<td align=left>
												<input class="gridInput" style="width:70%;" type=text name="cc_bank" id="cc_bank" value="<?php echo $res['cardprovider']; ?>">
											</td>				
										</tr>
										<tr>
											<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Credit Card No.&nbsp;:</td>
											<td align=left>
												<input class="gridInput" style="width:70%;" type=text name="cc_no" id="cc_no" value="<?php echo $res['cardno']; ?>">
											</td>				
										</tr>
										<tr>
											<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Name of Credit Card&nbsp;:</td>
											<td align=left>
												<input class="gridInput" style="width:70%;" type=text name="cc_name" id="cc_name" value="<?php echo $res['cardname']; ?>">
											</td>				
										</tr>
										<tr>
											<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Expiry Date&nbsp;:</td>
											<td align=left>
												<input class="gridInput" style="width:70%;" type=text name="cc_expiry" id="cc_expiry" value="<?php echo $res['cardexpiry']; ?>" placeholder="MM/YY">
											</td>				
										</tr>
										
										<tr>
											<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Transaction Approval No.&nbsp;:</td>
											<td align=left>
												<input class="gridInput" style="width:70%;" type=text name="cc_approvalno" id="cc_approvalno" value="<?php echo $res['cardapproval']; ?>" onchange='javascript: saveHeader();'>
											</td>				
										</tr>				
									</table>
								</fieldset>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<table class="cell-border" id="details">
			<thead>
				<tr>
					<th></th>
					<th width=10%>SO #</th>
					<th width=10%>DATE</th>
					<th width=10%>SOA #</th>
					<th width=20%>PATIENT</th>
					<th >PROCEDURE</th>
					<th width=10%>PRICE</th>
					<th width=8%>DISC.</th>
					<th width=10%>AMOUNT</th>
					<th></th>
				</tr>
			</thead>
		</table>

		<table width=100% class="td_content">
			<tr>
				<td width=50% valign=top>
					Transaction Remarks: <br/>
					<textarea rows=2 type="text" id="remarks" style="width:95%;" onchange='javascript: saveHeader();'><?php echo $res['remarks']; ?></textarea><br/><br/>
					<?php if($status == 'Active' || $status == '') { ?>
						<!--a href="#" class="topClickers" onClick="javascript:addItem();"><img src="images/icons/add-2.png" width=16 height=16 border=0 align="absmiddle">&nbsp;New Entry (Ins)</a>&nbsp;-->
						<a href="#" class="topClickers" onClick="javascript:passWordCheck();"><img src="images/icons/discount-icon.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Apply Line Discount (Shift + D)</a>&nbsp;
						<a href="#" class="topClickers" onClick="javascript:deleteItem();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Selected Item (Del)</a>&nbsp;
						<a href="#" class="topClickers" onclick="javascript:browseSO();"><img src="images/icons/bill.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Browse S.O</a>&nbsp;
						<a href="#" class="topClickers" onclick="javascript:browseSOA();"><img src="images/icons/bill.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Browse SOA</a>&nbsp;
					<?php } ?>									
				</td>
				<td align=right width=50% valign=top>					
					<span class="bareBold"><b>Gross Sales (Non-VAT):</b></span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="grossSales" id="grossSales" value="<?php echo number_format($res['grossSales'],2); ?>" readonly><br/>
					<span class="bareBold">Less &raquo; Discounts :</span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="salesDiscount" id="salesDiscount" value="<?php echo number_format($res['discount'],2); ?>" readonly><br/>
					<span class="bareBold"><b>Sub-Total :</b></span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="subTotal" id="subTotal" value="<?php echo number_format($res['subTotal'],2); ?>" readonly><br/>
					<span class="bareBold">Less &raquo; EWT :</span> &nbsp;&nbsp;
						<select id="ct_atc_code" name="ct_atc_code" style="width: 100px;" class="gridInput" onchange="computeEWT(this.value);" />
							<option value="">- NA -</option>
							<option value="WC160" <?php if($res['tax_code'] == 'WC160') { echo "selected"; } ?>>WC160 (2%)</option>
						</select><input style="width:50px;text-align:right;" type=text name="ewt" id="ewt" value="<?php echo number_format($res['ewt'],2); ?>" readonly><br/>
					<span class="bareBold">Less &raquo; OPA/SC/PWD Discount :</span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="scDiscount" id="scDiscount" value="<?php echo number_format($res['sc_discount'],2); ?>" readonly><br/>
					<span class="bareBold"><b>Amount Due :</b></span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="amtDue" id="amtDue" value="<?php echo number_format($res['amount_due'],2); ?>" readonly><br/>
					<span class="bareBold">Amount Settled :</span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="amtPaid" id="amtPaid" value="<?php echo number_format($res['amount_paid'],2); ?>" readonly><br/>
					<span class="bareBold"><b>Balance Due :</b></span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="balance" id="balance" value="<?php echo number_format($res['balance'],2); ?>" readonly><br/>
				</td>
			</tr>
		</table>	
	</form>
</div>
<div id="itemEntry" style="display: none;">
	<form name="frmItemEntry" id="frmItemEntry">
		<input type="hidden" id="recordId" name="recordId">
		<table width="100%" cellspacing=2 cellpadding=0 >
			<tr>
				<td class="bareThin" align=left width=40%>Description :</td>
				<td align=left>
					<input type="text" name="itemDescription" id="itemDescription" class="inputSearch2" style="width: 80%; padding-left: 22px;">
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Item Code :</td>
				<td align=left>
					<input type="text" name="itemCode" id="itemCode" class="gridInput" style="width: 80%;" disabled>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Unit :</td>
				<td align=left>
					<input type="text" name="itemUnit" id="itemUnit" class="gridInput" style="width: 80%;" disabled>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Quantity :</td>
				<td align=left>
					<input type="text" name="itemQty" id="itemQty" class="gridInput"style="width: 80%;" value=1 onchange="javascript: computeItemAmount(this.value);">
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Unit Price :</td>
				<td align=left>
					<input type="text" name="itemCost" id="itemCost" class="gridInput" style="width: 80%;" value='0.00' disabled>
					<input type="hidden" name="is_sprice" id="is_sprice">
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Special Rate :</td>
				<td align=left>
					<input type="text" name="itemSpecial" id="itemSpecial" class="gridInput" style="width: 80%;" value='0.00' disabled>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Amount :</td>
				<td align=left>
					<input type="text" name="itemAmount" id="itemAmount" class="gridInput" style="width: 80%;" value='0.00' disabled>
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="lineDiscount" style="display: none;">
	<table width=100% cellpadding=0 cellspacing=0>
		<tr>
			<td>Discount (In Peso Value Per Unit) :</td>
			<td>
				<input type="text" name="disc" id="disc" value="0.00">
			</td>
		</tr>
	</table>
</div>
<div id="passcheck" style="display: none;">
	<table width=100% cellpadding=0 cellspacing=0>
		<tr><td height=4>&nbsp;</td></tr>
		<tr>
			<td align=center><input type="password" name="spass" id="spass" placeholder="Enter Supervisor Password" class="gridInput" style="width: 80%;"></td>
		</tr>
	</table>
</div>
<div id="cashTender" style="display: none;">
	<table width=100% cellpadding=0 cellspacing=0>
		<tr><td height=4>&nbsp;</td></tr>
		<tr>
			<td align=left><b>AMOUNT DUE:</b><br/><input type="text" name="balanceDue" id="balanceDue" value="<?php echo number_format($res['amount_due'],2); ?>" class="gridInput" style="height: 40px; font-size: 22px; font-weight: bold; text-align: right; width: 100%;" readonly></td>
		</td>
		<tr><td height=4>&nbsp;</td></tr>
		<tr>
			<td align=left><b>CASH TENDERED:</b><br/><input type="text" name="cashTendered" id="cashTendered" class="gridInput" style="height: 40px; font-size: 22px; font-weight: bold; text-align: right; width: 100%;" value="<?php echo number_format($res['cash_tendered'],2); ?>" onchange="javascript: computeChange(this.value);"></td>
		</td>
		<tr><td height=4>&nbsp;</td></tr>
		<tr>
			<td align=left><b>CHANGE DUE:</b><br/><input type="text" name="changeDue" id="changeDue" class="gridInput" style="height: 40px; font-size: 22px; font-weight: bold; text-align: right; width: 100%;" value="<?php echo number_format($res['change_due'],2); ?>" readonly></td>
		</td>				
	</table>
</div>
<div id="solist" style="display:none;"></div>
</body>
</html>