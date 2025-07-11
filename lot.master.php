<?php
	include("includes/dbUSE.php");
	session_start();
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Citilights Garden - Home Owners Registration File</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/date.js"></script>
<script language="javascript" src="js/tableH.js"></script>
<script>

	function showReservation() {
		var bk = $("#block_no").val(); var lt = $("#lot_no").val();
		parent.showReservation(bk,lt);
	}
	
	function loadSeqNo(obj,bk,lt) {	
		$(obj).closest("tr").siblings().removeClass("diffcolor");
		$(obj).toggleClass("diffcolor");
		$.post("tennant.datacontrol.php",{ mod: "viewRecord", bk: bk, lt: lt, sid: Math.random()}, function (data) {
				$("#block_no").val(data['block_no']);
				$("#lot_no").val(data['lot_no']);
				$("#price").val(data['price']);
				$("#price").val(data['price']);
				$("#amount").val(data['contract_price']);
				$("#lot_area").val(data['lot_area']);
				$("#rec_id").val(data['rec_id']);
				if(data['rsv_id'] != "") {
					getReservation(data['rsv_id']);
				}
		},"json");
	}
	
	function commit2buy() {
		var bk = $("#block_no").val(); var lt = $("#lot_no").val();
		var cid = "";
		var msg = "";
		
		$.post("tennant.datacontrol.php", {mod: "checkStatus", bk: bk, lt: lt, sid: Math.random() }, function(data) {
			var stat = data['status'];
			switch(stat) {
				case "Available":
					parent.sendErrorMessage("Unable to continue. Block #"+bk+", Lot #"+lt+" does not have any reservation yet. Please process Reservation Payment first before committing to buy.");
					return false;
				break;
				case "Sold":
					parent.sendErrorMessage("Unable to continue. Block #"+bk+", Lot #"+lt+" is already sold.");
					return false;
				break;
				case "Reserved":
					parent.commitNow(bk,lt,'');
				break;
			}
		},"json");
		
	}
	
	function updateLotRecord() {
		$.post("tennant.datacontrol.php", { mod: "updateLotRecord", rec_id: $("#rec_id").val(), bk: $("#block_no").val(), lt: $("#lot_no").val(), la: $("#lot_area").val(), price: parent.stripComma($("#price").val()), amount: parent.stripComma($("#amount").val()), sid: Math.random() }, function(data) { 
			if(data == "error") {
				parent.sendErrorMessage("Unable to update details on this record. Block # " + $("#block_no").val()+", Lot # " + $("#block_no").val() + " is currently reserved and in active status;");
			} else {
				alert("Record Successfully Updated...");
				parent.showLotMaster();
			}
		},"html");	
	}
	
	function getReservation(id) {
		$.post("tennant.datacontrol.php", { mod: "getReservation", rsv_id: id, sid: Math.random() }, function (data) {
			$("#lname").val(decodeURI(data['lname']));
			$("#fname").val(data['fname']);
			$("#mname").val(data['mname']);
			$("#email").val(data['email']);
			$("#tel_no").val(data['tel_no']);
		},"json");
	}
	
	function compLotPrice() {
		var psqm = $("#price").val();
			psqm = parent.parent.stripComma(psqm);
			if(psqm == "") { psqm = 0; }
		var area = $("#lot_area").val();
			if(area == "") { area = 0; }
		var msg = "";
		var amt; 
		
		if(isNaN(psqm) == true) { msg = msg + "Price per Square Meter must be in integer or decimal value."; }
		
		amt = psqm * area;
		$("#amount").val(parent.kSeparator(amt.toFixed(2)));
	}
	
	function viewPayHistory() {
		var bk = $("#block_no").val(); var lt = $("#lot_no").val();
		$.post("tennant.datacontrol.php", { mod: "getCID", block_no: bk, lot_no: lt, sid: Math.random() }, function(data) {
			if(data[0] == 0) {
				parent.sendErrorMessage("Unable to fetch client's Payment History. Block No. "+bk+", Lot No. "+lt+" is still " +data[1]+"..");
			} else {
				parent.showPayHistory(data[0]);
			}
		},"json");
		
	}
	
	function deleteLot() {
		if($("#block_no").val() == "" || $("#lot_no").val() == "") { 
			parent.sendErrorMessage("You did not select any record to delete.");
		} else {
			if(confirm("Are you sure you want to delete this record?") == true) {
				$.post("tennant.datacontrol.php", { mod: "deleteLot", bk: $("#block_no").val(), lt: $("#lot_no").val(), sid: Math.random() }, function() { alert("Lot Record Successfully Deleted!"); parent.showLotMaster(); });
			}
		}	
	}
