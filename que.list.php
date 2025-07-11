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
			"scrollY":  "300px",
			"select":	'single',
			"pagingType": "full_numbers",
			"bProcessing": true,
			"responsive": true,
			"sAjaxSource": "data/quelist.php",
			"scroller": true,
			<?php if($_GET['code'] != 'undefined') { ?>
				"initComplete": function() {
					this.api().page.jumpToData("<?php echo $_GET['code']; ?>",1);
				},
			<?php } ?>
			"aoColumns": [
			  { mData: 'id' } ,	
			  { mData: 'piority_no' } ,
			  { mData: 'priority' } , 
			  { mData: 'so' } ,
			  { mData: 'patient_name' },
			  { mData: 'gender' },
			  { mData: 'station' },
			  { mData: 'timequeued' },
			  { mData: 'timepicked' }, 
			  { mData: 'pickedby' },
			],
			"aoColumnDefs": [
			    { "className": "dt-body-center", "targets": [2,3,5,7,8]},
				{ "className": "dt-body-left", "targets": [4,6,9]},
			    { "targets": [0,1], "visible": false }
            ]
		});
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
		$('#itemlist').DataTable().ajax.url("data/phleblist.php").load();
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

	function grabPatient() {
		var table = $("#itemlist").DataTable();		
		var so;
		var pri_no;
	   	$.each(table.rows('.selected').data(), function() {
		    so = this['so'];
			pri_no = this['priority'];
	   	});

		if(!pri_no) {
			parent.sendErrorMessage("- It appears you have not selected any record from the given list yet...");
		} else {
			$.post("src/sjerp.php", { mod: "grabPatient", so_no: so, pri_no: pri_no, callStation: "LABORATORY", sid: Math.random()},function() {
				alert("Patient will be called out in a while... You may prepare the necessary extraction/phleb requirements for this patient...")
			});
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

	<table id="itemlist" style="font-size:11px;">
		<thead>
			<tr>
				<th width=1%></th>
				<th WIDTH=1%></th>
				<th width=10%>PRIORITY #</th>
				<th width=8%>SO #</th>
				<th width=12%>PATIENT NAME</th>
				<th width=8%>GENDER</th>
				<th width=12%>STATION CALLED</th>
				<th width=12%>TIME QUEUED</th>
				<th width=12%>TIME PICKED</th>
				<th>PICKED BY</th>
			</tr>
		</thead>
	</table>
</div>


</body>
</html>