<?php
	ini_set("memory_limit", "5024M");
	set_time_limit(0);
	session_start();
	include("../includes/dbUSE.php");

	//echo var_dump($_SESSION);
	//		concat(`description`,' - Supreme') `description`, concat(`full_description`,' - Supreme') `full_description`																			
	
	 function genIcode($source,$group,$sgroup,$type,$color){
		list($bit1) = getArray("select `code` from cebuglass.options_igroup where `group` = '$group';");
		list($bit2) = getArray("SELECT `code` FROM cebuglass.options_isgroup WHERE `subgroup_id` = '$sgroup';");
		list($bit3) = getArray("select `code` from cebuglass.options_itype where `type` = '$type';");
		list($bit4) = getArray("select `code` from cebuglass.options_colors where `id` = '$color';");
		$bit5 = $source;
		
		if($bit1 == "") { $bit1 = "00"; }
		if($bit2 == "") { $bit2 = "00"; }
		if($bit3 == "") { $bit3 = "000"; }
		if($bit4 == "") { $bit4 = "0000"; }
		
		$icode = $bit1.$bit2.$bit3.$bit4.$bit5;
		list($itemCode) = getArray("SELECT LPAD(ifnull(MAX(series+1),1),4,0) FROM (SELECT TRIM(LEADING '0' FROM(SUBSTRING_INDEX(SUBSTRING_INDEX(item_code, '-', 2), '-', -1))) AS series FROM products_master WHERE SUBSTRING_INDEX(SUBSTRING_INDEX(item_code, '-', 1), '-', -1) = '$icode') a;");
		return $icode.'-'.$itemCode;
		
	}

	$data = dbquery("SELECT
					    `stock_code`
					    , `desc1`
					    , `desc2`
					    , `cost`
					    , `group`
					    , `subgroup`
					    , `itype`
					FROM
					    `cebuglass`.`missing`;");

	while($row = mysql_fetch_array($data)){
		
		$icode = genIcode('',$row[group],$row[subgroup],$row[itype],'');

		$str= "INSERT ignore INTO cebuglass.products_master (company,source,`group`,`sgroup`,`type`,`item_code`,`indcode`,`description`,`full_description`,cogs_acct,exp_acct,asset_acct,rev_acct,unit)
				VALUES ('1','$source','$row[group]','$row[subgroup]','','$icode','".mysql_real_escape_string($row[stock_code])."','".mysql_real_escape_string($row[desc1])."','".mysql_real_escape_string($row[desc2])."','600701','','120701','400701','');";
		
		dbquery($str);
		echo $str;
		echo "<br><br>";
		
	}

?>

<div style="font-size:9pt;" >
	<?php echo $str; ?>
</div>