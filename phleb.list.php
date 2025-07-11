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

		var principleSelection = [
			"IMMUNOCHROMATOGRAPHY"
		]

	$(document).ready(function() {
		var myTable = $('#itemlist').DataTable({
			"keys": true,
			"scrollY":  "310px",
			"select":	'single',
			"pagingType": "full_numbers",
			"pageLength": 50,
			"bProcessing": true,
			"responsive": true,
			"sAjaxSource": "data/phleblist.php?displayPending=<?php echo $_REQUEST['displayPending']; ?>",
			"scroller": true,
			"order": [[13,'desc'],[1, "asc"]],
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
			  { mData: 'sample_type' },
			  { mData: 'parent_code' },
			  { mData: 'sdate' },
			  { mData: 'subcat' }
			],
			"aoColumnDefs": [
			    { "className": "dt-body-center", "targets": [1,2,3,5,6,7,8]},
			    { "targets": [0,12,13,14], "visible": false }
            ]
		});

		$("#phleb_testprinciple").autocomplete({
			source: principleSelection, minLength: 0
			}).focus(function() {
			$(this).data("uiAutocomplete").search($(this).val());
			});

		$('#itemlist tbody').on('dblclick', 'tr', function () {
			var data = myTable.row(this).data();
		
			parent.collectSample(data['so'],data['code'],data['parent_code']);

		});

		$("#phleb_date").datetimepicker();

		$('#phleb_by').autocomplete({
			source:'suggestEmployee.php', 
			minLength:3
		});

	});
	
	function editRecord(){
		var table = $("#itemlist").DataTable();		
		var arr = [];
	   $.each(table.rows('.selected').data(), function() {
		   arr.push(this["id"]);
	   });
	  
		if(!arr[0] || arr[0] == "undefined") {
			parent.sendErrorMessage("Please select a record from the list, and once highlighted, click \"<b><i>Edit Selected Record</i></b>\" again...");
		} else {
			parent.showServiceInfo(arr[0]);	
		}
	}

	function refreshList() {
		$('#itemlist').DataTable().ajax.url("data/phleblist.php").load();
	}

	function collectSample() {
		var table = $("#itemlist").DataTable();		
	
		var sono; 
		var code; 
	   	var parentcode;
	   	var subcat;
		$.each(table.rows('.selected').data(), function() {
			sono = this['so'];
			code = this['code'];
			parentcode = this['parent_code'];
			subcat = this['subcat'];
	   	});


		$.post("src/sjerp.php", {
			mod: "retrieveOrderForSample",
			code: code,
			sono: sono,
			parentcode: parentcode,
			subcat: subcat,
			sid: Math.random() },
			function(data) {


				var pdetails = decodeURIComponent(data['patient_name']) + " ^ " + data['gender'] + " ^ " + data['age'] + "YO";
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
				$("#phleb_subcat").val(data['subcat']);
				$("#phleb_spectype").val(data['sample_type']);
				$("#phleb_serialno").val(data['series']);
				$("#phleb_containertype").val(data['container_type']);

				/* Retrieve List of tests that may be using the sample specimen/smaple */
				var scount = parseFloat(data['subcat']);
				if(scount ==  '1') {
					$.post("src/sjerp.php", { mod: "retrieveSameSample", sono: data['so'], code: data['code'], ctype: data['container_type'], stype: data['sample_type'], subcat: data['subcat'], sid: Math.random() }, function(reshtml) {
						$("#sampleField").html(reshtml);
					},"html");

				} else { $("#sampleField").html(''); }




				var dis = $("#sampleFormDetails").dialog({
					title: "Specimen Collection Details",
					width: 640,
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
			$.post("src/sjerp.php", { mod: "grabPatient", so_no: so, pri_no: pri_no, patient: patient, gender: gender, callStation: "LABORATORY", sid: Math.random()},function() {
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
			<td width=50%>
				<button class="ui-button ui-widget ui-corner-all" onClick="grabPatient();">
					<span class="ui-icon ui-icon-volume-on"></span> Grab Patient
				</button>
				<button class="ui-button ui-widget ui-corner-all" onClick="collectSample();">
					<img src="images/icons/syringe.png" width=12 height=12 align=absmiddle /> Collect Sample
				</button>
				<button class="ui-button ui-widget ui-corner-all" onClick="refreshList();">
					<span class="ui-icon ui-icon-refresh"></span> Display Current
				</button>
				<button class="ui-button ui-widget ui-corner-all" onClick="displayPending();">
					<span class="ui-icon ui-icon-copy"></span> Display Pending Requests
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
				<th width=10%>SAMPLE TYPE</th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</thead>
	</table>
</div>
<div id="sampleFormDetails" style="display: none;">
	<form name="frmSample" id="frmSample">
		<input type="hidden" name = "phleb_sono" id = "phleb_sono">
        <input type="hidden" name = "phleb_parentcode" id = "phleb_parentcode">
        <input type="hidden" name = "phleb_subcat" id = "phleb_subcat">
		<input type="hidden" name = "phleb_code" id = "phleb_code">
		<input type="hidden" name = "phleb_spectype" id = "phleb_spectype">
		<input type="hidden" name = "phleb_serialno" id = "phleb_serialno">
		<table width=100% cellpadding=0 cellspacing=0 style="border: 1px solid #cdcdcd; padding: 10px;">
			<tr>
				<td align="right" width="30%"  class="bareBold" style="padding-right: 15px;">Sales Order&nbsp;:</td>
				<td align=left colspan=2>
					<input class="gridInput" style="width:100%;" type=text name="phleb_sodetails" id="phleb_sodetails" readonly>
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="30%" class="bareBold" style="padding-right: 15px;">Patient Details&nbsp;:</td>
				<td align=left colspan=2>
					<input type="text" class="gridInput" style="width:100%;" name="phleb_pname" id="phleb_pname" readonly>
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="30%" class="bareBold" style="padding-right: 15px;">Requesting Physician&nbsp;:</td>
				<td align=left colspan=2>
					<input type="text" class="gridInput" style="width:100%;" name="phleb_physician" id="phleb_physician">
				</td>					
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="30%" class="bareBold" style="padding-right: 15px;">Requested Procedure&nbsp;:</td>
				<td align=left>
					<input type="text" class="gridInput" style="width:95%;" name="phleb_procedure" id="phleb_procedure" readonly>
				</td>			
				<td rowspan=20 id="sampleField" title="Other lab requests from customer that may use the same sample." valign=top>


				</td>		
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="30%" class="bareBold" style="padding-right: 15px;">Sample Details&nbsp;:</td>
				<td align=left width=50%>
					<input type="text" class="gridInput" style="width:95%;" name="phleb_sampledetails" id="phleb_sampledetails" readonly>
				</td>	
					
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="30%" class="bareBold" style="padding-right: 15px;">Preferred Container&nbsp;:</td>
				<td align=left>
					<select class="gridInput" style="width:95%;" name="phleb_containertype" id="phleb_containertype">
						<?php
							$cquery = $o->dbquery("select id,`type` from options_containers;");
							while(list($cid,$ctype) = $cquery->fetch_array()) {
								echo "<option value='$cid'>$ctype</option>";
							}
						?>
					</select>
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="30%" class="bareBold" style="padding-right: 15px;">Date & Time of Collection&nbsp;:</td>
				<td align=left>
					<input type="text" class="gridInput" style="width:95%;" name="phleb_date" id="phleb_date" value="<?php echo date('m/d/Y H:i'); ?>">
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="30%" class="bareBold" style="padding-right: 15px;">Test Kit Vendor&nbsp;:</td>
				<td align=left>
					<input type="text" class="gridInput" style="width:95%;" name="phleb_testkit" id="phleb_testkit">
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="30%" class="bareBold" style="padding-right: 15px;">Lot # (If Applicable)&nbsp;:</td>
				<td align=left>
					<input type="text" class="gridInput" style="width:95%;" name="phleb_testkit_lotno" id="phleb_testkit_lotno">
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="30%" class="bareBold" style="padding-right: 15px;">Expiry Date&nbsp;:</td>
				<td align=left>
					<input type="date" class="gridInput" style="width:95%;" name="phleb_testkit_expiry" id="phleb_testkit_expiry">
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="30%" class="bareBold" style="padding-right: 15px;">Test Principle&nbsp;:</td>
				<td align=left>
					<input type="text" class="gridInput" style="width:95%;" name="phleb_testprinciple" id="phleb_testprinciple">
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="30%" class="bareBold" style="padding-right: 15px;">Extraction Site&nbsp;:</td>
				<td align=left>
					<select class="gridInput" style="width:95%;" name="phleb_location" id="phleb_location">
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
				<td align="right" width="30%" class="bareBold" style="padding-right: 15px;">Extracted By&nbsp;:</td>
				<td align=left>
					<input type="text" class="inputSearch2" style="width:95%;padding-left:22px;" name="phleb_by" id="phleb_by">
				</td>				
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td align="right" width="30%" class="bareBold" style="padding-right: 15px;" valign=top>Remarks/Memo&nbsp;:</td>
				<td align=left colspan=2>
					<textarea name="phleb_remarks" id="phleb_remarks" style="width:100%;" rows=1></textarea>
				</td>				
			</tr>
		</table>
	</form>
</div>
<form name="frmDisplayPending" id="frmDisplayPending" action="phleb.list.php" method="POST">
	<input type="hidden" name="displayPending" id="displayPending" value="Y">
</form>

</body>
</html>