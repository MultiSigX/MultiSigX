<?php 
use lithium\storage\Session;
use app\models\Addresses;
use app\extensions\action\Functions;

		$user = Session::read('member');
		$id = $user['_id'];

		$addresses = Addresses::find('all',array(
			'conditions'=>array('username'=>$user['username'])
		));

	$refered = Addresses::find('all',array(
		'conditions'=>array(
			'addresses.email'=>$user['email'],
			'username'=>array('$ne'=>$user['username'])
		)
	));
	$function = new Functions();
		$countChild = $function->countChilds($id);
		$countParents = $function->countParents($id);		
		$levelOne = $function->levelOneChild($id);

	$friends = Addresses::find('all',array(
		'conditions'=>array('username'=>$user['username']),
	));
	$emails = array();
	foreach($friends as $friend){
		array_push($emails,$friend['addresses'][0]['email'] );
		array_push($emails,$friend['addresses'][1]['email'] );
		array_push($emails,$friend['addresses'][2]['email'] );		
	}
	$countfriends = count(array_unique( $emails));
?>	

<div class="col-md-3 container-fluid">
		<div class="panel panel-primary">
			<div class="panel-heading">MultiSigX - Menu</div>
			<div class="panel-body">
			<div id="sidebar"> 
				<a href="#One" class="list-group-item item-border" data-toggle="collapse" data-parent="#sidebar">
					<i class="fa fa-plus"></i> My wallets <span class="badge pull-right"><?=count($addresses)?></span>
				</a>
				<div id="One" class="list-group subitem collapse" style="padding:10px">
					<ul class="list-group">
						<?php foreach($addresses as $address){?>
						<li class="list-group-item">
							<a href="/ex/dashboard/<?=$address['name']?>" class="list-group-subitem">
								<?=$address['name']?>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div> <!-- subitem -->
				<a href="#Two" class="list-group-item item-border" data-toggle="collapse" data-parent="#sidebar">
					<i class="fa fa-plus"></i> My other wallets <span class="badge pull-right"><?=count($refered)?></span>
				</a>
				<div id="Two" class="list-group subitem collapse" style="padding:10px">
						<?php foreach($refered as $address){?>
						<li class="list-group-item">
							<a href="/ex/dashboard/<?=$address['name']?>" class="list-group-subitem">
								<?=$address['name']?>
							</a>
						</li>
						<?php } ?>
				</div>
				<a href="#Three" class="list-group-item item-border" data-toggle="collapse" data-parent="#sidebar">
					<i class="fa fa-plus"></i> Signin Referral  
				</a>
				<div id="Three" class="list-group subitem collapse" style="padding:10px">
						<li class="list-group-item">
							<a href="/ex/reference/All" class="list-group-subitem">
								All Child  <span class="pull-right badge "><?=$countChild?></span>
							</a>
						</li>
						<li class="list-group-item">
							<a href="/ex/reference/Two" class="list-group-subitem">
								> 2 Child <span class="pull-right badge "><?=$countChild-$levelOne?></span>
							</a>
						</li>
						<li class="list-group-item">
							<a href="/ex/reference/Parents" class="list-group-subitem">
								Parents <span class="pull-right badge "><?=$countParents?></span>
							</a>
						</li>

				</div>
				<!-- subitem -->
				<a href="/ex/friends" class="list-group-item item-border">
					<i class="fa fa-play"></i> Friends 
					<span class="pull-right badge "><?=$countfriends?></span>	
				</a> 
				<a href="/ex/settings" class="list-group-item item-border">
					<i class="fa fa-play"></i> Settings </a>
				<a href="/ex/dashboard" class="list-group-item item-border">
					<i class="fa fa-play"></i> Dashboard </a>

			</div> <!-- sidebar -->           
			</div>
		</div>
	</div>