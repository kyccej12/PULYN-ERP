<?php
	session_start();
	
	/* Added for Advance Search Function */
	if($_POST['sflag'] == 'Y') {
		if($_POST['stxt_description'] != '') { $b = "&idesc=$_POST[stxt_description]"; }
		if($_POST['stxt_dtf'] != '' && $_POST['stxt_dt2'] != '') {
			$c = "&dtf=".formatDate($_POST['stxt_dtf'])."&dt2=".formatDate($_POST['stxt_dt2']);
		} else {
			if($_POST['stxt_dtf'] != '') { $c = "&doc_date=".formatDate($_POST['stxt_dtf']); } 
			if($_POST['stxt_dt2'] != '') { $c = "&doc_date= '".formatDate($_POST['stxt_dt2']); }
		}
	}
	
?>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>OMDC Prime Medical Diagnostics Corp.</title>
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="ui-assets/keytable/css/keyTable.jqueryui.css">
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/page.jumpToData().js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/keytable/js/dataTables.keyTable.min.js"></script>
	<script>
		
		$(document).ready(function() {
			var myTable = $('#itemlist').DataTable({
				"sAjaxSource": "data/solist.php",
				"scrollY":  "350px",
				"select":	'single',
				"pageLength": 50,
				"pagingType": "full_numbers",
				"order": [[ 1, "desc" ]],
				"aoColumns": [
				  { mData: 'so_no' },
				  { mData: 'myso' },
				  { mData: 'soa_no' },
				  { mData: 'sdate' },
				  { mData: 'patient_name' },
				  { mData: 'customer' },
				  { mData: 'terms_desc' },
				  { mData: 'amount' },
				  { mData: 'remarks' },
				  { mData: 'status' },
				  { mData: 'cstat' }
				],
				"aoColumnDefs": [
					{ "className": "dt-body-center", "targets": [1,2,5,6,8] },
					{ "targets": [ 0], "visible": false }
				]
			});

			$('#itemlist tbody').on('dblclick', 'tr', function () {
				var data = myTable.row( this ).data();	
				parent.viewSO(data['so_no']);
			});

		});
		
		function viewSO() {
			var table = $("#itemlist").DataTable();		
			var so_no;
			$.each(table.rows('.selected').data(), function() {
				so_no = this["so_no"];
			});

			if(!so_no) {
				parent.sendErrorMessage("Please select record to view.");
			} else {
				parent.viewSO(so_no);
			}
		}

		function searchRecord() {
			$("#stxt_dtf").datepicker(); $("#stxt_dt2").datepicker();
			$("#searchDiv").dialog({
				title: "Search Record", 
				width: 400,
				resizable: false, 
				modal: true, 
				buttons: {
					"Search Record": function() {
						document.getElementById('frmSearch').submit();
					},
					"Close": function() { $(this).dialog("close"); }
				}
			});
		}

		function refreshList() {
			$('#itemlist').DataTable().ajax.url("data/solist.php").load();
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
				<button class="ui-button ui-widget ui-corner-all" onClick="parent.viewSO('');">
					<span class="ui-icon ui-icon-plusthick"></span> Create Sales Order
				</button>
				<button class="ui-button ui-widget ui-corner-all" onClick="viewSO();">
					<span class="ui-icon ui-icon-newwin"></span> Open Selected Sales Order
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
				<th width=6%>SO #</th>
				<th width=6%>SOA #</th>
				<th width=6%>DATE</th>
				<th width=15%>PATIENT NAME</th>
				<th width=15%>BILL TO</th>
				<th width=6%>TERMS</th>
				<th width=8%>AMOUNT</th>
				<th>DOCUMENT REMARKS</th>
				<th width=10%>DOC. STATUS</th>
				<th width=20%>ORDER STATUS</th>
			</tr>
		</thead>
	</table>
    <div id="searchDiv" style="display: none;">
		<form name = "frmSearch" id = "frmSearch" method = "POST" action = "phy.list.php">
			<input type = "hidden" name = "sflag" id = "sflag" value = "Y">
			<table width = "100%" cellpading = 0 cellspacing = 0>
				<tr>
					<td class="spandix-l">Date Covered :</td>
					<td ><input type="text" style="width:80%;" class="nInput" name="stxt_dtf" id="stxt_dtf"></td>
				</tr>
				<tr>
					<td class="spandix-l"></td>
					<td ><input type="text" style="width:80%;" class="nInput" name="stxt_dt2" id="stxt_dt2"></td>
				</tr>
				<tr>
					<td class="spandix-l">Item Description :</td>
					<td ><input type="text" style="width:80%;" class="nInput" name="stxt_description" id="stxt_description"></td>
				</tr>
			</table>
		</form>
	</div>
	</body>
</html>