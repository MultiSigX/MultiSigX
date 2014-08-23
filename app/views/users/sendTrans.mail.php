<div style="background-color:#eeeeee;height:50px;padding-left:20px;padding-top:10px">
	<img src="https://<?=COMPANY_URL?>/img/<?=COMPANY_URL?>.png" alt="<?=COMPANY_URL?>">
</div>
<h4>Hi,</h4>
<p><?=$compact['data']['who']['firstname']?> <?=$compact['data']['who']['lastname']?>, with username: <?=$compact['data']['who']['username']?> and email: <?=$compact['data']['who']['email']?> had signed a withdrawal of <?=$compact['data']['withdraw_amount']?> <?=$compact['data']['currency']?> <?=$compact['data']['currencyName']?>.</p>

<p>The MultiSigX address: <?=$compact['data']['multiAddress']?> is shared by:
<ul>
	<li><?=$compact['data']['emails'][0]?> - <?=$compact['data']['relation'][0]?></li>
	<li><?=$compact['data']['emails'][1]?> - <?=$compact['data']['relation'][1]?></li>
	<li><?=$compact['data']['emails'][2]?> - <?=$compact['data']['relation'][2]?></li>
</ul>
</p>

<p>The amount <?=$compact['data']['withdraw_amount']?> <?=$compact['data']['currency']?> <?=$compact['data']['currencyName']?> has been withdrawn to <?=$compact['data']['withdraw_address']?>, with commission amount <?=$compact['data']['commission_amount']?> and transaction fees to miners <?=$compact['data']['tx_fee']?> from the transaction id txid: "<?=$compact['data']['txid']?>"</p>
<p>The transaction is complete.</p>
<p>Thanks,<br>
MultiSigX 
</p>


