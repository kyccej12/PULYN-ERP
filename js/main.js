/* Percentage of Screen Size */
var wWidth = $(window).width();
var xWidth = wWidth * 0.8;
var xWidth = wWidth * 0.95;

var enumResultSelection = [
	"NEGATIVE",
	"POSITIVE",
	"REACTIVE",
	"NON-REACTIVE",
	"WEAKLY REACTIVE"
]

var ptResultSelection = [
	"NEGATIVE",
	"POSITIVE",
]

var havResultSelection = [
	"NEGATIVE",
	"POSITIVE",
]

var patStatus = [
	"A.P.E",
	"FOR EMPLOYMENT",
	"WALKIN",
	"A.P.E COMPLETION",
	"CONSULTATION",
	"PERSONAL",
	"MANDATORY REQUIREMENT",
	"STUDENT",
	"SYMPTOMATIC",
	"ASSYMPTOMATIC"
]

var monthNames = ["January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December"
];

/* Search Supplier on Reports */
$(document).ready(function($){
    $('#cab_snamem, #subledger_sname').autocomplete({
		source:'suggestSupplier.php', 
		minLength:3,
	});
	
	$('#subledger_acct,#gls_acct').autocomplete({
		source:'suggestAcctCodeOnly.php', 
		minLength:3,
	});
	
	$('#gls_client, #subledger_sid').autocomplete({
		source:'suggestContacts.php', 
		minLength:3,
	});

	$("#enum_result").autocomplete({
		source: enumResultSelection
	});
	
	$("#tmp_date").datepicker();

	$('#phleb_by').autocomplete({
		source:'suggestEmployee.php', 
		minLength:3
	});

	$('#appPatientId').autocomplete({
		source:'suggestPatient.php', 
		minLength:3,
		select: function(event,ui) {
			$("#appPatientName").val(ui.item.name);
			$("#appPatientAddress").val(ui.item.addr);
			$("#appGender").val(ui.item.gender);
			$("#appBirthdate").val(ui.item.bday);
			$("#appContactNo").val(ui.item.contactno);
		}
	});

	$("#antigen_result" ).autocomplete({
		source: enumResultSelection, minLength: 0
	}).focus(function() {
		$(this).data("uiAutocomplete").search($(this).val());
	});

	$("#antibody_result_igm, #antibody_result_igg" ).autocomplete({
		source: enumResultSelection, minLength: 0
	}).focus(function() {
		$(this).data("uiAutocomplete").search($(this).val());
	});

	$("#pt_result").autocomplete({
		source: ptResultSelection
   	});

   $("#enum_patientstat, #btype_patientstat").autocomplete({
	   source: patStatus
  	});

	$("#hav_result").autocomplete({
		source: havResultSelection
	});

	$("#ogtt_uglucose, #first_hr_uglucose, #second_hr_uglucose").autocomplete({
		source: ptResultSelection,
		minLength: 0
	}).focus(function() {
		$(this).data("uiAutocomplete").search($(this).val());
	});

	$("#desc_impression").jqte();
	$("#template_details").jqte();

});

function popSaver() {
	$('#popSaver').fadeIn('fast').delay(1000).fadeOut('slow');
}

function decodeEntities(encodedString) {
    var translate_re = /&(nbsp|amp|quot|lt|gt);/g;
    var translate = {
        "nbsp":" ",
        "amp" : "&",
        "quot": "\"",
        "lt"  : "<",
        "gt"  : ">"
    };
    return encodedString.replace(translate_re, function(match, entity) {
        return translate[entity];
    }).replace(/&#(\d+);/gi, function(match, numStr) {
        var num = parseInt(numStr, 10);
        return String.fromCharCode(num);
    });
}

function stripComma(val) {
	return val.replace(/,/g,"");
}

function kSeparator(val) {
	var val = parseFloat(val);
		val = val.toFixed(2);
	var a = val.split(".");
	var kValue = a[0];
	//if(a[1] == '' || a[1] == 'undefined') { a[1] = '00'; }

	var sRegExp = new RegExp('(-?[0-9]+)([0-9]{3})');
	while(sRegExp.test(kValue)) {
		kValue = kValue.replace(sRegExp, '$1,$2');
	}

	if(a[1] != "") {
		kValue = kValue + "." + a[1]; 
		return kValue;
	} else {
		return kValue + ".00";
	}
}
	
function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}	

function sendErrorMessage(msg) {
	$("#message").html(msg);
	$("#errorMessage").dialog({
		width: 400,
		resizable: false,
		modal: true,
		buttons: [
			{
			 	text: "Okay",
				click: function() { $(this).dialog("close"); },
				icons: { primary: "ui-icon-check" }
			}
		]
	});
}

function showLoaderMessage() {
	$("#loaderMessage").dialog({ width: 400, height: 150, closable: false, modal: true,  open: function(event, ui) {
        $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
    }});
}

function acctLookupReport(inputString,el) {
	if(inputString.length == 0) {
		$('#suggestions').hide();
	} else {
		var op = $("#"+el+"").offset();
		$.post("acctlookup_r.php", {queryString: inputString, el: el, sid: Math.random() }, function(data){
		if(data.length > 0) {
			$('#suggestions').css({top: op.top+20, left: op.left});
			$('#suggestions').show();
			$('#autoSuggestionsList').html(data);
		} else { $("#suggestions").hide(); }
		});
	}
}

function pickAccountReport(acct_code,el) {
	document.getElementById(el).value = acct_code;
}

