<?php
	session_start();
	ini_set("memory_limit","1024M");
	ini_set("max_execution_time",0);
	//ini_set("display_errors","On");

	require_once "../../handlers/initDB.php";
	$pay = new myDB;
	
	/* MYSQL QUERY */
		$now = date("m/d/Y h:i a");
		
		if($_GET['proj'] != "") { $fs = " and a.proj = '$_GET[proj]' "; }
		$co = $pay->getArray("select * from companies where company_id = '$_SESSION[company]';");
		list($xmonth) = $pay->getArray("SELECT DATE_FORMAT('$_GET[year]-$_GET[month]-01','%M %Y');");
		
	/* END OF MYSQL */
		
	include("../../lib/PHPExcel/PHPExcel.php");
	date_default_timezone_set('Asia/Manila');
	set_time_limit(0);
		
	$headerStyle = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
		'borders' => array('outline' => array('style' =>PHPExcel_Style_Border::BORDER_THIN)),
	);
	
	$contentStyle = array(
		'borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);
	
	$signSpace = array(
		'borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);
	
	$totalStyle = array(
		'font' => array('bold' => true),
		'borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);
	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(9);
	$objPHPExcel->getProperties()->setCreator("Payroll Master")
								 ->setLastModifiedBy("Payroll Master")
								 ->setTitle("$co[company_name] - STATUTORY")
								 ->setSubject("$co[company_name] - STATUTORY")
								 ->setDescription("$co[company_name] - STATUTORY")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1","$co[company_name]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2","$co[company_address]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3","$co[tel_no]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","STATUTORY DEDUCTIONS FOR THE PERIOD $xmonth");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A6","ID NO.");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B6","EMPLOYEE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C6","DEPARTMENT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D6","SSS ID");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E6","HDMF ID");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F6","PHIC ID");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G6","SSS (EE)");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H6","SSS (ER + EC)");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I6","HDMF (EE)");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J6","HDMF (ER)");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K6","PHIC (EE)");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L6","PHIC (ER)");

	
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(16);
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
	$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setAutoSize(true);

	
	$objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('B6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('C6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('D6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('E6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('F6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('G6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('H6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('I6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('J6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('K6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('L6')->applyFromArray($headerStyle);

	
	$row = 7;

	$a = $pay->dbquery("SELECT a.emp_id, CONCAT(b.lname, ', ',b.fname, ' ',LEFT(mname,1),'.') AS emp_name, c.dept_name, b.sss_no, b.hdmf_no, b.phealth_no, SUM(a.sss_premium) AS sss_premium, SUM(a.sss_premium_er) AS sss_premium_er, SUM(philhealth_premium) AS philhealth_premium, SUM(philhealth_premium_er) AS philhealth_premium_er, SUM(a.pagibig_premium) AS pagibig_premium, SUM(pagibig_premium_er) AS pagibig_premium_er FROM omdcpayroll.emp_payslip a LEFT JOIN omdcpayroll.emp_masterfile b ON a.emp_id = b.emp_id LEFT JOIN omdcpayroll.options_dept c ON a.dept = c.id WHERE period_id IN (SELECT period_id FROM omdcpayroll.pay_periods WHERE reportingMonth = '$_GET[month]' AND reportingYear = '$_GET[year]') $fs GROUP BY a.emp_id ORDER BY emp_name;");
	$sssGT = 0; $sssERGT = 0;  $hdmfGT = 0; $hdmfERGT = 0; $phGT = 0; $phERGT = 0;
	while($data2 = $a->fetch_array()) {
	
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$data2['emp_id']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$data2['emp_name']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$data2['dept_name']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$data2['sss_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$data2['hdmf_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$data2['phealth_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$data2['sss_premium']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$data2['sss_premium_er']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$data2['pagibig_premium']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$data2['pagibig_premium_er']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$row,$data2['philhealth_premium']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$row,$data2['philhealth_premium_er']);
		
		for($y = 0; $y <= 11; $y++) { $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($y,$row)->applyFromArray($contentStyle); }
		for($z = 6; $z <= 11; $z++) { $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($z,$row)->getNumberFormat()->setFormatCode('#,##0.00'); }
		
		$row++;
		$sssGT+=$data2['sss_premium']; $sssERGT+=$data2['sss_premium_er']; $hdmfGT+=$data2['pagibig_premium']; $hdmfERGT+=$data2['pagibig_premium_er']; $phGT += $data2['philhealth_premium']; $phERGT += $data2['philhealth_premium_er'];
			
	}

	/* GRAND TOTAL */
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$sssGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$sssERGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$hdmfGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$hdmfERGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$row,$phGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$row,$phERGT);

	for($y = 6; $y <= 11; $y++) { $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($y,$row)->applyFromArray($totalStyle); }
	for($z = 6; $z <= 11; $z++) { $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($z,$row)->getNumberFormat()->setFormatCode('#,##0.00'); }
	
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("Statutory Deductions");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="statutory.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>