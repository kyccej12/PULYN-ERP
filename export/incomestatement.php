<?php
	//ini_set("display_errors","On");
	session_start();
	ini_set("max_execution_time",0);
	require_once '../lib/PHPExcel/PHPExcel.php';
	require_once "../handlers/_generics.php";
	
	$mydb = new _init;

	date_default_timezone_set('Asia/Manila');
	set_time_limit(0);
	ini_set("memory_limit",-1);

	/* MYSQL QUERIES SECTION */
		$now = date("m/d/Y h:i a");
		$ydtf = "$_GET[year]-01-01";
		$xydtf = "01/01/$_GET[year]";
		$dtf = "$_GET[year]-$_GET[month]-01";
		$xdtf = "$_GET[month]/01/$_GET[year]";
		$fs = '';
		list($dt2,$xdt2,$month) = $mydb->getArray("select last_day('$dtf'), date_format(last_day('$dtf'),'%m/%d/%Y'), date_format('$dtf','%M');");
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
		
		if($_GET['cc'] != '') { $fs = " and cost_center = '$_GET[cc]' "; list($pcode) = $mydb->getArray("select proj_code from options_project where proj_id = '$_GET[cc]';"); } else { $pcode = "Consolidated"; }
	/* END OF SQL QUERIES */

	$headerStyle = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
		'borders' => array('outline' => array('style' =>PHPExcel_Style_Border::BORDER_THIN)),
	);
	
	$totalStyleLabel = array(
		'font' => array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
		'borders' => array('outline' => array('style' =>PHPExcel_Style_Border::BORDER_THIN)),
	);
	
	$totalStyle = array(
		'font' => array('bold' => true),
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
								 ->setTitle("$co[company_name] - INCOME STATEMENT")
								 ->setSubject("$co[company_name] - INCOME STATEMENT")
								 ->setDescription("$co[company_name] - INCOME STATEMENT")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3","$co[tel_no]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","$co[website]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","INCOME STATEMENT FOR THE PERIOD $month, $_GET[year] ($pcode)");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A5","");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A6","");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B6","FOR THE PERIOD");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C6","YEAR-TO-DATE");
	$objPHPExcel->getActiveSheet()->getStyle("B6")->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle("C6")->applyFromArray($headerStyle);
	

	$row = 7;
	$a = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '9' AND doc_date BETWEEN '$ydtf' AND '$dt2' $fs GROUP BY a.acct order by b.description asc;");
	while($b = $a->fetch_array(MYSQLI_BOTH)) {
		$bb = $mydb->getArray("select sum(credit-debit) as amount from acctg_gl where acct = '$b[acct]' and doc_date between '$dtf' and '$dt2' $fs group by acct;");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"($b[acct]) $b[description]");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$bb['amount']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$b['amount']);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($contentStyle);
		
		$SalesGT+=$bb['amount']; $ySalesGT+=$b['amount']; $row++;
	}
	
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'TOTAL NET SALES');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$SalesGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$ySalesGT);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');


	/* COST OF SALES */
	$row+=2;
	$c = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE  b.acct_grp = '14' AND doc_date BETWEEN '$ydtf' AND '$dt2' $fs GROUP BY a.acct order by b.description asc;");
	while($d = $c->fetch_array(MYSQLI_BOTH)) {
		$dd = $mydb->getArray("select sum(debit-credit) as amount from acctg_gl where acct = '$d[acct]' and doc_date between '$dtf' and '$dt2' $fs group by acct;");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"($d[acct]) $d[description]");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$dd['amount']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$d['amount']);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($contentStyle);
		
		$cosGT+=$dd['amount']; $yCosGT+=$d['amount']; $row++;
	}
	
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'TOTAL COST OF SALES OR SERVICES');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$cosGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$yCosGT);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	

	/* GROSS PROFIT */
	$row = $row+2;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'GROSS PROFIT');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,($SalesGT-$cosGT));
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,($ySalesGT-$yCosGT));
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');

	/* OPERATING EXPENSES */
	$row+=2;
	$e = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp IN ('12') AND doc_date BETWEEN '$ydtf' AND '$dt2' $fs GROUP BY a.acct ORDER BY b.description;");
	while($f = $e->fetch_array(MYSQLI_BOTH)) {
		$ff = $mydb->getArray("select sum(debit-credit) as amount from acctg_gl where acct = '$f[acct]' and doc_date between '$dtf' and '$dt2' $fs group by acct;");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"($f[acct]) $f[description]");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$ff['amount']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$f['amount']);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($contentStyle);
		
		$opexGT+=$ff['amount']; $yOpexGT+=$f['amount']; $row++;
	}
	
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'TOTAL OPERATING EXPENSES');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$opexGT);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$yOpexGT);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');

	/* NET OPERATING INCOME OR LOSS */
	$row+=2;
	$noi = $SalesGT - $cosGT - $opexGT;
	$ynoi = $ySalesGT - $yCosGT - $yOpexGT;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'NET OPERATING INCOME/LOSS');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$noi);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$ynoi);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');

	$row++;
	list($yOI) = $mydb->getArray("SELECT ROUND(SUM(credit-debit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE  b.acct_grp = '11' AND doc_date BETWEEN '$ydtf' AND '$dt2' $fs GROUP BY b.acct_grp;");
	list($OI) = $mydb->getArray("SELECT ROUND(SUM(credit-debit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE  b.acct_grp = '11' AND doc_date BETWEEN '$dtf' AND '$dt2' $fs GROUP BY b.acct_grp;");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'ADD: OTHER INCOME');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$OI);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$yOI);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');

	/* NET INCOME OR LOSS BEFORE DEPRECIATION */
	$row++;
	$noibd = $noi + $OI; $ynoibd = $ynoi + $yOI;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'NET INCOME/LOSS BEFORE DEPRECIATION');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$noibd);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$ynoibd);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');


	/* DEPRECIATION EXPENSE */
	$row+=2;
	$depQuery = $mydb->dbquery("SELECT a.acct, a.cost_center, b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE  b.acct_grp in ('13') AND doc_date BETWEEN '$ydtf' AND '$dt2' $fs GROUP BY a.acct, a.cost_center order by a.cost_center, b.description;");
	while($depRow = $depQuery->fetch_array(MYSQLI_BOTH)) {
		list($curDep) = $mydb->getArray("select sum(debit-credit) as amount from acctg_gl where acct = '$depRow[acct]' and cost_center = '$depRow[cost_center]' and doc_date between '$dtf' and '$dt2' $fs group by acct;");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"($depRow[acct]) $depRow[description]");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$curDep['amount']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$depRow['amount']);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($contentStyle);
		
		$depGT+=$curDep['amount']; $yDepGT+=$depRow['amount']; $row++;
	}

	/* NET INCOME OR LOSS BEFORE DEPRECIATION */
	$row+=2;
	$noiad = $noibd - $depGT; $ynoiad = $ynoibd - $yDepGT;	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'NET INCOME/LOSS AFTER DEPRECIATION');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$noiad);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$ynoiad);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	
	/* Provision for Income Tax */
	$row++;
	$itax = ROUND($noiad * 0.30,2); $yitax = ROUND($ynoiad * 0.30,2);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'PRIVISION FOR INCOME TAX @ 30%');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$itax);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$yitax);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	
	/* Net Income After Tax */
	$row++;
	//$itax = ROUND($noiad * 0.30,2); $yitax = ROUND($ynoiad * 0.30,2);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'NET INCOME AFTER TAX');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,ROUND($noiad-$itax,2));
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,ROUND($ynoiad-$yitax,2));
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,$row)->getNumberFormat()->setFormatCode('#,##0.00');


	foreach(range('B','Z') as $columnID) { $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true); }
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(30);
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("INCOME STATEMENT - CONSOLIDATED");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="incomestatement.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>