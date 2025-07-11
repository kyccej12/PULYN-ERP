<?php
	session_start();
	include("../handlers/initDB.php");
	$con = new myDB;
	$data = array();

	switch($_GET[mod]) {
		case "sp":
			$sql = "select record_id, `code`, `description`, unit, special_price, previous_price, if(with_validity='N','No','Yes') as isvalid, with_validity, if(valid_until!='0000-00-00',date_format(valid_until,'%m/%d/%Y'),'') as validuntil FROM contact_sprice WHERE contact_id = '$_REQUEST[cid]';";
		break;
		case "invoices":
			$sql = "SELECT doc_no, CONCAT(UCASE(invoice_type),'-',lpad(doc_no,6,0)) AS invoice, DATE_FORMAT(invoice_date,'%m/%d/%Y') AS idate, b.description AS terms, a.remarks, amount, applied_amount, balance FROM invoice_header a INNER JOIN options_terms b ON a.terms = b.terms_id WHERE customer = '$_GET[cid]' AND `status` not in ('Active','Cancelled') and branch = '$_SESSION[branchid]' UNION ALL SELECT '99999999' AS doc_no, '' AS invoice, '' AS idate, '' AS termDesc, '' AS remarks, '' AS amount, '' AS applied_amount, '' AS balance;";
		break;
		case "po":
			$sql = "SELECT po_no, LPAD(po_no,6,0) AS mypo, DATE_FORMAT(po_date,'%m/%d/%Y') AS po_date, b.description AS terms_desc, a.remarks, a.amount FROM po_header a LEFT JOIN options_terms b ON a.terms = b.terms_id WHERE supplier = '$_REQUEST[cid]' AND `status` = 'Finalized';";
		break;
	}
	
	
	$datares = $con->dbquery($sql);
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
?>