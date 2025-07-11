<?php 
	
	session_start();
	require_once "handlers/_generics.php";
	
    $o = new _init;

    $a = $o->getArray("SELECT record_id AS id, LPAD(a.so_no,6,0) AS myso,DATE_FORMAT(b.so_date,'%m/%d/%Y') AS sodate, LPAD(b.patient_id,6,0) AS mypid,b.patient_name AS pname, FLOOR(DATEDIFF(b.so_date,c.birthdate)/364.25) AS age,IF(c.gender='M','Male','Female') AS gender, DATE_FORMAT(c.birthdate,'%m/%d/%Y') AS bday, e.patientstatus, b.physician, a.code, a.procedure, sampletype, serialno, DATE_FORMAT(extractdate,'%m/%d/%Y') AS exday,TIME_FORMAT(extractime,'%h:%i %p') AS etime,extractby, a.location, a.lotno FROM lab_samples a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_patientstat e ON b.patient_stat = e.id WHERE a.record_id = '$_REQUEST[lid]';");
    list($impression,$rdate,$physician,$consultant,$verified,$resulttype) = $o->getArray("select impression,date_format(result_date,'%m/%d/%Y'),physician,consultant,verified, result_type from lab_descriptive where so_no = '$a[myso]' and branch = '$_SESSION[branchid]' and code = '$a[code]' and serialno = '$a[serialno]';");


    list($withfile) = $o->getArray("select with_file from lab_samples where so_no = '$a[myso]' and branch = '$_SESSION[branchid]' and code = '$a[code]' and serialno = '$a[serialno]';");

    if(isset($_REQUEST['tid']) && $_REQUEST['tid'] != '') {
        list($impression) = $o->getArray("select template from xray_templates where id = '$_GET[tid]';");
        $consultant = $_REQUEST['consultant'];
    }


