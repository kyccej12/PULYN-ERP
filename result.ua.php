<?php 
	
	session_start();
	require_once "handlers/_generics.php";
	
    $o = new _init;

    $a = $o->getArray("SELECT record_id AS id, LPAD(a.so_no,6,0) AS myso,DATE_FORMAT(b.so_date,'%m/%d/%Y') AS sodate, LPAD(b.patient_id,6,0) AS mypid,b.patient_name AS pname, FLOOR(ROUND(DATEDIFF(b.so_date,c.birthdate) / 364.25,2)) AS age,IF(c.gender='M','Male','Female') AS gender, DATE_FORMAT(c.birthdate,'%m/%d/%Y') AS bday,e.patientstatus,b.physician,a.code,a.procedure,sampletype,serialno,DATE_FORMAT(extractdate,'%m/%d/%Y') AS exday,TIME_FORMAT(extractime,'%h:%i %p') AS etime,extractby,a.location FROM lab_samples a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_patientstat e ON b.patient_stat = e.id WHERE a.record_id = '$_REQUEST[lid]';");
    $b = $o->getArray("select * from lab_uaresult where so_no = '$a[myso]' and serialno = '$a[serialno]' and branch = '$_SESSION[branchid]';");
   
    /* SET DEFAULT VALUE */
    if(!$b['glucose']) { $b['glucose'] = 'NEGATIVE'; }
    if(!$b['protein']) { $b['protein'] = 'NEGATIVE'; }

    if($b['ph'] >= 7) { 
        $uratesDisabled = "disabled"; 
        $poDisabled = '';
    } else {
        $uratesDisabled = ''; 
        $poDisabled = "disabled";
    }


?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Prime Care Cebu, Inc.</title>
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/texteditor/jquery-te-1.4.0.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="ui-assets/texteditor/jquery-te-1.4.0.min.js"></script>
	<script language="javascript" src="js/main.js?sid=<?php echo uniqid(); ?>"></script>
    <script>
        $(function() { 
            $("#ua_date").datepicker(); 
            
            var availableOptions = [
                "NEGATIVE",
                "TRACE",
                "RARE",
                "FEW",
                "MODERATE",
                "ABUNDANT",
                "0-1/LPF",
                "0-1/HPF",
            ];

            $("#blood, #bilirubin, #ketone, #urobilinogen, #nitrite, #glucose, #protein, #leukocyte, #amorphous_urates, #calcium_oxalate, #uric_acid, #amorphous_po4, #triple_phosphates, #bacteria, #mucus_thread, #yeast, #squamous, #bladder, #renal, #coarse_granular, #casts_wbc, #casts_rbc" ).autocomplete({
                 source: availableOptions
            });

        });

        $(document).on('keypress', 'input', function(e) {
            if(e.keyCode == 13) {
                e.preventDefault();
                var inputs = $(this).closest('form').find(':input:visible');
                 inputs.eq( inputs.index(this)+ 1 ).focus();
            }
        });

        function checkPhValue(val) {
           /*  var ph = parseFloat(val);

            if(ph >= 7) {
                $("#amorphous_urates").val('');
                $("#amorphous_urates").attr({ disabled: true });
                $("#amorphous_po4").attr({ disabled: false });
            } else {
                $("#amorphous_po4").val('');
                $("#amorphous_po4").attr({ disabled: true });
                $("#amorphous_urates").attr({ disabled: false });
            }
            */

        }

    </script>
