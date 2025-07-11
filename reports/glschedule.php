<?php
	session_start();
	ini_set("max_execution_time",0);
	//ini_set("display_errors","On");
	include "../handlers/_generics.php";
	
	$mydb = new _init;

	$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
	if($_GET['acct'] != '') { $f1 = " and a.acct = '$_GET[acct]' "; }
	if($_GET['client'] != '') { $f2 = " and a.contact_id = '$_GET[client]' "; }
	$now = date("m/d/Y h:i a");

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="../ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#ffffff" margin="10" width="215">	
	<?php echo '<table width="100%">
		<tr>
			<td style="color:#000000; padding-top: 15px;">
				<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
			</td>
			<td width="40%" align=right>
				<span style="font-weight: bold; font-size: 9pt; color: #000000;">GL Account Schedule</span><br /><span style="font-size: 6pt; font-style: italic;"><b>'.$lbl.'</b><br/>Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
			</td>
		</tr>
	</table>';
	?>
	<table cellspacing=0 cellpadding=0 border=0 width=100% cellspacing=5>	
	<tr>
		<td class="gridHead" width="5%" align=left><b>DATE</b></td>
		<td class="gridHead" width="8%" align=center><b>TRANS. #</b></td>
		<td class="gridHead" width="8%" align=center><b>JOURNAL</b></td>
		<td class="gridHead" width="15%" align=left><b>CLIENT/SUPPLIER</b></td>
		<td class="gridHead"  width="15%" align=center><b>TIN #</b></td>
		<td class="gridHead" align=left><b>TRANSACTION REMARKS</b></td>
		<td class="gridHead" width="10%" align=center><b>REF. #</b></td>
		<td class="gridHead" width="10%" align=center><b>REF. DATE</b></td>
		<td class="gridHead" width="10%" align=right><b>DEBIT</b></td>
		<td class="gridHead" width="10%" align=right><b>CREDIT</b></td>
		<td class="gridHead" width="10%" align=right><b>BALANCE</b></td>
	</tr>
		<?php
			$i = 1;
			$query = $mydb->dbquery("SELECT DISTINCT acct from acctg_gl a where a.acct != '' and doc_date <= '".$mydb->formatDate($_GET['dt2'])."' $f1 $f2 ORDER BY acct");
			while($row = $query->fetch_array(MYSQLI_BOTH)) {
				list($acctDesc) = $mydb->getArray("select description from acctg_accounts where acct_code = '$row[acct]';");
				list($acctBB) = $mydb->getArray("select sum(debit-credit) as amount from acctg_gl a where doc_date < '".$mydb->formatDate($_GET['dtf'])."' AND a.acct = '$row[acct]' $f2;");
				echo "<tr bgcolor=\"".$mydb->initBackground($i)."\"><td colspan=4 class=\"grid\"><b>($row[acct]) $acctDesc</b></td><td colspan=5 align=right class=\"grid\"><b>Account Beginning Balance &raquo;</b></td><td colspan=2 align=right class=\"grid\"><b>".$mydb->formatNumber($acctBB,2)."</b></td></tr>";
				$dbGT = 0; $crGT = 0;
				$inQuery = $mydb->dbquery("SELECT doc_no, CONCAT(cy,'-',LPAD(doc_no,5,0)) AS dno, DATE_FORMAT(doc_date,'%m/%d/%Y') AS dd8, doc_type, debit, credit, IFNULL(CONCAT('(',LPAD(contact_id,3,0),') ',c.tradename),'') AS tradename, c.tin_no, doc_remarks, CONCAT(DATE_FORMAT(ref_date,'%Y'),ref_no,ref_type) AS xreference, CONCAT(DATE_FORMAT(doc_date,'%Y'),doc_no,doc_type) AS xdocreference, CONCAT(ref_type,'-',LPAD(ref_no,5,0)) AS xref, DATE_FORMAT(ref_date,'%m/%d/%Y') AS xrefdate FROM acctg_gl a LEFT JOIN contact_info c ON a.contact_id=c.file_id WHERE doc_date BETWEEN '".$mydb->formatDate($_GET['dtf'])."' AND '".$mydb->formatDate($_GET['dt2'])."' AND a.acct = '$row[acct]' $f2 order by a.doc_date asc, a.doc_type, a.doc_no asc;");
				
				while($inrow = $inQuery->fetch_array(MYSQLI_BOTH)) {
					
					if($inrow['xdocreference'] !== $inrow['xreference']) { $ref = $inrow['xref']; $refdate = $inrow['xrefdate']; } else { $ref = ''; $refdate = ''; }
					
					echo '<tr bgcolor='.$mydb->initBackground($i).'>
						<td align=left class="grid">' . $inrow['dd8'] . '</td>
						<td align=center class="grid">' . $inrow['dno'] . '</td>
						<td align=center class="grid">' . $inrow['doc_type'] . '</td>
						<td align=left class="grid">' . $inrow['tradename'] . '</td>
						<td align=center class="grid">' . $inrow['tin_no'] . '</td>
						<td align=left class="grid"><i>' . $inrow['doc_remarks'] . '</i></td>
						<td align=center class="grid">' . $ref . '</td>
						<td align=center class="grid">' . $refdate . '</td>
						<td align=right class="grid">' . number_format($inrow['debit'],2) . '</td>
						<td align=right class="grid">' . number_format($inrow['credit'],2) . '</td>
						<td align=left class="grid">&nbsp;</td>
					</tr>'; $dbGT+=$inrow['debit']; $crGT+=$inrow['credit'];
				}
				
				echo "<tr bgcolor=\"".$mydb->initBackground($i)."\">
					<td colspan=8 align=right class=\"grid\"><br/><b>Account Subtotal &raquo;<b></td>
					 <td align=right class=\"grid\">---------------<br/><b>".number_format($dbGT,2)."</b><br/>==========</td>
					 <td align=right class=\"grid\">---------------<br/><b>".number_format($crGT,2)."</b><br/>==========</td>
					 <td></td>
			     </tr>
				 <tr bgcolor=\"".$mydb->initBackground($i)."\">
					<td colspan=9 align=right class=\"grid\"><b>Account Net Change &raquo;</b></td><td colspan=2 align=right class=\"grid\"><b>".$mydb->formatNumber(($dbGT-$crGT),2)."</b></td>
				</tr>
				 <tr bgcolor=\"".$mydb->initBackground($i)."\">
					<td colspan=9 align=right class=\"grid\"><b>Account Ending Balance &raquo;</b></td><td colspan=2 align=right class=\"grid\"><b>".$mydb->formatNumber(($acctBB + ($dbGT-$crGT)),2)."</b></td>
				</tr>"; $i++;
			}
		?>
		<tr><td colspan=10 style="padding: 20px;"><a href="#" onclick="javascript: window.print();" style="font-size: 11px;"><img src="../images/print.png" width=16 height=16>&nbsp;Print</a></td></tr>
	</table>
</body>
</html>