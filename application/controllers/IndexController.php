<?php

class IndexController extends Hw_Controller_Action
{
	public function indexAction()
	{
		
		$this->view->title="Hundertwasser";
		$this->_helper->layout->setLayout('index');
	}
	
	public function landingAction()
	{
	    $this->view->title="Hundertwasser";
		$this->_helper->layout->setLayout('landing');
	}

	public function menuAction()
	{
		$mainMenu = array(
		array('title'=>'Home', 'url'=>$this->view->baseUrl().'/'),
		array('title'=>'Browse Apa', 'url'=>$this->view->baseUrl().'/apa/browse'),
		array('title'=>'News', 'url'=>$this->view->baseUrl().'/news/browse'),
		array('title'=>'About', 'url'=>$this->view->baseUrl().'/html/about/'),
		array('title'=>'Contact', 'url'=>$this->view->baseUrl().'/html/contactus/'),
		array('title'=>'Help', 'url'=>$this->view->baseUrl().'/html/help/'),
		);

		$this->view->menu = $mainMenu;
		$this->_helper->viewRenderer->setResponseSegment('menu');
	}

	public function advertAction()
	{

		$this->_helper->viewRenderer->setResponseSegment('advert');
	}
}
