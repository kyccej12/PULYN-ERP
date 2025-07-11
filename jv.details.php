<?php
	session_start();
	//ini_set("display_errors","On");
	require_once "handlers/_jvfunct.php";
	
	$p = new myJV;
	$isReadOnly = ''; $isDisabled = '';
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['j_no']) && $_REQUEST['j_no']!=''){ 
		$j_no = $_REQUEST['j_no']; 
		$res = $p->getArray("select *, date_format(j_date,'%m/%d/%Y') as d8, concat('DOCUMENT NO. ',cy,'-',lpad(j_no,10,0)) as jno, if(ca_date='0000-00-00','',date_format(ca_date,'%m/%d/%Y')) as ca_d8 from journal_header where j_no = '$j_no' and branch = '$_SESSION[branchid]';");
	} else {
		$j_no = ''; $res['status'] = "Active"; $dS = "1"; $cSelected = "N"; $res['locked'] = 'N'; $res['created_by'] = $uid;
	}
	
?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="js/jquery.dialogextend.js"></script>
	<script language="javascript" src="js/jv.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>
		$('html').click(function(){ $("#suggestions").fadeOut(200); });
		$(function() { 
			<?php if ($res['status'] == 'Active') { ?> $("#j_date").datepicker(); $("#ca_date").datepicker(); <?php }?>
			 $("#ref_date").datepicker();
			 $("#app_invoice_docdate").datepicker();
			
		  $('#acct_description').autocomplete({
			source:'suggestAccount.php', 
			minLength:3,
			select: function(event,ui) {
				$("#acct_code").val(ui.item.acct_code);
				$("#acct_description").val(decodeURIComponent(ui.item.acct));
			}
		   });
		   
		    $('#customer_id').autocomplete({
				source:'suggestContacts.php', 
				minLength:3,
				select: function(event,ui) {
					$("#customer_id").val(ui.item.cid);
				}
			});
			
			 $('#app_client').autocomplete({
				source:'suggestContacts.php', 
				minLength:3,
				select: function(event,ui) {
					$("#app_client").val(ui.item.cid);
				}
			});
			
			$('#app_acct').autocomplete({
				source:'suggestAcctCodeOnly.php', 
				minLength:3
			});
			
			$('#app_invoice_name').autocomplete({
				source:'suggestContactNoCode.php', 
				minLength:3,
				select: function(event,ui) {
					$("#app_invoice_tin").val(ui.item.tin_no);
					$("#app_invoice_address").val(decodeURIComponent(ui.item.addr));
				}
			});
			
			$('#app_debit_acct').autocomplete({
				source:'suggestAcctCodeOnly.php', 
				minLength:3
			});

			getTotals('<?php echo $j_no; ?>');
		});

	</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div style="padding: 10px;">
	<form name="xform" id="xform">
		<input type="hidden" name="cSelected" id="cSelected" value="<?php echo $cSelected; ?>">
		<input type=hidden name="prev_j_date" id="prev_j_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; } ?>" />
		<table width=99% cellpadding=0 cellspacing=0 border=0 align=center>
			<tr>
				<td class="upper_menus" align=left>
					<?php $p->setHeaderControls($res['status'],$res['locked'],$j_no,$_SESSION['userid'],$dS,$res['linked'],$_SESSION['utype']); ?>
				</td>
				<td align=right style='padding-right: 5px;'><?php if($j_no) { $p->setNavButtons($j_no); } ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
		<table width=100% cellpadding="0" cellspacing="0" align=center class="tableRounder">
			<tr>
				<td align=left class="gridHead-left"></td>
				<td align=right class="gridHead-right"><?php echo $res['jno']; ?></td>
			</tr>
			<tr>
				<td width="100%" colspan=2>
					<table border="0" cellpadding="0" cellspacing="1" width=100%>
						<tr><td height=4></td></tr>
						<tr>
							<td align="right" width="15%" class="bareBold" style="padding-right: 5px;">Document No. :</td>
							<td align=left>
								<input class="gridInput" style="width:140px;" type=text name="j_no" id="j_no" value="<?php echo $j_no; ?>"  readonly />
							</td>
							<td align="right" width="15%" class="bareBold" style="padding-right: 5px;"></td>
							<td align=left>
								<input type=hidden name="ca_refno" id="ca_refno" value="<?php echo $res['ca_refno']; ?>"  />
							</td>
						</tr>
						<tr><td height=2></td></tr>
						<tr>
							<td align="right" width="15%" class="bareBold" style="padding-right: 5px;">Document Date&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:140px;" type=text name="j_date" id="j_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; } ?>" />
							</td>
							<td align="right" width="15%" class="bareBold" style="padding-right: 5px;"></td>
							<td align=left>
								<input type=hidden name="ca_date" id="ca_date" value="<?php echo $res['ca_d8']; ?>"  />
							</td>
						</tr>
						<tr><td height=2></td></tr>
						<tr>
							<td align=right valign="top" class=bareBold style="padding-right: 5px;">Explanation :</td>
							<td align=left colspan=3>
								<textarea class="gridInput" type="text" id="remarks" style="width:72%;height:50px;"><?php echo $res['explanation']; ?></textarea>
							</td>
						</tr>
						<tr><td height=4></td></tr>
					</table>
				</td>
			</tr>
		</table>
		<table><tr><td height=2></td></tr></table>
		<table cellspacing=0 cellpadding=0 border=0 width=100%>
			<tr bgcolor="#887e6e">
				<td align=center class="ui-state-default" style="padding: 5px;" width="7%">REF #</td>
				<td align=center class="ui-state-default" width="7%">REF DATE</td>
				<td align=center class="ui-state-default" width="7%" style="padding: 5px;">REF TYPE</td>
				<td align=left class="ui-state-default" width="7%" style="padding: 5px;">CLIENT</td>
				<td align=left class="ui-state-default" width="7%" style="padding: 5px;">ACCT CODE</td>
				<td align=left class="ui-state-default" style="padding: 5px;">ACCT DESCRIPTION</td>
				<td align=center class="ui-state-default" width="10%" style="padding: 5px;">COST CENTER</td>
				<td align=center class="ui-state-default" width="7%" style="padding: 5px;">DEBIT</td>
				<td align=center class="ui-state-default" width="7%" style="padding: 5px;">CREDIT</td>
				<td align=center class="ui-state-default" width="10%" style="padding: 5px;"><?php if($res['status'] == 'Active') { echo "AMOUNT"; }?></td>
				<td align=center class="ui-state-default" width="15">&nbsp;</td>
			</tr>
			<?php if(($res['status'] == "Active" || $res['status'] == "") && $res['locked'] != 'Y') { ?>
			<tr bgcolor="ededed">
				<td align=center class="grid" align=center><input class="gridInput" type=text name="ref_no" id="ref_no" style="width:90%"/></td>
				<td align=center class="grid" align=center><input class="gridInput" type=text name="ref_date" id="ref_date" style="width:90%"/></td>
				<td align=center class="grid" align=center>
					<select name="ref_type" id="ref_type" style="width:95%" class="gridInput">
						<option Value = "JV">- JV -</option>
						<option value = "CV">- CV -</option>
						<option value = "APV">- APV -</option>
						<option value = "DC">- DA/CA -</option>
						<option value = "SI">- Invoice -</option>
					</select>
				</td>
				<td align=center class="grid" align=center><input class="gridInput" type=text name="customer_id" id="customer_id" style="width:90%"  /></td>
				<td align=center class="grid" colspan=2>
					<input type="hidden" name="acct_code" id="acct_code" />
					<input type=text class="inputSearch" style="padding-left: 22px; width: 100%;" name="acct_description" id="acct_description" />
				</td>
				<td align=center class="grid">
					<?php echo $p->constructCostCenter(); ?>
				</td>
				<td align=center class="grid"><input type="radio" name="side" id="side" value="DB"></td>
				<td align=center class="grid"><input type="radio" name="side" id="side" value="CR"></td>
				<td align=center class="grid"><input class="gridInput" type=text id="amount" style="width: 70%; text-align: right;"/>&nbsp;<a href="#" onclick="javascript: addDetails();" title="Add Item"><img src="images/icons/add-2.png" width=18 height=18 style="vertical-align: middle;" /></a></td>
				<td align=center class="grid"></td>
			</tr>
			<?php } ?>
		</table>
		<div id="jdetails" style="height: 160px; overflow-x: auto; border-bottom: 3px solid #4297d7;">
			<?php $p->JVDETAILS($j_no,$trace_no) ?>
		</div>
		<table width=100% cellpadding = 0 cellspacing = 0 align=right style="padding-top: 10px;">
			<tr>
				<td align=left>Voucher Prepared By :  <input type="text" name="createBy" id="createBy" class="gridInput" style="width: 250px;" value="<?php $p->getUname($res['created_by']); ?>" readonly>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Posted On :  <input type="text" name="postOn" id="postOn" class="gridInput" style="width: 200px;" value="<?php $p->getUname($res['postOn']); ?>" readonly></td>
				<td align=right 30%><b>No. of Line Entries</b> :  <input type="text" name="noLines" id="noLines" class="gridInput" style="width: 100px;text-align: right;" value=""></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr>
				<td align=right colspan=2 width=100%>Total Debit Amount :  <input type="text" name="dbTotal" id="dbTotal" class="gridInput" style="width: 100px;text-align: right;" value=""></td>
			</tr>
			<tr><td height=2></td></tr>
			<tr>
				<td align=right colspan=2 width=100%>Total Credit Amount :  <input type="text" name="crTotal" id="crTotal" class="gridInput" style="width: 100px;text-align: right;" value=""></td>
			</tr>
		</table>
	</form>
