<?php 
	//ini_set("display_errors","on");
	session_start();
	require_once "handlers/_generics.php";
	
    $o = new _init;

    $order = $o->getArray("select *, date_format(extractdate,'%m/%d/%Y') as exdate from lab_samples where record_id = '$_REQUEST[lid]';");
    $a = $o->getArray("SELECT record_id AS id, LPAD(a.so_no,6,0) AS myso,DATE_FORMAT(b.so_date,'%m/%d/%Y') AS sodate, LPAD(b.patient_id,6,0) AS mypid,b.patient_name AS pname, YEAR(b.so_date) - YEAR(c.birthdate) AS age, c.birthdate, IF(c.gender='M','Male','Female') AS gender, gender as xgender, DATE_FORMAT(c.birthdate,'%m/%d/%Y') AS bday,e.patientstatus,b.physician,a.code,a.procedure,a.sampletype,a.serialno,DATE_FORMAT(extractdate,'%m/%d/%Y') AS exday,TIME_FORMAT(extractime,'%h:%i %p') AS etime,extractby,a.location FROM lab_samples a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_patientstat e ON b.patient_stat = e.id WHERE b.so_no = '$order[so_no]';");
    $b = $o->getArray("select * from lab_bloodchem where so_no = '$a[myso]' and serialno = '$order[serialno]' and branch = '$_SESSION[branchid]';");

    if(count($b) == 0) {
        $b = $o->getArray("select * from lab_bloodchem_temp where serialno = '$order[serialno]' order by parsed_on desc limit 1;");
    }

    $o->calculateAge($a['sodate'],$a['birthdate']);

    list($testCount) = $o->getArray("select count(*) from lab_samples where serialno = '$order[serialno]';");
    if($testCount > 1) {
        $procedure = '';
        $code ='';
        $testQuery = $o->dbquery("select `procedure`, code from lab_samples where serialno = '$order[serialno]';");
        while($testRow = $testQuery->fetch_array()) {
            $procedure .= $testRow[0] . ",";
            $code .= $testRow[1] . ",";
        }
        $procedure = substr($procedure,0,-1);
    } else { $procedure = $order['procedure']; }


    function checkTest($code,$serialno) {
        global $o;

        list($isTested) = $o->getArray("select count(*) from lab_samples where `code` = '$code' and serialno = '$serialno';");
        if($isTested > 0 ) { return true; } else { return false; }

    }

     /* Previous Results */
     $d = $o->getArray("select *, concat('<br/>',date_format(result_date,'%m/%d/%Y')) as rdate from lab_bloodchem where so_no = '$a[myso]' and result_date < '$b[result_date]' limit 1,1;");
     $e = $o->getArray("select *, concat('<br/>',date_format(result_date,'%m/%d/%Y')) as rdate from lab_bloodchem where so_no = '$a[myso]' and result_date < '$b[result_date]' limit 2,1;");
     $f = $o->getArray("select *, concat('<br/>',date_format(result_date,'%m/%d/%Y')) as rdate from lab_bloodchem where so_no = '$a[myso]' and result_date < '$b[result_date]' limit 3,1;");
 
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Pulyn Dialysis & Diagnostics Medical Center</title>
    <link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/texteditor/jquery-te-1.4.0.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/datatables/css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="ui-assets/texteditor/jquery-te-1.4.0.min.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
	<script language="javascript" src="js/main.js?sid=<?php echo uniqid(); ?>"></script>
    <script>
         $(document).ready(function($) { 

        $("#bloodchem_date").datepicker();
        var myTable = $('#itemlist').DataTable({
                "scrollY":  "540",
                "scrollCollapse": true,
                "select":	'single',
                "searching": false,
                "bSort": false,
                "paging": false,
                "info": false,
              
                "aoColumnDefs": [
                    { "className": "dt-body-center", "targets": [1,2,3,4,5,6] },
                ]
            });

            var remarksSelection = [
                "TEST DONE TWICE",
            ];

            $("#remarks").autocomplete({
                source: remarksSelection,
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

        /*
        function computeLDL() {
            var a = parseFloat($("#triglycerides").val());
            var b = parseFloat($("#hdl").val());
            var c = parseFloat($("#cholesterol").val());

            if(a != '' && b != '' && c != '') {
                var ldl = c - b - (a/5);
                $("#ldl").val(ldl.toFixed(2));
                console.log(ldl);
            }
        }

        function computeVLDL() {
            var a = $("#triglycerides").val();

            if(a != '') {
                var b = parseFloat(a) / 5;
                $("#vldl").val(b.toFixed(2));
            }
        }
        */

    </script>
    <style>
        .dataTables_wrapper {
            display: inline-block;
            font-size: 11px;
            width: 100%;
        }
        
        table.dataTable tr.odd { background-color: #f5f5f5;  }
        table.dataTable tr.even { background-color: white; }
        .dataTables_filter input { width: 250px; }

        .noBorders {
            border: none !important; text-align: center; background-color: inherit !important;
        }

    </style>
</head>
<!-- <body onload="computeLDL(); computeVLDL();"> -->
<body>
    <form name="frmBloodChemResult" id="frmBloodChemResult"> 
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
         <tr>
             <td width=35% valign=top>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">SO #&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="bloodchem_sono" id="bloodchem_sono" value="<?php echo $a['myso']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Service Order Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="bloodchem_sodate" id="bloodchem_sodate" value="<?php echo $a['sodate']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient ID&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bloodchem_pid" id="bloodchem_pid" value="<?php echo $a['mypid']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Result Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="bloodchem_date" id="bloodchem_date" value="<?php if($rdate !='') { echo $rdate; } else { echo date('m/d/Y'); } ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient Name&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bloodchem_pname" id="bloodchem_pname" value="<?php echo $a['pname']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>

                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Gender&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bloodchem_gender" id="bloodchem_gender" value="<?php echo $a['gender']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Birthdate&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bloodchem_birthdate" id="bloodchem_birthdate" value="<?php echo $a['bday']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Age&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bloodchem_age" id="bloodchem_age" value="<?php echo $a['age']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient Status&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bloodchem_patientstat" id="bloodchem_patientstat" value="<?php echo $a['patientstatus']; ?>" readonly> 
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Requesting Physician&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bloodchem_physician" id="bloodchem_physician" value="<?php echo $a['physician']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                </table>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Test or Procedure&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bloodchem_procedure" id="bloodchem_procedure" value="<?php echo $procedure ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Procedure Code&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bloodchem_code" id="bloodchem_code" value="<?php echo $code; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Specimen Type&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="bloodchem_spectype" id="bloodchem_spectype">
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
                            <input type="text" class="gridInput" style="width:100%;" name="bloodchem_serialno" id="bloodchem_serialno" value="<?php echo $order['serialno']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Date Extracted&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bloodchem_extractdate" id="bloodchem_extractdate" value="<?php echo $a['exday']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Time Extracted&nbsp;:</td>
                        <td align=left>
                
                            <input type="text" class="gridInput" style="width:100%;" name="bloodchem_extracttime" id="bloodchem_extracttime" value="<?php echo $a['etime']; ?>" readonly>

                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Extracted By&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="bloodchem_extractby" id="bloodchem_extractby" value="<?php echo $a['extractby']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Phleb/Imaging Site&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="bloodchem_location" id="bloodchem_location">
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
            <table width=100% id = "itemlist" class="cell-border" style="font-size: 8pt;">
                <thead style="color: #fff; background-color: #000;">
                    <tr>
                        <th>PARAMETER</th>
                        <th>CURRENT RESULT</th>
                        <th>FLAG</th>
                        <th>PREVIOUS<?php echo $d['rdate']; ?></th>
                        <th>PREVIOUS<?php echo $e['rdate']; ?></th>
                        <th>PREVIOUS<?php echo $f['rdate']; ?></th>
                        <th>REFERENCE VALUES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(checkTest('L021',$order['serialno']) || checkTest('L212',$order['serialno'])) { ?>
                    <tr>
                        <td>Glucose/FBS</td>
                        <td>
                            <input type="text" class="noBorders" name="glucose" id="glucose" value="<?php echo number_format($b['glucose'],2); ?>" pattern="^\d*(\.\d{0,2})?$" >
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L021',$b['glucose']); ?></td>
                        <td><?php echo $d['glucose']; ?></td>
                        <td><?php echo $e['glucose']; ?></td>
                        <td><?php echo $f['glucose']; ?></td>
                        <td><?php echo $o->getAttribute('L021',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L004',$order['serialno']) || checkTest('L209',$order['serialno'])) { ?>
                    <tr>
                        <td>Blood Uric Acid</td>
                        <td>
                            <input type="text" class="noBorders" name="uric" id="uric" value="<?php echo number_format($b['uric'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['xgender'],'L004',$b['uric']); ?></td>
                        <td><?php echo $d['uric']; ?></td>
                        <td><?php echo $e['uric']; ?></td>
                        <td><?php echo $f['uric']; ?></td>
                        <td><?php echo $o->getAttribute('L004',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L009',$order['serialno'])) { ?>
                    <tr>
                        <td>Random Blood Sugar</td>
                        <td>
                            <input type="text" class="noBorders" name="rbs" id="rbs" value="<?php echo number_format($b['rbs'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['xgender'],'L009',$b['uric']); ?></td>
                        <td><?php echo $d['rbs']; ?></td>
                        <td><?php echo $e['rbs']; ?></td>
                        <td><?php echo $f['rbs']; ?></td>
                        <td><?php echo $o->getAttribute('L009',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L016',$order['serialno']) || checkTest('L252',$order['serialno'])) { ?>   
                    <tr>
                        <td>Blood Urea Nitrogen (BUN)</td>
                        <td>
                            <input type="text" class="noBorders" name="bun" id="bun" value="<?php echo number_format($b['bun'],3); ?>">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L016',$b['bun']); ?></td>
                        <td><?php echo $d['bun']; ?></td>
                        <td><?php echo $e['bun']; ?></td>
                        <td><?php echo $f['bun']; ?></td>
                        <td><?php echo $o->getAttribute('L016',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L018',$order['serialno'])) { ?>
                    <tr>
                        <td>Total Cholesterol</td>
                        <td>
                            <input type="text" class="noBorders" name="total_chol" id="total_chol" value="<?php echo number_format($b['total_chol'],2); ?>" pattern="^\d*(\.\d{0,2})?$" onchange="javascript: computeLDL();">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L018',$b['total_chol']); ?></td>
                        <td><?php echo $d['total_chol']; ?></td>
                        <td><?php echo $e['total_chol']; ?></td>
                        <td><?php echo $f['total_chol']; ?></td>
                        <td><?php echo $o->getAttribute('L018',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L023',$order['serialno'])) { ?>
                    <tr>
                        <td>SGOT/AST</td>
                        <td>
                            <input type="text" class="noBorders" name="sgot" id="sgot" value="<?php echo number_format($b['sgot'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L023',$b['sgot']); ?></td>
                        <td><?php echo $d['sgot']; ?></td>
                        <td><?php echo $e['sgot']; ?></td>
                        <td><?php echo $f['sgot']; ?></td>
                        <td><?php echo $o->getAttribute('L023',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L022',$order['serialno'])) { ?>
                    <tr>
                        <td>SGPT/ALT</td>
                        <td>
                            <input type="text" class="noBorders" name="sgpt" id="sgpt" value="<?php echo number_format($b['sgpt'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L022',$b['sgpt']); ?></td>
                        <td><?php echo $d['sgpt']; ?></td>
                        <td><?php echo $e['sgpt']; ?></td>
                        <td><?php echo $f['sgpt']; ?></td>
                        <td><?php echo $o->getAttribute('L022',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L020',$order['serialno']) || checkTest('L144',$order['serialno']) || checkTest('L211',$order['serialno'])) { ?>
                    <tr>
                        <td>CREATININE (CREA)</td>
                        <td>
                            <input type="text" class="noBorders" name="creatinine" id="creatinine" value="<?php echo number_format($b['creatinine'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L020',$b['creatinine']); ?></td>
                        <td><?php echo $d['creatinine']; ?></td>
                        <td><?php echo $e['creatinine']; ?></td>
                        <td><?php echo $f['creatinine']; ?></td>
                        <td><?php echo $o->getAttribute('L020',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L026',$order['serialno'])) { ?>
                    <tr>
                        <td>SODIUM (Na)</td>
                        <td>
                            <input type="text" class="noBorders" name="sodium" id="sodium" value="<?php echo number_format($b['sodium'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L026',$b['sodium']); ?></td>
                        <td><?php echo $d['sodium']; ?></td>
                        <td><?php echo $e['sodium']; ?></td>
                        <td><?php echo $f['sodium']; ?></td>
                        <td><?php echo $o->getAttribute('L026',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L025',$order['serialno'])) { ?>
                    <tr>
                        <td>POTASSIUM (K)</td>
                        <td>
                            <input type="text" class="noBorders" name="potassium" id="potassium" value="<?php echo number_format($b['potassium'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L025',$b['potassium']); ?></td>
                        <td><?php echo $d['potassium']; ?></td>
                        <td><?php echo $e['potassium']; ?></td>
                        <td><?php echo $f['potassium']; ?></td>
                        <td><?php echo $o->getAttribute('L025',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L031',$order['serialno'])) { ?>
                    <tr>
                        <td>TSH (THYROID STIMULATING HORMONES)</td>
                        <td>
                            <input type="text" class="noBorders" name="tsh" id="tsh" value="<?php echo number_format($b['tsh'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L031',$b['tsh']); ?></td>
                        <td><?php echo $d['tsh']; ?></td>
                        <td><?php echo $e['tsh']; ?></td>
                        <td><?php echo $f['tsh']; ?></td>
                        <td><?php echo $o->getAttribute('L031',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L033',$order['serialno'])) { ?>
                    <tr>
                        <td>FT3</td>
                        <td>
                            <input type="text" class="noBorders" name="ft3" id="ft3" value="<?php echo number_format($b['ft3'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L033',$b['ft3']); ?></td>
                        <td><?php echo $d['ft3']; ?></td>
                        <td><?php echo $e['ft3']; ?></td>
                        <td><?php echo $f['ft3']; ?></td>
                        <td><?php echo $o->getAttribute('L033',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L032',$order['serialno'])) { ?>
                    <tr>
                        <td>FT4 (THYROXINE)</td>
                        <td>
                            <input type="text" class="noBorders" name="ft4" id="ft4" value="<?php echo number_format($b['ft4'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L032',$b['ft4']); ?></td>
                        <td><?php echo $d['ft4']; ?></td>
                        <td><?php echo $e['ft4']; ?></td>
                        <td><?php echo $f['ft4']; ?></td>
                        <td><?php echo $o->getAttribute('L032',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L061',$order['serialno'])) { ?>
                    <tr>
                        <td>T3</td>
                        <td>
                            <input type="text" class="noBorders" name="t3" id="t3" value="<?php echo number_format($b['t3'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L061',$b['t3']); ?></td>
                        <td><?php echo $d['t3']; ?></td>
                        <td><?php echo $e['t3']; ?></td>
                        <td><?php echo $f['t3']; ?></td>
                        <td><?php echo $o->getAttribute('L061',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L062',$order['serialno'])) { ?>
                    <tr>
                        <td>T4</td>
                        <td>
                            <input type="text" class="noBorders" name="t4" id="t4" value="<?php echo number_format($b['t4'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L062',$b['t4']); ?></td>
                        <td><?php echo $d['t4']; ?></td>
                        <td><?php echo $e['t4']; ?></td>
                        <td><?php echo $f['t4']; ?></td>
                        <td><?php echo $o->getAttribute('L062',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L029',$order['serialno']) || checkTest('L006',$order['serialno']) || checkTest('L255',$order['serialno'])) { ?>
                    <tr>
                        <td>CALCIUM (Ca)/TOTAL Ca</td>
                        <td>
                            <input type="text" class="noBorders" name="calcium" id="calcium" value="<?php echo number_format($b['calcium'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L029',$b['calcium']); ?></td>
                        <td><?php echo $d['calcium']; ?></td>
                        <td><?php echo $e['calcium']; ?></td>
                        <td><?php echo $f['calcium']; ?></td>
                        <td><?php echo $o->getAttribute('L029',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L121',$order['serialno'])) { ?>
                    <tr>
                        <td>PHOSPHORUS</td>
                        <td>
                            <input type="text" class="noBorders" name="phosphorus" id="phosphorus" value="<?php echo number_format($b['phosphorus'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L121',$b['phosphorus']); ?></td>
                        <td><?php echo $d['phosphorus']; ?></td>
                        <td><?php echo $e['phosphorus']; ?></td>
                        <td><?php echo $f['phosphorus']; ?></td>
                        <td><?php echo $o->getAttribute('L121',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L203',$order['serialno'])) { ?>
                    <tr>
                        <td style="font-weight:bold; padding-top:10px; font-size:9pt;" align=left coslpan=>THYROID PANEL (COMPLETE)</td>
                    </tr>
                    <tr>
                        <td>TSH</td>
                        <td>
                            <input type="text" class="noBorders" name="tsh" id="tsh" value="<?php echo number_format($b['tsh'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L031',$b['tsh']); ?></td>
                        <td><?php echo $d['tsh']; ?></td>
                        <td><?php echo $e['tsh']; ?></td>
                        <td><?php echo $f['tsh']; ?></td>
                        <td><?php echo $o->getAttribute('L031',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <tr>
                        <td>FT3</td>
                        <td>
                            <input type="text" class="noBorders" name="ft3" id="ft3" value="<?php echo number_format($b['ft3'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L033',$b['ft3']); ?></td>
                        <td><?php echo $d['ft3']; ?></td>
                        <td><?php echo $e['ft3']; ?></td>
                        <td><?php echo $f['ft3']; ?></td>
                        <td><?php echo $o->getAttribute('L033',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <tr>
                        <td>FT4 (THYROXINE)</td>
                        <td>
                            <input type="text" class="noBorders" name="ft4" id="ft4" value="<?php echo number_format($b['ft4'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L032',$b['ft4']); ?></td>
                        <td><?php echo $d['ft4']; ?></td>
                        <td><?php echo $e['ft4']; ?></td>
                        <td><?php echo $f['ft4']; ?></td>
                        <td><?php echo $o->getAttribute('L032',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <tr>
                        <td>T3</td>
                        <td>
                            <input type="text" class="noBorders" name="t3" id="t3" value="<?php echo number_format($b['t3'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L061',$b['t3']); ?></td>
                        <td><?php echo $d['t3']; ?></td>
                        <td><?php echo $e['t3']; ?></td>
                        <td><?php echo $f['t3']; ?></td>
                        <td><?php echo $o->getAttribute('L061',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <tr>
                        <td>T4</td>
                        <td>
                            <input type="text" class="noBorders" name="t4" id="t4" value="<?php echo number_format($b['t4'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L062',$b['t4']); ?></td>
                        <td><?php echo $d['t4']; ?></td>
                        <td><?php echo $e['t4']; ?></td>
                        <td><?php echo $f['t4']; ?></td>
                        <td><?php echo $o->getAttribute('L062',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L030',$order['serialno'])) { ?>
                        <tr>
                        <td>Triglycerides</td>
                        <td>
                            <input type="text" class="noBorders" name="triglycerides" id="triglycerides" value="<?php echo number_format($b['triglycerides'],2); ?>" pattern="^\d*(\.\d{0,2})?$" onchange="javascript: computeLDL();">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L030',$b['triglycerides']); ?></td>
                        <td><?php echo $d['triglycerides']; ?></td>
                        <td><?php echo $e['triglycerides']; ?></td>
                        <td><?php echo $f['triglycerides']; ?></td>
                        <td><?php echo $o->getAttribute('L030',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L052',$order['serialno']) || checkTest('L206',$order['serialno'])) { ?>
                    <tr>
                        <td colspan=7><b>LIPID PANEL&nbsp;:</b></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                    </tr>
                    <tr>
                        <td>Total Cholesterol</td>
                        <td>
                            <input type="text" class="noBorders" name="cholesterol" id="cholesterol" value="<?php echo number_format($b['cholesterol'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L018',$b['cholesterol']); ?></td>
                        <td><?php echo $d['cholesterol']; ?></td>
                        <td><?php echo $e['cholesterol']; ?></td>
                        <td><?php echo $f['cholesterol']; ?></td>
                        <td><?php echo $o->getAttribute('L018',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <tr>
                        <td>HDL - Chol&nbsp;</td>
                        <td>
                            <input type="text" class="noBorders" name="hdl" id="hdl" value="<?php echo number_format($b['hdl'],2); ?>" pattern="^\d*(\.\d{0,2})?$" onchange="javascript: computeLDL();">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L024',$b['hdl']); ?></td>
                        <td><?php echo $d['hdl']; ?></td>
                        <td><?php echo $e['hdl']; ?></td>
                        <td><?php echo $f['hdl']; ?></td>
                        <td><?php echo $o->getAttribute('L024',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <tr>
                        <td>Triglycerides</td>
                        <td>
                            <input type="text" class="noBorders" name="triglycerides" id="triglycerides" value="<?php echo number_format($b['triglycerides'],2); ?>" pattern="^\d*(\.\d{0,2})?$" onchange="javascript: computeLDL();">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L030',$b['triglycerides']); ?></td>
                        <td><?php echo $d['triglycerides']; ?></td>
                        <td><?php echo $e['triglycerides']; ?></td>
                        <td><?php echo $f['triglycerides']; ?></td>
                        <td><?php echo $o->getAttribute('L030',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <tr>
                        <td>LDL - Chol</td>
                        <td>
                            <!-- <input type="text" class="noBorders" name="ldl" id="ldl" value="<?php echo number_format($b['ldl'],2); ?>" pattern="^\d*(\.\d{0,2})?$" onchange="javascript: computeLDL();"> -->
                            <input type="text" class="noBorders" name="ldl" id="ldl" value="<?php echo number_format($b['ldl'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L080',$b['ldl']); ?></td>
                        <td><?php echo $d['ldl']; ?></td>
                        <td><?php echo $e['ldl']; ?></td>
                        <td><?php echo $f['ldl']; ?></td>
                        <td><?php echo $o->getAttribute('L080',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <tr>
                        <td>VLDL</td>
                        <td>
                            <!-- <input type="text" class="noBorders" name="vldl" id="vldl" value="<?php echo number_format($b['vldl'],2); ?>" pattern="^\d*(\.\d{0,2})?$" onchange="javascript: computeVLDL();"> -->
                            <input type="text" class="noBorders" name="vldl" id="vldl" value="<?php echo number_format($b['vldl'],2); ?>" pattern="^\d*(\.\d{0,2})?$" >
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L081',$b['vldl']); ?></td>
                        <td><?php echo $d['vldl']; ?></td>
                        <td><?php echo $e['vldl']; ?></td>
                        <td><?php echo $f['vldl']; ?></td>
                        <td><?php echo $o->getAttribute('L081',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L119',$order['serialno'])) { ?>
                    <tr>
                        <td>Ionized Calcium</td>
                        <td>
                            <input type="text" class="noBorders" name="ion_calcium" id="ion_calcium" value="<?php echo number_format($b['ion_calcium'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L119',$b['ion_calcium']); ?></td>
                        <td><?php echo $d['ion_calcium']; ?></td>
                        <td><?php echo $e['ion_calcium']; ?></td>
                        <td><?php echo $f['ion_calcium']; ?></td>
                        <td><?php echo $o->getAttribute('L119',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L019',$order['serialno'])) { ?>
                    <tr>
                        <td>HBA1C (HEMOGLOBIN A1C)</td>
                        <td>
                            <input type="text" class="noBorders" name="hba1c" id="hba1c" value="<?php echo number_format($b['hba1c'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L019',$b['hba1c']); ?></td>
                        <td><?php echo $d['hba1c']; ?></td>
                        <td><?php echo $e['hba1c']; ?></td>
                        <td><?php echo $f['hba1c']; ?></td>
                        <td><?php echo $o->getAttribute('L019',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L092',$order['serialno'])) { ?>
                    <tr>
                        <td>Alkaline Phosphatase</td>
                        <td>
                            <input type="text" class="noBorders" name="alkaline" id="alkaline" value="<?php echo number_format($b['alkaline'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L092',$b['alkaline']); ?></td>
                        <td><?php echo $d['alkaline']; ?></td>
                        <td><?php echo $e['alkaline']; ?></td>
                        <td><?php echo $f['alkaline']; ?></td>
                        <td><?php echo $o->getAttribute('L092',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } if(checkTest('L196',$order['serialno'])) { ?>
                    <tr>
                        <td colspan=7><b>ELECTROLYTES&nbsp;:</b></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                    </tr>
                    <tr>
                        <td>Sodium (Na)</td>
                        <td>
                            <input type="text" class="noBorders" name="electrolytes_na" id="electrolytes_na" value="<?php echo number_format($b['electrolytes_na'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L026',$b['electrolytes_na']); ?></td>
                        <td><?php echo $d['electrolytes_na']; ?></td>
                        <td><?php echo $e['electrolytes_na']; ?></td>
                        <td><?php echo $f['electrolytes_na']; ?></td>
                        <td><?php echo $o->getAttribute('L026',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <tr>
                        <td>Potassium (K)&nbsp;</td>
                        <td>
                            <input type="text" class="noBorders" name="electrolytes_k" id="electrolytes_k" value="<?php echo number_format($b['electrolytes_k'],2); ?>" pattern="^\d*(\.\d{0,2})?$" onchange="javascript: computeLDL();">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L025',$b['electrolytes_k']); ?></td>
                        <td><?php echo $d['electrolytes_k']; ?></td>
                        <td><?php echo $e['electrolytes_k']; ?></td>
                        <td><?php echo $f['electrolytes_k']; ?></td>
                        <td><?php echo $o->getAttribute('L025',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <tr>
                        <td>Chloride (CI)</td>
                        <td>
                            <input type="text" class="noBorders" name="electrolytes_ci" id="electrolytes_ci" value="<?php echo number_format($b['electrolytes_ci'],2); ?>" pattern="^\d*(\.\d{0,2})?$" onchange="javascript: computeLDL();">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L028',$b['electrolytes_ci']); ?></td>
                        <td><?php echo $d['electrolytes_ci']; ?></td>
                        <td><?php echo $e['electrolytes_ci']; ?></td>
                        <td><?php echo $f['electrolytes_ci']; ?></td>
                        <td><?php echo $o->getAttribute('L028',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <tr>
                        <td>Ionized Calcium</td>
                        <td>
                            <input type="text" class="noBorders" name="ion_calcium" id="ion_calcium" value="<?php echo number_format($b['ion_calcium'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L118',$b['ion_calcium']); ?></td>
                        <td><?php echo $d['ion_calcium']; ?></td>
                        <td><?php echo $e['ion_calcium']; ?></td>
                        <td><?php echo $f['ion_calcium']; ?></td>
                        <td><?php echo $o->getAttribute('L118',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <tr>
                        <td>Total Calcium</td>
                        <td>
                            <input type="text" class="noBorders" name="total_calcium" id="total_calcium" value="<?php echo number_format($b['total_calcium'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L029',$b['total_calcium']); ?></td>
                        <td><?php echo $d['total_calcium']; ?></td>
                        <td><?php echo $e['total_calcium']; ?></td>
                        <td><?php echo $f['total_calcium']; ?></td>
                        <td><?php echo $o->getAttribute('L029',$a['age'],$a['gender']); ?></td>	
                    </tr>
                    <?php } ?>
                    <tr>
                        <td>Remarks&nbsp;:</td>
                        <td colspan=6>
                            <textarea name="remarks" id="remarks" style="width: 99%;" rows=3><?php echo $b['remarks']; ?></textarea>
                        </td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                    </tr>
                    </tbody>                   
                </table>
            </td>
        </tr>
    </table>              
</form>

<div id="printConsolidation" name="printConsolidation" style="display: none;">
	<p style="margin-left: 20px; text-align: justify;" id="message">It appears that the selected result belongs to one consolidated result sheet. You may select from the given list w/c result you wish to print.</span></p><br/>
	<form name="otherTests" id="otherTests">

	</form>
</div>

</body>
</html>