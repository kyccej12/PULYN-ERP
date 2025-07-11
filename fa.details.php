<?php
	session_start();
	
	//ini_set("display_errors","On");
	require_once "handlers/initDB.php";
	$p = new myDB;
	
	
	if(isset($_GET['fid']) && $_GET['fid'] != "") { 
		$res = $p->getArray("select *,if(warranty_exp != '0000-00-00',date_format(warranty_exp,'%m/%d/%Y'),'') as wnty,if(date_acquired != '0000-00-00',date_format(date_acquired,'%m/%d/%Y'),'') as pd8,if(cv_date != '0000-00-00',date_format(cv_date,'%m/%d/%Y'),'') as cd8,if(date_assigned != '0000-00-00',date_format(date_assigned,'%m/%d/%Y'),'') as dassigned from fa_master where fid = '$_GET[fid]';"); 
	}
	
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title><?php echo $_SESSION[companyName]; ?></title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="style/jquery-ui.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="js/jquery.js"></script>
	<script language="javascript" src="js/jquery-ui.js"></script>
	<script language="javascript" src="js/date.js"></script>
	<script>
		function fa_save() {
			var msg = "";
			var cost = parent.stripComma($("#cost").val());

			if($("#asset_no").val() == "") { msg = msg + "- You did not specify Asset No. for this Fixed Asset<br/>"; }
			if($("#asset_description").val() == "") { msg = msg + "- You did not Asset Description for this Fixed Asset<br/>"; }
			if($("#category").val() == "") { msg = msg + "- You did not specify what category this Fixed Asset belong<br/>"; }
			if($("#assigned_to").val() == "") { msg = msg + "- You must indicate the personnel responsible for this asset<br/>"; }
			if(isNaN(cost) == true) { msg = msg + "- You have specified an invalid acquisition cost<br/>"; }
			if(isNaN($("#lifespan").val()) == true || $("#lifespan").val() == '') { msg = msg + "- You have specified an invalid Asset life span<br/>"; }
			if($("#lifespan").val() == "") { msg = msg + "- For lapsing purposes, please specify life span for this asset.<br/>"; }
			if($("#po_date").val() == "") { msg = msg + "- For lapsing purposes, please specify Acqusisition Date for this asset<br/>"; }
			if($("#asset_acct").val() == "") { msg = msg + "- For lapsing purposes, please indicate Asset Account for this Fixed Asset<br/>"; }
			if($("#adpn_acct").val() == "") { msg = msg + "- For lapsing purposes, please indicate Accumulated Dep'n Account for this Fixed Asset<br/>"; }
			if($("#dpn_acct").val() == "") { msg = msg + "- For lapsing purposes, please indicate Depreciation Expense for this Fixed Asset<br/>"; }
			
			
			if(msg!="") {
				parent.sendErrorMessage(msg);
			} else {
				$.post("main.datacontrol.php", { mod: "checkDupAssetNo", fid: $("#fid").val(), asset_no: $("#asset_no").val(), sid: Math.random() }, function(data) {
					if(data == "NODUPLICATE") {
						var url = $(document.frmasset).serialize();
							url = "mod=saveAsset&"+url;
						$.post("main.datacontrol.php", url);
						alert("Asset Successfully Saved!"); 
						parent.closeDialog("#fadetails");
						parent.showFA();
					} else { parent.sendErrorMessage("Unable to save this information. A duplicate Asset No. has been detected!"); }
				},"html");	
			}
		}
		
		$(function() { $("#date_assigned").datepicker(); $("#cv_date").datepicker(); $("#po_date").datepicker(); $("#check_date").datepicker(); $("#warranty_exp").datepicker(); });
	</script>
