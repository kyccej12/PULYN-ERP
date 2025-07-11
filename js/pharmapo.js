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
	$.post("pharmapo.datacontrol.php", { mod: "getTotals", po_no: $("#po_no").val(), sid: Math.random() }, function(data) {
		$("#total_due").val(parent.kSeparator(data['gross']));
	},"json");
}

function savePOHeader() {
	var msg = "";
	if($("#cSelected").val() == "N") { msg = msg + "- Invalid supplier or source for the Purchase Order<br/>"; }
	/* if($("#requested_by").val() == "") { msg = msg + "- You have not indicated who requested for the items on this P.O"; } */
	
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else{
		$.post("pharmapo.datacontrol.php", { mod: "saveHeader", trace_no: $("#trace_no").val(), po_no: $("#po_no").val(), po_date: $("#po_date").val(), terms: $("#terms").val(), cid: $("#customer_id").val(), cname: $("#customer_name").val(), addr: $("#cust_address").val(), requested_by: $("#requested_by").val(), mrs: $("#mrs_no").val(), del_addr: $("#delivery_address").val(), date_needed: $("#date_needed").val(), remarks: $("#remarks").val(), sid: Math.random() },function(pono){
			$("#po_no").val(pono);
			parent.popSaver();
		},"html");
	}
}

function computeItemAmount(qty) {
	if(isNaN(qty) == true) { parent.sendErrorMessage("-Invalid Quantity!"); $("#itemQty").val(''); }
	var cost = parseFloat(parent.stripComma($("#itemCost").val()));
	
	var amount = qty * cost;
		amount = amount.toFixed(2);

	$("#itemAmount").val(parent.kSeparator(amount));
}

function addItem() {
	$("#itemEntry").dialog({title: "Add Item", width: 440, resizable: false, modal: true, buttons: [
		{ 
			text: "Add Item",
			icons: { primary: "ui-icon-check" },
			click: function() { 
					var msg = "";

					if($("#itemCode").val() == "") { msg = msg + "- Invalid Item Code<br/>"; }
					if(isNaN($("#itemQty").val()) == true) { msg = msg + "- Invalid Quantity<br/>"; }

					if(msg != '') {
						parent.sendErrorMessage(msg);
					
					} else {
						$.post("pharmapo.datacontrol.php", { 
							mod: "addItem", 
							po_no: $("#po_no").val(), 
							trace_no: $("#trace_no").val(), 
							item: $("#itemCode").val(), 
							description: $("#itemDescription").val(), 
							unit: $("#itemUnit").val(), 
							qty: $("#itemQty").val(),
							cost: $("#itemCost").val(), 
							amount: $("#itemAmount").val(),
							costcenter: $("#itemCostCenter").val(),
							sid: Math.random() }, 
						function(gt) {
							redrawDataTable();
							getTotals();
							$("#frmItemEntry").trigger("reset");
							
						});	
					}
				}
		},
		{

			text: "Close",
			icons: { primary: "ui-icon-close" },
			click: function() { $(this).dialog("close"); $("#frmItemEntry").trigger("rest"); }
		}]
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
			$.post("pharmapo.datacontrol.php", { mod: "deleteLine", lid: arr[0], po_no: $("#po_no").val(), sid: Math.random() }, function() { redrawDataTable(); getTotals(); });
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
								$.post("pharmapo.datacontrol.php", { 
									mod: "updateItem",
									lid: $("#recordId").val(), 
									po_no: $("#po_no").val(), 
									trace_no: $("#trace_no").val(), 
									item: $("#itemCode").val(), 
									description: $("#itemDescription").val(), 
									unit: $("#itemUnit").val(), 
									qty: $("#itemQty").val(),
									cost: $("#itemCost").val(), 
									amount: $("#itemAmount").val(),
									costcenter: $("#itemCostCenter").val(),
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

function finalize() {
	var po_no = $("#po_no").val();
	$.post("pharmapo.datacontrol.php", { mod: "check4print", po_no: po_no, sid: Math.random() }, function(data) { 
		if(data == "noerror") {
			if(confirm("Are you sure you want to finalize this Purchase Order?") == true) {
				$.post("pharmapo.datacontrol.php", { mod: "finalizePO", po_no: po_no, sid: Math.random() }, function() { parent.viewPharmaPO(po_no); });
			}
		} else {
			switch(data) {
				case "head": parent.sendErrorMessage("Unable to print document. Document is not yet saved..."); break;
				case "det": parent.sendErrorMessage("Unable to print document. It seems that you have not added any product yet to this Purchase Order..."); break;
				case "both": parent.sendErrorMessage("There is nothing to print. Please make it sure you have saved entries you've made in this Purchase Order..."); break;
			}
		}
	},"html");
}

function reopenPO() {
	if(confirm("Are you sure you want to set this document to active status?") == true) {
		$.post("pharmapo.datacontrol.php", { mod: "reopenPO", po_no:  $("#po_no").val(), sid: Math.random() }, function() {
			location.reload();
		});
	}
}

function cancelPO() {
	if(confirm("Are you sure you want to Cancel this document?") == true) {
		$.post("pharmapo.datacontrol.php", { mod: "cancel", po_no:  $("#po_no").val(), sid: Math.random() }, function(){
			alert("Receving Report Successfully Cancelled!");
			parent.showPharmaPOList();
		});
	}
}

function reusePO() {
	if(confirm("Are you sure you want to Recycle this document?") == true) {
		$.post("pharmapo.datacontrol.php", { mod: "reopenPO", po_no:  $("#po_no").val(), sid: Math.random() }, function(){
			location.reload();
		});
	}
}

function reprintPO() {
	parent.printPharmaPO( $("#po_no").val());
}


function applyDiscount(){
	if(line===undefined){
		alert("Please select ");
	}else{
		$("#discountDiv").dialog({
			title: "Apply Line Discount",
			width: 280,
			resizable: false,
			modal: true,
			buttons: {
				
				"Apply Line Discount": function() { 
					var discount = $("#poDiscount").val(); 
					
					$.post("pharmapo.datacontrol.php",{
						mod : "applyDiscount" ,
						lineid : line,
						discount : discount,
						po_no : $("#po_no").val(),
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
	
function addDescription(){
	if(line===undefined){
		alert("Please select an item from the item list .");
	}else{
		$.post("pharmapo.datacontrol.php", { mod: "getCurrentDescription", lineid: line, sid: Math.random() }, function(ret) {
			$("#customDesc").val(ret);
			$("#itemDescDiv").dialog({ 
				title: "Custom Item Description",
				width: 540,
				resizable: false,
				modal: true,
			buttons: {
				"Save Changes": function() { 
					$.post("pharmaapo.datacontrol.php", { mod: "saveCustomDesc", po_no: $("#po_no").val(), lineid: line, desc: $("#customDesc").val(), sid: Math.random() }, function(res) {
						$("#details").html(res);
						$("#customDesc").val('');
						$("#itemDescDiv").dialog("close");
					},"html");
				},
				"Cancel": function() {
					$("#customDesc").val(''); $(this).dialog("close");
				}
			}
			});
		},"html");
	}
}