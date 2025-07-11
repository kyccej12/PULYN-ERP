<?php
	session_start();
	require_once("handlers/_rfpfunct.php");
	$con = new myRFP;


	switch($_POST['mod']) {
		case "saveHeader":
			list($a) = $con->getArray("select count(*) from rfp_header where rfp_no = '$_POST[rfp_no]' and branch = '$_SESSION[branchid]';");
			if($a > 0) {
				$con->dbquery("update ignore rfp_header set date_needed = '".$con->formatDate($_POST['date_needed'])."',rfp_date = '".$con->formatDate($_POST['rfp_date'])."', supplier='$_POST[cid]', supplier_name='".$con->escapeString(htmlentities($_POST['cname']))."', supplier_addr='".$con->escapeString(htmlentities($_POST['addr']))."',requested_by='".$con->escapeString(htmlentities($_POST['requested_by']))."',remarks='".$con->escapeString($_POST['remarks'])."',updated_by='$_SESSION[userid]', updated_on=now() where rfp_no='$_POST[rfp_no]' and branch = '$_SESSION[branchid]';");
			} else {
				$con->dbquery("insert ignore into rfp_header (date_needed,rfp_no,rfp_date,supplier,supplier_name,supplier_addr,requested_by,remarks,created_by,created_on,branch) values ('".$con->formatDate($_POST['date_needed'])."','$_POST[rfp_no]','".$con->formatDate($_POST['rfp_date'])."','$_POST[cid]','".$con->escapeString(htmlentities($_POST['cname']))."','".$con->escapeString(htmlentities($_POST['addr']))."','".$con->escapeString(htmlentities($_POST['requested_by']))."','".$con->escapeString(htmlentities($_POST['remarks']))."','$_SESSION[userid]',now(),'$_SESSION[branchid]');");
			}
		break;
		case "getInvoices":
		
			list($b) = $con->getArray("select count(*) from apv_header where `status` = 'Posted' and supplier = trim(LEADING 0 from '$_POST[cid]') and with_rfp != 'Y';");
			if($b > 0) {

				echo "<table width=100% cellpadding=2 cellspacing=0>
						<tr>
							<td class=gridHead width=10%>APV #</td>
							<td class=gridHead align=center width=8%>APV DATE</td>
							
							<td class=gridHead width=26%>REMARKS</td>
							<td class=gridHead align=center width=8%>DUE DATE</td>
							<td class=gridHead width=10% align=right style=\"padding-right: 20px;\">GROSS</td>
							<td class=gridHead align=right width=8% style=\"padding-right: 20px;\">VAT</td>
							<td class=gridHead align=right>EWT</td>
							<td class=gridHead align=right width=10%>NET PAYABLE</td>
							<td class=gridHead width=10>&nbsp;</td>
						</tr>
					";

				$c = $con->dbquery("select cy, apv_no, concat(cy,'-',lpad(apv_no,6,0)) as ap, date_format(apv_date,'%m/%d/%Y') as ad8, date_format(DATE_ADD(apv_date, INTERVAL terms DAY),'%m/%d/%Y') as due_date, amount as net_payable, ROUND(amount+ewt_amount,2) as gross, vat, ewt_amount, remarks from apv_header where `status`='Posted' and supplier = trim(LEADING 0 from '$_POST[cid]') and with_rfp != 'Y';");
				$i = 0; $t = 0;
				while($d = $c->fetch_array()) {
					$checked = "";
					$needle = $d['cy']."|".$d['apv_no'];
					if(isset($_SESSION['ques'])) {
						if(in_array($needle, $_SESSION['ques'])) {
							$checked = "checked"; 
						}
					}

					echo "<tr bgcolor='".$con->initBackground($i)."'>
							<td class=grid valign=top>$d[ap]</td>
							<td class=grid valign=top align=center>$d[ad8]</td>
							<td class=grid align=left valign=top>$d[remarks]</td>
							<td class=grid valign=top align=center>$d[due_date]</td>
							<td class=grid align=right valign=top>".number_format($d['gross'],2)."</td>
							<td class=grid valign=top align=right>".number_format($d['vat'],2)."</td>
							<td class=grid align=right valign=top>".number_format($d['ewt_amount'],2)."</td>
							<td class=grid align=right valign=top>".number_format($d['net_payable'],2)."</td>
							<td valign=top><input type='checkbox' id='_$i' value='$needle' onclick='tagRR(this.id,this.value);' $checked></td>
						</tr>"; $i++;
				}
				if($i < 5) {
						for($i;$i <=5; $i++) {
							echo "<tr  bgcolor='".$con->initBackground($i)."'><td colspan=9 class=grid>&nbsp;</td></tr>";
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
		case "loadInvoice2AP":
			if(count($_SESSION['ques']) > 0) {
				foreach($_SESSION['ques'] as $index) {
					$netGT = 0; $grossGT = 0;
					$subindex = explode("|",$index);
					list($cy,$apv_no) = $subindex;
					
					$sh = $con->dbquery("select apv_date, remarks, date_add(apv_date,INTERVAL terms DAY) as duedate, amount as net_payable, ROUND(amount+ewt_amount,2) as gross, vat, ewt_amount from apv_header where apv_no = '$apv_no';");
					while($_sh = $sh->fetch_array()) {
						list($_hs) = $con->getArray("select count(*) from apv_subheader where apv_no = '$apv_no' and cy = '$cy';");
						if($_hs > 0) {
							$_hss = $con->dbquery("select concat('Inv # <b>',invoice_no,'</b> dtd. <b>',date_format(invoice_date,'%m/%d/%Y'),'</b>') as st from apv_subheader where apv_no = '$apv_no';");
							while($_z = $_hss->fetch_array()) { $remarks = $remarks . $_z[0] . '; '; }
						} else { $remarks = $_hs['remarks']; }
						$con->dbquery("insert ignore into rfp_details (rfp_no,apv_no,apv_date,apv_remarks,due_date,amount,vat,ewt,net_payable,branch) values ('$_POST[rfp_no]','$apv_no','$_sh[apv_date]','".$con->escapeString($remarks)."','$_sh[duedate]','$_sh[gross]','$_sh[vat]','$_sh[ewt_amount]','$_sh[net_payable]','$_SESSION[branchid]');");
					}
					$con->dbquery("update apv_header set with_rfp = 'Y', rfp_no = '$_POST[rfp_no]', rfp_date = '".$con->formatDate($_POST['rfp_date'])."' where apv_no = '$apv_no';");
				}
				$con->RFPDETAILS($_POST['rfp_no']);
				$con->updateHeadAmount($_POST['rfp_no']);
				unset($_SESSION['ques']);
			}
		break;
		case "deleteline":
			list($apv_no,$cy) = $con->getArray("select apv_no, date_format(apv_date,'%Y') as cy from rfp_details where line_id = '$_POST[lid]';");
			$con->dbquery("update ignore apv_header set with_rfp = 'N', rfp_no = '', rfp_date='' where apv_no = '$apv_no' and cy = '$cy';");
			$con->dbquery("delete from rfp_details where line_id = '$_POST[lid]';");
			$con->updateHeadAmount($_POST['rfp_no']);
			$con->RFPDETAILS($_POST['rfp_no']);
		break;
		case "getSummaryValues":
			$con->getSummaryValues($_POST['rfp_no'],$status='Active');
			echo json_encode(array("total"=>$con->total,"vat"=>$con->vat,"ewt"=>$con->ewt,"net"=>$con->net));
		break;
		case "check4print":
			list($aaa) = $con->getArray("select count(*) from rfp_details where rfp_no = '$_POST[rfp_no]';");
			echo $aaa;
		break;
		case "finalizeRFP":
			$con->dbquery("update rfp_header set status = 'Finalized', updated_by = '$_SESSION[userid]', updated_on = now() where rfp_no = '$_POST[rfp_no]';");
			$con->updateHeadAmount($_POST['rfp_no']);
		break;
		case "reopenRFP":
			$con->dbquery("update rfp_header set status = 'Active', updated_by = '$_SESSION[userid]', updated_on = now() where rfp_no = '$_POST[rfp_no]';");
			$con->trailer("Request for Payment","ReOpen Request for Payment(TERMS) No. $_POST[rfp_no]', Reason : ".$con->escapeString(htmlentities($_POST['remarks'])));
		break;
		case "cancel":
			$con->dbquery("update ignore rfp_header set status='Cancelled', updated_by='$_SESSION[userid]', updated_on = now() where rfp_no ='$_POST[rfp_no]' ;");
			$con->trailer("Request for Payment","Cancel Request for Payment(TERMS) No. $_POST[rfp_no]', Reason : ".$con->escapeString(htmlentities($_POST['remarks'])));
		
		break;
		case "getDocInfo":
			$m = $con->getArray("select a.status,if(a.status='Cancelled','Cancelled By',if(a.status='Finalized','Finalized By','Last Updated By')) as lbl, a.status,if(a.status='Cancelled','Cancelled On',if(a.status='Finalized','Finalized On','Last Updated On')) as lbl2,b.fullname as cby, date_format(created_on,'%m/%d/%Y %r') as con, c.fullname as uby, date_format(updated_on,'%m/%d/%Y %r') as uon from rfp_header a left join user_info b on a.created_by=b.emp_id left join user_info c on a.updated_by=c.emp_id where rfp_no = '$_POST[rfp_no]' and a.branch = '$_SESSION[branchid]';");
			$n = $con->dbquery("select a.rr_no,with_ap,if(with_ap = 'Y',concat('AP-',a.apv_no),if(with_cv='Y',concat('CV-',a.cv_no),'')) as doc_no, if(with_ap='Y',date_format(c.apv_date,'%m/%d/%y'),if(with_cv='Y',date_format(d.cv_date,'%m/%d/%y'),'')) as doc_date,  date_format(a.rr_date,'%m/%d/%y') as rd8 from rr_header a left join rr_details b on a.rr_no=b.rr_no and a.branch=b.branch and a.company=b.company left join apv_header c on a.apv_no=c.apv_no and a.branch=c.branch and a.company=c.company left join cv_header d on a.cv_no=d.cv_no a.branch=d.branch where b.po_no = '$_POST[po_no]' and a.branch = '$_SESSION[branchid]' group by a.rr_no;");
			while(list($o,$p,$u,$v,$w) = $n->fetch_array()) {
				if($o != "") { $q = $q . " RR # $o Dtd. $w;"; }
				if($u != "" && $u != $ou) { $z = $z . " $u Dtd. $v;"; }

				if($p = "Y") {
					$doc = explode("-",$u);
					$f = $con->dbquery("select a.cv_no, date_format(a.cv_date,'%m/%d/%y') as cd8 from cv_header a left join cv_details b on a.cv_no=b.cv_no and a.branch=b.branch where b.ref_no = '$doc[1]' and b.ref_type='AP' and b.acct_branch = '$_SESSION[branchid]';");
					while(list($g,$h) = $f->fetch_array()) {
						$t = $t . "CV # $g Dtd. $h;";					}
				}
				$ou = $u;
			}

			if($q == "") { $q = "None "; }
			if($t == "") { $t = "None "; }
			if($z == "") { $z = "None "; }

			echo "<table width=100% cellpadding=2 cellspacing=0 style='font-size: 11px;'>
					<tr>
						<td width='35%'>Created By</td>
						<td width=5>:</td>
						<td style='padding-left:10px;'>$m[cby]</td>
					</tr>
					<tr>
						<td>Created On</td>
						<td width=5>:</td>
						<td style='padding-left:10px;'>$m[con]</td>
					</tr>
					<tr>
						<td>$m[lbl]</td>
						<td width=5>:</td>
						<td style='padding-left:10px;'>$m[uby]</td>
					</tr>
					<tr>
						<td>$m[lbl2]</td>
						<td width=5>:</td>
						<td style='padding-left:10px;'>$m[uon]</td>
					</tr>
					<tr>
						<td valign=top>RR Reference</td>
						<td valign=top width=5 valign=top>:</td>
						<td style='padding-left:10px;' valign=top>".substr_replace($q, "", -1)."</td>
					</tr>
					<tr>
						<td valign=top>AP Reference</td>
						<td valign=top width=5>:</td>
						<td style='padding-left:10px;'>".substr_replace($z, "", -1)."</td>
					</tr>
					<tr>
						<td valign=top>CV Reference</td>
						<td valign=top width=5>:</td>
						<td style='padding-left:10px;'>".substr_replace($t, "", -1)."</td>
					</tr>
				  </table>";

		break;
	}
?>