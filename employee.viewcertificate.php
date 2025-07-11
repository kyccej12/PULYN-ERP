<?php
	session_start();
	include("includes/dbUSE.php");

 /* READING SELECTED FILE */
	if(isset($_POST['viewFlag'])) {
		$fileId = $_POST['fileid'];
		$result = mysql_query("select filename, filetype, filesize, filepath from hris.emp_certificates where record_id = '$fileId';");
		list($filename, $filetype, $filesize, $filepath) = mysql_fetch_array($result);
		
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-length: $filesize");
		header("Content-type: $filetype");
		readfile($filepath);
	}
 /* END READING FILE */
		mysql_close($con);
 ?>