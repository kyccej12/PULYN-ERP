<?php 
	//ini_set("display_errors","on");
	session_start();
	require_once "handlers/_generics.php";
	
    $o = new _init;

    $order = $o->getArray("select *, serialno, date_format(extractdate,'%m/%d/%Y') as exdate from lab_samples where record_id = '$_REQUEST[lid]';");
    $a = $o->getArray("SELECT record_id AS id, LPAD(a.so_no,6,0) AS myso,DATE_FORMAT(b.so_date,'%m/%d/%Y') AS sodate, LPAD(b.patient_id,6,0) AS mypid,b.patient_name AS pname, YEAR(b.so_date) - YEAR(c.birthdate) AS age, c.birthdate, IF(c.gender='M','Male','Female') AS gender, gender as xgender, DATE_FORMAT(c.birthdate,'%m/%d/%Y') AS bday,e.patientstatus,b.physician,a.code,a.procedure,a.sampletype,a.serialno,DATE_FORMAT(extractdate,'%m/%d/%Y') AS exday,TIME_FORMAT(extractime,'%h:%i %p') AS etime,extractby,a.location FROM lab_samples a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_patientstat e ON b.patient_stat = e.id WHERE b.so_no = '$order[so_no]';");
    $b = $o->getArray("select * from lab_spchem where so_no = '$a[myso]' and serialno = '$order[serialno]' and branch = '$_SESSION[branchid]';");

    // if(count($b) == 0) {
    //     $b = $o->getArray("select * from lab_spchem_temp where serialno = '$order[serialno]' order by parsed_on desc limit 1;");
    // }

    $o->calculateAge($a['sodate'],$a['birthdate']);

    list($testCount) = $o->getArray("select count(*) from lab_samples where serialno = '$order[serialno]';");
    if($testCount > 1) {
        $procedure = '';
        $code ='';
        $testQuery = $o->dbquery("select `procedure`, code from lab_samples where serialno = '$order[serialno]';");
        while($testRow = $testQuery->fetch_array()) {
            $procedure .= $testRow['procedure'] . ",";
            $code .= $testRow['code'] . ",";
        }
        $procedure = substr($procedure,0,-1);
    } else { $procedure = $order['procedure']; $code = $order['code']; }


    function checkTest($code,$serialno) {
        global $o;

        list($isTested) = $o->getArray("select count(*) from lab_samples where `code` = '$code' and serialno = '$serialno';");
        if($isTested > 0 ) { return true; } else { return false; }

    }

     /* Previous Results */
     $d = $o->getArray("select *, concat('<br/>',date_format(result_date,'%m/%d/%Y')) as rdate from lab_spchem where so_no = '$a[myso]' and result_date < '$b[result_date]' limit 1,1;");
     $e = $o->getArray("select *, concat('<br/>',date_format(result_date,'%m/%d/%Y')) as rdate from lab_spchem where so_no = '$a[myso]' and result_date < '$b[result_date]' limit 2,1;");
     $f = $o->getArray("select *, concat('<br/>',date_format(result_date,'%m/%d/%Y')) as rdate from lab_spchem where so_no = '$a[myso]' and result_date < '$b[result_date]' limit 3,1;");
 
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

        $("#spchem_date").datepicker();
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
<body>
    <form name="frmspchemResult" id="frmspchemResult"> 
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
         <tr>
             <td width=35% valign=top>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">SO #&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="spchem_sono" id="spchem_sono" value="<?php echo $a['myso']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Service Order Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="spchem_sodate" id="spchem_sodate" value="<?php echo $a['sodate']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient ID&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="spchem_pid" id="spchem_pid" value="<?php echo $a['mypid']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-left: 15px;">Result Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="spchem_date" id="spchem_date" value="<?php if($rdate !='') { echo $rdate; } else { echo date('m/d/Y'); } ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient Name&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="spchem_pname" id="spchem_pname" value="<?php echo $a['pname']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>

                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Gender&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="spchem_gender" id="spchem_gender" value="<?php echo $a['gender']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Birthdate&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="spchem_birthdate" id="spchem_birthdate" value="<?php echo $a['bday']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Age&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="spchem_age" id="spchem_age" value="<?php echo $a['age']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Patient Status&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="spchem_patientstat" id="spchem_patientstat" value="<?php echo $a['patientstatus']; ?>" readonly> 
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Requesting Physician&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="spchem_physician" id="spchem_physician" value="<?php echo $a['physician']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                </table>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Test or Procedure&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="spchem_procedure" id="spchem_procedure" value="<?php echo $procedure ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Procedure Code&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="spchem_code" id="spchem_code" value="<?php echo $code; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Specimen Type&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="spchem_spectype" id="spchem_spectype">
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
                            <input type="text" class="gridInput" style="width:100%;" name="spchem_serialno" id="spchem_serialno" value="<?php echo $order['serialno']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Date Extracted&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="spchem_extractdate" id="spchem_extractdate" value="<?php echo $a['exday']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Time Extracted&nbsp;:</td>
                        <td align=left>
                
                            <input type="text" class="gridInput" style="width:100%;" name="spchem_extracttime" id="spchem_extracttime" value="<?php echo $a['etime']; ?>" readonly>

                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Extracted By&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="spchem_extractby" id="spchem_extractby" value="<?php echo $a['extractby']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-left: 15px;">Phleb/Imaging Site&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="spchem_location" id="spchem_location">
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
                    <?php if(checkTest('L019',$order['serialno'])) { ?>
                    <tr>
                        <td>HBA1C (HEMOGLOBIN A1C)</td>
                        <td>
                            <input type="text" class="noBorders" name="spchem_hba1c" id="spchem_hba1c" value="<?php echo number_format($b['hba1c'],2); ?>" pattern="^\d*(\.\d{0,2})?$">
                        </td>
                        <td><?php echo $o->checkChemValues($a['age'],$a['gender'],'L019',$b['hba1c']); ?></td>
                        <td><?php echo $d['hba1c']; ?></td>
                        <td><?php echo $e['hba1c']; ?></td>
                        <td><?php echo $f['hba1c']; ?></td>
                        <td><?php echo $o->getAttribute('L019',$a['age'],$a['gender']); ?></td>	
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