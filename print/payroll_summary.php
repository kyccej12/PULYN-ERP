<?php

	//include("../includes/dbUSE.php");
	include("../includes/dbUSE.php");
	require_once '../lib/PHPExcel/PHPExcel.php';

	include("class.employee.php");

	if(isset($_REQUEST['dept']) && $_REQUEST['dept'] !=''){
		$dept = " AND department = '".$_REQUEST['dept']."' ";
	}

	date_default_timezone_set('Asia/Manila');
	set_time_limit(0);
	session_start();
	
	

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
	
	$uid = $_SESSION['userid'];	
	$now = date("m/d/Y h:i a");
	

/* MYSQL QUERIES SECTION */

/* MYSQL QUERIES SECTION */
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(9);
	$objPHPExcel->getProperties()->setCreator("PORT 80")
								 ->setLastModifiedBy("Root Admin")
								 ->setTitle("hris - Employee List")
								 ->setSubject("hris - Employee List")
								 ->setDescription("hris - Employee List")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Exported File");
	
	//$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A6","Employee Name"); // 0
	
	$_colheader = dbquery("SELECT line_id,col,description FROM hris.payroll_header ORDER BY line_id;");
	while($ind = mysql_fetch_array($_colheader)){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("$ind[col]".'6',"$ind[description]");
		$objPHPExcel->getActiveSheet()->getColumnDimension("$ind[col]")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getStyle("$ind[col]".'6')->applyFromArray($headerStyle);
	}
	
	$col_total = array();
	
	
	
	$q = mysql_query("SELECT company,branch,id_no, CONCAT(lname,', ',fname) AS emp, tax_bracket, designation, department, if(pay_type='SEMI',monthly_rate,daily_rate) as basic_rate, daily_rate, monthly_rate, pay_type, IF(pay_type='SEMI','Monthly','Daily') AS ptype, rice_subsidy, clothing, laundry, insurance, other_non_tax,`status` from hris.e_master a WHERE company = '$_SESSION[company]' and `status` NOT IN ('Terminated','Resigned') AND id_no != '' and filestatus = 'Active' $dept ORDER BY lname ASC;");
	//$str = "SELECT company,branch,id_no, CONCAT(lname,', ',fname) AS emp, tax_bracket, designation, department, if(pay_type='SEMI',monthly_rate,daily_rate) as basic_rate, daily_rate, monthly_rate, pay_type, IF(pay_type='SEMI','Monthly','Daily') AS ptype, rice_subsidy, clothing, laundry, insurance, other_non_tax,`status` from hris.e_master a WHERE company = '$_SESSION[company]' and `status` NOT IN ('Terminated','Resigned') AND id_no != '' and filestatus = 'Active' $dept ORDER BY lname ASC;";
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1",$str);
	$row = 7; $total_db = 0; $total_cr = 0;
	while($res = mysql_fetch_array($q)){ $sub='';

			
		$emp = new Employee($res['id_no'],$_REQUEST['period_id']);
		
		$_colheader = dbquery("SELECT line_id,php_var,col,description FROM hris.payroll_header ORDER BY line_id;");
		
		$insquery=""; 
		$insval = "'".$res['id_no']."','".$_REQUEST['period_id']."'";
		while($ind = mysql_fetch_array($_colheader)){
			$col_vaue = $emp->out($ind[line_id]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("$ind[col]".$row,$col_vaue);
			
			if($ind[line_id]>2){
				$col_total[$ind['line_id']] +=$col_vaue;
				$objPHPExcel->getActiveSheet()->getStyle("$ind[col]".'6')->applyFromArray($headerStyle);
				$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($ind['line_id'],$row)->getNumberFormat()->setFormatCode('#,##0.00');
			}
			
			if($ind[line_id]>3){
				$insquery .= mysql_real_escape_string(",`".$ind['php_var']."`");
				$insval .= ",'".$col_vaue."'";
			}
			
		}
		if($_REQUEST['final']=='Y'){
			$main_query= "INSERT IGNORE INTO hris.paysum (id_no,period_id".$insquery.") values (".$insval.");";
			dbquery("INSERT INTO hris.query_log (qry) VALUES ('".mysql_real_escape_string($main_query)."');");
			dbquery($main_query);
			$emp->postLoan($emp->id_no,$emp->period,$emp->ddsched);
		}			
			
			
		$row++;	  
	}
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle("Employee Master File");
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
			
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="employee.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>