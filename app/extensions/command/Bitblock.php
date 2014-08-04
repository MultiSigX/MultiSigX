<?php
namespace app\extensions\command;
use app\models\Bitblocks;
use app\extensions\action\Bitcoin;

//every 2 seconds cron job for adding transactions....
ini_set('memory_limit', '-1');

class Bitblock extends \lithium\console\Command {

  public function run() {
	$coin = new Bitcoin('http://'.BITCOIN_WALLET_SERVER.':'.BITCOIN_WALLET_PORT,BITCOIN_WALLET_USERNAME,BITCOIN_WALLET_PASSWORD);

	$getblockcount = $coin->getblockcount();
		
		$height = Bitblocks::find('first',array(
			'order' => array('height'=>'DESC')
		));
	
	$h = (int)$height['height'] + 1;
		for($i = $h;$i<=$h+999;$i++)	{
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$pagestarttime = $mtime; 

			$data = array();
			if($i <= $getblockcount){
				$getblockhash = $coin->getblockhash($i);
				$getblock = $coin->getblock($getblockhash);

				$data = array(
					'confirmations' => $getblock['confirmations'],
					'height' => $getblock['height'],
					'version' => $getblock['version'],
					'time' => new \MongoDate ($getblock['time']),
					'difficulty' => $getblock['difficulty'],
				);

				$txid = 0;					
				foreach($getblock['tx'] as $txx){
					if($txx!=""){
						$getrawtransaction = $coin->getrawtransaction((string)$txx);
						$decoderawtransaction = $coin->decoderawtransaction($getrawtransaction);
						$txvin = 0;
						$data['txid'][$txid]['version'] = $decoderawtransaction['version'];
						$data['txid'][$txid]['txid'] = $decoderawtransaction['txid'];
						$data['txid'][$txid]['locktime'] = $decoderawtransaction['locktime'];						
						if($decoderawtransaction['vin']){
							foreach($decoderawtransaction['vin'] as $vin){
							if($vin['coinbase']){
								$data['txid'][$txid]['vin'][$txvin]['coinbase'] = $vin['coinbase'];
							}
							if($vin['vout']){
								$data['txid'][$txid]['vin'][$txvin]['vout'] = $vin['vout'];														
							}
							if( $vin['scriptSig']['asm']){
								$data['txid'][$txid]['vin'][$txvin]['scriptSig']['asm'] = $vin['scriptSig']['asm'];														
							}
							if( $vin['scriptSig']['hex']){
								$data['txid'][$txid]['vin'][$txvin]['scriptSig']['hex'] = $vin['scriptSig']['hex'];																					
							}
							if($vin['sequence']){
								$data['txid'][$txid]['vin'][$txvin]['sequence'] = $vin['sequence'];						
							}
									$txvin ++;
							}	
						}else{print_r("No vin\n");}
						$txvout = 0;				
						if($decoderawtransaction['vout']){
							foreach($decoderawtransaction['vout'] as $vout){
								$data['txid'][$txid]['vout'][$txvout]['value'] = $vout['value'];
								$data['txid'][$txid]['vout'][$txvout]['n'] = $vout['n'];
								$data['txid'][$txid]['vout'][$txvout]['scriptPubKey']['addresses'] = $vout['scriptPubKey']['addresses'];						
								$data['txid'][$txid]['vout'][$txvout]['scriptPubKey']['asm'] = $vout['scriptPubKey']['asm'];													
								$data['txid'][$txid]['vout'][$txvout]['scriptPubKey']['hex'] = $vout['scriptPubKey']['hex'];																				
								$data['txid'][$txid]['vout'][$txvout]['scriptPubKey']['reqSigs'] = $vout['scriptPubKey']['reqSigs'];																				
								$data['txid'][$txid]['vout'][$txvout]['scriptPubKey']['type'] = $vout['scriptPubKey']['type'];													
								$txvout++;			
							}
						}else{print_r("No vout\n");}
					}
				$txid ++;											
				}
			
				Bitblocks::create()->save($data);
				
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$pageendtime = $mtime;
	$pagetotaltime = ($pageendtime - $pagestarttime);
	print_r($pagetotaltime."-".$getblock['height'])	;
	print_r("\n");
			}
		}
	}
}
?>