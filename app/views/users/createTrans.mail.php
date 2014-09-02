<div style="background-color:#eeeeee;height:50px;padding-left:20px;padding-top:10px">
	<img src="https://<?=COMPANY_URL?>/img/<?=COMPANY_URL?>.png" alt="<?=COMPANY_URL?>">
</div>
<h4>Hi,</h4>
<p><?=$compact['data']['who']['firstname']?> <?=$compact['data']['who']['lastname']?>, with username: <?=$compact['data']['who']['username']?> and email: <?=$compact['data']['who']['email']?> has created a withdrawal of <?=$compact['data']['withdraw_amount']?> <?=$compact['data']['currency']?> <?=$compact['data']['currencyName']?>.</p>

<p>The MultiSigX address: <?=$compact['data']['multiAddress']?> is shared by:
<ul>
	<li><?=$compact['data']['emails'][0]?> - <?=$compact['data']['relation'][0]?></li>
	<li><?=$compact['data']['emails'][1]?> - <?=$compact['data']['relation'][1]?></li>
	<li><?=$compact['data']['emails'][2]?> - <?=$compact['data']['relation'][2]?></li>
</ul>
</p>

<p>The amount for <?=$compact['data']['currency']?> <?=$compact['data']['currencyName']?> has been withdrawn to:<br>
1. <?=$compact['data']['withdraw.address.0']?> - <?=number_format($compact['data']['withdraw.amount.0'],8)?><br>
2. <?=$compact['data']['withdraw.address.1']?> - <?=number_format($compact['data']['withdraw.amount.1'],8)?><br>
3. <?=$compact['data']['withdraw.address.2']?> - <?=number_format($compact['data']['withdraw.amount.2'],8)?><br>
 with commission amount <?=$compact['data']['commission_amount']?> and transaction fees to miners <?=$compact['data']['tx_fee']?> from the transaction id txid: "<?=$compact['data']['txid']?>"</p>

<p>
The raw transaction is "<?=$compact['data']['createrawtransaction']?>".
</p>

<p>The coin <?=$compact['data']['multiAddress']?> is using <?=$compact['data']['security']?> of 3 security, so you will need to sign this transaction using MultiSigX.com or any other client. You will also need access to your private keys sent by email on <?=gmdate('Y-M-d H:i:s',$compact['data']['DateTime']->sec)?> as MultiSigX.com-<?=$compact['data']['name']?>-MSX-Print-[x].pdf.</p>

<p>Please sign in to MultiSigX.com and follow the Dashboard->Withdraw coins option.</p>

<p>If you do not approve the transaction, you need not do anything. Your coins will be secure on the address <?=$compact['data']['multiAddress']?> until <?=$compact['data']['security']?> of 3 of the above users sign the transaction.</p>

<p>Thanks,<br>
MultiSigX 
</p>

