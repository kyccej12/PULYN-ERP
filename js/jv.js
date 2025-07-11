/******** jOURNAL Voucher ********/
function showLoaderMessage() {
	$("#loaderMessage").dialog({ width: 400, height: 150, closable: false, modal: true,  open: function(event, ui) {
        $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
    }});
}

function checkLockDate(el,myDate,prevDate) {
	$.post("src/sjerp.php", { mod: "checkDateLock", myDate: myDate, sid: Math.random() }, function(ret) {
		if(ret != "Ok") {
			parent.sendErrorMessage("Unable to change document as the period you have specified appears to already have been marked as locked!");
			document.getElementById(el).value = prevDate;
		}
	},"html");
}

function contactlookup(inputString,el) {
	if(inputString.length == 0) {
		$('#suggestions').hide();
	} else {
		var op = $("#"+el+"").offset();
		$.post("contactlookup.php", {queryString: ""+inputString+"" }, function(data){
		if(data.length > 0) {
			$('#suggestions').css({top: op.top+20, left: op.left});
			$('#suggestions').show();
			$('#autoSuggestionsList').html(data);
		} else { $("#suggestions").hide(); }
		});
	}
}

function pickContact(cid,cname,addr,terms) {
	$("#cSelected").val('Y');
	$("#customer_id").val(cid);
	$("#customer_name").val(decodeURIComponent(cname));
	$("#cust_address").val(decodeURIComponent(addr));
}

function acctlookup(inputString,el,mod) {
	if(inputString.length == 0) {
		$('#suggestions').hide();
	} else {
		var op = $("#"+el+"").offset();
		$.post("acctlookup.php", {queryString: ""+inputString+"", element_mod: ""+mod+""}, function(data){
		if(data.length > 0) {
			$('#suggestions').css({top: op.top+20, left: op.left});
			$('#suggestions').show();
			$('#autoSuggestionsList').html(data);
		} else { $("#suggestions").hide(); }
		});
	}
}

function getTotals(j_no) {
	$.post("jv.datacontrol.php", { mod: "getTotals", j_no: j_no, sid: Math.random() }, function(tot) {
		$("#dbTotal").val(tot['db']);
		$("#crTotal").val(tot['cr']);
		$("#noLines").val(tot['lines']);
	},"json");
}

function selectAcct(acct_code,acct_desc) {
$("#acct_code").val(acct_code);
$("#acct_description").val(decodeURIComponent(acct_desc));
}

function selectAcct2(acct_code,acct_desc) {
	$("#applied_acct").val(acct_code);
	$("#applied_acct_title").html(decodeURIComponent(acct_desc));
}

function div_acctAuto(el,inputString) {
	if(inputString.length == 0) {
		$('#suggestions').hide();
	} else {
		var op = $("#"+el+"").offset();
		$.post("acctlookup2.php", {queryString: ""+inputString+"", }, function(data){
		if(data.length > 0) {
			$('#suggestions').css({top: op.top+20, left: op.left});
			$('#suggestions').show();
			$('#autoSuggestionsList').html(data);
		} else { $("#suggestions").hide(); }
		});
	}
}

function saveJVHeader() {
	if($("#remarks").val() == "") {
		parent.sendErrorMessage("Please fill up \"Explanation\" and state the purpose of this journal voucher...")
	} else {
		$.post("jv.datacontrol.php", { mod: "saveHeader", j_no: $("#j_no").val(), trace_no : $("#trace_no").val(),j_date: $("#j_date").val(), ca_refno: $("#ca_refno").val(), ca_date: $("#ca_date").val(), remarks: $("#remarks").val(), sid: Math.random() }, function(data) {
			$("#j_no").val(data);
		},"html");
		
	}
}

function computeBalance(amount) {
	var amount = parseFloat(amount);

	if(isNaN(amount) == true) {
		parent.sendErrorMessage("Invalid amount specified");
	} else {
		if(amount < 0) {
			parent.sendErrorMessage("You must specify amount in absolute value...");
			$("#app_amount").val('');
			$("#app_amount").focus();
		} else if(amount > parseFloat(parent.stripComma($("#app_balance").val()))) {
			parent.sendErrorMessage("Amount applied is more than the balance of the specified document...");
			$("#app_amount").val('');
			$("#app_amount").focus();
		} else {
			var balance = parseFloat(parent.stripComma($("#app_balance").val())) - amount;
				balance = balance.toFixed(2);
			 $("#app_balance").val(parent.kSeparator(balance));
		}
	}
}


