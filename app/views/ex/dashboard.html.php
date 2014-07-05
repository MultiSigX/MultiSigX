<div class="white">
	<div class="col-md-12  container-fluid" >
		<div class="panel panel-primary">
			<div class="panel-heading">Dashboard</div>
			<div class="panel-body">
				<div class="row" style="margin:10px">
					<div class="col-md-12" style="border:1px solid gray;padding:10px " >
					<?php foreach($currencies as $currency){ 
					$curr = $curr . ", ". $currency['currency']['name']." ".$currency['currency']['unit'];
					}?>
					<?php 
					$current = array();
					foreach($addresses as $address){
						array_push($current, $address['currency']);
					}
					?>
					<h3>You can create one MultiSigX in these <?=substr($curr,1,strlen($curr))?> currencies.</h3> 
					<h4>Your current MultiSigX</h4>
					<p>Secure your bitcoins on MultiSigX wallets.
					<?php foreach($currencies as $currency){
					if(!in_array($currency['currency']['unit'],$current)){
					?>
					<a href="/ex/create" class="btn btn-primary">Create new <?=$currency['currency']['name']?> MultiSigX</a>
					<?php }
					}?>
					</p>
					<?php if(count($addresses)==0){?>
						<p class="alert alert-danger">You have not created any MultiSigX wallets.</p>
					<?php }else{?>
						<h2>Your own wallets:</h2>
					<?php }?>					
					<?php foreach($addresses as $address){?>
					<hr>
					<div class="row">					
						<div class="col-md-1 col-xs-6"><?=$address['currencyName']?></div>
						<div class="col-md-3 col-xs-6"><a href="/ex/name/<?=$address['name']?>"><?=$address['name']?></a></div>						
						<div class="col-md-2 col-xs-6">
							<a href="#" class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Deposit coins" data-toggle="modal" data-target="#DepositCoins" onClick="DepositCoins('<?=$address['currencyName']?>','<?=$address['msxRedeemScript']['address']?>');"><i class="icon-chevron-left icon-large"></i></a>
							&nbsp;&nbsp;
							<a href="#" class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Check address balance" data-toggle="modal" data-target="#CheckBalance" onClick="CheckBalance('<?=$address['currencyName']?>','<?=$address['currency']?>','<?=$address['msxRedeemScript']['address']?>');"><i class="icon-tasks icon-large"></i></a>
							&nbsp;&nbsp;
							<a href="/ex/withdraw/<?=$address['msxRedeemScript']['address']?>"  class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Withdraw coins"><i class="icon-chevron-right icon-large"></i></a>

						</div>
						<div class="col-md-2 col-xs-6"><strong><?=$address['security']?></strong> of 3 
<small><?=gmdate('Y-M-d h:i:s',$address['DateTime']->sec)?></small></div>						

						<div class="col-md-4 col-xs-12"><strong>
							<a href="/ex/address/<?=$address['msxRedeemScript']['address']?>"><code><?=$address['msxRedeemScript']['address']?></code></a>
							</strong>
						</div>						
					</div> <!--  row -->
					<?php }?>
					<?php if(count($addresses)==0){?>
					<p class="alert alert-danger">You are not referred in any MultiSigX wallets.</p>
					<?php }else{?>
					<h2>You are referred in wallets:</h2>					
					<?php }?>
					<?php foreach($refered as $address){
						if($address['username']!=$user['username']){
					?>
					<hr>
					<div class="row">					
						<div class="col-md-1 col-xs-6"><?=$address['currencyName']?></div>
						<div class="col-md-3 col-xs-6"><a href="/ex/name/<?=$address['name']?>"><?=$address['name']?></a></div>						
						<div class="col-md-2 col-xs-6">
							<a href="#" class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Deposit coins" data-toggle="modal" data-target="#DepositCoins" onClick="DepositCoins('<?=$address['currencyName']?>','<?=$address['msxRedeemScript']['address']?>');"><i class="icon-chevron-left icon-large"></i></a>
							&nbsp;&nbsp;
							<a href="#" class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Check address balance" data-toggle="modal" data-target="#CheckBalance" onClick="CheckBalance('<?=$address['currencyName']?>','<?=$address['currency']?>','<?=$address['msxRedeemScript']['address']?>');"><i class="icon-tasks icon-large"></i></a>
							&nbsp;&nbsp;
							<a href="/ex/withdraw/<?=$address['msxRedeemScript']['address']?>"  class=" tooltip-x" rel="tooltip-x" data-placement="top" title="Withdraw coins"><i class="icon-chevron-right icon-large"></i></a>

						</div>
						<div class="col-md-2 col-xs-6"><strong><?=$address['security']?></strong> of 3 
<small><?=gmdate('Y-M-d h:i:s',$address['DateTime']->sec)?></small></div>						

						<div class="col-md-4 col-xs-12"><strong>
							<a href="/ex/address/<?=$address['msxRedeemScript']['address']?>"><code><?=$address['msxRedeemScript']['address']?></code></a>
							</strong>
						</div>						
					</div> <!--  row -->
						<?php }?>
					<?php }?>
					</div>
				</div>
			</div>
			<div class="panel-footer">You can get 100 XGC, GreenCoins (Identified Digital Currency) from <a href="http://greencoin.io" target="_blank">http://greencoin.io</a>, try MultiSigX security.  </div>
		</div>
	</div>
</div>
<?=$final?>

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