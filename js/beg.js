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

	function pickContact(cid,cname,addr,terms) {
		$("#cSelected").val('Y');
		$("#customer_id").val(cid);
		$("#customer_name").val(decodeURIComponent(cname));
		$("#cust_address").val(decodeURIComponent(addr));
	}

	function saveARBHeader() {
		$.post("beg.datacontrol.php", { mod: "saveARBHeader", doc_no: $("#doc_no").val(), doc_date: $("#doc_date").val(), remarks: $("#remarks").val() });
	}

	function addARBDetails() {
		var msg="";
		var amt = parent.stripComma($("#amount").val());

		if($("#customer_id").val() == "") { msg = msg + "Customer Code is required...<br/>"; }
		if($("#customer_name").val() == "") { msg = msg + "- Customer Name is required...<br/>"; }
		if($("#inv_no").val() == "") { msg = msg + "- Invoice No. should not be empty...<br/>"; }
		if($("#inv_date").val() == "") { msg = msg + "- Invoice Date should not be empty...<br/>"; }
		//if($("#po_no").val() == "") { msg = msg + "- PO No. should not be empty...<br/>"; }
		//if($("#po_date").val() == "") { msg = msg + "- PO Date should not be empty...<br/>"; }
		if(isNaN(amt) == true || $("#amount").val() == "") { msg = msg + "- Invalid Invoice Amount...<br/>"; }

		if(msg != "") {
			parent.sendErrorMessage(msg);
		} else {
			$.post("beg.datacontrol.php", { mod: "saveARDetails", doc_date: $("#doc_date").val(), cid: $("#customer_id").val(), cname: $("#customer_name").val(), inv_no: $("#inv_no").val(), inv_date: $("#inv_date").val(), po_no: $("#po_no").val(), po_date: $("#po_date").val(), amount: $("#amount").val() }, function (data) {
				$("#begdetails").html(data);
				$("#customer_id").val("");
				$("#customer_name").val("");
				$("#inv_no").val("");
				$("#inv_date").val("");
				$("#po_no").val("");
				$("#po_date").val("");
				$("#amount").val("");
			},"html");
		}
	}

	function deleteARLine(line_id) {
		if(confirm("Are you sure you want to delete this line entry?") == true) {
			$.post("beg.datacontrol.php", { mod: "deleteARLine", lid: line_id, sid: Math.random() }, function(data) {
				$("#begdetails").html(data);
			},"html");
		}
	}

	function finalizeARB() {
		if(confirm("Are you sure you want to Finalize & Post Beginning Balance to General Ledger?") == true) {
			$.post("beg.datacontrol.php", { mod: "finalizeARB", sid: Math.random()}, function(){
				location.reload();
			});
		}
	}

	function reOpenARB() {
		if(confirm("Are you sure you want to set this document to active status?") == true) {
			$.post("beg.datacontrol.php", { mod: "reOPENARB", sid: Math.random() }, function() {
				location.reload();
			});
		}
	}

	/* AP */
	function saveAPBHeader() {
		$.post("beg.datacontrol.php", { mod: "saveAPBHeader", doc_no: $("#doc_no").val(), doc_date: $("#doc_date").val(), remarks: $("#remarks").val() });
	}

	function addAPBDetails() {
		var msg="";
		var amt = parent.stripComma($("#amount").val());

		if($("#customer_id").val() == "") { msg = msg + "Customer Code is required...<br/>"; }
		if($("#customer_name").val() == "") { msg = msg + "- Customer Name is required...<br/>"; }
		if($("#inv_no").val() == "") { msg = msg + "- Invoice No. should not be empty...<br/>"; }
		if($("#inv_date").val() == "") { msg = msg + "- Invoice Date should not be empty...<br/>"; }
		//if($("#po_no").val() == "") { msg = msg + "- PO No. should not be empty...<br/>"; }
		//if($("#po_date").val() == "") { msg = msg + "- PO Date should not be empty...<br/>"; }
		if(isNaN(amt) == true || $("#amount").val() == "") { msg = msg + "- Invalid Invoice Amount...<br/>"; }

		if(msg != "") {
			parent.sendErrorMessage(msg);
		} else {
			$.post("beg.datacontrol.php", { mod: "saveAPDetails", doc_date: $("#doc_date").val(), cid: $("#customer_id").val(), cname: $("#customer_name").val(), inv_no: $("#inv_no").val(), inv_date: $("#inv_date").val(), po_no: $("#po_no").val(), po_date: $("#po_date").val(), amount: $("#amount").val() }, function (data) {
				$("#begdetails").html(data);
				$("#customer_id").val("");
				$("#customer_name").val("");
				$("#inv_no").val("");
				$("#inv_date").val("");
				$("#po_no").val("");
				$("#po_date").val("");
				$("#amount").val("");
			},"html");
		}
	}