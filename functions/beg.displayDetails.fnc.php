<?php
	function initBackground($i) {
		if($i%2==0){ $bgC = "#ededed"; } else { $bgC = "#ffffff"; }
		return $bgC;
	}
	
	
	function ARDETAILS($company,$branch) {
		$i = 0; $t = 0;
		$details = dbquery("select line_id, customer, lpad(customer,3,0) as cid, customer_name as cname, invoice_no, date_format(invoice_date,'%m/%d/%Y') as inv_date, po_no, date_format(po_date,'%m/%d/%Y') as po_date, amount from arbeg_details where company = '$company' and branch = '$branch' order by customer_name, invoice_date asc;");
		list($status) = getArray("select status from arbeg_header where company = '$company' and branch = '$branch';");
		
		while($x = mysql_fetch_array($details)) {
			if($status == "Active") { $dbt = '<a href="#" onclick="javascript: deleteARLine(\''.$x['line_id'].'\');" title="Delete Entry"><img src="images/icons/delete-icon.png" width=16 height=16 style="vertical-align: middle;" /></a>'; } else { $dbt = ""; }
			if($xor != $x['cid']) { 
				$cid = $x['cid']; $cname = $x['cname']; $t=0;
				list($cCount) = getArray("select count(*) from arbeg_details where company = '$_SESSION[company]' and branch = '$_SESSION[branchid]' and customer = '$x[customer]';");
			 } else { $cid = ""; $cname= ""; $t++; }
			echo '<tr bgcolor="'.initBackground($i).'">
					<td align=center class="grid" width="10%"><b>'.$cid.'</b></td>
					<td align=left class="grid" width="35%"><b>'.$cname.'</b></td>
					<td align=center class="grid" width="10%" style="padding-left: 10px;">'.$x['invoice_no'] .'</td>
					<td align=center class="grid" width="10%" style="padding-left: 10px;">'.$x['inv_date'] .'</td>
					<td align=center class="grid" width="10%" style="padding-left: 10px;">'.$x['po_no'] .'</td>
					<td align=center class="grid" width="10%" style="padding-left: 10px;">'.$x['po_date'] .'</td>
					<td class="grid" width="10%" align=right style="padding-right: 1%;">'.number_format($x['amount'],2).'</td>
					<td align=center class="grid" width="5%">'.$dbt.'</td>
				</tr>';$amtGT += $x['amount']; $xor = $x['cid']; $i++;
				
				if(($t+1) == $cCount) {
				list($cAmount) = getArray("select sum(amount) from arbeg_details where company='$_SESSION[company]' and branch= '$_SESSION[branchid]' and customer = '$x[customer]';");
				echo '<tr bgcolor="'.initBackground($i).'">
					<td align=right class="grid" colspan=6 style="border-top: 1px solid #d9d9d9;"></td>
					<td class="grid" width="10%" align=right style="padding-bottom: 1%; padding-right: 1%; border-top: 1px solid  #4a4a4a;"><b><i>'.number_format($cAmount,2).'</i></b></td>
					<td align=center class="grid" width="5%"></td>'; $i++;
				}

		}


		
		if($i < 5) { for($i; $i <= 4; $i++) {
			echo '<tr bgcolor='.initBackground($i).'>
						<td align=left class="grid" width="100%" colspan=8>&nbsp;</td>
				</tr>';
			}
		}
		
		echo '<tr>
				<td class="grid" align=right colspan="6"><b>GRAND TOTAL &raquo;</b></td>
				<td class="grid" width="10%" align=right style="font-weight: bold;padding-right: 1%;"><span id=amtGT>'.number_format($amtGT,2) . '</span></td>
				<td class="grid" align=right width=5%>&nbsp;</td>
			</tr>';
	}

	function APDETAILS($company,$branch) {
		$i = 0; $t = 0;
		$details = dbquery("select line_id, customer, lpad(customer,3,0) as cid, customer_name as cname, invoice_no, date_format(invoice_date,'%m/%d/%Y') as inv_date, po_no, date_format(po_date,'%m/%d/%Y') as po_date, amount from apbeg_details where company = '$company' and branch = '$branch' order by customer, invoice_date asc;");
		list($status) = getArray("select status from apbeg_header where company = '$company' and branch = '$branch';");
		
		while($x = mysql_fetch_array($details)) {
			if($status == "Active") { $dbt = '<a href="#" onclick="javascript: deleteAPLine(\''.$x['line_id'].'\');" title="Delete Entry"><img src="images/icons/delete-icon.png" width=16 height=16 style="vertical-align: middle;" /></a>'; } else { $dbt = ""; }
			if($xor != $x['cid']) { 
				$cid = $x['cid']; $cname = $x['cname']; $t=0;
				list($cCount) = getArray("select count(*) from apbeg_details where company = '$_SESSION[company]' and branch = '$_SESSION[branchid]' and customer = '$x[customer]';");
			 } else { $cid = ""; $cname= ""; $t++; }
			echo '<tr bgcolor="'.initBackground($i).'">
					<td align=center class="grid" width="10%"><b>'.$cid.'</b></td>
					<td align=left class="grid" width="35%"><b>'.$cname.'</b></td>
					<td align=center class="grid" width="10%" style="padding-left: 10px;">'.$x['invoice_no'] .'</td>
					<td align=center class="grid" width="10%" style="padding-left: 10px;">'.$x['inv_date'] .'</td>
					<td align=center class="grid" width="10%" style="padding-left: 10px;">'.$x['po_no'] .'</td>
					<td align=center class="grid" width="10%" style="padding-left: 10px;">'.$x['po_date'] .'</td>
					<td class="grid" width="10%" align=right style="padding-right: 1%;">'.number_format($x['amount'],2).'</td>
					<td align=center class="grid" width="5%">'.$dbt.'</td>
				</tr>';$amtGT += $x['amount']; $xor = $x['cid']; $i++;
				
				if(($t+1) == $cCount) {
				list($cAmount) = getArray("select sum(amount) from apbeg_details where company='$_SESSION[company]' and branch= '$_SESSION[branchid]' and customer = '$x[customer]';");
				echo '<tr bgcolor="'.initBackground($i).'">
					<td align=right class="grid" colspan=6 style="border-top: 1px solid #d9d9d9;"></td>
					<td class="grid" width="10%" align=right style="padding-bottom: 1%; border-top: 1px solid  #4a4a4a;"><b><i>'.number_format($cAmount,2).'</i></b></td>
					<td align=center class="grid" width="5%"></td>'; $i++;
				}

		}


		
		if($i < 5) { for($i; $i <= 4; $i++) {
			echo '<tr bgcolor='.initBackground($i).'>
						<td align=left class="grid" width="100%" colspan=8>&nbsp;</td>
				</tr>';
			}
		}
		
		echo '<tr>
				<td class="grid" align=right colspan="6"><b>GRAND TOTAL &raquo;</b></td>
				<td class="grid" width="10%" align=right style="font-weight: bold;padding-right: 1%;"><span id=amtGT>'.number_format($amtGT,2) . '</span></td>
				<td class="grid" align=right width=5%>&nbsp;</td>
			</tr>';
	}

?>