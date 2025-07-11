<?php
	include_once("../lib/PHPExcel/PHPExcel.php");
	date_default_timezone_set('Asia/Manila');
	$objPHPExcel = new PHPExcel();
	$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);

	$format = 'dd/mm/yyyy';
	for ($i = 1; $i < 5; ++$i)
	{
		$date = new DateTime('2016-12-0'.$i);
		$sheet->setCellValueByColumnAndRow(0, $i, 
										   PHPExcel_Shared_Date::PHPToExcel( $date ));

		$sheet->getStyleByColumnAndRow(0, $i)
			->getNumberFormat()->setFormatCode($format);
	}

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("test.xlsx");
?>