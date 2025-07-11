<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	include("../../handlers/_generics.php");
	$con = new _init;

	$xdtf = date('Y-01-01');
	
	function separator1000_php($aVal) {
	    if($aVal!=0) { return 'P'.number_format($aVal); } else { return '0'; }
	}

	function barValueFormat($aLabel) {
	    if($aLabel != 0) { return 'P'.number_format($aLabel,2); }
	}

	$data = array();
	$data2 = array();
	$avg = array();
	$lbl = array();

	for($i = 0; $i <= 11; $i++) {
		
		list($dtf,$dt2,$month) = $con->getArray("select date_add('$xdtf',INTERVAL $i MONTH),last_day(date_add('$xdtf',INTERVAL $i MONTH)), upper(date_format(date_add('$xdtf',INTERVAL $i MONTH),'%b'));");
		list($ly_dtf,$ly_dt2) = $con->getArray("select date_sub('$dtf',INTERVAL $i YEAR),last_day(date_sub('$dt2',INTERVAL $i YEAR));");
		
		list($amt) = $con->getArray("SELECT SUM(amountPaid) FROM cr_header WHERE `status` = 'Finalized' AND crDate between '$dtf' and '$dt2';");
		list($amt2) = $con->getArray("SELECT SUM(amountPaid) FROM cr_header WHERE `status` = 'Finalized' AND crDate between '$ly_dtf' and '$ly_dtf';");
	
		$data[$i] = $amt;
		$data2[$i] = $amt2;

		$lbl[$i] = $month;
		$amt = 0; $amt2 = 0;
	}
 

	require_once('../../lib/graph/jpgraph.php');
	require_once('../../lib/graph/jpgraph_bar.php');


	$graph = new Graph(800,200,'auto');
	$graph->SetScale("textlin");
	$theme_class=new UniversalTheme;
	$graph->SetTheme($theme_class);
	$graph->SetMargin(100,0,25,0);
	$graph->SetBox(false);
	$graph->ygrid->SetFill(false);
	$graph->xaxis->SetTickLabels($lbl);
	$graph->yaxis->HideLine(false);
	$graph->yaxis->HideTicks(false,false);
	$graph->yaxis->SetLabelFormatCallback('separator1000_php');
	
	$b1plot = new BarPlot($data);
	$b1plot->SetLegend("Current Period");
	$b2plot = new BarPlot($data2);
	$b2plot->SetLegend("Last Year");
	

	$b1plot->SetColor("white");
	$b1plot->SetFillColor("#11cccc");
	
	$b2plot->SetColor("white");
	$b2plot->SetFillColor("#1111cc");

	$gbplot = new GroupBarPlot(array($b1plot,$b2plot));
	$graph->Add($gbplot);
	
	$b1plot->value->Show();
	$b1plot->value->SetFont(FF_ARIAL,FS_NORMAL,7);
	$b1plot->value->SetColor("#4a4a4a");
	$b1plot->value->setFormatCallback('convert2Short');
	$b1plot->value->setAngle(90);
	
	$b2plot->value->Show();
	$b2plot->value->SetFont(FF_ARIAL,FS_NORMAL,7);
	$b2plot->value->SetColor("#4a4a4a");
	$b2plot->value->setFormatCallback('convert2Short');
	$b2plot->value->setAngle(90);
	
	$graph->Stroke();

?>