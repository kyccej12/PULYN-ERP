/******** Check Voucher ********/
function checkLockDate(el,myDate,prevDate) {
	$.post("src/sjerp.php", { mod: "checkDateLock", myDate: myDate, sid: Math.random() }, function(ret) {
		if(ret != "Ok") {
			parent.sendErrorMessage("Unable to change document as the period you have specified appears to already have been marked as locked!");
			document.getElementById(el).value = prevDate;
		}
	},"html");
}

function getTotals() {
	$.post("cv.datacontrol.php", { mod: "getTotals", cv_no: $("#cv_no").val(), sid: Math.random() }, function(data) {
		$("#grossAmount").val(data['gross']);
		$("#netOfVat").val(data['netOfVat']);
		$("#vat").val(data['vat']);
		$("#taxWithheld").val(data['ewt']);
		$("#netAmount").val(data['netAmount']);
	},"json");
}


function saveCVHeader() {
	var msg  = "";
	if($("#customer_id").val() == "") { msg = msg + "- Invalid or missing Payee Information<br/>"; }
	if($("#fundsource").val() != "") {
		if($("#fundsource").val() != '10103') {
			if($("#check_no").val() == "") { msg = msg + "- Invalid Check No.<br/>"; }
			if($("#check_date").val() == "") { msg = msg + "- Invalid Check Date<br/>"; }
		}
	} else { msg = msg + "- Please select Source of Fund for this disbursement<br/>"; }
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		$.post("cv.datacontrol.php", { mod: "checkDuplicateNo", cv_no: $("#cv_no").val(), check_no: $("#check_no").val(), source: $("#fundsource").val(), sid: Math.random() }, function(ret) {
			if(ret[0] == "0") {
				$.post("cv.datacontrol.php", { mod: "saveHeader", cv_no: $("#cv_no").val(), ccode: $("#customer_id").val(), cname: $("#customer_name").val(), address: $("#cust_address").val(), source: $("#fundsource").val(), bank: $("#fundsource").val(), check_no: $("#check_no").val(), check_date: $("#check_date").val(), cv_date: $("#cv_date").val(), remarks: $("#remarks").val(), ca_refno: $("#ca_refno").val(), ca_date: $("#ca_date").val(), sid: Math.random() }, function() { parent.popSaver(); });
			} else {
				parent.sendErrorMessage("- It appears that the Check No. specified in this Disbursement Voucher has already been used by <b>CV No. " + ret[1] + "</b> dated <b>" + ret[2] + "</b> amounting to <b>P"+ret[3]+"</b>."); 
			}
		},"json");
	}
}

function getCheckSeries(source) {
	if(source != '10103') {
		$.post("cv.datacontrol.php", { mod: "getCheckSeries", source: source, cv_no: $("#cv_no").val(), sid: Math.random() }, function(data) {
			$("#check_no").val(data[0]);
		},"json");
	}
}

function loadInvoices() {
	if($("#customer_id").val() == "") {
		parent.sendErrorMessage("ERROR: Please select payee first before downloading any unpaid Vouchers Payable.");
	} else {
		$.post("cv.datacontrol.php", { mod: "getInvoices", cv_no: $("#cv_no").val(), cid: $("#customer_id").val(), sid: Math.random() }, function(data) {
		if(data.length > 0) {
			$("#invoices").html(data);
			$("#invoices").dialog({title: "Supplier's Vouchers Payable", width: 720, height: 360, resizable: false, modal: true, buttons: { "Upload Vouchers Payable":  function() { loadAP2CV(); }, "Close Window": function() { $(this).dialog("close"); }}
			}).dialogExtend({
			"closable" : true,
		    "maximizable" : false,
		    "minimizable" : true });
		} else {
				parent.sendErrorMessage("Unable to find any unpaid Vouchers Payable for this payee. Make sure you have specified the correct Payee...");
			}
		});
	}
}

function loadAP2CV() {
	$.post("cv.datacontrol.php", { mod: "loadAP2CV", cv_no: $("#cv_no").val(), cv_date: $("#cv_date").val(), bank: $("#fundsource").val(), cid: $("#customer_id").val(), sid: Math.random() }, function(data) {
		if(data.length > 0) {
			$("#details").html(data);
			getTotals();
			$("#invoices").dialog("close");
		} else { parent.sendErrorMessage("Unable to continue. There is nothing to attach. Please make sure you have selected the invoices you wish to attach..."); }
	});
}

