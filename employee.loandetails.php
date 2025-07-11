<?php
	include("includes/dbUSE.php");
	session_start();
	
	if(isset($_GET['fid']) && $_GET['fid'] != "") { $res = getArray("select *,date_format(date_availed,'%m/%d/%Y') as tdate from hris.e_loans where file_id='$_GET[fid]';"); }
	
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
	function saveLoan(fid) {
		var msg = "";
		
		if($("#id_no").val() == "") { msg = msg + "- You must select an employee for this Loan<br/>"; }
		if($("#loan_type").val() == "") { msg = msg + "- You must select <b><i>Loan Type</i></b><br/>"; }
		if(isNaN(parent.stripComma($("#amount").val())) == true) { msg  = msg + "Invalid amount specified<br/>"}
		
		if(msg!="") {
			parent.sendErrorMessage("<b>Unable to continue due to the following error(s):</b><br/><br/>" + msg);
		} else {
			var url = $(document.frmloan).serialize();
				url = "mod=saveLoan&"+url;
			$.post("payroll.datacontrol.php", url);
			alert("Record Successfuly Saved!"); 
			parent.close_div2();
			parent.showLoans();	
		}
	}
	
	function computeAmrtz() {
		if(isNaN($("#terms").val()) == true || $("#terms").val() == "") {
			var terms = 0; $("#terms").val('0');
		} else { var terms = parseFloat($("#terms").val()); }
		if(isNaN(parent.stripComma($("#amount").val())) == true || $("#terms").val() == "") {
			var amt = 0; $("#amount").val('0.00');
			   
		} else { var amt = parseFloat(parent.stripComma($("#amount").val()));  amt = amt.toFixed(2); }
		
		var dedu = amt / terms;
		    dedu = dedu.toFixed(2);
		$("#dedu_amount").val(parent.kSeparator(dedu));
		$("#amount").val(parent.kSeparator(amt));
		
	}
	
	function deleteLoan(fid) {
		if(confirm("Are you sure you want to delete this record?") == true) {
			$.post("payroll.datacontrol.php", { mod: "deleteLoan", fid: fid, sid: Math.random() }, function(data) { 
				if(data == 'error') {
					parent.sendErrorMessage("Unable to delete this record. The system has found loan payments posted on Payroll Register.");
				} else {
					alert("Record Successfully Deleted!"); 
					parent.close_div2();
					parent.showLoans();
				}
			},"html");
		}
	}
	
	$(function() { $("#loan_date").datepicker(); });
</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">

 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<form name="frmloan" id="frmloan">
		<input type="hidden" id = "fid" name="fid" value="<?php echo $_GET['fid']; ?>">
		<tr>
			<td style="padding:0px;" valign=top>
				<table align=center width=100% height=30 border=0 cellspacing=0 cellpadding=0  style="border-bottom:2px solid black; background-color:#595959; background-image: url(images/4.jpg); font-weight:bold; color:#ffffff;">
					<tr>
						<td align="left" style="padding-left: 3px; font-weight: bold; font-size: 12px;" valign=middle><img src="images/icons/loans.png" border=0 height=22 width=22 align=absmiddle />&nbsp;EMPLOYEE LOANS</td>
						<td align=right width="15%" style="padding-right: 2px;" valign=middle>
							<a href="javascript: parent.close_div2();" style="text-decoration: none; font-size: 11px; color: #ffffff;"><img src="images/icons/button-logout-text.png" border=0 title="Close"  /></a>
						</td>
					</tr>
				</table>
				<table width=100% border=0 cellspacing=2 cellpadding=0>
					<tr>
						<td valign=top width="90%" class="td_content" style="padding: 10px;">		
							<table border="0" cellpadding="0" cellspacing="0" width=100%>
								<tr><td class="spandix-l" width=35%>Employee :</td>
									<td align=left>
										<select name="id_no" id="id_no" style="width: 90%;" class="nInput">
											<option value="">- Select Employee -</option>
											<?php
												$elq = dbquery("select id_no, concat(lname,', ',fname) as emp from hris.e_master where `filestatus` = 'Active' order by lname;");
												while(list($idno,$name) = mysql_fetch_array($elq)) {
													print "<option value='$idno' ";
													if($res['id_no'] == $idno) { print "selected"; }
													print ">$name</option>";
												}
											?>
										</select>
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td class="spandix-l" width=35%>Loan Type :</td>
									<td align=left>
										<select name="loan_type" id="loan_type" style="width: 90%;" class="nInput">
											<option value="">- Select Loan -</option>
											<?php
												$lq = dbquery("select type, description from hris.e_loantype;");
												while(list($type,$desc) = mysql_fetch_array($lq)) {
													print "<option value='$type' ";
													if($res['loan_type'] == $type) { print "selected"; }
													print ">$desc</option>";
												}
											?>
										</select
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td class="spandix-l" width=35%>Loan Date :</td>
									<td align=left>
										<input type="text" name="loan_date" id="loan_date" style="width: 138px;" class="nInput" value="<?php if($res['date_availed'] !='') { echo $res['tdate']; } else { echo date('m/d/Y'); }; ?>">
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td class="spandix-l" width=35%>Amount :</td>
									<td align=left>
										<input type="text" name="amount" id="amount" style="width: 138px;" class="nInput" value="<?php echo number_format($res['amount'],2); ?>" onchange="computeAmrtz();">
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr>
									<td class="spandix-l" width="35%">Terms (No. of Pay Days) :</td>
									<td align="left">
										<input type="text" name="terms" id="terms" style="width: 138px;" class="nInput" value="<?php if($res['terms'] !='') { echo $res['terms']; } else { echo '1'; }; ?>" onchange="computeAmrtz();">
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td class="spandix-l" width=35%>Ded'n Amount :</td>
									<td align=left>
										<input type="text" name="dedu_amount" id="dedu_amount" style="width: 138px" class="nInput" value="<?php echo number_format($res['dedu_amount'],2); ?>" readonly>
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td class="spandix-l" width=35%>Remarks :</td>
									<td align=left>
										<textarea name="remarks" id="remarks" style="width: 90%" rows=2><?php echo $res['remarks']; ?></textarea>
									</td>
								</tr>
								<tr><td height=4 colspan="2"></td></tr>
								<tr><td colspan=2><hr></hr></td></tr>
								<tr><td height=4></td></tr>
								<tr>
									<td align=center colspan=2>
										<button type="button" onClick="saveLoan(<?php echo $_GET['rid']; ?>);" class="buttonding" id="btn_rsv" style="width:150px;"><img src="images/icons/floppy.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Save Record</b></button>
										<?php if(isset($_GET['fid']) && $_GET['fid'] != "") { ?>
										<button type="button" onClick="deleteLoan('<?php echo $_GET['fid']; ?>');" class="buttonding" id="btn_rsv" style="width:170px;"><img src="images/icons/delete.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Delete Record</b></button>
										<?php } ?>
									</td>
								</tr>
							</table>
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