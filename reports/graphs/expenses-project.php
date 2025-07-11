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
	$lbl = array();

	$z = dbquery("SELECT proj_code, ROUND(SUM(debit-credit),2) AS amt FROM acctg_gl a INNER JOIN acctg_accounts b ON a.acct = b.acct_code AND a.company = b.company INNER JOIN options_project c ON a.cost_center = c.proj_id WHERE doc_date BETWEEN '".formatDate($_GET[dtf])."' AND '".formatDate($_GET[dt2])."' AND b.acct_grp = '12' AND a.cost_center != '' GROUP BY a.cost_center UNION ALL SELECT 'Others' AS proj_code, ROUND(SUM(debit-credit),2) AS amt FROM acctg_gl a INNER JOIN acctg_accounts b ON a.acct = b.acct_code AND a.company = b.company WHERE doc_date BETWEEN '".formatDate($_GET[dtf])."' AND '".formatDate($_GET[dt2])."' AND b.acct_grp = '12' AND a.cost_center = '' GROUP BY a.cost_center");

	$i = 0;
	while($y = mysql_fetch_array($z)) {
		$data[$i] = $y[1];
		$lbl[$i] = $y[0];
		$i++;
	}
 

	require_once('../../lib/graph/jpgraph.php');
	require_once('../../lib/graph/jpgraph_bar.php');




	$graph = new Graph(800,200,'auto');
	$graph->SetScale("textlin");
	$theme_class=new UniversalTheme;
	$graph->SetTheme($theme_class);
	
	$top = 20;
	$bottom = 40;
	$left = 100;
	$right = 10;
	$graph->SetMargin($left,$right,$top,$bottom);
	//$graph->SetMargin(100,0,20,0);

	$graph->SetBox(false);
	$graph->ygrid->SetFill(false);
	$graph->xaxis->SetTickLabels($lbl);
	$graph->yaxis->HideLine(false);
	$graph->yaxis->HideTicks(false,false);
	$graph->yaxis->SetLabelFormatCallback('separator1000_php');
	
	$b1plot = new BarPlot($data);
	$b1plot->SetColor("white");
	$b1plot->SetFillColor("#11cccc");
	$b1plot->SetWidth(0.5);

	$graph->Add($b1plot);
	
	$b1plot->value->Show();
	$b1plot->value->SetFont(FF_ARIAL,FS_NORMAL,7);
	$b1plot->value->SetColor("#4a4a4a");
	$b1plot->value->setFormatCallback('convert2Short');
	//$b1plot->value->SetAngle(60); 
	
	$graph->Stroke();

?>