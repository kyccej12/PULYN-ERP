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
		list($total,$discount,$net) = $con->getArray("select ifnull(sum(amount),0),ifnull(sum(discount),0),ifnull(sum(amount_due),0) from pharma_so_details where so_no = '$so_no' and branch = '$_SESSION[branchid]';");
		$con->dbquery("update ignore pharma_so_header set gross = '$total', discount = '$discount', net = '$net', amount_due = '$net'  where so_no = '$so_no' and branch = '$_SESSION[branchid]';");
	}

    switch($_REQUEST['mod']) {
        // case "saveHeader":

        //     list($isCount) = $con->getArray("select count(*) from pharma_so_header where so_no = '$_POST[so_no]';");
        //     if($isCount > 0) {
        //         $queryString = "UPDATE IGNORE pharma_so_header set csi_no = '$_POST[csi_no]', so_date = '".$con->formatDate($_POST['so_date'])."', pid = '$_POST[pid]', patient_name = '$_POST[pname]', patient_address = '$_POST[paddr]', customer_code = '$_POST[cid]',customer_name = '".$con->escapeString(htmlentities($_POST['cname']))."',customer_address = '".$con->escapeString(htmlentities($_POST['caddr']))."',terms = '$_POST[terms]', scpwd_id = '$_POST[sc_id]', physician = '".$con->escapeString(htmlentities($_POST['physician']))."', remarks = '".$con->escapeString(htmlentities($_POST['remarks']))."', updated_by = '$_SESSION[userid]' , updated_on = now() where so_no = '$_POST[so_no]';";
        //     } else {
        //         $queryString = "INSERT IGNORE INTO pharma_so_header (so_no,csi_no,branch,so_date,pid,patient_name,patient_address,customer_code,customer_name,customer_address,terms,scpwd_id,physician,remarks,trace_no,created_by,created_on) VALUES ('$_POST[so_no]','$_POST[csi_no]','$bid','".$con->formatDate($_POST['so_date'])."','$_POST[pid]','".$con->escapeString(htmlentities($_POST['pname']))."','".$con->escapeString(htmlentities($_POST['paddr']))."','$_POST[cid]','".$con->escapeString(htmlentities($_POST['cname']))."','".$con->escapeString(htmlentities($_POST['caddr']))."','$_POST[terms]','$_POST[sc_id]','".$con->escapeString(htmlentities($_POST['physician']))."','".$con->escapeString(htmlentities($_POST['remarks']))."','$_POST[trace_no]','$_SESSION[userid]',now());";
        //         //echo "INSERT IGNORE INTO pharma_so_header (so_no,branch,so_date,pid,patient_name,patient_address,customer_code,customer_name,customer_address,terms,scpwd_id,physician,remarks,trace_no,created_by,created_on) VALUES ('$_POST[so_no]','$bid','".$con->formatDate($_POST['so_date'])."','$_POST[patient_id]','".$con->escapeString(htmlentities($_POST['patient_name']))."','".$con->escapeString(htmlentities($_POST['patient_address']))."','$_POST[customer_code]','".$con->escapeString(htmlentities($_POST['customer_name']))."','".$con->escapeString(htmlentities($_POST['customer_address']))."','$_POST[terms]','$_POST[sc_id]','".$con->escapeString(htmlentities($_POST['physician']))."','".$con->escapeString(htmlentities($_POST['remarks']))."','$_POST[trace_no]','$_SESSION[userid]',now());";
        //     }

        //     $con->dbquery($queryString);

        // break;

        // case "addItem":

            
        //     $scpwd_id = $_POST['scpwd_id'];
            
        //     $qty = $con->formatDigit($_POST['qty']);
        //     $price = $con->formatDigit($_POST['cost']);
        //     $amt = ROUND($qty*$price,2);

        //     if($scpwd_id != '') {
        //         $discount = $amt * 0.20;
        //         $due = $amt - $discount;
        //     } else { $discount = 0; $due = $amt; }

            
        //     $con->dbquery("INSERT INTO pharma_so_details (so_no,branch,`code`,`description`,unit,unit_price,qty,amount,discount,amount_due,trace_no) VALUES ('$_POST[so_no]','$bid','$_POST[item]','".$con->escapeString(htmlentities($_POST['description']))."','$_POST[unit]','$price','$qty','$amt','$discount','$due','$_POST[trace_no]');");
        //     updateAmount($_POST['so_no']);
        // break;

        case "saveHeader":

            list($isCount) = $con->getArray("select count(*) from pharma_so_header where so_no = '$_POST[so_no]';");
            if($isCount > 0) {
                $queryString = "UPDATE IGNORE pharma_so_header set csi_no = '$_POST[csi_no]', so_date = '".$con->formatDate($_POST['so_date'])."', pid = '$_POST[pid]', patient_name = '$_POST[pname]', patient_address = '$_POST[paddr]', customer_code = '$_POST[cid]',customer_name = '".$con->escapeString(htmlentities($_POST['cname']))."',customer_address = '".$con->escapeString(htmlentities($_POST['caddr']))."',terms = '$_POST[terms]', scpwd_id = '$_POST[sc_id]', physician = '".$con->escapeString(htmlentities($_POST['physician']))."', remarks = '".$con->escapeString(htmlentities($_POST['remarks']))."', disc_type = '$_POST[disc_type]', disc_percent = '$_POST[disc_percent]', updated_by = '$_SESSION[userid]' , updated_on = now() where so_no = '$_POST[so_no]';";
            } else {
                $queryString = "INSERT IGNORE INTO pharma_so_header (so_no,csi_no,branch,so_date,pid,patient_name,patient_address,customer_code,customer_name,customer_address,terms,scpwd_id,physician,remarks,disc_type,disc_percent,trace_no,created_by,created_on) VALUES ('$_POST[so_no]','$_POST[csi_no]','$bid','".$con->formatDate($_POST['so_date'])."','$_POST[pid]','".$con->escapeString(htmlentities($_POST['pname']))."','".$con->escapeString(htmlentities($_POST['paddr']))."','$_POST[cid]','".$con->escapeString(htmlentities($_POST['cname']))."','".$con->escapeString(htmlentities($_POST['caddr']))."','$_POST[terms]','$_POST[sc_id]','".$con->escapeString(htmlentities($_POST['physician']))."','".$con->escapeString(htmlentities($_POST['remarks']))."','$_POST[discountType]','$_POST[discountPercent]','$_POST[trace_no]','$_SESSION[userid]',now());";
                //echo "INSERT IGNORE INTO pharma_so_header (so_no,branch,so_date,pid,patient_name,patient_address,customer_code,customer_name,customer_address,terms,scpwd_id,physician,remarks,trace_no,created_by,created_on) VALUES ('$_POST[so_no]','$bid','".$con->formatDate($_POST['so_date'])."','$_POST[patient_id]','".$con->escapeString(htmlentities($_POST['patient_name']))."','".$con->escapeString(htmlentities($_POST['patient_address']))."','$_POST[customer_code]','".$con->escapeString(htmlentities($_POST['customer_name']))."','".$con->escapeString(htmlentities($_POST['customer_address']))."','$_POST[terms]','$_POST[sc_id]','".$con->escapeString(htmlentities($_POST['physician']))."','".$con->escapeString(htmlentities($_POST['remarks']))."','$_POST[trace_no]','$_SESSION[userid]',now());";
            }

            $con->dbquery($queryString);

        break;

        case "addItem":

            $disCounter = $_POST['discPercent'];
            $scpwd_id = $_POST['scpwd_id'];
            
            $qty = $con->formatDigit($_POST['qty']);
            $price = $con->formatDigit($_POST['cost']);
            $amt = ROUND($qty*$price,2);

            // if($scpwd_id != '') {
            //     $discount = $amt * 0.20;
            //     $due = $amt - $discount;
            // } else { $discount = 0; $due = $amt; }

            /* Discount */
			
            if($disCounter != '') {
            $discount = $amt * ($_POST['discPercent']/100);
            $due = $amt - $discount;
             } else { $discount = 0; $due = $amt; }
            
            $con->dbquery("INSERT INTO pharma_so_details (so_no,branch,`code`,`description`,unit,unit_price,qty,amount,discount,disctype,discpercent,amount_due,trace_no) VALUES ('$_POST[so_no]','$bid','$_POST[item]','".$con->escapeString(htmlentities($_POST['description']))."','$_POST[unit]','$price','$qty','$amt','$discount','$_POST[discType]','$_POST[discPercent]','$due','$_POST[trace_no]');");
            updateAmount($_POST['so_no']);
        break;

        case "deleteLine":
            $con->deleteRow($table="pharma_so_details",$arg = "line_id='$_POST[lid]'");
            updateAmount($_POST['so_no']);
        break;

        case "getTotals":
            $t = $con->getArray("SELECT gross, discount, net, amount_due FROM pharma_so_header WHERE so_no = '$_POST[so_no]' AND branch = '$bid';");           
            echo json_encode(array("gross"=>number_format($t['gross'],2), "discount"=>number_format($t['discount'],2), "net"=>number_format($t['net'],2), "due"=>number_format($t['amount_due'],2)));
            updateAmount($so_no);
        break;

        // case "applyDiscount":
		// 	$d = $con->getArray("select * from pharma_so_details where line_id = '$_POST[lid]';");
			
        //     $discount = ROUND(($d['amount']) * ($_POST['discPercent']/100),2);
        //     $adue = $d['amount'] - $discount;      
           
        //     $con->dbquery("update ignore pharma_so_details set discount = '$discount', disctype='$_POST[discType]', discpercent = '$_POST[discPercent]', amount_due = '$adue' where line_id = '$_POST[lid]';");
        //     updateAmount($_POST['so_no']);	
        // break;

        case "check4print":
			list($a) = $con->getArray("select count(*) from pharma_so_header where so_no = '$_POST[so_no]' and branch = '$bid';");
			list($b) = $con->getArray("select count(*) from pharma_so_details where so_no = '$_POST[so_no]' and branch = '$bid';");
			
			if($a == 0 && $b > 0) { echo "head"; }
			if($b == 0 && $a > 0) { echo "det"; }
			if($a == 0 && $b == 0) { echo "both"; }
			if($a > 0 && $b > 0) { echo "noerror"; }
		break;

        case "finalize":
            $con->dbquery("update ignore pharma_so_header set `status` = 'Finalized', updated_by = '$_SESSION[userid]', updated_on = now() where so_no = '$_POST[so_no]' and branch = '$bid';");
            updateAmount($_POST['so_no']);
        break;

        case "checkBilled":
            if($con->countRows("select so_no from pharma_so_header where so_no = '$_POST[so_no]' and branch = '$bid' and (billed = 'Y' or paid ='Y');") > 0) {
                echo "processed";
            }
        break;

        case "reopen":
            $con->dbquery("update pharma_so_header set status = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where so_no = '$_POST[so_no]' and branch = '$bid';");
        break;
        
        case "cancel":
            $con->dbquery("update ignore pharma_so_header set `status` = 'Cancelled', updated_by = '$_SESSION[userid]', updated_on = now() where so_no = '$_POST[so_no]' and branch = '$bid';");
        break; 

        case "retrieve":
            $data = array();
			$srrd = $con->dbquery("SELECT line_id as id, `code`, description, unit, qty, unit_price, discount, amount as amount_due FROM pharma_so_details WHERE trace_no = '$_REQUEST[trace_no]';");
			while($row = $srrd->fetch_array()) {
				$data[] = array_map('utf8_encode',$row);
			}
			$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
			echo json_encode($results);	
        break;

        case "verify":
            $con->dbquery("update pharma_so_header set cstatus = '12', verified = 'Y', verified_by = '$_SESSION[userid]', verified_on = now() where so_no = '$_POST[so_no]' and branch = '$bid';");
        
           // echo "SELECT a.so_no AS so, b.code as parent_code, b.code, b.description AS `procedure`, a.physician, d.sample_type FROM pharma_so_header a LEFT JOIN pharma_so_details b ON a.trace_no = b.trace_no LEFT JOIN patient_info c ON a.patient_id = c.patient_id LEFT JOIN services_master d ON b.code = d.code WHERE a.status = 'Finalized' AND cstat IN (2,4,12) AND d.with_subtests = 'N' AND d.category IN ('1','2') AND a.so_no = '$_POST[so_no]' UNION SELECT a.so_no AS so, e.parent as parent_code, e.code, e.description AS `procedure`, a.physician, f.sample_type FROM pharma_so_header a LEFT JOIN pharma_so_details b ON a.trace_no = b.trace_no LEFT JOIN patient_info c ON a.patient_id = c.patient_id LEFT JOIN services_master d ON b.code = d.code LEFT JOIN services_subtests e ON b.code = e.parent LEFT JOIN services_master f ON e.code = f.code WHERE a.status = 'Finalized' AND cstat IN (2,4,12) AND d.with_subtests = 'Y' AND f.category IN ('1','2') AND a.so_no = '$_POST[so_no]';";

            /* Send Lab Request Pending Extraction */
            $so = $con->dbquery("SELECT a.so_no AS so, b.code as parent_code, b.code, b.description AS `procedure`, a.physician, d.sample_type FROM pharma_so_header a LEFT JOIN pharma_so_details b ON a.trace_no = b.trace_no LEFT JOIN patient_info c ON a.patient_id = c.patient_id LEFT JOIN services_master d ON b.code = d.code WHERE a.status = 'Finalized' AND d.with_subtests = 'N' AND d.category IN ('1','2') AND a.so_no = '$_POST[so_no]' UNION SELECT a.so_no AS so, e.parent as parent_code, e.code, e.description AS `procedure`, a.physician, f.sample_type FROM pharma_so_header a LEFT JOIN pharma_so_details b ON a.trace_no = b.trace_no LEFT JOIN patient_info c ON a.patient_id = c.patient_id LEFT JOIN services_master d ON b.code = d.code LEFT JOIN services_subtests e ON b.code = e.parent LEFT JOIN services_master f ON e.code = f.code WHERE a.status = 'Finalized' AND d.with_subtests = 'Y' AND f.category IN ('1','2') AND a.so_no = '$_POST[so_no]';");
            while($eRow = $so->fetch_array()) {
                list($labCount) = $con->getArray("select count(*) from lab_samples where so_no = '$eRow[so]' and parent_code = '$eRow[parent_code]' and code = '$eRow[code]';");
                if($labCount == 0) {
                    $con->dbquery("INSERT IGNORE INTO lab_samples (branch,so_no,parent_code,code,`procedure`,sampletype,physician,created_by,created_on) values ('$bid','$eRow[so]','$eRow[parent_code]','$eRow[code]','$eRow[procedure]','$eRow[sample_type]','$eRow[physician]','$uid',now());");
                }
            }
			
        
        break;

    }



?>