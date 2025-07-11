<?php	
	session_start();
	include("handlers/_generics.php");
	$mydb = new _init;
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['soa_no']) && $_REQUEST['soa_no'] != '') { 
		$res = $mydb->getArray("select *, lpad(customer_code,6,'0') as cid, lpad(soa_no,6,0) as soano, date_format(soa_date,'%m/%d/%Y') as d8 from soa_header where soa_no = '$_REQUEST[soa_no]' and branch = '$_SESSION[branchid]';");
		$cSelected = "Y"; $soa_no = $res['soano']; $status = $res['status']; $traceNo = $res['trace_no'];
	} else {  
		$status = "Active"; $traceNo = $mydb->generateRandomString();
	}

	list($pax) = $mydb->getArray("select count(*) from soa_details where soa_no = '$_REQUEST[soa_no]';");


	function setSOClickers($status,$soa_no,$uid,$dS,$urights) {
		global $mydb;
	
		switch($status) {
			case "Finalized":
				list($posted_by,$posted_on) = $mydb->getArray("select fullname as name, date_format(updated_on,'%m/%d/%Y %p') as date_posted from soa_header a left join user_info b on a.updated_by = b.emp_id where a.soa_no='$soa_no';");
				if($urights == "admin") {
					$headerControls = "<a href=\"#\" class=\"topClickers\" onclick=\"javascript: reopen();\"><img src='images/icons/edit.png' align=absmiddle width=16 height=16 />&nbsp;Set this Document to Active Status</a>&nbsp;";
				}
				$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onClick=\"javascript: print();\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Print Statement of Account</a>&nbsp;<a href=\"#\" class=\"topClickers\" onClick=\"javascript: printPharma();\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Print Pharmacy Statement of Account</a>&nbsp;<a href=\"#\" class=\"topClickers\" onClick=\"javascript: exportSOA();\"><img src=\"images/icons/excel.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Export to Excel</a>&nbsp;";
			break;
			case "Cancelled":
				if($urights == "admin") {
					$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:reuse();\" style=\"padding: 5px;\"><img src=\"images/icons/refresh.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Recycle this Document</a>&nbsp;";	
				}
			break;
			case "Active": default:
				$headerControls = "<a href=\"#\" class=\"topClickers\" onClick=\"javascript:finalize();\"><img src=\"images/icons/ok.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Finalize Sales Order</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:saveHeader();\"><img src=\"images/save.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Save Changes</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:browseSO();\"><img src=\"images/icons/options.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Browse Unbilled S.O</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:browseCSO();\"><img src=\"images/icons/options.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Browse Unbilled C.S.O</a>&nbsp;&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:browsePharmaSO();\"><img src=\"images/icons/options.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Browse Unbilled Pharmacy S.O</a>&nbsp;";
				if($urights == "admin" && $dS != 1) {
					$headerControls = $headerControls . "&nbsp;<a href=\"#\" class=\"topClickers\" onclick=\"javascript:cancel();\"><img src=\"images/icons/cancel.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Cancel this Document</a>";
				}
			break;
		}
	
		echo $headerControls;
	}
	
	function setSONavs($soa_no) {
		global $mydb;
		list($fwd) = $mydb->getArray("select soa_no from soa_header where soa_no > $soa_no and branch = '$_SESSION[branchid]' limit 1;");
		list($prev) = $mydb->getArray("select soa_no from soa_header where soa_no < $soa_no and branch = '$_SESSION[branchid]' order by soa_no desc limit 1;");
		list($last) = $mydb->getArray("select soa_no from soa_header where branch = '$_SESSION[branchid]' order by soa_no desc limit 1;");
		list($first) = $mydb->getArray("select soa_no from soa_header where branch = '$_SESSION[branchid]' order by soa_no asc limit 1;");
		if($prev)
			$nav = $nav . "<a href=# onclick=\"parent.viewSOA('$prev');\"><img src='images/resultset_previous.png'  title='Previous Record' /></a>";
		if($fwd) 
			$nav = $nav . "<a href=# onclick=\"parent.viewSOA('$fwd');\"><img src='images/resultset_next.png' 'title='Next Record' /></a>";
		echo "<a href=# onclick=\"parent.viewSOA('$first');\"><img src='images/resultset_first.png' title='First Record' /><a>" . $nav . "<a href=# onclick=\"parent.viewSOA('$last');\"><img src='images/resultset_last.png' title='Last Record' /></a>";
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
	<script language="javascript" src="js/soa.js?sid=<?php echo uniqid(); ?>"></script>
	<script>
	
		$(document).ready(function($) {
			
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
					"url": "soa.datacontrol.php",
					"data": { trace_no: "<?php echo $traceNo; ?>", mod: "retrieve", sid: Math.random() },
					"method": "POST"	
				},
				"scrollY":  "240",
				"select":	'single',
				"bProcessing": true,
				"searching": false,
				"paging": false,
				"info": false,
				"aoColumns": [
					{ mData: 'lid' },
					{ mData: 'sono' },
					{ mData: 'sdate' },
					{ mData: 'pname' },
					{ mData: 'code' },
					{ mData: 'description' },
					{ mData: 'unit' },
					{ mData: 'qty' },
					{ mData: 'price' },
					{ mData: 'amount' },
					{ mData: 'soa_no' },
				],
				"aoColumnDefs": [
					{ "className": "dt-body-center", "targets": [1,2,4,6,7,8,9]},
					{ "targets": [10], "visible": false }
				]
			});
			
			<?php if($status == 'Finalized' || $status == 'Cancelled') {
				echo "$(\"#xform :input\").prop('disabled',true);";
			} else { ?>
				$("#soa_date").datepicker();
			
			
			<?php } ?>
		});

		function redrawDataTable() {
			$('#details').DataTable().ajax.url("soa.datacontrol.php?mod=retrieve&trace_no=<?php echo $traceNo; ?>").load();
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
				<td width=80% class="upper_menus" align=left>
					<?php setSOClickers($status,$soa_no,$uid,$dS,$_SESSION['utype']); ?>
				</td>
				<td width=30% align=right style='padding-right: 5px;'><?php if($soa_no != '') { setSONavs($soa_no); } ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>

		<table border="0" cellpadding="0" cellspacing="1" width=100% class="td_content">
			<tr>
				<td width=60% valign=top>
					<table width=100% style="padding:0px 0px 0px 0px;">
						<tr><td height=2></td></tr>
						<tr>
							<td align="left" class="bareBold" style="padding-left: 35px;" valign=top>Billed To :</td>
							<td align=left>
								<table cellspacing=0 cellpadding=0 border=0 width=100%>
									<tr>
										<td width=25%><input type="text" id="customer_code" name="customer_code" value="<?php echo $res['cid']?>" class="inputSearch2" style="padding-left: 22px; width:98%;" onchange="javascript: saveHeader();"></td>
										<td width=75% align=right colspan=2><input class="gridInput" type="text" name="customer_name" id="customer_name" autocomplete="off" value="<?php echo $res['customer_name']; ?>" style="width: 100%;" readonly></td>
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
					</table>
				</td>
				<td valign=top>
					<table border="0" cellpadding="0" cellspacing="1" width=100%>
						<tr><td height=2></td></tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">SOA No.&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="soa_no" id="soa_no" value="<?php echo $soa_no; ?>" >
							</td>				
						</tr>
						<tr>
							<td align="left" width="50%" class="bareBold" style="padding-left: 35px;">Date&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:70%;" type=text name="soa_date" id="soa_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>">
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
					<th width=8%>SO #</th>
					<th width=8%>DATE</th>
					<th width=20%>PATIENT</th>
					<th width=8%>CODE</th>
					<th >PROCEDURE/SERVICE</th>
					<th width=8%>UNIT</th>
					<th width=8%>QTY</th>
					<th width=10%>PRICE</th>
					<th width=10%>AMT. DUE</th>
				</tr>
			</thead>
		</table>

		<table width=100% class="td_content">
			<tr>
				<td width=50%>
					Transaction Remarks: <br/>
					<textarea rows=2 type="text" id="remarks" style="width:83%;" onchange='javascript: saveHeader();'><?php echo $res['remarks']; ?></textarea>
				</td>
				<td align=right width=50% valign=top>
					
					Transaction Total : &nbsp;&nbsp;<input style="width:200px;text-align:right;" type=text name="grandTotal" id="grandTotal" value="<?php echo number_format($res['amount'],2); ?>" readonly> <br />
					No of Pax : &nbsp;&nbsp;<input style="width:200px;text-align:right;" type=text name="pax" id="pax" value="<?php echo $pax; ?>" readonly>		

				</td>
			</tr>
			<tr>
				<td align=left colspan=2 style="padding-top: 15px;">
					<?php if($status == 'Active' || $status == '') { ?>
						<!--a href="#" class="topClickers" onClick="javascript:addItem();"><img src="images/icons/add-2.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Add Item</a-->
						<!--a href="#" class="topClickers" onClick="javascript:updateItem();"><img src="images/icons/discount-icon.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Apply Line Discount</a-->
						<a href="#" class="topClickers" onClick="javascript:deleteItem();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Selected Item</a>
						<a href="#" class="topClickers" onClick="javascript:deleteAllitem();" style="padding-left:10px;"><img src="images/icons/trash.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove All Entries</a>
					<?php } ?>
				</td>
			</tr>
		</table>	
	</form>
</div>
<div id="solist" style="display:none;"></div>
</body>
</html>