$(document).ready(function($){
	  $('#description').autocomplete({
		source:'suggestItemsCost.php', 
		minLength:3,
		select: function(event,ui) {
			$("#product_code").val(ui.item.item_code);
			$("#unit_price").val(parent.kSeparator(ui.item.unit_price));
			$("#unit").val(ui.item.unit);
			$("#qty").focus();
		}
	});
});

$(document).ready(function($){
	$('#customer_id').autocomplete({
		source:'suggestContacts.php', 
		minLength:3,
		select: function(event,ui) {
			$("#cSelected").val('Y');
			$("#customer_id").val(ui.item.cid);
			$("#customer_name").val(decodeURIComponent(ui.item.cname));
			$("#cust_address").val(decodeURIComponent(ui.item.addr));
		}
	});
});

function checkLockDate(el,myDate,prevDate) {
	$.post("src/sjerp.php", { mod: "checkDateLock", myDate: myDate, sid: Math.random() }, function(ret) {
		if(ret != "Ok") {
			parent.sendErrorMessage("Unable to change document as the period you have specified appears to already have been marked as locked!");
			document.getElementById(el).value = prevDate;
		}
	},"html");
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

function showLoaderMessage() {
	$("#loaderMessage").dialog({ width: 400, height: 150, closable: false, modal: true,  open: function(event, ui) {
        $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
    }});
}


function saveAdjHeader() {
	var msg = "";
	if($("#cSelected").val() == "N") { msg = msg + "- Customer / Supplier Information is missing or incorrect.<br/>"; }
	if($("#ref_no").val() == "") { msg = msg + "- Please specify a valid reference no.<br/>"; }
	if($("#ref_date").val() == "") { msg = msg + "- Please specify a valid reference date.<br/>"; }
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else{
		$.post("adj.datacontrol.php", { mod: "saveHeader", doc_no: $("#doc_no").val(), doc_date: $("#doc_date").val(), cid: $("#customer_id").val(), cname: $("#customer_name").val(), addr: $("#cust_address").val(), requested_by: $("#requested_by").val(), adj_type: $("#adjustment_type").val(), ref_type: $("#ref_type").val(), ref_no: $("#ref_no").val(), ref_date: $("#ref_date").val(), remarks: $("#remarks").val(), sid: Math.random() },function(data){
			$("#loaderMessage").dialog("close");
		});
	}
}

function addDetails() {
	var msg = "";
	var icode = $("#product_code").val();
	var idesc = $("#description").val();
	var qty = parseFloat(parent.stripComma($("#qty").val()));
	var price = parseFloat(parent.stripComma($("#unit_price").val()));
	var amount = parseFloat(parent.stripComma($("#amount").val()));

	if(icode == "") { msg = msg + "- Product Code not specified<br/>"; }
	if(idesc == "") { msg = msg + "- Product Description not specified<br/>"; }
	if(isNaN(qty) == true || qty == "") { msg = msg + "- Invalid Quantity<br/>"; }
	if(isNaN(price) == true || price == "") { msg = msg + "- Invalid Unit Price<br/>"; }
	if(isNaN(amount) == true || amount == "") { msg = msg + "- Invalid Amount<br/>"; }
	
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		$.post("adj.datacontrol.php", { mod: "insertDetail", doc_no: $("#doc_no").val(), icode: icode, desc: idesc, qty: qty, unit: $("#unit").val(), price: price, amount: amount, sid: Math.random() }, function(data) {
			$("#podetails").html(data);
			$("#product_code").val('');
			$("#description").val('');
			$("#unit_price").val('');
			$("#unit").val('');
			$("#qty").val('');
			$("#amount").val('');
			$("#description").focus();
		},"html");
	}
}

function deleteDetails(lid,doc_no) {
	if(confirm("Are you sure you want to remove this entry?") == true) {
		$.post("adj.datacontrol.php", { mod: "deleteDetails", lid: lid, doc_no: doc_no, sid: Math.random() }, function(data) { $("#podetails").html(data); $("#description").focus(); },"html");
	}

}

function usabQty(doc_no,lineid,price,type) {
	var txtobj = 'qty['+lineid+']';
	var objamt1 = 'amt['+lineid+']';

	var cur_qty = document.getElementById(txtobj).value;
		
		if(type == 'up') { newqty = parseFloat(cur_qty) + 1; } else { newqty = parseFloat(cur_qty) - 1; }
		newqty = newqty.toFixed(4);
	if(newqty < 1) { 
		parent.sendErrorMessage("Quantity must not be lower than one, other wise, you may delete the entire line should you wish this item to be not included in this Receiving Report..."); 
	} else {
	   document.getElementById(txtobj).value = newqty;
	   $.post("adj.datacontrol.php", { mod: "usabQty", lid: lineid, val: newqty, price: price, doc_no: doc_no, sid: Math.random() }, function(data) {
			$("#amtGT").html(data['amt2']);
			document.getElementById(objamt1).innerHTML = data['amt1'];
	   },"json");
	}
}

function updateQty(val,doc_no,lineid,price) {
	var txtobj = 'qty['+lineid+']';
	var objamt1 = 'amt['+lineid+']';

	$.post("adj.datacontrol.php", { mod: "usabQty", lid: lineid, val: val, price: price, doc_no: doc_no, sid: Math.random() }, function(data) {
		$("#amtGT").html(data['amt2']);
		document.getElementById(objamt1).innerHTML = data['amt1'];
	  },"json");
}

function printADJ(doc_no,uid) {
	$.post("adj.datacontrol.php", { mod: "check4print", doc_no: doc_no, sid: Math.random() }, function(data) { 
		if(data == "noerror") {
			showLoaderMessage();
			$.post("adj.datacontrol.php", { mod: "finalizeADJ", doc_no: doc_no, sid: Math.random() }, function() {
				$("#loaderMessage").dialog("close");
				location.reload();
				//parent.viewADJ(doc_no);
			});
		} else {
			switch(data) {
				case "head": parent.sendErrorMessage("Unable to print document. Document is not yet saved..."); break;
				case "det": parent.sendErrorMessage("Unable to print document. It seems that you have not added any product yet to this Purchase Order..."); break;
				case "both": parent.sendErrorMessage("There is nothing to print. Please make it sure you have saved entries you've made in this Purchase Order..."); break;
			}
		}
	},"html");
}

function reopenADJ(doc_no) {
	if(confirm("Are you sure you want to set this document to active status?") == true) {
		$.post("adj.datacontrol.php", { mod: "reopenADJ", doc_no: doc_no, sid: Math.random() }, function() {
			location.reload();
			//parent.viewADJ(doc_no);
		});
	}
}

function cancelPO(doc_no) {
	if(confirm("Are you sure you want to Cancel this document?") == true) {
		$.post("adj.datacontrol.php", { mod: "cancel", doc_no: doc_no, sid: Math.random() }, function(){
			alert("Receving Report Successfully Cancelled!");
			parent.showPOList();
		});
	}
}

function reusePO(doc_no) {
	if(confirm("Are you sure you want to Recycle this document?") == true) {
		$.post("adj.datacontrol.php", { mod: "reopenADJ", doc_no: doc_no, sid: Math.random() }, function(){
			parent.viewADJ(doc_no);
		});
	}
}

function reprintPO(doc_no,uid) {
	parent.printPO(doc_no,uid);
}
