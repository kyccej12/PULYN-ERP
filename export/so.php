<?php
	session_start();
	require_once '../lib/PHPExcel/PHPExcel.php';
	include("../includes/dbUSE.php");
	

	$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
	$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
	$_ihead = getArray("select so_no, lpad(so_no,2,0) as rr, date_format(so_date,'%m/%d/%Y') as d8, customer, customer_name, customer_addr, received_by, amount, remarks from so_header where so_no = '$_GET[so_no]' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
	$_idetails = dbquery("select item_code, description, qty, unit, cost, amount from so_details where so_no = '$_GET[so_no]' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]';");
	
	if($_SESSION['company'] == 1) { $company = 'Señor San Jose Franchising Corp. ('.$bit['branch_name'].')'; }
	if($_SESSION['company'] == 2) { $company = 'Señor San Jose Panaderia Inc. ('.$bit['branch_name'].')'; }
	if($_SESSION['company'] == 3) { $company = 'Fell\'s Point Food Corporation ('.$bit['branch_name'].')'; }
	
	
	$so_no = str_pad($_GET['so_no'],5,0,STR_PAD_LEFT);	
	$fname = $co['short_name'].'-SO-'.$so_no.'.xlsx';
	
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
								 ->setTitle("$co[company_name] - SALES ORDER")
								 ->setSubject("$co[company_name] - SALES ORDER")
								 ->setDescription("$co[company_name] - SALES ORDER")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1","Customer ID");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B1","Customer Name");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C1","Item ID");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D1","Quantity");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E1","Description");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F1","Date");
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);

	$row = 2;
	while($data = mysql_fetch_array($_idetails)) {
		$patterns = array();
		$patterns[0] = '/&Ntilde;/';
		$patterns[1] = '/&ntilde;/';
		$replacements = array();
		$replacements[0] = 'Ñ';
		$replacements[1] = 'ñ';
		
		$customer = preg_replace($patterns, $replacements, $_ihead['customer_name']);
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$_ihead['customer']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$customer);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$data['item_code']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$data['qty']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$data['description']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$_ihead['d8']);
		$row++;
	}
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("Sales Order Form");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$fname.'"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>