function tagRR(el,val) {
	var obj = document.getElementById(el);
	var myURL;
	if(obj.checked == true) { var push = "Y"; } else { var push = "N"; }
		$.post("cv.datacontrol.php", { mod: "tagRR", push: push, val: val, sid: Math.random() });
}

function loadRFP(){
	if($("#fundsource").val() == ''){
		parent.sendErrorMessage("-Please select fundsource.");
	}else{
		$("#loaderMessage").dialog({title: "Processing....", width: 540, modal: true, resizable: false }).dialogExtend({ "closable" : false });
		$.post("cv.datacontrol.php", { mod: "getRFP", payee : $("#customer_id").val(),sid: Math.random() }, function(data) {
			if(data.length > 0) {
				$("#loaderMessage").dialog("close");
				$("#invoices").html(data);
				$("#invoices").dialog({title: "Request for Payment (TERMS)", width: 980, height: 480, resizable: false, modal: true, buttons: {
						"Upload RFP":  function() { loadslctdRFP(); },
						"Close Window": function() { $(this).dialog("close"); }
					}
				 });
			} else {
				$("#loaderMessage").dialog("close");
				parent.sendErrorMessage("Unable to find any outstanding Request for Payment...");
			}
		});
	}
}

function loadslctdRFP(){
 	var sRFP = $("input[name=dummy_rfp]:checked").val(); 
 	if(sRFP != undefined){
 		var tmp_obj = sRFP.split("|");
 		var rfp_no = tmp_obj[1];
 		
 		$.post("cv.datacontrol.php", { mod: "loadSelectedRFP" , rfp_no: rfp_no, cv_no : $("#cv_no").val(), bank: $("#fundsource").val(), cv_date: $("#cv_date").val(), sid: Math.random() }, function(data) {
			if($("#customer_id").val() == '') {			
				$("#customer_id").val(data['supplier']);
				$("#customer_name").val(data['supplier_name']);
				$("#cust_address").val(data['supplier_addr']);
				$("#remarks").val($("#remarks").val()+data['remarks']+' / ');
			}
			$("#invoices").dialog("close"); 
			refreshDetails();
		},'json');
 	}else{
 		parent.sendErrorMessage("Plese select a document to upload...");
 	}
}

function loadGRFP(){
	if($("#fundsource").val() == "") {
		parent.sendErrorMessage("Unable to continue as you have yet to identify Source of Fund for this voucher");
	} else {
		$("#loaderMessage").dialog({title: "Processing....", width: 480, modal: true, resizable: false }).dialogExtend({ "closable" : false });
		$.post("cv.datacontrol.php", { mod: "getGRFP" , proj_code: $("#proj_code").val(), payee : $("#customer_id").val(), trace_no : $("#trace_no").val() , sid: Math.random() }, function(data) {
			data = data.trim();
			if(data.length > 0) {
				$("#loaderMessage").dialog("close");
				$("#invoices").html(data);
					$("#invoices").dialog({title: "Request for Payment (Cash)", width: 800, height: 480, resizable: false, modal: true, buttons: {
						"Upload RFP":  function() { loadslctdGRFP();  $(this).dialog("close"); },
						"Close Window": function() { $(this).dialog("close"); }
					}
				 });
			} else {
				$("#loaderMessage").dialog("close");
				parent.sendErrorMessage("Unable to find any outstanding Request for Payment...");
			}
		});
	}
}

function loadslctdGRFP(){
	$("*").css("cursor", "progress");
 	var sRFP = $("input[name=dummy_grfp]:checked").val(); 
 	if(sRFP != undefined){
 		
		var tmp_obj = sRFP.split("|");
 		var grfp_no = tmp_obj[0];
		var grfp_date = tmp_obj[1];
		var amount = tmp_obj[2];
		
		$.post("cv.datacontrol.php", { mod: "loadSelectedGRFP", cv_no: $("#cv_no").val(), cv_date: $("#cv_date").val(), bank: $("#fundsource").val(), grfp_no: grfp_no, grfp_date: grfp_date, amount: amount, sid: Math.random() }, function(data) {
			$("#customer_id").val(data['payee_code']);
			$("#customer_name").val(data['payee']);
			$("#cust_address").val(data['cust_address']);
			$("#remarks").val($("#remarks").val()+data['remarks']+' / ');
			refreshDetails();
		},"json");
		
	} else {
		parent.sendErrorMessage("Please select a document to upload...");
	}
	$("*").css("cursor", "default");
}

