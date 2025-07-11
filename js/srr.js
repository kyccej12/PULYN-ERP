function computeItemAmount(qty) {
	if(isNaN(qty) == true) { parent.sendErrorMessage("-Invalid Quantity!"); $("#itemQty").val(''); }
	var cost = parseFloat(parent.stripComma($("#itemCost").val()));
	
	var amount = qty * cost;
		amount = amount.toFixed(2);

	$("#itemAmount").val(parent.kSeparator(amount));
}

function addItem() {
	$("#itemEntry").dialog({title: "Add Item", width: 440, resizable: false, modal: true, buttons: { 
			"Add Item": function() { 
				var msg = "";

				if($("#itemCode").val() == "") { msg = msg + "- Invalid Item Code<br/>"; }
				if(isNaN($("#itemQty").val()) == true) { msg = msg + "- Invalid Quantity<br/>"; }

				if(msg != '') {
					parent.sendErrorMessage(msg);
				
				} else {
					$.post("srr.datacontrol.php", { 
						mod: "addItem", 
						srr_no: $("#srr_no").val(), 
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
			},
			"Cancel": function() { $(this).dialog("close"); $("#frmItemEntry").trigger("rest"); }
		}
	
	});
}

function deleteItem(){
	var table = $("#details").DataTable();
	var arr = [];
   $.each(table.rows('.selected').data(), function() {
	   arr.push(this["id"]);
   });
  
	if(!arr[0]) {
		parent.sendErrorMessage("Please select a record to delete.");
	} else {
		if(confirm("Are you sure you want to remove this line entry?") == true) {
			$.post("sw.datacontrol.php", { mod: "deleteLine", lid: arr[0], srr_no: $("#srr_no").val(), sid: Math.random() }, function(gt) { redrawDataTable(); $("#grandTotal").val(gt); });
		}
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
		
		$.post("srr.datacontrol.php", { mod: "retrieveLine", lid: arr[0], sid: Math.random() }, function(data) { 
			$("#itemDescription").val(parent.decodeEntities(data['description']));
			$("#itemCode").val(data['item_code']);
			$("#itemUnit").val(data['unit']);
			$("#itemQty").val(data['qty']);
			$("#itemCost").val(data['ucost']);
			$("#itemAmount").val(data['amt']);
		
			$("#itemEntry").dialog({title: "Update Line Entry", width: 440, resizable: false, modal: true, buttons: { 
					"Save Changes": function() { 
						var msg = "";
		
						if($("#itemCode").val() == "") { msg = msg + "- Invalid Item Code<br/>"; }
						if(isNaN($("#itemQty").val()) == true) { msg = msg + "- Invalid Quantity<br/>"; }
		
						if(msg != '') {
							parent.sendErrorMessage(msg);
						
						} else {
							if(confirm("Are you sure you want to save changes made to this entry?") == true) {
								$.post("srr.datacontrol.php", { 
									mod: "updateItem", 
									lid: arr[0],
									srr_no: $("#srr_no").val(), 
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
					"Cancel": function() { $(this).dialog("close"); $("#frmItemEntry").trigger("reset"); }
				}
			
			});	
		
		},"json");
	}
}

function saveSRRHeader() {
	var msg = "";
	if($("#received_from").val() == "") { msg = msg + "- Please specify where the item(s) came from by filling up \"<b>Received From</b>\" input field..."; }
	if($("#received_by").val() == "") { msg = msg + "- Please identify the person who received and evaluated the item(s) by filling up \"<b>Checked & Received By</b>\" input field..."; }
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		$.post("srr.datacontrol.php", { mod: "saveHeader", trace_no: $("#trace_no").val(), srr_no: $("#srr_no").val(), srr_date: $("#srr_date").val(), from: $("#received_from").val(), by: $("#received_by").val(), ref_type: $("#ref_type").val(), ref_no: $("#ref_no").val(), ref_date: $("#ref_date").val(), remarks: $("#remarks").val(), sid: Math.random() }, function(data) { if($("#srr_no").val() == "") { $("#srr_no").val(data); } parent.popSaver(); });
	}
}

function finalizeSRR(srr_no,uid) {	
	$.post("srr.datacontrol.php", { mod: "check4print", srr_no: srr_no, sid: Math.random() }, function(data) { 
		if(data == "noerror") {
			if(confirm("Are you sure you want to finalize this Stocks Receiving Receipt?") == true) {
				$.post("srr.datacontrol.php", { mod: "finalizeSRR", srr_no: srr_no, srr_date: $("#srr_date").val(), ref_type: $("#ref_type").val(), remarks: $("#remarks").val(), sid: Math.random() }, function() {
					parent.viewSRR(srr_no);
				});
			}
		} else {
			switch(data) {
				case "head": parent.sendErrorMessage("Unable to print document. Document is not yet saved..."); break;
				case "det": parent.sendErrorMessage("Unable to print document. It seems that you have not added any product yet to this Stocks Receiving Receipt..."); break;
				case "both": parent.sendErrorMessage("There is nothing to print. Please make it sure you have saved entries you've made in this Stocks Receiving Receipt..."); break;
			}
		}
	},"html");
}

function reopenSRR(srr_no) {
	if(confirm("Are you sure you want to set this document to active status?") == true) {
		$.post("srr.datacontrol.php", { mod: "reopenSRR", srr_no: srr_no, sid: Math.random() }, function() {
			parent.viewSRR(srr_no);
		});
	}
}

function cancelSRR(srr_no) {
	if(confirm("Are you sure you want to Cancel this document?") == true) {
		$.post("srr.datacontrol.php", { mod: "cancel", srr_no: srr_no, sid: Math.random() }, function(){
			alert("Stocks Transfer Receipt Successfully Cancelled!");
			parent.showSRR();
		});
	}
}

function reuseSRR(srr_no) {
	if(confirm("Are you sure you want to Recycle this document?") == true) {
		$.post("srr.datacontrol.php", { mod: "reopenPO", srr_no: srr_no, sid: Math.random() }, function(){
			parent.viewSRR(srr_no);
		});
	}
}

function reprintSRR(srr_no,uid) {
	window.open("print/srr.print.php?srr_no="+srr_no+"&sid="+Math.random()+"&user="+uid+"&reprint=Y","Sales Order","location=1,status=1,scrollbars=1,width=640,height=720");
}