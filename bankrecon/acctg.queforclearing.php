<?php
	session_start();
	include("../includes/dbUSE.php");
	
	dbquery("update acctg_gl set tmp_cleared = '$_REQUEST[push]' where record_id='$_REQUEST[xval]';");
	$date = formatDate($_POST['xdate']);
	
	$cleared_d8 = getArray("select distinct cleared_on from acctg_gl where cleared_on < '$date' and branch = '1' order by cleared_on desc limit 1;");
	if($cleared_d8[0] != '0000-00-00') { $prev_date = $cleared_d8[0]; } else { $prev_date = $date; }
	$x = getArray("select ifnull(sum(debit-credit),0) from acctg_gl where cleared_on <= '$prev_date' and doc_branch='1' and acct='$_POST[acct_code]' and cleared='Y' and branch = '1' group by acct;");
	
	$cbalance = getArray("select ifnull(ROUND(sum(debit-credit),2),0), ifnull(abs(ROUND(sum(debit-credit),2)),0) from acctg_gl where (tmp_cleared='Y' or cleared='Y') and acct='$_POST[acct_code]' and branch = '1';");
	$c_cleared = getArray("select count(*) from acctg_gl where tmp_cleared='Y' and credit > 0 and acct='$_POST[acct_code]' and branch = '1';");
	$d_cleared = getArray("select count(*) from acctg_gl where tmp_cleared='Y' and debit > 0 and acct='$_POST[acct_code]' and branch = '1';");
		
	echo json_encode(array("bbalance"=>"0$x[0]", "balance_beginning" => number_format($x[0],2), "cbalance"=>$cbalance[0], "clearedbalance"=>number_format($cbalance[0],2),"c_cleared"=>$c_cleared[0],"d_cleared"=>$d_cleared[0],"abscbalance"=>ROUND($cbalance[1],2)));
	mysql_close($con);	
?>