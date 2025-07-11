<?php
	session_start();
	include("handlers/_generics.php");
	$con = new _init();
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
				"scrollY":  "380",
				"select":	'single',
				"searching": false,
				"paging": false,
				"info": false,
				"bSort": false,
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [0,1,2,5,9] },
					{ className: "dt-body-right", "targets": [7,8] }
				]
			});

			
			$('#itemlist tbody').on('dblclick', 'tr', function () {
				var data = myTable.row( this ).data();	
				parent.viewOR(data[0]);
			});

			$("#stxt").keyup(function(e) { 
				if(e.keyCode === 13 ) { searchRecord(); }
			});

		});

		function viewOR() {
			var table = $("#itemlist").DataTable();
			var doc_no;
			$.each(table.rows('.selected').data(), function() {
				doc_no = this[0];
			});
			
			if(!doc_no) {
				parent.sendErrorMessage("You have not selected any record yet!");
			} else {
				parent.viewOR(doc_no);
			}
		}

		function searchRecord() {
			$("#mainLoading").css("z-index","999");
			$("#mainLoading").show();

			var stxt = $("#stxt").val();
			document.frmSearch.searchtext.value = stxt;
			document.frmSearch.submit();
		}

		function jumpPage(page,stxt) {

			$("#mainLoading").css("z-index","999");
			$("#mainLoading").show();

			document.frmPaging.page.value = page;
			document.frmPaging.searchtext.value = stxt;
			document.frmPaging.submit();
		}


	</script>
	<style>
		.dataTables_wrapper {
			display: inline-block;
			font-size: 11px;
			width: 100%; 
		}
		
		table.dataTable tr.odd { background-color: #f5f5f5;  }
		table.dataTable tr.even { background-color: white; }
		.dataTables_filter input { width: 250px; }
	</style>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
<div id="">
<table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:2px;">
		<tr>
			<td>
				<button class="ui-button ui-widget ui-corner-all" onClick="parent.viewOR('');">
					<span class="ui-icon ui-icon-plusthick"></span> Create Official Receipt
				</button>
				<button class="ui-button ui-widget ui-corner-all" onClick="viewOR();">
					<span class="ui-icon ui-icon-newwin"></span> Open Selected Record
				</button>
				<button class="ui-button ui-widget ui-corner-all" onClick="parent.showOR();">
					<span class="ui-icon ui-icon-refresh"></span> Reload List
				</button>
			</td>
			<td align=right>
				<input name="stxt" id="stxt" type="text" class="gridInput" style="width: 240px; height: 24px;" value="<?php echo $_REQUEST['searchtext']; ?>" placeholder="Search Record">
				<button class="ui-button ui-widget ui-corner-all" onClick="javascript: searchRecord();">
					<span class="ui-icon ui-icon-search"></span> Search Record
				</button>
			</td>
		</tr>
	</table>
	<table class="cell-border" id="itemlist" style="font-size:11px;">
		<thead>
			<tr>
				<th width=8%>DOC #</th>
				<th width=8%>DATE</th>
				<th width=6%>OR #</th>
				<th width=12%>SO #</th>
				<th width=15%>CHARGED TO</th>
				<th width=10%>CASH TYPE</th>
				<th>DOCUMENT REMARKS</th>
				<th width=8%>AMT DUE</th>
				<th width=8%>AMT PAID</th>
				<th width=8%>STATUS</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$rowsPerPage = 50;
				if(isset($_REQUEST['page'])) { if($_REQUEST['page'] <= 0) { $pageNum = 1; } else { $pageNum = $_REQUEST['page']; }} else { $pageNum = 1; }
				$offset = ($pageNum - 1) * $rowsPerPage;
				$searchString = '';

				if($_REQUEST['searchtext'] && $_REQUEST['searchtext'] != '') {
					$term = htmlentities(trim($_REQUEST['searchtext']));
					
					list($dCount) = $con->getArray("select count(*) from or_details where (so_no = '$_REQUEST[searchtext]' || code = '$_REQUEST[searchtext]' || description like '%$term%' || pname like '%$term%');");
					if($dCount > 0) {
						$inQuery = '';
						$dCountQuery = $con->dbquery("select doc_no from or_details where (so_no = '$_REQUEST[searchtext]' || code = '$_REQUEST[searchtext]' || description like '%$term%' || pname like '%$term%');");
						while(list($searchDocNo) = $dCountQuery->fetch_array()) {
							$inQuery .= "'$searchDocNo',";
						}
						$inQuery .= "'0'";
						$inSO = "doc_no in ($inQuery)";
					} else { $inSO = "doc_no = '$_REQUEST[searchtext]'"; }
					
					$searchString .= " and ($inSO || or_no like '%$_REQUEST[searchtext]%' || scpwd_id = '$_REQUEST[searchtext]' || customer_name like '%$term%' || remarks like '%$term%' || checkno like '%$_REQUEST[searchtext]%' ||  DATE_FORMAT(doc_date,'%m/%d/%Y') like '%$_REQUEST[searchtext]%') ";
				
					list($totalRows) = $con->getArray("select format(count(*),0) from or_header;");
					$ender = "(filtered from $totalRows total entries)";
	
				} else { $ender = "entries"; }

				$query = "SELECT LPAD(doc_no,6,'0') AS doc_no, or_no, '' as so, DATE_FORMAT(doc_date,'%m/%d/%Y') AS d8, a.customer_code, a.customer_name, b.cashtype, remarks, format(amount_due,2) as amount_due, format(amount_paid,2) as amount_paid, `status` FROM or_header a LEFT JOIN options_cashtype b ON a.cashtype = b.id WHERE branch = '$_SESSION[branchid]' $searchString";
		
				/* Paging Section */
				$numrows = $con->getArray("select count(*) from ($query) a;");
				$maxPage = ceil($numrows[0]/$rowsPerPage);
				$_i = $con->dbquery("$query ORDER BY doc_no desc LIMIT $offset,$rowsPerPage");
				
				$showFrom = ($pageNum - 1) * $rowsPerPage + 1;
				$showTo = $showFrom + $rowsPerPage - 1;
				if($showTo > $numrows[0]) { $showTo = $numrows[0]; }

				while($row = $_i->fetch_array()) {
					
					$so = '';
					$dQuery = $con->dbquery("SELECT DISTINCT so_no, pname FROM or_details WHERE doc_no = '$row[doc_no]';");	
					while($dRow = $dQuery->fetch_array()) {
						if($dRow['so_no'] != '') {
							$so .= "<a href=\"#\" onclick=\"javascript: parent.viewSO('$dRow[so_no]');\" style=\"text-decoration: none; color: black;\" title=\"Click to View Sales Order Details\">$dRow[so_no] &raquo; $dRow[pname]</a><br/>";
						}
					}

					if($row['customer_code'] == 0) { list($cname) = $con->getArray("select distinct concat(pname, '(Patient)') from or_details where doc_no = '$row[doc_no]' limit 1"); } else { $cname = $row['customer_name']; }

					echo "<tr>
							<td>$row[doc_no]</td>
							<td>$row[d8]</td>
							<td>$row[or_no]</td>
							<td>$so</td>
							<td>$cname</td>
							<td>$row[cashtype]</td>
							<td>$row[remarks]</td>
							<td>$row[amount_due]</td>
							<td>$row[amount_paid]</td>
							<td>$row[status]</td>
					</tr>";

				}

			?>

		</tbody>
	</table>
	<table bgcolor="#e9e9e9" width=100% cellpadding=5 cellspacing=0>
		<tr>
			<?php if($numrows[0] > 0) { ?>
			<td>
				<span style="font-size: 11px; font-weight: bold;"><?php echo "Showing " . number_format($showFrom) . " to " . number_format($showTo) . " of " . number_format($numrows[0]) . " " . $ender ?></span>
			</td>
			<td align=right style="padding-right: 10px;"><?php if ($pageNum > 1) { ?><a href="javascript:jumpPage('<?php echo ($pageNum - 1); ?>','<?php echo $_REQUEST['searchtext']; ?>')" class="a_link" title="Previous Page"><span style="font-size: 18px;">&laquo;</span></a>&nbsp;<?php } ?>
				<span style="font-size: 11px;">Page <?php echo $pageNum; ?> of <?php echo $maxPage; ?></span>&nbsp;
					<?php if($pageNum != $maxPage) { ?><a href="javascript:jumpPage('<?php echo ($pageNum + 1); ?>','<?php echo $_REQUEST['searchtext']; ?>')" class="a_link" title="Next Page"><span style="font-size: 18px;">&raquo;</span></a><?php } ?>&nbsp;&nbsp;
						<?php if($maxPage > 1) { ?>
						<span style="font-size: 11px;">Jump To: </span>
							<select id="jpage" name="jpage" style="width: 40px; padding: 0px;" onchange="javascript:jumpPage(this.value,'<?php echo $_REQUEST['searchtext']; ?>');">
							<?php
									for ($x = 1; $x <= $maxPage; $x++) {
										echo "<option value='$x' ";
										if($pageNum == $x) { echo "selected"; }
										echo ">$x</option>";
									}
								?>
								</select>
					<?php } ?>
			</td> 
			<?php } ?>
		</tr>
	</table>
</div>

<div id="mainLoading" style="display:none; width:100%;height:100%;position:absolute;top:0;margin:auto;"> 
	<div style="background-color:white;width:10%;height:20%;;margin:auto;position:relative;top:100;">
		<img style="display:block;margin-left:auto;margin-right:auto;" src="images/ajax-loader.gif" width=100 height=100 align=absmiddle /> 
	</div>
	<div id="mainLoading2" style="background-color:white;width:100%;height:100%;position:absolute;top:0;margin:auto;opacity:0.8;"> </div>
</div>
<form name="frmSearch" id="frmSearch" action="or.list.php" method="POST">
	<input type="hidden" name="isSearch" id="isSearch" value="Y">
	<input type="hidden" name="searchtext" id="searchtext" value="<?php echo $_REQUEST['searchtext']; ?>">
</form>
<form name="frmPaging" id="frmPaging" action="or.list.php" method="POST">
	<input type="hidden" name="page" id="page" value="<?php echo $pageNum; ?>">
	<input type="hidden" name="searchtext" id="searchtext" value="<?php echo $_REQUEST['searchtext']; ?>">	
</form>
</body>
</html>