</div>
<div id="applyInvoice" style="padding: 10px; display: none;">
	<form name="frmInvoice" id="frmInvoice">
		<table align=center border=0 width=100% cellpadding=0 cellspacing=3>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Payee/Supplier:</td>
				<td align=left>
					<input type=text id="app_invoice_name" name="app_invoice_name" class="inputSearch2" style="width:220px; padding-left: 22px;">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;" valign=top>Payee/Supplier's Address :</td>
				<td align=left>
					<textarea name="app_invoice_address" id="app_invoice_address" style="width: 220px;font-size: 11px;" rows=3></textarea>
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Payee/Supplier's Tin # :</td>
				<td align=left>
					<input type=text id="app_invoice_tin" name="app_invoice_tin" class="gridInput" style="width:140px">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Invoice/Reference # :</td>
				<td align=left>
					<input type=text id="app_invoice_docno" name="app_invoice_docno" class="gridInput" style="width:140px">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Date :</td>
				<td align=left>
					<input type=text id="app_invoice_docdate" name="app_invoice_docdate" class="gridInput" style="width:140px" >
				</td>
			</tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Amount :</td>
				<td align=left>
					<input type=text id="app_invoice_amount" name="app_invoice_amount" class="gridInput" style="width:140px">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Default Purchases Account :</td>
				<td align=left>
					<input type=text id="app_debit_acct" name="app_debit_acct" class="inputSearch2" style="width:140px; padding-left: 22px;">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Vatable :</td>
				<td align=left>
					<select name="app_vatable" id="app_vatable" class="gridInput" style="width:140px;">
						<option value="Y">- Yes -</option>
						<option value="N">- No -</option>
					</select>
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Tax Code :</td>
				<td align=left>
					<select name="app_atc" id="app_atc" class="gridInput" style="width:140px;">
						<option value="">- NA -</option>
						<?php
							$tq1 = $p->dbquery("select atc_code, description, rate from options_atc order by rate;");
							while(list($aa1,$bb1,$cc1) =$tq1->fetch_array(MYSQLI_BOTH)) {
								echo "<option value='$aa1' title='$bb1'>$aa1 ($cc1 %)</option>";
							}
						?>	
					</select>
				</td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
	</form>
