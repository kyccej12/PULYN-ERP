<?php

	function STRDETAILS($str_no,$status,$lock) {
		$i = 1;
		$details = dbquery("select line_id, str_no, item_code, description, qty, unit from str_details where str_no='$str_no' and branch = '$_SESSION[branchid]';");
		echo '<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">';
		while(list($line_id, $str_no, $item_code, $description, $qty, $unit) = mysql_fetch_array($details)) {
		  echo '<tr bgcolor="'.initBackground($i).'" onmouseover="highlightTableRowVersionA(this, \'#3399ff\');" id="obj_'.$lined_id.'" onclick="selectLine(this,\''.$line_id.'\');">
					<td align=left class="grid" width="15%">'.$item_code.'</td>
					<td align=left class="grid" width="41%">'.strtoupper($description).'</td>
					<td align=center class="grid" width="15%">'.identStockCode($item_code).'</td>
					<td align=center class="grid" width="16%">'.identUnit($unit).'</td>
					<td align=center class="grid">';
					if(($status == 'Active' || $status == '') && $lock != 'Y') {
						echo '<input type="text" id="qty['.$line_id.']" class="gridInput" style="width: 70%; text-align: center;" value="'.number_format($qty,2).'" onchange="updateQty(this.value,\''.$str_no.'\',\''.$line_id.'\',\''.$qty.'\');"></td>';
					} else { echo number_format($qty,2); }
					echo '</td>
				 </tr>';	
			$i++;					
		}
		
		if($i < 8) { for($i; $i <= 7; $i++) {
			echo '<tr bgcolor='.initBackground($i).'>
						<td align=left class="grid" width="100%" colspan=5>&nbsp;</td>
				</tr>';
			}
		}
		echo '</table>';
	}

?>