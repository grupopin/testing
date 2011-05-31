<?php

class SearchController extends Hw_Controller_Action
{
	public function indexAction()
	{


		$this->_helper->layout->setLayout('skins');
	}







	public function norouteAction()
	{
		// redirect to home page
		$this->_redirect('/');
	}
}