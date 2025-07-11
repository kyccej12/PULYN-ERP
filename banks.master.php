<?php
	session_start();
	include("includes/dbUSE.php");
		
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

	var UID = "";
	
	function getBank() {
		var table = $("#itemlist").DataTable();
		var arr = [];
	    $.each(table.rows('.selected').data(), function() {  arr.push(this["bid"]);  });
		if(!arr[0] || arr[0] == "undefined") {
			parent.sendErrorMessage("Unable to retrieve record. Please select a record from the list, and once highlighted, press  \"<b><i>View User Information</i></b>\" button again...");
		} else {
			parent.showBankDetails(arr[0]);
		}
	}

	$(document).ready(function() {
	    $('#itemlist').dataTable({
			"scrollY":  "330px",
			"select":	'single',
			"pagingType": "full_numbers",
			"bProcessing": true,
			"sAjaxSource": "data/banklist.php",
			"aoColumns": [
			  { mData: 'bid' } ,
			  { mData: 'bank_name' },
			  { mData: 'bank_address' },
			  { mData: 'tel_no' },
			  { mData: 'acct_type' },
			  { mData: 'acct' }
			],
			"aoColumnDefs": [
				{ className: "dt-body-center", "targets": [0,3,4]}
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
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
			<table width="100%"  cellspacing="0" cellpadding="0" style="padding-left: 5px; margin-bottom: 5px;">
				<tr>
					<td>
						<a href="#" class="topClickers" onClick="parent.showBankDetails('');"><img src="images/icons/add-2.png" width=18 height=18 align=absmiddle />&nbsp;Add Bank Account</a>&nbsp;&nbsp;
						<a href="#" class="topClickers" onClick="getBank();"><img src="images/icons/dbcr.png" width=18 height=18 align=absmiddle />&nbsp;View/Edit Selected Record</a>
					</td>
				</tr>
			</table>
			<table id="itemlist" style="font-size:11px;">
				<thead>
					<tr>
						<th width=10%>FILE ID</th>
						<th width=20%>BANK NAME</th>
						<th>ADDRESS</th>
						<th width=15%>TEL NO</th>
						<th width=15%>ACCT TYPE</th>
						<th width=15%>GL CODE</th>
					</tr>
				</thead>
			</table>
		</td>
	</tr>
 </table>
</body>
</html>
<?php mysql_close($con);