<?php
	session_start();
	require_once "handlers/_jvfunct.php";

	$p = new myJV;
	$j_no = ltrim($_POST['j_no'],0);

	switch($_POST['mod']){
		case "saveHeader":
			//list($isE) = $p->getArray("select count(*) from journal_header where j_no = '$j_no';");
			if($_POST['j_no'] !='') {
				$p->dbquery("update journal_header set cy='".$p->formatCY($_POST['j_date'])."', j_date = '".$p->formatDate($_POST['j_date'])."', ca_refno = '$_POST[ca_refno]', ca_date = '".$p->formatDate($_POST['ca_date'])."', explanation = '".$p->escapeString($_POST['remarks'])."', updated_by='$_SESSION[userid]', updated_on = now() where j_no = '$j_no' and branch = '1';");
			} else {
				list($j_no) = $p->getArray("select ifnull(max(j_no),0)+1 from journal_header where branch = '1';"); 
				$p->dbquery("insert ignore into journal_header (branch,cy,j_no, j_date, ca_refno, ca_date, explanation, created_by,created_on) values ('1','".$p->formatCY($_POST['j_date'])."','$j_no','".$p->formatDate($_POST['j_date'])."','$_POST[ca_refno]','".$p->formatDate($_POST['ca_date'])."','".$p->escapeString($_POST['remarks'])."','$_SESSION[userid]',now());");
			}
			
			echo str_pad($j_no,6,0,STR_PAD_LEFT);
			
		break;
		case "insertDetail":
			if($_POST['dc'] == "DB") { $db = $p->formatDigit($_POST['amount']); $cr = 0; } else { $cr = $p->formatDigit($_POST['amount']); $db = 0; }
			if($_POST['ref_date'] == "" || $_POST['ref_no'] == "") { $_POST['ref_no'] = $j_no; $_POST['ref_date'] = $_POST['j_date']; $_POST['ref_type'] = 'JV'; }
			
			$p->dbquery("insert ignore into journal_details (branch,cy,j_no,ref_no,ref_date,ref_type,client,acct,acct_desc,cost_center,debit,credit,balance) values ('1','".$p->formatCY($_POST['j_date'])."','$j_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','$_POST[ref_type]','$_POST[cid]','$_POST[acode]','".$p->escapeString($p->getAcctDesc($_POST[acode],$_SESSION['company']))."','$_POST[ccenter]','$db','$cr','".$p->formatDigit($_POST['amount'])."');");
			$p->JVDETAILS($j_no);
		break;
		case "saveAppliedDoc":
			if($_POST['side'] == "DB") { $side = "debit"; } else { $side = "credit"; }
			$p->dbquery("insert ignore into journal_details (branch,cy,j_no,ref_no,ref_date,ref_type,client,acct,acct_desc,`$side`,trace_no,applied_lid) values ('1','".$p->formatCY($_POST[j_date])."','$j_no','$_POST[ref_no]','".$p->formatDate($_POST[ref_date])."','$_POST[ref_type]','$_POST[client]','$_POST[acct]','".$p->escapeString($p->getAcctDesc($_POST[acct],$_SESSION['company']))."','".$p->formatDigit($_POST[amount])."','$_POST[trace_no]','$_POST[lid]');");
			$p->JVDETAILS($j_no);
		break;
		case "addInvoice":
			if($_POST['isVat'] == "Y") { $vat = ROUND(($_POST['amount'] / 1.12) * 0.12,2); }
			$net = $_POST['amount'] - $vat;
			if($_POST['atc'] != "") { list($ewt,$erate) = $p->getArray("select ROUND($net * (rate/100),2) as ewt,rate from options_atc where atc_code = '$_POST[atc]';"); }
			$payable = $_POST['amount'] - $ewt;
			
			if($ewt > 0) { $p->dbquery("insert ignore into journal_details (branch,cy,j_no,ref_no,ref_date,ref_type,acct,acct_desc,credit) values ('1','".$p->formatCY($_POST['j_date'])."','$j_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','SI','30207','".$p->getAcctDesc("30207",1)."','$ewt');"); }
			if($vat > 0) { $p->dbquery("insert ignore into journal_details (branch,cy,j_no,ref_no,ref_date,ref_type,acct,acct_desc,debit) values ('1','".$p->formatCY($_POST['j_date'])."','$j_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','SI','30208','".$p->getAcctDesc("30208",1)."','$vat');"); }
			if($_POST['dbAcct'] != '') {
				$p->dbquery("insert ignore into journal_details (branch,cy,j_no,ref_no,ref_date,ref_type,acct,acct_desc,debit) values ('1','".$p->formatCY($_POST['j_date'])."','$j_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','SI','".$_POST['dbAcct']."','".$p->getAcctDesc($_POST['dbAcct'],1)."','$net');");
			}

			$p->dbquery("insert ignore into journal_invoices (branch,cy,j_no,supplier,supplier_name,supplier_address,supplier_tin,invoice_no,invoice_date,net_payable,input_vat,ewt_code,ewt_rate,ewt_amount) values ('1','".$p->formatCY($_POST['j_date'])."','$j_no','$_POST[supplier]','".$p->escapeString($_POST['sname'])."','".$p->escapeString($_POST['saddr'])."','$_POST[stin]','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','$payable','$vat','$_POST[atc]','$erate','$ewt');");
			$p->JVDETAILS($j_no);
		break;
		case "deleteInvoice":
			$p->dbquery("delete from journal_details where j_no = '$j_no' and ref_no = '$_POST[ref_no]' and ref_type = '$_POST[ref_type]' and branch = '1';");
			$p->dbquery("delete from journal_invoices where j_no = '$j_no' and invoice_no = '$_POST[ref_no]' and branch = '1';");
			$p->JVDETAILS($j_no,$status='Active',$lock='N');
		break;
		case "deleteLine":
			$p->dbquery("delete from journal_details where record_id = '$_POST[lid]';");
			$p->JVDETAILS($j_no);
		break;
		case "iccenter":
			$uLoop = $p->dbquery("select unitcode, costcenter from options_costcenter");
			$option = "<select class='gridInput' style='width: 95%' onblur='ichangeNa(this.value,$_POST[lid]);'><option value='...'>- Cost Center -</option>";
			while(list($pid,$pname) = $uLoop->fetch_array()) {
				$option = $option ."<option value='$pid' ";
				if($_POST['cc'] == $pid) { $option = $option . "selected"; }
				$option = $option . ">".strtoupper($pname)."</option>";
			}
				$option = $option . "</select>";
				echo $option;
		break;
		case "ichangeNa":
			if($_POST['val'] == '...') { $_POST['val'] = ""; echo "..."; } else { echo $p->identCostCenter($_POST['val']); }
			$p->dbquery("update ignore journal_details set cost_center = '$_POST[val]' where record_id = '$_POST[lid]';");
		break;
		case "check4print":
			list($aaa) = $p->getArray("select count(*) from journal_details where j_no = '$j_no' and branch = '1';");
			if($aaa > 0) {
				list($db,$cr) = $p->getArray("select sum(debit), sum(credit) from journal_details where j_no = '$j_no' and branch = '1';");
				if($db!=$cr) { echo "DiBalanse"; } else {
					$p->dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct,debit,credit,acct_branch,ref_no,ref_date,ref_type,cost_center,doc_remarks,posted_by,posted_on) SELECT a.branch,a.cy, a.j_no AS doc_no, a.j_date AS doc_date, 'JV' AS doc_type, b.client AS contact_id, b.acct, SUM(debit) AS debit, SUM(credit) AS credit, '1' as acct_branch, b.ref_no, b.ref_date, b.ref_type, b.cost_center,a.explanation AS doc_remarks,'$_SESSION[userid]',NOW() FROM journal_header a LEFT JOIN journal_details b ON a.j_no=b.j_no and a.branch=b.branch WHERE a.branch='1' and a.j_no = '$j_no' AND `status` != 'Posted' group by b.cost_center, b.acct, b.ref_no, b.ref_type, b.client;");
					$p->dbquery("update ignore journal_header set status = 'Posted', updated_by = '$_SESSION[userid]', updated_on = now() where j_no = '$j_no' and branch = '1';");
					
					$xx = $p->dbquery("select ref_no, ref_type, `client`, acct, abs(debit-credit) as xamount, applied_lid as lid from journal_details where j_no = '$j_no' and ref_type != 'JV' and ref_no != '$j_no';");
					while($xxx = $xx->fetch_array(MYSQLI_BOTH)) {$p->applyBalanceNa($_SESSION['company'],$_SESSION['branchid'],$xxx['ref_no'],$xxx['ref_type'],$xxx['acct'],$xxx['xamount'],$xxx['client'],$xxx['lid']);	}
					echo "noerror";
				}
			} else { echo "waySulod"; }
		break;
		case "checkB4Reopen":
			list($isCleared) = $p->getArray("select count(*) from acctg_gl where doc_no = '$j_no' and doc_type = 'JV' and branch = '1' and cleared = 'Y';");
			if($isCleared > 0) { echo "notOk"; } else { echo "Ok"; }
		break;
		case "reopenJV":
			$p->dbquery("delete from acctg_gl where doc_no = '$j_no' and doc_type = 'JV' and branch = '1';");
			$p->dbquery("update ignore journal_header set status = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where j_no = '$j_no' and branch = '1';");
			$xx = $p->dbquery("select ref_no, ref_type, `client`, acct, abs(debit-credit) as xamount, applied_lid as lid from journal_details where j_no = '$j_no' and ref_type != 'JV' and ref_no != '$j_no' and branch = '1';");
			while($xxx = $xx->fetch_array(MYSQLI_BOTH)) { $p->revertBalance($_SESSION['company'],$_SESSION['branchid'],$xxx['ref_no'],$xxx['ref_type'],$xxx['acct'],$xxx['xamount'], $xxx['client'], $xxx['lid']);	}
		break;
		case "cancel":
			$p->dbquery("update journal_header set status = 'Cancelled' where j_no = '$j_no' and branch = '1';");
		break;
		case "unlinkJV":
			$p->dbquery("update journal_header set linked = 'N', doc_no = '', doc_type = '' where j_no = '$j_no' and branch = '1';");
		break;
		case "getTotals":
			$a = $p->getArray("select count(*) as `lines`, format(sum(debit),2) as db, format(sum(credit),2) as cr from journal_details where j_no = '$j_no' and branch = '1' group by j_no;");
			if($a[0] != '') { echo json_encode($a); } else { echo json_encode(array('line'=>'0','cr'=>'0.00','db'=>'0.00')); }
		break;
	}

?>