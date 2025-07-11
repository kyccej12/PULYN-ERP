
<?php	
	session_start();
	require_once("handlers/_pofunct.php");
	$p = new myPO;
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['po_no']) && $_REQUEST['po_no'] != '') { 
		$res = $p->getArray("select *, lpad(po_no,6,0) as pono, lpad(supplier,6,0) as sup_code, date_format(po_date,'%m/%d/%Y') as d8, if(date_needed != '0000-00-00',date_format(date_needed,'%m/%d/%Y'),'') as nd8 from po_header where po_no='$_REQUEST[po_no]' and branch = '1';");
		$cSelected = "Y"; $status = $res['status']; $po_no = $res['pono']; $lock = $res['locked']; $trace_no = $res['trace_no'];
	} else {  
		$status = "Active"; $cSelected = "N"; $lock = 'N'; $po_no = '';
		$trace_no = $p->generateRandomString();
	}
		
	
	if($res['status'] != "Active") { $isReadOnly = "readonly"; $isDisabled = "disabled"; }
	
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
	<script language="javascript" src="js/po.js?sessid=<?php echo uniqid(); ?>"></script>
	<script>
	
		var line;
		
		function selectLine(obj,lid) {
			gObj = obj;
			$(obj).closest("tr").siblings().removeClass("diffcolor");
			$(obj).toggleClass("diffcolor");
			line = lid;
		}
		
		$(document).ready(function() { 
			$("#po_date").datepicker(); 
			$("#date_needed").datepicker(); 
			$('#qty').bind('keypress', function(e) { if(e.keyCode ==13){ addDetails(); } });
			$('#amount').bind('keypress', function(e) { if(e.keyCode ==13){ addDetails(); } });
			
			<?php if($status == 'Finalized' || $status == 'Cancelled') { echo "$(\"#xform :input\").prop('disabled',true);"; } ?>
			
			$('#itemDescription').autocomplete({
				source:'suggestItemsCost.php', 
				minLength:3,
				select: function(event,ui) {
					$("#itemCode").val(ui.item.item_code);
					$("#itemUnit").val(decodeURIComponent(ui.item.unit));
					$("#itemCost").val(decodeURIComponent(ui.item.unit_price));
					computeItemAmount($("#itemQty").val());
				}
			});

			$('#customer_id').autocomplete({
				source:'suggestContacts.php', 
				minLength:3,
				select: function(event,ui) {
					$("#cSelected").val('Y');
					$("#customer_id").val(ui.item.cid);
					$("#customer_name").val(decodeURIComponent(ui.item.cname));
					$("#cust_address").val(decodeURIComponent(ui.item.addr));
					$("#terms").val(ui.item.terms);
					
					savePOHeader();
				}
			});

			$('#requested_by').autocomplete({
				source:'suggestEmployee.php', 
				minLength:3
			});
		
			$('#details').dataTable({
				"ajax": {
					"url": "po.datacontrol.php",
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
					{ mData: 'item_code' },
					{ mData: 'description' },
					{ mData: 'unit' },
					{ mData: 'qty', render: $.fn.dataTable.render.number(',', '.', 2, '')},
					{ mData: 'cost', render: $.fn.dataTable.render.number(',', '.', 2, '') },
					{ mData: 'amount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [1,3,4,5,6]},
					{ className: "dt-body-right", "targets": [7,8]},
					{ "targets": [0,1], "visible": false }
				]
			});
		
		
		});

		function redrawDataTable() {
			$('#details').DataTable().ajax.url("po.datacontrol.php?mod=retrieve&trace_no=<?php echo $trace_no; ?>").load();
		}
		
	</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div>
	<form name="xform" id="xform">
		<input type="hidden" name="cSelected" id="cSelected" value="<?php echo $cSelected; ?>">
		<input type=hidden name="prev_po_date" id="prev_po_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>">
		<input type="hidden" name="trace_no" id="trace_no" value="<?php echo $trace_no; ?>">
		<table width=98% cellpadding=0 cellspacing=0 border=0 align=center>
			<tr>
				<td class="upper_menus" align=left>
					<?php $p->setHeaderControls($status,$po_no,$_SESSION['utype']); ?>
				</td>
				<td width=30% align=right style='padding-right: 5px;'><?php if($po_no) { $p->setNavButtons($po_no,$_SESSION['userid']); } ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>

		<table width=100% cellpadding="0" cellspacing="0" align=center class="td_content">
			<tr>

				<td width=50% valign=top>
					<table width=100% style="padding:0px 0px 0px 0px;">
						<tr><td height=2></td></tr>
						<tr>
							<td class="bareBold" align=left valign=top width=25% style="padding-left: 35px;">Supplier :</td>
							<td align="left">
								<table cellspacing=0 cellpadding=0 border=0 width=100%>
									<tr>
										<td width=25%><input type="text" id="customer_id" name="customer_id" value="<?php echo $res['sup_code']?>" class="inputSearch2" style="padding-left: 22px;"></td>
										<td width=75% align=right colspan=2><input class="gridInput" type="text" name="customer_name" id="customer_name" autocomplete="off" value="<?php echo $res['supplier_name']; ?>" style="width: 100%;" readonly></td>
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
							<td align="left" class="bareBold" style="padding-left: 35px;" valign=top></td>
							<td align=left>
								<input type="hidden" name="delivery_address" id="delivery_address">
							</td>				
						</tr>
						<tr>
							<td class="bareBold" align=left style="padding-left: 35px;">Credit Term&nbsp;:</td>
							<td align="left">
								<select id="terms" name="terms" class=gridInput style="width: 150px;font-size: 11px;" />
									<?php
										$tq = $p->dbquery("select terms_id, description from options_terms order by terms_id;");
										while(list($tid,$td) = $tq->fetch_array()) {
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
							<td align="left" width="30%" class="bareBold" style="padding-left: 45px;">P.O No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:40%;" type=text name="po_no" id="po_no" value="<?php echo $po_no; ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="left" width="30%" class="bareBold" style="padding-left: 45px;">Trans. Date&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:40%;" type=text name="po_date" id="po_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="left" class="bareBold" style="padding-left: 45px;">Requested By :</td>
							<td align=left>
								<input class="gridInput" style="width:40%;" type=text name="requested_by" id="requested_by" value="<?php echo $res['requested_by']; ?>">
							</td>				
						</tr>
						<tr>
							<td align="left" width="30%" class="bareBold" style="padding-left: 45px;">Purchase Request No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:40%;" type=text name="mrs_no" id="mrs_no" value="<?php  echo $res['mrs_no']; ?>">
							</td>				
						</tr>
						<tr>
							<td align="left" width="30%" class="bareBold" style="padding-left: 45px;">Date Needed&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:40%;" type=text name="date_needed" id="date_needed" value="<?php  echo $res['nd8']; ?>">
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
					<th width=15%>COST CENTER</th>
					<th width=15%>ITEM CODE</th>
					<th >DESCRIPTION</th>
					<th width=10%>UNIT</th>
					<th width=10%>QTY</th>
					<th width=10%>COST</th>
					<th width=12%>AMOUNT</th>
				</tr>
			</thead>
		</table>

		<table width=100% class="td_content">
			<tr>
				<td width=50%>
					Transaction Remarks: <br/>
					<textarea type="text" id="remarks" style="width:83%;" onchange="savePOHeader();"><?php echo $res['remarks']; ?></textarea>
				</td>
				<td align=right width=50% valign=top>
					<table border="0" cellpadding="0" cellspacing="1" width=100%>			
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 40%;">Total Amount&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="total_due" id="total_due" value="<?php echo number_format($res['amount'],2); ?>" readonly>
							</td>				
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align=left colspan=2 style="padding-top: 15px;">
					<?php if($status == 'Active') { ?>
						<a href="#" class="topClickers" onClick="javascript:addItem();"><img src="images/icons/add-2.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Add Item</a>&nbsp;
						<a href="#" class="topClickers" onClick="javascript:updateItem();"><img src="images/icons/edit.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Update Selected Item</a>&nbsp;
						<a href="#" class="topClickers" onClick="javascript:deleteItem();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Line Entry</a>&nbsp;&nbsp;
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
					<input type="text" name="itemCost" id="itemCost" class="gridInput" style="width: 80%;" value='0.00' readonly>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Amount :</td>
				<td align=left>
					<input type="text" name="itemAmount" id="itemAmount" class="gridInput" style="width: 80%;" value='0.00' readonly>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Cost Center :</td>
				<td align=left>
					<select name="itemCostCenter" id="itemCostCenter" class="gridInput" style="width: 80%;">
						<?php
							$ccQuery = $p->dbquery("SELECT unitcode, costcenter from options_costcenter order by costcenter");
							while($ccRow = $ccQuery->fetch_array()) {
								echo "<option value = '$ccRow[0]'>$ccRow[1]</option>";
							}
						?>
					</select>
				</td>
			</tr>
		</table>
	</form>
</div>
</body>
</html>