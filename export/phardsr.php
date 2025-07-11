<?php
	session_start();
	ini_set("max_execution_time",0);
	//ini_set("display_errors","on");
	require_once '../lib/PHPExcel/PHPExcel.php';
	require_once '../handlers/_generics.php';

	$mydb = new _init;
	$dtf = $mydb->formatDate($_GET['dtf']);
	$dt2 = $mydb->formatDate($_GET['dt2']);
	$fs = '';

    //$query = $mydb->dbquery("SELECT * FROM (SELECT 'so' AS `type`, a.so_no, DATE_FORMAT(b.so_date, '%m/%d/%Y') AS so_date, '' AS si_no, '' AS sidate, b.customer_code, b.customer_name, b.patient_name, c.description AS xterms, a.code, a.description, a.amount, a.discount, ROUND(a.amount-a.discount,2) AS charge_sales, '0' AS cash_sales, '0' AS cc_sales FROM pharma_so_details a LEFT JOIN pharma_so_header b ON a.trace_no = b.trace_no LEFT JOIN options_terms c ON b.terms = c.terms_id WHERE b.status = 'Finalized' AND b.terms NOT IN ('0','100') AND b.so_date BETWEEN '$dtf' AND '$dt2' $fs UNION ALL SELECT 'so' AS `type`, a.so_no, DATE_FORMAT(b.so_date, '%m/%d/%Y') AS so_date, '' AS si_no, '' AS sidate, b.customer_code, b.customer_name, b.patient_name, c.description AS xterms, a.code, a.description, a.amount, a.discount, '0' AS charge_sales, '0' AS cash_sales, '0' AS cc_sales FROM pharma_so_details a LEFT JOIN pharma_so_header b ON a.trace_no = b.trace_no LEFT JOIN options_terms c ON b.terms = c.terms_id WHERE b.status= 'Finalized' AND b.terms IN ('100') AND b.so_date BETWEEN '$dtf' AND '$dt2' $fs UNION ALL SELECT 'si' AS `type`, a.so_no, DATE_FORMAT(b.doc_date,'%m/%d/%Y') AS so_date, b.si_no AS si_no, DATE_FORMAT(b.doc_date,'%m/%d/%Y') AS sidate, b.customer_code, b.customer_name, b.patient_name, 'CASH' AS terms, a.code, a.description, a.amount AS amount, a.discount, '0' AS charge_sales, ROUND(a.amount-a.discount,2) AS cash_sales, '0' AS cc_sales FROM pharma_si_details a LEFT JOIN pharma_si_header b ON a.trace_no = b.trace_no WHERE b.status= 'Finalized' AND b.doc_date BETWEEN '$dtf' AND '$dt2' $fs) a ORDER BY so_no, si_no;");
    $query = $mydb->dbquery("SELECT * FROM (SELECT 'so' AS `type`, a.so_no, DATE_FORMAT(b.so_date, '%m/%d/%Y') AS so_date, '' AS si_no, '' AS sidate, b.customer_code, b.customer_name, b.patient_name, c.description AS xterms, a.code, a.description, a.unit_price, a.qty, a.amount, a.discount, ROUND(a.amount-a.discount,2) AS charge_sales, '0' AS cash_sales FROM pharma_so_details a LEFT JOIN pharma_so_header b ON a.trace_no = b.trace_no LEFT JOIN options_terms c ON b.terms = c.terms_id WHERE b.status = 'Finalized' AND b.terms NOT IN ('0','100') AND b.so_date BETWEEN '$dtf' AND '$dt2' $fs UNION ALL SELECT 'so' AS `type`, a.so_no, DATE_FORMAT(b.so_date, '%m/%d/%Y') AS so_date, '' AS si_no, '' AS sidate, b.customer_code, b.customer_name, b.patient_name, c.description AS xterms, a.code, a.description, a.unit_price, a.qty, a.amount, a.discount, '0' AS charge_sales, '0' AS cash_sales FROM pharma_so_details a LEFT JOIN pharma_so_header b ON a.trace_no = b.trace_no LEFT JOIN options_terms c ON b.terms = c.terms_id WHERE b.status= 'Finalized' AND b.terms IN ('100') AND b.so_date BETWEEN '$dtf' AND '$dt2' $fs UNION ALL SELECT 'si' AS `type`, a.so_no, DATE_FORMAT(b.doc_date,'%m/%d/%Y') AS so_date, b.si_no AS si_no, DATE_FORMAT(b.doc_date,'%m/%d/%Y') AS sidate, b.customer_code, b.customer_name, b.patient_name, 'CASH' AS terms, a.code, a.description, a.unit_price, a.qty,  a.amount AS amount, a.discount, '0' AS charge_sales, ROUND(a.amount-a.discount,2) AS cash_sales FROM pharma_si_details a LEFT JOIN pharma_si_header b ON a.trace_no = b.trace_no WHERE b.status= 'Finalized' AND b.doc_date BETWEEN '$dtf' AND '$dt2' $fs) a ORDER BY so_no, si_no;");

	date_default_timezone_set('Asia/Manila');
	$now = date("m/d/Y h:i a");

	/* MYSQL QUERIES SECTION */
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
	/* END OF SQL QUERIES */

	$headerStyle = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
		'borders' => array('outline' => array('style' =>PHPExcel_Style_Border::BORDER_THIN)),
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
								 ->setTitle("$co[company_name] - Detailed Pharmacy Sales Report")
								 ->setSubject("$co[company_name] - Detailed Pharmacy Sales Report")
								 ->setDescription("$co[company_name] - Detailed Pharmacy Sales Report")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	

	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(48);
	$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension("N")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("O")->setAutoSize(true);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3",$co['tel_no']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","Detailed Pharmacy Sales Report Covered Period $_GET[dtf] to $_GET[dt2]");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A6","SO #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B6","SO DATE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C6","SI #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D6","SI DATE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E6","BILLED TO");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F6","PATIENT NAME");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G6","TERMS");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H6","CODE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I6","DESCRIPTION");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J6","UNIT PRICE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K6","QTY");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L6","AMOUNT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M6","DISCOUNT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("N6","CHARGE SALES");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("O6","CASH SALES");

	for($colheader = 0; $colheader <= 14; $colheader++) { $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($colheader,6)->applyFromArray($headerStyle); }

	$row = 7;
	while($data = $query->fetch_array()) {

		list($employer) = $mydb->getArray("select employer from patient_info where patient_id = '$row[patient_id]';");
		$charge = ''; $cash  = '';

		switch($data['type']) {
			case "so": if($data['so_no'] != $xso) {	$charge = $data['charge_sales']; $cash = ''; $chargeGT += $data['charge_sales']; } break;
			case "si": if($data['si_no'] != $xor) {	$cash = $data['cash_sales']; $charge = ''; $cashGT += $data['cash_sales']; } break;
		}

		if($data['customer_code'] != 0) { $billedto = html_entity_decode($data['customer_name']); } else { $billedto = 'Cash Walk-in Customer'; }

		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$data['so_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$data['so_date']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$data['si_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$data['sidate']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$data['customer_name']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,html_entity_decode($data['patient_name']));
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$data['xterms']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$data['code']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$data['description']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$data['unit_price'],2);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$row,$data['qty'],2);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$row,$data['amount'],2);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$row,$data['discount'],2);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,$row,$charge);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$row,$cash);
		/* NUMBER FORMAT */
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(9,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(11,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(12,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(13,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(14,$row)->getNumberFormat()->setFormatCode('#,##0.00');

		for($contentLoop = 0; $contentLoop <= 14; $contentLoop++) {
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($contentLoop,$row)->applyFromArray($contentStyle);
		}
		$row++; $xso = $data['si_no']; $xor = $data['so_no'];
	}


	/* TOTAL */
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,$row,$chargeGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$row,$cashGT);

	/* NUMBER FORMAT */
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(13,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(14,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(13,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(14,$row)->applyFromArray($totalStyle);
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->freezePane('A7');
	$objPHPExcel->getActiveSheet()->setTitle("Pharmacy Detailed Sales Report");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="pahrmacy-dsr.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>