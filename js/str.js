function getTotals() {
	$.post("str.datacontrol.php", { mod: "getTotals", str_no: $("#str_no").val(), sid: Math.random() }, function(data) {
		$("#grandTotal").val(data['total']);
	},"json");
}

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

function saveHeader() {
	var msg = "";
	if($("#transferred_to").val() == "") { msg = msg + "- Please specify the the destination branch for this stocks transfer..."; }
	
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		$.post("str.datacontrol.php", { mod: "saveHeader", str_no: $("#str_no").val(), str_date: $("#str_date").val(), transto: $("#transferred_to").val(), by: $("#requested_by").val(), request_date: $("#request_date").val(), remarks: $("#remarks").val(), sid: Math.random() }, function() { parent.popSaver(); });
	}
}

function addDetails() {
	var msg = "";
	//alert($("#qty").val());
	var icode = $("#product_code").val();
	var idesc = $("#description").val();
	var qty = parseFloat(parent.stripComma($("#qty").val()));

	if(icode == "") { msg = msg + "- Product Code not specified<br/>"; }
	if(idesc == "") { msg = msg + "- Product Description not specified<br/>"; }
	if(isNaN(qty) == true || qty == "") { msg = msg + "- Invalid Quantity<br/>"; }
	
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else {
		$.post("str.datacontrol.php", { mod: "insertDetail", str_no: $("#str_no").val(), icode: icode, desc: idesc, qty: qty, unit: $("#unit").val(), sid: Math.random() }, function(data) {
			$("#details").html(data);
			$("#product_code").val('');
			$("#description").val('');
			$("#autodescription").val('');
			$("#unit").val('');
			$("#qty").val('');
			$("#description").focus();
		},"html");
	}
}

function deleteDetails(lid,str_no) {
	if(confirm("Are you sure you want to remove this entry?") == true) {
		$.post("str.datacontrol.php", { mod: "deleteDetails", lid: lid, str_no: str_no, sid: Math.random() }, function(data) { $("#details").html(data); getTotals(); },"html");
	}

}

function updateQty(val,str_no,lineid,oQty) {
	var val = parent.stripComma(val);
	var txtobj = 'qty['+lineid+']';

	if(isNaN(val) == true || parseFloat(val) < 0) {
		parent.sendErrorMessage("You have specified an invlaid quantity!");
		document.getElementById(txtobj).value = oQty;
	} else {	
		$.post("str.datacontrol.php", { mod: "usabQty", lid: lineid, val: val, str_no: str_no, sid: Math.random() });
	}
}

function finalizeSTR(str_no,uid) {
	$.post("str.datacontrol.php", { mod: "check4print", str_no: str_no, sid: Math.random() }, function(data) { 
		if(data == "noerror") {
			if(confirm("Are you sure you want to finalize this Stocks Transfer Receipt?") == true) {
				$.post("str.datacontrol.php", { mod: "finalizeSTR", str_no: str_no, str_date: $("#str_date").val(), trans_branch: $("#transferred_to").val(), remarks: $("#remarks").val(), sid: Math.random() }, function() {
					parent.viewSTR(str_no);
				});
			}
		} else {
			switch(data) {
				case "head": parent.sendErrorMessage("Unable to print document. Document is not yet saved..."); break;
				case "det": parent.sendErrorMessage("Unable to print document. It seems that you have not added any product yet to this Purchase Order..."); break;
				case "both": parent.sendErrorMessage("There is nothing to print. Please make it sure you have saved entries you've made in this Stocks Withdrawal Slip..."); break;
			}
		}
	},"html");
}

function reopenSTR(str_no) {
	if(confirm("Are you sure you want to set this document to active status?") == true) {
		$.post("str.datacontrol.php", { mod: "reopenSTR", str_no: str_no, sid: Math.random() }, function() {
			parent.viewSTR(str_no);
		});
	}
}

function cancelSTR(str_no) {
	if(confirm("Are you sure you want to Cancel this document?") == true) {
		$.post("str.datacontrol.php", { mod: "cancel", str_no: str_no, sid: Math.random() }, function(){
			alert("Stocks Transfer Receipt Successfully Cancelled!");
			parent.showSTR();
			parent.viewSTR(str_no);
		});
	}
}

function reuseSTR(str_no) {
	if(confirm("Are you sure you want to Recycle this document?") == true) {
		$.post("str.datacontrol.php", { mod: "reopenSTR", str_no: str_no, sid: Math.random() }, function(){
			parent.viewSTR(str_no);
		});
	}
}