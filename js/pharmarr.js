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

function itemLookup(inputString,el) {
	$("#isSearch").val(1);
	if(inputString.length == 0) {
		$('#suggestions').hide();
	} else {
		var op = $("#"+el+"").offset();
		$.post("itemlookupcost.php", {queryString: ""+inputString+"" }, function(data){
		if(data.length > 0) {
			$('#suggestions').css({top: op.top+20, left: op.left, width: '500px'});
			$('#suggestions').show();
			$('#autoSuggestionsList').html(data);
		} else { $("#suggestions").hide(); }
		});
	}
}

function computeItemAmount(qty) {
	if(isNaN(qty) == true) { parent.sendErrorMessage("-Invalid Quantity!"); $("#itemQty").val(''); }
	var cost = parseFloat(parent.stripComma($("#itemCost").val()));
	
	var amount = qty * cost;
		amount = amount.toFixed(2);

	$("#itemAmount").val(parent.kSeparator(amount));
}

function getTotals() {
	$.post("pharmarr.datacontrol.php", { mod: "getTotals", rr_no: $("#rr_no").val(), sid: Math.random() }, function(data) {
		$("#total_amount").val(data['amt']);
	},"json");
}

function savePRRHeader() {
	var msg = "";
	if($("#cSelected").val() == "N") { msg = msg + "- Invalid Supplier Details.<br/>"; }
	if($("#invoice_no").val() == "") { msg = msg + "- Please Specify Purchase Reference (Invoice, DR, etc.) for this Receivng Report.<br/>"; }
	if($("#invoice_date").val() == "") { msg = msg + "- Please Specify Purchase Reference Date for Receiving Report<br/>"; }
	
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		$.post("pharmarr.datacontrol.php", { mod: "saveHeader", trace_no: $("#trace_no").val(), rr_no: $("#rr_no").val(), rr_date: $("#rr_date").val(), cid: $("#customer_id").val(), cname: $("#customer_name").val(), addr: $("#cust_address").val(), recby: $("#received_by").val(), ino: $("#invoice_no").val(), idate: $("#invoice_date").val(), remarks: $("#remarks").val(), sid: Math.random() },function(data){
			$("#rr_no").val(data);
			parent.popSaver();
		});
	}
}

function checkDuplicateInvoice(val) {
	$.post("pharmarr.datacontrol.php", {mod: "checkDuplicateInvoice", ref_no: val, cust: $("#customer_id").val(), sid: Math.random() }, function(data) {
		if(data['err_msg'] == "DUP") {
			parent.sendErrorMessage("Duplicate Reference No. Detected for this Supplier. <br/><br/><b>RR No.:</b> "+data['rr_no'] + "<br/><b>RR Date: </b>" +data['rr_date']);
			$("#invoice_no").val('');
		}
	},"json");
}

function downloadPharmaPO() {
	if($("#customer_id").val() == "") {
		parent.sendErrorMessage("Please select supplier first before downloading unserved Purchase Orders.");
	} else {
		$.post("pharmarr.datacontrol.php", { mod: "getPOS", rr_no: $("#rr_no").val(), cid: $("#customer_id").val(), sid: Math.random() }, function(data) {
			if(data.length > 0) {
				$("#invoiceAttachment").html(data);
				$("#invoiceAttachment").dialog({title: "Unserved Purchase Orders", width: 640, height: 360, resizable: false, modal: true, buttons: [
					{
						text: "Upload Purchase Order",
						click: function() { loadPO(); },
						icons: { primary: "ui-icon-check" }
					},
					{
						text: "Close Window",
						click: function() { $(this).dialog("close"); },
						icons: { primary: "ui-icon-closethick" }
					}]
				 });
			} else {
				parent.sendErrorMessage("Unable to find any Purchase Order issued to this supplier. Make sure you have specified the correct supplier code...");
			}
		});
	}
}

function tagPO(el,val) {
	var obj = document.getElementById(el);
	var myURL;
	if(obj.checked == true) { var push = "Y"; } else { var push = "N"; }
	$.post("pharmarr.datacontrol.php", { mod: "tagPO", push: push, val: val, sid: Math.random() });
}

function loadPO() {
	$.post("pharmarr.datacontrol.php", { mod: "loadPO", trace_no: $("#trace_no").val(), rr_no: $("#rr_no").val(), sid: Math.random() }, function() {
		getTotals();
		redrawDataTable();
		$("#invoiceAttachment").dialog("close");
	});
}

function deletePharmaItem(){
	var table = $("#details").DataTable();
	var arr = [];
   $.each(table.rows('.selected').data(), function() {
	   arr.push(this["id"]);
   });
  
	if(!arr[0]) {
		parent.sendErrorMessage("Please select a record to delete.");
	} else {
		if(confirm("Are you sure you want to remove this line entry?") == true) {
			$.post("pharmarr.datacontrol.php", { mod: "deleteLine", lid: arr[0], rr_no: $("#rr_no").val(), sid: Math.random() }, function() { redrawDataTable(); getTotals(); });
		}
	}
}

