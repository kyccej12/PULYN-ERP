<?php
	session_start();
	require_once '../lib/PHPExcel/PHPExcel.php';
	include("../handlers/_generics.php");
	ini_set("max_execution_time",-1);
	
	$con = new _init;

	date_default_timezone_set('Asia/Manila');
	set_time_limit(0);

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
		$bit = $con->getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
		
		if($_GET['group'] != "") { $fs1 = " and `group` = '$_GET[group]' "; }
		$query = $con->dbquery("SELECT item_code, unit, description FROM products_master WHERE `group` IN ('FG','RM','TG') AND description!='' AND company='$_SESSION[company]' $fs1 $xsearch ORDER BY description");
				
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
								 ->setTitle("$co[company_name] - INVENTORY BOOK")
								 ->setSubject("$co[company_name] - INVENTORY BOOK")
								 ->setDescription("$co[company_name] - INVENTORY BOOK")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3","$co[tel_no]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","$co[website]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","Inventory Book");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A5","");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A7","ITEM CODE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B7","DESCRIPTION");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C7","UNIT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D7","BEG.");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E7","RETURNS");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F7","WITHDRAWALS");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G7","TRANSFERS");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H7","SOLD");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I7","END");
	
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

	$row = 8;
	
	$isE = $con->getArray("SELECT doc_no,doc_date FROM phy_header WHERE company='$_SESSION[company]' AND branch='$_SESSION[branchid]' AND `status` = 'Finalized' AND doc_date <= '".formatDate($_GET['dtf'])."' order by doc_date desc limit 1;");
	if($isE['doc_no'] == '') { $baseD8 = '2015-12-01'; } else { $baseD8 = $isE['doc_date']; }
				
	while($data = $query->fetch_array()) {
		
		list($idesc,$unit) = $con->getArray("select description, unit from products_master where item_code = '$data[item_code]' and company = '$_SESSION[company]';");
		$desc = rawurlencode($idesc);
		
		$pi = $con->getArray("select ifnull(sum(b.qty),0) from phy_header a inner join phy_details b on a.doc_no=b.doc_no and a.company=b.company and a.branch=b.branch where a.doc_no = '$isE[doc_no]' and a.company = '$_SESSION[company]' and a.branch = '$_SESSION[branchid]' and b.item_code = '$row[item_code]' and a.status = 'Finalized';");
					
		/* Running Inventory Before Period */ 
		$run = $con->getArray("SELECT IFNULL(SUM(purchases+`returns`-withdrawals-transfers-sold),0) AS currentbalance FROM (SELECT b.qty AS purchases, 0 AS `returns`, 0 AS withdrawals, 0 AS transfers, 0 AS sold FROM rr_header a INNER JOIN rr_details b ON a.rr_no=b.rr_no AND a.company=b.company AND a.branch=b.branch WHERE a.rr_date > '$baseD8' AND a.rr_date < '".formatDate($_GET['dtf'])."' AND a.status='Finalized' AND b.item_code = '$row[item_code]' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, b.qty AS `returns`, 0 AS withdrawals, 0 AS transfers, 0 AS sold FROM srr_header a INNER JOIN srr_details b ON a.srr_no=b.srr_no AND a.company=b.company AND a.branch=b.branch WHERE a.srr_date > '$baseD8' and a.srr_date < '".formatDate($_GET[dtf])."' AND a.status='Finalized' AND b.item_code = '$row[item_code]'  AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' and b.item_code='$row[item_code]' UNION ALL SELECT 0 AS purchases, 0 AS `returns`, b.qty AS withdrawals, 0 AS transfers, 0 AS sold FROM sw_header a INNER JOIN sw_details b ON a.sw_no=b.sw_no AND a.company=b.company AND a.branch=b.branch WHERE a.sw_date > '$baseD8' AND a.sw_date < '".formatDate($_GET[dtf])."' AND a.status ='Finalized' AND b.item_code = '$row[item_code]' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, 0 AS `returns`, 0 AS withdrawals, b.qty AS transfers, 0 AS sold FROM str_header a INNER JOIN str_details b ON a.str_no=b.str_no AND a.company=b.company AND a.branch=b.branch WHERE a.str_date > '$baseD8' and a.str_date < '".formatDate($_GET[dtf])."' AND a.status='Finalized' AND b.item_code = '$row[item_code]' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, 0 AS `returns`,0 AS withdrawals, 0 AS transfers, b.qty AS sold FROM invoice_header a INNER JOIN invoice_details b ON a.company=b.company AND a.branch=b.branch AND a.invoice_no=b.invoice_no WHERE a.invoice_date > '$baseD8' and a.invoice_date < '".formatDate($_GET[dtf])."' AND a.status = 'Finalized' AND b.item_code = '$row[item_code]' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, 0 AS `returns`,0 AS withdrawals, 0 AS transfers, b.qty AS sold FROM pos_header a INNER JOIN pos_details b ON a.tmpfileid=b.tmpfileid WHERE a.trans_date > '$baseD8' and a.trans_date < '".formatDate($_GET[dtf])."' AND a.status = 'Finalized' AND b.item_code = '$row[item_code]' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]') a;");
		/* Current Inventory */
		$cur = $con->getArray("SELECT ifnull(sum(purchases),0) as purchases, ifnull(sum(`returns`),0) as returns, ifnull(sum(withdrawals),0) as withdrawals, ifnull(sum(transfers),0) as transfers, ifnull(sum(sold),0) as sold, IFNULL(SUM(purchases+`returns`-withdrawals-transfers-sold),0) AS currentbalance FROM (SELECT b.qty AS purchases, 0 AS `returns`, 0 AS withdrawals, 0 AS transfers, 0 AS sold FROM rr_header a INNER JOIN rr_details b ON a.rr_no=b.rr_no AND a.company=b.company AND a.branch=b.branch WHERE a.rr_date BETWEEN '".formatDate($_GET[dtf])."' AND '".formatDate($_GET[dt2])."' AND a.status='Finalized' AND b.item_code = '$row[item_code]' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, b.qty AS `returns`, 0 AS withdrawals, 0 AS transfers, 0 AS sold FROM srr_header a INNER JOIN srr_details b ON a.srr_no=b.srr_no AND a.company=b.company AND a.branch=b.branch WHERE a.srr_date BETWEEN '".formatDate($_GET[dtf])."' AND '".formatDate($_GET[dt2])."' AND a.status='Finalized' AND b.item_code = '$row[item_code]'  AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, 0 AS `returns`, b.qty AS withdrawals, 0 AS transfers, 0 AS sold FROM sw_header a INNER JOIN sw_details b ON a.sw_no=b.sw_no AND a.company=b.company AND a.branch=b.branch WHERE a.sw_date BETWEEN '".formatDate($_GET[dtf])."' AND '".formatDate($_GET[dt2])."' AND a.status ='Finalized' AND b.item_code = '$row[item_code]' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, 0 AS `returns`, 0 AS withdrawals, b.qty AS transfers, 0 AS sold FROM str_header a INNER JOIN str_details b ON a.str_no=b.str_no AND a.company=b.company AND a.branch=b.branch WHERE a.str_date BETWEEN '".formatDate($_GET[dtf])."' AND '".formatDate($_GET[dt2])."' AND b.item_code = '$row[item_code]' AND a.status='Finalized' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, 0 AS `returns`,0 AS withdrawals, 0 AS transfers, b.qty AS sold FROM invoice_header a INNER JOIN invoice_details b ON a.company=b.company AND a.branch=b.branch AND a.invoice_no=b.invoice_no WHERE a.invoice_date BETWEEN '".formatDate($_GET[dtf])."' AND '".formatDate($_GET[dt2])."' AND a.status = 'Finalized' AND b.item_code = '$row[item_code]' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, 0 AS `returns`,0 AS withdrawals, 0 AS transfers, b.qty AS sold FROM pos_header a INNER JOIN pos_details b ON a.tmpfileid=b.tmpfileid WHERE a.trans_date BETWEEN '".formatDate($_GET[dtf])."' AND '".formatDate($_GET[dt2])."' AND a.status = 'Finalized' AND b.item_code = '$row[item_code]' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]') a HAVING purchases != 0 OR `returns` != 0 OR  sold != 0 OR currentbalance != 0;");
		
		/* Balance End */
		$end = ROUND($pi[0]+$run[0]+$cur['currentbalance'],2);
					
					
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$data['item_code']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$idesc);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$unit);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$pi[0]+$run[0]);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$cur['returns']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$cur['withdrawals']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$cur['transfers']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$cur['sold']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$end);
		
		
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
		$row++;
	}
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("INVENTORY STOCKARD");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="stockcard.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>