<?php	
	session_start();
	include("handlers/_generics.php");
	$mydb = new _init;
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['so_no']) && $_REQUEST['so_no'] != '') { 
		$res = $mydb->getArray("select *, lpad(so_no,6,0) as sono, date_format(so_date,'%m/%d/%Y') as d8, if(loa_date!='0000-00-00',date_format(loa_date,'%m/%d/%Y'),'') as load8, if(hmo_card_expiry!='0000-00-00',date_format(hmo_card_expiry,'%m/%d/%Y'),'') as exd8, lpad(patient_id,6,'0') as pid, if(customer_code!=0,lpad(customer_code,6,'0'),'') as cid from so_header where so_no = '$_REQUEST[so_no]' and branch = '$_SESSION[branchid]';");
		$cSelected = "Y"; $terms = $res['terms']; $so_no = $res['sono']; $status = $res['status']; $traceNo = $res['trace_no']; $prio = str_pad($res['priority_no'],6,'0',STR_PAD_LEFT);
	} else {  
		list($prio) = $mydb->getArray("select lpad(ifnull(max(priority_no),0)+1,6,0) from so_header where branch = '$_SESSION[branchid]' and so_date = '".date('Y-m-d')."';"); 
		$status = "Active"; $terms = 0; $traceNo = $mydb->generateRandomString();
	}

	list($testTotal) = $mydb->getArray("select count(*) from so_details where trace_no = '$res[trace_no]';");

	function setSOClickers($status,$so_no,$terms,$urights) {
		global $mydb;
	
		switch($status) {
			case "Finalized":
				list($posted_by,$posted_on) = $mydb->getArray("select fullname as name, date_format(updated_on,'%m/%d/%Y %p') as date_posted from so_header a left join user_info b on a.updated_by = b.emp_id where a.so_no='$so_no';");
				if($urights == "admin") {
					$headerControls = '
						<button type = "button" name = "setActive" class="ui-button ui-widget ui-corner-all" onClick="reopen();">
							<span class="ui-icon ui-icon-unlocked"></span> Set this Document to Active Status
						</button>
					';
				}

				if($terms != 0) {
					
					$headerControls .= '
						<button type = "button" name = "setVerify" class="ui-button ui-widget ui-corner-all" onClick="verify();">
							<span class="ui-icon ui-icon-check"></span> Mark as Verified
						</button>
					';
				}

				$headerControls .= '
					<button type = "button" name = "setPrint" class="ui-button ui-widget ui-corner-all" onClick="javascript: printSO();">
						<span class="ui-icon ui-icon-print"></span> Re-Print Sales Order
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
		list($fwd) = $mydb->getArray("select so_no from so_header where so_no > $so_no and branch = '$_SESSION[branchid]' limit 1;");
		list($prev) = $mydb->getArray("select so_no from so_header where so_no < $so_no and branch = '$_SESSION[branchid]' order by so_no desc limit 1;");
		list($last) = $mydb->getArray("select so_no from so_header where branch = '$_SESSION[branchid]' order by so_no desc limit 1;");
		list($first) = $mydb->getArray("select so_no from so_header where branch = '$_SESSION[branchid]' order by so_no asc limit 1;");
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
	<script language="javascript" src="js/so.js?sid=<?php echo uniqid(); ?>"></script>
	<script>
	
		$(document).ready(function($) {

			$('#patient_id').autocomplete({
				source:'suggestPatient.php', 
				minLength:3,
				select: function(event,ui) {
					$("#patient_name").val(ui.item.name);
					$("#patient_address").val(ui.item.addr);

					if(ui.item.mid_no != '') {
						if(confirm("It appears that this patient has an active PAG_IBIG MID NO. Do you wish to use this on this Service Order?") == true) {
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

			var doctorsName = [
                "LYNETTE R. FERNANDEZ, MD, FPCR",
                "ERIC R. BALILI, MD, FPCR",
                "ESTHER FEBE T. MADARANG, MD, FPCR",
                "MARC JENSSEN I. CABALLERO, MD, FPCR",
                "CHRISTIAN DARREL B. BAISAC, MD, FPCR",
                "CARIE ANNE BANATE-JORGE, MD, FPCR",
				"CRISTINA BARIQUIT, RN, MD",
				"Colleen Iris Sanchez, M.D",
            ];

			$("#physician").autocomplete({
                source: doctorsName,
                minLength: 3
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });

			$('#details').dataTable({
				"ajax": {
					"url": "so.datacontrol.php",
					"data": { trace_no: "<?php echo $traceNo; ?>", mod: "retrieve", sid: Math.random() },
					"method": "POST"	
				},
				"scrollY":  "150",
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
					{ mData: 'amount_due', render: $.fn.dataTable.render.number(',', '.', 2, '') },
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [1,3,4,5,6]},
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
			$('#details').DataTable().ajax.url("so.datacontrol.php?mod=retrieve&trace_no=<?php echo $traceNo; ?>").load();
		}

		function printQSlip(priorityno) {
			var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/qslip.php?priority="+priorityno+"&sid="+Math.random()+"'></iframe>";
			$("#queueingslip").html(txtHTML);
			$("#queueingslip").dialog({ title: "Queueing Slip", width: 400, height: 520, resizable: false, modal: true });
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
					<?php setSOClickers($status,$so_no,$terms,$_SESSION['utype']); ?>
				</td>
				<td width=30% align=right style='padding-right: 5px;'><?php if($so_no != '') { setSONavs($so_no); } ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>

		<table border="0" cellpadding="0" cellspacing="1" width=100% class="td_content">
			<tr>
				<td width=60% valign=top>
					<table width=100% style="padding:0px 0px 0px 0px;">
						<tr><td height=2></td></tr>
						<tr>
							<td align="left"  width=25%  class="bareBold" style="padding-left: 35px;">Priority No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:25%;" type=text name="priority_no" id="priority_no" value="<?php echo $prio; ?>" readonly>&nbsp;<a href="#" onclick="javascript: printQSlip(<?php echo $prio; ?>);"><img src="images/icons/barcode-scanner.png" width=22 height=18 align=absmiddle /></a>
							</td>				
						</tr>
	
						<tr>
							<td class="bareBold" align=left style="padding-left: 35px;" valign=top>Patient :</td>
							<td align="left">
								<table cellspacing=0 cellpadding=0 border=0 width=100%>
									<tr>
										<td width=25%><input type="text" id="patient_id" name="patient_id" value="<?php echo $res['pid']?>" class="inputSearch2" style="padding-left: 22px;width:98%;"></td>
										<td width=75% align=right colspan=2><input class="gridInput" type="text" name="patient_name" id="patient_name" autocomplete="off" value="<?php echo strtoupper($res['patient_name']); ?>" style="width: 100%;" readonly></td>
									</tr>
									<tr>
										<td style="font-size: 9px; padding-left: 5px; color: gray;">Patient ID</td><td colspan=2 style="font-size: 9px; padding-left: 5px; color: gray;">Patient Name</td>
									</tr>
									<tr>
										<td width=100% colspan=2><input class="gridInput" type="text" id="patient_address" name="patient_address" value="<?php echo $res['patient_address']?>" style="width: 100%;" readonly></td>
									</tr>
									<tr>
										<td colspan=2 style="font-size: 9px; padding-left: 5px; color: gray;" colspan=2 >Address</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr><td height=2></td></tr>
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
							<td align="left" width="25%" class="bareBold" style="padding-left: 35px;">Requesting Physician&nbsp;:</td>
							<td  align=left width=80%><input class="gridInput" type="text" id="physician" name="physician" value="<?php echo $res['physician']?>" style="width: 100%;"></td>				
						</tr>
					</table>
				</td>
				<td valign=top>
					<table border="0" cellpadding="0" cellspacing="1" width=100%>
						<tr><td height=2></td></tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Service Order No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="so_no" id="so_no" value="<?php echo $so_no; ?>" >
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
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">HMO Card No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="hmo_card_no" id="hmo_card_no" value="<?php echo $res['hmo_card_no']; ?>" onchange='javascript: saveSOHeader();'>
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">HMO Card Expiry Date&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="hmo_expiry_date" id="hmo_expiry_date" value="<?php echo $res['exd8']; ?>">
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">With LOA&nbsp;:</td>
							<td align=left>
								<select class="gridInput" style="width:70%;" type=text name="with_loa" id="with_loa">
									<option value='N' <?php if($res['with_loa'] == 'N') { echo "selected"; }?>>- No -</option>
									<option value='Y' <?php if($res['with_loa'] == 'Y') { echo "selected"; }?>>- Yes -</option>
								</select>
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">LOA Date&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="loa_date" id="loa_date" value="<?php echo $res['load8']; ?>">
							</td>				
						</tr>	
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Patient Status&nbsp;:</td>
							<td align=left>
								<select class="gridInput" style="width:70%;" name="patient_stat" id="patient_stat" >
									<?php
										$srQuery = $mydb->dbquery("select id, patientstatus from options_patientstat");
										while($srRow = $srQuery->fetch_array()) {
											echo "<option value='$srRow[0]' ";
											if($res['patient_stat'] == $srRow[0]) { echo "selected"; }
											echo ">$srRow[1]</option>";
										}
									?>
								</select>
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">PAG-IBIG MID No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="mid_no" id="mid_no" value="<?php echo $res['mid_no']; ?>">
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Digital Marketing Promo No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="digi_promo" id="digi_promo" value="<?php echo $res['digi_promo']; ?>">
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
					<th width=12%>ITEM CODE</th>
					<th >DESCRIPTION</th>
					<th width=8%>UNIT</th>
					<th width=8%>QTY</th>
					<th width=12%>UNIT PRICE</th>
					<th width=12%>AMT. DUE</th>
				</tr>
			</thead>
		</table>

		<table width=100% class="td_content">
			<tr>
				<td width=50%>
					Transaction Remarks: <br/>
					<textarea rows=2 type="text" id="remarks" style="width:83%;" onchange='javascript: saveSOHeader();'><?php echo $res['remarks']; ?></textarea>
				</td>
				<td align=right width=50% valign=top>
					Total No. of Test(s) : &nbsp;&nbsp;<input style="width:200px;text-align:right;" type=text name="testTotal" id="testTotal" value="<?php echo number_format($testTotal,2); ?>" readonly> <br />
					Transaction Total : &nbsp;&nbsp;<input style="width:200px;text-align:right;" type=text name="grandTotal" id="grandTotal" value="<?php echo number_format($res['amount'],2); ?>" readonly>

				</td>
			</tr>
			<tr>
				<td align=left colspan=2 style="padding-top: 15px;">
					<?php if($status == 'Active' || $status == '') { ?>
						<a href="#" class="topClickers" onClick="javascript:addItem();"><img src="images/icons/add-2.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Add Item</a>&nbsp;
						<!--a href="#" class="topClickers" onClick="javascript:updateItem();"><img src="images/icons/discount-icon.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Apply Line Discount</a-->&nbsp;
						<a href="#" class="topClickers" onClick="javascript:deleteItem();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Selected Item</a>
					<?php } ?>
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
					<input type="text" name="itemQty" id="itemQty" class="gridInput"style="width: 80%;" value=1 onchange="javascript: computeItemAmount();">
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
					<input type="text" name="itemSpecial" id="itemSpecial" class="gridInput" style="width: 80%;" value='0.00' onchange="javascript: computeItemAmount();">
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
<div id="queueingslip" style="display: none;"></div>
</body>
</html>