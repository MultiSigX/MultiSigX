<?php 
namespace app\controllers;

use app\models\Users;
use app\models\Details;
use app\models\Currencies;
use app\models\Addresses;
use app\models\Relations;
use app\models\Pages;

use lithium\data\Connections;
use app\extensions\action\Functions;
use app\extensions\action\Bitcoin;
use app\extensions\action\Greencoin;
use app\controllers\UsersController;
use lithium\security\Auth;
use lithium\storage\Session;
use app\extensions\action\GoogleAuthenticator;
use lithium\util\String;
use MongoID;

use \lithium\template\View;
use \Swift_MailTransport;
use \Swift_Mailer;
use \Swift_Message;
use \Swift_Attachment;
use li3_qrcode\extensions\action\QRcode;

class ExController extends \lithium\action\Controller {

	public function index(){
	return $this->render(array('json' => array("EX"=>false)));
	}
	public function dashboard($what = null){
		
		$UC = new UsersController();
	
		$user = Session::read('member');
		$id = $user['_id'];
		if($id==null){$this->redirect(array('controller'=>'Users','action'=>'signup'));}		
		$addresses = Addresses::find('all',array(
			'conditions'=>array('username'=>$user['username'])
		));
		$balances = array();
		foreach($addresses as $address){
			$final = $UC->CheckBalance($address['msxRedeemScript']['address'],$address['currencyName'],true);
			array_push($balances, array('address'=>$address['msxRedeemScript']['address'],'balance'=>$final['final']));
		}
		$refered = Addresses::find('all',array(
			'conditions'=>array('addresses.email'=>$user['email'])
		));

		$currencies = Currencies::find('all',array('order'=>array('currency.name'=>-1)));		
		
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		if($what=="signed"){
			$msg = "You have already signed this withdrawal. Please wait for others to sign and send this transaction!";
		}

		return compact('user','addresses','refered','currencies','balances','title','keywords','description','msg');
	}
	
