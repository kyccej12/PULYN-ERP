<?php
	session_start();
	include('handlers/_generics.php');
	$o = new _init();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="style/jquery.timepicker.css" rel="stylesheet" type="text/css" />
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="ui-assets/keytable/css/keyTable.jqueryui.css">
<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery.timepicker.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/page.jumpToData().js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/keytable/js/dataTables.keyTable.min.js"></script>
<script>

	$(document).ready(function() {
		var myTable = $('#itemlist').DataTable({
			"keys": true,
			"scrollY":  "300px",
			"select":	'single',
			"pagingType": "full_numbers",
			"bProcessing": true,
			"responsive": true,
			"sAjaxSource": "data/xray.templates.php",
			"scroller": true,
			"aoColumns": [
			  { mData: 'id' } ,
			  { mData: 'template_category' } ,
			  { mData: 'title' } ,
			  { mData: 'xray_type' } ,
			  { mData: 'template_owner' } ,
			  { mData: 'created' },
			  { mData: 'updated' },
			  { mData: 'uby' },
			  { mData: 'status' }
			],
			"aoColumnDefs": [
			    { "className": "dt-body-center", "targets": [3,5,6,7]},
			    { "targets": [0], "visible": false }
            ]
		});

		$('#itemlist tbody').on('dblclick', 'tr', function () {
			var data = myTable.row( this ).data();	
			parent.xrayTemplateDetails(data[0]);
		});


		$('#itemlist tbody').on('dblclick', 'tr', function () {
			
		});

	});
	
	function refreshList() {
		$('#itemlist').DataTable().ajax.url("data/xray.templates.php").load();
	}

	function updateTemplate() {
		var table = $("#itemlist").DataTable();		
			var id;
			$.each(table.rows('.selected').data(), function() {
				id = this[0];
			});

			if(!id) {
				parent.sendErrorMessage("Please select record to view.");
			} else {
				parent.xrayTemplateDetails(id);
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
<div id = "main">
	<table width="100%" cellspacing="0" cellpadding="0" style="padding-left: 5px; margin-bottom: 2px;">
		<tr>
			<td>
				<button class="ui-button ui-widget ui-corner-all" onClick="parent.xrayTemplateDetails('');">
					<span class="ui-icon ui-icon-plusthick"></span> Create Template
				</button>
				<button class="ui-button ui-widget ui-corner-all" onClick="updateTemplate();">
					<span class="ui-icon ui-icon-plusthick"></span> View Template Details
				</button>
				<button class="ui-button ui-widget ui-corner-all" onClick="refreshList();">
					<span class="ui-icon ui-icon-refresh"></span> Reload List
				</button>
			</td>
		</tr>
	</table>
	<table id="itemlist" style="font-size:11px;">
		<thead>
			<tr>
				<th></th>
				<th width="10%">Category</th>
				<th>Template Title</th>
				<th width=10%>Type</th>
				<th width=15%>Owner</th>
				<th width=10%>Created On</th>
				<th width=10%>Last Updated On</th>
				<th width=10%>Last Updated By</th>
				<th width=10%>Status</th>
			</tr>
		</thead>
	</table>
</div>

</body>
</html>