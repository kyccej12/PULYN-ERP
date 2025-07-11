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
		$dtf = "2018-12-31";
		list($dt2,$ending) = $mydb->getArray("select last_day('$_GET[year]-$_GET[month]-01'),date_format(last_day('$_GET[year]-$_GET[month]-01'),'%M %Y');");
		$co = $mydb->getArray("select * from companies where company_id = '$_SESSION[company]';");
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
								 ->setTitle("$co[company_name] - BALANCE SHEET")
								 ->setSubject("$co[company_name] - BALANCE SHEET")
								 ->setDescription("$co[company_name] - BALANCE SHEET")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3","$co[tel_no]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","$co[website]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","BALANCE SHEET ENDING $ending");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A5","");
	
	
	/* Cash & Cash Equivalents */
	$row = 7;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A6","CASH & CASH EQUIVALENTS");
	$objPHPExcel->getActiveSheet()->getStyle("A6")->applyFromArray($headerStyle);
	$a = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct = b.acct_code WHERE b.acct_grp = '1' AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	while($b = $a->fetch_array(MYSQLI_BOTH)) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"($b[acct]) $b[description]");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$b['amount']);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$cceGT+= $b['amount']; $row++;
	}
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'TOTAL CASH & CASH EQUIVALENTS');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$cceGT);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');

	/* ACCOUNTS RECEIVABLE */
	$row+=2;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'ACCOUNTS RECEIVABLE');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($headerStyle);
	
	$c = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp in ('2') AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	while($d = $c->fetch_array(MYSQLI_BOTH)) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"($d[acct]) $d[description]");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$d['amount']);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$arGT+= $d['amount']; $row++;
	}
	
	if(!$c) { $row++; }
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'TOTAL ACCOUNTS RECEIVABLE');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$arGT);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');

	/* PROPERTIES & EQUIPMENT */
	$row+=2;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'PROPERTIES & EQUIPMENT');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($headerStyle);
	
	$g = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '3' AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	while($h = $g->fetch_array(MYSQLI_BOTH)) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"($h[acct]) $h[description]");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$h['amount']);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$peGT+=$h['amount']; $row++;
	}
	
	if(!$g) { $row++; }
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'TOTAL PROPERTIES & EQUIPMENT');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$peGT);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');

	/* OTHER CURRENT & NONCURRENT ASSETS */
	$row+=2;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'CURRENT/NON-CURRENT ASSETS');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($headerStyle);
	
	$k = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(debit-credit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp in ('4','5') AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	$row++;
	while($l = $k->fetch_array(MYSQLI_BOTH)) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"($l[acct]) $l[description]");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$l['amount']);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$ncaGT+=$l['amount']; $row++;
	}
	
	if(!$k) { $row++; }
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'TOTAL OTHER CURRENT/NON-CURRENT ASSETS');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$ncaGT);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');

	/* TOTAL ASSETS */
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'TOTAL ASSETS');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,ROUND(($cceGT+$arGT+$peGT+$ncaGT),2));
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	
	/* CURRENT LIABILITIES */
	$row+=2;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'CURRENT LIABILITIES');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($headerStyle);
	
	$m = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '6' AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	$row++;
	while($n = $m->fetch_array(MYSQLI_BOTH)) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"($n[acct]) $n[description]");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$n['amount']);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$clGT+=$n['amount']; $row++;
	}
	
	if(!$m) { $row++; }
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'TOTAL CURRENT LIABILITIES');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$clGT);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	
	/* OTHER CURRENT LIABILITIES */
	$row+=2;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'OTHER CURRENT LIABILITIES');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($headerStyle);
	
	$o = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp in ('7') AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	$row++;
	while($p = $o->fetch_array(MYSQLI_BOTH)) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"($p[acct]) $p[description]");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$p['amount']);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$oclGT+=$p['amount']; $row++;
	}
	
	if(!$o) { $row++; }
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'TOTAL OTHER CURRENT LIABILITIES');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$oclGT);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	
	$row+=2;
	list($income) = $mydb->getArray("SELECT ROUND(SUM(credit-debit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp in ('9','10','11','12','13','14') AND doc_date BETWEEN '$dtf' AND '$dt2';");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'ACCUMULATED INCOME OR LOSS');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$income);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	
	/* EQUITIES */
	$row+=2;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'EQUITY');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($headerStyle);
	
	$s = $mydb->dbquery("SELECT a.acct, b.description, ROUND(SUM(credit-debit),2) AS amount FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct=b.acct_code WHERE b.acct_grp = '8' AND doc_date BETWEEN '$dtf' AND '$dt2' GROUP BY acct;");
	$row++;
	while($t = $s->fetch_array(MYSQLI_BOTH)) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,"($t[acct]) $t[description]");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$t['amount']);
		
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($contentStyle);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($contentStyle);
		$eqGT+=$t['amount']; $row++;
	}
	
	if(!$s) { $row++; }
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'TOTAL EQUITY');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$eqGT);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,'TOTAL LIABILITIES & EQUITIES');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,ROUND(($clGT+$oclGT+$income+$eqGT),2));
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($totalStyleLabel);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($totalStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->getNumberFormat()->setFormatCode('#,##0.00');

	foreach(range('B','Z') as $columnID) { $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true); }
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(30);
	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("INCOME STATEMENT - CONSOLIDATED");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="balancesheet.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>