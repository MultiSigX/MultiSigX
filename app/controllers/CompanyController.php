<?php
namespace app\controllers;
use app\models\Pages;

class CompanyController extends \lithium\action\Controller {

	public function index(){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		return compact('title','keywords','description');	
	}
	public function contact(){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		return compact('title','keywords','description');	
	}
	public function aboutus(){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		return compact('title','keywords','description');	
	}
	public function risk(){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		return compact('title','keywords','description');	
	}
	public function verification(){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		return compact('title','keywords','description');	
	}
	public function privacy(){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		return compact('title','keywords','description');	
	}
	public function termsofservice(){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		return compact('title','keywords','description');	
	}
	public function legal(){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		return compact('title','keywords','description');	
	}
	public function FAQ(){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>$this->request->controller.'/'.$this->request->action)
		));

		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		return compact('title','keywords','description');	
	}
	public function how(){
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