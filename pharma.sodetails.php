<?php	
	session_start();
	include("handlers/_generics.php");
	$mydb = new _init;
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['so_no']) && $_REQUEST['so_no'] != '') { 
		$res = $mydb->getArray("select *, lpad(so_no,6,0) as sono, date_format(so_date,'%m/%d/%Y') as d8, if(customer_code!=0,lpad(customer_code,6,'0'),'') as cid from pharma_so_header where so_no = '$_REQUEST[so_no]';");
		$cSelected = "Y"; $terms = $res['terms']; $so_no = $res['sono']; $status = $res['status']; $traceNo = $res['trace_no'];
	} else {
		list($so_no) = $mydb->getArray("select ifnull(max(so_no),0)+1 from pharma_so_header where branch = '$_SESSION[branchid]';"); 
		$status = "Active"; $terms = 0; $traceNo = $mydb->generateRandomString();
	}

	function setSOClickers($status,$so_no,$terms,$urights) {
		global $mydb;
	
		switch($status) {
			case "Finalized":
				list($posted_by,$posted_on) = $mydb->getArray("select fullname as name, date_format(updated_on,'%m/%d/%Y %p') as date_posted from pharma_so_header a left join user_info b on a.updated_by = b.emp_id where a.so_no = '$so_no';");
				if($urights == "admin") {
					$headerControls = '
						<button type = "button" name = "setActive" class="ui-button ui-widget ui-corner-all" onClick="reopen();">
							<span class="ui-icon ui-icon-unlocked"></span> Set this Document to Active Status
						</button>
					';
				}

				$headerControls .= '
					<button type = "button" name = "setPrint" class="ui-button ui-widget ui-corner-all" onClick="javascript: printSO();">
						<span class="ui-icon ui-icon-print"></span> Re-Print Sales Order
					</button>
					<button type = "button" name = "setPrint" class="ui-button ui-widget ui-corner-all" onClick="javascript: printSOLetter();">
						<span class="ui-icon ui-icon-print"></span> Re-Print Sales Order (Letter Size)
					</button>
					<button type = "button" name = "setPrint" class="ui-button ui-widget ui-corner-all" onClick="javascript: printCSI();">
						<span class="ui-icon ui-icon-print"></span> Re-Print Sales Charge Invoice
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
						<button type = "button" class="ui-button ui-widget ui-corner-all" onClick="finalizeSO();">
							<span class="ui-icon ui-icon-check"></span> Finalize & Print Sales Order
						</button>
						<button type = "button" class="ui-button ui-widget ui-corner-all" onClick="saveSOHeader();">
							<span class="ui-icon ui-icon-disk"></span> Save Changes Made
						</button>

				';
				if($urights == "admin" && $so_no != '') {
					$headerControls .= '
						<button type = "button" name = "setRecycle" class="ui-button ui-widget ui-corner-all" onClick="javascript: cancel();">
							<span class="ui-icon ui-icon-cancel"></span> Cancel Sales Order
						</button>
					';
					
				}
			break;
		}
	
		echo $headerControls;
	}
	
	function setSONavs($so_no) {
		global $mydb;
		list($fwd) = $mydb->getArray("select so_no from pharma_so_header where so_no > '$so_no' and branch = '$_SESSION[branchid]' limit 1;");
		list($prev) = $mydb->getArray("select so_no from pharma_so_header where so_no < '$so_no' order by so_no desc limit 1;");
		list($last) = $mydb->getArray("select so_no from pharma_so_header where branch = '$_SESSION[branchid]' order by so_no desc limit 1;");
		list($first) = $mydb->getArray("select so_no from pharma_so_header where branch = '$_SESSION[branchid]' order by so_no asc limit 1;");
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
	<title>OMDC Prime Medical Diagnostics Corp.</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/datatables/css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
	<script language="javascript" src="js/pharmaso.js?sid=<?php echo uniqid(); ?>"></script>
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

			$('#patient_id').autocomplete({
				source:'suggestPatient.php', 
				minLength:3,
				select: function(event,ui) {
					$("#patient_name").val(ui.item.name);
					$("#patient_address").val(ui.item.addr);
				}
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
					"url": "pharma.sodatacontrol.php",
					"data": { trace_no: "<?php echo $traceNo; ?>", mod: "retrieve", sid: Math.random() },
					"method": "POST"	
				},
				"scrollY":  "220",
				"select":	'single',
				"pagingType": "full_numbers",
				"bProcessing": true,
				"searching": false,
				"paging": false,
				"info": false,
				
				"aoColumns": [
					{ mData: 'id' },
					{ mData: 'code' },
					{ mData: 'description' },
					{ mData: 'unit' },
					{ mData: 'qty', render: $.fn.dataTable.render.number(',', '.', 2, '')},
					{ mData: 'unit_price', render: $.fn.dataTable.render.number(',', '.', 2, '') },
					{ mData: 'discount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
					{ mData: 'amount_due', render: $.fn.dataTable.render.number(',', '.', 2, '') },
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [1,3,4,5,6,7]},
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
			$('#details').DataTable().ajax.url("pharma.sodatacontrol.php?mod=retrieve&trace_no=<?php echo $traceNo; ?>").load();
		}

		function selectDiscount(val) {
			switch(val) {
				case "E":
					$("#discountPercent").val('10');
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
				<td class="upper_menus" align=left width="80%">
					<?php setSOClickers($status,$so_no,$terms,$_SESSION['utype']); ?>
				</td>
				<td width=30% align=right style='padding-right: 5px;'><?php setSONavs($so_no); ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>

		<table border="0" cellpadding="0" cellspacing="1" width=100% class="td_content">
			<tr>
				<td width=60% valign=top>
					<table width=100% style="padding:0px 0px 0px 0px;">
					<tr><td height=2></td></tr>
						<tr>
							<td class="bareBold" align=left style="padding-left: 35px;" valign=top>Customer :</td>
							<td align="left">
								<table cellspacing=0 cellpadding=0 border=0 width=100%>
									<tr>
										<td width=25%><input type="text" id="patient_id" name="patient_id" value="<?php echo $res['pid']?>" class="inputSearch2" style="padding-left: 22px;width:98%;"></td>
										<td width=75% align=right colspan=2><input class="gridInput" type="text" name="patient_name" id="patient_name" autocomplete="off" value="<?php echo $res['patient_name']; ?>" style="width: 100%;"></td>
									</tr>
									<tr>
										<td style="font-size: 9px; padding-left: 5px; color: gray;">Customer ID</td><td colspan=2 style="font-size: 9px; padding-left: 5px; color: gray;">Customer Name</td>
									</tr>
									<tr>
										<td width=100% colspan=2><input class="gridInput" type="text" id="patient_address" name="patient_address" value="<?php echo $res['patient_address']?>" style="width: 100%;"></td>
									</tr>
									<tr>
										<td colspan=2 style="font-size: 9px; padding-left: 5px; color: gray;" colspan=2 >Address</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr><td height=2></td></tr>
						<tr>
							<td align="left" class="bareBold" style="padding-left: 35px;" valign=top>Bill To :</td>
							<td align=left>
								<table cellspacing=0 cellpadding=0 border=0 width=100%>
									<tr>
										<td width=25%><input type="text" id="customer_code" name="customer_code" value="<?php echo $res['customer_code']?>" class="inputSearch2" style="padding-left: 22px; width:98%;" placeholder = "0" onchange="javascript: checkClear(this.value);"></td>
										<td width=75% align=right colspan=2><input class="gridInput" type="text" name="customer_name" id="customer_name" autocomplete="off" value="<?php echo $res['customer_name']; ?>" style="width: 100%;"></td>
									</tr>
									<tr>
										<td style="font-size: 9px; color: gray; padding-left: 5px;">Company ID</td><td colspan=2 style="font-size: 9px; padding-left: 5px; color: gray;">Company Name</td>
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
						<tr>
							<td align="left" width="25%" class="bareBold" style="padding-left: 35px;">Referring Physician&nbsp;:</td>
							<td align=left>
								<input type="text" class="gridInput" style="width:50%;" name="physician" id="physician" value="<?php echo $res['physician']; ?>" >
							</td>				
						</tr>
					</table>
				</td>
				<td valign=top>
					<table border="0" cellpadding="0" cellspacing="1" width=100%>
						<tr><td height=2></td></tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Sales Order No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="so_no" id="so_no" value="<?php echo STR_PAD($so_no,6,'0',STR_PAD_LEFT); ?>" readonly >
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Date&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="so_date" id="so_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>">
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Senior Citizen/PWD ID&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="scpwd_id" id="scpwd_id" value="<?php echo $res['scpwd_id']; ?>">
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Charge SI No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="csi_no" id="csi_no" value="<?php echo $res['csi_no']; ?>">
							</td>				
						</tr>
						<tr>
							<td height=60px;></td>
						<tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Discount Type:&nbsp;:</td>
							<td>
								<select name="discountType" id="discountType" class="gridInput" style="width: 80%;" onchange="javascript: selectDiscount(this.value);">
									<option value='' <?php if($res['disc_type'] == '') { echo "selected"; } ?>>- Select Discount Type -</option>
									<option value='E' <?php if($res['disc_type'] == 'E') { echo "selected"; } ?>>Employee's Discount</option>
									<option value='D' <?php if($res['disc_type'] == 'D') { echo "selected"; } ?>>Doctor's Discount</option>
									<option value='S' <?php if($res['disc_type'] == 'S') { echo "selected"; } ?>>SC/PWD Discount</option>
									<option value='O' <?php if($res['disc_type'] == 'O') { echo "selected"; } ?>>Other Discounts</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Discount Percentage:&nbsp;:</td>
							<td>
								<input type="text" name="discountPercent" id="discountPercent" class="gridInput" style="width: 80%; font-size: 11px;" pattern="^\d*(\.\d{0,2})?$" value="<?php echo $res['disc_percent'] ?>" readonly>
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
					<th width=12%>SKU</th>
					<th >DESCRIPTION</th>
					<th width=8%>UNIT</th>
					<th width=8%>QTY</th>
					<th width=12%>UNIT PRICE</th>
					<th width=12%>DISCOUNT</th>
					<th width=12%>AMT. DUE</th>
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
						<!-- <a href="#" class="topClickers" onClick="javascript:applyDiscount();"><img src="images/icons/discount-icon.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Apply Line Discount</a>&nbsp; -->
						<a href="#" class="topClickers" onClick="javascript:deleteItem();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Selected Item</a>
					<?php } ?>
				</td>
				<td align=right width=50% valign=top>
					
					<span class="bareBold"><b>Gross Sales (Non-VAT):</b></span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="grossSales" id="grossSales" value="<?php echo number_format($res['gross'],2); ?>" readonly><br/>
					<span class="bareBold">Less &raquo; Discounts :</span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="salesDiscount" id="salesDiscount" value="<?php echo number_format($res['discount'],2); ?>" readonly><br/>
					<span class="bareBold"><b>Sub-Total :</b></span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="subTotal" id="subTotal" value="<?php echo number_format($res['net'],2); ?>" readonly><br/>
					<span class="bareBold"><b>Amount Due :</b></span> &nbsp;&nbsp;<input style="width:150px;text-align:right;" type=text name="amtDue" id="amtDue" value="<?php echo number_format($res['amount_due'],2); ?>" readonly><br/>
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
<!-- <div id="discounter" style="display: none;">
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
</div> -->
<div id="queueingslip" style="display: none;"></div>
</body>
</html>