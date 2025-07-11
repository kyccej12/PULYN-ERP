<?php
	set_time_limit(0);
	session_start();
    include("../includes/dbUSE.php");

	if($_SESSION['company'] == "1") { $vatAcct = '2204'; } else { $vatAcct = '2202'; }
	
	list($dtf,$dt2,$end) = getArray("SELECT '$_REQUEST[year]-$_REQUEST[month]-01',LAST_DAY('$_REQUEST[year]-$_REQUEST[month]-01'), date_format(LAST_DAY('$_REQUEST[year]-$_REQUEST[month]-01'),'%m/%d/%Y');");
	$d = dbquery("SELECT LEFT(REPLACE(b.tin_no,'-',''),9) AS tin_no,b.tradename,'' AS lname, '' AS fname,'' AS mname, REPLACE(address,'\r\n',' ') AS address ,  REPLACE(billing_address,'\r\n',' ') AS billing_address,0 as h,IF(b.vatable='N',SUM(ROUND(debit-credit,2)*-1),0) AS zrated,IF(b.vatable='Y',SUM(ROUND(debit-credit,2)),0) vatable, ROUND(IF(b.vatable='Y',SUM(ROUND(debit-credit,2)),0) * 0.12,2) vat FROM $dbase.acctg_gl a INNER JOIN contact_info b ON a.contact_id =  b.file_id WHERE a.doc_date BETWEEN '$dtf' AND '$dt2' AND a.acct = '$vatAcct' GROUP BY a.contact_id;");
	$own_tin = '123456789';

	$owner = array('tin' => '000070182',
					'taxPayerClass' => 'Non-Individual' ,
					'registeredName' => '"SENIOR SAN JOSE FRANCHISING CORPORATION"',
					'taxPayerLname' => '',
					'taxPayerFname' => '',
					'taxPayerMname' => '',
					'tradeName' => '"$co[company_name]"',
					'subStreet' => '"NEST BLDG."',
					'street' => '"A.S FORTUNA AVE."',
					'barangay' =>'',
					'district' => '',
					'city' => '"MANDAUE CITY"',
					'zip' => '6014',
					'rdo' => '123',
					'fiscal_calendar' => '12', 
					'fiscal_monthend' => '');

	$th =0; $tzrated=0; $tvatable=0; $tvat=0;
	while($rc = mysql_fetch_array($d)){
		if($rc[tradename]!=''){ $tradename= '"'.utf8_encode(html_entity_decode($rc[tradename])).'"';  }else{$tradename='';}
		$address=$rc[address];
		$billing_address=$rc[billing_address];
		if($rc[lname]!=''){ $lname= '"'.utf8_encode(html_entity_decode($rc[lname])).'"';  }else{$lname='';}
		if($rc[fname]!=''){ $fname= '"'.utf8_encode(html_entity_decode($rc[fname])).'"';  }else{$fname='';}
		if($rc[lname]!=''){ $lname= '"'.utf8_encode(html_entity_decode($rc[lname])).'"';  }else{$lname='';}
		$h = $rc[h];
		$zrated= $rc[zrated];
		$vatable= $rc[vatable];
		$vat= $rc[vat];
		$d_string .= "D,S,".$rc[tin_no].",".$tradename.",".$lname.",".$fnam.",".$mname. "," . ",\"". utf8_encode(html_entity_decode($address)) . "\",\"" . utf8_encode(html_entity_decode($billing_address)) . "\"," . $h . ",".$zrated.",".$vatable.",".$vat.",".$own_tin.",".$end.PHP_EOL;
		
		$th+=$h; 
		$tzrated+=$zrated; 
		$tvatable+=$vatable; 
		$tvat+=$vat;
	}
	$return = "H,S,".$owner[registeredName].",". $owner[taxPayerLname].",". $owner[taxPayerFname].",". $owner[taxPayerMname].",".$owner[tradeName].",".$owner[barangay].",". $owner[city].",".$owner[zip].",".$th.",".$tzrated.",".$tvatable.",".$tvat.",".$owner[rdo].",".$end.","."12". PHP_EOL;
	$return .= $d_string;
	
	

	
	//echo $return;
  $filename = "000070182S".date('Ymdhis').'.DAT';
  $handle = fopen("../dbbackup/".$filename,'w+');
  fwrite($handle,$return);
  fclose($handle);
  
  $filesize = filesize("../dbbackup/".$filename);
  $filetype = filetype("../dbbackup/".$filename);
	
  header("Content-Disposition: attachment; filename=$filename");
  header("Content-length: $filesize");
  header("Content-Type: application/force-download");
  readfile("../dbbackup/$filename");

 ?>