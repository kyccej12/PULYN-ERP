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
	
	
	function ADJDETAILS($doc_no) {
		$i = 1;
		$details = dbquery("select line_id, doc_no, item_code, description, qty, cost, unit, amount from adj_details where doc_no='$doc_no' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
		list($status,$lock) = getArray("select status,locked from adj_header where doc_no='$doc_no' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
		while(list($line_id, $doc_no, $item_code, $description, $qty, $price, $unit, $amount) = mysql_fetch_array($details)) {
		   echo '<tr bgcolor="'.initBackground($i).'">
 					<td align=left class="grid" width="5%">&nbsp;</td>
					<td align=left class="grid" width="10%">'.$item_code.'</td>
					<td align=left class="grid" width="35%">'.strtoupper($description).'</td>
					<td align=center class="grid" width="10%">'.identUnit($unit).'</td>
					<td align=center class="grid" width="10%">'.number_format($price,2).'</td>
					<td align=center class="grid" width="10%">';
					if(($status == 'Active' || $status == '') && $lock != 'Y') {
						echo '<input type="text" id="qty['.$line_id.']" class="gridInput" style="width: 90%; text-align: center;" value="'.number_format($qty,2).'" onchange="updateQty(this.value,'.$doc_no.','.$line_id.',\''. $price . '\');">';
					} else { echo number_format($qty,2); }
			echo '</td>
				   <td align=right class="grid" width="10%"><span id="amt['.$line_id.']">'.number_format($amount,2).'</span></td>
				   <td align=center class="grid" width="10%">';
			if(($status == 'Active' || $status == '') && $lock != 'Y') {	
				echo '<a href="#" onclick="deleteDetails('.$line_id.','.$doc_no.')"><img src="images/icons/delete.png" height=14 width=14 align=absmiddle title="Click to remove entry"/></a>';
			} else { echo "&nbsp;"; }		
			echo '</td></tr>';	
			$i++; $t++; $amount_GT+=$amount;						
		}
		
		
		if($i < 8) { for($i; $i <= 7; $i++) {
			echo '<tr bgcolor='.initBackground($i).'>
						<td align=left class="grid" width="100%" colspan=8>&nbsp;</td>
				</tr>';
			}
		}
		
		echo '<tr>
				<td class="grid" align=right colspan="6"><b>TOTAL AMOUNT &raquo;&nbsp;&nbsp;&nbsp;</b></td>
				<td class="grid" width="10%" align=center style="font-weight: bold;"><span id="amtGT">' . number_format($amount_GT,2) . '</span></td>
				<td class="grid" align=right width=10%>&nbsp;</td>
			</tr>';
	}

?>