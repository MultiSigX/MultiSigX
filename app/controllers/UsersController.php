<?php
namespace app\controllers;

use app\extensions\action\OAuth2;
use app\models\Pages;
use app\models\Users;
use app\models\Details;
use app\models\Addresses;
use app\models\Parameters;
use app\models\Settings;
use app\models\Greenblocks;
use app\models\Bitblocks;
use app\models\File;
use lithium\data\Connections;
use app\extensions\action\Functions;
use app\extensions\action\Bitcoin;
use app\extensions\action\Litecoin;
use app\extensions\action\Greencoin;
use lithium\security\Auth;
use lithium\storage\Session;
use app\extensions\action\GoogleAuthenticator;
use li3_qrcode\extensions\action\QRcode;
use lithium\util\String;
use MongoID;

use \lithium\template\View;
use \Swift_MailTransport;
use \Swift_Mailer;
use \Swift_Message;
use \Swift_Attachment;

class UsersController extends \lithium\action\Controller {

	public function index(){
	}
	public function signup() {	
		if($this->request->data) {	
      $Users = Users::create($this->request->data);
      $saved = $Users->save();
			if($saved==true){
			$verification = sha1($Users->_id);

			$bitcoin = new Bitcoin('http://'.BITCOIN_WALLET_SERVER.':'.BITCOIN_WALLET_PORT,BITCOIN_WALLET_USERNAME,BITCOIN_WALLET_PASSWORD);
			$bitcoinaddress = $bitcoin->getaccountaddress("MSX-".$this->request->data['username']);

			
//			$oauth = new OAuth2();
//			$key_secret = $oauth->request_token();
			$ga = new GoogleAuthenticator();
			
			$data = array(
				'user_id'=>(string)$Users->_id,
				'username'=>(string)$Users->username,
				'email.verify' => $verification,
				'mobile.verified' => "No",				
				'mobile.number' => "",								
				'key'=>$ga->createSecret(64),
				'secret'=>$ga->createSecret(64),
				'Friend'=>array(),
				'balance.BTC' => (float)0,
				'balance.LTC' => (float)0,				
				'balance.XGC' => (float)0,								
				'balance.USD' => (float)0,				
				'balance.EUR' => (float)0,
				'balance.GBP' => (float)0,				
			);
			Details::create()->save($data);

			$email = $this->request->data['email'];
			$name = $this->request->data['firstname'];
			
			$view  = new View(array(
				'loader' => 'File',
				'renderer' => 'File',
				'paths' => array(
					'template' => '{:library}/views/{:controller}/{:template}.{:type}.php'
				)
			));
			$body = $view->render(
				'template',
				compact('email','verification','name'),
				array(
					'controller' => 'users',
					'template'=>'confirm',
					'type' => 'mail',
					'layout' => false
				)
			);

			$transport = Swift_MailTransport::newInstance();
			$mailer = Swift_Mailer::newInstance($transport);
	
			$message = Swift_Message::newInstance();
			$message->setSubject("Verification of email from ".COMPANY_URL);
			$message->setFrom(array(NOREPLY => 'Verification email '.COMPANY_URL));
			$message->setTo($Users->email);
			$message->addBcc(MAIL_1);
			$message->addBcc(MAIL_2);			
			$message->addBcc(MAIL_3);		

			$message->setBody($body,'text/html');
			
			$mailer->send($message);
			$this->redirect('Users::email');	
			
			}
		}	
		
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		return compact('title','keywords','description','saved','Users');	

	}
	
		public function email(){
		$user = Session::read('member');
		$id = $user['_id'];
		$details = Details::find('first',
			array('conditions'=>array('user_id'=>$id))
		);

		if(isset($details['email']['verified'])){
			$msg = "Your email is verified.";
		}else{
			$msg = "Your email is <strong>not</strong> verified. Please check your email to verify.";
			
		}
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		
		return compact('title','keywords','description','msg');	
	}

