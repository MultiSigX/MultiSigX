<?php
namespace app\controllers;
use lithium\storage\Session;
use app\models\Users;
use app\models\Details;
use app\models\Parameters;

use MongoID;
use MongoDate;

use lithium\data\Connections;

class AdminController extends \lithium\action\Controller {


	public function __init(){
		$user = Session::read('member');
		
		$id = $user['_id'];
		$details = Details::find('first',
			array('conditions'=>array('user_id'=>$id))
		);
		if(strtolower(str_replace("@","",strstr($user['email'],"@")))==strtolower(COMPANY_URL) 
			&& $details['email.verified']=="Yes"
			&&	( 
						strtolower(MAIL_1)==strtolower($user['email'])
			|| strtolower(MAIL_2)==strtolower($user['email'])
			|| strtolower(MAIL_3)==strtolower($user['email']) 	
			|| strtolower(MAIL_4)==strtolower($user['email'])
						)
		){
			return true;
		}else{
			return false;
		}
	}

	public function index() {
		if($this->__init()==false){$this->redirect('ex::dashboard');	}
		$user = Session::read('member');
		$id = $user['_id'];

		if($this->request->data){
					$StartDate = new MongoDate(strtotime($this->request->data['StartDate']));
					$EndDate = new MongoDate(strtotime($this->request->data['EndDate']));			
		}else{
			$StartDate = new MongoDate(strtotime(gmdate('Y-m-d H:i:s',mktime(0,0,0,gmdate('m',time()),gmdate('d',time()),gmdate('Y',time()))-60*60*24*30)));
			$EndDate = new MongoDate(strtotime(gmdate('Y-m-d H:i:s',mktime(0,0,0,gmdate('m',time()),gmdate('d',time()),gmdate('Y',time()))+60*60*24*1)));
		}

		
		$mongodb = Connections::get('default')->connection;
		$UserRegistrations = Users::connection()->connection->command(array(
			'aggregate' => 'users',
			'pipeline' => array( 
				array( '$project' => array(
					'_id'=>0,
					'created' => '$created',
				)),
				array( '$match' => array( 'created'=> array( '$gte' => $StartDate, '$lte' => $EndDate ) ) ),
				array('$group' => array( '_id' => array(
						'year'=>array('$year' => '$created'),
						'month'=>array('$month' => '$created'),						
						'day'=>array('$dayOfMonth' => '$created'),												
				),
						'count' => array('$sum' => 1), 
				)),
				array('$sort'=>array(
					'_id.year'=>-1,
					'_id.month'=>-1,
					'_id.day'=>-1,					
//					'_id.hour'=>-1,					
				)),
			)
		));
		$TotalUserRegistrations = Users::connection()->connection->command(array(
			'aggregate' => 'users',
			'pipeline' => array( 
				array( '$project' => array(
					'_id'=>0,
					'created' => '$created',
				)),
				array('$group' => array( '_id' => array(
						'year'=>array('$year' => '$created'),
				),
						'count' => array('$sum' => 1), 
				)),
				array('$sort'=>array(
					'_id.year'=>-1,
				)),
			)
		));
		
		$new = array();
		
  $days = ($EndDate->sec - $StartDate->sec)/(60*60*24);
		for($i=0;$i<=$days;$i++){
			$date = gmdate('Y-m-d',($EndDate->sec)-$i*60*60*24);
			$new[$date] = array();
		}
		
		foreach($UserRegistrations['result'] as $UR){
			$URdate = date_create($UR['_id']['year']."-".$UR['_id']['month']."-".$UR['_id']['day']);			
			$urDate = date_format($URdate,"Y-m-d");
				$new[$urDate] = array(
					'Register'=> $UR['count']
				);
		}
		return compact('new');
	}
	
		public function map(){
		if($this->__init()==false){$this->redirect('ex::dashboard');}	
		if($this->__init()==true){	
			$mongodb = Connections::get('default')->connection;
			$IPDetails = Details::connection()->connection->command(array(
				'aggregate' => 'details',
				'pipeline' => array( 
					array( '$project' => array(
						'_id'=>0,
						'ip' => '$lastconnected.IP',
						'iso' => '$lastconnected.ISO',					
					)),
					array('$group' => array( '_id' => array(
							'iso'=> '$iso',
					),
							'count' => array('$sum' => 1), 
					)),
				)
			));
			
					$details = Details::find('all',array(
				'conditions'=>array('lastconnected.loc'=>array('$exists'=>true)),
				'fields'=>array('lastconnected.loc','lastconnected.ISO'),
				'sort'=>array('lastconnected.ISO'=>'ASC')
			));
		}
		return compact('IPDetails','details');
	}
	public function down(){
		if($this->__init()==false){			$this->redirect('ex::dashboard');	}
		if($this->__init()==true){	
			$data = array(
			'server' => (boolean)false
			);
			Parameters::find('all')->save($data);
		}
		return compact('$data');
	}
	public function up(){
		if($this->__init()==false){	$this->redirect('ex::dashboard');	}
		if($this->__init()==true){	
			$data = array(
			'server' => (boolean)true
			);
			Parameters::find('all')->save($data);
		}
		return compact('$data');
	}	
	public function CheckServer(){
		if($this->__init()==true){
				return $this->render(array('json' => array(
					'Refresh'=> 'Yes',
				)));
		}
		if($this->__init()==false){			
			$parameters = Parameters::find('first');
			if($parameters['server']==true){
				return $this->render(array('json' => array(
					'Refresh'=> 'Yes',
				)));
			}else{
				return $this->render(array('json' => array(
					'Refresh'=> 'No',
				)));
			}
		}
	}
	
}
?>