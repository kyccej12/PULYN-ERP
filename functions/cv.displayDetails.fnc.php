<?php
	function initBackground($i) {
		if($i%2==0){ $bgC = "#ededed"; } else { $bgC = "#ffffff"; }
		return $bgC;
	}
	
	function constructCostCenter() {
		
		global $con;
		
		$uLoop = $con->query("SELECT dept_abbrv,dept_name FROM kredoithris.options_dept ORDER BY dept_name");
		$option = "<select id='cost_center' name='cost_center' class='gridInput' style='width: 95%'><option value=''>- NA -</option>";
		while(list($myUnit,$myDesc) = $uLoop->fetch_array(MYSQLI_BOTH)) {
			$option = $option ."<option value='$myUnit'>".strtoupper($myUnit)."</option>";
		}
		$option = $option . "</select>";
		return $option;
	}
	
	function CVDETAILS($cv_no,$status,$lock) {
		
		global $con;
		
		$i = 1;
		$details = $con->query("select record_id as line_id, ref_no, if(ref_type!='SI',concat(ref_type,'-',ref_no),ref_no) as xref, date_format(ref_date,'%m/%d/%Y') as rd8, if(ref_type = 'SI','RR/Invoice',ref_type) as ref_type, ref_type as xref_type, concat(ref_no,ref_type) as xchecker, if(acct_branch!='1',concat(acct,'-',lpad(acct_branch,2,0)),acct) as acct, acct_desc, debit, credit, if(cost_center='','...',cost_center) as cost_center from cv_details where cv_no = '$cv_no' and branch = '1' order by ref_no, ref_type, debit desc, acct_desc;");
		echo '<table width=100% cellspacing=0 cellpadding=0>';
		while($x = $details->fetch_array(MYSQLI_BOTH)) {
			if($status == "Active") { 
				if($x['xref_type'] == 'SI') { $dbt = '<a href="#" onclick="javascript: deleteInvoice(\''.$x['ref_no'].'\',\''.$x['xref_type'].'\');" title="Delete Invoice From Check Voucher"><img src="images/icons/delete.png" width=16 height=16 style="vertical-align: middle;" /></a>'; } else { $dbt = '<a href="#" onclick="javascript: deleteLine(\''.$x['line_id'].'\',\''.$cv_no.'\');" title="Delete Line Entry"><img src="images/icons/delete.png" width=16 height=16 style="vertical-align: middle;" /></a>'; }
				if($x['xref_type'] == "AP" || $x['xref_type'] == "AP-BB") {	$mydb = "<a href=\"#\" style=\"text-decoration: none; color: black;\" onclick=\"javascript: changeAPAmount($x[line_id],'$x[debit]');\" title=\"Click figure to change the amount.\">".number_format($x['debit'],2)."</a>"; } else { $mydb = number_format($x['debit'],2); }
				$center = "<a href=\"#\" style=\"text-decoration: none; color: black;\" onclick=\"javascript: changeCostCenter($x[line_id],'$x[cost_center]');\">$x[cost_center]</a>";
			} else { $dbt = '&nbsp;'; $center = $x['cost_center']; $mydb = number_format($x['debit'],2); }
			if($ox != $x['xchecker']) { $ref_no = $x['xref']; $ref_date = $x['rd8']; } else { $ref_no = ''; $ref_date = ''; }
			echo '<tr bgcolor="'.initBackground($i).'">
					<td align=center class="grid" width="8%"><b>'.$ref_no.'</b></td>
					<td align=center class="grid" width="8%"><b>'.$ref_date.'</b></td>
					<td align=center class="grid" width="8%" style="padding-left: 10px;">'.$x['ref_type'] .'</td>
					<td align=left class="grid" width="10%" style="padding-left: 10px;">'.$x['acct'].'</td>
					<td align=left class="grid" width="31%">'.$x['acct_desc'].'</td>
					<td align=center class="grid" width="10%" id="c_'.$x['line_id'].'">'.$center.'</td>
					<td class="grid" width="8%" align=right style="padding-right: 20px;" id="xc_'.$x['line_id'].'">'.$mydb.'</td>
					<td class="grid" align=right width="8%" style="padding-right: 10px;">'.number_format($x['credit'],2).'</td>
					<td class="grid" align=right style="padding-right: 5px;">'.$dbt.'</td>
				</tr>'; $dbGT+=$x['debit']; $crGT+=$x['credit']; $i++; $ox = $x['xchecker'];
		}
		echo '<tr>
				<td class="grid" align=right colspan="6"><b>TOTAL AMOUNT &raquo;&nbsp;&nbsp;&nbsp;</b></td>
				<td class="grid" width="8%" align=right style="font-weight: bold;padding-right: 20px;"><span id=amtGT>'.number_format($dbGT,2) . '</span></td>
				<td class="grid" width="8%" align=right style="font-weight: bold;padding-right: 10px;"><span id=amtGT>'.number_format($crGT,2) . '</span></td>
				<td class="grid">&nbsp;</td>
			</tr>';

		
		if($i < 10) { for($i; $i <= 10; $i++) {
			echo '<tr bgcolor='.initBackground($i).'>
						<td align=left class="grid" width="100%" colspan=10>&nbsp;</td>
				</tr>';
			}
		}
		echo '</table>';
		
	}

?>