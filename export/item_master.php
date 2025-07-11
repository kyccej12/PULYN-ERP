<?php

	session_start();
	set_time_limit(0);
	ini_set("memory_limit", "5024M");
	require_once '../lib/PHPExcel/PHPExcel.php';
	include("../includes/dbUSE.php");

	date_default_timezone_set('Asia/Manila');
	set_time_limit(0);

	
	if($_GET['acct'] != '') { $f1 = " and a.acct = '$_GET[acct]' "; }
	if($_GET['conso'] != "Y") { $f2 = " and a.branch = '$_SESSION[branchid]' "; }

	if(isset($_REQUEST['group']) && $_REQUEST['group']!='' ){
		$filterg = " AND a.group = '$_REQUEST[group]' ";
	}else{
		$filterg = "";
	}
	/* MYSQL QUERIES SECTION */
		
		$query = dbquery("SELECT item_code,indcode,description,full_description,group_description,subgroup_description,unit,unit_cost,srp,walkin_price,
							unit_price1,unit_price2,unit_price3,unit_price4,unit_price5,unit_price6,unit_price7,unit_price8,
							price_a,price_b,price_c,price_aaa,price_bbb,price_ccc,price_proj,price_ox
						  FROM cebuglass.products_master a LEFT JOIN cebuglass.options_igroup b ON a.group = b.group LEFT JOIN cebuglass.options_isgroup c ON a.sgroup = c.subgroup_id WHERE 1 = 1 $filterg;");
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
								 ->setTitle("$co[company_name] - STOCKCARD")
								 ->setSubject("$co[company_name] - STOCKCARD")
								 ->setDescription("$co[company_name] - STOCKCARD")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1","CEBU GLASS ALUMINUM PALACE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2","");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3","");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","Product List");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A5","");
	

	/* 0 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A7","ITEM CODE");
	/* 1 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B7","Description");
	/* 2 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C7","Stock Code");
	/* 3 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D7","INVENTORY GROUP");
	/* 4 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("E7","SUB GROUP");
	/* 5 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("F7","UNIT");
	/* 6 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("G7","UNIT COST");
	/* 7 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("H7","PRICE LIST");
	/* 8 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("I7","WALK IN PRICE");
	/* 9 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("J7","A(%)");
	/* 10 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("K7","PRICE LEVEL (A)");
	/* 11 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("L7","B(%)");
	/* 12 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("M7","PRICE LEVEL (B)");
	/* 13 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("N7","C(%)");
	/* 14 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("O7","PRICE LEVEL (C)");
	/* 15 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("P7","AAA(%)");
	/* 16 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("Q7","PRICE LEVEL (AAA)");
	/* 17 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("R7","BBB(%)");
	/* 18 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("S7","PRICE LEVEL (BBB)");
	/* 19 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("T7","CCC(%)");
	/* 20 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("U7","PRICE LEVEL (CCC)");
	/* 21 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("V7","PROJECT(%)");
	/* 22 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("W7","PROJECT");
	/* 23 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("X7","OX(%))");
	/* 24 */ $objPHPExcel->setActiveSheetIndex(0)->setCellValue("Y7","OX");

	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("N")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("O")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("P")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("R")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("S")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("T")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("U")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("V")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("W")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("X")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setAutoSize(true);

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
	$objPHPExcel->getActiveSheet()->getStyle('L7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('M7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('N7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('O7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('P7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('Q7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('R7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('S7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('T7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('U7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('V7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('W7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('X7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('Y7')->applyFromArray($headerStyle);

	$row = 8;
	while($data = mysql_fetch_array($query)) {
		//list($igroup) = getArray("select group_description from options_igroup where `group`='$data[group]';");
							
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$data['item_code']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$data['full_description']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$data['indcode']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$data['group_description']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$data['subgroup_description']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$data['unit']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$data['unit_cost']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$data['srp']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$data['walkin_price']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$data['price_a']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$row,$data['unit_price4']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$row,$data['price_b']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$row,$data['unit_price5']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,$row,$data['price_c']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$row,$data['unit_price6']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15,$row,$data['price_aaa']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(16,$row,$data['unit_price1']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(17,$row,$data['price_bbb']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(18,$row,$data['unit_price2']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(19,$row,$data['price_ccc']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(20,$row,$data['unit_price3']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(21,$row,$data['price_proj']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(22,$row,$data['unit_price7']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(23,$row,$data['price_ox']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(24,$row,$data['unit_price8']);

		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(3,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(4,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(5,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(9,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(11,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(12,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(13,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(14,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(15,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(16,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(17,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(18,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(19,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(20,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(21,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(22,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(23,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(24,$row)->applyFromArray($contentStyle);

		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(9,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(11,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(12,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(13,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(14,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(15,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(16,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(17,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(18,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(19,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(20,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(21,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(22,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(23,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(24,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$row++;
	}
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("Product List");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="productlist.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>