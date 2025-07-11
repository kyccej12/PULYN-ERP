<?php	
	session_start();
	include("functions/beg.displayDetails.fnc.php");
	include("includes/dbUSE.php");
	

	$res = getArray("select *, date_format(doc_date,'%m/%d/%Y') as d8 from apbeg_header where doc_no = '1' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
	if(!$res['status']) { $res['status'] = 'Active'; }
		
	function setHeaderControls($status,$doc_no,$uid,$dS) {
		list($urights) = getArray("select user_type from user_info where emp_id='$uid'");
		switch($status) {
			case "Posted":
				list($posted_by,$posted_on) = getArray("select fullname as name, date_format(updated_on,'%m/%d/%Y %p') as date_posted from apbeg_header a left join user_info b on a.updated_by=b.emp_id where a.doc_no='$doc_no';");
				if($urights == "admin") {
					$headerControls = "<button type=button onclick=\"javascript: reOpenAPB();\"><img src='images/icons/edit.png' align=absmiddle width=16 height=16 />&nbsp;Set this Document to Active Status</button>";
				} else { echo "<b>Posted By:</b> $posted_by  <b>::  Posted On:</b> $posted_on"; }
			break;
			case "Active": default:
				$headerControls = "<button  onClick=\"javascript:finalizeAPB();\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Finalize & Post AP Beginning</button>&nbsp;<button onclick=\"javascript:saveAPBHeader();\"><img src=\"images/save.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Save Changes</button>&nbsp;";
			break;
		}
		echo $headerControls;
	}
	
	if($res['status'] != "Active") { $isReadOnly = "readonly"; $isDisabled = "disabled"; }
	
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
	<script language="javascript" src="js/beg.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>
		$('html').click(function(){ $("#suggestions").fadeOut(200); });
		$(function() { 
			<?php if ($res['status'] == 'Active') { ?>$("#doc_date").datepicker(); $("#inv_date").datepicker(); $("#po_date").datepicker(); <?php }?> $("#ref_date").datepicker();
		});

		function deleteAPLine(line_id) {
			if(confirm("Are you sure you want to delete this line entry?") == true) {
				$.post("beg.datacontrol.php", { mod: "deleteAPLine", lid: line_id, sid: Math.random() }, function(data) {
					$("#begdetails").html(data);
				},"html");
			}
		}

		function finalizeAPB() {
			if(confirm("Are you sure you want to Finalize & Post Beginning Balance to General Ledger?") == true) {
				$.post("beg.datacontrol.php", { mod: "finalizeAPB", sid: Math.random()}, function(){
					location.reload();
				});
			}
		}

		function reOpenAPB() {
			if(confirm("Are you sure you want to set this document to active status?") == true) {
				$.post("beg.datacontrol.php", { mod: "reOPENAPB", sid: Math.random() }, function() {
					location.reload();
				});
			}
		}

	</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div style="padding: 10px;">
	<table width=100% border=0 cellpadding=0 cellspacing=0 align=center>
		<tr>
			<td valign=top >
				<table width=98% cellpadding=0 cellspacing=0 border=0 align=center>
					<tr>
						<td class="upper_menus" align=left>
							<?php setHeaderControls($res['status'],$doc_no,$_SESSION['userid'],$dS); ?>
						</td>
						<td align=right style='padding-right: 5px;'><?php if($doc_no) { setNavButtons($doc_no); } ?></td>
					</tr>
					<tr><td height=2></td></tr>
				</table>
				<table width=98% cellpadding="0" cellspacing="0" align=center style="border: 1px solid #436178;">
					<tr>
						<td align=left class="gridHead">ACCOUNTS PAYABLE BEGINNING BALANCE</td>
						<td align=right class="gridHead"></td>
					</tr>
					<tr>
						<td width="100%" colspan=2>
							<table border="0" cellpadding="0" cellspacing="1" width=100%>
								<tr><td height=4></td></tr>
								<tr>
									<td align="right" width="15%" class="bareBold" style="padding-right: 5px;">Document Date&nbsp;:</td>
									<td align=left>
										<input class="gridInput" style="width:140px;" type=text name="doc_date" id="doc_date" value="<?php if(!$res['d8']) { echo date('m/d/Y'); } else { echo $res['d8']; } ?>" <?php echo $isReadonly; ?>/ >
									</td>
								</tr>
								<tr><td height=2></td></tr>
								<tr>
									<td align=right valign="top" class=bareBold style="padding-right: 5px;">Memo :</td>
									<td align=left>
										<textarea class="gridInput" type="text" id="remarks" style="width:80%;" <?php if($res['status'] == "Active") { echo "onblur=\"saveAPBHeader();\" "; } echo $isReadOnly; ?>><?php echo $res['explanation']; ?></textarea>
									</td>
								</tr>
								<tr><td height=4></td></tr>
							</table>
						</td>
					</tr>
				</table>
				<table><tr><td height=2></td></tr></table>
				<table width=98% cellpadding="0" cellspacing="0" align=center>
					<tr>
						<td colspan=2>
							<table cellspacing=0 cellpadding=0 border=0 width=100%>
								<tr bgcolor="#887e6e">
									<td align=left class="gridHead" width="10%" style="padding-left: 10px;">CODE</td>
									<td align=left class="gridHead" width="35%">SUPPLIER NAME</td>
									<td align=center class="gridHead" width="10%">INV #</td>
									<td align=center class="gridHead" width="10%">INV DATE</td>
									<td align=center class="gridHead" width="10%">PO #</td>
									<td align=center class="gridHead" width="10%">PO DATE</td>
									<td align=center class="gridHead" width="10%">AMOUNT</td>
									<td align=center class="gridHead" width="5%">&nbsp;</td>
								</tr>
								<?php if($res['status'] == "Active" || $res['status'] == "") { ?>
								<tr bgcolor="ededed">
									<td align=center class="grid" width="45%" align=center colspan=2><input type="hidden" name="customer_id" id="customer_id"><input class="inputSearch" style="padding-left: 22px; width:95%;" type=text name="customer_name" id="customer_name" autocomplete="off"  <?php if($res['status'] == "Active" or $res['status'] == "") { echo "onkeyup=\"contactlookup(this.value, this.id);\""; } ?> /></td>
									<td align=center class="grid" width="10%" align=center><input class="gridInput" type=text name="inv_no" id="inv_no" style="width:90%"/></td>
									<td align=center class="grid" width="10%" align=center><input class="gridInput" type=text name="inv_date" id="inv_date" style="width:90%"/></td>
									<td align=center class="grid" width="10%" align=center><input class="gridInput" type=text name="po_no" id="po_no" style="width:90%"/></td>
									<td align=center class="grid" width="10%" align=center><input class="gridInput" type=text name="po_date" id="po_date" style="width:90%"/></td>
									<td align=center class="grid" width="10%"><input class="gridInput" type=text id="amount" style="width: 90%;text-align: right;"/></td>
									<td align=center class="grid" width="5%"><a href="#" onclick="javascript: addAPBDetails();" title="Add Item"><img src="images/icons/add-2.png" width=18 height=18 style="vertical-align: middle;" /></a></td>
								</tr>
							<?php } ?>
							</table>
							<table cellpadding=0 cellspacing=0 border=0 width=100% id="begdetails">
								<?php APDETAILS($_SESSION['company'],$_SESSION['branchid']); ?>
							</table>
						</td>
					</tr>
				</table>
				<table><tr><td height=8></td></tr></table>
			</td>
		</tr>
	</table>
</div>
<div class="suggestionsBox" id="suggestions" style="display: none;">
	<div class="suggestionList" id="autoSuggestionsList">&nbsp;</div>
</div>
<?php include("includes/applydiv.php"); ?>
</body>
</html>