<?php
	session_start();
	require_once '../lib/PHPExcel/PHPExcel.php';
	include("../handlers/_generics.php");
	$con = new _init;

	date_default_timezone_set('Asia/Manila');
	set_time_limit(0);
		

	switch($_GET['type']) { 
		case "CR": $lbl = "Collection Receipts Journal Summary >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'CR' "; break;
		case "SI": $lbl = "Sales Journal Summary >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'SI' "; break;
		case "CV": $lbl = "Cash/Check Disbursement Journal Summary >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'CV' "; break;
		case "AP": $lbl = "Accounts Payable Journal Summary >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'AP' "; break;
		case "JV": $lbl = "General (JV) Journal Summary >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'JV' "; break;
		case "DA": $lbl = "Debit/Credit Advise Journal Summary >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'JV' "; break;
		case "APB": $lbl = "Accounts Payable - Beginning Balance Summary >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'APB' "; break;
		case "ARB": $lbl = "Accounts Receivable - Beginning Balance Summary >> (Date Covered: $_GET[dtf] to $_GET[dt2])"; $f1 = "and a.doc_type = 'ARB' "; break;
	}
	
	if($_GET['acct'] != '') { $f1 = " and a.acct = '$_GET[acct]' "; }
	if($_GET['conso'] != "Y") { $f2 = " and a.branch = '$_SESSION[branchid]' "; }

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
		$bit = $con->getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
		$query = $con->dbquery("select a.acct, b.description, ROUND(sum(debit-credit),2) as amt from acctg_gl a left join acctg_accounts b on a.acct=b.acct_code and a.company=b.company where doc_date between '".$con->formatDate($_GET['dtf'])."' and '".$con->formatDate($_GET['dt2'])."' and a.company='$_SESSION[company]' $f1 $f2 group by a.acct;");
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
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3","$co[tel_no]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","$co[website]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A5",$lbl);
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A7","ACCT CODE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B7","ACCT DESCRIPTION");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C7","DEBIT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D7","CREDIT");
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);

	$objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('C7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('D7')->applyFromArray($headerStyle);

	$row = 8;
	while($data = $query->fetch_array()) {
		if($data['acct'] == "") { $acct = "--"; $desc = "UNCLASSIFIED ENTRY"; } else { $acct = $data['acct']; $desc = $data['description']; }
		if($data['amt'] > 0) { $db = $data['amt']; $cr = '0'; } else { $db = 0; $cr = abs($data['amt']); }
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$acct);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$desc);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$db);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$cr);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(3,$row)->applyFromArray($contentStyle);
		$row++;
	}
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("JOURNAL SCHEDULE - SUMMARY");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="journalschedule.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>