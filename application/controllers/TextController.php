<?php

class TextController extends Hw_Controller_Action
{
  public function indexAction()
  {


    //$archsFinder = new Arch();
    //        $this->view->arch = $archFinder->fetchByArchId($id);
    $this->view->title = 'Skins:Text';
    $this->_helper->layout->setLayout('skins');

  }

  public function viewAction(){
    $hl=(int)$this->_getParam('hl',0);
    $id=$this->_getParam('id',0);

    print "text:$id, hl:$hl<br>";
    $this->view->title = 'Skins:Text';
    $this->_helper->layout->setLayout('skins');
  }

  public function itemAction(){

    parent::itemAction();


    //$this->_helper->layout->setLayout('skin_results');
  }


  public function norouteAction()
  {
    // redirect to home page
    $this->_redirect('/');
  }
}