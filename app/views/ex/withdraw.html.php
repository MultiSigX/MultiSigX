<div class="white">
	<div class="col-md-12  container-fluid" >
		<div class="panel panel-success">
			<div class="panel-heading"><a href="/ex/dashboard">Dashboard <i class="icon-chevron-left icon-large"></i></a>&nbsp;&nbsp;
			Address: <?=$addresses['msxRedeemScript']['address']?> <?=$addresses['currencyName']?> <?=$addresses['currency']?></div>
			<div class="panel-body" style="text-align:center">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h2>MultiSigX Coin Details</h2>
					</div>
					<div class="panel-body">
						<h4>Withdraw coins from<br>
						<code><?=$addresses['msxRedeemScript']['address']?></code><br>
						<?=$addresses['CoinName']?><br>
						<?=$addresses['name']?><br>
						<?=$addresses['currencyName']?> <?=$addresses['currency']?><br>
						Balance: <?=$final_balance?> <?=$addresses['currency']?><br>
						Security: <strong style="color:red"><?=$addresses['security']?></strong> sign required of 3 to send payments<br>
					</h4>
					</div>
					<div class="panel-footer">
					Created on: <?=gmdate(DATE_RFC850,$addresses['DateTime']->sec)?>
					</div>
				</div>
			<div class="row">
					<div class="col-md-12" style="text-align:center " >
						<div id="SelfUser" class="col-xs-12 col-md-6 col-md-offset-3" style="text-align:center" >
							<div class="panel panel-success">
								<div class="panel-heading">
								<h2><i class=" icon icon-user"></i> <?=$user['username']?></h2>
								</div>
								<div class="panel-body">
								<?php 
								$commarray = array();
								foreach($data as $d){?>
<?php
foreach($relations as $relation){
	if($d['relation']==$relation['type']){
		array_push($commarray, $relation['commission']);
	}
}
?>
								<?php if($d['username']==$user['username']){?>
										<h3><?=$d['relation']?>: <a href="mailto:<?=$d['email']?>?subject=MultiSigX" target="_blank"><?=$d['email']?></a></h3>
									<?php }?>
									
								<?php }
								$commission = max($commarray);
								?>
								<h3> Signed by:</h3>
								<?php if($transact['create']['username']==$user['username']){?>
								
									<h4>
									Transaction created on:<br>
									<span class="label label-success"><?=gmdate(DATE_RFC850,$transact['create']['DateTime']->sec)?></span><br><br>
									by <?=$transact['create']['username']?>, send to
									<?php 
									for($i=0;$i<3;$i++){?>
									<code class="label label-success"><?=number_format($transact['create']['withdraw']['amount'][$i],8)?> <?=$addresses['currency']?> - <?=$transact['create']['withdraw']['address'][$i]?></code><br>
									<?php }?>
									</h4>
									<?php if($transact['sign']==null){?>
									<a href="/users/DeleteCreateTrans/<?=$addresses['msxRedeemScript']['address']?>"><i class="glyphicon glyphicon-remove"></i> Delete</a>
									<?php }else{?>
									
									<?php }?>

								<?php }?>
								<?php 
									$signed = "No";
									$noOfSign = 0;
									if(count($transact['sign'])>0){
										foreach($transact['sign'] as $sign){
											$noOfSign++;
										}
									}
									foreach($transact['sign'] as $sign){
										echo "<h4>".$sign['username']." <br><small>".gmdate(DATE_RFC850,$sign['DateTime']->sec)."</small></h4>";
										if($sign['username']==$user['username']){	
											$signed = "Yes";		
											break;
										}
									}
								?>
								<?php if($signed=="No"){?>
											<h3><button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#Modal<?=$button?>"><?=$button?></button>
											</h3>
								<?php }else{ ?>
											<h3>You have already signed this transaction.</h3>
								<?php } ?>
								<?php if($noOfSign==$addresses['security']){?>
									<?php if($addresses['sendTrans']==null){?>
										<h3><button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#Modal<?=$button?>"><?=$button?></button>
									<?php }else{?>
										<h3>The transaction is complete!<br>Funds transferred!</h3>
									<?php }?>
								<?php }?>
								<?php if($msg=='No'){
									print_r("<div class='alert alert-danger'>Transaction not created!</div>");
								}?>								
								</div>
							</div>
						</div>
					</div>	
				</div>
				
				<div class="row">
					<?php foreach($data as $d){?>
						<?php if($d['username']!=$user['username']){?>
					<div id="User<?=$d['relation']?>" class="col-md-6 col-md-offset-3 col-xs-12 " style="text-align:center ">
						<div class="panel panel-danger">
								<div class="panel-heading">
								<h2><i class=" icon icon-user"></i> 
								<?php if($d['username']==""){?>
								Not Registered!
								<?php }else{?>
								<?=$d['username']?>
								<?php }?>
								</h2>
								</div>
								<div class="panel-body">
								<h2><?=$d['relation']?></h2>
									<br>
									<?=$d['email']?>
									<br>
									<code><?=$d['address']?></code>

					<?php if($transact['create']['username']==$d['username']){?>
						<h4>
						Transaction created on:<br>
						<span class="label label-success"><?=gmdate(DATE_RFC850,$transact['create']['DateTime']->sec)?></span><br><br>
						by <?=$transact['create']['username']?>, send to
						<?php 
						for($i=0;$i<3;$i++){?>
						<code class="label label-success"><?=number_format($transact['create']['withdraw']['amount'][$i],8)?> <?=$addresses['currency']?> - <?=$transact['create']['withdraw']['address'][$i]?></code><br>
						<?php }?>
						</h4>
					<?php }?>					

					
									
								</div>
							</div>
					</div>
						<?php }?>
					<?php }?>

				</div>

			</div>
			<div class="panel-footer"></div>
			</div>
		</div>
	</div>

