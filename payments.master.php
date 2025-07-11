<?php
	include("includes/dbUSE.php");
	session_start();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Ozian Realty Development & Services Incorporated</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/date.js"></script>
<script language="javascript" src="js/tableH.js"></script>
<script>
	var rID = "";

	function selectRID(obj) {
		$(obj).closest("tr").siblings().removeClass("diffcolor");
		$(obj).toggleClass("diffcolor");
		tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); rID = tmp_obj[1];
	}
	
	function showPayDetails() {
		if(rID == "") {
			parent.sendErrorMessage("Unable to continue. No record selected!");
		} else { 
			$.post("tennant.datacontrol.php", { mod: "getPayDetails", rid: rID, sid: Math.random() }, function(data) {
				parent.getPayDetails(data['type'],data['month'],data['year'],data['file_id'],data['bk'],data['lt'],data['amount']);
			},"json");
		}
	}
	
	function viewPayHistory() {
		if(rID == "") {
			parent.sendErrorMessage("Unable to continue. No record selected!");
		} else { 
			$.post("tennant.datacontrol.php", { mod: "getPayDetails", rid: rID, sid: Math.random() }, function(data) {
				parent.showPayHistory(data['file_id']);
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
					<td align="left" style="font-weight: bold; font-size: 11px; padding-left: 5px;" valign=middle><img src="images/icons/collection.png" width=20 height=20 align=absmiddle border=0 />&nbsp;&nbsp;Official Receipt Register</td>
					<td align=right width="50%" style="padding-right: 2px;" valign=middle>
						<a href="javascript: parent.close_div();" style="text-decoration: none; font-size: 11px; color: #ffffff;"><img src="images/icons/button-logout-text.png" border=0 title="Close"  /></a>
					</td>
				</tr>
			</table>
			<table border="0" cellpadding="0" cellspacing="0" width=100%>
				<tr bgcolor="#595959">
					<td align=center class="dgridhead" width="10%">FILE ID #&nbsp;<a href="#" onclick="javascript: parent.showORRegister(1,'<?php echo $_GET['searchtext']; ?>');"><img src="images/down2.png" border=0 align=absmiddle /></a></td>
					<td align=center class="dgridhead" width="15%">OR #&nbsp;<a href="#" onclick="javascript: parent.showORRegister(2,'<?php echo $_GET['searchtext']; ?>');"><img src="images/down2.png" border=0 align=absmiddle /></a></td>
					<td align=center class="dgridhead" width="15%">OR DATE&nbsp;<a href="#" onclick="javascript: parent.showORRegister(3,'<?php echo $_GET['searchtext']; ?>');"><img src="images/down2.png" border=0 align=absmiddle /></a></td>
					<td align=center class="dgridhead" width="25%">CLIENT'S NAME&nbsp;<a href="#" onclick="javascript: parent.showORRegister(3,'<?php echo $_GET['searchtext']; ?>');"><img src="images/down2.png" border=0 align=absmiddle /></a></td>
					<td align=center class="dgridhead" width="10%">AMOUNT</td>
					<td align=center class="dgridhead" width="15%">PAYMENT TYPE</td>
					<td align=center class="dgridhead">DOC STATUS</td>
					<td align=center class="dgridhead" width="20">&nbsp;</td>
				</tr>
			</table>
			<div id="details" style="height:385px; overflow: auto;">
				<table width=100% cellspacing=0 cellpadding=0 style="padding: 0 2px 2px 2px;" onMouseOut="javascript:highlightTableRowVersionA(0);">
				<?php
					$i = 0;
					
					if(isset($_GET['searchtext']) && $_GET['searchtext'] != '') { 
						$araynako = explode(" ",$_GET['searchtext']);
						foreach($araynako as $sakit) {
							$tunga = $tunga . "record_id = '$sakit' || file_id = '$sakit' || or_no like '%$sakit%' || odate = '%$sakit%' || `client` like '%$sakit%' ||";
						}
						
						$tunga = substr($tunga,0,-3);
						$gipangita = " and ($tunga) ";
					}
					
					switch($_GET['sort']) {
						case "1": $order = " order by record_id asc "; break;
						case "2": $order = " order by or_no asc "; break;
						case "3": $order = " order by or_date asc "; break;
						case "4": $order = " order by client asc "; break;
					}
					
					$getRec = dbquery("SELECT * FROM (SELECT record_id, a.file_id, or_no, DATE_FORMAT(or_date,'%m/%d/%Y') AS odate, or_date, amount, IF(a.type='AMRTZ','Mo. Amortization','Down Payment') AS `type`, CONCAT(lname,', ',fname,' ',mname) AS `client`,a.status FROM payments a LEFT JOIN contracts b ON a.file_id = b.file_id ORDER BY or_date DESC) a WHERE 1=1 $gipangita $order;");	
					while($row = mysql_fetch_array($getRec)) {
						if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
					
						echo "<tr bgcolor=\"$bgC\" onmouseover=\"highlightTableRowVersionA(this, '#3399ff');\" id='obj_$row[record_id]' onclick='selectRID(this);'>
								<td class=dgridbox align=center width=\"10%\">$row[record_id]</td>
								<td class=dgridbox align=center width=\"15%\">$row[or_no]</td>
								<td class=dgridbox align=center width=\"15%\">$row[odate]</td>
								<td class=dgridbox align=center width=\"25%\">$row[client]</td>
								<td class=dgridbox align=center width=\"10%\">".number_format($row['amount'],2)."</td>
								<td class=dgridbox align=center width=\"15%\">$row[type]</td>
								<td class=dgridbox align=center width=\"10%\">$row[status]</td>
							</tr>"; $i++; $highlighter = ""; $clicker = "";
						}
					if($i < 22) {
						for($i; $i <= 22; $i++) {
							if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
							echo "<tr bgcolor='$bgC'><td colspan='8'>&nbsp;</td></tr>";
						}
					}
				?>
				</table>
			</div>
			<table width="100%"  cellspacing="0" cellpadding="0" style="padding-left: 5px;">
				<tr><td height=8></td></tr>
				<tr>
					<td>
						<button onClick="showPayDetails();" class="buttonding" id="btn_rsv" style="width: 220px;"><img src="images/icons/crr.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;View Payment Details</b></button>
						<button onClick="viewPayHistory();" class="buttonding" id="btn_pay" style="width: 240px;"><img src="images/icons/apv256.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;View Client's Payment History</b></button>
						<button onClick="parent.showSearch('ofreg');" class="buttonding" id="btn_dpst" style="width: 180px;"><img src="images/icons/search.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Search Record</b></button>
					</td>
				</tr>
			</table>
		</td>
	</tr>
 </table>
</body>
</html>
<?php mysql_close($con);