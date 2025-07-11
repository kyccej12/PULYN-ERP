<?php
	function initBackground($i) {
		if($i%2==0){ $bgC = "#ededed"; } else { $bgC = "#ffffff"; }
		return $bgC;
	}
	function SODETAILS($trace_no,$status,$lock,$urights) {
		$i = 1;
		
		$details = dbquery("SELECT a.line_id, a.so_no, a.item_code, a.description, a.qty, a.cost, a.unit, a.amount, b.label, a.comm,a.discount, discount_percent, round(qty*discount,2) as tdisc,round(qty*comm,2) as tcomm, disc_lock, if(a.custom_description!='',concat('<br/><b>Other Description:</b> ',custom_description),'') as custDesc FROM so_details a INNER JOIN cebuglass.price_level b ON a.plevel = b.line_id WHERE trace_no = '$trace_no';");
		echo '<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">';
		while(list($line_id, $so_no, $item_code, $description, $qty, $price, $unit, $amount,$plevel,$comm,$disc,$percent,$tdisc,$tcomm, $lock, $custDesc) = mysql_fetch_array($details)) {
		 
			if($lock=='Y'){ $lock_img = 'images/icons/locked.png';	} else { $lock_img = 'images/icons/unlocked.png';} 
			
			if($_SESSION['utype'] == 'admin' && $status == "Active"){ 
				if($lock == "Y") {
					$lockBtn = "&nbsp;<a href=\"#\" onclick=\"unlockMe($line_id);\" title=\"Click to allow item for discounting\"><span id=\"imgLockBtn_$line_id\"><img src=\"images/icons/locked.png\" height=14 width=14 align=absmiddle /></a></span>";
				} else {
					$lockBtn = "&nbsp;<a href=\"#\" onclick=\"lockMe($line_id);\" title=\"Click to lock item for discounting\"><span id=\"imgLockBtn_$line_id\"><img src=\"images/icons/unlocked.png\" height=14 width=14 align=absmiddle /></a></span>";
				}
			} else { $lockBtn = ""; }
				
			echo '<tr bgcolor="'.initBackground($i).'" onmouseover="highlightTableRowVersionA(this, \'#3399ff\');" id="obj_'.$lined_id.'" onclick="selectSLid(this,\''.$line_id.'\');">
					<td align=left class="grid" width="10%" valign=top>'.$item_code.'</td>
					<td align=left class="grid"  width="36%" valign=top>'.$description.$custDesc.'</td>
					<td align=center class="grid" width="5%" valign=top>'.identUnit($unit).'</td>
					<td align=center class="grid" width="8%" valign=top>';
						if($status == 'Active' || $status == '') {
							echo '<input type="text" id="qty['.$line_id.']" style="width: 60%; text-align: center; border: none; background-color: '.initBackground($i).';" value="'.number_format($qty,2).'" onchange="updateQty(this.value,\''.$line_id.'\',\''. $price . '\');">';
						} else { echo number_format($qty,2); }
					echo '</td>
			  		<td align=center class="grid" width="8%" valign=top>'.$plevel.'</td>
					<td align=center class="grid" width="8%" valign=top>';
						if(($status == 'Active' || $status == '') && $urights == 'admin') {
							echo '<input type="text" id="price['.$line_id.']" style="width: 60%; text-align: center; border: none; background-color:'.initBackground($i).'" value="'.number_format($price,2).'" onchange="updatePrice(this.value,\''.$line_id.'\',\''. $price . '\',\''.$qty.'\');">';
						} else { echo number_format($price,2); }
					echo '</td>
					<td align=center class="grid" width="8%" valign=top>'.number_format($comm,2).'</td>
					<td align=center class="grid" width="8%" valign=top><span id="disc['.$line_id.']">'.number_format($disc,2).'</span></td>
				    <td align=right class="grid"  width="10%" style="padding-right: 20px;" valign=top><span id="amt['.$line_id.']">'.number_format(($amount),2).'</span>'.$lockBtn.'</td>';	
			   $i++;		
		}
		
		
		if($i < 8) { for($i; $i <= 7; $i++) {
			echo '<tr bgcolor='.initBackground($i).'>
						<td align=left class="grid" width="100%" colspan=10>&nbsp;</td>
				</tr>';
			}
		}
		echo "</table>";
	}

?>