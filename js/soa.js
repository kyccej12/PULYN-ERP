function saveHeader() {
    var msg = '';

    if($("#customer_code").val() == '') {
        msg = msg + "- Invalid Customer Code!<br/>";
    }

    if(msg != '') {
        parent.sendErrorMessage(msg);
    } else {
        $.post("soa.datacontrol.php", {
            mod: "saveHeader",
            trace_no: $("#trace_no").val(),
            soa_no: $("#soa_no").val(),
            soa_date: $("#soa_date").val(),
            cid: $("#customer_code").val(),
            cname: $("#customer_name").val(),
            caddr: $("#customer_address").val(),
            terms: $("#terms").val(),
            remarks: $("#remarks").val(),
            sid: Math.random()

        },function(so) { if($("#soa_no").val() == '') { $("#soa_no").val(so); } },"html");
    }

}

// function browseSO() {

//     var msg = '';
//     if($("#soa_no").val() == '') {
//         msg = msg + "- It appears that you have yet to save initially saved this document."; 
//     }

//     if($("#customer_code").val() == '') {
//         msg = msg + "- Unable to continue as it appear you have yet to specify a valid client info.<br/>";
//     }

//     if(msg != '') {
//         parent.sendErrorMessage(msg);
//     } else {
//         $.post("soa.datacontrol.php", {
//             mod: "browseSO",
//             cid: $("#customer_code").val(),
//             soa_no: $("#soa_no").val(),
//             sid: Math.random(),

//         },function(rset) { 
//             if(rset.length > 0) { 
//                 $("#solist").html(rset);
//                 var myso = $("#solist").dialog({
//                     title: "Unbilled Sales Order", 
//                     width: 720, 
//                     height: 480, 
//                     resizable: false,
//                     buttons: [
//                         {
//                             text: "Upload Selected Sales Orders",
//                             click: function() {
//                                 if($('#frmFetchedSO input:checked').length > 0) {
                                    
//                                     var mydata  = $("#frmFetchedSO").serialize();
//                                         mydata = mydata + "&mod=uploadSO&soa_no="+$("#soa_no").val()+"&trace_no="+$("#trace_no").val()+"&sid="+Math.random()+"";                            
//                                     $.ajax({
//                                         "type": "POST",
//                                         "url": "soa.datacontrol.php",
//                                         "data": mydata,
//                                         "success": function(res) { $("#grandTotal").val(res); redrawDataTable(); myso.dialog("close"); }
//                                     });
//                                 } else {
//                                     parent.sendErrorMessage("Unable to continue as it appears you have not selected any Sales Order from the list yet.");
//                                 }
//                             },
//                             icons: { primary: "ui-icon-check" }
//                         },
//                         {
//                             text: "Cancel",
//                             click: function() { $(this).dialog("close"); },
//                             icons: { primary: "ui-icon-closethick" }
//                         }
//                     ]                     
//                 });
//             } else { 
//                 parent.sendErrorMessage("Unable to find any unbilled Sales Order from this client."); 
//             }
//         },"html");
//     }
// }

function browseSO() {

    var msg = '';
    if($("#soa_no").val() == '') {
        msg = msg + "- It appears that you have yet to save initially saved this document."; 
    }

    if($("#customer_code").val() == '') {
        msg = msg + "- Unable to continue as it appear you have yet to specify a valid client info.<br/>";
    }

		var msg = '';
    if(msg != '') {
        parent.sendErrorMessage(msg);
    } else {
        $.post("soa.datacontrol.php", {
            mod: "browseSO",
            cid: $("#customer_code").val(),
            soa_no: $("#soa_no").val(),
            sid: Math.random(),

        },function(rset) { 
            if(rset.length > 0) { 
                $("#solist").html(rset);
                var myso = $("#solist").dialog({
                    title: "Unbilled Sales Order", 
                    width: 960, 
                    height: 480, 
                    resizable: false,
                    buttons: [
                        {
                            text: "Check All",
                           click: function() {
                                $('input[type=checkbox]').not(this).prop('checked', $(this).prop('checked', this.checked));
                            },
                            icons: { primary: "ui-icon-check" }
                        },
                        {
                            text: "Upload Selected Sales Orders",
                            click: function() {
                                if($('#frmFetchedSO input:checked').length > 0) {
                                    
                                    var mydata  = $("#frmFetchedSO").serialize();
                                        mydata = mydata + "&mod=uploadSO&soa_no="+$("#soa_no").val()+"&trace_no="+$("#trace_no").val()+"&sid="+Math.random()+"";                            
                                    $.ajax({
                                        "type": "POST",
                                        "url": "soa.datacontrol.php",
                                        "data": mydata,
                                        "success": function(res) { $("#grandTotal").val(res); redrawDataTable(); myso.dialog("close"); }
                                    });
                                } else {
                                    parent.sendErrorMessage("Unable to continue as it appears you have not selected any Sales Order from the list yet.");
                                }
                            },
                            icons: { primary: "ui-icon-extlink" }
                        },
                        {
                            text: "Cancel",
                            click: function() { $(this).dialog("close"); },
                            icons: { primary: "ui-icon-closethick" }
                        }
                    ]                     
                });
            } else { 
                parent.sendErrorMessage("Unable to find any unbilled Sales Order from this client."); 
            }
        },"html");
    }
}


