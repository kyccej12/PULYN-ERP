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

	function getFullDesc($icode) {
		list($desc,$fdesc) = getArray("select description, full_description from products_master where item_code = '$icode';");
		if($fdesc != '') { return $fdesc; } else { return $desc; }
	}
	
	
	function SRRDETAILS($doc_no,$status) {
		$i = 1;
		$details = dbquery("select line_id, doc_no, item_code, description, qty, unit from phy_details where doc_no='$doc_no' and branch = '$_SESSION[branchid]';");
		echo '<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">';
		while(list($line_id, $doc_no, $item_code, $description, $qty, $unit) = mysql_fetch_array($details)) {
		    echo '<tr bgcolor="'.initBackground($i).'" onmouseover="highlightTableRowVersionA(this, \'#3399ff\');" id="obj_'.$lined_id.'" onclick="selectSLid(this,\''.$line_id.'\');">
					<td align=left class="grid" width="15%">'.$item_code.'</td>
					<td align=left class="grid" width="41%">'.strtoupper(getFullDesc($item_code)).'</td>
					<td align=center class="grid" width="15%">'.identStockCode($item_code).'</td>
					<td align=center class="grid" width="16%">'.identUnit($unit).'</td>
					<td align=center class="grid">';
					if(($status == 'Active' || $status == '') && $lock != 'Y') {
						echo '<input type="text" id="qty['.$line_id.']" class="gridInput" style="width: 70%; text-align: center;" value="'.number_format($qty,2).'" onchange="updateQty(this.value,\''.$doc_no.'\',\''.$line_id.'\',\''.$qty.'\');"></td>';
					} else { echo number_format($qty,2); }
					echo '</td>
				 </tr>';	
			$i++;				
		}
		
		
		if($i < 8) { for($i; $i <= 7; $i++) {
			echo '<tr bgcolor='.initBackground($i).'>
						<td align=left class="grid" width="100%" colspan=8>&nbsp;</td>
				</tr>';
			}
		}
		echo '</table>';
	}
?>