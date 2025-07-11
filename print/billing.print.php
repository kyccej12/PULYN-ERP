  <?php
	session_start();
	//ini_set("display_errors","On");
	//ini_set("error_reporint","E_ALL");
	include("../lib/mpdf6/mpdf.php");
	include("../includes/dbUSE.php");


/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	
	$res = getArray("select *,date_format(billingDate,'%m/%d/%Y') as billDate, date_format(periodFrom,'%m/%d/%y') as pfrom, date_format(periodTo,'%m/%d/%y') as p2, date_format(date_add(billingDate,INTERVAL 30 DAY),'%m/%d/%Y') as dueDate from billing where recordID = '$_REQUEST[docNo]';");
	list($acctType) = getArray("select record_type from homeowners where record_id = '$res[acctID]';");
	
	switch($acctType) {
		case "ONL":
			list($tennant) = getArray("select concat('(',lpad(record_id,3,'0'),') ',IF(fname = '',lname,CONCAT(lname,', ',fname))) AS `name` from homeowners where tower = '$res[tower]' and tower_unit = '$res[unit]' and record_type = 'T' order by contract_start desc limit 1;");
			$owner = '('.str_pad($res['acctID'],3,'0',STR_PAD_LEFT) . ') ' . $res['acctName'];
		break;
		case "T":
			list($owner) = getArray("select concat('(',lpad(record_id,3,'0'),') ',IF(fname = '',lname,CONCAT(lname,', ',fname))) AS `name` from homeowners where tower = '$res[tower]' and tower_unit = '$res[unit]' and record_type = 'ONL' order by contract_start desc limit 1;");
			$tennant = '('.str_pad($res['acctID'],3,'0',STR_PAD_LEFT) . ') ' . $res['acctName'];
		break;
		default:
			$owner = '(' . str_pad($res['acctID'],3,'0',STR_PAD_LEFT) . ') ' . $res['acctName'];
			$tennant = '';
		break;
	}
	
	if($acctType == 'ONL') {
		
	}
	
	
	
/* END OF SQL QUERIES */

$mpdf=new mPDF('win-1252','A4','','',30,30,90,10,45,10);
$mpdf->use_embeddedfonts_1252 = true;    // false is default
$mpdf->SetProtection(array('print'));
$mpdf->SetAuthor("PORT80 Solutions");
$mpdf->SetDisplayMode(60);

$html = '
<html>
<head>
<style>
	body {
		font-family: arial;
		font-size: 10pt;
	 }
	td { vertical-align: top; }

	</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
	<table width=100% cellpadding="2">
		<tr>
			<td width=80% style="padding-left: 50px;">'.$owner.'</td>
			<td width=20% style="padding-left: 20px;">'.$res['billDate'].'</td>
		</tr>
		<tr>
			<td width=80% style="padding-left: 50px;">'.$tennant.'</td>
			<td width=20% style="padding-left: 20px;">'.$res['dueDate'].'</td>
		</tr>
		<tr>
			<td width=80% style="padding-left: 50px;">Tower '.$res['tower'].'-'.$res['unit'].'</td>
			<td width=20% style="padding-left: 20px;">30 Days</td>
		</tr>
	</table>
