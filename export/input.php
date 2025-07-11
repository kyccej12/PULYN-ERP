<?php
	set_time_limit(0);
    include("../handlers/_generics.php");
	$con = new _init;

	list($dtf,$dt2,$end) = getArray("SELECT '".$_REQUEST['year']."-".$_REQUEST['month']."-01',LAST_DAY('".$_REQUEST['year']."-".$_REQUEST['month']."-01'), date_format(LAST_DAY('".$_REQUEST['year']."-".$_REQUEST['month']."-01'),'%m/%d/%Y') ;");
	$d = $con->dbquery("SELECT LEFT(REPLACE(b.tin_no,'-',''),9) AS tin_no,b.tradename,'' AS lname, '' AS fname,'' AS mname, REPLACE(address,'\r\n',' ') AS address ,  REPLACE(billing_address,'\r\n',' ') AS billing_address,0 as h,IF(b.vatable='N',SUM(ROUND(debit-credit,2)),0) AS zrated,IF(b.vatable='Y',SUM(ROUND(debit-credit,2)),0) vatable, ROUND(IF(b.vatable='Y',SUM(ROUND(debit-credit,2)),0) * 0.12,2) vat FROM sjpi.acctg_gl a INNER JOIN contact_info b ON a.contact_id =  b.file_id WHERE a.doc_date BETWEEN '$dtf' AND '$dt2' AND a.acct = '1401' GROUP BY a.contact_id;");
	$own_tin = '123456789';
	$owner = array('tin' => '000070182',
					'taxPayerClass' => 'Non-Individual' ,
					'registeredName' => '"SENIOR SAN JOSE FRANCHISING CORPORATION"',
					'taxPayerLname' => '',
					'taxPayerFname' => '',
					'taxPayerMname' => '',
					'tradeName' => '"$co[company_name]"',
					'subStreet' => 'NEST BLDG.',
					'street' => '"A.S FORTUNA AVE."',
					'barangay' =>'',
					'district' => '',
					'city' => 'MANDAUE CITY',
					'zip' => '6014',
					'rdo' => '123',
					'fiscal_calendar' => '12', 
					'fiscal_monthend' => '');

	while($rc = mysql_fetch_array($d)){
		if($rc[tradename]!=''){ $tradename= '"'.utf8_encode(html_entity_decode($rc[tradename])).'"';  }else{$tradename='';}
		//if($rc[address]!=''){ $address= '"'.utf8_encode(html_entity_decode($rc[address])).'"';  }else{$address='';}
		//if($rc[billing_address]!=''){ $billing_address= '"'.utf8_encode(html_entity_decode($rc[billing_address])).'"';  }else{$billing_address='';}
		$address=$rc[address];
		$billing_address=$rc[billing_address];
		if($rc[lname]!=''){ $lname= '"'.utf8_encode(html_entity_decode($rc[lname])).'"';  }else{$lname='';}
		if($rc[fname]!=''){ $fname= '"'.utf8_encode(html_entity_decode($rc[fname])).'"';  }else{$fname='';}
		if($rc[lname]!=''){ $lname= '"'.utf8_encode(html_entity_decode($rc[lname])).'"';  }else{$lname='';}
		$h = $rc[h];
		$zrated= $rc[zrated];
		$vatable= $rc[vatable];
		$vat= $rc[vat];
		$d_string .= "D,P,".$rc[tin_no].",".$tradename.",".$lname.",".$fnam.",".$mname. "," . ",\"". utf8_encode(html_entity_decode($address)) . "\",\"" . utf8_encode(html_entity_decode($billing_address)) . "\"," . $h . ",".$zrated.",".$vatable.",".$vat.",".$own_tin.",".$end.PHP_EOL;
		$th+=$h; 
		$tzrated+=$zrated; 
		$tvatable+=$vatable; 
		$tvat+=$vat;
	}
	$return = "H,P,".$owner[registeredName].",". $owner[taxPayerLname].",". $owner[taxPayerFname].",". $owner[taxPayerMname].",".$owner[tradeName].",".$owner[barangay].",". $owner[city].",".$owner[zip].",".$th.",".$tzrated.",".$tvatable.",".$tvat.",".$owner[rdo].",".$end.","."12". PHP_EOL;
	$return .= $d_string;

  $filename = "000070182P".date('Ymdhis').'.DAT';
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