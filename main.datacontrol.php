<?php
	session_start();
	include("includes/dbUSE.php");
	$date = date('Y-m-d');
	
	function trailer($module,$action) {
		dbquery("insert into traillog (user_id,`timestamp`,ipaddress,module,`action`) values ('$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','$module','".mysql_real_escape_string($action)."');");
	}

	function initBackground($i) {
		if($i%2==0){ $bgC = "#ededed"; } else { $bgC = "#ffffff"; }
		return $bgC;
	}
	
	switch($_POST['mod']) {
		
		/* Asset Management */
		case "saveAsset":
			if($_POST['fid'] != "") {
				dbquery("update ignore fa_master set asset_no='$_POST[asset_no]', asset_description='".mysql_real_escape_string(htmlentities($_POST['asset_description']))."', category='$_POST[category]', serial_no='$_POST[serial_no]', vendor='$_POST[vendor]', po_no='$_POST[po_no]', date_acquired='".formatDate($_POST['po_date'])."', inv_no='$_POST[inv_no]', cv_no='$_POST[cv_no]', cv_date='".formatDate($_POST['check_date'])."', check_no='$_POST[check_no]', warranty_exp='".formatDate($_POST['warranty_exp'])."', life_span='$_POST[lifespan]', asset_acct='$_POST[asset_acct]', adeprn_acct='$_POST[adepn_acct]', deprn_acct='$_POST[depn_acct]', cost='".formatDigit($_POST['cost'])."', assigned_to='".mysql_real_escape_string(htmlentities($_POST['assigned_to']))."', date_assigned='".formatDate($_POST['date_assigned'])."', proj_code='$_POST[proj_code]', `status`='$_POST[status]', remarks='".mysql_real_escape_string(htmlentities($_POST['remarks']))."', updated_by='$_SESSION[userid]', updated_on = now(), vatable = '$_POST[amount_type]' where fid = '$_POST[fid]';");
			} else {
				dbquery("insert into fa_master (asset_no,asset_description,category,serial_no,vendor,po_no,date_acquired,inv_no,cv_no,cv_date,check_no,warranty_exp,life_span,asset_acct,adeprn_acct,deprn_acct,cost,assigned_to,date_assigned,proj_code,`status`,remarks,created_by,created_on,vatable) values ('$_POST[asset_no]','".mysql_real_escape_string(htmlentities($_POST['asset_description']))."','$_POST[category]','$_POST[serial_no]','".mysql_real_escape_string(htmlentities($_POST['vendor']))."','$_POST[po_no]','".formatDate($_POST['po_date'])."','$_POST[inv_no]','$_POST[cv_no]','".formatDate($_POST['check_date'])."','$_POST[check_no]','".formatDate($_POST['warranty_exp'])."','$_POST[lifespan]','$_POST[asset_acct]','$_POST[adepn_acct]','$_POST[depn_acct]','".formatDigit($_POST['cost'])."','".mysql_real_escape_string(htmlentities($_POST['assigned_to']))."','".formatDate($_POST['date_assigned'])."','$_POST[proj_code]','$_POST[status]','".mysql_real_escape_string(htmlentities($_POST['remarks']))."','$_SESSION[userid]',now(),'$_POST[amount_type]');");
			}
		break;

		case "checkDupAssetNo":
			if($_POST['fid'] != '') { $f1 = " and fid!='$_POST[fid]' "; }
			list($isE) = getArray("select count(*) from fa_master where asset_no = '$_POST[asset_no]' $f1;");
			if($isE > 0) { echo "DUPLICATE"; } else { echo "NODUPLICATE"; }
		break;
		
	}
	mysql_close($con);
?>