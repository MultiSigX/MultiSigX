<?php
use app\models\Users;
use app\models\Details;
use app\models\Addresses;

$users = Users::count();
$addresses = Addresses::count();
?>
Registered users <?=$users+23?> using <?=addresses+43?> addresses.