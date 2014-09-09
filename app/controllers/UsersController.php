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
					return $this->redirect(array('controller'=>'signin'));
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
			
			$Amount[0] = $this->request->data['sendAmount'][0];
			$Amount[1] = $this->request->data['sendAmount'][1];
			$Amount[2] = $this->request->data['sendAmount'][2];
			$Address[0] = $this->request->data['sendToAddress'][0];
			$Address[1] = $this->request->data['sendToAddress'][1];
			$Address[2] = $this->request->data['sendToAddress'][2];
			$TxFee = $this->request->data['SendTrxFee'];
			$finalBalance = $this->request->data['finalBalance'];
			$Commission = $this->request->data['commission'];
			$CommissionValue = $this->request->data['CommissionTotal'];			
			$currency = $this->request->data['currency'];
			
			$calcBalance = round($Amount[0]+$Amount[1]+$Amount[2]+$TxFee+$CommissionValue,8);
//			print_r($calcBalance);
//			print_r($finalBalance);
			if(round($finalBalance,8)!=$calcBalance){
				return $this->redirect(array('controller'=>'ex','action'=>'withdraw/'.$multiAddress.'/create/No'));
			}
			
			switch ($currency){
				case "BTC":
				$coin = new Bitcoin('http://'.BITCOIN_WALLET_SERVER.':'.BITCOIN_WALLET_PORT,BITCOIN_WALLET_USERNAME,BITCOIN_WALLET_PASSWORD);
				$currencyName = "Bitcoin";

				$opts = array(
			  'http'=> array(
					'method'=> "GET",
					'user_agent'=> "MozillaXYZ/1.0"));
				$context = stream_context_create($opts);
				$json = file_get_contents('http://blockchain.info/address/'.$multiAddress.'/?format=json', false, $context);
				$function = new Functions();
				$jdec = $function->objectToArray(json_decode($json));
//				print_r($jdec);
				foreach($jdec['txs'] as $txid){
					foreach($txid->out as $out){
						if($out->addr == $multiAddress){
							$x_txid = $txid->hash;
							$x_value = $out->value;
							$x_vout = $out->n;
							$x_scriptPubKey = $out->script;
						}
					}
				}

/*				print_r("script=".$x_scriptPubKey);
				print_r("value=".$x_value);
				print_r("out=".$x_vout);
				print_r("txid=".$x_txid);exit;
*/				
				$wallet_address = BITCOIN_WALLET_ADDRESS;
				break;

				case "XGC":
				$coin = new Greencoin('http://'.GREENCOIN_WALLET_SERVER.':'.GREENCOIN_WALLET_PORT,GREENCOIN_WALLET_USERNAME,GREENCOIN_WALLET_PASSWORD);
				$currencyName = "GreenCoin";
				$transaction = Greenblocks::find("first",array(
					"conditions"=>array("txid.vout.scriptPubKey.addresses"=>$multiAddress)
				));
				$wallet_address = GREENCOIN_WALLET_ADDRESS;
				
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
				break;
			}		
// 				bitcoin.createrawtransaction ([{"txid":unspent[WhichTrans]["txid"], 
//          "vout":unspent[WhichTrans]["vout"],
//          "scriptPubKey":unspent[WhichTrans]["scriptPubKey"],
//          "redeemScript":unspent[WhichTrans]["redeemScript"]}],
//					{SendAddress:HowMuch/100000000.00,ChangeAddress:Leftover/100000000.00})

			
			
			
			$createTrans = array(
				'txid'=>$x_txid,
				'vout'=>$x_vout,
				'scriptPubKey'=>$x_scriptPubKey,
				'redeemScript'=>$addresses['msxRedeemScript']['redeemScript'],
			);
			
			$createData = array($wallet_address=>round($CommissionValue,8));
			
			if($Amount[0]>0 && $Address[0]!=""){
				$createData = array_merge_recursive($createData,array($Address[0]=>round($Amount[0],8)));
			}
			if($Amount[1]>0 && $Address[1]!=""){
				$createData = array_merge_recursive($createData,array($Address[1]=>round($Amount[1],8)));
			}
			if($Amount[2]>0 && $Address[2]!=""){
				$createData = array_merge_recursive($createData,array($Address[2]=>round($Amount[2],8)));
			}
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
						'createTran.withdraw.address.0' => $Address[0],
						'createTran.withdraw.amount.0' => round($Amount[0],8),
						'createTran.withdraw.address.1' => $Address[1],
						'createTran.withdraw.amount.1' => round($Amount[1],8),
						'createTran.withdraw.address.2' => $Address[2],
						'createTran.withdraw.amount.2' => round($Amount[2],8),
						
						'createTran.commission_amount' => round($CommissionValue,8),
						
						'createTran.tx_fee' => round($this->request->data['SendTrxFee'],8),
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
						'withdraw.address.0' => $Address[0],
						'withdraw.amount.0' => round($Amount[0],8),
						'withdraw.address.1' => $Address[1],
						'withdraw.amount.1' => round($Amount[1],8),
						'withdraw.address.2' => $Address[2],
						'withdraw.amount.2' => round($Amount[2],8),

						'commission_amount' => round($CommissionValue,8),
						'tx_fee' => round($this->request->data['SendTrxFee'],8),
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
						$function->sendEmailTo($email,$compact,'users','createTrans',"MultiSigX.com create transaction",$from,'','','',$attach);
					/////////////////////////////////Email//////////////////////////////////////////////////				
					}
				
				} // if $createrawtransaction
			}	
