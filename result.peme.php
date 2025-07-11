<?php 
	
	session_start();
	require_once "handlers/_generics.php";
	
    $o = new _init;

    //$a = $o->getArray("SELECT record_id AS id, LPAD(a.so_no,6,0) AS myso,DATE_FORMAT(a.so_date,'%m/%d/%Y') AS sodate, a.so_date, b.birthdate, LPAD(a.pid,6,0) AS mypid,CONCAT(b.lname,', ',b.fname,' ',b.mname) AS pname,IF(b.gender='M','Male','Female') AS gender, DATE_FORMAT(b.birthdate,'%m/%d/%Y') AS bday,a.code,a.procedure,sampletype,serialno,DATE_FORMAT(extractdate,'%m/%d/%Y') AS exday,TIME_FORMAT(extractime,'%h:%i %p') AS etime,extractby,a.location FROM lab_samples a  LEFT JOIN patient_info b ON a.pid = b.patient_id WHERE a.record_id = '$_REQUEST[lid]';");
   
    $a = $o->getArray("select * from patient_info where patient_id = '$_REQUEST[pid]';");
    $b = $o->getArray("select * from peme where so_no = '$_REQUEST[so_no]' and pid = '$_REQUEST[pid]';");
    $c = $o->getArray("SELECT pid,examined_by,DATE_FORMAT(examined_on,'%d/%m/%Y') AS examin_d8,TIME_FORMAT(examined_on,'%h:%m:%s') AS examin_tym,evaluated_by,DATE_FORMAT(evaluated_on,'%d/%m/%Y') AS eval_d8, TIME_FORMAT(evaluated_on,'%h:%m:%s') AS eval_tym FROM peme WHERE so_no = '$_REQUEST[so_no]' and pid = '$_REQUEST[pid]';");
    //$d = $o->getArray("SELECT LPAD(prio,6,0) AS prio, LPAD(so_no,6,0) AS so, DATE_FORMAT(so_date,'%m/%d/%Y') AS sodate, `code`, `procedure`, CONCAT(b.lname,', ',b.fname,', ',b.mname) AS pname, b.gender, DATE_FORMAT(b.birthdate,'%m/%d/%Y') AS bday,  FLOOR(DATEDIFF(so_date,b.birthdate)/364.25) AS age, compname, a.status, a.so_date,prio AS `priority`,b.birthdate, a.pid, CONCAT(c.fullname,', ',c.prefix) AS ex_by, CONCAT(d.fullname,', ',d.prefix) AS ev_by FROM peme a LEFT JOIN patient_info b ON a.pid = b.patient_id LEFT JOIN options_doctors c ON a.examined_by = c.id LEFT JOIN options_doctors d ON a.evaluated_by = d.id where so_no = '$_REQUEST[so_no]' and pid = '$_REQUEST[pid]';");
    $d = $o->getArray("SELECT LPAD(prio,6,0) AS prio, LPAD(e.so_no,6,0) AS so, DATE_FORMAT(a.so_date,'%m/%d/%Y') AS sodate, `code`, `procedure`, CONCAT(b.lname,', ',b.fname,', ',b.mname) AS pname, b.gender, DATE_FORMAT(b.birthdate,'%m/%d/%Y') AS bday,  FLOOR(DATEDIFF(a.so_date,b.birthdate)/364.25) AS age, compname, e.customer_name, e.customer_address, a.status, a.so_date,prio AS `priority`,b.birthdate, a.pid, CONCAT(c.fullname,', ',c.prefix) AS ex_by, CONCAT(d.fullname,', ',d.prefix) AS ev_by FROM peme a LEFT JOIN patient_info b ON a.pid = b.patient_id LEFT JOIN options_doctors c ON a.examined_by = c.id LEFT JOIN options_doctors d ON a.evaluated_by = d.id LEFT JOIN so_header e ON a.so_no = e.so_no AND a.pid = e.patient_id WHERE a.so_no = '$_REQUEST[so_no]' and a.pid = '$_REQUEST[pid]';");
    $hbsagRes = $o->getArray("SELECT * FROM lab_enumresult WHERE so_no='$_REQUEST[so_no]' AND CODE in ('L042','L051');");
    $hepaRes = $o->getArray("SELECT * FROM lab_enumresult WHERE so_no='$_REQUEST[so_no]' AND  code = 'L050';");
    $ptRes = $o->getArray("SELECT * FROM lab_enumresult WHERE so_no='$_REQUEST[so_no]' AND code = 'L037';");
    $btRes = $o->getArray("SELECT * FROM lab_bloodtyping WHERE so_no='$_REQUEST[so_no]' AND code= 'L040';");
    $antigenRes = $o->getArray("SELECT * FROM lab_antigenresult WHERE so_no='$_REQUEST[so_no]' AND code= 'L087';");


    $pmh = explode(",",$b['pm_history']);

    list($brgy) = $o->getArray("SELECT brgyDesc FROM options_brgy WHERE brgyCode = '$a[brgy]';");
    list($ct) = $o->getArray("SELECT citymunDesc FROM options_cities WHERE cityMunCode = '$a[city]';");
    list($prov) = $o->getArray("SELECT provDesc FROM options_provinces WHERE provCode = '$a[province]';");

    if($a['street'] != '') { $myaddress.=$a['street'].", "; }
    if($brgy != "") { $myaddress .= $brgy.", "; }
    if($ct != "") { $myaddress .= $ct.", "; }
    if($prov != "")  { $myaddress .= $prov.", "; }
    $myaddress = substr($myaddress,0,-2);

    list($cstat) = $o->getArray("select civil_status from options_civilstatus where csid = '$a[cstat]';");

    if($c['examined_by'] != '') {
        list($docfullname,$docprefix,$doclicenseno) = $o->getArray("SELECT concat(fullname,',') as fullname, concat(prefix, ' &raquo;') as prefix, license_no FROM options_doctors WHERE id = '$c[examined_by]';");
    }

	if($c['evaluated_by'] != '') {
        list($doctorfullname,$doctorprefix,$doctorlicenseno) = $o->getArray("SELECT concat(fullname,',') as fullname, concat(prefix, ' &raquo;') as prefix, license_no FROM options_doctors WHERE id = '$c[evaluated_by]';");
    }

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Pulyn Dialysis & Diagnostics Medical Center</title>
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/texteditor/jquery-te-1.4.0.css" rel="stylesheet" type="text/css" />
	<link href="style/style-mobile.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="ui-assets/texteditor/jquery-te-1.4.0.min.js"></script>
	<script language="javascript" src="js/main.js?sid=<?php echo uniqid(); ?>"></script>
    <script>
        $(function() { 
            $("#cbc_date").datepicker();
            $("#pe_date").datepicker(); 

            var peResultCollection = [
            "YES",
            "NO",
            "OCCASIONAL",
            ];

            var pePregnantCollection = [
            "YES",
            "NO",
            ];

            var peBloodtyping = [
            "A+",
            "A-",
            "B+",
            "B-",
            "AB+",
            "AB-",
            "O+",
            "O-",
            ];

            var peHbsag = [
            "NON-REACTIVE",
            "REACTIVE",
            ];

            var pePregnancy = [
            "POSITIVE",
            "NEGATIVE",
            ];

            var peIshihara = [
            "Adequate",
            "Inadequate",
            ];

            $("#pe_ishihara").autocomplete({
                source: peIshihara, minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });

            $("#pe_hbsag_normal").autocomplete({
                source: peHbsag, minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });

            $("#pe_pt_normal,#pe_hepa_normal").autocomplete({
                source: pePregnancy, minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });


            $("#pe_bt_normal").autocomplete({
                source: peBloodtyping, minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });

            $("#pe_pregnant").autocomplete({
                source: pePregnantCollection, minLength: 0
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });


            $("#pe_smoker,#pe_alcoholic,#pe_drugs").autocomplete({
                source: peResultCollection, minLength: 0
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

        function calculateBMI() {
            var ht = parseFloat($("#pe_ht").val()) / 100;
            var wt = parseFloat($("#pe_wt").val());
            

            if(ht>0 && wt>0) {
                var bmi = wt / (ht*ht);
                    bmi = bmi.toFixed(2);
                $("#pe_bmi").val(bmi);
            }

        }

        function checkResult(code) {
            $.post("src/sjerp.php",{ mod: "checkPEResult", so_no: $("#pe_sono").val(), pid: $("#pe_pid").val(), code: code, sid: Math.random() }, function(data) {
                if(data['lid']) {
                    if(data['with_file'] == 'Y'){
                        parent.viewAttachment(code,$("#pe_sono").val(),data['serialno'],data['lid'],data['with_file']);
                    }else {
                        parent.printResult(code,$("#pe_sono").val(),data['serialno'],data['lid']);
                    }
                }
            },"json");
        }

        function checkXray(code) {
            var with_file;
            $.post("src/sjerp.php",{ mod: "checkXrayPEResult", so_no: $("#pe_sono").val(), pid: $("#pe_pid").val(), code: code, sid: Math.random() }, function(data) {
                if(data['lid']) {
                    if(data['with_file'] == 'Y'){
                        parent.viewAttachment(code,$("#pe_sono").val(),data['serialno'],data['lid'],data['with_file']);
                    }else {
                        parent.printResult(code,$("#pe_sono").val(),data['serialno'],data['lid']);
                    }
                }
            },"json");
        }

        function checkXray2(code) {
            var with_file;
            $.post("src/sjerp.php",{ mod: "checkXrayPEResult", so_no: $("#pe_sono").val(), pid: $("#pe_pid").val(), code: code, sid: Math.random() }, function(data) {
                if(data['lid']) {
                    if(data['with_file'] == 'Y'){
                        parent.viewAttachment(code,$("#pe_sono").val(),data['serialno'],data['lid'],data['with_file']);
                    }else {
                        parent.printResult(code,$("#pe_sono").val(),data['serialno'],data['lid']);
                    }
                }
            },"json");
        }

        function checkEcg(code) {
            $.post("src/sjerp.php",{ mod: "checkPEResult", so_no: $("#pe_sono").val(), pid: $("#pe_pid").val(), code: code, sid: Math.random() }, function(data) {
                if(data['lid']) {
                    parent.printResult(code,$("#pe_sono").val(),data['serialno'],data['lid']);
                }
            },"json");
        }

        function viewFile(code) {
            $.post("src/sjerp.php",{ mod: "checkPEResult", so_no: $("#pe_sono").val(), pid: $("#pe_pid").val(), code: code, sid: Math.random() }, function(data) {
                if(data['lid']) {
                    parent.viewAttachment(code,$("#pe_sono").val(),data['serialno'],data['lid']);
                }
            },"json");
        }

        function viewPDFAttachment(code) {
            $.post("src/sjerp.php",{ mod: "checkPEResult", so_no: $("#pe_sono").val(), pid: $("#pe_pid").val(), code: code, sid: Math.random() }, function(data) {
                if(data['lid']) {
                    parent.printResult(code,$("#pe_sono").val(),data['serialno'],data['lid']);
                }
            },"json");
        }

        function openAttachment(code) {
            $.post("src/sjerp.php",{ mod: "openAttachment", so_no: $("#pe_sono").val(), pid: $("#pe_pid").val(), code: code, sid: Math.random() }, function(data) {
                
                if(data.length > 0) {
                
                    $("#fileLocation").html(data);
                    var dis = $("#imageAttachment").dialog({
                    title: "Image File Attachment",
                    width: 740,
                    height: 640,
                    resizeable: false,
                    modal: true,
                    buttons: [
                            {
                                text: "Close",
                                icons: { primary: "ui-icon-closethick" },
                                click: function() { $(this).dialog("close"); }
                            }
                        ]
                    });
               
                 } else {
                    parent.sendErrorMessage("It appears that there is no file associated or attached to this test yet.."); 
                }

            },"html");
        }

        function marknormal() {
            $("#pe_sa_normal").val('Y');
            $("#pe_hs_normal").val('Y');
            $("#pe_mouth_normal").val('Y');
            $("#pe_neck_normal").val('Y');
            $("#pe_lungs_normal").val('Y');
            $("#pe_heart_normal").val('Y');
            $("#pe_check_normal").val('Y');
            $("#pe_abdomen_normal").val('Y');
            $("#pe_ref_normal").val('Y');
            $("#pe_extr_normal").val('Y');
            $("#pe_ee_normal").val('Y');
            $("#pe_nose_normal").val('Y');
            $("#pe_genitals_normal").val('Y');
            $("#pe_bpe_normal").val('Y');
            $("#pe_rect_normal").val('Y');

        }

        $( function() {
        $( "#frmPEMEPop" ).dialog({
        title: "Services Availed",
        modal: true,
        width: 400,
        buttons: {
            Ok: function() {
            $( this ).dialog( "close" );
            }
        }
        });
    } );

    </script>
     <style>
        .border {
            border-right: 1px solid black;
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }
        .borderless {
            border-left: 1px solid black;
            border-bottom: 1px solid black;
        }
        .border-no-right {
            border-left: 1px solid black;
            border-bottom: 1px solid black;
            border-top: 1px solid black;
        }
        a {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <form name="frmVitals" id="frmVitals">
        <input type="hidden" name="pe_sono" id="pe_sono" value="<?php echo $_REQUEST['so_no']; ?>">
        <input type="hidden" name="pe_pid" id="pe_pid" value="<?php echo $_REQUEST['pid']; ?>">
		<table width=100% cellpadding=0 cellspacing=0 style="boder-collpase: collapse;">
            <tr><td colspan=8 align=center><img src="images/doc-header.jpg" width=85% height=85% align=absmiddle /></td></tr>
			<tr>
				<td colspan=4 align=center class=bebottom>
					<input type="radio" id="pe_type" name="pe_type" value="APE">&nbsp;<span class="spadix-l">Annual Physical Examination</span>
				</td>
				<td colspan=4 align=center class=bebottom>
					<input type="radio" id="type" name="type" value="PE">&nbsp;<span class="spadix-l">Pre-Employment Requirements</span>
				</td>
			</tr>
			<tr><td height=4></td></tr>
			<tr>
				<td width=8% class="bebottom" >Last Name :</td>
				<td width=17% class="bebottom">	
					<input type="text" name="pe_lname" id="pe_lname" style="border: none; font-size: 11px; font-weight: bold;" value="<?php echo $a['lname']; ?>">
				</td>
				<td width=8% class="bebottom">First Name :</td>
				<td width=17% class="bebottom">	
					<input type="text" name="pe_fname" id="pe_fname" style="border: none; font-size: 11px; font-weight: bold;" value="<?php echo $a['fname']; ?>">
				</td>
				<td width=8% class="bebottom">Middle Name :</td>
				<td width=17% class="bebottom">	
					<input type="text" name="pe_mname" id="pe_mname" style="border: none; font-size: 11px; font-weight: bold;" value="<?php echo $a['mname']; ?>">
				</td>
				<td width=8% class="bebottom">Date :</td>
				<td width=17% class="bebottom">	
					<input type="text" name="pe_date" id="pe_date" style="border: none; font-size: 11px; font-weight: bold;" value="<?php echo date('m/d/Y'); ?>">
				</td>
			</tr>
            <tr>
				<td class="bebottom" >Address :</td>
				<td class="bebottom">	
					<input type="text" name="pe_address" id="pe_address" style="border: none; font-size: 11px;width: 98%; font-weight: bold;" value="<?php echo $myaddress; ?>">
				</td>
				<td class="bebottom">Age :</td>
				<td class="bebottom">	
                <input type="text" name="pe_age" id="pe_age" style="border: none; font-size: 11px;width: 98%; font-weight: bold;" value="<?php echo $d['age']; ?>">
				</td>
				<td class="bebottom">Civil Status :</td>
				<td class="bebottom">	
					<input type="text" name="pe_cstatus" id="pe_cstatus" style="border: none; font-size: 11px; width: 98%; font-weight: bold;" value="<?php echo $cstat; ?>">
				</td>
				<td class="bebottom">Gender :</td>
				<td class="bebottom">	
					<input type="text" name="pe_gender" id="pe_gender" style="border: none; font-size: 11px; width: 98%; font-weight: bold;" value="<?php echo $a['gender']; ?>">
				</td>
			</tr>
            <tr>
				<td class="bebottom" >Place of Birth :</td>
				<td class="bebottom">	
					<input type="text" name="pe_pob" id="pe_pob" style="border: none; font-size: 11px; width: 98%; font-weight: bold;" value="<?php echo $a['birthplace']; ?>">
				</td>
				<td class="bebottom">Date of Birth :</td>
				<td class="bebottom">	
					<input type="text" name="pe_dob" id="pe_dob" style="border: none; font-size: 11px; width: 98%; font-weight: bold;" value="<?php echo $a['birthdate']; ?>">
				</td>
				<td class="bebottom"></td>
				<td class="bebottom" colspan=3>	
					<!--input type="text" name="pe_insurance" id="pe_insurance" style="border: none; font-size: 11px; width: 98%; font-weight: bold;"-->
				</td>
			</tr>
            <tr>
				<td class="bebottom" >Occupation :</td>
				<td class="bebottom">	
					<input type="text" name="pe_occ" id="pe_occ" style="border: none; font-size: 11px; width: 98%; font-weight: bold;" value="<?php echo $a['occupation']; ?>">
				</td>
				<td class="bebottom">Company :</td>
				<td class="bebottom">	
					<input type="text" name="pe_comp" id="pe_comp" style="border: none; font-size: 11px; width: 98%; font-weight: bold;" value="<?php echo $d['customer_name']; ?>">
				</td>
				<td class="bebottom">Tel/Mobile # :</td>
				<td class="bebottom" colspan=3>	
					<input type="text" name="pe_contact" id="pe_contact" style="border: none; font-size: 11px; width: 98%; font-weight: bold;" value="<?php echo $a['mobile_no']; ?>">
				</td>
			</tr>
            <tr><td style="padding-top:10px;"></td></tr>
		</table>
        <table width=100% cellpadding=5><tr><td align=center><span style="font-size: 10pt; font-weight: bold;">PHYSICAL EXAMINATION</span></td></tr></table>
        <table width=100% cellspacing=0 cellpadding=3>
            <tr><td style="padding-top:10px;"></td></tr>
            <tr>
                <td class="spandix-l" align=left colspan=3>
                    Temp: <input type="text" name="pe_temp" id="pe_temp" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['temp']; ?>"><sup>0</sup>C&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    PR:  <input type="text" name="pe_pr" id="pe_pr" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['pulse']; ?>">bpm&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    RR:  <input type="text" name="pe_rr" id="pe_rr" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['rr']; ?>">bpm&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    BP:  <input type="text" name="pe_bp" id="pe_bp" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['bp']; ?>">mm/HG&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Ht:  <input type="text" name="pe_ht" id="pe_ht" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['ht']; ?>" onchange="calculateBMI();">cm&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                    Wt:  <input type="text" name="pe_wt" id="pe_wt" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['wt']; ?>" onchange="calculateBMI();">kgs    
               </td>
            </tr>
            <tr>
                <td class="spandix-l" align=left>
                    Visual Acuity: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Right Eye:  <input type="text" name="pe_lefteye" id="pe_lefteye" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['lefteye']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Left Eye:  <input type="text" name="pe_righteye" id="pe_righteye" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['righteye']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Jaeger Test: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Right Eye:  <input type="text" name="j_lefteye" id="j_lefteye" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['jaegerleft']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Left Eye:  <input type="text" name="j_righteye" id="j_righteye" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['jaegerright']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    BMI:  <input type="text" name="pe_bmi" id="pe_bmi" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['bmi']; ?>" readonly>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
               </td>
               <td class="spandix-l"d><input type=radio name="pe_bmitype" id="pe_bmitype" value="Underweight" <?php if($b['bmi_category'] == 'Underweight') { echo "checked"; } ?>>&nbsp;Underweight</td>
               <td class="spandix-l"><input type=radio name="pe_bmitype" id="pe_bmitype" value="Overweight"  <?php if($b['bmi_category'] == 'Overweight') { echo "checked"; } ?>>&nbsp;Overweight</td>
            </tr>
             <tr>
               <td class="spandix-l" align=left>
                    Correctional: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Right Eye:  <input type="text" name="pe_correct_lefteye" id="pe_correct_lefteye" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['correct_left']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Left Eye:  <input type="text" name="pe_correct_righteye" id="pe_correct_righteye" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['correct_right']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Correctional: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Right Eye:  <input type="text" name="pe_jcorrect_lefteye" id="pe_jcorrect_lefteye" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['jcorrect_left']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Left Eye:  <input type="text" name="pe_jcorrect_righteye" id="pe_jcorrect_righteye" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['jcorrect_right']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Ishihara Test:  <input type="text" name="pe_ishihara" id="pe_ishihara" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 80px;font-weight: bold;" value="<?php echo $b['ishihara']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                </td>
                <td class="spandix-l" ><input type=radio name="pe_bmitype" id="pe_bmitype" value="Normal"  <?php if($b['bmi_category'] == 'Normal') { echo "checked"; } ?>>&nbsp;Normal Weight</td>
               <td class="spandix-l"><input type=radio name="pe_bmitype" id="pe_bmitype" value="Obese"  <?php if($b['bmi_category'] == 'Obese') { echo "checked"; } ?>>&nbsp;Obese</td>
            </tr> 
            <tr>
                <td class="spandix-l" align=center>
                    
                </td>
              
            </tr>               
        </table>
        <table width=100% cellpadding=5><tr><td align=center><span style="font-size: 10pt; font-weight: bold;">MEDICAL HISTORY</span></td></tr></table>
        <table width=100% cellpadding=3>
            <tr>
                <td width=12% class="spandix-l" valign=top>Past Medical History :</td>
                <td align=right>
                    <table width=100% cellpadding=2 cellspacing=0>
                        <tr>
                            <td width=33% valign=top>
                                <?php
                                    $medh1 = $o->dbquery("select id, history from options_medicalhistory order by id limit 0,10");
                                    while($medh1_row = $medh1->fetch_array()) {
                                        
                                        if(in_array($medh1_row[0], $pmh)) { $sltd = 'checked'; } else { $sltd = ''; }

                                        
                                        echo '<input type="checkbox" name="pe_medhistory[]" id="pe_medhistory[]" value="'.$medh1_row[0].'" '.$sltd.'>&nbsp;&nbsp;<span class="spandix-l">'.$medh1_row[1].'</span><br/>';
                                    }
                                ?>
                            </td>
                                      
                            <td width=33% valign=top>
                                 <?php
                                    $medh2 = $o->dbquery("select id, history from options_medicalhistory order by id limit 10,10");
                                    while($medh2_row = $medh2->fetch_array()) {
                                        if(in_array($medh2_row[0], $pmh)) { $sltd = 'checked'; } else { $sltd = ''; }
                                        echo '<input type="checkbox" name="pe_medhistory[]" id="pe_medhistory[]" value="'.$medh2_row[0].'" '.$sltd.'>&nbsp;&nbsp;<span class="spandix-l">'.$medh2_row[1].'</span><br/>';
                                    }
                                ?>      
                            </td>

                            <td width=33% valign=top>
                                <?php
                                    $medh3 = $o->dbquery("select id, history from options_medicalhistory order by id limit 20,10");
                                    while($medh3_row = $medh3->fetch_array()) {
                                        if(in_array($medh3_row[0], $pmh)) { $sltd = 'checked'; } else { $sltd = ''; }
                                        echo '<input type="checkbox" name="pe_medhistory[]" id="pe_medhistory[]" value="'.$medh3_row[0].'" '.$sltd.'>&nbsp;&nbsp;<span class="spandix-l">'.$medh3_row[1].'</span><br/>';
                                    }
                                ?>
                                <input type="text" name="pm_others" id="pm_others" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 250px;font-weight: bold;" value="<?php echo $b['pm_others']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
            <tr>
                <td class="spandix-l">Family History :</td>
                <td align=right><input type="text" name="pe_famhistory" id="pe_famhistory" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 98%;font-weight: bold;" value="<?php echo $b['fm_history']; ?>"></td>
            </tr>
            <tr>
                <td class="spandix-l">Previous Hospitalization :</td>
                <td align=right><input type="text" name="pe_hospitalization" id="pe_hospitalization" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 98%;font-weight: bold;" value="<?php echo $b['pv_hospitalization']; ?>"></td>
            </tr>
            <tr>
                <td class="spandix-l">Current Medication :</td>
                <td align=right><input type="text" name="pe_current_med" id="pe_current_med" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 98%;font-weight: bold;" value="<?php echo $b['current_med']; ?>"></td>
            </tr>
            <tr>
                <td colspan=2 class="spandix-l">
                    Menstrual History: <input type="text" name="pe_menshistory" id="pe_menshistory" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 160px;font-weight: bold;" value="<?php echo $b['mens_history']; ?>">y.o&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Parity:  <input type="text" name="pe_parity" id="pe_parity" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 160px;font-weight: bold;" value="<?php echo $b['parity']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    LMP:  <input type="text" name="pe_lmp" id="pe_lmp" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 200px;font-weight: bold;" value="<?php echo $b['lmp']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Contraceptive Use:  <input type="text" name="pe_contra" id="pe_contra" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 160px;font-weight: bold;" value="<?php echo $b['contraceptives']; ?>">      
                </td>
            </tr>
            <tr>
                <td colspan=2 class="spandix-l">
                    Smoker: <input type="text" name="pe_smoker" id="pe_smoker" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 160px;font-weight: bold;" value="<?php echo $b['smoker']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Pregnant:  <input type="text" name="pe_pregnant" id="pe_pregnant" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 160px;font-weight: bold;" value="<?php echo $b['pregnant']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Alcoholic Beverage/Drinker:  <input type="text" name="pe_alcoholic" id="pe_alcoholic" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 160px;font-weight: bold;" value="<?php echo $b['alcoholic']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Illicit Drug Use:  <input type="text" name="pe_drugs" id="pe_drugs" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 160px;font-weight: bold;" value="<?php echo $b['drugs']; ?>">      
                </td>
            </tr>
            <tr><td height=10></td></tr>
        </table>
        <table width=100% cellpadding=5 cellspacing=0 style="border-collapse: collapse; font-size: 11px;">
            <tr>
                <td width=15% align=center style="border: 1px solid black; font-weight: bold;">Review of Systems</td>
                <td width=8% align=center style="border: 1px solid black; font-weight: bold;">Status</td>
                <td width=27% align=center style="border: 1px solid black; font-weight: bold;">Findings</td>
                <td width=15% align=center style="border: 1px solid black; font-weight: bold;">Status</td>
                <td width=8% align=center style="border: 1px solid black; font-weight: bold;">Normal</td>
                <td width=27% align=center style="border: 1px solid black; font-weight: bold;">Findings</td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Head & Scalp</td>
                <td style="border: 1px solid black;" align=center>
                    <select name="pe_hs_normal" id="pe_hs_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['hs_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['hs_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_hs_findings" id="pe_hs_findings" value="<?php echo $b['hs_findings']; ?>"></td>
                <td style="border: 1px solid black;">Lungs</td>
                <td style="border: 1px solid black;" align=center>
                    
                    <select name="pe_lungs_normal" id="pe_lungs_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['lungs_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['lungs_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_lungs_findings" id="pe_lungs_findings" value="<?php echo $b['lungs_findings']; ?>"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Eyes & Ears</td>
                <td style="border: 1px solid black;" align=center>
                   
                    <select name="pe_ee_normal" id="pe_ee_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['ee_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['ee_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_ee_findings" id="pe_ee_findings" value="<?php echo $b['ee_findings']; ?>"></td>
                <td style="border: 1px solid black;">Heart</td>
                <td style="border: 1px solid black;" align=center>
                  
                    <select name="pe_heart_normal" id="pe_heart_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['heart_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['heart_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_heart_findings" id="pe_heart_findings" value="<?php echo $b['heart_findings']; ?>"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Skin/Allergy</td>
                <td style="border: 1px solid black;" align=center>
                   
                    <select name="pe_sa_normal" id="pe_sa_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['sa_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['sa_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>                           
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_sa_findings" id="pe_sa_findings" value="<?php echo $b['sa_findings']; ?>"></td>
                <td style="border: 1px solid black;">Abdomen</td>
                <td style="border: 1px solid black;" align=center>
                 
                    <select name="pe_abdomen_normal" id="pe_abdomen_normal">
                    <option value=''>N/A</option>
                        <option value="Y" <?php if($b['abdomen_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['abdomen_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_abdomen_findings" id="pe_abdomen_findings" value="<?php echo $b['abdomen_findings']; ?>"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Nose/Sinuses</td>
                <td style="border: 1px solid black;" align=center>
                   
                    <select name="pe_nose_normal" id="pe_nose_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['nose_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['nose_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>                
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_nose_findings" id="pe_nose_findings" value="<?php echo $b['nose_findings']; ?>"></td>
                <td style="border: 1px solid black;">Genitals</td>
                <td style="border: 1px solid black;" align=center>
                    
                    <select name="pe_genitals_normal" id="pe_genitals_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['genitals_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['genitals_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>                 
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_genitals_findings" id="pe_genitals_findings" value="<?php echo $b['genitals_findings']; ?>"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Mouth/Teeth/Tongue</td>
                <td style="border: 1px solid black;" align=center>
                   
                    <select name="pe_mouth_normal" id="pe_mouth_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['mouth_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['mouth_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>                        
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_mouth_findings" id="pe_mouth_findings" value="<?php echo $b['mouth_findings']; ?>"></td>
                <td style="border: 1px solid black;">Extremities</td>
                <td style="border: 1px solid black;" align=center>
                  
                    <select name="pe_extr_normal" id="pe_extr_normal">
                     <option value=''>N/A</option>
                        <option value="Y" <?php if($b['extr_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['extr_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>               
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_extr_findings" id="pe_extr_findings" value="<?php echo $b['extr_findings']; ?>"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Neck/Thyroid</td>
                <td style="border: 1px solid black;" align=center>
                    
                    <select name="pe_neck_normal" id="pe_neck_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['neck_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['neck_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>                
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_neck_findings" id="pe_neck_findings" value="<?php echo $b['neck_findings']; ?>"></td>
                <td style="border: 1px solid black;">Reflexes</td>
                <td style="border: 1px solid black;" align=center>
                   
                    <select name="pe_ref_normal" id="pe_ref_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['ref_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['ref_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select> 
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_ref_findings" id="pe_ref_findings" value="<?php echo $b['ref_findings']; ?>"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;">Breast-Axillla</td>
                <td style="border: 1px solid black;" align=center>
                    <select name="pe_check_normal" id="pe_check_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['check_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['check_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_check_findings" id="pe_check_findings" value="<?php echo $b['check_findings']; ?>"></td>
                <td style="border: 1px solid black;">BPE</td>
                <td style="border: 1px solid black;" align=center>
                   
                    <select name="pe_bpe_normal" id="pe_bpe_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['bpe_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['bpe_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>                
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_bpe_findings" id="pe_bpe_findings" value="<?php echo $b['bpe_findings']; ?>"></td>
            </tr>
            <tr>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;">Rectal</td>
                <td style="border: 1px solid black;" align=center>
                  
                    <select name="pe_rect_normal" id="pe_rect_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['rect_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['rect_normal'] == 'N') { echo "selected"; } ?>>Not Normal</option>
                    </select>                
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_rect_findings" id="pe_rect_findings" value="<?php echo $b['rect_findings']; ?>"></td>
            </tr>
        </table>
        <table style="padding-top:5px;"><tr>
                <td><button type=button style="padding:5px; background-color:#dfeffc; color:#1d5987;border-radius:5px;border:1px solid #1d5987;cursor:pointer;" onclick="javascript: marknormal();">Mark All Common Systems as "Normal"</button></td>
        </td></tr></table>
        <table width=100% cellpadding=5 cellspacing=0 style="border-collapse: collapse; font-size: 11px; margin-top: 10px;">
            <tr>
                <td width=1% class="border-no-right">&nbsp;</td>
                <td width=14% align=center class="border" style="font-weight: bold;">Laboratory</td>
                <td width=8% align=center style="border: 1px solid black; font-weight: bold;">Status</td>
                <td width=27% align=center style="border: 1px solid black; font-weight: bold;">Findings</td>
                <td width=1% class="border-no-right">&nbsp;</td>
                <td width=14% align=center class="border" style="font-weight: bold;">Review of Systems</td>
                <td width=8% align=center style="border: 1px solid black; font-weight: bold;">Status</td>
                <td width=27% align=center style="border: 1px solid black; font-weight: bold;">Findings</td>
            </tr>
            <tr>
                <td width=1% align=left class="borderless"> <?php list($xrayIndc) = $o->getArray("SELECT COUNT(code) FROM lab_samples WHERE so_no= '$_REQUEST[so_no]' AND code in ('X001','X002') AND status= '4';"); ?>
                    <?php if($xrayIndc>0){ echo "<img src=images/success.gif>"; } ?>
                </td>
                <td class="border">Chest X-Ray&nbsp;&nbsp;<a onclick="javascript: checkXray('X001'); javascript: checkXray2('X002');" title="Click to View X-Ray Result"><img src="images/icons/open-icon.png" width=8 height=8 align=top /></a></td>
                <td style="border: 1px solid black;" align=center>
                    <select name="pe_chest_normal" id="pe_chest_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['chest_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['chest_normal'] == 'N') { echo "selected"; } ?>>With Findings</option>
                    </select>   
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_chest_findings" id="pe_chest_findings" value="<?php echo $b['chest_findings']; ?>"></td>
                <td width=1% align=left class="borderless"> <?php list($ecgIndc) = $o->getArray("SELECT COUNT(with_file) FROM lab_samples WHERE so_no= '$_REQUEST[so_no]' AND CODE='X017' AND with_file='Y';"); 
                     if($ecgIndc>0){ echo "<img src=images/success.gif>"; } ?>
                </td>
                <td class="border">ECG&nbsp;&nbsp;<a onclick="javascript: viewPDFAttachment('X017');" title="Click to View ECG Result"><img src="images/icons/open-icon.png" width=8 height=8 align=top /></a></td>
                <td style="border: 1px solid black;" align=center>
                    <select name="pe_ecg_normal" id="pe_ecg_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['ecg_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['ecg_normal'] == 'N') { echo "selected"; } ?>>With Findings</option>
                    </select>                
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_ecg_findings" id="pe_ecg_findings" value="<?php echo $b['ecg_findings']; ?>"></td>
            </tr>
            <tr>
                <td width=1% align=left class="borderless"> <?php list($cbcIndc) = $o->getArray("SELECT COUNT(code) FROM lab_samples WHERE so_no= '$_REQUEST[so_no]' AND code='L010' AND status= '4';"); 
                     if($cbcIndc>0){ echo "<img src=images/success.gif>"; } ?>
                </td>
                <td class="border">CBC&nbsp;&nbsp;<a onclick="javascript: checkResult('L010'); javascript: viewFile('L010');" title="Click to View CBC Result"><img src="images/icons/open-icon.png" width=8 height=8 align=top /></a></td>
                <td style="border: 1px solid black;" align=center>
                     <select name="pe_cbc_normal" id="pe_cbc_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['cbc_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['cbc_normal'] == 'N') { echo "selected"; } ?>>With Findings</option>
                    </select>                
             
                <td style="border: 1px solid black;">
                 <input type="text" style="width: 100%; border: none;" name="pe_cbc_findings" id="pe_cbc_findings" value="<?php echo $b['cbc_findings']; ?>">
                                   
                </td>
                <td width=1% align=left class="borderless"> <?php list($papIndc) = $o->getArray("SELECT COUNT(with_file) FROM lab_samples WHERE so_no= '$_REQUEST[so_no]' AND CODE='L076' AND with_file='Y';"); 
                     if($papIndc>0){ echo "<img src=images/success.gif>"; } ?>
                </td>
                <td class="border">Papsmear&nbsp;&nbsp;<a onclick="javascript: viewFile('L076');" title="Click to View Papsmear Result"><img src="images/icons/open-icon.png" width=8 height=8 align=top /></a></td>
                <td style="border: 1px solid black;" align=center>
                   
                    <select name="pe_papsmear_normal" id="pe_papsmear_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['pap_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['pap_normal'] == 'N') { echo "selected"; } ?>>With Findings</option>
                    </select>  

                </td>
                <td style="border: 1px solid black;">
                    <input type="text" style="width: 100%; border: none;" name="pe_pap_findings" id="pe_pap_findings" value="<?php echo $b['pap_findings']; ?>">
                                
                </td>
            </tr>
            <tr>
                <td width=1% align=left class="borderless"> <?php list($uaIndc) = $o->getArray("SELECT COUNT(CODE) FROM lab_samples WHERE so_no= '$_REQUEST[so_no]' AND CODE='L012' AND STATUS= '4';"); 
                     if($uaIndc>0){ echo "<img src=images/success.gif>"; } ?>
                </td>
                <td class="border">Urinalysis&nbsp;&nbsp;<a onclick="javascript: checkResult('L012'); javascript: viewFile('L012');" title="Click to View UA Result"><img src="images/icons/open-icon.png" width=8 height=8 align=top /></a></td>
                <td style="border: 1px solid black;" align=center>
                   
                    <select name="pe_ua_findings_normal" id="pe_ua_findings_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['ua_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['ua_normal'] == 'N') { echo "selected"; } ?>>With Findings</option>
                    </select>  

                </td>
                <td style="border: 1px solid black;">
                    <input type="text" style="width: 100%; border: none;" name="pe_ua_findings" id="pe_ua_findings" value="<?php echo $b['ua_findings']; ?>">
                                
                </td>
                <td width=1% align=left class="borderless"> <?php list($hbsagIndc) = $o->getArray("SELECT COUNT(code) FROM lab_samples WHERE so_no= '$_REQUEST[so_no]' AND code in ('L042','L051') AND status= '4';"); 
                     if($hbsagIndc>0){ echo "<img src=images/success.gif>"; } ?>
                </td>
                <td class="border">HbsAg&nbsp;&nbsp;<a onclick="javascript: checkResult('L042'); javascript: checkResult('L051');" title="Click to View HbsAg Result"><img src="images/icons/open-icon.png" width=8 height=8 align=top /></a></td>
                <td style="border: 1px solid black;" align=center>
                <input type="text" style="width: 100%; border: none;" name="pe_hbsag_normal" id="pe_hbsag_normal" value="<?php echo $hbsagRes['result']; ?>">

                </td>
                <td style="border: 1px solid black;">
                    <input type="text" style="width: 100%; border: none;" name="pe_hbsag_findings" id="pe_hbsag_findings" value="<?php echo $hbsagRes['remarks']; ?>">
                                
                </td>
            </tr>
            <tr>
                <td width=1% align=left class="borderless"> <?php list($stoolIndc) = $o->getArray("SELECT COUNT(code) FROM lab_samples WHERE so_no= '$_REQUEST[so_no]' AND code='L036' AND status= '4';"); 
                     if($stoolIndc>0){ echo "<img src=images/success.gif>"; } ?>
                </td>
                <td class="border">Fecalysis&nbsp;&nbsp;<a onclick="javascript: checkResult('L036');" title="Click to View Fecalysis Result"><img src="images/icons/open-icon.png" width=8 height=8 align=top /></a></td>
                <td style="border: 1px solid black;" align=center>
                
            
                    <select name="pe_se_normal" id="pe_se_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['se_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['se_normal'] == 'N') { echo "selected"; } ?>>With Findings</option>
                    </select>                
                    </select>                               
                </td>
                <td style="border: 1px solid black;">
                    <input type="text" style="width: 100%; border: none;" name="pe_se_findings" id="pe_se_findings" value="<?php echo $b['se_findings']; ?>">
                     
                </td>
                <td width=1% align=left class="borderless"> <?php list($hepaIndc) = $o->getArray("SELECT COUNT(code) FROM lab_samples WHERE so_no= '$_REQUEST[so_no]' AND code='L050' AND status= '4';"); 
                     if($hepaIndc>0){ echo "<img src=images/success.gif>"; } ?>
                </td>
                <td class="border">HEPATITIS A&nbsp;&nbsp;<a onclick="javascript: checkResult('L050');" title="Click to View HEPATITIS A Result"><img src="images/icons/open-icon.png" width=8 height=8 align=top /></a></td>
                <td style="border: 1px solid black;" align=center>
                <input type="text" style="width: 100%; border: none;" name="pe_hepa_normal" id="pe_hepa_normal" value="<?php echo $hepaRes['result']; ?>">

                </td>
                <td style="border: 1px solid black;">
                    <input type="text" style="width: 100%; border: none;" name="pe_hepa_findings" id="pe_hepa_findings" value="<?php echo $hepaRes['remarks']; ?>">
                                
                </td>
            </tr>
            <tr>
                <td width=1% align=left class="borderless"> <?php list($ptIndc) = $o->getArray("SELECT COUNT(code) FROM lab_samples WHERE so_no= '$_REQUEST[so_no]' AND code='L037' AND status= '4';"); 
                     if($ptIndc>0){ echo "<img src=images/success.gif>"; } ?>
                </td>
                <td class="border">Pregnancy Test&nbsp;&nbsp;<a onclick="javascript: checkResult('L037');" title="Click to View Pregnancy Result"><img src="images/icons/open-icon.png" width=8 height=8 align=top /></a></td>
                <td style="border: 1px solid black;" align=center>
                <input type="text" style="width: 100%; border: none;" name="pe_pt_normal" id="pe_pt_normal" value="<?php echo $ptRes['result']; ?>">
                     
                </td>
                <td style="border: 1px solid black;">
                    <input type="text" style="width: 100%; border: none;" name="pe_pt_findings" id="pe_pt_findings" value="<?php echo $ptRes['remarks']; ?>">
                     
                </td>
                <td class="border-no-right"></td>
                <td class="border">OTHER PROCEDURES:</td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
            </tr>
            <tr>
            <td width=1% align=left class="borderless"> <?php list($dtIndc) = $o->getArray("SELECT COUNT(with_file) FROM lab_samples WHERE so_no= '$_REQUEST[so_no]' AND CODE='L047' AND with_file='Y';"); 
                     if($dtIndc>0){ echo "<img src=images/success.gif>"; } ?>
            </td>
            <td class="border">Drug Test&nbsp;&nbsp;<a onclick="javascript: viewPDFAttachment('L047');" title="Click to View Drug Test Result"><img src="images/icons/open-icon.png" width=8 height=8 align=top /></a></td>
                <td style="border: 1px solid black;" align=center>
                    
                    <select name="pe_dt_normal" id="pe_dt_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['dt_normal'] == 'Y') { echo "selected"; } ?>>POSITIVE</option>
                        <option value="N" <?php if($b['dt_normal'] == 'N') { echo "selected"; } ?>>NEGATIVE</option>
                    </select>                
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_dt_findings" id="pe_dt_findings" value="<?php echo $b['dt_findings']; ?>"></td>
                <td class="border-no-right"></td>
                <td class="border"><input type="text" style="width: 100%; border: none;" name="pe_others1" id="pe_others1" value="<?php echo $b['others1_name']; ?>"></td>
                <td style="border: 1px solid black;" align=center>
                   
                    <select name="pe_others1_normal" id="pe_others1_normal">
                        <option value=''>N/A</option>
                        <option value="Y" <?php if($b['others1_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                        <option value="N" <?php if($b['others1_normal'] == 'N') { echo "selected"; } ?>>With Findings</option>
                    </select>                
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_others1_findings" id="pe_others1_findings" value="<?php echo $b['others1_findings']; ?>"></td>
            </tr>
            <tr>
                <td width=1% align=left class="borderless"> <?php list($btIndc) = $o->getArray("SELECT COUNT(code) FROM lab_samples WHERE so_no= '$_REQUEST[so_no]' AND code='L040' AND status= '4';"); 
                        if($btIndc>0){ echo "<img src=images/success.gif>"; } ?>
                </td>
                <td class="border">Blood Typing&nbsp;&nbsp;<a onclick="javascript: checkResult('L040');" title="Click to View Blood Typping Result"><img src="images/icons/open-icon.png" width=8 height=8 align=top /></a></td>
                    <td style="border: 1px solid black;" align=center>
                        <input type="text" style="width: 100%; border: none;" name="pe_bt_normal" id="pe_bt_normal" value="<?php echo $btRes['result']; ?>">
                    
                    </td>
                    <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_bt_findings" id="pe_bt_findings" value="<?php echo $btRes['remarks']; ?>"></td>
                    <td class="border-no-right"></td>
                    <td class="border"><input type="text" style="width: 100%; border: none;" name="pe_others2" id="pe_others2" value="<?php echo $b['others2_name']; ?>"></td>
                    <td style="border: 1px solid black;" align=center>
                        <select name="pe_others2_normal" id="pe_others2_normal">
                            <option value=''>N/A</option>
                            <option value="Y" <?php if($b['others2_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                            <option value="N" <?php if($b['others2_normal'] == 'N') { echo "selected"; } ?>>With Findings</option>
                        </select>                       
                </td>
                <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_others2_findings" id="pe_others2_findings" value="<?php echo $b['others2_findings']; ?>"></td>
            </tr>
            <tr>
                <td width=1% align=left class="borderless"> <?php list($antigenIndc) = $o->getArray("SELECT COUNT(code) FROM lab_samples WHERE so_no= '$_REQUEST[so_no]' AND code='L087' AND status= '4';"); 
                        if($antigenIndc>0){ echo "<img src=images/success.gif>"; } ?>
                </td>
                <td class="border">Rapid Antigen Test&nbsp;&nbsp;<a onclick="javascript: checkResult('L087');" title="Click to View RAPID TEST (COVID-19) ANTIGEN Result"><img src="images/icons/open-icon.png" width=8 height=8 align=top /></a></td>
                    <td style="border: 1px solid black;" align=center>
                        <input type="text" style="width: 100%; border: none;" name="pe_antigen_normal" id="pe_antigen_normal" value="<?php echo $antigenRes['result']; ?>">
                    
                    </td>
                    <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_antigen_findings" id="pe_antigen_findings" value="<?php echo $b['antigen_findings']; ?>"></td>
                    <td class="border-no-right"></td>
                    <td class="border"><input type="text" style="width: 100%; border: none;" name="pe_others3" id="pe_others3" value="<?php echo $b['others3_name']; ?>"></td>
                    <td style="border: 1px solid black;" align=center>
                        <select name="pe_others3_normal" id="pe_others3_normal">
                            <option value=''>N/A</option>
                            <option value="Y" <?php if($b['others2_normal'] == 'Y') { echo "selected"; } ?>>Normal</option>
                            <option value="N" <?php if($b['others2_normal'] == 'N') { echo "selected"; } ?>>With Findings</option>
                        </select>                       
                    </td>
                    <td style="border: 1px solid black;"><input type="text" style="width: 100%; border: none;" name="pe_others3_findings" id="pe_others3_findings" value="<?php echo $b['others3_findings']; ?>"></td>
            </tr>
        </table>
        <table width=100% cellpadding=5 cellspacing=0>
            <tr><td colspan=2 class="spandix-l">I Hereby Certify that I have examined and found the employee to be <select class=gridInput name="pe_fit" id="pe_fit"><option value="FIT" <?php if($b['pe_fit'] == 'FIT') { echo "selected"; } ?>>FIT</option><option value="UNFIT" <?php if($b['pe_fit'] == 'UNFIT') { echo "selected"; } ?>>UNFIT</option></select> for employment.<br/><b>CLASSIFICATION:</b></td></tr>                
            <tr>
                <td width=20% style="padding-left: 5%;" class="spandix-l"><input type="radio" name="pe_class" id="pe_class" value="A" <?php if($b['classification'] == 'A') { echo "checked"; } ?>>&nbsp;&nbsp;CLASS A</td>
                <td width=80% class="spandix-l">
                    Physically fit for all types of work
                   </td>
            </tr>
            <tr>
                <td width=20% style="padding-left: 5%;" class="spandix-l" valign=top><input type="radio" name="pe_class" id="pe_class" value="B" <?php if($b['classification'] == 'B') { echo "checked"; } ?>>&nbsp;&nbsp;CLASS B</td>
                <td width=80% class="spandix-l">Physically fit for all types of work
                <br/>
                    Has Minor ailment/defect. Easily curable or offers no handicap to applied.
                    <br/>
                    <input type="radio" name="pe_class_b" id="pe_class_b" value="1" <?php if($b['class_b'] == '1') { echo "checked"; } ?>>&nbsp;&nbsp;Needs Treatment Correction : &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="pe_class_b_remarks1" id="pe_class_b_remarks1" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width:370px;" value="<?php echo $b['class_b_remarks1']; ?>">
                    <br/>
                    <input type="radio" name="pe_class_b" id="pe_class_b" value="2" <?php if($b['class_b'] == '2') { echo "checked"; } ?>>&nbsp;&nbsp;Treatment Optional For : &nbsp;&nbsp;<input type="text" name="pe_class_b_remarks2" id="pe_class_b_remarks2" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 400px;" value="<?php echo $b['class_b_remarks2']; ?>">
                
                </td>
            </tr>
            <tr>
                <td width=20% style="padding-left: 5%;" class="spandix-l" valign=top><input type="radio" name="pe_class" id="pe_class" value="C" <?php if($b['classification'] == 'C') { echo "checked"; } ?>>&nbsp;&nbsp;CLASS C</td>
                <td width=80% class="spandix-l">Physically fit for less strenous type of work. Has minor ailments/defects.
                <br/>
                    Easily curable or offers no handicap to job applied.
                    <br/>
                    <input type="radio" name="pe_class_c" id="pe_class_c" value="1" <?php if($b['class_c'] == '1') { echo "checked"; } ?>>&nbsp;&nbsp;Needs Treatment Correction : &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="pe_class_c_remarks1" id="pe_class_c_remarks1" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width:370px;" value="<?php echo $b['class_c_remarks1']; ?>">
                    <br/>
                    <input type="radio" name="pe_class_c" id="pe_class_c" value="2" <?php if($b['class_c'] == '2') { echo "checked"; } ?>>&nbsp;&nbsp;Treatment Optional For : &nbsp;&nbsp;<input type="text" name="pe_class_c_remarks2" id="pe_class_c_remarks2" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 400px;" value="<?php echo $b['class_c_remarks2']; ?>">
                
                </td>
            </tr>
            <tr> 
                <td width=20% style="padding-left: 5%;" class="spandix-l"><input type="radio" name="pe_class" id="pe_class" value="D" <?php if($b['classification'] == 'D') { echo "checked"; } ?>>&nbsp;&nbsp;CLASS D</td>
                <td width=80% class="spandix-l">
                    Employment at the risk and discretion of the management
                </td>
            </tr>
            <tr>
                <td width=20% style="padding-left: 5%;" class="spandix-l"><input type="radio" name="pe_class" id="pe_class" value="E" <?php if($b['classification'] == 'E') { echo "checked"; } ?>>&nbsp;&nbsp;CLASS E</td>
                <td width=80% class="spandix-l">
                    Unfit for Employment
                </td>
            </tr>
            <tr>
                <td width=20% style="padding-left: 5%;" class="spandix-l"><input type="radio" name="pe_class" id="pe_class" value="PENDING" <?php if($b['classification'] == 'PENDING') { echo "checked"; } ?>>&nbsp;&nbsp;PENDING</td>
                <td width=80% class="spandix-l">
                    For further evaluation of: &nbsp;&nbsp;&nbsp;<input type="text" name="pe_eval_remarks" id="pe_eval_remarks" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 380px;" value="<?php echo $b['pending_remarks']; ?>">
                </td>
            </tr>
            <tr><td colspan=2 class="spandix-l">Remarks: <input type="text" name="pe_remarks" id="pe_remarks" style="border-top: none; border-left: none; border-right: none; border-bottom: 1px solid black; font-size: 11px; width: 70%;" value="<?php echo $b['overall_remarks']; ?>"></td></tr>
        </table>
        <table><tr><td height="10"></td></tr></table>
        <!-- <table width=100%>
            <tr>
                <td class="spandix-l" width=15%>Examined By: </td>
                <td >
                    <select name="pe_examined_by" id="pe_examined_by" style="width: 250px;">
                        <option value=''>- Select Examining Physician</option>
                        <?php
                            $equery = $o->dbquery("SELECT CONCAT(fullname,', ',prefix), id FROM options_doctors WHERE id NOT IN (1,2);");
                            while($erow = $equery->fetch_array()) {
                                echo "<option value='$erow[1]' ";
                                if($b['examined_by'] == $erow[1]) { echo "selected"; }
                                echo ">$erow[0]<option>";
                            }
                        ?>
                    </select>                       
                </td>
            </tr>
            <tr>
                <td class="spandix-l">Evaluated By: </td>
                <td >
                    <select name="pe_evaluated_by" id="pe_evaluated_by" style="width: 250px;">
                     <option value=''>- Select Evaluating Physician</option>
                        <?php
                            $equery = $o->dbquery("SELECT CONCAT(fullname,', ',prefix), id FROM options_doctors WHERE id NOT IN (1,2);");
                            while($erow = $equery->fetch_array()) {
                                echo "<option value='$erow[1]' ";
                                if($b['evaluated_by'] == $erow[1]) { echo "selected"; }
                                echo ">$erow[0]<option>";
                            }
                        ?>
                    </select>                       
                </td>
            </tr>
        </table> -->

        <table width=100%>
            <tr style="float:left;">
                <td class="spandix-1" width="25%" style="font-size:12px;">Examined By:</td>
                <td class="spandix-1"><input type="text" style="width: 390px; font-weight:300; padding:10px; font-size:13px;" name="pe_examined_by" id="pe_evaluated_by" value="<?php echo $docfullname,' ',$docprefix,' ',$c['examin_d8'], ' ',$c['examin_tym']; ?>" readonly></td>
            </tr>
            <tr style="float:right;">
                <td class="spandix-1" width="25%" style="font-size:12px; padding-left: 10px;">Evaluated By:</td>
                <td class="spandix-1"><input type="text" style="width: 390px; font-weight:300; padding:10px; font-size:13px;" name="pe_evaluated_by" id="pe_evaluated_by" value="<?php echo $doctorfullname,' ',$doctorprefix,' ',$c['eval_d8'], ' ',$c['eval_tym']; ?>" readonly></td>
            </tr>
        </table>
	</form>
    <div id="imageAttachment" name="imageAttachment" style="display: none;">
        <p id="fileLocation" name="fileLocation"></p>
    </div>
    <div id="pemePop" style="display: none;">
	<form name="frmPEMEPop" id="frmPEMEPop">
		<table width=100% callpaddin=0 cellspacing=3>
		<tr>
			<td align=center>
                <?php
                    list($custQuery) = $o->getArray("SELECT DISTINCT description FROM so_details WHERE so_no= '$_REQUEST[so_no]';");
                    echo $custQuery;
                ?>
			</td>
		</tr>
            <tr><td height=4></td></tr>
		</table>
	</form>
    </div>
</body>
</html>