</htmlpageheader>
<htmlpagefooter name="myfooter"></htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table width="100%" style="border-collapse: collapse;" cellpadding="2">';
	/* Association Due */
	if($res['assocDues'] > 0) {
		$html .= '<tr><td width=50% style="padding-left: 20px";>&raquo; Association Dues</td><td align=right style="padding-right: 10px;"><b>&#8369;'.number_format($res['assocDues'],2).'</b></td></tr>';
	}
	
	/* Water Bill */
	if($res['waterBill'] > 0) {
		$html .='<tr><td width=50% style="padding-left: 20px;">&raquo; Water Bill ('.$res[pfrom].' - '.$res[p2] .')</td><td align=right style="padding-right: 10px;"><b>&#8369;'.number_format($res['waterBill'],2).'</b></td></tr>
				<tr><td width=100% colspan=2 style="padding-left: 40px; font-size: 9pt;">Current Reading: <b>'.$res[curReading].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Prev. Reading: '.$res[prevReading].'</b></td></tr>
				<tr><td width=100% colspan=2 style="padding-left: 40px; font-size: 9pt;">Total Consumption in Cubic Meters: <b>'. ($res[curReading] - $res[prevReading]) .'CBM</b></td></tr>
				<tr><td width=100% colspan=2 style="padding-left: 40px; font-size: 9pt;"><br/><b>Water Consumption Breakdown: </b></td></tr>';
				
				$usage = $res['curReading'] - $res['prevReading'];  
				
				if($usage <= 10) {
					$first = round($usage * 60,2);			
					$html .= '<tr><td width=100% colspan=2 style="padding-left: 50px; font-size: 9pt;">1 - ' . $usage .' CBM: ('.$usage.' CBM x P60.00) = <b>&#8369;'. number_format($first,2) .'</b></td></tr>';
				} else {
					if($usage > 10) {
						$html .= '<tr><td width=100% colspan=2 style="padding-left: 50px; font-size: 9pt;">1 - 10 CBM: (10 CBM x &#8369;60.00) &nbsp;&nbsp;= <b>&#8369;600.00</b></td></tr>';
					}
				}
				
				if($usage > 10 && $usage <= 20) {
					$xusage = $usage - 10;
					$second = round($xusage * 60,2);			
					$html .= '<tr><td width=100% colspan=2 style="padding-left: 50px; font-size: 9pt;">11 - ' . $usage .' CBM: ('.$xusage.' CBM x &#8369;90.00) = <b>&#8369;'. number_format($second,2) .'</b></td></tr>';
				} else {
					if($usage > 20) {
						$html .= '<tr><td width=100% colspan=2 style="padding-left: 50px; font-size: 9pt;">11 - 20 CBM: (10 CBM x &#8369;90.00) = <b>&#8369;900.00</b></td></tr>';
					}
				}
				
				if($usage > 20 && $usage <= 30) {
					$xusage = $usage - 20;
					$third = round($xusage * 150,2);			
					$html .= '<tr><td width=100% colspan=2 style="padding-left: 50px; font-size: 9pt;">21 - ' . $usage .' CBM: ('.$xusage.' CBM x &#8369;150.00) = <b>&#8369;'. number_format($third,2) .'</b></td></tr>';
				} else {
					if($usage > 30) {
						$html .= '<tr><td width=100% colspan=2 style="padding-left: 50px; font-size: 9pt;">21 - 30 CBM: (10 CBM x &#8369;150.00) = <b>&#8369;1,500.00</b></td></tr>';
					}
				}
				
				if($usage > 30 && $usage <= 40) {
					$xusage = $usage - 30;
					$fourth = round($xusage * 250,2);			
					$html .= '<tr><td width=100% colspan=2 style="padding-left: 50px; font-size: 9pt;">31 - ' . $usage .' CBM: ('.$xusage.' CBM x &#8369;250.00) = <b>&#8369;'. number_format($fourth,2) .'</b></td></tr>';
				} else {
					if($usage > 40) {
						$html .= '<tr><td width=100% colspan=2 style="padding-left: 50px; font-size: 9pt;">31 - 40 CBM: (10 CBM x &#8369;250.00) = <b>&#8369;2,500.00</b></td></tr>';
					}
				}

				
				if($usage > 40) {
					$xusage = $usage - 40;
					$fourth = round($xusage * 300,2);			
					$html .= '<tr><td width=100% colspan=2 style="padding-left: 50px; font-size: 9pt;">41 - ' . $usage .' CBM: ('.$xusage.' CBM x &#8369;300.00) = <b>&#8369;'. number_format($fourth,2) .'</b></td></tr>';
				}
				  
	}
	
	if($res['stpCharges'] > 0) {
		$html .= '<tr><td width=50% style="padding-left: 20px";>&raquo; STP</td><td align=right style="padding-right: 10px;"><b>&#8369;'.number_format($res['stpCharges'],2).'</b></td></tr>';
	}
	
	if($res['phase3'] > 0) {
		$html .= '<tr><td width=50% style="padding-left: 20px";>&raquo; Phase 3</td><td align=right style="padding-right: 10px;"><b>&#8369;'.number_format($res['phase3'],2).'</b></td></tr>';
	}
	
	if($res['insurance'] > 0) {
		$html .= '<tr><td width=50% style="padding-left: 20px";>&raquo; Insurance</td><td align=right style="padding-right: 10px;"><b>&#8369;'.number_format($res['insurance'],2).'</b></td></tr>';
	}
	
	if($res['parkingDues'] > 0) {
		$html .= '<tr><td width=50% style="padding-left: 20px";>&raquo; Parking Dues</td><td align=right style="padding-right: 10px;"><b>&#8369;'.number_format($res['parkingDues'],2).'</b></td></tr>';
	}
	
	if($res['otherCharges'] > 0) {
		$html .= '<tr><td width=50% style="padding-left: 20px";>&raquo; otherCharges</td><td align=right style="padding-right: 10px;"><b>&#8369;'.number_format($res['otherCharges'],2).'</b></td></tr>';
	}
	$html .= '<tr><td colspan=2 align=right style="padding-right: 10px;"><br/><br/><br/><b>TOTAL BALANCE DUE &raquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&#8369;'.number_format($res['balanceDue'],2).'</b></td></tr>';
	

$html .= '</table>
</body>
</html>
';
$html = html_entity_decode($html);
$mpdf->WriteHTML($html);
$mpdf->Output(); 
exit;

mysql_close($con);
?>