<?php
    include("includes/dbUSE.php");
	
	
    $return.="CREATE DATABASE IF NOT EXISTS `pccmain`;\n\n";
    $return.="USE `pccmain`;\n\n";
 
	@mysql_select_db('pccmain',$con);
 
    $tables = array();
    $result = mysql_query('SHOW TABLES');
    while($row = mysql_fetch_row($result))
    {
      $tables[] = $row[0];
    }

  foreach($tables as $table)
  {
    $result = mysql_query('SELECT * FROM '.$table);
    $num_fields = mysql_num_fields($result);
    
    $return.= 'DROP TABLE IF EXISTS `'.$table.'`;';
    $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
    $return.= "\n\n".$row2[1].";\n\n";
    
    for ($i = 0; $i < $num_fields; $i++) 
    {
      while($row = mysql_fetch_row($result))
      {
        $return.= 'INSERT INTO '.$table.' VALUES(';
        for($j=0; $j<$num_fields; $j++) 
        {
          $row[$j] = addslashes($row[$j]);
		  $row[$j] = preg_replace("/\r\n/","",$row[$j]);
          if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
          if ($j<($num_fields-1)) { $return.= ','; }
        }
        $return.= ");\n";
      }
    }
    $return.="\n\n\n";
  }
  
  //save file
  $filename = "DB".date('Ymdhis').'.sql';
  $handle = fopen("dbbackup/".$filename,'w+');
  fwrite($handle,$return);
  fclose($handle);
  
  $filesize = filesize("dbbackup/".$filename);
  $filetype = filetype("dbbackup/".$filename);

  header("Content-Disposition: attachment; filename=$filename");
  header("Content-length: $filesize");
  header("Content-type: $filetype");
  readfile("dbbackup/$filename");
  
 ?>