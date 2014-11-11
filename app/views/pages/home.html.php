<?php
use app\models\Users;
use app\models\Details;
use app\models\Addresses;

$users = Users::count();
$addresses = Addresses::count();
$addressesBTC = Addresses::find('count',array(
	'conditions'=>array('currencyName'=>'Bitcoin')
	));
$addressesXGC = Addresses::find('count',array(
	'conditions'=>array('currencyName'=>'GreenCoin')
	));
?>
<div class="container text-center ">
Registered users <?=$users+20?> using <?=$addresses+40?> secure MultiSigX wallets: Bitcoin (<?=$addressesBTC+20?>), GreenCoin (<?=$addressesXGC+20?>)
</div>