function deleteInvoice(ref_no) {
	if(confirm("Are you sure you want to delete this invoice from this Accounts Payable Voucher?") == true) {
		$.post("jv.datacontrol.php", { mod: "deleteInvoice", ref_no: ref_no, cv_no: $("#cv_no").val(), cid: $("#customer_id").val(), sid: Math.random() }, function(data) {
			$("#cvdetails").html(data);
			getTotals($("#j_no").val());
		},"html");
	}
}

function encodeInvoices() {
	$("#applyInvoice").dialog({
		title: "Supplier's Invoices", 
		width: 540,  
		resizable: false, 
			buttons: {
			"Apply Invoice":  function() { applyInvoice(); },
			"Close Window": function() { $(this).dialog("close"); }
		}
	});
}

function applyInvoice() {
	var msg = "";
	var amt = parseFloat(parent.stripComma($("#app_invoice_amount").val()));
	if($("#app_invoice_docno").val() == "") { msg = msg + "- You must specify Invoice or Reference # before applying this document to CV...<br/>"; }
		if(isNaN(amt) == true || amt == '0.00' || amt == "") { msg = msg + "- Invalid amount specified...<br/>"; }
		if(msg != "") {
			parent.sendErrorMessage(msg);
	} else {
		$.post("jv.datacontrol.php", { mod: "addInvoice", j_no: $("#j_no").val(), j_date: $("#j_date").val(), sname: $("#app_invoice_name").val(), saddr: $("#app_invoice_address").val(), stin: $("#app_invoice_tin").val(), ref_no: $("#app_invoice_docno").val(), ref_date: $("#app_invoice_docdate").val(), amount: amt, dbAcct: $("#app_debit_acct").val(), isVat: $("#app_vatable").val(), atc: $("#app_atc").val(), sid: Math.random() }, function(data) {
			$("#jdetails").html(data);
			$("#applyInvoice").dialog("close");
			$("#frmInvoice")[0].reset();
			getTotals($("#j_no").val());
		});
	}
}

function deleteInvoice(ref_no,ref_type) {
	if(confirm("Are you sure you want to delete this invoice from this Voucher?") == true) {
		$.post("jv.datacontrol.php", { mod: "deleteInvoice", ref_no: ref_no, ref_type: ref_type, j_no: $("#j_no").val(), j_date: $("#j_date").val(), sid: Math.random() }, function(data) {
			$("#jdetails").html(data);
			getTotals($("#j_no").val());
		},"html");
	}
}

function addDetails() {
	
	if($("#j_no").val() != '') {
	
		var msg = "";
		var ref_no = $("#ref_no").val();
		var ref_date = $("#ref_date").val();
		var ref_type = $("#ref_type").val();
		var cid = $("#customer_id").val();
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
			$.post("jv.datacontrol.php", { mod: "insertDetail", j_no: $("#j_no").val(), trace_no : $("#trace_no").val() ,j_date: $("#j_date").val(), ref_no: ref_no, ref_date: ref_date, ref_type: ref_type, cid: cid, acode: acode, adesc: adesc, dc: dc, amount: amount, ccenter: ccenter, atc_code: $("#atc_code").val(), bank: $("#bank").val(), sid: Math.random() }, function(data) {
				$("#jdetails").html(data); getTotals($("#j_no").val());
				$("#ref_no").val(''); $("#ref_date").val(), $("#acct_code").val(''); $("#customer_id").val(''); $("#acct_description").val(''), $("#cost_center").val(''); $("#amount").val('');
			},"html");
		}
	} else {
		
		parent.sendErrorMessage("- Please save initial changes made to this voucher before adding detailed entries.");
	}
}

function deleteLine(lid,j_no) {
	if(confirm("Are you sure you want to remove this entry?") == true) {
		$.post("jv.datacontrol.php", { mod: "deleteLine", lid: lid, j_no: j_no, sid: Math.random(), trace_no : $("#trace_no").val() }, function(data) { $("#jdetails").html(data); getTotals($("#j_no").val()); },"html");
	}
}

function changeCostCenter(lid,cc) {
	var el = 'c_'+lid;
	$.post("jv.datacontrol.php", { mod: "iccenter", cc: cc, lid: lid, sid: Math.random() },function(data){
		document.getElementById(el).innerHTML = data;
	},"html");
	}

function ichangeNa(val, lid) {
	$.post("jv.datacontrol.php", { mod: "ichangeNa", lid: lid, val: val, sid: Math.random() }, function(cost_center) {
		var el = 'c_'+lid;
		document.getElementById(el).innerHTML =  "<a href=\"#\" style=\"text-decoration: none; color: black;\" onclick=\"javascript: changeCostCenter("+lid+",'"+val+"');\">"+cost_center+"</a>";
	},"html");
}

