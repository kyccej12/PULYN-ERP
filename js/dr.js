function contactlookup(inputString,el) {
	if(inputString.length == 0) {
		$('#suggestions').hide();
	} else {
		var op = $("#"+el+"").offset();
		$.post("contactlookup.php", {queryString: ""+inputString+"" }, function(data){
		if(data.length > 0) {
			$('#suggestions').css({top: op.top+20, left: op.left});
			$('#suggestions').show();
			$('#autoSuggestionsList').html(data);
		} else { $("#suggestions").hide(); }
		});
	}
}

function checkLockDate(el,myDate,prevDate) {
	$.post("src/sjerp.php", { mod: "checkDateLock", myDate: myDate, sid: Math.random() }, function(ret) {
		if(ret != "Ok") {
			parent.sendErrorMessage("Unable to change document as the period you have specified appears to already have been marked as locked!");
			document.getElementById(el).value = prevDate;
		}
	},"html");
}

function pickContact(cid,cname,addr,terms) {
	$("#cSelected").val('Y');
	$("#customer_id").val(cid);
	$("#customer_name").val(decodeURIComponent(cname));
	$("#cust_address").val(decodeURIComponent(addr));

	saveDRHeader();
}

function itemLookup(inputString,el) {
	$("#isSearch").val(1);
	if(inputString.length == 0) {
		$('#suggestions').hide();
	} else {
		var op = $("#"+el+"").offset();
		$.post("itemlookup.php", {queryString: ""+inputString+"" }, function(data){
		if(data.length > 0) {
			$('#suggestions').css({top: op.top+20, left: op.left, width: '500px'});
			$('#suggestions').show();
			$('#autoSuggestionsList').html(data);
		} else { $("#suggestions").hide(); }
		});
	}
}

function pickItem(icode,idesc,cost,unit) {
	$("#product_code").val(icode);
	$("#description").val(decodeURIComponent(idesc));
	$("#unit_price").val(parent.kSeparator(cost));
	$("#unit").val(unit);
	$("#qty").focus();
}

function computeAmount() {
	var price = parseFloat(parent.stripComma($("#unit_price").val()));
	var qty = parseFloat(parent.stripComma($("#qty").val()));

	if(isNaN(qty) == true || isNaN(price) == true || qty == "" || price == "") {
		parent.sendErrorMessage("Invalid Quantity or Price. Please check your entries and try again...")
	} else {
		var amt = price * qty;
			amt = amt.toFixed(2);
		$("#amount").val(parent.kSeparator(amt));
	}

}

function saveDRHeader() {
	var msg = "";
	if($("#cSelected").val() == "N") { msg = msg + "- Invalid supplier or source for the Receiving Report<br/>"; }
	if(msg != "") {
		parent.sendErrorMessage("Unale to continue due to the follwing error(s): <br/><br/>"+msg+"");
	} else {
		$.post("dr.datacontrol.php", { mod: "saveHeader", dr_no: $("#dr_no").val(), dr_stub_no: $("#dr_stub_no").val(), dr_date: $("#dr_date").val(), cid: $("#customer_id").val(), cname: $("#customer_name").val(), addr: $("#cust_address").val(), remarks: $("#remarks").val(), sid: Math.random() });
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
		parent.sendErrorMessage("Unable to continue due to the following error(s): <br/><br/>"+msg+"");
	} else {
		$.post("dr.datacontrol.php", { mod: "insertDetail", dr_no: $("#dr_no").val(), icode: icode, desc: idesc, qty: qty, unit: $("#unit").val(), price: price, amount: amount, sid: Math.random() }, function(data) {
			$("#rrdetails").html(data)
		},"html");
	}
}

function deleteDetails(lid,dr_no) {
	if(confirm("Are you sure you want to remove this entry?") == true) {
		$.post("dr.datacontrol.php", { mod: "deleteDetails", lid: lid, dr_no: dr_no, sid: Math.random() }, function(data) { $("#rrdetails").html(data); },"html");
	}

}

function usabQty(dr_no,lineid,price,type) {
	var txtobj = 'qty['+lineid+']';
	var objamt1 = 'amt['+lineid+']';

	var cur_qty = document.getElementById(txtobj).value;
		
		if(type == 'up') { newqty = parseFloat(cur_qty) + 1; } else { newqty = parseFloat(cur_qty) - 1; }
		newqty = newqty.toFixed(2);
	if(newqty < 1) { 
		parent.sendErrorMessage("Quantity must not be lower than one, other wise, you may delete the entire line should you wish this item to be not included in this Receiving Report..."); 
	} else {
	   document.getElementById(txtobj).value = newqty;
	   $.post("dr.datacontrol.php", { mod: "usabQty", lid: lineid, val: newqty, price: price, dr_no: dr_no, sid: Math.random() }, function(data) {
			$("#amtGT").html(data['amt2']);
			document.getElementById(objamt1).innerHTML = data['amt1'];
	   },"json");
	}
}

function updateQty(val,dr_no,lineid,price) {
	var txtobj = 'qty['+lineid+']';
	var objamt1 = 'amt['+lineid+']';

	$.post("dr.datacontrol.php", { mod: "usabQty", lid: lineid, val: val, price: price, dr_no: dr_no, sid: Math.random() }, function(data) {
		$("#amtGT").html(data['amt2']);
		document.getElementById(objamt1).innerHTML = data['amt1'];
	  },"json");
}

function printDR(dr_no,uid) {
	$.post("dr.datacontrol.php", { mod: "check4print", dr_no: dr_no, sid: Math.random() }, function(data) { 
		if(data == "noerror") {
			$.post("dr.datacontrol.php", { mod: "finalizeRR", dr_no: dr_no, sid: Math.random() }, function() {
				window.open("print/dr.print.php?dr_no="+dr_no+"&sid="+Math.random()+"&user="+uid+"","Receiving Report","location=1,status=1,scrollbars=1,width=640,height=720");
				parent.viewDR(dr_no);
			});
		} else {
			switch(data) {
				case "head": parent.sendErrorMessage("Unable to print document. Document is not yet saved..."); break;
				case "det": parent.sendErrorMessage("Unable to print document. It seems that you have not added any product yet to this Receiving Report..."); break;
				case "both": parent.sendErrorMessage("There is nothing to print. Please make it sure you have saved what entries you've made..."); break;
			}
		}
	},"html");
}

function reopenDR(dr_no) {
	if(confirm("Are you sure you want to set this document to active status?") == true) {
		$.post("dr.datacontrol.php", { mod: "reopenRR", dr_no: dr_no, sid: Math.random() }, function() {
			parent.viewDR(dr_no);
		});
	}
}

function cancelDR(dr_no) {
	if(confirm("Are you sure you want to Cancel this document?") == true) {
		$.post("dr.datacontrol.php", { mod: "cancel", dr_no: dr_no, sid: Math.random() }, function(){
			alert("Delivery Receipt Successfully Cancelled!");
			parent.showRRList();
		});
	}
}

function reuseRR(dr_no) {
	if(confirm("Are you sure you want to Recycle this document?") == true) {
		$.post("dr.datacontrol.php", { mod: "reopenRR", dr_no: dr_no, sid: Math.random() }, function(){
			parent.viewDR(dr_no);
		});
	}
}

function reprintDR(dr_no,uid) {
	window.open("print/dr.print.php?dr_no="+dr_no+"&sid="+Math.random()+"&user="+uid+"&reprint=Y","Receiving Report","location=1,status=1,scrollbars=1,width=640,height=720");
}