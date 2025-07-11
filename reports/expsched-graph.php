<?php
		session_start();
		ini_set("max_execution_time",0);
		ini_set("memory_limit",-1);
		include("../includes/dbUSE.php");

		require_once('../lib/graph/jpgraph.php');
		require_once('../lib/graph/jpgraph_bar.php');
		 
		function separator1000_php($aVal) {
		    if($aVal!=0) { return 'P'.number_format($aVal); } else { return '0'; }
		}

		function barValueFormat($aLabel) {
		    if($aLabel != 0) { return 'P'.number_format($aLabel,2); }
		} 
 
 		if($_GET['branch'] != "") { $fs1 = " and acct_branch = '$_GET[branch]';"; }

		$data1y = array();
		$data2y = array();
		$label = array();

		$dtf = "$_GET[year]-01-01";
		list($dtf2) = getArray("select date_sub('$dtf',INTERVAL 1 YEAR);");

		for($i = 0; $i <= 11; $i++) {
			list($x1,$lbl) = getArray("select date_add('$dtf',INTERVAL $i MONTH), upper(date_format(date_add('$dtf',INTERVAL $i MONTH),'%b'));");
			list($x2) = getArray("select last_day('$x1');");
			list($amt) = getArray("SELECT ifnull(ROUND(SUM(debit-credit),2),0) FROM acctg_gl WHERE company = '$_SESSION[company]' and acct = '$_GET[acct]' AND doc_date BETWEEN '$x1' AND '$x2' $fs1;");
			$data1y[$i] = $amt;

			/* Last Year */
			list($ly_x1) = getArray("select date_add('$dtf2',INTERVAL $i MONTH);");
			list($ly_x2) = getArray("select last_day('$ly_x1');");
			list($amt2) = getArray("SELECT ifnull(ROUND(SUM(debit-credit),2),0) FROM acctg_gl WHERE company = '$_SESSION[company]' and acct = '$_GET[acct]' AND doc_date BETWEEN '$ly_x1' AND '$ly_x2' $fs1;");
			$data2y[$i] = $amt2;

			$label[$i] = $lbl;
		}


		// Create the graph. These two calls are always required
		$graph = new Graph(680,320,'auto');
		$graph->SetScale("textlin");

		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		$graph->SetMargin(100,0,20,0);

		//$graph->yaxis->SetTickPositions(array(0,250000,500000,750000,1000000,1500000), array(125000,250000,375000,500000,750000));
		$graph->SetBox(false);
		$graph->title->Set($_GET[title]);
		$graph->ygrid->SetFill(false);
		$graph->xaxis->SetTickLabels($label);
		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		$graph->yaxis->SetLabelFormatCallback('separator1000_php');

		// Create the bar plots
		$b1plot = new BarPlot($data1y);
		$b1plot->SetLegend("Current Year");

		$b2plot = new BarPlot($data2y);
		$b2plot->SetLegend("Last Year");

		// Create the grouped bar plot
		$gbplot = new GroupBarPlot(array($b1plot,$b2plot));
		// ...and add it to the graPH
		$graph->Add($gbplot);


		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#cc1111");

		$b2plot->SetColor("white");
		$b2plot->SetFillColor("#11cccc");

		// Display the graph
		$graph->Stroke();

	?>