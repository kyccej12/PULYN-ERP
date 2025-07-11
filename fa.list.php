<?php
	require_once "handlers/initDB.php";
	$p = new myDB;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?php echo $_SESSION[companyName]; ?></title>

<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link href="style/dropMenu.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
<script language="javascript" src="js/jquery.dialogextend.js"></script>
<script language="javascript" src="js/dropMenu.js"></script>
<script language="javascript" src="js/jquery.center.js"></script>
	
<script>

	var UID = "";
	
	function selectFA(obj) {
		gObj = obj;
		$(obj).closest("tr").siblings().removeClass("diffcolor");
		$(obj).toggleClass("diffcolor");
		tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); UID = tmp_obj[1];
	}
	
	function viewAsset() {
		if(UID == "") {
			parent.sendErrorMessage("Unable to retrieve record. Please select a record from the list, and once highlighted, press  \"<b><i>View Asset Information</i></b>\" button again...");
		} else {
			parent.viewFA(UID);
		}
	}
	
	function generateLapsing() {
		
		//alert(cmonth);
		$("#lapsingfilter").dialog({
				width: 400,
				resizable: false,
				modal: true,
				buttons: {
					"Generate Excel": function() { 
					var cyear = $('#fy_fix').val();
					var fxmonth = $("#fm_fixasset").val();
					
					window.open("export/fix_lapsing.php?cyear="+cyear+"&cmonth="+fxmonth+"","Inventory Stockcard","location=1,status=1,scrollbars=1,width=640,height=720");
					},
					"Export to JV": function() { 
					var cyear = $('#fy_fix').val();
					var fxmonth = $("#fm_fixasset").val();
					
					window.open("export/fix_lapsing_jv.php?cyear="+cyear+"&cmonth="+fxmonth+"","Inventory Stockcard","location=1,status=1,scrollbars=1,width=640,height=720");
					}
				}
			});
	}
	
</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
			<table border="0" cellpadding="0" cellspacing="0" width=100%>
				<tr bgcolor="#595959">
					<td align=left class="dgridhead" width="10%">ASSET #</td>
					<td align=left class="dgridhead" width="20%">DESCRIPTION</td>
					<td align=left class="dgridhead" width="25%">CATEGORY</td>
					<td align=center class="dgridhead" width="10%">ASSET COST</td>
					<td align=left class="dgridhead" width="12%">ASSIGNED TO</td>
					<td align=center class="dgridhead" width="13%">DATE ACQUIRED</td>
					<td align=center class="dgridhead">STATUS</td>
					<td align=center class="dgridhead" width="18">&nbsp;</td>
				</tr>
			</table>
			<div id="details" style="height:410px; overflow: auto;">
				<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">
				<?php
					$i = 0;
					
					if(isset($_GET['searchtext']) && $_GET['searchtext'] != '') { 
						$araynako = explode(" ",$_GET['searchtext']);
						foreach($araynako as $sakit) {
							$tunga = $tunga . " asset_no = '$sakit' || proj_code like '%$sakit%' || asset_description  like '%$sakit%' || vendor like '%$sakit%' || b.category like '%$sakit%' ||";
						}
						
						$tunga = substr($tunga,0,-3);
						$gipangita = " and ($tunga) ";
					}

					$getRec = $p->dbquery("select fid, asset_no, asset_description, b.category, a.cost, if(date_acquired='0000-00-00','',date_format(date_acquired,'%m/%d/%Y')) as po, `status`, assigned_to from fa_master a left join fa_category b on a.category=b.id where 1=1 $gipangita order by asset_no;");
					while($row = $getRec->fetch_array(MYSQLI_ASSOC)) {
						if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
						echo "<tr bgcolor=\"$bgC\" onmouseover=\"highlightTableRowVersionA(this, '#3399ff');\" id='obj_$row[fid]' onclick='selectFA(this);'>
								<td class=dgridbox align=left width=\"10%\" valign=top>$row[asset_no]</td>
								<td class=dgridbox align=left width=\"20%\" valign=top>$row[asset_description]</td>
								<td class=dgridbox align=left width=\"25%\" valign=top>$row[category]</td>
								<td class=dgridbox align=right width=\"10%\" valign=top style='padding-right: 20px;'>".number_format($row['cost'],2)."</td>
								<td class=dgridbox align=left width=\"14%\" valign=top style='padding-left: 15px;'>$row[assigned_to]</td>
								<td class=dgridbox align=center width=\"11%\" valign=top>$row[po]&nbsp;</td>
								<td class=dgridbox align=center width=\"10%\" valign=top>$row[status]</td>
							</tr>"; $i++; 
						}
					if($i < 20) {
						for($i; $i <= 20; $i++) {
							if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
							echo "<tr bgcolor='$bgC'><td colspan='7'>&nbsp;</td></tr>";
						}
					}
				?>
				</table>
			</div>
			<table width="100%"  cellspacing="0" cellpadding="0" style="padding-left: 5px;">
				<tr><td height=8></td></tr>
				<tr>
					<td>
						<button onClick="parent.viewFA('');" class="buttonding"><img src="images/icons/add.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;New Asset Record</b></button>
						<button onClick="viewAsset();" class="buttonding"><img src="images/icons/fasset.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;View Asset Information</b></button>
						<button onClick="parent.showFA();" class="buttonding"><img src="images/icons/refresh.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Refresh List</b></button>
						<button onClick="parent.showSearch('asset');" class="buttonding"><img src="images/icons/search.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Search Record</b></button>
						<button onClick="generateLapsing()" class="buttonding"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export</b></button>
					</td>
				</tr>
			</table>

		</td>
	</tr>
 </table>

 <div id="lapsingfilter" style = "display:none;">
	<table  width="80%"> 
		<tr>
			<td class='dgridbox' style="padding-left:20px;">Year</td>
			<td class='dgridbox'>
				<select name="fy_fix" id="fy_fix" style="width: 70%;">
					<?php
						$minYear = 2016;
						list($maxYear) = getArray("SELECT YEAR(NOW())+5;");
						while($minYear<=$maxYear){
							echo "<option value='$minYear'> $minYear </option>";
							$minYear++;
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class='dgridbox' style="padding-left:20px;">Month</td>
			<td class='dgridbox'>
				<select name="fm_fixasset" id="fm_fixasset" style="width: 70%;">
					<option value=""> SELECT </option>
					<?php
						$month = array('1'=>'JAN','2'=>'FEB','3'=>'MAR','4'=>'APR','5'=>'MAY','6'=>'JUN','7'=>'JUL','8'=>'AUG','9'=>'SEP','10'=>'OCT','11'=>'NOV','12'=>'DEC');
						//$minMonth = 1;
						foreach($month as $key => $value){
							echo "<option value='$key'> $value </option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>	
			<td align ="center" class='dgridbox' colspan=2 >
			<!--	<button onclick="export_list('pdf');" class="buttonding"><img src="images/icons/pdf.png" width=18 height=18 align=absmiddle />&nbsp;PDF Format</button> &nbsp;&nbsp; -->
			</td>
		</tr>
	</table>
</div>

</body>
</html>
<?php mysql_close($con);