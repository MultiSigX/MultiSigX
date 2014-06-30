<?php 
namespace app\controllers;

use app\models\Users;
use app\models\Details;
use app\models\Currencies;
use app\models\Addresses;
use app\models\Relations;

use lithium\data\Connections;
use app\extensions\action\Functions;
use app\extensions\action\Bitcoin;
use app\extensions\action\Greencoin;
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

	public function index(){}
	public function dashboard(){
	
		$user = Session::read('member');
		$id = $user['_id'];
		if($id==null){$this->redirect(array('controller'=>'Pages','action'=>'home/'));}		
		
		$addresses = Addresses::find('all',array(
			'conditions'=>array('username'=>$user['username'])
		));
		$refered = Addresses::find('all',array(
			'conditions'=>array('addresses.email'=>$user['email'])
		));
		
		return compact('user','addresses','refered');
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
				
			$publickeys = array(
				$this->request->data['pubkeycompress'][1],
				$this->request->data['pubkeycompress'][2],
				$this->request->data['pubkeycompress'][3],
				);
			$createMultiSig	= $coin->createmultisig($security,$publickeys);

			$data = array(

					'addresses.0.passphrase'=>$this->request->data['passphrase'][1],
					'addresses.0.dest'=>$this->request->data['dest'][1],
					'addresses.0.private'=>$this->request->data['private'][1],
					'addresses.0.pubkeycompress'=>$this->request->data['pubkeycompress'][1],
					'addresses.0.email'=>$this->request->data['email'][1],
					'addresses.0.address'=>$this->request->data['address'][1],
					'addresses.0.relation'=>$this->request->data['relation'][1],					

					'addresses.1.passphrase'=>$this->request->data['passphrase'][2],
					'addresses.1.dest'=>$this->request->data['dest'][2],
					'addresses.1.private'=>$this->request->data['private'][2],
					'addresses.1.pubkeycompress'=>$this->request->data['pubkeycompress'][2],
					'addresses.1.email'=>$this->request->data['email'][2],
					'addresses.1.address'=>$this->request->data['address'][2],
					'addresses.1.relation'=>$this->request->data['relation'][2],					
					
					'addresses.2.passphrase'=>$this->request->data['passphrase'][3],
					'addresses.2.dest'=>$this->request->data['dest'][3],
					'addresses.2.private'=>$this->request->data['private'][3],
					'addresses.2.pubkeycompress'=>$this->request->data['pubkeycompress'][3],
					'addresses.2.email'=>$this->request->data['email'][3],
					'addresses.2.address'=>$this->request->data['address'][3],
					'addresses.2.relation'=>$this->request->data['relation'][3],					
					
				'key'=>$this->request->data['key'],
				'secret'=>$this->request->data['secret'],
				'username'=>$this->request->data['username'],
				'currency'=>$this->request->data['currency'],
				'currencyName'=>$currency,				
				'security'=>$this->request->data['security'],
				'DateTime' => new \MongoDate(),
				'name'=>$user['username']."-".$this->request->data['currency']."-".$oneCode,
				'msxRedeemScript' => $createMultiSig,
			);
				
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
				$qrcode->png($address['private'], QR_OUTPUT_DIR.$i.'-'.$addresses['username']."-private.png", 'H', 7, 2);
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
			
			'name'=>$addresses['name'],			
			'DateTime'=>$addresses['DateTime'],			
			'address'=>$addresses['msxRedeemScript']['address'],						
			'redeemScript'=>$addresses['msxRedeemScript']['redeemScript'],									
			'email'=>$addresses['addresses'][$i]['email'],			
			'passphrase'=>$addresses['addresses'][$i]['passphrase'],						
			'dest'=>$addresses['addresses'][$i]['dest'],						
			'private'=>$addresses['addresses'][$i]['private'],						
			'pubkeycompress'=>$addresses['addresses'][$i]['pubkeycompress'],						
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

rename(QR_OUTPUT_DIR.'x-'.$printdata['username']."-MSX-Print".".pdf",QR_OUTPUT_DIR.'x-'.$printdata['username']."-MSX-Print-".$i.".pdf");


// sending email to the users 
/////////////////////////////////Email//////////////////////////////////////////////////
					$function = new Functions();
					$compact = array('data'=>$printdata);
					// sendEmailTo($email,$compact,$controller,$template,$subject,$from,$mail1,$mail2,$mail3)
					$from = array(NOREPLY => "noreply@".COMPANY_URL);
					$email = $addresses['addresses'][$i]['email'];
					$attach = QR_OUTPUT_DIR.'x-'.$printdata['username']."-MSX-Print-".$i.".pdf";
					
					$mail1 = MAIL_1;
					$mail2 = MAIL_2;
					$function->sendEmailTo($email,$compact,'users','create',"MultiSigX,com important document",$from,$mail1,$mail2,'',$attach);
/////////////////////////////////Email//////////////////////////////////////////////////				

} // create PDF files 

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
		$currencies = Currencies::find('all');
		return compact('user','details','passphrase','currencies','relations');
	}

	public function address($address = null){
		$user = Session::read('member');
		$id = $user['_id'];
		if($id==null){$this->redirect(array('controller'=>'Pages','action'=>'home/'));}		
	
		$addresses = Addresses::find('first',array(
			'conditions'=>array('msxRedeemScript.address'=>$address)
		));

		return compact('user','addresses');
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
		
		return compact('user','addresses','data');
	}
	public function settings(){}
}
?>