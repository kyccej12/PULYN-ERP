<?php
	session_start();

	ini_set("memory_limit","5024M");
	set_time_limit(-1);
	
	require_once '../lib/PHPExcel/PHPExcel.php';
	include("../includes/dbUSE.php");

	date_default_timezone_set('Asia/Manila');
		
	$co = getArray("select * from companies where company_id = '$_SESSION[company]';");
	$bit = getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
	if($_GET['branch'] != '') {
		list($myBranch) = getArray("select branch_name from options_branches where branch_code = '$_GET[branch]';");
		$fs1 = " and a.branch = '$_GET[branch]' ";
	} else { $myBranch = "Consolidated"; }
	
	switch($_GET['type']) {
		case "1": $lbl = "Unserved Purchase Orders"; $f1 = " and qty_dld = 0"; break;
		case "2": $lbl = "Partially Served Purchase Orders"; $f1 = " and qty_dld > 0 and qty_dld < qty"; break;
		case "3": $lbl = "Fully Served Purchases Orders"; $f1 = " and qty_dld >= qty";	break;
		case "4": $lbl = "Partial/Fully Served P.Os"; $f1 = " and qty_dld > 0";	break;
		default: $lbl = "All Purchases"; $f1 = "";	break;
	}


	/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");
	$query = mysql_query("SELECT * FROM (SELECT lpad(a.po_no,6,0) as po, date_format(po_date,'%m/%d/%y') as pd8, po_date, concat('(',supplier,') ',supplier_name) as supp, b.item_code, b.description, b.qty, b.unit, (b.cost-b.discount) as cost, ROUND(b.qty * (b.cost-b.discount),2) as amount, b.qty_dld from po_header a left join po_details b on a.po_no=b.po_no and a.branch = b.branch where a.po_date between '".formatDate($_GET['dtf'])."' and '".formatDate($_GET['dt2'])."' and a.status='Finalized' $f0) a where 1=1 $f1 order by po_date desc;");
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
								 ->setTitle("CGAP - SUMMARY OF PURCHASES")
								 ->setSubject("CGAP - SUMMARY OF PURCHASES")
								 ->setDescription("CGAP - SUMMARY OF PURCHASES")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Expored File");
	
	//$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(24);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(50);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(12);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A7","P.O. NO.");	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B7","DATE");	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C7","SUPPLIER");	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D7","ITEM");	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E7","QTY");	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F7","UNIT");	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G7","COST");	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H7","AMOUNT");	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I7","QTY DEL'D");	

	$objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('C7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('D7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('E7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('F7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('G7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('H7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('I7')->applyFromArray($headerStyle);

	$row = 8; $amtGT = 0;

	while($data = mysql_fetch_array($query)) {
		if($data['po'] != $oldPO) { $po = $data['po']; $date = $data['pd8']; $supp = $data['supp']; } else { $po = ""; $date = ""; $supp = ""; }

		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$po);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$date);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$supp);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$data['item_code'].' - '.$data['description']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$data['qty']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$data['unit']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$data['cost']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$data['amount']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$data['qty_dld']);

		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(4,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->getNumberFormat()->setFormatCode('#,##0.00');

		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(3,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(4,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(5,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row)->applyFromArray($contentStyle);

		$oldPO = $row['po']; $amtGT+=$row['amount'];
		$row++;
	}
	
	/* TOTAL */
	//$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$dbGT);
	//$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$crGT);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$amtGT);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("SUMMARY OF PURCHASES");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="summary_of_purchases.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>