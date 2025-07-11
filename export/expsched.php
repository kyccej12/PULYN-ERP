<?php
	session_start();
	set_time_limit(0);
	require_once '../lib/PHPExcel/PHPExcel.php';
	require_once '../handlers/_generics.php';
	
	$mydb = new _init;
		
	$now = date("m/d/Y h:i a");
	$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
	$dtf = "$_GET[year]-$_GET[month]-01";
	$ydtf = "$_GET[year]-01-01";
	$fs1 = '';
	list($dt2,$period) = $mydb->getArray("select last_day('$dtf'), date_format('$dtf','%M %Y');");
	

	$headerStyle = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
		'borders' => array('outline' => array('style' =>PHPExcel_Style_Border::BORDER_THIN)),
	);
	
	$resultStyle = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT),
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
	$objPHPExcel->getProperties()->setCreator("Medgruppe Polyclinics & Diagnostic Center, Inc.")
								 ->setLastModifiedBy("Medgruppe Polyclinics & Diagnostic Center, Inc.")
								 ->setTitle("Medgruppe Polyclinics & Diagnostic Center, Inc. - Schedule of Expense")
								 ->setSubject("Medgruppe Polyclinics & Diagnostic Center, Inc. - Schedule of Expense")
								 ->setDescription("Medgruppe Polyclinics & Diagnostic Center, Inc. - Schedule of Expense")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3",$co['tel_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A5","Schedule of Expense Covering the Period $period");
	
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(48);
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
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,7,"EXPENSE ACCOUNT");
	$uLoop = $mydb->dbquery("SELECT 'PCC' AS costcenter UNION ALL SELECT costcenter FROM options_costcenter ORDER BY costcenter;");
	$z = 1;
	while($cc = $uLoop->fetch_array(MYSQLI_BOTH)) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($z,7,$cc[0]);
		$z++;
	}
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($z++,7,"TOTAL EXPENSE FOR THE PERIOD");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($z++,7,"BUDGET");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($z++,7,"VARIANCE (AMOUNT)");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($z++,7,"VARIANCE (%)");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($z++,7,"YTD EXPENSE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($z++,7,"YTD % TO ANNUAL");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($z++,7,"ANNUAL BUDGET");
	
	for($x = 0; $x <= ($z-1); $x++) {
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($x,7)->applyFromArray($headerStyle);
	}


	$row = 8;
	$c = $mydb->dbquery("SELECT DISTINCT acct, b.description FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct = b.acct_code WHERE doc_date between '$dtf' and '$dt2' and b.acct_grp in ('12','13') ORDER BY a.acct $fs1");
	$i = 0; $aeGT = 0; $budgetGT = 0; $annualGT = 0; $ytdeGT = 0; $varianceGT = 0; 

	while(list($acct,$description) = $c->fetch_array(MYSQLI_BOTH)) {
		list($ae) = $mydb->getArray("select sum(debit-credit) from acctg_gl where doc_date between '$dtf' and '$dt2' and acct = '$acct' $fs1");
		list($ytde) = $mydb->getArray("select sum(debit-credit) from acctg_gl where doc_date between '$ydtf' and '$dt2' and acct = '$acct' $fs1");
		list($budget,$annual) = $mydb->getArray("select ROUND(budget/12,2), budget from budgets where year = '$_GET[year]' and acct = '$acct';");
		if($budget == '') { $budget = 0; }
		$variance = $ae-$budget;
		
		$acctPCT = ROUND(($variance/$ae) * 100,2);
		$ytdePCT = ROUND(($ytde/$annual) * 100,2);
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"($acct) $description");
		$x = 1;
		$uLoop = $mydb->dbquery("SELECT '' as unitcode,'PCC' as costcenter UNION ALL SELECT unitcode,costcenter from options_costcenter order by costcenter;");
		while($cc = $uLoop->fetch_array(MYSQLI_BOTH)) {
			list($ccExpense) = $mydb->getArray("SELECT SUM(debit-credit) AS amount FROM acctg_gl WHERE acct = '$acct' AND doc_date BETWEEN '$dtf' AND '$dt2' and cost_center = '$cc[0]' GROUP BY cost_center;");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x,$row,$ccExpense);
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($x,$row)->getNumberFormat()->setFormatCode('#,##0.00');
			$x++;
		}
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,$ae);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,$budget);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,$variance);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,$acctPCT);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,$ytde);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,$ytdePCT);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,$annual);
		
		for($z = 0; $z <= ($x-1); $z++) {
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($z,$row)->applyFromArray($contentStyle);
			if($z >= 1) { $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($z,$row)->getNumberFormat()->setFormatCode('#,##0.00'); }
		}
		
		$row++; $aeGT+=$ae; $budgetGT+=$budget; $annualGT+=$annual; $ytdeGT+=$ytde; $varianceGT += $variance;
	}
	
	$x=1;
	$uLoop = $mydb->dbquery("SELECT '' as unitcode, 'PCC' as costcenter UNION ALL SELECT unitcode,costcenter from options_costcenter order by costcenter;");
	while($cc = $uLoop->fetch_array(MYSQLI_BOTH)) {
		list($ccExpenseGT) = $mydb->getArray("SELECT SUM(debit-credit) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b on a.acct = b.acct_code WHERE b.acct_grp in ('12','13') AND doc_date BETWEEN '$dtf' AND '$dt2' and cost_center = '$cc[0]' GROUP BY cost_center;");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x,$row,$ccExpenseGT);
		$x++;
	}

	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,$aeGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,$budgetGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,$varianceGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,"");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,$ytdeGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,"");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($x++,$row,$annualGT);

	for($z = 1; $z <= ($x-1); $z++) {
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($z,$row)->applyFromArray($resultStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($z,$row)->getNumberFormat()->setFormatCode('#,##0.00'); 
	}

	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("Schedule of Expense");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="expsched.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>