function finalizeJV(j_no,uid) {
	if(confirm("Are you sure you want to Finalize & Post this document to the General Ledger?") == true) {
		showLoaderMessage();
		var j_no2 = $("#j_no").val();
		$.post("jv.datacontrol.php", { mod: "check4print", j_no: j_no2,trace_no : $("#trace_no").val(), sid: Math.random() }, function(data) { 
			if(data == "noerror") {
				parent.viewJV(j_no2);
			} else {
				$("#loaderMessage").dialog("close");
				if(data == "waySulod") { parent.sendErrorMessage("Unable to finalize document. No details added to this Journal Voucher"); }
				if(data == "DiBalanse") { parent.sendErrorMessage("Unable to finalize document. Please check journal entries and make sure that both debit and credit grand total values are equal/balance"); }
			}
		},"html");
	}
}

function reopenJV(j_no){
	if(confirm("Are you sure you want to set this document to active status?") == true) {
		showLoaderMessage();
		$.post("jv.datacontrol.php", { mod: "checkB4Reopen", j_no: j_no,trace_no : $("#trace_no").val(), sid: Math.random() }, function(data) {
			if(data == "notOk") {
				$("#loaderMessage").dialog("close");
				if(confirm("This document seems to have undergone the bank reconciliation process. Setting this to active may affect reconciled figures. Do you still wish to contineu?") == true) {
					$.post("jv.datacontrol.php", { mod: "reopenJV", j_no: j_no, sid: Math.random() }, function() { location.reload(); });
				}
			} else {
				$.post("jv.datacontrol.php", { mod: "reopenJV", j_no: j_no, sid: Math.random() }, function() { location.reload(); });
			}
		},"html");
	}
}

function cancelJV(j_no){
	if(confirm("Are you sure you want to cancel this document?") == true) {
		$.post("jv.datacontrol.php", { mod: "cancel", j_no: j_no, sid: Math.random() }, function(){
			alert("Journal Voucher Successfully Cencelled!");
			location.reload();
		});
	}
}

function unlinkJV(j_no){
	if(confirm("Are you sure you want to unlink this document to its source document?") == true) {
		$.post("jv.datacontrol.php", { mod: "unlinkJV", j_no: j_no, sid: Math.random() }, function() {
			location.reload();
		});
	}
}

function applyDocuments() {
	$("#applyDivs").dialog({title: "Apply To Other Documents", width: 480, resizable: false, modal: true });
}

function checkForDoc(doctype) {
	if(doctype == '') {
		clearMe();
	} else {
		if($("#app_acct").val() != "") {
			if(isNaN($("#app_acct").val()) == true) { var acct = $("#app_acct").val(); } else { var acct = $("#app_acct").val(); }
			$.post("src/sjerp.php", { mod: "verifyACCT", acct: acct, sid: Math.random() }, function(data) { 
				if(data == "NotFound") { 
					parent.sendErrorMessage("- Invalid Account!");
					clearMe();
				} else {
					$.post("src/sjerp.php", { mod: "checkForDoc", doctype: doctype, cid: $("#app_client").val(), acct: acct, sid: Math.random() }, function(ret) {
						$("#balances").html(ret);
						$("#app_docno").val(''); $("#app_docdate").val(''); $("#app_amount").val('');
					},"html");
				}
			},"html");
		} else { parent.sendErrorMessage("- Please Indicate Account Code for the document to be applied..."); $("#app_acct").val(''); clearMe(); }
	}
}

function selectDocument(a,b,c,d,e,f) {
	$("#app_docno").val(a); $("#app_docdate").val(b); $("#app_amount").val(parent.kSeparator(d)); $("#app_side").val(e); $("#app_lid").val(f);
}

function applyNow() {
	if($("#app_side").val() == "") {
		parent.sendErrorMessage("- Invalid Document Input. Please select a document from the result if there is any...");
	} else {
		if(isNaN(parent.stripComma($("#app_amount").val())) == true) { 
			parent.sendErrorMessage("- Invalid amount..."); 
		} else {
			$.post("jv.datacontrol.php", { mod: "saveAppliedDoc", trace_no: $("#trace_no").val(), j_no: $("#j_no").val(), j_date: $("#j_date").val(), ref_no: $("#app_docno").val(), ref_type: $("#app_doctype").val(), ref_date: $("#app_docdate").val(), client: $("#app_client").val(), acct: $("#app_acct").val(), side: $("#app_side").val(), amount: $("#app_amount").val(), lid: $("#app_lid").val(), sid: Math.random() }, function(data) {
				$("#jdetails").html(data);
				getTotals($("#j_no").val())
				clearMe();
			},"html");
		}
	}
}


function clearMe() {
	$(document.applydocs)[0].reset();
	$("#balances").html('');
}
