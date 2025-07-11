<?php
	ini_set("display_errors","On");
	include("../handlers/_generics.php");
	ini_set("memory_limit","2056M");
	ini_set("max_execution_time",0);
	
	
	$con = new _init();

	list($tmpfilename) = $con->getArray("select UCASE(left(MD5(RAND()),12)) as trace_no;"); 
	
	
	/* FOR OUTDATED PHP LIBRARY */
	if(!function_exists('str_getcsv')) {
		function str_getcsv($input, $delimiter = ',', $enclosure = '"') {

			if( ! preg_match("/[$enclosure]/", $input) ) {
			  return (array)preg_replace(array("/^\\s*/", "/\\s*$/"), '', explode($delimiter, $input));
			}

			$token = "##"; $token2 = "::";
			//alternate tokens "\034\034", "\035\035", "%%";
			$t1 = preg_replace(array("/\\\[$enclosure]/", "/$enclosure{2}/",
				 "/[$enclosure]\\s*[$delimiter]\\s*[$enclosure]\\s*/", "/\\s*[$enclosure]\\s*/"),
				 array($token2, $token2, $token, $token), trim(trim(trim($input), $enclosure)));

			$a = explode($token, $t1);
			foreach($a as $k=>$v) {
				if ( preg_match("/^{$delimiter}/", $v) || preg_match("/{$delimiter}$/", $v) ) {
					$a[$k] = trim($v, $delimiter); $a[$k] = preg_replace("/$delimiter/", "$token", $a[$k]); }
			}
			$a = explode($token, implode($token, $a));
			return (array)preg_replace(array("/^\\s/", "/\\s$/", "/$token2/"), array('', '', $enclosure), $a);

		}
	}
	/* END */
	
	
	$error = 0;

	$temp = explode(".",$_FILES["uploadedfile"]["name"]);
	$filename =  $tmpfilename . "." . end($temp);
	$path = "temp/$filename";
	$imageFileType = pathinfo($path,PATHINFO_EXTENSION);


	// Check file size
	if ($_FILES["uploadedfile"]["size"] > 2000000) {
	    echo ">> Sorry, your file is too large.<br/>";
	    $error = 1;
	}

	// Allow certain file formats
	if($imageFileType != "CSV" && $imageFileType != "csv") {
	    echo ">> Sorry, invalid file format detected.<br/>";
	    $error = 1;
	}

	if ($error == 0 ) {
	    move_uploaded_file($_FILES["uploadedfile"]["tmp_name"],$path); 
		$file = "temp/$filename";
		$handle = fopen($file, "r");
		$read = file_get_contents($file);
		$lines = explode("\n", $read);
		
		$uploaded=0; $fail=0;
		/* Read Text File And Process Raw Log File */
		foreach($lines as $key => $value){
			$data = str_getcsv($value);
			if($data[0]  != '') {
				if($data[1] != '') {
					list($bdoAcct) = $con->getArray("select trim(leading '0' from '$data[1]');");
					list($area) = $con->getArray("SELECT `AREA` FROM omdcpayroll.emp_masterfile WHERE trim(LEADING '0' from ACCT_NO) = '$bdoAcct';");
				} else {
					$einfo = explode(',',$data[0]);
					list($area) = $con->getArray("select `AREA` from omdcpayroll.emp_masterfile where lname like '%$einfo[0]%' and fname like '%".trim($einfo[1])."%';");
				}	
				$txtstring = "INSERT IGNORE INTO omdcpayroll.thirteenth_month (`name`,bank,amount,`year`,`area`) VALUES ('$data[0]','".str_pad($data[1],12,'0',STR_PAD_LEFT)."','".$con->formatDigit($data[2])."','$_POST[fiscal_year]','$area');";
				$con->dbquery($txtstring);
			}
		}
	}
	
	if($error == 0) { echo "THIRTEENTH MONTH FILE SUCCESSFULLY UPLOADED...."; }
	
?>