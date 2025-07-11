<?php
	session_start();
	include("handlers/_generics.php");
	$con = new _init;
?>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>City HomeBasic Construction Supply</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>
		var sPO = "";

		function selectItem(obj) {
			$(obj).closest("tr").siblings().removeClass("diffcolor");
			$(obj).toggleClass("diffcolor");
			tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); sPO = tmp_obj[1];
		}
		
		function viewDetails() {
			if(sPO == "") {
				parent.sendErrorMessage("Please select record to view...");
			} else {
				var arg = sPO.split("|");
				switch(arg[1]) {
					case "SI":
						parent.viewSI(arg[0]);
					break;
					case "RR":
						parent.viewRR(arg[0]);
					break;
					case "SRR":
						parent.viewSRR(arg[0]);
					break;
					case "SW":
						parent.viewSW(arg[0]);
					break;
					case "STR":
						parent.viewSTR(arg[0]);
					break;
				}
			}
		}
	</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >

	<table width="100%" class="tgrid" cellpadding=0 cellspacing=0>
		<tr>
			<td class="dgridhead" align=center width=10%><b>DOC #</b></td>
			<td class="dgridhead" align=center width=10%><b>DOC DATE</b></td>
			<td class="dgridhead" align=center width=10%><b>DOC TYPE</b></td>
			<td class="dgridhead" align=left width=30%><b>DESTINATION/ORIGIN</b></td>
			<td class="dgridhead" align=center width=10%><b>IN</b></td>
			<td class="dgridhead" align=center width=10%><b>OUT</b></td>
			<td class="dgridhead" align=center><b>RUN. QTY</b></td>
			<td class="dgridhead" width=18>&nbsp;</td>
		</tr>
	</table>

	<div id="details" style="height:415px; overflow: auto;">
		<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">
			<?php
				$isE = $con->getArray("SELECT doc_no,posting_date as doc_date FROM phy_header WHERE branch = '$_SESSION[branchid]' AND `status` = 'Finalized' AND posting_date <= '".$con->formatDate($_GET['dtf'])."' order by doc_date desc limit 1;");
				if($isE['doc_no'] == '') { $baseD8 = '2021-08-01'; } else { $baseD8 = $isE['doc_date']; }
				

				$pi = $con->getArray("select ifnull(sum(b.qty),0) from phy_header a left join phy_details b on a.doc_no=b.doc_no and a.branch=b.branch where a.branch = '$_SESSION[branchid]' and b.item_code = '$_GET[item_code]' and a.status = 'Finalized' and a.posting_date = '$baseD8' group by b.item_code;");
				$runbal = $con->getArray("select sum(purchases+inbound-pullouts-outbound-sold) from ibook where doc_date >= '2018-05-26' and doc_date < '".$con->formatDate($_GET['dtf'])."' and item_code = '$_GET[item_code]' and doc_branch = '$_SESSION[branchid]';");
				$beg = ROUND(($pi[0]+$runbal[0]),2);

				echo "<tr bgcolor=\"#ffffff\" id='obj_$_GET[item_code]'>
						<td class=\"grid\" valign=top align=center style=\"padding-left: 5px;\" colspan=6><b>BALANCE FORWARDED FROM PREVIOUS PERIOD >></b></td>
						<td class=\"grid\" valign=top align=center width=19%><b>".number_format($beg,2)."</b></td>
					</tr>";
				
				$query = $con->dbquery("select doc_type, doc_no, lpad(doc_no,6,0) as xdoc, cname, date_format(doc_date,'%m/%d/%Y') as dd8, if((purchases+inbound-pullouts-outbound-sold) > 0,abs(purchases+inbound-pullouts-outbound-sold),0) as `in`, if((purchases+inbound-pullouts-outbound-sold) < 0,(purchases+inbound-pullouts-outbound-sold),0) as `out`, (purchases+inbound-pullouts-outbound-sold) as run from ibook where item_code = '$_GET[item_code]' and doc_branch = '$_SESSION[branchid]' and doc_date between '".$con->formatDate($_GET['dtf'])."' and '".$con->formatDate($_GET['dt2'])."';");
				while($row = $query->fetch_array()) {
					if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }

					if($run != "") { $run += $row['run']; } else { $run = $beg + $row['run']; }
					
					echo "<tr bgcolor=\"$bgC\" id='obj_$row[doc_no]|$row[doc_type]' onMouseOver=\"javascript: highlightTableRowVersionA(this, '#e6f0fa');\" onclick=\"javascript: selectItem(this);\">
							<td class=\"grid\" valign=top align=center style=\"padding-left: 5px;\" width=10%>".$row['xdoc']."</td>
							<td class=\"grid\" valign=top align=center width=10%>".$row['dd8']."</td>
							<td class=\"grid\" valign=top align=center width=10%>".$row['doc_type']."</td>
							<td class=\"grid\" valign=top align=left width=31%>".$row['cname']."&nbsp;</td>
							<td class=\"grid\" valign=top align=center width=10%>".number_format($row['in'],2)."</td>
							<td class=\"grid\" valign=top align=center width=10%>".abs(number_format($row['out'],2))."</td>
							<td class=\"grid\" valign=top align=center width=19%>".number_format($run,2)."</td>
					</tr>"; $i++;
				}
				
				//print_r($query);

				if($i < 18) {	
					for($i; $i <= 17; $i++) {	if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
						echo "<tr bgcolor=\"$bgC\">
							<td class=\"grid\" colspan=7>&nbsp;</td>
						</tr>";
					}
				}
			?>
		</table>
	</div>
    <table width=100% cellpadding=5 cellspacing=0>
		<tr>
			<td align=left>
				<button onClick="parent.exportStockcard('<?php echo $_GET['item_code'] ?>','<?php echo $_GET['unit'] ?>','<?php echo $_GET['dtf'] ?>','<?php echo $_GET['dt2'] ?>');" class="buttonding"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Export Stockcard to Excel</button>
				<button onClick="viewDetails();" class="buttonding"><img src="images/icons/bill.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;View Transaction Details</b></button>
			</td>
		</tr>
	</table>
	</body>
</html>