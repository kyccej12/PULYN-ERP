<?php
	include("includes/dbUSE.php");
	session_start();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Ozian Realty Development</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/tableH.js"></script>
<script>

	var cID = "";

	function showReservation() {
		var bk = $("#block_no").val(); var lt = $("#lot_no").val();
		parent.showReservation(bk,lt);
	}
	function selectCID(obj) {
		$(obj).closest("tr").siblings().removeClass("diffcolor");
		$(obj).toggleClass("diffcolor");
		tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); cID = tmp_obj[1];
	}
	function showComDetails() {
		if(cID == "") {
			parent.sendErrorMessage("Unable to retrieve record. Please select a record from the list, and once highlighted, press  \"<b><i>View Record Details</i></b>\" button again...");
		}else{
			$.post("tennant.datacontrol.php", { mod: "getFileID", cid: cID, sid: Math.random() }, function (data) {
				parent.commitNow(data['block_no'],data['lot_no'],cID);
			},"json");
		}
	}
	
	function showPayHistory() {
		if(cID == "") {
			parent.sendErrorMessage("Unable to retrieve record. Please select a record from the list, and once highlighted, press  \"<b><i>View Payment History</i></b>\" button again...");
		} else {
			parent.showPayHistory(cID);
		}
	}
	
	function voidAccount() {
		if(cID == "") {
			parent.sendErrorMessage("Unable to continue. No record selected!");
		} else { 
			$.post("tennant.datacontrol.php", {mod: "checkAcctStat", cid: cID, sid: Math.random() }, function(data){
				if(data['status'] == "Closed") {
					parent.sendErrorMessage("Unable to continue. The account you selected has already been closed!");
				} else {
					if(confirm("Closing this account would enable client's lot to be open for sale. Are you sure you want to contine?") == true) {
						$.post("tennant.datacontrol.php", { mod: "voidAccount", cid: cID, sid: Math.random() }, function() { alert("Account Successfully Closed!"); parent.showActiveAccts(); } );
					}
				}
			},"json");
		}
	}
