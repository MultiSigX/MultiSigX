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
	checkform();
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

function DepositCoins(name,address){
$("#DepositAddress").html(address);
$("#DepositModalLabel").html(name + ' Address');
	$.getJSON('/Users/CreateQRCode/'+address,
		function(ReturnValues){
			$("#DepositAddressImg").attr("src","/qrcode/out/"+address+".png");
		}
	);

}
function CheckBalance(name,currency,address){
	$("#CheckBalanceAddress").html(address);
	$("#CheckModalLabel").html(name+" transactions and balance");
	$.getJSON('/Users/CheckBalance/'+address+'/'+name,
		function(ReturnValues){
			$("#CheckBalanceHTML").html(ReturnValues['html']);
		}
	);	
}

function checkform() {
	var count = 0;
	var op = document.getElementById("Currency").getElementsByTagName("option");
	for (var i = 0; i < op.length; i++) {	
		if(op[i].disabled == true){
			count++;
		}
	}
	if($("#CoinName").val()==""){document.getElementById('SubmitButton').disabled = true;return false}
	document.getElementById('SubmitButton').disabled = true;
	if(count==2){
		document.getElementById('SubmitButton').disabled = true;
	}else{
		if($("#Email2").val()!="" && $("#Email3").val()!="" && validateEmail($("#Email2").val()) && validateEmail($("#Email3").val())){
			document.getElementById('SubmitButton').disabled = false;		
		}else{
			document.getElementById('SubmitButton').disabled = true;			
		}
	}
}
function ChangeRelationEmail(name,value,default_escrow){
	if(value=="MultiSigX - Escrow")	{
		$("#"+name).val(default_escrow);
		$("#"+name).prop('readonly', 'readonly');
	}else{
		$("#"+name).prop('readonly', false);		
	}
}

function ChangeTheme(name,uri){
	$.getJSON('/Users/ChangeTheme/'+name+'/'+uri,
	function(ReturnValues){
		window.location.assign (ReturnValues['uri']);
	});	
}

function CreateTrans(amount, commission, txfee){

//alert(amount);
//alert(commission);
//alert(txfee);
// $Amount = (float)$final_balance-$currencies['txFee']-($final_balance*$commission/100);
SendTxFee = Math.round((amount - $("#SendAmount0").val() - $("#SendAmount1").val() - $("#SendAmount2").val() - (parseFloat(amount)*parseFloat(commission)/100))*1000000)/1000000;
if(SendTxFee<=0){
	if($("#SendAmount2").val()==0){
		amount1 = Math.round((amount - $("#SendAmount0").val() - (parseFloat(amount)*parseFloat(commission)/100))*1000000)/1000000;
		$("#SendAmount1").val(amount1-SendTxFee);
	}
return false;
}
$("#SendTxFee").html(SendTxFee);
$("#SendTrxFee").val(SendTxFee);
	if($("#SendToAddress0").val()!="" && $("#SendToAddress0").val().length >= 34){
		$("#CreateSubmit").removeAttr('disabled');			
	}else{
		document.getElementById('CreateSubmit').disabled = true;
	}
	if($("#SendToAddress0").val()!=""){
		$("#Error0").html("Enter correct address and amount!");
	}else{
		$("#Error0").html("");
	}
	if($("#SendToAddress1").val()!=""){
		$("#Error1").html("Enter correct address and amount!");
	}else{
		$("#Error1").html("");
	}
	if($("#SendToAddress2").val()!=""){
		$("#Error2").html("Enter correct address and amount!");
	}else{
		$("#Error0").html("");
	}

	if($("#SendAmount0").val()<0){
		$("#Error0").html("Check Amount!");
	}else{
		$("#Error0").html("");
	}
	if($("#SendAmount1").val()<0){
		$("#Error1").html("Check Amount!");
	}else{
		$("#Error1").html("");
	}
	if($("#SendAmount2").val()<0){
		$("#Error2").html("Check Amount!");
	}else{
		$("#Error2").html("");
	}
	
	if($("#SendAmount0").val()>0){
		if($("#SendToAddress0").val()!="" && $("#SendToAddress0").val().length >= 34){
			$("#CreateSubmit").removeAttr('disabled');			
		}else{
			$("#Error0").html("Enter correct address and amount!");
			document.getElementById('CreateSubmit').disabled = true;
		}
		$("#Error0").html("Enter correct address and amount!");
	}else{
		$("#Error0").html("");
	}
	if($("#SendAmount1").val()>0){
		if($("#SendToAddress1").val()!="" && $("#SendToAddress0").val().length >= 34){
			$("#CreateSubmit").removeAttr('disabled');			
		}else{
			$("#Error1").html("Enter correct address and amount!");
			document.getElementById('CreateSubmit').disabled = true;
		}
		$("#Error1").html("Enter correct address and amount!");
	}else{
		$("#Error1").html("");
	}
	if($("#SendAmount2").val()>0){
		if($("#SendToAddress2").val()!="" && $("#SendToAddress0").val().length >= 34){
			$("#CreateSubmit").removeAttr('disabled');			
		}else{
			$("#Error2").html("Enter correct address and amount!");
			document.getElementById('CreateSubmit').disabled = true;
		}

		$("#Error2").html("Enter correct address and amount!");
	}else{
		$("#Error2").html("");
	}	
	
	
}

