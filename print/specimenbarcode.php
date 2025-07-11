<?php
    session_start();
	require_once("../lib/mpdf6/mpdf.php");
	require_once("../handlers/initDB.php");
    include("../handlers/_generics.php");


    $con = new _init;
    $serialno = $_GET['id'];
    $a = $con->getArray("SELECT b.patient_name AS pname, DATE_FORMAT(c.birthdate,'%m/%d/%y') AS bday, b.so_date as xorderdate, c.birthdate as xbday, YEAR(b.so_date) - YEAR(c.birthdate) AS age,c.gender,CONCAT(DATE_FORMAT(extractdate,'%m/%d/%Y'),' ',TIME_FORMAT(extractime,'%h:%i %p')) AS tstamp, e.sample_type, a.`code`, `procedure`, g.subcategory, a.serialno, b.so_no FROM lab_samples a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_sampletype e ON a.sampletype = e.id  LEFT JOIN services_master f ON a.code = f.code LEFT JOIN options_servicesubcat g ON f.subcategory = g.id WHERE a.serialno = '$serialno';");

   $con->calculateAge2($a['xorderdate'],$a['xbday']);

    if($a['code'] == 'L047') { $bSize = '0.75'; } else { $bSize = '0.8'; }

    $mpdf=new mPDF('win-1252','BARCODE','','',2,2,1,0,0,0);
    $mpdf->use_embeddedfonts_1252 = true;    // false is default
    $mpdf->setAutoTopMargin='stretch';
    $mpdf->setAutoBottomMargin='stretch';
    $mpdf->use_kwt = true;
    $mpdf->SetProtection(array('print'));
    $mpdf->SetAuthor("Opon Medical Diagnostic Corporation");
    $mpdf->SetDisplayMode(100);

    $procedure = '';
    $testQuery = $con->dbquery("SELECT IF(b.short_description != '',b.short_description,b.description) FROM lab_samples a LEFT JOIN services_master b ON a.code = b.code LEFT JOIN options_servicecat c ON b.category = c.id WHERE a.serialno = '$a[serialno]' AND a.so_no = '$a[so_no]' AND c.id IN ('1','2');");
    while($testRow = $testQuery->fetch_array()) {
        $procedure .= $testRow[0] . ",";
    }

    if($a['code'] != 'L047') {

        $html = '<html>
                <head>
                    <title>Specimen Barcode</title>
                    <style>
                        body {
                            font-family: sans;
                            font-size: 5.5pt;
                        }    
                    </style>
                </head>
                <body>
                    <table width=100% cellpadding=0 cellspacing=0  style="font-weight: bold;">
                        <tr>
                            <td align=left colspan=2>'.$a['pname'].'^' . $a['gender'] . '^' .$con->ageDisplay. '</td>
                        </tr>
                        <tr>
                            <td align=left colspan=2>DOB: '.$a['bday'].'</td>
                        </tr>
                        <tr><td colspan=2 align=center><barcode code="'.substr($serialno,0,10).'" type="C128A" height="0.8" size="0.85"></td></tr>
                        <tr>
                            <td align=left>'.$a['tstamp'].'</td>
                            <td width=50% align=right>'. $serialno . '</td>
                        </tr>
                         <tr>
                            <td colspan=2>'. strtoupper($procedure) . '</td>
                        </tr>     
                    </table>
                </body>
            </html>';
    } else {
        $html = '<html>
            <head>
                <title>Specimen Barcode</title>
                <style>
                    body {
                        font-family: sans;
                        font-size: 5.5pt;
                    }    
                </style>
            </head>
            <body>
                <table width=100% cellpadding=0 cellspacing=0  style="font-weight: bold;">
                    <tr>
                        <td align=left colspan=2>&nbsp;</td>
                    </tr>
                    <tr><td colspan=2 align=center><barcode code="'.substr($serialno,0,10).'" type="C128A" height="1.5" size="0.81"></td></tr>
                    <tr>
                        <td align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$a['tstamp'].'</td>
                        <td width=50% align=right>'. $serialno . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                     <tr>
                        <td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. strtoupper($procedure) . '</td>
                    </tr>
                    <tr><td height=3></td></tr>    
                    <tr>
                        <td colspan=4 align=left>&nbsp;</td>
                    </tr>    
                </table>
            </body>
        </html>';
        
    }

 $mpdf->WriteHTML($html);
 $mpdf->Output();
 exit;
?>