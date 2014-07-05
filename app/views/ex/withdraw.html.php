<div class="white">
	<div class="col-md-12  container-fluid" >
		<div class="panel panel-success">
			<div class="panel-heading"><a href="/ex/dashboard">Dashboard <i class="icon-chevron-left icon-large"></i></a>&nbsp;&nbsp;
			Address: <?=$addresses['msxRedeemScript']['address']?> <?=$addresses['currencyName']?> <?=$addresses['currency']?></div>
			<div class="panel-body">
				<h2>Withdraw coins from <?=$addresses['msxRedeemScript']['address']?></h2>
				<div class="row" style="margin:10px">
					<div class="col-md-12" style="border:1px solid gray;padding:10px " >
					<div class="table-responsive">
					<table class="table table-striped table-condensed table-bordered">
						<tr>
							<th>Email</th>
							<th><span class=" tooltip-x" rel="tooltip-x" data-placement="top" title="This address should be used for reciving withdrawal of funds from MultiSigX address"><?=$addresses['currencyName']?> Address: <?=$addresses['currency']?></span></th>							
							<th>Verified</th>
							<th>Relation</th>							
						</tr>
						<?php 
						foreach($data as $d){?>
							<tr <?php if($d['username']==$user['username']){?> style="color:#AA0000;font-weight:bold"<?php }?>>
								<td><?=$d['email']?></td>
								<td><code><?=$d['address']?></code></td>
								<td>
								<?php if($d['username']==""){?>
								Not Verified,
								<?php }else{?>
								<?=$d['username']?>,
								<?php }?>
								<?php if($d['username']==$user['username']){?>
								Authorised!
								<?php }else{?>
								Not authorised!
								<?php }?>
								</td>
								<td><?=$d['relation']?></td>
							</tr>
						<?php }?>
					</table>
					</div>
					</div>
				</div>
				<div class="row" style="text-align:center ">
					<div class="col-md-4">Balance: <?=$final_balance?> <?=$addresses['currency']?></div>
					<div class="col-md-4"></div>					
					<div class="col-md-4"></div>
				</div>
				<div class="progress">
				<?php if($next >= 1){?>
				  <div class="progress-bar progress-bar-success progress-bar-striped active" style="width: 33%">
					<span class="sr-only">33% Complete (success)</span>
				  </div>
				<?php }?>
				<?php if($next >= 2){?>				  
				  <div class="progress-bar progress-bar-warning progress-bar-striped active" style="width: 33%">
					<span class="sr-only">20% Complete (warning)</span>
				  </div>
				<?php }?>				  
				<?php if($next >= 3){?>				  
				  <div class="progress-bar progress-bar-danger progress-bar-striped active" style="width: 34%">
					<span class="sr-only">10% Complete (danger)</span>
				  </div>
				<?php }?>				  
				</div>
			</div>
			<div class="panel-footer"></div>
		</div>
	</div>
</div>