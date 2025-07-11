<?php
	ini_set('memory_limit','1024M');
	set_time_limit(0);

	require_once '../lib/PHPExcel/PHPExcel.php';
	date_default_timezone_set('Asia/Manila');

	require_once '../handlers/_generics.php';
	$con = new _init();
	
	session_start();
	$cutoff = $_REQUEST['cutoff'];
	$proj = $_REQUEST['proj'];
	
	if($proj != '') { $f1 = " and a.proj = '$proj' "; } else { $f1 = ''; }
	$q = $con->dbquery("SELECT TRIM(BOTH '' FROM LPAD(a.acct_no,12,0)) as bank_acct,b.lname,b.fname,net_pay FROM redglobalhris.emp_payslip a left join redglobalhris.emp_masterfile b on a.emp_id = b.emp_id WHERE a.period_id = '$cutoff' AND net_pay > 0 and b.atm_bank = 1 $f1;");	
	list($batchCode) = $con->getArray("SELECT LPAD('$_REQUEST[batch]','2','0');");

	
	$fundAcct = '4190246088';
	$compCode = 'D5K';
	$creditDate = $con->formatDate($_REQUEST['date']);

	$headerStyle = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
		'borders' => array('outline' => array('style' =>PHPExcel_Style_Border::BORDER_THIN)),
	);
	
	$headerStyle2 = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
		'borders' => array('outline' => array('style' =>PHPExcel_Style_Border::BORDER_THIN)),
	);
	
	$contentStyle = array(
		'borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);
	
	$totalStyle = array(
		'font' => array('bold' => true),
		'borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);
	
	$uid = $_SESSION['userid'];	
	$now = date("m/d/Y h:i a");
	

/* MYSQL QUERIES SECTION */

/* MYSQL QUERIES SECTION */
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(9);
	$objPHPExcel->getProperties()->setCreator("PORT 80")
								 ->setLastModifiedBy("Rolan Paderanga")
								 ->setTitle("Red Global - BDO Transmittal")
								 ->setSubject("Red Global - BDO Transmittal")
								 ->setDescription("Red Global - BDO Transmittal")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1","Medgruppe Polyclinics & Diagnostic Center, Inc.");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2","Credit Date: ");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3","Company Code: ");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","Funding Account: ");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A5","Batch: ");
	
	$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(14);
	
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('66B2FF');
	$objPHPExcel->getActiveSheet()->getStyle('A3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('66B2FF');
	$objPHPExcel->getActiveSheet()->getStyle('A4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('66B2FF');
	$objPHPExcel->getActiveSheet()->getStyle('A5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('66B2FF');
	
	$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('A4')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($headerStyle2);
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B2",$creditDate);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B3","D5K");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B4","4190246088");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B5",$_REQUEST['batch']);
	
	$objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('B4')->applyFromArray($headerStyle2);
	$objPHPExcel->getActiveSheet()->getStyle('B5')->applyFromArray($headerStyle2);
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A6","ACCOUNT #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B6","AMOUNT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C6","LAST NAME");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D6","FIRST NAME");
	
	$objPHPExcel->getActiveSheet()->getStyle('A6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
	$objPHPExcel->getActiveSheet()->getStyle('B6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
	$objPHPExcel->getActiveSheet()->getStyle('C6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
	$objPHPExcel->getActiveSheet()->getStyle('D6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
	
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);

	
	$objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('B6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('C6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('D6')->applyFromArray($headerStyle);
	
	$row = 7;
	while($res = $q->fetch_array()){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$res['bank_acct']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$res['net_pay']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$res['lname']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$res['fname']);
			
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($contentStyle);
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(3,$row)->applyFromArray($contentStyle);
			
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');	
		
		$row++; $ctr++; $ttake_home += $res['net_pay'];
	}
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$ttake_home);
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);	
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');	
		if($_REQUEST['proj'] ==  '1'){
			 $row = $row + 3;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A".$row,"Prepared By: "); $row = $row + 2;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".$row,"JOSIE M. MAGNO"); $row = $row + 1;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".$row,"Finance Head"); 
			 
			 $row = $row + 3;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A".$row,"Approved By: "); $row = $row + 2;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".$row,"RONALD Y. ELPA"); $row = $row + 1;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".$row,"PRESIDENT"); 
		}else{
			 $row = $row + 3;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A".$row,"Prepared By: "); $row = $row + 2;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".$row,""); $row = $row + 1;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".$row,""); 
			 
			 $row = $row + 3;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A".$row,"Checked By: "); $row = $row + 2;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".$row,"JOSIE M. MAGNO"); $row = $row + 1;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".$row,"Finance Head"); 
			 
			 $row = $row + 3;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A".$row,"Approved By: "); $row = $row + 2;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".$row,"RONALD Y. ELPA"); $row = $row + 1;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".$row,"PRESIDENT"); 
		}
		
		
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("Transmittal File");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="bdotransmittal.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>