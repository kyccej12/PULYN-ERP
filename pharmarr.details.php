<?php	
	/* UNSET QUED FOR DELETION */
	session_start();
	unset($_SESSION['ques']);
	
	//ini_set("display_errors","On");
	require_once "handlers/_pharmarrfunct.php";
	$p = new myRR;
	//$res = array();
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['rr_no']) && $_REQUEST['rr_no'] != '') {  
		$res = $p->getArray("select *, lpad(rr_no,6,0) as rrno, lpad(supplier,6,0) as sup_code, date_format(rr_date,'%m/%d/%Y') as d8, date_format(invoice_date,'%m/%d/%Y') as id8 from pharma_rr_header where rr_no='$_REQUEST[rr_no]' and branch = '1';");
		$cSelected = "Y"; $status = $res['status']; $lock = $res['locked']; $rr_no = $res['rrno']; $trace_no = $res['trace_no'];
	} else {  
		$trace_no = $p->generateRandomString();
		$status = "Active"; $dS = "1"; $cSelected = "N"; $lock = "N";
	}
	
	if($status != "Active") { $isReadOnly = "readonly"; $isDisabled = "disabled"; }
	
?>
<!doctype html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/datatables/css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
	<script language="javascript" src="js/pharmarr.js?sid=<?php echo uniqid(); ?>"></script>
	<script>
		
		var line;
		function selectLine(obj,lid) {
			gObj = obj;
			$(obj).closest("tr").siblings().removeClass("diffcolor");
			$(obj).toggleClass("diffcolor");
			line = lid;
		}
		
		$(document).ready(function($){
			<?php if($status == 'Finalized' || $status == 'Cancelled') { echo "$(\"#xform :input\").prop('disabled',true);"; } ?>
			$("#po_date").datepicker(); $("#rr_date").datepicker(); $("#invoice_date").datepicker();

			$('#itemDescription').autocomplete({
				source:'suggestPharmaItems.php', 
				minLength:3,
				select: function(event,ui) {
					$("#itemCode").val(ui.item.code);
					$("#itemUnit").val(decodeURIComponent(ui.item.unit));
					$("#itemCost").val(decodeURIComponent(ui.item.cost));
					computeItemAmount($("#itemQty").val());
				}
			});

			$('#received_by').autocomplete({
				source:'suggestEmployee.php', 
				minLength:3
				
			});

			$('#customer_id').autocomplete({
				source:'suggestContacts.php', 
				minLength:3,
				select: function(event,ui) {
					$("#cSelected").val('Y');
					$("#customer_id").val(ui.item.cid);
					$("#customer_name").val(decodeURIComponent(ui.item.cname));
					$("#cust_address").val(decodeURIComponent(ui.item.addr));
					savePRRHeader();
				}
			});

			$('#details').dataTable({
				"ajax": {
					"url": "pharmarr.datacontrol.php",
					"data": { trace_no: "<?php echo $trace_no; ?>", mod: "retrieve", sid: Math.random() },
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
					{ mData: 'costcenter' },
					{ mData: 'cc' },
					{ mData: 'po' },
					{ mData: 'podate' },
					{ mData: 'item_code' },
					{ mData: 'description' },
					{ mData: 'unit' },
					{ mData: 'qty', render: $.fn.dataTable.render.number(',', '.', 2, '')},
					{ mData: 'cost', render: $.fn.dataTable.render.number(',', '.', 2, '') },
					{ mData: 'amount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [1,3,4,5,6,7,8]},
					{ className: "dt-body-right", "targets": [9,10]},
					{ "targets": [0,1], "visible": false }
				]
			});

		});
		
		function redrawDataTable() {
			$('#details').DataTable().ajax.url("pharmarr.datacontrol.php?mod=retrieve&trace_no=<?php echo $trace_no; ?>").load();
		}

		function printPharmaRR() {
			//window.open("print/rr.print.php?rr_no="+rr_no+"&sid="+Math.random()+"&user="+uid+"&reprint=Y","Receiving Report","location=1,status=1,scrollbars=1,width=640,height=720");
			parent.printPharmaRR($("#rr_no").val());
		}
	</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div style="padding: 10px;">
	<form name="xform" id="xform">
		<input type="hidden" name="cSelected" id="cSelected" value="<?php echo $cSelected; ?>">
		<input type="hidden" name="trace_no" id="trace_no" value="<?php echo $trace_no; ?>">
		<input type=hidden name="prev_rr_date" id="prev_rr_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>" >
		<table width=100% cellpadding=0 cellspacing=0 border=0 align=center>
			<tr>
				<td class="upper_menus" align=left>
					<?php $p->setHeaderControls($status,$rr_no,$_SESSION['utype']); ?>
				</td>
				<td width=30% align=right style='padding-right: 5px;'><?php if($rr_no) { $p->setNavButtons($rr_no); } ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="1" width=100% class = "td_content">
			<tr>
				<td width=50% valign=top>
					<table width=100% style="padding:0px 0px 0px 0px;">
						<tr><td height=2></td></tr>
						<tr>
							<td class="bareBold" align=left valign=top width=25% style="padding-left: 35px;">Supplier&nbsp;:</td>
							<td align="left">
								<table cellspacing=0 cellpadding=0 border=0 width=100%>
									<tr>
										<td width=25%><input type="text" id="customer_id" name="customer_id" value="<?php echo $res['sup_code']?>" class="inputSearch2" style="padding-left: 22px;"></td>
										<td width=75% align=right colspan=2><input type="text" name="customer_name" id="customer_name" autocomplete="off" class="gridInput" value="<?php echo $res['supplier_name']; ?>" style="width: 100%;;" readonly></td>
									</tr>
									<tr>
										<td style="font-size: 9px; padding-left: 5px;">Code</td><td colspan=2 style="font-size: 9px; padding-left: 20px;">Supplier Name</td>
									</tr>
									<tr>
										<td width=100% colspan=2><input class="gridInput" type="text" id="cust_address" name="cust_address" value="<?php echo $res['supplier_addr']?>" style="width: 100%;" readonly></td>
									</tr>
									<tr>
										<td colspan=2 style="font-size: 9px; padding-left: 5px;" colspan=2 >Address</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td align="left" class="bareBold" style="padding-left: 35px;">Received By&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:60%;" type=text name="received_by" id="received_by" value="<?php echo $res['received_by']; ?>">
							</td>				
						</tr>
					</table>
				</td>
				<td valign=top>
					<table border="0" cellpadding="0" cellspacing="1" width=100%>
						<tr><td height=2></td></tr>
						<tr>
							<td align="left" width="40%" class="bareBold" style="padding-left: 55px;">RR. No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:40%;" type=text name="rr_no" id="rr_no" value="<?php echo $rr_no; ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="left" width="40%" class="bareBold" style="padding-left: 55px;">Date Received&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:40%;" type=text name="rr_date" id="rr_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>" onchange="javascript: checkLockDate(this.id,this.value,$('#prev_rr_date').val());" >
							</td>				
						</tr>
						<tr>
							<td align="left" width="40%" class="bareBold" style="padding-left: 55px;">Delivery Ref. # (Invoice, DR)&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:40%;" type=text name="invoice_no" id="invoice_no" value="<?php echo $res['invoice_no']; ?>"  onchange='javascript: checkDuplicateInvoice(this.value);'>
							</td>				
						</tr>
						<tr>
							<td align="left" width="40%" class="bareBold" style="padding-left: 55px;">Delivery Ref. Date&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:40%;" type=text name="invoice_date" id="invoice_date" value="<?php echo $res['id8']; ?>" onchange='javascript: savePRRHeader();'>
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
					<th></th>
					<th width=12%>COST CENTER</th>
					<th width=8%>PO #</th>
					<th width=8%>DATE</th>
					<th width=12%>ITEM CODE</th>
					<th >DESCRIPTION</th>
					<th width=8%>UNIT</th>
					<th width=8%>QTY</th>
					<th width=8%>COST</th>
					<th width=10%>AMOUNT</th>
				</tr>
			</thead>
		</table>
		<table width=100% class="td_content">
			<tr>
				<td width=50%>
					Transaction Remarks: <br/>
					<textarea type="text" id="remarks" style="width:83%;"><?php echo $res['remarks']; ?></textarea>
				</td>
				<td align=right width=50% valign=top>
					Total Amount&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input style="width:160px; text-align:right;" type=text name="total_amount" id="total_amount" value="<?php echo number_format($res['amount'],2); ?>" readonly>
				</td>
			</tr>
			<tr>
				<td align=left colspan=2 style="padding-top: 15px;">
					<?php if($status == 'Active') { ?>
						<a href="#" class="topClickers" onClick="javascript:addPharmaItem();"><img src="images/icons/add-2.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Add Item</a>&nbsp;
						<a href="#" class="topClickers" onClick="javascript:updateItem();"><img src="images/icons/edit.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Update Selected Item</a>&nbsp;
						<a href="#" class="topClickers" onClick="javascript:deletePharmaItem();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Line Entries</a>&nbsp;&nbsp;
						<!--a href="#" class="topClickers" onClick="javascript:addDescription();"><img src="images/icons/article.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Add Custom Item Description</a-->
					<?php } ?>
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="loaderMessage" title="Processing..." style="display: none;">
	<p><span style="float:left; margin:0 7px 20px 0;"><img src="images/loader.gif" /></span>Please wait while the system is processing your request...</p>
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
					<input type="text" name="itemCode" id="itemCode" class="gridInput" style="width: 80%;" readonly>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Unit :</td>
				<td align=left>
					<input type="text" name="itemUnit" id="itemUnit" class="gridInput" style="width: 80%;" readonly>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Quantity :</td>
				<td align=left>
					<input type="text" name="itemQty" id="itemQty" class="gridInput"style="width: 80%;" value=0 onchange="javascript: computeItemAmount(this.value);">
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Unit Cost :</td>
				<td align=left>
					<input type="text" name="itemCost" id="itemCost" class="gridInput" style="width: 80%;" value='0.00' disabled>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Amount :</td>
				<td align=left>
					<input type="text" name="itemAmount" id="itemAmount" class="gridInput" style="width: 80%;" value='0.00' disabled>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Cost Center :</td>
				<td align=left>
					<select name="itemCostCenter" id="itemCostCenter" class="gridInput" style="width: 80%;">
						<?php
							$ccQuery = $p->dbquery("SELECT unitcode, costcenter from options_costcenter where unitcode = '150';");
							while($ccRow = $ccQuery->fetch_array()) {
								echo "<option value = '$ccRow[0]'>$ccRow[1]</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>PO # :</td>
				<td align=left>
					<input type="text" name="itemPONo" id="itemPONo" class="gridInput" style="width: 80%;">
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>PO Date</td>
				<td align=left>
					<input type="text" name="itemPODate" id="itemPODate" class="gridInput" style="width: 80%;">
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="invoiceAttachment" style="display: none;"></div>
</body>
</html>