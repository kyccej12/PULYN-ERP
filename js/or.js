function retrieveSO(val) {
    $.post("or.datacontrol.php", { mod: "retrieveSO", val: val, docno: $("#doc_no").val(), trace_no: $("#trace_no").val(), sid: Math.random() }, function(data) {
        switch(data['respond']) {
        
            case "used": 
                parent.sendErrorMessage("Specified Sales Order has already been processed.")
            break;
            case "notFound":
                parent.sendErrorMessage("Sales Order Not Found!");
            break;
            case "currentActive":
                parent.sendErrorMessage("Sales Order Currently loaded to an active document. Please see Document Seq No. <a href=\"#\" onclick=\"viewOR("+data['doc_no']+");\">"+data['docno']+"</a> dtd. "+data['date']+"");
            break;
            case "ok":
                $("#terms").val(data['terms']);
                $("#patient_id").val(data['pid']);
                $("#patient_name").val(data['patient_name']);
                $("#patient_address").val(data['patient_address']);
                $("#customer_code").val(data['cid']);
                $("#customer_name").val(data['customer_name']);
                $("#customer_address").val(data['customer_address']);
                $("#scpwd_id").val(data['scpwd_id']);
                $("#mid_no").val(data['mid_no']);
                $("#so_no").val(data['sono']);
                $("#or_no").val(data['or_no']);
                $("#doc_no").val(data['doc_no']);

                redrawDataTable();

            break;
        }
    },"json");

}

function browseSO() {

    var msg = '';
    //if($("#doc_no").val() == '') {
     //   msg = msg + "- It appears that you have yet to save initially saved this document."; 
   // }

    if(msg != '') {
        parent.sendErrorMessage(msg);
    } else {
        $.post("or.datacontrol.php", {
            mod: "browseSO",
            cid: $("#customer_code").val(),
            doc_no: $("#doc_no").val(),
            sid: Math.random(),

        },function(rset) { 
            if(rset.length > 0) { 
                $("#solist").html(rset);
                var myso = $("#solist").dialog({
                    title: "Unbilled Sales Order", 
                    width: 960, 
                    height: 480, 
                    resizable: false,
                    buttons: [
						{
							text: "Upload Selected Sales Order",
							click: function() {
								if($('#frmFetchedSO input:checked').length > 0) {
									
									var mydata  = $("#frmFetchedSO").serialize();
										mydata = mydata + "&mod=retrieveSO&docno="+$("#doc_no").val()+"&trace_no="+$("#trace_no").val()+"&sid="+Math.random()+"";                            
									$.ajax({
										type: "POST",
										url: "or.datacontrol.php",
										data: mydata,
										dataType: "json",
										success: function(data) { 
											
											switch(data['respond']) {
												case "used": 
													parent.sendErrorMessage("Specified Sales Order has already been processed.")
												break;
												case "notFound":
													parent.sendErrorMessage("Sales Order Not Found!");
												break;
												case "currentActive":
													parent.sendErrorMessage("Sales Order Currently loaded to an active document. Please see Document Seq No. <a href=\"#\" onclick=\"viewOR("+data['doc_no']+");\">"+data['docno']+"</a> dtd. "+data['date']+"");
												break;
												case "ok":
													$("#terms").val(data['terms']);
			
													$("#customer_code").val(data['cid']);
													$("#customer_name").val(data['customer_name']);
													$("#customer_address").val(data['customer_address']);
													$("#scpwd_id").val(data['scpwd_id']);
													$("#mid_no").val(data['mid_no']);
													$("#or_no").val(data['or_no']);
													$("#doc_no").val(data['doc_no']);
									
													redrawDataTable();
													getTotals();
												break;
											}
											
											myso.dialog("close"); 
										}
									});
								} else {
									parent.sendErrorMessage("Unable to continue as it appears you have not selected any Sales Order from the list yet.");
								}
							},
							icons: { primary: "ui-icon-check" }
						},
						{
							text: "Close",
							click: function() { $(this).dialog("close"); },
							icons: { primary: "ui-icon-closethick" }
						}
					]
                });
            } else { 
                parent.sendErrorMessage("Unable to find any unbilled Sales Order from this client."); 
            }
        },"html");
    }
}

