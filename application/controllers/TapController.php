<?php

class TapController extends Hw_Controller_Action
{
    public function indexAction()
    {
        

        //$archsFinder = new Arch();
//        $this->view->arch = $archFinder->fetchByArchId($id);

    }
    
    public function itemAction(){
    	  //print_r($this->_request->getParams());
      parent::itemAction();
    }
    
    
    public function norouteAction()
    {
        // redirect to home page
        $this->_redirect('/');
    }
}