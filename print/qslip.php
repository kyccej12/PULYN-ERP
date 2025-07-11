<?php
    session_start();
	require_once("../lib/mpdf6/mpdf.php");
	require_once("../handlers/initDB.php");

    $con = new myDB;
    $pno = $_GET['priority'];
    
    $co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
   
    $mpdf=new mPDF('win-1252','QSLIP','','',5,5,5,5,5,5);
    $mpdf->use_embeddedfonts_1252 = true;    // false is default
    $mpdf->setAutoTopMargin='stretch';
    $mpdf->setAutoBottomMargin='stretch';
    $mpdf->use_kwt = true;
    $mpdf->SetProtection(array('print'));
    $mpdf->SetAuthor("OMDC Prime Diagnostics");
    $mpdf->SetDisplayMode(25);

    $html = '<html>
                <head>
                    <title>Priority No.</title>
                    <style>
                        body {
                            font-family: arial;
                            font-size: 8pt;
                        }    
                    </style>
                </head>
                <body>
                    <table width=100% cellpadding=0 cellspacing=0>
                        <tr>
                            <td width=75><img src="../images/logo-small.png" width=64 height=64 align=absmiddle></td>
                            <td style="color:#000000; padding-top: 5px;" valign=top>
                                <b>'.strtoupper($co['company_name']).'</b><br/><span style="font-size: 7pt;">'.$co['company_address'].'<br/>Tel # '.$co['tel_no'].'</span>
                            </td>
                        </tr>
                        <tr><td colspan=2 height=5>&nbsp;</td></tr>
                        <tr><td colspan=2 height=20 style="margin-top: 5px; border-top: 0.1em solid black;">&nbsp;</td></tr>
                        <tr><td colspan=2 style="font-size: 42pt; font-weight: bold;" align=center>'.str_pad($pno,4,'0',STR_PAD_LEFT).'<br/><barcode size=2 code="'.substr($pno,0,10).'" height=0.5 type="C128A"></td></tr>
                        <tr><td colspan=2 style="padding-top: 20px; font-weight: 11pt;" align=center>PRIORITY NUMBER</td></tr>
                    </table>
                </body>
            </html>';

 $mpdf->WriteHTML($html);
 $mpdf->Output();
 exit;
?>