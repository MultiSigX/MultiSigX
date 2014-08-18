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
					<button type="button" class="btn btn-success 
					<?php if($next != '' ){ ?>
						disabled
					<?php }?>" 
					data-toggle="modal" data-target="#ModalCreate">Create </button>
				  </div>
				  <div class="btn-group">
					<button type="button" class="btn btn-warning <?php if($next != 1){?>disabled<?php }?>" 
					data-toggle="modal" data-target="#ModalSign">Sign </button>
				  </div>
				  <div class="btn-group">
					<button type="button" class="btn btn-danger <?php if($next != 2){?>disabled<?php }?>"
					data-toggle="modal" data-target="#ModalConfirm">Confirm </button>
				  </div>
				</div>
			</div>
			<div class="panel-footer"></div>
		</div>
	</div>
</div>
<?php
$Amount = (float)$final_balance-$currencies['txFee']-($final_balance*$commission/100);
?>

<div class="modal fade" id="ModalCreate" tabindex="-1" role="dialog" aria-labelledby="myModalCreate" aria-hidden="true">
	<?=$this->form->create("",array('url'=>'/users/createTrans','class'=>'form-group has-error')); ?>
	<?=$this->form->hidden('address', array('value'=>$addresses['msxRedeemScript']['address'])); ?>
	<?=$this->form->hidden('currency', array('value'=>$addresses['currency'])); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      	<h4 class="modal-title" id="CreateModalLabel">Create Transaction</h4>
      </div>
      <div class="modal-body" style="text-align:center ">
				<?=$this->form->field('sendToAddress', array('type' => 'text', 'label'=>'To Address','placeholder'=>'1J2sVS4Yt5QRrvBK22ZySqgsMNe9ypysqo','class'=>'form-control','onBlur'=>'CreateTrans('.$final_balance.','.$commission.','.$currencies['txFee'].');' )); ?>
				<?=$this->form->field('sendAmount', array('type' => 'text', 'label'=>'Amount','placeholder'=>'0.0000','class'=>'form-control','value'=>$Amount,'readonly'=>'readonly')); ?>				
				<?=$this->form->field('sendTxFee', array('type' => 'text', 'label'=>'Tx Fee to Miners','placeholder'=>'0.0001','class'=>'form-control','value'=>$currencies['txFee'],'onBlur'=>'CreateTrans('.$final_balance.','.$commission.',this.value);' )); ?>
				<?=$this->form->field('commission', array('type' => 'text', 'label'=>'Commission %','placeholder'=>'1','class'=>'form-control','value'=>$commission,'readonly'=>'readonly' )); ?>
				<?=$this->form->field('commissionValue', array('type' => 'text', 'label'=>'Commission','placeholder'=>'1','class'=>'form-control','value'=>$final_balance*$commission/100,'readonly'=>'readonly' )); ?>				
      </div>
      <div class="modal-footer">
				<?=$this->form->submit('Create' ,array('class'=>'btn btn-primary','disabled'=>'disabled','id'=>'CreateSubmit')); ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
	<?=$this->form->end(); ?>	
</div>


<div class="modal fade" id="ModalSign" tabindex="-1" role="dialog" aria-labelledby="myModalSign" aria-hidden="true">
	<?=$this->form->create("",array('url'=>'/users/signTrans','class'=>'form-group has-error')); ?>
	<?=$this->form->hidden('address', array('value'=>$addresses['msxRedeemScript']['address'])); ?>
	<?=$this->form->hidden('currency', array('value'=>$addresses['currency'])); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      	<h4 class="modal-title" id="SignModalLabel">Sign Transaction</h4>
      </div>
      <div class="modal-body" style="text-align:center ">
				<p>You will need the private key from the document<br><strong>MultiSigX.com-<?=$addresses['name']?>-MSX-Print-[x].pdf</strong> which was emailed to you on <?=gmdate('Y-M-d H:i:s',$addresses['DateTime']->sec)?></p>
				<?=$this->form->field('privKey', array('type' => 'text', 'label'=>'Private Key','placeholder'=>'5KWUbdCd6hScBzzToger9xUZmELnw16uK5d3j9TH85VJZddFmhw','class'=>'form-control','onBlur'=>'SignTrans();' )); ?>
      </div>
      <div class="modal-footer">
				<?=$this->form->submit('Sign' ,array('class'=>'btn btn-primary','disabled'=>'disabled','id'=>'SignSubmit')); ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
	<?=$this->form->end(); ?>	
</div>

<div class="modal fade" id="ModalConfirm" tabindex="-1" role="dialog" aria-labelledby="myModalConfirm" aria-hidden="true">
	<?=$this->form->create("",array('url'=>'/users/confirmTrans','class'=>'form-group has-error')); ?>
	<?=$this->form->hidden('address', array('value'=>$addresses['msxRedeemScript']['address'])); ?>
	<?=$this->form->hidden('currency', array('value'=>$addresses['currency'])); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      	<h4 class="modal-title" id="SignModalLabel">Confirm Transaction</h4>
      </div>
      <div class="modal-body" style="text-align:center ">
				<p>You will need the private key from the document<br><strong>MultiSigX.com-<?=$addresses['name']?>-MSX-Print-[x].pdf</strong> which was emailed to you on <?=gmdate('Y-M-d H:i:s',$addresses['DateTime']->sec)?></p>
				<?=$this->form->field('confirmPrivKey', array('type' => 'text', 'label'=>'Private Key','placeholder'=>'5KWUbdCd6hScBzzToger9xUZmELnw16uK5d3j9TH85VJZddFmhw','class'=>'form-control','onBlur'=>'ConfirmTrans();' )); ?>
      </div>
      <div class="modal-footer">
				<?=$this->form->submit('Sign' ,array('class'=>'btn btn-primary','disabled'=>'disabled','id'=>'ConfirmSubmit')); ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
	<?=$this->form->end(); ?>	
</div>
