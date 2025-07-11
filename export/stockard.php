<?php
	session_start();
	require_once '../lib/PHPExcel/PHPExcel.php';
	include("../handlers/_generics.php");
	$con = new _init;

	date_default_timezone_set('Asia/Manila');
	set_time_limit(0);

	/* MYSQL QUERIES SECTION */
		
		$now = date("m/d/Y h:i a");
		$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
		$bit = $con->getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
		$query = $con->dbquery("select * from (select 'RR' as doc_type, lpad(a.rr_no,6,0) as doc_no, date_format(a.rr_date,'%m/%d/%y') as doc_date, a.rr_date as xdate, concat('(',a.supplier,') ',a.supplier_name) as contact, b.qty as `in`, 0 as `out` from rr_header a left join rr_details b on a.rr_no=b.rr_no and a.company=b.company and a.branch=b.branch where a.company = '$_SESSION[company]' and a.branch='$_SESSION[branchid]' and b.item_code = '$_GET[item_code]' and a.status = 'Finalized' and a.rr_date between '".$con->formatDate($_GET['dtf'])."' and '".$con->formatDate($_GET['dt2'])."' union all select 'SRR' as doc_type, lpad(a.srr_no,6,0) as doc_no, date_format(a.srr_date,'%m/%d/%y') as doc_date, a.srr_date as xdate, received_from as contact, b.qty as `in`, 0 as `out` from srr_header a left join srr_details b on a.srr_no=b.srr_no and a.company=b.company and a.branch=b.branch where a.company = '$_SESSION[company]' and a.branch='$_SESSION[branchid]' and b.item_code = '$_GET[item_code]' and a.status = 'Finalized' and a.srr_date between '".$con->formatDate($_GET['dtf'])."' and '".$con->formatDate($_GET['dt2'])."' union all select 'SW' as doc_type, lpad(a.sw_no,6,0) as doc_no, date_format(a.sw_date,'%m/%d/%y') as doc_date, a.sw_date as xdate, withdrawn_by as contact, 0 as `in`, b.qty as `out` from sw_header a left join sw_details b on a.sw_no=b.sw_no and a.company=b.company and a.branch=b.branch where a.company = '$_SESSION[company]' and a.branch='$_SESSION[branchid]' and b.item_code = '$_GET[item_code]' and a.status = 'Finalized' and a.sw_date between '".$con->formatDate($_GET['dtf'])."' and '".$con->formatDate($_GET['dt2'])."' union all select 'SI' as doc_type, lpad(a.invoice_no,6,0) as doc_no, date_format(a.invoice_date,'%m/%d/%y') as doc_date, a.invoice_date as xdate, concat('(',a.customer,') ',a.customer_name) as contact, 0 as `in`, b.qty as `out` from invoice_header a left join invoice_details b on a.invoice_no=b.invoice_no and a.company=b.company and a.branch=b.branch where a.company = '$_SESSION[company]' and a.branch='$_SESSION[branchid]' and b.item_code = '$_GET[item_code]' and a.status = 'Finalized' and a.invoice_date between '".$con->formatDate($_GET['dtf'])."' and '".$con->formatDate($_GET['dt2'])."' union all select 'STR' as doc_type, lpad(a.str_no,6,0) as doc_no, date_format(a.str_date,'%m/%d/%y') as doc_date, a.str_date as xdate, c.branch_name as contact, 0 as `in`, b.qty as `out` from str_header a left join str_details b on a.str_no=b.str_no and a.company=b.company and a.branch=b.branch  left join options_branches c on a.transferred_to=c.branch_code and a.company=c.company where a.company = '$_SESSION[company]' and a.branch='$_SESSION[branchid]' and b.item_code = '$_GET[item_code]' and a.status = 'Finalized' and a.str_date between '".$con->formatDate($_GET['dtf'])."' and '".$con->formatDate($_GET['dt2'])."' union all select 'POS' as doc_type, lpad(a.trans_id,6,0) as doc_no, date_format(trans_date,'%m/%d/%Y') as doc_date, trans_date as xdate, 'Walk-in Customer' as contact, '0' as `in`, b.qty as `out` from pos_header a left join pos_details b on a.tmpfileid=b.tmpfileid where a.company='$_SESSION[company]' and a.branch='$_SESSION[branchid]' and b.item_code = '$_GET[item_code]' and a.status = 'Finalized' and a.trans_date between '".$con->formatDate($_GET['dtf'])."' and '".$con->formatDate($_GET['dt2'])."') a order by xdate asc;");
		list($idesc,$unit) = $con->getArray("select description, unit from products_master where item_code = '$_GET[item_code]' and company = '$_SESSION[company]';");
	
		$isE = $con->getArray("SELECT doc_no,doc_date FROM phy_header WHERE company='$_SESSION[company]' AND branch='$_SESSION[branchid]' AND `status` = 'Finalized' AND doc_date <= '".$con->formatDate($_GET['dtf'])."' order by doc_date desc limit 1;");
		if($isE['doc_no'] == '') { $baseD8 = '2015-12-01'; } else { $baseD8 = $isE['doc_date']; }
					
		/* Physical Inventory */
		$pi = $con->getArray("select ifnull(sum(b.qty),0) from phy_header a left join phy_details b on a.doc_no=b.doc_no and a.company=b.company and a.branch=b.branch where a.doc_no = '$isE[doc_no]' and a.company = '$_SESSION[company]' and a.branch = '$_SESSION[branchid]' and b.item_code = '$_GET[item_code]' and a.status = 'Finalized';");
				
		/* Running Inventory Before Period */
		$runbal = $con->getArray("SELECT IFNULL(SUM(purchases+`returns`-withdrawals-transfers-sold),0) AS currentbalance FROM (SELECT b.qty AS purchases, 0 AS `returns`, 0 AS withdrawals, 0 AS transfers, 0 AS sold FROM rr_header a LEFT JOIN rr_details b ON a.rr_no=b.rr_no AND a.company=b.company AND a.branch=b.branch WHERE a.rr_date > '$baseD8' AND a.rr_date < '".$con->formatDate($_GET[dtf])."' AND a.status='Finalized' AND b.item_code = '$_GET[item_code]' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, b.qty AS `returns`, 0 AS withdrawals, 0 AS transfers, 0 AS sold FROM srr_header a LEFT JOIN srr_details b ON a.srr_no=b.srr_no AND a.company=b.company AND a.branch=b.branch WHERE a.srr_date > '$baseD8' and a.srr_date < '".$con->formatDate($_GET[dtf])."' AND a.status='Finalized' AND b.item_code = '$_GET[item_code]'  AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, 0 AS `returns`, b.qty AS withdrawals, 0 AS transfers, 0 AS sold FROM sw_header a LEFT JOIN sw_details b ON a.sw_no=b.sw_no AND a.company=b.company AND a.branch=b.branch WHERE a.sw_date > '$baseD8' AND a.sw_date < '".$con->formatDate($_GET[dtf])."' AND a.status ='Finalized' AND b.item_code = '$_GET[item_code]' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, 0 AS `returns`, 0 AS withdrawals, b.qty AS transfers, 0 AS sold FROM str_header a LEFT JOIN str_details b ON a.str_no=b.str_no AND a.company=b.company AND a.branch=b.branch WHERE a.str_date > '$baseD8' and a.str_date < '".$con->formatDate($_GET[dtf])."' AND a.status='Finalized' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, 0 AS `returns`,0 AS withdrawals, 0 AS transfers, b.qty AS sold FROM invoice_header a LEFT JOIN invoice_details b ON a.company=b.company AND a.branch=b.branch AND a.invoice_no=b.invoice_no WHERE a.invoice_date > '$baseD8' and a.invoice_date < '".$con->formatDate($_GET[dtf])."' AND a.status = 'Finalized' AND b.item_code = '$_GET[item_code]' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]' UNION ALL SELECT 0 AS purchases, 0 AS `returns`,0 AS withdrawals, 0 AS transfers, b.qty AS sold FROM pos_header a LEFT JOIN pos_details b ON a.tmpfileid=b.tmpfileid WHERE a.trans_date > '$baseD8' and a.trans_date < '".$con->formatDate($_GET[dtf])."' AND a.status = 'Finalized' AND b.item_code = '$_GET[item_code]' AND a.company='$_SESSION[company]' AND a.branch='$_SESSION[branchid]') a;");			
		$beg = ROUND($pi[0]+$runbal[0],2);
		
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
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3","$co[tel_no]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","$co[website]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","Inventory Stockcard");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A5","($_GET[item_code]) $desc");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A7","DOC #");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B7","DOC DATE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C7","DOC TYPE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D7","DESTINATION/ORIGIN");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E7","IN");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F7","OUT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G7","RUNNING QTY");
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);

	$objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('C7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('D7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('E7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('F7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('G7')->applyFromArray($headerStyle);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,8,'BALANCE FORWARDED FROM PREVIOUS PERIOD >>');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,8,$beg);
	
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,8)->applyFromArray($contentStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,8)->applyFromArray($contentStyle);
	
	$row = 9;
	while($data = $query->fetch_array()) {

		$qty = $data['in'] - $data['out'];
		$run += $qty;

		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$data['doc_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$data['doc_date']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$data['doc_type']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,$data['contact']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$data['in']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$data['out']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$run+$beg);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(3,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(4,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(5,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row)->applyFromArray($contentStyle);
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