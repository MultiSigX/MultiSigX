<div class="col-md-12  container-fluid" >
	<div class="panel panel-primary">
		<div class="panel-heading">Settings</div>
		<div class="panel-body" style="text-align:center">
			<?=$this->form->create(null,array('class'=>'form-group has-error','id'=>'MSXSettingForm')); ?>
			<h4>Gender</h4>
			<input type="radio" name="Gender" id="GenderMale" checked  value="Male"> Male
			<input type="radio" name="Gender" id="GenderFemale" value="Female"> Female<br>
			<h4>Alternate email</h4>
			<?=$this->form->field('email', array('label'=>'','type'=>'email','value'=>$detail['settings']['email'],'class'=>'form-control')); ?>
			<input type="submit" id="SubmitButton" class="btn btn-primary" value="Save" onClick='$("#SubmitButton").attr("disabled", "disabled");'><br>
			<?=$this->form->end(); ?>
		</div>
		<div class="panel-footer">
		</div>
	</div>
</div>
