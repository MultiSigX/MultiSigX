<?php
namespace app\extensions\action;

use lithium\storage\Session;

use app\models\Users;
use app\models\Details;

use lithium\data\Connections;
use \lithium\template\View;

use \Swift_MailTransport;
use \Swift_Mailer;
use \Swift_Message;
use \Swift_Attachment;

class Functions extends \lithium\action\Controller {

	public function XGCFormat($value=null){
		$full = $value*10000000;
		$right = substr($full,-8,9);

		$Integer = floor($value);
		$Decimal = ($value - $Integer)*100;
		$DecimalTwo = floor($Decimal);
		$DecimalRest = substr(round($Decimal - $DecimalTwo,8),2,7);
		$valueString = (string)"<strong style='font-size:larger'>".$Integer.".".$DecimalTwo."</strong>".$DecimalRest;
		return $valueString;
	}

	public function roman($integer, $upcase = true){
		$table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1);
		$return = '';
		while($integer > 0){
			foreach($table as $rom=>$arb){
				if($integer >= $arb){
					$integer -= $arb;
					$return .= $rom;
					break;
				}
			}
		}
		return $return;
	} 



	function get_ip_address() {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
						return $ip;
					}
				}
			}
		}
	}

		

	public function toFriendlyTime($seconds) {
	  $measures = array(
		'year'=>24*60*60*30*12,	  	  
		'month'=>24*60*60*30,	  
		'day'=>24*60*60,
		'hour'=>60*60,
		'minute'=>60,
		'second'=>1,
		);
	  foreach ($measures as $label=>$amount) {
		if ($seconds >= $amount) {  
		  $howMany = floor($seconds / $amount);
		  return $howMany." ".$label.($howMany > 1 ? "s" : "");
		}
	  } 
	  return "now";
	}   

		public function number_to_words($number) {
	   
		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'one',
			2                   => 'two',
			3                   => 'three',
			4                   => 'four',
			5                   => 'five',
			6                   => 'six',
			7                   => 'seven',
			8                   => 'eight',
			9                   => 'nine',
			10                  => 'ten',
			11                  => 'eleven',
			12                  => 'twelve',
			13                  => 'thirteen',
			14                  => 'fourteen',
			15                  => 'fifteen',
			16                  => 'sixteen',
			17                  => 'seventeen',
			18                  => 'eighteen',
			19                  => 'nineteen',
			20                  => 'twenty',
			30                  => 'thirty',
			40                  => 'fourty',
			50                  => 'fifty',
			60                  => 'sixty',
			70                  => 'seventy',
			80                  => 'eighty',
			90                  => 'ninety',
			100                 => 'hundred',
			1000                => 'thousand',
			1000000             => 'million',
			1000000000          => 'billion',
			1000000000000       => 'trillion',
			1000000000000000    => 'quadrillion',
			1000000000000000000 => 'quintillion'
		);
	   
		if (!is_numeric($number)) {
			return false;
		}
	   
		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
				'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
				E_USER_WARNING
			);
			return false;
		}
	
		if ($number < 0) {
			return $negative . $this->number_to_words(abs($number));
		}
   
		$string = $fraction = null;
	   
		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}

		switch (true) {
			case $number < 21:
				$string = $dictionary[$number];
				break;
			case $number < 100:
				$tens   = ((int) ($number / 10)) * 10;
				$units  = $number % 10;
				$string = $dictionary[$tens];
				if ($units) {
					$string .= $hyphen . $dictionary[$units];
				}
				break;
			case $number < 1000:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
				if ($remainder) {
					$string .= $conjunction . $this->number_to_words($remainder);
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($number, 1000)));
				$numBaseUnits = (int) ($number / $baseUnit);
				$remainder = $number % $baseUnit;
				$string = $this->number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
				if ($remainder) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= $this->number_to_words($remainder);
				}
				break;
		}
	   
		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}
	   
		return $string;
	}
	
	
	public function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}
 
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map($this->objectToArray, $d);
		}
		else {
			// Return array
			return $d;
		}
	}
	
	public function arrayToObject($d) {
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return (object) array_map($this->arrayToObject, $d);
		}
		else {
			// Return object
			return $d;
		}
	}
	function sendEmailTo($email = null,$compact = null,$controller=null,$template=null,$subject=null,$from=null,$mail1 = null,$mail2 = null,$mail3 = null,$attach = null){

			$view  = new View(array(
			'loader' => 'File',
			'renderer' => 'File',
			'paths' => array(
				'template' => '{:library}/views/{:controller}/{:template}.{:type}.php'
			)
		));
			$body = $view->render(
				'template',
				compact('compact'),
				array(
					'controller' => $controller,
					'template'=>$template,
					'type' => 'mail',
					'layout' => false
				)
			);

			$transport = Swift_MailTransport::newInstance();
			$mailer = Swift_Mailer::newInstance($transport);
	
			$message = Swift_Message::newInstance();
			$message->setSubject($subject);
			$message->setFrom($from);
			$message->setTo($email);
			if($attach!=null){
				$swiftAttachment = Swift_Attachment::fromPath($attach);
				$message->attach($swiftAttachment);
			}
			if($mail1!=null){			
				$message->addBcc($mail1);			
			}
			if($mail2!=null){			
				$message->addBcc($mail2);			
			}
			if($mail3!=null){
				$message->addBcc($mail3);		
			}
			$message->setBody($body,'text/html');
			$mailer->send($message);

	
	}