function refreshDetails() {
	$.post("cv.datacontrol.php", { mod: "refreshDetails", cv_no: $("#cv_no").val(), sid: Math.random() }, function(data) {
		$("#details").html(data);
		getTotals();
	},"html");
}

function encodeInvoices() {
	if($("#customer_id").val() == "") { 
		parent.sendErrorMessage("Invalid any payee for this check voucher..."); 
	} else {
		$("#applyDivs").dialog({
			title: "Supplier's Invoices", 
			width: 540,  
			resizable: false, 
				buttons: {
				"Apply Invoice":  function() { applyInvoice(); },
				"Close Window": function() { $(this).dialog("close"); }
			}
		});
	}
}

function applyInvoice() {
	var msg = "";
	var amt = parseFloat(parent.stripComma($("#app_amount").val()));
	if($("#app_docno").val() == "") { msg = msg + "- You must specify Invoice or Reference # before applying this document to CV...<br/>"; }
		if(isNaN(amt) == true || amt == '0.00' || amt == "") { msg = msg + "- Invalid amount specified...<br/>"; }
		if(msg != "") {
			parent.sendErrorMessage(msg);
	} else {
		$.post("cv.datacontrol.php", { mod: "addInvoice", cv_no: $("#cv_no").val(), cv_date: $("#cv_date").val(), bank: $("#fundsource").val(), sname: $("#app_payee_name").val(), saddr: $("#app_payee_address").val(), stin: $("#app_payee_tin").val(), ref_no: $("#app_docno").val(), ref_date: $("#app_docdate").val(), amount: amt, dbAcct: $("#app_debit_acct").val(), isVat: $("#app_vatable").val(), atc: $("#app_atc").val(), sid: Math.random() }, function(data) {
			$("#details").html(data);
			getTotals();
			$("#applyDivs").dialog("close");
			$("#applydocs")[0].reset();
		});
	}
}

function deleteInvoice(ref_no,ref_type) {
	if(confirm("Are you sure you want to delete this invoice from this Accounts Payable Voucher?") == true) {
		$.post("cv.datacontrol.php", { mod: "deleteInvoice", ref_no: ref_no, ref_type: ref_type, cv_no: $("#cv_no").val(), cv_date: $("#cv_date").val(), cid: $("#customer_id").val(), bank: $("#fundsource").val(), sid: Math.random() }, function(data) {
			$("#details").html(data);
			getTotals();
		},"html");
	}
}

function showInvoices(cv_no,cy) {
	$.post("cv.datacontrol.php", { mod: "showInvoices", cv_no: cv_no, cy: cy, sid: Math.random() }, function(data) {
	if(data.length > 0) {
		$("#invoices").html(data);
			$("#invoices").dialog({title: "Supplier's Invoices", width: 580, height: 420, resizable: false, modal: true, buttons: {
					"Close Window": function() { $(this).dialog("close"); }
				}
			 });
		} else {
			parent.sendErrorMessage("Unable to find any document attached to this Accounts Payable Voucher. Make sure you have specified the correct supplier code...");
		}
	});
}

function addDetails() {
	var msg = "";
	var ref_no = $("#ref_no").val();
	var ref_date = $("#ref_date").val();
	var ref_type = $("#ref_type").val();
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
		$.post("cv.datacontrol.php", { mod: "insertDetail", cv_no: $("#cv_no").val(), cv_date: $("#cv_date").val(), ref_no: ref_no, ref_date: ref_date, ref_type: ref_type, acode: acode, adesc: adesc, dc: dc, amount: amount, ccenter: ccenter, bank: $("#fundsource").val(), sid: Math.random() }, function(data) {
			$("#details").html(data);
			getTotals();
			$("#ref_no").val(''); $("#ref_date").val(), $("#acct_code").val(''); $("#autodescription").val(''); $("#acct_description").val(''); $("#cost_center").val(''); $("#amount").val('');
		},"html");
	}
}

