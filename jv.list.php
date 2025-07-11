<?php
	session_start();
	//include("includes/dbUSE.php");
	$today = date('Y-m-d');


	$rowsPerPage = 15;
	if(isset($_REQUEST['page'])) { if($_REQUEST['page'] <= 0) { $pageNum = 1; } else { $pageNum = $_REQUEST['page']; }} else { $pageNum = 1; }
	$offset = ($pageNum - 1) * $rowsPerPage;
	
	if(isset($_REQUEST['searchtext']) && !empty($_REQUEST['searchtext'])) { 
		$fs1 = " and (j_no = '$_REQUEST[searchtext]' || j_date = '$_REQUEST[searchtext]' || explanation like  '%$_REQUEST[searchtext]%') "; 
		if($_REQUEST['includeDetails'] == "Y") {
			$fs1 = $fs1 . " or j_no in (select j_no from journal_details where ref_no = '$_REQUEST[searchtext]' || acct = '$_REQUEST[searchtext]' || acct_desc like '%$_REQUEST[searchtext]%') ";
		}
	}
	//$numrows = getArray("select count(*) from journal_header where company = '$_SESSION[company]' and branch = '$_SESSION[branchid]' $fs1 ");
	//$maxPage = ceil($numrows[0]/$rowsPerPage);

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
		
		function viewJV() {
			var table = $("#itemlist").DataTable();
			var arr = [];
		    $.each(table.rows('.selected').data(), function() { arr.push(this["jno"]); });
			
			if(!arr[0]) {
				parent.sendErrorMessage("Please select record to view.");
			} else {
				parent.viewJV(arr[0]);
			}
		}
		
		$(document).ready(function() {
			$('#itemlist').dataTable({
				"scrollY":  "350px",
				"select":	'single',
				"pagingType": "full_numbers",
				"sAjaxSource": "data/jvlist.php",
				"aoColumns": [
				  { mData: 'jno' } ,
				  { mData: 'jd8' },
				  { mData: 'explanation' },
				  { mData: 'status' }
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [0,1,3] }
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
			<a href="#" onClick="parent.viewJV('');" class="topClickers"><img src="images/icons/add-2.png" width=18 height=18 align=absmiddle />&nbsp;New Voucher</a>&nbsp;&nbsp;
			<a href="#" onClick="viewJV();" class="topClickers"><img src="images/icons/bill.png" width=18 height=18 align=absmiddle />&nbsp;View/Edit Record</a>
			<a href="#" onClick="parent.showJV();" class="topClickers"><img src="images/icons/refresh.png" width=18 height=18 align=absmiddle />&nbsp;Reload List</a>
		</td>
	</table>
	<table id="itemlist" style="font-size:11px;">
		<thead>
			<tr>
				<th width=10%>VOUCHER #</th>
				<th width=10%>DATE</th>
				<th>EXPLANATION</th>
				<th width=15%>DOC STATUS</th>
			</tr>
		</thead>
	</table>
</body>
</html>