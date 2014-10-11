<div style="background-color:#eeeeee;height:50px;padding-left:20px;padding-top:10px">
	<img src="https://<?=COMPANY_URL?>/img/<?=COMPANY_URL?>.png" alt="<?=COMPANY_URL?>">
</div>
<h4>Hi,</h4>
<p><?=$compact['data']['who']['firstname']?> <?=$compact['data']['who']['lastname']?>, with username: <?=$compact['data']['who']['username']?> and email: <?=$compact['data']['who']['email']?> had signed a withdrawal of <?=number_format($compact['data']['withdraw.amount.0']+$compact['data']['withdraw.amount.1']+$compact['data']['withdraw.amount.2'],8)?> <?=$compact['data']['currency']?> <?=$compact['data']['currencyName']?>.</p>

<p>The MultiSigX address: <?=$compact['data']['multiAddress']?> is shared by:
<ul>
	<li><?=$compact['data']['emails'][0]?> - <?=$compact['data']['relation'][0]?></li>
	<li><?=$compact['data']['emails'][1]?> - <?=$compact['data']['relation'][1]?></li>
	<li><?=$compact['data']['emails'][2]?> - <?=$compact['data']['relation'][2]?></li>
</ul>
</p>

<p>The amount for <?=$compact['data']['currency']?> <?=$compact['data']['currencyName']?> has been withdrawn to :<br>
1. <?=$compact['data']['withdraw.address.0']?> - <?=number_format($compact['data']['withdraw.amount.0'],8)?><br>
2. <?=$compact['data']['withdraw.address.1']?> - <?=number_format($compact['data']['withdraw.amount.1'],8)?><br>
3. <?=$compact['data']['withdraw.address.2']?> - <?=number_format($compact['data']['withdraw.amount.2'],8)?><br>
* Change Address: <?=$compact['data']['withdraw.changeAddress']?> - <?=number_format($compact['data']['withdraw.changeAmount'],8)?><br>

 with commission amount <?=number_format($compact['data']['commission_amount'],8)?> and transaction fees to miners <?=number_format($compact['data']['tx_fee'],8)?> from the transaction id txid: "<?=$compact['data']['txid']?>"</p>

<p>The transaction is complete.</p>
<p>Thanks,<br>
MultiSigX 
</p>


