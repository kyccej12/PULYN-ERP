<?php
	include("includes/dbUSE.php");
	$res = getArray("select fname,mname,lname,tel_no,address,email from contracts where file_id='$_GET[fileID]';");
	session_start();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Ozian Realty Development</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link href="style/jquery-ui.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/jquery-ui.js"></script>
<script language="javascript" src="js/date.js"></script>
<script>

	var net = "<?php echo $lot['net']; ?>";

	$(function() {
		$("#or_date").datepicker();
		$("#check_date").datepicker();
	});
	
	function savePay(submod) {
		$.post("tennant.datacontrol.php", { mod: "savePayment", file_id: $("#file_id").val(), or_no: $("#or_no").val(), or_date: $("#or_date").val(), type: $("#type").val(), month: $("#month").val(), year: $("#year").val(), amount: $("#amount_due").val(), pay_type: $("#p_type").val(), check_no: $("#check_no").val(), check_date: $("#check_date").val(), recby: $("#receivedby").val(), sid: Math.random() },function() {
			alert("Record Successfully Saved...");
			parent.close_div2();
			parent.showPayHistory($("#file_id").val());
		});
	}
	
	
</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<input type="hidden" id = "file_id" value="<?php echo $_GET['fileID']; ?>">
	<tr>
		<td style="padding:0px;" valign=top>
			<table align=center width=100% height=30 border=0 cellspacing=0 cellpadding=0  style="border-bottom:2px solid black; background-color:#595959; background-image: url(images/4.jpg); font-weight:bold; color:#ffffff;">
				<tr>
					<td align="left" style="font-weight: bold; font-size: 12px;" valign=middle>&nbsp;&nbsp; Payment Details</td>
					<td align=right width="6%" style="padding-right: 5px;" valign=middle>
						<a href="javascript: parent.close_div2();" style="text-decoration: none; font-size: 11px; color: #ffffff;"><img src="images/icons/button-logout-text.png" border=0 title="Close"  /></a>
					</td>
				</tr>
			</table>
			<table width=100% border=0 cellspacing=2 cellpadding=0>
				<tr>
					<td valign=top width="90%" class="td_content" style="padding: 10px;">		
						<table border="0" cellpadding="0" cellspacing="0" width=100%>
							<tr>
								<td width=33%>
									<input type="text" id="lname" class="nInput" style="width: 90%;" value="<?php echo $res['lname']; ?>" readonly />
									<br>
									<span class="spandix-l">LAST NAME</span>
								</td>
								<td width=33%>
									<input type="text" id="fname" class="nInput" style="width: 90%;" value="<?php echo $res['fname']; ?>" readonly/>
									<br>
									<span class="spandix-l">FIRST NAME</span>
								</td>
								<td width=33%>
									<input type="text" id="mname" class="nInput" style="width: 90%;" value="<?php echo $res['mname']; ?>" readonly />
									<br>
									<span class="spandix-l">MIDDLE NAME</span>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td width="100%" colspan=3>
									<textarea id="address" style="width:95%;" rows=1 readonly><?php echo $res['address']; ?></textarea>
									<br>
									<span class="spandix-l">ADDRESS:</span>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td width="100%" colspan=3>
									<input type="text" id="telno" class="nInput" style="width: 60%;" value="<?php echo $res['tel_no']; ?>" readonly />
									<br>
									<span class="spandix-l">TELEPHONE/MOBILE NO.</span>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td width="100%" colspan=3>
									<input type="text" id="email" class="nInput" style="width: 60%;" value="<?php echo $res['email']; ?>" readonly />
									<br>
									<span class="spandix-l">EMAIL ADDRESS</span>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr><td colspan=3><hr></hr></td></tr>
							<tr>
								<td width="100%" colspan=3>
									<fieldset>
										<legend><span class="spandix">Payment Details</span></legend>
											<table width=100% cellpadding=0 cellspacing=0 border=0>
												<tr><td class=spandix width=50%>OR # :</td><td><input type="text" id="or_no" class="nInput" value=""  style="width: 60%" /></td></tr>
												<tr><td height=1></td></tr>
												<tr><td class=spandix>OR Date :</td><td><input type="text" id="or_date" class="nInput" style="width: 60%" value="<?php echo date('m/d/Y'); ?>" /></td></tr>
												<tr><td height=1></td></tr>
												<tr><td class=spandix>Block # :</td><td><input type="text" id="bk" class="nInput" value="<?php echo $_GET['block_no']; ?>" readonly style="width: 60%" /></td></tr>
												<tr><td height=1></td></tr>
												<tr><td class=spandix>Lot # :</td><td><input type="text" id="lt" class="nInput" value="<?php echo  $_GET['lot_no']; ?>" readonly style="width: 60%" /></td></tr>
												<tr><td height=1></td></tr>
												<tr><td class=spandix valign=top>Payment For :</td>
													<td>
														<input type="hidden" id="month" value="<?php echo $_GET['month']; ?>">
														<input type="hidden" id="year" value="<?php echo $_GET['year']; ?>">
														<input type="hidden" id="type" value="<?php echo $_GET['type']; ?>">
														<input type="text" class="nInput" value="<?php if($_GET['type'] == 'DP') { echo "Down Payment Amortization"; } else { echo "Monthly Amortization"; }; ?>" readonly style="width: 60%" /><br/>
														<input type="text" class="nInput" value="<?php $month = _month($_GET['month']); echo $month; ?>" readonly style="width: 60%" /><br/>
														<input type="text" class="nInput" value="<?php echo  $_GET['year']; ?>" readonly style="width: 60%" />
													</td>
												</tr>
												<tr><td height=1></td></tr>
												<tr><td class=spandix>Amount Due:</td><td><input type="text" id="amount_due" class="nInput" style="width: 60%" value="<?php echo number_format($_GET['amount'],2); ?>" /></td></tr>
												<tr><td height=1></td></tr>
												<tr><td class=spandix>Payment Type:</td>
													<td>
														<select id="p_type" class="nInput" style="width: 60%;">
															<option value="Cash" >Cash Payment</option>
															<option value="Check" >Check Payment</option>
														</select>
													</td>
												</tr>
												<tr><td height=1></td></tr>
												<tr><td class=spandix>Check # (For Check Payment Only) :</td><td><input type="text" id="check_no" class="nInput" style="width: 60%" /></td></tr>
												<tr><td height=1></td></tr>
												<tr><td class=spandix>Check Date (For Check Payment Only) :</td><td><input type="text" id="check_date" class="nInput"   style="width: 60%" /></td></tr>
												<tr><td height=1></td></tr>
												<tr><td class=spandix>Payment Received By :</td><td><input type="text" id="receivedby" class="nInput" style="width: 60%" /></td></tr>
											</table>
									  </fieldset>
								</td>
							</tr>
							<tr><td height=4></td></tr>
							<tr>
								<td align=center colspan=3>
									<button onClick="savePay();" class="buttonding" id="btn_rsv" style="width: 220px;"><img src="images/icons/floppy.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Save & Finalize Payment</b></button>
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