<?php
	session_start();
	
	ini_set("max_execution_time",-1);
	ini_set("memory_limit",-1);
	
	include("includes/dbUSE.php");
	include("functions/so.displayDetails.fnc.php");
	
	
	
	function updateHeaderAmt($traceno) {
		list($gross,$discount,$comm) = getArray("SELECT SUM(ROUND(qty*cost,2)) AS gross, SUM(ROUND(discount*qty,2)) AS discount, SUM(ROUND(qty*comm,2)) as comm FROM so_details WHERE trace_no = '$traceno';");	
		dbquery("update ignore so_header set amount=(0$gross-0$discount),discount=0$discount,commission=0$comm where trace_no = '$traceno';");
	}

	switch($_POST['mod']) {
		case "saveHeader":
			list($isE) = getArray("select count(*) from so_header where trace_no = '$_POST[trace_no]';");
			if($isE > 0) {
				/* Added to Prevent user from invoking update if using multiple windows */
				list($isStat) = getArray("select `status` from so_header where trace_no = '$_POST[trace_no]';");
				if($isStat != 'Finalized') {
					$s = "update ignore so_header set customer = '$_POST[cid]', received_by = '".mysql_real_escape_string(htmlentities($_POST['received_by']))."', customer_name = '".mysql_real_escape_string(htmlentities($_POST['cname']))."', customer_addr = '".mysql_real_escape_string(htmlentities($_POST['addr']))."', so_date = '".formatDate($_POST['so_date'])."', srep='$_POST[srep]', est_delivery='".formatDate($_POST['est_delivery'])."', remarks = '".mysql_real_escape_string(htmlentities($_POST['remarks']))."', po_type='$_POST[po_type]', po_no='$_POST[po_no]', po_date='".formatDate($_POST['po_date'])."', updated_by = '$_SESSION[userid]', updated_on = now() where trace_no = '$_POST[trace_no]' and branch = '$_SESSION[branchid]';";
					dbquery("insert into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SO','UPDATED SALES ORDER # $_POST[so_no] -> CUSTOMER = $_POST[cid] -> CNAME = ".mysql_real_escape_string(htmlentities($_POST['cname']))." -> SO DATE = $_POST[so_date] -> EST. DELVRY = $_POST[est_delivery]','$_POST[so_no]');");
				}
			} else {
				list($so_no) = getArray("select ifnull(max(so_no),0)+1 from so_header where branch = '$_SESSION[branchid]';"); 
				$s = "insert ignore into so_header (trace_no, branch, so_no, so_date, received_by, customer, customer_name, customer_addr, srep, est_delivery, po_no, po_date, remarks, created_by, created_on) values ('$_POST[trace_no]','$_SESSION[branchid]','$so_no','".formatDate($_POST['so_date'])."','".mysql_real_escape_string(htmlentities($_POST['received_by']))."','$_POST[cid]','".mysql_real_escape_string(htmlentities($_POST['cname']))."','".mysql_real_escape_string(htmlentities($_POST['addr']))."','$_POST[srep]','".formatDate($_POST['est_delivery'])."','$_POST[po_no]','".formatDate($_POST['po_date'])."','".mysql_real_escape_string(htmlentities($_POST['remarks']))."','$_SESSION[userid]',now());";
				dbquery("insert into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SO','CREATED SALES ORDER # $so_no -> CUSTOMER = $_POST[cid] -> CNAME = ".mysql_real_escape_string(htmlentities($_POST['cname']))." -> SO DATE = $_POST[so_date] -> EST. DELVRY = $_POST[est_delivery]','$so_no');");
				echo $so_no;
			}
			dbquery($s);
		break;

		case "checkForeignEntries":
			list($isE) = getArray("select count(*) from so_details where so_no = '$_POST[so_no]' and branch = '$_SESSION[branchid]';");
			if($isE > 0) { echo "wf"; } else { echo "ok"; }
		break;

		case "removePrevious":
			dbquery("delete from so_details where so_no = '$_POST[so_no]' and branch = '$_SESSION[branchid]';");
		break;

		case "fetchDetails":
			SODETAILS($_POST['trace_no']);
		break;
		
		case "getTotals":
			list($gross,$disc,$comm,$net) = getArray("select sum(ROUND(qty*(cost+comm),2)) as gross, sum(round(qty*discount,2)) as discount, sum(round(qty*comm,2)) as comm, SUM(ROUND(qty * (cost-discount),2)) as net from so_details where trace_no = '$_POST[trace_no]';");
			if($gross=="") { $gross = "0.00"; $disc = "0.00"; $net = "0.00"; $comm = "0.00"; }
			echo json_encode(array('gross' => $gross, 'discount' => $disc, "net" => $net, "comm" => $comm));
		break;
		
		case "insertDetail":
			$price = formatDigit($_POST['price']);
			$comm = $_POST['comm'];
			$qty = formatDigit($_POST['qty']);
			$amt = ROUND($price * $qty,2) + ROUND($comm * $qty,2) ;
			$plevel = $_POST['price_level'];
			$comm = $_POST['comm'];
			$s = "insert ignore into so_details (trace_no,branch,so_no,item_code,description,sales_group,qty,unit,cost,amount,plevel,comm) values ('$_POST[trace_no]','$_SESSION[branchid]','$_POST[so_no]','$_POST[icode]','".mysql_real_escape_string(htmlentities($_POST['desc']))."','".identSGroup($_POST['icode'],$company=1)."','$qty','$_POST[unit]','$price','$amt','$plevel','$comm');";
			dbquery($s);
			
			/* AUDIT TRAIL PURPOSES */
			dbquery("insert into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SO','PRODUCT MANUALLY ADDED TO SALES ORDER # $_POST[so_no] -> ITEM = $_POST[icode] -> QTY = $qty -> PRICE = $price','$_POST[so_no]');");		
			updateHeaderAmt($_POST['trace_no']);
			SODETAILS($_POST['trace_no'],$status='Active',$lock='N');
		break;
		
		case "deleteLine":
			/* AUDIT TRAIL PURPOSES */
			dbquery("insert into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SO','PRODUCT DELETED FROM SALES ORDER # $_POST[so_no] -> PO NO = 0$po_no -> ITEM = $icode -> QTY = $qty -> PRICE = $price','$_POST[so_no]');");
			dbquery("delete from so_details where line_id = '$_POST[lid]';");
			updateHeaderAmt($_POST['trace_no']);
			SODETAILS($_POST['trace_no']);
		break;
		
		case "usabQty":
			$gamt = ROUND(formatDigit($_POST['val']) * formatDigit($_POST['price']),2);
			dbquery("update so_details set qty = '".formatDigit($_POST['val'])."', amount = (0$gamt - (".formatDigit($_POST['val'])." * discount)) where line_id = '$_POST[lid]';");
			
			list($lamt) = getArray("select amount from so_details where line_id = '$_POST[lid]';");
			echo json_encode(array('amt1' => number_format($lamt,2)));
		
			updateHeaderAmt($_POST['trace_no']);	
		break;
		
		case "check4print":
			list($a) = getArray("select count(*) from so_header where trace_no = '$_POST[trace_no]';");
			list($b) = getArray("select count(*) from so_details where trace_no = '$_POST[trace_no]';");
			
			if($a == 0 && $b > 0) { echo "head"; }
			if($b == 0 && $a > 0) { echo "det"; }
			if($a == 0 && $b == 0) { echo "both"; }
			if($a > 0 && $b > 0) { echo "noerror"; }
		break;
		
		case "finalizePO":
			dbquery("update so_header set status = 'Finalized', updated_by = '$_SESSION[userid]', updated_on = now() where trace_no = '$_POST[trace_no]';");		
			updateHeaderAmt($_POST['trace_no']);
			/* AUDIT TRAIL PURPOSES */
			//dbquery("insert into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SO','SALES ORDER # $_POST[so_no] FINALIZED BY USER','$_POST[so_no]');");
		break;
		case "reopenPO":
			dbquery("update so_header set status = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where trace_no = '$_POST[trace_no]';");
			
			/* AUDIT TRAIL PURPOSES */
			//dbquery("insert into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SO','SALES ORDER # $_POST[so_no] RE-OPENED BY USER','$_POST[so_no]');");
		break;
		case "cancel":
			dbquery("update so_header set status = 'Cancelled', updated_by = '$_SESSION[userid]', updated_on = now() where trace_no = '$_POST[trace_no]';");
			
			/* AUDIT TRAIL PURPOSES */
			//dbquery("insert into traillog (branch,user_id,`timestamp`,ipaddress,module,`action`,doc_no) values ('$_SESSION[branchid]','$_SESSION[userid]',now(),'$_SERVER[REMOTE_ADDR]','SO','SALES ORDER # $_POST[so_no] CANCELLED BY USER','$_POST[so_no]');");
		break;
		case 'applyDiscount':
			list($price,$qty,$gross,$comm) = getArray("select cost, qty, ROUND(qty*cost,2) as gross,round(qty*comm,2) as comm from so_details where line_id = '$_POST[lineid]';");
			$pct = ROUND($_POST[discount]/100,2);
			$dUoM = ROUND($price * $pct,2); 
			$tDisc = ROUND($dUoM * $qty,2);
			$netOfDisc = $gross - $tDisc;
			
			 dbquery("UPDATE so_details SET discount_percent = '$_POST[discount]' WHERE line_id = '$_POST[lineid]';");
			 dbquery("UPDATE so_details SET discount = 0$dUoM, amount = 0$netOfDisc WHERE line_id = '$_POST[lineid]';");
			 updateHeaderAmt($_POST['trace_no']);
			 SODETAILS($_POST['trace_no']);
		break;
		case "changePL":
			$txt = "SELECT CASE 
						  WHEN '$_POST[pl]' = '1' THEN if(b.unit_price1=0,b.walkin_price,b.unit_price1) 
						  WHEN '$_POST[pl]' = '2' THEN if(b.unit_price2=0,b.walkin_price,b.unit_price2) 
						  WHEN '$_POST[pl]' = '3' THEN if(b.unit_price3=0,b.walkin_price,b.unit_price3) 
						  WHEN '$_POST[pl]' = '4' THEN if(b.unit_price4=0,b.walkin_price,b.unit_price4) 
						  WHEN '$_POST[pl]' = '5' THEN if(b.unit_price5=0,b.walkin_price,b.unit_price5) 
						  WHEN '$_POST[pl]' = '6' THEN if(b.unit_price6=0,b.walkin_price,b.unit_price6) 
						  WHEN '$_POST[pl]' = '7' THEN if(b.unit_price7=0,b.walkin_price,b.unit_price7)
						  WHEN '$_POST[pl]' = '8' THEN if(b.unit_price8=0,b.walkin_price,b.unit_price8) 
						  ELSE walkin_price END AS amount 
				   FROM products_master b WHERE b.item_code = '$_POST[item]';";
		
			list($uprice) = getArray($txt);
			echo $uprice;
		break;
		case "checkDiscLock":
			list($lock) = getArray("select disc_lock from cebuglass.so_details where line_id = '$_POST[lineid]';");
			echo $lock;
		break;
		case "toggleLock":	
			dbquery("UPDATE cebuglass.so_details a SET a.disc_lock=IF(disc_lock='Y','N','Y') WHERE a.line_id = '$_POST[lineid]'; ");
			SODETAILS($_POST['trace_no'],$status="Active",$lock="",$_SESSION['utype']);
		break;
		case "getCurrentDescription":
			list($idesc) = getArray("select description from so_details where line_id = '$_POST[lineid]';");
			echo html_entity_decode($idesc);
		break;
		case "saveCustomDesc":
			dbquery("update so_details set custom_description = '".mysql_real_escape_string(htmlentities($_POST[desc]))."' where line_id = '$_POST[lineid]';");
			SODETAILS($_POST['trace_no'],$status="Active",$lock="",$_SESSION['utype']);
		break;
		case "checkQTYDLD":
			list($mySO) = getArray("SELECT DISTINCT so_no FROM so_details WHERE trace_no = '$_POST[trace_no]' AND qty_dld > 0;");
			if($mySO > 0) {
				list($ino,$inodsp) = getArray("select distinct doc_no, concat(lpad(branch,2,0),'-',lpad(doc_no,6,0)) from invoice_details where so_no = '$mySO' limit 1;");
			} else { $mySO = 0; $ino = ""; $inodsp = ""; }
			
			echo json_encode(array("mySO" => $mySO, "ino" => $ino, "inodsp" => $inodsp));
			
		break;
	}
	

	mysql_close($con);

?>