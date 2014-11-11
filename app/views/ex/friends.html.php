<div class="row container-fluid">
	<?php echo $this->_render('element', 'sidebar-menu');?>	
	<div class="col-md-9  container-fluid" >
		<div class="panel panel-primary">
			<div class="panel-heading">Friends</div>
			<div class="panel-body" style="text-align:center">
			
			<?php foreach($emails as $email){?>
				<button ><?=$email?></button>
			<?php }?>
			</div>
		</div>
	</div>
</div>