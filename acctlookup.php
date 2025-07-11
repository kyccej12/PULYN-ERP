<?php
	session_start();
	include("handlers/initDB.php");
	$con = new myDB;

	if(isset($_POST['queryString'])) {
	$r = dbquery("select concat('[',acct_code,'] ',description) as acct, acct_code from acctg_accounts where  LOCATE('$_POST[queryString]', description) > 0 and company = '$_SESSION[company]' LIMIT 10");
	$q = $con->escapeString($_REQUEST['queryString']);
		if(strlen($_POST['queryString']) > 0) {
				echo "<table width=100% cellpadding=5 cellspacing=0 onMouseOut=\"javascript: highlightTableRowVersionA(0);\">
						<tr><td width=30% class=\"gridHead\" align=left style=\"padding-left: 10px;\">Search Results For: $_POST[queryString]</td></tr>";
					while (list($acct_desc,$acct_code) = $r->fetch_array()) {
						$acctdesc = preg_replace('/(' . $q . ')/i', '<span style="font-weight:bold;">$1</span>', $acct_desc);
						if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
						echo "<tr onMouseOver=\"javascript: highlightTableRowVersionA(this, '#e6f0fa');\" bgcolor='$bgC' onclick=\"selectAcct('$acct_code','".rawurlencode($acct_desc)."');\" style=\"cursor: pointer;\" >
								<td class=\"grid\" align=left>$acctdesc</td>
							  </tr>";
						$i++;
					}
				echo "</table>";
			
		}
	}
?>