function SMS($mobile,$msg){
		$smsdata = array(
			'user' => SMSLANE_USERNAME,
			'password' => SMSLANE_PASSWORD,
			'msisdn' => str_replace("+","", $mobile),
			'sid' => SMSLANE_SID,
			'gwid'=> 2,
			'msg' => $msg,
			'fl' =>"0",
		);
	
		$sms = new Smslane();		

		list($header, $content) = $sms->SendSMS(
			"http://www.smslane.com/vendorsms/pushsms.aspx", // the url to post to
			"http://".$_SERVER['HTTP_HOST']."/users/sms", // its your url
			$smsdata
		);
		
//		print_r($header);
//		print_r($content);
	}
	
function WorldSMS($mobile,$msg){
		$smsdata = array(
			'user' => WORLDSMSLANE_USERNAME,
			'password' => WORLDSMSLANE_PASSWORD,
			'msisdn' => str_replace("+","", $mobile),
			'sid' => WORLDSMSLANE_SID,
			'msg' => $msg,
			'fl' =>"0",
		);
	
		$sms = new Smslane();		

		list($header, $content) = $sms->worldSMS(
			"http://world.smslane.com/vendorsms/GlobalPush.aspx", // the url to post to
			"http://".$_SERVER['HTTP_HOST']."/users/sms", // its your url
			$smsdata
		);
		
//		print_r($header);
//		print_r($content);
	}
	
	public function getChilds($user_id){
	#Retrieving a Full Tree
	/* 	SELECT node.user_id
	FROM details AS node,
			details AS parent
	WHERE node.lft BETWEEN parent.lft AND parent.rgt
		   AND parent.user_id = 3
	ORDER BY node.lft;
	
	parent = db.details.findOne({user_id: ObjectId("50f19bc39d5d0ce409000012")});
	query = {left: {$gt: parent.left, $lt: parent.right}};
	select = {user_id: 1};
	db.details.find(query,select).sort({left: 1})
	 */
		$ParentDetails = Details::find('first',array(
			'conditions'=>array(
			'user_id' => $user_id
			)));

			$left = $ParentDetails['left'];
			$right = $ParentDetails['right'];

			$NodeDetails = Details::find('all',array(
			'conditions' => array(
				'left'=>array('$gt'=>$left),
				'right'=>array('$lt'=>$right)
			)),
			array('order'=>array('left'=>'ASC'))
		);
		return $NodeDetails;
	}
	
	public function countChilds($user_id){
	#Retrieving a Full Tree
	/* 	SELECT node.user_id
	FROM details AS node,
			details AS parent
	WHERE node.lft BETWEEN parent.lft AND parent.rgt
		   AND parent.user_id = 3
	ORDER BY node.lft;
	
	parent = db.details.findOne({user_id: ObjectId("50e876e49d5d0cbc08000000")});
	query = {left: {$gt: parent.left, $lt: parent.right}};
	select = {user_id: 1};
	db.details.find(query,select).sort({left: 1})
	 */
		$ParentDetails = Details::find('first',array(
			'conditions'=>array(
			'user_id' => $user_id
			)));
		
			$left = $ParentDetails['left'];
			$right = $ParentDetails['right'];
		$NodeDetails = Details::count(array(
			'conditions' => array(
				'left'=>array('$gt'=>$left),
				'right'=>array('$lt'=>$right)
			))
		);
		return $NodeDetails;
	}
	
	public function levelOneChild($user_id){
		$ParentDetails = Details::find('first',array(
			'conditions'=>array(
			'user_id' => $user_id
			)));
	
	$children = Details::find("all",array(
		'conditions'=>array('ancestors'=>$ParentDetails['username'])
	));
	$single = 0;
	foreach($children as $child){
		if(count($child['ancestors'])==1){
			$single++;
		}
	}
	return $single;
	}
	
	public function getParents($user_id){
	#Retrieving a Single Path above a user
	/* SELECT parent.user_id
	FROM details AS node,
			details AS parent
	WHERE node.lft BETWEEN parent.lft AND parent.rgt
			AND node.user_id = 10
	ORDER BY node.lft;
	
	node = db.details.findOne({user_id: ObjectId("50e876e49d5d0cbc08000000")});
	query = {left: {$gt: node.left, $lt: node.right}};
	select = {user_id: 1};
	db.details.find(query,select).sort({left: 1})
	 */
			$NodeDetails = Details::find('first',array(
				'conditions'=>array(
				'user_id' => $user_id
			)));
			$left = $NodeDetails['left'];
			$right = $NodeDetails['right'];

			$ParentDetails = Details::find('all',array(
				'conditions' => array(
					'left'=>array('$lt'=>$left),
					'right'=>array('$gt'=>$right)
				)),
				array('order'=>array('left'=>'ASC'))
			);
		return $ParentDetails;
	}	

	public function countParents($user_id){
	#Retrieving a Single Path above a user
	/* SELECT parent.user_id
	FROM details AS node,
			details AS parent
	WHERE node.lft BETWEEN parent.lft AND parent.rgt
			AND node.user_id = 10
	ORDER BY node.lft;
	
	node = db.details.findOne({user_id: ObjectId("50e876e49d5d0cbc08000000")});
	query = {left: {$gt: node.left, $lt: node.right}};
	select = {user_id: 1};
	db.details.find(query,select).sort({left: 1})
	 */
			$NodeDetails = Details::find('first',array(
				'conditions'=>array(
				'user_id' => $user_id
			)));

			$left = $NodeDetails['left'];
			$right = $NodeDetails['right'];

			$ParentDetails = Details::count(array(
				'conditions' => array(
					'left'=>array('$lt'=>$left),
					'right'=>array('$gt'=>$right)
				))
			);
		return $ParentDetails;
	}	
	
}
?>