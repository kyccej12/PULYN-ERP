<?php 
	//ini_set("display_errors","on");
	session_start();
	require_once "handlers/_generics.php";
	
    $o = new _init;
    $b = array();

    $a = $o->getArray("SELECT record_id AS id, LPAD(a.so_no,6,0) AS myso,DATE_FORMAT(b.so_date,'%m/%d/%Y') AS sodate, LPAD(b.patient_id,6,0) AS mypid,b.patient_name AS pname, YEAR(b.so_date) - YEAR(c.birthdate) AS age,IF(c.gender='M','Male','Female') AS gender, DATE_FORMAT(c.birthdate,'%m/%d/%Y') AS bday,e.patientstatus,b.physician,a.code,a.procedure,sampletype,serialno,DATE_FORMAT(extractdate,'%m/%d/%Y') AS exday,TIME_FORMAT(extractime,'%h:%i %p') AS etime,extractby,a.location FROM lab_samples a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_patientstat e ON b.patient_stat = e.id WHERE a.record_id = '$_REQUEST[lid]';");
    $b = $o->getArray("select *, verified from lab_audiometry where so_no = '$a[myso]' and serialno = '$a[serialno]' and pid = '$a[mypid]';");

    list($rdate,$preparedby,$performedby,$verified) = $o->getArray("select date_format(result_date,'%m/%d/%Y'),prepared_by,performed_by,verified from lab_audiometry where so_no = '$a[myso]' and serialno = '$a[serialno]' and pid = '$a[mypid]';");

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
        $(function() { 
            $("#audio_date").datepicker(); 
        
            var availableOptions = [
                "BILATERAL NORMAL HEARING ACUITY",
                "MILD HEARING LOSS",
                "MODERATE HEARING LOSS",
                "MODERATELY SEVERE HEARING LOSS",
                "SEVERE HEARING LOSS",
                "PROFOUND"
            ];

            $("#remarks").autocomplete({
                source: availableOptions,
                minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            }); 
        
        });

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

        function printResult() {
            var so_no = $('#audio_sono').val();
            var code = $('#audio_code').val();
            var serialno = $('#audio_serialno').val();
             
            parent.printAudioResult(so_no,code,serialno);

        }

        function computeAvgLeft() {
            var a = $("#2k_l").val();
            var b = $("#1k_l").val();
            var c = $("#500_l").val();
            var d = 3;

            var e = parseFloat(a) + parseFloat(b) + parseFloat(c);
            var f = parseFloat(e) / parseFloat(d);

           // console.log(e);


            $("#avg_l").val(f.toFixed(2));
        }

        function computeAvgRight() {
            var a = $("#2k_r").val();
            var b = $("#1k_r").val();
            var c = $("#500_r").val();
            var d = 3;

            var e = parseFloat(a) + parseFloat(b) + parseFloat(c);
            var f = parseFloat(e) / parseFloat(d);
            $("#avg_r").val(f.toFixed(2));
        }
    </script>
    <style>
        .centered {
            text-align: center;
        }
    </style>
