<?php
	include("includes/dbUSE.php");
	session_start();
	if(isset($_REQUEST['queryString']) && $_REQUEST['queryString']!= "") {
		$q = mysql_real_escape_string($_REQUEST['queryString']);
		//$r = mysql_query("select file_id,tradename,concat(`address`,', ',b.city,', ',c.province) as address,terms from contact_info a left join options_cities b on a.city=b.city_id left join options_provinces c on a.province=c.province_id where locate('$_REQUEST[queryString]',tradename) > 0 and company in ('0','$_SESSION[company]') limit 20");
		$r = mysql_query("SELECT file_id,tradename,CONCAT(`address`,', ',d.brgyDesc,', ',b.citymunDesc,', ',c.provDesc) AS address,terms FROM contact_info a LEFT JOIN options_cities b ON a.city = b.citymunCode LEFT JOIN options_provinces c ON a.province = c.provCode LEFT JOIN options_brgy d ON a.brgy = d.brgyCode WHERE LOCATE('$q',tradename) > 0;");
	
		//if ($r)	{
			$i = 0;
			echo "<table width=100% border=0 cellspacing=0 cellpadding=0 onMouseOut=\"javascript:highlightTableRowVersionA(0);\">";
					echo "<tr><td colspan=2 class=gridhead style='padding: 10px;'>Search Results for \"<b>$_REQUEST[queryString]</b>\" string</td></tr>";
					while(list($fid,$name,$address,$terms) = mysql_fetch_array($r)) {
						$name = html_entity_decode($name);
						if($i%2==0){ $bgC = "#ededed"; } else { $bgC = "#ffffff"; }
						$xname = preg_replace('/(' . $q . ')/i', '<span style="font-weight:bold;">$1</span>', $name);
						echo "<tr onMouseOver=\"javascript:highlightTableRowVersionA(this,'#95f0e8');\" onclick=\"pickContact('$fid','".rawurlencode($name)."','".rawurlencode($address)."','$terms');\"><td class=grid bgcolor='$bgC' style='padding-left: 10px;' width=15%>$fid</td><td class=grid bgcolor='$bgC' style='padding-left: 10px;'>$xname</td></tr>";
						$i++;
					}
			echo "</table>";
		//}
	}
	mysql_close($con);
?>