<div style="background-color:#eeeeee;height:50px;padding-left:20px;padding-top:10px">
	<img src="https://<?=COMPANY_URL?>/img/<?=COMPANY_URL?>.png" alt="<?=COMPANY_URL?>">
</div>
<h4>Hi,</h4>
<p>User <strong><?=$compact['data']['username']?>, <?=$compact['data']['createEmail']?></strong> has added you as one of the security for the coins.</p>
<p>Coin Name: <?=$compact['data']['CoinName']?></p>
<p>Users responsible:
	<ul>
		<li><?=$compact['data']['createEmail']?>: <?=$compact['data']['relation0']?></li>
		<li><?=$compact['data']['user1']?>: <?=$compact['data']['relation1']?></li>
		<li><?=$compact['data']['user2']?>: <?=$compact['data']['relation2']?></li>
	</ul>
</p>
<p><strong>Security: <?=$compact['data']['security']?> of 3</strong></p>
<p>Attached <strong>MultiSigX.com-<?=$compact['data']['name']?>-MSX-Print-<?=$compact['data']['i']?>.pdf</strong> file is a copy of your MultiSigX&#8482;. It contains the encrypted keys used to secure your MultiSigX <?=$compact['data']['currencyName']?> <?=$compact['data']['currency']?> wallets. You must keep this email in your records for safe keeping. Because it is encrypted with your passcode, and because we do not have a copy of your passcode, forgetting the passcode used with your accounts will result in loss of your <?=$compact['data']['currencyName']?>. You may want to print the attached file and store it in a safe place. </p>
<p>You can deposit your coins to <strong><?=$compact['data']['address']?></strong> using your favorite clients.</p>
<p>If you want to withdraw coins from this <strong><?=$compact['data']['address']?></strong> address, then you will have to signin/create a new account on <a href="https://multisigx.com">MultiSigX</a>.</p>
<p>If <?=$compact['data']['username']?> withdraws from this <strong><?=$compact['data']['address']?></strong> address which requires multiple signatures, we will send you an email for signing instruction for approval of coins.</p>

<p>&copy; MultiSigX&#8482; Inc.</p>
