<?php
	session_start();
	include("includes/dbUSE.php");

	switch($_POST['mod']) {
		case "reloadDays":
				$opt.="<option value=''> SELECT </option>";
			$qry = dbquery("SELECT DAY(LAST_DAY('2016-10-01')) AS xval,'01-10' AS mask UNION ALL 
							SELECT DAY(LAST_DAY('2016-10-01')) AS xval,'11-20' AS mask UNION ALL 
							SELECT DAY(LAST_DAY('".$_POST['year']."-".$_POST['month']."-01')) AS xval, CONCAT('21-',DAY(LAST_DAY('".$_POST['year']."-".$_POST['month']."-01'))) AS mask
							;");
			while($row=mysql_fetch_array($qry)){
				$opt .= "<option value='$row[mask]'> $row[mask] </option>";
			}
			echo $opt;
		break;

		case "laodDataTable":
			list($date1,$date2) = explode("-",$_POST['date']);
			$emplist = dbquery("select id_no, concat(lname,', ',fname) as emp from hris.e_master where (id_no != '' or id_no is not null) and company = '$_SESSION[company]' and filestatus = 'Active' AND department = '$_POST[dept]' order by lname;");
			
			$table = "<table width='100%' style='font-size:9pt;border-collapse:collapse' >";
			$table.="<tr> <td width='185px'>NAME</td>";
				for($xday = $date1;$xday<=$date2;$xday++){
					list($dayname,$maskdate) = mysql_fetch_array(mysql_query("SELECT DAYNAME('$_POST[year]-$_POST[month]-$xday') AS day_name,DATE_FORMAT('$_POST[year]-$_POST[month]-$xday','%m/%d/%Y') AS mask_date;"));
					$table.="<td align=center> ($dayname) </br> $maskdate </td>";
				}
			$table.="</tr>";
			$table.= "</table>";

			
			$table .= "<table width='100%' style='font-size:9pt;border-collapse:collapse' >";
			//HEAD
			/*	$table.="<tr> <td width='150px'>NAME</td>";
					for($xday = $date1;$xday<=$date2;$xday++){
						list($dayname,$maskdate) = mysql_fetch_array(mysql_query("SELECT DAYNAME('$_POST[year]-$_POST[month]-$xday') AS day_name,DATE_FORMAT('$_POST[year]-$_POST[month]-$xday','%m/%d/%Y') AS mask_date;"));
						$table.="<td align=center> ($dayname) </br> $maskdate </td>";
					}
				$table.="</tr>";*/
			//BODY
			$bg=1;
			while($emp_row = mysql_fetch_array($emplist)){
				if($bg%2==0){ $bg_color = "#ffffff";	}else{	$bg_color = "#d7d7d7"; }
				$table .= "<tr style='font-size:8pt;background-color:$bg_color' > <td width='185px'> $emp_row[emp] </td>";
					for($xday = $date1;$xday<=$date2;$xday++){
						$opt_sched = mysql_query("SELECT a.sched,a.sched_desc FROM options_sched a;");

						$table .="<td align=center> <select style='font-size:7pt;width:78px' onchange='savePlotSched(this.value,$emp_row[id_no],\"$_POST[year]-".str_pad($_POST['month'],2,'0',STR_PAD_LEFT)."-$xday\")' >
											<option value='' > -SELECT- </option>";
								list($sc) = getArray("SELECT sched FROM hris.e_schedule WHERE `date` = '$_POST[year]-".str_pad($_POST['month'],2,'0',STR_PAD_LEFT)."-".str_pad($xday,2,'0',STR_PAD_LEFT)."' AND id_no = '$emp_row[id_no]';");
								while($sched = mysql_fetch_array($opt_sched)){
									if($sched['sched']==$sc){ $s = 'selected';	}else{	$s = ''; }
									$table .="<option value=$sched[sched] $s> $sched[sched_desc] </option>";
								}
						$table.="</select>  </td>";
					}

				$table .= "</tr>";
			$bg++;
			}

			$table.= "</table>";

			echo $table; 
		break;
		
		case 'savePlot':
		list($flag) = getArray("SELECT COUNT(line_id) FROM hris.e_schedule a WHERE a.date = '$_POST[idate]' AND a.id_no = '$_POST[id_no]';");
		echo "SELECT COUNT(line_id) FROM hris.e_schedule a WHERE a.date = '$_POST[idate]' AND a.id_no = '$_POST[id_no]';";
		if($flag>0){
			echo "UPDATE hris.e_schedule a SET a.sched = '$_POST[ivalue]' WHERE a.date = '$_POST[idate]' AND a.id_no = '$_POST[id_no]';";
			dbquery("UPDATE hris.e_schedule a SET a.sched = '$_POST[ivalue]' WHERE a.date = '$_POST[idate]' AND a.id_no = '$_POST[id_no]';");
		}else{
			echo "INSERT IGNORE INTO hris.e_schedule (sched,`date`,id_no) VALUES ('$_POST[ivalue]','$_POST[idate]','$_POST[id_no]');";
			dbquery("INSERT IGNORE INTO hris.e_schedule (sched,`date`,id_no) VALUES ('$_POST[ivalue]','$_POST[idate]','$_POST[id_no]');");
		}
		break;
	}

	mysql_close($con);

?>