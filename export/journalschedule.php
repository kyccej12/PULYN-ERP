<?php
	session_start();
	require_once '../lib/PHPExcel/PHPExcel.php';
	include("../handlers/_generics.php");
	$con = new _init;

	date_default_timezone_set('Asia/Manila');
	set_time_limit(0);

	switch($_GET['type']) { 
		case "CR": $lbl = "Collection Receipts Journal >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'CR' "; break;
		case "SI": $lbl = "Sales Journal >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'SI' "; break;
		case "CV": $lbl = "Cash/Check Disbursement Journal >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'CV' "; break;
		case "AP": $lbl = "Accounts Payable Journal >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'AP' "; break;
		case "JV": $lbl = "General (JV) Journal >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'JV' "; break;
		case "DA": $lbl = "Debit/Credit Advise Journal >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'JV' "; break;
		case "APB": $lbl = "Accounts Payable - Beginning Balance >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'APB' "; break;
		case "ARB": $lbl = "Accounts Receivable - Beginning Balance >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'ARB' "; break;
	}
	
	if($_GET['acct'] != '') { $f1 = " and a.acct = '$_GET[acct]' "; }


	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
		$query = $con->dbquery("SELECT doc_no, CONCAT(LPAD(branch,2,0),'-',LPAD(doc_no,5,0)) AS dno, DATE_FORMAT(doc_date,'%m/%d/%Y') AS dd8, a.acct, b.description, debit, credit, IF(doc_type IN ('SOA','CR'),CONCAT(d.lname,', ',d.fname,' (',d.tower,'-',d.tower_unit,')'),IFNULL(CONCAT('(',LPAD(contact_id,3,0),') ',c.tradename),'')) AS tradename, contact_id, CONCAT(a.branch,'-',doc_no) AS xdoc, doc_remarks FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code LEFT JOIN contact_info c ON a.contact_id=c.file_id LEFT JOIN homeowners d ON a.contact_id = d.record_id WHERE doc_date BETWEEN '".$con->formatDate($_GET['dtf'])."' AND '".$con->formatDate($_GET['dt2'])."' $f1 $f2 $f3 order by doc_no asc;");
	/* END OF SQL QUERIES */

	$headerStyle = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
		'borders' => array('outline' => array('style' =>PHPExcel_Style_Border::BORDER_THIN)),
	);
	
	$contentStyle = array(
		'borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);
	
	$totalStyle = array(
		'font' => array('bold' => true),
		'borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);
	
	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(9);
	$objPHPExcel->getProperties()->setCreator("Root Admin")
								 ->setLastModifiedBy("Root Admin")
								 ->setTitle("$co[company_name] - JOURNAL SCHEDULE")
								 ->setSubject("$co[company_name] - JOURNAL SCHEDULE")
								 ->setDescription("$co[company_name] - JOURNAL SCHEDULE")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3",$co['tel_no']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4",$co['website']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A5",$lbl);
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A7","DOC #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B7","DATE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C7","CLIENT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D7","GL ACCOUNT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E7","DEBIT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F7","CREDIT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G7","MEMO");
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(24);

	$objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('C7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('D7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('E7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('F7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('G7')->applyFromArray($headerStyle);

	$row = 8;
	while($data = $query->fetch_array()) {
		if($xdoc != $data['xdoc']) { $memo = $data['doc_remarks']; $dno = $data['dno']; $d8 = $data['dd8']; $cust = $data['tradename']; } else { $memo = ""; $dno = ""; $d8 = ""; $cust = ""; }
		$acct = "($data[acct]) $data[description]";

		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$dno);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$d8);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$cust);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$acct);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$data['debit']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$data['credit']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$memo);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(3,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(4,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(5,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row)->applyFromArray($contentStyle);
		$row++; $xdoc = $data['xdoc']; $dbGT+=$data['debit']; $crGT+=$data['credit'];
	}
	
	/* TOTAL */
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$dbGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$crGT);

	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(4,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(5,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(4,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(5,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("JOURNAL SCHEDULE - DETAILED");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="journalschedule.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>