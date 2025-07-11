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
			"scrollY":  "300px",
			"select":	'single',
			"pageLength": 50,
			"pagingType": "full_numbers",
			"bProcessing": true,
			"responsive": true,
			"sAjaxSource": "data/imaginglist.php",
			"scroller": true,
			"order": [[2, "desc"]],
			<?php if($_GET['code'] != 'undefined') { ?>
				"initComplete": function() {
					this.api().page.jumpToData("<?php echo $_GET['code']; ?>",1);
				},
			<?php } ?>
			"aoColumns": [
			  { mData: 'id' } ,
			  { mData: 'priority' } ,
			  { mData: 'so' } ,
			  { mData: 'sodate' },
			  { mData: 'patient_name' },
			  { mData: 'gender' },
			  { mData: 'birthdate' },
			  { mData: 'age' }, 
			  { mData: 'code' },
			  { mData: 'procedure' },
              { mData: 'physician' },
			  { mData: 'parent_code' }
			],
			"aoColumnDefs": [
			    { "className": "dt-body-center", "targets": [1,2,3,5,6,7,8]},
			    { "targets": [0,11], "visible": false }
            ]
		});

		$('#itemlist tbody').on('dblclick', 'tr', function () {
			collectSample();
		});

		$("#phleb_date").datetimepicker();
		$("#phleb_testkit_expiry").datepicker();

		$('#phleb_by').autocomplete({
			source:'suggestEmployee.php', 
			minLength:3
		});

	});
	
	function refreshList() {
		var type = $("#displayType").val();
		var xurl = "data/imaginglist.php?type="+type+"&sid="+Math.random()+"";
		$('#itemlist').DataTable().ajax.url(xurl).load();
	}


	function collectSample() {
		var table = $("#itemlist").DataTable();		
		var lid;
		var sono; 
		var code; 
	   	var parentcode;
		$.each(table.rows('.selected').data(), function() {
			lid = this['id'];
			sono = this['so'];
			code = this['code'];
			parentcode = this['parent_code'];
	   	});


		$.post("src/sjerp.php", {
			mod: "retrieveOrderForSample",
			code: code,
			sono: sono,
			parentcode: parentcode,
			sid: Math.random() },
			function(data) {

				var pdetails = data['patient_name'] + " ^ " + data['gender'] + " ^ " + data['age'] + "YO";
				var procedure = data['code'] + " ^ " + decodeURIComponent(data['particulars']);
				var sample = data['xsample'] + " ^ " + data['series'];
				var sodetails = data['so'] + " ^ " + data['sodate'];

				$("#phleb_sono").val(data['so']);
				$("#phleb_sodetails").val(sodetails);
				$("#phleb_pid").val(data['pid']);
				$("#phleb_pname").val(pdetails);
				$("#phleb_physician").val(data['physician']);
				$("#phleb_procedure").val(procedure);
				$("#phleb_sampledetails").val(sample);
				$("#phleb_code").val(data['code']);
				$("#phleb_parentcode").val(data['parent_code']);
				$("#phleb_spectype").val(data['sample_type']);
				$("#phleb_serialno").val(data['series']);

				var dis = $("#sampleFormDetails").dialog({
					title: "Radiograph Details",
					width: 540,
					resizeable: false,
					modal: true,
					buttons: [
						{
							text: "Save Changes Made",
							icons: { primary: "ui-icon-check" },
							click: function() {
								var msg = '';
								if($("#phleb_by").val() == "") { msg = msg + "- Please identify the person who took this specimen<br/>"; }
								
								if(msg != '') {
									parent.sendErrorMessage(msg);
								} else {
									if(confirm("Are you sure you want save this data?") == true) {
										var dataString = $("#frmSample").serialize();
										dataString = "mod=saveSample&" + dataString;
										$.ajax({
											type: "POST",
											url: "src/sjerp.php",
											data: dataString,
											success: function() {
		
												if(confirm("Extraction details successfully saved! Do you want to print Barcode Label for the extracted sample now?") == true) {
													$("#sampleFormDetails").trigger("reset");
													dis.dialog("close");
													refreshList();
													parent.printBarcode($("#phleb_serialno").val());
												}
												if(code != 'X017') {
													parent.writeImagingResult(lid,code);
												}
											}
										});
									}
								}

							}
						},
						{
							text: "Print Barcode",
							icons: { primary: "ui-icon-print" },
							click: function() { parent.printBarcode($("#phleb_serialno").val()); }
						},
						{
							text: "Close",
							icons: { primary: "ui-icon-closethick" },
							click: function() { $(this).dialog("close"); $('#sampleDetails').trigger("reset"); }
						}
					]
				});

			},"json"
		);
	}

	function grabPatient() {
		var table = $("#itemlist").DataTable();		
		var so;
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
			$.post("src/sjerp.php", { mod: "grabPatient", so_no: so, pri_no: pri_no, patient: patient, gender: gender, callStation: "X-RAY", sid: Math.random()},function() {
				alert("Patient will be called out in a while... You may prepare the necessary procedure requirements prior to patients arrival.")
			});
		}

	}

	function displayPending() {
		document.frmDisplayPending.submit();
	}