function updateItem(){
	
	var table = $("#details").DataTable();
   	$.each(table.rows('.selected').data(), function() {
	   id = this["id"];
	   cc = this['costcenter'];
	   description = this['description'];
	   item_code = this['item_code'];
	   unit = this['unit'];
	   qty = this['qty'];
	   cost = this['cost'];
	   amount = this['amount'];
	   po = this['po'];
	   podate = this['podate'];
   });
  
	if(!id) {
		parent.sendErrorMessage("Please select line entry to update.");
	} else {
		$("#recordId").val(id);
		$("#itemDescription").val(description);
		$("#itemCode").val(item_code);
		$("#itemUnit").val(unit);
		$("#itemQty").val(qty);
		$("#itemCost").val(cost);
		$("#itemAmount").val(amount);
		$("#itemCostCenter").val(cc);
		$("#itemPONo").val(po);
		$("#itemPODate").val(podate);

		var dis = $("#itemEntry").dialog({title: "Add Item", width: 440, resizable: false, modal: true, buttons: [
			{ 
				text: "Update Line Entry",
				icons: { primary: "ui-icon-check" },
				click: function() { 
						var msg = "";
	
						if($("#itemCode").val() == "") { msg = msg + "- Invalid Item Code<br/>"; }
						if(isNaN($("#itemQty").val()) == true) { msg = msg + "- Invalid Quantity<br/>"; }
	
						if(msg != '') {
							parent.sendErrorMessage(msg);
						} else {
							if(confirm("Are you sure you want to save changes made to this entry?") == true) {
								$.post("pharmarr.datacontrol.php", { 
									mod: "updateItem",
									lid: $("#recordId").val(), 
									rr_no: $("#rr_no").val(), 
									trace_no: $("#trace_no").val(), 
									item: $("#itemCode").val(), 
									description: $("#itemDescription").val(), 
									unit: $("#itemUnit").val(), 
									qty: $("#itemQty").val(),
									cost: $("#itemCost").val(), 
									amount: $("#itemAmount").val(),
									costcenter: $("#itemCostCenter").val(),
									po: $("#itemPONo").val(),
									podate: $("#itemPODate").val(),
									sid: Math.random() }, 
								function(gt) {
									redrawDataTable();
									getTotals();
									$("#frmItemEntry").trigger("reset");
									dis.dialog("close");
								});	
							}
						}
					}
			},
			{
	
				text: "Close",
				icons: { primary: "ui-icon-close" },
				click: function() { $(this).dialog("close"); $("#frmItemEntry").trigger("reset"); }
			}]
		});
	}
}

function finalizePRR() {
	$.post("pharmarr.datacontrol.php", { mod: "check4print", rr_no: $("#rr_no").val(), sid: Math.random() }, function(data) { 
		if(data == "noerror") {
			if(confirm("Are you sure you want to finalize this Receiving Report?") == true) {
				$.post("pharmarr.datacontrol.php", { mod: "finalizeRR", rr_no: $("#rr_no").val(), sid: Math.random() }, function() {
					parent.viewPharmaRR($("#rr_no").val());
				});
			}
		} else {
			switch(data) {
				case "head": parent.sendErrorMessage("Unable to print document. Document is not yet saved..."); break;
				case "det": parent.sendErrorMessage("Unable to print document. It seems that you have not added any product yet to this Receiving Report..."); break;
				case "both": parent.sendErrorMessage("There is nothing to print. Please make it sure you have saved what entries you've made..."); break;
			}
		}
	},"html");
}

function reopenPharmaRR() {
	if(confirm("Are you sure you want to set this document to active status?") == true) {
		$.post("pharmarr.datacontrol.php", { mod: "reopenPharmaRR", rr_no: $("#rr_no").val(), sid: Math.random() }, function() {
			location.reload();
		});
	}
}

function cancelRR() {
	if(confirm("Are you sure you want to Cancel this document?") == true) {
		$.post("pharmarr.datacontrol.php", { mod: "cancel", rr_no:  $("#rr_no").val(), sid: Math.random() }, function(){
			alert("Receving Report Successfully Cancelled!");
			parent.showPharmaRR();
		});
	}
}

function reuseRR() {
	if(confirm("Are you sure you want to Recycle this document?") == true) {
		$.post("pharmarr.datacontrol.php", { mod: "reopenPharmaRR", rr_no:  $("#rr_no").val(), sid: Math.random() }, function(){
			location.reload();
		});
	}
}


function addPharmaItem() {
	$("#itemEntry").dialog({title: "Add Item", width: 440, resizable: false, modal: true, buttons: { 
			"Add Item": function() { 
				var msg = "";

				if($("#itemCode").val() == "") { msg = msg + "- Invalid Item Code<br/>"; }
				if(isNaN($("#itemQty").val()) == true) { msg = msg + "- Invalid Quantity<br/>"; }

				if(msg != '') {
					parent.sendErrorMessage(msg);
				
				} else {
					$.post("pharmarr.datacontrol.php", { 
						mod: "addPharmaItem",
						rr_no: $("#rr_no").val(), 
						costcenter: $("#itemCostCenter").val(),
						trace_no: $("#trace_no").val(), 
						item: $("#itemCode").val(), 
						description: $("#itemDescription").val(), 
						unit: $("#itemUnit").val(), 
						qty: $("#itemQty").val(),
						cost: $("#itemCost").val(), 
						amount: $("#itemAmount").val(), 
						po: $("#itemPONo").val(), 
						podate: $("#itemPODate").val(), 
						sid: Math.random() }, 
					function(gt) {
						redrawDataTable();
						getTotals();
						$("#frmItemEntry").trigger("reset");
						dis.dialog("close");
						
					});
				}
			},
			"Cancel": function() { $(this).dialog("close"); $("#frmItemEntry").trigger("rest"); }
		}
	
	});
}
