<?php	
	
	session_start();
	//ini_set("display_errors","On");
	
	unset($_SESSION['ques']);
	require_once "handlers/_cvfunct.php";	
	$p = new myCV;
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['cv_no']) && $_REQUEST['cv_no']!='') { 
		$res = $p->getArray("select *, lpad(cv_no,6,0) as cvno, lpad(payee,6,0) as xpayee, date_format(cv_date,'%m/%d/%Y') as d8, if(check_date != '0000-00-00',date_format(check_date,'%m/%d/%Y'),'') as cd8, if(ca_date!='0000-00-00',date_format(ca_date,'%m/%d/%Y'),'') as ca_d8, if(source = '10100','',check_no) as ckno from cv_header where cv_no='$_REQUEST[cv_no]' and branch = '1';");
		$cv_no = $res['cvno']; $status = $res['status']; $lock=$res['lock']; $cSelected = "Y";
	} else {  
		list($cv_no) = $p->getArray("select lpad((ifnull(max(cv_no),0)+1),6,0) from cv_header where branch = '1';"); 
		$status = "Active"; $lock = "N"; $dS = "1"; $cSelected = "N";
	}
		
	
	
	
	if($res['status'] != "Active") { $isReadOnly = "readonly"; $isDisabled = "disabled"; }
	