</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
			<table align=center width=100% height=30 border=0 cellspacing=0 cellpadding=0  style="border-bottom:2px solid black; background-color:#595959; background-image: url(images/4.jpg); font-weight:bold; color:#ffffff;">
				<tr>
					<td align="left" style="font-weight: bold; font-size: 11px; padding-left: 5px;" valign=middle><img src="images/icons/online.png" width=20 height=20 align=absmiddle border=0 />&nbsp;&nbsp;Active Accounts Master List</td>
					<td align=right width="10%" style="padding-right: 2px;" valign=middle>
						<a href="javascript: parent.close_div();" style="text-decoration: none; font-size: 11px; color: #ffffff;"><img src="images/icons/button-logout-text.png" border=0 title="Close"  /></a>
					</td>
				</tr>
			</table>
			<table border="0" cellpadding="0" cellspacing="0" width=100%>
				<tr bgcolor="#595959">
					<td align=center class="dgridhead" width="6%">FILE ID #&nbsp;<a href="#" onclick="javascript: parent.showActiveAccts(4,'<?php echo $_GET['searchtext']; ?>');"><img src="images/down2.png" border=0 align=absmiddle /></a></td>
					<td align=center class="dgridhead" width="6%">BLOCK #&nbsp;<a href="#" onclick="javascript: parent.showActiveAccts(3,'<?php echo $_GET['searchtext']; ?>');"><img src="images/down2.png" border=0 align=absmiddle /></a></td>
					<td align=center class="dgridhead" width="5%">LOT #</td>
					<td align=center class="dgridhead" width="10%">DATE SOLD&nbsp;<a href="#" onclick="javascript: parent.showActiveAccts(2,'<?php echo $_GET['searchtext']; ?>');"><img src="images/down2.png" border=0 align=absmiddle /></a></td>
					<td align=center class="dgridhead" width="20%">CLIENT'S NAME&nbsp;<a href="#" onclick="javascript: parent.showActiveAccts(1,'<?php echo $_GET['searchtext']; ?>');"><img src="images/down2.png" border=0 align=absmiddle /></a></td>
					<td align=center class="dgridhead" width="15%">CONTRACT PRICE</td>
					<td align=center class="dgridhead" width="10%">DOWN PAYMENT</td>
					<td align=right class="dgridhead" width="12%">AMOUNT AMORTIZED</td>
					<td align=center class="dgridhead" width="12%">TERMS</td>
					<td align=center class="dgridhead">STATUS</td>
					<td align=center class="dgridhead" style="width: 20px;">&nbsp;</td>
				</tr>
			</table>
			<div id="details" style="height:390px; overflow: auto;">
				<table width=100% cellspacing=0 cellpadding=0 style="padding: 0 2px 2px 2px;" onMouseOut="javascript:highlightTableRowVersionA(0);">
				<?php
					$i = 0;
					
					if(isset($_GET['searchtext']) && $_GET['searchtext'] != '') { 
						$araynako = explode(" ",$_GET['searchtext']);
						foreach($araynako as $sakit) {
							$tunga = $tunga . "fname like '%$sakit%' || lname like '%$sakit%' || mname like '%$sakit%' || email like '%$sakit%' || address like '%$sakit%' ||";
						}
						$tunga = substr($tunga,0,-3);
						$gipangita = " and ($tunga) ";
					}
					
					switch($_GET['sort']) {
						case "1": $order = " order by lname, fname, mname "; break;
						case "2": $order = " order by trans_date asc "; break;
						case "3": $order = " order by block_no asc, lot_no asc "; break;
						case "4": $order = " order by file_id asc "; break;
					}				
					$getRec = dbquery("SELECT file_id, block_no, lot_no,  CONCAT(lname,', ',fname,' ',mname) AS client, DATE_FORMAT(trans_date,'%m/%d/%Y') AS tdate, contract_price, ROUND(dp_amount+10000) as dp_amount, ROUND(contract_price-dp_amount,2) AS loanable, CONCAT(pp_terms,' Months') AS contract, status FROM contracts WHERE 1=1 $gipangita $order;");
					while($row = mysql_fetch_array($getRec)) {
						if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
						echo "<tr bgcolor=\"$bgC\" onmouseover=\"highlightTableRowVersionA(this, '#3399ff');\" id='obj_$row[file_id]' onclick='selectCID(this);'>
								<td class=dgridbox align=center width=\"6%\">$row[file_id]</td>
								<td class=dgridbox align=center width=\"6%\">$row[block_no]</td>
								<td class=dgridbox align=center width=\"5%\">$row[lot_no]</td>
								<td class=dgridbox align=center width=\"10%\">$row[tdate]</td>
								<td class=dgridbox align=center width=\"20%\">$row[client]</td>
								<td class=dgridbox align=center width=\"15%\">".number_format($row['contract_price'],2)."</td>
								<td class=dgridbox align=center width=\"10%\">".number_format($row['dp_amount'],2)."</td>
								<td class=dgridbox align=center width=\"15%\">".number_format($row['loanable'],2)."</td>
								<td class=dgridbox align=center >$row[contract]</td>
								<td class=dgridbox align=center width=\"5%\">$row[status]</td>
							</tr>"; $i++; 
						}
					if($i < 18) {
						for($i; $i <= 18; $i++) {
							if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
							echo "<tr bgcolor='$bgC'><td colspan='10'>&nbsp;</td></tr>";
						}
					}
				?>
				</table>
			</div>
			<table width="100%"  cellspacing="0" cellpadding="0" style="padding-left: 5px;">
				<tr><td height=8></td></tr>
				<tr>
					<td>
						<button onClick="showComDetails();" class="buttonding" id="btn_rsv" style="width: 200px;"><img src="images/icons/cyl_bal.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;View Record Details</b></button>
						<button onClick="showPayHistory();" class="buttonding" id="btn_rsv" style="width: 220px;"><img src="images/icons/apv256.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;View Payment History</b></button>
						<button onClick="voidAccount();" class="buttonding" id="btn_rsv" style="width: 220px; <?php if($_SESSION['utype'] != 'admin') { echo "visibility: hidden;"; } ?>" ><img src="images/icons/cancel48.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Close Selected Account</b></button>
						<button onClick="parent.showSearch('active');" class="buttonding" id="btn_dpst" style="width: 180px;"><img src="images/icons/search.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Search Record</b></button>
					</td>
				</tr>
			</table>
		</td>
	</tr>
 </table>
</body>
</html>
<?php mysql_close($con);