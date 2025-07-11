function computeItemAmount(qty) {
	if(isNaN(qty) == true) { parent.sendErrorMessage("-Invalid Quantity!"); $("#itemQty").val(''); }
	var cost = parseFloat(parent.stripComma($("#itemCost").val()));

	var amount = qty * cost;
	amount = amount.toFixed(2);

	$("#itemAmount").val(parent.kSeparator(amount));
}

function getTotals() {
	$.post("pharma.sodatacontrol.php", { mod: "getTotals", so_no: $("#so_no").val(), sid: Math.random() }, function(amt) {
		$("#grossSales").val(amt['gross']);
		$("#salesDiscount").val(amt['discount']);
		$("#subTotal").val(amt['net']);
		$("#amtDue").val(amt['due']);
	},"json");

}

// function addItem() {
// 	saveSOHeader();
// 	$("#itemEntry").dialog({
// 		title: "Add Item", 
// 		width: 440, 
// 		resizable: false, 
// 		modal: true, 
// 		buttons: [
// 			{ 
// 				text: "Add Item",
// 				click: function() { 
// 					var msg = "";

// 					if($("#itemCode").val() == "") { msg = msg + "- Invalid Item Code<br/>"; }
// 					if(isNaN($("#itemQty").val()) == true) { msg = msg + "- Invalid Quantity<br/>"; }

// 					if(msg != '') {
// 						parent.sendErrorMessage(msg);
					
// 					} else {
// 						$.post("pharma.sodatacontrol.php", { 
// 							mod: "addItem", 
// 							so_no: $("#so_no").val(), 
// 							scpwd_id: $("#scpwd_id").val(),
// 							trace_no: $("#trace_no").val(), 
// 							item: $("#itemCode").val(), 
// 							description: $("#itemDescription").val(), 
// 							unit: $("#itemUnit").val(), 
// 							qty: $("#itemQty").val(),
// 							cost: $("#itemCost").val(),
// 							disc: $("#itemDiscount").val(), 
// 							amount: $("#itemAmount").val(), 
// 							sid: Math.random() }, 
// 						function(gt) {
// 							redrawDataTable();
// 							getTotals();
// 							$("#frmItemEntry").trigger("reset");
// 						});
// 					}
// 				},
// 				icons: { primary: "ui-icon-check" }
// 		    }, 
// 			{ 
// 				text: "Close",
// 				click: function() { $(this).dialog("close"); $("#frmItemEntry").trigger("reset"); },
// 				icons: { primary: "ui-icon-closethick" }
// 			}
// 		]
// 	});
// }

