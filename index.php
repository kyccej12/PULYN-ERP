<?php 
	
	session_start();
	require_once "handlers/_generics.php";
	
	$o = new _init;
	if(isset($_SESSION['authkey'])) { $exception = $o->validateKey(); if($o->exception != 0) {	$URL = $HTTP_REFERER . "login/index.php?exception=" . $o->exception; } } else { $URL = $HTTP_REFERER . "login"; }
	if($URL) { header("Location: $URL"); };

	list($role) = $o->getArray("SELECT `role` from user_info where emp_id = '$_SESSION[userid]';");

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Pulyn Dialysis & Diagnostics Medical Center</title>
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<link href="ui-assets/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="ui-assets/texteditor/jquery-te-1.4.0.css" rel="stylesheet" type="text/css" />
	<link href="style/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="ui-assets/jquery/jquery-1.12.3.js"></script>
	<script language="javascript" src="ui-assets/themes/smoothness/jquery-ui.js"></script>
	<script language="javascript" src="ui-assets/texteditor/jquery-te-1.4.0.min.js"></script>
	<script language="javascript" src="js/main.js?sid=<?php echo uniqid(); ?>"></script>
	<script language="javascript" src="js/jquery.dialogextend.js"></script>
	<script language="javascript" src="js/jquery.center.js"></script>
	<script language="javascript" src="js/dropMenu.js"></script>
	<script>
		$(function() { $("#popSaver").centerIt({vertical: false}); });
		$('html').click(function(){ $("#suggestions").fadeOut(200); });


		/* Query for new file from Machines */
		myInterval = setInterval(checkHL7Messages, 30000);


		<?php if($role == 'MEDICAL TECHNOLOGIST' || $role == 'SYSAD') { ?>
			myInterva2 = setInterval(pingLab, 70000);
		<?php } ?>

		function checkHL7Messages() {
			$.post("inbound/checkinbound.php");
		}

		function pingLab() {
			$.post("src/sjerp.php", { mod: "checkForExtraction", sid: Math.random()}, function(data) {
				var xcount = parseInt(data);
				if(xcount > 0) {
					var audio = new Audio('audio/dingdong.mp3');
						audio.play();
				}
			},"html");
		}


	</script>
</head>
<body bgcolor="#ffffff" leftmargin="0" bottommargin="0" rightmargin="0" topmargin="0" <?php if($o->cpass == 'Y') { echo "onLoad=\"showChangePass();\""; } ?> style="background: url(images/wallpaper-6.jpg); background-size: 100% 100%; background-repeat: no-repeat;">
 <table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td colspan=2 style="height:37px; background-color:#595959; background-image: url(images/4.png);"><?php $o->createMenu($_SESSION['userid']); ?></td>
		<td align=right style="height:37px; padding-right: 5px; background-color:#595959; background-image: url(images/4.png);"><img src="images/icons/user.png" align=absmiddle border=0 width=18 height=18 /><span style="font-size: 11px; font-weight: bold; color: #ffffff;">&nbsp;<?php $o->getUname($_SESSION['userid']); ?>&nbsp;&nbsp;&nbsp;|</span>&nbsp;<a href="logout.php" style="font-size: 12px; font-weight: bold; color: #ffffff; text-decoration: none;" title="Click to Logout"><img src="images/button-logout.png" align=absmiddle border=0 width=24 height=24 />Logout</a></td>
	</tr>
	<tr height=90%>
		<td colspan=3>
			<table width="100%" height="100%" align="center" valign=middle>
				<tr>
					<td align=center>
						<img src="images/logo-small.png">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=3>
			<table width="100%" height="100%" cellpadding=0 cellspacing=0 align="center" valign=middle>
				<tr bgcolor="#363535">
					<td align=center style="font-family: arial, helvetica, sans-serif; color: #fefefe; font-size: 11px; height: 15px; font-weight: bold;">&copy; Exclusively Developed for Pulyn Dialysis & Diagnostics Medical Center by Port80 Business Solutions</td>
				</tr>
			</table>
		</td>
	</tr>
 </table>
