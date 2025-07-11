function checkLockDate(el,myDate,prevDate) {
	$.post("src/sjerp.php", { mod: "checkDateLock", myDate: myDate, sid: Math.random() }, function(ret) {
		if(ret != "Ok") {
			parent.sendErrorMessage("Unable to change document as the period you have specified appears to already have been marked as locked!");
			document.getElementById(el).value = prevDate;
		}
	},"html");
}

function getTotals() {
	$.post("apv.datacontrol.php", { mod: "getTotals", apv_no: $("#apv_no").val(), sid: Math.random() }, function(data) {
		$("#grossAmount").val(data['gross']);
		$("#netOfVat").val(data['netOfVat']);
		$("#vat").val(data['vat']);
		$("#taxWithheld").val(data['ewt']);
		$("#netPayable").val(data['netPayable']);
		$("#appliedAmount").val(data['applied']);
		$("#balanceDue").val(data['balance']);
	},"json");
}

function saveAPVHeader() {
	var msg = "";
	if($("#cSelected").val() == "N") { msg = msg + "- Invalid or missing Supplier Information<br/>"; }
	if(msg != "") {
		parent.sendErrorMessage("Unable to continue due to the follwing error(s): <br/><br/>"+msg+"");
	} else {
		$.post("apv.datacontrol.php", { mod: "saveHeader", trace_no: $("#trace_no").val(), apv_no: $("#apv_no").val(), apv_date: $("#apv_date").val(), cid: $("#customer_id").val(), cname: $("#customer_name").val(), addr: $("#cust_address").val(), terms: $("#terms").val(), atc_code: $("#atc_code").val(), remarks: $("#remarks").val(), sid: Math.random() },function(data){
			if(data == "error") {
				parent.sendErrorMessage("- The system encountered an error while trying to save this document. Please refresh page and try again.");
			} else {	parent.popSaver(); }
		});
	}
}

function loadInvoices() {
	if($("#customer_id").val() == "") {
		parent.sendErrorMessage("ERROR: You must specify the supplier information before downloading posted RR(s).");
	} else {
		$.post("apv.datacontrol.php", { mod: "getInvoices", apv_no: $("#apv_no").val(), cid: $("#customer_id").val(), sid: Math.random() }, function(data) {
			if(data.length > 0) {
				$("#invoiceAttachment").html(data);
				$("#invoiceAttachment").dialog({title: "Supplier's Invoices", width: 800, height: 460, resizable: false, modal: true, buttons: {
						"Upload Invoices (Vat)":  function() { loadInvoice2AP('Y'); },
						"Upload Invoices (Non-Vat)":  function() { loadInvoice2AP('N'); },
						"Close Window": function() { $(this).dialog("close"); }
					}
				 }).dialogExtend({
					"closable" : true,
					"maximizable" : false,
					"minimizable" : true
				});
			} else {
				parent.sendErrorMessage("Unable to find outstanding RR(s) for this supplier. Make sure you have specified the correct supplier code...");
			}
		});
	}
}

function tagRR(el,val) {
	var obj = document.getElementById(el);
	var myURL;
	if(obj.checked == true) { var push = "Y"; } else { var push = "N"; }
	$.post("apv.datacontrol.php", { mod: "tagRR", push: push, val: val, sid: Math.random() });
}

function loadInvoice2AP(isVat) {
	$.post("apv.datacontrol.php", { mod: "loadInvoice2AP", trace_no: $("#trace_no").val(), apv_no: $("#apv_no").val(), apv_date: $("#apv_date").val(), atc: $("#atc_code").val(), isVat: isVat, cid: $("#customer_id").val(), sid: Math.random() }, function() {
		getTotals(); redrawDataTable();
		$("#invoiceAttachment").dialog("close");
	});
}

function deleteInvoice(ref_no) {
	if(confirm("Are you sure you want to delete this invoice from this Accounts Payable Voucher?") == true) {
		$.post("apv.datacontrol.php", { mod: "deleteInvoice", ref_no: ref_no, apv_no: $("#apv_no").val(), apv_date: $("#apv_date").val(), cid: $("#customer_id").val(), sid: Math.random() }, function(data) {
			$("#details").html(data);
			getTotals();
		},"html");
	}
}

function encodeInvoices() {
	$("#applyDivs").dialog({
			title: "Direct Purchases", 
			width: 380, 
			height: 320, 
			resizable: false, 
			buttons: {
				"Apply Invoice":  function() { applyInvoice(); },
				"Close Window": function() { $(this).dialog("close"); }
			}
	});
}

