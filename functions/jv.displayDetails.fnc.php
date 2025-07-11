<?php
		function JVDETAILS($j_no,$trace_no) {
			$i = 0; $t = 0;
			list($status,$lock) = getArray("select status,locked from journal_header where j_no = '$j_no' and company='$_SESSION[company]' and branch='$_SESSION[branchid]';");
			if($trace_no==''){
				$details = dbquery("select record_id as line_id,  ref_no, if(ref_date='0000-00-00','',date_format(ref_date,'%m/%d/%Y')) as rd8, ref_type, concat(ref_no,ref_type) as xchecker, client as myclient, if(client='0','',lpad(client,3,0)) as xclient, client, acct, acct_desc, debit, credit, if(cost_center='','...',cost_center) as cost_center from journal_details where j_no in ('$j_no','0') and trace_no is null and company='$_SESSION[company]' and branch='$_SESSION[branchid]' order by debit, acct_desc;");
			}else{
				$details = dbquery("select record_id as line_id,  ref_no, if(ref_date='0000-00-00','',date_format(ref_date,'%m/%d/%Y')) as rd8, ref_type, concat(ref_no,ref_type) as xchecker, client as myclient, if(client='0','',lpad(client,3,0)) as xclient, client, acct, acct_desc, debit, credit, if(cost_center='','...',cost_center) as cost_center from journal_details where j_no in ('$j_no','0') and trace_no = '$trace_no' and company='$_SESSION[company]' and branch='$_SESSION[branchid]' order by debit, acct_desc;");
			}
			
			echo '<table width=100% cellspacing=0 cellpadding=0>';
			while($x = mysql_fetch_array($details)) {

				if(($status == 'Active' || $status == '') && $lock != 'Y') { 
					$dbt = '<a href="#" onclick="javascript: deleteLine(\''.$x['line_id'].'\',\''.$j_no.'\');" title="Delete Invoice From Accounts Payable Voucher"><img src="images/icons/delete-icon.png" width=16 height=16 style="vertical-align: middle;" /></a>'; 
					$center = "<a href=\"#\" style=\"text-decoration: none; color: black;\" onclick=\"javascript: changeCostCenter($x[line_id],'$x[cost_center]');\">$x[cost_center]</a>";
				} else {
					$dbt = ""; 
					$center = $x['cost_center'];
				}
				if($x['client'] != "" || $x['client'] != 0) { $title = getContactName($x['myclient']); } else { $title = ""; }
				echo '<tr bgcolor="'.initBackground($i).'">
						<td align=center class="grid" width="8%"><b>'.$x['ref_no'].'</b></td>
						<td align=center class="grid" width="8%"><b>'.$x['rd8'].'</b></td>
						<td align=center class="grid" width="8%" style="padding-left: 10px;">'.$x['ref_type'] .'</td>
						<td align=center class="grid" width="8%" title="'.$title.'">'.$x['xclient'] .'</td>
						<td align=left class="grid" width="7%" style="padding-left: 10px;">'.$x['acct'].'</td>
						<td align=left class="grid" idth="17%">'.$x['acct_desc'].'</td>
						<td align=center class="grid" width="10%" id="c_'.$x['line_id'].'">'.$center.'</td>
						<td class="grid" width="8%" align=right style="padding-right: 1%;">'.number_format($x['debit'],2).'</td>
						<td class="grid" width="8%" align=right style="padding-right: 1%;">'.number_format($x['credit'],2).'</td>
						<td align=center class="grid" width="10%">&nbsp;</td>
						<td align=center class="grid" width="8%">'.$dbt.'</td>
					</tr>'; $dbGT+=$x['debit']; $crGT+=$x['credit']; $i++; $ox = $x['xchecker'];
			}


		
			if($i < 5) { for($i; $i <= 4; $i++) {
				echo '<tr bgcolor='.initBackground($i).'>
							<td align=left class="grid" width="100%" colspan=11>&nbsp;</td>
					</tr>';
				}
			}
			
			echo '<tr>
					<td class="grid" align=right colspan="7" width=66%><b>TOTAL AMOUNT &raquo;&nbsp;&nbsp;&nbsp;</b></td>
					<td class="grid" width="8%" align=right style="font-weight: bold;padding-right: 1%;"><span id=amtGT>'.number_format($dbGT,2) . '</span></td>
					<td class="grid" width="8%" align=right style="font-weight: bold;padding-right: 1%;"><span id=amtGT>'.number_format($crGT,2) . '</span></td>
					<td class="grid" align=right width=10%>&nbsp;</td>
					<td class="grid" align=right width=8%>&nbsp;</td>
				</tr>
				
			</table>';
		}

?>