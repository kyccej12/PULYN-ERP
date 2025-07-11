<?php
	include("includes/dbUSE.php");
	session_start();
	
	$lot = getArray("select rsv_id, lot_area, price_sqm, contract_price, contract_price as net from lot_master where block_no='$_GET[block_no]' and lot_no = '$_GET[lot_no]';");
	$res2 = getArray("SELECT *, DATE_FORMAT(trans_date,'%m/%d/%Y') AS tdate from contracts where file_id='$_GET[fileID]';");

	
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
	
	var payD = "";
	
	function tagPayment(obj,val) {
		payD = val;
		$(obj).closest("tr").siblings().removeClass("diffcolor");
		$(obj).toggleClass("diffcolor");
	}
	
	function passMe() {		
		if(payD == "") {
			parent.sendErrorMessage("You have not selected any schedule to make payments...");
		} else {
			data = payD.split("|");
			$.post("tennant.datacontrol.php",{mod: "retrievePay", fileID: data[3], type: data[0], month: data[1], year: data[2]},function(xdata){
				if(xdata > 0) { parent.sendErrorMessage("Unable to process your request. Client has already posted payments for this schedule. To view its details, you may click \"<b><i>View Payment Details on Selected Schedule</i></b>\" button"); 
				} else {
					parent.makePay(data[0],data[1],data[2],data[3],data[4],data[5],data[6]);
				}
			},"html");
		}
	}
	
	function passMyDetails() {		
		if(payD == "") {
			parent.sendErrorMessage("You have not selected any schedule to make payments...");
		} else {
			data = payD.split("|");
			$.post("tennant.datacontrol.php",{mod: "retrievePay", fileID: data[3], type: data[0], month: data[1], year: data[2]},function(xdata){
				if(xdata == 0) { parent.sendErrorMessage("Unable to process your request. Client has not posted any payments for this schedule yet..."); 
				} else {
					parent.getPayDetails(data[0],data[1],data[2],data[3],data[4],data[5],data[6]);
				}
			},"html");
		}
	}
	
	function generateSOA(file_id,rsv_id) {
		window.open("reports/soa.php?file_id="+file_id+"&rsv_id="+rsv_id+"","Statement of Account","location=1,status=1,scrollbars=1,width=640,height=720");
	}