function CheckTotal(amount,commission){
	Total = parseFloat($("#SendAmount0").val()) + parseFloat($("#SendAmount1").val()) + parseFloat($("#SendAmount2").val()) + (parseFloat(amount)*parseFloat(commission)/100) + parseFloat($("#SendTxFee").html());
	
	if(Math.round(Total*1000000)/1000000==Math.round(amount*1000000)/1000000){return true;}else{
		$("#CreateAlert").html("<b>Totals do not match! Please recheck!</b>");
	return false;}
}

function SignTrans(){
	if($("#PrivKey").val()!="" && $("#PrivKey").val().length >= 34){
		$("#SignSubmit").removeAttr('disabled');			
	}else{
		document.getElementById('SignSubmit').disabled = true;				
	}

}
function ConfirmTrans(){
	if($("#ConfirmPrivKey").val()!="" && $("#ConfirmPrivKey").val().length >= 34){
		$("#ConfirmSubmit").removeAttr('disabled');			
	}else{
		document.getElementById('ConfirmSubmit').disabled = true;				
	}

}

function CheckTOTP(){
	if($("#CheckCode").val()==""){return false;}
	$.getJSON('/Users/CheckTOTP/',{
			  CheckCode:$("#CheckCode").val()
			  },
		function(ReturnValues){
			if(ReturnValues['value']==false){
				$("#ErrorTOTP").attr('class','alert alert-danger');
				//window.location.assign("/ex/settings");		
			}
			if(ReturnValues['value']==true){
				$("#SuccessTOTP").attr('class','alert alert-success');
				$("#EnterTOTP").attr('class','hidden');
				$("#ErrorTOTP").attr('class','hidden');
			}
		}
	);
}

function changeFlag(flag){
	flagname = flag.toLowerCase();
	flagname = "/img/Flags/" + flagname + ".gif";
	$("#ImageFlag").attr("src",flagname);
}

function sendSMS(){
	$("#SMSSending").html("SMS sending...");
	$("#SMSSending").attr("disabled", "disabled");
	$.getJSON('/Users/SendSMS/',{},
		function(ReturnValues){
			$("#SMSSending").html("SMS sent!");		
		});
}

function CheckMobile(){
	SMSCode = $("#CheckMobileCode").val();
	$.getJSON('/Users/CheckSMS/',{
		CheckCode:SMSCode
	},
		function(ReturnValues){
		if(ReturnValues['msg']=="Confirmed"){
			$("#EnterMobileCode").hide();
			$("#ConfirmMobileCode").show();
		}
		});

}

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 

function DeleteCoin(address){
	$.getJSON('/Users/DeleteCoin/'+address,
	function(ReturnValues){
		window.location.assign (ReturnValues['uri']);
	});	

}
