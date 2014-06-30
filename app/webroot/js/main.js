//JS Document

function SendPassword(){
	$.getJSON('/Users/SendPassword/'+$("#Username").val(),
		function(ReturnValues){
			if(ReturnValues['Password']=="Password Not sent"){
				$("#UserNameIcon").attr("class", "glyphicon glyphicon-remove");
				$("#LoginEmailPassword").hide();
				return false;
			}
			$("#LoginEmailPassword").show();
			$("#LoginButton").removeAttr('disabled');			
			$("#UserNameIcon").attr("class", "glyphicon glyphicon-ok");
			
			if(ReturnValues['TOTP']=="Yes"){
				$("#TOTPPassword").show();
			}else{
				$("#TOTPPassword").hide();
			}
		}
	);
}


function CheckFirstName(value){
	if(value.length>=2){
		$("#FirstNameIcon").attr("class", "glyphicon glyphicon-ok");	
	}else{
		$("#FirstNameIcon").attr("class", "glyphicon glyphicon-remove");			
	}
}
function CheckLastName(value){
	if(value.length>=2){
		$("#LastNameIcon").attr("class", "glyphicon glyphicon-ok");	
	}else{
		$("#LastNameIcon").attr("class", "glyphicon glyphicon-remove");			
	}
}
function CheckUserName(value){
	if(value.length>6){
		$.getJSON('/Users/username/'+value,
		function(ReturnValues){
			if(ReturnValues['Available']=='Yes'){
				$("#UserNameIcon").attr("class", "glyphicon glyphicon-ok");	
			}else{
				$("#UserNameIcon").attr("class", "glyphicon glyphicon-remove");							
			}
		});
	}else{
		$("#UserNameIcon").attr("class", "glyphicon glyphicon-remove");			
	}
}
function CheckUserNameLogin(value){
	if(value.length>6){
		$.getJSON('/Users/username/'+value,
		function(ReturnValues){
			if(ReturnValues['Available']=='Yes'){
				$("#UserNameIcon").attr("class", "glyphicon glyphicon-remove");					
			}else{
				$("#UserNameIcon").attr("class", "glyphicon glyphicon-ok");	
			}
		});
	}else{
		$("#UserNameIcon").attr("class", "glyphicon glyphicon-remove");			
	}
}

function CheckEmail(email){
	email = email.toLowerCase();
	$("#Email").val(email);	
	if(validateEmail(email)){
		$.getJSON('/Users/signupemail/'+email,
			function(ReturnValues){
			if(ReturnValues['Available']=='Yes'){
				$("#EmailIcon").attr("class", "glyphicon glyphicon-ok");					

			}else{
				$("#EmailIcon").attr("class", "glyphicon glyphicon-remove");
			}
		});							
	}else{
		$("#EmailIcon").attr("class", "glyphicon glyphicon-remove");						
	}
}
function CheckPassword(value){
	if(value.length>6){
		if($("#Password").val()==$("#Password2").val()){
			$("#PasswordIcon").attr("class", "glyphicon glyphicon-ok");			
			$("#Password2Icon").attr("class", "glyphicon glyphicon-ok");					
		}else{
			$("#PasswordIcon").attr("class", "glyphicon glyphicon-remove");					
			$("#Password2Icon").attr("class", "glyphicon glyphicon-remove");							
		}
	}else{
		$("#PasswordIcon").attr("class", "glyphicon glyphicon-remove");					
		$("#Password2Icon").attr("class", "glyphicon glyphicon-remove");							
	}
}

function PasstoPhrase(){
	value1 = $("#Passphrase1").val();
	value2 = $("#Passphrase2").val()	
	value3 = $("#Passphrase3").val()	
	email1 = $('#Email1').val();
	email2 = $('#Email2').val();	
	email3 = $('#Email3').val();	
	username = $('#Username').val();	
	key = $('#Key').val();
	secret = $('#secret').val();
	for(i=1;i<=3;i++){
		var address = "#Address"+i;
		var private = "#Private"+i;
		var pubkeycompress = "#Pubkeycompress"+i;
		var value = 'value'+i;
		var email = 'email'+i;		
		keys = GenerateKeys(i,eval(value),eval(email));
		var this_priv = keys.privkey.toString();
		var this_public = keys.pubkey.toString();
		var this_pubkeycompress = keys.pubkeycompress.toString();		
		$(address).val(this_public);
		$(private).val(this_priv);
		$(pubkeycompress).val(this_pubkeycompress);		
	}
}

function GenerateKeys(j,value,email){
	var	dest = "#Dest"+j;
	bytes = strToBytes(value);
  text = mn_encode(Crypto.util.bytesToHex(bytes));
	$(dest).val(text);
	bytes = Crypto.util.hexToBytes(mn_decode(text.trim()));
  text = bytesToString(bytes);	
	//Create Bitcoin address.
	var keys = btc.keys(Crypto.SHA256(email+value+secret+key+Crypto.SHA256(username+key+secret)));
//		alert(keys);
	return keys;
	}

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 

