<?php
	session_start();
	include("includes/dbUSE.php");	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Geck Distributors</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/tableH.js"></script>
<script>
	function saveDedu(id_no,loanid,dtf,dt2,amount,el) {
		$.post("payroll.datacontrol.php", { mod: "saveDedu", id_no: id_no, lid: loanid, dtf: dtf, dt2: dt2, amount: amount, sid: Math.random() }, function(data) {
			if(data['error'] == "sobra") {
				parent.sendErrorMessage("Error: Unable to deduct specified amount as total deduction for the said employee exceeds its projected gross pay...")
				document.getElementById(el).value = "";
			}
		},"json");
	}
</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
			<table align=center width=100% height=30 border=0 cellspacing=0 cellpadding=0  style="border-bottom:2px solid black; background-color:#595959; background-image: url(images/4.jpg); font-weight:bold; color:#ffffff;">
				<tr>
					<td align="left" style="font-weight: bold; font-size: 11px; padding-left: 5px;" valign=middle><img src="images/icons/loans.png" width=20 height=20 align=absmiddle border=0 />&nbsp;&nbsp;MANAGE PAYROLL DEDUCTIONS (Period: <?php echo $_GET['dtf'] . ' - ' . $_GET['dt2']; ?></td>
					<td align=right width="10%" style="padding-right: 2px;" valign=middle>
						<a href="javascript: parent.close_div();" style="text-decoration: none; font-size: 11px; color: #ffffff;"><img src="images/icons/button-logout-text.png" border=0 title="Close"  /></a>
					</td>
				</tr>
			</table>
			<table border="0" cellpadding="0" cellspacing="0" width=100%>
				<tr bgcolor="#595959">
					<td align=center class="dgridhead" width="10%">ID NO</td>
					<td align=left class="dgridhead" width="30%">EMPLOYEE</td>
					<td align=left class="dgridhead" width="10%">LOAN ID</td>
					<td align=center class="dgridhead" width="15%">DATE AVAILED</td>
					<td align=center class="dgridhead" width="15%">BALANCE</td>
					<td align=center class="dgridhead">AMOUNT APPLIED</td>
					<td align=center class="dgridhead" width="18">&nbsp;</td>
				</tr>
			</table>
			<div id="details" style="height:390px; overflow: auto;">
				<table width=100% cellspacing=0 cellpadding=0 style="padding: 0 2px 2px 2px;" onMouseOut="javascript:highlightTableRowVersionA(0);">
				<?php
					$i = 0;	
					$getRec = dbquery("select distinct a.id_no, concat(lname,', ',fname) as e_name from hris.e_dtr a left join e_master b on a.id_no=b.id_no where paysched = '$_GET[batch]' and a.trans_date between '".formatDate($_GET['dtf'])."' and '".formatDate($_GET['dt2'])."';");
					while($row = mysql_fetch_array($getRec)) {
						$in = dbquery("select file_id, lpad(file_id,3,0) as fid, date_format(date_availed,'%m/%d/%Y') as d8, amount, balance from hris.e_loans where id_no='$row[id_no]' and balance > 0;");
						while($xrow = mysql_fetch_array($in)) {
							if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
							list($ltype) = getArray("select description from hris.e_loantype where type='$row[loan_type]';");
							list($ded) = getArray("select amount from hris.e_paydeductions where loan_id = '$xrow[file_id]' and dtf = '".formatDate($_GET['dtf'])."' and dt2 = '".formatDate($_GET['dt2'])."' and id_no = '$row[id_no]';");
							echo "<tr bgcolor=\"$bgC\" onmouseover=\"highlightTableRowVersionA(this, '#3399ff');\">
									<td class=dgridbox align=center width=\"10%\">$row[id_no]</td>
									<td class=dgridbox align=left width=\"30%\">$row[e_name]</td>
									<td class=dgridbox align=center width=\"10%\">$xrow[fid]</td>
									<td class=dgridbox align=center width=\"15%\">$xrow[d8]</td>
									<td class=dgridbox align=center width=\"15%\">".number_format($xrow['balance'],2)."</td>
									<td class=dgridbox align=center><input type='text' class=gridInput id='".$xrow['file_id']."'' style='width: 80px; text-align: center;' onchange=\"saveDedu('".$row['id_no']."','".$xrow['file_id']."','".formatDate($_GET['dtf'])."','".formatDate($_GET['dt2'])."',this.value,this.id);\" value=\"".$ded."\"></td>
								</tr>"; $i++; 
							}
						}
					if($i < 30) {
						for($i; $i <= 30; $i++) {
							if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
							echo "<tr bgcolor='$bgC'><td colspan='7'>&nbsp;</td></tr>";
						}
					}
				?>
				</table>
			</div>
		</td>
	</tr>
 </table>
</body>
</html>
<?php mysql_close($con);