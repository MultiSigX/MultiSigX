<div class="white">
	<div class="col-md-12  container-fluid" >
		<div class="panel panel-primary">
			<div class="panel-heading">MultiSigX API documentation</div>
			<div class="panel-body">
				<h3>MultiSigX API</h3>
					<p>All MultiSigX API's  require authorization. The authorization KEY is available in Settings - Security API tab under API.</p>
					<p>The example of API key: <strong>FLDGNIMJVNRMB3MXPF2EVH2VKAYY7DUBA43YZF7RVQ4MMHAHABAUK7JJZSJG3PXE</strong></p>
				<ol>
					<li><a href="#CreateWallet">CreateWallet</a></li>
					<li><a href="#CreateTransaction">CreateTransaction</a></li>
					<li><a href="#SignTransaction">SignTransaction</a></li>
					<li><a href="#SendTransaction">SendTransaction</a></li>
				</ol>
		<?php 
		if(strlen($details['key'])>0){
		$key = $details['key'];
		}else{
		$key = "YOUR_API_KEY";
		}
		?>
			<h4>CreateWallet</h4>
				<p class="alert alert-danger">All parameters are to be submitted by POST only</p>
				<p>URL: https://MultiSigX.com/API/CreateWallet/<?=$key?>/parameters.......</p>
			<h5>Parameters</h5>
				<table class="table table-condensed table-bordered table-hover" style="width:50% ">
					<tr>
					<th>Parameter</th>
					<th>Required</th>
					<th>Description</th>
					<th>Default</th>
					</tr>
					<tr>
						<td>security</td>
						<td>yes</td>
						<td>type of security required 2 of 3 or 3 of 3</td>
						<td>2</td>
					</tr>
					<tr>
						<td>coin</td>
						<td>yes</td>
						<td>Coin name XGC or BTC</td>
						<td>XGC</td>
					</tr>				
					<tr>
						<td>coinName</td>
						<td>yes</td>
						<td>Name your own coin</td>
						<td>MultiSigXCoinName</td>
					</tr>					
					<tr>
						<td>changeAddress</td>
						<td>yes</td>
						<td>MultiSigX or Your own address</td>
						<td>MultiSigX</td>
					</tr>					
					<tr>
						<td>changeAddressValue</td>
						<td>No</td>
						<td>required only if you choose your own address</td>
						<td>-</td>
					</tr>	
					<tr>
						<td>email1</td>
						<td>yes</td>
						<td>your own email address</td>
						<td><?=$user['email']?></td>
					</tr>					
					<tr>
						<td>relation1</td>
						<td>yes</td>
						<td>
							<ul class="list-unstyled">
									<li>Self</li>
									<li>Partner</li>
									<li>Friend</li>
									<li>Family</li>
									<li>Employer</li>
									<li>Employee</li>
									<li>Business</li>
									<li>Escrow</li>
									<li>Print / Cold storage</li>
									<li>MultiSigX – self escrow</li>
							</ul>
						</td>
						<td>Self</td>
					</tr>					
					<tr>
						<td>email2</td>
						<td>yes</td>
						<td>2nd email address</td>
						<td>2nd<?=$user['email']?></td>
					</tr>					
					<tr>
						<td>relation2</td>
						<td>yes</td>
						<td>
							<ul class="list-unstyled">
									<li>Self</li>
									<li>Partner</li>
									<li>Friend</li>
									<li>Family</li>
									<li>Employer</li>
									<li>Employee</li>
									<li>Business</li>
									<li>Escrow</li>
									<li>Print / Cold storage</li>
									<li>MultiSigX – self escrow</li>
							</ul>
						</td>
						<td>Self</td>
					</tr>					
					<tr>
						<td>email3</td>
						<td>yes</td>
						<td>3rd email address</td>
						<td>3rd<?=$user['email']?></td>
					</tr>					
					<tr>
						<td>relation3</td>
						<td>yes</td>
						<td>
							<ul class="list-unstyled">
									<li>Self</li>
									<li>Partner</li>
									<li>Friend</li>
									<li>Family</li>
									<li>Employer</li>
									<li>Employee</li>
									<li>Business</li>
									<li>Escrow</li>
									<li>Print / Cold storage</li>
									<li>MultiSigX – self escrow</li>
							</ul>
						</td>
						<td>Self</td>
					</tr>					
					</table>
				<?php 
		if(strlen($details['key'])>0){?>
		<div class="APIexample">
		<form action="/API/CreateWallet/<?=$key?>" method="post" target="_blank" role="form" class="form-horizontal">
		<div class="form-group">
    <label for="security" class="col-sm-2 control-label">Security</label>
    <div class="col-sm-5">
      <select name="security" id="security" class="col-sm-5 form-control">
							<option value="2">2 of 3</option>
							<option value="3">3 of 3</option>
						</select>
    </div>
  </div>
		<div class="form-group">
    <label for="coin" class="col-sm-2 control-label">Coin</label>
    <div class="col-sm-5">
      <select name="coin" id="coin" class="col-sm-5 form-control">
							<option value="XGC">XGC - GreenCoin</option>
							<option value="BTC">BTC - Bitcoin</option>
						</select>
    </div>
  </div>  
		<div class="form-group">
    <label for="coinName" class="col-sm-2 control-label">CoinName</label>
    <div class="col-sm-5">
      <input type="text" name="coinName" id="coinName"  class="form-control">
    </div>
  </div>  
		<div class="form-group">
    <label for="changeAddress" class="col-sm-2 control-label">Change Address</label>
    <div class="col-sm-5">
      <select name="changeAddress" id="changeAddress" class="col-sm-5 form-control">
							<option value="MultiSigX">MultiSigX</option>
							<option value="Simple">Your own address</option>
						</select>
    </div>
  </div>  		
		<div class="form-group">
    <label for="changeAddressValue" class="col-sm-2 control-label">Change Address value</label>
    <div class="col-sm-5">
      <input type="text" name="changeAddressValue" id="changeAddressValue"  class="form-control">
    </div>
  </div>  		
		<div class="form-group">
    <label for="email1" class="col-sm-2 control-label">Email 1</label>
    <div class="col-sm-5">
      <input type="text" name="email1" id="email1"  class="form-control">
    </div>
  </div>  				
		<div class="form-group">
    <label for="relation1" class="col-sm-2 control-label">Relation 1</label>
    <div class="col-sm-5">
      <select name="relation1" id="relation1" class="col-sm-5 form-control">
							<option value="Self">Self</option>
							<option value="Print storage">Print storage</option>
							<option value="Partner">Partner</option>
							<option value="Friend">Friend</option>
							<option value="Family">Family</option>
							<option value="Escrow">Escrow</option>
							<option value="Employer">Employer</option>
							<option value="Employee">Employee</option>
							<option value="Business">Business</option>
						</select>
    </div>
  </div>  		
		<div class="form-group">
    <label for="email2" class="col-sm-2 control-label">Email 2</label>
    <div class="col-sm-5">
      <input type="text" name="email2" id="email2" class="form-control">
    </div>
  </div>  				
		<div class="form-group">
    <label for="relation2" class="col-sm-2 control-label">Relation 2</label>
    <div class="col-sm-5">
      <select name="relation2" id="relation2" class="col-sm-5 form-control">
							<option value="Self">Self</option>
							<option value="Print storage">Print storage</option>
							<option value="Partner">Partner</option>
							<option value="Friend">Friend</option>
							<option value="Family">Family</option>
							<option value="Escrow">Escrow</option>
							<option value="Employer">Employer</option>
							<option value="Employee">Employee</option>
							<option value="Business">Business</option>
						</select>
    </div>
  </div>  		
		<div class="form-group">
    <label for="email3" class="col-sm-2 control-label">Email 3</label>
    <div class="col-sm-5">
      <input type="text" name="email3" id="email3"  class="form-control">
    </div>
  </div>  				
		<div class="form-group">
    <label for="relation3" class="col-sm-2 control-label">Relation 3</label>
    <div class="col-sm-5">
      <select name="relation3" id="relation3" class="col-sm-5 form-control">
							<option value="Self">Self</option>
							<option value="Print storage">Print storage</option>
							<option value="Partner">Partner</option>
							<option value="Friend">Friend</option>
							<option value="Family">Family</option>
							<option value="Escrow">Escrow</option>
							<option value="Employer">Employer</option>
							<option value="Employee">Employee</option>
							<option value="Business">Business</option>
						</select>
    </div>
  </div>  		
		<input type="submit" value="Create Wallet and send emails" class="btn btn-primary">
		</form>
		</div>
		<?php }?>
		<h5>Returns</h5>
		<pre>
{
 "success":1,
 "now":1413457103,
 "result":
 {
  "security":"2",
  "coin":"XGC",
  "coinName":"SomeCoinName",
  "changeAddress":"MultiSigX",
  "changeAddressValue":"",
  "email1":"YourName@gmail.com",
  "relation1":"Self",
  "email2":"EmailName1@gmail.com",
  "relation2":"Self",
  "email3":"EmailName2@gmail.com",
  "relation3":"Self"
 }
}</pre>		
			<h4><a name="CreateTransaction">CreateTransaction</a></h4>
				<p class="alert alert-danger">All parameters are to be submitted by POST only</p>
				<p>URL: https://MultiSigX.com/API/CreateTransaction/<?=$key?>/parameters.......</p>
			<h5>Parameters</h5>
				<table class="table table-condensed table-bordered table-hover" style="width:50% ">
					<tr>
					<th>Parameter</th>
					<th>Required</th>
					<th>Description</th>
					<th>Default</th>
					</tr>
					<tr>
						<td>address</td>
						<td>yes</td>
						<td>Withdrawal address</td>
						<td>None</td>
					</tr>
					<tr>
						<td>amount</td>
						<td>yes</td>
						<td>Withdrawal amount</td>
						<td>None</td>
					</tr>
				</table>
				<?php 
		if(strlen($details['key'])>0){?>
		<div class="APIexample">
			<form action="/API/CreateTransaction/<?=$key?>" method="post" target="_blank" role="form" class="form-horizontal">
		<div class="form-group">
    <label for="address" class="col-sm-2 control-label">Address</label>
    <div class="col-sm-5">
      <input type="text" name="address" id="Address" class="form-control">
    </div>
  </div>  				
		<div class="form-group">
    <label for="amount" class="col-sm-2 control-label">Amount</label>
    <div class="col-sm-5">
      <input type="text" name="amount" id="Amount" class="form-control">
    </div>
  </div>  							
		<input type="submit" value="Create Transaction" class="btn btn-primary">
			</form>
		</div>
		<?php }?>
		<h5>Returns</h5>
		<pre>
{
 "success":1,
 "now":1413457103,
 "result":
 {
  "address":"1CExstj2rh9NEcDMb7F2PQTDdDHasTBvXD",
  "amount":"10",
 }
}</pre>		
			<h4><a name="SignTransaction">SignTransaction</a></h4>
				<p class="alert alert-danger">All parameters are to be submitted by POST only</p>
				<p>URL: https://MultiSigX.com/API/SignTransaction/<?=$key?>/parameters.......</p>
			<h5>Parameters</h5>
				<table class="table table-condensed table-bordered table-hover" style="width:50% ">
					<tr>
					<th>Parameter</th>
					<th>Required</th>
					<th>Description</th>
					<th>Default</th>
					</tr>
					<tr>
						<td>privatekey</td>
						<td>yes</td>
						<td>Your Private Key</td>
						<td>None</td>
					</tr>
				</table>			
				<?php 
		if(strlen($details['key'])>0){?>
		<div class="APIexample">
		</div>
		<?php }?>
		<h5>Returns</h5>
		<pre>
{
 "success":1,
 "now":1413457103,
 "result":
 {
  "address":"1CExstj2rh9NEcDMb7F2PQTDdDHasTBvXD",
  "amount":"10",
 }
}</pre>		

				<h4><a name="SendTransaction">SendTransaction</a></h4>
				<p class="alert alert-danger">All parameters are to be submitted by POST only</p>
				<p>URL: https://MultiSigX.com/API/SendTransaction/<?=$key?>/parameters.......</p>
			<h5>Parameters</h5>
				<table class="table table-condensed table-bordered table-hover" style="width:50% ">
					<tr>
					<th>Parameter</th>
					<th>Required</th>
					<th>Description</th>
					<th>Default</th>
					</tr>
					<tr>
						<td>privatekey</td>
						<td>yes</td>
						<td>Your Private Key</td>
						<td>None</td>
					</tr>
				</table>			
				<?php 
		if(strlen($details['key'])>0){?>
		<div class="APIexample">
		</div>
		<?php }?>
		<h5>Returns</h5>
		<pre>
{
 "success":1,
 "now":1413457103,
 "result":
 {
  "address":"1CExstj2rh9NEcDMb7F2PQTDdDHasTBvXD",
  "amount":"10",
 }
}</pre>		
				
			</div>
		</div>
	</div>
</div>