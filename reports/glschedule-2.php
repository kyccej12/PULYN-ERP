<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	include("../includes/dbUSE.php");

	$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
	$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
	if($_GET['conso'] != "Y") { $f1 = " and a.acct_branch = '$_SESSION[branchid]' "; $lbl = $bit['branch_name']; } else { $lbl = "CONSOLIDATED"; }
	if($_GET['acct'] != '') { $f2 = " and a.acct = '$_GET[acct]' "; }
	
	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$query = mysql_query("SELECT doc_no, CONCAT(lpad(a.acct_branch,2,0),'-',LPAD(doc_no,5,0)) AS dno, concat(doc_type,doc_no) as xref, DATE_FORMAT(doc_date,'%m/%d/%Y') AS dd8, doc_type, a.acct, b.description, sum(debit) as debit, sum(credit) as credit, IFNULL(CONCAT('(',LPAD(contact_id,3,0),') ',c.tradename),'') AS tradename, doc_remarks FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code LEFT JOIN contact_info c ON a.contact_id=c.file_id WHERE doc_date BETWEEN '".formatDate($_REQUEST['dtf'])."' AND '".formatDate($_REQUEST['dt2'])."' $f1 $f2 GROUP BY doc_no, doc_type, acct order by doc_date,doc_no,doc_type;");
	/* END OF SQL QUERIES */

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>$co[company_name] ERP System Ver. 1.0b</title>
	<link href="../ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="10" width="215">	
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
		<tr bgcolor="#887e6e">
			<td width="5%" align=left class="gridHead"><b>DOC #</b></td>
			<td width="5%" align=center class="gridHead"><b>DATE</b></td>
			<td width="10%" align=center class="gridHead"><b>DOC TYPE</b></td>
			<td width="15%" align=left class="gridHead"><b>CONTACT INFO</b></td>
			<td width="15%" align=left class="gridHead"><b>GL ACCOUNT</b></td>
			<td width="10%" align=right class="gridHead"><b>DEBIT</b></td>
			<td width="10%" align=right class="gridHead"><b>CREDIT</b></td>
			<td width="30%" align=left class="gridHead"><b>MEMO</b></td>
		</tr>
		<?php
			while($row = mysql_fetch_array($query)) {
				echo '<tr>
					<td align=left valign=top class="grid"><b>' . $row['dno'] . '</b></td>
					<td align=center  valign=top class="grid"><b>' . $row['dd8'] . '</b></td>
					<td align=center  valign=top class="grid">' . $row['doc_type'] . '</td>
					<td align=left  valign=top class="grid">' . $row['tradename'] . '</td>
					<td align=left  valign=top class="grid">('.$row['acct'].') ' . $row['description'] . '</td>
					<td align=right  valign=top class="grid">' . number_format($row['debit'],2) . '</td>
					<td align=right  valign=top class="grid">' . number_format($row['credit'],2) . '</td>
					<td align=left  valign=top class="grid"><i>' . $row['doc_remarks'] . '</i></td>
				</tr>'; $dbGT+=$row['debit']; $crGT+=$row['credit'];
			}
			echo '<tr>
					<td align=left valign=top class="grid" colspan=5>GRAND TOTAL &raquo;</td>
					<td align=right  valign=top class="grid"><b>' . number_format($dbGT,2) . '</b></td>
					<td align=right  valign=top class="grid"><b>' . number_format($crGT,2) . '</b></td>
					<td align=left  valign=top class="grid"></td>
				</tr>';
		?>
	</table>
</body>
</html>