<?php
	function initBackground($i) {
		if($i%2==0){ $bgC = "#ededed"; } else { $bgC = "#ffffff"; }
		return $bgC;
	}
	
	function CRDETAILS($trans_no) {
		$i = 0;
		$details = dbquery("select line_id,lpad(doc_no,6,0) as doc_no,if(invoice_no!='0',lpad(invoice_no,6,0),'') as invoice_no,date_format(invoice_date,'%m/%d/%Y') as id8, ref_type, terms, date_format(due_date,'%m/%d/%y') as due, balance_due, amount_paid from cr_details where trans_no = '$trans_no' and branch = '$_SESSION[branchid]';");
		echo '<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">';
		while($x = mysql_fetch_array($details)) {
			echo '<tr bgcolor="'.initBackground($i).'" onmouseover="highlightTableRowVersionA(this, \'#3399ff\');" id="obj_'.$x[line_id].'" onclick="selectLine(this,\''.$x[line_id].'\');">
					<td align=center class="grid" width="10%">'.$x['doc_no'].'</td>
					<td align=center class="grid" width="15%">'.$x['id8'].'</td>
					<td align=center class="grid" width="15%">'.$x['invoice_no'].'</td>
					<td align=center class="grid" width="16%" style="padding-left: 10px;">'.$x['terms'].' Days</td>
					<td align=center class="grid" width="15%">'.$x['due'].'</td>
					<td class="grid" width="15%" align=center>'.number_format($x['balance_due'],2).'</td>
					<td class="grid" align=center>'.number_format($x['amount_paid'],2).'</td>
				</tr>'; $paidGT+=$x['amount_paid']; $i++;
		}

		if($i < 9) { for($i; $i <= 8; $i++) {
			echo '<tr bgcolor='.initBackground($i).'>
						<td align=left class="grid" width="100%" colspan=7>&nbsp;</td>
				</tr>';
			}
		}
		echo "</table>";
	}

?>