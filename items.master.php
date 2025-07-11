<?php
	session_start();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Opon Medical Diagnostic Corp.</title>
<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="ui-assets/keytable/css/keyTable.jqueryui.css">
<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/page.jumpToData().js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/keytable/js/dataTables.keyTable.min.js"></script>
<script>
	
	function showPrintMaster() {
		parent.showFilterDiv();
	}
	
	function retrieveGroups(type) {
		if(type != "") {
			if(type == 1 || type == 2 || type == 4) {
				$.post("src/sjerp.php", { mod: "getGroups", type: type, sid: Math.random() }, function(data) {
					$("#item_group").html(data);
					$("#item_code").val('');	
				},"html");
			} else { $("#item_group").html('<option value="">- All Groups -</option>'); }
		} else {
			$("#item_group").html('<option value="">- All Groups -</option>');
		}
	}
	
	function printMaster() {
		window.open("reports/itemmaster.php?category="+$("#item_mgroup").val()+"&group="+$("#item_group").val()+"&sid="+Math.random()+"","Item Master List","location=1,status=1,scrollbars=1,width=640,height=720");
	}
	
	function editRecord(){
		var table = $("#itemlist").DataTable();		
		var arr = [];
	   $.each(table.rows('.selected').data(), function() {
		   arr.push(this["record_id"]);
	   });
	  
		if(!arr[0] || arr[0] == "undefined") {
			parent.sendErrorMessage("Please select a record from the list, and once highlighted, click \"<b><i>Edit Selected Record</i></b>\" again...");
		} else {
			parent.showItemInfo(arr[0]);	
		}
	}


	$(document).ready(function() {
		var myTable = $('#itemlist').DataTable({
			"keys": true,
			"scrollY":  "300px",
			"select":	'single',
			"pageLength": 50,
			"pagingType": "full_numbers",
			"bProcessing": true,
			"responsive": true,
			"sAjaxSource": "data/itemlist.php",
			"scroller": true,
			"order": [[ 2, "asc" ],[1,"asc"]],
			<?php if($_GET['icode'] != 'undefined') { ?>
				"initComplete": function() {
					this.api().page.jumpToData("<?php echo $_GET['icode']; ?>",1);
				},
			<?php } ?>
			"aoColumns": [
			  { mData: 'record_id' } ,
			  { mData: 'item_code' } ,
			  { mData: 'supplier' } ,
			  { mData: 'description' },
			  { mData: 'brand' },
			  { mData: 'unit' },
			  { mData: 'unit_cost', render: $.fn.dataTable.render.number(',', '.', 2, '') },
			  { mData: 'mgroup' },
			  { mData: 'sgroup' },
			  { mData: 'qty_onhand' },
			],
			"aoColumnDefs": [
              { className: "dt-body-right", "targets": [6,]},
			  { className: "dt-body-center", "targets": [1,5]},
			  { "targets": [0], "visible": false }
            ]
		});
	});
	
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
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td style="padding:0px;" valign=top>
			<table width="100%" cellspacing="0" cellpadding="0" style="padding-left: 5px; margin-bottom: 5px;">
				<tr>
					<td>
						<a href="#" class="topClickers" onClick="parent.showItemInfo('');"><img src="images/icons/add.png" width=18 height=18 align=absmiddle />&nbsp;Add New Item</a>&nbsp;&nbsp;
						<a href="#" id="edit" class="topClickers" onClick="editRecord();"><img src="images/icons/edit.png" width=18 height=18 align=absmiddle />&nbsp;Edit Selected Record</a>&nbsp;&nbsp;
						<a href="#" class="topClickers" onClick="parent.showItems();"><img src="images/icons/refresh.png" width=18 height=18 align=absmiddle />&nbsp;Refresh List</a>&nbsp;
						<!-- <a href="#" class="topClickers" onClick="showPrintMaster();"><img src="images/icons/print.png" width=18 height=18 align=absmiddle />&nbsp;Print Master List</a> -->
					</td>
				</tr>
			</table>
			<table class="cell-border" id="itemlist" style="font-size:11px;">
				<thead>
					<tr>
						<th>RECORD ID</th>
						<th width=10%>ITEM CODE</th>
						<th width=10%>SUPPLIER</th>
						<th>DESCRIPTION</th>
						<th width=12%>BRAND</th>
						<th width=8%>UNIT</th>
						<th width=12%>UNIT COST</th>
						<th width=15%>CATEGORY</th>
						<th width=15%>GROUP</th>
						<th width=10%>ON-HAND</th>
					</tr>
				</thead>
			</table>
		</td>
	</tr>
 </table>
  <div id="masterDiv" style="display:none;">	
	<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content" style="padding: 10px;">
		<tr>
			<td class="spandix-l" width="35%">Category :</td>
			<td align="left">
				<select name="item_mgroup" id="item_mgroup" style="width:  80%; font-size: 11px;" class="nInput" onchange="retrieveGroups(this.value);">
					<option value="">- All Categories -</option>
					<?php
						$mit = mysql_query("select mid,mgroup from options_mgroup;");
						while(list($o,$oo) = mysql_fetch_array($mit)) {
							echo "<option value='$o'>$oo</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4 colspan="2"></td></tr>
		<tr>
			<td class="spandix-l" width="35%">Inventory Group :</td>
			<td align="left">
				<select name="item_group" id="item_group" style="width: 80%; font-size: 11px;" class="nInput">
					<option value="">- All Groups -</option>
					<?php
						$iut = mysql_query("select `group`,group_description from options_igroup order by group_description asc;");
						while(list($t,$tt) = mysql_fetch_array($iut)) {
							echo "<option value='$t'>$tt</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4 colspan="2"></td></tr>
		<tr><td colspan=2><hr></hr></td></tr>
		<tr>
			<td align=center colspan=2>
				<button onClick="printMaster();" class="buttonding" style="font-size: 11px;"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Generate Report</button>
			</td>
		</tr>
	</table>
</div>
</body>
</html>