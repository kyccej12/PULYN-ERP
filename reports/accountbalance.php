<?php
	session_start();
	ini_set("max_execution_time",0);
	//ini_set("display_errors","On");
	include "../handlers/_generics.php";
	
	$mydb = new _init;

	$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
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
				<span style="font-weight: bold; font-size: 9pt; color: #000000;">Account Balance</span><br /><span style="font-size: 6pt; font-style: italic;"><b>('.$_GET['cid'].') '.$mydb->getContactName($_GET['cid']).'<br/>('.$_GET['acct'].') '.$mydb->getAcctDesc($_GET['acct'],'$_SESSION[company]').'</b><br/>Date As Of ' . $_GET['asof']  .'</span>
			</td>
		</tr>
	</table>';
	?>
	<table cellspacing=0 cellpadding=0 border=0 width=100% cellspacing=5>	
	<tr>
		<td class="gridHead" width="5%" align=left><b>DATE</b></td>
		<td class="gridHead" width="8%" align=center><b>TRANS. #</b></td>
		<td class="gridHead" width="8%" align=center><b>JOURNAL</b></td>
		<td class="gridHead" align=left><b>TRANSACTION REMARKS</b></td>
		<td class="gridHead" width="10%" align=center><b>REF. #</b></td>
		<td class="gridHead" width="10%" align=center><b>REF. DATE</b></td>
		<td class="gridHead" width="10%" align=right><b>DEBIT</b></td>
		<td class="gridHead" width="10%" align=right><b>CREDIT</b></td>
		<td class="gridHead" width="10%" align=right><b>APPLIED AMOUNT</b></td>
		<td class="gridHead" width="10%" align=right><b>BALANCE</b></td>
	</tr>
		<?php
			$i = 1;
				
				$inQuery = $mydb->dbquery("SELECT doc_no, doc_date, CONCAT(cy,'-',LPAD(doc_no,5,0)) AS dno, DATE_FORMAT(doc_date,'%m/%d/%Y') AS dd8, doc_type, debit, credit, doc_remarks, CONCAT(DATE_FORMAT(ref_date,'%Y'), ref_no,ref_type) AS xreference, CONCAT(DATE_FORMAT(doc_date,'%Y'),doc_no,doc_type) AS xdocreference, CONCAT(ref_type,'-',LPAD(ref_no,5,0)) AS xref, DATE_FORMAT(ref_date,'%m/%d/%Y') AS xrefdate FROM acctg_gl a WHERE doc_date <= '".$mydb->formatDate($_GET['asof'])."' AND a.acct = '$_GET[acct]' AND contact_id = '$_GET[cid]' ORDER BY a.doc_date ASC, a.doc_type, a.doc_no ASC;");
				
				while($inrow = $inQuery->fetch_array(MYSQLI_BOTH)) {
					
					if($inrow['debit'] > 0) { $applied = "sum(credit-debit)"; } else { $applied = "sum(debit-credit)"; }
					
					if($inrow['xreference'] != $inrow['xdocreference']) {
						$balance = 0; $appliedAmount = 0; 
						$ref = $inrow['xref']; $refdate = $inrow['xrefdate'];
					} else {
						$ref = ''; $refdate = '';
						list($appliedAmount) = $mydb->getArray("select $applied from acctg_gl where ref_type = '$inrow[doc_type]' and ref_no = '$inrow[doc_no]' and ref_date = '$inrow[doc_date]';");
						$balance = $inrow['debit'] - $inrow['credit'] - $appliedAmount;
						$mydb->dbquery("update acctg_gl set applied_amount = 0$appliedAmount where doc_no = '$inrow[doc_no]' and doc_type = '$inrow[doc_type]' and acct = '$_GET[acct]' and contact_id = '$_GET[cid]';");
						
					}
					
					echo '<tr bgcolor='.$mydb->initBackground($i).'>
						<td align=left class="grid">' . $inrow['dd8'] . '</td>
						<td align=center class="grid">' . $inrow['dno'] . '</td>
						<td align=center class="grid">' . $inrow['doc_type'] . '</td>
						<td align=left class="grid"><i>' . $inrow['doc_remarks'] . '</i></td>
						<td align=center class="grid">' . $ref . '</td>
						<td align=center class="grid">' . $refdate . '</td>
						<td align=right class="grid">' . number_format($inrow['debit'],2) . '</td>
						<td align=right class="grid">' . number_format($inrow['credit'],2) . '</td>
						<td align=right class="grid">' . number_format($appliedAmount,2) . '</td>
						<td align=right class="grid">' . number_format($balance,2) . '</td>
					</tr>'; $dbGT+=$inrow['debit']; $crGT+=$inrow['credit']; $appliedGT+=$appliedAmount; $balanceGT+=$balance; $i++;
				}
				echo '<tr>
						<td align=left class="gridHead" colspan=6>GRAND TOTAL</td>
						<td align=right class="gridHead">' . number_format($dbGT,2) . '</td>
						<td align=right class="gridHead">' . number_format($crGT,2) . '</td>
						<td align=right class="gridHead">' . number_format($appliedGT,2) . '</td>
						<td align=right class="gridHead">' . number_format($balanceGT,2) . '</td>
					</tr>';
		?>
		<tr><td colspan=8 style="padding: 20px;"><a href="#" onclick="javascript: window.print();" style="font-size: 11px;"><img src="../images/print.png" width=16 height=16>&nbsp;Print</a></td></tr>
	</table>
</body>
</html>