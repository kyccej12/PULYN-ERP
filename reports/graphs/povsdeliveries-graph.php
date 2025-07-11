<?php
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	include("../../includes/dbUSE.php");
	
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
		list($dtf,$dt2,$month) = getArray("select date_add('$xdtf',INTERVAL $i MONTH),last_day(date_add('$xdtf',INTERVAL $i MONTH)), upper(date_format(date_add('$xdtf',INTERVAL $i MONTH),'%b'));");
		
		list($amt,$amt2) = getArray("SELECT ROUND(SUM(b.qty*b.cost),2) AS amount, ROUND(SUM(qty_dld*cost),2) AS dld_amount FROM po_header a INNER JOIN po_details b ON a.company=b.company AND a.branch = b.branch AND a.po_no = b.po_no WHERE a.status = 'Finalized' AND po_date BETWEEN '$dtf' AND '$dt2';");
	
		$data[$i] = $amt;
		$data2[$i] = $amt2;

		$lbl[$i] = $month;
		$amt = 0; $amt2 = 0;
	}
 

	require_once('../../lib/graph/jpgraph.php');
	require_once('../../lib/graph/jpgraph_bar.php');
	require_once('../../lib/graph/jpgraph_line.php');


	$graph = new Graph(800,280,'auto');
	$graph->SetScale("textlin");
	$theme_class=new UniversalTheme;
	$graph->SetTheme($theme_class);
	$graph->SetMargin(100,0,20,0);
	$graph->SetBox(false);
	$graph->ygrid->SetFill(false);
	$graph->xaxis->SetTickLabels($lbl);
	$graph->yaxis->HideLine(false);
	$graph->yaxis->HideTicks(false,false);
	$graph->yaxis->SetLabelFormatCallback('separator1000_php');
	
	$b1plot = new BarPlot($data);
	$b1plot->SetLegend("Purchase Order (In Peso Value)");
	$b2plot = new BarPlot($data2);
	$b2plot->SetLegend("Delivered (In Peso Value)");

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