//			print_r($data);
		return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$multiAddress.'/sign'));
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
			
			$noOfTrans = count($addresses['signTran']);
			
			switch ($currency){
				case "BTC":
				$coin = new Bitcoin('http://'.BITCOIN_WALLET_SERVER.':'.BITCOIN_WALLET_PORT,BITCOIN_WALLET_USERNAME,BITCOIN_WALLET_PASSWORD);
				$currencyName = "Bitcoin";
				$transaction = Bitblocks::find("first",array(
					"conditions"=>array("txid.vout.scriptPubKey.addresses"=>$multiAddress)
				));

				$opts = array(
			  'http'=> array(
					'method'=> "GET",
					'user_agent'=> "MozillaXYZ/1.0"));
				$context = stream_context_create($opts);
				$json = file_get_contents('http://blockchain.info/address/'.$multiAddress.'/?format=json', false, $context);
				$function = new Functions();
				$jdec = $function->objectToArray(json_decode($json));
//				print_r($jdec);
				foreach($jdec['txs'] as $txid){
					foreach($txid->out as $out){
						if($out->addr == $multiAddress){
							$x_txid = $txid->hash;
							$x_value = $out->value;
							$x_vout = $out->n;
							$x_scriptPubKey = $out->script;
						}
					}
				}

				$wallet_address = BITCOIN_WALLET_ADDRESS;
				break;

				case "XGC":
				$coin = new Greencoin('http://'.GREENCOIN_WALLET_SERVER.':'.GREENCOIN_WALLET_PORT,GREENCOIN_WALLET_USERNAME,GREENCOIN_WALLET_PASSWORD);
				$currencyName = "GreenCoin";
				$transaction = Greenblocks::find("first",array(
					"conditions"=>array("txid.vout.scriptPubKey.addresses"=>$multiAddress)
				));
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

				$wallet_address = GREENCOIN_WALLET_ADDRESS;
				break;
			}		