	public function create(){
		$user = Session::read('member');
		$id = $user['_id'];
		$ga = new GoogleAuthenticator();
		
		if($id==null){$this->redirect(array('controller'=>'Pages','action'=>'home/'));}		

		if($this->request->data){
			
			$oneCode = $ga->getCode($secret);	
			
			switch ($this->request->data['currency']){
				case "BTC":
				$coin = new Bitcoin('http://'.BITCOIN_WALLET_SERVER.':'.BITCOIN_WALLET_PORT,BITCOIN_WALLET_USERNAME,BITCOIN_WALLET_PASSWORD);
				$currency = "Bitcoin";
				break;

				case "XGC":
				$coin = new Greencoin('http://'.GREENCOIN_WALLET_SERVER.':'.GREENCOIN_WALLET_PORT,GREENCOIN_WALLET_USERNAME,GREENCOIN_WALLET_PASSWORD);
				$currency = "GreenCoin";
				break;
			}
			$security = (int)$this->request->data['security'];
			/*
print_r(GREENCOIN_WALLET_PASSWORD)				;
print_r(GREENCOIN_WALLET_USERNAME)				;
print_r(GREENCOIN_WALLET_PORT)				;
print_r(GREENCOIN_WALLET_SERVER)				;
print_r($coin);
*/				
			$publickeys = array(
				$this->request->data['pubkeycompress'][1],
				$this->request->data['pubkeycompress'][2],
				$this->request->data['pubkeycompress'][3],
				);
			$name = $user['username']."-".$this->request->data['currency']."-".$oneCode;
//			$AddMultiSig	= $coin->addmultisigaddress($security,$publickeys,'MSX-'.$name);			
			$createMultiSig	= $coin->createmultisig($security,$publickeys);

			$data = array(

					'addresses.0.passphrase'=>$this->request->data['passphrase'][1],
					'addresses.0.dest'=>$this->request->data['dest'][1],
//					'addresses.0.private'=>$this->request->data['private'][1],
					'addresses.0.pubkeycompress'=>$this->request->data['pubkeycompress'][1],
					'addresses.0.email'=>$this->request->data['email'][1],
					'addresses.0.address'=>$this->request->data['address'][1],
					'addresses.0.relation'=>$this->request->data['relation'][1],					

					'addresses.1.passphrase'=>$this->request->data['passphrase'][2],
					'addresses.1.dest'=>$this->request->data['dest'][2],
//					'addresses.1.private'=>$this->request->data['private'][2],
					'addresses.1.pubkeycompress'=>$this->request->data['pubkeycompress'][2],
					'addresses.1.email'=>$this->request->data['email'][2],
					'addresses.1.address'=>$this->request->data['address'][2],
					'addresses.1.relation'=>$this->request->data['relation'][2],					
					
					'addresses.2.passphrase'=>$this->request->data['passphrase'][3],
					'addresses.2.dest'=>$this->request->data['dest'][3],
//					'addresses.2.private'=>$this->request->data['private'][3],
					'addresses.2.pubkeycompress'=>$this->request->data['pubkeycompress'][3],
					'addresses.2.email'=>$this->request->data['email'][3],
					'addresses.2.address'=>$this->request->data['address'][3],
					'addresses.2.relation'=>$this->request->data['relation'][3],					
					
				'key'=>$this->request->data['key'],
				'secret'=>$this->request->data['secret'],
				'username'=>$this->request->data['username'],
				'currency'=>$this->request->data['currency'],
				'currencyName'=>$currency,				
				'CoinName'=>$this->request->data['CoinName'],
				'security'=>$this->request->data['security'],
				'DateTime' => new \MongoDate(),
				'name'=>$name,
				'msxRedeemScript' => $createMultiSig,
			);
			
if($this->request->data['email'][2]	== DEFAULT_ESCROW){
	$data1 = array(
	'addresses.1.private'=>$this->request->data['private'][2]
	);

	$data = array_merge($data,$data1);
//	print_r($data);	
}
if($this->request->data['email'][3]	== DEFAULT_ESCROW){
	$data2 = array(
	'addresses.2.private'=>$this->request->data['private'][3]
	);
	$data = array_merge($data,$data2);	
}

//print_r($data);exit;


      $Addresses = Addresses::create($data);
      $saved = $Addresses->save();
			
			$addresses = Addresses::find('first',array(
				'conditions'=>array('_id'=>$Addresses->_id)
			));

// create print PDF for all 3 users

// Delete all old files from the system
if ($handle = opendir(QR_OUTPUT_DIR)) {
    while (false !== ($entry = readdir($handle))) {
		 if ($entry != "." && $entry != "..") {
			 	if(strpos($entry,$addresses['username'])){
					unlink(QR_OUTPUT_DIR.$entry);
				}
			}
    }
    closedir($handle);
}
//Create all QRCodes
			$qrcode = new QRcode();			
			$i = 0;
			foreach($addresses['addresses'] as $address){
				$qrcode->png($address['passphrase'], QR_OUTPUT_DIR.$i.'-'.$addresses['username']."-passphrase.png", 'H', 7, 2);
				$qrcode->png($address['dest'], QR_OUTPUT_DIR.$i.'-'.$addresses['username']."-dest.png", 'H', 7, 2);				
				$qrcode->png($this->request->data['private'][$i+1], QR_OUTPUT_DIR.$i.'-'.$addresses['username']."-private.png", 'H', 7, 2);				
				$qrcode->png($address['pubkeycompress'], QR_OUTPUT_DIR.$i.'-'.$addresses['username']."-pc.png", 'H', 7, 2);				
				$i++;
			}

			$qrcode->png($addresses['msxRedeemScript']['address'], QR_OUTPUT_DIR.'x-'.$addresses['username'].'-'.$addresses['msxRedeemScript']['address']."-address.png", 'H', 7, 2);

			$qrcode->png($addresses['msxRedeemScript']['redeemScript'], QR_OUTPUT_DIR.'x-'.$addresses['username'].'-'."redeemScript.png", 'H', 7, 2);

//create PDF files...
for($i=0;$i<=2;$i++){

		$printdata = array(
			'i'=>$i,
			'username'=>$addresses['username'],
			'createEmail'=>$addresses['addresses'][0]['email'],
			'user1'=>$addresses['addresses'][1]['email'],
			'user2'=>$addresses['addresses'][2]['email'],
			'relation0'=>$addresses['addresses'][0]['relation'],						
			'relation1'=>$addresses['addresses'][1]['relation'],						
			'relation2'=>$addresses['addresses'][2]['relation'],			
			'address0'=>$addresses['addresses'][0]['address'],						
			'address1'=>$addresses['addresses'][1]['address'],						
			'address2'=>$addresses['addresses'][2]['address'],			
			
			'name'=>$addresses['name'],			
			'DateTime'=>$addresses['DateTime'],			
			'address'=>$addresses['msxRedeemScript']['address'],						
			'redeemScript'=>$addresses['msxRedeemScript']['redeemScript'],									
			'email'=>$addresses['addresses'][$i]['email'],			
			'passphrase'=>$addresses['addresses'][$i]['passphrase'],						
			'dest'=>$addresses['addresses'][$i]['dest'],						
			'private'=>$this->request->data['private'][$i+1],						
			'pubkeycompress'=>$addresses['addresses'][$i]['pubkeycompress'],						
			'CoinName'=>$addresses['CoinName'],
			'currency'=>$addresses['currency'],						
			'currencyName'=>$currency,
			'security'=>$addresses['security'],						
		);

		$view  = new View(array(
		'paths' => array(
			'template' => '{:library}/views/{:controller}/{:template}.{:type}.php',
			'layout'   => '{:library}/views/layouts/{:layout}.{:type}.php',
		)
		));
		echo $view->render(
		'all',
		compact('printdata'),
		array(
			'controller' => 'print',
			'template'=>'print',
			'type' => 'pdf',
			'layout' =>'print'
		)
		);	

rename(QR_OUTPUT_DIR.'MultiSigX.com-'.$printdata['name']."-MSX-Print".".pdf",QR_OUTPUT_DIR.'MultiSigX.com-'.$printdata['name']."-MSX-Print-".$i.".pdf");


// sending email to the users 
/////////////////////////////////Email//////////////////////////////////////////////////
	$function = new Functions();
	$compact = array('data'=>$printdata);
	// sendEmailTo($email,$compact,$controller,$template,$subject,$from,$mail1,$mail2,$mail3)
	$from = array(NOREPLY => "noreply@".COMPANY_URL);
	$email = $addresses['addresses'][$i]['email'];
	$attach = QR_OUTPUT_DIR.'MultiSigX.com-'.$printdata['name']."-MSX-Print-".$i.".pdf";
	
	$function->sendEmailTo($email,$compact,'users','create',"MultiSigX,com important document",$from,'','','',$attach);
/////////////////////////////////Email//////////////////////////////////////////////////				

} // create PDF files 

// Delete all old files from the system
if ($handle = opendir(QR_OUTPUT_DIR)) {
    while (false !== ($entry = readdir($handle))) {
		 if ($entry != "." && $entry != "..") {
			 	if(strpos($entry,$addresses['username'])){
					unlink(QR_OUTPUT_DIR.$entry);
				}
			}
    }
    closedir($handle);
}
//Create all QRCodes

			$this->redirect('Ex::dashboard');	


		} // if $this->request->data
		
		$details = Details::find('first',array(
			'conditions'=>array('username'=>$user['username'])
		));
		$relations = Relations::find('all',array(
			'order'=>array('type'=>-1)
		));


		$passphrase[0] = $ga->createSecret(64);
		$passphrase[1] = $ga->createSecret(64);		
		$passphrase[2] = $ga->createSecret(64);		
		$currencies = Currencies::find('all',array('order'=>array('currency.name'=>-1)));
		$addresses = Addresses::find('all',array(
			'conditions'=>array('username'=>$user['username']),
			'fields'=>array('currencyName','currency')
		));
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		
		return compact('user','details','passphrase','currencies','relations','addresses','title','keywords','description');
	}

