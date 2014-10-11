<?php
namespace app\controllers;
use app\extensions\action\Bitcoin;
use app\extensions\action\Litecoin;
use app\extensions\action\Greencoin;
use lithium\util\String;

class CommonController extends \lithium\action\Controller {


	public function CurrencyAddress($currency=null,$address = null){
		///////////////////// Change of code required when Virtual Currency added			
		switch($currency){
			case "BTC":
				$coin = new Bitcoin('http://'.BITCOIN_WALLET_SERVER.':'.BITCOIN_WALLET_PORT,BITCOIN_WALLET_USERNAME,BITCOIN_WALLET_PASSWORD);
				break;
			case "XGC":
				$coin = new Greencoin('http://'.GREENCOIN_WALLET_SERVER.':'.GREENCOIN_WALLET_PORT,GREENCOIN_WALLET_USERNAME,GREENCOIN_WALLET_PASSWORD);
				break;
				case "LTC":
				$coin = new Litecoin('http://'.LITECOIN_WALLET_SERVER.':'.LITECOIN_WALLET_PORT,LITECOIN_WALLET_USERNAME,LITECOIN_WALLET_PASSWORD);
				break;
		}
		// End of /////////////////// Change of code required when Virtual Currency added			
			$verify = $coin->validateaddress($address);
			return $this->render(array('json' => array(
			'verify'=> $verify,
			'currency'=>$currency,
		)));
	}
}
?>