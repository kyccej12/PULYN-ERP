<?php
	session_start();
	include("handlers/_generics.php");
	$o = new _init;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
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
			"keys": true,
			"scrollY":  "340",
			"select":	'single',
			"pagingType": "full_numbers",
			"bProcessing": true,
			"responsive": true,
			"sAjaxSource": "data/appointmentlist.php",
			"scroller": true,
			<?php if($_GET['code'] != 'undefined') { ?>
				"initComplete": function() {
					this.api().page.jumpToData("<?php echo $_GET['code']; ?>",1);
				},
			<?php } ?>
			"aoColumns": [
			  { mData: 'id' } ,
			  { mData: 'patient_name' } ,
			  { mData: 'gender' } ,
			  { mData: 'bday' },
			  { mData: 'contact_no' },
			  { mData: 'schedule' },
			  { mData: 'slot' },
			  { mData: 'request' }, 
			  { mData: 'request_type' },
			  { mData: 'preferred_doctor' },
              { mData: 'status' }
			],
			"aoColumnDefs": [
			    { "className": "dt-body-center", "targets": [1,2,4,5,6,7,9]},
				{ "className": "dt-body-left", "targets": [8]},
			    { "targets": [0], "visible": false }
            ]
		});

		function myFunction() {
		  myVar = setInterval(alertFunc, 10000);
		}


	});
	
	function editRecord(){
		var table = $("#itemlist").DataTable();		
		var arr = [];
	   $.each(table.rows('.selected').data(), function() {
		   arr.push(this["id"]);
	   });
	  
		if(!arr[0] || arr[0] == "undefined") {
			parent.sendErrorMessage("Please select a record from the list, and once highlighted, click \"<b><i>Edit Selected Record</i></b>\" again...");
		} else {
			parent.showServiceInfo(arr[0]);	
		}
	}

	function refreshList() {
		$('#itemlist').DataTable().ajax.url("data/appointmentlist.php").load();
	}

	function collectSample() {
		var table = $("#itemlist").DataTable();		
		var lid;
		var so;
	   	$.each(table.rows('.selected').data(), function() {
		    lid = this["id"];
		    so_no = this['so'];
	   	});

		if(!lid) {
			parent.sendErrorMessage("- It appears you have not selected any orders from the given list yet...");
		} else {
			parent.collectSample(lid,so_no);
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

	<table width="100%" cellspacing="0" cellpadding="0" style="padding-left: 5px; margin-bottom: 5px;">
		<tr>
			<td>
				<a href="#" class="topClickers" onClick="parent.makeAppointment();"><img src="images/icons/add-2.png" width=18 height=18 align=absmiddle />&nbsp;Make Appointment</a>&nbsp;
				<a href="#" class="topClickers" onClick="refreshList()"><img src="images/icons/refresh.png" width=18 height=18 align=absmiddle />&nbsp;Refersh List</a>&nbsp;
			</td>
		</tr>
	</table>
	<table id="itemlist" style="font-size:11px;">
		<thead>
			<tr>
				<th></th>
				<th width=15%>PATIENT</th>
				<th width=7%>GENDER</th>
				<th width=5%>AGE</th>
				<th width=8%>CONTACT #</th>
				<th width=12%>DATE</th>
				<th width=8%>TIME</th>
				<th width=10%>CATEGORY</th>
				<th width=12%>REQUEST TYPE</th>
				<th width=12%>PREFERRED DOCTOR</th>
				<th>STATUS</th>
			</tr>
		</thead>
	</table>

</div>

</body>
</html>