	public function address($address = null){
		$user = Session::read('member');
		$id = $user['_id'];
		if($id==null){$this->redirect(array('controller'=>'Pages','action'=>'home/'));}		
	
		$addresses = Addresses::find('first',array(
			'conditions'=>array('msxRedeemScript.address'=>$address)
		));

		$data = array();
		foreach($addresses['addresses'] as $address){
			$userFind = Users::find('first',array(
				'conditions'=>array('email'=>$address['email'])
			));
			array_push($data, array(
				'email'=>$address['email'],
				'relation'=>$address['relation'],
				'address'=>$address['address'],				
				'username'=>$userFind['username']
				));
		}
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		
		return compact('user','addresses','data','title','keywords','description');
	}

	public function name($name = null){
		$user = Session::read('member');
		$id = $user['_id'];
		if($id==null){$this->redirect(array('controller'=>'Pages','action'=>'home/'));}		

		$addresses = Addresses::find('first',array(
			'conditions'=>array('name'=>$name)
		));

		$data = array();
		foreach($addresses['addresses'] as $address){
			$userFind = Users::find('first',array(
				'conditions'=>array('email'=>$address['email'])
			));
			array_push($data, array(
				'email'=>$address['email'],
				'relation'=>$address['relation'],
				'address'=>$address['address'],
				'username'=>$userFind['username']
				));
		}
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		
		return compact('user','addresses','data','title','keywords','description');
	}
	public function settings(){}

