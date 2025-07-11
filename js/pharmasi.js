function computeItemAmount(qty) {
	if(isNaN(qty) == true) { parent.sendErrorMessage("-Invalid Quantity!"); $("#itemQty").val(''); }
	var cost = parseFloat(parent.stripComma($("#itemCost").val()));

	var amount = qty * cost;
	amount = amount.toFixed(2);

	$("#itemAmount").val(parent.kSeparator(amount));
}

function getTotals() {
	$.post("pharma.sidatacontrol.php", { mod: "getTotals", doc_no: $("#doc_no").val(), sid: Math.random() }, function(amt) {
		$("#grossSales").val(amt['gross']);
		$("#salesDiscount")	.val(amt['discount']);
		$("#subTotal").val(amt['net']);
		$("#amtDue").val(amt['due']);
		$("#amtPaid").val(amt['paid']);
		$("#balance").val(amt['balance']);
		$("#balanceDue").val(amt['due']);
	},"json");

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
						$.post("pharma.sidatacontrol.php", { 
							mod: "addItem", 
							doc_no: $("#doc_no").val(), 
							trace_no: $("#trace_no").val(), 
							item: $("#itemCode").val(), 
							description: $("#itemDescription").val(), 
							unit: $("#itemUnit").val(), 
							qty: $("#itemQty").val(),
							cost: $("#itemCost").val(), 
							amount: $("#itemAmount").val(), 
							sid: Math.random() }, 
						function(gt) {
							redrawDataTable();
							getTotals();
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
	var lid;
	var code;
   $.each(table.rows('.selected').data(), function() {
		lid = this["id"];
		code = this['code'];
   });
  
	if(!lid) {
		parent.sendErrorMessage("Please select a record to delete.");
	} else {
		$.post("pharma.sidatacontrol.php", { mod: "checkLabSamples", lid: lid, doc_no: $("#doc_no").val(), code: code, sid: Math.random() }, function() {
			if(confirm("Are you sure you want to remove this line entry?") == true) {
				$.post("pharma.sidatacontrol.php", { 
					mod: "deleteLine", 
					lid: lid, 
					doc_no: $("#doc_no").val(), 
					code: code, 
					doc_no: $("#doc_no").val(), 
					sid: Math.random() }, 
				function() { getTotals(); redrawDataTable(); });
			}
		});
		
	}
}

function updateItem() {
	var table = $("#details").DataTable();
	var arr = [];
	$.each(table.rows('.selected').data(), function() {
		arr.push(this["id"]);
	});

	if(!arr[0]) {
		parent.sendErrorMessage("Please select a record to update.");
	} else {
		
		$.post("pharma.sidatacontrol.php", { mod: "retrieveLine", lid: arr[0], sid: Math.random() }, function(data) { 
			$("#itemDescription").val(parent.decodeEntities(data['description']));
			$("#itemCode").val(data['item_code']);
			$("#itemUnit").val(data['unit']);
			$("#itemQty").val(data['qty']);
			$("#itemCost").val(data['ucost']);
			$("#itemAmount").val(data['amt']);
		
			$("#itemEntry").dialog({
				title: "Update Line Entry", 
				width: 440, 
				resizable: false, 
				modal: true, 
				buttons: [
					{ 
						text: "Save Changes",
						click: function() { 
							var msg = "";
			
							if($("#itemCode").val() == "") { msg = msg + "- Invalid Item Code<br/>"; }
							if(isNaN($("#itemQty").val()) == true) { msg = msg + "- Invalid Quantity<br/>"; }
			
							if(msg != '') {
								parent.sendErrorMessage(msg);
							
							} else {
								if(confirm("Are you sure you want to save changes made to this entry?") == true) {
									$.post("pharma.sidatacontrol.php", { 
										mod: "updateItem", 
										lid: arr[0],
										doc_no: $("#doc_no").val(), 
										trace_no: $("#trace_no").val(), 
										item: $("#itemCode").val(), 
										description: $("#itemDescription").val(), 
										unit: $("#itemUnit").val(), 
										qty: $("#itemQty").val(),
										cost: $("#itemCost").val(), 
										amount: $("#itemAmount").val(), 
										sid: Math.random() }, 
									function(gt) {
										redrawDataTable();
										$("#grandTotal").val(gt);
										$("#frmItemEntry").trigger("reset");
										
									});
								}
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
		
		},"json");
	}
}

function applyDiscount(){
	saveHeader();
	var lineid;
	var table = $("#details").DataTable();
	$.each(table.rows('.selected').data(), function() {
		lineid = this["id"];
	});

   if(!lineid) {
		parent.sendErrorMessage("Please select a line entry you wish to apply for discount.");
	} else {
		if($("#scpwd_id").val() != '') {
			parent.sendErrorMessage("It appears that this patient is a Senior Citizen or PWD and shall be automatically be given the government mandated 20% discounts")
		} else {
			var dis = $("#discounter").dialog({
				title: "Line Discount", 
				width: 440, 
				resizable: false, 
				modal: true, 
				buttons: [
					{ 
						text: "Apply Discount",
						click: function() { 
							$.post("pharma.sidatacontrol.php", { mod: "applyDiscount", cid: $("#customer_code").val(), doc_no: $("#doc_no").val(), discPercent: $("#discountPercent").val(), discType: $("#discountType").val(), lid: lineid, sid: Math.random() }, function() {
								redrawDataTable(); 
								getTotals();
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
}

function checkClear(val) {
	if(val == '' || val == 0) {
		$("#customer_code").val(''); $("#customer_name").val(''); $("#customer_address").val(''); $("#terms").val(0);
	}
}

function saveHeader() {
	var msg = "";
	if($("#cSelected").val() == "N") { msg = msg + "- Invalid or missing Customer Information<br/>"; }
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		$.post("pharma.sidatacontrol.php", { mod: "saveHeader", trace_no: $("#trace_no").val(),doc_no: $("#doc_no").val(), doc_date: $("#doc_date").val(), si_no: $("#si_no").val(), cid: $("#customer_code").val(), cname: $("#customer_name").val(), caddr: $("#customer_address").val(), terms: $("#terms").val(), sc_id: $("#scpwd_id").val(), remarks: $("#remarks").val(), sid: Math.random() }, function (data) {

		},"html");
		parent.popSaver();
	}
}

function browseSO() {
	saveHeader();
    var msg = '';

    if(msg != '') {
        parent.sendErrorMessage(msg);
    } else {
        $.post("pharma.sidatacontrol.php", {
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
										url: "pharma.sidatacontrol.php",
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
													parent.sendErrorMessage("Sales Order Currently loaded to an active document. Please see Document Seq No. <a href=\"#\" onclick=\"parent.viewPharmaSI("+data['doc_no']+");\">"+data['docno']+"</a> dtd. "+data['date']+"");
												break;
												case "ok":

													$("#scpwd_id").val(data['scpwd_id']);
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
						$.post("pharma.sidatacontrol.php", {
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

	var terms = $("#terms").val();
	var msg = "";

	if(terms == '0') {
		var balance = parseFloat(parent.stripComma($("#balance").val()));

		if(balance > 0) {
			msg = msg + "- Payment has not been settled yet for this cash transasction..";
		}
	}


	if(msg != '') {
		parent.sendErrorMessage(msg);
	} else {
		if(confirm("Are you sure you want to finalize this Transaction?") == true) { 
	
			$.post("pharma.sidatacontrol.php", { mod: "check4print", doc_no: $("#doc_no").val(), sid: Math.random() }, function(data) {
				if(data == "noerror") {
					$("#uppermenus").html('');
					$.post("pharma.sidatacontrol.php", { mod: "finalize", doc_no: $("#doc_no").val(), sid: Math.random() }, function() {
						parent.viewPharmaSI($("#doc_no").val());
						printSO();
					});
				} else {
					switch(data) {
						case "head": parent.sendErrorMessage("Unable to finalize this document as it seems it hasn't been saved yet."); break;
						case "det": parent.sendErrorMessage("Unable to finalize this document as it seems products or services haven't been added yet."); break;
						case "both": parent.sendErrorMessage("Unable to finalize this document as it seems it hasn't been saved yet."); break;
					}
				}
			},"html");
		}

	}
}

function reopen() {
	if(confirm("Are you sure you want to set this document to active status?") == true) {
		$.post("pharma.sidatacontrol.php", { mod: "reopen", doc_no: $("#doc_no").val(), terms: $("#terms").val(), sid: Math.random() }, function() {
			parent.viewPharmaSI($("#doc_no").val()); 
		});
	}
}

function cancel() {

	if(confirm("Are you sure you want to Cancel this document?") == true) {
		$.post("pharma.sidatacontrol.php", { mod: "cancel", doc_no: $("#doc_no").val(), sid: Math.random() }, function(){
			alert("Sales Invoice Successfully Cancelled!");
			parent.viewPharmaSI($("#doc_no").val());
		});
	}
}

function reuse() {
	if(confirm("Are you sure you want to Recycle this document?") == true) {
		$.post("pharma.sidatacontrol.php", { mod: "reopen", doc_no: $("#doc_no").val(), sid: Math.random() }, function(){
			parent.viewPharmaSI($("#doc_no").val());
		});
	}
}

function printSI() {
	var doc_no = $("#doc_no").val();
	parent.printPharmaSI(doc_no);
}