<?php
	function initBackground($i) {
		if($i%2==0){ $bgC = "#ededed"; } else { $bgC = "#ffffff"; }
		return $bgC;
	}
	
	
	function constructUnit() {
		$uLoop = dbquery("select unit,description from options_units order by description");
		$option = "<select id='unit' class='gridInput' style='width: 95%'><option value=''>- Unit -</option>";
		while(list($myUnit,$myDesc) = mysql_fetch_array($uLoop)) {
			$option = $option ."<option value='$myUnit'>".strtoupper($myDesc)."</option>";
		}
		$option = $option . "</select>";
		return $option;
	}
	
	
	function RRDETAILS($rr_no,$status) {
		$i = 0; $t = 0;
		$details = dbquery("select line_id, rr_no, lpad(po_no,6,0) as po_no, date_format(po_date,'%m/%d/%Y') as po_date, item_code, description, qty, cost, unit, amount from rr_details where rr_no='$rr_no' and branch = '$_SESSION[branchid]';");
		echo '<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">';
			while(list($line_id, $rr_no, $po_no, $po_date, $item_code, $description, $qty, $price, $unit, $amount) = mysql_fetch_array($details)) {
			     if($po_no!='') { list($poQty,$poTdld) = getArray("select qty,qty_dld as tqty from po_details where item_code = '$item_code' and  po_no = '$po_no' and branch = '$_SESSION[branchid]';"); } else { $poQTY = 0; $poQTY = 0; }
				 echo '<tr bgcolor="'.initBackground($i).'" onmouseover="highlightTableRowVersionA(this, \'#3399ff\');" id="obj_'.$lined_id.'" onclick="selectLine(this,\''.$line_id.'\');">
						<td align=center class="grid" width="8%">'.$po_no.'</td>
						<td align=center class="grid" width="10%">'.$po_date.'</td>
						<td align=left class="grid" width="10%">'.$item_code.'</td>
						<td align=left class="grid" width="31%">'.strtoupper($description).'</td>
						<td align=center class="grid" width="10%">'.identUnit($unit).'</td>
						<td align=center class="grid" width="10%">';
					if(($status == 'Active' || $status == '') && $lock != 'Y') {
							echo '<input type="text" id="qty['.$line_id.']" style="border: none; width: 90%; text-align: center; background-color: '.initBackground($i).'" value="'.number_format($qty,2).'" onchange="updateQty(this.value,\''.$rr_no.'\',\''.$line_id.'\',\''. $price . '\',\''.$qty.'\',\''.$po_no.'\',\''.$poQty.'\',\''.$poTdld.'\');">';
						} else { echo number_format($qty,2); }
					echo '</td>
						<td align=center class="grid" width="10%">'.number_format($price,2).'</td>
						<td align=right class="grid" style="padding-right: 20px;"><span id="amt['.$line_id.']">'.number_format($amount,2).'</span></td>
					</tr>';	
				$i++;						
			}
				
		
			if($i < 8) { for($i; $i <= 7; $i++) {
				echo '<tr bgcolor='.initBackground($i).'>
							<td align=left class="grid" width="100%" colspan=9>&nbsp;</td>
					</tr>';
				}
			}
		echo '</table>';
	}

?>