	public function confirm($email=null,$verify=null){
		if($email == "" || $verify==""){
			if($this->request->data){
				if($this->request->data['email']=="" || $this->request->data['verified']==""){
					return $this->redirect('Users::email');
				}
				$email = $this->request->data['email'];
				$verify = $this->request->data['verified'];
			}else{return $this->redirect('Users::email');}
		}
		$finduser = Users::first(array(
			'conditions'=>array(
				'email' => $email,
			)
		));

		$id = (string) $finduser['_id'];
			if($id!=null){
				$data = array('email.verified'=>'Yes');
				Details::create();
				$details = Details::find('all',array(
					'conditions'=>array('user_id'=>$id,'email.verify'=>$verify)
				))->save($data);

				if(empty($details)==1){
					return $this->redirect('Users::email');
				}else{
					return $this->redirect('ex::dashboard');
				}
			}else{return $this->redirect('Users::email');}

	}

	public function SendPassword($username=""){
		$users = Users::find('first',array(
					'conditions'=>array('username'=>$username)
				));
		$id = (string)$users['_id'];
		if($id==""){
			return $this->render(array('json' => array("Password"=>"Password Not sent","TOTP"=>"No")));
		}
		
		$ga = new GoogleAuthenticator();
		$secret = $ga->createSecret(64);
		$details = Details::find('first',array(
					'conditions'=>array('username'=>$username,'user_id'=>(string)$id)
		));
		if($details['oneCodeused']=='Yes' || $details['oneCodeused']==""){
			$oneCode = $ga->getCode($secret);	
			$data = array(
				'oneCode' => $oneCode,
				'oneCodeused' => 'No'
			);
			$details = Details::find('all',array(
						'conditions'=>array('username'=>$username,'user_id'=>(string)$id)
			))->save($data);
		}
		$details = Details::find('first',array(
					'conditions'=>array('username'=>$username,'user_id'=>(string)$id)
		));
		$oneCode = $details['oneCode'];
		$totp = "No";

		if($details['TOTP.Validate']==true && $details['TOTP.Login']==true){
			$totp = "Yes";
		}
		
		$view  = new View(array(
			'loader' => 'File',
			'renderer' => 'File',
			'paths' => array(
				'template' => '{:library}/views/{:controller}/{:template}.{:type}.php'
			)
		));
		$email = $users['email'];
			$body = $view->render(
				'template',
				compact('users','oneCode','username'),
				array(
					'controller' => 'users',
					'template'=>'onecode',
					'type' => 'mail',
					'layout' => false
				)
			);

			$transport = Swift_MailTransport::newInstance();
			$mailer = Swift_Mailer::newInstance($transport);
	
			$message = Swift_Message::newInstance();
			$message->setSubject("Sign in password for ".COMPANY_URL);
			$message->setFrom(array(NOREPLY => 'Sign in password from '.COMPANY_URL));
			$message->setTo($email);
			$message->setBody($body,'text/html');
			$mailer->send($message);
			return $this->render(array('json' => array("Password"=>"Password sent to email","TOTP"=>$totp)));
	}

	public function username($username=null){
		$usercount = Users::find('all',array(
			'conditions'=>array('username'=>$username)
		));
		if(count($usercount)==0){
			$Available = 'Yes';
		}else{
			$Available = 'No';
		}
			return $this->render(array('json' => array(
			'Available'=> $Available,
		)));
	}
	public function signupemail($email=null){
		$usercount = Users::find('all',array(
			'conditions'=>array('email'=>$email)
		));
		if(count($usercount)==0){
			$Available = 'Yes';
		}else{
			$Available = 'No';
		}
			return $this->render(array('json' => array(
			'Available'=> $Available,
		)));
	}
	public function forgotpassword(){
		if($this->request->data){
			$user = Users::find('first',array(
				'conditions' => array(
					'email' => $this->request->data['email']
				),
				'fields' => array('_id')
			));
			$email = $user['email'];
//		print_r($user['_id']);
			$details = Details::find('first', array(
				'conditions' => array(
					'user_id' => (string)$user['_id']
				),
				'fields' => array('key')
			));
//					print_r($details['key']);exit;
		$key = $details['key'];
		if($key!=""){
		$email = $this->request->data['email'];
			$view  = new View(array(
				'loader' => 'File',
				'renderer' => 'File',
				'paths' => array(
					'template' => '{:library}/views/{:controller}/{:template}.{:type}.php'
				)
			));
			$body = $view->render(
				'template',
				compact('email','key'),
				array(
					'controller' => 'users',
					'template'=>'forgot',
					'type' => 'mail',
					'layout' => false
				)
			);

			$transport = Swift_MailTransport::newInstance();
			$mailer = Swift_Mailer::newInstance($transport);
	
			$message = Swift_Message::newInstance();
			$message->setSubject("Password reset link from ".COMPANY_URL);
			$message->setFrom(array(NOREPLY => 'Password reset email '.COMPANY_URL));
			$message->setTo($email);
			$message->addBcc(MAIL_1);
			$message->addBcc(MAIL_2);			
			$message->addBcc(MAIL_3);		

			$message->setBody($body,'text/html');
			$mailer->send($message);
			}
		}
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		return compact('title','keywords','description');	

	}
	public function CreateQRCode($address=null){
		$qrcode = new QRcode();			
		$qrcode->png($address, QR_OUTPUT_DIR.$address.".png", 'H', 7, 2);		
		return $this->render(array('json' => array("QRCode"=>$address)));
	}
	
