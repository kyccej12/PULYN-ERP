<?php

include("includes/dbUSEi.php");
/*
$dtf = '2016-12-24';
$dt2 = '2017-01-06';
*/

$dtf = '2017-04-21';
$dt2 = '2017-05-19';

$transaction = dbquery("SELECT `file_id`, `tmpfileid`, `shift`, `trans_date`, `payment_type`, `cc_name`, `cc_issuer`, `cc_expire`, `cc_approval_no`, `due`, `tendered`,`change`, `status`, `finalized_on`, `finalized_by`, `cancelled_on`, `cancelled_by` FROM `pos`.`trans_journal` a WHERE a.trans_date BETWEEN '$dtf' AND '$dt2' GROUP BY `tmpfileid` ORDER BY finalized_on;");

while($row=$transaction->fetch_array()){
	list($trans_id) = getArray("SELECT MAX(trans_id)+1 FROM sjpi.pos_header a;"); 
	
	
	echo "INSERT INTO sjpi.pos_header (tmpfileid,company,branch,trans_id,trans_date,shift,amount,tendered,balance,`status`,created_on,created_by) 
			VALUES 
		('$row[tmpfileid]','2','20','$trans_id','$row[trans_date]','0','$row[due]','$row[tendered]','$row[change]','$row[status]','$row[finalized_on]','$row[finalized_by]');";
	echo "<br/>";
	dbquery("INSERT ignore INTO sjpi.pos_header (tmpfileid,company,branch,trans_id,trans_date,shift,amount,tendered,balance,`status`,created_on,created_by) 
			VALUES 
		('$row[tmpfileid]','2','20','$trans_id','$row[trans_date]','0','$row[due]','$row[tendered]','$row[change]','$row[status]','$row[finalized_on]','$row[finalized_by]');");
	
	
	$trans_details = dbquery("SELECT tmpfileid,item_code,description,qty,price,amount FROM pos.temporders WHERE tmpfileid = '$row[tmpfileid]';");
	
	
	while($row_d = $trans_details->fetch_array()){
		list($rev_acct) = getArray("SELECT rev_acct FROM pos.products_master a WHERE a.item_code = '$row_d[item_code]';"); 
		echo "INSERT INTO sjpi.pos_details (tmpfileid,trans_id,item_code,description,sales_group,qty,price,disc_price,amount,uid) 
				VALUES
			  ('$row_d[tmpfileid]','$trans_id','$row_d[item_code]','$row_d[description]','$rev_acct','$row_d[qty]','$row_d[price]','0','$row_d[amount]','$row[finalized_by]');";
		
		dbquery("INSERT INTO sjpi.pos_details (tmpfileid,trans_id,item_code,description,sales_group,qty,price,disc_price,amount,uid) 
				VALUES
			  ('$row_d[tmpfileid]','$trans_id','$row_d[item_code]','$row_d[description]','$rev_acct','$row_d[qty]','$row_d[price]','0','$row_d[amount]','$row[finalized_by]');");
	
	}
	echo "<br/>#############################################################################################################################################################################";
}

?>