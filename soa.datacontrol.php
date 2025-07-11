<?php
    session_start();
    include("handlers/_generics.php");
	ini_set('max_execution_time',0);
	ini_set('memory_limit',-1);

    $con = new _init;

    function updateAmount($soa_no) {
        global $con;

        list($amtDue) = $con->getArray("select sum(amount) from soa_details where soa_no = '$soa_no' and branch = '$_SESSION[branchid]';");
        $con->dbquery("update soa_header set amount = '$amtDue', balance = '$amtDue', amount_paid = '0' where soa_no = '$soa_no' and branch = '$_SESSION[branchid]';");
    
        echo number_format($amtDue,2);
    
    }


    switch($_REQUEST['mod']) {

        case "saveHeader":
            if($_POST['soa_no'] == '') {
                list($soano) = $con->getArray("select ifnull(max(soa_no),0)+1 from soa_header where branch = '$_SESSION[branchid]';"); 
                $con->dbquery("INSERT IGNORE INTO soa_header (trace_no,soa_no,branch,soa_date,customer_code,customer_name,customer_address,remarks,terms,created_by,created_on) VALUES ('$_POST[trace_no]','$soano','$_SESSION[branchid]','".$con->formatDate($_POST['soa_date'])."','$_POST[cid]','".$con->escapeString(htmlentities($_POST['cname']))."','".$con->escapeString(htmlentities($_POST['caddr']))."','".$con->escapeString(htmlentities($_POST['remarks']))."','$_POST[terms]','$_SESSION[userid]',now());");
            } else {
                $con->dbquery("UPDATE IGNORE soa_header set soa_date='".$con->formatDate($_POST['soa_date'])."',customer_code='$_POST[cid]',customer_name='".$con->escapeString(htmlentities($_POST['cname']))."',customer_address='".$con->escapeString(htmlentities($_POST['caddr']))."',remarks='".$con->escapeString(htmlentities($_POST['remarks']))."',terms='$_POST[terms]',updated_by='$_SESSION[userid]',updated_on=now() where soa_no = '$_POST[soa_no]' and branch = '$_SESSION[branchid]';");
               $soano = $_POST['soa_no'];
            }
            echo str_pad($soano,6,'0',STR_PAD_LEFT);
        break;

        case "retrieve":
            $data = array();
			$srrd = $con->dbquery("SELECT line_id as lid, LPAD(so_no,6,0) AS sono, soa_no, DATE_FORMAT(so_date,'%m/%d/%Y') AS sdate, CONCAT('(',LPAD(pid,6,'0'),') ',pname) AS pname, `code`, description,unit,FORMAT(qty,2) AS qty,FORMAT(unit_price,2) AS price, FORMAT(amount,2) AS amount FROM soa_details WHERE trace_no = '$_REQUEST[trace_no]';");
			while($row = $srrd->fetch_array()) {
				$data[] = array_map('utf8_encode',$row);
			}
			$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
			echo json_encode($results);	
        break;

        // case "browseSO":
        //     $q = "SELECT so_no, lpad(so_no,6,0) as sono, DATE_FORMAT(so_date,'%m/%d/%Y') AS sdate, patient_name, amount, a.remarks FROM so_header a LEFT JOIN contact_info b ON a.customer_code = b.file_id WHERE branch = '$_SESSION[branchid]' AND customer_code = '$_POST[cid]' AND billed != 'Y' AND a.terms != '0' AND so_no NOT IN (SELECT so_no FROM soa_details WHERE soa_no = '$_POST[soa_no]' AND branch = '$_SESSION[branchid]') AND `status` = 'Finalized' ORDER BY so_date DESC, soa_no DESC;";
            
        //     if($con->countRows($q) > 0) {
        //         echo "
        //             <form name=\"frmFetchedSO\" id=\"frmFetchedSO\">
        //             <table width=100% cellspacing=0 cellpadding=0>

        //                 <tr>
        //                     <td class=gridHead width=10%>SO #</td>
        //                     <td class=gridHead width=10%>DATE</td>
        //                     <td class=gridHead width=25%>PATIENT</td>
        //                     <td class=gridHead >REMARKS</td>
        //                     <td class=gridHead width=15% align=right>AMOUNT</td>
        //                     <td class=gridHead width=20>&nbsp;</td>
        //                 </tr>";

        //         $soQuery = $con->dbquery($q); $i=0;
        //         while($soRow = $soQuery->fetch_array()) {
        //             echo "<tr bgcolor='".$con->initBackground($i)."'>
        //                     <td class=grid width=10%>".$soRow['sono']."</td>
        //                     <td class=grid width=10%>".$soRow['sdate']."</td>
        //                     <td class=grid width=25%>".$con->escapeString($soRow['patient_name'])."</td>
        //                     <td class=grid >".$soRow['remarks']."</td>
        //                     <td class=grid width=15% align=right>".number_format($soRow['amount'],2)."</td>
        //                     <td class=grid width=20 align=center><input type=checkbox name=\"so[]\" id=\"so[]\" value='".$soRow['so_no']."'></td>
        //                 </tr>
                    
        //             ";
        //             $i++;

        //         }
        //          echo "</table>  
        //         </form>";


        //     }

        // break;

        case "browseSO":

            $s = '';

			if($_POST['cid'] != '') { $s .= " and a.customer_code = '$_POST[cid]' "; }
			if($_POST['stxt'] != '') {
				$s .= " and (a.so_no = '$_POST[stxt]' || a.customer_name like '%$_POST[stxt]%' || a.patient_name like '%$_POST[stxt]%') ";
			}

            $q = "SELECT so_no, lpad(so_no,6,0) as sono, DATE_FORMAT(so_date,'%m/%d/%Y') AS sdate, patient_name, customer_name as charge_to, amount, a.remarks FROM so_header a LEFT JOIN contact_info b ON a.customer_code = b.file_id WHERE branch = '$_SESSION[branchid]' AND customer_code = '$_POST[cid]' AND billed != 'Y' AND paid != 'Y' AND so_no NOT IN (SELECT so_no FROM soa_details WHERE soa_no = '$_POST[soa_no]' AND branch = '$_SESSION[branchid]') AND `status` = 'Finalized' $s ORDER BY so_date DESC, soa_no DESC;";
            
            if($con->countRows($q) > 0) {
                echo "
                    <form name=\"frmFetchedSO\" id=\"frmFetchedSO\">
                    <table width=100% cellspacing=0 cellpadding=0>
                        <tr>
                            <td colspan=7 align=right style=\"padding: 4px;\">
                                <input type=\"text\" name=\"so_search\" id=\"so_search\" class=\"gridInput\" style=\"width: 200px;\" value=\"$_POST[stxt]\">
                                <button type = \"button\" name = \"setPrintLab\" class=\"ui-button ui-widget ui-corner-all\" onClick=\"javascript: searchBrowseSO();\">
                                    <span class=\"ui-icon ui-icon-search\"></span> Search
                                </button>	
                            </td>
                        </tr>
                        <tr>
                            <td class=gridHead width=10%>SO #</td>
                            <td class=gridHead width=10%>DATE</td>
                            <td class=gridHead width=25%>PATIENT</td>
                            <td class=gridHead width=20%>BILLED TO</td>
                            <td class=gridHead >REMARKS</td>
                            <td class=gridHead width=15% align=right>AMOUNT</td>
                            <td class=gridHead width=20>&nbsp;</td>
                        </tr>";

                $soQuery = $con->dbquery($q); $i=0;
                while($soRow = $soQuery->fetch_array()) {
                    echo "<tr bgcolor='".$con->initBackground($i)."'>
                            <td class=grid width=10%>".$soRow['sono']."</td>
                            <td class=grid width=10%>".$soRow['sdate']."</td>
                            <td class=grid width=25%>".$con->escapeString(htmlentities($soRow['patient_name']))."</td>
                            <td class=grid >".$soRow['charge_to']."</td>
                            <td class=grid >".$soRow['remarks']."</td>
                            <td class=grid width=15% align=right>".number_format($soRow['amount'],2)."</td>
                            <td class=grid width=20 align=center><input type=checkbox name=\"so[]\" id=\"so[]\" value='".$soRow['so_no']."'></td>
                        </tr>
                    
                    ";
                    $i++;

                }
                 echo "</table>  
                </form>";


            }

        break;

        case "uploadSO":
            $arr = $_POST['so'];

            foreach($arr as $so_no) {
                $sQuery = $con->dbquery("SELECT a.so_no, a.so_date, a.patient_id AS pid, a.patient_name AS pname, b.code, b.description, b.qty, b.unit, b.unit_price, b.amount FROM so_header a LEFT JOIN so_details b ON a.so_no = b.so_no AND a.branch = b.branch WHERE a.so_no = '$so_no' and a.branch = '$_SESSION[branchid]';");
                
                while($s = $sQuery->fetch_array()) {
                    $con->dbquery("INSERT INTO soa_details (soa_no,branch,so_no,so_date,pid,pname,`code`,`description`,qty,unit,unit_price,amount,trace_no) VALUES ('$_POST[soa_no]','$_SESSION[branchid]','$s[so_no]','$s[so_date]','$s[pid]','".$con->escapeString($s['pname'])."','$s[code]','$s[description]','$s[qty]','$s[unit]','$s[unit_price]','$s[amount]','$_POST[trace_no]');");
                }
            }
  
            updateAmount($_POST['soa_no']);
            
        break;

        case "browseCSO":

            $s = '';

			if($_POST['cid'] != '') { $s .= " and a.customer_code = '$_POST[cid]' "; }
			if($_POST['stxt'] != '') {
				$s .= " and (a.cso_no = '$_POST[stxt]' || pid like '%$_POST[stxt]%' || pname like '%$_POST[stxt]%') ";
			}

            $q = "SELECT LPAD(a.cso_no,6,0) AS sono, DATE_FORMAT(cso_date,'%m/%d/%Y') AS sdate, b.line_id AS lid, b.pid, b.pname AS patient_name, a.customer_name AS charge_to, b.description AS `service`, b.amount, a.remarks FROM omdcmobile.cso_header a LEFT JOIN omdcmobile.cso_details b ON a.cso_no = b.cso_no AND a.branch = b.branch LEFT JOIN contact_info c ON a.customer_code = c.file_id WHERE a.branch = '$_SESSION[branchid]' AND customer_code = '$_POST[cid]' AND b.billed != 'Y' AND b.description NOT IN (SELECT DISTINCT pid FROM soa_details WHERE soa_no = '$_POST[soa_no]' AND branch = '$_SESSION[branchid]') AND `status` = 'Finalized' $s ORDER BY a.cso_no ASC, b.pname ASC;";
            
            if($con->countRows($q) > 0) {
                echo "
                    <form name=\"frmFetchedSO\" id=\"frmFetchedSO\">
                    <table width=100% cellspacing=0 cellpadding=0>
                        <tr>
                            <td colspan=7 align=right style=\"padding: 4px;\">
                                <input type=\"text\" name=\"so_search\" id=\"so_search\" class=\"gridInput\" style=\"width: 200px;\" value=\"$_POST[stxt]\">
                                <button type = \"button\" name = \"setPrintLab\" class=\"ui-button ui-widget ui-corner-all\" onClick=\"javascript: searchBrowseCSO();\">
                                    <span class=\"ui-icon ui-icon-search\"></span> Search
                                </button>	
                            </td>
                        </tr>

                        <tr>
                            <td class=gridHead width=10%>CSO #</td>
                            <td class=gridHead width=10%>DATE</td>
                            <td class=gridHead width=25%>PATIENT</td>
                            <td class=gridHead width=25%>BILLED TO</td>
                            <td class=gridHead >REMARKS</td>
                            <td class=gridHead width=15% align=right>AMOUNT</td>
                            <td class=gridHead width=20>&nbsp;</td>
                        </tr>";

                $soQuery = $con->dbquery($q); $i=0;
                while($soRow = $soQuery->fetch_array()) {
                    echo "<tr bgcolor='".$con->initBackground($i)."'>
                            <td class=grid width=10%>".$soRow['sono']."</td>
                            <td class=grid width=10%>".$soRow['sdate']."</td>
                            <td class=grid width=25%>".$soRow['patient_name']."</td>
                            <td class=grid >".$soRow['charge_to']."</td>
                            <td class=grid >".$soRow['service']."</td>
                            <td class=grid width=15% align=right>".number_format($soRow['amount'],2)."</td>
                            <td class=grid width=20 align=center><input type=checkbox name=\"so[]\" id=\"so[]\" value='".$soRow['lid']."'></td>
                        </tr>
                    
                    ";
                    $i++;

                }
                 echo "</table>  
                </form>";


            }

        break;


        case "uploadCSO":

            $arr = $_POST['so'];
            foreach($arr as $lid) {
                $csoRow = $con->getArray("select * from omdcmobile.cso_details where line_id = '$lid';");
                list($unit) = $con->getArray("select unit from services_master where `code` = '$csoRow[code]';");
                list($csodate) = $con->getArray("select cso_date from omdcmobile.cso_header where cso_no = '$csoRow[cso_no]';");

                $con->dbquery("INSERT IGNORE INTO soa_details (soa_no,branch,so_type,so_no,so_date,pid,pname,`code`,description,unit,qty,unit_price,amount,trace_no) VALUES ('$_POST[soa_no]','1','CSO','$csoRow[cso_no]','$csodate','$csoRow[pid]','$csoRow[pname]','$csoRow[code]','$csoRow[description]','$unit','$csoRow[qty]','$csoRow[unit_price]','$csoRow[amount]','$_POST[trace_no]');");
                $con->dbquery("UPDATE IGNORE omdcmobile.cso_details SET billed = 'Y', soa_no = '$_POST[soa_no]' WHERE line_id = '$lid';");
            }

            updateAmount($_POST['soa_no']);

        break;

        case "browsePharmaSO":

            $s = '';

			if($_POST['cid'] != '') { $s .= " and a.customer_code = '$_POST[cid]' "; }
			if($_POST['stxt'] != '') {
				$s .= " and (a.so_no = '$_POST[stxt]' || a.patient_name like '%$_POST[stxt]%') ";
			}

            $q = "SELECT LPAD(a.so_no,6,0) AS sono, DATE_FORMAT(so_date,'%m/%d/%Y') AS sdate, b.line_id AS lid, a.patient_name AS patient_name, a.customer_name AS charge_to, b.description AS `service`,b.unit_price, b.qty, b.discount, b.amount_due, a.remarks FROM pharma_so_header a LEFT JOIN pharma_so_details b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN contact_info c ON a.customer_code = c.file_id WHERE a.branch = '$_SESSION[branchid]' AND customer_code = '$_POST[cid]' AND b.billed != 'Y' AND b.line_id NOT IN (SELECT line_id FROM soa_details WHERE soa_no = '$_POST[soa_no]' AND branch = '$_SESSION[branchid]') AND `status` = 'Finalized' $s ORDER BY a.so_no DESC, a.patient_name ASC;";
            
            if($con->countRows($q) > 0) {
                echo "
                    <form name=\"frmFetchedSO\" id=\"frmFetchedSO\">
                    <table width=100% cellspacing=0 cellpadding=0>
                        <tr>
                            <td colspan=10 align=right style=\"padding: 4px;\">
                                <input type=\"text\" name=\"so_search\" id=\"so_search\" class=\"gridInput\" style=\"width: 200px;\" value=\"$_POST[stxt]\">
                                <button type = \"button\" name = \"setPrintLab\" class=\"ui-button ui-widget ui-corner-all\" onClick=\"javascript: searchBrowsePharmaSO();\">
                                    <span class=\"ui-icon ui-icon-search\"></span> Search
                                </button>	
                            </td>
                        </tr>

                        <tr>
                            <td class=gridHead width=5%>SO #</td>
                            <td class=gridHead width=5%>DATE</td>
                            <td class=gridHead width=15%>PATIENT</td>
                            <td class=gridHead width=20%>BILLED TO</td>
                            <td class=gridHead width=20%>PRODUCT</td>
                            <td class=gridHead width=10%>UNIT PRICE</td>
                            <td class=gridHead width=5%>QTY</td>
                            <td class=gridHead width=5%>DISC.</td>
                            <td class=gridHead width=20%>REMARKS</td>
                            <td class=gridHead width=15% align=right>AMOUNT</td>
                            <td class=gridHead width=20%>&nbsp;</td>
                        </tr>";

                $soQuery = $con->dbquery($q); $i=0;
                while($soRow = $soQuery->fetch_array()) {
                    echo "<tr bgcolor='".$con->initBackground($i)."'>
                            <td class=grid width=5%>".$soRow['sono']."</td>
                            <td class=grid width=5%>".$soRow['sdate']."</td>
                            <td class=grid width=15%>".$soRow['patient_name']."</td>
                            <td class=grid >".$soRow['charge_to']."</td>
                            <td class=grid >".$soRow['service']."</td>
                            <td class=grid >".$soRow['unit_price']."</td>
                            <td class=grid >".$soRow['qty']."</td>
                            <td class=grid >".$soRow['discount']."</td>
                            <td class=grid width=20%>".$soRow['remarks']."</td>
                            <td class=grid width=15% align=right>".number_format($soRow['amount_due'],2)."</td>
                            <td class=grid width=20% align=center><input type=checkbox name=\"so[]\" id=\"so[]\" value='".$soRow['lid']."'></td>
                        </tr>
                    
                    ";
                    $i++;

                }
                 echo "</table>  
                </form>";


            }

        break;


        case "uploadPharmaSO":

            $arr = $_POST['so'];
            foreach($arr as $lid) {
                $csoRow = $con->getArray("select * from pharma_so_details where line_id = '$lid';");
                list($unit) = $con->getArray("select unit from pharma_master where unit = '$csoRow[unit]';");
                list($csodate) = $con->getArray("select so_date from pharma_so_header where so_no = '$csoRow[so_no]';");
                list($pid,$pname) = $con->getArray("select pid, patient_name from pharma_so_header where so_no = '$csoRow[so_no]';");

                $con->dbquery("INSERT IGNORE INTO soa_details (soa_no,branch,so_type,so_no,so_date,pid,pname,`code`,description,unit,qty,unit_price,amount,trace_no) VALUES ('$_POST[soa_no]','1','PHARMA','$csoRow[so_no]','$csodate','$pid','$pname','$csoRow[code]','$csoRow[description]','$unit','$csoRow[qty]','$csoRow[unit_price]','$csoRow[amount_due]','$_POST[trace_no]');");
                $con->dbquery("UPDATE IGNORE pharma_so_details SET billed = 'Y', soa_no = '$_POST[soa_no]' WHERE line_id = '$csoRow[line_id]';");
            }

            updateAmount($_POST['soa_no']);

        break;


        case "deleteItem":

            $csoRow = $con->getArray("select * from soa_details where line_id = '$_POST[lid]';");
            $soPharma = $con->getArray("select * from pharma_so_details where line_id = '$_POST[lid]';");
            
            if($csoRow['so_type'] == 'CSO') {
                $con->dbquery("UPDATE omdcmobile.cso_details SET billed = 'N', soa_no = '' where cso_no = '$csoRow[so_no]' and pid = '$csoRow[pid]';");
            }
            if($csoRow['so_type'] == 'PHARMA') {
                $con->dbquery("UPDATE pharma_so_details SET billed = 'N' where so_no = '$csoRow[so_no]' and branch = '1' and line_id = '$_POST[lid]';");
            }

            $con->dbquery("delete from soa_details where line_id = '$_POST[lid]';");
            updateAmount($_POST['soa_no']);
        break;

        case "deleteAllitem":

            $csoRow = $con->getArray("select * from soa_details where soa_no = '$_POST[soa_no]';");
            
            if($csoRow['so_type'] == 'SO') {
                $con->dbquery("UPDATE IGNORE so_header SET billed = 'N', soa_no = '' where so_no = '$csoRow[so_no]' and branch = '1'  and soa_no = '$csoRow[soa_no]';");
            }

            if($csoRow['so_type'] == 'CSO') {
                $con->dbquery("UPDATE IGNORE omdcmobile.cso_details SET billed = 'N', soa_no = '' where cso_no = '$csoRow[so_no]' and branch = '1' and soa_no = '$csoRow[soa_no]';");
            }
            if($csoRow['so_type'] == 'PHARMA') {
                $con->dbquery("UPDATE IGNORE pharma_so_details SET billed = 'N', soa_no = '' where so_no = '$csoRow[so_no]' and branch = '1' and soa_no = '$csoRow[soa_no]';");
            }

            $con->dbquery("delete from soa_details where soa_no = '$_POST[soa_no]';");
            updateAmount($_POST['soa_no']);
        break;

        case "check4print":
			list($a) = $con->getArray("select count(*) from soa_header where soa_no = '$_POST[soa_no]' and branch = '$_SESSION[branchid]';");
			list($b) = $con->getArray("select count(*) from soa_details where soa_no = '$_POST[soa_no]' and branch = '$_SESSION[branchid]';");
			
			if($a == 0 && $b > 0) { echo "head"; }
			if($b == 0 && $a > 0) { echo "det"; }
			if($a == 0 && $b == 0) { echo "both"; }
			if($a > 0 && $b > 0) { echo "noerror"; }
		break;

        case "finalize":
            $con->dbquery("update ignore soa_header set `status` = 'Finalized', updated_by = '$_SESSION[userid]', updated_on = now() where soa_no = '$_POST[soa_no]' and branch = '$_SESSION[branchid]';");
            updateAmount($_POST['soa_no']);

            /* Update SO Status */
            $sQuery = $con->dbquery("SELECT DISTINCT so_no FROM soa_details WHERE soa_no = '$_POST[soa_no]' AND branch = '$_SESSION[branchid]';");
            while($sRow = $sQuery->fetch_array()) {
                $con->dbquery("update so_header set billed = 'Y', soa_no = '$_POST[soa_no]' where so_no = '$sRow[so_no]' and branch = '$_SESSION[branchid]';");
            }

        break;

        case "checkPayment":
            echo json_encode($con->getArray("select amount_paid from soa_header where soa_no = '$_POST[soa_no]';"));
        break;

        case "reopen":
            $con->dbquery("update ignore soa_header set status = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where soa_no = '$_POST[soa_no]' and branch = '$_SESSION[branchid]';");
        
             /* Update SO Status */
             $sQuery = $con->dbquery("SELECT DISTINCT so_no FROM soa_details WHERE soa_no = '$_POST[soa_no]' AND branch = '$_SESSION[branchid]';");
             while($sRow = $sQuery->fetch_array()) {
                 $con->dbquery("update so_header set billed = 'N', soa_no = NULL where so_no = '$sRow[so_no]' and branch = '$_SESSION[branchid]';");
             }
        
        break;

    }

?>