<?php
	//ini_set("display_errors","On");
	require_once "../handlers/initDB.php";
	$con = new myDB;
	
	list($dtf,$dt2,$looper) = $con->getArray("SELECT period_start, period_end, DATEDIFF(period_end,period_start) AS looper FROM omdcpayroll.pay_periods WHERE period_id = '$_REQUEST[period]';");
	$batch = $_REQUEST['batch']; $dept = $_REQUEST['dept']; $period = $_REQUEST['period'];

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Prime Care Cebu Payroll System Ver. 1.0b</title>
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link href="../ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="../ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="../ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="../js/tableH.js"></script>
	<script>

		
		function saveSched(shift,eid,date,batch,dept,period) {
			$.post("misc-data.php", { mod: "saveSchedule", shift: shift, eid: eid, date: date, batch: batch, dept: dept, period: period, sid: Math.random() });
		}
	
	</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top colspan=2>
			<table border="0" cellpadding="0" cellspacing="0" width=100%>
				<tr bgcolor="#cdcdcd">
					<td align=left class="spandix-l" style="border-bottom: 1px solid black; padding-left: 20px; width: 250px;"><b>Employee</b></td>
					
					<?php
						
						for($x=0; $x <= $looper; $x++) {
							list($dayLabel) = $con->getArray("select date_format(date_add('$dtf',INTERVAL $x DAY),'%a %m/%d/%y');");
							echo '<td align=center class="spandix-l" style="border-bottom: 1px solid black; padding: 4px;"><b>'.$dayLabel.'</b></td>';
						}
			
					?>
				</tr>
				
				<?php
					$eQuery = $con->dbquery("select emp_id,`area`,concat(lname,', ',fname,' ',left(mname,1),'.') as ename, shift from omdcpayroll.emp_masterfile where payroll_batch = '$batch' and dept = '$dept' AND employment_status NOT IN (7,8,9,10);");
					while($eRow = $eQuery->fetch_array(MYSQLI_BOTH)) {
						$i = 0;
						if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
						
						echo '<tr bgcolor="'.$bgC.'">
								<td class="grid">'.$eRow['ename'].'</td>';
								 $z = 0;
								for($x=0; $x <= $looper; $x++) {
									list($date) = $con->getArray("select date_add('$dtf',INTERVAL $x DAY);");
									list($dtrSched,$isLock) = $con->getArray("select `shift`,slock from omdcpayroll.emp_dtrfinal where emp_id = '$eRow[emp_id]' and `date` = '$date';");
									if($sLock == 'Y') { $isDisabled = 'disabled'; }
									
									echo '<td class="grid" align=center>
											<select id="emp_shift['.$z.']" name="emp_shift['.$z.']" style="width : 90%;" onchange="javascript: saveSched(this.value,\''.$eRow['emp_id'].'\',\''.$date.'\',\''.$batch.'\',\''.$dept.'\',\''.$period.'\');" '.$isDisabled.'>';
											$_sq = $con->dbquery("SELECT '0' AS shift_id, 'Rest Day' AS remarks UNION ALL SELECT '99' AS shift_id, 'Flexible Time' AS remarks UNION ALL SELECT DISTINCT shift_id, remarks FROM omdcpayroll.emp_shifts");
											echo '<option value="">- Select -</option>';
											while($sqrow = $_sq->fetch_array()) {
												
												if($sqrow[0] == 0) { 
													$lbl = "Rest Day"; 
												} elseif($sqrow[0] == 99) { 
													$lbl = "Flexi";
												} else { $lbl = "S".$sqrow[0]; }
												
												echo "<option value='$sqrow[0]' title='$sqrow[1]' ";
												if($sqrow[0] == $dtrSched) { echo "selected"; }
												echo ">$lbl</option>";
											}
									echo "</select></td>";
									$z++;
								}
					
							$i++;
						
						echo '</tr>';
					}
				?>
			</table>
		</td>
	</tr>
 </table>
</body>
</html>