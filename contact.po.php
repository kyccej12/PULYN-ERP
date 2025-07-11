<?php
	include("includes/dbUSE.php");
	session_start();
	
	if(isset($_GET['fid']) && $_GET['fid'] != "") { $res = getArray("select * from contact_info where file_id='$_GET[fid]';"); }
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Geck Distributors</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link href="style/jquery-ui.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/jquery-ui.js"></script>
<script language="javascript" src="js/date.js"></script>
<script>
	$(function() {
		$("#po_date").datepicker();
		$("#received").datepicker();
	});
	
	function addPO() {
		var msg = "";
		if($("#po_no").val() == "") { msg =  msg + "- PO No. is required<br/>"; }
		if($("#po_date").val() == "") { msg =  msg + "- PO Date is required<br/>"; }
		if($("#amount").val() == "") { msg =  msg + "- PO Amount is required<br/>"; }
		if($("#received").val() == "") { msg =  msg + "- Date received is required<br/>"; }
		
		if(msg != "") {
			parent.sendErrorMessage("Unable to continue due to the following error(s):<br/><br/>"+msg);
		} else {
			$.post("geck.datacontrol.php", { mod: "addCustPO", po_no: $("#po_no").val(), po_date: $("#po_date").val(), amount: $("#amount").val(), received: $("#received").val(), ccode: <?php echo $_GET['fid']; ?>, sid: Math.random() }, function(data) {
				if(data == 'Error') {
					parent.sendErrorMessage("Error: A duplicate PO Number detected!");
				} else {
					$("#details").html(data);
					$("#po_no").val('');
					$("#po_date").val(''); 
					$("#amount").val(''); 
					$("#received").val('');
					document.getElementById("po_no").focus = true;
				}
			},"html");
		}
		
	}
	
	function deleteCustPO(fid,ccode) {
		if(confirm("Are you sure you want to delete this record?") == true) {
			$.post("geck.datacontrol.php", { mod: "delCustPO", fid: fid, ccode: ccode, sid: Math.random() }, function(data){ 
				"Record Successfully Deleted!";
				$("#details").html(data);
			},"html");
		}
	}

</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">

 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<form name="contactinfo" id="contactinfo">
		<input type="hidden" id = "fid" name="fid" value="<?php echo $_GET['fid']; ?>">
		<tr>
			<td style="padding:0px;" valign=top>
				<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td valign=top width="90%" class="td_content" style="padding: 0px;">		
							 <table cellspacing=0 cellpadding=0 border=0 width=100%>
								<tr bgcolor="#887e6e">
									<td align=center class="gridHead" width="15%">P.O No.</td>
									<td align=center class="gridHead" width="15%">P.O Date</td>
									<td align=center class="gridHead"  width="20%">AMOUNT</td>
									<td align=center class="gridHead"  width="20%">DATE RECEIVED</td>
									<td align=center class="gridHead">AMT. CONSUMED</td>
									<td align=center class="gridHead" width="18">&nbsp;</td>
								</tr>
								<tr bgcolor="#fefefe">
									<td align=center class="grid"><input type="text" id="po_no" name="po_no" style="width:95%" /></td>
									<td align=center class="grid"><input type="text" id="po_date" name="po_date" style="width:95%" /></td>
									<td align=center class="grid"><input type="text" id="amount" name="amount" style="width:95%" /></td>
									<td align=center class="grid"><input type="text" id="received" name="received" style="width:95%" /></td>
									<td align=left class="grid"><a href="#" onclick="javascript: addPO();"><img src="images/icons/add-2.png" border=0 width=20 height=20 align=absmiddle title="Click to Add to List" /></a></td>
								    <td align=center class="grid" width="18">&nbsp;</td>
								</tr>
							</table>
							<div id="details" style="height:290px; overflow: auto;">
								<table cellspacing=0 cellpadding=0 border=0 width=100%>
									<?php
										$vf = dbquery("SELECT file_id, po_no, DATE_FORMAT(po_date,'%m/%d/%Y') AS po_date, amount, DATE_FORMAT(date_received,'%m/%d/%Y') AS received FROM contact_po WHERE customer = '$_GET[fid]' ORDER BY po_date DESC;");
										while($row = mysql_fetch_array($vf)) {
											if($i%2==0){ $bgC = "#ededed"; } else { $bgC = "#ffffff"; }
											echo "<tr bgcolor=\"$bgC\">
													<td class=grid align=center width=\"15%\">$row[po_no]</td>
													<td class=grid align=center width=\"15%\">$row[po_date]</td>
													<td class=grid align=center width=\"20%\">".number_format($row['amount'],2)."</td>
													<td class=grid align=center width=\"20%\">$row[received]</td>
													<td class=grid align=right style='padding-right: 20px;'><a href='#' onclick='javascript: deleteCustPO($row[file_id],$_GET[fid]);'><img src='images/icons/delete.png' border=0 width=16 height=16 align=absmiddle title='Click to delete from list' /></a></td>
												  </tr>"; $i++; 
										}
										
										if($i < 13) {
											for($i; $i <= 12; $i++) {
												if($i%2==0){ $bgC = "#ededed"; } else { $bgC = "#ffffff"; }
												echo '<tr bgcolor="'.$bgC.'">
														<td align=left class="grid" width="100%" colspan=5>&nbsp;</td>
												</tr>';
											}
										}
									?>
								</table>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	 </form>
 </table>

</body>
</html>
<?php mysql_close($con);