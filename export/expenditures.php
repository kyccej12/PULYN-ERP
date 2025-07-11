<?php
	session_start();
	require_once '../lib/PHPExcel/PHPExcel.php';
	include("../handlers/_generics.php");
	$con = new _init;

	date_default_timezone_set('Asia/Manila');
	set_time_limit(0);
		
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
	
	$headerStyle = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
		'borders' => array('outline' => array('style' =>PHPExcel_Style_Border::BORDER_THIN)),
	);
	
	$contentStyle = array(
		'borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);
	
	$contentStyle0 = array(
		'font' => array('bold' => true),
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
								 ->setTitle("Medgruppe Polyclinics & Diagnostic Center, Inc. - Summary of Purchases & Expenditures")
								 ->setSubject("Medgruppe Polyclinics & Diagnostic Center, Inc. - Summary of Purchases & Expenditures")
								 ->setDescription("Medgruppe Polyclinics & Diagnostic Center, Inc. - Summary of Purchases & Expenditures")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Expored File");
	
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(60);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setAutoSize(true);
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3",$co['tel_no']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4",$co['website']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A5","Summary of Purchases & Expenditures");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A6","Covering the Period $_GET[dtf] to $_GET[dt2]");
	
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A7","DOC #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B7","TYPE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C7","DATE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D7","S.I/DR #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E7","PAYEE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F7","NET OF VAT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G7","V-A-T");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H7","W/TAX");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I7","NET PAYABLE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J7","GROSS AMOUNT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K7","Y-T-D AMOUNT");



	$objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('C7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('D7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('E7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('F7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('G7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('H7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('I7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('J7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('K7')->applyFromArray($headerStyle);
	

	/* Main Query */
	
	list($ydtf) = $con->getArray("SELECT CONCAT(DATE_FORMAT('".formatDate($_GET['dt2'])."','%Y'),'-01-01')");
	$query = $con->dbquery("SELECT DISTINCT a.acct, b.description FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct = b.acct_code WHERE b. acct_grp IN ('3','12') AND doc_date BETWEEN '".$con->formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' ORDER BY b.description");

	$row = 8;
	while($data = $query->fetch_array()) {
	
		list($ytd) = $con->getArray("select ifnull(sum(debit-credit),0) from acctg_gl where acct = '$data[acct]' and doc_date between '$ydtf' and '".formatDate($_GET['dt2'])."';");
	
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"$data[description] ($data[acct])");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$row,$ytd);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle0);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10,$row)->applyFromArray($contentStyle0);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$row++;
	
		$inQuery = $con->dbquery("SELECT doc_no, doc_date, doc_type, sum(debit) as amount, c.tradename, c.vatable FROM acctg_gl a LEFT JOIN contact_info c on a.contact_id = c.file_id WHERE doc_date BETWEEN '".formatDate($_GET['dtf'])."' AND '".formatDate($_GET['dt2'])."' AND acct = '$data[acct]' group by doc_no, doc_type, a.acct order by a.doc_date, a.doc_no;");
		while($inRow = $inQuery->fetch_array()) {
			
			$ewt = 0; $acctewt = 0; $nvat = 0;
			
			switch($inRow['doc_type']) {
				case "AP":
					list($refno) = $con->getArray("select ref_no from apv_details where apv_no = '$inRow[doc_no]' and acct = '$data[acct]';");
				break;
				case "CV":
					list($refno) = $con->getArray("select ref_no from cv_details where cv_no = '$inRow[doc_no]' and acct = '$data[acct]';");
				break;
			}
		
			
			
			list($ewt) = $con->getArray("select ifnull(sum(credit),0) from acctg_gl where doc_no = '$inRow[doc_no]' and doc_type = '$inRow[doc_type]' and acct = '30207';");		
			
			/* Get EWT RATE */
			if($ewt > 0) {
				
				list($docgross) = $con->getArray("select sum(debit) from acctg_gl where doc_no = '$inRow[doc_no]' and doc_type = '$inRow[doc_type]';");
				
				if($inRow['vatable'] == "Y") {
					$nvat = ROUND($docgross / 1.12,2);
					$vat = ROUND($nvat * 0.12,2);
					$erate = $ewt/$nvat;
					$acctewt = ROUND($inRow['amount'] / 1.12 * $erate,2);
				}
			}	
			
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$inRow['doc_no']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$inRow['doc_type']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$inRow['doc_date']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$refno);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$inRow['tradename']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$inRow['amount']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$vat);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$acctewt);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,($inRow['amount']-$acctewt));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$inRow['amount']);
			for($i = 0; $i <= 9; $i++) {
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($i,$row)->applyFromArray($contentStyle);
				if($i > 4) {
					$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($i,$row)->getNumberFormat()->setFormatCode('#,##0.00');
					
				}
			}
			$row++; $nvgt+=$inRow['amount']; $vatGT+=$vat; $ewtGT+=$acctewt; $netGT+=($inRow['amount']-$acctewt); $grossGT+=$inRow['amount']; 
		}
		
		$ytdGT+=$ytd;
	}
	
	/* TOTAL */
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$nvgt);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$vatGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$ewtGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$netGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$grossGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$row,$ytdGT);

	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(5,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(9,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(5,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(9,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("PURCHASES & EXPENDITURES");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="summaryofexpenditures.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>