function browseSOA() {

    var msg = '';
    //if($("#doc_no").val() == '') {
     //   msg = msg + "- It appears that you have yet to save initially saved this document."; 
   // }

    if(msg != '') {
        parent.sendErrorMessage(msg);
    } else {
        $.post("or.datacontrol.php", {
            mod: "browseSOA",
            cid: $("#customer_code").val(),
            doc_no: $("#doc_no").val(),
            sid: Math.random(),

        },function(rset) { 
            if(rset.length > 0) { 
                $("#solist").html(rset);
                var myso = $("#solist").dialog({
                    title: "Unbilled Sales Order", 
                    width: 960, 
                    height: 480, 
                    resizable: false,
                    buttons: {
                        "Upload Selected Statement of Account": function() {
                            if($('#frmFetchedSO input:checked').length > 0) {
                                
                                var mydata  = $("#frmFetchedSO").serialize();
                                    mydata = mydata + "&mod=retrieveSOA&docno="+$("#doc_no").val()+"&or_no="+$("#or_no").val()+"&trace_no="+$("#trace_no").val()+"&sid="+Math.random()+"";                            
                                $.ajax({
                                    type: "POST",
                                    url: "or.datacontrol.php",
                                    data: mydata,
									dataType: "json",
                                    success: function(data) { 
										
										switch(data['respond']) {

											case "ok":

												if($("#doc_no").val() == '') {
													$("#customer_code").val(data['cid']);
													$("#customer_name").val(data['cname']);
													$("#customer_address").val(data['caddr']);
													$("#or_no").val(data['or_no']);
													$("#doc_no").val(data['doc_no']);
												}
												redrawDataTable();
												getTotals();
											break;

											case "mismatch":
												parent.sendErrorMessage("Sales order seems to be of different patients. For cash transactions, multiple sales orders should be of the same patient.");
											break;

										}
										
										 myso.dialog("close"); 
									}
                                });
                            } else {
                                parent.sendErrorMessage("Unable to continue as it appears you have not selected any Sales Order from the list yet.");
                            }
                        },
                        "Cancel": function() { $(this).dialog("close"); }
                    } 
                });
            } else { 
                parent.sendErrorMessage("Unable to find any unbilled Sales Order from this client."); 
            }
        },"html");
    }
}

function redrawDataTable() {
    var trace_no = $("#trace_no").val()
    $('#details').DataTable().ajax.url("or.datacontrol.php?mod=retrieve&trace_no="+trace_no+"&sid="+Math.random()+"").load();
	getTotals();
}

function saveHeader() {
	$.post("or.datacontrol.php", {
		mod: "saveHeader",
		trace_no: $("#trace_no").val(),
		doc_no: $("#doc_no").val(),
		docdate: $("#doc_date").val(),
		or_no: $("#or_no").val(),
		so_no: $("#so_no").val(),
		pid: $("#patient_id").val(),
		pname: $("#patient_name").val(),
		paddress: $("#patient_address").val(),
		cid: $("#customer_code").val(),
		cname: $("#customer_name").val(),
		caddress: $("#customer_address").val(),
		scid: $("#scpwd_id").val(),
		mid_no: $("#mid_no").val(),
		remarks: $("#remarks").val(),
		cashtype: $("#cash_type").val(),
		cc_type: $("#cc_type").val(),
		cc_bank: $("#cc_bank").val(),
		cc_name: $("#cc_name").val(),
		cc_no: $("#cc_no").val(),
		cc_expiry: $("#cc_expiry").val(),
		cc_approvalno: $("#cc_approvalno").val(),
		ck_bank: $("#ck_bank").val(),
		ck_no: $("#ck_no").val(),
		ck_date: $("#ck_date").val(),
		sid: Math.random()
	},function(data) { if($("#doc_no").val() == '') { $("#doc_no").val(data['docno']); $("#or_no").val(data['orno']); }},"json");

	parent.popSaver();
}

function getTotals() {
	$.post("or.datacontrol.php", { mod: "getTotals", doc_no: $("#doc_no").val(), sid: Math.random() }, function(data) {
		$("#grossSales").val(data['gross']);
		$("#salesDiscount").val(data['discount']);
		$("#subTotal").val(data['subtotal']);
		$("#ewt").val(data['ewt']);
		$("#scDiscount").val(data['sc']);
		$("#amtDue").val(data['adue']);
		$("#amtPaid").val(data['paid']);
		$("#balance").val(data['balance']);
		$("#balanceDue").val(data['adue']);
	},"json");
}

function computeEWT(code) {
	$.post("or.datacontrol.php", { mod: "updateEWT", doc_no: $("#doc_no").val(), cid: $("#customer_code").val(), ecode: code, scid: $("#scpwd_id").val(), sid: Math.random() }, function() {
		getTotals();
	});
}

function computeItemAmount(qty) {
	if(isNaN(qty) == true) { parent.sendErrorMessage("-Invalid Quantity!"); $("#itemQty").val(''); }
	var cost = parseFloat(parent.stripComma($("#itemCost").val()));
	var sprice = parseFloat(parent.stripComma($("#itemSpecial").val()));

	if(cost != sprice) { var amount = qty * sprice; } else { var amount = qty * cost; }
	amount = amount.toFixed(2);

	$("#itemAmount").val(parent.kSeparator(amount));
}