</head>
<body>
    <form name="frmUrinalysisReport" id="frmUrinalysisReport"> 
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
         <tr>
             <td width=35% valign=top>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">SO #&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="ua_sono" id="ua_sono" value="<?php echo $a['myso']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Service Order Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="ua_sodate" id="ua_sodate" value="<?php echo $a['sodate']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient ID&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_pid" id="ua_pid" value="<?php echo $a['mypid']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Result Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="ua_date" id="ua_date" value="<?php if($rdate !='') { echo $rdate; } else { echo date('m/d/Y'); } ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient Name&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_pname" id="ua_pname" value="<?php echo $a['pname']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>

                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Gender&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_gender" id="ua_gender" value="<?php echo $a['gender']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Birthdate&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_birthdate" id="ua_birthdate" value="<?php echo $a['bday']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Age&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_age" id="ua_age" value="<?php echo $a['age']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient Status&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_patientstat" id="ua_patientstat" value="<?php echo $a['patientstatus']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Requesting Physician&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_physician" id="ua_physician" value="<?php echo $a['physician']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                </table>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Test or Procedure&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_procedure" id="ua_procedure" value="<?php echo $a['procedure']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Procedure Code&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_code" id="ua_code" value="<?php echo $a['code']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Specimen Type&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="ua_spectype" id="ua_spectype">
                                <?php
                                    $iun = $o->dbquery("select id,sample_type from options_sampletype;");
                                    while(list($aa,$ab) = $iun->fetch_array()) {
                                        echo "<option value='$aa'";
                                        if($aa == $a['sampletype']) { echo "selected"; }
                                       echo ">$ab</option>";
                                    }
                                ?>
                            </select>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Sample Serial No.&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_serialno" id="ua_serialno" value="<?php echo $a['serialno']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Date Extracted&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_extractdate" id="ua_extractdate" value="<?php echo $a['exday']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Time Extracted&nbsp;:</td>
                        <td align=left>
                
                            <input type="text" class="gridInput" style="width:100%;" name="ua_extracttime" id="ua_extracttime" value="<?php echo $a['etime']; ?>" readonly>

                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Extracted By&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ua_extractby" id="ua_extractby" value="<?php echo $a['extractby']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Extraction Site&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="ua_location" id="ua_location">
                                <?php
                                    $iun = $o->dbquery("select id,location from lab_locations;");
                                    while(list($aa,$ab) = $iun->fetch_array()) {
                                        echo "<option value='$aa' ";
                                        if($aa == $a['location']) { echo "selected"; }
                                        echo ">$ab</option>";
                                    }
                                ?>
                            </select>
                        </td>				
                    </tr>
                </table>   
            </td>
            <td width=1%>&nbsp;</td>
            <td width=64% valign=top >
                 <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
                 <table width=100% cellpadding=0 cellspacing=3 class="td_content">
                    <tr>
                        <td align="left" colspan=3 class="bareBold" style="padding-left: 15px;"><b>PHYSICAL&nbsp;:</b></td>
                    </tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 25px;">Color&nbsp;:</td>
                        <td align=left width=30%>
                             <input type="text" class="gridInput" style="width:100%;" name="color" id="color" value="<?php echo $b['color']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 25px;">Transparancy&nbsp;:</td>
                        <td align=left>
                            <select name="appearance" id="appearance" class="gridInput" style="width:100%;">
                                <option value="CLEAR" <?php if($b['appearance'] == 'CLEAR') { echo "selected"; } ?>>CLEAR</option>
                                <option value="HAZY" <?php if($b['appearance'] == 'HAZY') { echo "selected"; } ?>>HAZY</option>
                                <option value="SLIGHTLY HAZY" <?php if($b['appearance'] == 'SLIGHTLY HAZY') { echo "selected"; } ?>>SLIGHTLY HAZY</option>
                                <option value="CLOUDY" <?php if($b['appearance'] == 'CLOUDY') { echo "selected"; } ?>>CLOUDY</option>
                                <option value="SLIGHTLY CLOUDY" <?php if($b['appearance'] == 'SLIGHTLY CLOUDY') { echo "selected"; } ?>>SLIGHTLY CLOUDY</option>
                                <option value="TURBID" <?php if($b['appearance'] == 'TURBID') { echo "selected"; } ?>>TURBID</option>
                            </select>
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 25px;">pH&nbsp;:</td>
                        <td align=left>
                            <select name="ph" id="ph" class="gridInput" style="width:100%;" onchange="javascript: checkPhValue(this.value);">
                            <?php
                                for($phloop = 4.5; $phloop <= 8; $phloop+=0.5) {
                                    echo "<option value='".number_format($phloop,1)."' "; 
                                    if($b['ph'] == $phloop) { echo "selected"; }
                                    echo ">".number_format($phloop,1)."</option>";
                                }

                            ?>
                            </select>
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
      
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;" valign=top>Specific Gravity&nbsp;:</td>
                        <td align=left valign=top>
                            <select name="gravity" id="gravity" class="gridInput" style="width:100%;">
                            <?php
                                for($sgloop = 1.005; $sgloop <= 1.030; $sgloop+=0.005) {
                                    $valsg = number_format($sgloop,3);


                                    echo "<option value='".$valsg."' "; 
                                    if($b['gravity'] == $valsg) { echo "selected"; }
                                    echo ">".$valsg."</option>";
                                }

                                /* echo "<option value = '1.030' ";
                                if($b['gravity'] == '1.030') { echo "selected"; }
                                echo ">1.030</option>"; */

                            ?>
                            </select>

                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;" valign=top></td>	
                    </tr>

                    <tr>
                        <td align="left" colspan=3 class="bareBold" style="padding-left: 15px; padding-top: 20px;"><b>CHEMICAL&nbsp;:</b></td>
                    </tr>           

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Blood&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="blood" id="blood" value="<?php echo $b['blood']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Bilirubin&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bilirubin" id="bilirubin" value="<?php echo $b['bilirubin']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Urobilinogen&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="urobilinogen" id="urobilinogen" value="<?php echo $b['urobilinogen']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Ketone&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="ketone" id="ketone" value="<?php echo $b['ketone']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Protein&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="protein" id="protein" value="<?php echo $b['protein']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Nitrite&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="nitrite" id="nitrite" value="<?php echo $b['nitrite']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Glucose&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="glucose" id="glucose" value="<?php echo $b['glucose']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                  
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Leukocyte&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="leukocyte" id="leukocyte" value="<?php echo $b['leukocyte']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                   
                    
                    <tr>
                        <td align="left" colspan=3 class="bareBold" style="padding-left: 15px; padding-top: 20px;"><b>MICROSCOPIC&nbsp;:</b></td>
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">WBC/hpf&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="wbc_hpf" id="wbc_hpf" value="<?php echo $b['wbc_hpf']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">RBC/hpf&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="rbc_hpf" id="rbc_hpf" value="<?php echo $b['rbc_hpf']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Yeast&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="yeast" id="yeast" value="<?php echo $b['yeast']; ?>">  
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Mucus Threads&nbsp;:</td>
                        <td align=left>
                            <select name="mucus_thread" id="mucus_thread" class="gridInput" style="width:100%;">
                                <option value="RARE" <?php if($b['mucus_thread'] == 'RARE') { echo "selected"; } ?>>RARE</option>
                                <option value="FEW" <?php if($b['mucus_thread'] == 'FEW') { echo "selected"; } ?>>FEW</option>
                                <option value="MODERATE" <?php if($b['mucus_thread'] == 'MODERATE') { echo "selected"; } ?>>MODERATE</option>
                                <option value="MANY" <?php if($b['mucus_thread'] == 'MANY') { echo "selected"; } ?>>MANY</option>
                                <option value="ABUNDANT" <?php if($b['mucus_thread'] == 'ABUNDANT') { echo "selected"; } ?>>ABUNDANT</option>
                            </select>
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Bacteria&nbsp;:</td>
                        <td align=left>
                            <select name="bacteria" id="bacteria" class="gridInput" style="width:100%;">
                                <option value="RARE" <?php if($b['bacteria'] == 'RARE') { echo "selected"; } ?>>RARE</option>
                                <option value="FEW" <?php if($b['bacteria'] == 'FEW') { echo "selected"; } ?>>FEW</option>
                                <option value="MODERATE" <?php if($b['bacteria'] == 'MODERATE') { echo "selected"; } ?>>MODERATE</option>
                                <option value="MANY" <?php if($b['bacteria'] == 'MANY') { echo "selected"; } ?>>MANY</option>
                                <option value="ABUNDANT" <?php if($b['bacteria'] == 'ABUNDANT') { echo "selected"; } ?>>ABUNDANT</option>
                            </select>
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Amorphous&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="amorphous_urates" id="amorphous_urates" value="<?php echo $b['amorphous_urates']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    
                    <tr>
                        <td align="left" colspan=3 class="bareBold" style="padding-left: 15px; padding-top: 20px;"><b>EPITHELIAL CELLS&nbsp;:</b></td>
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Squamous&nbsp;:</td>
                        <td align=left>
                            <select name="squamous" id="squamous" class="gridInput" style="width:100%;">
                                <option value="RARE" <?php if($b['squamous'] == 'RARE') { echo "selected"; } ?>>RARE</option>
                                <option value="FEW" <?php if($b['squamous'] == 'FEW') { echo "selected"; } ?>>FEW</option>
                                <option value="MODERATE" <?php if($b['squamous'] == 'MODERATE') { echo "selected"; } ?>>MODERATE</option>
                                <option value="MANY" <?php if($b['squamous'] == 'MANY') { echo "selected"; } ?>>MANY</option>
                                <option value="ABUNDANT" <?php if($b['squamous'] == 'ABUNDANT') { echo "selected"; } ?>>ABUNDANT</option>
                            </select>
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Bladder&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bladder" id="bladder" value="<?php echo $b['bladder']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">Renal&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="renal" id="renal" value="<?php echo $b['renal']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>
                    
                    <tr>
                        <td align="left" colspan=3 class="bareBold" style="padding-left: 15px; padding-top: 20px;"><b>CASTS&nbsp;:</b></td>
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">&nbsp;</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="hyaline" id="hyaline" value="<?php echo $b['hyaline']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">&nbsp;</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="coarse_granular" id="coarse_granular" value="<?php echo $b['coarse_granular']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">&nbsp;</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="casts_wbc" id="casts_wbc" value="<?php echo $b['casts_wbc']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;">&nbsp;</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="casts_rbc" id="casts_rbc" value="<?php echo $b['casts_rbc']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" colspan=3 class="bareBold" style="padding-left: 15px; padding-top: 20px;"><b>CRYSTALS&nbsp;:</b></td>
                    </tr>     

                    <!-- <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="calcium_oxalate" id="calcium_oxalate" value="<?php echo $b['calcium_oxalate']; ?>" >
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>

                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="uric_acid" id="uric_acid" value="<?php echo $b['uric_acid']; ?>" >
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="amorphous_po4" id="amorphous_po4" value="<?php echo $b['amorphous_po4']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="triple_phosphates" id="triple_phosphates" value="<?php echo $b['triple_phosphates']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr> -->
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="crystal1" id="crystal1" value="<?php echo $b['crystal1']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="crystal2" id="crystal2" value="<?php echo $b['crystal2']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="crystal3" id="crystal3" value="<?php echo $b['crystal3']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    <tr>
                        <td align="left" class="bareBold" style="padding-left: 25px;"></td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="crystal4" id="crystal4" value="<?php echo $b['crystal4']; ?>">
                        </td>
                        <td align="left" class="bareBold" style="padding-left: 15px;"></td>	
                    </tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; padding-top: 5px;" valign=top><b>Comment&nbsp;:</b></td>
                        <td align=left width=75% colspan=3>
                            <input type="text" class="gridInput" style="width:90%; height: 50px; text-align: center;" name="remarks" id="remarks" value="<?php echo $b['remarks']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>              
</form>
</body>
</html>