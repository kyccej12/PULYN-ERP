<?php
	session_start();
	include("../handlers/initDB.php");
	$con = new myDB;

	$data = array();

	$datares = $con->dbquery("SELECT doc_no, LPAD(doc_no,6,'0') AS dno, or_no, DATE_FORMAT(doc_date,'%m/%d/%Y') AS d8, '' as so, IF(customer_code = '0', 'CHARGED TO PATIENT', customer_name) AS cname, b.cashtype, remarks, amount_due, amount_paid, `status` FROM or_header a LEFT JOIN options_cashtype b ON a.cashtype = b.id WHERE branch = '$_SESSION[branchid]';");
	while($row = $datares->fetch_array()){
		$mySO = '';
		$dQuery = $con->dbquery("select distinct so_no, pname from or_details where doc_no = '$row[doc_no]' and (so_no!='' or so_no is not null);");
		while($dRow = $dQuery->fetch_array()) {
			$mySO .= "<a href=\"#\" onclick=\"javascript: parent.viewSO($dRow[so_no]);\" style=\"text-decoration: none; color: black;\" title=\"Click to View Sales ORder Details\">$dRow[so_no] &raquo; $dRow[pname]</a><br/>";
		}
		$row['so'] = $mySO;

	  $data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
?>