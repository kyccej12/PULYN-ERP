<?php
	ini_set("memory_limit", -1);
	set_time_limit(-1);
	session_start();
	include("../includes/dbUSE.php");

	//echo var_dump($_SESSION);
	//		concat(`description`,' - Supreme') `description`, concat(`full_description`,' - Supreme') `full_description`																			
	
	 function genIcode($source,$group,$sgroup,$type,$color){
		list($bit1) = getArray("select `code` from options_igroup where `group` = '$group';");
		list($bit2) = getArray("SELECT `code` FROM options_isgroup WHERE `subgroup_id` = '$sgroup';");
		list($bit3) = getArray("select `code` from options_itype where `type` = '$type';");
		list($bit4) = getArray("select `code` from options_colors where `id` = '$color';");
		$bit5 = $source;
		
		if($bit1 == "") { $bit1 = "00"; }
		if($bit2 == "") { $bit2 = "00"; }
		if($bit3 == "") { $bit3 = "000"; }
		if($bit4 == "") { $bit4 = "0000"; }
		
		$icode = $bit1.$bit2.$bit3.$bit4.$bit5;
		list($itemCode) = getArray("SELECT LPAD(ifnull(MAX(series+1),1),4,0) FROM (SELECT TRIM(LEADING '0' FROM(SUBSTRING_INDEX(SUBSTRING_INDEX(item_code, '-', 2), '-', -1))) AS series FROM products_master WHERE SUBSTRING_INDEX(SUBSTRING_INDEX(item_code, '-', 1), '-', -1) = '$icode') a;");
		return $icode.'-'.$itemCode;
		
	}

	$data = dbquery("SELECT `company`, `category`, `source`, `group`, `sgroup`, `type`, `item_code`, `barcode`, `indcode`, `brand`, CONCAT(`description`,' - Supreme') `description`, CONCAT(`full_description`,' - Supreme') `full_description`, `size`, `color`, `cogs_acct`, `exp_acct`, `asset_acct`, `rev_acct`, `unit`, `unit_cost`, `walkin_price`, `unit_price1`, `unit_price2`, `unit_price3`, `unit_price4`, `unit_price5`, `unit_price6`, `unit_price7`, `unit_price8`, `srp`, `beg_qty`, `minimum_level`, `reorder_pt`, `vat_exempt`, `active`, `commission`, `supplier`, `encoded_by`, `encoded_on`, `updated_by`, `updated_on`, `sort`, `file_status`, `price_a`, `price_aaa`, `price_b`, `price_bbb`, `price_c`, `price_ccc`, `price_proj`, `price_ox` FROM cebuglass.products_master ab WHERE item_code NOT IN (
SELECT  `item_code` FROM cebuglass.products_master c WHERE c.description LIKE '%retazo%'  UNION ALL 
SELECT  `item_code` FROM cebuglass.products_master a WHERE a.sgroup IN (4,6,5,7)  UNION ALL 
SELECT  `item_code` FROM cebuglass.products_master b WHERE b.sgroup IN (8) AND b.indcode IN ('JT-21(HA)','JT-21((MF))','JT-21(WH)','STD-13006','RA-750-S','RA-750-D','STD-13001','STD-13002','STD-13003'));");
	$ctr=0;
	while($row = mysql_fetch_array($data)){
		$ctr++;
		$icode = genIcode($row[source],$row[group],$row[sgroup],$row[type],$row[color]);
		//echo $icode."<br>";
		dbquery("insert ignore into `products_master` ( `company`, `category`, `source`, `group`, `sgroup`, `type`, `item_code`, `barcode`, `indcode`, `brand`, `description`, `full_description`, `size`, `color`, `cogs_acct`, `exp_acct`, `asset_acct`, `rev_acct`, `unit`, `unit_cost`, `walkin_price`, `unit_price1`, `unit_price2`, `unit_price3`, `unit_price4`, `unit_price5`, `unit_price6`, `unit_price7`, `unit_price8`, `srp`, `beg_qty`, `minimum_level`, `reorder_pt`, `vat_exempt`, `active`, `commission`, `supplier`, `encoded_by`, `encoded_on`, `updated_by`, `updated_on`, `sort`, `file_status`, `price_a`, `price_aaa`, `price_b`, `price_bbb`, `price_c`, `price_ccc`, `price_proj`, `price_ox`) 
				values 
			('$row[company]', '$row[category]', '$row[source]', '$row[group]', '$row[sgroup]', '$row[type]', '$icode', '$row[barcode]', '$row[indcode]', '$row[brand]', '".mysql_real_escape_string($row[description])."', '".mysql_real_escape_string($row[full_description])."', '$row[size]', '$row[color]', '$row[cogs_acct]', '$row[exp_acct]', '$row[asset_acct]', '$row[rev_acct]', '$row[unit]', '$row[unit_cost]', '$row[walkin_price]', '$row[unit_price1]', '$row[unit_price2]', '$row[unit_price3]', '$row[unit_price4]', '$row[unit_price5]', '$row[unit_price6]', '$row[unit_price7]', '$row[unit_price8]', '$row[srp]', '$row[beg_qty]', '$row[minimum_level]', '$row[reorder_pt]', '$row[vat_exempt]', '$row[active]', '$row[commission]', '$row[supplier]', '$row[encoded_by]', '$row[encoded_on]', '$row[updated_by]', '$row[updated_on]', '$row[sort]', '$row[file_status]', '$row[price_a]', '$row[price_aaa]', '$row[price_b]', '$row[price_bbb]', '$row[price_c]', '$row[price_ccc]', '$row[price_proj]', '$row[price_ox]');");
		$str.= "insert into `products_master` ( `company`, `category`, `source`, `group`, `sgroup`, `type`, `item_code`, `barcode`, `indcode`, `brand`, `description`, `full_description`, `size`, `color`, `cogs_acct`, `exp_acct`, `asset_acct`, `rev_acct`, `unit`, `unit_cost`, `walkin_price`, `unit_price1`, `unit_price2`, `unit_price3`, `unit_price4`, `unit_price5`, `unit_price6`, `unit_price7`, `unit_price8`, `srp`, `beg_qty`, `minimum_level`, `reorder_pt`, `vat_exempt`, `active`, `commission`, `supplier`, `encoded_by`, `encoded_on`, `updated_by`, `updated_on`, `sort`, `file_status`, `price_a`, `price_aaa`, `price_b`, `price_bbb`, `price_c`, `price_ccc`, `price_proj`, `price_ox`) 
				values 
			('$row[company]', '$row[category]', '$row[source]', '$row[group]', '$row[sgroup]', '$row[type]', '$icode', '$row[barcode]', '$row[indcode]', '$row[brand]', '$row[description]', '$row[full_description]', '$row[size]', '$row[color]', '$row[cogs_acct]', '$row[exp_acct]', '$row[asset_acct]', '$row[rev_acct]', '$row[unit]', '$row[unit_cost]', '$row[walkin_price]', '$row[unit_price1]', '$row[unit_price2]', '$row[unit_price3]', '$row[unit_price4]', '$row[unit_price5]', '$row[unit_price6]', '$row[unit_price7]', '$row[unit_price8]', '$row[srp]', '$row[beg_qty]', '$row[minimum_level]', '$row[reorder_pt]', '$row[vat_exempt]', '$row[active]', '$row[commission]', '$row[supplier]', '$row[encoded_by]', '$row[encoded_on]', '$row[updated_by]', '$row[updated_on]', '$row[sort]', '$row[file_status]', '$row[price_a]', '$row[price_aaa]', '$row[price_b]', '$row[price_bbb]', '$row[price_c]', '$row[price_ccc]', '$row[price_proj]', '$row[price_ox]');"."<br><br>";
	
	}

?>

<div style="font-size:7pt;" >
	<?php echo $str; 
		echo "CTR->".$ctr;
	?>
</div>