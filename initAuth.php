<?php
	$con = mysql_connect('localhost', 'root', '');
	if (!$con) { die('Could not connect: ' . mysql_error());  }
	@mysql_select_db("demo",$con);

	switch($_POST['mod']) {

		case "getCompany":
			list($empid) = mysql_fetch_array(mysql_query("SELECT emp_id FROM user_info a WHERE a.username = '$_REQUEST[uname]';"));
			if($empid!="") {
			$bl = mysql_query("select distinct company_id,company_name from companies a left join company_rights b on a.company_id = b.company where b.uid = '$empid';");
				echo "<option value=''>- Select Company -</option>";
				while(list($bid,$bname) = mysql_fetch_array($bl)) {
					echo "<option value='$bid'>$bname</option>";
				}
			} else { echo "error"; }

		break;

		case "getnewbranch":
			list($empid) = mysql_fetch_array(mysql_query("SELECT emp_id FROM user_info a WHERE a.username = '$_REQUEST[uname]';"));
			$bl = mysql_query("select distinct branch_code,branch_name from options_branches a left join company_rights b on a.branch_code = b.branch and a.company = b.company where b.uid = '$empid' and b.company ='$_POST[company]';");
			while(list($bid,$bname) = mysql_fetch_array($bl)) {
				echo "<option value='$bid'>$bname</option>";
			}	
		break;
	}

	@mysql_close($con);
?>