function searchBrowseSO() {
	
	var searchValue = $("#so_search").val();

	if(searchValue != '') {
		var msg = '';
		if(msg != '') {
			parent.sendErrorMessage(msg);
		} else {
			$.post("soa.datacontrol.php", {
				mod: "browseSO",
				stxt: searchValue,
				cid: $("#customer_code").val(),
				soa_no: $("#soa_no").val(),
				sid: Math.random(),

			},function(rset) { 
				if(rset.length > 0) { 
					$("#solist").html(rset);
					var myso = $("#solist").dialog({
						title: "Unbilled Sales Order", 
						width: 960, 
						height: 480, 
						resizable: false,
						buttons: [
							{
								text: "Upload Selected Sales Order",
								click: function() {
									if($('#frmFetchedSO input:checked').length > 0) {
										
										var mydata  = $("#frmFetchedSO").serialize();
											mydata = mydata + "&mod=uploadSO&soa_no="+$("#soa_no").val()+"&trace_no="+$("#trace_no").val()+"&sid="+Math.random()+"";                            
                                            $.ajax({
                                                "type": "POST",
                                                "url": "soa.datacontrol.php",
                                                "data": mydata,
                                                "success": function(res) { $("#grandTotal").val(res); redrawDataTable(); myso.dialog("close"); }
                                            });
                                        } else {
                                            parent.sendErrorMessage("Unable to continue as it appears you have not selected any Sales Order from the list yet.");
                                        }
                                    },
								icons: { primary: "ui-icon-exlink" }
							},
							{
								text: "Close",
								click: function() { $(this).dialog("close"); },
								icons: { primary: "ui-icon-closethick" }
							}
						]
					});
				} else { 
					parent.sendErrorMessage("Unable to find any unbilled Sales Order from this client."); 
				}
			},"html");
		}
	}
}

function browseCSO() {

    var msg = '';
    if($("#soa_no").val() == '') {
        msg = msg + "- It appears that you have yet to save initially saved this document."; 
    }

    if($("#customer_code").val() == '') {
        msg = msg + "- Unable to continue as it appear you have yet to specify a valid client info.<br/>";
    }

    if(msg != '') {
        parent.sendErrorMessage(msg);
    } else {
        $.post("soa.datacontrol.php", {
            mod: "browseCSO",
            cid: $("#customer_code").val(),
            soa_no: $("#soa_no").val(),
            sid: Math.random(),

        },function(rset) { 
            if(rset.length > 0) { 
                $("#solist").html(rset);
                var myso = $("#solist").dialog({
                    title: "Unbilled Corporate Sales Order", 
                    width: 960, 
                    height: 540, 
                    resizable: false,
                    buttons: [
                        {
                            text: "Check All",
                           click: function() {
                                $('input[type=checkbox]').not(this).prop('checked', $(this).prop('checked', this.checked));
                            },
                            icons: { primary: "ui-icon-check" }
                        },
                        {
                            text: "Upload Selected Sales Orders",
                            click: function() {
                                if($('#frmFetchedSO input:checked').length > 0) {
                                    
                                    var mydata  = $("#frmFetchedSO").serialize();
                                        mydata = mydata + "&mod=uploadCSO&soa_no="+$("#soa_no").val()+"&trace_no="+$("#trace_no").val()+"&sid="+Math.random()+"";

                                    $("#mainLoading").css("z-index","999");
                                    $("#mainLoading").show(); 

                                    $.ajax({
                                        "type": "POST",
                                        "url": "soa.datacontrol.php",
                                        "data": mydata,
                                        "success": function(res) { $("#grandTotal").val(res); redrawDataTable(); myso.dialog("close"); /* location.reload(); */ }
                                    });
                                } else {
                                    parent.sendErrorMessage("Unable to continue as it appears you have not selected any Sales Order from the list yet.");
                                }
                            },
                            icons: { primary: "ui-icon-extlink" }
                        },
                        {
                            text: "Cancel",
                            click: function() { $(this).dialog("close"); },
                            icons: { primary: "ui-icon-closethick" }
                        }
                    ]                     
                });
            } else { 
                parent.sendErrorMessage("Unable to find any unbilled Sales Order from this client."); 
            }
        },"html");
    }
}

