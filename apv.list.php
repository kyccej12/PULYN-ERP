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
		function viewAP() {
			var table = $("#itemlist").DataTable();
			var arr = [];
		    $.each(table.rows('.selected').data(), function() { arr.push(this["apv"]); });
			
			if(!arr[0]) {
				parent.sendErrorMessage("No record selected. Please select a line record to view, and then press \"View Voucher Details\" button...");
			} else {
				parent.viewAP(arr[0]);
			}
		}
		
		$(document).ready(function() {
			$('#itemlist').dataTable({
				"scrollY":  "350px",
				"select":	'single',
				"pageLength": 50,
				"pagingType": "full_numbers",
				"sAjaxSource": "data/apvlist.php",
				"aoColumns": [
				  { mData: 'apv' } ,
				  { mData: 'ad8' },
				  { mData: 'sname' },
				  { mData: 'amount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
				  { mData: 'remarks' },
				  { mData: 'status' },
				  { mData: 'docs' },
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [0,1,5,6] },
					{ className: "dt-body-right", "targets": [3] }
				],
				"order": [[ 1, "desc" ]]
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
	<table width=100% cellpadding=0 cellspacing=0 style="padding-left: 5px; margin-bottom: 2px;">
		<tr>
			<td align=left>
				<a href="#" onClick="parent.viewAP('');" class="topClickers"><img src="images/icons/add.png" width=18 height=18 align=absmiddle />&nbsp;New Voucher</a>&nbsp;&nbsp;
				<a href="#" onClick="viewAP();" class="topClickers"><img src="images/icons/bill.png" width=16 height=16 align=absmiddle />&nbsp;View/Edit Voucher</a>&nbsp;&nbsp;
				<a href="#" class="topClickers" onClick="parent.showAPVList();"><img src="images/icons/refresh.png" width=18 height=18 align=absmiddle />&nbsp;Reload List</a>
			</td>
		</tr>
	</table>
	<table class="cell-border" id="itemlist" style="font-size:11px;">
		<thead>
			<tr>
				<th width=8%>APV #</th>
				<th width=8%>DATE</th>
				<th width=22%>PAYEE</th>
				<th width=12%>AMOUNT DUE</th>
				<th>DOCUMENT REMARKS</th>
				<th width=10%>STATUS</th>
				<th width=12%>DOCS APPLIED</th>
			</tr>
		</thead>
	</table>
</body>
</html>