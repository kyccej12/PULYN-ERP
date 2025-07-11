<?php 
	session_start();
	require_once "handlers/_generics.php";
	
    $o = new _init;

    $a = $o->getArray("SELECT record_id AS id, LPAD(a.so_no,6,0) AS myso,DATE_FORMAT(b.so_date,'%m/%d/%Y') AS sodate, LPAD(b.patient_id,6,0) AS mypid,b.patient_name AS pname, YEAR(b.so_date) - YEAR(c.birthdate) AS age,IF(c.gender='M','Male','Female') AS gender, DATE_FORMAT(c.birthdate,'%m/%d/%Y') AS bday,e.patientstatus,b.physician,a.code,a.procedure,sampletype,serialno,DATE_FORMAT(extractdate,'%m/%d/%Y') AS exday,TIME_FORMAT(extractime,'%h:%i %p') AS etime,extractby,a.location FROM lab_samples a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_patientstat e ON b.patient_stat = e.id WHERE a.record_id = '$_REQUEST[lid]';");
    $b = $o->getArray("select * from lab_egfr where so_no = '$a[myso]' and serialno = '$a[serialno]' and branch = '$_SESSION[branchid]';");
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Prime Medical Diagnostic Corp.</title>
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/texteditor/jquery-te-1.4.0.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="ui-assets/texteditor/jquery-te-1.4.0.min.js"></script>
	<script language="javascript" src="js/main.js?sid=<?php echo uniqid(); ?>"></script>
    <script>
        $(function() { $("#tsh_date").datepicker(); });

        $(document).on('keydown', 'input[pattern]', function(e){
            var input = $(this);
            var oldVal = input.val();
            var regex = new RegExp(input.attr('pattern'), 'g');

            setTimeout(function(){
                var newVal = input.val();
                if(!regex.test(newVal)){
                input.val(oldVal); 
                }
            }, 1);
        });

    </script>
</head>
<body>
    <form name="frmEGFR" id="frmEGFR"> 
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
            <tr>
                <td width=35% valign=top>
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">SO #&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="egfr_sono" id="egfr_sono" value="<?php echo $a['myso']; ?>" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Service Order Date&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="egfr_sodate" id="egfr_sodate" value="<?php echo $a['sodate']; ?>" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient ID&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="egfr_pid" id="egfr_pid" value="<?php echo $a['mypid']; ?>" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Result Date&nbsp;:</td>
                            <td align=left>
                                <input class="gridInput" style="width:100%;" type=text name="egfr_date" id="egfr_date" value="<?php if($rdate !='') { echo $rdate; } else { echo date('m/d/Y'); } ?>">
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient Name&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="egfr_pname" id="egfr_pname" value="<?php echo $a['pname']; ?>" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>

                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Gender&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="egfr_gender" id="egfr_gender" value="<?php echo $a['gender']; ?>" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Birthdate&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="egfr_birthdate" id="egfr_birthdate" value="<?php echo $a['bday']; ?>" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Age&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="egfr_age" id="egfr_age" value="<?php echo $a['age']; ?>" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient Status&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="egfr_patientstat" id="egfr_patientstat" value="<?php echo $a['patientstatus']; ?>" readonly> 
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Requesting Physician&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="egfr_physician" id="egfr_physician" value="<?php echo $a['physician']; ?>" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                    </table>
                    <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                    <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Test or Procedure&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="egfr_procedure" id="egfr_procedure" value="<?php echo $a['procedure']; ?>" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Procedure Code&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="egfr_code" id="egfr_code" value="<?php echo $a['code']; ?>" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Specimen Type&nbsp;:</td>
                            <td align=left>
                                <select class="gridInput" style="width:100%;" name="egfr_spectype" id="egfr_spectype">
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
                                <input type="text" class="gridInput" style="width:100%;" name="egfr_serialno" id="egfr_serialno" value="<?php echo $a['serialno']; ?>" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Date Extracted&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="egfr_extractdate" id="egfr_extractdate" value="<?php echo $a['exday']; ?>" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Time Extracted&nbsp;:</td>
                            <td align=left>
                    
                                <input type="text" class="gridInput" style="width:100%;" name="egfr_extracttime" id="egfr_extracttime" value="<?php echo $a['etime']; ?>" readonly>

                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Extracted By&nbsp;:</td>
                            <td align=left>
                                <input type="text" class="gridInput" style="width:100%;" name="egfr_extractby" id="egfr_extractby" value="<?php echo $a['extractby']; ?>" readonly>
                            </td>				
                        </tr>
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Phleb/Imaging Site&nbsp;:</td>
                            <td align=left>
                                <select class="gridInput" style="width:100%;" name="egfr_location" id="egfr_location">
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
                            <td align="left" width=33% class="bareBold" style="padding-left: 15px; font-weight: bold;">PARAMETER</td>
                            <td align="left" width=33% class="bareBold" style="padding-left: 15px; font-weight: bold;">RESULT</td>
                            <td align="center" width=33% class="bareBold" style="padding-left: 15px; font-weight: bold;">REFERENCE RANGE</td>
                        </tr>
                        <tr><td style="color:white;" height=5 colspan=3><hr></td></tr>

                        <!-- egfr -->
                        <tr>
                            <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">	EGFR (ESTIMATED eGFR)&nbsp;:</td>
                            <td width=35%>
                                <input type="text" class="bareBold" name="egfr_result" id="egfr_result" value="<?php echo number_format($b['egfr'],2); ?>">
                            </td>
                            <td align="center" width=55% class="bareBold" style="padding-left: 15px; font-weight: bold;"><?php echo $o->getAttribute('L121',$a['age'],$a['gender']); ?></td>	
                        </tr>                    
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">	Creatinine&nbsp;:</td>
                            <td width=35%>
                                <input type="text" class="bareBold" name="egfr_crea" id="egfr_crea" value="<?php echo number_format($b['crea'],2); ?>">
                            </td>
                            <td align="center" width=55% class="bareBold" style="padding-left: 15px; font-weight: bold;"><?php echo $o->getAttribute('L020',$a['age'],$a['gender']); ?></td>	
                        </tr>                    
                        <tr><td height=3></td></tr>
                        <tr>
                            <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;" valign=top>Remarks&nbsp;:</td>
                            <td align=left width=75% colspan=3>
                                <textarea name="egfr_remarks" id="egfr_remarks" style="width: 90%;" rows=3><?php echo $b['remarks']; ?></textarea>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>              
    </form>
</body>
</html>