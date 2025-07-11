<?php
    session_start();
    //ini_set("display_errors","On");
	require_once("../lib/mpdf6/mpdf.php");
	require_once("../handlers/initDB.php");
    require_once("../handlers/_generics.php");

    $con = new _init;
    $_ihead = $con->getArray("SELECT LPAD(doc_no,6,0) AS docno, DATE_FORMAT(created_on, '%m/%d/%Y %h:%i:%s %p') AS dprocessed, or_no, date_format(doc_date,'%m/%d/%Y') as ddate, date_format(doc_date,'%M %d') as date1, date_format(doc_date,'%Y') as date2, customer_code, customer_name, customer_address, scpwd_id, gross, net_of_vat, ewt, vat, sc_discount, amount_due, cashtype, checkno, checkbank, cardtype FROM or_header WHERE doc_no = '$_REQUEST[doc_no]' and branch = '$_SESSION[branchid]';");
    $_ipatient = $con->getArray("SELECT pname from or_details WHERE doc_no = '$_REQUEST[doc_no]' AND branch = '$_SESSION[branchid]';");
    $_createdby = $con->getArray("SELECT fullname FROM user_info a LEFT JOIN or_header b ON a.emp_id = b.created_by WHERE doc_no = '$_REQUEST[doc_no]' AND branch = '$_SESSION[branchid]';");


    if($_ihead['customer_code'] == 0) {
        list($pname,$paddress) = $con->getArray("SELECT DISTINCT pname, paddr FROM or_details  WHERE doc_no = '$_REQUEST[doc_no]' AND branch = '$_SESSION[branchid]';");
    } else if($_ihead['checkbank'] != '') {
        $pname = $_ihead['customer_name'];
        $paddress = $_ihead['customer_address'];
        list($tin,$bizstyle) = $con->getArray("select tin_no, bizstyle from contact_info where file_id = '$_ihead[customer_code]';");
    } else {
        $pname = $_ihead['customer_name'];
        $pname2 = '/' . ' ' .$_ipatient['pname'];
        $paddress = $_ihead['customer_address'];
        list($tin,$bizstyle) = $con->getArray("select tin_no, bizstyle from contact_info where file_id = '$_ihead[customer_code]';");
    }
    
    list($digs,$fracs) = explode(".",$_ihead['amount_due']);
	if($fracs != '00') { $fracs = " & $fracs/100"; } else {	$fracs ='';}
	$word = $con->inWords($digs);

    $mpdf=new mPDF('win-1252','FOLIO-H','','',20,15,20,10,25,10);
    $mpdf->use_embeddedfonts_1252 = true;    // false is default
    $mpdf->SetProtection(array('print'));
    $mpdf->SetAuthor("Primecare Cebu");
    $mpdf->SetDisplayMode(50);

    $html = '
