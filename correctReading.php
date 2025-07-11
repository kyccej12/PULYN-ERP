<?php
	include("includes/dbUSE.php");


	$q = dbquery("select trans_date, shift from shift_status order by trans_date, shift;");
	while($x = mysql_fetch_array($q)) {	
		$getRec = dbquery("SELECT shift, b.pump, a.subpump, a.initial, a.final, b.poster FROM reading_report a LEFT JOIN pumps b ON a.subpump=b.subpump WHERE shift = '$x[shift]' AND trans_date = '$x[trans_date]';");
		while($row = mysql_fetch_array($getRec)) {
			list($liters,$adue,$price) = getArray("select sum(liters), ROUND(sum(amount_due),2), price_per_liter as price from ws_slip where trans_date = '$x[trans_date]' and shift = '$x[shift]' and subpump = '$row[subpump]';");
			$final = $initial + $liters; $poster = $row['poster'];
			dbquery("update ignore reading_report set gross_sales = '$adue' where subpump = '$row[subpump]' and trans_date = '$x[trans_date]' and shift = '$x[shift]';");
			echo "update ignore reading_report set gross_sales = '$adue' where subpump = '$row[subpump]' and trans_date = '$x[trans_date]' and shift = '$x[shift]';<br/>";
		}
	}
?>