<?php
	session_start();
	include("includes/dbUSE.php");	
	list($looper) = getArray("select datediff('".formatDate($_GET['dt2'])."','".formatDate($_GET['dtf'])."')");
	$id_no = $_GET['id_no'];
	list($sched) = getArray("select paysched from hris.e_master where id_no = '$id_no';");
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>$co[company_name] ERP System Ver. 1.0b</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>
		function saveDTR(rid,date,day,sched,id_no,type,val) {
			if(val == "") {
				parent.sendErrorMessage("Invalid Value specified!")
			} else {
				$.post("payroll.datacontrol.php", { mod: "saveEDTR", rid: rid, date: date, day: day, type: type, sched: sched, id_no: id_no, val: val, sid: Math.random()});
			}
		}

		function updateBySched(rid,date,day,sched,id_no,type,tsched) {
			if(tsched == "") {
				parent.sendErrorMessage("Invalid Value specified!")
			} else {
				$.post("payroll.datacontrol.php", { mod: "saveEDTR", rid: rid, date: date, day: day, sched: sched, id_no: id_no, tsched: tsched, sid: Math.random()});
			}
		}
		
		function updateOT(rid,val) {
			if(val == "") {
				parent.sendErrorMessage("Invalid Value specified!")
			} else {
				$.post("payroll.datacontrol.php", { mod: "updateOT", rid: rid, val: val, sid: Math.random()});
			}
		}
		
		function printDTR(dtf,dt2,id_no) {
			window.open("reports/dtr.php?dtf="+dtf+"&dt2="+dt2+"&id_no="+id_no+"&sid="+Math.random()+"","Transaction History","location=1,status=1,scrollbars=1,width=640,height=720");
		}

		function otApprove(rid,val) {
			if(val == "Y") {
				if(confirm("Are you sure you want to approve employee's overtime?") == true) {
					$.post("payroll.datacontrol.php", { mod: "otApprove", rid: rid, sid: Math.random() });
				}
			} else {
				$.post("payroll.datacontrol.php", { mod: "otDisApprove", rid: rid, sid: Math.random() });	
			}
		}
	</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
			<table border="0" cellpadding="0" cellspacing="0" width=100%>
				<tr bgcolor="#cdcdcd">
					<td align=left class="spandix-l" style="border-bottom: 1px solid black; padding-left: 20px; width:20%"><b>DATE</b></td>
					<td align=center class="spandix-l" style="border-bottom: 1px solid black; padding: 4px; width:20%"><b>TIME IN</b></td>
					<td align=center class="spandix-l" style="border-bottom: 1px solid black; padding: 4px; width:20%"><b>TIME OUT</b></td>
					<td align=center class="spandix-l" style="border-bottom: 1px solid black; padding: 4px; width:20%"><b>LATE</b></td>
					<td align=center class="spandix-l" style="border-bottom: 1px solid black; padding: 4px; width:20%"><b>SCHEDULE</b></td>
					<td align=center class="spandix-l" style="border-bottom: 1px solid black; padding: 4px; width:20%"><b>REG. HRS</b></td>
					<td align=center class="spandix-l" style="border-bottom: 1px solid black; padding: 4px; width:20%"><b>OT</b></td>
					<td align=center class="spandix-l" style="border-bottom: 1px solid black; padding: 4px; width:20%"><b>OT APPROVED?</b></td>
				</tr>
				<?php
					
					for($x=0; $x <= $looper; $x++) {
						if($x%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
						list($date,$xd8,$day) = getArray("select date_add('".formatDate($_GET['dtf'])."', INTERVAL $x DAY),date_format(date_add('".formatDate($_GET['dtf'])."', INTERVAL $x DAY),'%m/%d/%y %a'), date_format(date_add('".formatDate($_GET['dtf'])."', INTERVAL $x DAY),'%a');");
						list($rid,$in,$out,$hrs,$late,$ot,$ota,$d_sched) = getArray("select record_id, time_format(t_in,'%H:%i') as `in`, time_format(t_out,'%H:%i') as `out`, hrs, late, ot, ot_approved,d_sched from hris.e_dtr where emp_id = '$id_no' and `date` = '$date';");
						//if($late!=''){ $late = round(60*$late);	}
						echo "<tr bgcolor=\"$bgC\" >
								<td class='dgridbox'>$xd8</td>
								<td class='dgridbox' align=center><input type='text' style='border: none; width: 50px; background-color: $bgC; text-align: center;' value='$in' onchange=\"javascript: saveDTR('$rid','$date','$day','$sched','$id_no','t_in',this.value);\"></td>
								<td class='dgridbox' align=center><input type='text' style='border: none; width: 50px; background-color: $bgC; text-align: center;' value='$out' onchange=\"javascript: saveDTR('$rid','$date','$day','$sched','$id_no','t_out',this.value);\"></td>
								<td class='dgridbox' align=center><input type='text' style='border: none; width: 50px; background-color: $bgC; text-align: center;' id=late[$rid] value='$late' readonly></td>
								<td class='dgridbox' align=center>";
								if(($sched==1||$sched==2) && ($in != '' ||$out!='')){
									echo "<select style='width:100px;text-align:center' onchange = \"updateBySched('$rid','$date','$day','$sched','$id_no','t_in',this.value)\">
												<option value=''>SELECT</option>";
											$opt_sched = dbquery("SELECT a.sched,a.sched_desc FROM options_sched a;");
											while($row = mysql_fetch_array($opt_sched)){
												if($d_sched==$row['sched']){ $slct = "selected=selected";	}else{$slct = "";	}
												echo "<option value = '$row[sched]' $slct> $row[sched_desc] </option>";
											}
									echo "</select>";
								}else{
									
								}
								echo "</td>
								<td class='dgridbox' align=center><input type='text' style='border: none; width: 50px; background-color: $bgC; text-align: center;' id=hrs[$rid] value='$hrs' readonly></td>
								<td class='dgridbox' align=center><input type='text' style='border: none; width: 50px; background-color: $bgC; text-align: center;' id=ot[$rid] value='$ot' onchange=\"javascript: updateOT('$rid',this.value);\"></td>
								<td class='dgridbox' align=center>";
								if($ot > 0) {
									if($ota == "Y") { $selected = "selected"; } else { $selected = ""; }
									echo "<select id=ota[$rid] onchange=\"otApprove('$rid',this.value);\"><option value='N'>N</option><option value='Y' $selected>Y</option></select>";
								}
						echo "</td>
						</tr>";
					}
				?>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding: 5px;">
			<button onClick="printDTR('<?php echo $_GET['dtf']; ?>','<?php echo $_GET['dt2']; ?>','<?php echo $_GET['id_no']; ?>');" class="buttonding" id="btn_rsv" style="width: 200px;"><img src="images/icons/print.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Print Daily Time Record</b></button>
			<button type="button" class="buttonding" style="font-size: 11px;" onclick="parent.refreshEDTR('<?php echo $_GET['dtf']; ?>','<?php echo $_GET['dt2']; ?>','<?php echo $id_no; ?>');"><img src="images/icons/refresh.png" width=18 height=18 align=absmiddle />&nbsp;&nbsp;Refresh List</b></button>
		</td>
	</tr>
 </table>
</body>
</html>
<?php mysql_close($con); ?>