	public function withdraw($address=null,$step=null,$msg=null){
		
		if($address==null){
			return $this->redirect(array('controller'=>'Ex','action'=>'dashboard/'));
		}				
		$user = Session::read('member');
		
		$id = $user['_id'];
		if($id==null){$this->redirect(array('controller'=>'Pages','action'=>'home/'));}		
		
		$addresses = Addresses::find('first',array(
			'conditions'=>array('msxRedeemScript.address'=>$address)
		));
		
		foreach($addresses['addresses'] as $multiUser){
			$multiUserEmail = $multiUser['email'];
			if($user['email']==$multiUserEmail){
				$authorizeUsername = $user['username'];
			}
		}

		switch($step){
			case '';
				return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$address.'/create'));
			break;
			
			case 'create';
				if($addresses['createTrans']!=null){
					return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$address.'/sign'));
				}
				$button = 'Create';
				break;
			
			case 'sign';
				if(count($addresses['signTrans']) == (int) $addresses['security']){
					return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$address.'/send'));
				}
				if($addresses['createTrans']==null){
					return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$address.'/create'));
				}
				$button = 'Sign';
			break;

			case 'send';
				$button = 'Send';
			break;
			
			default:
			return $this->redirect(array('controller'=>'Ex','action'=>'withdraw/'.$address.'/create'));
			break;
		}
		
		$transact = array();
		
		//who has created, signed, send transactions
		if($addresses['createTran']!=null){
				$create = $addresses['createTran'];
		}
		if($addresses['signTran']!=null){
				$sign = $addresses['signTran'];
		}
		if($addresses['sendTran']!=""){
				$send = $addresses['sendTran'];
		}
		$transact = array('create'=>$create,'sign'=>$sign,'send'=>$send);
		
		$UC = new UsersController();

		$final = $UC->CheckBalance($address,$addresses['currencyName'],true);
		$final_balance = $final['final'];

		$currencies = Currencies::find("first",array(
			"conditions"=>array("currency.name"=>$addresses['currencyName'])
		));
		
		$data = array();
		foreach($addresses['addresses'] as $address){
			$userFind = Users::find('first',array(
				'conditions'=>array('email'=>$address['email'])
			));
			array_push($data, array(
				'email'=>$address['email'],
				'relation'=>$address['relation'],
				'address'=>$address['address'],				
				'username'=>$userFind['username']
				));
		}
		
		
		$next = $step;
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$relations = Relations::find('all',array(
			'order'=>array('type'=>-1)
		));

		
		
		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		
		return compact('user','addresses','data','final_balance','next','title','keywords','description','currencies','relations','button','transact','msg');
	}
	
}
?>