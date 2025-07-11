<?php	
	session_start();
	include("handlers/_generics.php");
	$mydb = new _init;
	
	$uid = $_SESSION['userid'];
	
	if(isset($_REQUEST['doc_no']) && $_REQUEST['doc_no'] != '') { 
		$res = $mydb->getArray("select *, date_format(doc_date,'%m/%d/%Y') as d8, if(customer_code!=0,lpad(customer_code,6,'0'),'') as cid from pharma_si_header where doc_no = '$_REQUEST[doc_no]' and branch = '$_SESSION[branchid]';");
		$cSelected = "Y"; $terms = $res['terms']; $doc_no = $res['doc_no']; $status = $res['status']; $traceNo = $res['trace_no']; $si_no = $res['si_no']; 
	} else {
		list($doc_no) = $mydb->getArray("select ifnull(max(doc_no),0)+1 from pharma_si_header where branch = '$_SESSION[branchid]';"); 
		list($si_no) = $mydb->getArray("select ifnull(max(si_no),0)+1 from pharma_si_header where branch = '$_SESSION[branchid]';");

		$status = "Active"; $terms = 0; $traceNo = $mydb->generateRandomString();
	}
	function setSOClickers($status,$doc_no,$terms,$urights) {
		global $mydb;
	
		switch($status) {
			case "Finalized":
				list($posted_by,$posted_on) = $mydb->getArray("select fullname as name, date_format(updated_on,'%m/%d/%Y %p') as date_posted from pharma_si_header a left join user_info b on a.updated_by = b.emp_id where a.doc_no = '$doc_no';");
				if($urights == "admin") {
					$headerControls = '
						<button type = "button" name = "setActive" class="ui-button ui-widget ui-corner-all" onClick="reopen();">
							<span class="ui-icon ui-icon-unlocked"></span> Set this Document to Active Status
						</button>
					';
				}

				$headerControls .= '
					<button type = "button" name = "setPrint" class="ui-button ui-widget ui-corner-all" onClick="javascript: printSI();">
						<span class="ui-icon ui-icon-print"></span> Re-Print Sales Invoice
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
							<span class="ui-icon ui-icon-check"></span> Finalize & Print Sales Order
						</button>
						<button type = "button" class="ui-button ui-widget ui-corner-all" onClick="saveHeader();">
							<span class="ui-icon ui-icon-disk"></span> Save Changes Made
						</button>
						<button type = "button" class="ui-button ui-widget ui-corner-all" onClick="browseSO();">
							<span class="ui-icon ui-icon-copy"></span> Copy From Sales Order
						</button>
						<button type = "button" class="ui-button ui-widget ui-corner-all" onClick="receiveCash();">
							<img src="images/icons/collection.png" width=12 height=12 align=absmiddle /> Settle Payment
						</button>

				';
				if($urights == "admin" && $doc_no != '') {
					$headerControls .= '
						<button type = "button" name = "setRecycle" class="ui-button ui-widget ui-corner-all" onClick="javascript: cancel();">
							<span class="ui-icon ui-icon-cancel"></span> Cancel Transaction
						</button>
					';
					
				}
			break;
		}
	
		echo $headerControls;
	}
	
	function setSONavs($doc_no) {
		global $mydb;
		list($fwd) = $mydb->getArray("select doc_no from pharma_si_header where doc_no > '$doc_no' and branch = '$_SESSION[branchid]' limit 1;");
		list($prev) = $mydb->getArray("select doc_no from pharma_si_header where doc_no < '$doc_no' order by doc_no desc limit 1;");
		list($last) = $mydb->getArray("select doc_no from pharma_si_header where branch = '$_SESSION[branchid]' order by doc_no desc limit 1;");
		list($first) = $mydb->getArray("select doc_no from pharma_si_header where branch = '$_SESSION[branchid]' order by doc_no asc limit 1;");
		if($prev)
			$nav = $nav . "<a href=# onclick=\"parent.viewPharmaSI('$prev');\"><img src='images/resultset_previous.png'  title='Previous Record' /></a>";
		if($fwd) 
			$nav = $nav . "<a href=# onclick=\"parent.viewPharmaSI('$fwd');\"><img src='images/resultset_next.png' 'title='Next Record' /></a>";
		echo "<a href=# onclick=\"parent.viewPharmaSI('$first');\"><img src='images/resultset_first.png' title='First Record' /><a>" . $nav . "<a href=# onclick=\"parent.viewPharmaSI('$last');\"><img src='images/resultset_last.png' title='Last Record' /></a>";
	}

		
