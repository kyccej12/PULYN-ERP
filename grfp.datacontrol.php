<?php
	session_start();
	require_once("handlers/_generics.php");
	$con = new _init;


	switch($_POST['mod']) {
		case "saveHeader":
			list($isE) = $con->getArray("select count(*) from grfp where grfp_no = '$_POST[grfp_no]' and branch = '$_SESSION[branchid]';");
			if($isE > 0) {
				$s = "UPDATE ignore grfp SET grfp_date= '".$con->formatDate($_POST['grfp_date'])."', emp_id = '$_POST[emp_id]', date_needed = '".$con->formatDate($_POST['date_needed'])."',payment_for = '".$con->escapeString(htmlentities($_POST['purpose']))."', remarks = '".$con->escapeString(htmlentities($_POST['remarks']))."', costcenter = '$_POST[costcenter]', payee = '".$con->escapeString(htmlentities($_POST['payee']))."',amount = '".$con->formatDigit($_POST['amount'])."', emp_name = '".$con->escapeString(htmlentities($_POST['emp_name']))."', department = '".$con->escapeString(htmlentities($_POST['dept']))."', payee_code = '$_POST[payeeid]' 
					  WHERE grfp_no = '$_POST[grfp_no]' AND branch = '$_SESSION[branchid]';";
				$grfp_no = $_POST['grfp_no'];
			} else {
				list($grfp_no) = $con->getArray("select ifnull(max(grfp_no),0)+1 from grfp where branch = '$_SESSION[branchid]';"); 
				$s = "INSERT IGNORE INTO grfp (branch,grfp_no,grfp_date,emp_id,date_needed,payment_for,remarks,costcenter,payee,amount,created_by,created_on,balance,emp_name,department,payee_code) VALUES 
						('$_SESSION[branchid]','$grfp_no','".$con->formatDate($_POST['grfp_date'])."','$_POST[emp_id]','".$con->formatDate($_POST['date_needed'])."','".$con->escapeString(htmlentities($_POST['purpose']))."','".$con->escapeString(htmlentities($_POST['remarks']))."','$_POST[costcenter]','".$con->escapeString(htmlentities($_POST['payee']))."','".$con->formatDigit($_POST['amount'])."','$_SESSION[userid]',now(),'$_POST[amount]','".$con->escapeString(htmlentities($_POST['emp_name']))."','".$con->escapeString(htmlentities($_POST['dept']))."','$_POST[payeeid]');";
				$con->trailer("General Request for Payment","Created General Request for Payment No. $_POST[grfp_no], Requestor = '$_POST[emp_id]', Requester Name = '".$con->escapeString(htmlentities($_POST['requested_by']))."', Payment For = '".$con->escapeString(htmlentities($_POST['purpose']))."', Date Needed = '".$con->formatDate($_POST['date_needed'])."', PROJ ID = '$_POST[proj_code]'");
			}
			$con->dbquery($s);
			echo $grfp_no;
		break;
		case "finalizeGRFP":
			$con->dbquery("update grfp set status = 'Finalized', updated_by = '$_SESSION[userid]', updated_on = now() where grfp_no ='$_POST[grfp_no]' and branch = '$_SESSION[branchid]';");
			$con->trailer("General Request for Payment","Finalize General Request for Payment No. $_POST[grfp_no]");
		break;
		case "reopenGRFP":
			$con->dbquery("update grfp set status = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where grfp_no ='$_POST[grfp_no]' and branch = '$_SESSION[branchid]';");
			$con->trailer("General Request for Payment","ReOpen General Request for Payment No. $_POST[grfp_no]', Reason : ".$con->escapeString(htmlentities($_POST['remarks'])));
		break;
		case "cancel":
			$con->dbquery("update grfp set status = 'Cancelled', updated_by = '$_SESSION[userid]', updated_on = now() where grfp_no = '$_POST[grfp_no]' and branch = '$_SESSION[branchid]';");
			$con->trailer("General Request for Payment","Cancel General Request for Payment No. $_POST[grfp_no]', Reason : ".$con->escapeString(htmlentities($_POST['remarks'])));
		break;
		case "getDocInfo":
			$m = $con->getArray("select a.status,if(a.status='Cancelled','Cancelled By',if(a.status='Finalized','Finalized By','Last Updated By')) as lbl, a.status,if(a.status='Cancelled','Cancelled On',if(a.status='Finalized','Finalized On','Last Updated On')) as lbl2,b.fullname as cby, date_format(created_on,'%m/%d/%Y %r') as con, c.fullname as uby, date_format(updated_on,'%m/%d/%Y %r') as uon from grfp a left join user_info b on a.created_by=b.emp_id left join user_info c on a.updated_by=c.emp_id where grfp_no = '$_POST[grfp_no]' and a.branch = '$_SESSION[branchid]';");
			if($q == "") { $q = "None "; }
			if($t == "") { $t = "None "; }
			if($z == "") { $z = "None "; }

			echo "<table width=100% cellpadding=2 cellspacing=0 style='font-size: 11px;'>
					<tr>
						<td width='30%'>Created By</td>
						<td width=5>:</td>
						<td style='padding-left:10px;'>$m[cby]</td>
					</tr>
					<tr>
						<td>Created On</td>
						<td width=5>:</td>
						<td style='padding-left:10px;'>$m[con]</td>
					</tr>
					<tr>
						<td>$m[lbl]</td>
						<td width=5>:</td>
						<td style='padding-left:10px;'>$m[uby]</td>
					</tr>
					<tr>
						<td>$m[lbl2]</td>
						<td width=5>:</td>
						<td style='padding-left:10px;'>$m[uon]</td>
					</tr>
					
				  </table>";

		break;
	}

	mysql_close($con);

?>