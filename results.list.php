<?php
	session_start();
	include("handlers/_generics.php");
	$o = new _init;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Opon Medical Diagnostic Corporation</title>
<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="ui-assets/keytable/css/keyTable.jqueryui.css">
<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/page.jumpToData().js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/keytable/js/dataTables.keyTable.min.js"></script>
<script>

	$(document).ready(function() {

		$("#release_date").datepicker();

		var myTable = $('#itemlist').DataTable({
			"keys": true,
			"scrollY":  "300px",
			"select":	'single',
			"pageLength": 50,
			"pagingType": "full_numbers",
			"bProcessing": true,
			"responsive": true,
			"sAjaxSource": "data/resultlist.php?displayPending=<?php echo $_REQUEST['displayPending']; ?>",
			"scroller": true,
			"order": [[ 1, "asc" ]],
			"aoColumns": [
			  { mData: 'id' } ,
			  { mData: 'priority' } ,
			  { mData: 'sono' } ,
			  { mData: 'sodate' },
			  { mData: 'pname' },
			  { mData: 'age' },
			  { mData: 'gender' },
			  { mData: 'procedure' },
              { mData: 'released' },
			  { mData: 'rby' },
			  { mData: 'rdate' },
			  { mData: 'release_mode' },
			  { mData: 'released_to' },
			  { mData: 'code' },
			  { mData: 'serialno' },
			  { mData: 'with_file' },
			  { mData: 'file_path' },
			  { mData: 'is_consolidated' },
			  { mData: 'xorderdate' },
			  { mData: 'xbday' }
			],
			"aoColumnDefs": [
			    { "className": "dt-body-center", "targets": [1,2,5,6,7,8,9,10,12]},
			    { "targets": [0,13,14,15,16,17,18,19], "visible": false }
            ]
		});
	});
	
	function refreshList() {
		$('#itemlist').DataTable().ajax.url("data/resultlist.php").load();
	}

	function releaseResult() {
		var table = $("#itemlist").DataTable();		
		var lid; var stat;
	   	$.each(table.rows('.selected').data(), function() {
		    lid = this["id"];
			code = this['code'];
	   	});

		if(lid) {
			var irelease = $("#releasing").dialog({
				title: "Process Result for Release",
				width: 480,
				resizable: false,
				modal: true,
				buttons: [
					{
						text: "Mark Record as Released",
						icons: { primary: "ui-icon-check" },
						click: function() {
							if(confirm("Are you sure you want to process this result for releasing?") == true) {
								var msg = "";
								if($("#release_to").val() == '') {
									parent.sendErrorMessage("Please identify the recipient of the result that you intend to release");
								} else {
									$.post("src/sjerp.php", { mod: "releaseResult", id: lid, code: code, mode: $("#release_mode").val(), date: $("#release_date").val(), to: $("#release_to").val(), remarks: $("#release_remarks").val(), sid: Math.random() }, function() {
										alert("Result successfully released to patient!");
										irelease.dialog("close"); $("#frmRelease").trigger("reset");
										refreshList();
									});

								}

							}
						}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() {
							$(this).dialog("close");
						}
					}
				]
			});

		} else {
			parent.sendErrorMessage("Please select result to release...")
		}

	}

	function printResult() {
		var table = $("#itemlist").DataTable();		
		var lid; var stat;
	   	$.each(table.rows('.selected').data(), function() {
		    lid = this["id"];
			code = this['code'];
			sono = this['sono'];
			serialno = this['serialno'];
			is_consolidated = this['is_consolidated'];
			// with_file = this['with_file'];
			// filepath = this['file_path'];
	   	});

		   if(lid) {

			if(is_consolidated == 'Y') {

			$.post("src/sjerp.php", { mod: "retrieveSameSampleForPrint", code: code, sono: sono, serialno: serialno, sid: Math.random() }, function(res) {
				$("#otherTests").html(res);
			},"html");



			var dis = $("#printConsolidation").dialog({ 
				title: "System Message",
				width: "480",
				modal: true,
				resizeable: false,
				buttons: [
					{
						icons: { primary: "ui-icon-print" },
						text: "Print Result",
						click: function() { 
							var dataString = $("#otherTests").serialize();
							window.open("print/result.bloodchem.php?so_no="+sono+"&serialno="+serialno+"&sid="+Math.random()+"&"+dataString+"","Inventory Stockcard","location=1,status=1,scrollbars=1,width=640,height=720");
							dis.dialog('close');
						
						}
					}
				]
			});
			} else {
				parent.printResult(code,sono,serialno,lid);
			}
		} else {
			parent.sendErrorMessage("Please select result to print!")
		}

	}

	function grabPatient() {
		var table = $("#itemlist").DataTable();		
		var sono;
		var pri_no;
		var patient;
		var gender;

	   	$.each(table.rows('.selected').data(), function() {
		    so = this['so'];
			pri_no = this['priority'];
			patient = this['patient_name'];
			gender = this['gender'];
	   	});

		if(!pri_no) {
			parent.sendErrorMessage("- It appears you have not selected any record from the given list yet...");
		} else {
			$.post("src/sjerp.php", { mod: "grabPatient", so_no: so, pri_no: pri_no, patient: patient, gender: gender, callStation: "RELEASING", sid: Math.random()},function() {
				alert("Patient will be called out in a while... You may prepare the necessary documents for releasing.")
			});
		}

	}

	function unpublishResult() {
		var table = $("#itemlist").DataTable();
		var lid; var code;
		$.each(table.rows('.selected').data(), function() {
			lid = this["id"];
			code = this["code"];
			sono = this["sono"];
			serialno = this["serialno"];
		});

		if(!lid) {
			parent.sendErrorMessage("Please select a result to reject...");
		}else {
			if(confirm("Are you sure you want to unpublish this result?") == true) {
				$.post("src/sjerp.php", { mod: "rejectResult", lid: lid, sono: sono, serialno: serialno, sid: Math.random() }, function() {
					alert("Result Successfully Rejected! Please go back to Manage Lab Samples... ");
					refreshList();
				});
			}
		}
	}

	function displayPending() {
		document.frmDisplayPending.submit();

	}