?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>OMDC Prime Medical Diagnostics Corp.</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/datatables/css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
	<script language="javascript" src="js/pharmasi.js?sid=<?php echo uniqid(); ?>"></script>
	<script>

	var customerSelection = [
		"Cash Walkin Customer",
	]
	
		$(document).ready(function($) {


			$("#customer_name" ).autocomplete({
				source: customerSelection, minLength: 0
			}).focus(function() {
				$(this).data("uiAutocomplete").search($(this).val());
			});

			$('#itemDescription').autocomplete({
				source:"suggestPharmaItems.php", 
				minLength:3,
				select: function(event,ui) {
					$("#itemCode").val(ui.item.code);
					$("#itemUnit").val(ui.item.unit);
					$("#itemCost").val(ui.item.price);
					computeItemAmount($("#itemQty").val());
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
					"url": "pharma.sidatacontrol.php",
					"data": { trace_no: "<?php echo $traceNo; ?>", mod: "retrieve", sid: Math.random() },
					"method": "POST"	
				},
				"scrollY":  "180",
				"select":	'single',
				"pagingType": "full_numbers",
				"bProcessing": true,
				"searching": false,
				"paging": false,
				"info": false,
				
				"aoColumns": [
					{ mData: 'id' },
					{ mData: 'so_no' },
					{ mData: 'code' },
					{ mData: 'description' },
					{ mData: 'unit' },
					{ mData: 'qty', render: $.fn.dataTable.render.number(',', '.', 2, '')},
					{ mData: 'unit_price', render: $.fn.dataTable.render.number(',', '.', 2, '') },
					{ mData: 'amount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
					{ mData: 'discount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
					{ mData: 'amount_due', render: $.fn.dataTable.render.number(',', '.', 2, '') },
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [1,2,,4,5,6]},
					{ className: "dt-body-right", "targets": [7,8,9]},
					{ "targets": [0], "visible": false }
				]
			});
			
			<?php if($status == 'Finalized' || $status == 'Cancelled') {
				echo "$(\"#xform :input:not([name=setActive], [name=setPrint], [name=setVerify],  [name=setRecycle])\").prop('disabled',true);";
			} else { ?>
				$("#so_date").datepicker(); $("#loa_date").datepicker(); $("#hmo_expiry_date").datepicker({changeMonth: true, changeYear: true, yearRange: "+00:+05"});
			
		
			<?php } ?>
		});

		function redrawDataTable() {
			$('#details').DataTable().ajax.url("pharma.sidatacontrol.php?mod=retrieve&trace_no=<?php echo $traceNo; ?>").load();
		}

		function selectDiscount(val) {
			switch(val) {
				case "E":
					$("#discountPercent").val('5');
					$("#discountPercent").attr({readonly: true});
				break;
				case "D":
					$("#discountPercent").val('10');
					$("#discountPercent").attr({readonly: true});
				break;
				default:
					$("#discountPercent").val('');
					$("#discountPercent").attr({readonly: false});
				break
			}
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
	<form name="xform" id="xform" onsubmit="return false;">
		<input type="hidden" name="cSelected" id="cSelected" value="<?php echo $cSelected; ?>">
		<input type=hidden name="trace_no" id="trace_no" value="<?php echo $traceNo; ?>">
		<table width=100% cellpadding=0 cellspacing=0 border=0 align=center>
			<tr>
				<td class="upper_menus" align=left>
					<?php setSOClickers($status,$doc_no,$terms,$_SESSION['utype']); ?>
				</td>
				<td width=30% align=right style='padding-right: 5px;'><?php setSONavs($doc_no); ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>

		<table border="0" cellpadding="0" cellspacing="1" width=100% class="td_content">
			<tr>
				<td width=60% valign=top>
					<table width=100% style="padding:0px 0px 0px 0px;">
						<tr><td height=2></td></tr>
						<tr>
							<td align="left" width="25%" class="bareBold" style="padding-left: 35px;">Sales Invoice No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:25%;" type=text name="si_no" id="si_no" value="<?php echo STR_PAD($si_no,6,'0',STR_PAD_LEFT); ?>" >
							</td>				
						</tr>
						<tr><td height=2></td></tr>
						<tr>
							<td align="left" class="bareBold" style="padding-left: 35px;" valign=top>Customer :</td>
							<td align=left>
								<table cellspacing=0 cellpadding=0 border=0 width=100%>
									<tr>
										<td width=25%><input type="text" id="customer_code" name="customer_code" value="<?php echo $res['cid']?>" class="inputSearch2" style="padding-left: 22px; width:98%;" placeholder = "0" onchange="javascript: checkClear(this.value);"></td>
										<td width=75% align=right colspan=2><input class="gridInput" type="text" name="customer_name" id="customer_name" autocomplete="off" value="<?php echo $res['customer_name']; ?>" style="width: 100%;"></td>
									</tr>
									<tr>
										<td style="font-size: 9px; color: gray; padding-left: 5px;">Customer ID</td><td colspan=2 style="font-size: 9px; padding-left: 5px; color: gray;">Customer Name</td>
									</tr>
									<tr>
										<td width=100% colspan=2><input class="gridInput" type="text" id="customer_address" name="customer_address" value="<?php echo $res['customer_address']?>" style="width: 100%;"></td>
									</tr>
									<tr>
										<td colspan=2 style="font-size: 9px; color: gray; padding-left: 5px;" colspan=2 >Billing Address</td>
									</tr>
								</table>
							</td>				
						</tr>
						<tr>
							<td align="left" width="25%" class="bareBold" style="padding-left: 35px;">Payment Terms&nbsp;:</td>
							<td align=left>
								<select class="gridInput" style="width:50%;" name="terms" id="terms" >
									<?php
										$srQuery = $mydb->dbquery("select terms_id, description from options_terms");
										while($srRow = $srQuery->fetch_array()) {
											echo "<option value='$srRow[0]' ";
											if($res['terms'] == $srRow[0]) { echo "selected"; }
											echo ">$srRow[1]</option>";
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
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Document No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="doc_no" id="doc_no" value="<?php echo STR_PAD($doc_no,6,'0',STR_PAD_LEFT); ?>" readonly >
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Transaction Date&nbsp;:</td>
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
					</table>
				</td>
			</tr>
		</table>

		<table id="details">
			<thead>
				<tr>
					<th></th>
					<th width=10%>SO #</th>
					<th width=10%>SKU</th>
					<th >DESCRIPTION</th>
					<th width=8%>UNIT</th>
					<th width=8%>QTY</th>
					<th width=10%>PRICE</th>
					<th width=10%>AMOUNT</th>
					<th width=8%>DISC.</th>
					<th width=10%>DUE</th>
				</tr>
			</thead>
		</table>

		<table width=100% class="td_content">
			<tr>
				<td width=50%>
					Transaction Remarks: <br/>
					<textarea rows=2 type="text" id="remarks" style="width:83%;" onchange='javascript: saveSOHeader();'><?php echo $res['remarks']; ?></textarea><br/><br/>
					<?php if($status == 'Active' || $status == '') { ?>
						<a href="#" class="topClickers" onClick="javascript:addItem();"><img src="images/icons/add-2.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Add Item</a>&nbsp;
						<a href="#" class="topClickers" onClick="javascript:applyDiscount();"><img src="images/icons/discount-icon.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Apply Line Discount</a>&nbsp;
						<a href="#" class="topClickers" onClick="javascript:deleteItem();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Selected Item</a>
					<?php } ?>
				</td>
				<td align=right width=50% valign=top>
					
					<span class="bareBold"><b>Gross Sales (Vatable):</b></span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="grossSales" id="grossSales" value="<?php echo number_format($res['gross'],2); ?>" readonly><br/>
					<span class="bareBold">Less &raquo; Discounts :</span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="salesDiscount" id="salesDiscount" value="<?php echo number_format($res['discount'],2); ?>" readonly><br/>
					<span class="bareBold"><b>Sub-Total :</b></span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="subTotal" id="subTotal" value="<?php echo number_format($res['net'],2); ?>" readonly><br/>
					<span class="bareBold"><b>Amount Due :</b></span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="amtDue" id="amtDue" value="<?php echo number_format($res['amount_due'],2); ?>" readonly><br/>
					<span class="bareBold">Amount Settled :</span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="amtPaid" id="amtPaid" value="<?php echo number_format($res['amount_paid'],2); ?>" readonly><br/>
					<span class="bareBold"><b>Balance Due :</b></span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="balance" id="balance" value="<?php echo number_format($res['balance'],2); ?>" readonly><br/>

				</td>
			</tr>
			<tr>
				<td align=left colspan=2 style="padding-top: 15px;">
					
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
				<td class="bareThin" align=left width=40%>Amount :</td>
				<td align=left>
					<input type="text" name="itemAmount" id="itemAmount" class="gridInput" style="width: 80%;" value='0.00' disabled>
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="discounter" style="display: none;">
	<table width="100%" cellpadding=2 cellspacing=2>
		<tr>
			<td class="spandix-l" width=35%>Discount Type: </td>
			<td>
				<select name="discountType" id="discountType" class="gridInput" style="width: 80%;" onchange="javascript: selectDiscount(this.value);">
					<option value=''>- Select Discount Type -</option>
					<option value='E'>Employee's Discount</option>
					<option value='D'>Doctor's Discount</option>
					<option value='O'>Other Discounts</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Discount Percentage:</td>
			<td>
				<input type="text" name="discountPercent" id="discountPercent" class="gridInput" style="width: 80%; font-size: 11px;" pattern="^\d*(\.\d{0,2})?$" value="0" readonly>
			</td>
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
<div id="solist" style="display: none;"></div>
</body>
</html>