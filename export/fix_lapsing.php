<?php
	session_start();
	require_once '../lib/PHPExcel/PHPExcel.php';
	include("../handlers/_generics.php");
	$con = new _init;

	date_default_timezone_set('Asia/Manila');
	ini_set("memory_limit","5048M");
	set_time_limit(0);
		
	$co = $con->getArray("select * from companies where company_id = '$_SESSION[company]';");
	
	
	$cyear = $_REQUEST['cyear'];
	if(isset($_REQUEST['cmonth']) && $_REQUEST['cmonth']!=''){
		$filterM = " AND line_id <= '$_REQUEST[cmonth]'  ";
		$cmonth = $_REQUEST['cmonth'];
	}
		
	
	//SELECT line_id,month_name FROM lapsing_month WHERE 1=1 $filterM  ORDER BY line_id;
	$month_header = $con->dbquery("SELECT line_id,month_name FROM lapsing_month WHERE 1=1 $filterM  ORDER BY line_id;");
	
	/* MYSQL QUERIES SECTION */
		$date = $con->formatDate($_GET['asof']);
		$now = date("m/d/Y h:i a");
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
								 ->setTitle("Port80 Business Solutions - ITEMS LIST")
								 ->setSubject("Port80 Business Solutions - ITEMS LIST")
								 ->setDescription("Port80 Business Solutions - ITEMS LIST")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$co['company_name']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A2",$co['company_address']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A3",$co['tel_no']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A4","");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A6","ASSET NO");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B6","PARTICULARS");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C6","CATEGORY");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D6","LOCATION");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E6","ACQUISITION DATE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F6","LIFE SPAN");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G6","GROSS OF VAT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H6","COST (NET OF VAT)");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I6","MONTHLY DEPN");
	
	$rowHead=6; $ch =9;
	while($col_h = mysql_fetch_array($month_header)){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($ch,$rowHead,$col_h[month_name]);
		//$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($ch,$rowHead)->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($ch,$rowHead)->applyFromArray($headerStyle);
		$ch++;
	}

	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($ch,$rowHead,"BOOK VALUE");
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($ch,$rowHead)->applyFromArray($headerStyle);
	
	//$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
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
	$objPHPExcel->getActiveSheet()->getStyle('M6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('N6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('O6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('P6')->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('Q6')->applyFromArray($headerStyle);

	
	$z = $con->dbquery("SELECT * FROM (SELECT a.payee AS supplier, c.tradename AS supplier_name, IFNULL(IF('$date' <= DATE_ADD(ref_date,INTERVAL c.terms DAY),'0',DATEDIFF('$date',DATE_ADD(ref_date,INTERVAL c.terms DAY))),0) AS daysdue, credit-debit AS amount FROM cv_header a INNER JOIN cv_details b ON a.cv_no = b.cv_no INNER JOIN contact_info c ON a.payee = c.file_id WHERE a.status = 'Posted' AND b.acct = '$_GET[acct]' UNION ALL SELECT a.cid AS supplier, c.tradename AS supplier_name, IFNULL(IF('$date' <= DATE_ADD(ref_date,INTERVAL c.terms DAY),'0',DATEDIFF('$date',DATE_ADD(ref_date,INTERVAL c.terms DAY))),0) AS daysdue, credit-debit AS amount FROM dc_header a INNER JOIN dc_details b ON a.doc_no = b.doc_no INNER JOIN contact_info c ON a.cid = c.file_id WHERE a.status = 'Posted' AND b.acct = '$_GET[acct]' UNION ALL SELECT a.supplier, a.supplier_name, IFNULL(IF('$date' <= DATE_ADD(apv_date,INTERVAL c.terms DAY),'0',DATEDIFF('$date',DATE_ADD(apv_date,INTERVAL c.terms DAY))),0) AS daysdue, credit-debit AS amount FROM apv_header a INNER JOIN apv_details b ON a.apv_no = b.apv_no INNER JOIN contact_info c ON a.supplier = c.file_id WHERE a.status = 'Posted' AND b.acct = '$_GET[acct]' UNION ALL SELECT b.client AS supplier, c.tradename AS supplier_name, IFNULL(IF('$date' <= DATE_ADD(ref_date,INTERVAL c.terms DAY),'0',DATEDIFF('$date',DATE_ADD(ref_date,INTERVAL c.terms DAY))),0) AS daysdue, credit-debit AS amount FROM journal_header a INNER JOIN journal_details b ON a.j_no = b.j_no INNER JOIN contact_info c ON b.client = c.file_id WHERE a.status = 'Posted' AND b.acct = '$_GET[acct]') a ORDER BY supplier_name, daysdue");
 
	$row1 =8;
	$cat_looper = $con->dbquery("SELECT id,category FROM 4dventures.fa_category;");
	
	while($indx_cat = $cat_looper->fetch_array()){

	  $assetByCat = $con->dbquery("SELECT *,CONCAT(iyear,'-',LPAD(imonth,2,0),'-01') AS lap_start,CONCAT(lyear,'-',LPAD(lmonth,2,0),'-01') AS lap_end FROM (
				SELECT asset_no,asset_description,date_acquired,cost,if(vatable='Y',cost/1.12,cost) as cost2,life_span,ROUND(if(vatable='Y',cost/1.12,cost)/(life_span*12),2) AS monthly,proj_code,
				MONTH(DATE_ADD(date_acquired,INTERVAL 1 MONTH)) AS imonth,YEAR(DATE_ADD(date_acquired,INTERVAL 1 MONTH)) AS iyear
				, MONTH(DATE_ADD(date_acquired,INTERVAL (life_span*12)+1 MONTH)) AS lmonth,YEAR(DATE_ADD(date_acquired,INTERVAL (life_span*12)+1 MONTH)) AS lyear
				FROM 4dventures.fa_master WHERE category = '$indx_cat[id]' ORDER BY asset_description
				) a;");
		while($row = $assetByCat->fetch_array()){
			
			list($proj) = $con->getArray("SELECT proj_name FROM 4dventures.options_project WHERE proj_id = '$row[proj_code]';");
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row1,$row[asset_no]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row1,$row[asset_description]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row1,$indx_cat[category]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row1,$proj);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row1,$row['date_acquired']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row1,$row['life_span']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row1,$row['cost']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row1,$row['cost2']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row1,$row['monthly']);
				$cl=9;
				//if($cmonth!='' &&$cmonth!=0){ $im = $cmonth; $imax=$cmonth;} else{ $im=1;$imax=12; $cmonth=12; }
				if($cmonth!='' &&$cmonth!=0){ $im = 1; $imax=$cmonth;} else{ $im=1;$imax=12; $cmonth=12; }
				
				for($ix=$im;$ix<=$imax;$ix++){
					$str_date = $cyear.'-'.str_pad($ix,2,'0',STR_PAD_LEFT).'-'.'01';
					list($flag) = $con->getArray("SELECT IF('$str_date' BETWEEN '$row[lap_start]' AND '$row[lap_end]','Y','N') AS flag;");
					//$monthly = "SELECT IF('$str_date' BETWEEN '$row[lap_start]' AND '$row[lap_end]','Y','N') AS flag;";
					if($flag=='Y'){
						$monthly = $row[monthly];
					}else{
						$monthly = '';
					}

					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($cl,$row1,$monthly);
					$cl++;
				}


				$date_e = $cyear.'-'.$cmonth.'-01';
				list($elapsed) = $con->getArray("SELECT TIMESTAMPDIFF(MONTH,'$row[lap_start]','$date_e')+1;");	
					$totlDpn = $elapsed * $monthly;
					if($totlDpn >= $row['cost2']){
						$totlDpn = $row['cost2'];
					}else{
						$totlDpn = $totlDpn;
					}
					$bookValue = $row['cost2'] - $totlDpn;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($cl,$row1,$bookValue);


				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(4,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(5,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(9,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(11,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(12,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(13,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(14,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(15,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(16,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(17,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(18,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(19,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(20,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				//$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(17,$row1)->getNumberFormat()->setFormatCode('#,##0.00');
				
				$row1++;
		}
	}
			
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8');
	header('Content-Disposition: attachment;filename="fix_asset_lapsing.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>