<?php
	session_start();
	include("includes/dbUSE.php");	

	function deptlist(){
		$qry = dbquery("select dept_code,dept_name from options_dept;");
				while($row = mysql_fetch_array($qry)){
					$opt .="<option value='$row[dept_code]'>$row[dept_name]</option>";
				}
				return $opt;
	}

	function yearlist(){
		$qry = dbquery("select year(curdate())-1 as yr union all select year(curdate()) AS yr union all select year(curdate())+1 AS yr;");
		while($row = mysql_fetch_array($qry)){
			$opt .="<option value='$row[yr]' >$row[yr]</option>";
		}
		return $opt;
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Ozian Realty Development & Services, Incorporated</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script language="javascript" src="js/tableH.js"></script>
<script>

	var UID = "";
	
	function selectFA(obj) {
		gObj = obj;
		$(obj).closest("tr").siblings().removeClass("diffcolor");
		$(obj).toggleClass("diffcolor");
		tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); UID = tmp_obj[1];
	}
	
	function reloadDays(){
		$.post('schedule.datacontrol.php',{mod:'reloadDays',year: $("#slctYear").val() ,month:$("#slctMonth").val() },function(data){
			$("#slctDay").html(data);
		});
	}

	function loadDataTable(){
		var date = $("#slctDay").val();
		var yr = $("#slctYear").val();
		var month = $("#slctMonth").val();
		var dept = $("#slctDept").val();
		if(date != null && date!="" && yr !="" && month != "" && dept != ""){
			$.post('schedule.datacontrol.php',{		
						mod:'laodDataTable',
						year: $("#slctYear").val() ,
						month:$("#slctMonth").val(),
						date:$("#slctDay").val(),
						dept : dept
			},function(data){
				$("#details").html(data);
			});
		}
	}

	function savePlotSched(ivalue,id_no,idate){
		//alert("ivalue:"+ivalue+"id_no:"+id_no+"DATE:"+idate);
		$.post('schedule.datacontrol.php',{
			mod:'savePlot',
			ivalue : ivalue,
			id_no : id_no,
			idate : idate
		},function(data){
			//$("#slctDay").html(data);
		});
	}
	
</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="1" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
			<div style = "font-family:Arial;color:grey;font-size:10pt;font-weight:bold;height:25px;background-color:white;"> 
				<div style="background-color:white;width:300px;float:left;padding-left: 5px;"> Department :  &nbsp; 
					<select id = "slctDept" onchange="loadDataTable()"> <?php echo deptlist();  ?> </select>
				</div>

				<div style="background-color:white;width:140px;float:left;padding-left: 5px;"> Year : &nbsp; 
					<select id="slctYear" onchange="reloadDays()"> <?php echo yearlist();  ?> </select>
				</div>

				<div style="background-color:white;width:180px;float:left;padding-left: 5px;"> Month : &nbsp; 
					<select id="slctMonth" onchange="reloadDays()">
						<option value=""> -SELECT-</option>
					<?php

						for($i=1;$i<=12;$i++){
							list($id,$name,$ndays) = mysql_fetch_array(mysql_query("SELECT MONTH('2016-$i-01') AS month_id,MONTHNAME('2016-$i-01') month_name,DAY(LAST_DAY('2016-$i-01')) AS n_days;"));
							echo "<option value = '$id'>$name</option>";
						}
					?>
				</select>
				</div>

				<div style="background-color:white;width:250px;float:left;padding-left: 5px;"> Day : &nbsp; 
					<select id = "slctDay" onchange="loadDataTable()" style="width:80px;text-align:center"> </select>
				</div>
			</div>
			<div id="details" style=" overflow: auto;">
				
			</div>
		</td>
	</tr>
 </table>
</body>
</html>
<?php mysql_close($con);