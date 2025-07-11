<?php

	function validateKey() {
		
		
		
		$tcur = time();
		function renew_timestamp($key,$time) {
			$v = dbquery("update active_sessions set timestamp=$time where sessid='$key';");
			if($v) { return true; }
		}
		
		$_sess = dbquery("select * from active_sessions where sessid='$_SESSION[authkey]';");
		if($_sess) {
			list($uid,$tstamp,$key) = getArray("select * from active_sessions where sessid='$_SESSION[authkey]';");
			$life = $tstamp - $tcur;
			if($life > 7200) {
				$exception = 2;
				unset($_SESSION['authkey']);
				unset($_SESSION['userid']);
				session_destroy();
			} else {
				if(renew_timestamp($key,$tcur) == true) { $exception = 0; } else { $exception = 3; }
			}
		} else {
			$exception = 4;
		}
		return $exception;
	}
	
	if(isset($_SESSION['authkey'])) {
		$exception = validateKey();
		if($exception != 0) {	$URL = $HTTP_REFERER . "login/index.php?exception=$exception"; }
	} else { $URL = $HTTP_REFERER . "login"; }
	
	if($URL) { header("Location: $URL"); };
	

?>