<?php
	function initBackground($i) {
		if($i%2==0){ $bgC = "#ededed"; } else { $bgC = "#ffffff"; }
		return $bgC;
	}
	
	function constructCostCenter() {
		$uLoop = dbquery("select dept_code,dept_name from options_dept order by dept_name");
		$option = "<select id='cost_center' name='cost_center' class='gridInput' style='width: 95%'><option value=''>- Cost Center -</option>";
		while(list($myUnit,$myDesc) = mysql_fetch_array($uLoop)) {
			$option = $option ."<option value='$myUnit'>".strtoupper($myUnit)."</option>";
		}
		$option = $option . "</select>";
		return $option;
	}
	
	

?>