</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
			<table align=center width=100% height=30 border=0 cellspacing=0 cellpadding=0  style="border-bottom:2px solid black; background-color:#595959; background-image: url(images/4.jpg); font-weight:bold; color:#ffffff;">
				<tr>
					<td align="left" style="font-weight: bold; font-size: 12px;" valign=middle>&nbsp;&nbsp;Lot Master Plan</td>
					<td align=right width="15%" style="padding-right: 2px;" valign=middle>
						<a href="javascript: parent.close_div();" style="text-decoration: none; font-size: 11px; color: #ffffff;"><img src="images/icons/button-logout-text.png" border=0 title="Close"  /></a>
					</td>
				</tr>
			</table>
			<table width=100% height=94% border=0 cellspacing=5 cellpadding=0>
				<input type="hidden" id="rec_id">
				<tr>
					<td width="25%" valign=top class="td_content">
						<table width="100%" cellspacing=0 cellpadding=0 style="padding-right: 10px;">
							<input type="hidden" id="seq_no" />
							<tr><td height=8></td></tr>
							<tr>
								<td align=right >
									<input type="text" id="block_no" class="nInput" value="" style="text-align: right; width: 40%;" <?php if($_SESSION['utype'] != 'admin') { echo "readonly"; } ?>/>
									<br><span class="spandix">Block #</span>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td align=right >
									<input type="text" id="lot_no" class="nInput" value="" style="text-align: right; width: 40%;" <?php if($_SESSION['utype'] != 'admin') { echo "readonly"; } ?> /><br><span class="spandix">LOT #</span>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td align=right >
									<input type="text" id="lot_area" class="nInput" value="" style="text-align: right; width: 40%;" onChange="compLotPrice();" <?php if($_SESSION['utype'] != 'admin') { echo "readonly"; } ?> /><br><span class="spandix">LOT AREA</span>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td align=right >
									<input type="text" id="price" class="nInput" value="" style="text-align: right; width: 40%;" onChange="compLotPrice();" <?php if($_SESSION['utype'] != 'admin') { echo "readonly"; } ?> /><br><span class="spandix">PRICE PER SQ. MTR.</span>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td align=right >
									<input type="text" id="amount" class="nInput" value="" style="text-align: right; width: 40%;" readonly /><br><span class="spandix">CONTRACT PRICE</span>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td align=left style="padding-right: 5px;padding-left: 5px;" valign=top>
									 <fieldset>
											<legend><span id="fLabel" class="spandix">SOLD/RESERVED TO:</span></legend>
											 <input type="text" id="lname" class="nInput" readonly style="width:250px;"><br><span class="spandix">LAST NAME:</span><br>
											 <input type="text" id="fname" class="nInput" readonly style="width:250px;"><br/><span class="spandix">FIRST NAME:</span><br/>
											 <input type="text" id="email" class="nInput" readonly style="width:250px;"><br/><span class="spandix">EMAIL:</span><br>
											 <input type="text" id="tel_no" class="nInput" style="width:250px;" readonly><br/><span class="spandix">TEL #:</span>
									  </fieldset>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td align=center>
									<?php if($_SESSION['utype'] == 'admin') { ?>
										<button onClick="updateLotRecord();" style="height: 40px; width: 200px;"><img src="images/icons/floppy.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Update Lot Record</button>&nbsp;
										<button onClick="parent.addPaMore();" style="height: 40px; width: 200px;"><img src="images/icons/add.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Add Lot Record</button>&nbsp;
										<button onClick="deleteLot();" style="height: 40px; width: 200px;"><img src="images/icons/delete.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Delete Lot Record</button>&nbsp;
									<?php } ?>
								</td>
							</tr>
						</table>
					</td>
					<td valign=top width="75%" class="td_content">		
						<table border="0" cellpadding="0" cellspacing="0" width=100%>
							<tr>
								<td width="100%" cellspacing=0 cellpadding=0>
									<table width=100% cellspacing=0 cellpadding=0 style="padding: 1px 2px 0px 2px;">
										<tr bgcolor="#595959">
											<td align=center class="dgridhead" width="4%">&nbsp;</td>
											<td align=center class="dgridhead" width="10%">BLOCK #</td>
											<td align=center class="dgridhead" width="10%">LOT #</td>
											<td align=center class="dgridhead" width="10%">LOT AREA</td>
											<td align=center class="dgridhead" width="15%">PRICE PER SQ. MTR.</td>
											<td align=center class="dgridhead" width="15%">CONTRACT PRICE</td>
											<td align=center class="dgridhead" width="10%">STATUS</td>
											<td align=center class="dgridhead" width="22%">SOLD/RESERVED TO</td>
											<td align=center class="dgridhead" width="4%">&nbsp;</td>
										</tr>
									</table>	
									<div id="details" style="height:360px; overflow: auto;">
										<table width=100% cellspacing=0 cellpadding=0 style="padding: 0 2px 2px 2px;" onMouseOut="javascript:highlightTableRowVersionA(0);">
											<?php
												$i = 0;
													$getRec = dbquery("select * from lot_master order by block_no, lot_no");
													list($totAv) = getArray("select count(*) from ozian.lot_master where status='AVAILABLE';");
													list($totSd) = getArray("select count(*) from ozian.lot_master where status='SOLD';");
													list($totRs) = getArray("select count(*) from ozian.lot_master where status='RESERVED';");
													while($row = mysql_fetch_array($getRec)) {
													if($row['rsv_id'] > 0 && $row['file_id'] == "") { list($cname) = getArray("select concat(lname,', ',fname,' ',mname) from reservation_master where reservation_id='$row[rsv_id]';"); }
													if($row['file_id'] > 0 && $row['rsv_id'] != "") { list($cname) = getArray("select concat(lname,', ',fname,' ',mname) from contracts where file_id='$row[file_id]';"); }
													if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
													echo "
													<tr bgcolor=\"$bgC\" onmouseover=\"highlightTableRowVersionA(this, '#3399ff');\" id='row[$i]' onclick=\"loadSeqNo(this,$row[1],$row[2]);\">
														<td class=dgridbox align=center width=\"4%\"></td>
														<td class=dgridbox align=center width=\"10%\">$row[1]</td>
														<td class=dgridbox align=center width=\"10%\">$row[2]</td>
														<td class=dgridbox align=center width=\"10%\">".number_format($row[3],0)."</td>
														<td class=dgridbox align=center width=\"15%\">".number_format($row[4],2)."</td>
														<td class=dgridbox align=center width=\"15%\">".number_format($row[5],2)."</td>
														<td class=dgridbox align=center width=\"10%\">$row[6]</td>
														<td class=dgridbox align=center width=\"24%\">$cname</td>
													</tr>"; $i++; $cname = "";
												}
											?>
										</table>
									</div>
								</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" style="padding-top: 20px;">
							<tr>
								<td width="40%">
									<table width="100%"  cellspacing="0" cellpadding="0" style="padding-left: 5px;">
										<tr>
											<td>
												<button onClick="showReservation();" class="buttonding" id="btn_rsv"><img src="images/icons/crr.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Make Reservation for Selected Record</b></button>
												<button onClick="commit2buy();" class="buttonding" id="btn_dpst"><img src="images/icons/contract.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Commit to Buy and Make Contract</b></button>
												<button onClick="viewPayHistory();" class="buttonding" id="btn_pay"><img src="images/icons/apv256.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;View Client's Payment History</b></button>
											</td>
										</tr>
									</table>
								</td>
								<td width="60%">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td class="tbold">No. of Units Available &raquo;</td>
											<td align="right" style="padding-right: 5px;"><input type="text" id="total" class="gridInput" style="width: 60%; text-align: right;" value="<?php echo $totAv; ?>" readonly /></td>
										</tr>
										<tr><td height=2></td></tr>
										<tr>
											<td class="tbold">No. of Units Sold &raquo;</td>
											<td align="right" style="padding-right: 5px;"><input type="text" id="total" class="gridInput" style="width: 60%; text-align: right;" value="<?php echo $totSd; ?>" readonly /></td>
										</tr>
										<tr><td height=2></td></tr>
										<tr>
											<td class="tbold">No. of Units Reserved &raquo;</td>
											<td align="right" style="padding-right: 5px;"><input type="text" id="total" class="gridInput" style="width: 60%; text-align: right;" value="<?php echo $totRs; ?>" readonly /></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
 </table>
</body>
</html>
<?php mysql_close($con);