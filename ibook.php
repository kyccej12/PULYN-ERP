<?php
	
	session_start();
	include("includes/dbUSE.php");
	$today = date('Y-m-d');
	
	if(isset($_REQUEST['searchtext']) && !empty($_REQUEST['searchtext'])) { 
		$xsearch = " and (item_code like '%$_REQUEST[searchtext]%' || description like '%$_REQUEST[searchtext]%') "; 
	}

?>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title></title>
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>
		var sPO = "";

		function selectItem(obj) {
			$(obj).closest("tr").siblings().removeClass("diffcolor");
			$(obj).toggleClass("diffcolor");
			tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); sPO = tmp_obj[1];
		}
		
		function viewStockcard() {
			if(sPO == "") {
				parent.sendErrorMessage("Please select record to view...");
			} else {
				var arg = sPO.split('|');
				parent.viewStockcard(arg[0],arg[1],arg[2],arg[3],arg[4]);
			}
		}
		
		function showSearch() {
			$("#search").dialog({title: "Search Record", width: 400, resizable: false, modal: true });
		}
		
		function searchRecord() {
			document.frmIbook.submit();
		}
		
	</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
	<table width="100%" class="tgrid" cellspacing=0 style="font-size: 11px;">
		<tr>
			<td class="ui-state-default" align=left width=10% style="padding: 5px;">ITEM CODE</td>
			<td class="ui-state-default" align=left width=30% style="padding-left: 5px;"> DESCRIPTION</td>
			<td class="ui-state-default" align=center width=5%>UNIT</td>
			<td class="ui-state-default" align=center width=8%>BEG.</td>
			<td class="ui-state-default" align=center width=8%>PURCHASES</td>
			<td class="ui-state-default" align=center width=8%>INBOUND</td>
			<td class="ui-state-default" align=center width=8%>PULLOUTS</td>
			<td class="ui-state-default" align=center width=8%>OUTBOUND</td>
			<td class="ui-state-default" align=center width=5%>SOLD</td>
			<td class="ui-state-default" align=center>END</td>
			<td class="ui-state-default" width=15>&nbsp;</td>
		</tr>
	</table>
	<div id="details" style="height:410px; overflow: auto;">
		<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">
			<?php

				$rowsPerPage = 18;
				if(isset($_REQUEST['page'])) { if($_REQUEST['page'] <= 0) { $pageNum = 1; } else { $pageNum = $_REQUEST['page']; }} else { $pageNum = 1; }
				$offset = ($pageNum - 1) * $rowsPerPage;

				if($_GET['group'] != "") { $fs1 = " and `category` = '$_GET[group]' "; }
				if($_GET['searchtext'] != '') { $fs2 = " and (a.item_code = '$_GET[searchtext]' || a.description like '%$_GET[searchtext]%') "; }
				$query = "SELECT DISTINCT a.item_code FROM ibook a LEFT JOIN products_master b ON a.item_code = b.item_code WHERE 1=1 $fs1 $fs2";
				
				/* Paging */
				$numrows = getArray("select count(*) from ($query) a;");
				$maxPage = ceil($numrows[0]/$rowsPerPage);
				$_i = dbquery("$query limit $offset,$rowsPerPage");
				
				$isE = getArray("SELECT doc_no, posting_date as doc_date FROM phy_header WHERE branch='$_SESSION[branchid]' AND `status` = 'Finalized' AND posting_date <= '".formatDate($_GET['dtf'])."' order by posting_date desc limit 1;");
				if($isE['doc_no'] == '') { $baseD8 = '2021-08-01'; } else { $baseD8 = $isE['doc_date']; }
				while($row = mysql_fetch_array($_i)) {
					if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
					list($description,$unit) = getArray("select description, unit from products_master where item_code = '$row[item_code]';");
					$desc = rawurlencode($description);
					

					$pi = getArray("select ifnull(sum(b.qty),0) from phy_header a left join phy_details b on a.doc_no = b.doc_no and a.branch=b.branch where a.branch = '$_SESSION[branchid]' and b.item_code = '$row[item_code]' and a.status = 'Finalized' and a.posting_date = '$baseD8' GROUP BY b.item_code;");
					$run = getArray("select sum(purchases+inbound-outbound-pullouts-sold) as run from ibook where doc_date >= '$baseD8' and doc_date < '".formatDate($_GET['dtf'])."' and item_code = '$row[item_code]' and doc_branch = '$_SESSION[branchid]';");
					$cur = getArray("select sum(purchases) as purchases, sum(inbound) as returns, sum(pullouts) as withdrawals, sum(outbound) as transfers, sum(sold) as sold, sum(purchases+inbound-outbound-pullouts-sold) as currentbalance from ibook where item_code = '$row[item_code]' and doc_date between '".formatDate($_GET['dtf'])."' and '".formatDate($_GET['dt2'])."' and doc_branch = '$_SESSION[branchid]';");
					
					$end = ROUND($pi[0]+$run[0]+$cur['currentbalance'],2);
					if($end!=0){
						echo "<tr bgcolor=\"$bgC\" id='obj_$row[item_code]|$unit|$desc|$_GET[dtf]|$_GET[dt2]' onMouseOver=\"javascript: highlightTableRowVersionA(this, '#e6f0fa');\" onclick=\"javascript: selectItem(this);\">
							<td class=\"grid\" valign=top align=left style=\"padding-left: 5px;\" width=10%>".$row['item_code']."</td>
							<td class=\"grid\" valign=top align=left width=31%>".$description."</td>
							<td class=\"grid\" valign=top align=center width=5%>".$unit."</td>
							<td class=\"grid\" valign=top align=center width=8%>".number_format(($pi[0]+$run[0]),2)."&nbsp;</td>
							<td class=\"grid\" valign=top align=center width=8%>".number_format($cur['purchases'],2)."&nbsp;</td>
							<td class=\"grid\" valign=top align=center width=8%>".number_format($cur['returns'],2)."</td>
							<td class=\"grid\" valign=top align=center width=8%>".number_format($cur['withdrawals'],2)."</td>
							<td class=\"grid\" valign=top align=center width=7%>".number_format($cur['transfers'],2)."</td>
							<td class=\"grid\" valign=top align=center width=8%>".number_format($cur['sold'],2)."</td>
							<td class=\"grid\" valign=top align=center>".number_format($end,2)."</td>
						</tr>"; $i++;
						unset($pi); unset($cur); unset($run); $end = 0;
					}
				}

				if($i < 18) {	
					for($i; $i <= 17; $i++) {	if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
						echo "<tr bgcolor=\"$bgC\">
							<td class=\"grid\" colspan=11>&nbsp;</td>
						</tr>";
					}
				}
			?>
		</table>
	</div>
    <table width=100% cellpadding=5 cellspacing=0>
		<tr>
			<td align=left>
				<button onClick="viewStockcard();" class="buttonding"><img src="images/icons/bill.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;View Item Stock Card</b></button>
				<button onClick="showSearch();" class="buttonding"><img src="images/icons/search.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Search Record</b></button>
			</td>
			<?php if($numrows[0] > 0) { ?>
			<td align=right style="padding-right: 10px;"><?php if ($pageNum > 1) { ?><a href="javascript: parent.jumpIBookPage('<?php echo ($pageNum - 1); ?>','<?php echo $_REQUEST['searchtext']; ?>','<?php echo $_REQUEST['group']; ?>','<?php echo $_REQUEST['dtf']; ?>','<?php echo $_REQUEST['dt2']; ?>')" class="a_link" title="Previous Page"><span style="font-size: 18px;">&laquo;</span></a>&nbsp;<?php } ?>
				<span style="font-size: 12px;">Page <?php echo $pageNum; ?> of <?php echo $maxPage; ?></span>&nbsp;
					<?php if($pageNum != $maxPage) { ?><a href="javascript: parent.jumpIBookPage('<?php echo ($pageNum + 1); ?>','<?php echo $_REQUEST['searchtext']; ?>','<?php echo $_REQUEST['group']; ?>','<?php echo $_REQUEST['dtf']; ?>','<?php echo $_REQUEST['dt2']; ?>')" class="a_link" title="Next Page"><span style="font-size: 18px;">&raquo;</span></a><?php } ?>&nbsp;&nbsp;
						<?php if($maxPage > 1) { ?>
						<span style="font-size: 12px;">Jump To: </span><select id="jpage" name="jpage" style="width: 40px; padding: 0px;" onchange="javascript: parent.jumpIBookPage(this.value,'<?php echo $_REQUEST['searchtext']; ?>','<?php echo $_REQUEST['group']; ?>','<?php echo $_REQUEST['dtf']; ?>','<?php echo $_REQUEST['dt2']; ?>');">
							<?php
									for ($x = 1; $x <= $maxPage; $x++) {
										echo "<option value='$x' ";
										if($pageNum == $x) { echo "selected"; }
										echo ">$x</option>";
									}
								?>
								 </select>
					<?php } ?>
			</td> <?php } ?>

		</tr>
	</table>
	<div id="search" style="display: none;">
		<form name="frmIbook" id="frmIbook" action="ibook.php" method=GET>
			<table width=100% border=0 cellspacing=2 cellpadding=0>
				<tr>
					<td valign=top width="95%" class="td_content" style="padding: 10px;">		
						<table border="0" cellpadding="0" cellspacing="0" width=100%>
							<tr>
								<td width=35%><span class="spandix-l" valign=top>Search String :</span></td>
								<td>
									<input type="hidden" id="group" name="group" value="<?php echo $_GET['group']; ?>">
									<input type="hidden" id="dtf" name="dtf" value="<?php echo $_GET['dtf']; ?>">
									<input type="hidden" id="dt2" name="dt2" value="<?php echo $_GET['dt2']; ?>">
									<input type="text" id="searchtext" name="searchtext" class="nInput" style="width: 100%;" value="" />
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr><td colspan=2><hr></hr></td></tr>
							<tr>
								<td align=center colspan=2>
									<button type="button" onClick="searchRecord();" onkeypress="if(event.keyCode == 13) { searchRecord(); }" class="buttonding" style="width: 180px; font-size: 11px;"><img src="images/icons/search.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Search Record Now</button>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>
	</div>
	</body>
</html>
<?php @mysql_close($con); ?>