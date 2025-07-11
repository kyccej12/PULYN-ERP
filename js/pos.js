	var lineNo = "";

	$(document).ready(function($){
			  $('#item_code').autocomplete({
				source:'suggestPOSPrice.php', 
				minLength:3
		    });
		});
	
	function selectLine(obj) {
		$(obj).closest("tr").siblings().removeClass("diffcolor");
		$(obj).toggleClass("diffcolor");
		tmp_obj = obj.id; tmp_obj = tmp_obj.split("_"); lineNo = tmp_obj[1];
	}

	function changeQty() {
		if(lineNo == "") {
			parent.sendErrorMessage("Please select line item that you wish to change its quantity...");
		} else {
			if(isNaN($("#giqty").val()) == true) {
				parent.sendErrorMessage("Invalid Quantity");
			} else {
				$.post("src/sjerp.php", { mod: "posChgQty", lid: lineNo, tmpfileid: $("#tmpfileid").val(), qty: $("#giqty").val(), tid: $("#trans_id").val(), tendered: $("#tendered").val(), sid: Math.random() }, function(data) {
					$("#posdetails").html(data);
					$.post("src/sjerp.php", { mod: "getAmountDue", tmpfileid: $("#tmpfileid").val(), sid: Math.random()}, function(data) {
						$("#amountdue").html(parent.kSeparator(data));
						$("#amtdue").val(data);
						$("#chgqty").fadeOut(200);
					},"html");
				},"html");
			}
		}
	}

	function itemLookup(inputString,el) {
		if(isNaN(inputString) == true) {
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
	}
	
	function contactLookup(inputString,el) {
		if(inputString.length == 0) {
			$('#suggestions').hide();
		} else {
			var op = $("#"+el+"").offset();
			$.post("contactlookup.php", {queryString: ""+inputString+"" }, function(data){
			if(data.length > 0) {
				$('#suggestions').css({top: op.top+20, left: op.left, width: '350px'});
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			} else { $("#suggestions").hide(); }
			});
		}
	}
	
	function insertDetailsByCode(barcode) {
		if(barcode != "") {
			$.post("src/sjerp.php", { mod: "insertItemByCode", barcode: barcode, tmpfileid: $("#tmpfileid").val(), tid: $("#trans_id").val(), sid: Math.random()}, function(data) {
				if(data=="Error") {
					parent.sendErrorMessage("Barcode Not Found!")
				} else {
					$("#posdetails").html(data);
					$.post("src/sjerp.php", { mod: "getAmountDue", tmpfileid: $("#tmpfileid").val(), sid: Math.random()}, function(data) {
						$("#amountdue").html(parent.kSeparator(data));
						$("#amtdue").val(data);
					},"html");
					$("#item_code").val('');
				}
			},"html");
		}
	}
	
	function pickItem(icode,description,price,unit) {
		$.post("src/sjerp.php", { mod: "insPOSDet", tmpfileid: $("#tmpfileid").val(), tid: $("#trans_id").val(), code: icode, desc: decodeURIComponent(description), price: price, unit: unit, sid: Math.random() }, function(data) {
			$("#posdetails").html(data);
			$("#item_code").val('');
			$.post("src/sjerp.php", { mod: "getAmountDue", tmpfileid: $("#tmpfileid").val(), sid: Math.random()}, function(data) {
				$("#amountdue").html(parent.kSeparator(data));
				$("#amtdue").val(data);
			},"html");
		},"html");
	}
	
	function deletePOSDetails(lid,tmpid) {
		if(confirm("Are you sure you want to remove this entry?") == true) {
			$.post("src/sjerp.php", { mod: "delPOSDet", lid: lid, tmpfileid: tmpid, tid: $("#trans_id").val(), sid: Math.random() }, function(data) {
				$("#posdetails").html(data);
				$.post("src/sjerp.php", { mod: "getAmountDue", tmpfileid: $("#tmpfileid").val(), sid: Math.random()}, function(data) {
					$("#amountdue").html(parent.kSeparator(data));
					$("#amtdue").val(data);
				},"html");
			},"html");
		}
	}
	
	function lookforbarcode(code) {
		var isSearch = $("#isSearch").val();
		if(isSearch != 1) {
			$("#suggestions").hide(); 
			$.post("src/sjerp.php", { mod: "look4barcode", barcode: code, tmpfileid: $("#tmpfileid").val(), tid: $("#trans_id").val(), sid: Math.random(), sid: Math.random() }, function(data) {
				if(data == "error") {
					parent.sendErrorMessage("Barcode not in trade merchandise database...");
					$("#item_code").val('');
					$("#item_code").focus();
				} else {
					$("#posdetails").html(data);
					$("#item_code").val('Search Product or Scan Barcode');
					$.post("src/sjerp.php", { mod: "getAmountDue", tmpfileid: $("#tmpfileid").val(), sid: Math.random()}, function(data) {
						$("#amountdue").html(parent.kSeparator(data));
						$("#amtdue").val(data);
					},"html");
				}
			})
		}
	}
	
	function showCust() {
		$("#cust_soa").fadeIn();
		$("#cust_soa").css({visibility: 'visible', display: 'block', width: '480px', height: '105px', top: '120px'});
		$("#cust_soa").centerIt({vertical: false});
		$("#xcname").focus();
	}
	
	function showTender() {
		$("#tenderDIv").dialog({title: "Tender Amount", width: 380, height: 200, resizable: false }).dialogExtend({
			"closable" : true,
			"maximizable" : false,
			"minimizable" : true
		});
		
		/* $("#priceupdate").fadeIn();
			$("#priceupdate").css({visibility: 'visible', display: 'block', width: '380px', height: '384px', top: '50px'});
			$("#priceupdate").centerIt({vertical: false});
			$("#gitender").focus(); 
		*/
	}

	function showChangeQty() {
		if(lineNo == "") {
			parent.sendErrorMessage("Please select line item that you wish to change its quantity...");
		} else {
			$.post("src/sjerp.php", { mod: "getLineQty", lid: lineNo, sid: Math.random() }, function(data) {
				$("#giqty").val(data);
				$("#chgqty").fadeIn();
				$("#chgqty").css({visibility: 'visible', display: 'block', width: '380px', height: '92px', top: '120px'});
				$("#chgqty").centerIt({vertical: false});
				$("#giqty").focus();
			},"html");
		}	
	}

	function showDiscount() {
		$("#discounter").fadeIn();
		$("#discounter").css({visibility: 'visible', display: 'block', width: '380px', height: '115px', top: '120px'});
		$("#discounter").centerIt({vertical: false});
		$("#gidiscount").focus();
	}
	

	function preFinalize() {
		$.post("src/sjerp.php", { mod: "posCheckQty", tid: $("#tmpfileid").val(), sid: Math.random() }, function(data) {
			if(data > 0) {
				if($("#terms").val() == 0) {
					showTender();
				} else {
					finalize();
				}
			} else {
				parent.sendErrorMessage("Error: Current transaction is still empty!")
			}
		},"html");
	}

	function finalize() {		
		var msg = "";
		
		if($("#terms").val() == 0) {
			if($("#tendered").val() == "0.00") { msg = msg + "- Invalid amount tendered by the customer<br/>"; 	}
		}
		
		if(msg != "") {
			parent.sendErrorMessage("Unable to continue due to the following error(s): <br/><br/>"+msg);
		} else {
			
			if($("#ws_stub_no").val() == "Encode WS # here") { var ws_no = ""; } else { var ws_no = $("#ws_stub_no").val(); }
			if($("#dr_no").val() == "Encode DR # here") { var dr_no = ""; } else { var dr_no = $("#dr_no").val(); }

			$.post("src/sjerp.php", { mod: "finalizePOS", tmpfileid: $("#tmpfileid").val(), tid: $("#trans_id").val(), trans_date: $("#trans_date").val(), shift: $("#shift").val(), cid: $("#cid").val(), cname: $("#cname").val(), addr: $("#addr").val(), terms: $("#terms").val(), ws_no: ws_no, dr_no: dr_no, tendered: $("#tendered").val(), pay_type: $("#pay_type").val(), bank: $("#bank").val(), ref_no: $("#ref_no").val(), ref_date: $("#ref_date").val(), approval: $("#approval").val(), sid: Math.random() }, function(data){
				if(data == "error") {
					parent.sendErrorMessage("Error: Current transaction is still empty!")
				} else {
					if($("#terms").val() == 0) {
						due = $("#amtdue").val(); tendered = $("#tendered").val()
						parent.drawerOpened(due,tendered);
					}
				}
			},"html");
		}
	}

	
	function applyDiscount(val) {
		if(isNaN(val) == true || val == "") {
			parent.sendErrorMessage("Invalid Discount Value");
		} else {
			var dtype =  $("input[name='dtype']:checked").val();
			$.post("src/sjerp.php", { mod: "posDiscount", disc: val, dtype: dtype, tid: $("#trans_id").val(), tmpfileid: $("#tmpfileid").val(), sid: Math.random() }, function(data) {
				$("#posdetails").html(data);
				$.post("src/sjerp.php", { mod: "getAmountDue", tmpfileid: $("#tmpfileid").val(), sid: Math.random()}, function(data) {
					$("#amountdue").html(parent.kSeparator(data));
					$("#amtdue").val(data);
					$("#discounter").fadeOut(200);
				},"html");
			},"html");
		}
	}
	

	function pickContact(fid,name,address,terms) {
		$("#cname").val(decodeURIComponent(name));
		$("#addr").val(decodeURIComponent(address));
		$("#terms").val(terms)
		$("#cid").val(fid);
		
		$("#custname").html(decodeURIComponent(name));
		$("#custadd").html(decodeURIComponent(address));
		$("#cust_soa").fadeOut(200);
	}
	
	function computeChange(val) {
		
		if(isNaN(parent.stripComma(val)) == true) { parent.sendErrorMessage("Error: Invalid Amount Input!") ; 
		} else {
			if(parseFloat(val) < parseFloat($("#amtdue").val())) {
				parent.sendErrorMessage("Error: Insufficient amount tendered!");
			} else {
				var change =  val - $("#amtdue").val();
				change = change.toFixed(4);
				$("#tendered").val(val);	
				$("#changedue").html(parent.kSeparator(change));
				$("#priceupdate").fadeOut();
				finalize();
			}
		}
	}

	function updateQty(qty,tmpfileid,lineid) {
		var objprice = 'price['+lineid+']';
		var objDisc = 'disc['+lineid+']';
		var objLineAmt = 'amt['+lineid+']';

		$.post("src/sjerp.php", { mod: "usabPosQty", lid: lineid, val: qty, tmpfileid: tmpfileid, sid: Math.random() }, function(data) {
			$("#amountdue").html(data['amt2']);
			$("#amtdue").val(data['amt0']);
			document.getElementById(objLineAmt).innerHTML = data['amt1'];
			document.getElementById(objDisc).innerHTML = data['disc'];	
		 },"json");

	}

	function usabQty(tmpfileid,lineid,type) {
		var objqty = 'qty['+lineid+']';
		var objprice = 'price['+lineid+']';
		var objDisc = 'disc['+lineid+']';
		var objLineAmt = 'amt['+lineid+']';

		var cur_qty = document.getElementById(objqty).value;
			    
		if(type == 'up') { newqty = parseFloat(cur_qty) + 1; } else { newqty = parseFloat(cur_qty) - 1; }
		    newqty = newqty.toFixed(4);
			if(newqty < 1) { 
				parent.sendErrorMessage("Quantity must not be lower than one, other wise, you may delete the entire line should you wish this item to be not included in this Receiving Report..."); 
			} else {
			   document.getElementById(objqty).value = newqty;
			   $.post("src/sjerp.php", { mod: "usabPosQty", lid: lineid, val: newqty, tmpfileid: tmpfileid, sid: Math.random() }, function(data) {
			   		$("#amountdue").html(data['amt2']);
			   		$("#amtdue").val(data['amt0']);
			   		document.getElementById(objLineAmt).innerHTML = data['amt1'];
			   		document.getElementById(objDisc).innerHTML = data['disc'];	
			   },"json");
		}
	}

	function qtyMove(a) {
		if(a == 1) { var x = 'up'; } else { var x = 'down'; }
		if(lineNo == "") {
			parent.sendErrorMessage("Please select line item that you wish to change its quantity...");
		} else {
			usabQty($("#tmpfileid").val(),lineNo,x);
		}
	}

	function updatePrice(val,tmpfileid,lineid,oldprice) {
		var objprice = 'price['+lineid+']';
		var objLineAmt = 'amt['+lineid+']';
		var objDisc = 'disc['+lineid+']';
		var objqty = 'qty['+lineid+']';

		var p = parseFloat(parent.stripComma(val));

		if(isNaN(p) == true) {
			parent.sendErrorMessage("Price specified is invalid!");
			document.getElementById(objprice).value = oldprice;
		} else {
			$.post("src/sjerp.php", { mod: "posUpdatePrice", tmpfileid: tmpfileid, lid: lineid, oprice: oldprice, price: p, qty: parent.stripComma(document.getElementById(objqty).value), sid: Math.random() }, function(data) {
				$("#amountdue").html(data['amt2']);
			   	$("#amtdue").val(data['amt0']);
			   	document.getElementById(objLineAmt).innerHTML = data['amt1'];	
			   	document.getElementById(objDisc).innerHTML = data['disc'];	
			},"json");
		}
	}

	function cancelTransaction() {
		if(confirm("Are you sure you want to cancel this transaction?") == true) {
			$.post("src/sjerp.php", { mod: "cancelPOS", trans_id: $("#trans_id").val(), sid: Math.random()}, function(){
				alert("Transaction Successfully Cancelled!");
				parent.close_div2();
				parent.showPOS();
			});
		}
	}
	
	$('html').click(function(){ $("#suggestions").fadeOut(200); $("#isSearch").val(''); });
	
	function init() {
		shortcut.add("F1", function() { showCust(); });
		shortcut.add("F2", function() { showDiscount(); });
		shortcut.add("F3", function() { showChangeQty(); });
		shortcut.add("F5", function() { showTender(); });
		shortcut.add("F8", function() { computeChange($('#gitender').val()); });
		shortcut.add("F12", function() { preFinalize(); });
		shortcut.add("pageup", function() { qtyMove(1); });
		shortcut.add("pagedown", function() { qtyMove(2); });
	} 
	
	$(function() { $("#priceupdate").centerIt({vertical: false});  $("#cust_soa").centerIt({vertical: false}); $("#ref_date").datepicker(); });
	
	window.onload = init;