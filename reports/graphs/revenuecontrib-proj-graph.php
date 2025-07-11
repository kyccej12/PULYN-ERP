<?php // content="text/plain; charset=utf-8"
	
	session_start();
	ini_set("display_errors","Off");
	ini_set("max_execution_time",0);
	ini_set("memory_limit",-1);
	include("../../includes/dbUSE.php");

	require_once ('../../lib/graph/jpgraph.php');
	require_once ('../../lib/graph/jpgraph_pie.php');
	require_once ('../../lib/graph/jpgraph_pie3d.php');


	function labelMe($aLabel) {
		if($aLabel > 0) { return '%.1f%%'; }
	} 

	// Some data
	//$data = array(40,60,21,33);
	$data = array();
	$lbl = array();
	$legend = array();

	$i = 0;
	$a = dbquery("SELECT proj_name, ROUND(SUM(credit-debit),2) AS amt FROM acctg_gl a INNER JOIN acctg_accounts b ON a.acct = b.acct_code AND a.company = b.company INNER JOIN options_project c ON a.cost_center = c.proj_id WHERE doc_date BETWEEN '".formatDate($_GET[dtf])."' AND '".formatDate($_GET[dt2])."' AND b.acct_grp in ('10','13') AND a.cost_center != '' GROUP BY a.cost_center UNION ALL SELECT 'Others' AS proj_code, ROUND(SUM(credit-debit),2) AS amt FROM acctg_gl a INNER JOIN acctg_accounts b ON a.acct = b.acct_code AND a.company = b.company WHERE doc_date BETWEEN '".formatDate($_GET[dtf])."' AND '".formatDate($_GET[dt2])."' AND b.acct_grp in ('10','13') AND a.cost_center = '' GROUP BY a.cost_center;");
	while($b = mysql_fetch_array($a)) {
		$data[$i] = $b[1];
		$lbl[$i] = "$b[0] (".convert2Short($b[1]).")";
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