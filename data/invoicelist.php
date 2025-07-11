<?php
	session_start();
	include("../includes/dbUSEi.php");
	
	$datares = $con->query("SELECT LPAD(doc_no,6,0) AS docno, if(invoice_no!=0,lpad(invoice_no,6,0),'') as inv_no, DATE_FORMAT(invoice_date,'%m/%d/%Y') AS idate, DATE_FORMAT(DATE_ADD(invoice_date,INTERVAL b.no_days DAY),'%m/%d/%Y') AS duedate, customer_name, b.description AS terms, remarks, amount, `status`, '' as cr FROM invoice_header a LEFT JOIN options_terms b ON a.terms=b.terms_id  where branch = '$_SESSION[branchid]' order by invoice_date desc;");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  $myDoc = "";
	  if($row['terms'] != 'Cash') {
		
		$a = dbquery("SELECT DISTINCT CONCAT ('<a href=\"#\" onclick=\"parent.viewCR(',a.trans_no,')\">',LPAD(a.trans_no,6,0),'</a>') AS xdoc FROM cr_header a LEFT JOIN cr_details b ON a.trans_no = b.trans_no AND a.branch = b.branch WHERE  b.doc_no =  trim(leading '0' from '$row[docno]') AND a.branch = '$_SESSION[branchid]';");
		while($b = $a->fetch_array(MYSQLI_ASSOC)) {
			$myDoc.="$b[xdoc]<br/>";
		}
		$myDoc=substr($myDoc,0,-5);
		if($myDoc!="") { $row['cr'] = $myDoc; }
	  }
	  
	  $data[] = array_map('html_entity_decode',$row);
	  //$data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>