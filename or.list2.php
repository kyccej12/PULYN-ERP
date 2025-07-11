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

		$(document).ready(function() {
			var myTable = $('#itemlist').DataTable({
				"scrollY":  "350px",
				"select":	'single',
				"pageLength": 50,
				"pagingType": "full_numbers",
				"pageLength": 50,
				"sAjaxSource": "data/orlist.php",
				"aoColumns": [
				  { mData: 'doc_no' } ,
				  { mData: 'dno' },
				  { mData: 'd8' },
				  { mData: 'or_no' },
				  { mData: 'so' },
				  { mData: 'cname' },
				  { mData: 'cashtype' },
				  { mData: 'remarks' },
				  { mData: 'amount_due', render: $.fn.dataTable.render.number(',', '.', 2, '') },
				  { mData: 'amount_paid', render: $.fn.dataTable.render.number(',', '.', 2, '') },
				  { mData: 'status' }
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [1,2,3,6,10] },
					{ className: "dt-body-right", "targets": [8,9] },
					{ "targets": [ 0], "visible": false }
				],
				"order": [[ 0, "desc" ]]
			});

			
			$('#itemlist tbody').on('dblclick', 'tr', function () {
				var data = myTable.row( this ).data();	
				parent.viewOR(data['doc_no']);
			});

		});

		function viewOR() {
			var table = $("#itemlist").DataTable();
			var doc_no;
			$.each(table.rows('.selected').data(), function() {
				doc_no = this["doc_no"];
			});
			
			if(!doc_no) {
				parent.sendErrorMessage("You have not selected any record yet!");
			} else {
				parent.viewOR(doc_no);
			}
		}

		function refreshList() {
			$('#itemlist').DataTable().ajax.url("data/orlist.php").load();
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
	<table width="100%" cellspacing="0" cellpadding="0" style="padding-left: 5px; margin-bottom:2px;">
		<tr>
			<td>
				<button class="ui-button ui-widget ui-corner-all" onClick="parent.viewOR('');">
					<span class="ui-icon ui-icon-plusthick"></span> Create Official Receipt
				</button>
				<button class="ui-button ui-widget ui-corner-all" onClick="viewOR();">
					<span class="ui-icon ui-icon-newwin"></span> Open Selected Record
				</button>
				<button class="ui-button ui-widget ui-corner-all" onClick="refreshList();">
					<span class="ui-icon ui-icon-refresh"></span> Reload List
				</button>
			</td>
		</tr>
	</table>
	<table id="itemlist" class="cell-border" style="font-size:11px;">
		<thead>
			<tr>
				<th></th>
				<th width=8%>DOC #</th>
				<th width=10%>DATE</th>
				<th width=6%>OR #</th>
				<th width=12%>SO #</th>
				<th width=12%>CHARGED TO</th>
				<th width=10%>CASH TYPE</th>
				<th>DOCUMENT REMARKS</th>
				<th width=10%>AMT DUE</th>
				<th width=8%>AMT PAID</th>
				<th width=8%>STATUS</th>
			</tr>
		</thead>
	</table>
</body>
</html>