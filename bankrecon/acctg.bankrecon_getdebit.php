<?php
	session_start();
	include("../includes/dbUSE.php");
	$date = formatDate($_POST['xdate']);
	
	$q = mysql_query("SELECT record_id AS gl_no, cy, doc_type, doc_no, date_format(doc_date,'%m/%d/%y') as doc_date, '' AS check_no, '' AS check_date, DATE_FORMAT(doc_date,'%m/%d/%y') AS ddate, debit AS db_amount,doc_type,contact_id AS contact,tmp_cleared,doc_remarks FROM acctg_gl WHERE doc_date > '2018-12-31' AND doc_date <= '$date' AND cleared='N' AND branch='1' AND acct='$_POST[acct_code]' AND debit > 0 ORDER BY doc_date asc, doc_type, doc_no ASC;");
	echo "<table width=100% cellpadding=0 cellspacing=0>";
	while ($row = mysql_fetch_array($q)) {
		list($payee) = getArray("select tradename from contact_info where file_id = '$row[contact]';");
		echo "<tr valign=top $bgC>
				<td class=grid width=10%>" . $row['doc_type'].'-'.$row['doc_no'] . "</td>
				<td class=grid width=10%>" . $row['doc_date'] . "</td>
				<td class=grid width=30%>" . $payee . "&nbsp;</td>
				<td class=grid width=30%>" . $row['doc_remarks'] . "&nbsp;</td>
				<td class=grid width=10% align=right style=\"padding-right: 20px;\">" . number_format($row['db_amount'],2) . "</td>
				<td class=grid><input type=checkbox id=\"glno_$row[gl_no]\" value=\"$row[gl_no]\" onclick=\"toggle_me(this.id,this.value);\" ";
				if($row['tmp_cleared'] == "Y") { echo "checked"; }
				echo "/></td>
			</tr>"; 
			    $i++; $payee = "";
	}
	echo "</table>";
	mysql_close($con);
?>