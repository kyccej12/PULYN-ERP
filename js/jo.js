function saveJOHeader() {
	var msg = "";
	if($("#cSelected").val() == "N") { msg = msg + "- Invalid supplier or source for the Purchase Order<br/>"; }
	if($("#requested_by").val() == "") { msg = msg + "- You have not indicated who requested for the items on this P.O"; }
	
	if(msg != "") {
		parent.sendErrorMessage(msg);
	} else{
		$.post("jo.datacontrol.php", { mod: "saveDocument", doc_no: $("#doc_no").val(), doc_date: $("#doc_date").val(), terms: $("#terms").val(), proj: $("#proj").val(), cid: $("#customer_id").val(), cname: $("#customer_name").val(), addr: $("#cust_address").val(), request_by: $("#request_by").val(), request_no: $("#request_no").val(), request_date: $("#request_date").val(), date_needed: $("#date_needed").val(), scope: $("#scope").val(), amount: $("#amount").val(), sid: Math.random() }, function(data){
			parent.popSaver();
		});
	}
}

function finalizeJO(doc_no) {
	if(confirm("Are you sure you want to finalize this document?") == true) {
		$.post("jo.datacontrol.php" , { mod: "finalize", doc_no: doc_no, sid: Math.random() }, function() {
			parent.viewJO(doc_no);
		});
	}
}

function cancelJO(doc_no) {
	if(confirm("Are you sure you want to cancel this document?") == true) {
		$.post("jo.datacontrol.php" , { mod: "cancel", doc_no: doc_no, sid: Math.random() }, function() {
			parent.viewJO(doc_no);
		});
	}
}

function reopenJO(doc_no) {
	if(confirm("Are you sure you want to set this Job Order to Active Status?") == true) {
		$.post("jo.datacontrol.php" , { mod: "active", doc_no: doc_no, sid: Math.random() }, function() {
			parent.viewJO(doc_no);
		});
	}
}
