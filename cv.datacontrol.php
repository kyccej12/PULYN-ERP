<?php
	session_start();
	
	//ini_set("display_errors","On");
	require_once "handlers/_cvfunct.php";

	$p = new myCV;
	$cv_no = ltrim($_POST['cv_no'],0);

	switch($_POST['mod']) {
		case "saveHeader":
			list($a) = $p->getArray("select count(*) from cv_header where cv_no = '$_POST[cv_no]' and branch = '$_SESSION[branchid]';");
			if($a > 0) {
				$p->dbquery("update ignore cv_header set cy='".$p->formatCY($_POST['cv_date'])."', cv_date='".$p->formatDate($_POST['cv_date'])."', payee='$_POST[ccode]', payee_name='".$p->escapeString(htmlentities($_POST['cname']))."', payee_addr = '".$p->escapeString(htmlentities($_POST['address']))."', source = '$_POST[source]', check_no='$_POST[check_no]', check_date='".$p->formatDate($_POST['check_date'])."', ca_refno = '$_POST[ca_refno]', ca_date = '".$p->formatDate($_POST['ca_date'])."', remarks='".$p->escapeString($_POST['remarks'])."',updated_by='$_SESSION[userid]', updated_on=now() where cv_no='$cv_no' and branch = '$_SESSION[branchid]';");
			} else {
				$p->dbquery("insert ignore into cv_header (cy,branch,cv_no,cv_date,payee,payee_name,payee_addr,source,check_no,check_date,ca_refno,ca_date,remarks,created_by,created_on) values ('".$p->formatCY($_POST['cv_date'])."','$_SESSION[branchid]','$cv_no','".$p->formatDate($_POST['cv_date'])."','$_POST[ccode]','".$p->escapeString(htmlentities($_POST['cname']))."','".$p->escapeString(htmlentities($_POST['address']))."','$_POST[source]','$_POST[check_no]','".$p->formatDate($_POST['check_date'])."','$_POST[ca_refno]','".$p->formatDate($_POST['ca_date'])."','".$p->escapeString($_POST['remarks'])."','$_SESSION[userid]',now());");
			}
		break;
		case "getInvoices":
			list($b) = $p->getArray("select count(*) from (select apv_no as doc_no from apv_header where supplier = trim(leading '0' from '$_POST[cid]') and balance > 0 and `status` = 'Posted' and apv_no not in (select distinct ref_no from cv_details where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and ref_type = 'AP') union all select b.invoice_no from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.company=b.company and a.branch=b.branch where a.company='$_SESSION[company]' and b.customer = trim(leading '0' from '$_POST[cid]') and balance > 0 and a.status = 'Posted' and b.invoice_no not in (select distinct ref_no from cv_details where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and ref_type = 'AP-BB')) a");
			if($b > 0) {
				echo "<table width=100% cellpadding=2 cellspacing=0>
						<tr>
							<td class=gridHead 10%>DOC #</td>
							<td class=gridHead align=center width=10%>DATE</td>
							<td class=gridHead width=30%>DOCUMENT REMARKS</td>
							<td class=gridHead align=right>AMOUNT</td>
							<td class=gridHead align=right>BALANCE</td>
							<td class=gridHead width=10>&nbsp;</td>
						</tr>
					";
				$i = 0; $j = 0;
					$c = $p->dbquery("select lpad(branch,3,0) as br_code, branch, 'AP' as doc_type, concat(cy,'-',lpad(apv_no,6,0)) as apv, apv_no, apv_date, date_format(apv_date,'%m/%d/%Y') as ad8, remarks, amount, balance from apv_header where supplier = trim(leading '0' from '$_POST[cid]') and balance > 0 and status = 'Posted' and concat(branch,apv_no) not in (select distinct concat(acct_branch,ref_no) from cv_details where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and ref_type = 'AP') union all select lpad(a.branch,3,0) as br_code, a.branch, 'AP-BB' as doc_type, concat(date_format(invoice_date,'%Y'),'-',invoice_no) as apv, invoice_no as apv_no, invoice_date as apv_date, date_format(invoice_date,'%m/%d/%Y') as ad8, a.explanation as remarks, b.amount, b.balance from apbeg_header a inner join apbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch where b.customer = trim(leading '0' from '$_POST[cid]') and balance > 0 and `status` = 'Posted' and b.invoice_no not in (select distinct ref_no from cv_details where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and ref_type = 'AP-BB') order by branch, apv_no;");
					while($d = $c->fetch_array(MYSQLI_BOTH)) {
					
					 $checked = "";
					$needle = $d['doc_type']."|".$d['apv_no']."|".$d['apv_date']."|".$d['balance']."|".$d['branch'];
					if(isset($_SESSION['ques'])) {
						if(in_array($needle, $_SESSION['ques'])) {
							$checked = "checked"; 
						}
					}

					//$bcode = $p->getCompany(1,$d['branch']);
					echo "<tr bgcolor='".$p->initBackground($i)."'>
							<td class=grid valign=top><a href='#' style='text-decoration: none;' onclick='parent.viewAP($d[apv_no]);'>$d[apv]</a></td>
							<td class=grid align=center valign=top>$d[ad8]</td>
							<td class=grid valign=top>$d[remarks]</td>
							<td class=grid align=right valign=top>".number_format($d['amount'],2)."</td>
							<td class=grid align=right valign=top>".number_format($d['balance'],2)."</td>
							<td valign=top><input type='checkbox' id=chk[$j] value='$needle' onclick='tagRR(this.id,this.value);' $checked></td>
						</tr>"; $i++; $j++;
				}
				if($i < 5) {
						for($i; $i <=5; $i++) {
							echo "<tr  bgcolor='".$p->initBackground($i)."'><td colspan=6 class=grid>&nbsp;</td></tr>";
						}
					}
				echo "</table>";
			}
		break;
		case "tagRR":
			$val = array();
			$push = $_REQUEST['push'];
			array_push($val,$_REQUEST['val']);
			if(!isset($_SESSION['ques'])) { $_SESSION['ques'] = array(); }
			if($push == 'Y') { if(array_search($val[0],$_SESSION['ques'])==0) { array_push($_SESSION['ques'],$val[0]); }
			} else { $_SESSION['ques'] = array_diff($_SESSION['ques'],$val); }
		break;
		case "loadAP2CV":
			if(count($_SESSION['ques']) > 0) {
				$payableGT = 0;
				foreach($_SESSION['ques'] as $index) {
					$netGT = 0; $grossGT = 0;
					$subindex = explode("|",$index);
					list($doc_type,$apv_no,$ad8,$balance,$acct_branch) = $subindex;
					$p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,debit,acct_branch) values ('$_SESSION[branchid]','".$p->formatCY($_POST['cv_date'])."','$cv_no','$apv_no','$ad8','$doc_type','30101','".$p->getAcctDesc('30101',$_SESSION['company'])."','$balance','$_SESSION[branchid]');");
					$amtGT+=$balance;
				}
				
				list($payable) = $p->getArray("select sum(debit) from cv_details where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and acct = '30101';");
				list($isCount) = $p->getArray("select count(*) from cv_details where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and acct = '$_POST[bank]';");
				if($isCount > 0) {
					$p->dbquery("update cv_details set credit = 0$payable where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and acct = '$_POST[bank]';");
				} else {
					$p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,credit,balance,acct_branch) values ('1','".$p->formatCY($_POST['cv_date'])."','$cv_no','$cv_no','".$p->formatDate($_POST['cv_date'])."','CV','$_POST[bank]','".$p->getAcctDesc($_POST['bank'],$_SESSION['company'])."','$payable','$payable','$_SESSION[branchid]');");
				}

				$p->updateHeadAmount($cv_no,$_POST['bank']);
				$p->CVDETAILS($cv_no,$status='Active',$lock='N');
				unset($_SESSION['ques']);
			}
		break;
		case "deleteInvoice":
			$p->dbquery("delete from cv_details where cv_no = '$cv_no' and ref_no = '$_POST[ref_no]' and ref_type = '$_POST[ref_type]' and branch = '$_SESSION[branchid]';");
			$p->dbquery("delete from cv_subheader where cv_no = '$cv_no' and invoice_no = '$_POST[ref_no]' and branch = '$_SESSION[branchid]';");
			list($bank) = $p->getArray("select source from cv_header where cv_no = '$cv_no' and branch = '$_SESSION[branchid]';");
			
			list($tcib) = $p->getArray("select sum(debit-credit) from cv_details where cv_no = '$cv_no' and acct != '$bank' and branch = '$_SESSION[branchid]';");
			list($bExist) = $p->getArray("select count(*) from cv_details where cv_no = '$cv_no' and acct = '$bank' and branch = '$_SESSION[branchid]';");
			if($bExist > 0) {
				$p->dbquery("update ignore cv_details set credit = 0$tcib where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and acct = '$bank';");
			} else {
				$p->dbquery("delete from cv_details where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and acct = '$bank';");
			}
			
			$p->updateHeadAmount($cv_no,$bank);
			$p->CVDETAILS($cv_no,$status='Active',$lock='N');
		break;
		case "addInvoice":
			if($_POST['isVat'] == "Y") { $vat = ROUND(($_POST['amount'] / 1.12) * 0.12,2); }
			$net = $_POST['amount'] - $vat;
			if($_POST['atc'] != "") { list($ewt,$erate,$ewtAcct) = $p->getArray("select ROUND($net * (rate/100),2) as ewt,rate,acct_code from options_atc where atc_code = '$_POST[atc]';"); }
			$payable = $_POST['amount'] - $ewt;
			
			if($ewt > 0) { $p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,credit,acct_branch) values ('1','".$p->formatCY($_POST['cv_date'])."','$cv_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','SI','30207','".$p->getAcctDesc('30207',1)."','$ewt','1');"); }
			if($vat > 0) { $p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,debit,acct_branch) values ('1','".$p->formatCY($_POST['cv_date'])."','$cv_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','SI','30208','".$p->getAcctDesc("30208",1)."','$vat','1');"); }
			list($bExist) = $p->getArray("select count(*) from cv_details where cv_no = '$cv_no' and acct = '$_POST[bank]' and branch = '$_SESSION[branchid]';");
			if($_POST['dbAcct'] != '') {
				$p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,debit,acct_branch) values ('1','".$p->formatCY($_POST['cv_date'])."','$cv_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','SI','".$_POST['dbAcct']."','".$p->getAcctDesc($_POST['dbAcct'],1)."','$net','1');");
			}
			if($bExist > 0) {
				$p->dbquery("update cv_details set credit = credit + 0$payable where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and acct = '$_POST[bank]';");
			} else {	
				$p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,credit,acct_branch) values ('1','".$p->formatCY($_POST['cv_date'])."','$cv_no','$cv_no','".$p->formatDate($_POST['cv_date'])."','CV','$_POST[bank]','".$p->escapeString($p->getAcctDesc($_POST['bank'],1))."','$payable','1');");
			}
			
			$p->dbquery("insert ignore into cv_subheader (branch,cy,cv_no,supplier,supplier_name,supplier_address,supplier_tin,invoice_no,invoice_date,net_payable,input_vat,ewt_code,ewt_rate,ewt_amount) values ('1','".$p->formatCY($_POST['cv_date'])."','$cv_no','$_POST[supplier]','".$p->escapeString($_POST['sname'])."','".$p->escapeString($_POST['saddr'])."','$_POST[stin]','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','$payable','$vat','$_POST[atc]','$erate','$ewt');");
			$p->updateHeadAmount($cv_no,$_POST['bank']);
			$p->CVDETAILS($cv_no,$status='Active',$lock='N');
		break;
		case "insertDetail":
			list($desc) = $p->getArray("select description from acctg_accounts where acct_code = '$_POST[acode]';");	
			if($_POST['dc'] == "DB") { 
				if($_POST['ref_no'] == "") { $_POST['ref_no'] = ltrim($cv_no,'0'); $_POST['ref_date'] = $_POST['cv_date']; $_POST['ref_type'] == 'CV'; }
				$p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,debit,balance,acct_branch,cost_center) values ('1','".$p->formatCY($_POST['cv_date'])."','$cv_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','$_POST[ref_type]','$_POST[acode]','".$p->escapeString($desc)."','".$p->formatDigit($_POST['amount'])."','".$p->formatDigit($_POST['amount'])."','1','$_POST[ccenter]');");
				list($tcib) = $p->getArray("select sum(debit-credit) from cv_details where cv_no = '$cv_no' and acct != '$_POST[bank]' and branch = '$_SESSION[branchid]';");
				list($bExist) = $p->getArray("select count(*) from cv_details where cv_no = '$cv_no' and acct = '$_POST[bank]' and branch = '$_SESSION[branchid]';");
				if($bExist > 0) {
					$p->dbquery("update cv_details set credit = 0$tcib where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and acct = '$_POST[bank]';");
				} else {
					$p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,credit,balance,acct_branch,cost_center) values ('1','".$p->formatCY($_POST['cv_date'])."','$cv_no','".ltrim($cv_no,'0')."','".$p->formatDate($_POST['cv_date'])."','CV','$_POST[bank]','".$p->escapeString($p->getAcctDesc($_POST['bank'],1))."','".$p->formatDigit($_POST['amount'])."','".$p->formatDigit($_POST['amount'])."','1','$_POST[ccenter]');");
				}
			} else { 
				$amt = $p->formatDigit($_POST['amount']);
				$p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,credit,balance,acct_branch,cost_center) values ('1','".$p->formatCY($_POST['cv_date'])."','$cv_no','$_POST[ref_no]','".$p->formatDate($_POST['ref_date'])."','$_POST[ref_type]','$_POST[acode]','".$p->escapeString($desc)."','$amt','$amt','1','$_POST[ccenter]');");
				if($_POST['acode'] != $_POST['bank']) {
					$p->dbquery("update cv_details set credit = credit - 0$amt where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and acct = '$_POST[bank]';");
				}
			}
			$p->dbquery("delete from cv_details where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and debit = '0' and credit = '0';");
			

			
			$p->updateHeadAmount($cv_no,$_POST['bank']);
			$p->CVDETAILS($cv_no,$status='Active',$lock='N');
		break;
		case "saveAppliedDoc":
			if($_POST['side'] == "DB") { $side = "debit"; } else { $side = "credit"; }
			$p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,`$side`,applied_lid) values ('1','".$p->formatCY($_POST['cv_date'])."','$cv_no','$_POST[ref_no]','".$p->formatDate($_POST[ref_date])."','$_POST[ref_type]','$_POST[acct]','".$p->getAcctDesc($_POST[acct])."','".$p->formatDigit($_POST[amount])."','$_POST[lid]');");
			list($tcib) = $p->getArray("select sum(debit-credit) from cv_details where cv_no = '$cv_no' and acct != '$_POST[bank]' and branch = '$_SESSION[branchid]';");
			list($bExist) = $p->getArray("select count(*) from cv_details where cv_no = '$cv_no' and acct = '$_POST[bank]' and branch = '$_SESSION[branchid]';");
			if($bExist > 0) {
				$p->dbquery("update ignore cv_details set credit = 0$tcib where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and acct = '$_POST[bank]';");
			} else {
				$p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,credit,balance,acct_branch,cost_center) values ('$_SESSION[branchid]','".$p->formatCY($_POST['cv_date'])."','$cv_no','".ltrim($cv_no,'0')."','".$p->formatDate($_POST['cv_date'])."','CV','$_POST[bank]','".$p->escapeString($p->getAcctDesc($_POST['bank'],1))."','".$p->formatDigit($_POST['amount'])."','".$p->formatDigit($_POST['amount'])."','1','$_POST[ccenter]');");
			}			
			$p->CVDETAILS($cv_no,$status='Active',$lock='N');
			$p->updateHeadAmount($cv_no,$_POST['bank']);
		break;
		case "deleteLine":
			$p->dbquery("delete from cv_details where record_id = '$_POST[lid]';");
			list($bank) = $p->getArray("select source from cv_header where cv_no = '$cv_no' and branch = '$_SESSION[branchid]';");
			
			list($tcib) = $p->getArray("select sum(debit-credit) from cv_details where cv_no = '$cv_no' and acct != '$bank' and branch = '$_SESSION[branchid]';");
			list($bExist) = $p->getArray("select count(*) from cv_details where cv_no = '$cv_no' and acct = '$bank' and branch = '$_SESSION[branchid]';");
			if($bExist > 0) {
				$p->dbquery("update ignore cv_details set credit = 0$tcib where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and acct = '$bank';");
			} else {
				$p->dbquery("delete from cv_details where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and acct = '$bank';");
			}		
			
			$p->updateHeadAmount($cv_no,$_POST['bank']);
			$p->CVDETAILS($cv_no,$status='Active',$lock='N');
		break;
		
		case "refreshDetails":
			$p->CVDETAILS($_POST['cv_no'],$status='Active',$lock='N');
		break;

		case "iccenter":
			$uLoop = $p->dbquery("select unitcode, costcenter from options_costcenter");
			$option = "<select class='gridInput' style='width: 95%' onblur='ichangeNa(this.value,$_POST[lid]);'><option value='...'>- Cost Center -</option>";
			while(list($unitcode,$costcenter) = $uLoop->fetch_array()) {
				$option = $option ."<option value='$unitcode' ";
				if($_POST['cc'] == $unitcode) { $option = $option . "selected"; }
				$option = $option . ">".strtoupper($costcenter)." [".$unitcode."]</option>";
			}
				$option = $option . "</select>";
				echo $option;
		break;
		case "ichangeNa":
			if($_POST['val'] == '...') { $_POST['val'] = ""; echo "..."; } else { echo $p->identCostCenter($_POST['val']); }
			$p->dbquery("update ignore cv_details set cost_center = '$_POST[val]' where record_id = '$_POST[lid]';");
		break;
		case "changeAP":
			echo '<input type="text" class="gridInput" style="width: 100%;text-align:right;" id="'.$_POST['lid'].'" value="'.number_format($_POST['amount'],2).'" onBlur="updateAPAmount(this.value,\''.$_POST['lid'].'\',\''.$cv_no.'\',\''.$_POST['amount'].'\');">';
		break;
		case "ichangeAngAP":
			$p->dbquery("update cv_details set debit = '".$p->formatDigit($_POST['val'])."' where record_id = '$_POST[lid]';");
			
			list($tcib) = $p->getArray("select sum(debit-credit) from cv_details where cv_no = '$cv_no' and acct != '$_POST[bank]' and acct in ('2001');");
			list($bExist) = $p->getArray("select count(*) from cv_details where cv_no = '$cv_no' and acct = '$_POST[bank]' and branch = '$_SESSION[branchid]';");
			if($bExist > 0) {
				$p->dbquery("update cv_details set credit = 0$tcib where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and acct = '$_POST[bank]';");
			}

			$p->updateHeadAmount($cv_no,$_POST['bank']);
			$p->CVDETAILS($cv_no,$status='Active',$lock='N');
		break;
		
		case "check4print":
			list($isE) = $p->getArray("select count(*) from cv_header where cv_no = '$cv_no' and branch = '$_SESSION[branchid]';");
			if($isE > 0) {
				list($aaa) = $p->getArray("select count(*) from cv_details where cv_no = '$cv_no' and branch = '$_SESSION[branchid]';");
				if($aaa > 0) {
					list($db,$cr) = $p->getArray("select sum(debit), sum(credit) from cv_details where cv_no = '$cv_no' and branch = '$_SESSION[branchid]';");
					if($db!=$cr) { echo "DiBalanse"; } else {
						$p->updateHeadAmount($cv_no,$_POST['bank']);
						$p->dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,debit,credit,ref_no,ref_date,ref_type,cost_center,doc_remarks,posted_by,posted_on) SELECT a.branch, a.cy, a.cv_no AS doc_no, a.cv_date AS doc_date, 'CV' AS doc_type, a.payee AS contact_id, IFNULL(b.acct_branch,'1') AS acct_branch, b.acct, SUM(debit) AS debit, SUM(credit) AS credit, b.ref_no, b.ref_date,b.ref_type, b.cost_center,a.remarks AS doc_remarks,'$_SESSION[userid]',now() from cv_header a left join cv_details b on a.cv_no=b.cv_no and a.branch=b.branch where a.cv_no = '$cv_no' and a.branch = '$_SESSION[branchid]' and `status` != 'Posted' GROUP BY b.cv_no,b.ref_no,b.ref_type,b.acct,b.cost_center;");
						$p->dbquery("update ignore cv_header set status = 'Posted', updated_by = '$_SESSION[userid]', updated_on = now() where cv_no = '$cv_no' and branch = '$_SESSION[branchid]';");
						
						list($cust,$cvdate) = $p->getArray("select payee,cv_date from cv_header where cv_no = '$cv_no' and branch = '$_SESSION[branchid]';");
						$xx = $p->dbquery("select ref_no, ref_type, acct_branch, acct, abs(debit-credit) as xamount, applied_lid from cv_details where cv_no = '$cv_no' and branch = '$_SESSION[branchid]' and ref_type not in ('CV','SI');");
						while($xxx = $xx->fetch_array(MYSQLI_BOTH)) { $p->applyBalanceNa(1,$xxx['acct_branch'],$xxx['ref_no'],$xxx['ref_type'],$xxx['acct'],$xxx['xamount'],$cust,$xxx['lid']);	}
						$rQuery = $p->dbquery("SELECT DISTINCT rfp_type, rfp_no FROM cv_details WHERE cv_no = '$cv_no' AND branch = '$_SESSION[branchid]' AND rfp_type != '' AND rfp_no != '';");
						while(list($rtype,$rno) = $rQuery->fetch_array()) {
							if($rtype == 'GRFP') {
								$p->dbquery("update ignore grfp set with_cv = 'Y', cv_no = '$cv_no', cv_date = '$cvdate' where grfp_no = '$rno' and branch = '$_SESSION[branchid]';");
							} else {
								$p->dbquery("update ignore rfp_header set with_cv = 'Y', cv_no = '$cv_no', cv_date = '$cvdate' where rfp_no = '$rno' and branch = '$_SESSION[branchid]';");
							}
						}
						
						echo "noerror";
					}
				} else { echo "waySulod"; }
			} else { echo "wayUlo"; }
		break;
		
		case "checkCleared":
			list($isCleared) = $p->getArray("select count(*) from acctg_gl where doc_no = '$cv_no' and branch = '$_SESSION[branchid]' and doc_type = 'CV' and cleared = 'Y';");
			if($isCleared > 0) { echo "notOk"; }
		break;
		
		case "reopenCV":
			echo "delete from acctg_gl where doc_no = '$cv_no' and doc_type = 'CV' and branch = '$_SESSION[branchid]';";
			$p->dbquery("delete from acctg_gl where doc_no = '$cv_no' and doc_type = 'CV' and branch = '$_SESSION[branchid]';");
			$p->dbquery("update ignore cv_header set status = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where cv_no = '$cv_no' and branch = '$_SESSION[branchid]';");
			list($cust) = $p->getArray("select payee from cv_header where cv_no = '$cv_no' and branch = '$_SESSION[branchid]';");
			$xx = $p->dbquery("select ref_no, ref_type, acct_branch,  acct, abs(debit-credit) as xamount, applied_lid from cv_details where cv_no = '$cv_no' and ref_type not in ('CV','SI') and branch = '$_SESSION[branchid]';");
			while($xxx = $xx->fetch_array(MYSQLI_BOTH)) { $p->revertBalance(1,$xxx['acct_branch'],$xxx['ref_no'],$xxx['ref_type'],$xxx['acct'],$xxx['xamount'], $cust, $xxx['lid']);	}

			$rQuery = $p->dbquery("SELECT DISTINCT rfp_type, rfp_no FROM cv_details WHERE cv_no = '$cv_no' AND branch = '$_SESSION[branchid]' AND rfp_type != '' AND rfp_no != '';");
			while(list($rtype,$rno) = $rQuery->fetch_array()) {
				if($rtype == 'GRFP') {
					$p->dbquery("update ignore grfp set with_cv = 'N', cv_no = '', cv_date = '0000-00-00' where grfp_no = '$rno' and branch = '$_SESSION[branchid]';");
				} else {
					$p->dbquery("update ignore rfp_header set with_cv = 'N', cv_no = '', cv_date = '0000-00-00' where rfp_no = '$rno' and branch = '$_SESSION[branchid]';");
				}
			}

	break;
		
		case "cancel":
			$p->dbquery("update ignore cv_header set status='Cancelled', updated_by='$_SESSION[userid]', updated_on = now() where cv_no ='$cv_no' and branch = '$_SESSION[branchid]';");
		break;
		
		case "getRFP":
			list($cutoff) = $p->getArray("SELECT date_format(DATE_SUB(NOW(), INTERVAL 12 MONTH),'%Y-%m-%d')");
			if (isset($_POST['payee']) && $_POST['payee'] != ''){ $f = " and supplier = trim(leading '0' from '$_POST[payee]') "; $g = " and a.payee = trim(leading '0' from '$_POST[payee]') "; }
			
			$inQuery = $p->dbquery("SELECT DISTINCT rfp_no FROM cv_details b left join cv_header b on b.cv_no = a.cv_no and b.branch = a.branch WHERE b.rfp_type IN ('RFP') AND b.branch = '$_SESSION[branchid]' $g;");
			if($inQuery) {
				$inString = '';
				while(list($rfpNos) = $inQuery->fetch_array()) {
					$inString .= "'".$rfpNos."',";
				}
				
				if($inString != '') { $inRFPS = " and a.rfp_no not in (" . substr($inString,0,-1) . ")"; }
			}
						
			
			$b = $p->countRows("SELECT count(*) from (SELECT a.rfp_no, a.rfp_date,a.supplier,a.supplier_name,a.remarks,a.amount FROM rfp_header a WHERE a.status = 'Finalized' and a.branch = '$_SESSION[branchid]' AND a.rfp_date > '$cutoff' $inRFPS $f) a;");
			if($b > 0) {
				echo "<table width=100% cellpadding=2 cellspacing=0>
						<tr>
							<td class=gridHead width=10%>RFP No.</td>
							<td class=gridHead align=center width=8%>RFP DATE</td>
							<td class=gridHead width=30%>SUPPLIER</td>
							<td class=gridHead width=34%>REMARKS</td>
							<td class=gridHead align=right width=10%>AMOUNT</td>
							<td class=gridHead width=10>&nbsp;</td>
						</tr>
					";
				$c = $p->dbquery("SELECT date_format(a.rfp_date,'%m/%d/%Y') as mask_date,lpad(a.rfp_no,6,0) as mask_no,a.rfp_no,a.rfp_date,a.supplier,a.supplier_name,a.remarks,a.amount FROM rfp_header a WHERE a.status = 'Finalized' and a.branch = '$_SESSION[branchid]' AND a.rfp_date > '$cutoff' $inRFPS $f;");
				$i = 0; $t = 0;
				while($d = $c->fetch_array()) {
					$checked = "";
					$needle = 'RFP|'.$d['rfp_no'].'';
					echo "<tr bgcolor='".$p->initBackground($i)."'>
							<td class=grid width=10% valign=top>".$d['mask_no']."</td>
							<td class=grid align=center width=8% valign=top>".$d['mask_date']."</td>
							<td class=grid width=30% valign=top>".$d['supplier_name']."</td>
							<td class=grid width=34% valign=top>".$d['remarks']."</td>
							<td class=grid align=right width=10% valign=top>".number_format($d['amount'],2)."</td>
							<td class=grid valign=top><input type='radio' name = 'dummy_rfp' id='_$i' value='$needle' $checked align=absmiddle></td>
						</tr>"; $i++;
				}
				if($i < 5) {
						for($i;$i <=5; $i++) {
							echo "<tr  bgcolor='".$p->initBackground($i)."'><td colspan=10 class=grid>&nbsp;</td></tr>";
						}
					}
				echo "</table>";
			}
			
			$inRFPS = ''; $inString = '';
		break;
		
		case "loadSelectedRFP":
			$res = $p->getArray("SELECT LPAD(supplier,6,'0') AS payee_code, supplier_name payee, supplier_addr AS cust_address, date_format(rfp_date,'%m/%d/%Y') as rfpd8, remarks, amount FROM rfp_header WHERE rfp_no = '$_POST[rfp_no]';");
			$p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,credit,acct_branch,cost_center,rfp_type,rfp_no,rfp_date) values ('$_SESSION[branchid]','".$p->formatCY($_POST['cv_date'])."','$_POST[cv_no]','$_POST[cv_no]','".$p->formatDate($_POST['cv_date'])."','CV','$_POST[bank]','".$p->escapeString(htmlentities($p->getAcctDesc($_POST['bank'],1)))."','".$p->formatDigit($res['amount'])."','1','','RFP','$_POST[rfp_no]','".$p->formatDate($_POST['rfpd8'])."');");
			
			$det = $p->dbquery("select * from rfp_details where rfp_no = '$_POST[rfp_no]';");
			while($detRow = $det->fetch_array()) {
				$p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,debit,acct_branch,cost_center,rfp_type,rfp_no,rfp_date) values ('$_SESSION[branchid]','".$p->formatCY($_POST['cv_date'])."','$_POST[cv_no]','$detRow[apv_no]','$detRow[apv_date]','APV','30101','".$p->escapeString(htmlentities($p->getAcctDesc('30101',$_SESSION['company'])))."','$detRow[net_payable]','$_SESSION[branchid]','','RFP','$_POST[rfp_no]','".$p->formatDate($_POST['rfpd8'])."');");
			}
			
			
			list($isE) = $p->getArray("select count(*) from cv_header where cv_no = '$_POST[cv_no]' and branch = '$_SESSION[branchid]';");
			if($isE > 0) {
				$p->dbquery("update ignore cv_header set cy = '".$p->formatCY($_POST['cv_date'])."', cv_date = '".$p->formatDate($_POST['cv_date'])."', payee = '$res[payee_code]', payee_name = '".$p->escapeString($res['payee'])."', payee_addr = '".$p->escapeString(htmlentities($caddress))."', source = '$_POST[bank]', remarks='".$p->escapeString($res['remarks'])."',updated_by='$_SESSION[userid]', updated_on=now() where cv_no = '$_POST[cv_no]' and branch = '$_SESSION[branchid]';");
			} else {
				$p->dbquery("insert ignore into cv_header (cy,branch,cv_no,cv_date,payee,payee_name,payee_addr,source,remarks,created_by,created_on) values ('".$p->formatCY($_POST['cv_date'])."','$_SESSION[branchid]','$_POST[cv_no]','".$p->formatDate($_POST['cv_date'])."','$res[payee_code]','".$p->escapeString($res['payee'])."','".$p->escapeString(htmlentities($caddress))."','$_POST[bank]','".$p->escapeString($res['remarks'])."','$_SESSION[userid]',now());");
			}
			
			$p->updateHeadAmount($cv_no,$_POST['bank']);
			echo json_encode($res);
		break;
		
		case "getGRFP":
			list($cutoff) = $p->getArray("SELECT date_format(DATE_SUB(NOW(), INTERVAL 12 MONTH),'%Y-%m-%d')");
			if($_POST['payee'] != '') { $f = " and payee_code = trim(leading '0' from '$_POST[payee]') "; $g = " and a.payee = trim(LEADING '0' from '$_POST[payee]') "; }
			
			$inString = '';
			$inQuery = $p->dbquery("SELECT DISTINCT rfp_no FROM cv_details b left join cv_header a on b.cv_no = a.cv_no and b.branch = a.branch WHERE b.rfp_type IN ('GRFP') AND b.branch = '$_SESSION[branchid]' $g;");
			while(list($rfpNos) = $inQuery->fetch_array()) {
				$inString .= "'".$rfpNos."',";
			}
				
			if($inString != '') { $inRFPS = " and a.grfp_no not in (" . substr($inString,0,-1) . ")"; }
			
			list($b) = $p->getArray("select count(*) from (select grfp_no, date_format(grfp_date,'%m/%d/%y') as gd8, grfp_date, payee, payment_for, amount from grfp a where with_cv != 'Y' and a.grfp_date >= '$cutoff' $inRFPS $f) a;");
			if($b > 0) {
				echo "<table width=100% cellpadding=5 cellspacing=0>
						<tr>
							<td class=gridHead width=10%>Unit Code</td>
							<td class=gridHead width=10%>RFP No.</td>
							<td class=gridHead align=center width=10%>RFP DATE</td>
							<td class=gridHead width=20% align=left>PAYEE</td>
							<td class=gridHead width=30% align=left>PURPOSE</td>
							<td class=gridHead align=right>AMOUNT</td>
							<td class=gridHead width=10>&nbsp;</td>
						</tr>
					";
				$c = $p->dbquery("SELECT grfp_no, DATE_FORMAT(grfp_date,'%m/%d/%Y') AS gd8, payee, payment_for, amount, a.costcenter FROM grfp a where with_cv != 'Y' and a.grfp_date >= '$cutoff' $inRFPS $f order by payee_code,a.costcenter;");
				$i = 0; $t = 0;
				while($d = $c->fetch_array()) {
					$checked = "";
					$needle = $d['grfp_no']. '|' . $d['gd8'] . '|' . $d['amount'];
					echo "<tr bgcolor='".$p->initBackground($i)."'>
							<td class=grid>&nbsp;&nbsp;".$p->identCostCenter($d['costcenter'])."</td>
							<td class=grid>".str_pad($d['grfp_no'],6,'0',STR_PAD_LEFT)."</td>
							<td class=grid align=center>".$d['gd8']."</td>
							<td class=grid>".$d['payee']."</td>
							<td class=grid>".$d['payment_for']."</td>
							<td class=grid align=right>".number_format($d['amount'],2)."&nbsp;&nbsp;</td>
							<td valign=top><input type='radio' name = 'dummy_grfp' id='_$i' value='$needle' $checked></td>
						</tr>"; $i++;
				}
				if($i < 5) {
						for($i;$i <=15; $i++) {
							echo "<tr  bgcolor='".$p->initBackground($i)."'><td colspan=7 class=grid>&nbsp;</td></tr>";
						}
					}
				echo "</table>";
			}
			
		break;

		case 'loadSelectedGRFP':
			$res = $p->getArray("SELECT lpad(payee_code,6,'0') as payee_code, payee, '' as cust_address, concat('RFP Remarks: ', remarks,'; Payment For: ',payment_for) as remarks, costcenter FROM grfp WHERE grfp_no = '$_POST[grfp_no]' and branch = '$_SESSION[branchid]';");
			list($caddress) = $p->getArray("select CONCAT(`address`,', ',d.brgyDesc,', ',b.citymunDesc,', ',c.provDesc) AS address from contact_info a LEFT JOIN options_cities b ON a.city = b.citymunCode LEFT JOIN options_provinces c ON a.province = c.provCode LEFT JOIN options_brgy d ON a.brgy = d.brgyCode where file_id = trim(LEADING '0' FROM '$res[payee_code]');");
			$res['cust_address'] = $caddress;
			$p->dbquery("insert ignore into cv_details (branch,cy,cv_no,ref_no,ref_date,ref_type,acct,acct_desc,credit,acct_branch,cost_center,rfp_type,rfp_no,rfp_date) values ('1','".$p->formatCY($_POST['cv_date'])."','$_POST[cv_no]','$_POST[cv_no]','".$p->formatDate($_POST['cv_date'])."','CV','$_POST[bank]','".$p->escapeString(htmlentities($p->getAcctDesc($_POST['bank'],1)))."','".$p->formatDigit($_POST['amount'])."','1','$res[costcenter]','GRFP','$_POST[grfp_no]','".$p->formatDate($_POST['grfp_date'])."');");
			
			list($isE) = $p->getArray("select count(*) from cv_header where cv_no = '$_POST[cv_no]' and branch = '$_SESSION[branchid]';");
			if($isE > 0) {
				$p->dbquery("update ignore cv_header set cy = '".$p->formatCY($_POST['cv_date'])."', cv_date = '".$p->formatDate($_POST['cv_date'])."', payee = '$res[payee_code]', payee_name = '".$p->escapeString($res['payee'])."', payee_addr = '".$p->escapeString(htmlentities($caddress))."', source = '$_POST[bank]', remarks='".$p->escapeString(htmlentities($res['remarks']))."',updated_by='$_SESSION[userid]', updated_on=now() where cv_no = '$_POST[cv_no]' and branch = '$_SESSION[branchid]';");
			} else {
				$p->dbquery("insert ignore into cv_header (cy,branch,cv_no,cv_date,payee,payee_name,payee_addr,source,remarks,created_by,created_on) values ('".$p->formatCY($_POST['cv_date'])."','$_SESSION[branchid]','$_POST[cv_no]','".$p->formatDate($_POST['cv_date'])."','$res[payee_code]','".$p->escapeString($res['payee'])."','".$p->escapeString(htmlentities($caddress))."','$_POST[bank]','".$p->escapeString($res['remarks'])."','$_SESSION[userid]',now());");
			}
			$p->updateHeadAmount($cv_no,$_POST['bank']);
			echo json_encode($res);
		break;
		
		case "preCheckReference":
		
			if($_POST['payee'] != '') { $f1 = " and payee_code = trim(leading '0' from '$_POST[payee]') "; $f2 = " and supplier = trim(leading '0' from '$_POST[payee]') "; } else { $f1 = ''; $f2 = ''; }
			if($_POST['rfp_type'] == "RFP") {
				$docString = "SELECT with_cv, rfp_no as ref_no, date_format(rfp_date,'%m/%d/%Y') as ref_date, proj_name as cc, amount FROM rfp_header WHERE rfp_no = trim(leading '0' from '$_POST[rfp_no]') AND `status` = 'Finalized' and rfp_no not in (select ref_no from cv_details where trace_no = '$_POST[trace_no]' and ref_type = 'RFP') $f2;";
			} else {
				$docString = "SELECT with_cv, grfp_no as ref_no, date_format(grfp_date,'%m/%d/%Y') as ref_date, proj_name as cc, amount FROM grfp WHERE grfp_no = trim(leading '0' from '$_POST[rfp_no]') AND `status` = 'Finalized' and grfp_no not in (select ref_no from cv_details where trace_no = '$_POST[trace_no]' and ref_type = 'GRFP') $f1;";
			}

			$doc = $p->getArray($docString);
			switch($doc[0]) {
				case "N": $stat = "Ok"; break;
				case "Y": $stat = "Applied"; break;
				default: $stat = "NotFound"; break;
			}
			echo json_encode(array("result"=>$stat,"ref_date"=>$doc['ref_date'],"cc"=>$doc['cc'],"amount"=>$doc['amount']));
			
		break;
		
		case "printCheck":
			list($check_no,$check_date,$payee,$amount) = $p->getArray("select check_no,date_format(check_date,'%m/%d/%Y') as check_date, payee_name, ROUND(amount,2) as amount from cv_header where cv_no = '$_POST[cv_no]' and branch = '$_SESSION[branchid]';");
			list($digs,$fracs) = explode(".",$amount);
			if($fracs != '00') { $xfracs = " & $fracs/100"; }
			$word = $p->inWords($digs) . $xfracs ." PESOS ONLY";
			echo json_encode(array("check_no"=>$check_no, "check_date"=>$check_date,"payee" => html_entity_decode($payee), "inw" => $word, "amount" => number_format($amount,2)));
		break;
		
		case "getCheckSeries":
			echo json_encode($p->getArray("select max(check_no)+1 from cv_header where source = '$_POST[source]';"));
		break;
		case "checkDuplicateNo":
			echo json_encode($p->getArray("SELECT COUNT(*), CONCAT(LPAD(branch,2,0),'-',LPAD(cv_no,5,0)) AS cv_no, DATE_FORMAT(cv_date,'%m/%d/%Y'), FORMAT(amount,2) AS amount FROM cv_header WHERE check_no = '$_POST[check_no]' AND source = '$_POST[source]' and check_no != '0' and cv_no != '$cv_no';"));
		break;
		case "getTotals":
			list($amount,$input,$ewt) = $p->getArray("SELECT amount, vat, ewt_amount FROM cv_header WHERE cv_no = '$cv_no' AND branch = '$_SESSION[branchid]';");
			if($input>0) { $netOfVat = number_format(($amount+$ewt-$input),2); } else { $netOfVat = "0.00"; }
			echo json_encode(array("gross"=>number_format(ROUND(($amount+$ewt),2),2),"netOfVat"=>$netOfVat,"netAmount"=>number_format($amount,2),"vat"=>number_format($input,2),"ewt"=>number_format($ewt,2)));		
		break;
	}

?>