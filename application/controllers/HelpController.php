<?php

class HelpController extends Zend_Controller_Action
{
    public function indexAction()
    {
   // print_r($this->_request);
        $id = (int)$this->_request->getParam('id');
        if ($id == 0) {
            $this->_redirect('/');
            return;
        }

     

    }
    
    public function browseAction()
    {
        $newsFinder = new Help();
        $this->view->news = $newsFinder->fetchAll(null, 'help_title');
        $this->view->title = 'Help Zone !!!';
    }

    public function norouteAction()
    {
        // redirect to home page
        $this->_redirect('/');
    }
}