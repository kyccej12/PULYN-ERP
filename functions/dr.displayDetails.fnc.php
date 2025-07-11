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
	
	
	function DRDETAILS($dr_no) {
			$i = 0; $t = 0;
			$details = dbquery("select line_id, dr_no, item_code, description, qty, cost, unit, amount from dr_details where dr_no='$dr_no';");
			list($status,$lock) = getArray("select status,locked from dr_header where dr_no='$dr_no';");

		if(($status == 'Active' || $status == '') && $lock != 'Y') {
			echo '<tr bgcolor="'.initBackground($i).'">
					<td align=center class="grid" width="5%" align=center><img src="images/icons/green_arrow.png" style="vertical-align: middle;" /></td>
					<td align=center class="grid" idth="45%" colspan=2><input type="hidden" id="product_code" /><input type=text class="inputSearch" style="padding-left: 22px;" id="description" style="width: 95%;" onkeyup="itemLookup(this.value,this.id);" /></td>
					<td align=center class="grid" width="10%">'.constructUnit().'</td>
					<td align=center class="grid" width="10%"><input class="gridInput" type=text id="unit_price"style="width: 90%; text-align: right;" onchange="computeAmount();"/></td>
					<td align=center class="grid" width="10%"><input class="gridInput" type=text id="qty" style="width: 90%; text-align: right;" onblur="computeAmount();" /></td>
					<td align=center class="grid" width="10%"><input class="gridInput" type=text id="amount" style="width: 90%;text-align: right;" readonly/></td>
					<td align=center class="grid" width="10%"><button type="button" onclick="javascript: addDetails();" title="Add Item"><img src="images/icons/add.png" width=18 height=18 style="vertical-align: middle;" />&nbsp;Add</button></td>
				</tr>';
			$i++;
		}
		

		while(list($line_id, $dr_no, $item_code, $description, $qty, $price, $unit, $amount) = mysql_fetch_array($details)) {
		   echo '<tr bgcolor="'.initBackground($i).'">
 					<td align=left class="grid" width="5%">&nbsp;</td>
					<td align=left class="grid" width="10%">'.$item_code.'</td>
					<td align=left class="grid" width="35%">'.strtoupper($description).'</td>
					<td align=center class="grid" width="10%">'.identUnit($unit).'</td>
					<td align=center class="grid" width="10%">'.number_format($price,2).'</td>
					<td align=center class="grid" width="10%">';
					if(($status == 'Active' || $status == '') && $lock != 'Y') {
						echo '<table cellpadding=0 cellspacing=0>
								<tr><td><input type="text" id="qty['.$line_id.']" class="gridInput" style="width: 50px; text-align: center;" value="'.number_format($qty,2).'" onchange="updateQty(this.value,'.$dr_no.','.$line_id.',\''. $price . '\');"></td>
							  		<td align=center style="padding-left: 2px; padding-bottom: 5px;">
							  			<div style="position: relative; left: 0; top: 0;">
										  <a href="#" onclick="usabQty('.$dr_no.','.$line_id.',\''. $price . '\',\'up\')"><img src="images/icons/arrow-up.png" style="position: relative; top: 0; left: 0; border: 0;"/></a>
										  <a href="#" onclick="usabQty('.$dr_no.','.$line_id.',\''. $price . '\',\'down\')"><img src="images/icons/arrow-down.png" style="position: absolute; top: 10px; left: 0; border: 0;"/></a>
							  			</div>
							  		</td>
							  	</tr>
							  </table>';
					} else { echo number_format($qty,2); }
			echo '</td>
					<td align=center class="grid" width="10%"><span id="amt['.$line_id.']">'.number_format($amount,2).'</span></td>
					<td align=center class="grid" width="10%">';
				if(($status == 'Active' || $status == '') && $lock != 'Y') {		
					echo '<a href="#" onclick="deleteDetails('.$line_id.','.$dr_no.')"><img src="images/delete.gif" height=14 width=14 align=absmiddle title="Click to remove entry"/></a>';
				} else { echo "&nbsp;"; }		
				echo '</td></tr>';	
			$i++; $t++; $amount_GT+=$amount;						
		}
		
		
		if($i < 5) { for($i; $i <= 4; $i++) {
			echo '<tr bgcolor='.initBackground($i).'>
						<td align=left class="grid" width="100%" colspan=8>&nbsp;</td>
				</tr>';
			}
		}
		
		echo '<tr>
				<td class="grid" align=right colspan="6"><b>TOTAL AMOUNT &raquo;&nbsp;&nbsp;&nbsp;</b></td>
				<td class="grid" width="10%" align=center style="font-weight: bold;"><span id=amtGT>'.number_format($amount_GT,2) . '</span></td>
				<td class="grid" align=right width=10%>&nbsp;</td>
			</tr>';
	}

?>