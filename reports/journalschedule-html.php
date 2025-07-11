<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	
	//ini_set("display_errors","On");
	require_once "../handlers/_generics.php";
	$mydb = new _init;
	
	
	switch($_GET['type']) { 
		case "CR": $lbl = "Collection Receipts Journal"; $f1 = "and a.doc_type = 'CR' "; break;
		case "SI": $lbl = "Sales Journal (Billing Statements)"; $f1 = "and a.doc_type = 'SOA' "; break;
		case "CV": $lbl = "Cash/Check Disbursement Journal"; $f1 = "and a.doc_type = 'CV' "; break;
		case "AP": $lbl = "Accounts Payable Journal"; $f1 = "and a.doc_type = 'AP' "; break;
		case "JV": $lbl = "General (JV) Journal"; $f1 = "and a.doc_type = 'JV' "; break;
		case "DA": $lbl = "Debit/Credit Advise Journal"; $f1 = "and a.doc_type = 'JV' "; break;
		case "APB": $lbl = "Accounts Payable - Beginning Balance"; $f1 = "and a.doc_type = 'APB' "; break;
		case "ARB": $lbl = "Accounts Receivable - Beginning Balance"; $f1 = "and a.doc_type = 'ARB' "; break;
	}
	
	if($_GET['acct'] != '') { $f2 = " and a.acct = '$_GET[acct]' "; }

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
		$query = $mydb->dbquery("SELECT doc_no, CONCAT(LPAD(branch,2,0),'-',LPAD(doc_no,5,0)) AS dno, DATE_FORMAT(doc_date,'%m/%d/%Y') AS dd8, a.acct, b.description, debit, credit, c.tradename, contact_id, CONCAT(a.branch,'-',doc_no) AS xdoc, doc_remarks FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code LEFT JOIN contact_info c ON a.contact_id=c.file_id WHERE doc_date BETWEEN '".$mydb->formatDate($_GET['dtf'])."' AND '".$mydb->formatDate($_GET['dt2'])."' $f1 $f2 $f3 order by doc_no asc;");
	/* END OF SQL QUERIES */

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="../ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#ffffff" margin=10 width="215">	
	<?php echo '<table width="100%" style="padding: 10px;">
		<tr>
			<td style="color:#000000;" width=80><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
			<td style="color:#000000; padding-top: 15px;">
				<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
			</td>
			<td width="40%" align=right>
				<span style="font-weight: bold; font-size: 11px; color: #000000;">'.$lbl.'<br/>Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
			</td>
		</tr>
	</table>';
	?>
	<table cellspacing=0 cellpadding=0 border=0 width=100% cellspacing=5>	
		<tr bgcolor="#887e6e">
			<td width="5%" align=left class="gridHead"><b>DOC #</b></td>
			<td width="5%" align=center class="gridHead"><b>DATE</b></td>
			<td width="10%" align=center class="gridHead"><b>CLIENT</b></td>
			<td width="15%" align=left class="gridHead"><b>GL ACCOUNT</b></td>
			<td width="10%" align=right class="gridHead"><b>DEBIT</b></td>
			<td width="10%" align=right class="gridHead"><b>CREDIT</b></td>
			<td width="30%" align=left class="gridHead" style="padding-left: 20px;"><b>MEMO</b></td>
		</tr>
		<?php
			while($row = $query->fetch_array(MYSQLI_BOTH)) {

				if($xdoc != $row['xdoc']) { $memo = $row['doc_remarks']; $dno = $row['dno']; $d8 = $row['dd8']; $cust = $row['tradename']; } else { $memo = ""; $dno = ""; $d8 = ""; $cust = ""; }
				echo '
					<tr>
						<td align=left class="grid"><b>' . $dno . '</b></td>
						<td align=center class="grid"><b>' . $d8 . '</b></td>
						<td align=left class="grid"><b>' . $cust . '</b></td>
						<td align=left class="grid">('.$row['acct'].') ' . $row['description'] . '</td>
						<td align=right class="grid">' . number_format($row['debit'],2) . '</td>
						<td align=right class="grid">' . number_format($row['credit'],2) . '</td>
						<td align=left class="grid" style="padding-left: 20px;"><i>' . $memo . '</i></td>
					</tr>';  $dbGT+=$row['debit']; $crGT+=$row['credit']; $xdoc = $row['xdoc'];
			}
			echo '<tr>
					<td align=left valign=top class="grid" colspan=4>GRAND TOTAL &raquo;</td>
					<td align=right  valign=top class="grid"><b>' . number_format($dbGT,2) . '</b></td>
					<td align=right  valign=top class="grid"><b>' . number_format($crGT,2) . '</b></td>
					<td align=left  valign=top class="grid"></td>
				</tr>';
		?>
	</table>
</body>
</html>