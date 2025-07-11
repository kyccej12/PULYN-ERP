<?php
	echo '<div id="acctgReportMain" style="display: none;">
			<div style="padding: 20px;">';
			$msx = $o->dbquery("SELECT menu_title, jfunct, ifnull(icon_name,'reports256.png') as icon FROM menu_sub a LEFT JOIN user_rights b ON a.submenu_id=b.MENU_ID WHERE b.UID='$_SESSION[userid]' and a.subAsset = 'Y' and a.parentAsset = '88' ORDER BY sort;");
			while($menux = $msx->fetch_array(MYSQLI_BOTH)) {
				echo "\t\t\t\t<div class=\"fileObjects\"><a href=\"#\" onClick=\"$menux[jfunct]\"><img src=\"images/icons/$menux[icon]\" width=60 height=60 align=absmiddle border=0 /><br/><br/>$menux[menu_title]</a></div>\n";
			}
			if($_SESSION['userid'] == 1) {
				echo "\t\t\t\t<div class=\"fileObjects\"><a href=\"#\" onClick=\"showVAT();\"><img src=\"images/icons/bir-logo.png\" width=60 height=60 align=absmiddle border=0 /><br/><br/>Generate BIR Relief DAT File</a></div>\n";
			}
	echo '</div></div>';		

?>