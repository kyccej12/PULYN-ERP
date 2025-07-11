<?php
	
	require_once 'handlers/initDB.php';
	
	class authenticate extends myDB {
		function generateUniqueId($maxLength = null) {
			$entropy = '';
			if (function_exists('openssl_random_pseudo_bytes')) {
				$entropy = openssl_random_pseudo_bytes(64, $strong);
				if($strong !== true) {
					$entropy = '';
				}
			}
				$entropy .= uniqid(mt_rand(), true);
			if (class_exists('COM')) {
				try {
					$com = new COM('CAPICOM.Utilities.1');
					$entropy .= base64_decode($com->GetRandom(64, 0));
					} catch (Exception $ex) {
				}
			}
				
			if (is_readable('/dev/urandom')) {
				$h = fopen('/dev/urandom', 'rb');
				$entropy .= fread($h, 64);
				fclose($h);
			}

			$hash = hash('whirlpool', $entropy);
			if ($maxLength) {
				return substr($hash, 0, $maxLength);
			}
				return $hash;
		}
		
		function verify($uname,$pass,$comp,$clinicno,$type) {
			if(!empty($uname) && !empty($pass)) {
				$res = parent::dbquery("select username, emp_id as user_id, fullname, user_type from user_info where username='$uname' and password=md5('$pass');");
				if($res) {
					
					list($uname,$uid,$fname,$utype) = $res->fetch_array();
					if(!empty($uid)) {
						$this->storeSession($uid,$utype,$comp,$clinicno,$type);
						return true;
					} else {
						return false;
					}
				} else { return false; }
			} 
		}

		function storeSession($uid,$utype,$comp,$clinicno,$type) {
			$skey = $this->generateUniqueId(32);
			parent::dbquery("insert ignore into active_sessions (userid,timestamp,sessid) values ('$uid','".time()."','$skey');");
			parent::dbquery("update user_info set last_logged_in=now(), ws_last_logged_in='$_SERVER[REMOTE_ADDR]' where emp_id='$uid';");
			
			
			/* Store Session Values */
			session_start();
			$_SESSION['userid'] = $uid;
			$_SESSION['authkey'] = $skey;
			$_SESSION['utype'] = $utype;
			$_SESSION['company'] = "1";
			$_SESSION['branchid'] = "1";
			$_SESSION['clinic_no'] = $clinicno;
			$_SESSION['type'] = $type;

		}
	}
	
	$auth = new authenticate();
	if($auth->verify($_POST['txtname'],$_POST['txtpass'],"1",$_POST['clinic_no'],$_POST['type']) == true) {
		$URL = $HTTP_REFERER . "index_clinic.php";
	} else {
		$URL = $HTTP_REFERER . "login_clinic/index.php";
	}
	
	header("Location: $URL");
	exit();
?>
