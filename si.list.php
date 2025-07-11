<?php
	session_start();
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
	<script>
	
		function viewSI() {
			var table = $("#itemlist").DataTable();
			var arr = [];
			$.each(table.rows('.selected').data(), function() {  arr.push(this["docno"]);  });
			if(!arr[0]) {
				parent.sendErrorMessage("Please select record to view.");
			} else {
				parent.viewSI(arr[0]);
			}
		}
		
		$(document).ready(function() {
			$('#itemlist').dataTable({
				"scrollY":  "350px",
				"select":	'single',
				"pagingType": "full_numbers",
				"sAjaxSource": "data/invoicelist.php",
				"aoColumns": [
				  { mData: 'docno' },
				  { mData: 'idate' },
				  { mData: 'customer_name' },
				  { mData: 'terms' },
				  { mData: 'duedate' },
				  { mData: 'amount', render: $.fn.dataTable.render.number(',', '.', 2, '')},
				  { mData: 'remarks' },
				  { mData: 'status' },
				  { mData: 'cr' },
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [0,1,3,4,7,8] },
					{ className: "dt-body-right", "targets": [5] }
				],
				"order": [[ 0, "desc" ]]
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
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
	<table width=100% cellpadding=0 cellspacing=0 style="padding-left: 5px; margin-bottom: 5spx;">
		<tr>
			<td align=left>
				<a href="#" class="topClickers" onClick="parent.viewSI('');"><img src="images/icons/add-2.png" width=18 height=18 align=absmiddle />&nbsp;New Invoice</a>&nbsp;
				<a href="#" class="topClickers" onClick="viewSI();"><img src="images/icons/bill.png" width=16 height=16 align=absmiddle />&nbsp;View/Edit Invoice</a>&nbsp;
				<a href="#" class="topClickers" onClick="parent.showSI();"><img src="images/icons/refresh.png" width=16 height=16 align=absmiddle />&nbsp;Reload List</a>
			</td>
		</tr>
	</table>
	<table id="itemlist" style="font-size:11px;">
		<thead>
			<tr>
				<th>DOC. #</th>
				<th>DATE</th>
				<th width=20%>CUSTOMER NAME</th>
				<th>TERMS</th>
				<th>DATE DUE</th>
				<th>AMOUNT</th>
				<th width=25%>DOCUMENT REMARKS</th>
				<th>STATUS</th>
				<th>CR #</th>
			</tr>
		</thead>
	</table>
</body>
</html>