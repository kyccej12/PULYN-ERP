<?php
	session_start();
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
	<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
	<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>
	<script>
		var sPO = "";
	
		function init() {
			var table = $("#itemlist").DataTable();
			var arr = [];
		   $.each(table.rows('.selected').data(), function() {
			   arr.push(this["cv"]);
		   });
		   sPO = arr[0];
		}
		
		function viewCV() {
			init();
			if(!sPO) {
				parent.sendErrorMessage("Please select a voucher from the given list, and click \"<b>View/Update Voucher</b>\" again....");
			} else {
				parent.viewCV(sPO);
			}
		}
		
		function printCheck() {
			init();
			if(!sPO) {
				parent.sendErrorMessage("Please select a voucher from the given list, and click \"<b>Print Check</b>\" again....");
			} else {
				$.post("cv.datacontrol.php", { mod: "printCheck", cv_no: sPO, sid: Math.random() }, function(data) {
					$("#check_no").val(data['check_no']);
					$("#check_date").val(data['check_date']);
					$("#payee").val(data['payee']);
					$("#amount").val(data['inw']);
					$("#amountf").val(data['amount']);
					$("#printCheck").dialog({
					title: "Print Check", 
					width: 600, 
					height: 245, 
					resizable: false, 
						buttons: {
							"Print Check":  function() { printCheckNow(); }
						}
					});	
				},"json");
			}
		}
		
		function printCheckNow() {
			txtHTML = "<iframe id='frmcust' frameborder=0 width='100%' height='100%' src=\"print/check.php?cv_no="+sPO+"&payee="+encodeURIComponent($("#payee").val())+"&cross="+$("#iscross").val()+"&amount="+$("#amountf").val()+"\"></iframe>";
			$("#mycheck").html(txtHTML);
			$("#mycheck").dialog({
				title: "Print Check", 
				width: 800, 
				height: 320, 
				resizable: false 
			});	
		}
			
		$(document).ready(function() {
			$('#itemlist').dataTable({
				"scrollY":  "350px",
				"select":	'single',
				"pageLength": 50,
				"pagingType": "full_numbers",
				"sAjaxSource": "data/cvlist.php",
				"aoColumns": [
				  { mData: 'cv' } ,
				  { mData: 'cd8' },
				  { mData: 'payee' },
				  { mData: 'check_no' },
				  { mData: 'ckdate' },
				  { mData: 'ca_refno' },
				  { mData: 'amount', render: $.fn.dataTable.render.number(',', '.', 2, '')},
				  { mData: 'remarks' },
				  { mData: 'status' },
				],
				"aoColumnDefs": [
					{ className: "dt-body-center", "targets": [0,1,3,4,5,8] },
					{ className: "dt-body-right", "targets": [6] }
				],
				"order": [[ 0, "desc" ]]
			});
		});
	</script>
	<style>
		.dataTables_wrapper {
			display: inline-block;
			font-size: 11px; padding: 3px;
			width: 99%; 
		}
		
		table.dataTable tr.odd { background-color: #f5f5f5;  }
		table.dataTable tr.even { background-color: white; }
		.dataTables_filter input { width: 250px; }
	</style>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" >
	<table width=100% cellpadding=0 cellspacing=0 style="padding-left: 5px; margin-bottom: 2px;">
		<tr>
			<td align=left>
				<a href="#" class="topClickers" onClick="parent.viewCV('');"><img src="images/icons/add-2.png" width=18 height=18 align=absmiddle />&nbsp;New Voucher</a>&nbsp;&nbsp;
				<a href="#" class="topClickers" onClick="viewCV();"><img src="images/icons/bill.png" width=16 height=16 align=absmiddle />&nbsp;&nbsp;View/Edit Voucher</a>&nbsp;&nbsp;
				<a href="#" class="topClickers" onClick="printCheck();"><img src="images/icons/check_icon.png" width=18 height=18 align=absmiddle />&nbsp;Print Check</a>&nbsp;&nbsp;
				<a href="#" class="topClickers" onClick="parent.showCV();"><img src="images/icons/refresh.png" width=18 height=18 align=absmiddle />&nbsp;Reload List</a>
			</td>
		</tr>
	</table>
	<table class="cell-border" id="itemlist" style="font-size:11px;">
		<thead>
			<tr>
				<th width=8%>CV #</th>
				<th width=8%>DATE</th>
				<th width=20%>PAYEE</th>
				<th width=8%>CHECK #</th>
				<th width=10%>CHECK DATE</th>
				<th width=10%>CA REF #</th>
				<th width=10%>AMOUNT</th>
				<th>DOCUMENT REMARKS</th>
				<th width=8%>STATUS</th>
			</tr>
		</thead>
	</table>
	<div id="printCheck" style="display: none;">
		<table border="0" cellpadding="0" cellspacing="0" width=100%>
			<input type="hidden" name="rid" id="rid">
			<tr><td class="spandix-l" width=25%>Check No. :</td>
				<td align=left>
					<input type="text" name="check_no" id="check_no" style="width: 138px; font-size: 11px;" class="nInput">
				</td>
			</tr>
			<tr><td height=4 colspan="2"></td></tr>
			<tr><td class="spandix-l" width=25%>Payee :</td>
				<td align=left>
					<input type="text" name="payee" id="payee" style="width: 90%; font-size: 11px;" class="nInput">
				</td>
			</tr>
			<tr><td height=4 colspan="2"></td></tr>
			<tr><td class="spandix-l" width=25%>Amount in Words :</td>
				<td align=left>
					<input type="text" name="amount" id="amount" style="width: 90%; font-size: 11px;" class="nInput">
				</td>
			</tr>
			<tr><td height=4 colspan="2"></td></tr>
			<tr><td class="spandix-l" width=25%>Amount in Figures :</td>
				<td align=left>
					<input type="text" name="amountf" id="amountf" style="width: 138px; font-size: 11px;" class="nInput">
				</td>
			</tr>
			<tr><td height=4 colspan="2"></td></tr>
			<tr>
				<td class="spandix-l" width="25%">Cross Check :</td>
				<td align="left">
					<select name="iscross" id="iscross" class="nInput" style="width: 100px; font-size: 11px;">
						<option value="N">- No -</option>
						<option value="Y">- Yes -</option>
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div id="mycheck" style="display: none;"></div>
</body>
</html>