<?php 
	$Amount = (float)$final_balance-$currencies['txFee']-($final_balance*$commission/100); 
	$comm = number_format($final_balance*$commission/100,4);
?>

<div class="modal fade" id="ModalCreate" tabindex="-1" role="dialog" aria-labelledby="myModalCreate" aria-hidden="true">
	<?=$this->form->create("",array('url'=>'/users/createTrans','class'=>'form-group has-error')); ?>
	<?=$this->form->hidden('address', array('value'=>$addresses['msxRedeemScript']['address'])); ?>
	<?=$this->form->hidden('SendTrxFee', array('value'=>0)); ?>
	<?=$this->form->hidden('CommissionTotal', array('value'=>number_format($comm,8))); ?>
	<?=$this->form->hidden('finalBalance', array('value'=>$final_balance)); ?>
	<?=$this->form->hidden('currency', array('value'=>$addresses['currency'])); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      	<h4 class="modal-title" id="CreateModalLabel">Create Transaction <small>for signatures</small></h4>
      </div>
      <div class="modal-body" style="text-align:center ">
							<table class="table table-stripped table-condensed">
								<tr>
									<th>#</th>
									<th width='70%'><?=$addresses['currencyName']?> Address <?=$addresses['currency']?></th>
									<th style="text-align:right">Amount <?=$addresses['currency']?></th>
								</tr>
								<?php 
								for($i=0;$i<3;$i++){
								if($i==0){$value = $Amount;}else{$value=0;}
								?>
								<tr>
									<td><?=($i+1)?></td>
									<td><?=$this->form->field('sendToAddress['.$i.']', array('type' => 'text', 'label'=>'','placeholder'=>'your coin address','class'=>'form-control','onBlur'=>'CreateTrans('.$final_balance.','.$commission.','.$currencies['txFee'].');' )); ?>
									<span id="Error<?=$i?>"></span>
									</td>
									<td>
									<?=$this->form->field('sendAmount['.$i.']', array('type' => 'number', 'label'=>'','placeholder'=>'0.0000','class'=>'form-control','value'=>$value,'min'=>0,'max'=>$Amount,'step'=>0.0001,'style'=>'text-align:right','onChange'=>'CreateTrans('.$final_balance.','.$commission.','.$currencies['txFee'].');','pattern'=>'' )); ?>
									</td>
								</tr>
								<?php } ?>
								<tr style="background-color:#ddd;color:black;font-size:large;">
									<td style="border-top:1px solid black"></td>
									<th style="border-top:1px solid black">Total</th>
									<td style="text-align:right;border-top:1px solid black"><?=number_format($Amount,8)?></td>
								</tr>
								<tr>
									<td style="border-top:1px solid black"></td>
									<th style="border-top:1px solid black">Tx Fee to Miners <?=$addresses['currency']?></th>
									<td style="background-color:#eee; text-align:right;border-top:1px solid black">
										<span  style="color:red;font-size:large" id="SendTxFee"><?=number_format($currencies['txFee'],8)?></span>
									</td>
								</tr>
								<tr>
									<td style="border-top:1px solid black"></td>
									<th style="border-top:1px solid black">Commission % of <?=$addresses['currency']?></th>
									<td style="background-color:#eee; text-align:right;border-top:1px solid black">
										<span  style="font-size:large" id="Commission"><?=number_format($commission,1)?>%</span>
									</td>
								</tr>
								<tr>
									<td style="border-top:1px solid black"></td>
									<th style="border-top:1px solid black">Commission <?=$addresses['currency']?></th>
									<td style="background-color:#eee; text-align:right;border-top:1px solid black"><span  style="font-size:large" id="CommissionValue"><?=number_format($comm,8)?></span></td>
								</tr>
								<tr style="background-color:#ddd;color:black;font-size:large;border-top:double;;border-bottom:double">
									<td></td>
									<th>Grand Total</th>
									<td style="text-align:right"><?=number_format($final_balance,8)?></td>
								</tr>
							</table>
							<div id="CreateAlert" class="alert alert-warning">
							Create transaction will not transfer funds to the addresses. It will authorize others to sign this transaction and finally transfer funds.
							</div>
      </div>
      <div class="modal-footer">
				<?=$this->form->submit('Create' ,array('class'=>'btn btn-primary','disabled'=>'disabled','id'=>'CreateSubmit','onClick'=>'return CheckTotal('.$final_balance.','.$commission.');')); ?>
				<?=$this->form->reset('Reset' ,array('class'=>'btn btn-primary','id'=>'ResetSubmit')); ?>
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

