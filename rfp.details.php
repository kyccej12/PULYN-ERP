<?php	
	/* UNSET QUED FOR DELETION */
	//ini_set("display_errors","On");
	session_start();
	unset($_SESSION['ques']);
	
	require_once("handlers/_rfpfunct.php");
	$con = new myRFP;
	
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['rfp_no']) && $_REQUEST['rfp_no']!='') { 
		$rfp_no = $_REQUEST['rfp_no']; 
		$res = $con->getArray("select *, lpad(supplier,6,0) as sup_code, date_format(rfp_date,'%m/%d/%Y') as d8, date_format(date_needed,'%m/%d/%Y') as d82 from rfp_header where rfp_no='$rfp_no';");
		$cSelected = "Y";
	} else {  
		list($rfp_no) = $con->getArray("select ifnull(max(rfp_no),0)+1 from rfp_header;"); 
		$res['status'] = "Active"; $dS = "1"; $cSelected = "N";
	}

	$con->getSummaryValues($rfp_no,$res['status']);
	
?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Redviper Ventures & Development Corp. ERP System Ver. 2.0</title>
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="js/rfp.js"></script>
	<script language="javascript" src="js/jquery.dialogextend.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>
		$('html').click(function(){ $("#suggestions").fadeOut(200); });
		$(document).ready(function($){ 
			<?php if ($res['status'] == 'Active') { ?>
				$("#rfp_date").datepicker(); 
				$("#date_needed").datepicker(); 
				$("#ref_date").datepicker(); 
				$('#customer_id').autocomplete({
					source:'suggestContacts.php', 
					minLength:3,
					select: function(event,ui) {
						$("#cSelected").val('Y');
						$("#customer_id").val(ui.item.cid);
						$("#customer_name").val(decodeURIComponent(ui.item.cname));
						$("#cust_address").val(decodeURIComponent(ui.item.addr));
						$("#po_term").val(ui.item.terms);
						saveHeader();
					}
				});
			<?php } else { echo "$(\"#xform :input\").prop('disabled',true);"; }?>
		});

	</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div style="padding: 10px;">
	<form name="xform" id="xform">
		<input type=hidden id="rfp_no" value="<?php echo $rfp_no; ?>">
		<input type="hidden" name="cSelected" id="cSelected" value="<?php echo $cSelected; ?>">

		<table width=100% cellpadding=0 cellspacing=0 border=0 align=center>
			<tr>
				<td class="upper_menus" align=left>
					<?php $con->setHeaderControls($res['status'],$rfp_no,$_SESSION['userid'],$dS); ?>
				</td>
				<td align=right style='padding-right: 5px;'><?php if($rfp_no) { $con->setNavButtons($rfp_no); } ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
		<table width=100% cellpadding="0" cellspacing="0" align=center style="border: 1px solid #436178;">
			<tr>
				<td align=left class="gridHead">REQUEST FOR PAYMENT</td>
				<td align=right class="gridHead">DOCUMENT NO. <?php echo str_pad($rfp_no,10,'0',STR_PAD_LEFT); ?></td>
			</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="" width=100% class="td_content">
			<tr>
				<td width=60% valign=top>
					<table width=100% style="padding:0px 0px 0px 0px;">
						<tr><td height=2></td></tr>
						<tr>
							<td class="bareBold" align=right valign=top width=25% style="padding-right: 5px;">Supplier&nbsp;:</td>
							<td align="left">
								<table cellspacing=0 cellpadding=0 border=0 width=100%>
									<tr>
										<td width=25%><input type="text" id="customer_id" name="customer_id" value="<?php echo $res['sup_code']?>" class="inputSearch2" style="padding-left: 22px;" ></td>
										<td width=75% align=right colspan=2><input type="text" name="customer_name" id="customer_name" autocomplete="off" class="gridInput" value="<?php echo $res['supplier_name']; ?>" style="width: 95%; font-weight: bold;" readonly></td>
									</tr>
									<tr>
										<td style="font-size: 9px; padding-left: 5px;">Code</td><td colspan=2 style="font-size: 9px; padding-left: 20px;">Supplier Name</td>
									</tr>
									<tr><td height=2></td></tr>
									<tr>
										<td width=100% colspan=2><input class="gridInput" type="text" id="cust_address" name="cust_address" value="<?php echo $res['supplier_addr']?>" style="font-weight: bold; width: 99%;" readonly></td>
									</tr>
									<tr>
										<td colspan=2 style="font-size: 9px; padding-left: 5px;" colspan=2 >Address</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr><td height=2></td></tr>
					</table>
				</td>
				<td valign=top>
					<table border="0" cellpadding="0" cellspacing="1" width=100%>
						<tr><td height=2></td></tr>
						<tr>
							<td align="right" width="30%" class="bareBold" style="padding-right: 5px;">Date Requested&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:140px;" type=text name="rfp_date" id="rfp_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>"  onchange='javascript: saveHeader();'>
							</td>				
						</tr>
						<tr><td height=2></td></tr>
						<tr>
							<td class="bareBold" align=right width=25% style="padding-right: 5px;">Requested By&nbsp;:</td>
							<td align="left">
								<input type="text" name="requested_by" id="requested_by" class="gridInput" style="width: 140px;" value="<?php echo $res['requested_by']; ?>" >
							</td>
						</tr>
						<tr><td height=2></td></tr>
						<tr>
							<td align="right" width="30%" class="bareBold" style="padding-right: 5px;">Date Needed&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:140px;" type=text name="date_needed" id="date_needed" value="<?php if(!$res['d82']) { echo ''; } else { echo $res['d82']; }?>"  onchange='javascript: ;'>
							</td>				
						</tr>
					</table>
				</td>
			</tr>

		</table>
		<table cellspacing=0 cellpadding=0 border=0 width=100% style="border: 1px solid #436178;">
			<tr bgcolor="#887e6e">
				<td align=center class="gridHead" width="10%">APV #</td>
				<td align=center class="gridHead" width="10%">APV DATE</td>
				<td align=left class="gridHead" width="30%">DETAILS</td>
				<td align=center class="gridHead" width="10%">DUE DATE</td>
				<td align=right class="gridHead" width="10%" style="padding-right: 5px;">GROSS</td>
				<td align=right class="gridHead" width="10%" style="padding-right: 5px;">VAT</td>
				<td align=right class="gridHead" width="10%" style="padding-right: 20px;">EWT</td>
				<td align=right class="gridHead" style="padding-right: 25px;">NET PAYABLE</td>
				<td align=right class="gridHead" width="10">&nbsp;</td>
			</tr>
		</table>
		<div id="apdetails" style="height: 115px; overflow-x: auto; border-bottom: 3px solid #4297d7;">
			<?php $con->RFPDETAILS($rfp_no); ?>
		</div>
		<table width=100% class="td_content">
			<tr>
				<td width=50% valign=top>
					Transaction Remarks: <br/>
					<textarea type="text" id="remarks" style="width:100%;"><?php echo $res['remarks']; ?></textarea>
					<?php if($res['status'] == 'Active') { ?>
						<br/><a href="#" class="topClickers" onClick="javascript: deleteRFPLine();"><img src="images/icons/delete.png" width=16 height=16 border=0 align="absmiddle">&nbsp;Remove Selected Item</a>
					<?php } ?>
				</td>
				<td align=right width=50%>
					<table border="0" cellpadding="0" cellspacing="1" width=100%>
						<tr>
							<td align="right" width="80%" class="bareBold" style="padding-right: 10px;font-weight: bold;">Amount Total&nbsp;:</td>
							<td align=right>
								<input class=gridInput style="width:80%;text-align:right;font-weight: bold;" type=text name="total_amount" id="total_amount" value="<?php echo $con->total; ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="right" width="80%" class="bareBold" style="padding-right: 10px;font-weight: bold;">V-A-T&nbsp;:</td>
							<td align=right>
								<input class=gridInput style="width:80%;text-align:right;font-weight: bold;" type=text name="vat_amount" id="vat_amount" value="<?php echo $con->vat; ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="right" width="80%" class="bareBold" style="padding-right: 10px;font-weight: bold;">Taxes Withheld&nbsp;:</td>
							<td align=right>
								<input class=gridInput style="width:80%;text-align:right;font-weight: bold;" type=text name="tax_withheld" id="tax_withheld" value="<?php echo $con->ewt; ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="right" width="80%" class="bareBold" style="padding-right: 10px;font-weight: bold;">Net Payable&nbsp;:</td>
							<td align=right>
								<input class=gridInput style="width:80%;text-align:right;font-weight: bold;" type=text name="net_payable" id="net_payable" value="<?php echo $con->net; ?>" readonly>
							</td>				
						</tr>
						<tr>
							<td align="right" width="80%" class="bareBold" style="padding-right: 10px;font-weight: bold;">CV No. Applied&nbsp;:</td>
							<td align=right>
								<input class=gridInput style="width:80%;text-align:right;font-weight: bold;" type=text name="rfp" id="rfp" value="<?php echo $con->cv; ?>" readonly>
							</td>				
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="invoices" style="diplay: none;"></div>
<div class="" id="cancel_box" style="display: none;">
	<table >
		<tr>
			<td style="color:grey">Reason : </td>
			<td> <textarea id = "cancel_remarks" rows=2 cols=30 > </textarea> </td>
		</tr>
	</table>
</div>
<div class="suggestionsBox" id="suggestions" style="display: none;">
	<div class="suggestionList" id="autoSuggestionsList">&nbsp;</div>
</div>

</body>
</html>