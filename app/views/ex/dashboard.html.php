<div class="row container-fluid">
	<?php echo $this->_render('element', 'sidebar-menu');?>	

	<div class="col-md-9  container-fluid" >
		<div class="panel panel-primary">
			<div class="panel-heading">Dashboard</div>
			<div class="panel-body" style="text-align:center">
			
						<?php foreach($currencies as $currency){ 
						$curr = $curr . ", ". $currency['currency']['name']." ".$currency['currency']['unit'];
						}?>
						<?php 
						$current = array();
						foreach($addresses as $address){
							array_push($current, $address['currency']);
						}
						?>
						<?php if($msg!=""){ ?>
							<p class="alert alert-warning"><?=$msg?></p>
						<?php }	?>
						<h3>You can create MultiSigX in: <?=substr($curr,1,strlen($curr))?> currencies.</h3> 
						<h4>Secure your coins on MultiSigX wallets.</h4>
						<?php 
//							foreach($currencies as $currency){
//							if(!in_array($currency['currency']['unit'],$current)){
									?>
									<a href="/ex/create" class="btn btn-primary">Create new 
									<?php
									//=$currency['currency']['name'];
									?> MultiSigX</a>
							<?php 
//								}
//					}?>
							<br><br>

						<?php if($what==""){?>
							<!------------------ My Wallets ------------------->														
							<div class="panel panel-primary">
								<div class="panel-heading"><?php 
									if(count($addresses)==0){?>
										<p class="alert alert-danger">You have not created any MultiSigX wallets.</p>
								<?php 
									}else{?><h2>My wallets:</h2><?php 
									}?></div>
								<div class="panel-body">
								<?php if($what==""){?>
									<div class="row">
									<?php foreach($addresses as $address){?>
										<div class="col-md-4">
											<a href="/ex/dashboard/<?=$address['name']?>"><?=$address['name']?></a>
										</div>
										<div class="col-md-4">
										<?=$address['CoinName']?>
										</div>
										<div class="col-md-4">
										<?=$address['currencyName']?> - <?=$address['currency']?>
										</div>
									<?php }?>
									</div>
								<?php }?>
								</div>
								<div class="panel-footer">
								The above wallets are secured by MultiSigX, be safe, be secure.
								</div>
							</div>
							<!------------------ My Wallets ------------------->							
							<!------------------ Referred Wallets ------------------->
							<div class="panel panel-primary">
								<div class="panel-heading">
									<?php 
										if(count($refered)==0){?>
											<p class="alert alert-danger">You are not using any MultiSigX wallets.</p>
											<?php 
										}else{?>
											<h2>My other wallets:</h2>					
										<?php 
										}?>
								</div>
								<div class="panel-body">
								<?php if($what==""){?>
									<div class="row">
									<?php foreach($refered as $address){?>
										<div class="col-md-4">
											<a href="/ex/dashboard/<?=$address['name']?>"><?=$address['name']?></a>
										</div>
										<div class="col-md-4">
										<?=$address['CoinName']?>
										</div>
										<div class="col-md-4">
										<?=$address['currencyName']?> - <?=$address['currency']?>
										</div>
									<?php }?>
									</div>
								</div>
								<?php }?>
								<div class="panel-footer">
								The above wallets are secured by MultiSigX, be safe, be secure.
								</div>
							</div>		
								<!------------------ Referred Wallets ------------------->
								<?php }else{?>
								
								<hr class="fearurette-dashboard">
								<h3><?=$singleaddress['currencyName']?> <?=$singleaddress['currency']?></h3>
								<h3><a href="/ex/name/<?=$singleaddress['name']?>"><?=$singleaddress['CoinName']?></a></h3>
								<h3><small><a href="/ex/name/<?=$singleaddress['name']?>"><?=$singleaddress['name']?></a></small></h3>
								<h4>
													<?php 
														foreach($balances as $balance){?>
														<?php if($balance['address']==$singleaddress['msxRedeemScript']['address']){?>
																<h4><?=$balance['balance']." ". $singleaddress['currency'];?></h4>
														<?php 
															if((float)$balance['balance']<=0){$disabled = ' disabled ';}else{$disabled = '';}
														}?>
													<?php }	?>
								<a href="#" class=" tooltip-x btn btn-success active btn-lg" rel="tooltip-x" data-placement="top" title="Deposit coins" data-toggle="modal" data-target="#DepositCoins" onClick="DepositCoins('<?=$singleaddress['currencyName']?>','<?=$singleaddress['msxRedeemScript']['address']?>');"><i class="fa-mail-reply fa fa-3x"></i> <br>&nbsp;Deposit&nbsp;</a>
								&nbsp;&nbsp;
								<a href="#" class=" tooltip-x btn btn-primary active btn-lg" rel="tooltip-x" data-placement="top" title="Check address balance" data-toggle="modal" data-target="#CheckBalance" onClick="CheckBalance('<?=$singleaddress['currencyName']?>','<?=$singleaddress['currency']?>','<?=$singleaddress['msxRedeemScript']['address']?>');"><i class="fa-tasks fa fa-3x"></i> <br>&nbsp;&nbsp;Check&nbsp;&nbsp;</a>
								&nbsp;&nbsp;
								<a href="/ex/withdraw/<?=$singleaddress['msxRedeemScript']['address']?>"  class=" tooltip-x btn btn-danger active btn-lg <?=$disabled?>" rel="tooltip-x" data-placement="top" title="Withdraw coins, create, sign and send"><i class="fa-mail-forward fa fa-3x"></i> <br>Withdraw</a>
								</h4>
								<h4><strong><?=$singleaddress['security']?></strong> of 3 <small><?=gmdate('Y-M-d h:i:s',$address['DateTime']->sec)?></small>
								<strong><a href="/ex/address/<?=$singleaddress['msxRedeemScript']['address']?>"><code><?=$singleaddress['msxRedeemScript']['address']?></code></a></strong>
													<a href="#" class="tooltip-x" rel="tooltip-x" data-placement="top" title="Delete MultiSigX address" onClick="DeleteCoin('<?=$singleaddress['msxRedeemScript']['address']?>')"><i class="fa-remove fa"></i></a>
									</h4>
									
									

								
								
								
								
								<?php } ?>
					</div>
			<div class="panel-footer">You can get 100 XGC, GreenCoins (Identified Digital Currency) from <a href="http://<?=GREENCOIN_WEB?>" target="_blank">http://<?=GREENCOIN_WEB?></a>, try MultiSigX security.  </div>
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
							<p>Send coins to this address. They are safe with <?=$address['security'] ?> of 3 security.</p>
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


<script>
//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse')
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse')
        }

        height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    })
});
</script>
