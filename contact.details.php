<?php
	include("handlers/initDB.php");
	session_start();

	$con = new myDB;
	
	if(isset($_GET['fid']) && $_GET['fid'] != "") { $res = $con->getArray("select * from contact_info where file_id='$_GET[fid]';"); }
	function getMod($def,$mod) {
		if($def == $mod) { echo "class=\"float2\""; }
	}
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
<link rel="stylesheet" type="text/css" href="style/style.css" />
<link rel="stylesheet" type="text/css" href="ui-assets/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
<script>
	$(document).ready(function() {
		$("#valid_until").datepicker();
		
		$('#sp_description').autocomplete({
			source:'suggestService.php', 
			minLength:3,
			select: function(event,ui) {
				$("#sp_code").val(ui.item.code);
				$("#sp_unit").val(decodeURIComponent(ui.item.unit));
				$("#sp_walkin").val(decodeURIComponent(ui.item.price));
			}
		});

		<?php 
			switch($_GET['mod']) {
				case "2":
					echo "$('#splist').dataTable({
						\"keys\": true,
						\"scrollY\":  \"240px\",
						\"select\":	\"single\",
						\"pagingType\": \"full_numbers\",
						\"bProcessing\": false,
						\"sAjaxSource\": \"data/contactsublist.php?mod=sp&cid=".$_GET['fid']."\",
						\"order\": [[ 1, \"asc\" ]],
						\"aoColumns\": [
						  { mData: 'record_id' },
						  { mData: 'code' },
						  { mData: 'description' },
						  { mData: 'unit' },
						  { mData: 'special_price', render: $.fn.dataTable.render.number(',', '.', 2, '') },
						  { mData: 'previous_price', render: $.fn.dataTable.render.number(',', '.', 2, '') },
						  { mData: 'isvalid' },
						  { mData: 'validuntil' }
						],
						\"aoColumnDefs\": [
							{className: \"dt-body-center\", \"targets\": [1,3,4,5,6]},
							{ \"targets\": [0], \"visible\": false }
						]
					});";
				break;
				case "3":
					echo "$('#ilist').dataTable({
						\"keys\": true,
						\"scrollY\":  \"210px\",
						\"select\":	\"single\",
						\"pagingType\": \"full_numbers\",
						\"bProcessing\": false,
						\"sAjaxSource\": \"data/contactsublist.php?mod=invoices&cid=".$_GET['fid']."\",
						\"aoColumns\": [
						  { mData: 'doc_no' } ,
						  { mData: 'invoice' } ,
						  { mData: 'idate' },
						  { mData: 'terms' },
						  { mData: 'remarks' },
						  { mData: 'amount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
						  { mData: 'applied_amount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
						  { mData: 'balance' }
						],
						\"aoColumnDefs\": [
							{className: \"dt-body-right\", \"targets\": [5,6,7]},
							{className: \"dt-body-center\", \"targets\": [1,2,3]},
							{ \"targets\": [0], \"visible\": false }
						]
					});";
				break;
				case "4":
					echo "$('#polist').dataTable({
						\"keys\": true,
						\"scrollY\":  \"240px\",
						\"select\":	\"single\",
						\"pagingType\": \"full_numbers\",
						\"bProcessing\": false,
						\"sAjaxSource\": \"data/contactsublist.php?mod=po&cid=".$_GET['fid']."\",
						\"order\": [[ 0, \"desc\" ]],
						\"aoColumns\": [
						  {	mData: 'po_no' },
						  { mData: 'mypo' },
						  { mData: 'po_date' },
						  { mData: 'terms_desc' },
						  { mData: 'remarks' },
						  { mData: 'amount', render: $.fn.dataTable.render.number(',', '.', 2, '') }
						],
						\"aoColumnDefs\": [
							{className: \"dt-body-right\", \"targets\": [5]},
							{className: \"dt-body-center\", \"targets\": [1,2,3]},
							{ \"targets\": [0], \"visible\": false }
						]
					});";
				break;
			}
		?>
	});

	function saveCInfo(fid) {
		if(confirm("Are you sure you want to save changes made to this record?") == true) {
			var msg = "";
			if($("#type").val() == "FSUPPLIER") {
				if($("#billing_address").val() == "") {
					var msg = msg + "- You have not specified foreign address of this foreign supplier<br/>"; 
				}
			} else {
				if($("#tradename").val() == "") { msg = msg + "- You did not specify customer/supplier name/trade name.<br/>"; }
				if($("#province").val() == "") { msg = msg + "- You did not specify Provincial Address for this customer/supplier<br/>"; }
				if($("#city").val() == "") { msg = msg + "- You did not specify City/Municipal Address for this customer/supplier<br/>"; }
			}

			if(msg!="") {
				parent.sendErrorMessage(msg);
			} else {
				var url = $(document.contactinfo).serialize();
				url = "mod=saveCInfo&"+url;
				$.post("src/sjerp.php", url);
				alert("Record Successfully Added or Updated!")
			}
		}
	}
	
	function deleteCust(fid) {
		if(confirm("Are you sure you want to delete this record?") == true) {
			$.post("src/sjerp.php", { mod: "deleteCust", fid: fid, sid: Math.random() }, function(){ "Customer Record Successfully Deleted!"; parent.closeDialog("#customerdetails"); parent.showCust(); });
		}	
	}

	function getCities(pid) {
		$.post("src/sjerp.php", { mod: "getCities", pid: pid, sid: Math.random() }, function(data) {
			$("#city").html(data);
		},"html");
	}
	
	function getBrgy(city) {
		$.post("src/sjerp.php", { mod: "getBrgy", city: city, sid: Math.random() }, function(data) {
			$("#brgy").html(data);
		},"html");
	}
	
	function changeMod(mod) {
		document.changeModPage.mod.value = mod;
		document.changeModPage.submit();
	}

	function addSpecialPrice() {

		$("#frmSpecialPrice").trigger("reset");
		$("sp_code").attr({readonly: false});
		$("#sp_description").attr({readonly: false});

		$("#specialPrice").dialog({title: "Special Price Details", width: 480, resizable: false, modal: true, buttons: {
				"Add Special Price": function() { 
					if(confirm("Are you sure you want add this file?") == true) {
						$.post("src/sjerp.php", { mod: "checkifSP", code: $("#sp_code").val(), cid: $("#fid").val(), sid: Math.random() }, function (spdata) {

							if(spdata == 'ok') {

								var msg = "";
								var sp_price = parent.stripComma($("#sp_specialprice").val());
								if(isNaN(sp_price) == true || $("#sp_specialprice").val() == '') {
									msg = msg + "- Invalid Special Price.<br/>"
								}
								if($("#with_validity").val() == 'Y') {
									if($("#valid_until").val() == '') {
										msg = msg + "- Please specify the validity period of the said special price.<br/>"
									}
								}

								if(msg == '') {
									$.post("src/sjerp.php", { 
											mod: "newSpecialPrice",
											cid: $("#fid").val(),
											code: $("#sp_code").val(),
											description: $("#sp_description").val(),
											unit: $("#sp_unit").val(),
											walkinprice: $("#sp_walkin").val(),
											spprice: $("#sp_specialprice").val(),
											isValid: $("#with_validity").val(),
											validUntil: $("#valid_until").val(),
											remarks: $("#sp_remarks").val(),
											sid: Math.random() 
										},function() { 
											alert("Record Successfully Saved!");
											changeMod(2);
										}
									);

								} else { parent.sendErrorMessage(msg); }
							} else { parent.sendErrorMessage("- Cannot save this record as new file as it appears an existing special price for this customer exists."); }

						},"html");
					}
				},
				"Cancel": function () { $(this).dialog("close"); }
			} 
		});
	}

	function updateSpecialPrice() {
		var table = $("#splist").DataTable();		
		var arr = [];
	    $.each(table.rows('.selected').data(), function() {
		   arr.push(this["record_id"]);
	    });

		if(!arr[0]) {
			parent.sendErrorMessage("Please select a record to update by clicking the line row...");
		} else {
			$.post("src/sjerp.php", { mod: "retrieveSpecialPrice", rid: arr[0], sid: Math.random() }, function(spdata) {
				$("#sp_description").val(spdata['description']);
				$("#sp_code").val(spdata['code']);
				$("#sp_unit").val(spdata['unit']);
				$("#sp_walkin").val(spdata['uprice']);
				$("#sp_specialprice").val(spdata['sprice']);
				$("#with_validity").val(spdata['with_validity']);
				$("#valid_until").val(spdata['validUntil']);
				$("#sp_remarks").val(spdata['remarks']);

				$("sp_code").attr({readonly: true});
				$("#sp_description").attr({readonly: true});

				$("#specialPrice").dialog({title: "Special Price Details", width: 480, resizable: false, modal: true, buttons: {
					"Update Record": function() {
						if(confirm("Are you sure you want save changes made to this file?") == true) {
							var msg = "";
							var sp_price = parent.stripComma($("#sp_specialprice").val());
							if(isNaN(sp_price) == true || $("#sp_specialprice").val() == '') {
								msg = msg + "- Invalid Special Price.<br/>"
							}
							if($("#with_validity").val() == 'Y') {
								if($("#valid_until").val() == '') {
									msg = msg + "- Please specify the validity period of the said special price.<br/>"
								}
							}

							if(msg == '') {
								$.post("src/sjerp.php", { 
										mod: "updateSpecialPrice",
										rid: arr[0],
										code: $("#sp_code").val(),
										description: $("#sp_description").val(),
										unit: $("#sp_unit").val(),
										walkinprice: $("#sp_walkin").val(),
										spprice: $("#sp_specialprice").val(),
										isValid: $("#with_validity").val(),
										validUntil: $("#valid_until").val(),
										remarks: $("#sp_remarks").val(),
										sid: Math.random() 
									},function() { 
										alert("Record Successfully Saved!");
										changeMod(2);
									}
								);

							} else { parent.sendErrorMessage(msg); }
						}


					 },
					"Cancel": function() { $(this).dialog("close"); }
				}});
			
			},"json");
		}
	}

	function removeSpecialPrice() {
		var table = $("#splist").DataTable();		
		var arr = [];
	    $.each(table.rows('.selected').data(), function() {
		   arr.push(this["record_id"]);
	    });

		if(!arr[0]) {
			parent.sendErrorMessage("Please select a record to remove by clicking the line row...");
		} else {
			if(confirm("Are you sure you want to remove this record?") == true) {
				$.post("src/sjerp.php", { mod: "removeSpecialPrice", rid: arr[0], sid: Math.random() }, function(){ alert("Special successfully removed."); changeMod(2); });
			}
		}
	}

	function viewPODetails() {
		var table = $("#polist").DataTable();		
		var arr = [];
	    $.each(table.rows('.selected').data(), function() {
		   arr.push(this["po_no"]);
	    });

		if(!arr[0]) {
			parent.sendErrorMessage("Please select a record to view by clicking the line row...");
		} else {
			parent.viewPO(arr[0]);
		}

	}
