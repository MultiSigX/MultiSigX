<?php
use lithium\storage\Session;
use app\extensions\action\Functions;
?>
<?php $user = Session::read('member'); ?>
<div class="navbar-wrapper">
	<div class="container">
		<div class="navbar navbar-inverse navbar-static-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">...</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/"><img src="/img/MultiSigX.png" alt="MultiSigX"><sup><small><strong style="color:red">beta</strong></small></sup></a>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li><a href="/company/how">How it works?</a></li>
						<li><a href="/company/FAQ">FAQ</a></li>
					</ul>
					<ul class="nav navbar-nav pull-right">
					<?php if($user!=""){ ?>
								<li><a href="/ex/dashboard" class=" tooltip-x" rel="tooltip-x" data-placement="bottom" title="Dashboard "><i class="icon-dashboard icon-large"></i></a></li>
								<li><a href="/ex/settings" class=" tooltip-x" rel="tooltip-x" data-placement="bottom" title="Settings "><i class="icon-gears icon-large"></i></a></li>								
					<li><a href="#" class='dropdown-toggle tooltip-x' data-toggle='dropdown'  rel="tooltip-x" data-placement="bottom" title="Themes "><i class="icon-th icon-large"></i></a>
						<ul class="dropdown-menu">
							<li><a href="#" onClick="ChangeTheme('chrome','<?=str_replace("/","_",$_SERVER['REQUEST_URI'])?>');"><i class="icon-heart-empty"></i> Chrome</a></li>			
							<li><a href="#" onClick="ChangeTheme('flaty','<?=str_replace("/","_",$_SERVER['REQUEST_URI'])?>');"><i class="icon-heart-empty"></i> Flaty</a></li>			
							<li><a href="#" onClick="ChangeTheme('lumen','<?=str_replace("/","_",$_SERVER['REQUEST_URI'])?>');"><i class="icon-heart-empty"></i> Lumen</a></li>			
							<li><a href="#" onClick="ChangeTheme('readable','<?=str_replace("/","_",$_SERVER['REQUEST_URI'])?>');"><i class="icon-heart-empty"></i> Readable</a></li>			
							<li><a href="#" onClick="ChangeTheme('dynamic','<?=str_replace("/","_",$_SERVER['REQUEST_URI'])?>');"><i class="icon-heart-empty"></i> Dynamic</a></li>			
							<li><a href="#" onClick="ChangeTheme('united','<?=str_replace("/","_",$_SERVER['REQUEST_URI'])?>');"><i class="icon-heart-empty"></i> United</a></li>			
							<li><a href="#" onClick="ChangeTheme('yeti','<?=str_replace("/","_",$_SERVER['REQUEST_URI'])?>');"><i class="icon-heart-empty"></i> Yeti</a></li>			
							<li><a href="#" onClick="ChangeTheme('amelia','<?=str_replace("/","_",$_SERVER['REQUEST_URI'])?>');"><i class="icon-heart-empty"></i> Amelia</a></li>			
							<li class="divider"></li>
							<li><a href="#" onClick="ChangeTheme('default','<?=str_replace("/","_",$_SERVER['REQUEST_URI'])?>');"><i class="icon-heart"></i> Default</a></li>										
						</ul>					
					</li>								

								<li>
<!--								<a href='#' class='dropdown-toggle' data-toggle='dropdown' >  -->
									<a href="#"><?=$user['username']?> </a>
<!-- 									<i class='glyphicon glyphicon-chevron-down'></i>&nbsp;&nbsp;&nbsp;
								</a>
							<ul class="dropdown-menu">
								<li><a href="/users/settings"><i class="icon-gears"></i> Settings</a></li>			
								<li><a href="/ex/dashboard"><i class="icon-dashboard"></i> Dashboard</a></li>
								<li class="divider"></li>				
								<li><a href="/logout"><i class="icon-power-off"></i> Logout</a></li>
							</ul>
-->							
 						</li>
						<li><a href="/logout" class=" tooltip-x" rel="tooltip-x" data-placement="bottom" title="Logout "><i class="icon-power-off icon-large"></i></a></li>
					<?php }else{?>					
						<li><a href="/login">Signin</a></li>
						<li><a href="/signup">Signup</a></li>
					<?php }?>				
					
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

