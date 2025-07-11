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

function getTotals() {
	$.post("si.datacontrol.php", { mod: "getTotals", trace_no: $("#trace_no").val(), sid: Math.random() }, function(data) {
		
		var applied = parseFloat(parent.stripComma($("#amount_applied").val()));
		var net = parseFloat((data['gross'])) - parseFloat((data['discount'])) - parseFloat((data['commission'])) - applied;
		    net = net.toFixed(2);
		
		$("#amount_b4_discount").val(parent.kSeparator(data['gross']));
		$("#discount_in_peso").val(parent.kSeparator(data['discount']));
		$("#total_due").val(parent.kSeparator(net));
		$("#balance_due").val(parent.kSeparator(net));
	},"json");
}

function computeAmount() {
	var price = parseFloat(parent.stripComma($("#unit_price").val()));
	var qty = parseFloat(parent.stripComma($("#qty").val()));

	if(isNaN(qty) == true || isNaN(price) == true || qty == "" || price == "") {
		parent.sendErrorMessage("Invalid Quantity or Price. Please check your entries and try again...")
	} else {
		var amt = price * qty;
			amt = amt.toFixed(4);
		$("#amount").val(parent.kSeparator(amt));
	}

}

function saveInvHeader() {
	var msg = "";
	if($("#cSelected").val() == "N") { msg = msg + "- Invalid or missing Customer Information<br/>"; }
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		$.post("si.datacontrol.php", { mod: "saveHeader", doc_no: $("#doc_no").val(), invoice_no: $("#invoice_no").val(), trace_no : $("#trace_no").val(), type: $("#docno_type").val(), invoice_date: $("#invoice_date").val(), postDate: $("#posting_date").val(), cid: $("#customer_id").val(), cname: $("#customer_name").val(), addr: $("#cust_address").val(), discount: $("#sales_discount").val(), terms: $("#terms").val(), srep: $("#sales_rep").val(), remarks: $("#remarks").val(), sid: Math.random() }, function(data) {
			parent.popSaver();
			$("#doc_no").val(data['docno']);
		},"json");
	}
}

/*
function applyDiscount(val) {
	$.post("si.datacontrol.php", {mod: "applyDiscount", invoice_no: $("#invoice_no").val() , trace_no : $("#trace_no").val(), sid: Math.random() }, function(data) {
		$("#rrdetails").html(data)
	},"html");
}
*/

function downloadPayRef(){
	$("#payref").dialog({ 
		title: "Download Sales Order", 
		width: 340, 
		resizable: false,
		modal: true,
		buttons: {
		  "Verify Sales Order":
			function() {
				$.post("si.datacontrol.php", { mod: "checkSOstat", so_no: $("#so_cno").val(), ino: $("#invoice_no").val(), sid: Math.random() }, function(ret) {
					if(ret['stat'] == "Ok") {
						if($("#invoice_no").val() != 0 || $("#invoice_no").val() !='') {
							$.post("si.datacontrol.php", { mod: "uploadDetailSO", so_no: $("#so_cno").val(), ino: $("#invoice_no").val(), trace_no: $("#trace_no").val(), sid: Math.random() },function(data) {
								$("#details").html(data);
							},"html");
						} else {
							$("#so_cname").val(ret['cname']);
							$("#so_cdate").val(ret['sodate']);
							$("#so_camt").val(ret['amount']);
							$("#so_srep").val(ret['salesrep']);
							$("#SOinfo").dialog({
								title: "Sales Order Information",
								width: 400,
								resizable: false,
								modal: true,
								buttons: {
									"Download Sales Order": function() {
										$.post("si.datacontrol.php", { mod: "uploadWholeSO", so_no: $("#so_cno").val(), trace_no: $("#trace_no").val(), sid: Math.random() },function(data) {
											parent.viewSI(data['docno']);
										},"json");
									}
								}
							});
						}
					} else {
						parent.sendErrorMessage("- You have specified an invalid Sales Order No.<br/>- The Sales Order you have specified is from a different Customer<br/>- Sales Order you have specified is fully served.");
					}
				},"json");
			}
		}
	});
}


function downloadPO() {
	if($("#customer_id").val() == "") {
		parent.sendErrorMessage("Please select customer first before downloading any Sales Order.");
	} else {
		showLoaderMessage();
		$.post("si.datacontrol.php", { mod: "getPOS", trace_no : $("#trace_no").val(), cid: $("#customer_id").val(), sid: Math.random() }, function(data) {
			if(data.length > 0) {
				$("#invoiceAttachment").html(data);
				$("#invoiceAttachment").dialog({title: "Unserved Sales Orders", width: 600, height: 360, resizable: false, modal: true, buttons: {
						"Upload Sales Order":  function() { loadPO(); },
						"Close Window": function() { $(this).dialog("close"); }
					}
				 });
			} else {
				parent.sendErrorMessage("Unable to find any Outstanding Sales Order for this customer. Make sure you have specified the correct Customer Code...");
			}
			$("#loaderMessage").dialog("close");
		});
	}
}

