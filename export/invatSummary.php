<?php
	session_start();
	set_time_limit(0);
	require_once '../lib/PHPExcel/PHPExcel.php';
	require_once '../handlers/_generics.php';
	
	$mydb = new _init;
		
	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
		$dtf = $mydb->formatDate($_GET['dtf']); $dt2 = $mydb->formatDate($_GET['dt2']);
		$query = $mydb->dbquery("SELECT * FROM (SELECT CONCAT('CV-',a.cv_no) AS doc_no, a.cv_date AS docdate, DATE_FORMAT(a.cv_date,'%m/%d/%Y') AS dd8, IF(b.supplier_name='',a.payee,b.supplier) AS payee, IF(b.supplier_name='',a.payee_name,b.supplier_name) AS payeename, supplier_address, supplier_tin, invoice_no, DATE_FORMAT(invoice_date,'%m/%d/%Y') AS idate, ROUND(b.net_payable+b.ewt_amount,2) AS gross, b.input_vat, b.ewt_amount AS ewt, ROUND(b.net_payable,2) AS net FROM cv_header a INNER JOIN cv_subheader b ON a.cv_no = b.cv_no AND a.branch = b.branch WHERE a.branch = '1' AND a.cv_date BETWEEN '$dtf' AND '$dt2' AND a.status = 'Posted' UNION ALL SELECT CONCAT('APV-',a.apv_no) AS doc_no, a.apv_date AS docdate, DATE_FORMAT(a.apv_date,'%m/%d/%Y') AS dd8, a.supplier AS payee, a.supplier_name AS payeename, '' AS supplier_address, '' AS supplier_tin, b.invoice_no, DATE_FORMAT(invoice_date,'%m/%d/%Y') AS idate, ROUND(b.net_payable+b.ewt_amount,2) AS gross, b.input_vat, b.ewt_amount AS ewt, ROUND(b.net_payable,2) AS net FROM apv_header a INNER JOIN apv_subheader b ON a.apv_no = b.apv_no AND a.branch = b.branch WHERE a.branch = '1' AND a.apv_date BETWEEN '$dtf' AND '$dt2' AND a.status = 'Posted' UNION ALL SELECT CONCAT('JV-',a.j_no) AS doc_no, a.j_date AS docdate, DATE_FORMAT(a.j_date,'%m/%d/%Y') AS dd8, b.supplier AS payee, b.supplier_name AS payeename, b.supplier_address, b.supplier_tin, invoice_no, DATE_FORMAT(invoice_date,'%m/%d/%Y') AS idate, ROUND(b.net_payable+b.ewt_amount,2) AS gross, b.input_vat, b.ewt_amount AS ewt, ROUND(b.net_payable-b.ewt_amount,2) AS net FROM journal_header a INNER JOIN journal_invoices b ON a.j_no = b.j_no AND a.branch = b.branch WHERE a.branch = '1' AND a.j_date BETWEEN '$dtf' AND '$dt2' AND a.status = 'Posted') a WHERE 1=1 ORDER BY docdate ASC, doc_no ASC;");
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
	$objPHPExcel->getProperties()->setCreator("Medgruppe Polyclinics & Diagnostic Center, Inc.")
								 ->setLastModifiedBy("Medgruppe Polyclinics & Diagnostic Center, Inc.")
								 ->setTitle("Medgruppe Polyclinics & Diagnostic Center, Inc. - Vatable Purchases Summary")
								 ->setSubject("Medgruppe Polyclinics & Diagnostic Center, Inc. - Vatable Purchases Summary")
								 ->setDescription("Medgruppe Polyclinics & Diagnostic Center, Inc. - Vatable Purchases Summary")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3",$co['tel_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A5","Summary of Vatable Purchases ($_GET[dtf] - $_GET[dt2]");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A7","DOC #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B7","DOC DATE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C7","REF NO");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D7","REF DATE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E7","PAYEE/SUPPLIER");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F7","ADDRESS");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G7","T-I-N #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H7","GROSS");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I7","V-A-T");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J7","EWT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K7","NET");
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F7")->setWidth(32);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);

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

	$row = 8;
	while($data = $query->fetch_array(MYSQLI_BOTH)) {
		if($data['payee'] != 0) {
			$ttt = $mydb->getArray("SELECT tin_no, address, brgy, city, province FROM contact_info WHERE file_id = '$row[payee]';");
			list($brgy) = $mydb->getArray("SELECT brgyDesc FROM options_brgy WHERE brgyCode = '$ttt[brgy]';");
			list($ct) = $mydb->getArray("SELECT cityMunDesc FROM options_cities WHERE cityMunCode = '$ttt[city]';");
			list($prov) = $mydb->getArray("SELECT provDesc FROM options_provinces WHERE provCode = '$ttt[province]';");
			$data['supplier_tin'] = $ttt['tin_no']; $data['supplier_address'] =  $ttt['address'] . ',' . $brgy .','.$ct.','.$prov;
		}
		
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$data['doc_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$data['dd8']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$data['invoice_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$data['idate']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$data['payeename']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$data['supplier_address']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$data['supplier_tin']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$data['gross']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$data['input_vat']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$data['ewt']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$row,$data['net']);
		
		
		for($ix=0;$ix<=10;$ix++) { $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($ix,$row)->applyFromArray($contentStyle); }
		for($ixy=7;$ixy<=10;$ixy++) { $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($ixy,$row)->getNumberFormat()->setFormatCode('#,##0.00'); }
		$row++; $grossGT+=$data['gross']; $inputGT+=$data['input_vat']; $ewtGT+=$data['ewt']; $netGT+=$data['net']; $brgy = ""; $ct = ""; $prov = "";
	}
	
	/* TOTAL */
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$grossGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$inputGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$ewtGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$row,$netGT);

	for($ixt=7;$ixt<=10;$ixt++) {
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($ixt,$row)->applyFromArray($totalStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($ixt,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	}

	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("Invoice Summary");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="inputvatsummary.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>