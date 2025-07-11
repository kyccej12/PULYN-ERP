<?php
	session_start();
	include('handlers/_generics.php');
	$o = new _init();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="style/jquery.timepicker.css" rel="stylesheet" type="text/css" />
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="ui-assets/keytable/css/keyTable.jqueryui.css">
<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery.timepicker.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/page.jumpToData().js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/keytable/js/dataTables.keyTable.min.js"></script>
<script>

	$(document).ready(function() {
		var myTable = $('#itemlist').DataTable({
			"keys": true,
			"scrollY":  "340px",
			"select":	'single',
			"pageLength": 50,
			"pagingType": "full_numbers",
			"bProcessing": true,
			"responsive": true,
			"sAjaxSource": "data/peme.eval.result.php?displayType=<?php echo $_REQUEST['displayType']; ?>",
			"scroller": true,
			"order": [[0, "asc"],[12, "desc"]],
			"aoColumns": [
			  { mData: 'prio' } ,
			  { mData: 'so' } ,
			  { mData: 'sodate' } ,
			  { mData: 'pname' },
			  { mData: 'gender' },
			  { mData: 'bday' }, 
			  { mData: 'age' },
              { mData: 'compname' },
			  { mData: 'code' },
			  { mData: 'procedure' },
			  { mData: 'clinic' },
			  { mData: 'so_date' },
			  { mData: 'priority' },
			  { mData: 'birthdate' },
			  { mData: 'pid' },
			  { mData: 'ex_by' },
			  { mData: 'ev_by' }
			],
			"aoColumnDefs": [
			    { "className": "dt-body-center", "targets": [0,1,3,4,5,6,8,9,10]},
			    { "targets": [5,8,11,12,13,14], "visible": false }
            ]
		});

		$('#itemlist tbody').on('dblclick', 'tr', function () {
			var data = myTable.row( this ).data();	
			parent.collectVitals(data['so'],data['pid']);
		});

		$("#vitals_date").datetimepicker();
		$("#vitals_testkit_expiry").datepicker();

		$('#vitals_by').autocomplete({
			source:'suggestEmployee.php', 
			minLength:3
		});

	});
	
	function refreshList() {
		$('#itemlist').DataTable().ajax.url("data/pemelist.php").load();
	}

	function grabPatient() {
		var table = $("#itemlist").DataTable();		
		var so;
		var pri_no;
		var patient;
		var gender;
		var clinic;

	   	$.each(table.rows('.selected').data(), function() {
		    so = this['so'];
			pri_no = this['priority'];
			patient = this['patient_name'];
			gender = this['gender'];
			clinic = this['clinic'];
	   	});

		if(!pri_no) {
			parent.sendErrorMessage("- It appears you have not selected any record from the given list yet...");
		} else {
			if(clinic != '') { var callStation = "Clinic " + clinic + ""; } else { var callStation = 'Doc. Sec.'}
			$.post("src/sjerp.php", { mod: "grabPatient", so_no: so, pri_no: pri_no, patient: patient, gender: gender, callStation: callStation, sid: Math.random()},function() {
				alert("Patient will be called out in a while... You may prepare the necessary procedure requirements prior to patients arrival.")
			});
		}

	}

	function collectSample() {
		var table = $("#itemlist").DataTable();		
		var so_no;
		$.each(table.rows('.selected').data(), function() {
			so_no = this['so'];
			pid = this['pid'];
		});
		
		if(!so_no) {
			parent.sendErrorMessage("Please select a record to continue....")
		} else {
		
			parent.printVitals(so_no,pid);
		}

	}
	function changeDisplay(val) {
		document.frmChangeDisplay.displayType.value = val;
		document.frmChangeDisplay.submit();
	}

	function printBatchResult() {
		$("#dtf").datepicker(); $("#dt2").datepicker();
		var disMessage = $("#printBatch").dialog({ 
			title: "Print PEME Batch Result",
			width: "500",
			modal: true,
			resizeable: false,
			buttons: [
					{
						icons: { primary: "ui-icon-print" },
						text: "Print Results",
						click: function() { 
							window.open("print/result.peme.batch.php?dtf="+$("#dtf").val()+"&dt2="+$("#dt2").val()+"&compName="+$("#cid").val()+"&sid="+Math.random()+"&sid="+Math.random()+"","Batch Result","location=1,status=1,scrollbars=1,width=640,height=720");
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

	.no_bottom {
		border-top: none;
		border-left: none;
		border-bottom: 1px solid black;
		padding: 5px;
	}
</style>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0">
<div id = "main">
	<table width="100%" cellspacing="0" cellpadding="0" style="padding-left: 5px; margin-bottom: 2px;">
		<tr>
			<td>
				<button class="ui-button ui-widget ui-corner-all" onClick="grabPatient();">
					<span class="ui-icon ui-icon-volume-on"></span> Grab Patient
				</button>
				<button class="ui-button ui-widget ui-corner-all" onClick="collectSample();">
					<span class="ui-icon ui-icon-print"></span> Print PE/ME Form
				</button>
				<button class="ui-button ui-widget ui-corner-all" onClick="printBatchResult();">
					<span class="ui-icon ui-icon-print"></span> Print Batch PE Form
				</button>
				<button class="ui-button ui-widget ui-corner-all" onClick="refreshList();">
					<span class="ui-icon ui-icon-refresh"></span> Reload List
				</button>
			</td>
		</tr>
	</table>
	<table class="cell-border" id="itemlist" style="font-size:11px;">
		<thead>
			<tr>
				<th width=6%>PRIO #</th>
				<th width=6%>SO #</th>
				<th width=6%>DATE</th>
				<th width=10%>PATIENT NAME</th>
				<th width=5%>SEX</th>
				<th></th>
				<th width=5%>AGE</th>
				<th width=15%>COMPANY</th>
				<th>CODE</th>
				<th>PROCEDURE</th>
				<th>CLINIC ASSIGNMENT</th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th width=12%>EXAMINED BY</th>
				<th width=12%>EVALUATED BY</th>
			</tr>
		</thead>
	</table>
</div>
<div id="printBatch" style="display: none;">
	<form name="frmPrintBatch" id="frmPrintBatch">
		<table width=100% callpaddin=0 cellspacing=3>
		<tr>
			<td width=35%><span class="spandix-l">Company Name: </span></td>
			<td>
				<select name="cid" id="cid" class="gridInput" style="width: 80%; font-size: 11px;">
					<option value=''>- All Customers -</option>
					<?php
						$custQuery = $o->dbquery("SELECT DISTINCT customer_code, customer_name FROM so_header WHERE customer_code != 0 AND `status` = 'Finalized' ORDER BY customer_name;");
						while($custRow = $custQuery->fetch_array()) {
							echo "<option value='$custRow[0]'>$custRow[1]</option>";

						}
					?>
				</select>
			</td>
		</tr>
			<tr>
				<td width=35% class="spandix-l">Date From:</td>
				<td>
					<input type="text" class="gridInput" style="width: 80%;" id="dtf" name="dtf" value = "<?php echo date('m/d/Y'); ?>">
				</td>
			</tr>
			<tr>
				<td width=35% class="spandix-l">Date To:</td>
				<td>
					<input type="text" class="gridInput" style="width: 80%;" id="dt2" name="dt2" value = "<?php echo date('m/d/Y'); ?>">
				</td>
			</tr>
		</table>
	</form>
</div>
</body>
</html>