?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Prime Care Cebu, Inc.</title>
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="ui-assets/texteditor/jquery-te-1.4.0.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
    <script language="javascript" src="ui-assets/texteditor/jquery-te-1.4.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
	
	
    <script>
        $(function() { 
            $("#desc_date").datepicker();
            $("#desc_impression").jqte();

            var myTable = $('#itemlist').DataTable({
                "keys": true,
                "scrollY":  "210px",
                "select":	'single',
                "pagingType": "full_numbers",
                "bProcessing": true,
                "responsive": true,
                "scroller": true,
                "aoColumnDefs": [
                    { "className": "dt-body-center", "targets": [2]},
                    { "targets": [0], "visible": false }
                ]
            });

            $('#itemlist tbody').on('dblclick', 'tr', function () {
                var data = myTable.row( this ).data();	
                document.loadTemplate.tid.value = data[0];
                document.loadTemplate.submit();
            });

            <?php 
                if($verified == 'Y') {
				    echo "$(\"#frmDescResult :input:not([name=setPrint], [name=setClose], [name=setUnpublish])\").prop('disabled',true);";
			    } 
            ?>

        });

        function showTemplates() {

           var dis =  $('#resultTemplates').dialog({
                title: "Result Templates",
                width: 800,
                height: 400,
                resizeable: false,
                modal: true,
                buttons: [
                    {
                        text: "Use Selected Template",
                        icons: { primary: "ui-icon-check" },
                        click: function() { 
                            var table = $("#itemlist").DataTable();
                            var tid;
                            $.each(table.rows('.selected').data(), function() {
                                tid = this[0];
                            });
                            
                            if(!tid) {
                                parent.sendErrorMessage("You have not selected any template yet!");
                            } else {
                                document.loadTemplate.tid.value = tid;
                                document.loadTemplate.consultant.value = $("#desc_consultant").val();
                                document.loadTemplate.submit();
                            }


                        }
                    }

                ]	

            });
        }

        function saveResult() {
            if(confirm("Are you sure you want save this data?") == true) {
                var dataString = $('#frmDescResult').serialize();
                    dataString = "mod=saveDescResult&" + dataString;
                $.ajax({
                    type: "POST",
                    url: "src/sjerp.php",
                    data: dataString,
                    success: function() {
                        alert("Result Successfully Saved!");
                        dis.dialog("close");
                        $("#frmDescResult").trigger("reset");
                    }
                });
            }   
        }

        function printResult() {
            var so_no = $('#desc_sono').val();
            var code = $('#desc_code').val();
            var serialno = $('#desc_serialno').val();
             
            parent.printDescriptiveResult(so_no,code,serialno);

        }

        function publishResult() {
            if(confirm("Are you sure you want to publish this result?") == true) {
                $.post("src/sjerp.php", { mod: "validateDescResult", desc_sono: $("#desc_sono").val(), desc_code: $("#desc_code").val(), desc_serialno: $("#desc_serialno").val(), sid: Math.random() }, function() {
                    alert("Result Successfully Published & Validated!")
                    document.loadTemplate.submit();
                });
            }
        }

        function unpublishResult() {
            if(confirm("Are you sure you want to unpublish this result?") == true) {
                $.post("src/sjerp.php", { mod: "invalidateDescResult", desc_sono: $("#desc_sono").val(), desc_code: $("#desc_code").val(), desc_serialno: $("#desc_serialno").val(), sid: Math.random() }, function() {
                    alert("Result Successfully unpublished!")
                    window.location.reload();
                });
            }

        }

        function checkXray(code) {
            var with_file;
            $.post("src/sjerp.php",{ mod: "checkXrayAttachment", so_no: $("#desc_sono").val(), pid: $("#desc_pid").val(), code: code, sid: Math.random() }, function(data) {
                if(data['lid']) {
                    if(data['with_file'] == 'Y'){
                        parent.viewXrayAttachment(code,$("#desc_code").val(),data['serialno'],data['lid'],data['with_file']);
                    }
                }
            },"json");
        }
    </script>
    <style>
		.dataTables_wrapper {
			/* display: inline-block; */
			font-size: 11px;
			width: 100%; 
		}
		
		table.dataTable tr.odd { background-color: #f5f5f5;  }
		table.dataTable tr.even { background-color: white; }
		.dataTables_filter input { width: 250px; }
	</style>
</head>
<body>

<form name="frmDescResult" id="frmDescResult"> 
    <table width=100% cellpadding=0 cellspacing=0 valign=top>
        <tr>
            <td width=35% valign=top>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>PATIENT & ORDER INFORMATION</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">SO No.&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="desc_sono" id="desc_sono" value="<?php echo $a['myso']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">SO Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="desc_sodate" id="desc_sodate" value="<?php echo $a['sodate']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient ID&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="desc_pid" id="desc_pid" value="<?php echo $a['mypid']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Result Date&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="desc_date" id="desc_date" value="<?php if($rdate !='') { echo $rdate; } else { echo $a['exday']; } ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Name&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="desc_pname" id="desc_pname" value="<?php echo $a['pname']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>

                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Gender&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="desc_gender" id="desc_gender" value="<?php echo $a['gender']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Birthdate&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="desc_birthdate" id="desc_birthdate" value="<?php echo $a['bday']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Patient Status&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="desc_patientstat" id="desc_patientstat" value="<?php echo $a['patientstatus']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Physician&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="desc_physician" id="desc_physician" value="<?php if($a['physician'] != '') { echo $a['physician']; } else { echo $physician; } ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                </table>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>SAMPLE DETAILS</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="desc_procedure" id="desc_procedure" value="<?php echo $a['procedure']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Procedure Code&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="desc_code" id="desc_code" value="<?php echo $a['code']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Specimen Type&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="desc_spectype" id="desc_spectype">
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
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Sample Serial No.&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="desc_serialno" id="desc_serialno" value="<?php echo $a['serialno']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">X-Ray No.&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="desc_xray_no" id="desc_xray_no" value="<?php echo $a['lotno']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Date Conducted&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="desc_extractdate" id="desc_extractdate" value="<?php echo $a['exday']; ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Time Extracted&nbsp;:</td>
                        <td align=left>
                
                            <input type="text" class="gridInput" style="width:100%;" name="desc_extracttime" id="desc_extracttime" value="<?php echo $a['etime']; ?>" readonly>

                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Conducted By&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="desc_extractby" id="desc_extractby" value="<?php echo $a['extractby']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Site Conducted&nbsp;:</td>
                        <td align=left>
                            <select class="gridInput" style="width:100%;" name="desc_location" id="desc_location">
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
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Consultant&nbsp;:</td>
                        <td align=left>
                           <select name="desc_consultant" id="desc_consultant" class="gridInput" style="width: 100%; font-size: 11px;">
                                <?php
                                    $query = $o->dbquery("SELECT id, fullname FROM options_doctors WHERE specialization like '%radiologist%' and id >= '7' ORDER BY fullname desc;");
                                    while($d = $query->fetch_array()) {
                                        echo "<option value='$d[0]' ";
                                        if($consultant == $d[0]) { echo "selected"; }
                                        echo ">$d[1]</option>";
                                    }
                                ?>
                            </select>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Result Summary&nbsp;:</td>
                        <td align=left>
                           <select name="desc_resultstat" id="desc_resultstat" class="gridInput" style="width: 100%; font-size: 11px;">
                               <option value='1' <?php if($resulttype == 1) { echo "selected"; } ?>>Normal</option>
                               <option value='2' <?php if($resulttype == 2) { echo "selected"; } ?>>With Findings</option>
                            </select>
                        </td>				
                    </tr>
                </table>   
            </td>
            <td width=65% valign=top>
                
                <textarea name="desc_impression" id="desc_impression" style="width:100%;" ><?php echo html_entity_decode($impression); ?></textarea><br/>
    
            </td>
        </tr>
    </table>
    <table width=100% style="margin-top: 2px; border-top: 1px solid #e9e9e9;">
        <td align=right style="padding-top: 5px;">
            <?php if($withfile == 'Y') { ?>
                <button type=button class="ui-button ui-widget ui-corner-all" onClick="checkXray('X001');">
                    <span class="ui-icon ui-icon-copy"></span> View Attachment
                </button>
            <?php } ?>
            <?php if($verified != 'Y') { ?>
                <button type=button class="ui-button ui-widget ui-corner-all" onClick="showTemplates();">
                    <span class="ui-icon ui-icon-copy"></span> Load Available Result Template
                </button>
                <button type=button class="ui-button ui-widget ui-corner-all" onClick="saveResult();">
                    <span class="ui-icon ui-icon-disk"></span> Save Changes Made
                </button>
                <button type=button class="ui-button ui-widget ui-corner-all" onClick="publishResult();">
                    <span class="ui-icon ui-icon-check"></span> Publish Result
                </button>
            <?php } else { ?>
                <button type=button class="ui-button ui-widget ui-corner-all" name="setUnpublish" id="setUnpublish" onClick="unpublishResult();">
                    <span class="ui-icon ui-icon-cancel"></span> Unpublish Result
                </button>
            <?php } ?>
            <button type=button name="setPrint" id="setPrint" class="ui-button ui-widget ui-corner-all" onClick="printResult();">
                <span class="ui-icon ui-icon-print"></span> Print Result
            </button>
            <button type=button name="setClose" id="setClose" class="ui-button ui-widget ui-corner-all" onClick="parent.closeDialog('#descResult');">
                <span class="ui-icon ui-icon-closeThick"></span> Close
            </button>                      
        </td>
    </table>
</form>
<form name="loadTemplate" id="loadTemplate" action="result.descriptive.php" method="_GET">
    <input type="hidden" name="lid" id="lid" value="<?php echo $_REQUEST['lid']; ?>">
    <input type="hidden" name="tid" id="tid" value="">
    <input type="hidden" name="consultant" id="consultant">
</form>
<div name="resultTemplates" id="resultTemplates" style="display: none;">
    <table width=100% id="itemlist" style="font-size:11px;">
        <thead>
            <tr>
                <th></th>
                <th width=65>Template Title</th>
                <th width=20%>Type</th>
                <th width=25%>Radiologist</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $i = $o->dbquery("SELECT a.id, title, if(xray_type=1,'Upper Extremities','Lower Extremities') as xray_type, b.fullname FROM xray_templates a left join options_doctors b on a.template_owner = b.id WHERE a.`status` != 'Inactive' ORDER BY b.fullname, title;");
                while($tdetails = $i->fetch_array()) {
                    echo "<tr>
                        <td>$tdetails[0]</td>
                        <td>$tdetails[1]</td>
                        <td>$tdetails[2]</td>
                        <td>$tdetails[3]</td>
                    </tr>";
                }
           ?>
        </tbody>
    </table>                           
</div>

</body>
</html>