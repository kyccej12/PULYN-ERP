<?php
	session_start();
	include("../includes/dbUSE.php");

	$sw_que = dbquery("select sw_no from sw_header where sw_no != '' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]' and amount = 0;");
	//echo "select sw_no from sw_header where sw_no != '' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]' and amount = 0;";
	
	while($row_sw = mysql_fetch_array($sw_que)){
		updateSWHeaderAmt($row_sw['sw_no']);
	}
	
	
	function updateSWHeaderAmt($sw_no) {
		list($amt) = getArray("select sum(amount) from sw_details where sw_no = '$sw_no' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
		echo "update ignore sw_header set amount='$amt' where sw_no = '$sw_no' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]'; <br>";
		dbquery("update ignore sw_header set amount='$amt' where sw_no = '$sw_no' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
	}
	
?>