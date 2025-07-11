<?php
	session_start();
	
	//ini_set("display_errors","On");
	require_once "handlers/_rrfunct.php";
	$p = new myRR;
	$bid = $_SESSION['branchid'];

	switch($_POST['mod']) {

		case "saveHeader":
			if($_POST['rr_no'] != '') {
				$rr_no = $_POST['rr_no'];
				$s = "update ignore rr_header set supplier = '$_POST[cid]', supplier_name = '".$p->escapeString(htmlentities($_POST['cname']))."', supplier_addr = '".$p->escapeString(htmlentities($_POST['addr']))."', received_by = '".$p->escapeString($_POST['recby'])."', rr_date = '".$p->formatDate($_POST['rr_date'])."', invoice_no='$_POST[ino]', invoice_date='".$p->formatDate($_POST['idate'])."', remarks = '".$p->escapeString(htmlentities($_POST['remarks']))."', updated_by = '$_SESSION[userid]', updated_on = now() where rr_no = '$_POST[rr_no]' and branch = '$bid';";
			} else {
				list($rr_no) = $p->getArray("select lpad((ifnull(max(rr_no),0)+1),6,0) from rr_header where branch = '1';"); 
				$s = "insert ignore into rr_header (branch, rr_no, rr_date, supplier, supplier_name, supplier_addr, received_by, remarks, created_by, created_on, trace_no) values ('$bid','$rr_no','".$p->formatDate($_POST['rr_date'])."','$_POST[cid]','".$p->escapeString(htmlentities($_POST['cname']))."','".$p->escapeString(htmlentities($_POST['addr']))."','".$p->escapeString($_POST['recby'])."','".$p->escapeString(htmlentities($_POST['remarks']))."','$_SESSION[userid]',now(),'$_POST[trace_no]');";
			}
			echo $rr_no;
			$p->dbquery($s);
		break;

		case "checkDuplicateInvoice":
			list($isCount) = $p->getArray("select count(*) from rr_header where supplier = trim(leading 0 from '$_POST[cust]') and invoice_no = '$_POST[ref_no]' and branch = '$bid';");
			if($isCount > 0) { 
				$q = $p->getArray("select rr_no, date_format(rr_date,'%m/%d/%y') as rdate from rr_header where supplier = trim(leading 0 from '$_POST[cust]') and invoice_no = '$_POST[ref_no]' and branch = '$bid' limit 1;");
				echo json_encode(array("err_msg" => "DUP", "rr_no" => $q['rr_no'], "rr_date" => $q['rdate']));
			} else {
				echo json_encode(array("err_msg" => "OK"));
			}
		break;

		case "insertDetail":
			$p->dbquery("insert ignore into rr_details (branch,rr_no,po_no,po_date,item_code,description,qty,unit,cost,amount) values ('$bid','$_POST[rr_no]','$_POST[po_no]','".$p->formatDate($_POST[po_date])."','$_POST[icode]','".$p->escapeString($_POST['desc'])."','".$p->formatDigit($_POST['qty'])."','$_POST[unit]','".$p->formatDigit($_POST['price'])."','".$p->formatDigit($_POST['amount'])."');");
			$p->updateHeaderAmt($_POST['rr_no'],$bid);
		break;

		case "updateItem":
			$p->dbquery("UPDATE rr_details set costcenter = '$_POST[costcenter]', po_no = '$_POST[po]', po_date = '".$p->formatDate($_POST['podate'])."', item_code = '$_POST[item]',description='".$p->escapeString(htmlentities($_POST['description']))."',qty = '".$p->formatDigit($_POST['qty'])."',unit = '$_POST[unit]',cost = '".$p->formatDigit($_POST['cost'])."',amount = '".$p->formatDigit($_POST['amount'])."' where line_id = '$_POST[lid]';");
			$p->updateHeaderAmt($_POST['rr_no'],$bid);
		break;

		case "getPOS":
			list($b) = $p->getArray("select count(*) from (select a.po_no, date_format(a.po_date,'%m/%d/%Y') as pd8, a.remarks, ROUND(sum(b.cost * (b.qty-b.qty_dld)),2) as amount from po_header a left join po_details b on a.po_no=b.po_no and a.branch=b.branch where b.qty_dld < b.qty and a.supplier = trim(leading '0' from '$_POST[cid]') and a.status = 'Finalized' and a.po_no not in (select distinct po_no from rr_details where rr_no = '$_POST[rr_no]' and branch = '$bid') group by a.po_no) a;");
			if($b > 0) {
				echo "<table width=100% cellpadding=2 cellspacing=0>
						<tr>
							<td class=gridHead width=15%>PO #</td>
							<td class=gridHead align=center width=15%>PO DATE</td>
							<td class=gridHead>DETAILS</td>
							<td class=gridHead align=right width=15%>AMOUNT</td>
							<td class=gridHead width=10>&nbsp;</td>
						</tr>
					";

				$c = $p->dbquery("select concat(lpad(a.branch,2,'0'),'-',lpad(a.po_no,6,0)) as po, a.po_no, a.po_date, a.branch, date_format(a.po_date,'%m/%d/%Y') as pd8, a.remarks, ROUND(sum(b.cost * (b.qty-b.qty_dld)),2) as amount from po_header a left join po_details b on a.po_no=b.po_no and a.branch=b.branch where b.qty_dld < b.qty and a.supplier = trim(leading '0' from '$_POST[cid]') and a.status = 'Finalized' and a.po_no not in (select distinct po_no from rr_details where rr_no = '$_POST[rr_no]' and branch = '$bid') group by a.po_no;");
				
				$i = 0;
				while($d = $c->fetch_array(MYSQLI_BOTH)) {
					$checked = "";
					$needle = $d['po_no']."|".$d['po_date']."|".$d['branch'];
					if(isset($_SESSION['ques'])) {
						if(in_array($needle, $_SESSION['ques'])) {
							$checked = "checked"; 
						}
					}

					echo "<tr bgcolor='".$p->initBackground($i)."'>
							<td class=grid valign=top>&nbsp;&nbsp;$d[po]</td>
							<td class=grid align=center valign=top>$d[pd8]</td>
							<td class=grid valign=top>$d[remarks]</td>
							<td class=grid align=right valign=top>".number_format($d['amount'],2)."</td>
							<td valign=top><input type='checkbox' id='$d[po_no]' value='$needle' onclick='tagPO(this.id,this.value);' $checked></td>
						</tr>"; $i++;
				}
				if($i < 5) {
						for($i;$i <=5; $i++) {
							echo "<tr  bgcolor='".$p->initBackground($i)."'><td colspan=6 class=grid>&nbsp;</td></tr>";
						}
					}
				echo "</table>";
			}
		break;

		case "tagPO":
			$val = array();
			$push = $_REQUEST['push'];
			array_push($val,$_REQUEST['val']);
			if(!isset($_SESSION['ques'])) { $_SESSION['ques'] = array(); }
			if($push == 'Y') { if(array_search($val[0],$_SESSION['ques'])==0) { array_push($_SESSION['ques'],$val[0]); }
			} else { $_SESSION['ques'] = array_diff($_SESSION['ques'],$val); }
		break;

		case "loadPO":
			if(count($_SESSION['ques']) > 0) {
				foreach($_SESSION['ques'] as $index) {
					$subindex = explode("|",$index);
					list($po_no,$po_date,$po_branch) = $subindex;	
					$opo = $p->dbquery("select b.costcenter, a.branch, item_code, description, (qty-qty_dld) as qty, unit, (b.cost-b.discount) as cost, ROUND((b.cost-b.discount) * (qty-qty_dld),2) as amount from po_header a left join po_details b on a.po_no = b.po_no and a.branch = b.branch where a.po_no = '$po_no' and a.branch = '$po_branch' and (qty-qty_dld) > 0;");
					while($op = $opo->fetch_array()) {
						$p->dbquery("insert ignore into rr_details (branch,rr_no,costcenter,po_no,po_date,po_branch,item_code,description,qty,unit,cost,amount,trace_no) values ('$bid','$_POST[rr_no]','$op[costcenter]','$po_no','$po_date','1','$op[item_code]','".$p->escapeString($op['description'])."','$op[qty]','$op[unit]','$op[cost]','$op[amount]','$_POST[trace_no]');");
					}
				}

				$p->updateHeaderAmt($_POST['rr_no'],$bid);
				unset($_SESSION['ques']);
			}
		break;

		case "deleteLine":
			$p->dbquery("delete from rr_details where line_id = '$_POST[lid]';");
			$p->updateHeaderAmt($_POST['rr_no'],$bid);
		break;
		
		case "check4print":
			list($a) = $p->getArray("select count(*) from rr_header where rr_no = '$_POST[rr_no]' and branch = '$bid';");
			list($b) = $p->getArray("select count(*) from rr_details where rr_no = '$_POST[rr_no]' and branch = '$bid';");
			
			if($a == 0 && $b > 0) { echo "head"; }
			if($b == 0 && $a > 0) { echo "det"; }
			if($a == 0 && $b == 0) { echo "both"; }
			if($a > 0 && $b > 0) { echo "noerror"; }
		break;
		
		case "getTotals":
			list($amt) = $p->getArray("select sum(amount) from rr_details where rr_no = '$_POST[rr_no]' and branch = '$bid';");
			echo json_encode(array("amt"=>number_format($amt,2)));
		break;
		
		case "finalizeRR":
			$p->dbquery("update rr_header set status = 'Finalized', updated_by = '$_SESSION[userid]', updated_on = now() where rr_no ='$_POST[rr_no]' and branch = '$bid';");
			$d = $p->dbquery("select po_no, po_branch, costcenter, item_code, qty from rr_details where rr_no = '$_POST[rr_no]' and branch = '$bid';");
			while($e = $d->fetch_array(MYSQLI_BOTH)) {
				$p->dbquery("update po_details set qty_dld = qty_dld + $e[qty] where po_no = '$e[po_no]' and item_code = '$e[item_code]' and costcenter = '$e[costcenter]' and branch = '$e[po_branch]';");
			}
			
			$iquery = $p->dbquery("SELECT a.branch, a.rr_no AS doc_no, a.rr_date AS doc_date, a.supplier, a.supplier_name, b.item_code, b.unit, b.qty FROM rr_header a INNER JOIN rr_details b ON a.rr_no = b.rr_no AND a.branch = b.branch WHERE a.rr_no = '$_POST[rr_no]' AND a.branch = '$bid';");
			while($ibook = $iquery->fetch_array()) {
				$p->dbquery("INSERT IGNORE INTO ibook (doc_no,doc_date,doc_type,doc_branch,ccode,cname,item_code,uom,purchases,posted_by,posted_on) VALUES ('$ibook[doc_no]','$ibook[doc_date]','RR','$ibook[branch]','$ibook[supplier]','".$p->escapeString($ibook['supplier_name'])."','$ibook[item_code]','$ibook[unit]','$ibook[qty]','$_SESSION[userid]',now());");
			}
		break;
		
		case "reopenRR":
			$p->dbquery("update rr_header set status = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where rr_no = '$_POST[rr_no]' and branch = '$bid';");
			$p->dbquery("delete from ibook where doc_type = 'RR' and doc_no = '$_POST[rr_no]' and doc_branch = '$bid';");
			$f = $p->dbquery("select po_no, po_branch, item_code, qty from rr_details where rr_no = '$_POST[rr_no]' and branch = '$bid';");
			while($g = $f->fetch_array(MYSQLI_BOTH)) {
				$p->dbquery("update po_details set qty_dld = qty_dld - $g[qty] where po_no = '$g[po_no]' and item_code = '$g[item_code]' and branch = '$g[po_branch]';");
			}
		break;
		
		case "cancel":
			$p->dbquery("update rr_header set status = 'Cancelled', updated_by = '$_SESSION[userid]', updated_on = now() where rr_no = '$_POST[rr_no]' and branch = '$bid';");
		break;

		case "retrieve":
			$data = array();
	
			$srrd = $p->dbquery("SELECT line_id AS id, a.costcenter, b.costcenter AS cc, LPAD(po_no,6,0) AS po, IF(po_date!='0000-00-00',DATE_FORMAT(po_date,'%m/%d/%Y'),'') AS podate, description, item_code, unit, qty, cost, amount FROM rr_details a LEFT JOIN options_costcenter b ON a.costcenter = b.unitcode where trace_no = '$_REQUEST[trace_no]';");
			while($row = $srrd->fetch_array()) {

				$data[] = array_map('utf8_encode',$row);
			}
			
			$results = ["sEcho" => 1,"iTotalRecords" => count($data),"iTotalDisplayRecords" => count($data),"aaData" => $data];
			echo json_encode($results);	
		break;
	}

?>