	public function CheckBalance($address=null,$name=null,$local=null){
	if ($name==""){return $this->render(array('json' => array("CheckBalance"=>false)));}
//	$address = '1Czx5pXiQ2Qk4hFqvXvnWgnuRUKza8pdNN';
			switch ($name){
				case "Bitcoin":
				$url = "https://blockchain.info/address/".$address."?format=json";
				$currency = "BTC";
				break;

				case "GreenCoin":
				$url = "http://greencoin.io/blockchain/address/".$address."/json";
				$currency = "GreenCoin";
				break;
			}
			$opts = array(
			  'http'=> array(
					'method'=> "GET",
					'user_agent'=> "MozillaXYZ/1.0"));
			$context = stream_context_create($opts);
			$json = file_get_contents($url, false, $context);
			$jdec = json_decode($json);
			$total_rec = ($jdec->total_received/100000000);
			$total_sent = ($jdec->total_sent/100000000);
			if($local===true){
				$final = ($total_rec-$total_sent);
				return compact('final');
			}
			$n_tx = $jdec->n_tx;
			$data = array();$i=0;
			foreach($jdec->txs as $tx){
				$j=0;
				if(count($tx->inputs)>0){
					foreach($tx->inputs as $input){
						$data[$i]['out'][$j]['addr']=$input->prev_out->addr;
						$data[$i]['out'][$j]['value']=$input->prev_out->value;
						$data[$i]['out'][$j]['spent']=(int)$input->prev_out->spent;
						$data[$i]['out'][$j]['script']=$input->prev_out->script;
						$j++;
					}
				}
				$j=0;
				if(count($tx->out)>0){				
					foreach($tx->out as $out){
						$data[$i]['in'][$j]['addr']=$out->addr;
						$data[$i]['in'][$j]['value']=$out->value;					
						$data[$i]['in'][$j]['spent']=(int)$out->spent;					
						$data[$i]['in'][$j]['script']=$out->script;
						$j++;
					}				
				}
				$i++;
			}
//print_r($data)			;
			
			$html = '<table class="table table-striped table-condensed table-bordered">
<tr>
	<td>No. Transactions</td>
	<td>'.$n_tx.'</td>
</tr>
<tr>
	<td>Total Received</td>
	<td>'.$total_rec.' '.$name.'</td>
</tr>
<tr>
	<td>Final Balance</td>
	<td>'.($total_rec-$total_sent).' '.$name.'</td>
</tr>
</table>
<table class="table table-striped table-condensed table-bordered" style="font-size:13px">';
foreach($data as $tx){
	$html = $html . "<tr>";
	$html = $html . "<td>";
	if(count($tx['out'])>0){
		foreach($tx['out'] as $t){
			if($t['addr']==$address){
				$html = $html . '<strong>'.$t['addr'].'</strong>';
			}else{
				$html = $html . $t['addr'];	
			}
			$html = $html . "</br>";		
			$html = $html . $t['value'];
			$html = $html . "</br>";		
		}
	}
	$html = $html . "</td>";
	$html = $html . "<td><code>";
	foreach($tx['in'] as $t){	
		if($t['addr']==$address){
			$html = $html . '<strong>'.$t['addr'].'</strong>';
		}else{
			$html = $html . $t['addr'];	
		}
		$html = $html . "</br>";		
		$html = $html . $t['value'];
		$html = $html . "</br>";				
	}
	$html = $html . "</code></td>";

	$html = $html . "</tr>";	
}
	$html = $html .'</table>';
		return $this->render(array('json' => array("html"=>$html,"name"=>$name)));
	}
	
