<?php

class AudioController extends Hw_Controller_Action
{
    public function indexAction()
    {
        

        //$archsFinder = new Arch();
//        $this->view->arch = $archFinder->fetchByArchId($id);

    }
    
    public function itemAction(){
    	  //print_r($this->_request->getParams());
    	  //die();
    	  $id = (int)$this->_request->getParam('id');
        if ($id == 0) {
            $this->_redirect('/');
            return;
        }

        $audioFinder = new Audio();
        //print_r($audioFinder);
        //die();
        $audio = $audioFinder->fetchRow('audio_id='.$id);
        //print "audio_id={$audio->audio_id}\n";
        //die();
        if ($audio->audio_id != $id) {
            $this->_redirect('/');
            return;
        }
        //die();
        //print_r($audioFinder);
        //$this->view->apas = $apa;
        $this->view->title    = $audio->title;
        $this->view->contents = $audio->subtitle;
        $this->_helper->layout->setLayout('skin_results');
    }
    
    
    public function norouteAction()
    {
        // redirect to home page
        $this->_redirect('/');
    }
}