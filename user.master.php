<?php
	session_start();
	//include("handlers/initDB.php");	
	//$con = new myDB;


	if(isset($_REQUEST['searchtext']) && $_REQUEST['searchtext']!=''){
		$srch = " and username like '%$_REQUEST[searchtext]%' ";
	}
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

	var UID = "";
	
	
	function init() {
		var table = $("#itemlist").DataTable();
		var arr = [];
	   $.each(table.rows('.selected').data(), function() {
		   arr.push(this["uid"]);
	   });
	   UID = arr[0];
	}
	
	function getUser() {
		init();
		if(UID == "" || UID == "undefined") {
			parent.sendErrorMessage("Unable to retrieve record. Please select a record from the list, and once highlighted, press  \"<b><i>View User Information</i></b>\" button again...");
		} else {
			parent.showUserDetails(UID);
		}
	}
	
	function viewUserInfo() {
		init();
		
		if(UID == "" || UID == "undefined") {
			parent.sendErrorMessage("Please select a record from the list, and once highlighted, press  \"<b><i>View User Information</i></b>\" button again...");
		} else {
			$.post("src/sjerp.php", { mod: "getUserDetails", uid: UID, sid: Math.random() }, function(data) {
				$("#uid").val(data['emp_id']);
				$("#fname").val(data['xfullname']);
				$("#uname").val(data['username']);
				$("#utype").val(data['user_type']);
				$("#rtype").val(data['r_type']);
				$("#uemail").val(data['email']);
				$("#urole").val(data['role']);
				$("#license_no").val(data['license_no']);
				$("#signatureImage").html(data['signaturefile']);
				
				var dis = $("#userdetails").dialog({
					title: "User Profile",
					width: 480,
					resizeable: false,
					modal: true,
					buttons: [
						{
							text: "Save Changes Made",
							icons: { primary: "ui-icon-check" },
							click: function() {
								var msg = '';

								if(confirm("Are you sure you want permanently save changes made to this file?") == true) {
									var frmString = $("#frmUserDetails").serialize();
								
									$.ajax({
										type: "POST",
										url: "src/sjerp.php",
										data: new FormData($('#frmUserDetails')[0]),
										cache: false,
										contentType: false,
										processData: false,
										success: function() {
											alert("Record Successfully Saved!");
											dis.dialog("close");
											$("#frmUserDetails").trigger("reset");
										}
									});
								}
							}
						},
						{
							text: "Close",
							icons: { primary: "ui-icon-closethick" },
							click: function() { $(this).dialog("close"); }
						}
					]
				});	

			},"json");
		}
	}
	
	function newUser() {
		$("#frmNewUserDetails").trigger("reset");
		var dis = $("#newuserdetails").dialog({
			title: "New User Profile",
			width: 480,
			resizeable: false,
			modal: true,
			buttons: [
				{
					text: "Add User",
					icons: { primary: "ui-icon-check" },
					click: function() {
						var msg = "";
			
						if($("#new_fname").val() == "") { msg = msg + "You did not specify user's full name.<br/>"; }
						if($("#new_uname").val() == "") { msg = msg + "You did not specify username for this user.<br/>"; }
						if($("#new_pass1").val() == "" || $("#new_pass2").val() == "") { msg = msg + "The system cannot accept empty password.<br/>"; }
						if($("#new_pass1").val() != $("#new_pass2").val()) { msg = msg + "Passwords do not match.<br/>"; }
						
						if(msg!="") {
							parent.sendErrorMessage(msg);
						} else {
							$.post("src/sjerp.php", {mod: "checkUname", uname: $("#uname").val(), sid: Math.random() }, function(data) {
								if(data > 0) {
									parent.sendErrorMessage("Username has already been used by another user");
								} else {
									$.ajax({
										type: "POST",
										url: "src/sjerp.php",
										data: new FormData($('#frmNewUserDetails')[0]),
										cache: false,
										contentType: false,
										processData: false,
										success: function() {
											alert("Record Successfully Saved!");
											parent.showUsers();
										}
									});
								}
							},"html");
						}	
					}
						
				},
				{
					text: "Close",
					icons: { primary: "ui-icon-closethick" },
					click: function() { $(this).dialog("close"); $("#frmNewUserDetails").trigger("reset"); }
				}
			]
		});	
	}

	function resetPass() {
		init();
		
		if(UID == "") {
			parent.sendErrorMessage("Unable to continue. Please select a record from the list, and once highlighted, press  \"<b><i>Reset User Password</i></b>\" button again...");
		} else {
			if(confirm("Are you sure you want to reset this user's password?") == true) {
				$.post("src/sjerp.php", { mod: "resetPassword", uid: UID, sid: Math.random()}, function() {
					alert("User password was set to default (123456). The specified user will be required to change his/her password during his/her next login.")
				});
			}
		}
	}

	
	
	$(document).ready(function() {
	    $('#itemlist').dataTable({
			"scrollY":  "340px",
			"select":	'single',
			"pagingType": "full_numbers",
			"bProcessing": true,
			"sAjaxSource": "data/userlist.php",
			"aoColumns": [
			  { mData: 'uid' } ,
			  { mData: 'uname' },
			  { mData: 'fullname' },
			  { mData: 'utype' },
			  { mData: 'lastlogged' },
			  { mData: 'stat' }
			],
			"aoColumnDefs": [
				{ className: "dt-body-center", "targets": [0,3,4,5]}
            ]
		});
		
		$('#urole, #new_urole').autocomplete({
				source:'hrd/suggestPosition.php', 
				minLength:3,
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
<div id="mainDiv" name="mainDiv">
	<table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
		<tr>
			<td  style="padding:0px;" valign=top>
				<table width="100%"  cellspacing="0" cellpadding="0" style="padding-left: 5px; margin-bottom: 5px;">
					<tr>
						<td>
							<a href="#" class="topClickers" onClick="newUser();"><img src="images/icons/adduser.png" width=18 height=18 align=absmiddle />&nbsp;New User</a>&nbsp;&nbsp;
							<a href="#" class="topClickers" onClick="viewUserInfo();"><img src="images/icons/personalinfo.png" width=18 height=18 align=absmiddle />&nbsp;Edit Selected Record</a>&nbsp;&nbsp;
							<a href="#" class="topClickers" onClick="getUser();"><img src="images/icons/options.png" width=18 height=18 align=absmiddle />&nbsp;View User Rights</a>&nbsp;&nbsp;
							<a href="#" class="topClickers" onClick="resetPass();"><img src="images/icons/secrecy-icon.png" width=18 height=18 align=absmiddle />&nbsp;Reset User Password</a>
						</td>
					</tr>
				</table>
				<table id="itemlist" style="font-size:11px;">
					<thead>
						<tr>
							<th width=10%>USER ID</th>
							<th width=10%>USERNAME</th>
							<th>FULL NAME</th>
							<th width=15%>PRIVILEGE TYPE</th>
							<th width=15%>LAST ACTIVE</th>
							<th width=15%>STATUS</th>
						</tr>
					</thead>
				</table>
			</td>
		</tr>
	</table>
</div>
<div id="userdetails" name="userdetails" style="display: none;">
	<form enctype="multipart/form-data" name="frmUserDetails" id="frmUserDetails" method="POST">
		<input type="hidden" name="uid" id="uid">
		<input type="hidden" name="mod" id="mod" value="updateUser">
		<table border="0" cellpadding="0" cellspacing="2" width=100%>
			<tr>
				<td width=35%><span class="spandix-l">Full Name :</span></td>
				<td>
					<input type="text" name="fname" id="fname" class="nInput" style="width: 80%;" />
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">Username :</span></td>
				<td>
					<input type="text" name="uname" id="uname" class="nInput" style="width: 80%;" />
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">User Type :</span></td>
				<td>
					<select name="utype" id="utype" style="width: 80%;" class="nInput" />
						<option value="user">Limited User</option>
						<option value="admin">Super User</option>
					</select>
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">Reports Privileges :</span></td>
				<td>
					<select name="rtype" id="rtype" style="width: 80%;" class="nInput" />
						<option value="user">Limited User</option>
						<option value="admin">Super User</option>
					</select>
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">Email Address :</span></td>
				<td colspan=3>
					<input type="text" name="uemail" id="uemail" class="nInput" style="width: 80%;" />
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">Role or Position :</span></td>
				<td>
				<input type="text" name="urole" id="urole" class="nInput" style="width: 80%;" />
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">License No. :</span></td>
				<td>
				<input type="text" name="license_no" id="license_no" class="nInput" style="width: 80%;" />
				</td>
			</tr>
			<tr>
				<td width=35% class="spandix-l">Signature File: </td>
				<td><input type=file name="signatureFile" id="signatureFile" style="width: 80%;"></td>
			</tr>
			<tr>
				<td width=35% class="spandix-l"></td>
				<td><span id="signatureImage" name="signatureImage" style="height: 200px;">&nbsp;</span></td>
			</tr>
		</table>
	</form>
</div>
<div id="newuserdetails" name="newuserdetails" style="display: none;">
	<form enctype="multipart/form-data" name="frmNewUserDetails" id="frmNewUserDetails" method="POST">
		<input type="hidden" name="mod" id="mod" value="newUser">
		<table border="0" cellpadding="0" cellspacing="2" width=100%>
			<tr>
				<td width=35%><span class="spandix-l">Full Name :</span></td>
				<td>
					<input type="text" name="new_fname" id="new_fname" class="nInput" style="width: 80%;" />
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">Username :</span></td>
				<td>
					<input type="text" name="new_uname" id="new_uname" class="nInput" style="width: 80%;" />
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">Password :</span></td>
				<td>
					<input type="password" name="new_pass1" id="new_pass1" class="nInput" style="width: 80%;" />
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">Confirm Password :</span></td>
				<td>
					<input type="password" name="new_pass2" id="new_pass2" class="nInput" style="width: 80%;" />
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">User Type :</span></td>
				<td>
					<select name="new_utype" id="new_utype" style="width: 80%;" class="nInput" />
						<option value="user">Limited User</option>
						<option value="admin">Super User</option>
					</select>
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">Reports Privileges :</span></td>
				<td>
					<select name="new_rtype" id="new_rtype" style="width: 80%;" class="nInput" />
						<option value="user">Limited User</option>
						<option value="admin">Super User</option>
					</select>
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">Email Address :</span></td>
				<td colspan=3>
					<input type="text" name="new_uemail" id="new_uemail" class="nInput" style="width: 80%;" />
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">Role or Position :</span></td>
				<td>
				<input type="text" name="new_urole" id="new_urole" class="nInput" style="width: 80%;" />
				</td>
			</tr>
			<tr>
				<td width=35%><span class="spandix-l">License No. :</span></td>
				<td>
				<input type="text" name="new_license_no" id="new_license_no" class="nInput" style="width: 80%;" />
				</td>
			</tr>
			<tr>
				<td width=35% class="spandix-l">Signature File: </td>
				<td><input type=file name="new_signatureFile" id="new_signatureFile" style="width: 80%;"></td>
			</tr>
		</table>
	</form>
</div>
</body>
</html>