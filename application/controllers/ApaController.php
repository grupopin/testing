<?php

class ApaController extends Hw_Controller_Action
{
    public function indexAction()
    {
        $id = (int)$this->_request->getParam('id');
        if ($id == 0) {
            $this->_redirect('/');
            return;
        }

        $apaFinder = new Apa();
        $apa = $apaFinder->fetchRow('apa_id='.$id);
        if ($apa->apa_id != $id) {
            $this->_redirect('/');
            return;
        }
        $this->view->apas = $apa;
        $this->view->title = $apa->apa_title_en;

        //$archsFinder = new Arch();
//        $this->view->arch = $archFinder->fetchByArchId($id);

    }
    
    public function itemAction(){
//    	  //print_r($this->_request->getParams());
//    	  $id = (int)$this->_request->getParam('id');
//        if ($id == 0) {
//            $this->_redirect('/');
//            return;
//        }
//
//        $apaFinder = new Apa();
//        $apa = $apaFinder->fetchRow('apa_id='.$id);
//        if ($apa->apa_id != $id) {
//            $this->_redirect('/');
//            return;
//        }
//        $this->view->apas = $apa;
//        $this->view->title = $apa->apa_title_en;

    	parent::itemAction();

    }
    
    public function browseAction()
    {
        $apaFinder = new Apa();
        $this->view->apas = $apaFinder->fetchAll(null, 'apa_title_en');
        $this->view->title = 'Browse Apa';
		
		$newsFinder=new News();
		$this->view->news = $newsFinder->fetchLatest(10);
		$this->view->title_news="Latest News";
		
		$usersFinder=new Users();
		$this->view->users = $usersFinder->fetchLatest(2);
		$this->view->title_users="Latest Users";
		
		$form = new Zend_Form;
		$form->setAction('/login/do')     
			 ->setMethod('post');
		$form->setAttrib('id', 'login');
		$form->addElement('text', 'username');
		$form->addElement('submit', 'Send');
		$this->view->form=$form;

    }

    public function norouteAction()
    {
        // redirect to home page
        $this->_redirect('/');
    }
}