function deleteLine(lid,cv_no,cy) {
	if(confirm("Are you sure you want to remove this entry?") == true) {
		$.post("cv.datacontrol.php", { mod: "deleteLine", lid: lid, cv_no: cv_no, cy: cy, bank: $("#fundsource").val(), sid: Math.random() }, function(data) { $("#details").html(data); getTotals(); },"html");
	}
}

function changeCostCenter(lid,cc) {
	var el = 'c_'+lid;
	$.post("cv.datacontrol.php", { mod: "iccenter", cc: cc, lid: lid, sid: Math.random() },function(data){
		document.getElementById(el).innerHTML = data;
	},"html");
}

function ichangeNa(val,lid) {
	$.post("cv.datacontrol.php", { mod: "ichangeNa", lid: lid, val: val, sid: Math.random() }, function(costCenter) {
		var el = 'c_'+lid;
		document.getElementById(el).innerHTML =  "<a href=\"#\" style=\"text-decoration: none; color: black;\" onclick=\"javascript: changeCostCenter("+lid+",'"+val+"');\">"+costCenter+"</a>";
	},"html");
}

function changeAPAmount(lid,amount) {
	var el = 'xc_'+lid;
	$.post("cv.datacontrol.php", { mod: "changeAP", amount: amount, lid: lid, sid: Math.random() },function(data){
		document.getElementById(el).innerHTML = data;
		document.getElementById(lid).focus();
	},"html");
}

function updateAPAmount(val,lid,cv_no,amount) {
	var el = 'xc_'+lid;
	val = parseFloat(parent.stripComma(val));
	amount = parseFloat(parent.stripComma(amount));
	if(val != amount) {
		$.post("cv.datacontrol.php", { mod: "ichangeAngAP", cv_no: cv_no, lid: lid, val: val, cv_no: $("#cv_no").val(), cv_date: $("#cv_date").val(), bank: $("#fundsource").val(), sid: Math.random() }, function(data) { $("#details").html(data); getTotals(); },"html");
	} else {
		var amt = amount.toFixed(2);
		    amt = parent.kSeparator(amt);
		document.getElementById(el).innerHTML =  "<a href=\"#\" style=\"text-decoration: none; color: black;\" onclick=\"javascript: changeAPAmount('"+lid+"','"+amount+"');\">"+amt+"</a>";
	}
}


function finalizeCV(cv_no,uid) {
	var msg  = "";
	if($("#customer_id").val() == "") { msg = msg + "- Invalid or missing Payee Information<br/>"; }
	if($("#fundsource").val() != "") {
		if($("#fundsource").val() != '10103') {
			if($("#check_no").val() == "") { msg = msg + "- Invalid Check No.<br/>"; }
			if($("#check_date").val() == "") { msg = msg + "- Invalid Check Date<br/>"; }
		}
	} else { msg = msg + "- Please select Source of Fund for this disburment<br/>"; }
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		if(confirm("Are you sure you want to finalize this Disbursement Voucher?") == true) {
			$.post("cv.datacontrol.php", { mod: "check4print", cv_no: cv_no,  bank: $("#fundsource").val(),sid: Math.random() }, function(data) {
				if(data == "noerror") {
					parent.viewCV(cv_no);
				} else {
					if(data == "wayUlo") { parent.sendErrorMessage("It appears that you have yet to save changes made to this voucher."); }
					if(data == "waySulod") { parent.sendErrorMessage("It appears that you have yet to include subsidiary details for this Check Voucher"); }
					if(data == "DiBalanse") { parent.sendErrorMessage("It appears that your subsidiary details is not yet balanced."); }
				}
			},"html");
		}
	}
}

function reopenCV(cv_no) {
	
	function subReopen() {
		
	}
	
	$.post("cv.datacontrol.php", { mod: "checkCleared", cv_no: cv_no, sid: Math.random() }, function(ret) {
		if(ret == "notOk") {
			if(confirm("It appears that this Disbursement Voucher has already been marked as \"cleared\" during Bank Recon Process. Setting this to active may affect reconciled figures of the Cash-in-Bank used in this voucher. Do you still wish to continue?") == true) {
				if(confirm("Are you sure you want to set this document to Active Status?") == true) {
					$.post("cv.datacontrol.php", { mod: "reopenCV", cv_no: cv_no,sid: Math.random() }, function(){
						location.reload();
					});
				}
			}	
		} else {
				if(confirm("Are you sure you want to set this document to Active Status?") == true) {
					$.post("cv.datacontrol.php", { mod: "reopenCV", cv_no: cv_no,sid: Math.random() }, function(){
						location.reload();
					});
				}
		}
		
	},"html");
}