</script>
<style>
	.dataTables_wrapper {
		display: inline-block;
	    font-size: 11px; padding: 3px;
		width: 99%; 
	}
	
	table.dataTable tr.odd { background-color: #f5f5f5;  }
	table.dataTable tr.even { background-color: white; }
	.dataTables_filter input { width: 250px; }
</style>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div id = "main">
	<table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
		<tr>
			<td style="padding:0px;" valign=top>
				<table width="100%" cellspacing="0" cellpadding="0" style="padding-left: 5px; margin-bottom: 5px;">
					<tr>
						<td>
							<a href="#" class="ui-button ui-widget ui-corner-all" onClick="grabPatient();"><span class="ui-icon ui-icon-volume-on"></span>&nbsp;Grab Patient</a>
							<a href="#" class="ui-button ui-widget ui-corner-all" onClick="printResult();"><img src="images/icons/print.png" width=13 height=13 align=absmiddle />&nbsp;Print Result</a>
							<a href="#" class="ui-button ui-widget ui-corner-all" onClick="releaseResult();"><img src="images/icons/receiving.png" width=13 height=13 align=absmiddle />&nbsp;Process Result For Releasing</a>
							<a href="#" class="ui-button ui-widget ui-corner-all" onClick="refreshList();"><img src="images/icons/refresh.png" width=13 height=13 align=absmiddle />&nbsp;Display Current Results</a>
							<a href="#" class="ui-button ui-widget ui-corner-all" onClick="displayPending();"><img src="images/icons/previous.png" width=13 height=13 align=absmiddle />&nbsp;Display Previous Results</a>
							<a href="#" class="ui-button ui-widget ui-corner-all" onClick="unpublishResult();"><img src="images/icons/cancel48.png" width=13 height=13 align=absmiddle />&nbsp;Unpublish Result</a>
						</td>
					</tr>
				</table>
				<table class="cell-border" id="itemlist" style="font-size:11px;">
					<thead>
						<tr>
							<th></th>
							<th width=8%>PRIORITY #</th>
							<th width=5%>SO #</th>
							<th width=8%>SO DATE</th>
							<th width=15%>PATIENT NAME</th>
                            <th width=5%>AGE</th>
							<th width=7%>GENDER</th>
							<th>PROCEDURE</th>
							<th width=8%>RELEASED?</th>
                            <th width=8%>RELEASED BY</th>
							<th width=10%>DATE RELEASED</th>
                            <th width=8%>MODE</th>
							<th width=12%>RELEASED TO</th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</thead>
				</table>
			</td>
		</tr>
	</table>
</div>
<div id="releasing" style="display: none;">
	<form name="frmRelease" id="frmRelease">
		<table width=100% callpaddin=0 cellspacing=3>
			<tr>
				<td width=35% class="spandix-l">Mode of Releasing :</td>
				<td>
					<select class=gridInput style="width: 80%;" name="release_mode" id="release_mode">
						<option value='PICKUP'>Pickup by Patient</option>
						<option value="EMAILED">Emailed to Patient</option>
						<option value="DELIVERED">Delivered to Patient</option>
					</select>
				</td>
			</tr>
			<tr>
				<td width=35% class="spandix-l">Releasing Date :</td>
				<td><input type="text" class="gridInput" style="width: 80%;" id="release_date" name="release_date" value = "<?php echo date('m/d/Y'); ?>"></td>
			</tr>
		
			<tr>
				<td width=35% class="spandix-l">Released To :</td>
				<td><input type="text" class="gridInput" style="width: 80%;" id="release_to" name="release_to"></td>
			</tr>
			<tr>
				<td width=35% class="spandix-l" valign=top>Other Remarks :</td>
				<td><textarea style="width: 80%;" id="release_remarks" name="release_remarks" rows=3></textarea></td>
			</tr>
		</table>
	</form>
</div>
<div id="printConsolidation" name="printConsolidation" style="display: none;">
	<p style="margin-left: 20px; text-align: justify;" id="message">It appears that the selected result belongs to one consolidated result sheet. You may select from the given list w/c result you wish to print.</span></p><br/>
	<form name="otherTests" id="otherTests">

	</form>
</div>
<div id="systemMessage" title="System Message" style="display: none;">
	<p style="margin-left: 20px; text-align: justify;" id="message">It appears that other results for this patient are also due for release. Do you wish to tag it and release it in batch?</span></p>
</div>
<form name="frmDisplayPending" id="frmDisplayPending" action="results.list.php" method="POST">
	<input type="hidden" name="displayPending" id="displayPending" value="Y">
</form>
</body>
</html>