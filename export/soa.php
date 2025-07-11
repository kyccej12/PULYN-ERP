<?php
	session_start();
	require_once '../lib/PHPExcel/PHPExcel.php';
	include("../handlers/_generics.php");
	$con = new _init;

	date_default_timezone_set('Asia/Manila');
	set_time_limit(0);
	
    $co = $con->getArray("select * from companies where company_id = '1';");
	$cso = $con->getArray("SELECT a.trace_no, LPAD(a.soa_no,6,'0') AS soano, a.customer_code AS cid, a.customer_name AS cname, a.customer_address AS caddr, a.remarks, c.description AS terms, DATE_FORMAT(soa_date,'%M %d, %Y') AS soadate, soa_date, b.tel_no, b.tin_no, b.cperson, a.created_by FROM soa_header a LEFT JOIN contact_info b ON a.customer_code = b.file_id LEFT JOIN options_terms c ON a.terms = c.terms_id WHERE a.soa_no = '$_REQUEST[soa_no]' AND a.branch = '$_SESSION[branchid]';");
	$d = $con->dbquery("SELECT LPAD(a.so_no,6,'0') AS sono, DATE_FORMAT(a.so_date,'%m/%d/%Y') AS sodate, a.so_type, a.pid, IF(c.gender='M','Male','Female') AS gender, b.customer_name, FLOOR(ROUND(DATEDIFF(a.so_date,c.birthdate) / 364.25,2)) AS age, DATE_FORMAT(b.from,'%m/%d/%Y') AS `from`, DATE_FORMAT(b.until,'%m/%d/%Y') AS UNTIL, a.pname, c.patient_id, DATE_FORMAT(c.birthdate,'%d %b %Y') AS bday, c.birthdate, a.`description`, a.amount, YEAR(b.cso_date) - YEAR(c.birthdate) AS age, c.badge_no FROM soa_details a LEFT JOIN omdcmobile.cso_header b ON a.so_no = b.cso_no LEFT JOIN patient_info c ON a.pid = c.patient_id  WHERE a.soa_no = '$_REQUEST[soa_no]' AND a.branch = '$_SESSION[branchid]' ORDER BY pname ASC;");	


	$headerStyle = array(
		'font' => array('bold' => true,'color' => array('rgb' => 'FFFFFF')),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
		'borders' => array('outline' => array('style' =>PHPExcel_Style_Border::BORDER_THIN)),
		'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '0066CC'))
	);
	
	$contentStyle = array(
		'borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);

	$contentStyle2 = array(
		'borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	);
	
	$totalStyle = array(
		'font' => array('bold' => true),
		'borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);
	$totalStyle = array(
		'font' => array('bold' => true),
		'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),'bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))
	);

	$centerStyle = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
    );
	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(9);
	$objPHPExcel->getProperties()->setCreator("Root Admin")
								 ->setLastModifiedBy("Root Admin")
								 ->setTitle("$co[company_name] - Mobile Result Summary")
								 ->setSubject("$co[company_name] - Mobile Result Summary")
								 ->setDescription("$co[company_name] - Mobile Result Summary")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,1,"$co[company_name]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,2,"$co[company_address]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,3,"$co[tel_no]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,4,"STATEMENT OF ACCOUNT");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,6,"Billed To:");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,6,"$cso[cname]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,7,"Billing Address:");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,7,"$cso[caddr]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,8,"Company:");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,8,"$cso[cname]");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,9,"SOA Date:");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,9,"$cso[soadate]");
	
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,1)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,2)->getFont()->setItalic(true);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,3)->getFont()->setItalic(true);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,4)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,6)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,7)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,8)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,9)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,10)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,11)->getFont()->setBold(true);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,11,"NO.");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,11,"PATIENT ID");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,11,"BADGE NO");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,11,"PATIENT NAME");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,11,"AGE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,11,"GENDER");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,11,"BIRTHDATE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,11,"AGE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,11,"SOA NO");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,11,"DATE AVAILED");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,11,"PROCEDURE");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,11,"AMOUNT");
	
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,11)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,11)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(2,11)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(3,11)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(4,11)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(5,11)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(6,11)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,11)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(8,11)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(9,11)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10,11)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(11,11)->applyFromArray($headerStyle);
	
	$row = 12; $i = 1;
	while($data = $d->fetch_array()) {

        list($soano) = $con->getArray("select soa_no from soa_header where soa_no= '$row[soa_no]';");
		list($employer) = $con->getArray("select employer from patient_info where patient_id = '$row[patient_id]';");

		if($data['so_type'] == 'CSO') {
			list($examin_d8) = $con->getArray("SELECT DATE_FORMAT(examined_on,'%m/%d/%Y') FROM omdcmobile.peme WHERE pid = '$data[pid]' and so_no = '$data[sono]';");
	}
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$row,$i);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$row,$data['patient_id']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$row,$data['badge_no']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$row,html_entity_decode($data['pname']));
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$row,$data['age']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$row,$data['gender']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$row,$data['bday']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$row,$data['age']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$row,$data['soano']);

		if($data['so_type'] == 'SO') {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$data['sodate']);
		}else {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$row,$examin_d8);
		}
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$row,$data['description']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$row,$data['amount']);

		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(5,$row)->applyFromArray($centerStyle);
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(1,$row)->applyFromArray($centerStyle);
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0,$row)->applyFromArray($centerStyle);
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(7,$row)->applyFromArray($centerStyle);
		
		/* NUMBER FORMAT */
		$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(11,$row)->getNumberFormat()->setFormatCode('#,##0.00');

		for($contentLoop = 0; $contentLoop <= 11; $contentLoop++) {
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($contentLoop,$row)->applyFromArray($contentStyle);
		}
		$row++; $i++; $gt+=$data['amount'];
	}

	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(24);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(24);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(48);
	$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(16);

    /* TOTAL */
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$row,$gt);

	/* NUMBER FORMAT */
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(11,$row)->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(11,$row)->applyFromArray($totalStyle);

	// Rename worksheet
	// $objPHPExcel->getActiveSheet()->freezePane('A12');
	$objPHPExcel->getActiveSheet()->setTitle("Statement of Account");
    
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="soa.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>