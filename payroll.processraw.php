<?php
	include("includes/dbUSE.php");

	$q = dbquery("SELECT a.record_id, emp_id, IFNULL(b.paysched,2) AS paysched, t_in, TIME_TO_SEC(t_in) AS isec, t_out, TIME_TO_SEC(t_out) AS iout FROM e_dtr a LEFT JOIN e_master b ON a.emp_id = b.id_no WHERE TIME_TO_SEC(t_in) > 0 AND TIME_TO_SEC(t_out) > 0;");
	while($data = mysql_fetch_array($q)) {
		switch($data['paysched']) {
			case "4":
				$in = 28800; $mid = 43200; $out = 61200; $ot = 63000; $nn = 3600;

				/* Late */
				if($data['isec'] > $in && $data['isec'] < 46800) { $late = ROUND(($data['isec'] - $in) / 3600,2); }

				/* Over Time */
				if($data['iout'] >= $ot) { $tot = ROUND(($data['iout'] - $out) / 3600,2); if($tot < 1) { $tot = 0; } }

				if($data['isec'] <= $in) { $myIn = $in; } else { $myIn = $data['isec']; }
				
				if($data['iout'] <= 46800 && $data['iout'] >= $mid) { $myOut = $mid; $nn = 0; }

				if($data['iout'] > 46800) {		
					if($data['iout'] >= $out && $data['iout'] < 64800) { $myOut = $out; } else { $myOut = $data['iout']; } 
				}
				
				$twork = $myOut - $myIn - $nn;
				$twork = ROUND($twork/3600,2) - $tot;

				dbquery("update e_dtr set hrs = 0$twork, late = 0$late, ot = 0$tot where record_id = '$data[record_id]';");

				echo "(IN: $data[t_in] | OUT: $data[t_out]) -> update e_dtr set hrs = 0$twork, late = 0$late, ot = 0$tot where record_id = '$data[record_id]';<br/>";

			break;
			case "5":
				$in = 32400; $mid = 50400; $out = 64800; $ot = 66600; $nn = 3600;

				/* Late */
				if($data['isec'] > $in && $data['isec'] < $mid) { $late = ROUND(($data['isec'] - $in) / 3600,2); }

				/* Over Time */
				if($data['iout'] >= $ot) { $tot = ROUND(($data['iout'] - $out) / 3600,2); if($tot < 1) { $tot = 0; } }

				if($data['isec'] <= $in) { $myIn = $in; } else { $myIn = $data['isec']; }
				
				if($data['iout'] > $mid) {		
					if($data['iout'] >= $out && $data['iout'] < 68400) { $myOut = $out; } else { $myOut = $data['iout']; } 
				} else { $myOut = $data['iout']; }
				
				$twork = $myOut - $myIn - $nn;
				$twork = ROUND($twork/3600,2) - $tot;

				dbquery("update e_dtr set hrs = 0$twork, late = 0$late, ot = 0$tot where record_id = '$data[record_id]';");

				echo "(IN: $data[t_in] | OUT: $data[t_out]) -> update e_dtr set hrs = 0$twork, late = 0$late, ot = 0$tot where record_id = '$data[record_id]';<br/>";
			break;

		}

		
		$twork = 0; $late = 0; $tot = 0;
	}


?>