function addItem() {
	saveSOHeader();
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
						$.post("pharma.sodatacontrol.php", { 
							mod: "addItem",
							so_no: $("#so_no").val(), 
							scpwd_id: $("#scpwd_id").val(),
							trace_no: $("#trace_no").val(), 
							item: $("#itemCode").val(), 
							description: $("#itemDescription").val(), 
							unit: $("#itemUnit").val(), 
							qty: $("#itemQty").val(),
							cost: $("#itemCost").val(),
							disc: $("#itemDiscount").val(), 
							amount: $("#itemAmount").val(), 
							discPercent: $("#discountPercent").val(), 
							discType: $("#discountType").val(),							
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
			if(confirm("Are you sure you want to remove this line entry?") == true) {
				$.post("pharma.sodatacontrol.php", { mod: "deleteLine", lid: lid, so_no: $("#so_no").val(), scpwd_id: $("#scpwd_id").val(), code: code, so_no: $("#so_no").val(), sid: Math.random() }, 
				 function() { getTotals(); redrawDataTable(); });
			}		
	}
}

// function applyDiscount(){
// 	saveSOHeader();
// 	var lineid;
// 	var table = $("#details").DataTable();
// 	$.each(table.rows('.selected').data(), function() {
// 		lineid = this["id"];
// 	});

//    if(!lineid) {
// 		parent.sendErrorMessage("Please select a line entry you wish to apply for discount.");
// 	} else {
// 		if($("#scpwd_id").val() != '') {
// 			parent.sendErrorMessage("It appears that this patient is a Senior Citizen or PWD and shall be automatically be given the government mandated 20% discounts")
// 		} else {
// 			var dis = $("#discounter").dialog({
// 				title: "Line Discount", 
// 				width: 440, 
// 				resizable: false, 
// 				modal: true, 
// 				buttons: [
// 					{ 
// 						text: "Apply Discount",
// 						click: function() { 
// 							$.post("pharma.sodatacontrol.php", { mod: "applyDiscount", cid: $("#customer_code").val(), so_no: $("#so_no").val(), discPercent: $("#discountPercent").val(), discType: $("#discountType").val(), lid: lineid, sid: Math.random() }, function() {
// 								redrawDataTable(); 
// 								getTotals();
// 								dis.dialog("close");
// 							});
// 						},
// 						icons: { primary: "ui-icon-check" }
// 					},
// 					{
// 						text: "Close",
// 						click: function() { $(this).dialog("close"); },
// 						icons: { primary: "ui-icon-closethick" }
// 					}
// 				]
// 			});
// 		}
// 	}
// }

function updateItem() {
	var table = $("#details").DataTable();
	var arr = [];
	$.each(table.rows('.selected').data(), function() {
		arr.push(this["id"]);
	});

	if(!arr[0]) {
		parent.sendErrorMessage("Please select a record to update.");
	} else {
		
		$.post("pharma.sodatacontrol.php", { mod: "retrieveLine", lid: arr[0], sid: Math.random() }, function(data) { 
			$("#itemDescription").val(parent.decodeEntities(data['description']));
			$("#itemCode").val(data['item_code']);
			$("#itemUnit").val(data['unit']);
			$("#itemQty").val(data['qty']);
			$("#itemCost").val(data['ucost']);
			$("#itemDiscount").val(data['discount']);
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
									$.post("pharma.sodatacontrol.php", { 
										mod: "updateItem", 
										lid: arr[0],
										so_no: $("#so_no").val(), 
										trace_no: $("#trace_no").val(), 
										scpwd_id: $("#scpwd_id").val(),
										item: $("#itemCode").val(), 
										description: $("#itemDescription").val(), 
										unit: $("#itemUnit").val(), 
										qty: $("#itemQty").val(),
										cost: $("#itemCost").val(),
										disc: $("#itemDiscount").val(),
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

function checkClear(val) {
	if(val == '' || val == 0) {
		$("#customer_code").val(''); $("#customer_name").val(''); $("#customer_address").val(''); $("#terms").val(0);
	}
}

function saveSOHeader() {
	var msg = "";
	if($("#cSelected").val() == "N") { msg = msg + "- Invalid or missing Customer Information<br/>"; }
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		$.post("pharma.sodatacontrol.php", { mod: "saveHeader", trace_no: $("#trace_no").val(),so_no: $("#so_no").val(), csi_no: $("#csi_no").val(), so_date: $("#so_date").val(), pid: $("#patient_id").val(), pname: $("#patient_name").val(), paddr: $("#patient_address").val(), cid: $("#customer_code").val(), cname: $("#customer_name").val(), caddr: $("#customer_address").val(), terms: $("#terms").val(), sc_id: $("#scpwd_id").val(), physician: $("#physician").val(), remarks: $("#remarks").val(), disc_type: $("#discountType").val(), disc_percent: $("#discountPercent").val(), sid: Math.random() }, function (data) {
			if($("#so_no").val() == "") { $("#so_no").val(data); }
		},"html");
		parent.popSaver();
	}
}

function finalizeSO() {
	if(confirm("Are you sure you want to finalize this Sales Order?") == true) { 
	
		$.post("pharma.sodatacontrol.php", { mod: "check4print", so_no: $("#so_no").val(), sid: Math.random() }, function(data) {
			if(data == "noerror") {
				$("#uppermenus").html('');
				$.post("pharma.sodatacontrol.php", { mod: "finalize", so_no: $("#so_no").val(), sid: Math.random() }, function() {
					parent.viewPharmaSO($("#so_no").val());
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

function reopen() {
	$.post("pharma.sodatacontrol.php", { mod: "checkBilled", so_no: $("#so_no").val(), sid: Math.random() }, function(stat) {
		if(stat == 'processed') {
			parent.sendErrorMessage("- It appears that this Sales Order has already been paid or billed...");
		} else {
			if(confirm("Are you sure you want to set this document to active status?") == true) {
				$.post("pharma.sodatacontrol.php", { mod: "reopen", so_no: $("#so_no").val(), terms: $("#terms").val(), sid: Math.random() }, function() {
					parent.viewPharmaSO($("#so_no").val()); 
				});
			}
		}
	},"html");
}

function cancel() {

	if(confirm("Are you sure you want to Cancel this document?") == true) {
		$.post("pharma.sodatacontrol.php", { mod: "cancel", so_no: $("#so_no").val(), sid: Math.random() }, function(){
			alert("Sales Order Successfully Cancelled!");
			parent.viewPharmaSO($("#so_no").val());
		});
	}
}

function reuse() {
	if(confirm("Are you sure you want to Recycle this document?") == true) {
		$.post("pharma.sodatacontrol.php", { mod: "reopen", so_no: $("#so_no").val(), sid: Math.random() }, function(){
			parent.viewPharmaSO($("#so_no").val());
		});
	}
}

function printSO() {
	var so_no = $("#so_no").val();
	parent.printPharmaSO(so_no);
}

function printSOLetter() {
	var so_no = $("#so_no").val();
	parent.printPharmaSOLetter(so_no);
}

function printCSI() {
	var so_no = $("#so_no").val();
	parent.printPharmaCSI(so_no);
}

function verify() {
	if(confirm("Are you sure you want to mark this on-account transaction as verified?") == true) {
		$.post("pharma.sodatacontrol.php", {
			mod: "verify",
			so_no: $("#so_no").val(),
			sid: Math.random() },
			function() {
				alert("On-Accounts transaction successfully verified!");
			}
		);

	}

}