<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	include("../includes/dbUSE.php");
	

	$now = date("m/d/Y h:i a");
	$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
	$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
		
	list($cname) = getArray("select tradename from contact_info where file_id = '$_REQUEST[cid]';");
	if($_GET['cid'] != "") { $fs1 = " and a.customer = '$_GET[cid]' "; }
	if($_GET['branch'] != 'undefined') { 
		if($_GET['branch'] != '') {
			$fs2 = " and a.branch = '$_GET[branch]' "; 
			list($myBranch) = getArray("select branch_name from options_branches where branch_code = '$_GET[branch]';"); 
		} else { $myBranch = "Consolidated"; }
	} else { $myBranch = $bit['branch_name']; $fs2 = " and a.branch = '$_SESSION[branchid]' "; }
	
	if($_GET['group'] != '') { $fs3 = " and c.group = '$_GET[group]' "; list($myGroup) = getArray("select concat('<br/>',group_description,' Products <br/>') from options_igroup where `group` = '$_GET[group]';"); } else { $fs3 = ''; $myGroup = '<br>All Products</br>'; }
	$query = mysql_query("select concat(lpad(a.branch,2,0),'-',a.doc_no) as invoice_no, date_format(invoice_date,'%m/%d/%Y') as id8, b.item_code, b.description, b.unit, b.qty, (b.cost-b.discount) as cost, ROUND(qty * (b.cost-b.discount),2) as amount from invoice_header a INNER JOIN invoice_details b on a.trace_no = b.trace_no INNER JOIN products_master c on b.item_code = c.item_code where a.invoice_date between '".formatDate($_GET['dtf'])."' and '".formatDate($_GET['dt2'])."' and a.status = 'Finalized' $fs1 $fs2 $fs3;");
	//echo "select concat(lpad(a.branch,2,0),'-',a.doc_no) as invoice_no, date_format(invoice_date,'%m/%d/%Y') as id8, b.item_code, b.description, b.unit, b.qty, (b.cost-b.discount) as cost, ROUND(qty * (b.cost-b.discount),2) as amount from invoice_header a INNER JOIN invoice_details b on a.trace_no = b.trace_no INNER JOIN products_master c on b.item_code = c.item_code where a.invoice_date between '".formatDate($_GET['dtf'])."' and '".formatDate($_GET['dt2'])."' and a.status = 'Finalized' $fs1 $fs2 $fs3;";
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>$co[company_name] ERP System Ver. 1.0b</title>
	<link href="../ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#ffffff" leftmargin="10" bottommargin="100" rightmargin="20" topmargin="10" width="215">	
	<?php echo '<table width="100%">
		<tr>
			<td style="color:#000000;" width=80><img src="../images/'.$co['headerlogo'].'" height=70 /></td>
			<td style="color:#000000; padding-top: 15px;">
				<b>'.$co['company_name'].'</b><br/><span style="font-size: 6pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'<br/>'.$co['website'].'<br/>VAT REG. TIN: '.$co['tin_no'].'</span>
			</td>
			<td width="40%" align=right>
				<span style="font-weight: bold; font-size: 9pt; color: #000000;">Customer Detailed Sales</span><br /><span style="font-weight: bold; font-size: 9pt; color: #000000;">('.$_REQUEST['cid'].') '.$cname.'</span><br/><span style="font-size: 6pt; font-style: italic;">'.$myBranch.$myGroup.'<br/>Date Covered ' . $_GET['dtf'] . ' - ' . $_GET['dt2'] .'</span>
			</td>
		</tr>
	</table>';
	?>
	<table cellspacing=0 cellpadding=0 border=0 width=100% cellspacing=5>	
		<tr bgcolor="#887e6e">
			<td width="12%" align=left class="gridHead"><b>TRANS #</b></td>
			<td width="12%" align=left class="gridHead"><b>DATE</b></td>
			<td width="10%" align=left class="gridHead"><b>ITEM CODE</b></td>
			<td width="25%" align=left class="gridHead"><b>DESCRIPTION</b></td>
			<td width="5%" align=center class="gridHead"><b>UNIT</b></td>
			<td width="12%" align=right class="gridHead"><b>QTY</b></td>
			<td width="12%" align=right class="gridHead"><b>PRICE</b></td>
			<td width="12%" align=right class="gridHead"><b>AMOUNT</b></td>
		</tr>
		<?php
			while($row = mysql_fetch_array($query)) {
				if($row['invoice_no']!=$xno) { $ino = $row['invoice_no']; $idate = $row['id8']; $cname = '('.$row['customer'].') ' . $row['customer_name']; } else { $ino = ""; $idate =''; $cname = ""; }
				
				
				echo '<tr>
					<td align=left class="grid">'. $ino . '</td>
					<td align=left class="grid">' . $idate . '</td>
					<td align=left class="grid">' . $row['item_code'] . '</td>
					<td align=left class="grid">' . $row['description'] . '</td>
					<td align=center class="grid">' . $row['unit'] . '</td>
					<td align=right class="grid">' . number_format($row['qty'],2) . '</td>
					<td align=right class="grid">' . number_format($row['cost'],2) . '</td>
					<td align=right class="grid">' . number_format($row['amount'],2) . '</td>
				</tr>'; $amtGT+=$row['amount']; $xno = $row['invoice_no'];
			}
			echo '<tr>
					<td align=left valign=top class="grid" colspan=7 style="border-top: 1px solid #4a4a4a; border-bottom: 1px solid #4a4a4a;"><b>GRAND TOTAL &raquo;</b></td>
					<td align=right  valign=top class="grid" style="border-top: 1px solid #4a4a4a; border-bottom: 1px solid #4a4a4a;"><b>' . number_format($amtGT,2) . '</b></td>
				</tr>';
		?>
	</table>
</body>
</html>