	public function ChangeTheme($name=null,$uri=null){
		if ($name==""){return $this->render(array('json' => array("ChangeTheme"=>false)));}	
		$user = Session::read('default');
		$data = array("theme"=>$name);
		
		$conditions = array("user_id"=>$user["_id"]);
		
		$details = Details::update($data,$conditions);
		
		$uri = str_replace("_","/",$uri);
		return $this->render(array('json' => array("uri"=>$uri)));	
	}
	public function Reviews(){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];

		$reviews = Details::find('all',array(
			'fields'=>array('review','username','user_id'),
			'conditions'=>array('review.title'=>array('$gt'=>'')),
			'order'=>array('review.datetime.date'=>'DESC','review.datetime.time'=>'DESC'),
			'limit'=>$limit
		));
		$mongodb = Connections::get('default')->connection;
		$point = Details::connection()->connection->command(array(
			'aggregate' => 'details',
			'pipeline' => array( 
				array( '$project' => array(
					'_id'=>0,
					'point' => '$review.points.point',
					'user_id'=>'$user_id',
					'username'=>'$username'							
				)),
				array('$group' => array( '_id' => array(
						'user_id'=>'$user_id',
						'username'=>'$username',
						),
					'point' => array('$sum' => '$point'),  
				)),
			)
		));
		$average = Details::connection()->connection->command(array(
			'aggregate' => 'details',
			'pipeline' => array( 
				array( '$project' => array(
					'_id'=>0,
					'point' => '$review.points.point',
					'user_id'=>'$user_id',
					'username'=>'$username'							
				)),
				array('$group' => array( '_id' => array(
						'user_id'=>'$user_id',
						'username'=>'$username',
						),
					'point' => array('$avg' => '$point'),  
				)),
			)
		));				
		return compact('title','keywords','description','reviews','point','average');	

	}
	public function review(){
		$user = Session::read('default');
		if ($user==""){	return $this->redirect('Users::signup');}

		if($this->request->data){
			Details::find('all',array(
				'conditions'=>array('user_id'=>$user['_id'])
			))->save($this->request->data);
			return $this->redirect('Users::reviews');
		}

		$reviews = Details::find('all',array(
			'fields'=>array('review','username'),
			'conditions'=>array('review.title'=>array('$gt'=>'')),
			'order'=>array('review.datetime.date'=>'DESC'),
			'limit'=>2
		));
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];

		return compact('title','keywords','description','reviews');	
	}
	public function DeleteCoin($address=null){
		$user = Session::read('default');
		if ($user==""){	return $this->redirect('Users::signup');}
		if($address!=""){
			$conditions = array('msxRedeemScript.address'=>$address);
			$address= Addresses::remove($conditions);
		}
		$uri = "/ex/dashboard";
		return $this->render(array('json' => array("Deleted"=>"Yes","uri"=>$uri)));	
	}
	public function createTrans(){
		$user = Session::read('default');
		if ($user==""){	return $this->redirect('Users::signup');}
		
		if($this->request->data){
			$multiAddress = $this->request->data['address'];
			
			$addresses = Addresses::find('first',array(
				'conditions'=>array('msxRedeemScript.address'=>$multiAddress)
			));
			
			$Amount = $this->request->data['sendAmount'];
			$Address = $this->request->data['sendToAddress'];
			$TxFee = $this->request->data['sendTxFee'];
			$Commission = $this->request->data['commission'];
			$CommissionValue = $this->request->data['commissionValue'];			
			$currency = $this->request->data['currency'];
			
			switch ($currency){
				case "BTC":
				$coin = new Bitcoin('http://'.BITCOIN_WALLET_SERVER.':'.BITCOIN_WALLET_PORT,BITCOIN_WALLET_USERNAME,BITCOIN_WALLET_PASSWORD);
				$currencyName = "Bitcoin";
				$transaction = Bitblocks::find("first",array(
					"conditions"=>array("txid.vout.scriptPubKey.addresses"=>$multiAddress)
				));
				$wallet_address = BITCOIN_WALLET_ADDRESS;
				break;

				case "XGC":
				$coin = new Greencoin('http://'.GREENCOIN_WALLET_SERVER.':'.GREENCOIN_WALLET_PORT,GREENCOIN_WALLET_USERNAME,GREENCOIN_WALLET_PASSWORD);
				$currencyName = "GreenCoin";
				$transaction = Greenblocks::find("first",array(
					"conditions"=>array("txid.vout.scriptPubKey.addresses"=>$multiAddress)
				));
				$wallet_address = GREENCOIN_WALLET_ADDRESS;
				break;
			}		
// 				bitcoin.createrawtransaction ([{"txid":unspent[WhichTrans]["txid"], 
//          "vout":unspent[WhichTrans]["vout"],
//          "scriptPubKey":unspent[WhichTrans]["scriptPubKey"],
//          "redeemScript":unspent[WhichTrans]["redeemScript"]}],
//					{SendAddress:HowMuch/100000000.00,ChangeAddress:Leftover/100000000.00})

			foreach($transaction['txid'] as $txid){
				foreach($txid['vout'] as $vout){
					foreach($vout['scriptPubKey']['addresses'] as $address){
						if($address == $multiAddress){
							$x_txid = $txid['txid'];
							$x_vout = $vout['n'];
							$x_value = $vout['value'];
							$x_scriptPubKey = $vout['scriptPubKey']['hex'];
						}
					}
				}
			}
			$createTrans = array(
				'txid'=>$x_txid,
				'vout'=>$x_vout,
				'scriptPubKey'=>$x_scriptPubKey,
				'redeemScript'=>$addresses['msxRedeemScript']['redeemScript'],
			);
			$createData = array(
				$Address=>round($Amount,8),
				$wallet_address=>round($CommissionValue,8)
			);
	
				$createrawtransaction = $coin->createrawtransaction(array($createTrans),$createData);
				if(is_array($createrawtransaction)){
					return compact('createrawtransaction');	
				}else{
					$data = array(
						'createTrans'=>$createrawtransaction,
						'createTran.DateTime'=>new \MongoDate(),
						'createTran.IP'=>$_SERVER['REMOTE_ADDR'],
						'createTran.username'=>$user['username'],
						'createTran.user_id'=>$user['_id'],
						'createTran.withdraw_address' => $Address,
						'createTran.withdraw_amount' => round($Amount,8),
						'createTran.commission_amount' => round($CommissionValue,8),
						'createTran.tx_fee' => $this->request->data['sendTxFee'],
					);
					$conditions = array('msxRedeemScript.address'=>$multiAddress);
					Addresses::update($data,$conditions);
					
					$addresses = Addresses::find('first',array(
						'conditions'=>array('msxRedeemScript.address'=>$multiAddress)
					));
					$email = array();
					$relation = array();
					foreach($addresses['addresses'] as $address){
						array_push($email,$address['email']);
						array_push($relation,$address['relation']);
					}
					$data = array(
						'who'=>$user,
						'currency'=>$currency,
						'currencyName'=>$currencyName,
						'multiAddress'=>$multiAddress,
						'security'=>$addresses['security'],
						'name'=>$addresses['name'],
						'CoinName'=>$addresses['CoinName'],
						'DateTime'=>$addresses['DateTime'],
						'emails'=>$email,
						'txid'=>$x_txid,
						'relation'=>$relation,
						'withdraw_address' => $Address,
						'withdraw_amount' => round($Amount,8),
						'commission_amount' => round($CommissionValue,8),
						'tx_fee' => round($this->request->data['sendTxFee'],8),
						'createTrans' => $createTrans,
						'createrawtransaction' => $createrawtransaction,
					);
					
					foreach($data['emails'] as $email){
					// sending email to the users 
					/////////////////////////////////Email//////////////////////////////////////////////////
						$function = new Functions();
						$compact = array('data'=>$data);
						// sendEmailTo($email,$compact,$controller,$template,$subject,$from,$mail1,$mail2,$mail3)
						$from = array(NOREPLY => "noreply@".COMPANY_URL);
						$email = $email;
						$attach = null;
						$function->sendEmailTo($email,$compact,'users','createTrans',"MultiSigX,com create transaction",$from,'','','',$attach);
					/////////////////////////////////Email//////////////////////////////////////////////////				
					}
				
				} // if $createrawtransaction
			}	
//			print_r($data);
		return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$multiAddress.'/1'));
	}
	
	public function signTrans(){
		$user = Session::read('default');
		if ($user==""){	return $this->redirect('Users::signup');}
		if($this->request->data){
			$multiAddress = $this->request->data['address'];
			$currency = $this->request->data['currency'];
			$privKey = $this->request->data['privKey'];
	
			$addresses = Addresses::find('first',array(
				'conditions'=>array('msxRedeemScript.address'=>$multiAddress)
			));
			
			switch ($currency){
				case "BTC":
				$coin = new Bitcoin('http://'.BITCOIN_WALLET_SERVER.':'.BITCOIN_WALLET_PORT,BITCOIN_WALLET_USERNAME,BITCOIN_WALLET_PASSWORD);
				$currencyName = "Bitcoin";
				$transaction = Bitblocks::find("first",array(
					"conditions"=>array("txid.vout.scriptPubKey.addresses"=>$multiAddress)
				));
				$wallet_address = BITCOIN_WALLET_ADDRESS;
				break;

				case "XGC":
				$coin = new Greencoin('http://'.GREENCOIN_WALLET_SERVER.':'.GREENCOIN_WALLET_PORT,GREENCOIN_WALLET_USERNAME,GREENCOIN_WALLET_PASSWORD);
				$currencyName = "GreenCoin";
				$transaction = Greenblocks::find("first",array(
					"conditions"=>array("txid.vout.scriptPubKey.addresses"=>$multiAddress)
				));
				$wallet_address = GREENCOIN_WALLET_ADDRESS;
				break;
			}		
// bitcoin.signrawtransaction (rawtransact,
//  [{"txid":unspent[WhichTrans]["txid"],
//  "vout":unspent[WhichTrans]["vout"],"scriptPubKey":unspent[WhichTrans]["scriptPubKey"],
//  "redeemScript":unspent[WhichTrans]["redeemScript"]}],
//  [multisigprivkeyone])			
			foreach($transaction['txid'] as $txid){
				foreach($txid['vout'] as $vout){
					foreach($vout['scriptPubKey']['addresses'] as $address){
						if($address == $multiAddress){
							$x_txid = $txid['txid'];
							$x_vout = $vout['n'];
							$x_value = $vout['value'];
							$x_scriptPubKey = $vout['scriptPubKey']['hex'];
						}
					}
				}
			}
			$signTrans = array(
				'txid'=>$x_txid,
				'vout'=>$x_vout,
				'scriptPubKey'=>$x_scriptPubKey,
				'redeemScript'=>$addresses['msxRedeemScript']['redeemScript'],
			);
			$rawtransact = $addresses['createTrans'];

			$signrawtransaction = $coin->signrawtransaction($rawtransact,array($signTrans),array($privKey));
			if(is_array($signrawtransaction['error'])){
				return compact('signrawtransaction');	
			}else{
				$data = array(
					'signTrans.0'=>$signrawtransaction,
					'signTran.0.DateTime'=>new \MongoDate(),
					'signTran.0.IP'=>$_SERVER['REMOTE_ADDR'],
					'signTran.0.username'=>$user['username'],
					'signTran.0.user_id'=>$user['_id'],					
					'signTran.0.withdraw_address' => $addresses['createTran']['withdraw_address'],
					'signTran.0.withdraw_amount' => $addresses['createTran']['withdraw_amount'],
					'signTran.0.commission_amount' => $addresses['createTran']['commission_amount'],
					'signTran.0.tx_fee' => $addresses['createTran']['tx_fee'],
				);
				$conditions = array('msxRedeemScript.address'=>$multiAddress);
				Addresses::update($data,$conditions);

				$addresses = Addresses::find('first',array(
					'conditions'=>array('msxRedeemScript.address'=>$multiAddress)
				));
				$email = array();
				$relation = array();
				foreach($addresses['addresses'] as $address){
					array_push($email,$address['email']);
					array_push($relation,$address['relation']);
				}
				$data = array(
					'who'=>$user,
					'currency'=>$currency,
					'currencyName'=>$currencyName,
					'multiAddress'=>$multiAddress,
					'security'=>$addresses['security'],
					'name'=>$addresses['name'],
					'CoinName'=>$addresses['CoinName'],
					'DateTime'=>$addresses['DateTime'],
					'emails'=>$email,
					'txid'=>$x_txid,
					'relation'=>$relation,
					'withdraw_address' => $addresses['createTran']['withdraw_address'],
					'withdraw_amount' => $addresses['createTran']['withdraw_amount'],
					'commission_amount' => $addresses['createTran']['commission_amount'],
					'tx_fee' => $addresses['createTran']['tx_fee'],
					'createTrans' => $createTrans,
					'signrawtransaction' => $signrawtransaction,
				);				
				foreach($data['emails'] as $email){
				// sending email to the users 
				/////////////////////////////////Email//////////////////////////////////////////////////
					$function = new Functions();
					$compact = array('data'=>$data);
					// sendEmailTo($email,$compact,$controller,$template,$subject,$from,$mail1,$mail2,$mail3)
					$from = array(NOREPLY => "noreply@".COMPANY_URL);
					$email = $email;
					$attach = null;
					$function->sendEmailTo($email,$compact,'users','signTrans',"MultiSigX,com sign transaction",$from,'','','',$attach);
				/////////////////////////////////Email//////////////////////////////////////////////////				
				}
			}
		}
		return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$multiAddress.'/2'));
	}

	public function confirmTrans(){
		$user = Session::read('default');
		if ($user==""){	return $this->redirect('Users::signup');}
		if($this->request->data){
			$multiAddress = $this->request->data['address'];
			$currency = $this->request->data['currency'];
			$privKey = $this->request->data['confirmPrivKey'];
	
			$addresses = Addresses::find('first',array(
				'conditions'=>array('msxRedeemScript.address'=>$multiAddress)
			));
			
			$noOfTrans = count($addresses['signTran']);
			
			
			
			switch ($currency){
				case "BTC":
				$coin = new Bitcoin('http://'.BITCOIN_WALLET_SERVER.':'.BITCOIN_WALLET_PORT,BITCOIN_WALLET_USERNAME,BITCOIN_WALLET_PASSWORD);
				$currencyName = "Bitcoin";
				$transaction = Bitblocks::find("first",array(
					"conditions"=>array("txid.vout.scriptPubKey.addresses"=>$multiAddress)
				));
				$wallet_address = BITCOIN_WALLET_ADDRESS;
				break;

				case "XGC":
				$coin = new Greencoin('http://'.GREENCOIN_WALLET_SERVER.':'.GREENCOIN_WALLET_PORT,GREENCOIN_WALLET_USERNAME,GREENCOIN_WALLET_PASSWORD);
				$currencyName = "GreenCoin";
				$transaction = Greenblocks::find("first",array(
					"conditions"=>array("txid.vout.scriptPubKey.addresses"=>$multiAddress)
				));
				$wallet_address = GREENCOIN_WALLET_ADDRESS;
				break;
			}		
// bitcoin.signrawtransaction (signedone["hex"],
//  [{"txid":unspent[WhichTrans]["txid"],
//  "vout":unspent[WhichTrans]["vout"],"scriptPubKey":unspent[WhichTrans]["scriptPubKey"],
//  "redeemScript":unspent[WhichTrans]["redeemScript"]}],
//  [multisigprivkeytwo])		
			foreach($transaction['txid'] as $txid){
				foreach($txid['vout'] as $vout){
					foreach($vout['scriptPubKey']['addresses'] as $address){
						if($address == $multiAddress){
							$x_txid = $txid['txid'];
							$x_vout = $vout['n'];
							$x_value = $vout['value'];
							$x_scriptPubKey = $vout['scriptPubKey']['hex'];
						}
					}
				}
			}
			$signTrans = array(
				'txid'=>$x_txid,
				'vout'=>$x_vout,
				'scriptPubKey'=>$x_scriptPubKey,
				'redeemScript'=>$addresses['msxRedeemScript']['redeemScript'],
			);
			$rawtransact = $addresses['signTrans'][$noOfTrans-1]['hex'];
			$signrawtransaction = $coin->signrawtransaction($rawtransact,array($signTrans),array($privKey));
			if(array_key_exists('error' ,$signrawtransaction)){
				return compact('signrawtransaction');	
			}else{
				$data = array(
					'signTrans.'.($noOfTrans)=>$signrawtransaction,
					'signTran.'.($noOfTrans).'.DateTime'=>new \MongoDate(),
					'signTran.'.($noOfTrans).'.IP'=>$_SERVER['REMOTE_ADDR'],
					'signTran.'.($noOfTrans).'.username'=>$user['username'],
					'signTran.'.($noOfTrans).'.user_id'=>$user['_id'],					
					'signTran.'.($noOfTrans).'.withdraw_address' => $addresses['createTran']['withdraw_address'],
					'signTran.'.($noOfTrans).'.withdraw_amount' => $addresses['createTran']['withdraw_amount'],
					'signTran.'.($noOfTrans).'.commission_amount' => $addresses['createTran']['commission_amount'],
					'signTran.'.($noOfTrans).'.tx_fee' => $addresses['createTran']['tx_fee'],
				);
				$conditions = array('msxRedeemScript.address'=>$multiAddress);
				Addresses::update($data,$conditions);

				$addresses = Addresses::find('first',array(
					'conditions'=>array('msxRedeemScript.address'=>$multiAddress)
				));
				$email = array();
				$relation = array();
				foreach($addresses['addresses'] as $address){
					array_push($email,$address['email']);
					array_push($relation,$address['relation']);
				}
				$data = array(
					'who'=>$user,
					'currency'=>$currency,
					'currencyName'=>$currencyName,
					'multiAddress'=>$multiAddress,
					'security'=>$addresses['security'],
					'name'=>$addresses['name'],
					'CoinName'=>$addresses['CoinName'],
					'DateTime'=>$addresses['DateTime'],
					'emails'=>$email,
					'txid'=>$x_txid,
					'relation'=>$relation,
					'withdraw_address' => $addresses['createTran']['withdraw_address'],
					'withdraw_amount' => $addresses['createTran']['withdraw_amount'],
					'commission_amount' => $addresses['createTran']['commission_amount'],
					'tx_fee' => $addresses['createTran']['tx_fee'],
					'createTrans' => $createTrans,
					'signrawtransaction' => $signrawtransaction,
				);				
				foreach($data['emails'] as $email){
				// sending email to the users 
				/////////////////////////////////Email//////////////////////////////////////////////////
					$function = new Functions();
					$compact = array('data'=>$data);
					// sendEmailTo($email,$compact,$controller,$template,$subject,$from,$mail1,$mail2,$mail3)
					$from = array(NOREPLY => "noreply@".COMPANY_URL);
					$email = $email;
					$attach = null;
					$function->sendEmailTo($email,$compact,'users','signTrans',"MultiSigX,com sign transaction",$from,'','','',$attach);
				/////////////////////////////////Email//////////////////////////////////////////////////				
				}
			}
			if($addresses['security']==2 && $noOfTrans == 2){
				$sendrawtransaction = $coin->sendrawtransaction($signrawtransaction['hex']);
				if(array_key_exists('error' ,$sendrawtransaction)){
					
					return compact('sendrawtransaction');	
				}else{
				$data = array(
					'sendTrans'=>$sendrawtransaction,
					'sendTran.DateTime'=>new \MongoDate(),
					'sendTran.IP'=>$_SERVER['REMOTE_ADDR'],
					'sendTran.username'=>$user['username'],
					'sendTran.user_id'=>$user['_id'],					
					'sendTran.withdraw_address' => $addresses['createTran']['withdraw_address'],
					'sendTran.withdraw_amount' => $addresses['createTran']['withdraw_amount'],
					'sendTran.commission_amount' => $addresses['createTran']['commission_amount'],
					'sendTran.tx_fee' => $addresses['createTran']['tx_fee'],
				);
				$conditions = array('msxRedeemScript.address'=>$multiAddress);
				Addresses::update($data,$conditions);
				return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$multiAddress.'/3'));		
				}
			}
			if($addresses['security']==3 && $noOfTrans == 3){
				$sendrawtransaction = $coin->sendrawtransaction($signrawtransaction['hex']);
				if(array_key_exists('error' ,$sendrawtransaction)){
					return compact('sendrawtransaction');	
				}else{
				$data = array(
					'sendTrans'=>$sendrawtransaction,
					'sendTran.DateTime'=>new \MongoDate(),
					'sendTran.IP'=>$_SERVER['REMOTE_ADDR'],
					'sendTran.username'=>$user['username'],
					'sendTran.user_id'=>$user['_id'],					
					'sendTran.withdraw_address' => $addresses['createTran']['withdraw_address'],
					'sendTran.withdraw_amount' => $addresses['createTran']['withdraw_amount'],
					'sendTran.commission_amount' => $addresses['createTran']['commission_amount'],
					'sendTran.tx_fee' => $addresses['createTran']['tx_fee'],
				);
				$conditions = array('msxRedeemScript.address'=>$multiAddress);
				Addresses::update($data,$conditions);
				return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$multiAddress.'/3'));		
				}
			}
		}
		return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$multiAddress.'/2'));		
	}
	
}
?>