function tagPO(el,val) {
	var obj = document.getElementById(el);
	var myURL;
	if(obj.checked == true) { var push = "Y"; } else { var push = "N"; }
	$.post("si.datacontrol.php", { mod: "tagPO", push: push, val: val, sid: Math.random() });
}

function loadPO() {
	showLoaderMessage();
	$.post("si.datacontrol.php", { mod: "loadPO", trace_no : $("#trace_no").val(), doc_no: $("#doc_no").val(), sid: Math.random() }, function(data) {
		if(data.length > 0) {
			$("#details").html(data);
			getTotals();
			$("#invoiceAttachment").dialog("close");
		} else { parent.sendErrorMessage("There nothing to upload. Please make sure you have selected purchases from the list given..."); }
		$("#loaderMessage").dialog("close");
	});
}

function addDetails() {
	var msg = "";
	var so_no = $("#so_no").val();
	var so_date = $("#so_date").val();
	var icode = $("#product_code").val();
	var idesc = $("#description").val();
	var qty = parseFloat(parent.stripComma($("#qty").val()));
	var price = parseFloat(parent.stripComma($("#unit_price").val()));
	var amount = parseFloat(parent.stripComma($("#amount").val()));
	var discount = $("#sales_discount").val();

	if(icode == "") { msg = msg + "- Product Code not specified<br/>"; }
	if(idesc == "") { msg = msg + "- Product Description not specified<br/>"; }
	if(isNaN(qty) == true || qty == "") { msg = msg + "- Invalid Quantity<br/>"; }
	if(isNaN(price) == true || price == "") { msg = msg + "- Invalid Unit Price<br/>"; }
	if(isNaN(amount) == true || amount == "") { msg = msg + "- Invalid Amount<br/>"; }
	
	if(msg != "") {
		parent.sendErrorMessage("Unable to continue due to the following error(s): <br/><br/>"+msg+"");
	} else {
		showLoaderMessage();
		$.post("si.datacontrol.php", { mod: "insertDetail", invoice_no: $("#invoice_no").val() , trace_no : $("#trace_no").val(), so_no: so_no, so_date: so_date, icode: icode, desc: idesc, qty: qty, unit: $("#unit").val(), price: price, amount: amount, sid: Math.random() }, function(data) {
			$("#rrdetails").html(data)
			$("#product_code").val('');
			$("#description").val('');
			$("#unit_price").val('');
			$("#unit").val('');
			$("#qty").val('');
			$("#linegross").val('');
			$("#amount").val('');
			$("#description").focus();
			$("#loaderMessage").dialog("close");
		},"html");
	}
}

function updateQty(val,lineid,price,qty) {
	var msg = "";
	var txtobj = 'qty['+lineid+']';
	var objamt1 = 'amt['+lineid+']';

	if(isNaN(val) == true) { 
		var msg = msg + "- You have specified an invalid figure (Quantity)"; 
	} else {
		if(parseFloat(val) > parseFloat(qty)) { msg = msg + "- You have specified a quantity greater than the original Sales Order Value"; document.getElementById(txtobj).value = qty; }
	}
	
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		$.post("si.datacontrol.php", { mod: "usabQty", lid: lineid, val: val, price: price, trace_no: $("#trace_no").val(), sid: Math.random() }, function(data) {
			document.getElementById(objamt1).innerHTML = data['amt1'];
			getTotals();
		},"json");
	}
}

function updatePrice(val,lineid,price,qty) {
	var msg = "";
	var txtobj = 'price['+lineid+']';
	var txtnprice = 'netprice['+lineid+']';
	var objamt1 = 'amt['+lineid+']';

	if(isNaN(val) == true || parseFloat(val) < 0) { var msg = msg + "- You have specified an invalid Price"; document.getElementById(txtobj).value = price; } 

	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		$.post("si.datacontrol.php", { mod: "usabPrice", lid: lineid, price: val, qty: qty, trace_no: $("#trace_no").val(), sid: Math.random() }, function(data) {
			document.getElementById(objamt1).innerHTML = data['amt1'];
			document.getElementById(txtnprice).innerHTML = data['nprice'];
			getTotals();
		},"json");
	}
}