?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/tautocomplete.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="js/jquery.dialogextend.js"></script>
	<script language="javascript" src="js/tautocomplete.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script language="javascript" src="js/cv.js"></script>
	<script>
		
		$(document).ready(function($){
			<?php if($status == 'Posted' || $status == 'Cancelled') { echo "$(\"#xform :input\").prop('disabled',true);"; } ?>
			$('#amount').bind('keypress', function(e) { if(e.keyCode ==13){ addDetails(); } });
			$("#cv_date").datepicker(); $("#ref_date").datepicker(); $("#check_date").datepicker(); $("#app_docdate").datepicker(); $("#ca_date").datepicker();
			
			$('#customer_id').autocomplete({
				source:'suggestContacts.php', 
				minLength:3,
				select: function(event,ui) {
					$("#cSelected").val('Y');
					$("#customer_id").val(ui.item.cid);
					$("#customer_name").val(decodeURIComponent(ui.item.cname));
					$("#cust_address").val(decodeURIComponent(ui.item.addr));
					saveCVHeader();
				}
			});
			
			$('#app_acct').autocomplete({
				source:'suggestAccount.php', 
				minLength:3
			});
			
			$('#app_client').autocomplete({
				source:'suggestContacts.php', 
				minLength:3,
				select: function(event,ui) {
					$("#app_client").val(ui.item.cid);
				}
			});
			
			$('#app_payee_name').autocomplete({
				source:'suggestContactNoCode.php', 
				minLength:3,
				select: function(event,ui) {
					$("#app_payee_tin").val(ui.item.tin_no);
					$("#app_payee_address").val(decodeURIComponent(ui.item.addr));
				}
			});
			
			$('#app_debit_acct').autocomplete({
				source:'suggestAcctCodeOnly.php', 
				minLength:3
			});
			
			var myAcct = $("#acct_description").tautocomplete({
				width: "600px",
				columns: ['Account Code','Description','Account Group'],
				hide: false,
				ajax: {
					url:  "suggestAccount-2.php",
					type: "GET",
					data:function() { var x = { term: myAcct.searchdata() }; return x; },
					success: function (data) {
						var filterData = [];
						var searchData = eval("/" + myAcct.searchdata() + "/gi");
						$.each(data, function (i,v) {
							if (v.description.search(new RegExp(searchData)) != -1) {
								filterData.push(v);
							}
						});
						return filterData;
					}
				},
				onchange: function () {
					var cellData = myAcct.all();
					$("#acct_code").val(cellData['Account Code']);
					$("#acct_description").val(cellData['Description']);
				}
			});
		});	
	</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div style="padding: 10px;">
	<form name="xform" id="xform">
		<input type=hidden id="cv_no" value="<?php echo $cv_no; ?>">
		<input type="hidden" name="cSelected" id="cSelected" value="<?php echo $cSelected; ?>">
		<input type=hidden name="prev_cv_date" id="prev_cv_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>">
		<table width=100% cellpadding=0 cellspacing=0 border=0 align=center>
			<tr>
				<td class="upper_menus" align=left>
					<?php $p->setHeaderControls($res['status'],$res['locked'],$cv_no,$_SESSION['userid'],$dS,$_SESSION['utype']); ?>
				</td>
				<td align=right style='padding-right: 5px;'><?php if($cv_no) { $p->setNavButtons($cv_no); } ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
		<table width=100% cellpadding="0" cellspacing="0" align=center class="tableRounder">
			<tr>
				<td align=left class="gridHead-left"></td>
				<td align=right class="gridHead-right"></td>
			</tr>
			<tr>
				<td width="100%" colspan=2>
					<table border="0" cellpadding="0" cellspacing="1" width=100%>
						<tr>
							<td width=50% valign=top>
								<table width=100% style="padding:0px 0px 0px 0px;">
									<tr><td height=2></td></tr>
									<tr>
										<td class="bareBold" align=left valign=top width=25% style="padding-left: 35px;">Payee&nbsp;:</td>
										<td align="left">
											<table cellspacing=0 cellpadding=0 border=0 width=100%>
												<tr>
													<td width=25%><input type="text" id="customer_id" name="customer_id" value="<?php echo $res['xpayee']?>" class="inputSearch2" style="padding-left: 22px; width:100%;"></td>
													<td width=75% align=right colspan=2><input type="text" name="customer_name" id="customer_name" autocomplete="off" class="gridInput" value="<?php echo $res['payee_name']; ?>" style="width: 98%;" readonly></td>
												</tr>
												<tr>
													<td style="font-size: 9px; padding-left: 5px;">Code</td><td colspan=2 style="font-size: 9px; padding-left: 20px;">Payee Name</td>
												</tr>
												<tr>
													<td width=100% colspan=2><input class="gridInput" type="text" id="cust_address" name="cust_address" value="<?php echo $res['payee_addr']?>" style="width: 100%;" readonly></td>
												</tr>
												<tr>
													<td colspan=2 style="font-size: 9px; padding-left: 5px;" colspan=2 >Address</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td class="bareBold" align=left width=25% style="padding-left: 35px;">Source of Fund&nbsp;:</td>
										<td align="left">
											<select id="fundsource" name="fundsource" style="width: 265px;" onchange="javascript: getCheckSeries(this.value);" class="gridInput" />
												<option value="">- Select Fund Source -</option>
												<?php
													$_j = $p->dbquery("SELECT acct_code, description FROM acctg_accounts WHERE acct_grp = '1' and parent != 'Y' and acct_code not in ('10102') order by acct_code;");
													while(list($a,$b) = $_j->fetch_array(MYSQLI_BOTH)) {
														echo "<option value='$a' ";
														if($res['source'] == $a) { echo "selected"; }
														echo ">($a) $b</option>";
													}
												?>
											</select>
											<input type=hidden name="ca_refno" id="ca_refno" >
											<input type=hidden name="ca_date" id="ca_date">
										</td>
									</tr>
								</table>
							</td>
							<td valign=top>
								<table border="0" cellpadding="0" cellspacing="1" width=100%>
									<tr><td height=2></td></tr>
									<tr>
										<td align="left" width="30%" class="bareBold" style="padding-left: 35px;">Voucher No.&nbsp;:</td>
										<td align=left>
											<input class="gridInput" style="width:140px;" type=text name="cv_no" id="cv_no" value="<?php echo $cv_no; ?>" readonly >
										</td>				
									</tr>
									<tr>
										<td align="left" width="30%" class="bareBold" style="padding-left: 35px;">Trans. Date&nbsp;:</td>
										<td align=left>
											<input class="gridInput" style="width:140px;" type=text name="cv_date" id="cv_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>" onChange = "javascript: checkLockDate(this.id,this.value,$('#prev_cv_date').val());" >
										</td>				
									</tr>
									<tr>
										<td class="bareBold" align=left valign=top width=25% style="padding-left: 35px;">Check # :</td>
										<td align="left">
											<input class="gridInput" type="text" style="width: 140px;" id="check_no" name="check_no" value = "<?php echo $res['ckno']; ?>">&nbsp;<span class="spandix-l">
										</td>
									</tr>
									<tr>
										<td class="bareBold" align=left valign=top width=25% style="padding-left: 35px;">Check Date :</td>
										<td align="left">
											<input class="gridInput" type="text" style="width: 140px;" id="check_date" name="check_date" value = "<?php echo $res['cd8']; ?>" <?php echo $isReadOnly; ?>>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table cellspacing=0 cellpadding=0 border=0 width=100% style="margin-top: 5px;">
			<tr bgcolor="#887e6e">
				<td align=center class="ui-state-default" style="padding: 5px 5px 5px 25px;" width="8%">REF #</td>
				<td align=center class="ui-state-default" style="padding: 5px;" width="8%">REF DATE</td>
				<td align=center class="ui-state-default" style="padding: 5px;" width="8%">REF TYPE</td>
				<td align=left class="ui-state-default" style="padding: 5px 5px 5px 25px;" width="10%">ACCT CODE</td>
				<td align=left class="ui-state-default" style="padding: 5px 5px 5px 25px;" width="30%">ACCT DESCRIPTION</td>
				<td align=center class="ui-state-default" style="padding: 5px;" width="10%">COST CENTER</td>
				<td align=center class="ui-state-default" style="padding: 5px;" width="8%">DEBIT</td>
				<td align=center class="ui-state-default" style="padding: 5px;" width="8%">CREDIT</td>
				<td align=center class="ui-state-default" style="padding: 5px;">AMOUNT</td>
				<td align=center class="ui-state-default" width="15">&nbsp;</td>
			</tr>
			<?php
				if(($status == "Active" || $status == "") && $lock != 'Y') {
					echo '<tr>
							<td align=center class="grid" align=center><input class="gridInput" type=text name="ref_no" id="ref_no" style="width:95%"/></td>
							<td align=center class="grid" align=center><input class="gridInput" type=text name="ref_date" id="ref_date" style="width:95%"/></td>
							<td align=center class="grid" align=center>
								<select name="ref_type" id="ref_type" style="width:95%" class="gridInput">
									<option value = "CV">- CV -</option>
									<option Value = "JV">- JV -</option>
									<option value = "APV">- APV -</option>
									<option value = "DC">- DA/CA -</option>
									<option value = "SI">- RR/Sup. Inv. -</option>
									<option value = "AP-BB">- Beg. Balance -</option>
								</select>
							</td>
							<td align=center class="grid" colspan=2><input type="hidden" id="acct_code" /><input type=text id="acct_description" style="width: 95%;" /></td>
							<td align=center class="grid">'.$p->constructCostCenter().'</td>
							<td align=center class="grid"><input type="radio" name="side" id="side" value="DB"></td>
							<td align=center class="grid"><input type="radio" name="side" id="side" value="CR"></td>
							<td align=center class="grid"><input class="gridInput" type=text id="amount" style="width: 70%;text-align: right;"/>&nbsp;&nbsp;<a href="#" onclick="javascript: addDetails();" title="Add Item"><img src="images/icons/add-2.png" width=18 height=18 style="vertical-align: middle;" /></a></td>
							<td align=center class="grid"></td>
						</tr>';
				}
			?>
		</table>
		<div id="details" style="height: 130px; overflow-x: auto; border-bottom: 3px solid #4297d7;">
			<?php $p->CVDETAILS($cv_no,$status,$lock) ?>
		</div>
		<table width=100%>
			<tr>
				<td width=50% valign=top>
					Transaction Remarks: <br/>
					<textarea type="text" id="remarks" style="width:83%;"><?php echo $res['remarks']; ?></textarea>
				</td>
				<td>
					<table width=100% cellpaddin=0 cellspacing=0>
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 60%;">Total Amount&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="grossAmount" id="grossAmount" value="<?php echo number_format(ROUND($res['amount']+$res['ewt_amount'],2),2); ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 60%;">Net of VAT&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="netOfVat" id="netOfVat" value="<?php if($res[vat] > 0) { echo number_format(($res['amount']+$res['ewt_amount']-$res['vat']),2); } else { echo "0.00"; } ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 60%;">VAT (12%)&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="vat" id="vat" value="<?php echo number_format($res['vat'],2); ?>">
							</td>				
						</tr>
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 60%;">Tax Withheld&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="taxWithheld" id="taxWithheld" value="<?php echo number_format($res['ewt_amount'],2); ?>">
							</td>				
						</tr>
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 60%;">Net Amount Disbursed&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="netAmount" id="netAmount" value="<?php echo number_format($res['amount'],2); ?>" readonly>
							</td>				
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="applyDivs" style="padding: 10px; display: none;">
	<form name="applydocs" id="applydocs">
		<table align=center border=0 width=100% cellpadding=0 cellspacing=3>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Payee/Supplier:</td>
				<td align=left>
					<input type=text id="app_payee_name" name="app_payee_name" class="inputSearch2" style="width:220px; padding-left: 22px;">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;" valign=top>Payee/Supplier's Address :</td>
				<td align=left>
					<textarea name="app_payee_address" id="app_payee_address" style="width: 220px;font-size: 11px;" rows=3></textarea>
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Payee/Supplier's Tin # :</td>
				<td align=left>
					<input type=text id="app_payee_tin" name="app_payee_tin" class="gridInput" style="width:140px">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Invoice/Reference # :</td>
				<td align=left>
					<input type=text id="app_docno" name="app_docno" class="gridInput" style="width:140px">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Date :</td>
				<td align=left>
					<input type=text id="app_docdate" name="app_docdate" class="gridInput" style="width:140px" >
				</td>
			</tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Amount :</td>
				<td align=left>
					<input type=text id="app_amount" name="app_amount" class="gridInput" style="width:140px">
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
<div id="applyDivs2" style="padding: 10px; display: none;">
	<form name="applydocs2" id="applydocs2">
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
					<input type=text id="app_docno2" name="app_docno2" class="gridInput" style="width:140px;" readonly>
				</td>
			</tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Ref Date :</td>
				<td align=left>
					<input type=text id="app_docdate2" name="app_docdate2" class="gridInput" style="width:140px" readonly>
				</td>
			</tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Balance Due :</td>
				<td align=left>
					<input type=text id="app_amount2" name="app_amount2" class="gridInput" style="width:140px" >
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
<div id="invoices" style="display: none;"></div>
<div class="suggestionsBox" id="suggestions" style="display: none;">
	<div class="suggestionList" id="autoSuggestionsList">&nbsp;</div>
</div>
<div id="loaderMessage" style="display: none;">
	<table width=100%>
		<tr>
			<td align=center style="color:grey; padding-top: 20px;"><img src="images/ajax-loader.gif" align=absmiddle>&nbsp;Please wait while the system is processing your request...</td>
		</tr>
	</table>
</div>
</body>
</html>