<?php	
	session_start();
	include("handlers/_generics.php");
	$mydb = new _init;
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['srr_no']) && $_REQUEST['srr_no'] != '') { 
		$res = $mydb->getArray("select *, lpad(srr_no,6,0) as srrno, date_format(srr_date,'%m/%d/%Y') as d8, if(ref_date='0000-00-00','',date_format(ref_date,'%m/%d/%Y')) as rd8 from srr_header where srr_no = '$_REQUEST[srr_no]' and branch = '$_SESSION[branchid]';");
		$cSelected = "Y"; $srr_no = $res['srrno']; $status = $res['status']; $traceNo = $res['trace_no'];
	} else {  
		$status = "Active"; $traceNo = $mydb->generateRandomString();
	}

	function setSRRClickers($status,$srr_no,$uid,$dS,$urights) {
		global $mydb;
	
		switch($status) {
			case "Finalized":
				list($posted_by,$posted_on) = $mydb->getArray("select fullname as name, date_format(updated_on,'%m/%d/%Y %p') as date_posted from srr_header a left join user_info b on a.updated_by = b.emp_id where a.srr_no='$srr_no';");
				if($urights == "admin") {
					$headerControls = "<a href=\"#\" class=\"topClickers\" onclick=\"javascript: reopenSRR('$srr_no');\"><img src='images/icons/edit.png' align=absmiddle width=16 height=16 />&nbsp;Set this Document to Active Status</a>&nbsp;";
				}
				$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onClick=\"javascript: parent.printSRR('$srr_no','$uid','N');\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Print Stocks Return Slip</a>&nbsp;";
			break;
			case "Cancelled":
				if($urights == "admin") {
					$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:reusePO('$srr_no');\" style=\"padding: 5px;\"><img src=\"images/icons/refresh.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Recycle this Document</a>&nbsp;";	
				}
			break;
			case "Active": default:
				$headerControls = "<a href=\"#\" class=\"topClickers\" onClick=\"javascript:finalizeSRR('$srr_no','$_SESSION[userid]');\"><img src=\"images/icons/ok.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Finalize Stocks Return Slip</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:saveSRRHeader();\"><img src=\"images/save.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Save Changes</a>&nbsp;";
				if($urights == "admin" && $dS != 1) {
					$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:cancelSRR('$srr_no');\"><img src=\"images/icons/cancel.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Cancel this Document</a>";
				}
			break;
		}
	
		echo $headerControls;
	}
	
	function setSRRNavs($srr_no) {
		global $mydb;
		list($fwd) = $mydb->getArray("select srr_no from srr_header where srr_no > $srr_no and branch = '$_SESSION[branchid]' limit 1;");
		list($prev) = $mydb->getArray("select srr_no from srr_header where srr_no < $srr_no and branch = '$_SESSION[branchid]' order by srr_no desc limit 1;");
		list($last) = $mydb->getArray("select srr_no from srr_header where branch = '$_SESSION[branchid]' order by srr_no desc limit 1;");
		list($first) = $mydb->getArray("select srr_no from srr_header where branch = '$_SESSION[branchid]' order by srr_no asc limit 1;");
		if($prev)
			$nav = $nav . "<a href=# onclick=\"parent.viewSRR('$prev');\"><img src='images/resultset_previous.png'  title='Previous Record' /></a>";
		if($fwd) 
			$nav = $nav . "<a href=# onclick=\"parent.viewSRR('$fwd');\"><img src='images/resultset_next.png' 'title='Next Record' /></a>";
		echo "<a href=# onclick=\"parent.viewSRR('$first');\"><img src='images/resultset_first.png' title='First Record' /><a>" . $nav . "<a href=# onclick=\"parent.viewSRR('$last');\"><img src='images/resultset_last.png' title='Last Record' /></a>";
	}

		
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
	<script language="javascript" src="js/srr.js"></script>
	<script>
	
		$(document).ready(function($) {
			
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

			$('#received_from').autocomplete({
				source:'suggestEmployee.php', 
				minLength:3
			});

			$('#received_by').autocomplete({
				source:'suggestEmployee.php', 
				minLength:3
			});

			$('#details').dataTable({
				"ajax": {
					"url": "srr.datacontrol.php",
					"data": { trace_no: "<?php echo $traceNo; ?>", mod: "retrieve", sid: Math.random() },
					"method": "POST"	
				},
				"scrollY":  "230",
				"select":	'single',
				"pagingType": "full_numbers",
				"bProcessing": true,
				"searching": false,
				"paging": false,
				"info": false,
				
				"aoColumns": [
					{ mData: 'id' },
					{ mData: 'item_code' },
					{ mData: 'description' },
					{ mData: 'unit' },
					{ mData: 'qty', render: $.fn.dataTable.render.number(',', '.', 2, '')},
					{ mData: 'cost', render: $.fn.dataTable.render.number(',', '.', 2, '') },
					{ mData: 'amount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [1,3,4,5,6]},
					{ "targets": [0], "visible": false }
				]
			});
			
			<?php if($status == 'Finalized' || $status == 'Cancelled') {
				echo "$(\"#xform :input\").prop('disabled',true);";
			} else { ?>
				$("#srr_date").datepicker(); $("#ref_date").datepicker();
			
			
			<?php } ?>
		});

		function redrawDataTable() {
			$('#details').DataTable().ajax.url("srr.datacontrol.php?mod=retrieve&trace_no=<?php echo $traceNo; ?>").load();
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
					<?php setSRRClickers($status,$srr_no,$uid,$dS,$_SESSION['utype']); ?>
				</td>
				<td width=30% align=right style='padding-right: 5px;'><?php if($srr_no != '') { setSRRNavs($srr_no); } ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>

		<table border="0" cellpadding="0" cellspacing="1" width=100% class="td_content">
			<tr>
				<td width=60% valign=top>
					<table width=100% style="padding:0px 0px 0px 0px;">
						<tr><td height=2></td></tr>
						<tr>
							<td class="bareBold" align=left width=25% style="padding-left: 35px;">Received From :</td>
							<td align="left">
								<input class="inputSearch2" style="width:80%;padding-left:22px;" type=text name="received_from" id="received_from" value="<?php echo $res['received_from']; ?>"  >
							</td>
						</tr>
						<tr>
							<td align="left" class="bareBold" style="padding-left: 35px;">Checked & Received By :</td>
							<td align=left>
								<input class="inputSearch2" style="width:80%;padding-left:22px;" type=text name="received_by" id="received_by" value="<?php echo $res['received_by']; ?>" onchange='javascript: saveSRRHeader();' >
							</td>				
						</tr>
						<tr>
							<td align="left" width="25%" class="bareBold" style="padding-left: 35px;">Source Reference Type&nbsp;:</td>
							<td align=left>
								<select class="gridInput" style="width:50%;" name="ref_type" id="ref_type" >
									<?php
										$srQuery = $mydb->dbquery("select id, srrtype from options_srrtype");
										while($srRow = $srQuery->fetch_array()) {
											echo "<option value='$srRow[0]' ";
											if($res['ref_type'] == $srRow[0]) { echo "selected"; }
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
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">SRR No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="srr_no" id="srr_no" value="<?php echo $srr_no; ?>" >
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Date Received&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="srr_date" id="srr_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>">
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Reference No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="ref_no" id="ref_no" value="<?php echo $res['ref_no']; ?>" onchange='javascript: saveSRRHeader();'>
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Reference Date&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="ref_date" id="ref_date" value="<?php echo $res['rd8']; ?>">
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
					<textarea rows=2 type="text" id="remarks" style="width:83%;" onchange='javascript: saveSRRHeader();'><?php echo $res['remarks']; ?></textarea>
				</td>
				<td align=right width=50% valign=top>
					
					Transaction Total : &nbsp;&nbsp;<input style="width:200px;text-align:right;" type=text name="grandTotal" id="grandTotal" value="<?php echo number_format($res['amount'],2); ?>" readonly>

				</td>
			</tr>
			<tr>
				<td align=left colspan=2 style="padding-top: 15px;">
					<?php if($status == 'Active' || $status == '') { ?>
						<a href="#" class="topClickers" onClick="javascript:addItem();"><img src="images/icons/add-2.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Add Item</a>&nbsp;
						<a href="#" class="topClickers" onClick="javascript:updateItem();"><img src="images/icons/edit.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Update Selected Item</a>&nbsp;
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
		</table>
	</form>
</div>
</body>
</html>