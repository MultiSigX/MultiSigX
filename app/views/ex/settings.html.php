<?php
use li3_qrcode\extensions\action\QRcode;
use app\models\Countries;
?>
<div class="col-md-12  container-fluid" >
	<div class="panel panel-primary">
		<div class="panel-heading">Settings</div>
		<div class="panel-body">
		<div class="panel panel-primary">
				<div class="panel-heading">Referral Program</div>
				<div class="panel-body">
				<h3>Refer link:</h3>
				<blockquote>
				By referring friends you can reduce the commission percent.
				<table class="table table-condensed table-hover" style="text-align:center">
					<tr>
						<th width="25%">#</th>
						<th width="25%" style="text-align:center">Level 1</th>
						<th width="25%" style="text-align:center">Level 2 above</th>
					</tr>
				<?php foreach ($commissions as $commission){?>
					<tr>
						<th><?=$commission['min']?> to <?=$commission['max']?></th>
						<td><?=$commission['Level'][0]?></td>
						<td><?=$commission['Level'][1]?></td>
					</tr>
				<?php }?>
					<tr class="success">
						<th>Your friends</th>
						<td><?=$levelOne?></td>
						<td><?=$countChild-$levelOne?></td>
					</tr>
				</table>
				</blockquote>
				<h4>Link</h4>
				<p>https://<?=COMPANY_URL?>/signup/?r=<?=$details['bitcoinaddress']?></p>
				<a href="mailto:?&body=https://<?=COMPANY_URL?>/signup/?r=<?=$details['bitcoinaddress']?>&subject=MultiSigX Refer">Send email</a>
				</div>
		</div>		
			<div class="panel panel-primary">
				<div class="panel-heading">Personal</div>
				<div class="panel-body">
					<div class="col-md-12 ">
						<?=$this->form->create(null,array('class'=>'form-group has-error','id'=>'MSXSettingForm')); ?>
						<h4>Gender</h4>
						<input type="radio" name="Gender" id="GenderMale" <?php if($details['settings']['Gender']=="Male"){echo " checked ";}?> value="Male" /> Male
						<input type="radio" name="Gender" id="GenderFemale" <?php if($details['settings']['Gender']=="Female"){echo " checked ";}?> value="Female" /> Female<br>
						<h4>Mobile Phone</h4>
						<?php $countries = Countries::find('all',array(
						'conditions'=>array('Phone'=>array('$ne'=>'00')),
						'order'=>array('Country'=>1)
						));?>
						<select name="Country" id="Country" class="form-control" onchange="changeFlag(this.value);">
						<?php foreach($countries as $country){?>
							<option value="<?=$country['ISO']?>" 
							<?php if($details['settings']['Country']==$country['ISO']){echo " selected ";}?>><?=$country['Country']?></option>
						<?php }?>
						</select><br>
						<span><img src="/img/Flags/<?=strtolower($details['settings']['Country'])?>.gif" width="100" height="60" id="ImageFlag" style="border:1px solid black;padding:2px"></span><br><br>
						<input type="tel" name="mobile" id="mobile" placeholder="9999999999" value="<?=$details['settings']['mobile']?>" class="form-control" /><br>
						<input type="submit" id="SubmitButton" class="btn btn-primary" value="Save" onClick='$("#SubmitButton").attr("disabled", "disabled");'><br>
					<?=$this->form->end(); ?>				
				</div>
			</div>
		</div>
					
		<div class="panel panel-primary">
				<div class="panel-heading">Validate Mobile</div>
				<div class="panel-body">			
					<div class="col-md-12 ">
					<?php if($details['SMSVerified']=="Yes"){?>
					<button href="#" class="btn btn-primary" >Mobile verified</button><br><br>
					<?php }else{?>
					<button href="#" class="btn btn-primary" onclick="sendSMS();" id="SMSSending">Send SMS to mobile</button><br><br>
					<label class="control-label" for="wallet-google-conf-code">Validate Mobile</label><br>
						<div class="alert alert-success hidden" id="SuccessTOTP">Mobile Validated</div>
						<div class="input-group" id="EnterMobileCode">
							<input class="form-control" required="required" maxlength="6" pattern="[0-9]{6}" id="CheckMobileCode" placeholder="Mobile Code" type="password">
								<span class="input-group-btn">
									<button type="button" class="btn btn-primary" onClick="CheckMobile();">Check Mobile</button>
								</span>
						</div><br>
					<?php }?>
					
					
						<span id="ErrorMobile" class="alert alert-danger hidden">Mobile Code not correct</span>
					</div>
				</div>
			</div>
		
		
		<div class="panel panel-primary">
				<div class="panel-heading">Upload Picture</div>
				<div class="panel-body">			
					<div class="col-md-12 ">
					<?=$this->form->create(null,array('url'=>'/ex/savepicture','class'=>'form-group has-error','id'=>'MSXPictureSettingForm','type'=>'file')); ?>					
					<label for="Picture">Your picture</label>
						<input id="Picture" name="Picture" type="file" />
						<p class="help-block">Your picture should be jpg, png, gif with max size 300x300 px</p>
						<?php	
						$filename = "imagename_address";
						if($$filename!=""){?>
							<img src="/documents/<?=$$filename?>" width="300px" style="padding:1px;border:1px solid black">
						<?php }?>						
					<br><br>
						<input type="submit" id="PictureSubmitButton" class="btn btn-primary" value="Save" onClick='$("#PictureSubmitButton").attr("disabled", "disabled");'><br>					
					<?=$this->form->end(); ?>				
					</div>
				</div>
			</div>
			<div class="panel panel-danger">
				<div class="panel-heading">Security API</div>
				<div class="panel-body">
				Key: <code><?=$details['key']?></code> use this key to access the API.
				</div>
			</div>
		
			<div class="panel panel-danger">
				<div class="panel-heading">Security</div>
				<div class="panel-body">
			<h4>Google TOTP<br>(Time based One Time Password)</h4>
			<ol>
				<li><strong>Install app:</strong>
    Install the app on your device. For Android at <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Google play</a> while for iOS at <a href="https://itunes.apple.com/en/app/google-authenticator/id388497605?mt=8" target="_blank">Apple Store</a>. 
				</li>
				<li><strong>Add account:</strong>
    Add an account to the Google Authenticator App and scan the QR code or manually enter exact code.</li>
				<li><strong>Confirm code:</strong>
    Google Authenticator will now show a number which in order to complete the two factor setup you need to enter below. </li>
				</ol>
				<p>The QR code for Google Authenticator access to your wallet is:</p>
					<img src="<?=$qrCodeUrl?>" scrolling="no" height="200px" /><br>
					<p>You can also copy the code manually:</p>
					<?=$details['secret']?>
					
					<form role="form" class="form-horizontal ">
						<div class="form-group col-xs-12">
							<div class="col-md-4 ">
							<label class="control-label" for="wallet-google-conf-code">Authenticator code</label><br>
								<div class="alert alert-success hidden" id="SuccessTOTP">TOTP Authenticated</div>
								<div class="input-group" id="EnterTOTP">
									<input class="form-control" required="required" maxlength="6" pattern="[0-9]{6}" id="CheckCode" placeholder="Please enter received code" type="password">
										<span class="input-group-btn">
											<button type="button" class="btn btn-primary" onClick="CheckTOTP();">Enable TOTP</button>
										</span>
								</div><br>
								<span id="ErrorTOTP" class="alert alert-danger hidden">TOTP not correct</span>
							</div>
						</div>
					</form>
				</div>
			</div>
		
					
			<div class="panel panel-danger">
				<div class="panel-heading">Password</div>
				<div class="panel-body">
			
			<h4>Change Password</h4>
					<?=$this->form->create("",array('url'=>'/ex/password/','role'=>'form','class'=>'form-horizontal')); ?>
					<div class="col-md-4 ">
					<?=$this->form->field('oldpassword', array('type' => 'password', 'label'=>'Old Password','placeholder'=>'Password','class'=>'form-control')); ?>					
					<?=$this->form->field('password', array('type' => 'password', 'label'=>'New Password','placeholder'=>'Password','class'=>'form-control' )); ?>
					<?=$this->form->field('password2', array('type' => 'password', 'label'=>'Repeat new password','placeholder'=>'same as above','class'=>'form-control' )); ?>
					<?=$this->form->hidden('key', array('value'=>$details['key']))?><br>
					<?=$this->form->submit('Change' ,array('class'=>'btn btn-primary')); ?>					
					</div>
					<?=$this->form->end(); ?>
			</div>
			</div>														
		</div>
		
		<div class="panel-footer">
		</div>
	</div>
</div>
