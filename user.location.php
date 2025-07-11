<?php
	session_start();
	include("includes/dbUSE.php");
	
	$res = getArray("select fullname, username, user_type, email, `C1`, `C2`,`C3` from user_info where emp_id='$_GET[uid]';");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Ozian Realty Development</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script>

	function insertBranch(bid,cid,uid,e) {
		var uid = "<?php echo $_GET['uid']; ?>"
		var push;
		if(document.getElementById(e).checked == true) { push = "Y"; } else { push = "N"; }
		$.post("src/sjerp.php", { mod: "insertBranch", bid: bid,cid:cid, push: push, uid: uid, sid: Math.random() });
	}
	
</script>
</head>
<body leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
	<table width=100% class="td_content">
		<?php
			$mtabs = dbquery("select company_id,company_name from companies;"); $i = 0;
			while(list($mid,$mname) = mysql_fetch_array($mtabs)) {
				echo "<tr><td class=spandix width=30% align=\"right\" valign=top style=\"padding-top: 5px;\"><b>$mname :</b></td><td valign=middle>";
				//$stabs = dbquery("select submenu_id, menu_title from menu_sub where parent_id='$mid';");
				$stabs = dbquery("SELECT branch_code,company,branch_name from options_branches WHERE company='$mid' ORDER BY branch_code;");
				echo "<table width=100%>";
				$tloop = 1; 
				while(list($bid,$cid,$br_name) = mysql_fetch_array($stabs)) {
					if($tloop > 2 ) { echo "<tr>"; $tloop = 1; }
						echo "<td width=33% valign=top class=spandix><input type=checkbox id=\"cbox[$i]\" value=\"SUBMENU|$sid\" onclick=\"javascript: insertBranch($bid,$cid,$_REQUEST[uid],this.id);\" ";
						$isExistSub = getArray("select count(*) as found from company_rights where company='$cid' and branch='$bid' and uid='$_REQUEST[uid]';");
						if($isExistSub[0] > 0) { echo "checked"; }
							echo ">&nbsp;$br_name</td>";
							if($tloop > 2 )  { echo "</tr>"; }
								$tloop++; $i++;
				}
				echo "</table>";
				echo "</td><td width=\"2%\"></td></tr>";
				echo "<tr><td class=\"spandix\" align=\"right\"></td><td colspan=2><hr width=80% align=left style=\"border: 1px solid #195977;\" /></td></tr>";	
			}
			
		?>
	</table>
</body>
</html>
<?php mysql_close($con);