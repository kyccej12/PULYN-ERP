<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("display_errors","on");
	require_once '../lib/PHPExcel/PHPExcel.php';
	require_once '../handlers/_generics.php';

	$mydb = new _init;

	$query = $mydb->dbquery("SELECT LPAD(a.file_id,6,0) AS cid, b.contacttype AS `type`, a.tradename AS compname, a.address AS addr, a.bizstyle, tin_no, a.tel_no, a.email, cperson FROM contact_info a LEFT JOIN options_ctype b ON a.type = b.id WHERE a.record_status != 'Deleted' GROUP BY a.tradename ORDER BY tradename ASC");

    
	date_default_timezone_set('Asia/Manila');
	$now = date("m/d/Y h:i a");

	/* MYSQL QUERIES SECTION */
		$co = $mydb->getArray("select * from companies where company_id = '1';");
	/* END OF SQL QUERIES */

	$headerStyle = array(
		'font' => array('bold' => true,'color' => array('rgb' => 'FFFFFF')),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
		'borders' => array('outline' => array('style' =>PHPExcel_Style_Border::BORDER_THIN)),
		'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '514DC4'))
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
								 ->setTitle("$co[company_name] - CUSTOMER LIST")
								 ->setSubject("$co[company_name] - CUSTOMER LIST")
								 ->setDescription("$co[company_name] - CUSTOMER LIST")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");

	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setName('test_img');
	$objDrawing->setDescription('test_img');
	$objDrawing->setPath('../images/doc-header-1.jpg');
	$objDrawing->setCoordinates('E1');                      
	//setOffsetX works properly
	$objDrawing->setOffsetX(5); 
	$objDrawing->setOffsetY(5);                
	//set width, height
	$objDrawing->setWidth(72); 
	$objDrawing->setHeight(72); 
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
	

	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A7","Customer/Supplier/Payees List Summary");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A9","NO");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B9","CUSTOMER ID");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C9","TYPE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D9","CUSTOMER/SUPPLIER NAME");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E9","ADDRESS");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F9","BUSINESS STYLE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G9","TIN NO.");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H9","TEL NO.");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I9","EMAIL");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J9","CONTACT PERSON");

	for($colheader = 0; $colheader <= 9; $colheader++) { $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($colheader,9)->applyFromArray($headerStyle); }

	$row = 10; $rows = 1;
	while($data = $query->fetch_array()) {

		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$rows);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$data['cid']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$data['type']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,html_entity_decode($data['compname']));
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,html_entity_decode($data['addr']));
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$data['bizstyle']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$data['tin_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$data['tel_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$data['email']);		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$data['cperson']);

		for($contentLoop = 0; $contentLoop <= 9; $contentLoop++) {
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($contentLoop,$row)->applyFromArray($contentStyle);
		}
		$row++; $rows++;
	}
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->freezePane('A10');
	$objPHPExcel->getActiveSheet()->setTitle("Customer Supplier Payees List");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="address_book.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>