<?php
	session_start();
	include("handlers/initDB.php");

	$con = new myDB;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Medgruppe Polyclinics & Diagnostic Center, Inc.</title>
<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="ui-assets/datatables/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="ui-assets/jquery/jquery-1.12.3.js"></script>
<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
<script language="javascript" src="js/jquery.dialogextend.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.jqueryui.js"></script>
<script type="text/javascript" charset="utf8" src="ui-assets/datatables/js/dataTables.select.js"></script>

<script>

	var UID = "";

	
	function init() {
		var table = $("#itemlist").DataTable();
		var arr = [];
	   $.each(table.rows('.selected').data(), function() {
		   arr.push(this["record_id"]);
	   });
	   UID = arr[0];
	}
	
	function getAccount() {
		init();
		if(!UID || UID == "undefined") {
			parent.sendErrorMessage("Unable to retrieve record. Please select a record from the list, and once highlighted, click  \"<b><i>View/Edit Selected Record</i></b>\"...");
		} else {
			
			$.post("src/sjerp.php", { mod: "getAcctDetails", recid: UID, sid: Math.random() }, function(data) {
				$("#recid").val(data['record_id']);
				$("#acct_code").val(data['acct_code']);
				$("#acct_description").val(data['description']);
				$("#acct_grp").val(data['acct_grp']);
				
				$("#acctDetails").dialog({title: "Account Details", width: 400, resizable: false, modal: true });
				
			},"json");
			
		}
	}
	
	function newAccount() {
		$("#acctDetailsNew").dialog({ title: "New Account", width: 400, resizable: false, modal: true });
	}
	
	function newRecord() {
		var msg = "";
		if($("#acct_code_new").val() == "") { msg = msg + "- Account Code is missing<br/>"; }
		if($("#acct_description_new").val() == "") { msg + "- Account Description is missing<br/>"; } 
	
		if(msg != "") {
			parent.sendErrorMessage(msg);
		} else {
			$.post("src/sjerp.php", { mod: "newAccount", acct_code: $("#acct_code_new").val(), acct_desc: $("#acct_description_new").val(), parent: $("#acct_parent_new").val(), acct_grp: $("#acct_grp_new").val(), sid: Math.random() },function(data) {
				if(data == "DUPLICATE") {
					parent.sendErrorMessage("Duplicate Account Code detected...")
				} else {
					alert("Account Successfully Added!");
					parent.closeDialog("#acctDetailsNew");
					parent.showAccounts(); 
				}
			});
		}
	
	}
	
	function saveRecord() {
		if(confirm("Are you sure you want to save changes made to this account?") == true) {
			$.post("src/sjerp.php", { mod: "updateAccount", recid: $("#recid").val(), acct_code: $("#acct_code").val(), acct_desc: $("#acct_description").val(), acct_grp: $("#acct_grp").val(), sid: Math.random() },function(data) {
				if(data == "DUPLICATE") {
					parent.sendErrorMessage("Duplicate Account Code detected...")
				} else {
					alert("Account Successfully Updated!");
					parent.closeDialog("#acctDetails");
					parent.showAccounts(); 
				}
			});
		}
	}
	
	function deleteAccount() {
		init();
		if(!UID || UID == "undefined") {
			parent.sendErrorMessage("Please select a record from the list, and once highlighted, click \"<b><i>Delete Record</i></b>\" again...");
		} else {
			if(confirm("Are you sure you want to delete this account?") == true) { 
				$.post("src/sjerp.php", {mod: "deleteAccount", rid: UID, sid: Math.random() }, function() {
					parent.showAccounts();
				});
			}
		}
	}
	
	function export2Excel() {
		window.open("export/chartofaccounts.php","Chart of Accounts","location=1,status=1,scrollbars=1,width=480,height=320");
	}
	
	$(document).ready(function() {
	    $('#itemlist').dataTable({
			"scrollY":  "300px",
			"select":	'single',
			"pagingType": "full_numbers",
			"bProcessing": true,
			"sAjaxSource": "data/chartlist.php",
			"aoColumns": [
			  { mData: 'record_id' },
			  { mData: 'acct_code' },
			  { mData: 'acct_desc' },
			  { mData: 'parent_acct' },
			  { mData: 'parent_title' },
			  { mData: 'acct_grp' },
			  { mData: 'grp_desc' }
			],
			"aoColumnDefs": [
				{ className: "dt-center", "targets": [1,3] },
				{ "targets": [0,3,5], "visible": false }
            ],
			"order": [[ 5, "asc" ]]
		});
	});
	
	function checkSeries(pid) {
		//if($("#acct_code_new").val() == "") {
			$.post("src/sjerp.php", { mod: "getAcctCodeSeries", parent: pid, sid: Math.random() }, function(ret) {
				$("#acct_code_new").val(ret);
			},"html");
		//}
	}
	
	function checkParents(group) {
		if(group != "") {
			$.post("src/sjerp.php", { mod: "getParentAccts", grp: group, sid: Math.random() }, function(htmldata) {
				$("#acct_parent_new").html(htmldata);
			},"html");
		} else {
			
		}
		
	}
	
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
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td  style="padding:0px;" valign=top>
		<table width="100%"  cellspacing="0" cellpadding="0" style="padding-left: 5px;margin-bottom:5px;">
				<tr><td height=8></td></tr>
				<tr>
					<td>
						<a href="#" class="topClickers" onClick="newAccount();"><img src="images/icons/add-2.png" width=18 height=18 align=absmiddle />&nbsp;New Account</a>&nbsp;&nbsp;
						<a href="#" class="topClickers" onClick="getAccount();"><img src="images/icons/dbcr.png" width=18 height=18 align=absmiddle />&nbsp;Edit Selected Details</a>&nbsp;&nbsp;
						<a href="#" class="topClickers" onClick="deleteAccount();"><img src="images/icons/delete.png" width=18 height=18 align=absmiddle />&nbsp;Delete Selected Record</a>&nbsp;&nbsp;
						<a href="#" class="topClickers" onClick="export2Excel();"><img src="images/icons/excel.png" width=18 height=18 align=absmiddle />&nbsp;Export List to Excel</a>&nbsp;&nbsp;
					</td>
				</tr>
			</table>
			<table id="itemlist" style="font-size:11px;">
				<thead>
					<tr>
						<th>RECORD ID</th>
						<th width=15%>ACCOUNT CODE</th>
						<th>ACCOUNT TITLE</th>
						<th>PARENT CODE</th>
						<th>PARENT ACCOUNT</th>
						<th>GRP ID</th>
						<th>ACCOUNT GROUP</th>
					</tr>
				</thead>
			</table>
		</td>
	</tr>
 </table>
 <div id="acctDetails" name="acctDetails" style="display: none;">
		<table border="0" cellpadding="0" cellspacing="0" width=100%>
			<tr>
				<td width=35%><span class="spandix-l">Account Code :</span></td>
				<td>
					<input type="text" id="acct_code" name="acct_code" class="nInput" style="width: 80%;" value="" />
					<input type="hidden" id = "recid" name="recid" value="">
				</td>
			</tr>
			<tr><td height=4></td></tr>
			<tr>
				<td width=35%><span class="spandix-l">Account Description :</span></td>
				<td>
					<input type="text" id="acct_description" name="acct_description" class="nInput" style="width: 80%;" value="" rows=1/>
				</td>
			</tr>
			<tr><td height=4></td></tr>
			<tr>
				<td width=35%><span class="spandix-l">Account Group:</span></td>
				<td>
					<select id="acct_grp" name="acct_grp" style="width: 80%; font-size: 11px;" class="nInput" />
					<?php
						$agq = $con->dbquery("select acct_grp,description from acctg_accountgrps");
						while($x = $agq->fetch_array()) {
							echo "<option value='$x[acct_grp]'>$x[description]</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr><td height=4></td></tr>
			<tr><td colspan=2><hr></hr></td></tr>
			<tr>
				<td align=center colspan=2>
					<button onClick="saveRecord();" class="buttonding" id="btn_rsv" style="width: 150px; font-size: 11px;"><img src="images/icons/floppy.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Save Changes</b></button>
				</td>
			</tr>
		</table>
 </div>
  <div id="acctDetailsNew" name="acctDetailsNew" style="display: none;">
		<table border="0" cellpadding="0" cellspacing="0" width=100%>
			<tr>
				<td width=35%><span class="spandix-l">Account Group:</span></td>
				<td>
					<select id="acct_grp_new" name="acct_grp_new" style="width: 80%; font-size: 11px;" class="nInput" onchange="checkParents(this.value);" />
					<option value=''>- Select Account Group -</option>
					<?php
						$agq = $con->dbquery("select acct_grp,description from acctg_accountgrps");
						while($x = $agq->fetch_array()) {
							echo "<option value='$x[acct_grp]'>$x[description]</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr><td height=4></td></tr>
			<tr>
				<td width=35%><span class="spandix-l">Parent Account :</span></td>
				<td>
					<select id="acct_parent_new" name="acct_parent_new" style="width: 80%; font-size: 11px;" class="nInput" onchange="checkSeries(this.value);" />
						<?php
							$paQuery = $con->dbquery("SELECT acct_code, description FROM acctg_accounts WHERE parent = 'Y' ORDER BY acct_code;");
							while($paRow = $paQuery->fetch_array()) {
								echo "<option value='$paRow[0]'>$paRow[1]</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr><td height=4></td></tr>
			<tr>
				<td width=35%><span class="spandix-l">Account Code :</span></td>
				<td>
					<input type="text" id="acct_code_new" name="acct_code_new" class="nInput" style="width: 80%;" value="" readonly />
				</td>
			</tr>
			<tr><td height=4></td></tr>
			<tr>
				<td width=35%><span class="spandix-l">Account Description :</span></td>
				<td>
					<input type="text" id="acct_description_new" name="acct_description_new" class="nInput" style="width: 80%;" value="" rows=1/>
				</td>
			</tr>
			<tr><td height=4></td></tr>
			<tr><td colspan=2><hr></hr></td></tr>
			<tr>
				<td align=center colspan=2>
					<button onClick="newRecord();" class="buttonding" id="btn_rsv" style="width: 150px; font-size: 11px;"><img src="images/icons/floppy.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Save Record</b></button>
				</td>
			</tr>
		</table>
 </div>
</body>
</html>