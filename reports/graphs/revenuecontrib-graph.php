<?php // content="text/plain; charset=utf-8"
	
	session_start();
	ini_set("display_errors","Off");
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	include("../../handlers/_generics.php");
	$con = new _init;

	require_once ('../../lib/graph/jpgraph.php');
	require_once ('../../lib/graph/jpgraph_pie.php');
	require_once ('../../lib/graph/jpgraph_pie3d.php');


	function labelMe($aLabel) {
		if($aLabel > 0) { return '%.1f%%'; }
	} 

	$data = array();
	$lbl = array();
	$legend = array();

	$i = 0;
	$a = $con->dbquery("SELECT b.description, ROUND(SUM(credit-debit),2) AS amt FROM acctg_gl a LEFT JOIN acctg_accounts b ON a.acct = b.acct_code WHERE doc_date BETWEEN '".$con->formatDate($_GET[dtf])."' AND '".$con->formatDate($_GET[dt2])."' AND b.acct_grp in ('9','10') GROUP BY a.acct;");
	while($b = $a->fetch_array()) {
		$data[$i] = $b[1];
		$lbl[$i] = "$b[0] (".$con->convert2Short($b[1]).")";
		//$legend[$i] = number_format($b[1],2);
		$i++;
	}

	$graph = new PieGraph(500,320);
	$graph->SetShadow();
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	 
	$p1 = new PiePlot($data);
	$p1->SetSize(0.25);
	$p1->SetCenter(0.80,0.50);
	$p1->SetGuideLines(true,false);
	$p1->SetGuideLinesAdjust(1);
	$p1->ExplodeAll(7);
	$p1->SetLabels($lbl);
	$p1->SetLabelPos(1);
	//$p1->SetLegends($legend);
	 
	$graph->Add($p1);
	$graph->Stroke();
 

?>