</head>
<body bgcolor="#d6d6d6" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<form name="frmasset" id="frmasset">
	<input type="hidden" name="fid" id="fid" value="<?php echo $_GET['fid']; ?>">
	<table width=100% cellspacing=0 cellpadding=0 class="td_content" style="padding: 10px;">
		<tr>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Category&nbsp;:</td>
			<td align=left width=33%>
				<select name="category" id="category" class="nInput" style="width:80%;">
					<option value="">- Select Category -</option>
					<?php 
						$c = $p->dbquery("select id, category from fa_category;");
						while(list($a,$b) = $c->fetch_array()) {
							echo "<option value='$a' ";
								if($a == $res['category']) { echo "selected"; }
									echo ">$b</option>";
						}
						unset($c);
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4 colspan="4"></td></tr>
		<tr>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Desciption&nbsp;:</td>
			<td align=left >
				<input type="text" name="asset_description" id="asset_description" class="nInput" style="width: 80%; font-weight: bold; " value="<?php echo $res['asset_description']; ?>" >
			</td>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">SN/VIN&nbsp;:</td>
			<td align=left width=33%>
				<input type="text" name="serial_no" id="serial_no" class="nInput" style="width: 80%;" value="<?php echo $res['serial_no']; ?>" >
			</td>
		</tr>
		<tr><td height=4 colspan="4"></td></tr>
		<tr>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Asset Number&nbsp;:</td>
			<td align=left width=33%>
				<input type="text" name="asset_no" id="asset_no" class="nInput" style="width: 80%; " value="<?php echo $res['asset_no']; ?>" >
			</td>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Warranty Expiration&nbsp;:</td>
			<td align=left width=33%>
				<input type="text" name="warranty_exp" id="warranty_exp" class="nInput" style="width: 80%;" value="<?php echo $res['wnty']; ?>" >
			</td>
		</tr>
		<tr><td height=4 colspan="4"></td></tr>
		<tr>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Vendor&nbsp;:</td>
			<td align=left >
				<input type="text" name="vendor" id="vendor" class="nInput" style="width: 80%;" value="<?php echo $res['vendor']; ?>" >
			</td>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Life Span (in years)&nbsp;:</td>
			<td align=left width=33%>
				<input type="text" name="lifespan" id="lifespan" class="nInput" style="width: 80%;" value="<?php echo $res['life_span']; ?>" >
			</td>
		</tr>
		<tr><td height=4 colspan="4"></td></tr>
		<tr>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">PO Number&nbsp;:</td>
			<td align=left width=33% >
				<input type="text" name="po_no" id="po_no" class="nInput" style="width:80%;" value="<?php echo $res['po_no']; ?>" >
			</td>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Invoice Number&nbsp;:</td>
			<td align=left width=33%>
				<input type="text" name="inv_no" id="inv_no" class="nInput" style="width: 80%;" value="<?php echo $res['inv_no']; ?>" >
			</td>
		</tr>
		<tr><td height=4 colspan="4"></td></tr>
		<tr>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">CV Number&nbsp;:</td>
			<td align=left width=33%>
				<input type="text" name="cv_no" id="cv_no" class="nInput" style="width:80%;" value="<?php echo $res['cv_no']; ?>" >
			</td>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Check Number&nbsp;:</td>
			<td align=left width=33%>
				<input type="text" name="check_no" id="check_no" class="nInput" style="width: 80%;" value="<?php echo $res['check_no']; ?>" >
			</td>
		</tr>
		<tr><td height=4 colspan="4"></td></tr>
		<tr>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Date Acquired&nbsp;:</td>
			<td align=left  width=33% >
				<input type="text" name="po_date" id="po_date" class="nInput" style="width: 80%;" value="<?php echo $res['pd8']; ?>" >
			</td>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Check Date&nbsp;:</td>
			<td align=left  width=33% >
				<input type="text" name="check_date" id="check_date" class="nInput" style="width: 80%;" value="<?php echo $res['cd8']; ?>" >
			</td>
		</tr>
		<tr><td height=4 colspan="4"></td></tr>
		<tr>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Asset Account&nbsp;:</td>
			<td align=left width=33%>
				<select name="asset_acct" id="asset_acct" class="nInput" style="width: 80%;" >
					<option value="" > - Select Asset Account - </option>
					<?php 
						$f = $p->dbquery("select acct_code,description from acctg_accounts where acct_grp = '3' and parent != 'Y';"); 
							while(list($d,$e) = $f->fetch_array()) {
								echo "<option value='$d' ";
								if($res['asset_acct'] == $d) { echo "selected"; }
								echo ">$e</option>";
							}
							unset($f);
						?>
				</select>
			</td>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Accu. Depn Account&nbsp;:</td>
			<td align=left width=33%>
				<select name="adepn_acct" id="adpn_acct" class="nInput"  style="width: 80%;" >
					<option value=""> - Select Acccu Dep'n Account - </option>
						<?php 
							$i = $p->dbquery("select acct_code, description from acctg_accounts where acct_grp = '5' and parent != 'Y';"); 
							while(list($g,$h) = $i->fetch_array()) {
								echo "<option value='$g' ";
								if($res['adeprn_acct'] == $g) { echo "selected"; }
								echo ">$h</option>";
							}
							unset($i);
						?>
				</select>
			</td>
		</tr>
		<tr><td height=4 colspan="4"></td></tr>
		<tr>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Cost&nbsp;:</td>
			<td align=left width=33% >
				<input type="text" name="cost" id="cost" class="nInput" style="width: 80%;" value="<?php echo number_format($res['cost'],2); ?>" >
			</td>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Dep. Expenses&nbsp;:</td>
			<td align=left width=33%>
				<select name="depn_acct" id="depn_acct" class="nInput"  style="width: 80%;" >
					<option value="" > - Select Dep'n Expenses - </option>
						<?php 
							$l = $p->dbquery("select acct_code, description from acctg_accounts where acct_grp = '13' and parent != 'Y';"); 
							while(list($j,$k) = $l->fetch_array()) {
								echo "<option value='$j' ";
								if($res['deprn_acct'] == $j) { echo "selected"; }
								echo ">$k</option>";
							}
						?>
				</select>
			</td>
		</tr>
		<tr><td height=4 colspan="4"></td></tr>

		
		<tr>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;"> Type of Cost  </td>
			<td align=left  width=33%>
				<select name="amount_type" class="nInput" style="width:80%;" id="amount_type" >
					<option value="Y" <?php if($res['vatable'] == "Y") { echo "selected"; } ?> > GROSS OF VAT </option>
					<option value="N" <?php if($res['vatable'] == "N") { echo "selected"; } ?> > NET OF VAT </option>
				</select>
			</td>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Status&nbsp;:</td>
			<td align=left width=33% >
				<select name="status" id="status" class="nInput" style="width:80%;" id="fa_status" >
					<option value=""> - Select Status - </option>
					<option value="IN-USE" <?php if($res['status'] == "IN-USE") { echo "selected"; } ?>>In-Use</option>
					<option value="REPAIR" <?php if($res['status'] == "REPAIR") { echo "selected"; } ?>>Under Repair</option>
					<option value="DISPOSED" <?php if($res['status'] == "DISPOSED") { echo "selected"; } ?>>Disposed/Sold</option>
					<option value="DONATED" <?php if($res['status'] == "DISPOSED") { echo "selected"; } ?>>Donated</option>
				</select>
			</td>
		</tr>

		<tr><td height=4 colspan="4"></td></tr>
		<tr>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Date Assigned&nbsp;:</td>
			<td align=left width=33%>
				<input type="text" name="date_assigned" id="date_assigned" class="nInput" style="width: 80%;" value="<?php echo $res['dassigned']; ?>" >
			</td>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Assigned To&nbsp;:</td>
			<td align=left width=33% >
				<input type="text" name="assigned_to" id="assigned_to" class="nInput" style="width: 80%;" value="<?php echo $res['assigned_to']; ?>" >
			</td>
		</tr>
		<tr><td height=4 colspan="4"></td></tr>
			<tr>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;">Currently Deployed&nbsp;:</td>
			<td align=left  width=33%>
				<select name="proj_code" class="nInput" style="width:80%;" id="proj_code" >
					
				</select>
			</td>
			
		</tr>
		<tr><td height=4 colspan="4"></td></tr>
		<tr>
			<td class="spandix-l" align=right width=18% style="padding-right: 10px;" valign=top>Remarks&nbsp;:</td>
			<td colspan=3 align=left><textarea type="text" id="remarks" name="remarks" style="width:92%;"><?php echo $res['remarks']; ?></textarea></td>											
		</tr>
		<tr><td height=4 colspan="4"></td></tr>
		<tr><td colspan=4><hr style="width: 90%"></hr></td></tr>
		</tr>
		<tr>
			<td colspan=4 style="padding-left: 40px;">
				<button type=button class="buttonding" stylye="height: 25px;" onclick="javascript: fa_save();" ><img src="images/icons/floppy.png" width=18 height=18 align=absmiddle />&nbsp;Save Record</button>
			</td>
		</tr>
	</table>
</form>
</body>
</html>
