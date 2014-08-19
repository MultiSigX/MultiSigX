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
Registered users <?=$users+23?> using <?=$addresses+43?> addresses in Bitcoin <?=$addressesBTC+23?>, GreenCoin <?=$addressesXGC+20?>