function addItem() {
	$("#itemEntry").dialog({
		title: "Add Item", 
		width: 440, 
		resizable: false, 
		modal: true, 
		buttons: [
			{ 
				text: "Add Item",
				click: function() { 
					var msg = "";

					if($("#itemCode").val() == "") { msg = msg + "- Invalid Item Code<br/>"; }
					if(isNaN($("#itemQty").val()) == true) { msg = msg + "- Invalid Quantity<br/>"; }

					if(msg != '') {
						parent.sendErrorMessage(msg);
					
					} else {
						$.post("or.datacontrol.php", { 
							mod: "addItem", 
							doc_no: $("#doc_no").val(),
							trace_no: $("#trace_no").val(),
							cid: $("#customer_code").val(),
							scid: $("#scpwd_id").val(), 
							item: $("#itemCode").val(), 
							description: $("#itemDescription").val(), 
							unit: $("#itemUnit").val(), 
							qty: $("#itemQty").val(),
							cost: $("#itemCost").val(), 
							sprice: $("#itemSpecial").val(), 
							ispecial: $("#is_sprice").val(),
							amount: $("#itemAmount").val(), 
							sid: Math.random() }, 
						function(gt) {
							redrawDataTable();
							$("#frmItemEntry").trigger("reset");
						});
					}
				},
				icons: { primary: "ui-icon-check" }
			},
			{
				text: "Close",
				click: function() { $(this).dialog("close"); $("#frmItemEntry").trigger("reset"); },
				icons: { primary: "ui-icon-closethick" }
			}
		]
	});
}

function deleteItem(){
  
	var table = $("#details").DataTable();
	$.each(table.rows('.selected').data(), function() {
		lineid = this["id"];
		so_no = this['sono'];
		code = this['code'];
	});

	if(!lineid) {
		parent.sendErrorMessage("Please select a record to delete.");
	} else {
		$.post("or.datacontrol.php", { mod: "checkLabSamples", lid: lineid, so_no: so_no, code: code, doc_no: $("#doc_no").val(), sid: Math.random() }, function(labStatus) {
			if(labStatus == 'notOk') {
				parent.sendErrorMessage("- You can no longer remove this line entry as it appears that one or more procedures have already been processed.");	
			} else {
				if(confirm("Are you sure you want to remove this line entry?") == true) {
					$.post("or.datacontrol.php", { mod: "deleteLine", lid: lineid, so_no: so_no, code: code, doc_no: $("#doc_no").val(), cid: $("#customer_code").val(), scid: $("#scpwd_id").val(), sid: Math.random() }, function(gt) { redrawDataTable(); });
				}
			}

		});
	}
}

function passWordCheck() {

	var table = $("#details").DataTable();
	$.each(table.rows('.selected').data(), function() {
		lineid = this["id"];
	});

	if(!lineid) {
		parent.sendErrorMessage("Please select a line entry to apply discount.");
	} else {
		var pass = $("#passcheck").dialog({
			title: "Supervisor Password Required", 
			width: 440, 
			resizable: false, 
			modal: true, 
			buttons: [
				{
					text: "Proceed",
					click: function() {
						if($("#spass")!='') {
							var myPass = $.md5($("#spass").val());
							$.post("src/sjerp.php",{ mod: "checkSPass", pass: myPass, sid: Math.random() },function(result) {  
								if(result == "ok") { pass.dialog("close"); applyDiscount(); $("#spass").val(''); } else { parent.sendErrorMessage("The password you entered is invalid!"); }
							},"html");
						} else { parent. sendErrorMessage("Invalid Password!"); }
					
					},
					icons: { primary: "ui-icon-check" }
				},
				{
					text: "Close",
					click: function() { $(this).dialog("close"); },
					icons: { primary: "ui-icon-closethick" }
				}
			]
		});
	}
}

function applyDiscount(){

	var table = $("#details").DataTable();
	$.each(table.rows('.selected').data(), function() {
		lineid = this["id"];
	});

   if(!lineid) {
		parent.sendErrorMessage("Please select a line entry to apply discount.");
	} else {

		var dis = $("#lineDiscount").dialog({
			title: "Update Line Entry", 
			width: 440, 
			resizable: false, 
			modal: true, 
			buttons: [
				{ 
					text: "Apply Discount",
					click: function() { 
						$.post("or.datacontrol.php", { mod: "applyDiscount", cid: $("#customer_code").val(), doc_no: $("#doc_no").val(), scid: $("#scpwd_id").val(), discount: $("#disc").val(), lid: lineid, sid: Math.random() }, function() {
							redrawDataTable(); 
							$("#discount").val('0.00');
							dis.dialog("close");
						});
					},
					icons: { primary: "ui-icon-check" }
				},
				{
					text: "Close",
					click: function() { $(this).dialog("close"); },
					icons: { primary: "ui-icon-closethick" }
				}
			]
		});
	}
}

