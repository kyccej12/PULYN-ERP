<?php
	session_start();
	ini_set("max_execution_time",0);
	include("includes/dbUSE.php");
	include("functions/si.displayDetails.fnc.php");

	@mysql("START TRANSACTION");
	
	function updateHeaderAmt($traceno) {
		list($terms) = getArray("select terms from invoice_header where trace_no = '$_POST[trace_no]';");
		list($gross,$discount,$commission) = getArray("SELECT SUM(round(qty*comm)+ROUND(qty*cost,2)) AS gross, SUM(ROUND(discount*qty,2)) AS discount, SUM(ROUND(comm*qty,2)) AS comm FROM invoice_details WHERE trace_no = '$traceno';");	

		$bal = ROUND(($gross-$discount-$commission),2);
		dbquery("update ignore invoice_header set amount=($gross-0$discount-0$commission),discount=$discount, commission=$commission, balance=$bal where trace_no = '$traceno';");
	}
	
	function postToGL($traceno) {
		
		/*Update Sales Order */
		$d = dbquery("select so_no, item_code, qty from invoice_details where trace_no = '$traceno' and (so_no != '' or so_no != 0);");
		while($e = mysql_fetch_array($d)) {
			dbquery("update ignore so_details set qty_dld = qty_dld + $e[qty] where so_no = '$e[so_no]' and item_code = '$e[item_code]' and branch = '$_SESSION[branchid]';");
		}
		
		/* POST TO GENERAL LEDGER */
		$a = dbquery("SELECT a.branch, doc_no, IF(posting_date='0000-00-00',invoice_date,posting_date) AS doc_date, IF(posting_date='0000-00-00',DATE_FORMAT(invoice_date,'%Y'),DATE_FORMAT(posting_date,'%Y')) AS cy, customer, a.terms, (amount-discount) as amount, b.vatable, a.remarks, a.updated_by, a.updated_on, a.discount FROM invoice_header a LEFT JOIN contact_info b ON a.customer=b.file_id WHERE trace_no = '$traceno';");
		while($row = mysql_fetch_array($a)) {
			
			/* REVENUE */
			$b = dbquery("SELECT IF(b.rev_acct='','4010',b.rev_acct) AS rev_acct, ROUND(SUM(amount),2) AS amount, IF('$row[vatable]' = 'Y',SUM(ROUND((amount/1.12) * 0.12,2)),0) AS vat FROM invoice_details a INNER JOIN products_master b ON a.item_code = b.item_code WHERE trace_no = '$traceno' GROUP BY b.rev_acct;"); 
			while($c = mysql_fetch_array($b)) {
				$net = $c['amount'] - $c['vat']; $vatT+=$c['vat'];
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,credit,doc_remarks,posted_by,posted_on) values ('$row[branch]','$row[cy]','$row[doc_no]','$row[doc_date]','SI','$row[customer]','$row[branch]','$c[rev_acct]','$net','".mysql_real_escape_string($row['remarks'])."','$row[updated_by]','$row[updated_on]');");		
			}
		
			if($row['vatable'] == "Y" && $vatT != 0) {
				dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,credit,doc_remarks,posted_by,posted_on) values ('$row[branch]','$row[cy]','$row[doc_no]','$row[doc_date]','SI','$row[customer]','$row[branch]','2201','$vatT','".mysql_real_escape_string($row['remarks'])."','$row[updated_by]','$row[updated_on]');");
			}

			/* DEBIT ACCOUNT */
			if($row['terms'] != 0) { $dAcct = "1101"; } else { $dAcct = "1001"; }
			dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,debit,doc_remarks,posted_by,posted_on) values ('$row[branch]','$row[cy]','$row[doc_no]','$row[doc_date]','SI','$row[customer]','$row[branch]','$dAcct','$row[amount]','".mysql_real_escape_string($row['remarks'])."','$row[updated_by]','$row[updated_on]');");
			
			/* INSERT COGS */
			$d = dbquery("SELECT b.cogs_acct,SUM(ROUND((qty*b.unit_cost)/1.12,2)) AS cost FROM invoice_details a LEFT JOIN products_master b ON a.item_code = b.item_code WHERE trace_no = '$traceno' GROUP BY b.cogs_acct;");
			while($e = mysql_fetch_array($d)) {
				if($e[cost] != '0.00') {
					dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,debit,doc_remarks,posted_by,posted_on) values ('$row[branch]','$row[cy]','$row[doc_no]','$row[doc_date]','SI','$row[customer]','$row[branch]','$e[cogs_acct]','$e[cost]','".mysql_real_escape_string($row['remarks'])."','$row[updated_by]','$row[updated_on]');");
				}
			}
			
			/* Insert Deduction to Inventory */
			$f = dbquery("SELECT b.asset_acct,SUM(ROUND((qty*b.unit_cost)/1.12,2)) AS cost FROM invoice_details a LEFT JOIN products_master b ON a.item_code = b.item_code WHERE trace_no = '$traceno' GROUP BY b.cogs_acct;");
			while($g = mysql_fetch_array($f)) {
				if($g[cost] != '0.00') {
					dbquery("insert ignore into acctg_gl (branch,cy,doc_no,doc_date,doc_type,contact_id,acct_branch,acct,credit,doc_remarks,posted_by,posted_on) values ('$row[branch]','$row[cy]','$row[doc_no]','$row[doc_date]','SI','$row[customer]','$row[branch]','$g[asset_acct]','$g[cost]','".mysql_real_escape_string($row['remarks'])."','$row[updated_by]','$row[updated_on]');");
				}
			}
			
			$net = 0; $vatT = 0;
		}
		
		/* Post Inventory to Inventory Book */
		$iquery = dbquery("SELECT a.branch, a.doc_no, a.invoice_date AS doc_date, a.customer,a.customer_name, b.item_code, b.unit, b.qty FROM invoice_header a INNER JOIN invoice_details b ON a.trace_no = b.trace_no WHERE a.trace_no = '$traceno';");
		while($ibook = mysql_fetch_array($iquery)) {
			dbquery("INSERT IGNORE INTO ibook (doc_no,doc_date,doc_type,doc_branch,ccode,cname,item_code,uom,sold,posted_by,posted_on) VALUES ('$ibook[doc_no]','$ibook[doc_date]','SI','$ibook[branch]','$ibook[customer]','".mysql_real_escape_string($ibook['customer_name'])."','$ibook[item_code]','$ibook[unit]','$ibook[qty]','$_SESSION[userid]',now());");
		}
	}

	switch($_POST['mod']) {
		case "saveHeader":
			list($isE) = getArray("select count(*) from invoice_header where trace_no = '$_POST[trace_no]';");
			if($isE > 0) {
				$s = "update ignore invoice_header set invoice_type='$_POST[type]', invoice_no='$_POST[invoice_no]', customer = '$_POST[cid]', customer_name = '".mysql_real_escape_string(htmlentities($_POST['cname']))."', customer_addr = '".mysql_real_escape_string(htmlentities($_POST['addr']))."', sales_rep='$_POST[srep]', terms = '$_POST[terms]', invoice_date = '".formatDate($_POST['invoice_date'])."', posting_date = '".formatDate($_POST['postDate'])."', remarks = '".mysql_real_escape_string($_POST['remarks'])."', updated_by = '$_SESSION[userid]', updated_on = now() where trace_no = '$_POST[trace_no]';";
				$docno = $_POST['doc_no'];
				
				/* Audit Trail */
				dbquery("insert ignore into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SI','UPDATED SALES INVOICE # $docno -> CUSTOMER = $_POST[cid] -> CNAME = ".mysql_real_escape_string(htmlentities($_POST['cname']))." -> INV DATE = $_POST[invoice_date] -> TERMS = $_POST[terms] -> DISCOUNT = $_POST[discount]','$ino');");
		
			} else {
				list($docno) = getArray("SELECT IFNULL(MAX(doc_no),0)+1 FROM invoice_header WHERE branch = '$_SESSION[branchid]';");
				$s = "insert ignore into invoice_header (branch, doc_no, invoice_no, invoice_type, invoice_date, posting_date, customer, customer_name, customer_addr, sales_rep, discount, terms, remarks, created_by, created_on,trace_no) values ('$_SESSION[branchid]','$docno','$_POST[invoice_no]','$_POST[type]','".formatDate($_POST['invoice_date'])."','".formatDate($_POST['postDate'])."','$_POST[cid]','".mysql_real_escape_string(htmlentities($_POST['cname']))."','".mysql_real_escape_string(htmlentities($_POST['addr']))."','$_POST[srep]','$_POST[discount]','$_POST[terms]','".mysql_real_escape_string($_POST['remarks'])."','$_SESSION[userid]',now(),'$_POST[trace_no]');";
			
				/* Audit Trail */
				dbquery("insert ignore into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SI','CREATED DOC # $docno -> CUSTOMER = $_POST[cid] -> CNAME = ".mysql_real_escape_string(htmlentities($_POST['cname']))." -> INV DATE = $_POST[invoice_date] -> TERMS = $_POST[terms] -> DISCOUNT = $_POST[discount]','$ino');");
			
			}
			dbquery($s);
			echo json_encode(array('docno'=>$docno));
		break;
		case "insertDetail":
			dbquery("insert ignore into invoice_details (branch,invoice_no,so_no,so_date,item_code,description,sales_group,qty,unit,cost,amount,trace_no) values ('$_SESSION[branchid]','$_POST[invoice_no]','$_POST[so_no]','".formatDate($_POST['so_date'])."','$_POST[icode]','".mysql_real_escape_string($_POST['desc'])."','".identSGroup($_POST['icode'],$_SESSION['company'])."','".formatDigit($_POST['qty'])."','$_POST[unit]','".formatDigit($_POST['price'])."','".formatDigit($_POST['amount'])."','$_POST[trace_no]');");
			
			/* AUDIT TRAIL PURPOSES */
			dbquery("insert ignore into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SI','PRODUCT MANUALLY ADDED TO SALES INVOICE # $_POST[invoice_no] -> SO # = 0$_POST[so_no] -> ITEM = $_POST[icode] -> QTY = $_POST[qty] -> PRICE = $_POST[price]','$_POST[invoice_no]');");
			
			updateHeaderAmt($_POST['trace_no']);
			IDETAILS($_POST['trace_no'],$status="Active",$lock="N");
		break;
		case "getTotals":
			list($terms) = getArray("select terms from invoice_header where trace_no = '$_POST[trace_no]';");
			list($gross,$net,$discount,$comm) = getArray("SELECT SUM(ROUND(qty*cost,2)) AS gross, sum(ROUND((qty*cost)-(qty*discount)-(qty*comm),2)) as net, SUM(ROUND(discount*qty,2)) AS discount, SUM(ROUND(comm*qty,2)) AS comm FROM invoice_details WHERE trace_no = '$_POST[trace_no]';");	
			if($gross=="") { $gross="0.00"; $discount="0.00"; $net="0.00"; $comm = "0.00"; }
			echo json_encode(array("gross"=>$gross, "net"=>$net, "discount" => $discount, "commission" => $comm));
		break;
		case "getPOS":
			list($cut) = getArray("SELECT DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 3 MONTH),'%Y-%m-%d');");
			
			list($b) = getArray("select count(*) from (select a.so_no, date_format(a.so_date,'%m/%d/%Y') as pd8, a.remarks, ROUND(sum((b.cost-b.discount) * (b.qty-b.qty_dld)),2) as amount from so_header a left join so_details b on a.so_no=b.so_no and a.branch=b.branch where a.so_date > '$cut' and a.branch='$_SESSION[branchid]' and b.qty_dld < b.qty and a.customer = trim(leading '0' from '$_POST[cid]') and a.status = 'Finalized' and a.so_no not in (select distinct so_no from invoice_details where trace_no = '$_POST[trace_no]') group by a.so_no) a;");
			if($b > 0) {
				echo '<table width=100% cellpadding=2 cellspacing=0>
						<tr>
							<td class="ui-state-default" style="padding: 5px;" width=15% align=center>SO #</td>
							<td class="ui-state-default" style="padding: 5px;" align=center width=15%>SO DATE</td>
							<td class="ui-state-default" style="padding: 5px;">TRANSACTION REMARKS</td>
							<td class="ui-state-default" style="padding: 5px;" align=right width=15%>AMOUNT</td>
							<td class="ui-state-default" style="padding: 5px;" width=10>&nbsp;</td>
						</tr>
					';

				$c = dbquery("select a.so_no as po, a.so_no, a.so_date, date_format(a.so_date,'%m/%d/%Y') as pd8, a.remarks, ROUND(sum((b.cost - b.discount) * (b.qty-b.qty_dld)),2) as amount from so_header a left join so_details b on a.so_no=b.so_no and a.branch=b.branch where a.so_date > '$cut' and a.branch='$_SESSION[branchid]' and b.qty_dld < b.qty and a.customer = trim(leading '0' from '$_POST[cid]') and a.status = 'Finalized' and a.so_no not in (select distinct so_no from invoice_details where trace_no = '$_POST[trace_no]') group by a.so_no;");
				$i = 0;
				while($d = mysql_fetch_array($c)) {
					$checked = "";
					$needle = $d['so_no']."|".$d['so_date']."|".$i;
					if(isset($_SESSION['ques'])) {
						if(in_array($needle, $_SESSION['ques'])) {
							$checked = "checked"; 
						}
					}
					if($i%2==0){ $bgC = "#ededed"; } else { $bgC = "#ffffff"; }
					echo "<tr bgcolor='".$bgC."'>
							<td class=grid valign=top align=center>".str_pad($d[po],6,'0',STR_PAD_LEFT)."</td>
							<td class=grid align=center valign=top>$d[pd8]</td>
							<td class=grid valign=top>$d[remarks]</td>
							<td class=grid align=right valign=top>".number_format($d['amount'],2)."</td>
							<td valign=top><input type='checkbox' id='ckbox[$i]' value='$needle' onclick='tagPO(this.id,this.value);' $checked></td>
						</tr>"; $i++;
				}
				if($i < 5) {
						for($i;$i <=5; $i++) {
							if($i%2==0){ $bgC = "#ededed"; } else { $bgC = "#ffffff"; }
							echo "<tr  bgcolor='".$bgC."'><td colspan=5 class=grid>&nbsp;</td></tr>";
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
					list($so_no,$so_date) = $subindex;	
					$opo = dbquery("select item_code, description, custom_description, (qty-qty_dld) as qty, unit, cost, discount, discount_percent, amount,comm from so_details where so_no = '$so_no' and branch = '$_SESSION[branchid]' and (qty-qty_dld) > 0;");
					while($op = mysql_fetch_array($opo)) {
						/* Added Process to avoid duplication */
						//list($isE) = getArray("select count(*) from invoice_details where trace_no = '$_POST[trace_no]' and so_no = '$so_no' and item_code = '$op[item_code]';");
				//		if($isE == 0) {
							dbquery("insert ignore into invoice_details (branch,doc_no,so_no,so_date,item_code,description,custom_description,sales_group,qty,unit,cost,discount,discount_percent,amount,comm,trace_no) values ('$_SESSION[branchid]','$_POST[doc_no]','$so_no','$so_date','$op[item_code]','".mysql_real_escape_string($op['description'])."','".mysql_real_escape_string($op['custom_description'])."','".identSGroup($op[item_code],$company=1)."','$op[qty]','$op[unit]','$op[cost]','$op[discount]','$op[discount_percent]','$op[amount]','$op[comm]','$_POST[trace_no]');");
							
							/* AUDIT TRAIL PURPOSES */
							dbquery("insert ignore into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SI','PRODUCT ADDED TO SALES INVOICE # $_POST[invoice_no] -> SO # = $so_no -> ITEM = $op[item_code] -> QTY = $op[qty] -> PRICE = $op[cost]','$_POST[invoice_no]');");
						//}
					}
				}

				updateHeaderAmt($_POST['trace_no']);
				IDETAILS($_POST['trace_no'],$status="Active",$lock="N");
				unset($_SESSION['ques']);
			}
		break;

		case "deleteLine":
			$det = getArray("select * from invoice_details where line_id = '$_POST[lid]';");
			
			/* AUDIT TRAIL PURPOSES */
			dbquery("insert ignore into traillog (company,branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[company]','$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SI','PRODUCT DELETED FROM SALES INVOICE WITH DOC # $det[doc_no] -> SO # = $det[so_no] -> ITEM = $det[item_code] -> QTY = $det[qty] -> PRICE = $det[cost]','$det[invoice_no]');");
						
			dbquery("delete from invoice_details where line_id = '$_POST[lid]';");
			updateHeaderAmt($_POST['trace_no']);
			IDETAILS($_POST['trace_no'],$status="Active",$lock="N");
		break;
		
		case "usabQty":
			$amt = ROUND(formatDigit($_POST['price']) * formatDigit($_POST['val']),2);
			dbquery("update invoice_details set qty = '".formatDigit($_POST['val'])."', amount = ROUND(0$amt - (".formatDigit($_POST['val'])." * discount),2) where line_id = '$_POST[lid]';");
			list($lamt) = getArray("select (amount+comm) from invoice_details where line_id = '$_POST[lid]';");
			updateHeaderAmt($_POST['trace_no']);

			echo json_encode(array('amt1' => number_format($lamt,2)));
		break;
		
		case "usabPrice":
			$amt = ROUND(formatDigit($_POST['price']) * formatDigit($_POST['qty']),2);
			list($disc) = getArray("select ROUND(".formatDigit($_POST['price'])." * (discount_percent/100),2) from invoice_details where line_id = '$_POST[lid]';");
			dbquery("update invoice_details set cost = '".formatDigit($_POST['price'])."', discount = 0$disc, amount = ROUND(0$amt - (".formatDigit($_POST['qty'])." * 0$disc),2) where line_id = '$_POST[lid]';");
			list($lamt,$nprice) = getArray("select (amount+comm), (cost - discount) as nprice from invoice_details where line_id = '$_POST[lid]';");
			updateHeaderAmt($_POST['trace_no']);

			echo json_encode(array('amt1' => number_format($lamt,2), 'nprice' => number_format($nprice,2)));
		break;
		
		case "check4print":
			list($a) = getArray("select count(*) from invoice_header where trace_no = '$_POST[trace_no]';");
			list($b) = getArray("select count(*) from invoice_details where trace_no = '$_POST[trace_no]';");
			
			if($a == 0 && $b > 0) { echo "head"; }
			if($b == 0 && $a > 0) { echo "det"; }
			if($a == 0 && $b == 0) { echo "both"; }
			if($a > 0 && $b > 0) { echo "noerror"; }
		break;
		
		case "checkSalesGroup":
			$a = mysql_query("SELECT item_code FROM (SELECT a.item_code, b.cogs_acct, b.asset_acct, b.rev_acct FROM invoice_details a LEFT JOIN products_master b ON a.item_code = b.item_code WHERE trace_no = '$_POST[trace_no]') a WHERE (cogs_acct = '' OR asset_acct = '' OR rev_acct = '') AND let(item_code,3) not in ('000','CFP');");
			if(mysql_num_rows($a)) { 
				while($b = mysql_fetch_array($a)) {
					$items .= "&raquo;" . $b['item_code'] . "<br/>";
				}
				echo $items;
			} else {
				echo "Ok";
			}
		break;
		
		case "finalize":
			dbquery("update ignore invoice_header set `status` = 'Finalized', updated_by = '$_SESSION[userid]', updated_on = now() where trace_no ='$_POST[trace_no]';");
			updateHeaderAmt($_POST['trace_no']);
			postToGL($_POST['trace_no']);
		break;
		
		case "reopen":
			dbquery("update ignore invoice_header set `status` = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where trace_no = '$_POST[trace_no]';");
			dbquery("delete from acctg_gl where doc_no = '$_POST[doc_no]' and branch = '$_SESSION[branchid]' and doc_type = 'SI';");
			dbquery("delete from ibook where doc_no = '$_POST[doc_no]' and doc_branch = '$_SESSION[branchid]' and doc_type = 'SI';");
	
			/* Restore S.O Status */
			$f = dbquery("select so_no, item_code, qty from invoice_details where trace_no = '$_POST[trace_no]' and so_no != '';");
			while($g = mysql_fetch_array($f)) {
				dbquery("update ignore so_details set qty_dld = qty_dld - $g[qty] where so_no = '$g[so_no]' and item_code = '$g[item_code]' and branch = '$_SESSION[branchid]';");
			}
			
			/* AUDIT TRAIL PURPOSES */
			dbquery("insert ignore into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SI','SALES INVOICE # $_POST[invoice_no] RE-OPENED BY USER','$_POST[invoice_no]');");

		break;
		
		case "cancel":
			dbquery("update ignore invoice_header set `status` = 'Cancelled', updated_by = '$_SESSION[userid]', updated_on = now() where trace_no = '$_POST[trace_no]';");
			
			/* AUDIT TRAIL PURPOSES */
			dbquery("insert ignore into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[company]','$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SI','SALES INVOICE # $_POST[invoice_no] CANCELLED BY USER','$_POST[invoice_no]');");
		
		break;

		case 'applyDiscount':
			 
			 list($price,$qty,$gross) = getArray("select cost, qty, ROUND(qty*cost,2) as gross from invoice_details where line_id = '$_POST[lineid]';");
			
			if($_POST['type'] == "PCT") {
				$pctW = $_POST['discount'];
				$pct = ROUND($_POST[discount]/100,2);
				$dUoM = ROUND($price * $pct,2);
			} else {
				$pctW = 0;
				$dUoM = $_POST['discount'];
			}
			 
			$tDisc = ROUND($dUoM * $qty,2);
			$netOfDisc = $gross - $tDisc;
			dbquery("UPDATE invoice_details SET discount_percent = $pctW, discount = 0$dUoM, amount = 0$netOfDisc WHERE line_id = '$_POST[lineid]';");

			/* dbquery("UPDATE cebuglass.invoice_details SET discount_percent = '$_POST[discount]' WHERE line_id = '$_POST[lineid]';");
			 list($disc) = getArray("SELECT cost * ROUND(discount_percent/100,2) AS discount FROM cebuglass.invoice_details  WHERE trace_no = '$_POST[trace_no]'; ");
			 dbquery("UPDATE cebuglass.invoice_details SET discount = 0$disc, amount = ROUND(qty * (cost-0$disc),2) WHERE line_id = '$_POST[lineid]';");
			 */
			 
			 updateHeaderAmt($_POST['trace_no']);
			 IDETAILS($_POST['trace_no'],$status="Active",$lock="N");
		break;

		case 'finalizePOScash':
			dbquery("UPDATE invoice_header a SET a.balance = '0',applied_amount = '$_POST[due]',`status` = 'Finalized',pay_type='cash'  WHERE a.trace_no = '$_POST[trace_no]';");
			postToGL($_POST['trace_no']);
		break;
		case "finalizePOScard":
			dbquery("UPDATE invoice_header a SET applied_amount = balance,a.balance = '0',pay_type = 'ccard',card_type='$_POST[cc_type]',issue_bank='$_POST[bank]',card_holder='".mysql_real_escape_string($_POST['cc_name'])."',card_no='$_POST[cc_no]',card_expiry='$_POST[cc_expiry]',trans_apprv='$_POST[approvalno]',`status` = 'Finalized'  WHERE a.trace_no = '$_POST[trace_no]';");
			postToGL($_POST['trace_no']);
		break;
		case "finalizeCheqCheckOut":
			dbquery("UPDATE invoice_header a SET applied_amount = balance, a.balance = '0', pay_type = 'check',issue_bank='$_POST[bank]',cheq_no='$_POST[cheq_no]' ,cheq_date='$_POST[cheq_date]',`status` = 'Finalized'  WHERE a.trace_no = '$_POST[trace_no]';");
			postToGL($_POST['trace_no']);
		break;
		
		case "checkSOstat":
			list($a) = getArray("SELECT SUM(qty-qty_dld) AS bal FROM so_header a INNER JOIN so_details b ON a.so_no = b.so_no AND a.branch = b.branch WHERE a.so_no = '$_POST[so_no]' AND a.branch = '$_SESSION[branchid]' AND a.status = 'Finalized';");
			if($a > 0) {
				list($scid, $cname,$so_date,$amount,$srep) = getArray("select customer, concat('(',customer,') ',customer_name) as cname, date_format(so_date,'%m/%d/%Y') as sd8, format(amount,2), b.fullname from so_header a INNER JOIN user_info b on a.created_by = b.emp_id where a.so_no = '$_POST[so_no]' and branch = '$_SESSION[branchid]';");
				if($_POST['ino'] != '') { list($icid) = getArray("select customer from invoice_header where invoice_no = '$_POST[ino]' and branch = '$_SESSION[branchid]';"); if($icid == $scid) { $stat = 'Ok'; } else { $stat = 'notOk'; } } else { $stat = 'Ok'; }
			} else { $stat = "notOk"; }
			echo json_encode(array("stat"=>$stat,"cname"=>$cname,"sodate"=>$so_date,"amount"=>$amount,"salesrep"=>$srep));
		break;
		
		case "uploadWholeSO":
			
			$_h = getArray("select * from so_header where so_no = '$_POST[so_no]' and branch = '$_SESSION[branchid]';");
			list($terms) = getArray("select terms from contact_info where file_id = '$_h[customer]';");
			list($docno) = getArray("SELECT IFNULL(MAX(doc_no),0)+1 FROM invoice_header WHERE branch = '$_SESSION[branchid]';");
			dbquery("insert ignore into invoice_header (branch, doc_no, invoice_date, posting_date, customer, customer_name, customer_addr, sales_rep, discount, commission, terms, remarks, created_by, created_on,trace_no) values ('$_SESSION[branchid]','$docno','".date('Y-m-d')."','".date('Y-m-d')."','$_h[customer]','".mysql_real_escape_string(htmlentities($_h['customer_name']))."','".mysql_real_escape_string(htmlentities($_h['customer_addr']))."','$_h[srep]','$_h[discount]','$_h[commission]','$terms','".mysql_real_escape_string($_h['remarks'])."','$_SESSION[userid]',now(),'$_POST[trace_no]');");
			
			$opo = dbquery("select item_code, description, custom_description, (qty-qty_dld) as qty, unit, cost, discount, discount_percent, amount, comm from so_details where so_no = '$_POST[so_no]' and branch = '$_SESSION[branchid]' and (qty-qty_dld) > 0;");
			while($op = mysql_fetch_array($opo)) {
				dbquery("insert ignore into invoice_details (branch,doc_no,so_no,so_date,item_code,description,custom_description,sales_group,qty,unit,cost,discount,discount_percent,amount,comm,trace_no) values ('$_SESSION[branchid]','$docno','$_POST[so_no]','$_h[so_date]','$op[item_code]','".mysql_real_escape_string($op['description'])."','".mysql_real_escape_string($op['custom_description'])."','".identSGroup($op[item_code],$company=1)."','$op[qty]','$op[unit]','$op[cost]','$op[discount]','$op[discount_percent]','$op[amount]','$op[comm]','$_POST[trace_no]');");	
			}
			
			updateHeaderAmt($_POST['trace_no']);
			echo json_encode(array("docno"=>$docno));
		break;
		
		case "uploadDetailsSO":
			$so_date = getArray("select so_date from so_header where so_no = '$_POST[so_no]' and branch = '$_SESSION[branchid]';");
			$opo = dbquery("select item_code, description, (qty-qty_dld) as qty, unit, cost, discount, discount_percent, amount,comm from so_details where so_no = '$_POST[so_no]' and branch = '$_SESSION[branchid]' and (qty-qty_dld) > 0;");
			while($op = mysql_fetch_array($opo)) {
				dbquery("insert ignore into invoice_details (branch,doc_no,so_no,so_date,item_code,description,custom_description,sales_group,qty,unit,cost,discount,discount_percent,amount,comm,trace_no) values ('$_SESSION[branchid]','$_POST[ino]','$_POST[so_no]','$_h[so_date]','$op[item_code]','".mysql_real_escape_string($op['description'])."','".mysql_real_escape_string($op['custom_description'])."','".identSGroup($op[item_code],$company=1)."','$op[qty]','$op[unit]','$op[cost]','$op[discount]','$op[discount_percent]','$op[amount]','$op[comm]','$_POST[trace_no]');");	
			}
		break;

		case "clearItems":
			dbquery("delete from invoice_details where trace_no = '$_POST[trace_no]';");
			dbquery("update invoice_heder set amount = 0, discount = 0, balance = 0, applied_amount = 0 where trace_no = '$_POST[trace_no]';");
			IDETAILS($_POST['trace_no'],$status="Active",$lock="N");

		break;
	}
	@mysql_query("COMMIT");
	mysql_close($con);

?>