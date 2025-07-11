<?php	
	/* UNSET QUED FOR DELETION */
	session_start();
	
	include("handlers/_generics.php");
	$con = new _init();

	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['sw_no']) && $_REQUEST['sw_no'] != '') { 
		$sw_no = $_REQUEST['sw_no']; 
		$res = $con->getArray("select *, date_format(sw_date,'%m/%d/%Y') as d8, if(request_date='0000-00-00','',date_format(request_date,'%m/%d/%Y')) as rd8 from pharma_sw_header where sw_no='$sw_no' and branch = '1';");
		$status = $res['status']; $traceNo = $res['trace_no'];
	} else {  
		$status = "Active";
		$traceNo = $con->generateRandomString();
	}
		
	function setHeaderControls($status,$lock,$sw_no,$uid,$dS) {
		global $con;
		list($urights) = $con->getArray("select user_type from user_info where emp_id='$uid'");
		if($lock != 'Y') {
			switch($status) {
				case "Finalized":
					list($posted_by,$posted_on) = $con->getArray("select fullname as name, date_format(updated_on,'%m/%d/%Y %p') as date_posted from pharma_sw_header a left join user_info b on a.updated_by=b.emp_id where a.sw_no='$sw_no';");
					if($urights == "admin") {
						$headerControls = "<a href=\"#\" class=\"topClickers\" onclick=\"javascript: reopenSW('$sw_no');\"><img src='images/icons/edit.png' align=absmiddle width=16 height=16 />&nbsp;Set this Document to Active Status</a>&nbsp;";
					}
					$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onClick=\"javascript:parent.printPharmaSW('$sw_no','$_SESSION[userid]','N');\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Print Stocks Withdrawal Slip</a>&nbsp;";
				break;
				case "Cancelled":
					if($urights == "admin") {
						$headerControls = $headerControls . "<a href=\"#\" class=\"topClickers\" onclick=\"javascript:reuseSW('$sw_no');\" style=\"padding: 5px;\"><img src=\"images/icons/refresh.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Recycle this Document</a>&nbsp;";	
					}
				break;
				case "Active": default:
					$headerControls = "<a href=\"#\" class=\"topClickers\" onClick=\"javascript:finalizeSW('$sw_no','$_SESSION[userid]');\"><img src=\"images/icons/ok.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Finalize Stocks Withdrawal Slip</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:saveSWHeader();\"><img src=\"images/save.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Save Changes</a>&nbsp;";
					if($urights == "admin" && $dS != 1) {
						$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:cancelSW('$sw_no');\"><img src=\"images/icons/cancel.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Cancel this Document</a>&nbsp;";
					}
				break;
			}
		} else {
			$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onClick=\"javascript:parent.printPharmaSW('$sw_no','$_SESSION[userid]','Y');\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Re-Print Stocks Withdrawal Slip</a>";
		}
		echo $headerControls;
	}
	
	function setNavButtons($sw_no) {
		global $con;

		list($fwd) = $con->getArray("select sw_no from pharma_sw_header where sw_no > '$sw_no'  and branch = '$_SESSION[branchid]' limit 1;");
		list($prev) = $con->getArray("select sw_no from pharma_sw_header where sw_no < '$sw_no' and branch = '$_SESSION[branchid]' order by sw_no desc limit 1;");
		list($last) = $con->getArray("select sw_no from pharma_sw_header where branch = '$_SESSION[branchid]' order by sw_no desc limit 1;");
		list($first) = $con->getArray("select sw_no from pharma_sw_header where branch = '$_SESSION[branchid]' order by sw_no asc limit 1;");
		if($prev)
			$nav = $nav . "<a href=# onclick=\"parent.viewPO('$prev');\"><img src='images/resultset_previous.png'  title='Previous Record' /></a>";
		if($fwd) 
			$nav = $nav . "<a href=# onclick=\"parent.viewSRR('$fwd');\"><img src='images/resultset_next.png' 'title='Next Record' /></a>";
		echo "<a href=# onclick=\"parent.viewSRR('$first');\"><img src='images/resultset_first.png' title='First Record' /><a>" . $nav . "<a href=# onclick=\"parent.viewSRR('$last');\"><img src='images/resultset_last.png' title='Last Record' /></a>";
	}

	
