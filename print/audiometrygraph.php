<?php // content="text/plain; charset=utf-8"


//ini_set("display_errors","On");

require_once ('../lib/jpgraph/jpgraph.php');
require_once ('../lib/jpgraph/jpgraph_line.php');
require_once ('../handlers/initDB.php');

$con = new myDB();

//$xLabel = array(125,250,500,750,1000,1500,2000,3000,4000,6000,12000,16000);
//$yLabel = array(-10,0,10,20,30,40,50,60,70,80,90,100);
 
 // Some (random) data
//$ydata   = array(11, 3, 8, 12, 5, 1, 9, 13, 5, 7);
//$ydata2  = array(1, 19, 15, 7, 22, 14, 5, 9, 21, 13 );
 
$ydata = $con->getArray("SELECT '-' as zero, if(125_l=0,'-',125_l) as 125_l, if(250_l=0,'-',250_l) as 250_l, if(500_l=0,'0',500_l) as 500_l, if(750_l=0,'-',750_l) as 750_l,  if(1k_l=0,'-',1k_l) AS 1k_l, if(1500_l=0,'-',1500_l) as 1500_l, if(2k_l=0,'-',2k_l) as 2k_l, if(3k_l=0,'-',3k_l) as 3k_l, if(4k_l=0,'-',4k_l) as 4k_l, if(6k_l=0,'-',6k_l) as 6k_l, if(8k_l=0,'-',8k_l) as 8k_l, if(12k_l=0,'-',12k_l) as 12k_l, if(16k_l=0,'-',16k_l) as 16k_l FROM omdc.lab_audiometry WHERE serialno = '$_REQUEST[sn]';");
$ydata2 = $con->getArray("SELECT '-' as zero, if(125_r=0,'-',125_r) as 125_r, if(250_r=0,'-',250_r) as 250_r, if(500_r=0,'-',500_r) as 500_r, if(750_r=0,'-',750_r) as 750_r,  if(1k_r=0,'-',1k_r) as 1k_r, if(1500_r=0,'-',1500_r) as 1500_r, if(2k_r=0,'-',2k_r) as 2k_r, if(3k_r=0,'-',3k_r) as 3k_r, if(4k_l=0,'-',4k_r) as 4k_r, if(6k_r=0,'-',6k_r) as 6k_r, if(8k_r=0,'-',8k_r) as 8k_r, if(12k_r=0,'-',12k_r) as 12k_r, if(16k_r=0,'-',16K_r) as 16k_r FROM omdc.lab_audiometry WHERE serialno = '$_REQUEST[sn]';");

//$lbl = array('0','125hz','250hz','500hz','750hz','1000hz','1500hz','2000hz','3000hz','4000hz','6000hz','8000hz','12000hz','16000hz');

$xdata = array();
$xdata2 = array();
$i = 0;
//foreach($ydata as $value) {
  // if($value > 0) {
   //     $xdata[$i] = $value;
   //      $i++;
  //  }
//}

$i = 0;
//foreach($ydata2 as $value) {
//   if($value > 0) {
//        $xdata2[$i] = $value;
//        $i++;
//    }
//}


// Size of the overall graph
$width=640;
$height=400;
 
// Create the graph and set a scale.
// These two calls are always required
$graph = new Graph($width,$height,'auto');
$graph->SetScale('textlin',-10,50);
$graph->SetShadow();
 
// Setup margin and titles
$graph->SetMargin(40,20,30,40);
// Adjust the X-axis
$graph->xaxis->SetPos("max");
$graph->xaxis->SetLabelSide(SIDE_UP);
$graph->xaxis->SetTickSide(SIDE_DOWN);
$graph->xaxis->title->Set('Frequency in Hertz');
$graph->xaxis->SetTickLabels(["0","125","250","500","750","1k","1.5k","2k","3k","4k","6k","8k","12k","16k","","","","","","","","","","","","","",""]);
$graph->yaxis->title->Set('Hearing Level in DB (ANSI 1961');
$graph->xaxis->scale->ticks->SetSize(8,3);
$graph->xaxis->scale->ticks->SetWeight(2);

$graph->xgrid->Show(); 


//$graph->xaxis->SetTickLabels($xLabel);
//$graph->yaxis->SetTickLabels($yLabel);
$graph->yaxis->title->SetFont( FF_FONT1 , FS_BOLD );
$graph->xaxis->title->SetFont( FF_FONT1 , FS_BOLD );

 
// Create the first data series
$lineplot=new LinePlot($ydata);
$lineplot->SetColor('blue');
$lineplot->mark->SetType(MARK_X);
$lineplot->mark->SetColor('blue');
$lineplot->SetWeight(2);   // Two pixel wide
 
// Add the plot to the graph
$graph->Add($lineplot);
 
// Create the first data series
$lineplot2=new LinePlot($ydata2);
$lineplot2->SetColor('red');
$lineplot2->mark->SetType(MARK_CIRCLE);
$lineplot2->mark->SetColor('red');
$lineplot2->SetWeight(2);   // Two pixel wide
 
// Add the plot to the graph
$graph->Add($lineplot2);
 
// Display the graph
$graph->Stroke();
?>