function showDashboard() {
	$("#preboard").dialog({title: "Data Dashboard", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function fetchDashboard() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='acctg_db.php?month="+$("#dboard_month").val()+"&year="+$("#dboard_year").val()+"'></iframe>";
	$("#acctgdash").html(txtHTML);
	$("#acctgdash").dialog({title: "Data Dashboard (Accounting)", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}
	
function showUsers() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='user.master.php'></iframe>";
	$("#userlist").html(txtHTML);
	$("#userlist").dialog({title: "System Users", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function showChangePass() {
	$("#userChangePass").dialog({ title: "Update Password", width: 480, height: 190, resizable: false, modal: true, buttons: {
					"Update my Password": function() {
						var msg = "";

						if($("#pass1").val() == "" || $("#pass2").val() == "") { msg = msg + "The system cannot accept empty password.<br/>"; }
						if($("#pass1").val() != $("#pass2").val()) { msg = msg + "New Passwords do not match.<br/>"; }
					
						if(msg!="") {
							sendErrorMessage(msg);
						} else {

							$.post("src/sjerp.php", { mod: "changePassword", uid:  $("#myUID").val(), pass: $("#pass1").val(), sid: Math.random() },function() {
								alert("You have successfully updated your password!");
								$("#userChangePass").dialog("close");
							});
						}
					},
					"Continue with the System": function () { $(this).dialog("close"); }
				} }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true,
	});
}
function addUser() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='user.details.php'></iframe>";
	$("#userdetails").html(txtHTML);
	$("#userdetails").dialog({title: "System User Info.", width: 400, height: 260, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true,
	});
}

function viewUserInfo(eid) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='user.update.php?eid="+eid+"'></iframe>";
	$("#userdetails").html(txtHTML);
	$("#userdetails").dialog({title: "System User Info.", width: 400, height: 260, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true,
	});
}


function showUserDetails(uid) {
	var uname;
	$.post("src/sjerp.php", { mod: "getUinfo", uid: uid, sid: Math.random() }, function(data) {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='user.rights.php?uid="+uid+"'></iframe>";
		$("#userrights").html(txtHTML);
		$("#userrights").dialog({title: "User Access Rights ("+data+")", width: 560, height: 670, resizable: false}).dialogExtend({
			"closable" : true,
		    "maximizable" : false,
		    "minimizable" : true,
		});
	 },"html");
}

function showCust(mod) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='contact.master.php?mod="+mod+"'></iframe>";
	$("#customerlist").html(txtHTML);
	$("#customerlist").dialog({title: "Customers/Payees/Suppliers", width: xWidth, height: 540,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function addPayee() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='contact.details.php?mod=1'></iframe>";
	$("#customerdetails").html(txtHTML);
	$("#customerdetails").dialog({title: "Customers/Payees/Suppliers", width: 1024, height: 520,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function showPayeeInfo(fid) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='contact.details.php?fid="+fid+"&mod=1&sid="+Math.random()+"'></iframe>";
	$("#customerdetails").html(txtHTML);
	$("#customerdetails").dialog({title: "Customers/Payees/Suppliers Info", width: 1024, height: 520, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

/****************/

function showPatients() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='patient.master.php'></iframe>";
	$("#customerlist").html(txtHTML);
	$("#customerlist").dialog({title: "Patient Archive", width: xWidth, height: 540,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function addPatient() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='patient.details.php?mod=1&sid="+Math.random()+"'></iframe>";
	$("#customerdetails").html(txtHTML);
	$("#customerdetails").dialog({title: "Patient Information", width: 720, height: 850,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function showPatientInfo(pid) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='patient.details.php?mod=1&pid="+pid+"&sid="+Math.random()+"'></iframe>";
	$("#customerdetails").html(txtHTML);
	$("#customerdetails").dialog({title: "Patient Information", width: 720, height: 850,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function showAppointments() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='appointment.list.php'></iframe>";
	$("#cvlist").html(txtHTML);
	$("#cvlist").dialog({title: "Clinic Appointments", width: xWidth, height: 540,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function makeAppointment() {
	$("#appDate").datepicker();

	var dis = $("#appointment").dialog({ 
		title: "Make Appointment",
		width: "500",
		modal: true,
		resizeable: false,
		buttons: [
			{
				icons: { primary: "ui-icon-check" },
				text: "Make Appointment",
				click: function() { 
					if(confirm("Are you sure you want to make this appointment?") == true) {
						var dataString = $("#patientAppointment").serialize();
							dataString = "mod=newAppointment&" + dataString;
							$.ajax({
								type: "POST",
								url: "src/sjerp.php",
								data: dataString,
								success: function() {
								alert("Appointment Succesfully set!");
								dis.dialog("close");
								$("#patientAppointment").trigger("reset");
							}
						});
					}	
				}
			},
			{
				icons: { primary: "ui-icon-closethick" },
				text: "Close Window",
				click: function() { 
					$("#appointment").dialog("close");
				}	

			}		

		]

	});
}

function queryRequestCategory(cat,selbox) {
	$.post("src/sjerp.php", { mod: "queryRequestCategory", category: cat, sid: Math.random() }, function(resultSet) {
		document.getElementById(selbox).innerHTML = resultSet;
	});


}

/****************/

function showQue() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='que.list.php?sid="+Math.random()+"'></iframe>";
	$("#itemlist").html(txtHTML);
	$("#itemlist").dialog({title: "Manage Queueing List", width: xWidth, height: 500,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}



/****************/

function showSO(){
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='so.list.php'></iframe>";
	$("#projlist").html(txtHTML);
	$("#projlist").dialog({title: "Service Order Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewSO(so_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='so.details.php?so_no="+so_no+"'></iframe>";
	$("#projdetails").html(txtHTML);
	$("#projdetails").dialog({title: "Service Order Details", width: 1120, height: 640, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printSO(so_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/so.print.php?so_no="+so_no+"&sid="+Math.random()+"'></iframe>";
	$("#poprint").html(txtHTML);
	$("#poprint").dialog({title: "PRINT >> SERVICE ORDER", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printLetterSO(so_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/soLetter.print.php?so_no="+so_no+"&sid="+Math.random()+"'></iframe>";
	$("#poprint").html(txtHTML);
	$("#poprint").dialog({title: "PRINT >> SERVICE ORDER", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showPEME(){
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='peme.list.php'></iframe>";
	$("#cvlist").html(txtHTML);
	$("#cvlist").dialog({title: "Physical/Medical Examination Requests", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}
function showEvaluations(){
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='peme.eval.list.php'></iframe>";
	$("#cvlist").html(txtHTML);
	$("#cvlist").dialog({title: "Physical/Medical Examination For Evaluations", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}
function showPEMEResults(){
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='peme.eval.result.php'></iframe>";
	$("#cvlist").html(txtHTML);
	$("#cvlist").dialog({title: "Physical/Medical Examination Results", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function showPEMEResults(){
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='peme.eval.result.php'></iframe>";
	$("#cvlist").html(txtHTML);
	$("#cvlist").dialog({title: "Physical/Medical Examination Results", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generatePEMEDR() {
	window.open("reports/pemeDR.php?dtf="+$("#peme_dtf").val()+"&dt2="+$("#peme_dt2").val()+"&cid="+$("#peme_cid").val()+"&sid="+Math.random()+"","CLinic PE/ME Detailed Report","location=1,status=1,scrollbars=1,width=640,height=720");

	// var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/pemeDR.php?dtf="+$("#peme_dtf").val()+"&dt2="+$("#peme_dt2").val()+"&cid="+$("#peme_cid").val()+"&sid="+Math.random()+"'></iframe>";
	// $("#report3").html(txtHTML);
	// $("#report3").dialog({title: "CLinic PE/ME Detailed Report", width: 640, height: 520, resizable: true }).dialogExtend({
	// 	"closable" : true,
	// 	"maximizable" : true,
	// 	"minimizable" : true
	// });
}

function generatePEMEDRX() {
	window.open("export/pemeDR.php?dtf="+$("#peme_dtf").val()+"&dt2="+$("#peme_dt2").val()+"&cid="+$("#peme_cid").val()+"&sid="+Math.random()+"","CLinic PE/ME Detailed Report","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showPEMEReport() {
	$("#peme_dtf").datepicker(); $("#peme_dt2").datepicker();
	$("#pemeReport").dialog({title: "PEME Detailed Report", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function printVitals(so_no,pid) {
	
	var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.peme.php?so_no="+so_no+"&pid="+pid+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
	$("#report1").html(txtHTML);
	$("#report1").dialog({title: "Print - Physical/Medical Examinition Form", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});

}

function showPEMETally() {
	$("#pemetally_dtf").datepicker(); $("#pemetally_dt2").datepicker();
	$("#pemeTally").dialog({title: "PE/ME Doctors' Tally Report", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generatePEMETally() {
	window.open("reports/pemeTally.php?dtf="+$("#pemetally_dtf").val()+"&dt2="+$("#pemetally_dt2").val()+"&cid="+$("#pemetally_cid").val()+"&sid="+Math.random()+"","PE/ME Doctors' Tally Report","location=1,status=1,scrollbars=1,width=640,height=720");
}

function generatePEMETallyX() {
	window.open("export/peme_tally.php?dtf="+$("#pemetally_dtf").val()+"&dt2="+$("#pemetally_dt2").val()+"&cid="+$("#pemetally_cid").val()+"&sid="+Math.random()+"","PE/ME Doctors' Tally Report","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showPEMEBatch() {
	$("#pemebatch_dtf").datepicker(); $("#pemebatch_dt2").datepicker();
	$("#pemeBatch").dialog({title: "Print PEME Batch", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}


function generatePEMEBatch() {
	window.open("print/result.pemebatch.php?cid="+$("#pemebatch_cid").val()+"&dtf="+$("#pemebatch_dtf").val()+"&dt2="+$("#pemebatch_dt2").val()+"&sid="+Math.random()+"","PE/ME Doctors' Tally Report","location=1,status=1,scrollbars=1,width=640,height=720");
}

/* Pharmacy Section */
function showPharmaItems(icode) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='pharma.master.php?sid="+Math.random()+"&icode="+icode+"'></iframe>";
	$("#itemlist").html(txtHTML);
	$("#itemlist").dialog({title: "Pharmacy Product List", width: xWidth, height: 500,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showPharmaItemInfo(rid) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='pharma.details.php?id="+rid+"&mod=1&sid="+Math.random()+"'></iframe>";
	$("#itemdetails").html(txtHTML);
	$("#itemdetails").dialog({title: "Product Details", width: 1120, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
		"maximizable" : false,
		"minimizable" : true
	});
}

function showPharmaSO(){
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='pharma.solist.php'></iframe>";
	$("#projlist").html(txtHTML);
	$("#projlist").dialog({title: "Sales Order Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewPharmaSO(so_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='pharma.sodetails.php?so_no="+so_no+"'></iframe>";
	$("#projdetails").html(txtHTML);
	$("#projdetails").dialog({title: "Sales Order Details", width: 1120, height: 690, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printPharmaSO(so_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/pharma.soprint.php?so_no="+so_no+"&sid="+Math.random()+"'></iframe>";
	$("#poprint").html(txtHTML);
	$("#poprint").dialog({title: "PRINT >> PHARMACY SALES ORDER", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printPharmaSOLetter(so_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/pharma.letter.soprint.php?so_no="+so_no+"&sid="+Math.random()+"'></iframe>";
	$("#poprint").html(txtHTML);
	$("#poprint").dialog({title: "PRINT >> PHARMACY SALES ORDER", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showPharmaSI(){
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='pharma.silist.php'></iframe>";
	$("#polist").html(txtHTML);
	$("#polist").dialog({title: "Sales Invoice Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function printPharmaCSI(so_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/pharma.csi.print.php?so_no="+so_no+"&sid="+Math.random()+"'></iframe>";
	$("#poprint").html(txtHTML);
	$("#poprint").dialog({title: "PRINT >> PHARMACY CHARGE SALES INVOICE", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function viewPharmaSI(doc_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='pharma.sidetails.php?doc_no="+doc_no+"'></iframe>";
	$("#podetails").html(txtHTML);
	$("#podetails").dialog({title: "Sales Invoice Details", width: 1120, height: 640, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printPharmaSI(doc_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/pharma.siprint.php?doc_no="+doc_no+"&sid="+Math.random()+"'></iframe>";
	$("#poprint").html(txtHTML);
	$("#poprint").dialog({title: "PRINT >> PHARMACY SALES INVOICE", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showPharmaReport() {
	$("#phar_dsr_dtf").datepicker(); $("#phar_dsr_dt2").datepicker();
	$("#pharDsr").dialog({title: "Pharmacy Detailed Sales Report", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generatePharDSR() {
	var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/pharDsr.php?dtf="+$("#phar_dsr_dtf").val()+"&dt2="+$("#phar_dsr_dt2").val()+"&item="+$("#phar_dsr_item").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report3").html(txtHTML);
	$("#report3").dialog({title: "Pharmacy Detailed Sales Report", width: 640, height: 520, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function generatePharDSRX() {
	window.open("export/phardsr.php?dtf="+$("#phar_dsr_dtf").val()+"&dt2="+$("#phar_dsr_dt2").val()+"&sid="+Math.random()+"","Inventory Stockcard","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showPharmaRR() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='pharmarr.list.php'></iframe>";
	$("#pharmarrlist").html(txtHTML);
	$("#pharmarrlist").dialog({title: "Pharmacy Receiving Report Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewPharmaRR(rr_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='pharmarr.details.php?rr_no="+rr_no+"'></iframe>";
	$("#pharmarrdetails").html(txtHTML);
	$("#pharmarrdetails").dialog({title: "Pharmacy Receiving Report Details", width: 1120, height: 560, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printPharmaRR(rr_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/pharmarr.print.php?rr_no="+rr_no+"&sid="+Math.random()+"'></iframe>";
	$("#pharmarrprint").html(txtHTML);
	$("#pharmarrprint").dialog({title: "PRINT >> PHARMACY RECEIVING REPORT", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showPharmaSW() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='pharmasw.list.php'></iframe>";
	$("#pharmaswlist").html(txtHTML);
	$("#pharmaswlist").dialog({title: "Pharmacy Stocks Withdrawal Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewPharmaSW(sw_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='pharmasw.details.php?sw_no="+sw_no+"'></iframe>";
	$("#pharmarrdetails").html(txtHTML);
	$("#pharmarrdetails").dialog({title: "Pharmacy Receiving Report Details", width: 1120, height: 560, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printPharmaSW(sw_no,uid,rePrint) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/pharmasw.print.php?sw_no="+sw_no+"&uid="+uid+"&rePrint="+rePrint+"&sid="+Math.random()+"'></iframe>";
	$("#pharmaswprint").html(txtHTML);
	$("#pharmaswprint").dialog({title: "PRINT >>PHARMACY STOCKS WITHDRAWAL SLIP", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showPharmaPOList() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='pharmapo.list.php'></iframe>";
	$("#pharmapolist").html(txtHTML);
	$("#pharmapolist").dialog({title: "Pharmacy Purchase Order Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewPharmaPO(po_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='pharmapo.details.php?po_no="+po_no+"'></iframe>";
	$("#pharmapodetails").html(txtHTML);
	$("#pharmapodetails").dialog({title: "Pharmacy Purchase Order Details", width: 1120, height: 540, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printPharmaPO(po_no,uid,rePrint) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/pharmapo.print.php?po_no="+po_no+"&rePrint="+rePrint+"&sid="+Math.random()+"&user="+uid+"'></iframe>";
	$("#pharmapoprint").html(txtHTML);
	$("#pharmapoprint").dialog({title: "PRINT >>PHARMACY PURCHASE ORDER", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printPharmaPOPList(po_no,uid,rePrint) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/pharmapo.plist.php?po_no="+po_no+"&rePrint="+rePrint+"&sid="+Math.random()+"&user="+uid+"'></iframe>";
	$("#pharmapoprint").html(txtHTML);
	$("#pharmapoprint").dialog({title: "PRINT >> PHARMACY PURCHASE ORDER PACKING LIST", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showCountList() {
	$("#countlist_dtf").datepicker(); $("#countlist_dt2").datepicker();
	$("#countlist").dialog({title: "Top Selling Products Report", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateCountList() {
	var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/countList.php?dtf="+$("#countlist_dtf").val()+"&dt2="+$("#countlist_dt2").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report3").html(txtHTML);
	$("#report3").dialog({title: "Top Selling Products Report", width: 640, height: 520, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function generateCountListX() {
	window.open("export/countList.php?dtf="+$("#countlist_dtf").val()+"&dt2="+$("#countlist_dt2").val()+"&item="+$("#countlist_item").val()+"&sid="+Math.random()+"","Top Selling Products Report","location=1,status=1,scrollbars=1,width=640,height=720");
}

/* END OF PHARMA */


/****************/
function showSOA() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='soa.list.php?sid="+Math.random()+"'></iframe>";
	$("#itemlist").html(txtHTML);
	$("#itemlist").dialog({title: "Statement of Account Summary", width: xWidth, height: 540,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function viewSOA(soa_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='soa.details.php?soa_no="+soa_no+"'></iframe>";
	$("#projdetails").html(txtHTML);
	$("#projdetails").dialog({title: "Statement of Account Details", width: 1270, height: 640, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printSOA(soa_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/soa.print.php?soa_no="+soa_no+"&sid="+Math.random()+"'></iframe>";
	$("#rrprint").html(txtHTML);
	$("#rrprint").dialog({title: "PRINT >> STATEMENT OF ACCOUNT", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printPharmaSOA(soa_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/soa.pharma.print.php?soa_no="+soa_no+"&sid="+Math.random()+"'></iframe>";
	$("#rrprint").html(txtHTML);
	$("#rrprint").dialog({title: "PRINT >> STATEMENT OF ACCOUNT - PHARMACY", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function exportSOA(soa_no) {
	window.open("export/soa.php?soa_no="+soa_no+"&sid="+Math.random()+"","Statement of Account","location=1,status=1,scrollbars=1,width=640,height=720");
}

/****************/

function showServices(code) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='services.php?sid="+Math.random()+"&code="+code+"'></iframe>";
	$("#itemlist").html(txtHTML);
	$("#itemlist").dialog({title: "List of Services", width: xWidth, height: 500,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showServiceInfo(id) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='service.details.php?id="+id+"&mod=1&sid="+Math.random()+"'></iframe>";
	$("#itemdetails").html(txtHTML);
	$("#itemdetails").dialog({title: "Service Details", width: 1120, height: 520, resizable: false }).dialogExtend({
		"closable" : true,
		"maximizable" : false,
		"minimizable" : true
	});
}


function showItems(icode) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='items.master.php?sid="+Math.random()+"&icode="+icode+"'></iframe>";
	$("#itemlist").html(txtHTML);
	$("#itemlist").dialog({title: "Supplies & Materials", width: xWidth, height: 500,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showItemInfo(rid) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='items.details.php?id="+rid+"&mod=1&sid="+Math.random()+"'></iframe>";
	$("#itemdetails").html(txtHTML);
	$("#itemdetails").dialog({title: "Product Details", width: 1120, height: 520, resizable: false }).dialogExtend({
		"closable" : true,
		"maximizable" : false,
		"minimizable" : true
	});
}

function showSgroup() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='isubgroup.master.php'></iframe>";
	$("#report5").html(txtHTML);
	$("#report5").dialog({title: "Inventory Sub Group List", width: 800, height: 400,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function exportProducts(item_code,unit,mydate) {
	window.open("export/products.php?sid="+Math.random()+"","Inventory Stockcard","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showVAT(){
	$("#vatRelief").dialog({title: "BIR RELIEF FILE GENERATOR", width: 400 }).dialogExtend({
		"closable" : true,
		"maximizable" : false,
		"minimizable" : true
	});
}

function generateVAT(){
	var vat_month = $("#vat_month").val();
	var vat_year = $("#vat_year").val();
	var vat_type = $("#vat_type").val();
	if(vat_type=='input'){
		window.open("export/input.php?month="+vat_month+"&year="+vat_year+"","Unclassified Accounts","location=1,status=1,scrollbars=1,width=640,height=720");
	}else{
		window.open("export/output.php?month="+vat_month+"&year="+vat_year+"","Unclassified Accounts","location=1,status=1,scrollbars=1,width=640,height=720");
	}	
}

function showAccounts() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='accounts.php'></iframe>";
	$("#accountlist").html(txtHTML);
	$("#accountlist").dialog({title: "Chart of Accounts", width: 1024, height: 480, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function showAccountInfo(rid) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='accounts.details.php?code="+rid+"'></iframe>";
	$("#accountdetails").html(txtHTML);
	$("#accountdetails").dialog({title: "Chart of Accounts", width: 480, height: 215, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function showBanks() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='banks.master.php'></iframe>";
	$("#banklist").html(txtHTML);
	$("#banklist").dialog({title: "CIB Accounts", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function showBankDetails(bid) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='bank.details.php?bid="+bid+"'></iframe>";
	$("#bankdetails").html(txtHTML);
	$("#bankdetails").dialog({title: "CIB Account Details", width: 480, height: 332, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function showPOList() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='po.list.php'></iframe>";
	$("#polist").html(txtHTML);
	$("#polist").dialog({title: "Purchase Order Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewPO(po_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='po.details.php?po_no="+po_no+"'></iframe>";
	$("#podetails").html(txtHTML);
	$("#podetails").dialog({title: "Purchase Order Details", width: 1120, height: 540, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printPO(po_no,uid,rePrint) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/po.print.php?po_no="+po_no+"&rePrint="+rePrint+"&sid="+Math.random()+"&user="+uid+"'></iframe>";
	$("#poprint").html(txtHTML);
	$("#poprint").dialog({title: "PRINT >> PURCHASE ORDER", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printPOPList(po_no,uid,rePrint) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/po.plist.php?po_no="+po_no+"&rePrint="+rePrint+"&sid="+Math.random()+"&user="+uid+"'></iframe>";
	$("#poprint").html(txtHTML);
	$("#poprint").dialog({title: "PRINT >> PURCHASE ORDER PACKING LIST", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showJOList() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='jo.list.php'></iframe>";
	$("#jolist").html(txtHTML);
	$("#jolist").dialog({title: "Job Order Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewJO(doc_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='jo.details.php?doc_no="+doc_no+"'></iframe>";
	$("#jodetails").html(txtHTML);
	$("#jodetails").dialog({title: "Job Order Details", width: 1120, height: 360, resizable: true, modal: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printJO(doc_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/jo.print.php?doc_no="+doc_no+"&sid="+Math.random()+"'></iframe>";
	$("#poprint").html(txtHTML);
	$("#poprint").dialog({title: "PRINT >> JOB ORDER", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showRRList() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='rr.list.php'></iframe>";
	$("#rrlist").html(txtHTML);
	$("#rrlist").dialog({title: "Receiving Report Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewRR(rr_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='rr.details.php?rr_no="+rr_no+"'></iframe>";
	$("#rrdetails").html(txtHTML);
	$("#rrdetails").dialog({title: "Receiving Report Details", width: 1120, height: 560, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printRR(rr_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/rr.print.php?rr_no="+rr_no+"&sid="+Math.random()+"'></iframe>";
	$("#rrprint").html(txtHTML);
	$("#rrprint").dialog({title: "PRINT >> RECEIVING REPORT", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showAPVList() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='apv.list.php'></iframe>";
	$("#apvlist").html(txtHTML);
	$("#apvlist").dialog({title: "Accounts Payable Voucher Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewAP(apv_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='apv.details.php?apv_no="+apv_no+"'></iframe>";
	$("#apvdetails").html(txtHTML);
	$("#apvdetails").dialog({title: "Accounts Payable Voucher Details", width: 1120, height: 600, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printAPV(apv_no,uid,rePrint) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/apv.print.php?apv_no="+apv_no+"&rePrint="+rePrint+"&sid="+Math.random()+"&user="+uid+"'></iframe>";
	$("#apvprint").html(txtHTML);
	$("#apvprint").dialog({title: "PRINT >> Vouchers Payable", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showRFP(){
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='rfp.list.php'></iframe>";
	$("#rfplist").html(txtHTML);
	$("#rfplist").dialog({title: "Request for Payment", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewRFP(rfp_no)	
	{
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='rfp.details.php?rfp_no="+rfp_no+"'></iframe>";
		$("#rfpdetails").html(txtHTML);
		$("#rfpdetails").dialog({title: "Request for Payment", width: 1120, height: 540, resizable: true }).dialogExtend({
			"closable" : true,
		    "maximizable" : true,
		    "minimizable" : true
		});
	}

function printRFP(rfp,uid) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/rfp.print.php?rfp_no="+rfp+"&sid="+Math.random()+"&user="+uid+"'></iframe>";
	$("#rfpprint").html(txtHTML);
	$("#rfpprint").dialog({title: "PRINT >> REQUEST FOR PAYMENT", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showGRFP(){//grfp_list
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='grfp.list.php'></iframe>";
	$("#grfplist").html(txtHTML);
	$("#grfplist").dialog({title: "Petty Cash Request", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewGRFP(grfp_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='grfp.details.php?grfp_no="+grfp_no+"'></iframe>";
	$("#grfpdetails").html(txtHTML);
	$("#grfpdetails").dialog({title: "Petty Cash Request", width: 1024, height: 360, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printGRFP(grfp_no,uid) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/grfp.print.php?grfp_no="+grfp_no+"&sid="+Math.random()+"&user="+uid+"'></iframe>";
	$("#grfpprint").html(txtHTML);
	$("#grfpprint").dialog({title: "PRINT >> Petty Cash Request", width: 560, height: 500, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showCV() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='cv.list.php'></iframe>";
	$("#cvlist").html(txtHTML);
	$("#cvlist").dialog({title: "Cash/Check Disbursement Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewCV(cv_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='cv.details.php?cv_no="+cv_no+"'></iframe>";
	$("#cvdetails").html(txtHTML);
	$("#cvdetails").dialog({title: "Cash/Check Disbursement Details", width: xWidth, height: 560, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function jumpCVPage(pageNum,stext,sdetails) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='cv.list.php?page="+pageNum+"&searchtext="+stext+"&includeDetails="+sdetails+"'></iframe>";
	$("#cvlist").html(txtHTML);
	$("#cvlist").dialog({title: "Cash/Check Disbursement Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function printCV(cv_no,uid,rePrint) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/cv.print.php?cv_no="+cv_no+"&rePrint="+rePrint+"&sid="+Math.random()+"&user="+uid+"'></iframe>";
	$("#cvprint").html(txtHTML);
	$("#cvprint").dialog({title: "PRINT >> Cash/Check Voucher", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showJV() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='jv.list.php'></iframe>";
	$("#jvlist").html(txtHTML);
	$("#jvlist").dialog({title: "Journal Voucher Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewJV(j_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='jv.details.php?j_no="+j_no+"'></iframe>";
	$("#jvdetails").html(txtHTML);
	$("#jvdetails").dialog({title: "Journal Voucher Details", width: xWidth, height: 540, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function jumpJVPage(pageNum,stext,sdetails) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='jv.list.php?page="+pageNum+"&searchtext="+stext+"&includeDetails="+sdetails+"'></iframe>";
	$("#jvlist").html(txtHTML);
	$("#jvlist").dialog({title: "Journal Voucher Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}


function printJV(j_no,uid,rePrint) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/jv.print.php?j_no="+j_no+"&rePrint="+rePrint+"&sid="+Math.random()+"&user="+uid+"'></iframe>";
	$("#apvprint").html(txtHTML);
	$("#apvprint").dialog({title: "PRINT >> JOURNAL VOUCHER", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

/* Fixed Asset Management */
function showFA() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='fa.list.php'></iframe>";
	$("#falist").html(txtHTML);
	$("#falist").dialog({title: "Fixed Assets Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewFA(fid) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='fa.details.php?fid="+fid+"&sid="+Math.random()+"'></iframe>";
	$("#fadetails").html(txtHTML);
	$("#fadetails").dialog({title: "Fixed Asset Details", width: xWidth, height: 515, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function showOR() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='or.list.php'></iframe>";
	$("#crlist").html(txtHTML);
	$("#crlist").dialog({title: "Official Receipts Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewOR(doc_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='or.details.php?doc_no="+doc_no+"'></iframe>";
	$("#crdetails").html(txtHTML);
	$("#crdetails").dialog({title: "Official Receipt Details", width: 1120, height: 690, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printOR(doc_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/or.print.php?doc_no="+doc_no+"&sid="+Math.random()+"'></iframe>";
	$("#crprint").html(txtHTML);
	$("#crprint").dialog({title: "PRINT >> OFFICIAL RECEIPT", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showARBeginning() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='arbeginning.php'></iframe>";
	$("#jvdetails").html(txtHTML);
	$("#jvdetails").dialog({title: "Consolidated Beginning Balance (Accounts Receivable)", width: xWidth, height: 540, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showAPBeginning() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='apbeginning.php'></iframe>";
	$("#jvdetails").html(txtHTML);
	$("#jvdetails").dialog({title: "Consolidated Beginning Balance (Accounts Payable)", width: xWidth, height: 540, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showSRR() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='srr.list.php'></iframe>";
	$("#srrlist").html(txtHTML);
	$("#srrlist").dialog({title: "Stocks Return Slip Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewSRR(srr_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='srr.details.php?srr_no="+srr_no+"'></iframe>";
	$("#srrdetails").html(txtHTML);
	$("#srrdetails").dialog({title: "Stocks Return Slip Details", width: 1120, height: 560, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printSRR(srr_no,uid,rePrint) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/srr.print.php?srr_no="+srr_no+"&uid="+uid+"&rePrint="+rePrint+"&sid="+Math.random()+"'></iframe>";
	$("#srrprint").html(txtHTML);
	$("#srrprint").dialog({title: "PRINT >> STOCKS RETURN SLIP", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showPhy() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='phy.list.php'></iframe>";
	$("#phylist").html(txtHTML);
	$("#phylist").dialog({title: "Physical Inventory Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewPhy(doc_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='phy.details.php?doc_no="+doc_no+"'></iframe>";
	$("#phydetails").html(txtHTML);
	$("#phydetails").dialog({title: "Physical Inventory Form", width: 1120, height: 560, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}


function printPhy(doc_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/phy.print.php?doc_no="+doc_no+"&sid="+Math.random()+"'></iframe>";
	$("#srrprint").html(txtHTML);
	$("#srrprint").dialog({title: "PRINT >> Physical Inventory Form", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showSW() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='sw.list.php'></iframe>";
	$("#swlist").html(txtHTML);
	$("#swlist").dialog({title: "Stocks Withdrawal Slip Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewSW(sw_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='sw.details.php?sw_no="+sw_no+"'></iframe>";
	$("#swdetails").html(txtHTML);
	$("#swdetails").dialog({title: "Stocks Withdrawal Slip Details", width: 1120, height: 560, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printSW(sw_no,uid,rePrint) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/sw.print.php?sw_no="+sw_no+"&uid="+uid+"&rePrint="+rePrint+"&sid="+Math.random()+"'></iframe>";
	$("#srrprint").html(txtHTML);
	$("#srrprint").dialog({title: "PRINT >> STOCKS WITHDRAWAL SLIP", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showSTR() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='str.list.php'></iframe>";
	$("#strlist").html(txtHTML);
	$("#strlist").dialog({title: "Stocks Transfer Receipt Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewSTR(str_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='str.details.php?str_no="+str_no+"'></iframe>";
	$("#strdetails").html(txtHTML);
	$("#strdetails").dialog({title: "Stocks Transfer Receipt Details", width: xWidth, height: 560, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function printSTR(str_no,uid,rePrint) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/str.print.php?str_no="+str_no+"&uid="+uid+"&rePrint="+rePrint+"&sid="+Math.random()+"'></iframe>";
	$("#srrprint").html(txtHTML);
	$("#srrprint").dialog({title: "PRINT >> STOCKS TRANSFER RECEIPT", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showAdj() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='adj.list.php'></iframe>";
	$("#adjlist").html(txtHTML);
	$("#adjlist").dialog({title: "Inventory Adjustments Summary", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewAdj(doc_no) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='adj.details.php?doc_no="+doc_no+"'></iframe>";
	$("#strdetails").html(txtHTML);
	$("#strdetails").dialog({title: "Inventory Adjustment Form", width: xWidth, height: 540, resizable: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showIBook() {
	$("#ibook_dtf").datepicker(); $("#ibook_dt2").datepicker(); 
	$("#inventorybook").dialog({title: "Inventory Book", width: 480 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function processInventory() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='ibook.php?group="+$("#ibook_group").val()+"&dtf="+$("#ibook_dtf").val()+"&dt2="+$("#ibook_dt2").val()+"'></iframe>";
	$("#ibook").html(txtHTML);
	$("#ibook").dialog({title: "Inventory Book", width: 1180, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function jumpIBookPage(page,stxt,group,dtf,dt2) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='ibook.php?page="+page+"&searchtext="+stxt+"&group="+group+"&dtf="+dtf+"&dt2="+dt2+"'></iframe>";
	$("#ibook").html(txtHTML);
	$("#ibook").dialog({title: "Inventory Book", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewStockcard(item_code,unit,description,dtf,dt2) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='stockcard.php?item_code="+item_code+"&unit="+unit+"&dtf="+dtf+"&dt2="+dt2+"'></iframe>";
	$("#stockcard").html(txtHTML);
	$("#stockcard").dialog({title: "Inventory Stockcard ("+item_code+"|"+decodeURIComponent(description)+"|"+unit+")", width: xWidth, height: 540, resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function exportStockcard(item_code,unit,dtf,dt2) {
	window.open("export/stockard.php?item_code="+item_code+"&unit="+unit+"&dtf="+dtf+"&dt2="+dt2+"&sid="+Math.random()+"","Inventory Stockcard","location=1,status=1,scrollbars=1,width=640,height=720");
}

function exportInventoryNow() {
	window.open("export/ibook.php?group="+$("#ibook_group").val()+"&dtf="+$("#ibook_dtf").val()+"&dt2="+$("#ibook_dt2").val()+Math.random()+"","Inventory Book","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showBranches() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='branch.master.php'></iframe>";
	$("#customerlist").html(txtHTML);
	$("#customerlist").dialog({title: "Branch List", width: 1024, height: 530,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function viewBranch(code) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='branch.details.php?code="+code+"'></iframe>";
	$("#customerdetails").html(txtHTML);
	$("#customerdetails").dialog({title: "Branch Details", width: 480, height: 460,resizable: false, modal: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

/* REPORTING */
function showJSched() {
	$("#js_dtf").datepicker(); $("#js_dt2").datepicker();
	$("#jsched").dialog({title: "Journal Schedule", width: 400, height: 290 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateSchedule() {
	if(isNaN($("#js_acct").val()) == true) {
		sendErrorMessage("Invalid account code specified...");
	} else {
		if($("#js_conso").prop("checked")) { var conso = "Y"; } else { var conso = "N"; }
		if($("#js_rtype").val() == "1") {
			window.open("reports/journalschedule-html.php?type="+$("#js_type").val()+"&dtf="+$("#js_dtf").val()+"&dt2="+$("#js_dt2").val()+"&acct="+$("#js_acct").val()+"&sid="+Math.random()+"","Journal Schedule","location=1,status=1,scrollbars=1,width=640,height=720");
		} else {
			window.open("reports/journalschedule-s.php?type="+$("#js_type").val()+"&dtf="+$("#js_dtf").val()+"&dt2="+$("#js_dt2").val()+"&acct="+$("#js_acct").val()+"&conso="+conso+"&sid="+Math.random()+"","Journal Schedule","location=1,status=1,scrollbars=1,width=640,height=720");	
		}
	}
}

function generateScheduleXLS() {
	if(isNaN($("#js_acct").val()) == true) {
		sendErrorMessage("Invalid account code specified...");
	} else {
		if($("#js_conso").prop("checked")) { var conso = "Y"; } else { var conso = "N"; }
		if($("#js_rtype").val() == "1") {
			window.open("export/journalschedule.php?type="+$("#js_type").val()+"&dtf="+$("#js_dtf").val()+"&dt2="+$("#js_dt2").val()+"&acct="+$("#js_acct").val()+"&conso="+conso+"&sid="+Math.random()+"","Journal Schedule","location=1,status=1,scrollbars=1,width=640,height=720");
		} else {
			window.open("export/journalschedule-s.php?type="+$("#js_type").val()+"&dtf="+$("#js_dtf").val()+"&dt2="+$("#js_dt2").val()+"&acct="+$("#js_acct").val()+"&conso="+conso+"&sid="+Math.random()+"","Journal Schedule","location=1,status=1,scrollbars=1,width=640,height=720");	
		}
	}
}

function showTrialDiv() {
	$("#tb_asof").datepicker();
	$("#tbalance").dialog({title: "Trial Balance", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateTB() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='reports/trialbalance.php?asof="+$("#tb_asof").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report7").html(txtHTML);
	$("#report7").dialog({title: "Cummulative Trial Balance", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function generateTBXLS() {
	if($("#tb_conso").prop("checked")) { var conso = "Y"; } else { var conso = "N"; }
	window.open("export/trialbalance.php?asof="+$("#tb_asof").val()+"&sid="+Math.random()+"","Trial Balance","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showMoTbal() {
	$("#moTBalance").dialog({title: "Trial Balance of Mo. Transactions", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateMOTB() {
	$.post("src/sjerp.php", { mod: "checkLockStatus", month: $("#motb_month").val(), year: $("#motb_year").val(), sid: Math.random() }, function(ret) {
		if(ret == "NotOK") {
			window.open("reports/motb.php?month="+$("#motb_month").val()+"&year="+$("#motb_year").val()+"&branch="+$("#motb_branch").val()+"&sid="+Math.random()+"","Trial Balance of Mo. Transactions","location=1,status=1,scrollbars=1,width=640,height=720");
		} else {
			$("#message").html("It appears that the period you have selected has yet to be closed. Should you wish to generate interim Trial Balance of Transactions, you may click <b>\"Proceed Anyway\"</b>. Please take note that interim Trial Balance may take longer to generate.");
			$("#errorMessage").dialog({
				width: 400,
				resizable: false,
				modal: true,
				buttons: {
					"Proceed Anyway": function() {
						var dtf = $("#motb_month").val()+"/01/"+$("#motb_year").val();
						var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='reports/motrialbalance.php?dtf="+dtf+"&sid="+Math.random()+"'></iframe>";
						$("#report9").html(txtHTML);
						$("#report9").dialog({title: "Trial Balance of Monthly Transactions", width: 560, height: 620, resizable: true }).dialogExtend({
							"closable" : true,
							"maximizable" : true,
							"minimizable" : true
						});
					},
					"Cancel": function () { $(this).dialog("close"); }
				}
			});
		}
	},"html");
}

function showGLSched() {
	$("#gls_dtf").datepicker(); $("#gls_dt2").datepicker();
	$("#glsched").dialog({title: "GL Account Schedule", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateGL(tag) {
	if(isNaN($("#gls_acct").val()) == true) {
		sendErrorMessage("Invalid account code specified...");
	} else {
		if(tag == 1) {
			var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/glschedule.php?dtf="+$("#gls_dtf").val()+"&dt2="+$("#gls_dt2").val()+"&acct="+$("#gls_acct").val()+"&client="+$("#gls_client").val()+"&sid="+Math.random()+"'></iframe>";
			$("#report8").html(txtHTML);
			$("#report8").dialog({title: "General Ledger Account Schedule", width: xWidth, height: 620, resizable: true }).dialogExtend({
				"closable" : true,
				"maximizable" : true,
				"minimizable" : true
			});
		
		} else {
			window.open("export/glschedule.php?dtf="+$("#gls_dtf").val()+"&dt2="+$("#gls_dt2").val()+"&acct="+$("#gls_acct").val()+"&sid="+Math.random()+"","Trial Balance","location=1,status=1,scrollbars=1,width=640,height=720");
		}
	}
}

function showCashFlow() {
	$("#cf_dtf").datepicker(); $("#cf_dt2").datepicker();
	$("#cashflow").dialog({title: "Cash Position Report", width: 400, height: 210 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateCashFlow() {
	var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/cashposition.php?dtf="+$("#cf_dtf").val()+"&dt2="+$("#cf_dt2").val()+"&source="+$("#cf_source").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report7").html(txtHTML);
	$("#report7").dialog({title: "Cash Position Report", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function showVAR() {
	$("#var").dialog({title: "Variance Analysis Report", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateVAR() {
	var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/var.php?type="+$("#budType").val()+"&year="+$("#budYear").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report7").html(txtHTML);
	$("#report7").dialog({title: "Variance Analysis Report", width: xWidth, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function showBST() {
	$("#bst").dialog({title: "Budgets & Sales Targets", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function getBST() {
	var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/bst.php?type="+$("#bstType").val()+"&year="+$("#bstYear").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report8").html(txtHTML);
	$("#report8").dialog({title: "Budgets & Sales Targets", width: 640, height: 520, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

/* Search Supplier on Reports */
$(document).ready(function($){
    $('#po_sname').autocomplete({
		source:'suggestSupplier.php', 
		minLength:3,
	});
});

function showPurchases() {
	$("#po_dtf").datepicker(); $("#po_dt2").datepicker();
	$("#purchases").dialog({title: "Summary of Purchases", width: 400, height: 245 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generatePurchases() {
	if($("#po_sname").val() != "") {
		var str = $("#po_sname").val();
		var supplier = str.substr(1,6);
	} else { var supplier = ""; }
	
	
	var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/purchases.php?dtf="+$("#po_dtf").val()+"&dt2="+$("#po_dt2").val()+"&type="+$("#po_type").val()+"&supplier="+supplier+"&sid="+Math.random()+"'></iframe>";
	$("#report9").html(txtHTML);
	$("#report9").dialog({title: "Summary of Purchases", width: 800, height: 540, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
	//window.open("reports/purchases.php?dtf="+$("#po_dtf").val()+"&dt2="+$("#po_dt2").val()+"&type="+$("#po_type").val()+"&supplier="+supplier+"&sid="+Math.random()+"","Trial Balance","location=1,status=1,scrollbars=1,width=640,height=720");
}

function generatePurchasesX() {
	if($("#po_sname").val() != "") {
		var str = $("#po_sname").val();
		var supplier = str.substr(1,6);
	} else { var supplier = ""; }
	
	
	//var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/purchases.php?dtf="+$("#po_dtf").val()+"&dt2="+$("#po_dt2").val()+"&type="+$("#po_type").val()+"&supplier="+supplier+"&sid="+Math.random()+"'></iframe>";

	window.open("export/purchase.php?dtf="+$("#po_dtf").val()+"&dt2="+$("#po_dt2").val()+"&type="+$("#po_type").val()+"&supplier="+supplier+"&sid="+Math.random()+"","Summary of Purchases","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showSumExpenses() {
	$("#exs_dtf").datepicker(); $("#exs_dt2").datepicker();
	$("#expsum").dialog({title: "Summary of Purchases & Expenditures", width: 450 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateSummaryExpenses() {
	window.open("export/expenditures.php?dtf="+$("#exs_dtf").val()+"&dt2="+$("#exs_dt2").val()+"&sid="+Math.random()+"","Trial Balance","location=1,status=1,scrollbars=1,width=640,height=720");
}


function showChecks() {
	$("#ic_dtf").datepicker(); $("#ic_dt2").datepicker();
	$("#checks").dialog({title: "Summary of Issued Checks", width: 400, height: 210 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateChecks() {
	var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/checkssummary.php?dtf="+$("#ic_dtf").val()+"&dt2="+$("#ic_dt2").val()+"&source="+$("#ic_source").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report6").html(txtHTML);
	$("#report6").dialog({title: "Summary of Issued Checks", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
	//window.open("reports/checkssummary.php?dtf="+$("#ic_dtf").val()+"&dt2="+$("#ic_dt2").val()+"&source="+$("#ic_source").val()+"&sid="+Math.random()+"","Trial Balance","location=1,status=1,scrollbars=1,width=640,height=720");
}

function generateChecksXLS() {
	window.open("export/checkssummary.php?dtf="+$("#ic_dtf").val()+"&dt2="+$("#ic_dt2").val()+"&source="+$("#ic_source").val()+"&sid="+Math.random()+"","Trial Balance","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showAcctgReports() {
	$("#acctgReportMain").dialog({title: "Accounting & Financial Reports", width: 1080 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function showCAB() {
	$("#cab_asof").datepicker();
	$("#accountbalance").dialog({title: "Statement of Account", width: 400, height: 245 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateSOA() {
	var msg = "";
	if($("#cab_sname").val() == '') { 
		msg = msg + "- You have specified an invalid Customer<br/>"; 
	} else {
		var str = $("#cab_sname").val();
		var cid = str.substr(1,6);
		$.post("src/sjerp.php", { "mod": "verifyCID", cid: cid, sid: Math.random() }, function(res) { if(res != "Ok") { msg = msg + "- You have specified an invalid Customer<br/>";  }},"html");
	}

	if(msg == "") {
		if($("#with_soa_num").prop("checked")) { var with_soa_num = "Y"; } else { var with_soa_num = "N"; }
		if($("#overdue_only").prop("checked")) { var overdue_only = "Y"; } else { var overdue_only = "N"; }
		var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/soa.php?asof="+$("#cab_asof").val()+"&cid="+cid+"&with_soa_num="+with_soa_num+"&overdue_only="+overdue_only+"&sid="+Math.random()+"'></iframe>";
		$("#report8").html(txtHTML);
		$("#report8").dialog({title: "Statement of Accouont", width: 560, height: 620, resizable: true }).dialogExtend({
			"closable" : true,
			"maximizable" : true,
			"minimizable" : true
		});
	} else { sendErrorMessage(msg); }
	
}

function showOutInvoices() {
	$("#coi_asof").datepicker();
	$.post("src/sjerp.php", { mod: "getHomeowners", sid: Math.random() }, function(ret) { $("#coi_cust").html(ret); },"html");
	$("#outstandingInvoices").dialog({title: "Unpaid Billing Statements", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateOutstanding() {
	if($("#coi_isoverdue").prop("checked")) { var od = "Y"; } else { var od = "N"; }
	var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/outstanding.php?asof="+$("#coi_asof").val()+"&cust="+$("#coi_cust").val()+"&od="+od+"&sid="+Math.random()+"'></iframe>";
	$("#report8").html(txtHTML);
	$("#report8").dialog({title: "Unpaid Billing Statements", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function showAPPayable() {
	$("#oap_asof").datepicker();
	$.post("src/sjerp.php", { mod: "getSuppliers", sid: Math.random() }, function(ret) { $("#oap_payee").html(ret); },"html");
	$("#overdueAP").dialog({title: "Outstanding Accounts Payable Voucher", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateOAP() {
	if($("#oap_isoverdue").prop("checked")) { var od = "Y"; } else { var od = "N"; }
	var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/apoutstanding.php?asof="+$("#coi_asof").val()+"&cust="+$("#oap_payee").val()+"&od="+od+"&sid="+Math.random()+"'></iframe>";
	$("#report6").html(txtHTML);
	$("#report6").dialog({title: "Outstanding Accounts Payable Voucher", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function showInVat() {
	$("#ivat_dtf").datepicker(); $("#ivat_dt2").datepicker();
	$.post("src/sjerp.php", { mod: "getSuppliers", sid: Math.random() }, function(ret) { $("#ivat_payee").html(ret); },"html");
	$("#inVatSummary").dialog({title: "Summary of Vatable Purchases", width: 400, modal: true }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateInVatSummaryPDF() {
	var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/invatSummary.php?dtf="+$("#ivat_dtf").val()+"&dt2="+$("#ivat_dt2").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report3").html(txtHTML);
	$("#report3").dialog({title: "Summary of Vatable Purchases", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function generateInVatSummaryXLS() {
	window.open("export/invatSummary.php?dtf="+$("#ivat_dtf").val()+"&dt2="+$("#ivat_dt2").val()+"&sid="+Math.random()+"","Summary of Vatable Purchases","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showSubLedger() {
	$("#subledger_asof").datepicker();
	$("#subledger").dialog({title: "Account Balance", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateSubLedger() {
	var msg = "";
	if($("#subledger_sid").val() == '') { 
		msg = msg + "- You have specified an invalid Customer or Supplier<br/>"; 
	} 
	if($("#subledger_acct").val() == '') { 
		msg = msg + "- Subsidiary Account must be identified before trying to generate this report<br/>"; 
	} else {
		$.post("src/sjerp.php", { "mod": "verifyACCT", acct: $("#subledger_acct").val(), sid: Math.random() }, function(res) { if(res == "NotFound") { msg = msg + "- You have specified an invalid Account Code<br/>";  }},"html");
	}
	
	
	if(msg == "") {
		var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/accountbalance.php?asof="+$("#subledger_asof").val()+"&acct="+$("#subledger_acct").val()+"&cid="+$("#subledger_sid").val()+"&sid="+Math.random()+"'></iframe>";
		$("#report9").html(txtHTML);
		$("#report9").dialog({title: "Account Balance", width: 560, height: 620, resizable: true }).dialogExtend({
			"closable" : true,
			"maximizable" : true,
			"minimizable" : true
		});
	} else { sendErrorMessage(msg); }
	
}

function showARAS() {
	$("#aras_asof").datepicker();
	$("#aras").dialog({title: "AR - Aging Schedule", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateARAS() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='reports/aras.php?asof="+$("#aras_asof").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report2").html(txtHTML);
	$("#report2").dialog({title: "AR - Aging Schedule", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function showAPAS() {
	$("#apas_asof").datepicker();
	$("#apas").dialog({title: "AP - Aging Schedule", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateAPAS() {
	if($("#apas_conso").prop("checked")) { var conso = "Y"; } else { var conso = "N"; }
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='reports/apas.php?asof="+$("#apas_asof").val()+"&sid="+Math.random()+"&conso="+conso+"'></iframe>";
	$("#report3").html(txtHTML);
	$("#report3").dialog({title: "AP - Aging Schedule", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function showIS() {
	$("#incomestatement").dialog({title: "Income Statement", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateIS() {
	$.post("src/sjerp.php", { mod: "checkLockStatus", month: $("#is_month").val(), year: $("#is_year").val(), sid: Math.random() }, function(ret) {
		if(ret == "NotOK") {
			var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='reports/mois-pdf.php?month="+$("#is_month").val()+"&cc="+$("#is_cc").val()+"&year="+$("#is_year").val()+"&sid="+Math.random()+"'></iframe>";
			$("#report1").html(txtHTML);
			$("#report1").dialog({title: "INCOME STATEMENT", width: 560, height: 620, resizable: true }).dialogExtend({
				"closable" : true,
				"maximizable" : true,
				"minimizable" : true
			});
		} else {
			$("#message").html("It appears that the period you have selected has yet to be closed. Should you wish to generate interim Trial Balance of Transactions, you may click <b>\"Proceed Anyway\"</b>. Please take note that interim Trial Balance may take longer to generate.");
			$("#errorMessage").dialog({
				width: 400,
				resizable: false,
				modal: true,
				buttons: {
					"Proceed Anyway": function() {
						window.open("reports/incomestatement.php?month="+$("#is_month").val()+"&cc="+$("#is_cc").val()+"&year="+$("#is_year").val()+"&sid="+Math.random()+"","INCOME STATEMENT","location=1,status=1,scrollbars=1,width=640,height=720");
						$(this).dialog("close");
					},
					"Cancel": function () { $(this).dialog("close"); }
				}
			});
		}
	},"html");
}

function generateISEX() {
	window.open("export/incomestatement.php?month="+$("#is_month").val()+"&cc="+$("#is_cc").val()+"&year="+$("#is_year").val()+"&sid="+Math.random()+"","Income Statement - Excel","location=1,status=1,scrollbars=1,width=400,height=240");
}

function showClosing() {
	$("#transLock").dialog({title: "Finalize Monthly Transactions", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function lockTransactions() {
	if($("#lock_year").val() == "" || isNaN($("#lock_year").val() == true)) {
			sendErrorMessage("Please specify a valid year in a form of integer or YYYY format");
		} else {
			$.post("src/sjerp.php", { mod: "checkLockStatus", month: $("#lock_month").val(), year: $("#lock_year").val(), sid: Math.random() }, function(ret) {
			if(ret == "Ok") {
				if(confirm("This action would lock transactions of the specified period. You can no longer make changes nor add transactions unless otherwise the specified period is lifted from the locking database. Do you still wish to continue?") == true) {	
					showLoaderMessage();
					$.post("src/sjerp.php", { mod: "lockStatusOk", branch: $("#lock_branch").val(), month: $("#lock_month").val(), year: $("#lock_year").val(), memo: $("#lock_memo").val(), sid: Math.random() }, function() {
						$("#loaderMessage").dialog("close");
						closeDialog("#transLock");
						$("#lock_year").val('');
						$("#lock_month").val('01');
						$("#lock_memo").val('');
						alert("Transactions for the specified has been successfully finalized and locked!");
					});	
				}
			} else {
				sendErrorMessage("The period you have selected appears to have been Finalized & already locked.");
			}
		},"html");
	}
}

function showUnlock() {
	$("#uLock").dialog({title: "Unlock Transactions", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function unlockTransactions() {
	if($("#ulock_year").val() == "" || isNaN($("#ulock_year").val() == true)) {
			sendErrorMessage("Please specify a valid year in a form of integer or YYYY format");
		} else {
			$.post("src/sjerp.php", { mod: "checkLockStatus", month: $("#ulock_month").val(), year: $("#ulock_year").val(), sid: Math.random() }, function(ret) {
			if(ret != "Ok") {
				if(confirm("This action would unlock transactions of the specified period. Changes made to documents on this period may affect previously posted Financial and other relevant reports. Do you wish to continue?") == true) {	
					$.post("src/sjerp.php", { mod: "unLock", branch: $("#ulock_branch").val(), month: $("#ulock_month").val(), year: $("#ulock_year").val(), sid: Math.random() }, function() {
						alert("Transactions for the specified period was successfully unlocked!");
						closeDialog("#uLock");
						$("#ulock_year").val('');
						$("#ulock_month").val('01');
					});	
				}
			} else {
				sendErrorMessage("The period you have selected seems to have not been locked yet...");
			}
		},"html");
	}
}

function showBS() {
	$("#balanceSheet").dialog({title: "Balance Sheet", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateBalanceSheet(tag) {
	$.post("src/sjerp.php", { mod: "checkLockStatus", month: $("#is_month").val(), year: $("#is_year").val(), sid: Math.random() }, function(ret) {
		if(ret == "NotOK") {
			var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='reports/mobs.php?month="+$("#bs_month").val()+"&year="+$("#bs_year").val()+"&sid="+Math.random()+"'></iframe>";
			$("#report1").html(txtHTML);
			$("#report1").dialog({title: "INCOME STATEMENT", width: 560, height: 620, resizable: true }).dialogExtend({
				"closable" : true,
				"maximizable" : true,
				"minimizable" : true
			});
		} else {
			$("#message").html("It appears that the period you have selected has yet to be closed. Should you wish to generate interim Balance Sheet, you may click <b>\"Proceed Anyway\"</b>. Please take note that interim Balance Sheet may take longer to generate.");
			$("#errorMessage").dialog({
				width: 400,
				resizable: false,
				modal: true,
				buttons: {
					"Proceed Anyway": function() {
						if(tag == 1) {
							window.open("reports/balancesheet.php?month="+$("#bs_month").val()+"&year="+$("#bs_year").val()+"&sid="+Math.random()+"","Balance Sheet","location=1,status=1,scrollbars=1,width=640,height=720");
						} else {
							window.open("export/balancesheet.php?month="+$("#bs_month").val()+"&year="+$("#bs_year").val()+"&sid="+Math.random()+"","Balance Sheet","location=1,status=1,scrollbars=1,width=640,height=720");
						}
						$(this).dialog("close");
					},
					"Cancel": function () { $(this).dialog("close"); }
				}
			});
		}
	},"html");
}

function showColPerf() {
	$("#ColPerf").dialog({title: "Collection Performance", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateColPerf() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='reports/colperf-2.php?year="+$("#colperf_year").val()+"&month="+$("#colperf_month").val()+"&branch="+$("#colperf_branch").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report7").html(txtHTML);
	$("#report7").dialog({title: "COLLECTION PERFORMANCE", width: xWidth, height: 520, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function showExpSched() {
	$("#ExpSched").dialog({title: "Schedule of Expense", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateExpSched(tag) {
	if($("#expsched_year").val() == "") {
		sendErrorMessage("You must indicate the calendar year for this Collection Performance report...");
	} else {
		if(tag == 1) {
			window.open("reports/expsched.php?month="+$("#expsched_month").val()+"&year="+$("#expsched_year").val()+"&sid="+Math.random()+"","Collection Performance","location=1,status=1,scrollbars=1,width=800,height=760");
		} else {
			window.open("export/expsched.php?month="+$("#expsched_month").val()+"&year="+$("#expsched_year").val()+"&sid="+Math.random()+"","Collection Performance","location=1,status=1,scrollbars=1,width=800,height=760");
		}
	}
}

function showSR() {
	$("#salesReportMain").dialog({title: "Sales & Inventory Reports", width: 980, height: 580 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function showSalesSummary() {
	$("#ss_dtf").datepicker(); $("#ss_dt2").datepicker();
	$("#salessummary").dialog({title: "Sales Summary Report", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateSalesSummary() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='reports/sr_summary-2.php?dtf="+$("#ss_dtf").val()+"&dt2="+$("#ss_dt2").val()+"&type="+$("#ss_type").val()+"&branch="+$("#ss_branch").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report3").html(txtHTML);
	$("#report3").dialog({title: "Sales Summary Report", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function showCPDC() {
	$("#cpdc_date").datepicker();
	$("#cpdc").dialog({title: "On-Due PDC Checks", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateCPDC() {
	var txtHTML = "<iframe id='frmsosummary' frameborder=0 width='100%' height='100%' src='reports/duepdcs.php?dtf="+$("#cpdc_date").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report1").html(txtHTML);
	$("#report1").dialog({title: "Sales Order Summary", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}


function showInquiry() {
	$("#sinq_date").datepicker();
	$("#salesinquiry").dialog({title: "Daily Sales & Collection Report", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateInquiry() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='reports/dscr.php?date="+$("#sinq_date").val()+"&branch="+$("#sinq_branch").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report3").html(txtHTML);
	$("#report3").dialog({title: "Daily Sales & Collection Report", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function showSalesContrib() {
	$("#scontrib_dtf").datepicker(); $("#scontrib_dt2").datepicker();
	$("#salesContrib").dialog({title: "Sales Contribution Per Product Group", width: 400, height: 210 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateContrib() {
	window.open("reports/salescontrib.php?dtf="+$("#scontrib_dtf").val()+"&dt2="+$("#scontrib_dt2").val()+"&branch="+$("#scontrib_branch").val()+"&sid="+Math.random()+"","Sales Contribution Per Product Group","location=1,status=1,scrollbars=1,width=840,height=720");	
}

function showContribBranch() {
	$("#scontrib2_dtf").datepicker(); $("#scontrib2_dt2").datepicker();
	$("#salesContribBranch").dialog({title: "Sales Contribution Per Branch", width: 400, height: 210 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateContribBranch() {
	window.open("reports/salescontrib2.php?dtf="+$("#scontrib2_dtf").val()+"&dt2="+$("#scontrib2_dt2").val()+"&group="+$("#scontrib2_type").val()+"&sid="+Math.random()+"","Sales Contribution Per Branch","location=1,status=1,scrollbars=1,width=840,height=720");	
}

function showSPerf() {
	$("#salesPerformance").dialog({title: "Sales Performance Per Product", width: 400, height: 210 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateSPerf() {
	window.open("reports/sperf.php?year="+$("#sperf_year").val()+"&group="+$("#sperf_type").val()+"&branch="+$("#sperf_branch").val()+"&sid="+Math.random()+"","Sales Performance Per Product","location=1,status=1,scrollbars=1,width=720,height=640");	
}

function showTopProducts() {
	$("#tp_dtf").datepicker(); $("#tp_dt2").datepicker();
	$("#topproducts").dialog({title: "Top Selling Products", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateTopProducts() {
	if(isNaN($("#tp_ranks").val() || $("#tp_rank").val() == '') == true) {
		sendErrorMessage("Cannot process report. No. of Ranks must be an integer...");
	} else {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='reports/topproducts.php?pg="+$("#tp_group").val()+"&dtf="+$("#tp_dtf").val()+"&dt2="+$("#tp_dt2").val()+"&type="+$("#tp_type").val()+"&ranks="+$("#tp_ranks").val()+"&branch="+$("#tp_branch").val()+"&sid="+Math.random()+"'></iframe>";
		$("#report5").html(txtHTML);
		$("#report5").dialog({title: "Top Products", width: 560, height: 620, resizable: true }).dialogExtend({
			"closable" : true,
			"maximizable" : true,
			"minimizable" : true
		});
	}
}

function showTopBranches() {
	$("#tc_dtf").datepicker(); $("#tc_dt2").datepicker();
	$("#topbranches").dialog({title: "Top Performing Branches", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateTopBranches() {
	window.open("reports/topbranches.php?dtf="+$("#tpb_dtf").val()+"&dt2="+$("#tpb_dt2").val()+"&sid="+Math.random()+"","Top Performing Branches","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showTopCustomers() {
	$("#tc_dtf").datepicker(); $("#tc_dt2").datepicker();
	$("#topcustomers").dialog({title: "Top Customers", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateTopCustomers() {
	if(isNaN($("#tp_ranks").val()) == true) {
		sendErrorMessage("Cannot process report. No. of Ranks must be an integer...");
	} else {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='reports/topcustomers.php?group="+$("#tc_group").val()+"&dtf="+$("#tc_dtf").val()+"&dt2="+$("#tc_dt2").val()+"&ranks="+$("#tc_ranks").val()+"&branch="+$("#tc_branch").val()+"&sid="+Math.random()+"'></iframe>";
		$("#report2").html(txtHTML);
		$("#report2").dialog({title: "Top Customers", width: 560, height: 620, resizable: true }).dialogExtend({
			"closable" : true,
			"maximizable" : true,
			"minimizable" : true
		});
	}
}

function showWeeklySales() {
	$("#weeklySales").dialog({title: "Weekly Sales Report", width: 400, height: 240 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateWS() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='reports/weeklySales.php?month="+$("#ws_month").val()+"&year="+$("#ws_year").val()+"&branch="+$("#ws_branch").val()+"&group="+$("#ws_group").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report4").html(txtHTML);
	$("#report4").dialog({title: "Weekly Sales Performance", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
	
	//window.open("reports/weeklySales.php?month="+$("#ws_month").val()+"&year="+$("#ws_year").val()+"&branch="+$("#ws_branch").val()+"&group="+$("#ws_group").val()+"&sid="+Math.random()+"","Weekly Sales Report","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showDRS() {
	$("#drs_dtf").datepicker(); $("#drs_dt2").datepicker();
	$("#drsummary").dialog({title: "Summary of Goods Transferred", width: 400, height: 210 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateDRSummary() {
	var txtHTML = "<iframe id='frmdrsummary' frameborder=0 width='100%' height='100%' src='reports/dr_summary.php?dtf="+$("#drs_dtf").val()+"&dt2="+$("#drs_dt2").val()+"&cid="+$("#drs_cid").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report2").html(txtHTML);
	$("#report2").dialog({title: "DR Summary", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
	//window.open("reports/dr_summary.php?dtf="+$("#drs_dtf").val()+"&dt2="+$("#drs_dt2").val()+"&cid="+$("#drs_cid").val()+"&sid="+Math.random()+"","Summary Goods Transferred","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showRRS() {
	$("#rrs_dtf").datepicker(); $("#rrs_dt2").datepicker();
	$("#rrsummary").dialog({title: "Summary of Goods Received", width: 400, height: 210 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateRRSummary() {
	var txtHTML = "<iframe id='frmrrsummary' frameborder=0 width='100%' height='100%' src='reports/rr_summary.php?dtf="+$("#rrs_dtf").val()+"&dt2="+$("#rrs_dt2").val()+"&cid="+$("#rrs_cid").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report5").html(txtHTML);
	$("#report5").dialog({title: "Summary of Goods Received from Suppliers", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
	//window.open("reports/rr_summary.php?dtf="+$("#rrs_dtf").val()+"&dt2="+$("#rrs_dt2").val()+"&cid="+$("#rrs_cid").val()+"&sid="+Math.random()+"","Summary Goods Received","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showDetailedSales() {
	$("#dsa_dtf").datepicker(); $("#dsa_dt2").datepicker();
	$("#detailedSales").dialog({title: "Detailed Sales Report", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateDetailedSales() {
	//window.open("reports/sr_detailed-2.php?dtf="+$("#ds_dtf").val()+"&dt2="+$("#ds_dt2").val()+"&cid="+$("#ds_cid").val()+"&sid="+Math.random()+"","Sales Report Detailed","location=1,status=1,scrollbars=1,width=640,height=720");
	var txtHTML = "<iframe id='frmdetailedsales' frameborder=0 width='100%' height='100%' src='reports/sr_detailed-2.php?dtf="+$("#dsa_dtf").val()+"&dt2="+$("#dsa_dt2").val()+"&cid="+$("#dsa_cid").val()+"&branch="+$("#dsa_branch").val()+"&group="+$("#dsa_group").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report4").html(txtHTML);
	$("#report4").dialog({title: "Customer Detailed Sales", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function showSOS() {
	$("#sos_dtf").datepicker(); $("#sos_dt2").datepicker();
	$("#sosummary").dialog({title: "Sales Order Summary", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateSOSummary() {
	var txtHTML = "<iframe id='frmsosummary' frameborder=0 width='100%' height='100%' src='reports/so_summary.php?dtf="+$("#sos_dtf").val()+"&dt2="+$("#sos_dt2").val()+"&cid="+$("#sos_cid").val()+"&srep="+$("#sos_srep").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report1").html(txtHTML);
	$("#report1").dialog({title: "Sales Order Summary", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
	//window.open("reports/so_summary.php?dtf="+$("#sos_dtf").val()+"&dt2="+$("#sos_dt2").val()+"&cid="+$("#sos_cid").val()+"&sid="+Math.random()+"","Sales Order Summary","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showSPS() {
	$("#sps_dtf").datepicker(); $("#sps_dt2").datepicker();
	$("#sperman").dialog({title: "Sales Report per Salesman", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateSPS() {
	var txtHTML = "<iframe id='frmsosummary' frameborder=0 width='100%' height='100%' src='reports/sperman.php?dtf="+$("#sps_dtf").val()+"&dt2="+$("#sps_dt2").val()+"&srep="+$("#sps_srep").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report4").html(txtHTML);
	$("#report4").dialog({title: "Sales Report per Salesman", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
	//window.open("reports/so_summary.php?dtf="+$("#sos_dtf").val()+"&dt2="+$("#sos_dt2").val()+"&cid="+$("#sos_cid").val()+"&sid="+Math.random()+"","Sales Order Summary","location=1,status=1,scrollbars=1,width=640,height=720");
}


function showSGW() {
	$("#sgw_dtf").datepicker(); $("#sgw_dt2").datepicker();
	$("#sgwsummary").dialog({title: "Summary of Goods Withdrawn", width: 400, height: 210 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateSGW() {
	var txtHTML = "<iframe id='frmsgw' frameborder=0 width='100%' height='100%' src='reports/sw_summary.php?dtf="+$("#sgw_dtf").val()+"&dt2="+$("#sgw_dt2").val()+"&type="+$("#sgw_type").val()+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
	$("#report5").html(txtHTML);
	$("#report5").dialog({title: "Summary of Goods Withdrawn", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
	//window.open("reports/sw_summary.php?dtf="+$("#sgw_dtf").val()+"&dt2="+$("#sgw_dt2").val()+"&type="+$("#sgw_type").val()+"&sid="+Math.random()+"","Summary of Goods Withdrawn","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showDMP() {
	$("#dmp_date").datepicker();
	$("#dmp").dialog({title: "Daily Material Plan", width: 400, height: 180 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateDMP() {
	window.open("reports/dmp.php?date="+$("#dmp_date").val()+"&type="+$("#dmp_type").val()+"&sid="+Math.random()+"","Daily Material Plan","location=1,status=1,scrollbars=1,width=640,height=720");
}

function generateDMPhtml() {
	window.open("reports/dmp.php?date="+$("#dmp_date").val()+"&type="+$("#dmp_type").val()+"&sid="+Math.random()+"","Daily Material Plan","location=1,status=1,scrollbars=1,width=640,height=720");
}

function showPOSDaily() {
	$("#ds_date").datepicker();
	$("#dailySales").dialog({title: "Daily Sales Report", width: 400, height: 220 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateDailySales() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='reports/dailysales.php?date="+$("#ds_date").val()+"&group="+$("#ds_group").val()+"&branch="+$("#ds_branch").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report1").html(txtHTML);
	$("#report1").dialog({title: "Daily Sales Report", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function showColReport() {
	$("#colrep_dtf").datepicker(); $("#colrep_dt2").datepicker();
	$("#collection").dialog({title: "Collection Report", width: 400, height: 245 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateCollectionReport() {
	if($("#colrep_sname").val() != "") {
		var str = $("#colrep_sname").val();
		var cid = str.substr(1,6);
	} else { cid = ""; }
	
	if($("#colrep_type").val() == 1) {
		window.open("reports/collectionreport-d.php?dtf="+$("#colrep_dtf").val()+"&dt2="+$("#colrep_dt2").val()+"&cid="+cid+"&sid="+Math.random()+"","Collection Report Detailed","location=1,status=1,scrollbars=1,width=640,height=720");
	} else {
		window.open("reports/collectionreport-s.php?dtf="+$("#colrep_dtf").val()+"&dt2="+$("#colrep_dt2").val()+"&cid="+cid+"&sid="+Math.random()+"","Collection Report Summary","location=1,status=1,scrollbars=1,width=640,height=720");
	}
}

function backupDBase() {
	window.open("dbBackup.php","Backup Database","location=1,status=1,scrollbars=1,width=300,height=180");
}

function closeDialog(frame) {
	$(frame).dialog("close");
}

	function open_overlay(){	
		$("#mainLoading").css("z-index","999");
		$("#mainLoading").show();
	}
	
	function close_overlay(){	
		$("#mainLoading").hide();
	}
	
	function showUnclass(){
		$("#unclass_dtf").datepicker(); $("#unclass_dt2").datepicker();
		$("#unclass").dialog({title: "Unclassified Accounts", width: 400 }).dialogExtend({
			"closable" : true,
		    "maximizable" : false,
		    "minimizable" : true
		});
	}
	
	function genUnclassified(){
		window.open("print/unclassified.php?dtf="+$("#unclass_dtf").val()+"&dt2="+$("#unclass_dt2").val()+"&doc_type="+$("#unclass_doc").val()+"&sid="+Math.random()+"","Unclassified Accounts","location=1,status=1,scrollbars=1,width=640,height=720");
	
	}
	
	function showIssuedDoc(){
		$("#listofdoc_dtf").datepicker(); $("#listofdoc_dt2").datepicker();
		$("#listofdoc").dialog({title: "Issued Documents", width: 400 }).dialogExtend({
			"closable" : true,
		    "maximizable" : false,
		    "minimizable" : true
		});
	}
	
	function genDocList(){
		window.open("print/issued_doc.php?dtf="+$("#listofdoc_dtf").val()+"&dt2="+$("#listofdoc_dt2").val()+"&doc_type="+$("#listofdoc_type").val()+"&status="+$("#listofdoc_status").val()+"","Unclassified Accounts","location=1,status=1,scrollbars=1,width=640,height=720");
	
	}

	function showGLPosting(){
		$("#glp_dtf").datepicker({ changeMonth: true, changeYear: true }); $("#glp_dt2").datepicker({ changeMonth: true, changeYear: true });
		$("#glposting").dialog({title: "Post Revenue to GL", width: 400 }).dialogExtend({
			"closable" : true,
		    "maximizable" : false,
		    "minimizable" : true
		});
	}
	
	function postRevenuetoGL(){
		window.open("tmp/gl_posting.php?dtf="+$("#glp_dtf").val()+"&dt2="+$("#glp_dt2").val()+"&sid="+Math.random()+"","Post Revenue to GL","location=1,status=1,scrollbars=1,width=640,height=720");
	
	}
	
	function showBankRecon(){
		$('#clearedbalance').val('0.00');
		$('#diffbalance').val('0.00');
		$('#balanceend').val('0.00');
		$('#balend').val('0.00');
		$('#balopen').val('0.00');
		$('#debits').val('0');
		$('#credits').val('0');
		$('#acct_code').val('');
		$("#cr_transaction").html("");
		$("#db_transaction").html("");
		$("#bankrecondiv").dialog({title: "Bank Reconciliation", width: xWidth }).dialogExtend({
			"closable" : true,
		    "maximizable" : false,
		    "minimizable" : true
		});
	}
	
	function bank_getData() {
		var xdt = $("#tmp_date").val();
		var acct = $("#acct_code").val();
		if(xdt!="" && acct!="") {
			$.post("bankrecon/acctg.bankrecon_getdebit.php", { xdate: xdt, acct_code:acct, sid: Math.random() }, function (data) {
				$("#db_transaction").html(data);
			}, "html");
			$.post("bankrecon/acctg.bankrecon_getcredit.php", { xdate: xdt, acct_code:acct, sid: Math.random() }, function (data) {
				$("#cr_transaction").html(data);
			}, "html");
			$.post("bankrecon/acctg.bankrecon_getbalances.php", { xdate: xdt, acct_code:acct, sid: Math.random() }, function (data) {
				$("#balanceopen").val(data.balance_beginning);
				$("#clearedbalance").val(data.clearedbalance);
				$("#debits").val(data.d_cleared);
				$("#credits").val(data.c_cleared);
				
				var end = $("#balanceend").val()
					end = end.replace(/,/g,"");
				var cleared = data.abscbalance;
					cleared = cleared.replace(/,/g,"");

				var diff = parseFloat(end) + parseFloat(cleared);
				$("#diffbalance").val(kSeparator(diff.toFixed(2)));
			}, "json");
		}
	}
	
	function toggle_me(el,val) {
		var xdt = $("#tmp_date").val();
		var acct = $("#acct_code").val();
		var obj = document.getElementById(el);
		if(obj.checked == true) { var push = "Y"; } else { var push = "N"; }
		$.post("bankrecon/acctg.queforclearing.php", { push: ""+push+"", xval: ""+val+"", xdate: ""+xdt+"", acct_code: ""+acct+"", sid:""+Math.random()+""}, function(data) {
			$("#clearedbalance").val(kSeparator(data.cbalance));
			$("#debits").val(data.d_cleared);
			$("#credits").val(data.c_cleared);
			var end = $("#balend").val()
				end = parseFloat(end.replace(/,/g,""));
			var diffBalance = end - parseFloat(data.abscbalance);				
			$("#diffbalance").val(kSeparator(diffBalance.toFixed(2)));
		},"json");
	}
	
	function clear_selected() {
		if($("#acct_code").val() == "") {
			alert("Error: Unable to continue. No data to reconcile.")
		} else {
			var diff = $("#diffbalance").val();
		    diff = parseFloat(diff.replace(/,/g,""))
			if(confirm("Are you sure you want to finalized this bank recon process?") == true) {
				if(diff > 0 || diff < 0) {
					alert("Error: Cannot continue... Difference Balance should be zero (0) upon finalizing Bank Recon");
				} else {
					$.post("bankrecon/acctg.bankrecon_clearnow.php", { acct_code: $("#acct_code").val(), date: $("#tmp_date").val(), balOpen: $("#balanceopen").val(), balend: $("#balend").val(), sid: Math.random() }, function(traceNo) {
						
					});
				}
			}
		}	
	}
	
	function check_all() {
	   if(confirm("Are you sure you want to check all entries on this form?") == true) {
			var acct = $("#acct_code").val();
			if(acct != "") {
				$.post("bankrecon/acctg.bankrecon_checkall.php", { acct_code: ""+$("#acct_code").val(), date: ""+$("#tmp_date").val(), sid: ""+Math.random()+"" }, function(data) {
					/* $('input[type=checkbox]').attr('checked',true);
					$("#clearedbalance").val(addCommas(data['cbalance']));
					$("#debits").val(data['d_cleared']);
					$("#credits").val(data['c_cleared']);
					var end = $("#balend").val()
					if(end != '') {	end = parseFloat(end.replace(/,/g,"")) } else { end = 0; }
					var diffBalance = end - parseFloat(data['abscbalance']);				
					$("#diffbalance").val(kSeparator(diffBalance.toFixed(2)));
					*/
					
					bank_getData();
				},"json");
			} else {
				alert("Error: Invalid Bank Account to reconcile.");
			}
	   }
	}
	
	function uncheck_all() {
	   if(confirm("Are you sure you want to uncheck all entries on this form?") == true) {
			var acct = $("#acct_code").val();
			if(acct != "") {
				$.post("bankrecon/acctg.bankrecon_uncheckall.php", { acct_code: ""+$("#acct_code").val(), date: ""+$("#tmp_date").val(), sid: ""+Math.random()+"" }, function(data) {
					$('input[type=checkbox]').attr('checked',false);
					$("#clearedbalance").val(addCommas(data['cbalance']));
					$("#debits").val(data['d_cleared']);
					$("#credits").val(data['c_cleared']);
					var end = $("#balend").val()
					if(end != '') {	end = parseFloat(end.replace(/,/g,"")) } else { end = 0; }
					var diffBalance = end - parseFloat(data['abscbalance']);				
					$("#diffbalance").val(kSeparator(diffBalance.toFixed(2)));
					
				},"json");
			} else {
				alert("Error: Invalid Bank Account to reconcile...");
			}
	   }
	}
	
	function computeDifference(endBalance) {
		
		//alert(endBalance);
		
		var end = parseFloat(stripComma(endBalance));
		var cleared = parseFloat(stripComma($("#clearedbalance").val()));

		var diff = cleared - end;
			
		$("#diffbalance").val(kSeparator(diff.toFixed(2)));

	}
	
		
	function unserved_po(){
		$("#unservedpo_dtf").datepicker(); $("#unservedpo_dt2").datepicker();
		$("#unservedpo").dialog({title: "Unserved S.O.", width: 400 }).dialogExtend({
			"closable" : true,
		    "maximizable" : false,
		    "minimizable" : true
		});
	}
	
	function showAudTrail() {
		$("#audDTF").datepicker(); $("#audDT2").datepicker(); 
		$("#audtrail").dialog({title: "Audit Trail", width: 400 }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}
	
	function viewAuditTrail(){
		window.open("reports/audittrail.php?dtf="+$("#audDTF").val()+"&dt2="+$("#audDT2").val()+"&user="+$("#audUser").val()+"&module="+$("#audType").val()+"&sid="+Math.random()+"","Unclassified Accounts","location=1,status=1,scrollbars=1,width=640,height=720");
	}
	
	/* Human Resources */
	function showEmployees() {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/employee.list.php'></iframe>";
		$("#e_list").html(txtHTML);
		$("#e_list").dialog({title: "Employee Masterfile", width: xWidth, height: 540, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function jumpEPage(pageNum,stext) {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/employee.list.php?page="+pageNum+"&searchtext="+stext+"&sid="+Math.random()+"'></iframe>";
		$("#e_list").html(txtHTML);
		$("#e_list").dialog({title: "Employee Masterfile", width: xWidth, height: 540, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function showEmpProfile(emp_id) {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/employee.details.php?emp_idno="+emp_id+"&sid="+Math.random()+"'></iframe>";
		$("#e_details").html(txtHTML);
		$("#e_details").dialog({title: "Employee Profile", width: xWidth, height: 800, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function newEmp() {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/employee.details.php?sid="+Math.random()+"'></iframe>";
		$("#e_details").html(txtHTML);
		$("#e_details").dialog({title: "New Employee Record", width: xWidth, height: 800, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function managePayroll() {
		$("#managePayroll").dialog({title: "Manage Payroll", width: xWidth, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}
	
	function managePayroll2() {
		$("#managePayroll2").dialog({title: "Manage Payroll", width: 1024, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function getPayperiods(ptype,selbox) {
		$.post("hrd/misc-data.php", { mod: "getPeriods", type: ptype, sid: Math.random() }, function(data) { document.getElementById(selbox).innerHTML = data; },"html");
	}
	
	
	
	function getEmployeesByDept(dept,batch,selbox) {
		$.post("hrd/misc-data.php", { mod: "getEmployeesByDept", dept: dept, batch: batch, sid: Math.random() }, function(data) { document.getElementById(selbox).innerHTML = data; },"html");
	}
	
	
	function populatePeriods(batch,selbox) {
		$.post("hrd/misc-data.php", { mod: "populatePeriods", batch: batch, sid: Math.random() }, function(htmlData) { document.getElementById(selbox).innerHTML = htmlData; },"html");
	}
	
	function populateEmployees(batch,selbox) {
		$.post("hrd/misc-data.php", { mod: "populateEmployees", batch: batch, sid: Math.random() }, function(htmlData) { document.getElementById(selbox).innerHTML = htmlData; },"html");
	}
	
	function showPayPeriods() {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/payperiods.php?sid="+Math.random()+"'></iframe>";
		$("#payperiods").html(txtHTML);
		$("#payperiods").dialog({title: "Payroll Cut-offs", width: 960, height: 480, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function showHolidays() {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/holidays.php?sid="+Math.random()+"'></iframe>";
		$("#holidays").html(txtHTML);
		$("#holidays").dialog({title: "National Holidays", width: 960, height: 480, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}
	
	function showLocalHolidays() {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/localholidays.php?sid="+Math.random()+"'></iframe>";
		$("#holidays").html(txtHTML);
		$("#holidays").dialog({title: "Local Holidays", width: 960, height: 480, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function showLeaves() {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/leaves.php'></iframe>";
		$("#leaves").html(txtHTML);
		$("#leaves").dialog({title: "Leaves & Absences",width: xWidth, height: 480, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}


	function showDeductions() {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/deductions.php'></iframe>";
		$("#deductions").html(txtHTML);
		$("#deductions").dialog({title: "Outright Payroll Deductions",width: xWidth, height: 480, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}
	
	function showIncentives() {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/incentives.php'></iframe>";
		$("#deductions").html(txtHTML);
		$("#deductions").dialog({title: "Salary Incentives",width: xWidth, height: 480, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function showLoans() {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/loans.php'></iframe>";
		$("#loans").html(txtHTML);
		$("#loans").dialog({title: "Loans/Long Term Deductions",width: xWidth, height: 480, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function showAdjustments() {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/adjustments.php'></iframe>";
		$("#adjustments").html(txtHTML);
		$("#adjustments").dialog({title: "Salary Adjustments",width: xWidth, height: 480, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}
	
	function showBasic2() {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/basic2.php'></iframe>";
		$("#adjustments").html(txtHTML);
		$("#adjustments").dialog({title: "Basic Salary 2",width: xWidth, height: 480, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function showImport() {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/importdtr.php'></iframe>";
		$("#importdtr").html(txtHTML);
		$("#importdtr").dialog({title: "Import DTR from Biometrics", width: 380, height: 240, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function showEmpDTR() {
		$("#manageDTR").dialog({title: "Manage Daily Time Record", width: 400, modal: true }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}
	
	function showEmpSchedules() {
		$("#plotSchedules").dialog({title: "Plot Employee Schedules", width: 400, modal: true }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}
	
	function plotSchedules() {
		var txtHTML = "<iframe id='frmSchedules' frameborder=0 width='100%' height='100%' src='hrd/employee.schedules.php?batch="+$("#plot_batch").val()+"&dept="+$("#plot_dept").val()+"&period="+$("#plot_cutoff").val()+"&sid="+Math.random()+"'></iframe>";
		$("#empdtr").html(txtHTML);
		$("#empdtr").dialog({title: "Plot Employee Schedules", width: xWidth, height: 660, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
		
	}
	
	function showEmpOT() {
		$("#manageOT").dialog({title: "Manage Employee Overtime", width: 400, modal: true }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function mdtr_pop_emp(type) {
		$.post("hrd/misc-data.php", { mod: "populateEmp", type: type, sid: Math.random() }, function(data) {
			$("#mdtr_emp").html(data);
		},"html");
	}
	
	function lb_pop_emp(dept) {
		if(dept!=""){
			$.post("hrd/misc-data.php", { mod: "populateEmp", dept: dept, sid: Math.random() }, function(data) {
				$("#lb_emp").html(data);
			},"html");
		} else { $("#mdtr_emp").html("<option value=''>- All Employees -</option>"); }
	}

	function getDTR() {
		if($("#mdtr_emp").val() != "" && $("#mdtr_cutoff").val() != "") {
			
			$.post("hrd/misc-data.php", { mod: "getEmpName", eid: $("#mdtr_emp").val(), sid: Math.random() }, function(data) {
				var txtHTML = "<iframe id='frmDTR' frameborder=0 width='100%' height='100%' src='hrd/employee.dtr.php?eid="+$("#mdtr_emp").val()+"&period="+$("#mdtr_cutoff").val()+"&sid="+Math.random()+"'></iframe>";
				$("#empdtr").html(txtHTML);
				$("#empdtr").dialog({title: "Manage Employee Daily Time Record "+data[0]+"", width: xWidth, height: 660, resizable: false }).dialogExtend({
					"closable" : true,
					"maximizable" : false,
					"minimizable" : true
				});
			},"json");
		} else {
			parent.sendErrorMessage("Unable to continue as you may have not selected an employee from the given list or you may have not specify the payroll period you wish to manage...");
		}
	}
	
	function getOT() {
		var txtHTML = "<iframe id='frmDTR' frameborder=0 width='100%' height='100%' src='hrd/employee.overtime.php?period="+$("#mot_cutoff").val()+"&pay_type="+$("#mot_batch").val()+"&sid="+Math.random()+"'></iframe>";
		$("#manageovertime").html(txtHTML);
		$("#manageovertime").dialog({title: "Manage Employee Daily Time Record", width: xWidth, height: 660, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function refreshDTR(eid,period,dept) {
		$.post("hrd/misc-data.php", { mod: "getEmpName", eid: eid, sid: Math.random() }, function(data) {
			var txtHTML = "<iframe id='frmDTR' frameborder=0 width='100%' height='100%' src='hrd/employee.dtr.php?eid="+eid+"&period="+period+"&sid="+Math.random()+"'></iframe>";
			$("#empdtr").html(txtHTML);
			$("#empdtr").dialog({title: "Manage Employee Daily Time Record "+data[0]+"", width: xWidth, height: 660, resizable: false }).dialogExtend({
				"closable" : true,
				"maximizable" : false,
				"minimizable" : true
			});
		},"json");
	}

	function showPrintDTR() {
		$("#printDTR").dialog({title: "Print Daily Time Record", width: 400, modal: true }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function printDTR(tag) {

		if(tag == 1) {
			var txtHTML = "<iframe id='frmDTR' frameborder=0 width='100%' height='100%' src='hrd/reports/dtr.php?period="+$("#pdtr_cutoff").val()+"&dept="+$("#pdtr_dept").val()+"&sid="+Math.random()+"'></iframe>";
			$("#report3").html(txtHTML);
			$("#report3").dialog({title: "Daily Time Record", width: 560, height: 620, resizable: true }).dialogExtend({
				"closable" : true,
				"maximizable" : true,
				"minimizable" : true
			});
		
		} else {
			window.open("hrd/reports/dtr-xls.php?period="+$("#pdtr_cutoff").val()+"&dept="+$("#pdtr_dept").val()+"&ptype="+$("#pdtr_type").val()+"&sid="+Math.random()+"","Daily Time Record (Excel)","location=1,status=1,scrollbars=1,width=640,height=720");
		}
		
	}

	function showPrintTardy() {
		$("#tardyDtf").datepicker(); $("#tardyDt2").datepicker();
		$("#printTardy").dialog({title: "Tardiness Report", width: 400, modal: true }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function printTardy() {
		var txtHTML = "<iframe id='frmDTR' frameborder=0 width='100%' height='100%' src='hrd/reports/tardiness.php?dtf="+$("#tardyDtf").val()+"&dt2="+$("#tardyDt2").val()+"&dept="+$("#tardyDept").val()+"&sid="+Math.random()+"'></iframe>";
		$("#report5").html(txtHTML);
		$("#report5").dialog({title: "Tardiness Report", width: 560, height: 620, resizable: true }).dialogExtend({
			"closable" : true,
			"maximizable" : true,
			"minimizable" : true
		});
	}

	function showPay() {
		if(confirm("Before processing payroll, please ensure that all short and long term deductions were considered, leaves & absences had been encoded and Daily Time Record was already checked and corrected. Do you still wish to continue?") == true) {
			$("#processPay").dialog({title: "Process Payroll", width: 400, modal: true }).dialogExtend({
				"closable" : true,
				"maximizable" : false,
				"minimizable" : true
			});
		}	
	}

	function processPay() {
		if(confirm("Are you sure you want to process payroll and prepare payslip thereafter?") == true) {
			window.open("hrd/processpay.php?cutoff="+$("#payCutoff").val()+"&dept="+$("#payDept").val()+"&sid="+Math.random()+"","Process Payroll","location=1,status=1,scrollbars=1,width=640,height=720");
		}
	}
	
	function showPrintPaySlip() {
		$("#printPaySlip").dialog({title: "Print Payslip", width: 400, modal: true }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function printPaySlip() {
		window.open("hrd/payslip.php?cutoff="+$("#payslipCutoff").val()+"&dept="+$("#payslipDept").val()+"&eid="+$("#payslipEmployee").val()+"&sid="+Math.random()+"","Print Payslip","location=1,status=1,scrollbars=1,width=640,height=720");
	}

	function showPrintPaySummary() {
		$("#paySummary").dialog({title: "Payroll Summary", width: 400, modal: true }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function processPaySummary() {
		window.open("hrd/reports/payrollsummary.php?cutoff="+$("#paySCutoff").val()+"&dept="+$("#paySDept").val()+"&sid="+Math.random()+"","Payroll Register","location=1,status=1,scrollbars=1,width=640,height=720");
	}

	function processPaySummaryExcel() {
		window.open("hrd/reports/payrollsummary-xls.php?cutoff="+$("#paySCutoff").val()+"&dept="+$("#paySDept").val()+"&sid="+Math.random()+"","Payroll Register","location=1,status=1,scrollbars=1,width=640,height=720");
	}
	
	function showBDO() {
		if(confirm("Once Bank transmittal has been created, records pertaining to the period selected are deemed final and shall be locked for future editing unless otherwise overridden by the Finance Manager or any person under authority. Do you still wish to continue?") == true) {
			$("#bdo_creditdate").datepicker();
			$("#bdoTransmittal").dialog({title: "BDO Transmittal Tool", width: 400, modal: true }).dialogExtend({
				"closable" : true,
				"maximizable" : false,
				"minimizable" : true
			});
		}	
	}
	
	function generateTransmittal() {
		window.open("export/bdo_transmittal.php?cutoff="+$("#bdo_cutoff").val()+"&proj="+$("#bdo_proj").val()+"&date="+$("#bdo_creditdate").val()+"&batch="+$("#bdo_batchcode").val()+"&sid="+Math.random()+"","Payroll Register","location=1,status=1,scrollbars=1,width=640,height=720");
	}
	
	function generateTransmittalX() {
		window.open("export/bdo_transmittalx.php?cutoff="+$("#bdo_cutoff").val()+"&proj="+$("#bdo_proj").val()+"&date="+$("#bdo_creditdate").val()+"&batch="+$("#bdo_batchcode").val()+"&sid="+Math.random()+"","Payroll Register","location=1,status=1,scrollbars=1,width=640,height=720");
	}
	
	
	function showOT() {
		$("#otSummary").dialog({title: "Summary of Approved Overtime", width: 400, modal: true }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function processOT() {
		
		//window.open("hrd/reports/otsummary.php?ptype="+$("#otType").val()+"&cutoff="+$("#otCutoff").val()+"&sid="+Math.random()+"","Overtime Summary","location=1,status=1,scrollbars=1,width=640,height=720");
	
		var txtHTML = "<iframe id='frmDTR' frameborder=0 width='100%' height='100%' src='hrd/reports/otsummary.php?cutoff="+$("#otCutoff").val()+"&dept="+$("#otDept").val()+"&sid="+Math.random()+"'></iframe>";
		$("#report2").html(txtHTML);
		$("#report2").dialog({title: "Overtime Summary", width: 560, height: 620, resizable: true }).dialogExtend({
			"closable" : true,
			"maximizable" : true,
			"minimizable" : true
		});
	}
	
	function showEmpLoanBalance() {
		$("#loanBalances").dialog({title: "Employee Loan Balances", width: 400, modal: true }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}
	
	function getEmpLoans() {
		window.open("hrd/reports/loanbalance-html.php?id_no="+$("#lb_emp").val()+"&sid="+Math.random()+"","Employee Loan Balances","location=1,status=1,scrollbars=1,width=640,height=720");
	}
	
	function showStatutory() {
		$("#printStatutory").dialog({title: "Summary of Statutory Deductions", width: 400, modal: true }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function printStatutory() {
		if($("#statYear").val() == "" && isNaN($("#statYear").val())==true) {
			parent.sendErrorMessage("- You have specified an invalid Year format!");
		} else {
			window.open("hrd/reports/statutory.php?year="+$("#statYear").val()+"&month="+$("#statMonth").val()+"&proj="+$("#statProj").val()+"&sid="+Math.random()+"","Summary of Statutory Deductions","location=1,status=1,scrollbars=1,width=640,height=720");
		}
	}
	
	function exportStatutory() {
		if($("#statYear").val() == "" && isNaN($("#statYear").val())==true) {
			parent.sendErrorMessage("- You have specified an invalid Year format!");
		} else {
			window.open("hrd/reports/statutory-xls.php?year="+$("#statYear").val()+"&month="+$("#statMonth").val()+"&proj="+$("#statProj").val()+"&sid="+Math.random()+"","Summary of Statutory Deductions","location=1,status=1,scrollbars=1,width=640,height=720");
		}
	}
	
	function showGrossCompensation() {
		$("#printGrossCompensation").dialog({title: "Employee Gross Compensation Report", width: 400, modal: true }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}

	function printGrossCompensation() {
		if($("#grossYear").val() == "" && isNaN($("#grossYear").val())==true) {
			parent.sendErrorMessage("- You have specified an invalid Year format!");
		} else {
			window.open("hrd/reports/gross-compensation.php?year="+$("#grossYear").val()+"&emp_type="+$("#grossType").val()+"&sid="+Math.random()+"","Gross Compensation Report","location=1,status=1,scrollbars=1,width=640,height=720");
		}
	}
	
	function showThirteenth() {

		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='hrd/thirteenth.upload.php'></iframe>";
		$("#empfam").html(txtHTML);
		$("#empfam").dialog({title: "Upload Thirteenth Month File", width: 400, height: 200 }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
	}
	
	function print13() {
		window.open("hrd/reports/thirteenth.slip.php?year="+$("#13_year").val()+"&area="+$("#13_area").val()+"&sid="+Math.random()+"","Thirteenth Month Pay","location=1,status=1,scrollbars=1,width=640,height=720");
	}
	
	function printThirteenth() {

		$("#printThirteenth").dialog({title: "Print Thirteenth Month Payslip", width: 400 }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});

	}
	
	/* 201 Addition */
	function showFam(eid) {
		$.post("payroll.datacontrol.php", { mod: "getEmpName", record_id: eid, sid: Math.random() }, function(data) {
			var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='employee.fbackground.php?eid="+eid+"'></iframe>";
			$("#empfam").html(txtHTML);
			$("#empfam").dialog({title: "Employee Family Background ("+data+")", width: xWidth, height: 520 }).dialogExtend({
				"closable" : true,
				"maximizable" : false,
				"minimizable" : true
			});
		});
	}

	function showEdu(eid) {
		$.post("payroll.datacontrol.php", { mod: "getEmpName", record_id: eid, sid: Math.random() }, function(data) {
			var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='employee.edubackground.php?eid="+eid+"'></iframe>";
			$("#empedu").html(txtHTML);
			$("#empedu").dialog({title: "Employee Educational Background ("+data+")", width: xWidth, height: 520 }).dialogExtend({
				"closable" : true,
				"maximizable" : false,
				"minimizable" : true
			});
		});
	}

	function showErecord(eid) {
		$.post("payroll.datacontrol.php", { mod: "getEmpName", record_id: eid, sid: Math.random() }, function(data) {
			var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='employee.experience.php?eid="+eid+"'></iframe>";
			$("#empexp").html(txtHTML);
			$("#empexp").dialog({title: "External Work Experience ("+data+")", width: xWidth, height: 520 }).dialogExtend({
				"closable" : true,
				"maximizable" : false,
				"minimizable" : true
			});
		});
	}

	function showErecord2(eid) {
		$.post("payroll.datacontrol.php", { mod: "getEmpName", record_id: eid, sid: Math.random() }, function(data) {
			var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='employee.experience2.php?eid="+eid+"'></iframe>";
			$("#empexpinternal").html(txtHTML);
			$("#empexpinternal").dialog({title: "Internal Work Experience ("+data+")", width: xWidth, height: 480 }).dialogExtend({
				"closable" : true,
				"maximizable" : false,
				"minimizable" : true
			});
		});
	}
	
	function showCert(eid) {
		$.post("payroll.datacontrol.php", { mod: "getEmpName", record_id: eid, sid: Math.random() }, function(data) {
			var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='employee.certificates.php?eid="+eid+"'></iframe>";
			$("#empcert").html(txtHTML);
			$("#empcert").dialog({title: "Memos, Certificates & Clearances ("+data+")", width: xWidth, height: 520 }).dialogExtend({
				"closable" : true,
				"maximizable" : false,
				"minimizable" : true
			});
		});
	}
	
	function uploadBio() {
		var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='e_uploaddtr.php'></iframe>";
		$("#changepass").html(txtHTML);
		$("#changepass").dialog({title: "Upload Data From Logbox", width: 480, height: 210, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true,
		});
	}

	/* Xray & Radiology */
function showImagingQueue() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='imaging.list.php?sid="+Math.random()+"'></iframe>";
	$("#itemlist").html(txtHTML);
	$("#itemlist").dialog({title: "Imaging Queueing List", width: xWidth, height: 500,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function validateECGResult(lid,code) {
	$("#ecgResult").html("<iframe id='frmECGResult' frameborder=0 width='100%' height='100%' src='result.ecg.php?lid="+lid+"'></iframe>");
	$("#ecgResult").dialog({
		title: "Write Result",
		width: 1200,
		height: 695,
		resizeable: false,
		modal: false
	});
}

function showImgSamples() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='imgsamples.list.php?sid="+Math.random()+"'></iframe>";
	$("#polist").html(txtHTML);
	$("#polist").dialog({title: "Manage Imaging Samples", width: xWidth, height: 500,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function manageImgResults() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='imgvalidation.php?sid="+Math.random()+"'></iframe>";
	$("#srrlist").html(txtHTML);
	$("#srrlist").dialog({title: "Validate Imaging Results", width: xWidth, height: 500,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showXrayTemplates() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='xray.templates.php?sid="+Math.random()+"'></iframe>";
	$("#solist").html(txtHTML);
	$("#solist").dialog({title: "X-Ray - Ultrasound Result Templates", width: xWidth, height: 500,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function xrayTemplateDetails(id) {
	$("#report3").html("<iframe id='xrayTemplate' frameborder=0 width='100%' height='100%' src='xray.templatedetails.php?id="+id+"&sid="+Math.random()+"'></iframe>");
	var dis = $("#report3").dialog({
		title: "X-Ray - Ultrasound Result Templates",
		width: 1024,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Changes Made",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#xrayTemplate').contents().find('#frmXrayTemplate').serialize();
					
						//var dataString = $("#frmDescResult").serialize();
						dataString = "mod=saveXrayTemplate&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Template Successfully Saved!");
								dis.dialog("close");
								showXrayTemplates();
								$("#frmXrayTemplate").trigger("reset");
							}
						});
					}
				}
			},
			{
				text: "Mark Template as Inactive",
				icons: { primary: "ui-icon-cancel" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want to mark this template as inactive?") == true) {
						var dataString = $('#xrayTemplate').contents().find('#frmXrayTemplate').serialize();
					
						//var dataString = $("#frmDescResult").serialize();
						dataString = "mod=cancelXrayTemplate&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Template Successfully mark as Inactive!");
								dis.dialog("close");
								showXrayTemplates();
								$("#frmXrayTemplate").trigger("reset");
							}
						});
					}
				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function printDescriptiveResult(so_no,code,serialno) {

	var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.xray.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
	$("#report5").html(txtHTML);
	$("#report5").dialog({title: "Print - XRAY RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});

}

function printECGResult(so_no,code,serialno) {

	var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.ecg.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
	$("#report5").html(txtHTML);
	$("#report5").dialog({title: "Print - ECG RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});

}

function showXrayLogbook() {
	var dis = $("#xrayLogBook").dialog({
		title: "Xray Results Logbook", 
		width: 480,
		resizable: false, 
		buttons: [
			{
				icons: { primary: "ui-icon-print" },
				text: "Generate Logbook",
				click: function() { 
					var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='reports/xray.logbook.php?dtf="+$("#xraylog_dtf").val()+"&dt2="+$("#xraylog_dt2").val()+"&consultant="+$("#xraylog_consultant").val()+"&type="+$("#xraylog_type").val()+"&encode="+$("#xraylog_encoder").val()+"&xraylog_sort="+$("#xraylog_sort").val()+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report5").html(txtHTML);
					$("#report5").dialog({title: "Xray Logbook", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});
				}
			},
			{
				icons: { primary: "ui-icon-closethick" },
				text: "Close Window",
				click: function() { 
					dis.dialog("close");
				}
			}
		]
	});
}


	/**** Lab Functions */
	
function showLabCollection() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='phleb.list.php?sid="+Math.random()+"'></iframe>";
	$("#itemlist").html(txtHTML);
	$("#itemlist").dialog({title: "Phleb Queueing List", width: xWidth, height: 500,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showServiceInfo(id) {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='service.details.php?id="+id+"&mod=1&sid="+Math.random()+"'></iframe>";
	$("#itemdetails").html(txtHTML);
	$("#itemdetails").dialog({title: "Service Details", width: 1120, height: 520, resizable: false }).dialogExtend({
		"closable" : true,
		"maximizable" : false,
		"minimizable" : true
	});
}

function printBarcode(serialno) {
	$.post("src/sjerp.php", { mod: "checkSerialStatus", serialno: serialno, sid: Math.random() }, function(result) {
		if(parseFloat(result['mycount']) > 0) {
			
			var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='print/specimenbarcode.php?id="+serialno+"&sid="+Math.random()+"'></iframe>";
			$("#barcode").html(txtHTML);
			$("#barcode").dialog({title: "Print - Barcode", width: 400, height: 200, resizable: false }).dialogExtend({
				"closable" : true,
				"maximizable" : false,
				"minimizable" : true
			});
		
		} else {
			
			sendErrorMessage("It appears that this specimen record hasn't been saved yet.. Please click save and try to print the barcode again.");

		}
	},"json");
}

function showResults() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='results.list.php?sid="+Math.random()+"'></iframe>";
	$("#polist").html(txtHTML);
	$("#polist").dialog({title: "Results & Releasing", width: xWidth, height: 500,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showSamples() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='samples.list.php?sid="+Math.random()+"'></iframe>";
	$("#srrlist").html(txtHTML);
	$("#srrlist").dialog({title: "Manage Lab Samples", width: xWidth, height: 500,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function showValidation() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='validation.php?sid="+Math.random()+"'></iframe>";
	$("#srrlist").html(txtHTML);
	$("#srrlist").dialog({title: "Validate Lab Results", width: xWidth, height: 500,resizable: false }).dialogExtend({
		"closable" : true,
	    "maximizable" : true,
	    "minimizable" : true
	});
}

function writeResult(lid,code) {
	switch(code) {
		case "L007":
		case "L071":
		case "L086":
		case "L096":	
		case "L099":
		case "L100":
			enumResult(lid,code);
		break;
		case "L999":
		case "L016":
		case "L018":
		case "L004":
		case "L022":
		case "L023":
		case "L021":
		case "L052":
		case "L009":
		case "L006":
		case "L206":
		case "L207":
		case "L208":
		case "L209":
		case "L210":
		case "L211":
		case "L212":
		case "L213":
		case "L214":
		case "L215":
		case "L216":
		case "L032":
		case "L033":
		case "L061":
		case "L062":
		case "L203":
		case "L196":
		case "L020":
		case "L025":
		case "L026":
		case "L119":
		case "L252":
		case "L255":
		case "L030":
			bloodChem(lid,code);
		break;
		// case "L019":
		// 	spchem(lid,code);
		// break;
		case "O117":
			audioResult(lid,code);
		break;
		case "L010":
		case "L135":
			cbcResult(lid,code);
		break;
		case "L015":
		case "L088":
			antibodyResult(lid,code);
		break;
		case "L087":
			antigenResult(lid,code);
		break;
		// case "L062":
		// case "L032":
		// 	ft4Result(lid,code);
		// break;
		case "L031":
			tshResult(lid,code);
		break;
		case "L012":
			uaResult(lid,code);
		break;
		case "L036":
		case "L114":
		case "L115":
		case "L116":
			stoolExam(lid,code);
		break;
		case "L039":
			ctbtResult(lid,code);
		break;
		case "L014":
			semenAnalysis(lid,code);
		break;
		case "L041":
		case "L050":
			hepaResult(lid,code);
		break;
		case "L042":
		case "L045":
		case "L043":
		case "L037":
		case "L051":
		case "L177":
		case "L223":
		case "L095":
			pregnancyResult(lid,code);
		break;
		// case "L052":
		// 	lipidResult(lid,code);
		// break;
		case "L040":
			bloodTyping(lid,code);
		break;
		case "L066":
			syphilisResult(lid,code);
		break;
		case "L064":
			dengueResult(lid,code);
		break;
		case "L075":
			ogttResult(lid,code);
		break;
		case "L044":
			hivResult(lid,code);
		break;
		case "L113":
			occultBlood(lid,code);
		break;
		// case "L196":
		// 	electrolytes(lid,code);
		// break;
		case "L121":
			eGRFR(lid,code);
		break;
		default:
			singleValueResult(lid,code);
		break;
	}
}

function validateResult(lid,code) {
	switch(code) {
		case "L007":
		case "L071":
		case "L086":
		case "L096":	
		case "L099":
		case "L100":
			validateEnumResult(lid,code);
		break;
		case "L999":
		case "L016":
		case "L018":
		case "L004":
		case "L022":
		case "L023":
		case "L021":
		case "L052":
		case "L009":
		case "L006":
		case "L206":
		case "L207":
		case "L208":
		case "L209":
		case "L210":
		case "L211":
		case "L212":
		case "L213":
		case "L214":
		case "L215":
		case "L216":
		case "L032":
		case "L033":
		case "L061":
		case "L062":
		case "L203":
		case "L196":
		case "L020":
		case "L025":
		case "L026":
		case "L119":
		case "L252":
		case "L255":
		case "L030":
			validateBloodChem(lid,code);
		break;
		// case "L019":
		// 	validateSPChem(lid,code);
		// break;
		case "O117":
			validateAudioResult(lid,code);
		break;
		case "L010":
		case "L135":
			validateCbcResult(lid,code);
		break;
		// case "L032":
		// case "L062":
		// 	validateFt4Result(lid,code);
		// break;
		case "L031":
			validateTshResult(lid,code);
		break;
		case "L012":
			validateUaResult(lid,code);
		break;
		case "L036":
		case "L114":
		case "L115":
		case "L116":
			validateStoolExam(lid,code);
		break;
		case "L039":
			validateCtbtResult(lid,code);
		break;
		case "L014":
			validateSemenAnalysis(lid,code);
		break;
		case "L015":
		case "L088":
			validateAntibodyResult(lid,code);
		break;
		case "L087":
			validateAntigenResult(lid,code);
		break;
		case "L041":
		case "L050":
			validateHepaResult(lid,code);
		break;
		case "L042":
		case "L045":
		case "L043":
		case "L051":
		case "L037":
		case "L177":
		case "L223":
		case "L095":
			validatePregnancyResult(lid,code);
		break;
		// case "L052":
		// 	validateLipidResult(lid,code);
		// break;
		case "L040":
			validateBloodtype(lid,code);
		break;
		case "L066":
			validateSyphilisResult(lid,code);
		break;
		case "L064":
			validateDengueResult(lid,code);
		break;
		case "L075":
			validateOgttResult(lid,code);
		break;
		case "L044":
			validateHivResult(lid,code);
		break;
		case "X017":
			validateECGResult(lid,code);
		break;
		case "L113":
			validateOccultBlood(lid,code);
		break;
		// case "L196":
		// 	validateElectrolytes(lid,code);
		// break;
		case "L121":
			validateEGRFR(lid,code);
		break;
		default:
			validateSingleValueResult(lid,code);
		break;
	}
}

function printResult(code,so_no,serialno,lid) {
	let xCode = code.substring(0,1);
	if(xCode == 'X' || xCode == 'U') {
		if(code == 'X017') {
			var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.ecg.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
		}else{
			var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.xray.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
		}
	} else {
		switch(code) {

			case "L010":
			case "L135":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.cbc.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L999":
			case "L016":
			case "L018":
			case "L004":
			case "L022":
			case "L023":
			case "L021":
			case "L052":
			case "L009":
			case "L006":
			case "L206":
			case "L207":
			case "L208":
			case "L209":
			case "L210":
			case "L211":
			case "L212":
			case "L213":
			case "L214":
			case "L215":
			case "L216":
			case "L032":
			case "L033":
			case "L061":
			case "L062":
			case "L025":
			case "L203":
			case "L196":
			case "L020":
			case "L026":
			case "L119":
			case "L252":
			case "L255":
			case "L030":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.bloodchem.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			// case "L019":
			// 	var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.spchem.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			// break;
			case "O117":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.audiometry.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L031":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.tsh.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L012":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.ua.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L036":
			case "L114":
			case "L115":
			case "L116":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.stool.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L014":
				semenAnalysis(lid,code);
			break;
			case "L007":
			case "L071":
			case "L086":
			case "L100":
			case "L132":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.enum.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L015":
			case "L088":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.antibody.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L087":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.antigen.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L041":
			case "L050":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.hepa.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L042":
			case "L045":
			case "L043":
			case "L051":
			case "L037":
			case "L177":
			case "L223":
			case "L095":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.pt.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			// case "L052":
			// 	var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.lipidpanel.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			// break;
			case "L040":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.bt.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L066":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.syphilis.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L064":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.dengue.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L075":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.ogtt.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L044":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.hiv.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L047":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.dt.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L039":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.ctbt.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L076":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.papsmear.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			case "L113":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.occultblood.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			// case "L196":
			// 	var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.electrolytes.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			// break;
			// case "L032":
			// case "L062":
			// 	var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.ft4.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			// break;
			case "L121":
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.egfr.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
			default:
				var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='print/result.singlevalue.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
			break;
		}
	}
	
	$("#report3").html(txtHTML);
	$("#report3").dialog({title: "Print Result", width: 750, height: 760, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});

}

function viewAttachment(code,so_no,serialno,lid) {
	$.post("src/sjerp.php",{ mod: "getFilePath", lid: lid, sid: Math.random() }, function(filePath) {
		if(filePath == '') {
			// sendErrorMessage("No File Attachment Found!");
		} else {
			var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='"+filePath+"'></iframe>";
			$("#report3").html(txtHTML);
			$("#report3").dialog({title: "Print Result", width: 750, height: 720, resizable: true }).dialogExtend({
				"closable" : true,
				"maximizable" : true,
				"minimizable" : true
			});
		}
	},"html");

}

function viewXrayAttachment(code,so_no,serialno,lid) {
	$.post("src/sjerp.php",{ mod: "getFilePath", lid: lid, code: code, so_no: so_no, serialno: serialno, sid: Math.random() }, function(filePath) {
		if(filePath == '') {
			// sendErrorMessage("No File Attachment Found!");
		} else {
			var txtHTML = "<iframe id='printResult' frameborder=0 width='100%' height='100%' src='"+filePath+"'></iframe>";
			$("#report3").html(txtHTML);
			$("#report3").dialog({title: "X-Ray Attachment", width: 750, height: 720, resizable: true }).dialogExtend({
				"closable" : true,
				"maximizable" : true,
				"minimizable" : true
			});
		}
	},"html");

}

function writeImagingResult(lid,code) {
	$("#descResult").html("<iframe id='frmResult' frameborder=0 width='100%' height='100%' src='result.descriptive.php?lid="+lid+"'></iframe>");
	$("#descResult").dialog({
		title: "Write Result",
		width: 1024,
		height: 695,
		resizeable: false,
		modal: false
	});
}

function audioResult(lid,code) {

	$("#audiometryResult").html("<iframe id='frmAudio' frameborder=0 width='100%' height='100%' src='result.audiometry.php?lid="+lid+"'></iframe>");
	
	var dis = $("#audiometryResult").dialog({
		title: "Write Result",
		width: 1024,
		height: 690,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Result Pending Validation",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var msg = '';

						if(msg != '') {
							parent.sendErrorMessage(msg);
						} else {
							var dataString = $('#frmAudio').contents().find('#frmAudio').serialize();
							dataString = "mod=saveAudioResult&" + dataString;
							$.ajax({
								type: "POST",
								url: "src/sjerp.php",
								data: dataString,
								success: function() {
									alert("Result Successfully Saved!");
									dis.dialog("close");
									$("#frmAudio").trigger("reset");
								}
							});
						}
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmAudio').contents().find('#audio_sono').val();
					var serialno = $('#frmAudio').contents().find('#audio_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.audiometry.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - AUDIOMETRY RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function validateAudioResult(lid,code) {

	$("#audiometryResult").html("<iframe id='frmAudio' frameborder=0 width='100%' height='100%' src='result.audiometry.php?lid="+lid+"'></iframe>");
	
	var dis = $("#audiometryResult").dialog({
		title: "Validate Result",
		width: 1024,
		height: 690,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Changes & Mark Result as Validated",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmAudio').contents().find('#frmAudio').serialize();
					
						dataString = "mod=validateAudioResult&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Marked as Validated!");
								showValidation();
								dis.dialog("close");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmAudio').contents().find('#audio_sono').val();
					var serialno = $('#frmAudio').contents().find('#audio_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.audiometry.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - AUDIOMETRY RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function hepaResult(lid,code) {

	$("#hepaResult").html("<iframe id='frmHepaResult' frameborder=0 width='100%' height='100%' src='result.hepa.php?lid="+lid+"'></iframe>");

	$("#hepaResult").dialog({
		title: "Write Result",
		width: 1200,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Result Pending Validation",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmHepaResult').contents().find('#frmHepaResult').serialize();
						dataString = "mod=saveHepaResult&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Saved!");
								$("#frmHepaResult").trigger("reset");
								dis.dialog("close");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmHepaResult').contents().find('#hepa_sono').val();
					var serialno = $('#frmHepaResult').contents().find('#hepa_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.hepa.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - HAV RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


					}
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});

}

function validateHepaResult(lid,code) {
	
$("#hepaResult").html("<iframe id='frmHepaResult' frameborder=0 width='100%' height='100%' src='result.hepa.php?lid="+lid+"'></iframe>");
	
 $("#hepaResult").dialog({
		title: "Write Result",
		width: 1200,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Changes & Mark Result as Validated",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmHepaResult').contents().find('#frmHepaResult').serialize();
						dataString = "mod=validateHepaResult&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Marked as Validated!");
								showValidation();
								dis.dialog("close");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmHepaResult').contents().find('#hepa_sono').val();
					var serialno = $('#frmHepaResult').contents().find('#hepa_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.hepa.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - HAV RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function eGRFR(lid,code) {
	
	$("#eGFR").html("<iframe id='frmEGFR' frameborder=0 width='100%' height='100%' src='result.egfr.php?lid="+lid+"'></iframe>");
	
	$("#eGFR").dialog({
		title: "Write Result",
		width: 1200,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Result Pending Validation",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmEGFR').contents().find('#frmEGFR').serialize();
						dataString = "mod=saveEGFR&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Saved!");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmEGFR').contents().find('#egfr_sono').val();
					var serialno = $('#frmEGFR').contents().find('#egfr_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.egfr.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - ESTIMATED eGFR RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function validateEGRFR(lid,code) {
	
	$("#eGFR").html("<iframe id='frmEGFR' frameborder=0 width='100%' height='100%' src='result.egfr.php?lid="+lid+"'></iframe>");
	
	var dis = $("#eGFR").dialog({
		title: "Write Result",
		width: 1200,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Changes & Mark Result as Validated",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmEGFR').contents().find('#frmEGFR').serialize();
						dataString = "mod=validateEGFR&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Marked as Validated!");
								showValidation();
								dis.dialog("close");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmEGFR').contents().find('#egfr_sono').val();
					var serialno = $('#frmEGFR').contents().find('#egfr_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.egfr.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - ESTIMATED eGFR RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function tshResult(lid,code) {
	
	$("#tshResult").html("<iframe id='frmTshResult' frameborder=0 width='100%' height='100%' src='result.tsh.php?lid="+lid+"'></iframe>");
	
	$("#tshResult").dialog({
		title: "Write Result",
		width: 1200,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Result Pending Validation",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmTshResult').contents().find('#frmTshResult').serialize();
						dataString = "mod=saveTshResult&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Saved!");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmTshResult').contents().find('#tsh_sono').val();
					var serialno = $('#frmTshResult').contents().find('#tsh_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.tsh.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - TSH RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function validateTshResult(lid,code) {
	
	$("#tshResult").html("<iframe id='frmTshResult' frameborder=0 width='100%' height='100%' src='result.tsh.php?lid="+lid+"'></iframe>");
	
	var dis = $("#tshResult").dialog({
		title: "Write Result",
		width: 1200,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Changes & Mark Result as Validated",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmTshResult').contents().find('#frmTshResult').serialize();
						dataString = "mod=validateTshResult&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Marked as Validated!");
								showValidation();
								dis.dialog("close");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmTshResult').contents().find('#tsh_sono').val();
					var serialno = $('#frmTshResult').contents().find('#tsh_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.tsh.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - TSH RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function singleValueResult(lid,code) {

	$("#singleResults").html("<iframe id='frmSingleValue' frameborder=0 width='100%' height='100%' src='result.single.php?lid="+lid+"'></iframe>");

		var dis = $("#singleResults").dialog({
			title: "Write Result",
			width: 1224,
			height: 660,
			resizeable: false,
			modal: true,
			buttons: [
				{
					text: "Save Result Pending Validation",
					icons: { primary: "ui-icon-check" },
					click: function() {
					
						if(confirm("Are you sure you want save this data?") == true) {
							var dataString = $('#frmSingleValue').contents().find("#frmSingleValue").serialize();
								dataString = "mod=saveSingleValueResult&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
									alert("Result Successfully Saved!");
								}
							});
							
						}

					}
				},
				{
					text: "Print Result",
					icons: { primary: "ui-icon-print" },
					click: function() {
						
						var so_no = $("#frmSingleValue").contents().find('#sresult_sono').val();
						var code = $("#frmSingleValue").contents().find('#sresult_code').val();
						var serialno = $("#frmSingleValue").contents().find('#sresult_serialno').val();

						var txtHTML = "<iframe id='printSingleValue' frameborder=0 width='100%' height='100%' src='print/result.singlevalue.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
						$("#report5").html(txtHTML);
						$("#report5").dialog({title: "Result - "+ $("#sresult_procedure").val() +"", width: 560, height: 620, resizable: true }).dialogExtend({
							"closable" : true,
							"maximizable" : true,
							"minimizable" : true
						});

						}
				},
				{
					text: "Close",
					icons: { primary: "ui-icon-closethick" },
					click: function() { $(this).dialog("close"); }
				}
			]
		});

}


function validateSingleValueResult(lid,code) {

	$("#singleResults").html("<iframe id='frmSingleValue' frameborder=0 width='100%' height='100%' src='result.single.php?lid="+lid+"'></iframe>");

	var dis = $("#singleResults").dialog({
		title: "Validate Result",
		width: 1224,
		height: 660,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Changes & Mark as Validated",
				icons: { primary: "ui-icon-check" },
				click: function() {
				var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmSingleValue').contents().find("#frmSingleValue").serialize();
						dataString = "mod=validateSingleValueResult&" + dataString;
							$.ajax({
								type: "POST",
								url: "src/sjerp.php",
								data: dataString,
								success: function() {
								alert("Result Successfully Marked as Validated!");
							}
						});
					}
					
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $("#frmSingleValue").contents().find('#sresult_sono').val();
					var code = $("#frmSingleValue").contents().find('#sresult_code').val();
					var serialno = $("#frmSingleValue").contents().find('#sresult_serialno').val();

					var txtHTML = "<iframe id='printSingleValue' frameborder=0 width='100%' height='100%' src='print/result.singlevalue.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report5").html(txtHTML);
					$("#report5").dialog({title: "Result - "+ $("#sresult_procedure").val() +"", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});

					}
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); $("#frmsingleValue").trigger("reset"); }
			}
		]
	});
}

// FT4 Result
function ft4Result(lid,code) {
	
	$("#ft4Result").html("<iframe id='frmFt4Result' frameborder=0 width='100%' height='100%' src='result.ft4.php?lid="+lid+"'></iframe>");
	
	$("#ft4Result").dialog({
		title: "Write Result",
		width: 1200,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Result Pending Validation",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmFt4Result').contents().find('#frmFt4Result').serialize();
						dataString = "mod=saveFT4Result&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Saved!");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmFt4Result').contents().find('#ft4_sono').val();
					var serialno = $('#frmFt4Result').contents().find('#ft4_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.ft4.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - FT4 RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function validateFt4Result(lid,code) {
	
	$("#ft4Result").html("<iframe id='frmFt4Result' frameborder=0 width='100%' height='100%' src='result.ft4.php?lid="+lid+"'></iframe>");
	
	var dis = $("#ft4Result").dialog({
		title: "Write Result",
		width: 1200,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Changes & Mark Result as Validated",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmFt4Result').contents().find('#frmFt4Result').serialize();
						dataString = "mod=validateFT4Result&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Marked as Validated!");
								showValidation();
								dis.dialog("close");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmFt4Result').contents().find('#ft4_sono').val();
					var serialno = $('#frmFt4Result').contents().find('#ft4_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.ft4.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - FT4 RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}


function ctbtResult(lid,code) {

	$("#ctbt_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "ctbtResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#ctbt_sono").val(data['myso']);
			$("#ctbt_sodate").val(data['sodate']);
			$("#ctbt_pid").val(data['mypid']);
			$("#ctbt_pname").val(data['pname']);
			$("#ctbt_gender").val(data['gender']);
			$("#ctbt_birthdate").val(data['bday']);
			$("#ctbt_age").val(data['age']);
			$("#ctbt_patientstat").val(data['patientstatus']);
			$("#ctbt_physician").val(data['physician']);
			$("#ctbt_procedure").val(data['procedure']);
			$("#ctbt_code").val(data['code']);
			$("#ctbt_spectype").val(data['sampletype']);
			$("#ctbt_serialno").val(data['serialno']);
			$("#ctbt_testkit").val(data['testkit']);
			$("#ctbt_testkit_lotno").val(data['lotno']);
			$("#ctbt_testkit_expiry").val(data['expiry']);
			$("#ctbt_extractdate").val(data['exday']);
			$("#ctbt_extracttime").val(data['etime']);
			$("#ctbt_extractby").val(data['extractby']);
			$("#ctbt_ct_min").val(data['ct_min']);
			$("#ctbt_ct_sec").val(data['ct_sec']);
			$("#ctbt_bt_min").val(data['bt_min']);
			$("#ctbt_bt_sec").val(data['bt_sec']);
			$("#ctbt_result_by").val(data['performed_by']);
			$("#ctbt_remarks").val(data['remarks']);

			var dis = $("#ctbtResult").dialog({
				title: "Write CTBT Result",
				width: 1040,
				height: 690,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
						
								if(confirm("Are you sure you want save this data?") == true) {
									var dataString = $("#frmCtbtResult").serialize();
										dataString = "mod=saveCtbtResult&" + dataString;
										$.ajax({
											type: "POST",
											url: "src/sjerp.php",
											data: dataString,
											success: function() {
												alert("Result Successfully Saved!");
												dis.dialog("close");
												$("#frmCtbtResult").trigger("reset");
										}
									});
								}
							}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); $("#frmCtbtResult").trigger("reset"); }
					}
				]
			});

		},"json"
	);
}

function validateCtbtResult(lid,code) {

	$("#ctbt_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "ctbtResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#ctbt_sono").val(data['myso']);
			$("#ctbt_sodate").val(data['sodate']);
			$("#ctbt_pid").val(data['mypid']);
			$("#ctbt_pname").val(data['pname']);
			$("#ctbt_gender").val(data['gender']);
			$("#ctbt_birthdate").val(data['bday']);
			$("#ctbt_age").val(data['age']);
			$("#ctbt_patientstat").val(data['patientstatus']);
			$("#ctbt_physician").val(data['physician']);
			$("#ctbt_procedure").val(data['procedure']);
			$("#ctbt_code").val(data['code']);
			$("#ctbt_spectype").val(data['sampletype']);
			$("#ctbt_serialno").val(data['serialno']);
			$("#ctbt_testkit").val(data['testkit']);
			$("#ctbt_testkit_lotno").val(data['lotno']);
			$("#ctbt_testkit_expiry").val(data['expiry']);
			$("#ctbt_extractdate").val(data['exday']);
			$("#ctbt_extracttime").val(data['etime']);
			$("#ctbt_extractby").val(data['extractby']);
			$("#ctbt_ct_min").val(data['ct_min']);
			$("#ctbt_ct_sec").val(data['ct_sec']);
			$("#ctbt_bt_min").val(data['bt_min']);
			$("#ctbt_bt_sec").val(data['bt_sec']);
			$("#ctbt_result_by").val(data['performed_by']);
			$("#ctbt_remarks").val(data['remarks']);

			var dis = $("#ctbtResult").dialog({
				title: "Validate CTBT Result",
				width: 1040,
				height: 690,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Changes & Mark as Validated",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want confirm and validate this result?") == true) {
							var dataString = $("#frmCtbtResult").serialize();
								dataString = "mod=validateCtbtResult&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Validated!");
										$("#frmCtbtResult").trigger("reset");
										dis.dialog("close");
										showValidation();
				
									}
								});
							}
						}
					},
					{
						text: "Print Result",
						icons: { primary: "ui-icon-print" },
						click: function() {
							
							var so_no = $("#ctbt_sono").val();
							var serialno = $("#ctbt_serialno").val();
							var code = $("#ctbt_code").val();
							
							var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.ctbt.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
							$("#report10").html(txtHTML);
							$("#report10").dialog({title: "Print - CTBT Result", width: 560, height: 620, resizable: true }).dialogExtend({
								"closable" : true,
								"maximizable" : true,
								"minimizable" : true
							});
		
		
						 }
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function electrolytes(lid,code) {

	$("#electro_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "electrolytes",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#electro_sono").val(data['myso']);
			$("#electro_sodate").val(data['sodate']);
			$("#electro_pid").val(data['mypid']);
			$("#electro_pname").val(data['pname']);
			$("#electro_gender").val(data['gender']);
			$("#electro_birthdate").val(data['bday']);
			$("#electro_age").val(data['age']);
			$("#electro_patientstat").val(data['patientstatus']);
			$("#electro_physician").val(data['physician']);
			$("#electro_procedure").val(data['procedure']);
			$("#electro_code").val(data['code']);
			$("#electro_spectype").val(data['sampletype']);
			$("#electro_serialno").val(data['serialno']);
			$("#electro_extractdate").val(data['exday']);
			$("#electro_extracttime").val(data['etime']);
			$("#electro_extractby").val(data['extractby']);
			$("#electro_sodium").val(data['sodium']);
			$("#electro_potassium").val(data['potassium']);
			$("#electro_chloride").val(data['chloride']);
			$("#electro_total_calcium").val(data['total_calcium']);
			$("#electro_remarks").val(data['remarks']);

			var dis = $("#electrolytesResult").dialog({
				title: "Write Result",
				width: 1024,
				height: 680,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
						
								if(confirm("Are you sure you want save this data?") == true) {
									var dataString = $("#frmElectroResult").serialize();
										dataString = "mod=saveElectrolytes&" + dataString;
										$.ajax({
											type: "POST",
											url: "src/sjerp.php",
											data: dataString,
											success: function() {
												alert("Result Successfully Saved!");
												dis.dialog("close");
												$("#frmElectroResult").trigger("reset");
										}
									});
								}
							}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); $("#frmElectroResult").trigger("reset"); }
					}
				]
			});

		},"json"
	);
}

function validateElectrolytes(lid,code) {

	$("#electro_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "electrolytes",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#electro_sono").val(data['myso']);
			$("#electro_sodate").val(data['sodate']);
			$("#electro_pid").val(data['mypid']);
			$("#electro_pname").val(data['pname']);
			$("#electro_gender").val(data['gender']);
			$("#electro_birthdate").val(data['bday']);
			$("#electro_age").val(data['age']);
			$("#electro_patientstat").val(data['patientstatus']);
			$("#electro_physician").val(data['physician']);
			$("#electro_procedure").val(data['procedure']);
			$("#electro_code").val(data['code']);
			$("#electro_spectype").val(data['sampletype']);
			$("#electro_serialno").val(data['serialno']);
			$("#electro_extractdate").val(data['exday']);
			$("#electro_extracttime").val(data['etime']);
			$("#electro_extractby").val(data['extractby']);
			$("#electro_sodium").val(data['sodium']);
			$("#electro_potassium").val(data['potassium']);
			$("#electro_chloride").val(data['chloride']);
			$("#electro_total_calcium").val(data['total_calcium']);
			$("#electro_remarks").val(data['remarks']);

			var dis = $("#electrolytesResult").dialog({
				title: "Validate Electrolytes Result",
				width: 1024,
				height: 680,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Changes & Mark as Validated",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want confirm and validate this result?") == true) {
							var dataString = $("#frmElectroResult").serialize();
								dataString = "mod=validateElectrolytes&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Validated!");
										$("#frmElectroResult").trigger("reset");
										dis.dialog("close");
										showValidation();
				
									}
								});
							}
						}
					},
					{
						text: "Print Result",
						icons: { primary: "ui-icon-print" },
						click: function() {
							
							var so_no = $("#electro_sono").val();
							var serialno = $("#electro_serialno").val();
							var code = $("#electro_code").val();
							
							var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.electrolytes.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
							$("#report10").html(txtHTML);
							$("#report10").dialog({title: "Print - Electrolytes Result", width: 560, height: 620, resizable: true }).dialogExtend({
								"closable" : true,
								"maximizable" : true,
								"minimizable" : true
							});
		
		
						 }
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function antigenResult(lid,code) {

	$("#antigen_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "antigenResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#antigen_sono").val(data['myso']);
			$("#antigen_sodate").val(data['sodate']);
			$("#antigen_pid").val(data['mypid']);
			$("#antigen_pname").val(data['pname']);
			$("#antigen_gender").val(data['gender']);
			$("#antigen_birthdate").val(data['bday']);
			$("#antigen_age").val(data['age']);
			$("#antigen_patientstat").val(data['patientstatus']);
			$("#antigen_physician").val(data['physician']);
			$("#antigen_procedure").val(data['procedure']);
			$("#antigen_code").val(data['code']);
			$("#antigen_spectype").val(data['sampletype']);
			$("#antigen_serialno").val(data['serialno']);
			$("#antigen_testkit").val(data['testkit']);
			$("#antigen_testkit_lotno").val(data['lotno']);
			$("#antigen_testkit_expiry").val(data['expiry']);
			$("#antigen_extractdate").val(data['exday']);
			$("#antigen_extracttime").val(data['etime']);
			$("#antigen_extractby").val(data['extractby']);
			$("#antigen_result").val(data['result']);
			$("#antigen_sensitivity").val(data['sensitivity']);
			$("#antigen_specificity").val(data['specificity']);
			$("#antigen_result_by").val(data['performed_by']);
			$("#antigen_remarks").val(data['remarks']);

			var dis = $("#antigenResult").dialog({
				title: "Write Result",
				width: 1040,
				height: 690,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
						
								if(confirm("Are you sure you want save this data?") == true) {
									var dataString = $("#frmAntigenResult").serialize();
										dataString = "mod=saveAntigenResult&" + dataString;
										$.ajax({
											type: "POST",
											url: "src/sjerp.php",
											data: dataString,
											success: function() {
												alert("Result Successfully Saved!");
												dis.dialog("close");
												$("#frmAntigenResult").trigger("reset");
										}
									});
								}
							}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); $("#frmAntigenResult").trigger("reset"); }
					}
				]
			});

		},"json"
	);
}

function validateAntigenResult(lid,code) {

	$("#antigen_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "antigenResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#antigen_sono").val(data['myso']);
			$("#antigen_sodate").val(data['sodate']);
			$("#antigen_pid").val(data['mypid']);
			$("#antigen_pname").val(data['pname']);
			$("#antigen_gender").val(data['gender']);
			$("#antigen_birthdate").val(data['bday']);
			$("#antigen_age").val(data['age']);
			$("#antigen_patientstat").val(data['patientstatus']);
			$("#antigen_physician").val(data['physician']);
			$("#antigen_procedure").val(data['procedure']);
			$("#antigen_code").val(data['code']);
			$("#antigen_spectype").val(data['sampletype']);
			$("#antigen_serialno").val(data['serialno']);
			$("#antigen_testkit").val(data['testkit']);
			$("#antigen_testkit_lotno").val(data['lotno']);
			$("#antigen_testkit_expiry").val(data['expiry']);
			$("#antigen_extractdate").val(data['exday']);
			$("#antigen_extracttime").val(data['etime']);
			$("#antigen_extractby").val(data['extractby']);
			$("#antigen_result").val(data['result']);
			$("#antigen_sensitivity").val(data['sensitivity']);
			$("#antigen_specificity").val(data['specificity']);
			$("#antigen_result_by").val(data['performed_by']);
			$("#antigen_remarks").val(data['remarks']);

			var dis = $("#antigenResult").dialog({
				title: "Validate Antigen Result",
				width: 1040,
				height: 690,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Changes & Mark as Validated",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want confirm and validate this result?") == true) {
							var dataString = $("#frmAntigenResult").serialize();
								dataString = "mod=validateAntigenResult&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Validated!");
										$("#frmAntigenResult").trigger("reset");
										dis.dialog("close");
										showValidation();
				
									}
								});
							}
						}
					},
					{
						text: "Print Result",
						icons: { primary: "ui-icon-print" },
						click: function() {
							
							var so_no = $("#antigen_sono").val();
							var serialno = $("#antigen_serialno").val();
							var code = $("#antigen_code").val();
							
							var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.antigen.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
							$("#report10").html(txtHTML);
							$("#report10").dialog({title: "Print - ANTIGEN Result", width: 560, height: 620, resizable: true }).dialogExtend({
								"closable" : true,
								"maximizable" : true,
								"minimizable" : true
							});
		
		
						 }
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function antibodyResult(lid,code) {

	$("#antibody_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "antibodyResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#antibody_sono").val(data['myso']);
			$("#antibody_sodate").val(data['sodate']);
			$("#antibody_pid").val(data['mypid']);
			$("#antibody_pname").val(data['pname']);
			$("#antibody_gender").val(data['gender']);
			$("#antibody_birthdate").val(data['bday']);
			$("#antibody_age").val(data['age']);
			$("#antibody_patientstat").val(data['patientstatus']);
			$("#antibody_physician").val(data['physician']);
			$("#antibody_procedure").val(data['procedure']);
			$("#antibody_code").val(data['code']);
			$("#antibody_spectype").val(data['sampletype']);
			$("#antibody_serialno").val(data['serialno']);
			$("#antibody_testkit").val(data['testkit']);
			$("#antibody_testkit_lotno").val(data['lotno']);
			$("#antibody_testkit_expiry").val(data['expiry']);
			$("#antibody_extractdate").val(data['exday']);
			$("#antibody_extracttime").val(data['etime']);
			$("#antibody_extractby").val(data['extractby']);
			$("#antibody_result_igm").val(data['result_igm']);
			$("#antibody_result_igg").val(data['result_igg']);
			$("#antibody_sensitivity").val(data['sensitivity']);
			$("#antibody_specificity").val(data['specificity']);
			$("#antibody_result_by").val(data['performed_by']);
			$("#antibody_remarks").val(data['remarks']);

			var dis = $("#antibodyResult").dialog({
				title: "Write Result",
				width: 1040,
				height: 690,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
						
								if(confirm("Are you sure you want save this data?") == true) {
									var dataString = $("#frmAntibodyResult").serialize();
										dataString = "mod=saveAntibodyResult&" + dataString;
										$.ajax({
											type: "POST",
											url: "src/sjerp.php",
											data: dataString,
											success: function() {
												alert("Result Successfully Saved!");
												dis.dialog("close");
												$("#frmAntibodyResult").trigger("reset");
										}
									});
								}
							}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); $("#frmantibodyResult").trigger("reset"); }
					}
				]
			});

		},"json"
	);
}

function validateAntibodyResult(lid,code) {

	$("#antibody_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "antibodyResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#antibody_sono").val(data['myso']);
			$("#antibody_sodate").val(data['sodate']);
			$("#antibody_pid").val(data['mypid']);
			$("#antibody_pname").val(data['pname']);
			$("#antibody_gender").val(data['gender']);
			$("#antibody_birthdate").val(data['bday']);
			$("#antibody_age").val(data['age']);
			$("#antibody_patientstat").val(data['patientstatus']);
			$("#antibody_physician").val(data['physician']);
			$("#antibody_procedure").val(data['procedure']);
			$("#antibody_code").val(data['code']);
			$("#antibody_spectype").val(data['sampletype']);
			$("#antibody_serialno").val(data['serialno']);
			$("#antibody_testkit").val(data['testkit']);
			$("#antibody_testkit_lotno").val(data['lotno']);
			$("#antibody_testkit_expiry").val(data['expiry']);
			$("#antibody_extractdate").val(data['exday']);
			$("#antibody_extracttime").val(data['etime']);
			$("#antibody_extractby").val(data['extractby']);
			$("#antibody_result_igm").val(data['result_igm']);
			$("#antibody_result_igg").val(data['result_igg']);
			$("#antibody_sensitivity").val(data['sensitivity']);
			$("#antibody_specificity").val(data['specificity']);
			$("#antibody_result_by").val(data['performed_by']);
			$("#antibody_remarks").val(data['remarks']);

			var dis = $("#antibodyResult").dialog({
				title: "Validate Antibody Result",
				width: 1040,
				height: 690,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Changes & Mark as Validated",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want confirm and validate this result?") == true) {
							var dataString = $("#frmAntibodyResult").serialize();
								dataString = "mod=validateAntibodyResult&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Validated!");
										$("#frmAntibodyResult").trigger("reset");
										dis.dialog("close");
										showValidation();
				
									}
								});
							}
						}
					},
					{
						text: "Print Result",
						icons: { primary: "ui-icon-print" },
						click: function() {
							
							var so_no = $("#antibody_sono").val();
							var serialno = $("#antibody_serialno").val();
							var code = $("#antibody_code").val();
							
							var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.antibody.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
							$("#report10").html(txtHTML);
							$("#report10").dialog({title: "Print - ANTIBODY IGM/IGG Result", width: 560, height: 620, resizable: true }).dialogExtend({
								"closable" : true,
								"maximizable" : true,
								"minimizable" : true
							});
		
		
						 }
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function hivResult(lid,code) {

	$("#hiv_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "hivResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#hiv_sono").val(data['myso']);
			$("#hiv_sodate").val(data['sodate']);
			$("#hiv_pid").val(data['mypid']);
			$("#hiv_pname").val(data['pname']);
			$("#hiv_gender").val(data['gender']);
			$("#hiv_birthdate").val(data['bday']);
			$("#hiv_age").val(data['age']);
			$("#hiv_patientstat").val(data['patientstatus']);
			$("#hiv_physician").val(data['physician']);
			$("#hiv_procedure").val(data['procedure']);
			$("#hiv_code").val(data['code']);
			$("#hiv_spectype").val(data['sampletype']);
			$("#hiv_serialno").val(data['serialno']);
			$("#hiv_extractdate").val(data['exday']);
			$("#hiv_extracttime").val(data['etime']);
			$("#hiv_extractby").val(data['extractby']);
			$("#hiv_one").val(data['hiv_one']);
			$("#hiv_two").val(data['hiv_two']);
			$("#hiv_half").val(data['hiv_half']);

			var dis = $("#hivResult").dialog({
				title: "Write Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
						
								if(confirm("Are you sure you want save this data?") == true) {
									var dataString = $("#frmHivResult").serialize();
										dataString = "mod=saveHivResult&" + dataString;
										$.ajax({
											type: "POST",
											url: "src/sjerp.php",
											data: dataString,
											success: function() {
												alert("Result Successfully Saved!");
												dis.dialog("close");
												$("#frmHivResult").trigger("reset");
										}
									});
								}
							}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); $("#frmHivResult").trigger("reset"); }
					}
				]
			});

		},"json"
	);
}

function validateHivResult(lid,code) {

	$("#hiv_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "hivResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#hiv_sono").val(data['myso']);
			$("#hiv_sodate").val(data['sodate']);
			$("#hiv_pid").val(data['mypid']);
			$("#hiv_pname").val(data['pname']);
			$("#hiv_gender").val(data['gender']);
			$("#hiv_birthdate").val(data['bday']);
			$("#hiv_age").val(data['age']);
			$("#hiv_patientstat").val(data['patientstatus']);
			$("#hiv_physician").val(data['physician']);
			$("#hiv_procedure").val(data['procedure']);
			$("#hiv_code").val(data['code']);
			$("#hiv_spectype").val(data['sampletype']);
			$("#hiv_serialno").val(data['serialno']);
			$("#hiv_extractdate").val(data['exday']);
			$("#hiv_extracttime").val(data['etime']);
			$("#hiv_extractby").val(data['extractby']);
			$("#hiv_one").val(data['hiv_one']);
			$("#hiv_two").val(data['hiv_two']);
			$("#hiv_half").val(data['hiv_half']);

			var dis = $("#hivResult").dialog({
				title: "Validate HIV Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Changes & Mark as Validated",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want confirm and validate this result?") == true) {
							var dataString = $("#frmHivResult").serialize();
								dataString = "mod=validateHivResult&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Validated!");
										$("#frmHivResult").trigger("reset");
										dis.dialog("close");
										showValidation();
				
									}
								});
							}
						}
					},
					{
						text: "Print Result",
						icons: { primary: "ui-icon-print" },
						click: function() {
							
							var so_no = $("#hiv_sono").val();
							var serialno = $("#hiv_serialno").val();
							var code = $("#hiv_code").val();
							
							var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.hiv.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
							$("#report10").html(txtHTML);
							$("#report10").dialog({title: "Print - HIV Result", width: 560, height: 620, resizable: true }).dialogExtend({
								"closable" : true,
								"maximizable" : true,
								"minimizable" : true
							});
		
		
						 }
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function occultBlood(lid,code) {

	$("#occultblood_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "occultblood",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#occultblood_sono").val(data['myso']);
			$("#occultblood_sodate").val(data['sodate']);
			$("#occultblood_pid").val(data['mypid']);
			$("#occultblood_pname").val(data['pname']);
			$("#occultblood_gender").val(data['gender']);
			$("#occultblood_birthdate").val(data['bday']);
			$("#occultblood_age").val(data['age']);
			$("#occultblood_patientstat").val(data['patientstatus']);
			$("#occultblood_physician").val(data['physician']);
			$("#occultblood_procedure").val(data['procedure']);
			$("#occultblood_code").val(data['code']);
			$("#occultblood_spectype").val(data['sampletype']);
			$("#occultblood_serialno").val(data['serialno']);
			$("#occultblood_extractdate").val(data['exday']);
			$("#occultblood_extracttime").val(data['etime']);
			$("#occultblood_extractby").val(data['extractby']);
			$("#occultblood_color").val(data['color']);
			$("#occultblood_consistency").val(data['consistency']);
			$("#occultbloodres").val(data['result']);
			$("#occultblood_remarks").val(data['remarks']);

			var dis = $("#occultResult").dialog({
				title: "Write Result",
				width: 1024,
				height: 680,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
						
								if(confirm("Are you sure you want save this data?") == true) {
									var dataString = $("#frmOccultResult").serialize();
										dataString = "mod=saveOccultBlood&" + dataString;
										$.ajax({
											type: "POST",
											url: "src/sjerp.php",
											data: dataString,
											success: function() {
												alert("Result Successfully Saved!");
												dis.dialog("close");
												$("#frmOccultResult").trigger("reset");
										}
									});
								}
							}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); $("#frmOccultResult").trigger("reset"); }
					}
				]
			});

		},"json"
	);
}

function validateOccultBlood(lid,code) {

	$("#occultblood_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "occultblood",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#occultblood_sono").val(data['myso']);
			$("#occultblood_sodate").val(data['sodate']);
			$("#occultblood_pid").val(data['mypid']);
			$("#occultblood_pname").val(data['pname']);
			$("#occultblood_gender").val(data['gender']);
			$("#occultblood_birthdate").val(data['bday']);
			$("#occultblood_age").val(data['age']);
			$("#occultblood_patientstat").val(data['patientstatus']);
			$("#occultblood_physician").val(data['physician']);
			$("#occultblood_procedure").val(data['procedure']);
			$("#occultblood_code").val(data['code']);
			$("#occultblood_spectype").val(data['sampletype']);
			$("#occultblood_serialno").val(data['serialno']);
			$("#occultblood_extractdate").val(data['exday']);
			$("#occultblood_extracttime").val(data['etime']);
			$("#occultblood_extractby").val(data['extractby']);
			$("#occultblood_color").val(data['color']);
			$("#occultblood_consistency").val(data['consistency']);
			$("#occultbloodres").val(data['result']);
			$("#occultblood_remarks").val(data['remarks']);

			var dis = $("#occultResult").dialog({
				title: "Validate Fecal Occult Blood Result",
				width: 1024,
				height: 680,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Changes & Mark as Validated",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want confirm and validate this result?") == true) {
							var dataString = $("#frmOccultResult").serialize();
								dataString = "mod=validateOccultBlood&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Validated!");
										$("#frmOccultResult").trigger("reset");
										dis.dialog("close");
										showValidation();
				
									}
								});
							}
						}
					},
					{
						text: "Print Result",
						icons: { primary: "ui-icon-print" },
						click: function() {
							
							var so_no = $("#occultblood_sono").val();
							var serialno = $("#occultblood_serialno").val();
							var code = $("#occultblood_code").val();
							
							var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.occultblood.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
							$("#report10").html(txtHTML);
							$("#report10").dialog({title: "Print - Fecal Occult Blood Result", width: 560, height: 620, resizable: true }).dialogExtend({
								"closable" : true,
								"maximizable" : true,
								"minimizable" : true
							});
		
		
						 }
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function syphilisResult(lid,code) {

	$("#enum_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "enumResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#enum_sono").val(data['myso']);
			$("#enum_sodate").val(data['sodate']);
			$("#enum_pid").val(data['mypid']);
			$("#enum_pname").val(data['pname']);
			$("#enum_gender").val(data['gender']);
			$("#enum_birthdate").val(data['bday']);
			$("#enum_age").val(data['age']);
			$("#enum_patientstat").val(data['patientstatus']);
			$("#enum_physician").val(data['physician']);
			$("#enum_procedure").val(data['procedure']);
			$("#enum_code").val(data['code']);
			$("#enum_spectype").val(data['sampletype']);
			$("#enum_serialno").val(data['serialno']);
			$("#enum_testkit").val(data['testkit']);
			$("#enum_testkit_lotno").val(data['lotno']);
			$("#enum_testkit_expiry").val(data['expiry']);
			$("#enum_extractdate").val(data['exday']);
			$("#enum_extracttime").val(data['etime']);
			$("#enum_extractby").val(data['extractby']);
			$("#enum_result").val(data['result']);
			$("#enum_result_by").val(data['performed_by']);
			$("#enum_remarks").val(data['remarks']);

			var dis = $("#enumResult").dialog({
				title: "Write Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
							if($("#enum_result").val() != '') {
								if(confirm("Are you sure you want save this data?") == true) {
									var dataString = $("#frmEnumResult").serialize();
									dataString = "mod=saveEnumResult&" + dataString;
									$.ajax({
										type: "POST",
										url: "src/sjerp.php",
										data: dataString,
										success: function() {
											alert("Result Successfully Saved!");
											$("#frmEnumResult").trigger("reset");
											dis.dialog("close");
										}
									});
								}
							}
						}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function validateSyphilisResult(lid,code) {
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "enumResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#enum_sono").val(data['myso']);
			$("#enum_sodate").val(data['sodate']);
			$("#enum_pid").val(data['mypid']);
			$("#enum_pname").val(data['pname']);
			$("#enum_gender").val(data['gender']);
			$("#enum_birthdate").val(data['bday']);
			$("#enum_age").val(data['age']);
			$("#enum_patientstat").val(data['patientstatus']);
			$("#enum_physician").val(data['physician']);
			$("#enum_procedure").val(data['procedure']);
			$("#enum_code").val(data['code']);
			$("#enum_spectype").val(data['sampletype']);
			$("#enum_serialno").val(data['serialno']);
			$("#enum_testkit").val(data['testkit']);
			$("#enum_testkit_lotno").val(data['lotno']);
			$("#enum_testkit_expiry").val(data['expiry']);
			$("#enum_extractdate").val(data['exday']);
			$("#enum_extracttime").val(data['etime']);
			$("#enum_extractby").val(data['extractby']);
			$("#enum_result").val(data['result']);
			$("#enum_result_by").val(data['performed_by']);
			$("#enum_remarks").val(data['remarks']);

			var dis = $("#enumResult").dialog({
				title: "Validate Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Publish & Mark as Validated",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if($("#enum_result").val() != '') {
								if(confirm("Are you sure you want save this data?") == true) {
									var dataString = $("#frmEnumResult").serialize();
									dataString = "mod=validateEnumResult&" + dataString;
									$.ajax({
										type: "POST",
										url: "src/sjerp.php",
										data: dataString,
										success: function() {
											alert("Result Successfully Confirmed & Published");
											$("#frmEnumResult").trigger("reset");
											dis.dialog("close");
											showValidation();
										}
									});
								}
							}
						}
					},
					{
						text: "Print Result",
						icons: { primary: "ui-icon-print" },
						click: function() {
							
							var so_no = $("#enum_sono").val();
							var code = $("#enum_code").val();
							var serialno = $("#enum_serialno").val();

							var txtHTML = "<iframe id='printSingleValue' frameborder=0 width='100%' height='100%' src='print/result.syphilis.php?so_no="+$("#enum_sono").val()+"&code="+$("#enum_code").val()+"&serialno="+$("#enum_serialno").val()+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
							$("#report5").html(txtHTML);
							$("#report5").dialog({title: "Result - "+ $("#enum_procedure").val() +"", width: 560, height: 620, resizable: true }).dialogExtend({
								"closable" : true,
								"maximizable" : true,
								"minimizable" : true
							});

						 }
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}


function enumResult(lid,code) {

	$("#enum_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "enumResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#enum_sono").val(data['myso']);
			$("#enum_sodate").val(data['sodate']);
			$("#enum_pid").val(data['mypid']);
			$("#enum_pname").val(data['pname']);
			$("#enum_gender").val(data['gender']);
			$("#enum_birthdate").val(data['bday']);
			$("#enum_age").val(data['age']);
			$("#enum_patientstat").val(data['patientstatus']);
			$("#enum_physician").val(data['physician']);
			$("#enum_procedure").val(data['procedure']);
			$("#enum_code").val(data['code']);
			$("#enum_spectype").val(data['sampletype']);
			$("#enum_serialno").val(data['serialno']);
			$("#enum_testkit").val(data['testkit']);
			$("#enum_testkit_lotno").val(data['lotno']);
			$("#enum_testkit_expiry").val(data['expiry']);
			$("#enum_extractdate").val(data['exday']);
			$("#enum_extracttime").val(data['etime']);
			$("#enum_extractby").val(data['extractby']);
			$("#enum_result").val(data['result']);
			$("#enum_result_by").val(data['performed_by']);
			$("#enum_remarks").val(data['remarks']);

			var dis = $("#enumResult").dialog({
				title: "Write Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
							if($("#enum_result").val() != '') {
								if(confirm("Are you sure you want save this data?") == true) {
									var dataString = $("#frmEnumResult").serialize();
									dataString = "mod=saveEnumResult&" + dataString;
									$.ajax({
										type: "POST",
										url: "src/sjerp.php",
										data: dataString,
										success: function() {
											alert("Result Successfully Saved!");
											$("#frmEnumResult").trigger("reset");
											dis.dialog("close");
											showValidation();
										}
									});
								}
							}
						}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function validateEnumResult(lid,code) {
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "enumResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#enum_sono").val(data['myso']);
			$("#enum_sodate").val(data['sodate']);
			$("#enum_pid").val(data['mypid']);
			$("#enum_pname").val(data['pname']);
			$("#enum_gender").val(data['gender']);
			$("#enum_birthdate").val(data['bday']);
			$("#enum_age").val(data['age']);
			$("#enum_patientstat").val(data['patientstatus']);
			$("#enum_physician").val(data['physician']);
			$("#enum_procedure").val(data['procedure']);
			$("#enum_code").val(data['code']);
			$("#enum_spectype").val(data['sampletype']);
			$("#enum_serialno").val(data['serialno']);
			$("#enum_testkit").val(data['testkit']);
			$("#enum_testkit_lotno").val(data['lotno']);
			$("#enum_testkit_expiry").val(data['expiry']);
			$("#enum_extractdate").val(data['exday']);
			$("#enum_extracttime").val(data['etime']);
			$("#enum_extractby").val(data['extractby']);
			$("#enum_result").val(data['result']);
			$("#enum_result_by").val(data['performed_by']);
			$("#enum_remarks").val(data['remarks']);

			var dis = $("#enumResult").dialog({
				title: "Validate Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save & Confirm Result",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if($("#enum_result").val() != '') {
								if(confirm("Are you sure you want save this data?") == true) {
									var dataString = $("#frmEnumResult").serialize();
									dataString = "mod=validateEnumResult&" + dataString;
									$.ajax({
										type: "POST",
										url: "src/sjerp.php",
										data: dataString,
										success: function() {
											alert("Result Successfully Confirmed & Published");
											$("#enumResult").trigger("reset");
											dis.dialog("close");
											showValidation();
										}
									});
								}
							}
						}
					},
					{
						text: "Print Result",
						icons: { primary: "ui-icon-print" },
						click: function() {
							
							var so_no = $("#sresult_sono").val();
							var code = $("#sresult_code").val();
							var serialno = $("#sresult_serialno").val();

							var txtHTML = "<iframe id='printSingleValue' frameborder=0 width='100%' height='100%' src='print/result.enum.php?so_no="+$("#enum_sono").val()+"&code="+$("#enum_code").val()+"&serialno="+$("#enum_serialno").val()+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
							$("#report5").html(txtHTML);
							$("#report5").dialog({title: "Result - "+ $("#enum_procedure").val() +"", width: 560, height: 620, resizable: true }).dialogExtend({
								"closable" : true,
								"maximizable" : true,
								"minimizable" : true
							});

						 }
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function dengueResult(lid,code) {

	$("#sresult_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "dengueResultView",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#dengue_sono").val(data['myso']);
			$("#dengue_sodate").val(data['sodate']);
			$("#dengue_pid").val(data['mypid']);
			$("#dengue_pname").val(data['pname']);
			$("#dengue_gender").val(data['gender']);
			$("#dengue_birthdate").val(data['bday']);
			$("#dengue_age").val(data['age']);
			$("#dengue_patientstat").val(data['patientstatus']);
			$("#dengue_physician").val(data['physician']);
			$("#dengue_procedure").val(data['procedure']);
			$("#dengue_code").val(data['code']);
			$("#dengue_spectype").val(data['sampletype']);
			$("#dengue_serialno").val(data['serialno']);
			$("#dengue_extractdate").val(data['exday']);
			$("#dengue_extracttime").val(data['etime']);
			$("#dengue_extractby").val(data['extractby']);
			$("#dengue_ag").val(data['dengue_ag']);
			$("#dengue_igg").val(data['dengue_igg']);
			$("#dengue_igm").val(data['dengue_igm']);

			var dis = $("#dengueResult").dialog({
				title: "Write Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
						
								if(confirm("Are you sure you want save this data?") == true) {
									var dataString = $("#frmDengueResult").serialize();
										dataString = "mod=saveDengueResult&" + dataString;
										$.ajax({
											type: "POST",
											url: "src/sjerp.php",
											data: dataString,
											success: function() {
												alert("Result Successfully Saved!");
												dis.dialog("close");
												$("#frmDengueResult").trigger("reset");
										}
									});
								}
							}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); $("#frmsingleValue").trigger("reset"); }
					}
				]
			});

		},"json"
	);
}

function validateDengueResult(lid,code) {

	$("#dengue_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "dengueResultView",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#dengue_sono").val(data['myso']);
			$("#dengue_sodate").val(data['sodate']);
			$("#dengue_pid").val(data['mypid']);
			$("#dengue_pname").val(data['pname']);
			$("#dengue_gender").val(data['gender']);
			$("#dengue_birthdate").val(data['bday']);
			$("#dengue_age").val(data['age']);
			$("#dengue_patientstat").val(data['patientstatus']);
			$("#dengue_physician").val(data['physician']);
			$("#dengue_procedure").val(data['procedure']);
			$("#dengue_code").val(data['code']);
			$("#dengue_spectype").val(data['sampletype']);
			$("#dengue_serialno").val(data['serialno']);
			$("#dengue_extractdate").val(data['exday']);
			$("#dengue_extracttime").val(data['etime']);
			$("#dengue_extractby").val(data['extractby']);
			$("#dengue_ag").val(data['dengue_ag']);
			$("#dengue_igg").val(data['dengue_igg']);
			$("#dengue_igm").val(data['dengue_igm']);

			var dis = $("#dengueResult").dialog({
				title: "Validate Dengue Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Changes & Mark as Validated",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want confirm and validate this result?") == true) {
							var dataString = $("#frmDengueResult").serialize();
								dataString = "mod=validateDengueResult&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Validated!");
										$("#frmDengueResult").trigger("reset");
										dis.dialog("close");
										showValidation();
				
									}
								});
							}
						}
					},
					{
						text: "Print Result",
						icons: { primary: "ui-icon-print" },
						click: function() {
							
							var so_no = $("#dengue_sono").val();
							var serialno = $("#dengue_serialno").val();
							var code = $("#dengue_code").val();
							
							var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.dengue.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
							$("#report10").html(txtHTML);
							$("#report10").dialog({title: "Print - Dengue Result", width: 560, height: 620, resizable: true }).dialogExtend({
								"closable" : true,
								"maximizable" : true,
								"minimizable" : true
							});
		
		
						 }
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function importHema() {
	var txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src='importhemaresults.php'></iframe>";
	$("#importdtr").html(txtHTML);
	$("#importdtr").dialog({title: "Import Hema Results", width: 480, height: 200, resizable: false }).dialogExtend({
		"closable" : true,
		"maximizable" : false,
		"minimizable" : true
	});
}

function cbcResult(lid,code) {

	$("#cbcResult").html("<iframe id='frmCbcResult' frameborder=0 width='100%' height='100%' src='result.cbc.php?lid="+lid+"'></iframe>");
	
	var dis = $("#cbcResult").dialog({
		title: "Write Result",
		width: 1324,
		height: 690,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Result Pending Validation",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var msg = '';
						
						if($('#frmCbcResult').contents().find("#wbc").val() == '' ) { msg = msg + "- Invalid or Empty Value for <b>WBC</b> count<br/>"; }
						if($('#frmCbcResult').contents().find("#rbc").val() == '' ) { msg = msg + "- Invalid or Empty Value for <b>RBC</b> count<br/>"; }
						if($('#frmCbcResult').contents().find("#hemoglobin").val() == '' ) { msg = msg + "- Invalid or Empty Value for <b>Hemoglobin</b> count<br/>"; }
						if($('#frmCbcResult').contents().find("#hematocrit").val() == '' ) { msg = msg + "- Invalid or Empty Value for <b>Hematocrit</b> count<br/>"; }
						if($('#frmCbcResult').contents().find("#platelate").val() == '' ) { msg = msg + "- Invalid or Empty Value for <b>Platelate</b> count<br/>"; }

						var totalDifferential = parseFloat($('#frmCbcResult').contents().find("#neutrophils").val()) + parseFloat($('#frmCbcResult').contents().find("#lymphocytes").val()) + parseFloat($('#frmCbcResult').contents().find("#monocytes").val()) + parseFloat($('#frmCbcResult').contents().find("#eosinophils").val()) + parseFloat($('#frmCbcResult').contents().find("#basophils").val());

						//if(totalDifferential != 100) { msg = msg + "- <b>Total Differential Count</b> != <b>100%</b><br/>"; }


						if(msg != '') {
							parent.sendErrorMessage(msg);
						} else {
							var dataString = $('#frmCbcResult').contents().find('#frmCBCResult').serialize();
							dataString = "mod=saveCBCResult&" + dataString;
							$.ajax({
								type: "POST",
								url: "src/sjerp.php",
								data: dataString,
								success: function() {
									alert("Result Successfully Saved!");
									dis.dialog("close");
									$("#frmCBCResult").trigger("reset");
								}
							});
						}
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmCbcResult').contents().find('#cbc_sono').val();
					var serialno = $('#frmCbcResult').contents().find('#cbc_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.cbc.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - CBC RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function validateCbcResult(lid,code) {

	$("#cbcResult").html("<iframe id='frmCbcResult' frameborder=0 width='100%' height='100%' src='result.cbc.php?lid="+lid+"'></iframe>");
	
	var dis = $("#cbcResult").dialog({
		title: "Validate Result",
		width: 1324,
		height: 690,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Changes & Mark Result as Validated",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmCbcResult').contents().find('#frmCBCResult').serialize();
					
						//var dataString = $("#frmDescResult").serialize();
						dataString = "mod=validateCBCResult&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Marked as Validated!");
								showValidation();
								dis.dialog("close");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmCbcResult').contents().find('#cbc_sono').val();
					var serialno = $('#frmCbcResult').contents().find('#cbc_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.cbc.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - CBC RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function bloodChem(lid,code) {
	
	$("#bloodChemResult").html("<iframe id='frmBloodChem' frameborder=0 width='100%' height='100%' src='result.bloodchem.php?lid="+lid+"'></iframe>");
	
	$("#bloodChemResult").dialog({
		title: "Write Result",
		width: xWidth,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Result Pending Validation",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmBloodChem').contents().find('#frmBloodChemResult').serialize();
						dataString = "mod=saveBloodChem&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Saved!");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmBloodChem').contents().find('#bloodchem_sono').val();
					var serialno = $('#frmBloodChem').contents().find('#bloodchem_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.bloodchem.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - BLOOD CHEMISTRY RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function validateBloodChem(lid,code) {
	
	$("#bloodChemResult").html("<iframe id='frmBloodChem' frameborder=0 width='100%' height='100%' src='result.bloodchem.php?lid="+lid+"'></iframe>");
	
	var dis = $("#bloodChemResult").dialog({
		title: "Write Result",
		width: 1200,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Changes & Mark Result as Validated",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmBloodChem').contents().find('#frmBloodChemResult').serialize();
						dataString = "mod=validateBloodChem&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Marked as Validated!");
								showValidation();
								dis.dialog("close");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmBloodChem').contents().find('#bloodchem_sono').val();
					var serialno = $('#frmBloodChem').contents().find('#bloodchem_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.bloodchem.php?so_no="+so_no+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - BLOOD CHEMISTRY RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function spchem(lid,code) {
	
	$("#specialChemistryResult").html("<iframe id='frmspchemResult' frameborder=0 width='100%' height='100%' src='result.spchem.php?lid="+lid+"'></iframe>");
	
	$("#specialChemistryResult").dialog({
		title: "Write Result",
		width: xWidth,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Result Pending Validation",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this result?") == true) {
						var dataString = $('#frmspchemResult').contents().find('#frmspchemResult').serialize();
						dataString = "mod=saveSPChem&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Saved!");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmspchemResult').contents().find('#spchem_sono').val();
					var serialno = $('#frmspchemResult').contents().find('#spchem_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.spchem.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - SPECIAL CHEMISTRY RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function validateSPChem(lid,code) {
	
	$("#specialChemistryResult").html("<iframe id='frmSpBloodChem' frameborder=0 width='100%' height='100%' src='result.spchem.php?lid="+lid+"'></iframe>");
	
	var dis = $("#specialChemistryResult").dialog({
		title: "Write Result",
		width: 1200,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Changes & Mark Result as Validated",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmSpBloodChem').contents().find('#frmspchemResult').serialize();
						dataString = "mod=validateSPChem&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Marked as Validated!");
								showValidation();
								dis.dialog("close");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmspchemResultResult').contents().find('#spchem_sono').val();
					var serialno = $('#frmspchemResultResult').contents().find('#spchem_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.spchem.php?so_no="+so_no+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - SPECIAL CHEMISTRY RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

/* Blood Chem Consolidated */
function writeChemistryResult(so_no) {
	
	$("#bloodChemResult").html("<iframe id='frmBloodChem' frameborder=0 width='100%' height='100%' src='result.bloodchem.conso.php?so_no="+so_no+"'></iframe>");
	
	$("#bloodChemResult").dialog({
		title: "Write Result",
		width: 1024,
		height: 920,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Result Pending Validation",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmBloodChem').contents().find('#frmBloodChemResult').serialize();
						dataString = "mod=saveBloodChem&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Saved!");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmBloodChem').contents().find('#bloodchem_sono').val();
					var serialno = $('#frmBloodChem').contents().find('#bloodchem_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.bloodchem.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report3").html(txtHTML);
					$("#report3").dialog({title: "Print - BLOOD CHEMISTRY RESULT", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}


function uaResult(lid,code) {
	
	$("#uaResult").html("<iframe id='frmUA' frameborder=0 width='100%' height='100%' src='result.ua.php?lid="+lid+"'></iframe>");
	
	$("#uaResult").dialog({
		title: "Write Result",
		width: 1024,
		height: 960,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Result Pending Validation",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmUA').contents().find('#frmUrinalysisReport').serialize();
						dataString = "mod=saveUAReport&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Saved!");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmUA').contents().find('#ua_sono').val();
					var serialno = $('#frmUA').contents().find('#ua_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.ua.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report1").html(txtHTML);
					$("#report1").dialog({title: "Print - Uranilysis (UA)", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function validateUaResult(lid,code) {
	
	$("#uaResult").html("<iframe id='frmUA' frameborder=0 width='100%' height='100%' src='result.ua.php?lid="+lid+"'></iframe>");
	
	var dis = $("#uaResult").dialog({
		title: "Validate Result",
		width: 1024,
		height: 720,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Changes & Mark Resullt as Validated",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmUA').contents().find('#frmUrinalysisReport').serialize();
						dataString = "mod=validateUAReport&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Marked as Validated!");
								showValidation();
								dis.dialog("close");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmUA').contents().find('#ua_sono').val();
					var serialno = $('#frmUA').contents().find('#ua_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.ua.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report1").html(txtHTML);
					$("#report1").dialog({title: "Print - Uranilysis (UA)", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}


function stoolExam(lid,code) {
	
	$("#stoolResult").html("<iframe id='frmStoolExam' frameborder=0 width='100%' height='100%' src='result.stool.php?lid="+lid+"'></iframe>");
	
	$("#stoolResult").dialog({
		title: "Write Result",
		width: 1024,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Result Pending Validation",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmStoolExam').contents().find('#frmStoolReport').serialize();
						dataString = "mod=saveStoolExam&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Saved!");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmStoolExam').contents().find('#stool_sono').val();
					var serialno = $('#frmStoolExam').contents().find('#stool_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.stool.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report2").html(txtHTML);
					$("#report2").dialog({title: "Print - Stool Exam", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function validateStoolExam(lid,code) {
	
	$("#stoolResult").html("<iframe id='frmStoolExam' frameborder=0 width='100%' height='100%' src='result.stool.php?lid="+lid+"'></iframe>");
	
	var dis = $("#stoolResult").dialog({
		title: "Write Result",
		width: 1024,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Confirm & Validate Result",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want confirm and publish this result?") == true) {
						var dataString = $('#frmStoolExam').contents().find('#frmStoolReport').serialize();
						dataString = "mod=validateStoolExam&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Marked as Validated!");
								showValidation();
								dis.dialog("close");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmStoolExam').contents().find('#stool_sono').val();
					var serialno = $('#frmStoolExam').contents().find('#stool_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.stool.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report2").html(txtHTML);
					$("#report2").dialog({title: "Print - Stool Exam", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function semenAnalysis(lid,code) {
	
	$("#semAnalReport").html("<iframe id='frmSemenAnalysis' frameborder=0 width='100%' height='100%' src='result.sar.php?lid="+lid+"'></iframe>");
	
	$("#stoolResult").dialog({
		title: "Write Result",
		width: 1024,
		height: 680,
		resizeable: false,
		modal: true,
		buttons: [
			{
				text: "Save Result Pending Validation",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmSemenAnalysis').contents().find('#frmSemenAnalysisReport').serialize();
						dataString = "mod=saveSemenAnalysis&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Saved!");
							}
						});
					}
				}
			},
			{
				text: "Print Result",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmSemenAnalysis').contents().find('#semen_sono').val();
					var serialno = $('#frmSemenAnalysis').contents().find('#semen_serialno').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.sar.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report2").html(txtHTML);
					$("#report2").dialog({title: "Print - Stool Exam", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function pregnancyResult(lid,code) {

	$("#pt_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "enumResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#pt_sono").val(data['myso']);
			$("#pt_sodate").val(data['sodate']);
			$("#pt_pid").val(data['mypid']);
			$("#pt_pname").val(data['pname']);
			$("#pt_gender").val(data['gender']);
			$("#pt_birthdate").val(data['bday']);
			$("#pt_age").val(data['age']);
			$("#pt_patientstat").val(data['patientstatus']);
			$("#pt_physician").val(data['physician']);
			$("#pt_procedure").val(data['procedure']);
			$("#pt_code").val(data['code']);
			$("#pt_spectype").val(data['sampletype']);
			$("#pt_serialno").val(data['serialno']);
			$("#pt_extractdate").val(data['exday']);
			$("#pt_extracttime").val(data['etime']);
			$("#pt_extractby").val(data['extractby']);
			$("#pt_result").val(data['result']);
			$("#pt_remarks").val(data['remarks']);

			var dis = $("#pregnancyResult").dialog({
				title: "Write Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want save this data?") == true) {
							var dataString = $("#frmPregnancyResult").serialize();
								dataString = "mod=savePregnancyResult&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Saved!");
										$("#frmPregnancyResult").trigger("reset");
										dis.dialog("close");
										showValidation();
				
									}
								});
							}
						}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function validatePregnancyResult(lid,code) {
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "enumResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#pt_sono").val(data['myso']);
			$("#pt_sodate").val(data['sodate']);
			$("#pt_pid").val(data['mypid']);
			$("#pt_pname").val(data['pname']);
			$("#pt_gender").val(data['gender']);
			$("#pt_birthdate").val(data['bday']);
			$("#pt_age").val(data['age']);
			$("#pt_patientstat").val(data['patientstatus']);
			$("#pt_physician").val(data['physician']);
			$("#pt_procedure").val(data['procedure']);
			$("#pt_code").val(data['code']);
			$("#pt_spectype").val(data['sampletype']);
			$("#pt_serialno").val(data['serialno']);
			$("#pt_extractdate").val(data['exday']);
			$("#pt_extracttime").val(data['etime']);
			$("#pt_extractby").val(data['extractby']);
			$("#pt_result").val(data['result']);
			$("#pt_remarks").val(data['remarks']);

			var dis = $("#pregnancyResult").dialog({
				title: "Validate Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save & Confirm Result",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want confirm and publish this result?") == true) {
							var dataString = $("#frmPregnancyResult").serialize();
								dataString = "mod=validatePregnancyResult&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Confirmed & Published");
										showValidation();
										dis.dialog("close");
									}
								});
							}
						}
					},
					{
						text: "Print Result",
						icons: { primary: "ui-icon-print" },
						click: function() {
							
							var so_no = $("#sresult_sono").val();
							var code = $("#sresult_code").val();
							var serialno = $("#sresult_serialno").val();

							var txtHTML = "<iframe id='printPregnancyResult' frameborder=0 width='100%' height='100%' src='print/result.pt.php?so_no="+$("#pt_sono").val()+"&code="+$("#pt_code").val()+"&serialno="+$("#pt_serialno").val()+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
							$("#report15").html(txtHTML);
							$("#report15").dialog({title: "Result - "+ $("#pt_procedure").val() +"", width: 560, height: 620, resizable: true }).dialogExtend({
								"closable" : true,
								"maximizable" : true,
								"minimizable" : true
							});

						 }
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function lipidResult(lid,code) {

	$("#lipid_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "lipidPanel",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#lipid_sono").val(data['myso']);
			$("#lipid_sodate").val(data['sodate']);
			$("#lipid_pid").val(data['mypid']);
			$("#lipid_pname").val(data['pname']);
			$("#lipid_gender").val(data['gender']);
			$("#lipid_birthdate").val(data['bday']);
			$("#lipid_age").val(data['age']);
			$("#lipid_patientstat").val(data['patientstatus']);
			$("#lipid_physician").val(data['physician']);
			$("#lipid_procedure").val(data['procedure']);
			$("#lipid_code").val(data['code']);
			$("#lipid_spectype").val(data['sampletype']);
			$("#lipid_serialno").val(data['serialno']);
			$("#lipid_extractdate").val(data['exday']);
			$("#lipid_extracttime").val(data['etime']);
			$("#lipid_extractby").val(data['extractby']);
			$("#lipid_cholesterol").val(data['cholesterol']);
			$("#lipid_triglycerides").val(data['triglycerides']);
			$("#lipid_hdl").val(data['hdl']);
			$("#lipid_ldl").val(data['ldl']);
			$("#lipid_vldl").val(data['vldl']);
			$("#lipid_remarks").val(data['remarks']);

			var dis = $("#lipidResult").dialog({
				title: "Write Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want save this data?") == true) {
							var dataString = $("#frmLipidResult").serialize();
								dataString = "mod=saveLipidPanel&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Saved!");
										$("#frmLipidResult").trigger("reset");
										dis.dialog("close");
										showValidation();
				
									}
								});
							}
						}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function validateLipidResult(lid,code) {

	$("#lipid_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "lipidPanel",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#lipid_sono").val(data['myso']);
			$("#lipid_sodate").val(data['sodate']);
			$("#lipid_pid").val(data['mypid']);
			$("#lipid_pname").val(data['pname']);
			$("#lipid_gender").val(data['gender']);
			$("#lipid_birthdate").val(data['bday']);
			$("#lipid_age").val(data['age']);
			$("#lipid_patientstat").val(data['patientstatus']);
			$("#lipid_physician").val(data['physician']);
			$("#lipid_procedure").val(data['procedure']);
			$("#lipid_code").val(data['code']);
			$("#lipid_spectype").val(data['sampletype']);
			$("#lipid_serialno").val(data['serialno']);
			$("#lipid_extractdate").val(data['exday']);
			$("#lipid_extracttime").val(data['etime']);
			$("#lipid_extractby").val(data['extractby']);
			$("#lipid_cholesterol").val(data['cholesterol']);
			$("#lipid_triglycerides").val(data['triglycerides']);
			$("#lipid_hdl").val(data['hdl']);
			$("#lipid_ldl").val(data['ldl']);
			$("#lipid_vldl").val(data['vldl']);
			$("#lipid_remarks").val(data['remarks']);

			var dis = $("#lipidResult").dialog({
				title: "Validate Lipid Panel Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want confirm and validate this result?") == true) {
							var dataString = $("#frmLipidResult").serialize();
								dataString = "mod=validateLipidResult&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Validated!");
										$("#frmLipidResult").trigger("reset");
										dis.dialog("close");
										showValidation();
				
									}
								});
							}
						}
					},
					{
						text: "Print Result",
						icons: { primary: "ui-icon-print" },
						click: function() {
							
							var so_no = $("#lipid_sono").val();
							var serialno = $("#lipid_serialno").val();
							var code = $("#lipid_code").val();
							
							var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.lipidpanel.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
							$("#report2").html(txtHTML);
							$("#report2").dialog({title: "Print - Lipid Panel", width: 560, height: 620, resizable: true }).dialogExtend({
								"closable" : true,
								"maximizable" : true,
								"minimizable" : true
							});
		
		
						 }
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function ogttResult(lid,code) {

	$("#ogtt_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "ogttResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#ogtt_sono").val(data['myso']);
			$("#ogtt_sodate").val(data['sodate']);
			$("#ogtt_pid").val(data['mypid']);
			$("#ogtt_pname").val(data['pname']);
			$("#ogtt_gender").val(data['gender']);
			$("#ogtt_birthdate").val(data['bday']);
			$("#ogtt_age").val(data['age']);
			$("#ogtt_patientstat").val(data['patientstatus']);
			$("#ogtt_physician").val(data['physician']);
			$("#ogtt_procedure").val(data['procedure']);
			$("#ogtt_code").val(data['code']);
			$("#ogtt_spectype").val(data['sampletype']);
			$("#ogtt_serialno").val(data['serialno']);
			$("#ogtt_extractdate").val(data['exday']);
			$("#ogtt_extracttime").val(data['etime']);
			$("#ogtt_extractby").val(data['extractby']);
			$("#ogtt_fasting").val(data['fasting']);
			$("#ogtt_uglucose").val(data['fasting_uglucose']);
			$("#ogttFirstHr").val(data['first_hr']);
			$("#first_hr_uglucose").val(data['first_hr_uglucose']);
			$("#second_hr").val(data['second_hr']);
			$("#second_hr_uglucose").val(data['second_hr_uglucose']);

			var dis = $("#ogttResult").dialog({
				title: "Write Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want save this data?") == true) {
							var dataString = $("#frmOgttResult").serialize();
								dataString = "mod=saveOgttResult&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Saved!");
										$("#frmOgttResult").trigger("reset");
										dis.dialog("close");
				
									}
								});
							}
						}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}


function validateOgttResult(lid,code) {

	$("#ogtt_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "ogttResult",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#ogtt_sono").val(data['myso']);
			$("#ogtt_sodate").val(data['sodate']);
			$("#ogtt_pid").val(data['mypid']);
			$("#ogtt_pname").val(data['pname']);
			$("#ogtt_gender").val(data['gender']);
			$("#ogtt_birthdate").val(data['bday']);
			$("#ogtt_age").val(data['age']);
			$("#ogtt_patientstat").val(data['patientstatus']);
			$("#ogtt_physician").val(data['physician']);
			$("#ogtt_procedure").val(data['procedure']);
			$("#ogtt_code").val(data['code']);
			$("#ogtt_spectype").val(data['sampletype']);
			$("#ogtt_serialno").val(data['serialno']);
			$("#ogtt_extractdate").val(data['exday']);
			$("#ogtt_extracttime").val(data['etime']);
			$("#ogtt_extractby").val(data['extractby']);
			$("#ogtt_fasting").val(data['fasting']);
			$("#ogtt_uglucose").val(data['fasting_uglucose']);
			$("#ogttFirstHr").val(data['first_hr']);
			$("#first_hr_uglucose").val(data['first_hr_uglucose']);
			$("#second_hr").val(data['second_hr']);
			$("#second_hr_uglucose").val(data['second_hr_uglucose']);

			var dis = $("#ogttResult").dialog({
				title: "Validate OGTT Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Validate & Publish Result",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want confirm and validate this result?") == true) {
							var dataString = $("#frmOgttResult").serialize();
								dataString = "mod=validateOgttResult&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Validated!");
										$("#frmOgttResult").trigger("reset");
										dis.dialog("close");
										showValidation();
				
									}
								});
							}
						}
					},
					{
						text: "Print Result",
						icons: { primary: "ui-icon-print" },
						click: function() {
							
							var so_no = $("#ogtt_sono").val();
							var serialno = $("#ogtt_serialno").val();
							var code = $("#ogtt_code").val();
							
							var txtHTML = "<iframe id='prntOgttResult' frameborder=0 width='100%' height='100%' src='print/result.ogtt.php?so_no="+so_no+"&code="+code+"&serialno="+serialno+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
							$("#report1").html(txtHTML);
							$("#report1").dialog({title: "Print - OGTT TEST 75G", width: 560, height: 620, resizable: true }).dialogExtend({
								"closable" : true,
								"maximizable" : true,
								"minimizable" : true
							});
		
		
						 }
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function bloodTyping(lid,code) {

	$("#btype_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "bloodType",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#btype_sono").val(data['myso']);
			$("#btype_sodate").val(data['sodate']);
			$("#btype_pid").val(data['mypid']);
			$("#btype_pname").val(data['pname']);
			$("#btype_gender").val(data['gender']);
			$("#btype_birthdate").val(data['bday']);
			$("#btype_age").val(data['age']);
			$("#btype_patientstat").val(data['patientstatus']);
			$("#btype_physician").val(data['physician']);
			$("#btype_procedure").val(data['procedure']);
			$("#btype_code").val(data['code']);
			$("#btype_spectype").val(data['sampletype']);
			$("#btype_serialno").val(data['serialno']);
			$("#btype_extractdate").val(data['exday']);
			$("#btype_extracttime").val(data['etime']);
			$("#btype_extractby").val(data['extractby']);
			$("#btype_result").val(data['result']);
			$("#btype_rh").val(data['rh']);
			$("#btype_result_by").val(data['performed_by']);
			$("#btype_remarks").val(data['remarks']);

			var dis = $("#bloodtypeResult").dialog({
				title: "Write Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save Result Pending Validation",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want save this data?") == true) {
							var dataString = $("#frmBloodType").serialize();
								dataString = "mod=saveBloodType&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Saved!");
										$("#frmBloodType").trigger("reset");
										dis.dialog("close");
										showValidation();
									}
								});
							}
						}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function validateBloodtype(lid,code) {

	$("#btype_date").datepicker();
	
	$.post("src/sjerp.php", {
		mod: "resultSingle",
		submod: "bloodType",
		lid: lid,
		sid: Math.random() },
		function(data) {

			$("#btype_sono").val(data['myso']);
			$("#btype_sodate").val(data['sodate']);
			$("#btype_pid").val(data['mypid']);
			$("#btype_pname").val(data['pname']);
			$("#btype_gender").val(data['gender']);
			$("#btype_birthdate").val(data['bday']);
			$("#btype_age").val(data['age']);
			$("#btype_patientstat").val(data['patientstatus']);
			$("#btype_physician").val(data['physician']);
			$("#btype_procedure").val(data['procedure']);
			$("#btype_code").val(data['code']);
			$("#btype_spectype").val(data['sampletype']);
			$("#btype_serialno").val(data['serialno']);
			$("#btype_extractdate").val(data['exday']);
			$("#btype_extracttime").val(data['etime']);
			$("#btype_extractby").val(data['extractby']);
			$("#btype_result").val(data['result']);
			$("#btype_rh").val(data['rh']);
			$("#btype_result_by").val(data['performed_by']);
			$("#btype_remarks").val(data['remarks']);

			var dis = $("#bloodtypeResult").dialog({
				title: "Write Result",
				width: 540,
				resizeable: false,
				modal: true,
				buttons: [
					{
						text: "Save & Confirm Result",
						icons: { primary: "ui-icon-check" },
						click: function() {
							var msg = '';
	
							if(confirm("Are you sure you want confirm and publish this result?") == true) {
							var dataString = $("#frmBloodType").serialize();
								dataString = "mod=validateBloodType&" + dataString;
								$.ajax({
									type: "POST",
									url: "src/sjerp.php",
									data: dataString,
									success: function() {
										alert("Result Successfully Saved!");
										$("#frmBloodType").trigger("reset");
										dis.dialog("close");
										showValidation();
									}
								});
							}
						}
					},
					{
						text: "Close",
						icons: { primary: "ui-icon-closethick" },
						click: function() { $(this).dialog("close"); }
					}
				]
			});

		},"json"
	);
}

function collectVitals(so_no,pid) {
	$("#pemeresult").html("<iframe id='frmPEME' frameborder=0 width='100%' height='100%' src='result.peme.php?so_no="+so_no+"&pid="+pid+"&sid="+Math.random()+"'></iframe>");
	$("#pemeresult").dialog({
		title: "Physical/Medical Examination Form",
		width: xWidth,
		height: 720,
		resizeable: false,
		modal: false,
		buttons: [
			{
				text: "Save Data",
				icons: { primary: "ui-icon-check" },
				click: function() {
					var msg = '';

					if(confirm("Are you sure you want save this data?") == true) {
						var dataString = $('#frmPEME').contents().find('#frmVitals').serialize();
						dataString = "mod=saveVitals&" + dataString;
						$.ajax({
							type: "POST",
							url: "src/sjerp.php",
							data: dataString,
							success: function() {
								alert("Result Successfully Saved!");
							}
						});
					}
				}
			},
			{
				text: "Attach Patient's Signature",
				icons: { primary: "ui-icon-pencil" },
				click: function() {
					
					var so_no = $('#frmPEME').contents().find('#pe_sono').val();
					var pid = $('#frmPEME').contents().find('#pe_pid').val();

					var txtHTML = "<iframe id='frmSignature' frameborder=0 width='100%' height='100%' src='result.signature.php?so_no="+so_no+"&pid="+pid+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#signaturepad").html(txtHTML);
					$("#signaturepad").dialog({title: "Signature Pad", width: 560, height: 320, resizable: false, modal: true });
				}
			},
			{
				text: "Print Form",
				icons: { primary: "ui-icon-print" },
				click: function() {
					
					var so_no = $('#frmPEME').contents().find('#pe_sono').val();
					var pid = $('#frmPEME').contents().find('#pe_pid').val();

					var txtHTML = "<iframe id='prntXrayResult' frameborder=0 width='100%' height='100%' src='print/result.peme.php?so_no="+so_no+"&pid="+pid+"&sid="+Math.random()+"&sid="+Math.random()+"'></iframe>";
					$("#report1").html(txtHTML);
					$("#report1").dialog({title: "Print - Physical/Medical Examinition Form", width: 560, height: 620, resizable: true }).dialogExtend({
						"closable" : true,
						"maximizable" : true,
						"minimizable" : true
					});


				 }
			},
			{
				text: "Close",
				icons: { primary: "ui-icon-closethick" },
				click: function() { $(this).dialog("close"); }
			}
		]
	});
}

function showDSR() {
	$("#dsr_dtf").datepicker(); $("#dsr_dt2").datepicker();
	$("#dsr").dialog({title: "Detailed Sales & Collection Report", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateDSR() {
	var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/dsr.php?dtf="+$("#dsr_dtf").val()+"&dt2="+$("#dsr_dt2").val()+"&item="+$("#dsr_item").val()+"&cid="+$("#dsr_cid").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report3").html(txtHTML);
	$("#report3").dialog({title: "Detailed Sales Report", width: 640, height: 520, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}

function generateDSRX() {
	window.open("export/dsr.php?dtf="+$("#dsr_dtf").val()+"&dt2="+$("#dsr_dt2").val()+"&item="+$("#dsr_item").val()+"&cid="+$("#dsr_cid").val()+"&sid="+Math.random()+"","Inventory Stockcard","location=1,status=1,scrollbars=1,width=640,height=720");
}

function soSummary(so_no) {
	$("#so_dtf").datepicker(); $("#so_dt2").datepicker();
	$("#soSummary").dialog({title: "Sales Order Summary", width: 400 }).dialogExtend({
		"closable" : true,
	    "maximizable" : false,
	    "minimizable" : true
	});
}

function generateSoSummary() {
	var txtHTML = "<iframe id='frmglsched' frameborder=0 width='100%' height='100%' src='reports/so_summary.php?dtf="+$("#so_dtf").val()+"&dt2="+$("#so_dt2").val()+"&cid="+$("#so_cid").val()+"&sid="+Math.random()+"'></iframe>";
	$("#report3").html(txtHTML);
	$("#report3").dialog({title: "Sales Order Summary", width: 640, height: 520, resizable: true }).dialogExtend({
		"closable" : true,
		"maximizable" : true,
		"minimizable" : true
	});
}