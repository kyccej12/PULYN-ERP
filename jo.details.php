
<?php	
	session_start();
	ini_set("display_errors","On");
	require_once("handlers/_pofunct.php");
	$p = new myPO;
	
	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['doc_no']) && $_REQUEST['doc_no'] != '') { 
		$res = $p->getArray("select *, lpad(doc_no,6,0) as docno, lpad(supplier,6,0) as sup_code, date_format(doc_date,'%m/%d/%Y') as d8, if(request_date != '0000-00-00',date_format(request_date,'%m/%d/%Y'),'') as rd8, if(expected_date != '0000-00-00',date_format(expected_date,'%m/%d/%Y'),'') as nd8 from joborder where doc_no = '$_REQUEST[doc_no]';");
		$cSelected = "Y"; $status = $res['status']; $doc_no = $res['docno']; $lock = $res['locked'];
		
	} else {  
		list($doc_no) = $p->getArray("select lpad((ifnull(max(doc_no),0)+1),6,0) from joborder;"); 
		$status = "Active"; $cSelected = "N"; $lock = 'N';
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
	<script language="javascript" src="js/jo.js"></script>
	<script>
	
		var line;
		
		function selectLine(obj,lid) {
			gObj = obj;
			$(obj).closest("tr").siblings().removeClass("diffcolor");
			$(obj).toggleClass("diffcolor");
			line = lid;
		}
		
		$(document).ready(function() { 
			$("#doc_date").datepicker(); 
			$("#date_needed").datepicker(); 
			$("#request_date").datepicker(); 
			
			<?php if($status == 'Finalized' || $status == 'Cancelled') { echo "$(\"#xform :input\").prop('disabled',true);"; } ?>
			
			$('#customer_id').autocomplete({
				source:'suggestContacts.php', 
				minLength:3,
				select: function(event,ui) {
					$("#cSelected").val('Y');
					$("#customer_id").val(ui.item.cid);
					$("#customer_name").val(decodeURIComponent(ui.item.cname));
					$("#cust_address").val(decodeURIComponent(ui.item.addr));
					$("#terms").val(ui.item.terms);
				}
			});
		});
		
	</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div>
	<form name="xform" id="xform">
		<input type="hidden" name="cSelected" id="cSelected" value="<?php echo $cSelected; ?>">
		<input type=hidden name="prev_po_date" id="prev_po_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>">
		<table width=98% cellpadding=0 cellspacing=0 border=0 align=center>
			<tr>
				<td class="upper_menus" align=left>
					<?php $p->setJOHeaderControls($status,$lock,$doc_no,$_SESSION['userid'],$_SESSION['utype']); ?>
				</td>
				<td width=30% align=right style='padding-right: 5px;'><?php if($doc_no) { $p->setJONavButtons($doc_no); } ?></td>
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
										<td class="bareBold" align=left valign=top width=25% style="padding-left: 35px;">Supplier :</td>
										<td align="left">
											<table cellspacing=0 cellpadding=0 border=0 width=100%>
												<tr>
													<td width=25%><input type="text" id="customer_id" name="customer_id" value="<?php echo $res['sup_code']?>" class="inputSearch2" style="padding-left: 22px;"></td>
													<td width=75% align=right colspan=2><input class="gridInput" type="text" name="customer_name" id="customer_name" autocomplete="off" value="<?php echo $res['supplier_name']; ?>" style="width: 100%;" readonly></td>
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
										<td align="left" class="bareBold" style="padding-left: 35px;" valign=top></td>
										<td align=left>
											<input type="hidden" name="delivery_address" id="delivery_address">
										</td>				
									</tr>
									<tr>
										<td class="bareBold" align=left style="padding-left: 35px;">Credit Terms&nbsp;:</td>
										<td align="left">
											<select class="gridInput" id="terms" name="terms" style="width: 150px; font-size: 11px;" />
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
									<tr>
										<td class="bareBold" align=left style="padding-left: 35px;">Cost Center&nbsp;:</td>
										<td align="left">
											<select class="gridInput" id="proj" name="proj" style="width: 150px; font-size: 11px;" />
												<?php
													$tq = $p->dbquery("select proj_id, proj_code from options_project order by proj_name;");
													while(list($pid,$pcode) = $tq->fetch_array(MYSQLI_BOTH)) {
														echo "<option value='$pid' ";
														if($res['proj'] == $pid) { echo "selected"; }
														echo ">$pcode</option>";
													}
												?>
											</select>
										</td>
									</tr>
								</table>
							</td>
							<td valign=top style="padding-left: 100px;">
								<table border="0" cellpadding="0" cellspacing="1" width=100%>
									<tr><td height=2></td></tr>
									<tr>
										<td align="left" width="30%" class="bareBold" style="padding-left: 45px;">J.O No.&nbsp;:</td>
										<td align=left>
											<input class="gridInput" style="width:60%;" type=text name="doc_no" id="doc_no" value="<?php echo $doc_no; ?>" readonly>
										</td>				
									</tr>
									<tr>
										<td align="left" width="30%" class="bareBold" style="padding-left: 45px;">Trans. Date&nbsp;:</td>
										<td align=left>
											<input class="gridInput" style="width:60%;" type=text name="doc_date" id="doc_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; }?>" readonly>
										</td>				
									</tr>
									<tr>
										<td align="left" class="bareBold" style="padding-left: 45px;">Requested By :</td>
										<td align=left>
											<input class="gridInput" style="width:60%;" type=text name="request_by" id="request_by" value="<?php echo $res['request_by']; ?>">
										</td>				
									</tr>
									<tr>
										<td align="left" width="30%" class="bareBold" style="padding-left: 45px;">Request No.&nbsp;:</td>
										<td align=left>
											<input class="gridInput" style="width:60%;" type=text name="request_no" id="request_no" value="<?php  echo $res['request_no']; ?>">
										</td>				
									</tr>
									<tr>
										<td align="left" width="30%" class="bareBold" style="padding-left: 45px;">Request Date&nbsp;:</td>
										<td align=left>
											<input class="gridInput" style="width:60%;" type=text name="request_date" id="request_date" value="<?php  echo $res['rd8']; ?>">
										</td>				
									</tr>
									<tr>
										<td align="left" width="30%" class="bareBold" style="padding-left: 45px;">Date Needed&nbsp;:</td>
										<td align=left>
											<input class="gridInput" style="width:60%;" type=text name="date_needed" id="date_needed" value="<?php  echo $res['nd8']; ?>">
										</td>				
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<table width=100% cellspacing = 0 cellpadding = 0>
									<tr>
										<td width=12% align=left class="bareBold" style="padding-left: 35px;" valign=top>Scope of Work :</td>
										<td style="padding-left: 10px;"><textarea type="text" id="scope" name="scope" style="width:87%;" cols=3><?php echo $res['scope']; ?></textarea></td>
									</tr>
									<tr>
										<td align=left class="bareBold" style="padding-left: 35px;">J.O Amount :</td>
										<td style="padding-left: 10px;"><input class="gridInput" style="width:150px;" type=text name="amount" id="amount" value="<?php  echo number_format($res['amount'],2); ?>"  onchange="saveJOHeader();"></td>
									</tr>
									<tr><td colspan=2 height=20>&nbsp;</td></tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="itemDescDiv" style="display: none;" align=center>
	<table border=0>
		<tr>
			<td width=25% valign=top><b>Custom Description : </b></td>
			<td> 
				<textarea name="customDesc" id="customDesc" style="font-family: arial; width: 95%;" rows=3></textarea>
			</td>
		</tr>
	</table>
</div>
<div id="loaderMessage" title="Processing..." style="display: none;">
	<p><span style="float:left; margin:0 7px 20px 0;"><img src="images/loader.gif" /></span>Please wait while the system is processing your request...</p>
</div>

</body>
</html>