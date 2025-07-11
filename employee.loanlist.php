<?php
	session_start();
	include("includes/dbUSE.php");	
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="js/jquery.dialogextend.js"></script>
	<script language="javascript" src="js/tableH.js"></script>
	<script>

	var lid = "";
	
	function computeAmrtz() {
		if(isNaN($("#terms").val()) == true || $("#terms").val() == "") {
			var terms = 0; $("#terms").val('0');
		} else { var terms = parseFloat($("#terms").val()); }
		if(isNaN(parent.stripComma($("#amount").val())) == true || $("#terms").val() == "") {
			var amt = 0; $("#amount").val('0.00');
			   
		} else { var amt = parseFloat(parent.stripComma($("#amount").val()));  amt = amt.toFixed(2); }
		
		var dedu = amt / terms;
		    dedu = dedu.toFixed(2);
		$("#dedu_amount").val(parent.kSeparator(dedu));
		$("#amount").val(parent.kSeparator(amt));
		
	}
	
	function selectFID(obj) {
		gObj = obj;
		$(obj).closest("tr").siblings().removeClass("diffcolor");
		$(obj).toggleClass("diffcolor");
		tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); lid = tmp_obj[1];
	}
	
	function viewFile() {
		if(lid == "") {
			parent.sendErrorMessage("Please loan file to view.");
		} else {
			$.post("payroll.datacontrol.php", { mod: "viewLoanFile", lid: lid, sid: Math.random() }, function(data) {
				$("#rid").val(data['file_id']);
				$("#id_no").val(data['id_no']);
				$("#loan_date").val(data['availed']);
				$("#loan_type").val(data['loan_type']);
				$("#amount").val(parent.kSeparator(data['amount']));
				$("#terms").val(data['terms']);
				$("#dedu_amount").val(parent.kSeparator(data['dedu_amount']));
				$("#remarks").val(data['remarks']);
				$("#loanDetails").dialog({
					title: "Employee Loan File", 
					width: 400, 
					height: 360, 
					resizable: false, 
						buttons: {
						"Save Record":  function() { saveLoan(); },
						"Mark File as Deleted": function() { deleteLoan(); }
					}
				});	
			},"json");
		}
	}
	
	function saveLoan() {
		if(confirm("Are you sure you want to save changes on this file?") == true) {
			$.post("payroll.datacontrol.php", { mod: "saveLoan", fid: $("#rid").val(), id_no: $("#id_no").val(), loan_type: $("#loan_type").val(), loan_date: $("#loan_date").val(), amount: $("#amount").val(), terms: $("#terms").val(), dedu_amount: $("#dedu_amount").val(), remarks: $("#remarks").val(), sid: Math.random() }, function() {
				alert("Record Successfully Saved!");
				$("#loanDetails").dialog("close");
				parent.showLoans();
			});
		}
	}
	
	function newFile() {
		$("#rid").val('');
		$("#id_no").val('');
		$("#loan_date").val('');
		$("#loan_type").val('');
		$("#amount").val('');
		$("#terms").val('1');
		$("#dedu_amount").val('');
		$("#remarks").val('');
		$("#loanDetails").dialog({
				title: "Employee Loan File", 
				width: 400, 
				height: 360, 
				resizable: false, 
				buttons: {
				"Save Record":  function() { saveLoan(); },
			}
		});	
	}
	
	function deleteLoan() {
		if(confirm("This would temporarily delete the file from the Employee loan database. Do you wish to continue?") == true) {
			$.post("payroll.datacontrol.php", { mod: "deleteLoan", fid: $("#rid").val(), sid: Math.random() }, function(data) {
				if(data == "error") {
					parent.sendErrorMessage("Unable to delete this Employee Loan File. There were deductions associated to this file...");
				} else {				
					alert("Record Successfully Deleted!");
					$("#loanDetails").dialog("close");
					parent.showLoans();
				}
			},"html");
		}
	}
	
	$(function() { $("#loan_date").datepicker(); });