</script>
<style>
	.dataTables_wrapper {
		display: inline-block;
	    font-size: 11px;
		width: 100%; 
	}
	
	table.dataTable tr.odd { background-color: #f5f5f5;  }
	table.dataTable tr.even { background-color: white; }
	.dataTables_filter input { width: 250px; }
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
					<img src="images/icons/x-ray.png" width=12 height=12 align=absmiddle /> Collect Radiograph
				</button>
			</td>
			<td align=right class="spandix-l">
				Display Type :&nbsp;&nbsp;&nbsp;&nbsp;<select id="displayType" name="displayType" class="gridInput" style="width: 250px; height: 24px; font-size: 11px;">
					<option value="">- All Requests -</option>
					<option value="1">Today's Requests</option>
					<option value="2">Previous Unfullfilled Requests</option>
					<option value="3">Fullfilled Requests</option>
				</select>&nbsp;<button class="ui-button ui-widget ui-corner-all" onClick="refreshList();">
					<span class="ui-icon ui-icon-refresh"></span> Load List
				</button>
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
				<th width=12%>PATIENT NAME</th>
				<th width=7%>GENDER</th>
				<th width=8%>BIRTHDATE</th>
				<th width=5%>AGE</th>
				<th width=7%>CODE</th>
				<th>REQUESTED PROCEDURE</th>
				<th width=12%>REQUESTING PHYSICIAN</th>
				<th></th>
			</tr>
		</thead>
	</table>
</div>
<div id="sampleFormDetails" style="display: none;">
	<form name="frmSample" id="frmSample">
		<input type="hidden" name = "phleb_sono" id = "phleb_sono">
        <input type="hidden" name = "phleb_parentcode" id = "phleb_parentcode">
		<input type="hidden" name = "phleb_code" id = "phleb_code">
		<input type="hidden" name = "phleb_spectype" id = "phleb_spectype">
		<input type="hidden" name = "phleb_serialno" id = "phleb_serialno">
		<input type="hidden" name="phleb_testkit" id="phleb_testkit">
		<input type="hidden" name="phleb_testkit_expiry" id="phleb_testkit_expiry">
		<table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
			<tr>
				<td align="right" width="35%"  class="bareBold" style="padding-right: 15px;">Sales Order&nbsp;:</td>
				<td align=left>
					<input class="gridInput" style="width:50%;" type=text name="phleb_sodetails" id="phleb_sodetails" readonly>
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="35%" class="bareBold" style="padding-right: 15px;">Patient Details&nbsp;:</td>
				<td align=left>
					<input type="text" class="gridInput" style="width:100%;" name="phleb_pname" id="phleb_pname" readonly>
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="35%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
				<td align=left>
					<input type="text" class="gridInput" style="width:100%;" name="phleb_physician" id="phleb_physician">
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="35%" class="bareBold" style="padding-right: 15px;">Requested Procedure&nbsp;:</td>
				<td align=left>
					<input type="text" class="gridInput" style="width:100%;" name="phleb_procedure" id="phleb_procedure" readonly>
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="35%" class="bareBold" style="padding-right: 15px;">Sample Details&nbsp;:</td>
				<td align=left>
				<input type="text" class="gridInput" style="width:50%;" name="phleb_sampledetails" id="phleb_sampledetails" readonly>
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="35%" class="bareBold" style="padding-right: 15px;">Date & Time of Collection&nbsp;:</td>
				<td align=left>
					<input type="text" class="gridInput" style="width:50%;" name="phleb_date" id="phleb_date" value="<?php echo date('m/d/Y H:i'); ?>">
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="35%" class="bareBold" style="padding-right: 15px;">Case No.&nbsp;:</td>
				<td align=left>
					<input type="text" class="gridInput" style="width:50%;" name="phleb_testkit_lotno" id="phleb_testkit_lotno">
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="35%" class="bareBold" style="padding-right: 15px;">Collection Site&nbsp;:</td>
				<td align=left>
					<select class="gridInput" style="width:50%;" name="phleb_location" id="phleb_location">
						<?php
							$iun = $o->dbquery("select id,location from lab_locations;");
							while(list($aa,$ab) = $iun->fetch_array()) {
								echo "<option value='$aa'>$ab</option>";
							}
						?>
					</select>
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="35%" class="bareBold" style="padding-right: 15px;">Performed By&nbsp;:</td>
				<td align=left>
					<input type="text" class="inputSearch2" style="width:100%;padding-left:22px;" name="phleb_by" id="phleb_by">
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="35%" class="bareBold" style="padding-right: 15px;" valign=top>Remarks/Memo&nbsp;:</td>
				<td align=left>
					<textarea name="phleb_remarks" id="phleb_remarks" style="width:100%;" rows=1></textarea>
				</td>				
			</tr>
		</table>
	</form>
</div>
<form name="frmDisplayPending" id="displayPending" action="imaging.list.php" method="GET">
	<input type="hidden" name="displayPending" id="displayPending" value="Y">
	<input type="hidden" name="sid" id="sid" value="<?php echo uniqid(); ?>">
</form>
</body>
</html>