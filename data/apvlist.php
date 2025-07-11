<?php
	session_start();
	include("../includes/dbUSEi.php");
	$data = array();
	$datares = $con->query("SELECT LPAD(apv_no,6,0) AS apv, DATE_FORMAT(apv_date,'%m/%d/%Y') AS ad8, supplier_name AS sname, remarks, amount, `status`, '' as docs FROM apv_header WHERE branch='1' ORDER BY apv_date DESC, apv_no desc;");
	while($row = $datares->fetch_array(MYSQLI_ASSOC)){
		$myDoc = "";

		$a = dbquery("SELECT DISTINCT CONCAT ('<a href=\"#\" onclick=\"parent.viewCV(',a.cv_no,')\" style=\"text-decoration: none;\">','CV-',LPAD(a.cv_no,6,0),'</a>') AS xdoc FROM cv_header a LEFT JOIN cv_details b ON a.cv_no = b.cv_no AND a.branch = b.branch WHERE  b.ref_no =  TRIM(LEADING '0' FROM '$row[apv]') AND ref_type = 'AP' AND a.branch = '1';");
		while($b = $a->fetch_array(MYSQLI_ASSOC)) {
			$myDoc.="$b[xdoc]<br/>";
		}
		$myDoc=substr($myDoc,0,-5);
		if($myDoc!="") { $row['docs'] = $myDoc; }

		
		$data[] = array_map('utf8_encode',$row);
	}
	$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
	echo json_encode($results);
	@mysqli_close($con);
?>