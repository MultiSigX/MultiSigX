<?php
?>
<?php $this->form->config(array( 'templates' => array('error' => '<div style="font-size:10px;background-color:#fcf8e3;padding:1px;">{:content}</div>'))); 
?>
<div class="row container-fluid">
	<div class="col-md-6" >
		<div class="panel panel-primary">
			<div class="panel-heading">Signup / Register</div>
			<div class="panel-body">
		<?=$this->form->create($Users,array('class'=>'form-group has-error')); ?>
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk" id="FirstNameIcon"></i>
					</span>
		<?=$this->form->field('firstname', array('label'=>'','placeholder'=>'First Name', 'class'=>'form-control','onkeyup'=>'CheckFirstName(this.value);' )); ?>
				</div>
			</div>				
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk" id="LastNameIcon"></i>
					</span>
		<?=$this->form->field('lastname', array('label'=>'','placeholder'=>'Last Name', 'class'=>'form-control','onkeyup'=>'CheckLastName(this.value);' )); ?>
				</div>
			</div>				
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk" id="UserNameIcon"></i>
					</span>
		<?=$this->form->field('username', array('label'=>'','placeholder'=>'username', 'class'=>'form-control','onkeyup'=>'CheckUserName(this.value);' )); ?>
				</div>
		<p class="label label-danger">Only characters and numbers, NO SPACES</p>				
			</div>				
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk" id="EmailIcon"></i>
					</span>

		<?=$this->form->field('email', array('label'=>'','placeholder'=>'name@youremail.com', 'class'=>'form-control','onkeyup'=>'CheckEmail(this.value);'  )); ?>
				</div>
			</div>				
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk" id="PasswordIcon"></i>
					</span>
		<?=$this->form->field('password', array('type' => 'password', 'label'=>'','placeholder'=>'Password', 'class'=>'form-control','onkeyup'=>'CheckPassword(this.value);' )); ?>
				</div>
			</div>				
			<div class="form-group has-error">			
				<div class="input-group">
					<span class="input-group-addon">
						<i class="glyphicon glyphicon-asterisk" id="Password2Icon"></i>
					</span>
		<?=$this->form->field('password2', array('type' => 'password', 'label'=>'','placeholder'=>'same as above', 'class'=>'form-control','onkeyup'=>'CheckPassword(this.value);' )); ?>
				</div>
			</div>				
		<?php // echo $this->recaptcha->challenge();?>
		<?=$this->form->submit('Sign up' ,array('class'=>'btn btn-primary btn-block')); ?>
		<?=$this->form->end(); ?>
			</div>
		</div>


	</div>
	<div class="col-md-6" >
		<div class="panel panel-primary">
			<div class="panel-heading">MultiSigX&#8482; Features</div>
			<div class="panel-body">
			<h3>Individuals<sup>*</sup> - NOMINAL 0.001% commission</h3>
			<ul>
				<li>Self, Self, Print</li>
				<li>Self, 2<sup>nd</sup> email, Print</li>				
				<li>Self, Web Wallet, Print</li>				
				<li>Self, 2<sup>nd</sup> email, Web Wallet</li>								
			</ul>
			<h3>Groups<sup>*</sup> - 1% commission on withdrawal</h3>
			<ul>
				<li>Self, Friend/Colleague, Print</li>
				<li>Self, Friend/Colleague, Friend/Colleague</li>				
				<li>Self, Friend/Colleague, Web Wallet</li>								
				<li>Self, Family, Friend/Colleague</li>								
			</ul>
			<h3>Business<sup>*</sup> -  2% commission on withdrawal</h3>
			<ul>
				<li>Self Business, Business, Web Wallet</li>
				<li>Self Business, Partner, Web Wallet</li>				
				<li>Self Business, Partner, Partner</li>				
			</ul>
			</div>
			<div class="panel-footer"><sup>*</sup><strong>2 of 3</strong> or <strong>3 of 3</strong> sign in required for security.</div>
		</div>
	</div>
</div>