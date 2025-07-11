<?php
	
	session_start();
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	include("../../handlers/_generics.php");

	require_once ('../../lib/graph/jpgraph.php');
	require_once ('../../lib/graph/jpgraph_bar.php');

	$data = array();
	$lbl = array();
	$legend = array();

	$i = 0;

	list($t) = $con->getArray("SELECT ROUND(SUM(b.qty*cost),2) AS amount FROM po_header a INNER JOIN po_details b ON a.company=b.company AND a.branch = b.branch AND a.po_no = b.po_no WHERE a.status = 'Finalized' AND po_date BETWEEN '".formatDate($_GET[dtf])."' AND '".formatDate($_GET[dt2])."';");
	$a = $con->dbquery("select * from (SELECT d.description, ROUND(SUM(b.qty*cost),2) AS amount FROM po_header a INNER JOIN po_details b ON a.company=b.company AND a.branch = b.branch AND a.po_no = b.po_no INNER JOIN products_master c ON b.item_code = c.item_code INNER JOIN igroup d ON c.group = d.line_id WHERE a.status = 'Finalized' AND po_date BETWEEN '".formatDate($_GET[dtf])."' AND '".formatDate($_GET[dt2])."' GROUP BY c.group) a order by amount desc;");
	while($b = $a->fetch_array()) {
		
		$perc = ROUND(($b[1]/$t)*100,2);
		
		$data[$i] = $b[1];
		$lbl[$i] = "$b[description] ($perc%)";
		$legend[$i] = number_format($b[1],2);
		$i++;
	}

	
	$width=840;
	$height=240;
	 
	// Set the basic parameters of the graph
	$graph = new Graph($width,$height);
	$graph->SetScale('textlin');
	 
	$top = 20;
	$bottom = 20;
	$left = 200;
	$right = 50;
	$graph->Set90AndMargin($left,$right,$top,$bottom);
	 
	// Nice shadow
	$graph->SetShadow();
	$graph->xaxis->SetTickLabels($lbl);
	$graph->xaxis->SetLabelAlign('right','center','right');
	$graph->yaxis->HideLabels();

	$bplot = new BarPlot($data);
	$bplot->SetFillColor('orange');
	$bplot->SetWidth(0.8);
	$graph->Add($bplot);
	$bplot->value->Show();
	$bplot->value->SetFont(FF_ARIAL,FS_NORMAL,7);
	$bplot->value->SetColor("#4a4a4a");
	$bplot->value->setFormatCallback('convert2Short');
	$graph->Stroke();

?>