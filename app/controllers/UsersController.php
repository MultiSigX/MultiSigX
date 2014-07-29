<?php
namespace app\controllers;

use app\extensions\action\OAuth2;
use app\models\Pages;
use app\models\Users;
use app\models\Details;
use app\models\Transactions;
use app\models\Parameters;
use app\models\Settings;
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
		$fromfilename = LITHIUM_APP_PATH."/webroot/bootstrap/css/".$name."-bootstrap.css";
		$tofilename = LITHIUM_APP_PATH."/webroot/bootstrap/css/bootstrap.css";
		copy($fromfilename, $tofilename);
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
		if ($user==""){	return $this->redirect('Users::index');}

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

}
?>