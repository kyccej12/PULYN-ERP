<?php
	session_start();
	include("includes/dbUSE.php");
	
	
	switch($_POST['mod']) {
		case "getHOInfo":
			$res = getArray("SELECT IFNULL(company,'') AS company, IFNULL(owner_contactno,'') AS owner_contactno, format(ROUND(43.75 * sqm,2),2) AS assoc_dues, format(ROUND(21.25 * sqm,2),2) AS phase3, sqm, vatable, '' as insurance FROM homeowners WHERE record_id = '$_POST[acct]';");
			$premium = 32.08325; $others = 4.81249; $vat = 3.86884;
	
			if($res['vatable'] = 'Y') {	$insurance = ROUND($res['sqm'] * ($premium + $others + $vat),2); } else { $insurace = ROUND($res['sqm'] * ($premium + $others),2); }
			$res['insurance'] = number_format($insurance,2);
			
			echo json_encode($res);
		
		break;
		case "computeWaterBill":
			$usage = $_POST['cur'] - $_POST['prev'];
			$stp = number_format(ROUND($usage * 30,2),2);
			
			
			if($usage <= 10) {
				$waterBill = round($usage * 60,2);
			}
			
			if($usage > 10 && $usage <= 20) {
				$excess = $usage - 10;
				$waterBill = 600 + ROUND($excess * 90,2);
			}
			
			if($usage > 20 && $usage <= 30) {
				$excess = $usage - 20;
				$waterBill = 1500 + ROUND($excess * 150,2);
			}
			
			if($usage > 30 && $usage <= 40) {
				$excess = $usage - 30;
				$waterBill = 3000 + ROUND($excess * 250,2);
			}
			
			if($usage > 40) {
				$excess = $usage - 40;
				$waterBill = 5500 + ROUND($excess * 300,2);
			}
			
			echo json_encode(array("wbill"=>number_format($waterBill,2),"stp"=>$stp));
		break;
		
		case "checkBillNo":
			list($isE) = getArray("select count(*) from billing where billingNo = '$_POST[billNo]' and recordID != '$_POST[rid]' and `status` != 'Cancelled';");
			if($isE == 0) { echo "ok"; } else { echo "notOK"; }
		break;
		
		case "saveBilling":
		
			if($_POST['acct'] != '') { list($acctName) = getArray("select concat(lname,', ',fname) from homeowners where record_id = '$_POST[acct]';"); }
			if($_POST['rid'] != '') {
				$qstring = "update billing set billingNo = '$_POST[bill_no]',acctID = '$_POST[acct]',acctName = '".mysql_real_escape_string($acctName)."',"
						."tower ='$_POST[tower]', unit = '$_POST[unit]', billingDate = '".formatDate($_POST['billing_date'])."', mobileNo = '$_POST[contact_no]', company = '$_POST[company]', add1 = '".mysql_real_escape_string($_POST['add1'])."',"
						."add2 = '".mysql_real_escape_string($_POST['add2'])."',periodFrom = '".formatDate($_POST['period_from'])."', periodTo = '".formatDate($_POST['period_to'])."', prevReading = '$_POST[previous_reading]',curReading = '$_POST[current_reading]',remarks = '".mysql_real_escape_string($_POST['remarks'])."',"
						."assocDues = '".formatDigit($_POST['assoc_dues'])."', waterBill = '".formatDigit($_POST['water_bill'])."', phase3 = '".formatDigit($_POST['phase3'])."', stpCharges = '".formatDigit($_POST['stp_charge'])."',"
						."insurance = '".formatDigit($_POST['insurance'])."', parkingDues = '".formatDigit($_POST['parking_dues'])."',otherCharges = '".formatDigit($_POST['other_charges'])."', balanceDue = '".formatDigit($_POST['total'])."',updatedBy = '$_SESSION[userid]', updatedOn=now() where recordID = '$_POST[rid]';";
				
				//echo $qstring;
				dbquery($qstring);
			} else {
				$rid = getArray("SHOW TABLE STATUS WHERE `name` = 'billing';");
				$qstring = "INSERT IGNORE INTO billing (billingNo,acctID,acctName,tower,unit,billingDate,mobileNo,company,add1,add2,periodFrom,periodTo,prevReading,curReading,remarks,assocDues,waterBill,"
						."phase3,stpCharges,insurance,parkingDues,otherCharges,balanceDue,createdBy,createdOn) values ('$_POST[bill_no]','$_POST[acct]','".mysql_real_escape_string($acctName)."',"
						."'$_POST[tower]','$_POST[unit]','".formatDate($_POST['billing_date'])."','$_POST[contact_no]','$_POST[company]','".mysql_real_escape_string($_POST['add1'])."',"
						."'".mysql_real_escape_string($_POST['add2'])."','".formatDate($_POST['period_from'])."','".formatDate($_POST['period_to'])."','$_POST[previous_reading]','$_POST[current_reading]','".mysql_real_escape_string($_POST['remarks'])."',"
						."'".formatDigit($_POST['assoc_dues'])."','".formatDigit($_POST['water_bill'])."','".formatDigit($_POST['phase3'])."','".formatDigit($_POST['stp_charge'])."',"
						."'".formatDigit($_POST['insurance'])."','".formatDigit($_POST['parking_dues'])."','".formatDigit($_POST['other_charges'])."','".formatDigit($_POST['total'])."','$_SESSION[userid]',now());";
				dbquery($qstring);
				echo $rid['Auto_increment'];
			}
		break;
		
		case "reopen":
			dbquery("update billing set `status` = 'Active' where recordID = '$_POST[rid]';");
			dbquery("delete from acctg_gl where doc_type = 'SOA' and doc_no = '$_POST[rid]';");
		break;
		
		case "postBilling":
			dbquery("update billing set `status` = 'Finalized', updatedBy = '$_SESSION[userid]', updatedOn = now() where recordID = '$_POST[rid]';");
			
			$row = getArray("select billingNo,billingDate,date_format(billingDate,'%Y') as cy, acctID,remarks,assocDues,waterBill,stpCharges,phase3,insurance,parkingDues,otherCharges,balanceDue from billing where recordID = '$_POST[rid]';");
			
			if($row['assocDues'] > 0) {
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,debit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','100301','$row[assocDues]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,credit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','400101','$row[assocDues]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
			}
			
			if($row['waterBill'] > 0) {
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,debit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','100303','$row[waterBill]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,credit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','400103','$row[waterBill]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
			}
			
			if($row['stpCharges'] > 0) {
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,debit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','100306','$row[stpCharges]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,credit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','400104','$row[stpCharges]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
			}
			
			if($row['phase3'] > 0) {
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,debit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','100302','$row[phase3]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,credit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','400102','$row[phase3]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
			}
			
			if($row['insurance'] > 0) {
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,debit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','100304','$row[insurance]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,credit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','400106','$row[insurance]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
			}
			
			if($row['parkingDues'] > 0) {
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,debit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','100313','$row[parking_dues]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,credit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','400105','$row[parking_dues]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
			}
			
			if($row['otherCharges'] > 0) {
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,debit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','100314','$row[otherCharges]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,credit,doc_remarks,posted_by,posted_on) values ('1','$row[cy]','$_POST[rid]','$row[billingDate]','SOA','$row[acctID]','1','400100','$row[otherCharges]','".mysql_real_escape_string($row['remarks'])."','$_SESSION[userid]',now());");
			}
			
		break;
		
		case "cancel":
			dbquery("update billing set `status` = 'Cancelled', cancelledBy = '$_SESSION[userid]', cancelledOn = now() where recordID = '$_POST[rid]';");
		break;
	}
	@mysql_close($con);
?>