<div id="search1" style="display: none;">
	<table width=100% border=0 cellspacing=2 cellpadding=0>
		<tr>
			<td valign=top width="95%" class="td_content" style="padding: 10px;">		
				<table border="0" cellpadding="0" cellspacing="0" width=100%>
					<tr>
						<td width=35%><span class="spandix-l" valign=top>Search String :</span></td>
						<td>
							<input type="hidden" id="searchmod">
							<input type="text" id="searchtxt" class="nInput" style="width: 100%;" value="" />
						</td>
					</tr>
					<tr><td height=4></td></tr>
					<tr><td colspan=2><hr></hr></td></tr>
					<tr>
						<td align=center colspan=2>
							<button type="button" onClick="searchRecord();" onkeypress="if(event.keyCode == 13) { searchRecord(); }" class="buttonding" style="width: 180px; font-size: 11px;"><img src="images/icons/search.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Search Record Now</button>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<div id="search2" style="display: none;">
	<table width=100% border=0 cellspacing=2 cellpadding=0>
		<tr>
			<td valign=top width="95%" class="td_content" style="padding: 10px;">		
				<table border="0" cellpadding="0" cellspacing="0" width=100%>
					<tr>
						<td width=35%><span class="spandix-l" valign=top>Search String :</span></td>
						<td>
							<input type="hidden" id="searchmod2">
							<input type="text" id="searchtxt2" class="nInput" style="width: 100%; font-size: 12px;" value="" /><br/>
							<input type="checkbox" id = "includeDetails" >&nbsp;<span class="spandix-l" valign=top><i>Search Including Item Details</i></span>
						</td>
					</tr>
					<tr><td height=4></td></tr>
					<tr><td colspan=2><hr></hr></td></tr>
					<tr>
						<td align=center colspan=2>
							<button type="button" onClick="searchRecord2();" onkeypress="if(event.keyCode == 13) { searchRecord2(); }" class="buttonding" style="width: 180px; font-size: 11px;"><img src="images/icons/search.png" width=24 height=24 align=absmiddle />&nbsp;&nbsp;Search Record Now</button>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<?php 
	include("includes/acctgDialogs.php");
	include("includes/acctgReports.php");
	include("includes/ibookDialogs.php");
	include("includes/labDialogs.php");
	include("includes/hrdivs.php");
 ?>

