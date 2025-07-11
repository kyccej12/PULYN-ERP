<?php
	session_start();
	require_once '../lib/PHPExcel/PHPExcel.php';
	include("../handlers/_generics.php");
	$con = new _init;

	date_default_timezone_set('Asia/Manila');
	set_time_limit(0);
		
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
	$bit = $con->getArray("select lpad(branch_code,2,0), branch_name from options_branches where branch_code = '$_SESSION[branchid]' and company = '$_SESSION[company]';");
	if($_GET['conso'] != "Y") { $f1 = " and a.branch = '$_SESSION[branchid]' "; $lbl = $bit['branch_name']; } else { $lbl = "CONSOLIDATED"; }
	/* MYSQL QUERIES SECTION */
	$now = date("m/d/Y h:i a");	
		
	//	if($_GET['conso'] == "N") { $fs1 = " and a.branch = '$_SESSION[branchid]' "; $lbl = "ACCOUNTS PAYABLE AGING SCHEDULE"; } else { $fs1  = ""; $lbl = "ACCOUNTS PAYABLE AGING SCHEDULE - CONSOLIDATED"; }
	$date = date('Y-m-d');
	$query = $con->dbquery("select supplier, b.tradename, b.credit_limit, c.description AS terms, SUM(cur)+SUM(bal1) + SUM(bal2) +SUM(bal3) + SUM(bal4) +SUM(bal5) AS total_bal FROM (select a.supplier, balance as cur, 0 as bal1, 0 as bal2, 0 as bal3, 0 as bal4, 0 as bal5 from (select a.supplier, if('$date' <= date_add(apv_date,INTERVAL a.terms DAY),'0',datediff('$date',date_add(apv_date,INTERVAL a.terms DAY))) as daysdue, balance from apv_header a left join options_terms b on a.terms=b.terms_id where apv_date <= '$date' and balance > 0 and a.status='Posted' and company = '$_SESSION[company]' union all select b.customer as supplier, if('$date' <= date_add(invoice_date,INTERVAL c.terms DAY),'0',datediff('$date',date_add(invoice_date,INTERVAL c.terms DAY))) as daysdue, balance from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch and a.company=b.company left join contact_info c on b.customer=c.file_id left join options_terms d on c.terms=d.terms_id where invoice_date <= '$date' and a.status = 'Posted' and balance > 0 and a.company = '$_SESSION[company]' and a.branch = '$_SESSION[branchid]') a where daysdue <= 0 union all select a.supplier, 0 as cur, balance as bal1, 0 as bal2, 0 as bal3, 0 as bal4, 0 as bal5 from (select a.supplier, if('$date' <= date_add(apv_date,INTERVAL a.terms DAY),'0',datediff('$date',date_add(apv_date,INTERVAL a.terms DAY))) as daysdue, balance from apv_header a left join options_terms b on a.terms=b.terms_id where apv_date <= '$date' and balance > 0 and a.status='Posted' and company = '$_SESSION[company]' and branch = '$_SESSION[branchid]' union all select b.customer as supplier, if('$date' <= date_add(invoice_date,INTERVAL c.terms DAY),'0',datediff('$date',date_add(invoice_date,INTERVAL c.terms DAY))) as daysdue, balance from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch and a.company=b.company left join contact_info c on b.customer=c.file_id left join options_terms d on c.terms=d.terms_id where invoice_date <= '$date' and a.status = 'Posted' and balance > 0 and a.company = '$_SESSION[company]' ) a where daysdue between 1 and 30 union all select a.supplier, 0 as cur, 0 as bal1, balance as bal2, 0 as bal3, 0 as bal4, 0 as bal5 from (select a.supplier, if('$date' <= date_add(apv_date,INTERVAL a.terms DAY),'0',datediff('$date',date_add(apv_date,INTERVAL a.terms DAY))) as daysdue, balance from apv_header a left join options_terms b on a.terms=b.terms_id where apv_date <= '$date' and balance > 0 and a.status='Posted' and company = '$_SESSION[company]' union all select b.customer as supplier, if('$date' <= date_add(invoice_date,INTERVAL c.terms DAY),'0',datediff('$date',date_add(invoice_date,INTERVAL c.terms DAY))) as daysdue, balance from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch and a.company=b.company left join contact_info c on b.customer=c.file_id left join options_terms d on c.terms=d.terms_id where invoice_date <= '$date' and a.status = 'Posted' and balance > 0 and a.company = '$_SESSION[company]') a where daysdue between 31 and 60 union all select a.supplier, 0 as cur, 0 as bal1, 0 as bal2, balance as bal3, 0 as bal4, 0 as bal5 from (select a.supplier, if('$date' <= date_add(apv_date,INTERVAL a.terms DAY),'0',datediff('$date',date_add(apv_date,INTERVAL a.terms DAY))) as daysdue, balance from apv_header a left join options_terms b on a.terms=b.terms_id where apv_date <= '$date' and balance > 0 and a.status='Posted' and company = '$_SESSION[company]' union all select b.customer as supplier, if('$date' <= date_add(invoice_date,INTERVAL c.terms DAY),'0',datediff('$date',date_add(invoice_date,INTERVAL c.terms DAY))) as daysdue, balance from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch and a.company=b.company left join contact_info c on b.customer=c.file_id left join options_terms d on c.terms=d.terms_id where invoice_date <= '$date' and a.status = 'Posted' and balance > 0 and a.company = '$_SESSION[company]') a where daysdue between 61 and 90 union all select a.supplier, 0 as cur, 0 as bal1, 0 as bal2, 0 as bal3, balance as bal4, 0 as bal5 from (select a.supplier, if('$date' <= date_add(apv_date,INTERVAL a.terms DAY),'0',datediff('$date',date_add(apv_date,INTERVAL a.terms DAY))) as daysdue, balance from apv_header a left join options_terms b on a.terms=b.terms_id where apv_date <= '$date' and balance > 0 and a.status='Posted' and company = '$_SESSION[company]' union all select b.customer as supplier, if('$date' <= date_add(invoice_date,INTERVAL c.terms DAY),'0',datediff('$date',date_add(invoice_date,INTERVAL c.terms DAY))) as daysdue, balance from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch and a.company=b.company left join contact_info c on b.customer=c.file_id left join options_terms d on c.terms=d.terms_id where invoice_date <= '$date' and a.status = 'Posted' and balance > 0 and a.company = '$_SESSION[company]') a where daysdue between 91 and 120 union all select a.supplier, 0 as cur, 0 as bal1, 0 as bal2, 0 as bal3, 0 as bal4, balance as bal5 from (select a.supplier, if('$date' <= date_add(apv_date,INTERVAL a.terms DAY),'0',datediff('$date',date_add(apv_date,INTERVAL a.terms DAY))) as daysdue, balance from apv_header a left join options_terms b on a.terms=b.terms_id where apv_date <= '$date' and balance > 0 and a.status='Posted' and company = '$_SESSION[company]' union all select b.customer as supplier, if('$date' <= date_add(invoice_date,INTERVAL c.terms DAY),'0',datediff('$date',date_add(invoice_date,INTERVAL c.terms DAY))) as daysdue, balance from apbeg_header a left join apbeg_details b on a.doc_no=b.doc_no and a.branch=b.branch and a.company=b.company left join contact_info c on b.customer=c.file_id left join options_terms d on c.terms=d.terms_id where invoice_date <= '$date' and a.status = 'Posted' and balance > 0 and a.company = '$_SESSION[company]') a where daysdue > 120) a left join contact_info b on a.supplier = b.file_id left join options_terms c on b.terms = c.terms_id group by supplier ORDER BY total_bal DESC;;");
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
								 ->setTitle("$co[company_name] - TRIAL BALANCE")
								 ->setSubject("$co[company_name] - TRIAL BALANCE")
								 ->setDescription("$co[company_name] - TRIAL BALANCE")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3","$co[tel_no]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","$co[website]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A5","TOP PAYABLES");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A7","Customer");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B7","Terms");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C7","Amount");
	
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	
	$objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('C7')->applyFromArray($headerStyle);
	
	$row = 8;
	while($data = $query->fetch_array()) {
		//if($data['amt'] > 0) { $db = $data['amt']; $cr = 0; } else { $db = 0; $cr = abs($data['amt']); }
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$data['supplier'].'-'.$data['tradename']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$data['terms']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$data['total_bal']);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$row++;
	}
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("TRIAL BALANCE");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="top_payable.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>