</div>
<div id="applyDivs" style="padding: 10px; display: none;">
	<form name="applydocs" id="applydocs">
		<table align=center border=0 width=100% cellpadding=0 cellspacing=3>
			<tr><td width=35% class=spandix-l align=right style="padding-right: 10px;">Client Code :</td>
				<td align=left>
					<input type=text id="app_client" name="app_client" class="inputSearch2" style="width:90%; font-size: 11px;  padding-left: 20px;">
				</td>
			</tr>
			<tr><td width=35% class=spandix-l align=right style="padding-right: 10px;">Acct. Code :</td>
				<td align=left>
					<input type=text id="app_acct" name="app_acct" class="inputSearch2" style="width:90%;  padding-left: 20px;">
				</td>
			</tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Doc Type :</td>
				<td align=left>
					<select name="app_doctype" id="app_doctype" class="gridInput" style="width: 90%; font-size: 11px;" onchange="checkForDoc(this.value);">
						<option value="">- Select Document type -</option>
						<option value="SI">Invoice</option>
						<option value="CR">Collection Receipt</option>
						<option value="APV">Accts. Payable Voucher</option>
						<option value="CV">Check Voucher</option>
						<option value="JV">Journal Voucher</option>
						<option value="DC">Debit/Credit Advise</option>
					</select>
				</td>
			</tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Ref # :</td>
				<td align=left>
					<input type=text id="app_docno" name="app_docno" class="gridInput" style="width:140px;" readonly>
				</td>
			</tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Ref Date :</td>
				<td align=left>
					<input type=text id="app_docdate" name="app_docdate" class="gridInput" style="width:140px" readonly>
				</td>
			</tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Balance Due :</td>
				<td align=left>
					<input type=text id="app_amount" name="app_amount" class="gridInput" style="width:140px" >
					<input type="hidden" id="app_side" name="app_side">
					<input type="hidden" id="app_lid" name="app_lid">
				</td>
			</tr>
			<tr><td height=8></td></tr>
		</table>
		<table align=center cellspacing=0 cellpadding=0 width=100% style="font-weight:bold; border-bottom: 1px solid black; border-top: 1px solid black;">
			<tr>
				<td align="left" class="gridHead" width="20%">Doc No.</td>
				<td align="center" class="gridHead" width="20%">Doc Date</td>
				<td align="right" class="gridHead" width="30%">Amount</td>
				<td align="right" class="gridHead">Balance</td>
				<td width=18 class="gridHead" style="width: 15px;">&nbsp;</td>
			</tr>
		</table>
		<div name="balances" id="balances" style="height: 150px; overflow: auto;"></div>
		<table align=center>
			<tr><td height=8></td></tr>
			<tr><td></td>
				<td>
					<button type="button" onClick='javascript: applyNow();' style="height: 30px;"><img src="images/icons/down3.png" with=16 height=16 border=0 align=absmiddle />&nbsp;Apply Document</button>
				</td>
			</tr>
		</table>
	</form>
</div>
<div class="suggestionsBox" id="suggestions" style="display: none;">
	<div class="suggestionList" id="autoSuggestionsList">&nbsp;</div>
</div>
<div id="loaderMessage" title="Processing..." style="display: none;">
	<p><span style="float:left; margin:0 7px 20px 0;"><img src="images/loader.gif" /></span>Please wait while the system is processing your request...</p>
</div>
</body>
</html>