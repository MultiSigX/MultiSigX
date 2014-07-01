<div class="white">
	<div class="col-md-12  container-fluid" >
		<div class="panel panel-primary">
			<div class="panel-heading">Dashboard</div>
			<div class="panel-body">
				<div class="row" style="margin:10px">
					<div class="col-md-12" style="border:1px solid gray;padding:10px " >
					<h4>Your current MultiSigX</h4>
					<p>Secure your bitcoins on MultiSigX wallets.
					<a href="/ex/create" class="btn btn-primary">Create new</a>
					</p>
					<?php if(count($addresses)==0){?>
						<p class="alert alert-danger">You have not created any MultiSigX wallets.</p>
					<?php }else{?>
						<h4>Your own wallets:</h4>
					<?php }?>					
					<table class="table table-striped table-condensed table-bordered">
					<?php foreach($addresses as $address){?>
						<tr>
							<td><?=$address['currencyName']?></td>						
							<td><a href="/ex/name/<?=$address['name']?>"><?=$address['name']?></a></td>
							<td><strong>
							<a href="/ex/address/<?=$address['msxRedeemScript']['address']?>"><code><?=$address['msxRedeemScript']['address']?></code></a>
							<a href="#" class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Deposit coins" data-toggle="modal" data-target="#DepositCoins" onClick="DepositCoins('<?=$address['currencyName']?>','<?=$address['msxRedeemScript']['address']?>');"><i class="fa fa-chevron-left fa-lg"></i></a>
							&nbsp;&nbsp;
							<a href="#" class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Check address balance" data-toggle="modal" data-target="#CheckBalance" onClick="CheckBalance('<?=$address['currencyName']?>','<?=$address['currency']?>','<?=$address['msxRedeemScript']['address']?>');"><i class="fa fa-tasks fa-lg"></i></a>

							&nbsp;&nbsp;
							<a href="#"  class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Withdraw coins"><i class="fa fa-chevron-right fa-lg"></i></a>
							</strong></td>							
							<td><strong><?=$address['security']?></strong> of 3</td>													
							<td><small><?=gmdate('Y-M-d h:i:s',$address['DateTime']->sec)?></small></td>							
						</tr>
					<?php }?>
					</table>

					<?php if(count($addresses)==0){?>
					<p class="alert alert-danger">You are not referred in any MultiSigX wallets.</p>
					<?php }else{?>
					<h4>You are referred in wallets:</h4>					
					<?php }?>
					<table class="table table-striped table-condensed table-bordered">
					<?php foreach($refered as $address){?>
						<tr>
							<td><?=$address['currencyName']?></td>						
							<td><a href="/ex/name/<?=$address['name']?>"><?=$address['name']?></a></td>
							<td><strong>
							<a href="/ex/address/<?=$address['msxRedeemScript']['address']?>"><code><?=$address['msxRedeemScript']['address']?></code></a>
							<a href="#" class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Deposit coins" data-toggle="modal" data-target="#DepositCoins" onClick="DepositCoins('<?=$address['msxRedeemScript']['address']?>');"><i class="fa fa-chevron-left fa-lg"></i></a>
							&nbsp;&nbsp;
							<a href="#" class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Check address balance" data-toggle="modal" data-target="#CheckBalance" onClick="CheckBalance('<?=$address['currencyName']?>','<?=$address['currency']?>','<?=$address['msxRedeemScript']['address']?>');"><i class="fa fa-tasks fa-lg"></i></a>

							&nbsp;&nbsp;
							<a href="#"  class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Withdraw coins"><i class="fa fa-chevron-right fa-lg"></i></a>
							</strong></td>							
							<td><strong><?=$address['security']?></strong> of 3</td>													
							<td><small><?=gmdate('Y-M-d h:i:s',$address['DateTime']->sec)?></small></td>							
						</tr>
					<?php }?>
					</table>
					</div>
				</div>
			</div>
			<div class="panel-footer"></div>
		</div>
	</div>
</div>


<div class="modal fade" id="DepositCoins" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      	<h4 class="modal-title" id="DepositModalLabel">Deposit Coins</h4>
      </div>
      <div class="modal-body" style="text-align:center ">
        <h4 id="DepositAddress"></h4>
				<img src="holder.js/300x300" style="border:1px solid" id="DepositAddressImg">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="CheckBalance" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="CheckModalLabel">CheckBalance Coins</h4>
      </div>
      <div class="modal-body" style="text-align:center ">
        <h4 id="CheckBalanceAddress"></h4>
				<div id="CheckBalanceHTML" style="overflow:auto;height:400px">
				</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>