</script>
</head>
<body bgcolor="#7f7f7f" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
			<table border="0" cellpadding="0" cellspacing="0" width=100%>
				<tr bgcolor="#595959">
					<td align=center class="dgridhead" width="50">FILE ID</td>
					<td align=left class="dgridhead" width="220">EMPLOYEE</td>
					<td align=center class="dgridhead" width="100">DATE AVAILED</td>
					<td align=center class="dgridhead" width="120">LOAN TYPE</td>
					<td align=center class="dgridhead" width="80">AMOUNT</td>
					<td align=center class="dgridhead" width="80">BALANCE</td>
					<td align=left class="dgridhead">REMARKS</td>
					<td align=center class="dgridhead" width="12">&nbsp;</td>
				</tr>
			</table>
			<div id="details" style="height:400px; overflow: auto;">
				<table width=100% cellspacing=0 cellpadding=0 onMouseOut="javascript:highlightTableRowVersionA(0);">
				<?php
					$i = 0;
					
					if(isset($_GET['searchtext']) && $_GET['searchtext'] != '') { 
						$araynako = explode(" ",$_GET['searchtext']);
						foreach($araynako as $sakit) {
							$tunga = $tunga . "b.lname like '%$sakit%' || b.fname like '%$sakit%' || b.mname like '%$sakit%' || a.id_no = '$sakit' ||";
						}
						
						$tunga = substr($tunga,0,-3);
						$gipangita = " and ($tunga) ";
					}
					
					$getRec = dbquery("SELECT file_id, LPAD(file_id,3,0) AS fid, CONCAT('(',a.id_no,') ',b.lname,', ',b.fname,' ',b.mname) AS emp, DATE_FORMAT(date_availed,'%m/%d/%Y') AS tdate, loan_type, terms, amount, dedu_amount, balance, remarks from hris.e_loans a LEFT JOIN hris.e_master b ON a.id_no=b.id_no WHERE b.company = '$_SESSION[company]' $gipangita and a.status = 'Active' ORDER BY date_availed DESC;");
					while($row = mysql_fetch_array($getRec)) {
						if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
						list($ltype) = getArray("select description from hris.e_loantype where type='$row[loan_type]';");
						echo "<tr bgcolor=\"$bgC\" onmouseover=\"highlightTableRowVersionA(this, '#3399ff');\" id='obj_$row[file_id]' onclick='selectFID(this);'>
								<td class=dgridbox align=center width=\"50\">$row[fid]</td>
								<td class=dgridbox align=left width=\"220\">$row[emp]</td>
								<td class=dgridbox align=center width=\"100\">$row[tdate]</td>
								<td class=dgridbox align=center width=\"120\">$ltype</td>
								<td class=dgridbox align=center width=\"80\">".number_format($row['amount'],2)."</td>
								<td class=dgridbox align=center width=\"80\">".number_format($row['balance'],2)."</td>
								<td class=dgridbox align=left>$row[remarks]&nbsp;</td>
							</tr>"; $i++; 
						}
					if($i < 30) {
						for($i; $i <= 30; $i++) {
							if($i%2==0){ $bgC = "#f5f5f5"; } else { $bgC = "#ffffff"; }
							echo "<tr bgcolor='$bgC'><td colspan='7'>&nbsp;</td></tr>";
						}
					}
				?>
				</table>
			</div>
			<table width="100%"  cellspacing="0" cellpadding="0" style="padding-left: 5px;">
				<tr><td height=8></td></tr>
				<tr>
					<td>
						<button onClick="newFile();" class="buttonding" id="btn_rsv"><img src="images/icons/add-2.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;New Record</b></button>
						<button onClick="viewFile();" class="buttonding"><img src="images/icons/reports256.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;View Record</b></button>
						<button onClick="parent.showSearch('emploans');" class="buttonding"><img src="images/icons/search.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Search Record</b></button>
					</td>
				</tr>
			</table>
		</td>
	</tr>
 </table>
<div id="loanDetails" style="display: none;">
	<table border="0" cellpadding="0" cellspacing="0" width=100%>
		<input type="hidden" name="rid" id="rid">
		<tr><td class="spandix-l" width=35%>Employee :</td>
			<td align=left>
				<select name="id_no" id="id_no" style="width: 90%; font-size: 11px;" class="nInput">
					<option value="">- Select Employee -</option>
					<?php
						$elq = dbquery("select id_no, concat(lname,', ',fname) as emp from hris.e_master where `filestatus` = 'Active' and company = '$_SESSION[company]' order by lname;");
						while(list($idno,$name) = mysql_fetch_array($elq)) {
							print "<option value='$idno'>$name</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4 colspan="2"></td></tr>
		<tr><td class="spandix-l" width=35%>Loan Type :</td>
			<td align=left>
				<select name="loan_type" id="loan_type" style="width: 90%; font-size: 11px;" class="nInput">
						<option value="">- Select Loan -</option>
					<?php
						$lq = dbquery("select type, description from hris.e_loantype;");
						while(list($type,$desc) = mysql_fetch_array($lq)) {
							print "<option value='$type' ";
							if($res['loan_type'] == $type) { print "selected"; }
							print ">$desc</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr><td height=4 colspan="2"></td></tr>
		<tr><td class="spandix-l" width=35%>Loan Date :</td>
			<td align=left>
				<input type="text" name="loan_date" id="loan_date" style="width: 138px; font-size: 11px;" class="nInput">
			</td>
		</tr>
		<tr><td height=4 colspan="2"></td></tr>
		<tr><td class="spandix-l" width=35%>Amount :</td>
			<td align=left>
				<input type="text" name="amount" id="amount" style="width: 138px; font-size: 11px;" class="nInput" onchange="computeAmrtz();">
			</td>
		</tr>
		<tr><td height=4 colspan="2"></td></tr>
		<tr>
			<td class="spandix-l" width="35%">Terms (No. of Pay Days) :</td>
			<td align="left">
				<input type="text" name="terms" id="terms" style="width: 138px;" class="nInput" onchange="computeAmrtz();">
			</td>
		</tr>
		<tr><td height=4 colspan="2"></td></tr>
			<tr><td class="spandix-l" width=35%>Ded'n Amount :</td>
			<td align=left>
				<input type="text" name="dedu_amount" id="dedu_amount" style="width: 138px; font-size: 11px;" class="nInput" readonly>
			</td>
		</tr>
		<tr><td height=4 colspan="2"></td></tr>
		<tr><td class="spandix-l" width=35% valign=top>Remarks :</td>
			<td align=left>
				<textarea name="remarks" id="remarks" style="width: 90%; font-size: 11px;" rows=2></textarea>
			</td>
		</tr>
	</table>
</div>
</body>
</html>
<?php mysql_close($con);