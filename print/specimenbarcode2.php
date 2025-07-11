<?php
    session_start();
	require_once("../lib/mpdf6/mpdf.php");
	require_once("../handlers/initDB.php");

    $con = new myDB;
    $serialno = $_GET['id'];
    $a = $con->getArray("SELECT a.code, b.patient_name AS pname, date_format(c.birthdate,'%m/%d/%y') as bday, FLOOR(ROUND(DATEDIFF(b.so_date,c.birthdate) / 364.25,2)) AS age,c.gender,CONCAT(DATE_FORMAT(extractdate,'%m/%d/%Y'),' ',TIME_FORMAT(extractime,'%h:%i %p')) AS tstamp, e.sample_type FROM lab_samples a LEFT JOIN so_header b ON a.so_no = b.so_no AND a.branch = b.branch LEFT JOIN patient_info c ON b.patient_id = c.patient_id LEFT JOIN options_sampletype e ON a.sampletype = e.id WHERE a.serialno = '$serialno';");

    if($a['code'] == 'L047') { $bSize = '0.75'; } else { $bSize = '0.80'; }

    $mpdf=new mPDF('win-1252','BARCODE','','',0,0,0,0,0,0);
    $mpdf->use_embeddedfonts_1252 = true;    // false is default
    $mpdf->setAutoTopMargin='stretch';
    $mpdf->setAutoBottomMargin='stretch';
    $mpdf->use_kwt = true;
    $mpdf->SetProtection(array('print'));
    $mpdf->SetAuthor("Opon Medical Diagnostic Corporation");
    $mpdf->SetDisplayMode(100);

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
                            <td align=left colspan=2>&nbsp;&nbsp;&nbsp;'.$a['pname'].'^' . $a['gender'] . '^'.$a['age'].'</td>
                        </tr>
                        <tr><td colspan=2 align=center><barcode code="'.substr($serialno,0,10).'" type="C128A" height="0.3" size="0.8"></td></tr>
                        <tr>
                            <td align=left>&nbsp;&nbsp;&nbsp;'.$a['tstamp'].'</td>
                            <td width=50% align=right>'. $serialno . '&nbsp;&nbsp;&nbsp;</td>
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
                    <tr><td colspan=2 align=center><barcode code="'.$serialno.'" type="C128A" height="0.6" size="0.75"></td></tr>
                    <tr>
                        <td align=left>&nbsp;&nbsp;&nbsp;'.$a['tstamp'].'</td>
                        <td width=50% align=right>'. $serialno . '&nbsp;&nbsp;&nbsp;</td>
                    </tr>    
                </table>
            </body>
        </html>';
        
    }

 $mpdf->WriteHTML($html);
 $mpdf->Output();
 exit;
?>