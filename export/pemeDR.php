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

    if($_GET['cid'] != '') { $fs .= " and b.customer_code = '$_GET[cid]' "; }


    $query = $mydb->dbquery("SELECT 'so' AS `type`,a.so_no, a.branch, DATE_FORMAT(a.so_date,'%m/%d/%Y') AS so_date, a.clinic AS clinic_no, a.procedure, b.customer_code, b.customer_name, a.pid AS patient_id, b.patient_name, CONCAT(b.terms,' ','Days') AS terms, a.code, a.examined_by, CONCAT(c.fullname,', ',c.prefix) AS examined_by, a.evaluated_by, CONCAT(d.fullname,', ',d.prefix) AS evaluated_by, e.description, e.unit_price FROM peme a LEFT JOIN so_header b ON a.so_no = b.so_no LEFT JOIN options_doctors c ON a.examined_by = c.id LEFT JOIN options_doctors d ON a.evaluated_by = d.id LEFT JOIN services_master e ON a.code = e.code WHERE 1=1 AND a.examined_by > 0 AND evaluated_by > 0 AND a.so_date BETWEEN '$dtf' AND '$dt2' $fs ORDER BY so_no;");
   
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
								 ->setTitle("$co[company_name] - PEME DETAILED REPORT")
								 ->setSubject("$co[company_name] - PEME DETAILED REPORT")
								 ->setDescription("$co[company_name] - PEME DETAILED REPORT")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	

	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(34);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(34);
	$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(16);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3",$co['tel_no']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","PEME DETAILED REPORT Covering the Period $_GET[dtf] to $_GET[dt2]");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A6","SO #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B6","SO DATE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C6","CLINIC #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D6","BILLED TO");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E6","PATIENT NAME");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F6","PROCEDURE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G6","TERMS");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H6","CODE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I6","EXAMINED BY");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J6","EVALUATED BY");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K6","AMOUNT");


	for($colheader = 0; $colheader <= 10; $colheader++) { $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($colheader,6)->applyFromArray($headerStyle); }

	$row = 7; $total= 0; $amountGT = 0;
	while($data = $query->fetch_array()) {

		$ptotal =''; $atotal = '';

		if($data['customer_code'] == 0) { $data['customer_name'] = 'Walk-in Customer'; }

        if($data['so_no'] != $paymaya) { $ptotal = number_format($data['branch']); $ptotal=''; $total += $data['branch']; }
        if($data['unit_price'] != $shopee) { $atotal = number_format($data['unit_price'],2); $atotal= ''; $amountGT += $data['unit_price']; }

        if($data['terms'] == 0) { $data['terms'] = 'Cash'; }

		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$data['so_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$data['so_date']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$data['clinic_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$data['customer_name']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,html_entity_decode($data['patient_name']));
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$data['procedure']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$data['terms']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$data['code']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$data['examined_by']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$data['evaluated_by']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$row,$data['unit_price']);

		/* NUMBER FORMAT */
		// $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	
		for($contentLoop = 0; $contentLoop <= 10; $contentLoop++) {
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($contentLoop,$row)->applyFromArray($contentStyle);
		}
		$row++; $paymaya = $data['branch']; $shopee = $data['so_no'];
	}

    $text = "TOTAL PATIENT EVALUATED";
	/* TOTAL */
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$text);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$total);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$row,$amountGT);

	/* NUMBER FORMAT */
    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(9,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(9,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10,$row)->applyFromArray($totalStyle);
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->freezePane('A7');
	$objPHPExcel->getActiveSheet()->setTitle("PEME DETAILED REPORT");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="pemedr.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>