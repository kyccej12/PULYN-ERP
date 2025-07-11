<?php	
	/* UNSET QUED FOR DELETION */
	session_start();
	require_once("handlers/initDB.php");
	$con = new myDB;

	
	$uid = $_SESSION['userid'];
	if(isset($_REQUEST['grfp_no']) && $_REQUEST['grfp_no'] != '') { 
		$grfp_no = $_REQUEST['grfp_no']; 
		$res = $con->getArray("SELECT a.grfp_no,date_format(a.grfp_date,'%m/%d/%Y') as gd8, a.emp_id as employee_id,a.costcenter,a.remarks,a.payee,a.emp_name,a.payment_for,a.amount,a.status,a.payee_code,a.department, date_format(date_needed,'%m/%d/%Y') as date_needed
						FROM grfp a WHERE a.branch = '$_SESSION[branchid]' AND a.grfp_no = '$_REQUEST[grfp_no]';");
		$cSelected = "Y"; 
	} else {  
		$res['status'] = "Active"; $dS = "1"; $cSelected = "N";
	}

	function setHeaderControls($status,$grfp_no,$uid,$dS) {
		
		global $con;
		
		list($urights) = $con->getArray("select user_type from user_info where emp_id = '$uid'");
		switch($status) {
			case "Finalized":
				if($urights == "admin") {
					$headerControls = $headerControls . "<a class=\"topClickers\" href=\"#\" onclick=\"javascript: reopenGRFP('$grfp_no');\"><img src='images/icons/edit.png' align=absmiddle width=16 height=16 />&nbsp;Set this Document to Active Status</a>";
				}
				$headerControls = $headerControls . "&nbsp;<a class=\"topClickers\" href=\"#\" onClick=\"javascript:parent.printGRFP('$grfp_no','$_SESSION[userid]');\"><img src=\"images/icons/print.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Print Request For Payment</a>&nbsp;";
			break;
			case "Cancelled":
				if($urights == "admin") {
					$headerControls = $headerControls . "<a class=\"topClickers\" href=\"#\" onclick=\"javascript:reopenGRFP('$grfp_no');\" style=\"padding: 5px;\"><img src=\"images/icons/refresh.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Recycle this Document</a>";	
				}
			break;
			case "Active": default:
			if(isset($grfp_no) && $grfp_no !=''){
				$headerControls = "<a class=\"topClickers\" href=\"#\" onClick=\"javascript:printGRFP('$grfp_no','$_SESSION[userid]');\"><img src=\"images/icons/ok.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Finalize Request for Payment</a>&nbsp;&nbsp;<a class=\"topClickers\" href=\"#\" onclick=\"javascript:saveGRFP();\"><img src=\"images/icons/floppy.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Save Changes</a>&nbsp;";
			}else{
				$headerControls = "<a class=\"topClickers\" href=\"#\" onclick=\"javascript:saveGRFP();\"><img src=\"images/icons/floppy.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Save Changes</a>&nbsp;";
			}
			if($urights == "admin" && $dS != 1) {
					$headerControls = $headerControls . "<a class=\"topClickers\" href=\"#\" onclick=\"javascript:cancelGRFP('$grfp_no');\"><img src=\"images/icons/cancel.png\" width=16 height=16 border=0 align=\"absmiddle\">&nbsp;Cancel this Document</a>";
				}
			break;
		}
		echo $headerControls;
	}
	
	function setNavButtons($grfp_no) {
		global $con;
		
		list($fwd) = $con->getArray("select grfp_no from grfp where grfp_no > $grfp_no and branch = '$_SESSION[branchid]' limit 1;");
		list($prev) = $con->getArray("select grfp_no from grfp where grfp_no < $grfp_no and branch = '$_SESSION[branchid]' order by grfp_no desc limit 1;");
		list($last) = $con->getArray("select grfp_no from grfp where branch = '$_SESSION[branchid]' order by grfp_no desc limit 1;");
		list($first) = $con->getArray("select grfp_no from grfp where branch = '$_SESSION[branchid]' order by grfp_no asc limit 1;");
		if($prev)
			$nav = $nav . "<a href=# onclick=\"parent.viewGRFP('$prev');\"><img src='images/resultset_previous.png'  title='Previous Record' /></a>";
		if($fwd) 
			$nav = $nav . "<a href=# onclick=\"parent.viewGRFP('$fwd');\"><img src='images/resultset_next.png' 'title='Next Record' /></a>";
		echo "<a href=# onclick=\"parent.viewGRFP('$first');\"><img src='images/resultset_first.png' title='First Record' /><a>" . $nav . "<a href=# onclick=\"parent.viewGRFP('$last');\"><img src='images/resultset_last.png' title='Last Record' /></a>";
	}

?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Redviper Ventures & Development Corp. ERP System Ver. 2.0</title>
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="js/jquery.dialogextend.js"></script>
	<script language="javascript" src="js/grfp.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>
		$('html').click(function(){ $("#suggestions").fadeOut(200); });
		$(function() { 
			<?php if($res['status'] === 'Active') { ?>
		
				$("#grfp_date").datepicker(); 
				$("#date_needed").datepicker();
				$('#emp_name').autocomplete({
					source:'suggestEmployee.php', 
					minLength:3,
					select: function(event,ui) {
						$("#cSelected").val('Y');
						$("#employee_id").val(ui.item.emp_id);
						$("#department").val(decodeURIComponent(ui.item.dname));
					}
				});
				
				$('#payeeid').autocomplete({
					source:'suggestContacts.php', 
					minLength:3,
					select: function(event,ui) {
						$("#payeeid").val(ui.item.cid);
						$("#payee").val(decodeURIComponent(ui.item.cname));
					}
				});
		
			<?php } else { echo "$(\"#xform :input\").prop('disabled',true);"; }?>
		});
	</script>
	<style>
       .ui-autocomplete {
            max-height: 150px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
            /* add padding to account for vertical scrollbar */
            padding-right: 20px;
        } 
	</style>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div>
	<form name="xform" id="xform">
		<input type=hidden id="grfp_no" value="<?php echo $grfp_no; ?>">
		<input type="hidden" name="cSelected" id="cSelected" value="<?php echo $cSelected; ?>">
		<table width=100% cellpadding=0 cellspacing=0 border=0 align=center>
			<tr>
				<td class="upper_menus" align=left>
					<?php setHeaderControls($res['status'],$grfp_no,$_SESSION['userid'],$dS); ?>
				</td>
				<td width=30% align=right style='padding-right: 5px;'><?php if($grfp_no) { setNavButtons($grfp_no); } ?></td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
		<table width=100% cellpadding="0" cellspacing="0" align=center style="border: 1px solid #436178;">
			<tr>
				<td align=left class="gridHead">General Request for Payment</td>
				<td align=right class="gridHead"><?php if($_REQUEST['grfp_no'] != '') echo "DOCUMENT NO. ". str_pad($_SESSION['branchid'],3,'0',STR_PAD_LEFT) . '-' . str_pad($grfp_no,10,'0',STR_PAD_LEFT); ?></td>
			</tr>
		</table>

		<table border="0" cellpadding="0" cellspacing="0" width=100% class="td_content">
			<tr>
				<td width=60% valign=top>
					<table width=100% style="padding:0px 0px 0px 0px;" >
						<tr><td height=2></td></tr>
						<tr>
							<td class="bareBold" align=right valign=top width=25% style="padding-right: 5px;">Requestor :</td>
							<td align="left">
								<input class="inputSearch2" type="text" name="emp_name" id="emp_name" autocomplete="off" value="<?php echo $res['emp_name']; ?>" style="padding-left: 22px; width: 75%;" >
								<input type="hidden" id="employee_id" name="employee_id" value="<?php echo $res['employee_id']?>">
							</td>
						</tr>
						<tr><td height=2></td></tr>
						<tr>
							<td align="right" class="bareBold" style="padding-right: 5px;">Department :</td>
							<td align=left>
								<input class="gridInput" style="width:75%;" type=text nam e= "department" id = "department" value="<?php echo $res['department']; ?>" readonly >
							</td>				
						</tr>
						<tr><td height=2></td></tr>
						<tr>
							<td align="right" class="bareBold" style="padding-right: 5px;">Purpose :</td>
							<td align=left>
								<input class="gridInput" style="width:75%;" type=text name="grfp_purpose" id="grfp_purpose" value="<?php echo $res['payment_for']; ?>" >
							</td>				
						</tr>
						<tr><td height=2></td></tr>
					</table>
				</td>
				<td valign=top>
					<table border="0" cellpadding="0" cellspacing="1" width=100%>
						<tr><td height=2></td></tr>
						<tr>
							<td align="right" width="30%" class="bareBold" style="padding-right: 5px;">Trans. Date&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:60%;" type=text name="grfp_date" id="grfp_date" value="<?php if(!$res['gd8']) { echo date('m/d/Y'); } else { echo $res['gd8']; }?>"  onchange='javascript:;'>
							</td>				
						</tr>
						<tr><td height=2></td></tr>
						<tr>
							<td align="right" width="30%" class="bareBold" style="padding-right: 5px;">Date Needed&nbsp;:</td>
							<td align=left>
								<input class="gridInput" style="width:60%;" type=text name="date_needed" id="date_needed" value="<?php if(!$res['date_needed']) { echo date('m/d/Y'); } else { echo $res['date_needed']; }?>"  onchange='javascript: ;'>
							</td>				
						</tr>
						<tr><td height=2></td></tr>
						<tr>
							<td align="right" width="30%" class="bareBold" style="padding-right: 5px;">Cost Center:</td>
							<td align=left>
								<select style = "width:60%;" id = "costcenter" name = "costcenter" class="gridInput"> 
									<?php $cc = $con->dbquery("SELECT unitcode, costcenter FROM options_costcenter order by costcenter;");
										while(list($ucode,$ccenter) = $cc->fetch_array()) {
											echo "<option value='$ucode' ";
											if($res['costcenter'] == $ucode) { echo "selected"; }
											echo ">$ccenter</option>";
										}
									?>
								</select>
							</td>				
						</tr>
						<tr><td height=2></td></tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width=100% colspan=2 valign=top class="inner_border_bottom">
					<table width=100% cellspacing=0 cellpadding=0 >
						<tr>
							<td align=right width=15% valign="top" class=bareBold style="padding-right: 5px;">Remarks/Memo :</td>
							<td align=left>
								&nbsp;<textarea type="text" id="remarks" style="width:87%;" rows=3><?php echo $res['remarks']; ?></textarea>
							</td>
						</tr>
						<tr><td height=4></td></tr>
						<tr>
							<td align="right" class="bareBold" style="padding-right: 5px;">Payee : </td>
							<td align=left>
								<input type='text'  id="payeeid" value="<?php echo $res['payee_code'];?>" class="inputSearch2" style="width: 80px; padding-left: 22px;">&nbsp;&nbsp;<input class="gridInput" type="text" id="payee" style="width:200px;" value="<?php echo $res['payee']; ?>" readonly >
							
							</td>
						</tr>
						<tr><td height=4></td></tr>
						<tr>
							<td align="right" class="bareBold" style="padding-right: 5px;">Amount : </td>
							<td align=left><input style = "text-align:right"  class="gridInput" type="text" id="grfp_amount" style="width:60px;" value="<?php echo number_format($res['amount'],2); ?>" ></td>
						</tr>
						<tr><td height=8></td></tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
</div>
<div class="suggestionsBox" id="suggestions" style="display: none;">
	<div class="suggestionList" id="autoSuggestionsList">&nbsp;</div>
</div>
<div class="suggestionsBox" id="cancel_box" style="display: none;">
	<table >
		<tr>
			<td style="color:grey">Reason : </td>
			<td> <textarea id = "cancel_remarks" rows=2 cols=30> </textarea> </td>
		</tr>
	</table>
</div>
</body>
</html>