<html>
<head>
<title>Specimen Barcode</title>
<style>
body {
font-family: sans-serif;
font-size: 9pt;
}
.nside { 
font-size:8.5pt;
line-height:1;
}
.nside-2 {
    font-size:8pt;
    line-height:.5;
}    
.process {
    line-height:.5;
}
</style>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
    <table width=100% cellpaddding=0 cellspacing=0>
        <tr>
            <td colspan=4 align=right>REFERENCE NO. :'.$_ihead['docno'].'</td>
        </tr>
        <tr><td height=30></td></tr>
        <tr>
            <td colspan=2 width=60%>&nbsp;</td>
            <td></td><td align=right>'.$_ihead['date1'].',&nbsp;'.$_ihead['date2'].'</td>
        </tr>
        <tr><td height=5></tr>
        <tr>
            <td width=60% colspan=2 style="padding-left: 50px;">'.$pname.'&nbsp;&nbsp;'.$pname2.'</td>
            <td width=20% align=center></td>
            <td width=20% align=left></td>
        </tr>
        <tr><td height=5></tr>
        <tr>
            <td width=60% colspan=2 style="padding-left: 20px;">'.$paddress.'</td>
            <td align=right style="padding-right: 25px;">'.$bizstyle.'</td>
            <td align=right>'.$tin.'</td>
        </tr>
        <tr>
            <td colspan=4 align=left style="padding:4px;padding-left:60px;">'.$word.' PESOS ' . $fracs . ' ONLY ***&nbsp;&nbsp;(&#8369;'.number_format($_ihead['amount_due'],2).')</td>
        </tr>
        <tr><td height=50></td></tr>
        <tr><td></td><td></td><td style="font-weight:bold;"></td><td></td></tr>
    </table>
    <table width="100%" class="process" style="font-size: 11px; margin-top:14px; border-collapse: collapse;" cellpadding="3">
            <tbody>';

                    list($soCount) = $con->getArray("SELECT COUNT(so_no) FROM or_details WHERE doc_no = '$_REQUEST[doc_no]' and branch = '$_SESSION[branchid]';");
                    if($soCount > 6) {
                        $dQuery = $con->dbquery("SELECT COUNT(`code`) AS pax, description, unit_price,  qty AS qty, SUM(amount_due) AS amount, SUM(discount) AS discount FROM or_details WHERE doc_no = '$_REQUEST[doc_no]' AND branch = '$_SESSION[branchid]' GROUP BY `code`;");
                        
                        while($dRow = $dQuery->fetch_array()) {
                            $html .= '<tr>
                                <td width=58% class="nside-2" colspan=4>('. $dRow['pax'] . ' Pax) ' . $dRow ['description'] . '</td>
                                <td width=10% class="nside-2" align=right>'.number_format($dRow['unit_price'],2).'</td>
                                <td width=22% class="nside-2" align=center>'.number_format($dRow['qty'],2).'</td>
                                <td width=10% class="nside-2" align=right>'.number_format($dRow['amount'],2).'</td>
                            </tr>';
                        }
                    }   else {                             
                
                        $dQuery = $con->dbquery("SELECT LPAD(so_no,6,0) AS so, description, qty AS qty ,amount AS si_amount, unit_price AS unit_price FROM or_details WHERE doc_no = '$_REQUEST[doc_no]' AND branch = '$_SESSION[branchid]';");
                        while($dRow = $dQuery->fetch_array()) {
                            $html .= '<tr>
                                <td width=12% colspan=2>'.$dRow['so'].'</td>
                                <td width=48% align=left style="padding-left:20px;">'.$dRow['description'].'</td>
                                <td width=10% align=left>'.number_format($dRow['unit_price'],2).'</td>
                                <td width=20% align=left>'.number_format($dRow['qty'],2).'</td>
                                <td width=10% align=right>'.number_format($dRow['si_amount'],2).'</td>
                            </tr>';
                        }
                    }
                    $html = $html .  '<tr>
                            </tr>
                    </tbody>
            </table>
</htmlpageheader>
        <htmlpagefooter name="myfooter">
                                <table width=100% height=70 class="nside">
                                    <tr>
                                        <td width=33.3%>&nbsp;</td>
                                        <td width=33.3%>&nbsp;</td>
                                        <td width=33.3% align=right>'.number_format($_ihead['net_of_vat'],2).'</td>
                                    </tr>
                                    <tr>
                                        <td width=33.3% align=left style="padding-left:120px;">'.number_format($_ihead['net_of_vat'],2).'</td>
                                        <td width=33.3% align=right>'.number_format($_ihead['vat'],2).'</td>
                                        <td width=33.3% align=right>'.number_format($_ihead['sc_discount'],2).'</td>
                                    </tr>
                                    <tr>
                                        <td width=33.3% align=left style="padding-left:120px;">'.number_format($_ihead['gross'],2).'</td>
                                        <td width=33.3% align=right>'.number_format($_ihead['gross'],2).'</td>
                                        <td width=33.3% align=right>'.number_format($_ihead['amount_due'],2).'</td>
                                    </tr>
                                    <tr>
                                        <td width=33.3% align=left style="padding-left:120px;">'.number_format($_ihead['zero_rated'],2).'</td>
                                        <td width=33.3% align=right>'.number_format($_ihead['vat'],2).'</td>
                                        <td width=33.3% align=right>0.00</td>
                                    </tr>
                                    <tr>
                                        <td width=33.3%>&nbsp;</td>
                                        <td width=33.3% align=right>EWT:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_ihead['ewt'].'</td>
                                        <td width=33.3% align=center style="padding-left:80px;font-weight:bold;">'.number_format($_ihead['amount_due'],2).'</td>
                                    </tr>
                                    <tr><td></td></tr>
                                    <tr>
                                        <td align=left>&nbsp;</td>
                                        <td align=right>&nbsp;</td>
                                        <td align=right>&nbsp;</td>
                                    </tr>
                                     <tr>';
                                     
                                     list($checkAmount,$checkNum,$checkBank) = $con->getArray("select gross,checkno,checkbank from or_header where doc_no = '$_ihead[docno]';");
                                    if($checkNum != 0) {
                                       $html .= '<tr>
                                       
                                        <td align=left style="padding-left:25px";>'.$checkAmount.'</td>
                                        <td align=left style="padding-left:15px";>'.$checkBank.' '.$checkNum.'</td>';
                                    }
                                    $html .='</tr>
                                    <tr><td></td><td align=right>'.$_ihead['scpwd_id'].'</td>&nbsp;&nbsp;&nbsp;<td align=right>'.$_createdby['fullname'].'</td></tr>
                                    <tr><td align-right></td></tr>
                                     <tr><td height=73></td></tr>
                                     <tr><td align=right colspan=4>Run Date: '.$_ihead['dprocessed'].'</td></tr>';
                                     $html .='</table>
                            </htmlpagefooter>
                        <sethtmlpageheader name="myheader" value="on" show-this-page="1" />
                        <sethtmlpagefooter name="myfooter" value="on" />
                    mpdf-->
                </body>
            </html>
';

$html = html_entity_decode($html);
$mpdf->WriteHTML($html);
$mpdf->Output(); exit;
exit;

mysql_close($con);
?>