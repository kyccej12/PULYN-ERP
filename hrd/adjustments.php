<?php
	
	require_once "../handlers/_generics.php";
	$mydb = new _init;
		
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="../ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="../ui-assets/datatables/css/jquery.dataTables.css">
	<script language="javascript" src="../ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="../ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="../js/jquery.dialogextend.js"></script>
	<script type="text/javascript" charset="utf8" src="../ui-assets/datatables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="../ui-assets/datatables/js/dataTables.jqueryui.js"></script>
	<script type="text/javascript" charset="utf8" src="../ui-assets/datatables/js/dataTables.select.js"></script>
	<script>

		var UID = "";

		
		function init() {
			var xUID;
			var table = $("#itemlist").DataTable();
			var arr = [];
		   $.each(table.rows('.selected').data(), function() {
			   arr.push(this["rid"]);
		   });
		   
		   UID = arr[0];
		}	  
		
		
		$(document).ready(function() { 
			$("#date").datepicker(); 
			$("#adjdate").datepicker(); 
			$('#emp_name').autocomplete({
				source:'../suggestEmployee.php', 
				minLength:3,
				select: function(event,ui) {
					$("#emp_id").val(ui.item.emp_id);
				}
			});
		
			$('#itemlist').dataTable({
				"scrollY":  "300px",
				"select":	'single',
				"pagingType": "full_numbers",
				"bProcessing": true,
				"sAjaxSource": "listings.php?mod=adjustments&sid="+Math.random()+"",
				"aoColumns": [
				  { mData: 'rid' },
				  { mData: 'emp_id' },
				  { mData: 'emp_name' },
				  { mData: 'adjtype' },
				  { mData: 'amount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
				  { mData: 'period' },
				  { mData: 'remarks' },
				  { mData: 'adjustment_date' }
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [1,3,4,5]},
					{ "targets": [0,7], "visible": false }
				],
				"order": [[7,"desc"],[2,"asc"]]
			});	
		
		});
		
		function getPayperiods(ptype,selbox) {
			$.post("misc-data.php", { mod: "getPeriods", type: ptype, sid: Math.random() }, function(data) { document.getElementById(selbox).innerHTML = data; },"html");
		}
		
		function getEmployees(ptype,selbox){
			$.post("misc-data.php", { mod: "getEmployees", type: ptype, sid: Math.random() }, function(data) { document.getElementById(selbox).innerHTML = data; },"html");
		}
		
		function selectUID(obj) {
			gObj = obj;
			$(obj).closest("tr").siblings().removeClass("diffcolor");
			$(obj).toggleClass("diffcolor");
			tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); xUID = tmp_obj[1];
			
			var str = xUID.split("|");
				UID = str[0];
				ptype = str[1];
			
		}
		
		function viewRecord() {
			
			init();
			
			if(!UID) {
				parent.sendErrorMessage("Unable to retrieve record. Please select a record from the list, and once highlighted, press  \"<b><i>View Record Details</i></b>\" button again...");
			} else {
				
				$.post("misc-data.php", { mod: "getAdjustment", id: UID, sid: Math.random() }, function(ech) {
					
					$("#rid").val(ech['record_id']);
					$("#emp_name").val(ech['emp_name']);
					$("#emp_id").val(ech['emp_id']);
					$("#type").val(ech['adjustment_type']);
					$("#adjdate").val(ech['adjdate']);
					$("#amount").val(ech['amt']);
					$("#remarks").html(ech['remarks']);
					$("#is_taxable").val(ech['taxable']);
					
					$("#record").dialog({title: "Record Details", width: 440, resizable: false, modal: true, buttons: { 
						"Save Changes": function() {
							
							var msg = "";
							if($("#emp_id").val() == "") { msg = msg + "- Please select employee from the given list<br/>"; }
							if($("#remarks").val() == "") { msg = msg + "- Transaction remarks must be duly noted for future reference.<br/>"; }
							if($("#amount").val() != "") {
								var amt = parent.stripComma($("#amount").val());
								if(isNaN(amt) == true) { msg = msg + "- You have specified an invalid amount."; }
							}
							
							if(msg != '') {
								parent.sendErrorMessage(msg);
							} else {
								if(confirm("Are you sure you want to save changes made to this record?") == true) {
									$.post("misc-data.php", { mod: "updateAdjustment", rid: $("#rid").val(), emp_id: $("#emp_id").val(), type: $("#type").val(), date: $("#adjdate").val(), amount: $("#amount").val(), taxable: $("#is_taxable").val(), remarks: $("#remarks").val(), sid: Math.random() }, function() {
										alert("Record Successfully Updated!");
										parent.showAdjustments();
									});
								}
							}
						},
						"Close": function() { $("#record").dialog("close"); }
					}}).dialogExtend({
						"closable" : true,
						"maximizable" : false,
						"minimizable" : true
					});
				},"json");
			}
		}
		
		function newRecord() {
			$(document.irec)[0].reset(); $("#remarks").html('');
			$("#record").dialog({title: "New Record", width: 440, resizable: false, modal: true, buttons: {
				"Save Record": function() {
					var msg = "";
					if($("#emp_id").val() == "") { msg = msg + "- Please select employee from the given list<br/>"; }
					if($("#remarks").val() == "") { msg = msg + "- Transaction remarks must be duly noted for future reference.<br/>"; }
					if($("#amount").val() != "") {
						var amt = parent.stripComma($("#amount").val());
						if(isNaN(amt) == true) { msg = msg + "- You have specified an invalid amount."; }
					}
					
					if(msg != "") { parent.sendErrorMessage(msg); } else {
						$.post("misc-data.php", { mod: "newAdjustment", emp_id: $("#emp_id").val(), type: $("#type").val(), date: $("#adjdate").val(), amount: $("#amount").val(), taxable: $("#is_taxable").val(), remarks: $("#remarks").val(), sid: Math.random() }, function() {
							alert("Record Successfully Saved!");
							parent.showAdjustments();
						});
					}
				},
				"Close": function() { $("#record").dialog("close"); }
			}}).dialogExtend({
				"closable" : true,
				"maximizable" : false,
				"minimizable" : true
			});
		}
		
		function deleteRecord() {
			init();
			if(!UID) {
					parent.sendErrorMessage("Nothing to delete. Please select a record from the list, and once highlighted, press  \"<b><i>Delete Record</i></b>\" button again...");
			} else {
				if(confirm("Are you sure you want to delete this record?") == true) {
				
					$.post("misc-data.php", { mod: "deleteAdjustment", rid: UID, sid: Math.random() }, function() {
						alert("Record Successfully Deleted!");
						parent.showAdjustments();
					});
				}	
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
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
	<div id="main">
		<table cellspacing=0 cellpadding=0 width=100% align=center style="margin-top: 10px;">
			<tr>
				<td align=left style="padding-right: 20px;">
					<a href="#" class="topClickers" onClick="newRecord();"><img src="../images/icons/adduser.png" width=18 height=18 align=absmiddle />&nbsp;New Record</a>&nbsp;
					<a href="#" class="topClickers" onClick="viewRecord();"><img src="../images/icons/edit.png" width=18 height=18 align=absmiddle />&nbsp;View/Edit Selected Record</a>&nbsp;
					<a href="#" class="topClickers" onClick="deleteRecord();"><img src="../images/icons/delete.png" width=18 height=18 align=absmiddle />&nbsp;Delete Selected Record</a>
					<a href="#" class="topClickers" onClick="parent.showAdjustments();"><img src="../images/icons/refresh.png" width=18 height=18 align=absmiddle />&nbsp;Refresh List</a>
				</td>
			</tr>
		</table>
		<table id="itemlist" style="font-size:11px;">
			<thead>
				<tr>
					<th width=1%>RID</th>
					<th width=10%>EMP. ID</th>
					<th width=15%>EMP. NAME</th>
					<th width=10%>TYPE</th>
					<th width=10%>AMOUNT</th>
					<th width=20%>PERIOD</th>
					<th>REMARKS</th>
				</tr>
			</thead>
		</table>
	</div>
	 <div id="record" style="display: none;">
		<form name="irec" id="irec">
			<table width=100% cellspacing=2 cellpadding=0>
				<tr>
					<td class="bareThin" align=left width=40%>Employee Name :</td>
					<td align=left>
						<input type="text" name="emp_name" id="emp_name" class="inputSearch2" style="width: 80%; padding-left: 22px;">
					</td>
				</tr>
				<tr>
					<td class="bareThin" align=left width=40%>Employee ID :</td>
					<td align=left>
						<input type="hidden" name="rid" id="rid" value="">
						<input type="text" name="emp_id" id="emp_id" class="gridInput" style="width: 80%;" readonly>
					</td>
				</tr>
				<tr>
					<td class="bareThin" align=left width=40%>Type :</td>
					<td align=left>
						<select name="type" id="type" style="width: 80%; font-size: 11px; padding: 5px;">
							<option value="DB">- Debit Adjustment -</option>
							<option value="CR">- Credit Adjustment -</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="bareThin" align=left width=40%>Taxable Adjustment? </td>
					<td align=left>
						<input type="hidden" name="rid" id="rid" value="">
						<select name="is_taxable" id="is_taxable" style="width: 80%; font-size: 11px; padding: 5px;">
							<option value='Y'>- Yes -</option>
							<option value='N'>- No -</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="bareThin" align=left width=40%>Date of Adjustment :</td>
					<td align=left>
						<input type="text" name="adjdate" id="adjdate" style="width: 80%;">
					</td>
				</tr>
				<tr>
					<td class="bareThin" align=left width=40%>Amount :</td>
					<td align=left>
						<input type="text" name="amount" id="amount" style="width: 80%;">
					</td>
				</tr>
				<tr>
					<td class="bareThin" align=left width=40% valign=top>Other Remarks :</td>
					<td align=left><textarea style="width: 80%; font-size: 11px;" rows=1 name="remarks" id="remarks"></textarea></td>
				</tr>
			</table>
		</form>
	</div>
</body>
</html>
<?php mysql_close($con); ?>