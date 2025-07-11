
<div id="applyDivs" style="padding: 10px; display:none;">
	<form name="applydocs" id="applydocs">
		<table align=center border=0>
			<tr><td height=4 colspan=2></td></tr>
			<tr><td class=spandix-l align=right width=45% style="padding-right: 10px;" valign=top>Account Code :</td>
				<td align=left>
					<input type=text id="applied_acct" name="applied_acct" class="inputSearch2" style="width:100px;  font-weight: bold;" onkeyup="div_acctAuto(this.id, this.value);">
					<br/>
					<span id="applied_acct_title" class="spandix-l" style="font-style: italic;"></span>
				</td>
			</tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Document Type :</td>
				<td  align=left>
				<select name="app_doctype" id="app_doctype" class="gridInput" style="width:121px;" onchange="searchAppDoc(this.value);">
					<option value=""> - Select Type - </option>
					<option value="WSI">Service Invoice</option>
					<option value="OR">Official Receipt</option> 
					<option value="APV">Accounts Payable</option> 
					<option value="CV">Cash/Check Voucher</option> 
					<option value="DC">DA/CA</option> 
					<option value="JV">Journal Voucher</option>	
				</select>
				</td>	
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Doc. No. :</td>
				<td align=left>
					<input type=text id="app_docno" name="app_docno" class="gridInput" style="width:110px">
					<input type=hidden id="app_docdate" name="app_docdate">
					<input type=hidden id="app_client" name="app_client">
				</td>
			</tr>
			<tr><td height=2></td></tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Balance :</td>
				<td align=left>
					<input type=text id="app_balance" name="app_balance" class="gridInput" style="width:110px" >
					<input type=hidden id="realbalance" name="realbalance">
				</td>
			</tr>
			<tr><td class=spandix-l align=right style="padding-right: 10px;">Applied Amount :</td>
				<td align=left>
					<input type=text id="app_amount" name="app_amount" class="gridInput" style="width:60px" onchange="computeBalance(this.value);">
					<select name="app_side" id="app_side" class="gridInput" style="width: 47px;">
						<option value="DB" title="Debit">DB</option>
						<option value="CR" title="Credit">CR</option>
					</select>
				</td>
			</tr>
			<tr><td height=2></td></tr>
		</table>
		<table align=center cellspacing=0 cellpadding=0 width=100% style="font-weight:bold; border-bottom: 1px solid black; border-top: 1px solid black;">
			<tr>
				<td align="left" class="gridHead" width="25%">Doc No.</td>
				<td align="center" class="gridHead" width="20%">Doc Date</td>
				<td align="right" class="gridHead" width="30%">Amount</td>
				<td align="right" class="gridHead">Balance</td>
				<td width=18 class="gridHead" style="width: 15px;">&nbsp;</td>
			</tr>
		</table>
		<div name="balances" id="balances" style="height: 152px; overflow: auto;"></div>
		<table align=center>
			<tr><td height=8></td></tr>
			<tr><td></td>
				<td>
					<button type="button" onClick='javascript: applyNow();' style="height: 30px;"><img src="images/icons/down3.png" with=16 height=16 border=0 align=absmiddle />&nbsp;Apply Document</button>
					<button type="button" onClick='javascript: $("#applyDivs").fadeOut(200); $("#applied_acct_title").html(""); $("#balances").html("");  $(document.applydocs)[0].reset();' style="height: 30px;"><img src="images/icons/cancelled.png" with=16 height=16 border=0 align=absmiddle />&nbsp;Close Window</button>
				</td>
			</tr>
		</table>
	</form>
</div>