<?php
	session_start();
	require_once("handlers/initDB.php");
	$con = new myDB;
	
	
	if(isset($_POST['searchFlag']) && $_POST['searchFlag'] == 'Y') {
		$searchString = '&search=Y';
		if($_POST['searchCust'] != '') { $searchString .= "&cust=".urlencode($_POST['searchCust']); }
		if($_POST['searchDtf'] != '') { $searchString .= "&dtf=$_POST[searchDtf]"; }
		if($_POST['searchDt2'] != '') { $searchString .= "&dt2=$_POST[searchDt2]"; }
		if($_POST['searchProject'] != '') { $searchString .= "&proj=$_POST[searchProject]"; }
		if($_POST['searchItem'] != '') { $searchString .= "&item=".urlencode($_POST['searchItem']); }
		if($_POST['searchStatus'] != '') { $searchString .= "&stat=$_POST[searchStatus]"; }
	}
	
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Redviper Ventures & Development Corp. ERP System Ver. 2.0</title>
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="style/dropMenu.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
	<script language="javascript" src="js/dropMenu.js"></script>
	<script>
		var id;

		function init() {
			var table = $("#itemlist").DataTable();
			$.each(table.rows('.selected').data(), function() {
				id = this["rfp_no"];
			});	
		}

		$(document).ready(function() {
			
			$("#searchDtf").datepicker(); $("#searchDt2").datepicker();
			
			$('#itemlist').dataTable({
				"scrollY":  "360px",
				"select":	'single',
				"pagingType": "full_numbers",
				"bProcessing": false,
				"language": {
				  "emptyTable": "No Records Found"
				},
				"iDisplayLength": 25,
				"order": [[ 1, "desc" ]],
				"sAjaxSource": "data/rfplist.php?1=1<?php echo $searchString; ?>",
				"aoColumns": [
				  { mData: 'rfp_no' },
				  { mData: 'rfp' },
				  { mData: 'rd8' },
				  { mData: 'payee' },
				  { mData: 'remarks' },
				  { mData: 'amount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
				  { mData: 'status' }
				  
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [1,2,6] },
					{ className: "dt-body-right", "targets": [5] },
					{ "targets": [0], "visible": false, "searchable": false }
				]
			});
		});
		
		function viewAP() {
			init();
			if(!id) {
				parent.sendErrorMessage("No File Selected");
			} else {
				parent.viewRFP(id);
			}
		}
		function viewDocInfo() {
			init();
			if(!id) { 
				parent.sendErrorMessage("Please select record to view."); 
			} else {
				$.post("rfp.datacontrol.php", {mod: "getDocInfo", rfp_no: id, sid: Math.random() }, function(data) { $("#docinfo").html(data); },"html")
				$("#docinfo").dialog({title: "Document Info >> RFP(Terms) # "+id+"", width: 340, height: 200, resizable: false });
			}
		}
		
		function showAdanceSearch() {
			$("#advanceSearch").dialog({
				title: "Advanced Search", 
				width: 420, 
				resizable: false,
				modal: true,
				buttons: {
					"Search Now": function() {
						document.advSearch.submit();
					},
					"Cancel": function() { $("#advanceSearch").dialog("close"); }
				}
			});
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
	<div id="docinfo" style="display: none;"></div>
	<div id="main">
		 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
			<tr>
				<td style="padding:0px;" valign=top>
					<div id="menu" class="chromestyle">
						<div id="submenu_1">
							<ul><li><a href="#" rel="dropmenu1" style="color: black; font-size: 11px;"><img src="images/icons/menu.png" width=12 height=16 align=absmiddle border=0 />&nbsp;&nbsp;Menu</a></li>
						</div>
					</div>
					<div id="dropmenu1" class="dropmenudiv"><a href="#" onClick="parent.viewRFP('');"><img src="images/icons/new_file.png" width=20 height=20 align=absmiddle border=0 />&nbsp;&nbsp;New File</a>
						<hr width=95% align=center style="border-color: #ffffff;"></hr><a href="#" onClick="viewAP();"><img src="images/icons/edit.png" width=20 height=20 align=absmiddle border=0 />&nbsp;&nbsp;Open Selected File</a>
						<hr width=95% align=center style="border-color: #ffffff;"></hr><a href="#" onClick="viewDocInfo();"><img src="images/icons/docinfo.png" width=20 height=20 align=absmiddle border=0 />&nbsp;&nbsp;Show File Info</a>
						<hr width=95% align=center style="border-color: #ffffff;"></hr><a href="#" onClick="parent.showRFP();"><img src="images/icons/refresh.png" width=20 height=20 align=absmiddle border=0 />&nbsp;&nbsp;Reload List</a>
						<hr width=95% align=center style="border-color: #ffffff;"></hr><a href="#" onClick="showAdanceSearch();"><img src="images/icons/search.png" width=20 height=20 align=absmiddle border=0 />&nbsp;&nbsp;Advanced Search</a>
					</div>
					<script type="text/javascript">cssdropdown.startDROP("menu")</script>
					<table id="itemlist" style="font-size:11px;">
						<thead>
							<tr>
								<th>ID</th>
								<th width=10%>RFP #</th>
								<th width=10%>DATE</th>
								<th width=15%>PAYEE</th>
								<th>DOCUMENT REMARKS</th>
								<th width=10%>AMOUNT</th>
								<th width=10%>STATUS</th>
							</tr>
						</thead>
					</table>
				</td>
			</tr>
		 </table>
	</div>
	<div id="advanceSearch" style="display: none;">
		<form name="advSearch" id="advSearch" method="POST" action="rfp.master.php">
			<input type="hidden" name="searchFlag" id="searchFlag" value="Y">
			<table width=100% cellpadding=0 cellspacing=0 class="td_content">
				<tr>
					<td width=40% class="spandix-l">Payee Name :</td>
					<td><input type="text" class="gridInput" id="searchCust" name="searchCust" style="width: 80%;"></td>
				</tr>
				<tr><td height=2></td></tr>
				<tr>
					<td width=40% class="spandix-l">Covered Period :</td>
					<td><input type="text" class="gridInput" name="searchDtf" id="searchDtf" style="width: 80%;" value=""></td>
				</tr>
				<tr><td height=2></td></tr>
				<tr>
					<td width=40% class="spandix-l"></td>
					<td><input type="text" class="gridInput" name="searchDt2" id="searchDt2" style="width: 80%;" value=""></td>
				</tr>
				<tr><td height=2></td></tr>
				<tr>
					<td width=40% class="spandix-l">Requestor :</td>
					<td><input type="text" class="gridInput" name="searchItem" id="searchItem" style="width: 80%;"></td>
				</tr>
				<tr><td height=2></td></tr>
				<tr>
					<td width=40% class="spandix-l">APV Details :</td>
					<td>
						<select class="gridInput" name="searchProject" id="searchProject" style="width: 80%; font-size: 11px;">
							<?php 
								$proj = $con->dbquery("SELECT a.proj_id,a.proj_code,a.proj_name FROM rvdc.options_project a where archived = 'N' AND company = 'RVDC' order by a.proj_name;");
								$poption.="<option value = ''>- All Projects -</option>";
								while(list($pid,$pcode,$pname) = $proj->fetch_array()) {
									if($res['proj_name']==$pid){ $selc = "selected";	} else { $selc = ""; }
									$poption .= "<option value = '$pid' $selc >$pname</option>";
								}
								echo $poption;
								unset($proj);
							?>
						</select>
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr>
					<td width=40% class="spandix-l">File Status :</td>
					<td>
						<select class="gridInput" name="searchStatus" id="searchStatus" style="width: 80%; font-size: 11px;">
							<option value=''>- All -</option>
							<option value='Active'>Active Document</option>
							<option value='Cancelled'>Cancelled Document</option>
							<option value='Finalized'>Finalized Document</option>
						</select>
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
</html>