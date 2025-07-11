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
    `Item ID` as indcode
    , `Item Description` AS desc1
    , `Description For Sales` desc2
    , `Item Description1` desc3
    , `Description for Sales1` desc4
    , `ITEM TYPE MAIN` igroup
    , `Item Type` itype
    , `ITEM GROUP` subgroup
    , `Stocking U/M` um
FROM
    `cebuglass`.`sheet1$`;");

	while($row = mysql_fetch_array($data)){

		switch ($row['um']) {
			case "LENGTH(s)" :
				$um = "LGT";
			break;
			case "PAIR(s)" :
				$um = "PR";
			break;
			case "PIECE (s)" :
				$um = "PC";
			break;
			case "PIECE(s)" :
				$um = "PC";
			break;
			case "PIECES(s)" :
				$um = "PC";
			break;
			case "ROLL(s)" :
				$um = "RL";
			break;
			case "SET" :
				$um = "SET";
			break;
			case "SET (s)" :
				$um = "SET";
			break;
			case "SET(s)" :
				$um = "SET";
			break;
			case "SETS (s)" :
				$um = "SET";
			break;
			case "UNIT(s)" :
				$um = "UNT";
			break;

		}
		switch ($row['subgroup']) {
			case 'SPECIALTY' :
				$itype='56';
			break;

			case 'SWING' :
				$itype='30';
			break;

			case 'SEALANT/ADHESIVES' :
				$itype='27';
			break;

			case 'FURNITURE' :
				$itype='54';
			break;

			case 'SLIDING' :
				$itype='33';
			break;

			case 'TOOLS' :
				$itype='28';
			break;

			case 'MISCELLANEOUS' :
				$itype='57';
			break;

			case 'SCREEN/FIXED' :
				$itype='53';
			break;

			case 'CASEMENT/AWNING' :
				$itype='52';
			break;

			case 'SHOWCASE' :
				$itype='58';
			break;

		}
		$icode = genIcode($row[source],'HW','15',$itype,'');
		$str.= "INSERT ignore INTO cebuglass.products_master (company,source,`group`,`sgroup`,`type`,`item_code`,`indcode`,`description`,`full_description`,cogs_acct,exp_acct,asset_acct,rev_acct,unit)
				VALUES ('1','L','HW','15','$itype','$icode','".mysql_real_escape_string($row[indcode])."','".mysql_real_escape_string($row[desc3])."','".mysql_real_escape_string($row[desc4])."','600701','','120701','400701','$um');"."<br><br>";
		dbquery("INSERT ignore INTO cebuglass.products_master (company,source,`group`,`sgroup`,`type`,`item_code`,`indcode`,`description`,`full_description`,cogs_acct,exp_acct,asset_acct,rev_acct,unit)
				VALUES ('1','L','HW','15','$itype','$icode','".mysql_real_escape_string($row[indcode])."','".mysql_real_escape_string($row[desc3])."','".mysql_real_escape_string($row[desc4])."','600701','','120701','400701','$um');");
	}

?>

<div style="font-size:7pt;" >
	<?php echo $str; ?>
</div>