// bitcoin.signrawtransaction (rawtransact,
//  [{"txid":unspent[WhichTrans]["txid"],
//  "vout":unspent[WhichTrans]["vout"],"scriptPubKey":unspent[WhichTrans]["scriptPubKey"],
//  "redeemScript":unspent[WhichTrans]["redeemScript"]}],
//  [multisigprivkeyone])			
			$signTrans = array(
				'txid'=>$x_txid,
				'vout'=>$x_vout,
				'scriptPubKey'=>$x_scriptPubKey,
				'redeemScript'=>$addresses['msxRedeemScript']['redeemScript'],
			);
			
			if($noOfTrans==0){
				$rawtransact = $addresses['createTrans'];
			}else{
				$rawtransact = $addresses['signTrans'][$noOfTrans-1]['hex'];
			}
			
			$signrawtransaction = $coin->signrawtransaction($rawtransact,array($signTrans),array($privKey));
			if(is_array($signrawtransaction['error'])){
				return compact('signrawtransaction');	
			}else{
				$data = array(
					'signTrans.'.($noOfTrans)=>$signrawtransaction,
					'signTran.'.($noOfTrans).'.DateTime'=>new \MongoDate(),
					'signTran.'.($noOfTrans).'.IP'=>$_SERVER['REMOTE_ADDR'],
					'signTran.'.($noOfTrans).'.username'=>$user['username'],
					'signTran.'.($noOfTrans).'.user_id'=>$user['_id'],					
					'signTran.'.($noOfTrans).'.withdraw.address.0' => $addresses['createTran']['withdraw']['address'][0],
					'signTran.'.($noOfTrans).'.withdraw.amount.0' => $addresses['createTran']['withdraw']['amount'][0],
					'signTran.'.($noOfTrans).'.withdraw.address.1' => $addresses['createTran']['withdraw']['address'][1],
					'signTran.'.($noOfTrans).'.withdraw.amount.1' => $addresses['createTran']['withdraw']['amount'][1],
					'signTran.'.($noOfTrans).'.withdraw.address.2' => $addresses['createTran']['withdraw']['address'][2],
					'signTran.'.($noOfTrans).'.withdraw.amount.2' => $addresses['createTran']['withdraw']['amount'][2],
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
					'withdraw.address.0' => $addresses['createTran']['withdraw']['address'][0],
					'withdraw.amount.0' => $addresses['createTran']['withdraw']['amount'][0],
					'withdraw.address.1' => $addresses['createTran']['withdraw']['address'][1],
					'withdraw.amount.1' => $addresses['createTran']['withdraw']['amount'][1],
					'withdraw.address.2' => $addresses['createTran']['withdraw']['address'][2],
					'withdraw.amount.2' => $addresses['createTran']['withdraw']['amount'][2],
					'commission_amount' => $addresses['createTran']['commission_amount'],
					'tx_fee' => $addresses['createTran']['tx_fee'],
					'signTrans' => $signTrans,
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
					$function->sendEmailTo($email,$compact,'users','signTrans',"MultiSigX.com sign transaction",$from,'','','',$attach);
				/////////////////////////////////Email//////////////////////////////////////////////////				
				}
			}
		}
		return $this->redirect(array('controller'=>'Ex','action'=>'dashboard'));
	}
	public function sendTrans(){
		$user = Session::read('default');
		if ($user==""){	return $this->redirect('Users::signup');}
		if($this->request->data){
			$multiAddress = $this->request->data['address'];
			$currency = $this->request->data['currency'];
	
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
			if($addresses['signTrans'][($noOfTrans-1)]['complete']==true){
				$signrawtransaction = $addresses['signTrans'][($noOfTrans-1)]['hex'];
			}else{
				return $this->redirect(array('controller'=>'ex','action'=>'dashboard/'));	
			}
print_r($addresses['security']);
print_r($noOfTrans);

			if($addresses['security']==$noOfTrans){
				$sendrawtransaction = $coin->sendrawtransaction($signrawtransaction);
				if(is_array($sendrawtransaction)){
					if(array_key_exists('error' ,$sendrawtransaction)){
					return compact('sendrawtransaction');	
					}
				}else{
					$data = array(
						'sendTrans'=>$sendrawtransaction,
						'sendTran.DateTime'=>new \MongoDate(),
						'sendTran.IP'=>$_SERVER['REMOTE_ADDR'],
						'sendTran.username'=>$user['username'],
						'sendTran.user_id'=>$user['_id'],					
						'sendTran.withdraw.address.0' => $addresses['createTran']['withdraw']['address'][0],
						'sendTran.withdraw.amount.0' => $addresses['createTran']['withdraw']['amount'][0],
						'sendTran.withdraw.address.1' => $addresses['createTran']['withdraw']['address'][1],
						'sendTran.withdraw.amount.1' => $addresses['createTran']['withdraw']['amount'][1],
						'sendTran.withdraw.address.2' => $addresses['createTran']['withdraw']['address'][2],
						'sendTran.withdraw.amount.2' => $addresses['createTran']['withdraw']['amount'][2],
						'sendTran.commission_amount' => $addresses['createTran']['commission_amount'],
						'sendTran.tx_fee' => $addresses['createTran']['tx_fee'],
					);
					$conditions = array('msxRedeemScript.address'=>$multiAddress);
					Addresses::update($data,$conditions);

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
						'txid'=>$sendrawtransaction,
						'relation'=>$relation,
						'withdraw.address.0' => $addresses['createTran']['withdraw']['address'][0],
						'withdraw.amount.0' => $addresses['createTran']['withdraw']['amount'][0],
						'withdraw.address.1' => $addresses['createTran']['withdraw']['address'][1],
						'withdraw.amount.1' => $addresses['createTran']['withdraw']['amount'][1],
						'withdraw.address.2' => $addresses['createTran']['withdraw']['address'][2],
						'withdraw.amount.2' => $addresses['createTran']['withdraw']['amount'][2],
						'commission_amount' => $addresses['createTran']['commission_amount'],
						'tx_fee' => $addresses['createTran']['tx_fee'],
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
						$function->sendEmailTo($email,$compact,'users','sendTrans',"MultiSigX.com Transaction complete",$from,'','','',$attach);
					/////////////////////////////////Email//////////////////////////////////////////////////				
					}
					
					return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$multiAddress.'/send'));		
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
						'txid'=>$sendrawtransaction,
						'relation'=>$relation,
						'withdraw_address' => $addresses['createTran']['withdraw_address'],
						'withdraw_amount' => $addresses['createTran']['withdraw_amount'],
						'commission_amount' => $addresses['createTran']['commission_amount'],
						'tx_fee' => $addresses['createTran']['tx_fee'],
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
						$function->sendEmailTo($email,$compact,'users','sendTrans',"MultiSigX.com Transaction complete",$from,'','','',$attach);
					/////////////////////////////////Email//////////////////////////////////////////////////				
					}					
					return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$multiAddress.'/send'));		
				}
			}
		}
		return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$multiAddress.'/sign'));		
	}

	public function DeleteCreateTrans($multiAddress=null){
		$user = Session::read('default');
		if ($user==""){	return $this->redirect('Users::signup');}
			$conditions = array('msxRedeemScript.address'=>$multiAddress);
			$unset = array('$unset'=>array('createTran'=>'','createTrans'=>'','signTran'=>'','signTrans'=>'','sendTran'=>'','sendTrans'=>''));
			Addresses::update($unset,$conditions);	
			return $this->redirect(array('controller'=>'ex','action'=>'withdraw/'.$multiAddress));		
	}
}
?>