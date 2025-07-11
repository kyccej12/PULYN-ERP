<?php
	session_start();
	require_once "../handlers/_generics.php";
	
	$mydb = new _init;
	
	$rid = $_POST['record_id'];
	$old_idno = $_POST['old_idno'];
	$emp_idno = $_POST['emp_idno'];
	$emp_type = $_POST['emp_type'];
	$emp_lname = $_POST['emp_lname'];
	$emp_fname = $_POST['emp_fname'];
	$emp_mname = $_POST['emp_mname'];
	$contact_person = $_POST['contact_person'];
	$contact_nos = $_POST['contact_nos'];
	
	$emp_sex = $_POST['emp_sex'];
	$emp_bday = $mydb->formatDate($_POST['emp_bday']);
	$blood_type = urldecode($_POST['emp_bloodtype']);
	$emp_cstat = $_POST['emp_cstat'];
	$emp_add1 = $_POST['emp_add1'];
	$emp_add2 = $_POST['emp_add2'];
	$emp_religion = $_POST['emp_religion'];
	$nationality = $_POST['emp_nationality'];
	$email_add = $_POST['email_add'];
	$tel_no = $_POST['tel_no'];
	$bio_id = $_POST['bio_id'];
	
	$date_hired = $mydb->formatDate($_POST['date_hired']); 
	$emp_desg = $_POST['emp_desg'];
	$emp_dept = $_POST['emp_dept'];
	$emp_ptype = $_POST['emp_ptype'];
	$emp_factor = $_POST['emp_factor'];
	$emp_stat = $_POST['emp_stat'];
	$emp_date_ret = $mydb->formatDate($_POST['date_ret']);
	$emp_flex = $_POST['emp_flex'];
	$emp_noon = $_POST['emp_noon_swipe'];
	$emp_batch = $_POST['emp_batch'];
	$emp_shift = $_POST['emp_shift'];
	$emp_area = $_POST['emp_area'];
	$emp_tin = $_POST['emp_tin'];
	$emp_sss_no = $_POST['emp_sss_no'];
	$emp_hdmf_no = $_POST['emp_hdmf_no'];
	$emp_phealth_no = $_POST['emp_phealth_no'];
	$w_sss = $_POST['emp_sss'];
	$w_ph = $_POST['emp_ph'];
	$w_hdmf = $_POST['emp_hdmf'];
	
	$emp_vl = $_POST['emp_vl'];
	$emp_sl = $_POST['emp_sl'];
	$emp_rate = $mydb->formatDigit($_POST['emp_rate']);
	$cola = $mydb->formatDigit($_POST['emp_cola']);
	$emp_allw = $mydb->formatDigit($_POST['emp_allw']);
	$emp_allwtype = $_POST['emp_allwtype'];
	$emp_allw_ntx = $mydb->formatDigit($_POST['emp_allw_ntx']);
	$emp_transpo = $mydb->formatDigit($_POST['emp_transpo']);
	$emp_meal = $mydb->formatDigit($_POST['emp_meal']);
	$emp_wtax = $_POST['emp_wtax'];
	$emp_tax = $mydb->formatDigit($_POST['emp_tax']);
	$emp_hdmf_ee = $mydb->formatDigit($_POST['emp_hdmf_ee']);
	$emp_coop = $mydb->formatDigit($_POST['emp_coop']);
	$emp_retirement = $mydb->formatDigit($_POST['emp_retirement']); 
	$emp_atmbank = $_POST['emp_atmbank'];
	$emp_bank = $_POST['emp_bank'];
	$emp_ewt = $_POST['emp_ewt'];
	
	$emp_remarks = $mydb->escapeString($_POST['emp_remarks']);
	
	if($rid == "") {
		$_xsearch = $mydb->getArray("select count(*) from omdcpayroll.emp_masterfile where emp_id = '$emp_idno';");
		if($_xsearch[0] == 0) {
			$_namesearch = $mydb->getArray("select count(*) as found from omdcpayroll.emp_masterfile where lname = '$emp_lname' and fname = '$emp_fname' and mname = '$emp_mname';");
			if($_namesearch[0] == 0) { 
			   
			   $query = "INSERT IGNORE INTO omdcpayroll.emp_masterfile (EMP_TYPE,EMP_ID,BIO_ID,LNAME,FNAME,MNAME,ADDRESS1,ADDRESS2,GENDER,BIRTHDATE,EMAIL_ADD,TEL_NO,BLOOD_TYPE,
							CIVIL_STATUS,CONTACT_PERSON,CONTACT_NOS,RELIGION,NATIONALITY,DATE_HIRED,DATE_RET,DESG,DEPT,PAYROLL_TYPE,PAYROLL_FACTOR,EMPLOYMENT_STATUS,FLEX_TIME,AUTO_NOON,BASIC_RATE,COLA,ALLOWANCE,
							ALLOWANCE_TYPE,NONTAX_ALLOWANCE,TRANSPO_ALLOWANCE,MEAL_ALLOWANCE,RETIREMENT_PLAN,COOP_PREMIUM,HDMF_PREMIUM,ACCT_NO,ATM_BANK,W_TAX,EMP_TAX,VL_CREDIT,SL_CREDIT,TIN_NO,SSS_NO,HDMF_NO,
							PHEALTH_NO,W_PHILHEALTH,W_SSS,W_HDMF,`SHIFT`,`AREA`,PAYROLL_BATCH,REMARKS,CREATED_BY,CREATED_ON) 
						VALUES ('$emp_type','$emp_idno','$bio_id','".htmlentities($emp_lname)."','".htmlentities($emp_fname)."','".htmlentities($emp_mname)."','".$mydb->escapeString(htmlentities($emp_add1))."',
						    '".$mydb->escapeString(htmlentities($emp_add2))."','$emp_sex','$emp_bday','$email_add','$tel_no','$blood_type','$emp_cstat','".htmlentities($contact_person)."','$contact_nos','$emp_religion',
							'$nationality','$date_hired','$emp_date_ret','$emp_desg','$emp_dept','$emp_ptype','$emp_factor','$emp_stat','$emp_flex','$emp_noon','$emp_rate','$cola','$emp_allw','$emp_allwtype','$emp_allw_ntx',
							'$emp_transpo','$emp_meal','$emp_retirement','$emp_coop','$emp_hdmf_ee','$emp_bank','$emp_atmbank','$emp_wtax','$emp_tax','$emp_vl','$emp_sl','$emp_tin','$emp_sss_no','$emp_hdmf_no','$emp_phealth_no',
							'$w_ph','$w_sss','$w_hdmf','$emp_shift','$emp_area','$emp_batch','$emp_remarks','$_SESSION[userid]',now());
						";
				echo $query;
				$mydb->dbquery($query);
				echo 1;
				
			} else { echo 2; }
		} else { echo 3; }
	} else {
		//Check if ID NO. was changed
		if ($old_idno != $emp_idno) { $_xsearch =  $mydb->getArray("select count(*) from omdcpayroll.emp_masterfile where emp_id='$emp_idno';"); } else { $_xsearch[0] == 0; }
		if($_xsearch[0] == 0) {
			
			$query = "UPDATE IGNORE omdcpayroll.emp_masterfile SET EMP_TYPE = '$emp_type',EMP_ID = '$emp_idno',BIO_ID = '$bio_id',LNAME = '".htmlentities($emp_lname)."',FNAME = '".htmlentities($emp_fname)."',
							MNAME = '".htmlentities($emp_mname)."',ADDRESS1 = '".$mydb->escapeString(htmlentities($emp_add1))."',ADDRESS2 = '".$mydb->escapeString(htmlentities($emp_add2))."',GENDER = '$emp_sex',
							BIRTHDATE = '$emp_bday',EMAIL_ADD = '$email_add',TEL_NO = '$tel_no',BLOOD_TYPE = '$blood_type',CIVIL_STATUS = '$emp_cstat',CONTACT_PERSON = '".htmlentities($contact_person)."',
							CONTACT_NOS = '$contact_nos',RELIGION = '$emp_religion',NATIONALITY = '$nationality',DATE_HIRED = '$date_hired',DATE_RET = '$emp_date_ret',DESG = '$emp_desg',DEPT = '$emp_dept',
							PAYROLL_TYPE = '$emp_ptype',PAYROLL_FACTOR='$emp_factor',EMPLOYMENT_STATUS = '$emp_stat',FLEX_TIME = '$emp_flex',AUTO_NOON = '$emp_noon',BASIC_RATE = '$emp_rate',COLA = '$cola',ALLOWANCE = '$emp_allw',ALLOWANCE_TYPE = '$emp_allwtype',
							NONTAX_ALLOWANCE = '$emp_allw_ntx',TRANSPO_ALLOWANCE = '$emp_transpo',MEAL_ALLOWANCE = '$emp_meal',RETIREMENT_PLAN = '$emp_retirement',COOP_PREMIUM = '$emp_coop',HDMF_PREMIUM = '$emp_hdmf_ee',
							ACCT_NO = '$emp_bank',ATM_BANK = '$emp_atmbank',W_TAX = '$emp_wtax',EMP_TAX='$emp_tax',VL_CREDIT = '$emp_vl',SL_CREDIT = '$emp_sl',TIN_NO = '$emp_tin',SSS_NO = '$emp_sss_no',HDMF_NO = '$emp_hdmf_no',
							PHEALTH_NO = '$emp_phealth_no',W_PHILHEALTH = '$w_ph',W_SSS = '$w_sss',W_HDMF = '$w_hdmf',`SHIFT` = '$emp_shift',`AREA` = '$emp_area',PAYROLL_BATCH='$emp_batch', `REMARKS` = '$emp_remarks', UPDATED_BY ='$_SESSION[userid]' ,UPDATED_ON = now() WHERE RECORD_ID = '$rid'
						";
			echo $query;
			$mydb->dbquery($query);
			echo 5; //Prompt User that Employee Record Successfully updated!
		} else { echo 4; } //If updated ID No. already exist, prompt user that the ID No. was already in used!
	}
?>