function applyInvoice() {
	var msg = "";
	var amt = parseFloat(parent.stripComma($("#app_amount").val()));
	if($("#app_docno").val() == "") { msg = msg + "- You must specify Invoice or Reference # before applying this document to AP...<br/>"; }
	if(isNaN(amt) == true || amt == '0.00' || amt == "") { msg = msg + "- Invalid amount specified...<br/>"; }

	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		$.post("apv.datacontrol.php", { mod: "addInvoice", apv_no: $("#apv_no").val(), apv_date: $("#apv_date").val(), ref_no: $("#app_docno").val(), ref_date: $("#app_docdate").val(), amount: amt, isVat: $("#app_vatable").val(), atc: $("#app_atc").val(), sid: Math.random() }, function(data) {
			$("#details").html(data);
			getTotals();
			$("#applyDivs").dialog("close");
			$("#applydocs")[0].reset();
		});
	}
}

function addDetails() {
	var msg = "";
	var ref_no = $("#ref_no").val();
	var ref_date = $("#ref_date").val();
	var acode = $("#acct_code").val();
	var adesc = $("#acct_description").val();
	var ccenter = $("#cost_center").val();
	var amount = parseFloat(parent.stripComma($("#amount").val()));

	if(acode == "") { msg = msg + "- Account Code not specified<br/>"; }
	if(adesc == "") { msg = msg + "- Account Description not specified<br/>"; }
	if(isNaN(amount) == true || amount == "") { msg = msg + "- Invalid Amount<br/>"; }
	
	if(msg != "") {
		parent.sendErrorMessage("Unable to continue due to the following error(s): <br/><br/>"+msg+"");
	} else {
		var dc = $("input:radio[name=side]:checked").val();
		$.post("apv.datacontrol.php", { mod: "insertDetail", apv_no: $("#apv_no").val(), apv_date: $("#apv_date").val(), ref_no: ref_no, ref_date: ref_date, acode: acode, adesc: adesc, dc: dc, amount: amount, ccenter: ccenter, sid: Math.random() }, function(data) {
			$("#details").html(data);
			getTotals();
			$("#ref_no").val(''); $("#ref_date").val(), $("#acct_code").val(''); $("#autodescription").val(''), $("#acct_description").val(''), $("#cost_center").val(''); $("#amount").val(''); $("#acct_description").focus();
		},"html");
	}
}

function deleteLine(lid,apv_no) {
	if(confirm("Are you sure you want to remove this entry?") == true) {
		$.post("apv.datacontrol.php", { mod: "deleteLine", lid: lid, apv_no: apv_no, sid: Math.random() }, function(data) { $("#details").html(data); getTotals(); },"html");
	}

}

function changeCostCenter(lid,cc) {
	var el = 'c_'+lid;
	$.post("apv.datacontrol.php", { mod: "iccenter", cc: cc, lid: lid, sid: Math.random() },function(data){
		document.getElementById(el).innerHTML = data;
	},"html");
}

function ichangeNa(val, lid) {
	$.post("apv.datacontrol.php", { mod: "ichangeNa", lid: lid, val: val, sid: Math.random() }, function(projName) {
		var el = 'c_'+lid;
		document.getElementById(el).innerHTML =  "<a href=\"#\" style=\"text-decoration: none; color: black;\" onclick=\"javascript: changeCostCenter("+lid+",'"+val+"');\">"+projName+"</a>";
	},"html");
}

function finalizeAPV(apv_no,uid) {
	if(confirm("Are you sure you want to finalize this voucher?") == true) {
		$.post("apv.datacontrol.php", { mod: "check4print", apv_no: apv_no, sid: Math.random() }, function(data) {
			if(data == "noerror") {
				parent.viewAP(apv_no);
			} else {
				if(data == "waySulod") { parent.sendErrorMessage("Unable to print document. No details added to this Accounts Payable Voucher"); }
				if(data == "DiBalanse") { parent.sendErrorMessage("Unable to print document. Please check journal entries and make sure that both debit and credit values are equal/balanced"); }
			}
		},"html");
	}
}

function reopenAP(apv_no) {
	var amountApplied = parseFloat($("#amountApplied").val());
	
	if(amountApplied > 0) {
		$.post("apv.datacontrol.php", { mod: "getApplied", apv_no: $("#apv_no").val(), sid: Math.random() }, function(data) {
			parent.sendErrorMessage("- It appears that payments were already made to this Accounts Payable Voucher. Please check the following documents: <br/><br/>"+data+"<br/>Existence of the documents above may prevent you from making general changes to this document.");
		},"html");
	} else {
		if(confirm("Are you sure you want to set this document to active status?") == true) {
			$.post("apv.datacontrol.php", { mod: "reopenAP", apv_no: apv_no, sid: Math.random() }, function() {
				location.reload();
			});
		}
	}
}

function cancelAPV(apv_no) {
	if(confirm("Are you sure you want to Cancel this document?") == true) {
		$.post("apv.datacontrol.php", { mod: "cancel", apv_no: apv_no, sid: Math.random() }, function(){
			alert("Accounts Payable Voucher Successfully Cancelled!");
			location.reload();
		});
	}
}

function reuseAP(apv_no) {
	if(confirm("Are you sure you want to Recycle this document?") == true) {
		$.post("apv.datacontrol.php", { mod: "reopenAP", apv_no: apv_no, sid: Math.random() }, function(){
			location.reload();
		});
	}
}