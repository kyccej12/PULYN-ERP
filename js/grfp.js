function saveGRFP2() {
	$.post("grfp.datacontrol.php", { 
		mod: "saveHeader", 
		grfp_no: $("#grfp_no").val(), 
		grfp_date: $("#grfp_date").val(), 
		emp_id: $("#employee_id").val(), 
		emp_name: $("#emp_name").val(),
		dept: $("#department").val(),
		costcenter: $("#costcenter").val(),
		purpose : $("#grfp_purpose").val(),
		remarks :$("#remarks").val() ,
		payee : $("#payee").val(),
		amount : $("#grfp_amount").val().replace(/,/g, ''),
		date_needed : $("#date_needed").val(),
		payeeid : $("#payeeid").val()
	});
}

function saveGRFP() {
	var msg = "";		
	var amount = parent.stripComma($("#grfp_amount").val());
	if($("#payeeid").val() == "") { msg = msg + "- Invalid Payee. Please make sure that you have the correct payee for disbursement requirements & liquidation.<br/>"; }
	if($("#grfp_purpose").val() == "") { msg = msg + "- Please indicate the purpose of this petty cash request.<br/>"; }
	if(isNaN(amount) == true) { msg = msg + "- You have specified and invalid amount.<br/>"; }
	if($("#emp_name").val() == "") { msg = msg + "- Please specify Requestor's name of this petty cash request.<br/>"; }
	
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		$.post("grfp.datacontrol.php", { 
			mod: "saveHeader", 
			grfp_no: $("#grfp_no").val(), 
			grfp_date: $("#grfp_date").val(), 
			emp_id: $("#employee_id").val(), 
			emp_name: $("#emp_name").val(),
			dept: $("#department").val(),
			costcenter: $("#costcenter").val(),
			purpose : $("#grfp_purpose").val(),
			remarks :$("#remarks").val() ,
			payee : $("#payee").val(),
			amount : $("#grfp_amount").val().replace(/,/g, ''),
			date_needed : $("#date_needed").val(),
			payeeid : $("#payeeid").val()
		},function(data){ parent.viewGRFP(data);  });
	}
}

function printGRFP(){
	var msg ="";
	var amount = parent.stripComma($("#grfp_amount").val());
	
	var amount = parent.stripComma($("#grfp_amount").val());
	if($("#payeeid").val() == "") { msg = msg + "- Invalid Payee. Please make sure that you have the correct payee for disbursement requirements & liquidation.<br/>"; }
	if($("#grfp_purpose").val() == "") { msg = msg + "- Please indicate the purpose of this petty cash request.<br/>"; }
	if(isNaN(amount) == true) { msg = msg + "- You have specified and invalid amount.<br/>"; }
	if($("#emp_name").val() == "") { msg = msg + "- Please specify Requestor's name of this petty cash request.<br/>"; }

	if(msg!=""){
		parent.sendErrorMessage(msg);
	}else{
		if(confirm("Are you sure you want to finalize this Request for Payment?") == true) {
			saveGRFP2();
			$.post('grfp.datacontrol.php',{
				mod : 'finalizeGRFP',
				grfp_no: $("#grfp_no").val(),
				sid: Math.random() 
			},function(data){
				parent.viewGRFP($("#grfp_no").val());
			});
		}
	}
	
}

function reopenGRFP(rfp_no) {
	
	$("#cancel_box").dialog({title: "Reopen Document", width: 340, height: 180, resizable: false, modal: true, buttons: {
			"ReOpen Document":  function() { reOPenProceed(rfp_no); },
			"Close": function() { $(this).dialog("close"); }
		}
	 }).dialogExtend({
		"closable" : true,
		"maximizable" : false,
		"minimizable" : true
	});
	
}
function reOPenProceed(rfp_no){
	$.post("grfp.datacontrol.php", { mod: "reopenGRFP", grfp_no: $("#grfp_no").val(), trace_no : $("#trace_no").val(), remarks: $("#cancel_remarks").val(), sid: Math.random() }, function(){
			alert("Request for Payment set to Active!");
			parent.viewGRFP($("#grfp_no").val());
	});
}
function cancelGRFP(rfp_no){
	
	$("#cancel_box").dialog({title: "Cancel Document", width: 340, height: 180, resizable: false, modal: true, buttons: {
			"Cancel Document":  function() { cancelProceed(rfp_no); },
			"Close": function() { $(this).dialog("close"); }
		}
	 }).dialogExtend({
		"closable" : true,
		"maximizable" : false,
		"minimizable" : true
	});
}

function cancelProceed(rfp_no){
	$.post("grfp.datacontrol.php", { mod: "cancel", grfp_no: $("#grfp_no").val(), remarks: $("#cancel_remarks").val(), sid: Math.random() }, function(){
			alert("Request for Payment Cancelled!");
			parent.viewGRFP($("#grfp_no").val());
	});
}

		
		
		
		