function searchBrowseCSO() {
	
	var searchValue = $("#so_search").val();

	if(searchValue != '') {
		var msg = '';
		if(msg != '') {
			parent.sendErrorMessage(msg);
		} else {
			$.post("soa.datacontrol.php", {
				mod: "browseCSO",
				stxt: searchValue,
				cid: $("#customer_code").val(),
				soa_no: $("#soa_no").val(),
				sid: Math.random(),

			},function(rset) { 
				if(rset.length > 0) { 
					$("#solist").html(rset);
					var myso = $("#solist").dialog({
                        title: "Unbilled Corporate Sales Order", 
                        width: 960, 
                        height: 540, 
						resizable: false,
						buttons: [
                            {
                                text: "Check All",
                               click: function() {
                                    $('input[type=checkbox]').not(this).prop('checked', $(this).prop('checked', this.checked));
                                },
                                icons: { primary: "ui-icon-check" }
                            },
							{
								text: "Upload Selected Sales Order",
								click: function() {
									if($('#frmFetchedSO input:checked').length > 0) {
										
										var mydata  = $("#frmFetchedSO").serialize();
											mydata = mydata + "&mod=uploadCSO&soa_no="+$("#soa_no").val()+"&trace_no="+$("#trace_no").val()+"&sid="+Math.random()+"";  
                                            
                                            $("#mainLoading").css("z-index","999");
                                            $("#mainLoading").show();
                                            
                                            $.ajax({
                                                "type": "POST",
                                                "url": "soa.datacontrol.php",
                                                "data": mydata,
                                                "success": function(res) { $("#grandTotal").val(res); redrawDataTable(); myso.dialog("close"); location.reload(); }
                                            });
                                        } else {
                                            parent.sendErrorMessage("Unable to continue as it appears you have not selected any Sales Order from the list yet.");
                                        }
                                    },
                                    icons: { primary: "ui-icon-extlink" }
                                },
							{
								text: "Cancel",
								click: function() { $(this).dialog("close"); },
								icons: { primary: "ui-icon-closethick" }
							}
						]
					});
				} else { 
					parent.sendErrorMessage("Unable to find any unbilled Sales Order from this client."); 
				}
			},"html");
		}
	}
}

function browsePharmaSO() {

    var msg = '';
    if($("#soa_no").val() == '') {
        msg = msg + "- It appears that you have yet to save initially saved this document."; 
    }

    if($("#customer_code").val() == '') {
        msg = msg + "- Unable to continue as it appear you have yet to specify a valid client info.<br/>";
    }

    if(msg != '') {
        parent.sendErrorMessage(msg);
    } else {
        $.post("soa.datacontrol.php", {
            mod: "browsePharmaSO",
            cid: $("#customer_code").val(),
            soa_no: $("#soa_no").val(),
            sid: Math.random(),

        },function(rset) { 
            if(rset.length > 0) { 
                $("#solist").html(rset);
                var myso = $("#solist").dialog({
                    title: "Unbilled Pharmacy Sales Order", 
                    width: 1234, 
                    height: 540, 
                    resizable: false,
                    buttons: [
                        {
                            text: "Check All",
                           click: function() {
                                $('input[type=checkbox]').not(this).prop('checked', $(this).prop('checked', this.checked));
                            },
                            icons: { primary: "ui-icon-check" }
                        },
                        {
                            text: "Upload Selected Sales Orders",
                            click: function() {
                                if($('#frmFetchedSO input:checked').length > 0) {
                                    
                                    var mydata  = $("#frmFetchedSO").serialize();
                                        mydata = mydata + "&mod=uploadPharmaSO&soa_no="+$("#soa_no").val()+"&trace_no="+$("#trace_no").val()+"&sid="+Math.random()+"";                            
                                    $.ajax({
                                        "type": "POST",
                                        "url": "soa.datacontrol.php",
                                        "data": mydata,
                                        "success": function(res) { $("#grandTotal").val(res); redrawDataTable(); myso.dialog("close"); /* location.reload(); */ }
                                    });
                                } else {
                                    parent.sendErrorMessage("Unable to continue as it appears you have not selected any Sales Order from the list yet.");
                                }
                            },
                            icons: { primary: "ui-icon-extlink" }
                        },
                        {
                            text: "Cancel",
                            click: function() { $(this).dialog("close"); },
                            icons: { primary: "ui-icon-closethick" }
                        }
                    ]                     
                });
            } else { 
                parent.sendErrorMessage("Unable to find any unbilled Sales Order from this client."); 
            }
        },"html");
    }
}

