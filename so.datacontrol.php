<?php
    session_start();
	require_once 'handlers/_generics.php';
	$con = new _init;
    $bid = $_SESSION['branchid'];

    if($_SESSION['authkey']) {
		$con->updateTstamp($_SESSION['authkey']);
	}

	function updateAmount($so_no) {
		global $con;
		list($total) = $con->getArray("select ifnull(sum(amount_due),0) from so_details where so_no = '$so_no';");
		$con->dbquery("update so_header set amount = '$total' where so_no = '$so_no';");
		echo number_format($total,2);
	}

    switch($_REQUEST['mod']) {
        case "saveHeader":

            if($_POST['terms'] != 0) { $sostat = '10'; } else { $sostat = '1'; }

            if($_POST['so_no'] != '') {
                $queryString = "UPDATE IGNORE so_header set so_date = '".$con->formatDate($_POST['so_date'])."',patient_id = '$_POST[pid]',patient_name = '".$con->escapeString(htmlentities($_POST['pname']))."',patient_address = '".$con->escapeString(htmlentities($_POST['paddr']))."',customer_code = '$_POST[cid]',customer_name = '".$con->escapeString(htmlentities($_POST['cname']))."',customer_address = '".$con->escapeString(htmlentities($_POST['caddr']))."',terms = '$_POST[terms]',hmo_card_no = '$_POST[hmo_no]',hmo_card_expiry = '".$con->formatDate($_POST['card_expiry'])."',scpwd_id = '$_POST[sc_id]',with_loa = '$_POST[with_loa]',loa_date = '".$con->formatDate($_POST['loa_date'])."', patient_stat = '$_POST[pstat]', mid_no = '".$con->formatDigit($_POST['mid_no'])."', digi_promo = '".$con->formatDigit($_POST['digi_promo'])."', physician = '".$con->escapeString(htmlentities($_POST['physician']))."',cstatus = '$sostat', remarks = '".$con->escapeString(htmlentities($_POST['remarks']))."', updated_by = '$_SESSION[userid]' , updated_on = now() where so_no = '$_POST[so_no]';";
                $sono = $_POST['so_no'];
            } else {
                list($sono) = $con->getArray("select ifnull(max(so_no),0)+1 from so_header where branch = '$bid';"); 
                $queryString = "INSERT IGNORE INTO so_header (so_no,branch,so_date,priority_no,patient_id,patient_name,patient_address,customer_code,customer_name,customer_address,terms,hmo_card_no,hmo_card_expiry,scpwd_id,with_loa,loa_date,patient_stat,mid_no,digi_promo,physician,cstatus,remarks,trace_no,created_by,created_on) VALUES ('$sono','$bid','".$con->formatDate($_POST['so_date'])."','$_POST[pri_no]','$_POST[pid]','".$con->escapeString(htmlentities($_POST['pname']))."','".$con->escapeString(htmlentities($_POST['paddr']))."','$_POST[cid]','".$con->escapeString(htmlentities($_POST['cname']))."','".$con->escapeString(htmlentities($_POST['caddr']))."','$_POST[terms]','$_POST[hmo_no]','".$con->formatDate($_POST['card_expiry'])."','$_POST[sc_id]','$_POST[with_loa]','".$con->formatDate($_POST['loa_date'])."','$_POST[pstat]', '".$con->formatDigit($_POST['mid_no'])."', '".$con->formatDigit($_POST['digi_promo'])."', '".$con->escapeString(htmlentities($_POST['physician']))."','$sostat','".$con->escapeString(htmlentities($_POST['remarks']))."','$_POST[trace_no]','$_SESSION[userid]',now());";
            }
            $con->dbquery($queryString);
            echo str_pad($sono,6,'0',STR_PAD_LEFT);
        break;

        case "addItem":

            $sprice = $con->formatDigit($_POST['sprice']);
            $qty = $con->formatDigit($_POST['qty']);
            $amt = $con->formatDigit($_POST['amount']);
            
            if($sprice != $uprice) { $price = $sprice; } else { $price = $uprice; }
            $con->dbquery("INSERT INTO so_details (so_no,branch,`code`,`description`,unit,unit_price,is_special,qty,amount,discount,amount_due,trace_no) VALUES ('$_POST[so_no]','$bid','$_POST[item]','".$con->escapeString(htmlentities($_POST['description']))."','$_POST[unit]','$sprice','$_POST[ispecial]','$qty','$amt','0','$amt','$_POST[trace_no]');");
            updateAmount($_POST['so_no']);
        break;

        case "checkLabSamples":
			list($isE) = $con->getArray("SELECT COUNT(*) FROM lab_samples WHERE so_no = '$_POST[so_no]' AND parent_code = '$_POST[code]' AND extracted = 'Y' and branch = '$bid';");
			if($isE > 0) { echo "notOk"; }
		break;

        case "deleteLine":
            $con->deleteRow($table="so_details",$arg = "line_id='$_POST[lid]'");
            $con->deleteRow($table="lab_samples",$arg = "so_no = '$_POST[so_no]' and parent_code = '$_POST[code]'");
            updateAmount($_POST['so_no']);
        break;

        case "check4print":
			list($a) = $con->getArray("select count(*) from so_header where so_no = '$_POST[so_no]' and branch = '$bid';");
			list($b) = $con->getArray("select count(*) from so_details where so_no = '$_POST[so_no]' and branch = '$bid';");
			
			if($a == 0 && $b > 0) { echo "head"; }
			if($b == 0 && $a > 0) { echo "det"; }
			if($a == 0 && $b == 0) { echo "both"; }
			if($a > 0 && $b > 0) { echo "noerror"; }
		break;

        case "finalize":
            $con->dbquery("update ignore so_header set `status` = 'Finalized', updated_by = '$_SESSION[userid]', updated_on = now() where so_no = '$_POST[so_no]' and branch = '$bid';");
            updateAmount($_POST['so_no']);
        break;

        case "checkBilled":
            if($con->countRows("select so_no from so_header where so_no = '$_POST[so_no]' and branch = '$bid' and (billed = 'Y' or paid ='Y');") > 0) {
                echo "processed";
            }
        break;

        case "reopen":
            if($_POST['terms'] != '0' || $_POST['terms'] != '100') { $soStat = '10'; } else { $soStat = '1'; }
            $con->dbquery("update so_header set status = 'Active', cstatus = '$soStat', updated_by = '$_SESSION[userid]', updated_on = now() where so_no = '$_POST[so_no]' and branch = '$bid';");
        break;
        
        case "cancel":
            $con->dbquery("update ignore so_header set `status` = 'Cancelled', updated_by = '$_SESSION[userid]', updated_on = now() where so_no = '$_POST[so_no]' and branch = '$bid';");
            $con->dbquery("update ignore lab_samples set `status` = '2', updated_by = '$_SESSION[userid]', updated_on = now() where so_no = '$_POST[so_no]' and branch = '$bid';");
        break; 

        case "retrieve":
            $data = array();
			$srrd = $con->dbquery("SELECT line_id as id, `code`, description, unit, qty, unit_price, amount_due FROM so_details WHERE trace_no = '$_REQUEST[trace_no]';");
			while($row = $srrd->fetch_array()) {
				$data[] = array_map('utf8_encode',$row);
			}
			$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
			echo json_encode($results);	
        break;

        case "verify":
            $con->dbquery("update so_header set cstatus = '12', verified = 'Y', verified_by = '$_SESSION[userid]', verified_on = now() where so_no = '$_POST[so_no]' and branch = '$bid';");
         
            /* Send Lab Request Pending Extraction */
            $so = $con->dbquery("SELECT a.so_no AS so, b.code as parent_code, b.code, b.description AS `procedure`, a.physician, d.sample_type, d.container_type FROM so_header a LEFT JOIN so_details b ON a.trace_no = b.trace_no LEFT JOIN patient_info c ON a.patient_id = c.patient_id LEFT JOIN services_master d ON b.code = d.code WHERE a.status = 'Finalized' AND d.with_subtests = 'N' AND d.category IN ('1','2','3') AND a.so_no = '$_POST[so_no]' UNION SELECT a.so_no AS so, e.parent as parent_code, e.code, e.description AS `procedure`, a.physician, f.sample_type, f.container_type FROM so_header a LEFT JOIN so_details b ON a.trace_no = b.trace_no LEFT JOIN patient_info c ON a.patient_id = c.patient_id LEFT JOIN services_master d ON b.code = d.code LEFT JOIN services_subtests e ON b.code = e.parent LEFT JOIN services_master f ON e.code = f.code WHERE a.status = 'Finalized' AND d.with_subtests = 'Y' AND f.category IN ('1','2','3') AND a.so_no = '$_POST[so_no]';");
            while($eRow = $so->fetch_array()) {
                list($labCount) = $con->getArray("select count(*) from lab_samples where so_no = '$eRow[so]' and parent_code = '$eRow[parent_code]' and code = '$eRow[code]';");
                if($labCount == 0) {
                    $con->dbquery("INSERT IGNORE INTO lab_samples (branch,so_no,parent_code,code,`procedure`,sampletype,samplecontainer,physician,created_by,created_on) values ('$bid','$eRow[so]','$eRow[parent_code]','$eRow[code]','$eRow[procedure]','$eRow[sample_type]','$eRow[container_type]','$eRow[physician]','$uid',now());");
                }
            }

            /* Send Request to Nursing Station for PEME */
            $gQuery = $con->dbquery("SELECT a.priority_no as prio, a.so_no, a.so_date, b.code as parentcode, b.code, b.description AS `procedure`, a.patient_id AS pid, c.birthplace, c.occupation AS occu, c.employer AS compname, c.mobile_no AS contactno FROM so_header a LEFT JOIN so_details b ON a.trace_no = b.trace_no LEFT JOIN patient_info c ON a.patient_id = c.patient_id WHERE a.so_no = '$_POST[so_no]' AND b.code IN ('M001') AND a.status IN (2,4,12) UNION ALL SELECT a.priority_no as prio, a.so_no, a.so_date, b.code as parentcode, e.code, e.description AS `procedure`, a.patient_id AS pid, c.birthplace, c.occupation AS occu, c.employer AS compname, c.mobile_no AS contactno FROM so_header a LEFT JOIN so_details b ON a.trace_no = b.trace_no LEFT JOIN patient_info c ON a.patient_id = c.patient_id LEFT JOIN services_master d ON b.code = d.code LEFT JOIN services_subtests e ON e.parent = d.code  WHERE a.so_no = '$_POST[so_no]' AND e.code IN ('M001') AND a.status IN (2,4,12) AND  d.with_subtests = 'Y';");
            while($hRow = $gQuery->fetch_array()) {
                $con->dbquery("INSERT IGNORE INTO peme (so_no,branch,so_date,prio,parentcode,code,`procedure`,pid,pob,occu,compname,contactno) values ('$hRow[so_no]','$bid','$hRow[so_date]','$hRow[prio]','$hRow[parentcode]','$hRow[code]','$hRow[procedure]','$hRow[pid]','" . $con->escapeString(htmlentities($hRow['birthplace'])) . "','$hRow[occu]','" . $con->escapeString(htmlentities($hRow['compname'])) . "','$hRow[contactno]');");
                echo "INSERT IGNORE INTO peme (so_no,branch,so_date,prio,parentcode,code,`procedure`,pid,pob,occu,compname,contactno) values ('$hRow[so_no]','$bid','$hRow[so_date]','$hRow[prio]','$hRow[parentcode]','$hRow[code]','$hRow[procedure]','$hRow[pid]','" . $con->escapeString(htmlentities($hRow['birthplace'])) . "','$hRow[occu]','" . $con->escapeString(htmlentities($hRow['compname'])) . "','$hRow[contactno]');";

            }
			
        
        break;

    }



?>