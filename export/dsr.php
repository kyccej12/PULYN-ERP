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

	if($_GET['item'] != '') { $fs = " and b.description like '%$_GET[item]%' "; }
	if($_GET['cid'] != '') { $fs .= " and a.customer_code = '$_GET[cid]' "; }
	//$query = $mydb->dbquery("SELECT * FROM (SELECT 'so' AS `type`, a.so_no, DATE_FORMAT(a.so_date,'%m/%d/%Y') AS sodate,'' AS or_no, '' AS ordate, a.customer_code, a.customer_name, a.patient_name, c.description AS xterms, b.code,b.description, b.amount_due, a.amount AS charge_sales, '0' AS cash_sales, '0' AS cc_sales FROM so_header a LEFT JOIN so_details b ON a.trace_no = b.trace_no LEFT JOIN options_terms c ON a.terms = c.terms_id WHERE a.status = 'Finalized' AND a.terms not in ('0','100') AND a.so_date BETWEEN '$dtf' AND '$dt2' $fs UNION ALL SELECT 'so' AS `type`, a.so_no, DATE_FORMAT(a.so_date,'%m/%d/%Y') AS sodate,'' AS or_no, '' AS ordate, a.customer_code, a.customer_name, a.patient_name, c.description AS xterms, b.code,b.description, b.amount_due, '0' AS charge_sales, '0' AS cash_sales, '0' AS cc_sales FROM so_header a LEFT JOIN so_details b ON a.trace_no = b.trace_no LEFT JOIN options_terms c ON a.terms = c.terms_id WHERE a.status = 'Finalized' AND a.terms in ('100') AND a.so_date BETWEEN '$dtf' AND '$dt2' $fs UNION ALL SELECT 'or' AS `type`, b.so_no, DATE_FORMAT(b.so_date,'%m/%d/%Y') AS so_date, a.or_no AS or_no, DATE_FORMAT(a.doc_date,'%m/%d/%Y') AS ordate, a.customer_code, a.customer_name, b.pname AS patient_name, 'Cash' AS terms, b.code, b.description, b.amount_due, 0 AS charge_sales, ROUND(a.gross-a.discount-a.sc_discount,2) AS cash_sales, '0' AS cc_sales FROM or_header a LEFT JOIN or_details b ON a.trace_no = b.trace_no WHERE a.status = 'Finalized' AND a.doc_date BETWEEN '$dtf' AND '$dt2' AND a.cashtype IN ('1','3') $fs UNION ALL SELECT 'cc' AS `type`, b.so_no, DATE_FORMAT(b.so_date,'%m/%d/%Y') AS so_date, a.doc_no AS or_no, DATE_FORMAT(a.doc_date,'%m/%d/%Y') AS ordate, a.customer_code, a.customer_name, b.pname AS patient_name, 'Cash' AS terms, b.code, b.description, b.amount_due, 0 AS charge_sales, '0' AS cash_sales, ROUND(a.gross-a.discount-a.sc_discount,2) AS cc_sales FROM or_header a LEFT JOIN or_details b ON a.trace_no = b.trace_no WHERE a.status = 'Finalized' AND a.doc_date BETWEEN '$dtf' AND '$dt2' AND a.cashtype IN (2) $fs) a ORDER BY or_no, so_no;");
	$query = $mydb->dbquery("SELECT * FROM (SELECT 'so' AS `type`, a.so_no, DATE_FORMAT(a.so_date,'%m/%d/%Y') AS sodate,'' AS or_no, '' AS ordate, a.customer_code, a.customer_name, a.patient_name, c.description AS xterms, b.code,b.description, b.amount_due,d.unit_cost, a.amount AS charge_sales, '0' AS cash_sales, '0' AS cc_sales FROM so_header a LEFT JOIN so_details b ON a.trace_no = b.trace_no LEFT JOIN options_terms c ON a.terms = c.terms_id LEFT JOIN services_master d ON b.code = d.code WHERE a.status = 'Finalized' AND a.terms NOT IN ('0','100') AND a.so_date BETWEEN '$dtf' AND '$dt2' $fs UNION ALL SELECT 'so' AS `type`, a.so_no, DATE_FORMAT(a.so_date,'%m/%d/%Y') AS sodate,'' AS or_no, '' AS ordate, a.customer_code, a.customer_name, a.patient_name, c.description AS xterms, b.code,b.description, b.amount_due,d.unit_cost, '0' AS charge_sales, '0' AS cash_sales, '0' AS cc_sales FROM so_header a LEFT JOIN so_details b ON a.trace_no = b.trace_no LEFT JOIN options_terms c ON a.terms = c.terms_id LEFT JOIN services_master d ON b.code = d.code WHERE a.status = 'Finalized' AND a.terms IN ('100') AND a.so_date BETWEEN '$dtf' AND '$dt2' $fs UNION ALL SELECT 'or' AS `type`, b.so_no, DATE_FORMAT(b.so_date,'%m/%d/%Y') AS so_date, a.or_no AS or_no, DATE_FORMAT(a.doc_date,'%m/%d/%Y') AS ordate, a.customer_code, a.customer_name, b.pname AS patient_name, 'Cash' AS terms, b.code, b.description, b.amount_due,c.unit_cost, 0 AS charge_sales, ROUND(a.gross-a.discount-a.sc_discount,2) AS cash_sales, '0' AS cc_sales FROM or_header a LEFT JOIN or_details b ON a.trace_no = b.trace_no LEFT JOIN services_master c ON b.code = c.code WHERE a.status = 'Finalized' AND a.doc_date BETWEEN '$dtf' AND '$dt2' AND a.cashtype IN ('1','3') $fs UNION ALL SELECT 'cc' AS `type`, b.so_no, DATE_FORMAT(b.so_date,'%m/%d/%Y') AS so_date, a.doc_no AS or_no, DATE_FORMAT(a.doc_date,'%m/%d/%Y') AS ordate, a.customer_code, a.customer_name, b.pname AS patient_name, 'Cash' AS terms, b.code, b.description, b.amount_due, c.unit_cost, 0 AS charge_sales, '0' AS cash_sales, ROUND(a.gross-a.discount-a.sc_discount,2) AS cc_sales FROM or_header a LEFT JOIN or_details b ON a.trace_no = b.trace_no LEFT JOIN services_master c ON b.code = c.code WHERE a.status = 'Finalized' AND a.doc_date BETWEEN '$dtf' AND '$dt2' AND a.cashtype IN (2) $fs) a ORDER BY or_no, so_no;");

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
								 ->setTitle("$co[company_name] - Detailed Sales Report")
								 ->setSubject("$co[company_name] - Detailed Sales Report")
								 ->setDescription("$co[company_name] - Detailed Sales Report")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	

	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(24);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(48);
	$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("N")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("O")->setAutoSize(true);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3",$co['tel_no']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","Detailed Sales Report Covering the Period $_GET[dtf] to $_GET[dt2]");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A6","SO #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B6","SO DATE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C6","OR #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D6","OR DATE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E6","BILLED TO");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F6","PATIENT NAME");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G6","EMPLOYER");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H6","TERMS");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I6","CODE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J6","DESCRIPTION");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K6","AMOUNT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L6","UNIT COST");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M6","CHARGE SALES");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("N6","CASH SALES");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("O6","CARD SALES");

	for($colheader = 0; $colheader <= 14; $colheader++) { $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($colheader,6)->applyFromArray($headerStyle); }

	$row = 7;
	while($data = $query->fetch_array()) {

		list($employer) = $mydb->getArray("select employer from patient_info where patient_id = '$row[patient_id]';");
		$charge = ''; $cash  = ''; $cc = '';

		switch($data['type']) {
			case "so": if($data['so_no'] != $xso) {	$charge = $data['charge_sales']; $cash = ''; $cc = ''; $chargeGT += $data['charge_sales']; } break;
			case "or": if($data['or_no'] != $xor) {	$cash = $data['cash_sales']; $charge = ''; $cc = ''; $cashGT += $data['cash_sales']; } break;
			case "cc":	if($data['or_no'] != $xor) { $cc = $data['cc_sales']; $charge = ''; $cash = ''; $ccGT += $data['cc_sales']; } break;
		}

		if($data['unit_cost'] != 0) { $costGT += $data['unit_cost']; }
		if($data['customer_code'] != 0) { $billedto = html_entity_decode($data['customer_name']); } else { $billedto = 'PATIENT'; }

		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$data['so_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$data['sodate']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$data['or_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$data['ordate']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$billedto);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,html_entity_decode($data['patient_name']));
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$employer);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$data['xterms']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$data['code']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$data['description']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$row,$data['amount_due']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$row,$data['unit_cost']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$row,$charge);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,$row,$cash);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$row,$cc);
		/* NUMBER FORMAT */
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(11,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(12,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(13,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(14,$row)->getNumberFormat()->setFormatCode('#,##0.00');

		for($contentLoop = 0; $contentLoop <= 14; $contentLoop++) {
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($contentLoop,$row)->applyFromArray($contentStyle);
		}
		$row++; $xso = $data['so_no']; $xor = $data['or_no'];
	}


	/* TOTAL */
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$row,$costGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$row,$chargeGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,$row,$cashGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$row,$ccGT);

	/* NUMBER FORMAT */
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(11,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(12,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(13,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(14,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(11,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(12,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(13,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(14,$row)->applyFromArray($totalStyle);
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->freezePane('A7');
	$objPHPExcel->getActiveSheet()->setTitle("Detailed Sales Report");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="dsr.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>