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
						$commarray = array();
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
						<?php 
						
							foreach($relations as $relation){
								if($d['relation']==$relation['type']){
									array_push($commarray, $relation['commission']);
								}
							}
						}
//						print_r($commarray);
//						print_r(max($commarray));
						$commission = max($commarray);
						?>
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
				  <div class="progress-bar progress-bar-success progress-bar-striped active" style="width: 33.3%">
				  </div>
				<?php }?>
				<?php if($next >= 2){?>				  
				  <div class="progress-bar progress-bar-warning progress-bar-striped active" style="width: 33.3%">
				  </div>
				<?php }?>				  
				<?php if($next >= 3){?>				  
				  <div class="progress-bar progress-bar-danger progress-bar-striped active" style="width: 33.3%">
				  </div>
				<?php }?>				  
				</div>
				<div class="btn-group btn-group-justified">
				  <div class="btn-group">
					<button type="button" class="btn btn-success <?php if($next != '' ){?>disabled<?php }?>" 
					data-toggle="modal" data-target="#ModalCreate"
					>Create </button>
				  </div>
				  <div class="btn-group">
					<button type="button" class="btn btn-warning <?php if($next != 1){?>disabled<?php }?>">Sign </button>
				  </div>
				  <div class="btn-group">
					<button type="button" class="btn btn-danger" <?php if($next != 2){?>disabled<?php }?>>Confirm </button>
				  </div>
				</div>
			</div>
			<div class="panel-footer"></div>
		</div>
	</div>
</div>

<div class="modal fade" id="ModalCreate" tabindex="-1" role="dialog" aria-labelledby="myModalCreate" aria-hidden="true">
	<?=$this->form->create("",array('url'=>'/users/createTrans','class'=>'form-group has-error')); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      	<h4 class="modal-title" id="CreateModalLabel">Create Transaction</h4>
      </div>
      <div class="modal-body" style="text-align:center ">
				<?=$this->form->field('sendToAddress', array('type' => 'text', 'label'=>'To Address','placeholder'=>'1J2sVS4Yt5QRrvBK22ZySqgsMNe9ypysqo','class'=>'form-control' )); ?>
				<?=$this->form->field('sendAmount', array('type' => 'text', 'label'=>'Amount','placeholder'=>'0.0000','class'=>'form-control','value'=>$final_balance )); ?>				
				<?=$this->form->field('sendTxFee', array('type' => 'text', 'label'=>'Tx Fee to Miners','placeholder'=>'0.0001','class'=>'form-control','value'=>$currencies['txFee'] )); ?>
				<?=$this->form->field('commission', array('type' => 'text', 'label'=>'Commission %','placeholder'=>'1','class'=>'form-control','value'=>$commission,'readonly'=>'readonly' )); ?>				
      </div>
      <div class="modal-footer">
				<?=$this->form->submit('Create' ,array('class'=>'btn btn-primary')); ?>								
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
	<?=$this->form->end(); ?>	
</div>
