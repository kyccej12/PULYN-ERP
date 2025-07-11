<?php

    session_start();

    include("handlers/_generics.php");
    $con = new _init();

    list($tmpfilename) = $con->getArray("select UCASE(left(MD5(RAND()),12)) as trace_no;"); 
	
	$error = 0;

	$temp = explode(".",$_FILES["userfile"]["name"]);
	$filename =  $tmpfilename . "." . end($temp);

	$path = "hema_hl7/csv/$filename";
	$imageFileType = pathinfo($path,PATHINFO_EXTENSION);


	// Check file size
	if ($_FILES["userfile"]["size"] > 2000000) {
	    echo ">> Sorry, your file is too large.<br/>";
	    $error = 1;
	}

	// Allow certain file formats
	if($imageFileType != "csv") {
	    echo ">> Sorry, invalid file format detected.<br/>";
	    $error = 1;
	}


	if ($error == 0 ) {
	    move_uploaded_file($_FILES["userfile"]["tmp_name"],$path); 

		$file = "hema_hl7/csv/$filename";
		fopen($file, "r");
		file_get_contents($file);
		$lines = explode("\n", file_get_contents($file));
		
		/* Read Text File And Process Raw Log File */
		$i = 0; $failCount = 0; $failID = ''; $successCount = 0;
		foreach($lines as $key => $value) {
			
			$cols[$i] = explode(",", trim($value));
			
            $sampleID = trim($cols[$i][0],'"');
            $tempDate = trim($cols[$i][2],'"');
            $wbc = trim($cols[$i][11],'"');
            $rbc = trim($cols[$i][26],'"');
            $hct = trim($cols[$i][33],'"');
            $hgb = trim($cols[$i][27],'"');
            $baso = trim($cols[$i][19],'"');
            $neu = trim($cols[$i][20],'"');
            $eos = trim($cols[$i][21],'"');
            $lymph = trim($cols[$i][22],'"');
            $mon = trim($cols[$i][23],'"');
            $plt = trim($cols[$i][34],'"');
            $mcv = trim($cols[$i][28],'"');
            $mch = trim($cols[$i][29],'"');
            $mchc = trim($cols[$i][30],'"');

            $tmpDate = explode("/",$tempDate);
            $resultDate = $tmpDate[2] . "-" . str_pad($tmpDate[0],2,'0',STR_PAD_LEFT) . "-" . str_pad($tmpDate[1],2,'0',STR_PAD_LEFT);

            $lab = $con->getArray("select * from lab_samples where serialno = '$sampleID' and `code` = 'L010' and `status` = '1';");
            if($lab['so_no'] > 0) {
              
                list($isE) = $con->getArray("select count(*) from lab_cbcresult where serialno = '$sampleID';");
                if($isE == 0) {

                    // $con->dbquery("insert ignore into lab_cbcresult_temp (result_date,serialno,wbc,rbc,hemoglobin,hematocrit,neutrophils,lymphocytes,monocytes,eosinophils,basophils,platelate,mcv,mch,mchc,parsed_by,parsed_on) values ('$resultDate','$sampleID','$wbc','$rbc','$hgb','$hct','$neu','$lymph','$mon','$eos','$baso','$plt','$mcv','$mch','$mchc','$uid',now());");   
                    $con->dbquery("insert ignore into lab_cbcresult (so_no,branch,sampletype,result_date,serialno,wbc,rbc,hemoglobin,hematocrit,neutrophils,lymphocytes,monocytes,eosinophils,basophils,platelate,mcv,mch,mchc,created_by,created_on) values ('$lab[so_no]','1','1','$resultDate','$sampleID','$wbc','$rbc','$hgb','$hct','$neu','$lymph','$mon','$eos','$baso','$plt','$mcv','$mch','$mchc','$uid',now());"); 
                    $con->dbquery("update ignore lab_samples set `status` = '3', updated_by = '$uid', updated_on = now() where so_no = '$lab[so_no]' and `code` = 'L010' and serialno = '$sampleID';");
                    $successCount++;
                } else {
                    $failCount++;
                    $failID .= $sampleID . ",";
                }
            } else {
                $failCount++;
                $failID .= $sampleID . ",";
            }

            $i++;
		}


        if($successCount > 0) {
            $msg = "Successfully Processed & Imported $successCount results!";
        }
        if($failCount > 0) {
            $msg .= " The following Sample IDs were not processed as it appears it has yet been registered in the LIS System: <br/>" . trim($failID,',') . ""; 
        }

        echo $msg;

	}

?>