<div id="userrights" style="display: none;"></div>
<div id="userdetails" style="display: none;"></div>
<div id="userlist" style="display: none;"></div>
<div id="customerlist" style="display: none;"></div>
<div id="customerdetails" style="display: none;"></div>
<div id="itemlist" style="display: none;"></div>
<div id="itemdetails" style="display: none;"></div>
<div id="accountlist" style="display: none;"></div>
<div id="accountdetails" style="display: none;"></div>
<div id="banklist" style="display: none;"></div>
<div id="bankdetails" style="display: none;"></div>
<div id="projlist" style="display: none;"></div>
<div id="projdetails" style="display: none;"></div>
<div id="polist" style="display: none;"></div>
<div id="podetails" style="display: none;"></div>
<div id="jolist" style="display: none;"></div>
<div id="jodetails" style="display: none;"></div>
<div id="poprint" style="display: none;"></div>
<div id="rrlist" style="display: none;"></div>
<div id="rrdetails" style="display: none;"></div>
<div id="rrprint" style="display: none;"></div>
<div id="solist" style="display: none;"></div>
<div id="sodetails" style="display: none;"></div>
<div id="soprint" style="display: none;"></div>
<div id="silist" style="display: none;"></div>
<div id="sidetails" style="display: none;"></div>
<div id="siprint" style="display: none;"></div>
<div id="crlist" style="display: none;"></div>
<div id="crdetails" style="display: none;"></div>
<div id="crprint" style="display: none;"></div>
<div id="srrlist" style="display: none;"></div>
<div id="srrdetails" style="display: none;"></div>
<div id="srrprint" style="display: none;"></div>
<div id="swlist" style="display: none;"></div>
<div id="swdetails" style="display: none;"></div>
<div id="strlist" style="display: none;"></div>
<div id="strdetails" style="display: none;"></div>
<div id="strprint" style="display: none;"></div>
<div id="phylist" style="display: none;"></div>
<div id="phydetails" style="display: none;"></div>
<div id="phyprint" style="display: none;"></div>
<div id="jvlist" style="display: none;"></div>
<div id="jvdetails" style="display: none;"></div>
<div id="jvvprint" style="display: none;"></div>
<div id="adjlist" style="display: none;"></div>
<div id="adjdetails" style="display: none;"></div>
<div id="apvlist" style="display: none;"></div>
<div id="apvdetails" style="display: none;"></div>
<div id="apvprint" style="display: none;"></div>
<div id="rfplist" style="display: none;"></div>
<div id="rfpdetails" style="display: none;"></div>
<div id="rfpprint" style="display: none;"></div>
<div id="grfplist" style="display: none;"></div>
<div id="grfpdetails" style="display: none;"></div>
<div id="grfpprint" style="display: none;"></div>
<div id="cvlist" style="display: none;"></div>
<div id="cvdetails" style="display: none;"></div>
<div id="cvprint" style="display: none;"></div>
<div id="changepass" style="display: none;"></div>
<div id="boq" style="display: none;"></div>
<div id="falist" style="display: none;"></div>
<div id="fadetails" style="display: none;"></div>
<div id="ibook" style="display: none;"></div>
<div id="stockcard" style="display: none;"></div>
<div id="emplist" style="display: none;"></div>
<div id="empfam" style="display: none;"></div>
<div id="empedu" style="display: none;"></div>
<div id="empexp" style="display: none;"></div>
<div id="empexpinternal" style="display: none;"></div>
<div id="empcert" style="display: none;"></div>
<div id="emploanlist" style="display: none;"></div>
<div id="empdetails" style="display: none;"></div>
<div id="manageempdtr" style="display: none;"></div>
<div id="manageovertime" style="display: none;"></div>
<div id="pos" style="display: none;"></div>
<div id="shiftmngr" style="display: none;"></div>
<div id="userlocation" style="display: none;"></div>
<div id="dashboard" style="display: none;"></div>
<div id="graveyardlist" style="display: none;"></div>
<div id="gravedetails" style="display: none;"></div>
<div id="holidaydetails" style="display: none;"></div>
<div id="schedulePlotter" style="display: none;"></div>
<div id="payperiods" style="display: none;"></div>
<div id="ratetable" style="display: none;"></div>
<div id="acctgdash" style="display: none;"></div>
<div id="holidayDetails" style="display: none;"></div>
<div id="payrollAdjustment" style="display: none;"></div>
<div id="manageHolidays" style="display: none;"></div>
<div id="pemeresult" style="display: none;"></div>
<div id="signaturepad" style="display: none;"></div>
<div id="pharmarrlist" style="display: none;"></div>
<div id="pharmarrdetails" style="display: none;"></div>
<div id="pharmarrprint" style="display: none;"></div>
<div id="pharmaswlist" style="display: none;"></div>
<div id="pharmaswdetails" style="display: none;"></div>
<div id="pharmaswprint" style="display: none;"></div>
<div id="pharmapolist" style="display: none;"></div>
<div id="pharmapodetails" style="display: none;"></div>
<div id="pharmapoprint" style="display: none;"></div>
<div id="cameraFrame" style="display: none;"></div>


<?php for($rpt = 1; $rpt <= 10; $rpt++) { echo "<div id=\"report$rpt\" style=\"display: none;\"></div>"; } ?>
<div id="loaderMessage" title="Processing..." style="display: none;">
	<p><span style="float:left; margin:0 7px 20px 0;"><img src="images/loader.gif" /></span>Please wait while the system is processing your request...</p>
</div>
<div id="errorMessage" title="Error Message" style="display: none;">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><b>Unable to continue due to the following error(s):</b></p>
	<p style="margin-left: 20px; text-align: justify;" id="message"></span></p>
</div>
<div id="loading_popout" style="display:none;" align=center>
	<progress id='progess_trick' value='40' max ='100' width='220px'></progress> <br>
	Please wait while the server is processing our request.
</div>
<div id="popSaver" name="popSaver" class = "popSaver">Record Has Been Successfully Saved...</div>
<div id="mainLoading" style="display:none; width:100%;height:100%;position:absolute;top:0;margin:auto;"> 
	<div style="background-color:white;width:10%;height:20%;;margin:auto;position:relative;top:100;">
		<img style="display:block;margin-left:auto;margin-right:auto;" src="images/ajax-loader.gif" width=128 height=128 align=absmiddle /> 
	</div>
	<div id="mainLoading2" style="background-color:white;width:100%;height:100%;position:absolute;top:0;margin:auto;opacity:0.5;"> </div>
</div>
<!--div style="position: absolute; right: 6px; bottom: 55px;"><img src = "images/logosmall.png"></div-->
<div class="suggestionsBox" id="suggestions" style="display: none;">
	<div class="suggestionList" id="autoSuggestionsList">&nbsp;</div>
</div>
</body>
</html>