function cancelCV(cv_no) {
	if(confirm("Are you sure you want to Cancel this document?") == true) {
		$.post("cv.datacontrol.php", { mod: "cancel", cv_no: cv_no,sid: Math.random() }, function(){
			alert("Disbursement Voucher Successfully Cancelled!");
			location.reload();
		});
	}
}

function reuseCV(cv_no) {
	if(confirm("Are you sure you want to recycle this document?") == true) {
		$.post("cv.datacontrol.php", { mod: "reopenCV", cv_no: cv_no, sid: Math.random() }, function() {
			location.reload();
		});
	}
}

function reprintCV(cv_no,uid) {
	window.open("print/cv.print.php?cv_no="+cv_no+"&sid="+Math.random()+"&user="+uid+"&reprint=Y","Cash/Check Voucher","location=1,status=1,scrollbars=1,width=640,height=720");
}

function updateHeader(){
	$.post("cv.datacontrol.php", { mod: "saveHeader", cv_no: $("#cv_no").val(), ccode: $("#customer_id").val(), cname: $("#customer_name").val(), address: $("#cust_address").val(), source: $("#fundsource").val() , bank: $("#fundsource").val(), check_no: $("#check_no").val(), check_date: $("#check_date").val(), cv_date: $("#cv_date").val(), remarks: $("#remarks").val() }, function() {
		
		},"html");
}

function applyDocuments() {
	if($("#customer_id").val() == "" || $("#customer_id").val() == 0) {
		parent.sendErrorMessage("You have to specify valid <b>Payee</b> first before trying to apply any posted documents to this Voucher.");
	} else {
		$("#app_client").val($("#customer_id").val()); $("#app_acct").focus();
		$("#applyDivs2").dialog({title: "Apply To Other Documents", width: 480, resizable: false, modal: true });
	}
}

function checkForDoc(doctype) {
	if(doctype == '') {
		clearMe();
	} else {
		if($("#app_acct").val() != "") {
			if(isNaN($("#app_acct").val()) == true) { var acct = $("#app_acct").val().substr(1,4); } else { var acct = $("#app_acct").val(); }
			$.post("src/sjerp.php", { mod: "verifyACCT", acct: acct, sid: Math.random() }, function(data) { 
				if(data == "NotFound") { 
					parent.sendErrorMessage("- Invalid Account!");
					clearMe();
				} else {
					$.post("src/sjerp.php", { mod: "checkForDoc", doctype: doctype, cid: $("#app_client").val(), acct: acct, sid: Math.random() }, function(ret) {
						$("#balances").html(ret);
						$("#app_docno2").val(''); $("#app_docdate2").val(''); $("#app_amount2").val('');
					},"html");
				}
			},"html");
		} else { parent.sendErrorMessage("- Please Indicate Account Code for the document to be applied..."); $("#app_acct").val(''); clearMe(); }
	}
}

function selectDocument(a,b,c,d,e,f) {
	$("#app_docno2").val(a); $("#app_docdate2").val(b); $("#app_amount2").val(parent.kSeparator(d)); $("#app_side").val(e); $("#app_lid").val(f);
}

function applyNow() {
	if($("#app_side").val() == "") {
		parent.sendErrorMessage("- Invalid Document Input. Please select a document from the result if there is any...");
	} else {
		if(isNaN(parent.stripComma($("#app_amount").val())) == true) { 
			parent.sendErrorMessage("- Invalid amount..."); 
		} else {
			var acct = $("#app_acct").val().substr(1,4);
			$.post("cv.datacontrol.php", { mod: "saveAppliedDoc", cv_no: $("#cv_no").val(), cv_date: $("#cv_date").val(), bank: $("#fundsource").val(), ref_no: $("#app_docno2").val(), ref_type: $("#app_doctype").val(), ref_date: $("#app_docdate2").val(), client: $("#app_client").val(), acct: acct, side: $("#app_side").val(), amount: $("#app_amount2").val(), lid: $("#app_lid").val(), sid: Math.random() }, function(data) {
				$("#details").html(data);
				clearMe();
			},"html");
		}
	}
}


function clearMe() {
	$(document.applydocs2)[0].reset();
	$("#balances").html('');
}