function checkClear(val) {
	if(val == '' || val == 0) {
		$("#customer_code").val(''); $("#customer_name").val(''); $("#customer_address").val(''); $("#terms").val(0);
	}
}

function receiveCash() {
	$("#cashTendered").focus();
	var dial = $("#cashTender").dialog({
		title: "Receive Cash", 
		width: 440, 
		resizable: false, 
		modal: true, 
		buttons:[ 
			{
				text: "Proceed",
				click: function() {
					if(confirm("Are you sure you want to proceed?") == true) {
						$.post("or.datacontrol.php", {
							mod: "updatePayment",
							doc_no: $("#doc_no").val(),
							paid: $("#balanceDue").val(),
							tendered: $("#cashTendered").val(),
							changeDue: $("#changeDue").val(),
							sid: Math.random() 
						}, function () {
							getTotals();
							dial.dialog("close");
						});
					}
				},
				icons: { primary: "ui-icon-check" }
			},
			{
				text: "Close",
				click: function() { $(this).dialog("close"); },
				icons: { primary: "ui-icon-closethick" }
			}
		]
	});
}

function computeChange() {
	var tendered = parseFloat(parent.stripComma($("#cashTendered").val()));
	if(isNaN(tendered) == true) {
		parent.sendErrorMessage("Invalid Amount Entered!");
		$("#changeDue").val(''); $("#cashTendered").val('');
	} else {
		var due = parseFloat(parent.stripComma($("#balanceDue").val()));
		if(tendered < due) {
			parent.sendErrorMessage("Amount entered is insufficient");
			$("#changeDue").val(''); $("#cashTendered").val('');
		} else {
			var change = tendered - due;
			    change = change.toFixed(2);
			$("#changeDue").val(parent.kSeparator(change));
		}

	}
}

function finalize() {
	var msg = '';
	var balance = parseFloat(parent.stripComma($("#balance").val()));
	var cashtype = $("#cash_type").val();
	
	if($("#or_no").val() == 0 || $("#or_no").val() == '') { msg = msg + "- Please indicate the receipt's Loose Leaf Series No.<br/>"; }


	if(cashtype == '1' || cashtype == '3') {
		
		if(balance > 0) {
			msg = msg + "Unable to finalize this document as it appears you haven't received the cash payment yet. Press <b>Shift + R</b> to receive cash or input the check amount.<br/>";
		}

		if(cashtype == 3) {
			if($("#ck_bank").val() == '') { msg = msg + "- Please specify the check's issuing bankg."; }
			if($("#ck_no").val() == '') { msg = msg + "- Please indicate the Check Number."; }
			if($("#ck_date").val() == '') { msg = msg + "- Please indicate the Check Date."; } 
		}
	} else {
		if($("#cc_type").val() == '') { msg = msg + "- Please specify the Credit Card Type (eg. Visa, Mastercard, etc).<br/>"; }
		if($("#cc_bank").val() == '') { msg = msg + "- Please specify Credit Card's Bank Issuer.<br/>"; }
		if($("#cc_name").val() == '') { msg = msg + "- Please specify the name written on the Credit Card.<br/>"; }
		if($("#cc_expiry").val() == '') { msg = msg + "- Please specify Credit Card's expiry month & year.<br/>"; }
		if($("#cc_approvalno").val() == '') { msg = msg + "- Please specify the transaction's approval no.<br/>"; }
	}

	if(msg != '') {
		parent.sendErrorMessage(msg);
	} else {
		if(confirm("Are you sure you want to finalize this transaction?") == true) {
			$.post("or.datacontrol.php",{ mod: "finalize", doc_no: $("#doc_no").val(), cid: $("#customer_code").val(), sid: Math.random() }, function() { parent.viewOR($("#doc_no").val()); });
		}
	}
}

function reopen() {
	if(confirm("Are you sure you want to set this document to \"Active\" status?") == true) {
		$.post("or.datacontrol.php",{ mod: "reopen", doc_no: $("#doc_no").val(), cid: $("#customer_code").val(), sid: Math.random() }, function() { parent.viewOR($("#doc_no").val()); });
	}
}

function cancel() {
	if(confirm("Are you sure you want to cancel this document?") == true) {
		$.post("or.datacontrol.php",{ mod: "cancel", doc_no: $("#doc_no").val(), sid: Math.random() }, function() { parent.viewOR($("#doc_no").val()); });
	}
}

function reuse() {
	if(confirm("Are you sure you want to recycle this document?") == true) {
		$.post("or.datacontrol.php",{ mod: "reuse", doc_no: $("#doc_no").val(), sid: Math.random() }, function() { parent.viewOR($("#doc_no").val()); });
	}
}