<?php 
	
	session_start();
	require_once "handlers/_generics.php";
	
    $o = new _init;

   // $a = $o->getArray("SELECT record_id AS id, LPAD(a.so_no,6,0) AS myso,DATE_FORMAT(a.so_date,'%m/%d/%Y') AS sodate, a.so_date, b.birthdate, LPAD(a.pid,6,0) AS mypid,CONCAT(b.lname,', ',b.fname,' ',b.mname) AS pname,IF(b.gender='M','Male','Female') AS gender, DATE_FORMAT(b.birthdate,'%m/%d/%Y') AS bday,a.code,a.procedure,sampletype,serialno,DATE_FORMAT(extractdate,'%m/%d/%Y') AS exday,TIME_FORMAT(extractime,'%h:%i %p') AS etime,extractby,a.location FROM lab_samples a  LEFT JOIN patient_info b ON a.pid = b.patient_id WHERE a.record_id = '$_REQUEST[lid]';");
    $b = $o->getArray("select * from lab_cbcresult where serialno = '$a[serialno]';");
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>OMDC Prime Medical Diagnostics Corp.</title>
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/texteditor/jquery-te-1.4.0.css" rel="stylesheet" type="text/css" />
    <link href="ui-assets/signature/jquery.signature.css" rel="stylesheet" type="text/css">
	<link href="style/style-mobile.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
    <script language="javascript" src="ui-assets/signature/jquery.signature.js"></script>
    <style>
        .container { margin: 10px; max-width: 560px; }
        .kbw-signature { width: 495px; height: 200px; }
    </style>
    <script>
        $(function() {
            var sig = $('#sig').signature({
                background: '#fff',
                color: '#000'
            });
           
            $('#disable').click(function() {
                var disable = $(this).text() === 'Disable';
                $(this).text(disable ? 'Enable' : 'Disable');
                sig.signature(disable ? 'disable' : 'enable');
            });

            $('#clear').click(function() {
                sig.signature('clear');
            });
            $('#json').click(function() {
                
                $.post("src/sjerp.php", { mod: "saveSignature", jsonSignature:sig.signature('toDataURL','image/jpeg'), so_no: $("#pe_sono").val(), pid: $("#pe_pid").val(), sid:Math.random() },function(){ 
                    alert("Signature Successfully Saved!");
                    parent.closeDialog("#signaturepad");

                });
                
               // alert(sig.signature('toJSON'));
            });
            $('#svg').click(function() {
                alert(sig.signature('toSVG'));
            });
        });
    </script>
</head>
<body>
    <form name="frmSignPal" id="frmSignPal">
        <input type="hidden" name="pe_sono" id="pe_sono" value="<?php echo $_REQUEST['so_no']; ?>">
        <input type="hidden" name="pe_pid" id="pe_pid" value="<?php echo $_REQUEST['pid']; ?>">
        <div class="container">
            <div id="sig"></div>
            <p style="clear: both;">
            <!--button id="disable">Disable</button-->
            <button type="button" id="clear">Clear</button>
            <button type="button" id="json">Save Signature</button>
            <!--button id="svg">To SVG</button-->
        </div>
    </form>
</body>
</html>