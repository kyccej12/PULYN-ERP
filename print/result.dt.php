<?php
	session_start();
    //ini_set("display_errors","On");
	//include("../lib/mpdf6//mpdf.php");
	include("../handlers/_generics.php");
    include("../lib/mpdf6/mpdf.php");

	$con = new _init;

/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");

    list($file) = $con->getArray("select CONCAT('../',file_path) from lab_samples where so_no = '$_REQUEST[so_no]' and `code` = 'L047' and serialno = '$_REQUEST[serialno]';");
    $b = $con->getArray("SELECT * FROM lab_samples WHERE so_no = '$_REQUEST[so_no]' and `code` = 'L047' and serialno = '$_REQUEST[serialno]';");
    if($b['updated_by'] != '') {
        list($medtechSignature) = $con->getArray("SELECT if(signature_file != '',concat('<img src=\"../images/signatures/',signature_file,'\" align=absmiddle />'),'<img src=\"../images/signatures/blank.png\" align=absmiddle />') as signature from user_info where emp_id = '$b[updated_by]';");
    }
/* END OF SQL QUERIES */



// if($file != '') {
//     $mpdf->SetWatermarkImage($file,0.2,'',array(1,1));
//     $mpdf->showWatermarkImage = false;
// }
// if($counter>0) {
//     $mpdf->SetWatermarkImage('../images/signatures/delacerna_signature.png',1,'',array(40,95));
//     $mpdf->showWatermarkImage = true;
// }

$html = '


<html>
<head>
	<style>
		body {font-family: sans-serif; font-size: 8pt; }
        .itemHeader {
            padding:5px;border:1px solid black; text-align: center; font-weight: bold;
        }

        .itemResult {
            padding:10px;border:1px solid black;text-align: center;
        }

        #items td { border: 1px solid; text-align: center; }
	</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">

</htmlpageheader>

<htmlpagefooter name="myfooter">
<table width=100% cellpadding=5>
	<tr>
        <td align=center valign=top>'.$medtechSignature.'</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td valign=top><img src="../images/signatures/leyson2.png" align=absmidddle /></td>
	</tr>
</table>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<div id="main">
    <table width=60% cellpadding=0 cellspacing=0 align=center style="margin: 5px;">
        <tr><td align=center></td></tr>
    </table>
</div>
</body>
</html>
';

$mpdf=new mPDF('c','FOLIO-H','','',5,5,5,5,5,51);
$mpdf->setDisplaymode(100);
$mpdf->setImportUse();
$mpdf->WriteHTML($html);

$pagecount = $mpdf->setSourceFile($file);
$tplId = $mpdf->importPage($pagecount);
$mpdf->UseTemplate($tplId);

$mpdf->Output();
exit;

?>