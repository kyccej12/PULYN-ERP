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
	
	
	function IDETAILS($trace_no,$status,$lock) {
		$i = 0;
		$details = dbquery("select line_id, if(so_no='','',so_no) as so_no, if(so_date='0000-00-00','',date_format(so_date,'%m/%d/%Y')) as so_date, item_code, description, qty, cost, Round(qty * discount,2) as discount, unit, (amount+comm) as amount, ROUND(qty*cost,2) as linegross, (cost-discount) as netprice, discount_percent, sales_group, ROUND(qty*comm) as commission, if(custom_description!='',concat('<br/><b>Other Description:</b> ',custom_description),'') as custDesc from invoice_details where  trace_no = '$trace_no';");
		echo '<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">';
		while(list($line_id, $so_no, $so_date, $item_code, $description, $qty, $price, $discount, $unit, $amount, $linegross, $netprice, $pct, $sgroup,$comm,$custDesc) = mysql_fetch_array($details)) {
		
		 echo '<tr bgcolor="'.initBackground($i).'" onmouseover="highlightTableRowVersionA(this, \'#3399ff\');" id="obj_'.$lined_id.'" onclick="selectSLid(this,\''.$line_id.'\');">
 					<td align=center class="grid" width="6%" valign=top>'.str_pad($so_no,6,0,STR_PAD_LEFT).'</td>
 					<td align=center class="grid" width="9%" valign=top>'.$so_date.'</td>
					<td align=left class="grid" width="10%" valign=top>'.$item_code.'</td>
					<td align=left class="grid" width="22%" valign=top>'.strtoupper($description).$custDesc.'</td>
					<td align=center class="grid" width="4%" valign=top>'.$unit.'</td>
					<td align=center class="grid" width="8%" valign=top>';
						if(($status == 'Active' || $status == '') && $lock != 'Y') {
							echo '<input type="text" id="qty['.$line_id.']" style="width: 60%; text-align: center; border: none; background-color:'.initBackground($i).'" value="'.number_format($qty,2).'" onchange="updateQty(this.value,\''.$line_id.'\',\''. $price . '\',\''.$qty.'\');">';
						} else { echo number_format($qty,2); }
					echo '
					</td>
					<td align=center class="grid" width="8%" valign=top>';
					if(($status == 'Active' || $status == '') && $lock != 'Y') {
						echo '<input type="text" id="price['.$line_id.']" style="width: 60%; text-align: center; border: none; background-color:'.initBackground($i).'" value="'.number_format($price,2).'" onchange="updatePrice(this.value,\''.$line_id.'\',\''. $price . '\',\''.$qty.'\');">';
					} else { echo number_format($price,2); }
					echo '</td>
					<td align=right class="grid" width="8%" style="padding-right: 3%;" valign=top>'.number_format($pct).'%</td>
					<td align=right class="grid" width="8%" style="padding-right: 3%;" valign=top><span id="netprice['.$line_id.']">'.number_format($netprice,2).'</span></td>
					<td align=center class="grid" width="8%" valign=top>'. number_format($comm,2) . '</td>
				    <td align=right class="grid" style="padding-right: 20px;" valign=top><span id="amt['.$line_id.']">'.number_format($amount,2).'</span></td>
				</tr>';	
			$i++;						
		}
		
		
		if($i < 7) { for($i; $i <= 6; $i++) {
			echo '<tr bgcolor='.initBackground($i).'><td align=left class="grid" width="100%" colspan=11>&nbsp;</td></tr>';
			}
		}
	
		echo "</table>";
	}

?>