<?php 
	
	session_start();
	require_once "handlers/_generics.php";
    $o = new _init;

    $a = $o->getArray("SELECT id, title, template, template_owner, xray_type, DATE_FORMAT(a.created_on, '%m/%d/%Y %r') AS created, DATE_FORMAT(a.updated_on, '%m/%d/%Y %r') AS updated, b.fullname, a.status FROM xray_templates a LEFT JOIN user_info b ON a.updated_by = b.emp_id where a.id = '$_REQUEST[id]';");
    
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
       
    </script>
</head>
<body>
    <form name="frmXrayTemplate" id="frmXrayTemplate"> 
        <input type="hidden" name="tempid" id="tempid" value="<?php echo $a['id']; ?>">
        <table width=100% cellpadding=0 cellspacing=0 valign=top>
         <tr>
             <td width=35% valign=top>
                <table width=100% cellspacing=0 cellpadding=0><tr><td width=100% class=gridHead align=center>Template Details</td></tr></table>
                <table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px; margin-bottom: 5px;">
                 <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Type&nbsp;:</td>
                        <td align=left>
                            <select name="template_category" id="template_category" class="gridInput" style="width:100%; font-size: 11px;">
                                <option value='X-Ray' <?php if($a['template_category'] == 'X-Ray') { echo "selected"; } ?>>X-Ray</option>
                                <option value='Ultrasound' <?php if($a['template_category'] == 'Ultrasound') { echo "selected"; } ?>>Ultrasound</option>
                               
                            </select>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Template Title&nbsp;:</td>
                        <td align=left>
                            <input class="gridInput" style="width:100%;" type=text name="template_title" id="template_title" value="<?php echo $a['title'] ?>">
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%"  class="bareBold" style="padding-right: 15px;">Type&nbsp;:</td>
                        <td align=left>
                            <select name="template_type" id="template_type" class="gridInput" style="width:100%; font-size: 11px;">
                                <option value='1' <?php if($a['xray_type'] == 1) { echo "selected"; } ?>>Upper Extremities (X-Ray)</option>
                                <option value='2' <?php if($a['xray_type'] == 2) { echo "selected"; } ?>>Lower Extremities (X-Ray)</option>
                                <option value='3' <?php if($a['xray_type'] == 3) { echo "selected"; } ?>>Abdominal (Ultrasound)</option>
                                <option value='4' <?php if($a['xray_type'] == 4) { echo "selected"; } ?>>Transvaginal (Ultrasound)</option>
                                <option value='5' <?php if($a['xray_type'] == 5) { echo "selected"; } ?>>Breast (Ultrasound)</option>
                                <option value='6' <?php if($a['xray_type'] == 6) { echo "selected"; } ?>>Vertebral (X-Ray)</option>
                                <option value='7' <?php if($a['xray_type'] == 7) { echo "selected"; } ?>>Chest (X-Ray)</option>
                                <option value='8' <?php if($a['xray_type'] == 8) { echo "selected"; } ?>>Skull (X-Ray)</option>
                                <option value='9' <?php if($a['xray_type'] == 9) { echo "selected"; } ?>>Pelvic (X-Ray)</option>
                            </select>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Template Owner&nbsp;:</td>
                        <td align=left>
                            <select name="template_owner" id="template_owner" class="gridInput" style="width: 100%; font-size: 11px;">
                                <?php
                                    $query = $o->dbquery("select id, fullname from options_doctors order by fullname;");
                                    while($d = $query->fetch_array()) {
                                        echo "<option value='$d[0]' ";
                                        if($d[0] == $a['template_owner']) { echo "selected"; }
                                        echo ">$d[1]</option>";
                                    }
                                ?>
                            </select>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>

                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Last Updated On&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="updated_on" id="updated_on" value="<?php echo $a['updated']; ?>" readonly>
                        </td>				
                    </tr>
                    <tr><td height=3></td></tr>
                    <tr>
                        <td align="left" width="35%" class="bareBold" style="padding-right: 15px;">Last Updated By&nbsp;:</td>
                        <td align=left>
                            <input type="text" class="gridInput" style="width:100%;" name="updated_by" id="updated_by" value="<?php echo $a['fullname']; ?>" readonly>
                        </td>				
                    </tr>
                </table>
            </td>
            <td width=65% valign=top>
                  
                <textarea name="template_details" id="template_details" style="width:100%;"><?php echo html_entity_decode($a['template']); ?></textarea>
                           
            </td>
        </tr>
    </table>              
</form>
</body>
</html>