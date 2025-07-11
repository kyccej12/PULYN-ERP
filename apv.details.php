<?php	
	/* UNSET QUED FOR DELETION */
	session_start();
	unset($_SESSION['ques']);
	
	//ini_set("display_errors","On");
	require_once "handlers/_apfunct.php";
	
	$p = new myAP;
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['apv_no']) && $_REQUEST['apv_no'] != '') { 
		$res = $p->getArray("select *, lpad(apv_no,6,0) as apvno, lpad(supplier,6,0) as sup_code, date_format(apv_date,'%m/%d/%Y') as d8 from apv_header where apv_no='$_REQUEST[apv_no]' and branch = '$_SESSION[branchid]';");
		$cSelected = "Y"; $apv_no = $res['apvno']; $lock = $res['locked']; $status = $res['status']; $trace_no = $res['trace_no'];
	} else {  
		list($apv_no) = $p->getArray("select lpad((ifnull(max(apv_no),0)+1),6,0) from apv_header where branch = '$_SESSION[branchid]';");
		$trace_no = $p->generateRandomString();
		$dS = "1"; $cSelected = "N"; $status = "Active"; $lock = "N";
	}

	if(!$_GET['mod']) { $mod = 1; } else { $mod = $_GET['mod']; }

?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/datatables/css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
	<script language="javascript" src="js/apv.js"></script>
	<script>
		$(document).ready(function($){
			
			<?php if($status == 'Posted' || $status == 'Cancelled') { echo "$(\"#xform :input\").prop('disabled',true);"; } ?>
			$('#amount').bind('keypress', function(e) { if(e.keyCode ==13){ addDetails(); } });
			$("#apv_date").datepicker(); $("#ref_date").datepicker();  $("#app_docdate").datepicker();
			
			$('#customer_id').autocomplete({
				source:'suggestContacts.php', 
				minLength:3,
				select: function(event,ui) {
					$("#cSelected").val('Y');
					$("#customer_id").val(ui.item.cid);
					$("#customer_name").val(decodeURIComponent(ui.item.cname));
					$("#cust_address").val(decodeURIComponent(ui.item.addr));
					$("#terms").val(ui.item.terms);
					saveAPVHeader();
				}
			});
			
			<?php switch($mod) { case "2": ?>
				$('#details').dataTable({
					"ajax": {
						"url": "apv.datacontrol.php",
						"data": { trace_no: "<?php echo $trace_no; ?>", mod: "retrieveLedger", sid: Math.random() },
						"method": "POST"	
					},
					"scrollY":  "145",
					"select":	'single',
					"pagingType": "full_numbers",
					"bProcessing": true,
					"searching": false,
					"paging": false,
					"info": false,
					
					"aoColumns": [
						{ mData: 'acct' },
						{ mData: 'acct_desc' },
						{ mData: 'costcenter' },
						{ mData: 'db', render: $.fn.dataTable.render.number(',', '.', 2, '')},
						{ mData: 'cr', render: $.fn.dataTable.render.number(',', '.', 2, '') },
					],
					"aoColumnDefs": [
						{ className: "dt-body-center", "targets": [0,2]},
						{ className: "dt-body-right", "targets": [3,4]},
					]
				});

			<?php break; case "1": ?>
				$('#details').dataTable({
					"ajax": {
						"url": "apv.datacontrol.php",
						"data": { trace_no: "<?php echo $trace_no; ?>", mod: "retrievePurchases", sid: Math.random() },
						"method": "POST"	
					},
					"scrollY":  "145",
					"select":	'single',
					"pagingType": "full_numbers",
					"bProcessing": true,
					"searching": false,
					"paging": false,
					"info": false,
					
					"aoColumns": [
						{ mData: 'id' },
						{ mData: 'rrno' },
						{ mData: 'rrdate' },
						{ mData: 'ino' },
						{ mData: 'idate' },
						{ mData: 'gross', render: $.fn.dataTable.render.number(',', '.', 2, '')},
						{ mData: 'ewt_amount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
						{ mData: 'input_vat', render: $.fn.dataTable.render.number(',', '.', 2, '') },
						{ mData: 'net', render: $.fn.dataTable.render.number(',', '.', 2, '') },
					],
					"aoColumnDefs": [
						{ className: "dt-body-center", "targets": [1,2,3,4]},
						{ className: "dt-body-right", "targets": [5,6,7,8]},
						{ "targets": [0], "visible": false }
					]
				});	

			<?php break; }?>
			
		});

		function redrawDataTable() {
			$('#details').DataTable().ajax.url("apv.datacontrol.php?mod=retrieve&trace_no=<?php echo $trace_no; ?>").load();
		}

		function changeMod(mod) {
			document.changeModPage.mod.value = mod;
			document.changeModPage.submit();
		}

		function deleteReference() {
			var table = $("#details").DataTable();
			var arr = [];
			$.each(table.rows('.selected').data(), function() {
				 arr[0] = this['rrno'];
				 arr[1] = this['ino'];
			});	

			if(arr[0]) {
				if(confirm("Are you sure you want to remove this reference") == true) {
					$.post("apv.datacontrol.php", { mod: "deleteInvoice", apv_no: $("#apv_no").val(), rr_no: arr[0], ino: arr[1], sid: Math.random() }, function() {
						redrawDataTable(); getTotals();
					});
				}
			} else {
				parent.sendErrorMessage("It appears that you haven't selected any record to remove..");
			}

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
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div style="padding: 10px;">
	<form name="xform" id="xform">
		<input type="hidden" name="cSelected" id="cSelected" value="<?php echo $cSelected; ?>">
		<input type="hidden" name="trace_no" id="trace_no" value="<?php echo $trace_no; ?>">
		<input type=hidden name="prev_apv_date" id="prev_apv_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>" >
		<table width=100% cellpadding=0 cellspacing=0 border=0 align=center>
			<tr>
				<td class="upper_menus" align=left>
					<?php $p->setHeaderControls($status,$apv_no,$_SESSION['utype']); ?>
				</td>
				<td align=right style='padding-right: 5px;'><?php if($apv_no) { $p->setNavButtons($apv_no); } ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content">
			<tr>
				<td width=50% valign=top>
					<table width=100% style="padding:0px 0px 0px 0px;">
						<tr><td height=2></td></tr>
						<tr>
							<td class="bareBold" align=left valign=top width=25% style="padding-left: 35px;">Supplier&nbsp;:</td>
							<td align="left">
								<table cellspacing=0 cellpadding=0 border=0 width=100%>
									<tr>
										<td width=25%><input type="text" id="customer_id" name="customer_id" value="<?php echo $res['sup_code']?>" class="inputSearch2" style="padding-left: 22px; width: 100%;"></td>
										<td width=75% align=right colspan=2><input type="text" name="customer_name" id="customer_name" autocomplete="off" class="gridInput" value="<?php echo $res['supplier_name']; ?>" style="width: 98%;" readonly></td>
									</tr>
									<tr>
										<td style="font-size: 9px; padding-left: 5px;">Code</td><td colspan=2 style="font-size: 9px; padding-left: 20px;">Supplier Name</td>
									</tr>
									<tr>
										<td width=100% colspan=2><input class="gridInput" type="text" id="cust_address" name="cust_address" value="<?php echo $res['supplier_addr']?>" style="width: 100%;" readonly></td>
									</tr>
									<tr>
										<td colspan=2 style="font-size: 9px; padding-left: 5px;" colspan=2 >Address</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="bareBold" align=left width=25% style="padding-left: 35px;">Credit Term&nbsp;:</td>
							<td align="left">
								<select id="terms" name="terms" style="width: 150px;" class="gridInput" />
									<?php
										$tq = $p->dbquery("select terms_id, description from options_terms order by terms_id;");
										while(list($tid,$td) = $tq->fetch_array(MYSQLI_BOTH)) {
											echo "<option value='$tid' ";
											if($res['terms'] == $tid) { echo "selected"; }
											echo ">$td</option>";
										}
									?>
								</select>
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
								<input class="gridInput" style="width:140px;" type=text name="apv_no" id="apv_no" value="<?php echo $apv_no; ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="left" width="30%" class="bareBold" style="padding-left: 35px;">Trans. Date&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:140px;" type=text name="apv_date" id="apv_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>" onChange = "checkLockDate(this.id,this.value,$('#prev_apv_date').val());" >
							</td>				
						</tr>
						<tr>
							<td class="bareBold" align=left width=25% style="padding-left: 35px;">Tax Code (EWT)&nbsp;:</td>
							<td align="left">
								<select id="atc_code" name="atc_code" style="width: 140px;" class="gridInput" />
									<option value="">- NA -</option>
									<?php
										$tq = $p->dbquery("select atc_code, description, rate from options_atc order by rate;");
										while(list($aa,$bb,$cc) = $tq->fetch_array(MYSQLI_BOTH)) {
											echo "<option value='$aa' ";
											if($res['atc_code'] == $aa) { echo "selected"; }
											echo " title='$bb'>$aa ($cc %)</option>";
										}
									?>
								</select>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table cellspacing=0 cellpadding=0 width=100% align=center style="margin-top: 5px;">
			<tr>
				<td style="padding: 0px 0px 1px 0px;">								
					<div id="custmenu" align=left class="ddcolortabs">
						<ul class=float2>
							<li><a href="#" <?php $p->getMod("1",$mod); ?> onclick="javascript: changeMod(1);"><span id="tbbalance2">Purchases Reference</span></a></li>
							<li><a href="#" <?php $p->getMod("2",$mod); ?> onclick="javascript: changeMod(2);"><span id="tbbalance3">Ledger Entries</span></a></li>
						</ul>
					</div>
				</td>
			</tr>
		</table>
		<?php switch($mod) { case "2": ?>
		<table id="details">
			<thead>
				<tr>
					<th width=15%>ACCT CODE</th>
					<th>DESCRIPTION</th>
					<th width=15%>UNIT CODE</th>
					<th width=15%>DEBIT</th>
					<th width=15%>CREDIT</th>
				</tr>
			</thead>
		</table>
		<?php break; case "1": ?>
		<table id="details">
			<thead>
				<tr>
					<th></th>
					<th width=10%>RR #</th>
					<th width=10%>RR DATE</th>
					<th width=15%>SI/DR #</th>
					<th width=15%>SI/DR DATE</th>
					<th width=10%>GROSS</th>
					<th width=10%>EWT</th>
					<th width=15%>INPUT VAT</th>
					<th width=15%>NET PAYABLE</th>
				</tr>
			</thead>
		</table>
		<?php break; } ?>
		
		<table width=100% class="td_content">
			<tr>
				<td width=50% valign=top>
					Transaction Remarks or Explanation: <br/>
					<textarea type="text" id="remarks" style="width:83%;"><?php echo $res['remarks']; ?></textarea><br/><br/>
					<?php 
						if($status == 'Active') { 
							if($mod == 1) {
								echo '<a href="#" class="topClickers" onClick="javascript:deleteReference();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Selected Purchases Reference</a>&nbsp;&nbsp;';
							} else {
								echo '<a href="#" class="topClickers" onClick="javascript:newEntry();"><img src="images/icons/add-2.png" width=16 height=16 border=0 align="absmiddle">&nbsp;New Ledger Entry</a>&nbsp;
									  <a href="#" class="topClickers" onClick="javascript:removeEntry();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Selected Entry</a>&nbsp;';
							}	
						}
					?>
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
							<td align="left" width="80%" class="bareBold" style="padding-left: 60%;">Net Payable&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="netPayable" id="netPayable" value="<?php echo number_format($res['amount'],2); ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 60%;">Amount Applied&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="amountApplied" id="amountApplied" value="<?php echo number_format($res['applied_amount'],2); ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="left" width="80%" class="bareBold" style="padding-left: 60%;">Balance Due&nbsp;:</td>
							<td align=right>
								<input style="width:80%;text-align:right;" type=text name="balanceDue" id="balanceDue" value="<?php echo number_format($res['balance'],2); ?>" readonly>
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
					<input type=text id="app_amount" name="app_amount" class="gridInput" style="width:80px">
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
							while(list($aa1,$bb1,$cc1) = $tq1->fetch_array(MYSQLI_BOTH)) {
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
<div class="suggestionsBox" id="suggestions" style="display: none;">
	<div class="suggestionList" id="autoSuggestionsList">&nbsp;</div>
</div>
<div id="invoiceAttachment" style="display: none;"></div>
<div id="loading_popout" style="display:none;" align=center>
	<progress id='progess_trick' value='40' max ='100' width='220px'></progress> <br>
	Please wait while the server is processing our request.
</div>
<div id="itemEntry" style="display: none;">
	<form name="frmItemEntry" id="frmItemEntry">
		<input type="hidden" id="recordId" name="recordId">
		<table width="100%" cellspacing=2 cellpadding=0 >
			<tr>
				<td class="bareThin" align=left width=40%>Account Title :</td>
				<td align=left>
					<input type="text" name="acctDescription" id="acctDescription" class="inputSearch2" style="width: 80%; padding-left: 22px;">
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Account Code :</td>
				<td align=left>
					<input type="text" name="acctCode" id="acctCode" class="gridInput" style="width: 80%;" readonly>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Cost Center :</td>
				<td align=left>
					<select name="costCenter" id="costCenter" class="gridInput" style="width: 80%;">
						<?php
							$ccQuery = $p->dbquery("SELECT unitcode, costcenter from options_costcenter order by costcenter");
							while($ccRow = $ccQuery->fetch_array()) {
								echo "<option value = '$ccRow[0]'>$ccRow[1]</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Amount :</td>
				<td align=left>
					<input type="text" name="entryAmount" id="entryAmount" class="gridInput"style="width: 80%;">
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Entry Type :</td>
				<td align=left>
					<select name="entryType" id="entryType" class="gridInput" style="width: 80%;">
						<option value="DB">Debit Entry</option>
						<option value="CR">Credit Entry</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Reference # :</td>
				<td align=left>
					<input type="text" name="refNo" id="refNo" class="gridInput" style="width: 80%;">
				</td>
			</tr>
			<tr>
				<td class="bareThin" align=left width=40%>Amount :</td>
				<td align=left>
					<input type="text" name="refDate" id="refDate" class="gridInput" style="width: 80%;">
				</td>
			</tr>
			
		</table>
	</form>
</div>
<form name="changeModPage" id="changeModPage" action="apv.details.php" method="GET" >
	<input type="hidden" name="apv_no" id="apv_no" value="<?php echo $apv_no; ?>">
	<input type="hidden" name="mod" id="mod">
</form>
</body>
</html>