</head>
<body>
    <form name="frmAudio" id="frmAudio"> 
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
         <tr>
             <td width=35% valign=top>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">SO #&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="audio_sono" id="audio_sono" value="<?php echo $a['myso']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Service Order Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="audio_sodate" id="audio_sodate" value="<?php echo $a['sodate']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient ID&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="audio_pid" id="audio_pid" value="<?php echo $a['mypid']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Result Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="audio_date" id="audio_date" value="<?php if($rdate !='') { echo $rdate; } else { echo date('m/d/Y'); } ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient Name&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="audio_pname" id="audio_pname" value="<?php echo $a['pname']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>

                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Gender&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="audio_gender" id="audio_gender" value="<?php echo $a['gender']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Birthdate&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="audio_birthdate" id="audio_birthdate" value="<?php echo $a['bday']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Age&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="audio_age" id="audio_age" value="<?php echo $a['age']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient Status&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="audio_patientstat" id="audio_patientstat" value="<?php echo $a['patientstatus']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Requesting Physician&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="audio_physician" id="audio_physician" value="<?php echo $a['physician']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                </table>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Test or Procedure&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="audio_procedure" id="audio_procedure" value="<?php echo $a['procedure']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Procedure Code&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="audio_code" id="audio_code" value="<?php echo $a['code']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Specimen Type&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="audio_spectype" id="audio_spectype">
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
                            <input type="text" class="gridInput" style="width:100%;" name="audio_serialno" id="audio_serialno" value="<?php echo $a['serialno']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Date Extracted&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="audio_extractdate" id="audio_extractdate" value="<?php echo $a['exday']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Time Extracted&nbsp;:</td>
                        <td align=left>
                
                            <input type="text" class="gridInput" style="width:100%;" name="audio_extracttime" id="audio_extracttime" value="<?php echo $a['etime']; ?>" readonly>

                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Case Number&nbsp;:</td>
                        <td align=left>
                
                            <input type="text" class="gridInput" style="width:100%;" name="audio_caseno" id="audio_caseno" value="<?php echo $a['lot_no']; ?>">

                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Extracted By&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="audio_extractby" id="audio_extractby" value="<?php echo $a['extractby']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Performed By&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="audio_performedby" id="audio_performedby">
                                <?php
                                    $iun = $o->dbquery("select emp_id,fullname from user_info where role = 'RELEASING ENCODERS';");
                                    while(list($aa,$ab) = $iun->fetch_array()) {
                                        echo "<option value='$aa' ";
                                        if($aa == $performedby) { echo "selected"; }
                                        echo ">$ab</option>";
                                    }
                                ?>
                            </select>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Prepared By&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="audio_preparedby" id="audio_preparedby">
                                <?php
                                    $iun = $o->dbquery("select emp_id,fullname from user_info where role = 'RADIOLOGIC TECHNOLOGIST';");
                                    while(list($aa,$ab) = $iun->fetch_array()) {
                                        echo "<option value='$aa' ";
                                        if($aa == $preparedby) { echo "selected"; }
                                        echo ">$ab</option>";
                                    }
                                ?>
                            </select>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                </table>   
            </td>
            <td width=1%>&nbsp;</td>
            <td width=64% valign=top >
                 <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>RESULT DETAILS</td></tr></table>
                 <table width=100% cellpadding=0 cellspacing=3 class="td_content">
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">FREQUENCY IN HERTZ (Hz)</td>
                        <td align="center" width=30% class="bareBold centered" style="padding-left: 15px; font-weight: bold;">RIGHT</td>
                        <td align="left" width=30% class="bareBold centered" style="padding-left: 15px; font-weight: bold;">LEFT</td>
                    </tr>
                    <tr><td height=5></td></tr>
                    <tr>
                        <td align="center" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">16,000&nbsp;</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="16k_r" id="16k_r" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['16k_r']); ?>">
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="16k_l" id="16k_l" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['16k_l']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">12,000&nbsp;</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="12k_r" id="12k_r" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['12k_r']); ?>">
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="12k_l" id="12k_l" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['12k_l']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">8,000&nbsp;</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="8k_r" id="8k_r" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['8k_r']); ?>">
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="8k_l" id="8k_l" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['8k_l']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">6,000&nbsp;</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="6k_r" id="6k_r" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['6k_r']); ?>">
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="6k_l" id="6k_l" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['6k_l']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">4,000&nbsp;</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="4k_r" id="4k_r" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['4k_r']); ?>">
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="4k_l" id="4k_l" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['4k_l']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">3,000&nbsp;</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="3k_r" id="3k_r" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['3k_r']); ?>">
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="3k_l" id="3k_l" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['3k_l']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">2,000&nbsp;</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="2k_r" id="2k_r" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['2k_r']); ?>" onchange="javascript: computeAvgRight();">
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="2k_l" id="2k_l" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['2k_l']); ?>" onchange="javascript: computeAvgLeft();">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">1,500&nbsp;</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="1500_r" id="1500_r" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['1500_r']); ?>">
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="1500_l" id="1500_l" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['1500_l']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">1,000&nbsp;</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="1k_r" id="1k_r" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['1k_r']); ?>" onchange="javascript: computeAvgRight();">
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="1k_l" id="1k_l" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['1k_l']); ?>" onchange="javascript: computeAvgLeft();">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">750&nbsp;</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="750_r" id="750_r" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['750_r']); ?>">
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="750_l" id="750_l" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['750_l']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">500&nbsp;</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="500_r" id="500_r" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['500_r']); ?>" onchange="javascript: computeAvgRight();">
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="500_l" id="500_l" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['500_l']); ?>" onchange="javascript: computeAvgLeft();">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">250&nbsp;</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="250_r" id="250_r" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['250_r']); ?>">
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="250_l" id="250_l" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['250_l']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">125&nbsp;</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="125_r" id="125_r" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['125_r']); ?>">
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="125_l" id="125_l" pattern="^\d*(\.\d{0,2})?$" value="<?php echo number_format($b['125_l']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">REMARKS&nbsp;:</td>
                        <td colspan=6>
                            <textarea name="remarks" id="remarks" style="width: 100%;" rows=3><?php echo $b['remarks']; ?></textarea>
                        </td>
                    </tr>
                    <tr><td height=15>&nbsp;</td></tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">AVERAGE&nbsp;:</td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="avg_r" id="avg_r" value="<?php echo number_format($b['avg_r'],2); ?>" readonly>
                        </td>
                        <td align=left width=20%>
                            <input type="text" class="gridInput centered" style="width:100%;" name="avg_l" id="avg_l" value="<?php echo number_format($b['avg_l'],2); ?>" readonly>
                        </td>
                    </tr>
                </table>
                <table><tr><td height=3></td></tr></table>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>REFERANGE RANGE</td></tr></table>
                 <table width=100% cellpadding=0 cellspacing=3 class="td_content">
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">NORMAL</td>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">0 - 20</td>
                    </tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">MILD HEARING LOSS</td>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">25 - 45</td>
                    </tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">MODERATE HEARING LOSS</td>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">50 - 60</td>
                    </tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">MODERATE SEVERE HEARING LOSS</td>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">65 - 75</td>
                    </tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">SEVERE HEARING LOSS</td>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">80 - 90</td>
                    </tr>
                    <tr>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">PROFOUND</td>
                        <td align="left" width=25% class="bareBold" style="padding-left: 15px; font-weight: bold;">95 - 100</td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>              
</form>
</body>
</html>