function finalizeSI() {
	if(confirm("Are you sure you want to Finalize & Post this document to the General Ledger?") == true) {
		var msg = "";
		saveInvHeader();
		if($("#invoice_type").val() == "prim") {
			if($("#invoice_no").val() == 0 || $("#invoice_no").val() == "") { msg = msg + "- You have selected this invoice to be primary, but you didn't fill-up the \"Loose Leaf Invoice No.\"."; }
		}
		if($("#cSelected").val() == "N") { msg = msg + "- Invalid supplier or source for this Sales Invoice<br/>"; }
		if(msg == ""){
			showLoaderMessage(); 
			$.post("si.datacontrol.php", { mod: "checkSalesGroup", trace_no: $("#trace_no").val(), sid: Math.random() }, function(ret) {
				if(ret == 'Ok') {
					
					$.post("si.datacontrol.php", { mod: "check4print", trace_no: $("#trace_no").val(), sid: Math.random() }, function(data) { 
						if(data == "noerror") {
							if($("#terms").val()=='0'){
								$("#loaderMessage").dialog("close");
								$("#payment_mode").dialog({ width: 530, height: 175, closable: true, modal: true});
							}else{
								$.post("si.datacontrol.php", { mod: "finalize", trace_no: $("#trace_no").val(), sid: Math.random() }, function() {
									parent.viewSI($("#doc_no").val());
								});
							}
						} else {
							$("#loaderMessage").dialog("close");
							switch(data) {
								case "head": parent.sendErrorMessage("Unable to print document. Document is not yet saved..."); break;
								case "det": parent.sendErrorMessage("Unable to print document. It seems that you have not added any product yet to this Receiving Report..."); break;
								case "both": parent.sendErrorMessage("There is nothing to print. Please make it sure you have saved what entries you've made..."); break;
							}
						}
						
					},"html");
				} else {
					parent.sendErrorMessage("The following items below does not have proper assignment of either \"Revenue\", \"Asset/Inventory\" or \"COGS\" accounts: <br/><br/>"+ret+"<br/>Please check lines highlighted in red and refer it to Accounting Department to resolve this issue.");
				}
				$("#loaderMessage").dialog("close");
			},"html");
		}else{
			parent.sendErrorMessage("Unable to continue due to the following error(s): <br/><br/>"+msg+"");
		}
	}
}

function reopenSI() {
	var applied = parseFloat(parent.stripComma($("#amount_applied").val()));
	if(applied > 0 && $("#terms").val() != 0) {
		parent.sendErrorMessage("- It appears that a payment has already been made to this invoice.");
	} else {
		if(confirm("Are you sure you want to set this document to active status?") == true) {
			showLoaderMessage(); $("#uppermenus").html('');
			$.post("si.datacontrol.php", { mod: "reopen", trace_no: $("#trace_no").val(), doc_no: $("#doc_no").val(), sid: Math.random() }, function() {
				parent.viewSI($("#doc_no").val());
				$("#loaderMessage").dialog("close");
			});
		}
	}
}

function cancelSI() {
	if(confirm("Are you sure you want to Cancel this document?") == true) {
		$.post("si.datacontrol.php", { mod: "cancel", trace_no: $("#trace_no").val(), doc_no: $("#doc_no").val(), sid: Math.random() }, function(){
			alert("Receving Report Successfully Cancelled!");
			parent.viewSI($("#doc_no").val());
		});
	}
}

function reuseSI() {
	if(confirm("Are you sure you want to Recycle this document?") == true) {
		$.post("si.datacontrol.php", { mod: "reopen", trace_no: $("#trace_no").val(), doc_no: $("#doc_no").val(), sid: Math.random() }, function(){
			parent.viewSI($("#doc_no").val());
		});
	}
}

function printSI(rprint) {
	parent.printSI($("#doc_no").val(),rprint);
}

function printPackingList() {
	parent.printPackingList($("#doc_no").val());
}

function viewDoc(){
	$("#docinfo").dialog({title: "Document Info", width: 340, height: 200, resizable: false });
}

