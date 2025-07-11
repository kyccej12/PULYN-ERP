<?php

	function createMenu($uid) {	

		//global $con;

		list($rcount) = getArray("select count(*) from user_rights where UID='$uid';");
		if($rcount > 0) {
			echo "<div id=\"menu\" class=\"chromestyle\">\n<div id=\"submenu_1\">\n<ul>";
			$res = $con->query("SELECT DISTINCT c.id, c.tab, c.iconf FROM user_rights a LEFT JOIN menu_sub b ON a.MENU_ID=b.submenu_id LEFT JOIN menu_main c ON b.parent_id=c.id WHERE a.UID='$uid' and c.id != '3' order by c.sort;");	
			while($main = $res->fetch_array(MYSQLI_BOTH)) {
				echo "<li><a href=\"#\" rel=\"dropmenu$main[0]\"><img src=\"images/icons/$main[iconf]\" width=20 height=20 align=absmiddle />&nbsp;$main[1]</a></li>\n";
			}
			echo "</ul>\n</div>\n</div>";
			$res2 = $con->query("SELECT DISTINCT c.id, c.tab FROM user_rights a LEFT JOIN menu_sub b ON a.MENU_ID=b.submenu_id LEFT JOIN menu_main c ON b.parent_id=c.id WHERE a.UID='$uid' and b.subAsset = 'N' and c.id != '3';");	
			while($sub = $res2->fetch_array(MYSQLI_BOTH)) {
				$ms = $con->query("SELECT menu_title, jfunct, icon_name FROM menu_sub a LEFT JOIN user_rights b ON a.submenu_id=b.MENU_ID WHERE parent_id='$sub[0]' AND b.UID='$uid' and a.subAsset = 'N' ORDER BY sort;");
				list($icount) = getArray("select (count(*)-1) as icount from (SELECT menu_title, jfunct, icon_name FROM menu_sub a LEFT JOIN user_rights b ON a.submenu_id=b.MENU_ID WHERE parent_id='$sub[0]' AND b.UID='$uid' and a.subAsset = 'N') a;");
				echo "<div id=\"dropmenu$sub[0]\" class=\"dropmenudiv\">";
				$cut = 0;
				while($menu = $ms->fetch_array(MYSQLI_BOTH)) {
					echo "<a href=\"#\" onClick=\"$menu[jfunct]\"><img src=\"images/icons/$menu[icon_name]\" width=20 height=20 align=absmiddle border=0 />&nbsp;&nbsp;$menu[menu_title]</a>\n";
					if($icount != 0) {
						if($cut < $icount) { echo "<hr width=95% align=center style=\"border-color: #ffffff;\"></hr>";	}
					}
					$cut++;
				}
				echo "</div>";
				
			}
			echo "<script type=\"text/javascript\">cssdropdown.startDROP(\"menu\")</script>";
		}
	}
	
	list($cpass,$fname) = getArray("select require_change_pass, fullname from user_info where emp_id='$_SESSION[userid]';");
	
?>