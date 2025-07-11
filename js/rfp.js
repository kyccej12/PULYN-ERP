function getSummaryValues(rfp_no) {
	$.post("rfp.datacontrol.php", { mod: "getSummaryValues", rfp_no: $("#rfp_no").val(), sid: Math.random() }, function(jsonData) {
		$("#total_amount").val(jsonData['total']);
		$("#tax_withheld").val(jsonData['ewt']);
		$("#vat_amount").val(jsonData['vat']);
		$("#net_payable").val(jsonData['net']);
	},"json");
}


function saveHeader() {
	var msg = "";
	if($("#cSelected").val() == "N") { msg = msg + "- You did not specify supplier or source for the Report<br/>"; }
	if(msg != "") {
		parent.sendErrorMessage("Unale to continue due to the follwing error(s): <br/><br/>"+msg+"");
	} else {
		$.post("rfp.datacontrol.php", { mod: "saveHeader",date_needed: $("#date_needed").val(), rfp_no: $("#rfp_no").val(), rfp_date: $("#rfp_date").val(), cid: $("#customer_id").val(), cname: $("#customer_name").val(), addr: $("#cust_address").val(), requested_by: $("#requested_by").val(), remarks: $("#remarks").val(),sid: Math.random() });
	}
}

function loadInvoices() {
	var msg="";
	if($("#proj_code").val() == "") { 
		msg+=" -Please select Project name. <br>";
	}
	if($("#customer_id").val() == "") {
		msg+=" -Please select supplier first before downloading unserved Accounts Payable Vouchers.";
	}
	if(msg!=''){
		parent.sendErrorMessage(msg);
	}else {
		$.post("rfp.datacontrol.php", { mod: "getInvoices", rfp_no: $("#rfp_no").val(), cid: $("#customer_id").val(), proj_id : $("#proj_code").val() ,sid: Math.random() }, function(data) {
			if(data.length > 0) {
				$("#invoices").html(data);
				$("#invoices").dialog({title: "Accounts Payable Voucher", width: 980, height: 480, resizable: false, modal: true, buttons: {
						"Upload A.P. Voucher":  function() { loadInvoice2AP(); },
						"Close Window": function() { $(this).dialog("close"); }
					}
				 });
			} else {
				parent.sendErrorMessage("Unable to find any outstanding Vouchers Payable. Please check supplier code if it is correct...");
			}
		});
	}
}

function tagRR(el,val) {
	var obj = document.getElementById(el);
	var myURL;
	if(obj.checked == true) { var push = "Y"; } else { var push = "N"; }
	$.post("rfp.datacontrol.php", { mod: "tagRR", push: push, val: val, sid: Math.random() });
}

function loadInvoice2AP() {
	$.post("rfp.datacontrol.php", { mod: "loadInvoice2AP", rfp_no: $("#rfp_no").val(), rfp_date: $("#rfp_date").val(), cid: $("#customer_id").val(), sid: Math.random() }, function(data) {
		if(data.length > 0) {
			$("#apdetails").html(data);
			$("#invoices").dialog("close");
			getSummaryValues();
		} else { parent.sendErrorMessage("There is nothing to attach. Please make sure you have selected the documents you wish to attach..."); }
	});
}

function deleteRFPLine() {
	var radioValue = $("input[name='lineItem']:checked").val();
	if(radioValue) {
		if(confirm("Are you sure you want to delete this entry from RFP?") == true) {
			$.post("rfp.datacontrol.php", { mod: "deleteline", lid: radioValue, rfp_no: $("#rfp_no").val(), sid: Math.random() }, function(data) {
				$("#apdetails").html(data);
				getSummaryValues();
			},"html");
		}
	} else {
		parent.sendErrorMessage("Please select a line entry to remove!");
	}
}

function printRFP(rfp_no,uid) {
	
	$.post("rfp.datacontrol.php", { mod: "check4print", rfp_no: rfp_no, sid: Math.random() }, function(data) { 
		if(data > 0) {
			if(confirm("Are you sure you want to Finalize this Request for Payment?") == true) {
				$.post("rfp.datacontrol.php", { mod: "finalizeRFP", rfp_no: rfp_no, sid: Math.random() }, function() {
					parent.viewRFP(rfp_no);
				});
			}
		} else {
			 parent.sendErrorMessage("Unable to print document. No details added to this \"Request For Payment\"");
		}
	},"html");
}

function reuseRFP(rfp_no) {
	if(confirm("Are you sure you want to Recycle this document?") == true) {
		$.post("rfp.datacontrol.php", { mod: "reopenAP", rfp_no: rfp_no, sid: Math.random() }, function(){
			parent.viewRFP(rfp_no);
		});
	}
}

function reprintRFP(rfp_no,uid) {
	parent.printRFP(rfp_no,uid);
}

function reopenRFP(rfp_no) {
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
	$.post("rfp.datacontrol.php", { mod: "reopenRFP", rfp_no: rfp_no, trace_no : $("#trace_no").val(), remarks: $("#cancel_remarks").val(), sid: Math.random() }, function(){
			alert("Request for Payment set to Active!");
			parent.viewRFP(rfp_no);
	});
}

function cancelRFP(rfp_no){
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
	$.post("rfp.datacontrol.php", { mod: "cancel", rfp_no: rfp_no, remarks: $("#cancel_remarks").val(), sid: Math.random() }, function(){
			alert("Request for Payment Cancelled!");
			parent.viewRFP(rfp_no);
	});
}
		