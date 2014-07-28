<?php
namespace app\controllers;
use app\models\Pages;

class APIController extends \lithium\action\Controller {
	public function index(){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		return compact('title','keywords','description');	
	}
}
?>