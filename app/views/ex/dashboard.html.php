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
							<td><strong><a href="/ex/address/<?=$address['msxRedeemScript']['address']?>"><?=$address['msxRedeemScript']['address']?></a></strong></td>							
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
							<td><strong><a href="/ex/address/<?=$address['msxRedeemScript']['address']?>"><?=$address['msxRedeemScript']['address']?></a></strong></td>							
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