function searchBrowsePharmaSO() {
	
	var searchValue = $("#so_search").val();

	if(searchValue != '') {
		var msg = '';
		if(msg != '') {
			parent.sendErrorMessage(msg);
		} else {
			$.post("soa.datacontrol.php", {
				mod: "browsePharmaSO",
				stxt: searchValue,
				cid: $("#customer_code").val(),
				soa_no: $("#soa_no").val(),
				sid: Math.random(),

			},function(rset) { 
				if(rset.length > 0) { 
					$("#solist").html(rset);
					var myso = $("#solist").dialog({
                        title: "Unbilled Pharmacy Sales Order", 
                        width: 1234, 
                        height: 540,
						resizable: false,
						buttons: [
                            {
                                text: "Check All",
                               click: function() {
                                    $('input[type=checkbox]').not(this).prop('checked', $(this).prop('checked', this.checked));
                                },
                                icons: { primary: "ui-icon-check" }
                            },
							{
								text: "Upload Selected Sales Order",
								click: function() {
									if($('#frmFetchedSO input:checked').length > 0) {
										
										var mydata  = $("#frmFetchedSO").serialize();
											mydata = mydata + "&mod=uploadPharmaSO&soa_no="+$("#soa_no").val()+"&trace_no="+$("#trace_no").val()+"&sid="+Math.random()+"";                            
                                            $.ajax({
                                                "type": "POST",
                                                "url": "soa.datacontrol.php",
                                                "data": mydata,
                                                "success": function(res) { $("#grandTotal").val(res); redrawDataTable(); myso.dialog("close"); location.reload(); }
                                            });
                                        } else {
                                            parent.sendErrorMessage("Unable to continue as it appears you have not selected any Sales Order from the list yet.");
                                        }
                                    },
                                    icons: { primary: "ui-icon-extlink" }
                                },
							{
								text: "Cancel",
								click: function() { $(this).dialog("close"); },
								icons: { primary: "ui-icon-closethick" }
							}
						]
					});
				} else { 
					parent.sendErrorMessage("Unable to find any unbilled Sales Order from this client."); 
				}
			},"html");
		}
	}
}


function deleteItem() {
    var table = $("#details").DataTable();
    var lineid;
    $.each(table.rows('.selected').data(), function() {
	    lineid = this["lid"];
    });

    if(!lineid) {
		parent.sendErrorMessage("Please select a record to remove.");
	} else {
        if(confirm("Are you sure you want to remove this line?") == true) {
            $.post("soa.datacontrol.php", {
                mod: "deleteItem",
                soa_no: $("#soa_no").val(),
                trace_no: $("#trace_no").val(),
                lid: lineid,
                sid: Math.random() },
                function(res) {
                 $("#grandTotal").val(res);
                 redrawDataTable();
                 location.reload();
                }
            );
        }
    }
}

function deleteAllitem() {
    var table = $("#details").DataTable();
    var soa_no;
    $.each(table.rows('.selected').data(), function() {
	    soa_no = this["soa_no"];
    });
    if(confirm("Are you sure you want to remove all entries?") == true) {
        $.post("soa.datacontrol.php", {
            mod: "deleteAllitem",
            soa_no: $("#soa_no").val(),
            trace_no: $("#trace_no").val(),
            sid: Math.random() },
            function(res) {
                $("#grandTotal").val(res);
                redrawDataTable();
                location.reload();
            }
        );
    }
}

function finalize() {
    if(confirm("Are you sure you want to finalize this Statement of Account?") == true) { 
	
		$.post("soa.datacontrol.php", { mod: "check4print", soa_no: $("#soa_no").val(), sid: Math.random() }, function(data) {
			if(data == "noerror") {
				$("#uppermenus").html('');
				$.post("soa.datacontrol.php", { mod: "finalize", soa_no: $("#soa_no").val(), sid: Math.random() }, function() {
					location.reload();
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
	$.post("soa.datacontrol.php", { mod: "checkPayment", soa_no: $("#soa_no").val(), sid: Math.random() }, function(paid) {
        var amt = parseFloat(paid[0]);
        
        if(amt > 0) {
			parent.sendErrorMessage("- It appears that this Statement of Account has been partially of fully paid...");
		} else {
			if(confirm("Are you sure you want to set this document to active status?") == true) {
				$.post("soa.datacontrol.php", { mod: "reopen", soa_no: $("#soa_no").val(), sid: Math.random() }, function() {
					location.reload(); 
				});
			}
		}
	},"html");
}

function print() {
    parent.printSOA($("#soa_no").val());
}

function printPharma() {
    parent.printPharmaSOA($("#soa_no").val());
}

function exportSOA() {
    parent.exportSOA($("#soa_no").val());
}