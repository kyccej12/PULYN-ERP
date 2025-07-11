<?php
	session_start();
	include("../includes/dbUSE.php");
	$date = formatDate($_POST['xdate']);
	
	$cleared_d8 = mysql_fetch_array(mysql_query("select distinct cleared_on from acctg_gl where cleared_on < '$date' and branch = '1' order by cleared_on desc limit 1;"));
	if($cleared_d8[0] != '0000-00-00') { $prev_date = $cleared_d8[0]; } else { $prev_date = $date; }
	$x = mysql_fetch_array(mysql_query("select sum(debit-credit) from acctg_gl where cleared_on <= '$prev_date' and acct='$_POST[acct_code]' and cleared='Y' and branch = '1' group by acct;"));
	
	$cbalance = mysql_fetch_array(mysql_query("select ROUND(sum(debit-credit),2),abs(ROUND(sum(debit-credit),2)) from acctg_gl where tmp_cleared='Y' and acct='$_POST[acct_code]' and branch = '1';"));
	$c_cleared = mysql_fetch_array(mysql_query("select count(*) from acctg_gl where tmp_cleared='Y' and credit > 0 and acct='$_POST[acct_code]' and branch = '1';"));
	$d_cleared = mysql_fetch_array(mysql_query("select count(*) from acctg_gl where tmp_cleared='Y' and debit > 0 and acct='$_POST[acct_code]' and branch = '1';"));
	
	echo json_encode(array("bbalance"=>"0$x[0]", "balance_beginning" => number_format($x[0],2), "cbalance"=>"0$cbalance[0]", "abscbalance"=>"0$cbalance[1]", "clearedbalance"=>number_format($cbalance[0],2),"c_cleared"=>$c_cleared[0],"d_cleared"=>$d_cleared[0]));
	mysql_close($con);
?>