<?php
	ini_set("error_reporting","1");
	include("includes/dbUSE.php");
	$today = date('Y-m-d');


	$rowsPerPage = 15;
	if(isset($_REQUEST['page'])) { if($_REQUEST['page'] <= 0) { $pageNum = 1; } else { $pageNum = $_REQUEST['page']; }} else { $pageNum = 1; }
	$offset = ($pageNum - 1) * $rowsPerPage;
	
	if(isset($_REQUEST['searchtext']) && !empty($_REQUEST['searchtext'])) { 
		$fs1 = " and (dr_no = '$_REQUEST[searchtext]' || customer = '%$_REQUEST[searchtext]%' ||  customer_name like '%$_REQUEST[searchtext]%' || dr_date = '$_REQUEST[searchtext]') "; 
		if($_REQUEST['includeDetails'] == "Y") {
			$fs1 = $fs1 . " or dr_no in (select dr_no from dr_details where item_code = '$_REQUEST[searchtext]' || description like '%$_REQUEST[searchtext]%') ";
		}
	}
	
	$numrows = getArray("select count(*) from dr_header where 1=1 $fs1;");
	$maxPage = ceil($numrows[0]/$rowsPerPage);

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Geck Distributors</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="js/jquery.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>
		var sDR = "";

		function selectDR(obj) {
			$(obj).closest("tr").siblings().removeClass("diffcolor");
			$(obj).toggleClass("diffcolor");
			tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); sDR = tmp_obj[1];
		}
		
		function viewDR() {
			if(sDR == "") {
				parent.sendErrorMessage("Please select record to view.");
			} else {
				parent.viewDR(sDR);
			}
		}
	</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
<table align=center width=100% height=30 border=0 cellspacing=0 cellpadding=0  style="border-bottom:2px solid black; background-color:#595959; background-image: url(images/4.jpg); font-weight:bold; color:#ffffff;">
	<tr>
		<td align="left" style="font-weight: bold; font-size: 11px; padding-left: 5px;" valign=middle><img src="images/icons/bill.png" width=20 height=20 align=absmiddle border=0 />&nbsp;&nbsp;DELIVERY RECEIPT SUMMARY</td>
		<td align=right width="10%" style="padding-right: 2px;" valign=middle>
			<a href="javascript: parent.close_div();" style="text-decoration: none; font-size: 11px; color: #ffffff;"><img src="images/icons/button-logout-text.png" border=0 title="Close"  /></a>
		</td>
	</tr>
</table>
<?php
	echo "<table width='100%' class=\"tgrid\" cellpadding=0 cellspacing=0 onMouseOut=\"javascript: highlightTableRowVersionA(0);\">";
	echo "<tr>
			<td class=\"dgridHead\" align=left width=10%><b>DR NO.</b></td>
			<td class=\"dgridHead\" align=left width=10%><b>DR DATE</b></td>
			<td class=\"dgridHead\" align=left width=30%><b>CUSTOMER'S NAME</b></td>
			<td class=\"dgridHead\" align=left><b>REMARKS/DETAILS</b></td>
			<td class=\"dgridHead\" align=right width=10%><b>AMOUNT</b></td>
			<td class=\"dgridHead\" align=left style='padding-left: 20px;' width=15%><b>STATUS</b></td>
		  </tr>";
	$_i = dbquery("select dr_no, lpad(dr_no,2,0) as rr, date_format(dr_date,'%m/%d/%Y') as rdate, customer, customer_name, remarks, amount, status from dr_header where 1=1 $fs1 order by dr_date desc LIMIT $offset, $rowsPerPage;");
	while($row = mysql_fetch_array($_i)) {
		if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
		echo "<tr bgcolor=\"$bgC\" id='obj_$row[dr_no]' onMouseOver=\"javascript: highlightTableRowVersionA(this, '#e6f0fa');\" onclick=\"javascript: selectDR(this);\">
				<td class=\"grid\" valign=top align=left style=\"padding-left: 5px;\">".$row['rr']."</td>
				<td class=\"grid\" valign=top align=left>".$row['rdate']."</td>
				<td class=\"grid\" valign=top align=left>(".$row['customer'].") ".strtoupper($row['customer_name'])."</td>
				<td class=\"grid\" valign=top align=left>".strtoupper($row['remarks'])."&nbsp;</td>
				<td class=\"grid\" valign=top align=right>".number_format($row['amount'],2)."</td>
				<td class=\"grid\" valign=top align=left style='padding-left: 20px;";
				if($row['status'] == "Cancelled") { echo "color: red;"; }
				echo "'><b>".strtoupper($row['status'])."</b></td></tr>"; $i++;
	}
	if($numrows[0] == 0) { echo "<tr bgcolor=\"$bgC\"><td class=\"grid\" valign=top align=center colspan=10>NO RECORDS FOUND!</td></tr>";
					$i = 2;
		}
		if($numrows[0] < 16) {	
			for($i; $i <= 15; $i++) {	if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
				echo "<tr bgcolor=\"$bgC\">
					<td class=\"grid\" colspan=6>&nbsp;</td>
				</tr>";
			}
		}
	echo "</table>";
?>
    <table width=100% cellpadding=5 cellspacing=0>
		<tr>
			<td align=left>
				<button onClick="parent.newDR();" class="buttonding" id="btn_rsv" style="width: 200px;"><img src="images/icons/add.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;New Delivery Receipt</b></button>
				<button onClick="viewDR();" class="buttonding" id="btn_rsv" style="width: 300px;"><img src="images/icons/bill.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;View Details of Selected Delivery Receipt</b></button>
				<button onClick="parent.showSearch2('dr');" class="buttonding" id="btn_dpst" style="width: 180px;"><img src="images/icons/search.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Search Record</b></button>
				<button onClick=" parent.close_div();" class="buttonding" id="btn_dpst" style="width: 180px;"><img src="images/icons/cancelled.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Close This Window</b></button>
			</td>
			<?php if($numrows[0] > 0) { ?>
			<td align=right style="padding-right: 10px;"><?php if ($pageNum > 1) { ?><a href="javascript: parent.jumpDRPage('<?php echo ($pageNum - 1); ?>','<?php echo $_REQUEST['searchtext']; ?>','<?php echo $_REQUEST['includeDetails']; ?>')" class="a_link" title="Previous Page"><span style="font-size: 18px;">&laquo;</span></a>&nbsp;<?php } ?>
				<span style="font-size: 12px;">Page <?php echo $pageNum; ?> of <?php echo $maxPage; ?></span>&nbsp;
					<?php if($pageNum != $maxPage) { ?><a href="javascript: parent.jumpDRPage('<?php echo ($pageNum + 1); ?>','<?php echo $_REQUEST['searchtext']; ?>','<?php echo $_REQUEST['includeDetails']; ?>')" class="a_link" title="Next Page"><span style="font-size: 18px;">&raquo;</span></a><?php } ?>&nbsp;&nbsp;
						<?php if($maxPage > 1) { ?>
						<span style="font-size: 12px;">Jump To: </span><select id="jpage" name="jpage" style="width: 40px; padding: 0px;" onchange="javascript: parent.jumpDRPage(this.value,'<?php echo $_REQUEST['searchtext']; ?>','<?php echo $_REQUEST['includeDetails']; ?>');">
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
	</body>
</html>