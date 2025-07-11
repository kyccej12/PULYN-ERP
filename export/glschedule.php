<?php
	session_start();
	ini_set("max_execution_time",0);
	//ini_set("display_errors","on");
	require_once '../lib/PHPExcel/PHPExcel.php';
	require_once '../handlers/_generics.php';

	$mydb = new _init;

	date_default_timezone_set('Asia/Manila');
	$now = date("m/d/Y h:i a");

	/* MYSQL QUERIES SECTION */
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
		if($_GET['acct'] != '') { $f2 = " and a.acct = '$_GET[acct]' "; }
	/* END OF SQL QUERIES */

		$headerStyle = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
	);

	$headerStyle2 = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
		'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);
	
	$headerStyle3 = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
	);
	
	$totalStyle = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
	);
	
	$contentStyle = array(
		'borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);
	
	$totalStyle = array(
		'font' => array('bold' => true),
		'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),'bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))
	);
	
	
	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(9);
	$objPHPExcel->getProperties()->setCreator("Root Admin")
								 ->setLastModifiedBy("Root Admin")
								 ->setTitle("$co[company_name] - GL ACCOUNT SCHEDULE")
								 ->setSubject("$co[company_name] - GL ACCOUNT SCHEDULE")
								 ->setDescription("$co[company_name] - GL ACCOUNT SCHEDULE")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3",$co['tel_no']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4",$co['website']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A5",$lbl);
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A7","DATE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B7","TRANS. #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C7","JOURNAL");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D7","CLIENT/SUPPLIER");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E7","TIN #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F7","REFERENCE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G7","DEBIT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H7","CREDIT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I7","BALANCE");
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(24);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(48);
	$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
	

	$objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('C7')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('D7')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('E7')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('F7')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('G7')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('H7')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('I7')->applyFromArray($headerStyle2);

	$row = 8;
	
	if($_GET['acct'] != '') { $f1 = " and a.acct = '$_GET[acct]' "; }
	$query = $mydb->dbquery("SELECT DISTINCT acct, b.description FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct = b.acct_code where 1=1 and a.doc_date <= '".$mydb->formatDate($_GET['dt2'])."' $f1 ORDER BY acct");
	while($data = $query->fetch_array(MYSQLI_BOTH)) {
		$row++;
		list($acctBB) = $mydb->getArray("select sum(debit-credit) as amount from acctg_gl where doc_date < '".$mydb->formatDate($_GET['dtf'])."' AND acct = '$data[acct]';");
		if(!$acctBB) { $acctBB = 0; }
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"($data[acct]) $data[description]");
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($headerStyle);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,"Account Beginning Balance");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$acctBB);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->applyFromArray($headerStyle3);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->applyFromArray($totalStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		
		
		$dbGT = 0; $crGT = 0; $row++;
		$inQuery = $mydb->dbquery("SELECT doc_no, CONCAT(cy,'-',LPAD(doc_no,5,0)) AS dno, DATE_FORMAT(doc_date,'%m/%d/%Y') AS dd8, doc_type, debit, credit, IFNULL(CONCAT('(',LPAD(contact_id,3,0),') ',c.tradename),'') AS tradename, c.tin_no, doc_remarks FROM acctg_gl a LEFT JOIN contact_info c ON a.contact_id=c.file_id WHERE doc_date BETWEEN '".$mydb->formatDate($_GET['dtf'])."' AND '".$mydb->formatDate($_GET['dt2'])."' AND a.acct = '$data[acct]' order by a.doc_date asc, a.doc_type, a.doc_no asc;");
		while($inrow = $inQuery->fetch_array(MYSQLI_BOTH)) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$inrow['dd8']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$inrow['dno']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$inrow['doc_type']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,html_entity_decode($inrow['tradename']));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$inrow['tin_no']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$inrow['doc_remarks']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$inrow['debit']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$inrow['credit']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,"");
			
			/* NUMBER FORMAT */
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row)->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->getNumberFormat()->setFormatCode('#,##0.00');
			
			$dbGT+=$inrow['debit']; $crGT+=$inrow['credit']; $row++;
			
		}

		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,"Account Subtotals");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$dbGT);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$crGT);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(5,$row)->applyFromArray($headerStyle3);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row)->applyFromArray($totalStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->applyFromArray($totalStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		
		$row++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,"Account Net Change");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,($dbGT-$crGT));
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->applyFromArray($headerStyle3);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->applyFromArray($totalStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		
		
		$row++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,"Account Ending Balance");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,($acctBB + ($dbGT-$crGT)));
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->applyFromArray($headerStyle3);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->applyFromArray($totalStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		
	}


	/* TOTAL */
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$dbGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$crGT);

	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->freezePane('A8');
	$objPHPExcel->getActiveSheet()->setTitle("GL ACCOUNT SCHEDULE");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="glsched.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>