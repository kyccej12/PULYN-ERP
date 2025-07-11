<?php
	function initBackground($i) {
		if($i%2==0){ $bgC = "#ededed"; } else { $bgC = "#ffffff"; }
		return $bgC;
	}
	
	function PODETAILS($po_no,$status,$lock) {
		$i = 1; $t = 0;
		$details = dbquery("select line_id, po_no, item_code, description, qty, cost, ROUND(qty * discount,2), unit, amount, qty_dld, ROUND(qty * cost,2) as linegross, if(custom_description!='',concat('<br/><b>Other Description:</b> ',custom_description),'') as custDesc from po_details where po_no='$po_no' and branch = '1';");
		echo '<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">';
		while(list($line_id, $po_no, $item_code, $description, $qty, $price, $discount, $unit, $amount, $dld, $linegross, $custDesc) = mysql_fetch_array($details)) {
		  
		  if($discount > 0 && $pct == 0) {
			$myDiscount =  number_format($discount,2);
		  } else { if($pct > 0) { $myDiscount = "$pct%"; } else { $myDiscount = ''; }}
		  echo '<tr bgcolor="'.initBackground($i).'" onmouseover="highlightTableRowVersionA(this, \'#3399ff\');" id="obj_'.$lined_id.'" onclick="selectLine(this,\''.$line_id.'\');">
					<td align=left class="grid" width="15%">'.$item_code.'</td>
					<td align=left class="grid">'.strtoupper($description).$custDesc.'</td>
					<td align=center class="grid" width="10%">'.identUnit($unit).'</td>
					<td align=center class="grid" width="10%">';
				if(($status == 'Active' || $status == '') && $lock != 'Y') {
						echo '<input type="text" id="qty['.$line_id.']" style="border: none; width: 90%; text-align: center; background-color: '.initBackground($i).'" value="'.number_format($qty,2).'" onchange="updateQty(this.value,\''.$po_no.'\',\''.$line_id.'\',\''. $price . '\');">';
				} else { echo number_format($qty,2); }
				echo '</td>
					<td align=center class="grid" width="10%">';
				if(($status == 'Active' || $status == '') && $lock != 'Y') {
					echo '<input type="text" id="price['.$line_id.']" style="border: none; width: 90%; text-align: center; background-color: '.initBackground($i).'" value="'.number_format($price,2).'" onchange="updatePrice(this.value,\''.$po_no.'\',\''.$line_id.'\');">';
				} else { echo number_format($price,2); }
					
				echo '</td>
					<td align=right class="grid" style="padding-right: 20px;" width="10%"><span id="amt['.$line_id.']">'.number_format($amount,2).'</span></td>
				</tr>';	
			$i++;						
		}
		if($i < 8) { for($i; $i <= 7; $i++) { echo '<tr bgcolor='.initBackground($i).'><td align=left class="grid" width="100%" colspan=8>&nbsp;</td></tr>'; }}
		echo '</table>';
	}
?>