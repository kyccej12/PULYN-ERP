<?php
	session_start();
	include("includes/dbUSE.php");
	include("functions/dr.displayDetails.fnc.php");

	function updateHeaderAmt($dr_no) {
		list($amt) = getArray("select sum(amount) from dr_details where dr_no = '$dr_no';");
		dbquery("update ignore dr_header set amount='$amt' where dr_no = '$dr_no';");
	}

	switch($_POST['mod']) {
		case "saveHeader":
			list($isE) = getArray("select count(*) from dr_header where dr_no = '$_POST[dr_no]';");
			if($isE > 0) {
				$s = "update ignore dr_header set dr_stub_no = '$_POST[dr_stub_no]', customer = '$_POST[cid]', customer_name = '".mysql_real_escape_string($_POST['cname'])."', customer_addr = '".mysql_real_escape_string($_POST['addr'])."', dr_date = '".formatDate($_POST['dr_date'])."', remarks = '".mysql_real_escape_string($_POST['remarks'])."', updated_by = '$_SESSION[userid]', updated_on = now() where dr_no = '$_POST[dr_no]';";
			} else {
				$s = "insert ignore into dr_header (dr_no, dr_stub_no, dr_date, customer, customer_name, customer_addr, remarks, created_by, created_on) values ('$_POST[dr_no]','$_POST[dr_stub_no]','".formatDate($_POST['dr_date'])."','$_POST[cid]','".mysql_real_escape_string($_POST['cname'])."','".mysql_real_escape_string($_POST['addr'])."','".mysql_real_escape_string($_POST['remarks'])."','$_SESSION[userid]',now());";
			}
			echo $s;
			dbquery($s);
		break;

		case "insertDetail":
			list($isE) = getArray("select count(*) from dr_details where dr_no = '$_POST[dr_no]' and item_code = '$_POST[icode]';");
			if($isE > 0) {
				$s = "update ignore dr_details set qty = qty + ".formatDigit($_POST['qty']).", amount = amount + ".formatDigit($_POST['amount'])." where dr_no = '$_POST[dr_no]' and item_code = '$_POST[icode]';";
			} else {
				$s = "insert ignore into dr_details (dr_no,item_code,description,qty,unit,cost,amount) values ('$_POST[dr_no]','$_POST[icode]','".mysql_real_escape_string($_POST['desc'])."','".formatDigit($_POST['qty'])."','$_POST[unit]','".formatDigit($_POST['price'])."','".formatDigit($_POST['amount'])."');";
			}
			dbquery($s);
			echo $s;
			updateHeaderAmt($_POST['dr_no']);
			DRDETAILS($_POST['dr_no']);
		break;
		case "deleteDetails":
			dbquery("delete from dr_details where line_id = '$_POST[lid]';");
			updateHeaderAmt($_POST['dr_no']);
			DRDETAILS($_POST['dr_no']);
		break;
		case "usabQty":
			$amt = ROUND(formatDigit($_POST['price']) * formatDigit($_POST['val']),2);
			dbquery("update dr_details set qty = '".formatDigit($_POST['val'])."', amount = '$amt' where line_id = '$_POST[lid]';");
			updateHeaderAmt($_POST['dr_no']);

			list($amtGT) = getArray("select sum(amount) from dr_details where dr_no = '$_POST[dr_no]';");
			echo json_encode(array('amt1' => number_format($amt,2), 'amt2' => number_format($amtGT,2)));
		break;
		case "check4print":
			list($a) = getArray("select count(*) from dr_header where dr_no = '$_POST[dr_no]';");
			list($b) = getArray("select count(*) from dr_details where dr_no = '$_POST[dr_no]';");
			
			if($a == 0 && $b > 0) { echo "head"; }
			if($b == 0 && $a > 0) { echo "det"; }
			if($a == 0 && $b == 0) { echo "both"; }
			if($a > 0 && $b > 0) { echo "noerror"; }
		break;
		case "finalizeRR":
			dbquery("update dr_header set status = 'Finalized', updated_by = '$_SESSION[userid]', updated_on = now() where dr_no ='$_POST[dr_no]';");
		break;
		case "reopenRR":
			dbquery("update dr_header set status = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where dr_no = '$_POST[dr_no]';");
		break;
		case "cancel":
			dbquery("update dr_header set status = 'Cancelled', updated_by = '$_SESSION[userid]', updated_on = now() where dr_no = '$_POST[dr_no]';");
		break;
	}

	mysql_close($con);

?>