</script>
<style>
	.dataTables_wrapper {
		display: inline-block;
		font-size: 11px; padding: 3px;
		width: 99%; 
	}
	
	table.dataTable tr.odd { background-color: #f5f5f5;  }
	table.dataTable tr.even { background-color: white; }
	.dataTables_filter input { width: 250px; }
	</style>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<form name="contactinfo" id="merchandise">
	<input type="hidden" name="fid" id="fid" value="<?php echo $res['file_id']; ?>">
	<table width="100%" cellspacing="0" cellpadding="5" style="border-bottom: 1px solid black; margin-bottom: 5px;">
		<tr>
			<td align=left>
			<?php 
				if($_GET['mod'] == 1) {
					echo '<a href="#" onClick="saveCInfo(\''. $_GET['fid'] . '\');" class="topClickers"><img src="images/icons/floppy.png" width=18 height=18 align=absmiddle />&nbsp;Add/Save Changes Made</a>';
					if($_GET['fid'] || $_GET['fid'] != "") { 
						echo '&nbsp;&nbsp;<a href="#" onClick="deleteCust(\'' . $_GET['fid'] .'\');" class="topClickers"><img src="images/icons/delete.png" width=18 height=18 align=absmiddle />&nbsp;Delete Record</a>';
					} 
				}
			?>
			</td>
		</tr>
	</table>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" >
		<tr>
			<td width="20%" align=left class="spandix-l">
				Trade or Individual Name :
			</td>
			<td width="40%" align=left>
				<input type="text" id="tradename" name="tradename" class="nInput" style="width: 80%;" value="<?php echo $res['tradename']; ?>" />
			</td>
			<td width="10%" class="spandix-l">
				Type :
			</td>
			<td width="30%">
				<select id="type" name="type" style="width: 80%;" class="nInput" />
					<?php
						$ctQuery = $con->dbquery("select id, contacttype from options_ctype;");
						while($ctRow =  $ctQuery->fetch_array()) {
							echo "<option value='$ctRow[0]' ";
							if($ctRow[0] == $res['type']) { echo "selected"; }
							echo ">$ctRow[1]</option>";
						}
					
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td width="20%" align=left class="spandix-l">
				Business Style :
			</td>
			<td width="40%" align=left>
				<input type="text" id="bizstyle" name="bizstyle" class="nInput" style="width: 80%;" value="<?php echo $res['bizstyle']; ?>" />
			</td>
			<td width="10%" class="spandix-l"></td>
			<td width="30%" class="spandix-l"><input type="checkbox" name="status" id="status" <?php if($res['active'] == 'N') { echo "checked"; } ?> value="N">&nbsp;Inactive<br/><input type="checkbox" name="prospect" id="prospect" <?php if($res['prospect'] == 'Y') { echo "checked"; }?> value="Y">&nbsp; Prospect</td>
		</tr>
	</table>
	<table cellspacing=0 cellpadding=0 width=100% align=center style="margin-top: 10px;">
		<tr>
			<td style="padding: 0px 0px 1px 0px;">
				<div id="custmenu" align=left class="ddcolortabs">
					<ul class=float2>
						<li><a href="#" <?php getMod("1",$_GET[mod]); ?> onclick="javascript: changeMod(1);"><span id="tbbalance1">General</span></a></li>
						<?php if($_GET['fid'] != '') { ?>
						<li><a href="#" <?php getMod("2",$_GET[mod]); ?> onclick="javascript: changeMod(2);"><span id="tbbalance2">Special Rates</span></a></li>
						<li><a href="#" <?php getMod("3",$_GET[mod]); ?> onclick="javascript: changeMod(3);"><span id="tbbalance3">Trade Transactions</span></a></li>
						<li><a href="#" <?php getMod("4",$_GET[mod]); ?> onclick="javascript: changeMod(4);"><span id="tbbalance4">Purchase Orders</span></a></li>
						<?php } ?>
					</ul>
				</div>
			</td>
		</tr>
	</table>
	<?php switch($_GET['mod']) {  case "1": default: ?>
	<table width="100%" cellpadding=0 cellspacing=1 class="td_content" style="padding:10px;">
		<tr>
			<td width=20% class="spandix-l" valign=top>Lot No./Street #/Village :</td>
			<td width=80% colspan=3><textarea name="address" id="address" style="width: 100%;" rows=1><?php echo $res['address']; ?></textarea></td>
		</tr>
		<tr>
			<td width=20% class="spandix-l">Province :</td>
			<td width=30%>
				<select id="province" name="province" style="width: 80%;" class="nInput" onchange="getCities(this.value);" />
					<option value="">- Select Province -</option>
					<?php
						$q0 = $con->dbquery("select provCode, provDesc from options_provinces order by provDesc asc;");
						while($_0 = $q0->fetch_array()) {
							print "<option value='$_0[0]' "; if($_0[0] == $res['province']) { echo "selected"; }
							print ">$_0[1]</option>";
						}
						
					?>
				</select>
			</td>
			<td width=20% class="spandix-l">Contact Person :</td>
			<td width=30%>
				<input type="text" id="cperson" name="cperson" class="nInput" style="width: 80%;" value="<?php echo $res['cperson']; ?>" />
			</td>
		</tr>
		<tr>
			<td width=20% class="spandix-l">Town / City :</td>
			<td width=30%>
				<select id="city" name="city" style="width: 80%;" class="nInput" onChange="getBrgy(this.value);" />
					<option value="">- Select City -</option>
					<?php
						$q1 = $con->dbquery("select citymunCode, citymunDesc from options_cities where provCode = '$res[province]' order by citymunDesc asc;");
						while($_1 = $q1->fetch_array()) {
							print "<option value='$_1[0]' "; if($_1[0] == $res['city']) { echo "selected"; }
							print ">$_1[1]</option>";
						}
					?>
				</select>
			</td>
			<td width=20% class="spandix-l">Assigned Sales Rep :</td>
			<td width=30%>
				<select id="srep" name="srep" style="width: 80%;" class="nInput" />
					<option value="">None</option>
					<?php
						$srtq = $con->dbquery("select record_id, sales_rep from options_salesrep order by sales_rep;");
						while(list($srid,$srtd) = $srtq->fetch_array()) {
							echo "<option value='$srid' ";
							if($res['srep'] == $srid) { echo "selected"; }
							echo ">$srtd</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td width=20% class="spandix-l">Barangay :</td>
			<td width=30%>
				<select id="brgy" name="brgy" style="width: 80%;" class="nInput" />
					<option value="">- Select Province -</option>
					<?php
						$q0 = $con->dbquery("select brgyCode, brgyDesc from options_brgy where citymunCode='$res[city]' order by brgyDesc asc;");
						while($_0 = $q0->fetch_array()) {
							print "<option value='$_0[0]' "; if($_0[0] == $res['brgy']) { echo "selected"; }
							print ">$_0[1]</option>";
						}
						
					?>
				</select>
			</td>
			<td width=20% class="spandix-l">Credit Limit :</td>
			<td width=30%>
				<input type="text" id="climit" name="climit" class="nInput" style="width: 80%;" value="<?php echo number_format($res['credit_limit'],2); ?>" onfocus="if(this.value == '0.00') { this.value = ''; }" onblur="if(this.value=='') { this.value='0.00'; } if(isNaN(parent.stripComma(this.value)) == true) { parent.sendErrorMessage('Error: Invalid User Input!'); this.value='0.00'; this.focus(); } " />
			</td>
			<td width=20% class="spandix-l"></td>
			<td width=30%><input type="hidden" name="price_level" name="price_level"></td>
		</tr>
		<tr>
			<td width=20% class="spandix-l">Tel. No. :</td>
			<td width=30%><input type="text" id="telno" name="telno" class="nInput" style="width: 80%;" value="<?php echo $res['tel_no']; ?>" /></td>
			<td width=20% class="spandix-l">Credit Terms :</td>
			<td width=30%>
				<select id="terms" name="terms" style="width: 80%;" class="nInput" />
					<?php
						$tq = $con->dbquery("select terms_id, description from options_terms order by terms_id;");
						while(list($tid,$td) = $tq->fetch_array()) {
							echo "<option value='$tid' ";
							if($res['terms'] == $tid) { echo "selected"; }
							echo ">$td</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td width=20% class="spandix-l">Fax No. :</td>
			<td width=30%><input type="text" id="faxno" name="faxno" class="nInput" style="width: 80%;" value="<?php echo $res['fax_no']; ?>" /></td>
			<td width=20% class="spandix-l">Vatable :</td>
			<td width=30%>
				<select id="vatable" name="vatable" style="width: 80%;" class="nInput" />
					<option value="Y" <?php if($res['vatable'] == 'Y') { echo "selected"; } ?>>Yes</option>
					<option value="N" <?php if($res['vatable'] == 'N') { echo "selected"; } ?>>No</option>
				</select>
			</td>
		</tr>
		<tr>
			<td width=20% class="spandix-l">Email Address :</td>
			<td width=30%>
				<input type="text" id="email" name="email" class="nInput" style="width: 80%;" value="<?php echo $res['email']; ?>" />
			</td>
			<td width=20% class="spandix-l">Payee/Supplier's Bank Acct # :</td>
			<td width=30%><input type="text" id="bank_acct" name="bank_acct" class="nInput" style="width: 80%;" value="<?php echo $res['bank_acct']; ?>" /></td>
		</tr>
		<tr>
			<td width=20% class="spandix-l">T-I-N No. :</td>
			<td width=30%>
				<input type="text" id="tin_no" name="tin_no" class="nInput" style="width: 80%;" value="<?php echo $res['tin_no']; ?>" />
			</td>
			<td width=20% class="spandix-l"></td>
			<td width=30%><input type="hidden" name="price_level" name="price_level"><input type="hidden" name="acctValid" id="acctValid"></td>
		</tr>
		<tr>
			<td colspan=4 width=100% align=left><hr width=100% align=center></hr></td>
		</tr>
		<tr>
			<td width=20% class="spandix-l" valign=top>Complete Foreign Address :</td>
			<td width=80% colspan=3><textarea name="billing_address" id="billing_address" style="width: 100%;" rows=1><?php echo $res['billing_address']; ?></textarea></td>
		</tr>
		<tr>
			<td width=20% class="spandix-l" valign=top>Complete Shipping Address :</td>
			<td width=80% colspan=3><textarea name="shipping_address" id="shipping_address" style="width: 100%;" rows=1><?php echo $res['shipping_address']; ?></textarea></td>
		</tr>
	</table>
	<?php break; case "2": ?>
	<table id="splist" style="font-size:11px;">
		<thead>
			<tr>
				<th>RECORD ID</th>
				<th width=10%>CODE</th>
				<th>DESCRIPTION</th>
				<th width=8%>UNIT</th>
				<th width=15%>SPEC. PRICE</th>
				<th width=15%>PREVIOUS PRICE</th>
				<th width=15%>WITH VALIDITY</th>
				<th width=15%>VALID UNTIL</th>
			</tr>
		</thead>
	</table>
	<table>
		<tr>
			<td align=left colspan=2 style="padding-top:5px;">
				<a href="#" class="topClickers" onClick="javascript:addSpecialPrice();"><img src="images/icons/add-2.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Add Special Price</a>&nbsp;
				<a href="#" class="topClickers" onClick="javascript:updateSpecialPrice();"><img src="images/icons/edit.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Update Selected Record</a>&nbsp;
				<a href="#" class="topClickers" onClick="javascript:removeSpecialPrice();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Special Price</a>
			</td>
		</tr>
	</table>
	<?php break; case "3": ?>
	<table id="ilist" style="font-size:11px;">
		<thead>
			<tr>
				<th width=10%>XDOC</th>
				<th width=10%>DOC. #</th>
				<th width=10%>DATE</th>
				<th width=10%>TERMS</th>
				<th>TRANSACTION REMARKS</th>
				<th width=12%>AMOUNT</th>
				<th width=12%>AMT. PAID</th>
				<th width=12%>BAL. DUE</th>
			</tr>
		</thead>
	</table>
	<table>
		<tr>
			<td align=left colspan=2 style="padding-top:5px;">
				<a href="#" class="topClickers" onClick="javascript:viewInvoice();"><img src="images/icons/invoice.png" width=16 height=16 border=0 align="absmiddle">&nbsp;View Transaction Details</a>
			</td>
		</tr>
	</table>
	<?php break; case "4": ?>
	<table id="polist" style="font-size:11px;">
		<thead>
			<tr>
				<th></th>
				<th width=12%>PO #</th>
				<th width=12%>DATE</th>
				<th width=12%>TERMS</th>
				<th>TRANSACTION REMARKS</th>
				<th width=15%>AMOUNT</th>
			</tr>
		</thead>
	</table>
	<table>
		<tr>
			<td align=left colspan=2 style="padding-top:5px;">
				<a href="#" class="topClickers" onClick="javascript:viewPODetails();"><img src="images/icons/invoice.png" width=16 height=16 border=0 align="absmiddle">&nbsp;View Transaction Details</a>
			</td>
		</tr>
	</table>			
	<?php break; } ?>
</form>
<form name="changeModPage" id="changeModPage" action="contact.details.php" method="GET" >
	<input type="hidden" name="fid" id="fid" value="<?php echo $_GET['fid']; ?>">
	<input type="hidden" name="mod" id="mod">
</form>

<div id="specialPrice" style="display: none;">
	<form name="frmSpecialPrice" id="frmSpecialPrice">
		<input type = "hidden" name = "sp_id" id = "sp_id">
		<table width=100% cellspacing=2 cellpadding=0>
			<tr>
				<td class="bareThin" align=left width=40%>Description :</td>
				<td align=left>
					<input type="text" name="sp_description" id="sp_description" class="inputSearch2" style="width: 80%; padding-left: 22px;">
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Code :</td>
				<td align=left>
					<input type="text" name="sp_code" id="sp_code" class="gridInput" style="width: 80%;" readonly>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Unit :</td>
				<td align=left>
				<input type="text" name="sp_unit" id="sp_unit" class="gridInput" style="width: 80%;" readonly>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Walkin Price :</td>
				<td align=left>
				<input type="text" name="sp_walkin" id="sp_walkin" class="gridInput" style="width: 80%;" readonly>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Special Price :</td>
				<td align=left>
				<input type="text" name="sp_specialprice" id="sp_specialprice" class="gridInput" style="width: 80%;">
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>With Validity </td>
				<td align=left>
					<input type="hidden" name="rid" id="rid" value="">
					<select name="with_validity" id="with_validity" class="gridInput" style="width: 80%;">
						<option value='Y'>- Yes -</option>
						<option value='N'>- No -</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Valid Until :</td>
				<td align=left>
					<input type="text" name="valid_until" id="valid_until" style="width: 80%;">
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40% valign=top>Memo/Remarks :</td>
				<td align=left><textarea type="text" style="width: 80%; font-size: 11px;" rows=1 name = "sp_remarks" id = "sp_remarks"></textarea></td>
			</tr>
		</table>
	</form>
</div>

</body>
</html>
<?php mysql_close($con);