</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
			<table align=center width=100% height=30 border=0 cellspacing=0 cellpadding=0  style="border-bottom:2px solid black; background-color:#595959; background-image: url(images/4.jpg); font-weight:bold; color:#ffffff;">
				<tr>
					<td align="left" style="font-weight: bold; font-size: 12px;" valign=middle>&nbsp;&nbsp;Payment History</td>
					<td align=right width="6%" style="padding-right: 2px;" valign=middle>
						<a href="javascript: parent.close_div();" style="text-decoration: none; font-size: 11px; color: #ffffff;"><img src="images/icons/button-logout-text.png" border=0 title="Close"  /></a>
					</td>
				</tr>
			</table>
			<table width=100% height=94% border=0 cellspacing=5 cellpadding=0>
				<input type="hidden" id="rec_id">
				<tr>
					<td width="40%" valign=top class="td_content" style="padding: 10px;">	
						<table border="0" cellpadding="0" cellspacing="0" width=100%>
								<tr>
									<td width=33%>
										<input type="text" id="com_lname" class="nInput" style="width: 90%;" value="<?php echo $res2['lname']; ?>" readonly />
										<br>
										<span class="spandix-l">LAST NAME</span>
									</td>
									<td width=33%>
										<input type="text" id="com_fname" class="nInput" style="width: 90%;" value="<?php echo $res2['fname']; ?>" readonly/>
										<br>
										<span class="spandix-l">FIRST NAME</span>
									</td>
									<td width=33%>
										<input type="text" id="com_mname" class="nInput" style="width: 90%;" value="<?php echo $res2['mname']; ?>" readonly />
										<br>
										<span class="spandix-l">MIDDLE NAME</span>
									</td>
								</tr>
								<tr><td height=2></td></tr>
								<tr>
									<td width="100%" colspan=3>
										<textarea id="com_address" style="width:95%;" rows=1 readonly><?php echo $res2['address']; ?></textarea>
										<br>
										<span class="spandix-l">ADDRESS:</span>
									</td>
								</tr>
								<tr><td height=2></td></tr>
								<tr>
									<td width="100%" colspan=3>
										<input type="text" id="com_telno" class="nInput" style="width: 60%;" value="<?php echo $res2['tel_no']; ?>" readonly />
										<br>
										<span class="spandix-l">TELEPHONE/MOBILE NO.</span>
									</td>
								</tr>
								<tr><td height=2></td></tr>
								<tr>
									<td width="100%" colspan=3>
										<input type="text" id="com_email" class="nInput" style="width: 60%;" value="<?php echo $res2['email']; ?>" readonly />
										<br>
										<span class="spandix-l">EMAIL ADDRESS</span>
									</td>
								</tr>
								<tr><td colspan=3><hr></hr></td></tr>
							</tr>
							<tr>
								<td width="100%" colspan=3>
									<fieldset>
										<legend><span class="spandix">TERMS AGREED:</span></legend>
										<table width=100% cellpadding=0 cellspacing=0 border=0>
											<tr><td class=spandix>Date Finalized :</td><td><input type="text" id="com_date" class="nInput" style="width: 60%" value="<?php echo $res2['tdate']; ?>" readonly /></td></tr>
											<tr><td height=1></td></tr>
											<tr><td class=spandix>Block # :</td><td><input type="text" id="com_bk" class="nInput" value="<?php echo $_GET['block_no']; ?>" readonly style="width: 60%" /></td></tr>
											<tr><td height=1></td></tr>
											<tr><td class=spandix>Lot # :</td><td><input type="text" id="com_lt" class="nInput" value="<?php echo  $_GET['lot_no']; ?>" readonly style="width: 60%" /></td></tr>
											<tr><td height=1></td></tr>
											<tr><td class=spandix>Lot Area (SQM):</td><td><input type="text" id="com_sqm" class="nInput" value="<?php echo  $lot['lot_area']; ?>" readonly style="width: 60%" /></td></tr>
											<tr><td height=1></td></tr>
											<tr><td class=spandix>Price Per SQM :</td><td><input type="text" id="com_psqm" class="nInput" style="width: 60%" value="<?php echo number_format($lot['price_sqm'],2); ?>" readonly /></td></tr>
											<tr><td height=1></td></tr>
											<tr><td class=spandix>Contract Price :</td><td><input type="text" id="com_cprice" class="nInput" style="width: 60%" value="<?php echo number_format($lot['contract_price'],2); ?>" readonly /></td></tr>
											<tr><td height=1></td></tr>
											<tr>
												<td class=spandix>Down Payment Option :</td>
												<td>
													<select id="com_dp_option" class="nInput" style="width: 60%" disabled >
														<option value="0" <?php if($res2['dp_option'] == "0") { echo "selected"; } ?>>Straight</option>
														<option value="15" <?php if($res2['dp_option'] == "15") { echo "selected"; } ?>>15%</option>
														<option value="20" <?php if($res2['dp_option'] == "20") { echo "selected"; } ?>>20%</option>
														<option value="25" <?php if($res2['dp_option'] == "25") { echo "selected"; } ?>>25%</option>
														<option value="30" <?php if($res2['dp_option'] == "30") { echo "selected"; } ?>>30%</option>
														<option value="40" <?php if($res2['dp_option'] == "40") { echo "selected"; } ?>>40%</option>
														<option value="50" <?php if($res2['dp_option'] == "50") { echo "selected"; } ?>>50%</option>
													</select>
												</td>
											</tr>
											<tr><td height=1></td></tr>
											<tr><td class=spandix>Down Payment Discount :</td><td><input type="text" id="com_dp_discount" class="nInput" value="<?php echo number_format($res2['dp_discount'],2); ?>" style="width: 60%" readonly /></td></tr>
												<tr><td height=1></td></tr>
											<tr><td class=spandix>Net Down Payment (Less Reservation Fee):</td><td><input type="text" id="com_dp" class="nInput" value="<?php echo number_format($res2['dp_amount'],2); ?>" readonly style="width: 60%" /></td></tr>
											<tr><td height=1></td></tr>
											<tr>
												<td class=spandix> Net Down Payment Payable In :</td>
												<td>
													<select id="com_dp_mos" class="nInput" style="width: 60%" disabled>
															<?php for ($i = 1; $i <= 6; $i++) { 
															echo "<option value='$i' ";
															if($res2['dp_terms'] == $i) { echo "selected"; }
															echo ">$i ";
															if($i < 2) { echo "Month"; } else { echo "Months"; }
															echo "</option>"; 
														} ?>
													</select>
												</td>
											</tr>
											<tr><td height=1></td></tr>
											<tr><td class=spandix>Monthly Amortization (DP) :</td><td><input type="text" id="com_dp_amrtz" class="nInput" style="width: 60%" value="<?php echo number_format($res2['dp_amrtz'],2); ?>" readonly /></td></tr>
											<tr><td height=1></td></tr>
											<tr>
												<td class=spandix>Terms (Principal Loanable) :</td>
												<td>
													<select id="com_pp_mos" class="nInput" style="width: 60%" disabled>
														<option value="1" <?php if($res2['pp_terms'] == "1") { echo "selected"; } ?>>1 Month</option>
														<option value="6" <?php if($res2['pp_terms'] == "6") { echo "selected"; } ?>>6 Months</option>
														<option value="12" <?php if($res2['pp_terms'] == "12") { echo "selected"; } ?>>12 Months</option>
														<option value="18" <?php if($res2['pp_terms'] == "18") { echo "selected"; } ?>>18 Months</option>
														<option value="24" <?php if($res2['pp_terms'] == "24") { echo "selected"; } ?>>24 Months</option>
														<option value="30" <?php if($res2['pp_terms'] == "30") { echo "selected"; } ?>>30 Months</option>
														<option value="36" <?php if($res2['pp_terms'] == "36") { echo "selected"; } ?>>36 Months</option>
														<option value="42" <?php if($res2['pp_terms'] == "42") { echo "selected"; } ?>>42 Months</option>
														<option value="48" <?php if($res2['pp_terms'] == "48") { echo "selected"; } ?>>48 Months</option>
														<option value="54" <?php if($res2['pp_terms'] == "54") { echo "selected"; } ?>>54 Months</option>
														<option value="60" <?php if($res2['pp_terms'] == "60") { echo "selected"; } ?>>60 Months</option>
														<option value="120" <?php if($res2['pp_terms'] == "120") { echo "selected"; } ?>>120 Months</option>
													</select>
												</td>
											</tr>
											<tr><td height=1></td></tr>
											<tr><td class=spandix>Monthly Amortization (Principal):</td><td><input type="text" id="com_pp_amrtz" class="nInput" style="width: 60%" value="<?php echo number_format($res2['pp_amrtz'],2); ?>" readonly /></td></tr>
											<tr><td height=1></td></tr>
										</table>
									</fieldset>
								</td>
							</tr>
						</table>
					</td>
					<td valign=top width="60%" class="td_content">		
						<table border="0" cellpadding="0" cellspacing="0" width=100%>
							<tr>
								<td width="100%" cellspacing=0 cellpadding=0>
									<table width=100% cellspacing=0 cellpadding=0 style="padding: 1px 2px 0px 2px;">
										<tr bgcolor="#595959">
											<td align=center class="dgridhead" width="10%">OR NO.</td>
											<td align=center class="dgridhead" width="10%">OR DATE</td>
											<td align=center class="dgridhead" width="15%">PAYMENT TYPE</td>
											<td align=center class="dgridhead" width="15%">DUE DATE</td>
											<td align=center class="dgridhead" width="15%">AMOUNT DUE</td>
											<td align=center class="dgridhead" width="15%">AMOUNT PAID</td>
											<td align=center class="dgridhead" width="16%">BALANCE</td>
											<td align=center class="dgridhead" width="4%">&nbsp;</td>
										</tr>
									</table>	
									<div id="details" style="height:380px; overflow: auto;">
										<table width=100% cellspacing=0 cellpadding=0 style="padding: 0 2px 2px 2px;" onMouseOut="javascript:highlightTableRowVersionA(0);">
											<?php
												$xrsq = mysql_query("SELECT or_no rs_no, DATE_FORMAT(or_date,'%m/%d/%Y') AS rs_odate, FORMAT(amount,2) AS rs_amount, amount FROM reservation_master WHERE reservation_id='$lot[rsv_id]';");
												
												if($xrsq) {
													$xrs = mysql_fetch_array($xrsq);
													echo "<tr bgcolor=\"#ffffff\">
															<td class=dgridbox align=center width=\"10%\">$xrs[rs_no]</td>
															<td class=dgridbox align=center width=\"10%\">$xrs[rs_odate]</td>
															<td class=dgridbox align=center width=\"15%\">Reservation Fee</td>
															<td class=dgridbox align=center width=\"15%\"> -- </td>
															<td class=dgridbox align=center width=\"15%\"> -- </td>
															<td class=dgridbox align=center width=\"15%\">$xrs[rs_amount]</td>
															<td class=dgridbox align=center width=\"20%\">".number_format($lot['net'] - $xrs['amount'],2)."</td>
														</tr>";
												}
												$i = 1; $runbalance = $lot['net'] - $xrs['amount']; $amountGT = $xrs['amount'];
												
												if($res2['dp_discount'] > 0) {
													echo "<tr bgcolor=\"#ffffff\">
															<td class=dgridbox align=center width=\"10%\">--</td>
															<td class=dgridbox align=center width=\"10%\">--</td>
															<td class=dgridbox align=center width=\"15%\">DP Discount</td>
															<td class=dgridbox align=center width=\"15%\"> -- </td>
															<td class=dgridbox align=center width=\"15%\"> -- </td>
															<td class=dgridbox align=center width=\"15%\">".number_format($res2[dp_discount],2)."</td>
															<td class=dgridbox align=center width=\"20%\">".number_format($runbalance - $res2['dp_discount'],2)."</td>
														</tr>";
														$runbalance = $runbalance - $res2['dp_discount']; $amountGT+=$res2['dp_discount'];
												}
												
												
												for($z = 1; $z <= $res2['dp_terms']; $z++) {
													list($dd,$dm) = getArray("select date_add('$res2[trans_date]', INTERVAL $z MONTH), date_format(date_add('$res2[trans_date]', INTERVAL $z MONTH),'%m/%d/%Y');");
													list($y,$m,$d) = explode('-',$dd);
													list($or_no,$or_date,$amount) = getArray("select or_no,date_format(or_date,'%m/%d/%Y'),amount from payments where file_id='$_GET[fileID]' and month='$m' and year='$y' and type='DP';");
													$runbalance-=$amount; $amountGT+=$amount;
													if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
													echo "<tr bgcolor=\"$bgC\" onmouseover=\"highlightTableRowVersionA(this, '#3399ff');\"  id='row_$i' onclick=\"tagPayment(this,'DP|$m|$y|$_GET[fileID]|$_GET[block_no]|$_GET[lot_no]|$res2[dp_amrtz]');\">
														<td class=dgridbox align=center width=\"10%\">$or_no</td>
														<td class=dgridbox align=center width=\"10%\">$or_date</td>
														<td class=dgridbox align=center width=\"15%\">Down Payment</td>
														<td class=dgridbox align=center width=\"15%\">$dm</td>
														<td class=dgridbox align=center width=\"15%\">".number_format($res2['dp_amrtz'],2)."</td>
														<td class=dgridbox align=center width=\"15%\">".number_format($amount,2)."</td>
														<td class=dgridbox align=center width=\"20%\">".number_format($runbalance,2)."</td>
													</tr>"; $i++; $amount = 0; $or_no = ""; $or_date = "";
												}
												
												$pp_beg = $dd;
												
												for($w = 1; $w <= $res2['pp_terms']; $w++) {
													list($dd,$dm) = getArray("select date_add('$pp_beg', INTERVAL $w MONTH), date_format(date_add('$pp_beg', INTERVAL $w MONTH),'%m/%d/%Y');");
													list($y,$m,$d) = explode('-',$dd);
													list($or_no,$or_date,$amount) = getArray("select or_no,date_format(or_date,'%m/%d/%Y'),amount from payments where file_id='$_GET[fileID]' and month='$m' and year='$y' and type='AMRTZ';");
													$runbalance-=$amount; $amountGT+=$amount;
													if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
													echo "<tr bgcolor=\"$bgC\" onmouseover=\"highlightTableRowVersionA(this, '#3399ff');\" id='row_$i' onclick=\"tagPayment(this,'AMRTZ|$m|$y|$_GET[fileID]|$_GET[block_no]|$_GET[lot_no]|$res2[pp_amrtz]');\">
														<td class=dgridbox align=center width=\"10%\">$or_no</td>
														<td class=dgridbox align=center width=\"10%\">$or_date</td>
														<td class=dgridbox align=center width=\"15%\">Mo. Amortization</td>
														<td class=dgridbox align=center width=\"15%\">$dm</td>
														<td class=dgridbox align=center width=\"15%\">".number_format($res2['pp_amrtz'],2)."</td>
														<td class=dgridbox align=center width=\"15%\">".number_format($amount,2)."</td>
														<td class=dgridbox align=center width=\"20%\">".number_format($runbalance,2)."</td>
													</tr>"; $i++; $amount = 0; $or_no = ""; $or_date = "";
												}
												
													$perc = ROUND(($amountGT / $lot['net']) * 100,2); 
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
												<?php if($res2['status'] != 'Closed') { ?>
													<button onClick="passMe();" class="buttonding" id="btn_rsv"><img src="images/icons/crr.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Make Payment for Selected Schedule</b></button>
													<button onClick="passMyDetails();" class="buttonding" id="btn_dpst"><img src="images/icons/contract.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;View Payment Details</b></button>
													<button onClick="generateSOA('<?php echo $_GET['fileID']; ?>','<?php echo $lot['rsv_id']; ?>');" class="buttonding" id="btn_pay"><img src="images/icons/apv256.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Print Client's Statement of Account</b></button>
												<?php } else {
													list($closed_by, $closed_on) = getArray("SELECT b.fullname, DATE_FORMAT(closed_on, '%m/%d/%Y %r') AS closed_on FROM contracts a LEFT JOIN user_info b ON a.closed_by=b.emp_id WHERE a.file_id='$_GET[fileID]'");
													echo "<span style='font-weight: bold; font-size: 20px; color: red; padding-left: 10px;'>ACCOUNT CLOSED</span><br/>";
													echo "<span style='font-weight: bold; font-size: 12px; color: red; padding-left: 10px;'>Closed By: $closed_by</span><br/>";	
													echo "<span style='font-weight: bold; font-size: 12px; color: red; padding-left: 10px;'>Closed On: $closed_on</span><br/>";													
												} ?>
											</td>
										</tr>
									</table>
								</td>
								<td width="60%">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td class="tbold">Total Amount Paid &raquo;</td>
											<td align="right" style="padding-right: 5px;"><input type="text" id="total" class="gridInput" style="width: 60%; text-align: right;" value="<?php echo number_format($amountGT,2); ?>" readonly /></td>
										</tr>
										<tr><td height=2></td></tr>
										<tr>
											<td class="tbold">% Payment Completed &raquo;</td>
											<td align="right" style="padding-right: 5px;"><input type="text" id="total" class="gridInput" style="width: 60%; text-align: right;" value="<?php echo $perc; ?>%" readonly /></td>
										</tr>
										<tr><td height=2></td></tr>
										<tr>
											<td class="tbold">Outstanding Balance &raquo;</td>
											<td align="right" style="padding-right: 5px;"><input type="text" id="total" class="gridInput" style="width: 60%; text-align: right;" value="<?php echo number_format($runbalance,2); ?>" readonly /></td>
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