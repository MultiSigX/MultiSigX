<?php
namespace app\controllers;
use app\models\Pages;
use app\models\Users;
use app\models\Details;
use app\models\Addresses;

use lithium\storage\Session;
use lithium\util\Validator;
use app\extensions\action\GoogleAuthenticator;
use app\extensions\action\PHPCoinAddress;
use app\extensions\action\Bitcoin;
use app\extensions\action\Greencoin;
use li3_qrcode\extensions\action\QRcode;
use app\extensions\action\Functions;

use \lithium\template\View;
use \Swift_MailTransport;
use \Swift_Mailer;
use \Swift_Message;
use \Swift_Attachment;


class APIController extends \lithium\action\Controller {
	public function index(){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));
		$user = Session::read('default');
		$id = $user['_id'];

		$details = Details::find('first',
			array('conditions'=>array('user_id'=> (string) $id))
		);

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		return compact('title','keywords','description','details','user');	
	}
	public function CreateWallet($key=null){
		$result = array();
		if(!$this->request->data){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>"Not submitted through POST."
			)));
		}
	 if ($key==null){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>"Key not specified. Please get your key from your settings page under security."
			)));
	 }else{
			$details = Details::find('first',array(
				'conditions'=>array('key'=>$key)
			));
			$user = Users::find('first',array(
				'conditions'=>array('_id'=>$details['user_id'])
			));
			
			if(count($details)==0){
				return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>"Incorrect Key! Please get your key from your settings page under security."
				)));
			}else{
//========================================================================
				$security = $this->request->data['security'];
				if($security==""){
					return $this->render(array('json' => array('success'=>0,
						'now'=>time(),
						'error'=>"Security not specified."
					)));
				}else{
					$result = array_merge_recursive($result,array('security'=>$security));
				}

				$coin = $this->request->data['coin'];
				if($coin==""){
					return $this->render(array('json' => array('success'=>0,
						'now'=>time(),
						'error'=>"Coin not specified."
					)));
				}else{
					$result = array_merge_recursive($result,array('coin'=>$coin));
				}

				$coinName = $this->request->data['coinName'];
				if($coinName==""){
					return $this->render(array('json' => array('success'=>0,
						'now'=>time(),
						'error'=>"CoinName not specified."
					)));
				}else{
					$result = array_merge_recursive($result,array('coinName'=>$coinName));
				}
				
				$changeAddress = $this->request->data['changeAddress'];
				if($changeAddress==""){
					return $this->render(array('json' => array('success'=>0,
						'now'=>time(),
						'error'=>"changeAddress not specified."
					)));
				}else{
					$result = array_merge_recursive($result,array('changeAddress'=>$changeAddress));
				}
				
				$changeAddressValue = $this->request->data['changeAddressValue'];
				if($changeAddressValue=="" && $changeAddress=="Simple"){
					return $this->render(array('json' => array('success'=>0,
						'now'=>time(),
						'error'=>"changeAddressValue not specified."
					)));
				}else{
					$result = array_merge_recursive($result,array('changeAddressValue'=>$changeAddressValue));
				}

				
				
				$email1 = $this->request->data['email1'];
				if(Validator::rule('email',$email1)==""){
					return $this->render(array('json' => array('success'=>0,
						'now'=>time(),
						'error'=>"Email1 not specified / Email1 not correct."
					)));
				}else{
					$result = array_merge_recursive($result,array('email1'=>$email1));
				}

				$relation1 = $this->request->data['relation1'];
				if($relation1==""){
					return $this->render(array('json' => array('success'=>0,
						'now'=>time(),
						'error'=>"Relation1 not specified."
					)));
				}else{
					$result = array_merge_recursive($result,array('relation1'=>$relation1));
				}

				$email2 = $this->request->data['email2'];
				if(Validator::rule('email',$email2)==""){
					return $this->render(array('json' => array('success'=>0,
						'now'=>time(),
						'error'=>"Email2 not specified."
					)));
				}else{
					$result = array_merge_recursive($result,array('email2'=>$email2));
				}

				$relation2 = $this->request->data['relation2'];
				if($relation2==""){
					return $this->render(array('json' => array('success'=>0,
						'now'=>time(),
						'error'=>"Relation2 not specified."
					)));
				}else{
					$result = array_merge_recursive($result,array('relation2'=>$relation2));
				}

				$email3 = $this->request->data['email3'];
				if(Validator::rule('email',$email3)==""){
					return $this->render(array('json' => array('success'=>0,
						'now'=>time(),
						'error'=>"Email3 not specified."
					)));
				}else{
					$result = array_merge_recursive($result,array('email3'=>$email3));
				}

				$relation3 = $this->request->data['relation3'];
				if($relation3==""){
					return $this->render(array('json' => array('success'=>0,
						'now'=>time(),
						'error'=>"Relation3 not specified."
					)));
				}else{
					$result = array_merge_recursive($result,array('relation3'=>$relation3));
				}		
		$ga = new GoogleAuthenticator();
		$passphrase[1] = $ga->createSecret(64);
		$passphrase[2] = $ga->createSecret(64);		
		$passphrase[3] = $ga->createSecret(64);		
		$username = $user['username'];
		$key = $details['key'];
		$secret = $details['secret'];
		for ($i=1;$i<=3;$i++){
			$email = "email".$i;
			$keys[$i] = $this->GenerateKeys($i,$passphrase[$i],$$email);
		}
//		print_r($keys);
		$oneCode = $ga->getCode($secret);	

			switch ($this->request->data['coin']){
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
				$keys[1]['public_hex'],
				$keys[2]['public_hex'],
				$keys[3]['public_hex'],
				);
				$name = $user['username']."-".$this->request->data['coin']."-".$oneCode;
				$createMultiSig	= $coin->createmultisig($security,$publickeys);
				$changeAddress = $this->request->data['changeAddress'];
				if($changeAddress=="MultiSigX"){
					$changeValue = $createMultiSig['address'];
				}else{
					$changeValue = $this->request->data['changeAddressValue'];
				}
				$data = array(

					'addresses.0.passphrase'=>$passphrase[1],
					'addresses.0.dest'=>"Not Required as created through API",
//					'addresses.0.private'=>$this->request->data['private'][1],
					'addresses.0.pubkeycompress'=>$keys[1]['public_hex'],
					'addresses.0.email'=>$email1,
					'addresses.0.address'=>$keys[1]['public'],
					'addresses.0.relation'=>$this->request->data['relation1'],					

					'addresses.1.passphrase'=>$passphrase[2],
					'addresses.1.dest'=>"Not Required as created through API",
//					'addresses.1.private'=>$this->request->data['private'][2],
					'addresses.1.pubkeycompress'=>$keys[2]['public_hex'],
					'addresses.1.email'=>$email2,
					'addresses.1.address'=>$keys[2]['public'],
					'addresses.1.relation'=>$this->request->data['relation2'],					
					
					'addresses.2.passphrase'=>$passphrase[3],
					'addresses.2.dest'=>"Not Required as created through API",
//					'addresses.2.private'=>$this->request->data['private'][3],
					'addresses.2.pubkeycompress'=>$keys[3]['public_hex'],
					'addresses.2.email'=>$email3,
					'addresses.2.address'=>$keys[3]['public'],
					'addresses.2.relation'=>$this->request->data['relation3'],					
					
				'key'=>$details['key'],
				'secret'=>$details['secret'],
				'username'=>$details['username'],
				'currency'=>$this->request->data['coin'],
				'currencyName'=>$currency,				
				'CoinName'=>$this->request->data['coinName'],
				'security'=>$this->request->data['security'],
				'DateTime' => new \MongoDate(),
				'name'=>$name,
				'msxRedeemScript' => $createMultiSig,
				'Change.default' => $changeAddress,
				'Change.value'=> $changeValue,
			);
			if($email[2]	== DEFAULT_ESCROW){
				$data1 = array(
			'addresses.1.private'=>$keys['private'][2]
			);
			$data = array_merge($data,$data1);
		//	print_r($data);	
		}
		if($email[3]	== DEFAULT_ESCROW){
			$data2 = array(
			'addresses.2.private'=>$keys['private'][3]
			);
			$data = array_merge($data,$data2);	
		}
      $Addresses = Addresses::create($data);
      $saved = $Addresses->save();
			
			$addresses = Addresses::find('first',array(
				'conditions'=>array('_id'=>$Addresses->_id)
			));
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
			$qrcode = new QRcode();			
			$i = 0;
			foreach($addresses['addresses'] as $address){
				$qrcode->png($address['passphrase'], QR_OUTPUT_DIR.$i.'-'.$addresses['username']."-passphrase.png", 'H', 7, 2);
				$qrcode->png($address['dest'], QR_OUTPUT_DIR.$i.'-'.$addresses['username']."-dest.png", 'H', 7, 2);				
				$qrcode->png($keys[$i+1]['private'], QR_OUTPUT_DIR.$i.'-'.$addresses['username']."-private.png", 'H', 7, 2);				
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
			'OpenPassword'=> $oneCode.'-'.substr($addresses['msxRedeemScript']['address'],0,6),
			'name'=>$addresses['name'],			
			'DateTime'=>$addresses['DateTime'],			
			'address'=>$addresses['msxRedeemScript']['address'],						
			'redeemScript'=>$addresses['msxRedeemScript']['redeemScript'],									
			'email'=>$addresses['addresses'][$i]['email'],			
			'passphrase'=>$addresses['addresses'][$i]['passphrase'],						
			'dest'=>$addresses['addresses'][$i]['dest'],						
			'private'=>$keys[$i+1]['private'],						
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
	$result = array_merge_recursive($result,array('address'=>$addresses['msxRedeemScript']['address']));
	$result = array_merge_recursive($result,array('redeemScript'=>$addresses['msxRedeemScript']['redeemScript']));
//========================================================================			
				return $this->render(array('json' => array('success'=>1,
				'now'=>time(),
				'result'=>$result
				)));
			}
		}
	}

	function GenerateKeys($j,$value,$email){
		$coin = new PHPCoinAddress();
		$keys = $coin->bitcoin();
		return $keys;
	}
	//////////////////////////////////////////////
	function coin_info($name,$coin) {
    print "\n$name";
//    print " [ prefix_public: " . CoinAddress::$prefix_public;
//				print "  prefix_private: " . CoinAddress::$prefix_private . " ]\n";
    print "uncompressed:\n";
    print 'public (base58): ' . $coin['public'] . "\n";
    print 'public (Hex)   : ' . $coin['public_hex'] . "\n";
    print 'private (WIF)  : ' . $coin['private'] . "\n";
    print 'private (Hex)  : ' . $coin['private_hex'] . "\n";
				
    print "compressed:\n";
    print 'public (base58): ' . $coin['public_compressed'] . "\n";
    print 'public (Hex)   : ' . $coin['public_compressed_hex'] . "\n";
    print 'private (WIF)  : ' . $coin['private_compressed'] . "\n";
    print 'private (Hex)  : ' . $coin['private_compressed_hex'] . "\n";
}


}
?>