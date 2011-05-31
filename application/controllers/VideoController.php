<?php

class VideoController extends Hw_Controller_Action
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

        $videoFinder = new Video();
        //print_r($audioFinder);
        //die();
        $video = $videoFinder->fetchRow('video_id='.$id);
        //print "audio_id={$audio->audio_id}\n";
        //die();
        if ($video->video_id != $id) {
            $this->_redirect('/');
            return;
        }
        //die();
        //print_r($audioFinder);
        //$this->view->apas = $apa;
        $this->view->title    = $video->title;
        $this->view->contents = $video->subtitle;
        $this->_helper->layout->setLayout('skin_results');
    }
    
    
    public function norouteAction()
    {
        // redirect to home page
        $this->_redirect('/');
    }
}