<div class="modal fade" id="ModalSend" tabindex="-1" role="dialog" aria-labelledby="myModalSend" aria-hidden="true">
	<?=$this->form->create("",array('url'=>'/users/sendTrans','class'=>'form-group has-error')); ?>
	<?=$this->form->hidden('address', array('value'=>$addresses['msxRedeemScript']['address'])); ?>
	<?=$this->form->hidden('currency', array('value'=>$addresses['currency'])); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      	<h4 class="modal-title" id="SignModalLabel">Send Transaction <small>from your MultiSigX wallet</small></h4>
      </div>
      <div class="modal-body" style="text-align:center ">
						<h4>Withdraw coins from<br>
						<code><?=$addresses['msxRedeemScript']['address']?></code><br>
						<?=$addresses['CoinName']?><br>
						<?=$addresses['name']?><br>
						<?=$addresses['currencyName']?> <?=$addresses['currency']?><br>
						Balance: <?=$final_balance?> <?=$addresses['currency']?><br>
						Security: <strong style="color:red"><?=$addresses['security']?></strong> sign required of 3 to send payments<br>
					</h4>
							<h4>
									Transaction created on:<br>
									<span class="label label-success"><?=gmdate(DATE_RFC850,$transact['create']['DateTime']->sec)?></span><br><br>
									by <?=$transact['create']['username']?>, send to
									<?php 
									for($i=0;$i<3;$i++){?>
									<code class="label label-success"><?=number_format($transact['create']['withdraw']['amount'][$i],8)?> <?=$addresses['currency']?> - <?=$transact['create']['withdraw']['address'][$i]?></code><br>
									<?php }?>
									</h4>
									<h3> Signed by:</h3>
									<?php 
									foreach($transact['sign'] as $sign){
										echo "<h4>".$sign['username']." <br><small>".gmdate(DATE_RFC850,$sign['DateTime']->sec)."</small></h4>";
									}
									?>
				  </div>
      <div class="modal-footer">
							<?=$this->form->submit('Send' ,array('class'=>'btn btn-primary','id'=>'SendSubmit')); ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
	<?=$this->form->end(); ?>	
</div>