?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
	<script language="javascript" src="js/pharmasw.js"></script>
	<script>
		
		$(document).ready(function($) { 

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

			$('#withdrawn_by').autocomplete({
				source:'suggestEmployee.php', 
				minLength:3
			});

			$('#details').dataTable({
				"ajax": {
					"url": "pharmasw.datacontrol.php",
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
			
				$('#request_date').datepicker(); $('#sw_date').datepicker();
			
			<?php } ?>
		});
		
		function redrawDataTable() {
			$('#details').DataTable().ajax.url("pharmasw.datacontrol.php?mod=retrieve&trace_no=<?php echo $traceNo; ?>").load();
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
		<input type="hidden" name="trace_no" id="trace_no" value="<?php echo $traceNo; ?>">
		<table width=100% cellpadding=0 cellspacing=0 border=0 align=center>
			<tr>
				<td class="upper_menus" align=left>
					<?php setHeaderControls($status,$lock,$sw_no,$_SESSION['userid'],$dS); ?>
				</td>
				<td width=20% align=right style='padding-right: 5px;'><?php if($sw_no) { setNavButtons($sw_no); } ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>

		<table border="0" cellpadding="0" cellspacing="1" width=100% class="td_content">
			<tr>
				<td width=50% valign=top>
					<table width=100%>
						<tr>
							<td class="bareBold" align=left width=30% style="padding-left: 10px;">Withdrawn or Requested By :</td>
							<td align="left">
								<input class="inputSearch2" style="width:70%;padding-left:22px;" type=text name="withdrawn_by" id="withdrawn_by" value="<?php echo $res['withdrawn_by']; ?>" onchange='javascript: saveSWHeader();'>
							</td>
						</tr>
						<tr>
							<td class="bareBold" align=left width=30% style="padding-left: 10px;">Purpose of Withdrawal&nbsp;:</td>
							<td align=left>
								<select class="gridInput" style="width:70%;" name="ref_type" id="ref_type" >
									<?php
										$tQuery = $con->dbquery("select id, `type` from options_wtype order by `type`;");
										while($tRow = $tQuery->fetch_array()) {
											echo "<option value='$tRow[0]' ";
											if($res['ref_type'] == $tRow[0]) { echo "selected"; }
											echo ">$tRow[1]</option>";
										}
									?>
								</select>
							</td>				
						</tr>
						<tr>
							<td class="bareBold" align=left width=30% style="padding-left: 10px;">Cost Center&nbsp;:</td>
							<td align=left>
								<select class="gridInput" style="width:70%;" name="cost_center" id="cost_center" >
									<?php
										$uQuery = $con->dbquery("select unitcode, costcenter from options_costcenter where unitcode = '150';");
										while($uRow = $uQuery->fetch_array()) {
											echo "<option value='$uRow[0]' ";
											if($res['cost_center'] == $tRow[0]) { echo "selected"; }
											echo ">$uRow[1]</option>";
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
							<td class="bareBold" align=left width=30% style="padding-left: 30px;">Doc. No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="sw_no" id="sw_no" value="<?php echo $sw_no; ?>" readonly >
							</td>				
						</tr>
						<tr>
							<td class="bareBold" align=left width=30% style="padding-left: 30px;">Transaction Date&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="sw_date" id="sw_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>" >
							</td>				
						</tr>
						<tr>
							<td class="bareBold" align=left width=30% style="padding-left: 30px;">Date Requested&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="request_date" id="request_date" value="<?php echo $res['rd8']; ?>" >
							</td>				
						</tr>
						<tr>
							<td class="bareBold" align=left width=30% style="padding-left: 30px;">Material Request No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="mr_no" id="mr_no" value="<?php echo $res['mr_no']; ?>" >
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
					<textarea rows=2 type="text" id="remarks" style="width:83%;" onchange='javascript: saveSWHeader();'><?php echo $res['remarks']; ?></textarea>
				</td>
				<td align=right width=50% valign=top>
					
					Transaction Total : &nbsp;&nbsp;<input style="width:200px;text-align:right;" type=text name="grandTotal" id="grandTotal" value="<?php echo number_format($res['amount'],2); ?>" readonly>

				</td>
			</tr>
			<tr>
				<td align=left colspan=2 style="padding-top: 10px;">
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
<div class="suggestionsBox" id="suggestions" style="display: none;">
	<div class="suggestionList" id="autoSuggestionsList">&nbsp;</div>
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