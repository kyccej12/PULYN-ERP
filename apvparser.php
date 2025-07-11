<?php
	session_start();
	include("includes/dbUSE.php");

	$a = dbquery("select branch, apv_no from fpfc.apv_header where amount = 0 and `status` = 'Posted';");
	while(list($branch,$apv_no) = mysql_fetch_array($a)) {
		list($ewt) = getArray("select sum(credit) from fpfc.apv_details where apv_no = '$apv_no' and acct = '2012' and branch = '$branch';");
		list($input) = getArray("select sum(debit) from fpfc.apv_details where apv_no = '$apv_no' and acct = '1401' and branch = '$branch';");
		list($ap) = getArray("select sum(credit) from fpfc.apv_details where apv_no = '$apv_no' and acct in ('2001','2002') and branch = '$branch';");
		
		dbquery("update ignore fpfc.apv_header set amount = 0$ap, vat = 0$input, ewt_amount = 0$ewt, balance = 0$ap-applied_amount where apv_no = '$apv_no' and branch = '$branch';");

		echo "update ignore fpfc.apv_header set amount = 0$ap, vat = 0$input, ewt_amount = 0$ewt, balance = 0$ap-applied_amount where apv_no = '$apv_no' and branch = '$branch';<br/>";

	}
?>