function applyDiscount(){
	if(SLid===undefined){
		alert("Please select ");
	}else{
		$("#discountDiv").dialog({
			width: 350,
			resizable: false,
			modal: true,
			buttons: {
				
				"Apply": function() { 
					var discount = $("#salesDiscount").val(); 
					
					$.post("si.datacontrol.php",{
						mod : "applyDiscount" ,
						lineid : SLid,
						discount : discount,
						trace_no : $("#trace_no").val(),
						type: $("input:radio[name=type]:checked").val(),
						sid: Math.random()
					},function(data){
						$("#details").html(data);
						getTotals();
					},"html");

					$(this).dialog("close"); 
				}
			}
		});
	}

}

	function cashCheckOut(tid) {
		$.post("src/sjerp.php", { mod: "getAmountDue", tmpfileid: tid, sid:Math.random() }, function(data) {
			$("#amountDue").val(data);
			$("#amountTendered").focus();
			$("#cashCheckOutForm").dialog({
				title: "Cash Checkout",
				modal: true, 
				title: "Cash Payment Method", 
				width: 500, 
				height: 460, 
				resizable: false, 
					buttons: {
					"Finalize Transaction":  function() { finalizePOScash(); }
				}
			});
		},"html");
	}

	function finalizePOScash() {

	var	tendered = parseFloat(parent.stripComma($("#amountTendered").val()));
	var	due = parseFloat(parent.stripComma($("#amountDue").val()));
		
		if(tendered < due || tendered == '' || tendered ==0) { parent.sendErrorMessage("Amount Tendered is less than the amount due"); $("#amountTendered").val(''); $("#changeDue").val('0.00'); } else {
			$.post("si.datacontrol.php",{  mod: "finalizePOScash", trace_no : $("#trace_no").val(), due: $("#amountDue").val(), tendered: $("#amountTendered").val() },function(data){
				$("#cashCheckOutForm").dialog("close");
				parent.viewSI($("#doc_no").val());
			});
		}
	}

	function computeChange(val) {
	var	tendered = parseFloat(parent.stripComma(val)); //parseFloat(val);
	var	due = parseFloat(parent.stripComma($("#amountDue").val()));
		
		if(tendered < due) { parent.sendErrorMessage("Amount Tendered is less than the amount due"); $("#amountTendered").val(''); $("#changeDue").val('0.00'); } else {
			var change = tendered - due;
				change = change.toFixed(2);
			$("#changeDue").val(parent.kSeparator(change));
		}
	}

	function ccCheckOut() {
		$("#cardCheckOutForm").dialog({
			modal: true, 
			title: "Credit Card Payment Method", 
			width: 380, 
			height: 405, 
			modal: true,
			resizable: false, 
				buttons: {
				"Finalize Transaction":  function() { finalizePOScard(); }
			}
		});	
	}

	function finalizePOScard() {
		if(confirm("Are you sure you want to finalize this transaction?") == true) {
			var msg = "";
			if($("#cc_name").val() == "") { msg = msg + "- Card Holder Name is empty<br/>"; }
			if($("#cc_no").val() == "") { msg = msg + "- Card No. is empty<br/>"; }
			if($("#cc_expiry").val() == "") { msg = msg + "- Card Expiry Date is empty<br/>"; }
			if($("#cc_approvalno").val() == "") { msg = msg + "- Transaction Approval No. is empty<br/>"; }
			
			if(msg!="") { parent.sendErrorMessage(msg); } else {
				$.post("si.datacontrol.php", { mod: "finalizePOScard", trace_no: $("#trace_no").val(), bank: $("#cc_bank").val(), cc_type: $("#cc_type").val(), cc_name: $("#cc_name").val(), cc_expiry: $("#cc_expiry").val(), approvalno: $("#cc_approvalno").val(), cc_no : $("#cc_no").val(), sid: Math.random()  }, function() {
					alert("Transaction Successfully Posted!");
					parent.viewSI($("#doc_no").val());
				});
			}
		}
	}

	function cheqCheckOut() {
		$("#cheqCheckOutForm").dialog({
			modal: true, 
			title: "Check Payment Method", 
			width: 380, 
			height: 265, 
			modal: true,
			resizable: false, 
				buttons: {
				"Finalize Transaction":  function() { finalizeCheqCheckOut(); }
			}
		});	
	}

	function finalizeCheqCheckOut(){
		var msg="";
		if($("#cheq_no").val() == "") { msg = msg + "- Check No. is empty.<br/>"; }
		if($("#cheq_date").val() == "") { msg = msg + "- Check Date is not specified. <br/>"; }

		if(msg!="") { parent.sendErrorMessage(msg); } else {
				$.post("si.datacontrol.php", { mod: "finalizeCheqCheckOut", trace_no: $("#trace_no").val(), bank: $("#cheq_bank").val(),cheq_no : $("#cheq_no").val() ,cheq_date : $("#cheq_date").val(), sid: Math.random()  }, function() {
					alert("Transaction Successfully Posted!");
					parent.viewSI($("#doc_no").val());
				});
			}
	}