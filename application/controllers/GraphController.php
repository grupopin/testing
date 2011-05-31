<?php

class GraphController extends Hw_Controller_Action
{
    public function indexAction()
    {
        

        //$archsFinder = new Arch();
//        $this->view->arch = $archFinder->fetchByArchId($id);

    }
    
    public function itemAction(){
    	  //print_r($this->_request->getParams());
//    	  $id = (int)$this->_request->getParam('id');
//        if ($id == 0) {
//            $this->_redirect('/');
//            return;
//        }
//
//        $graphFinder = new Graph();
//        $graph = $graphFinder->fetchRow('graph_id='.$id);
//        if ($graph->graph_id != $id) {
//            $this->_redirect('/');
//            return;
//        }
//        //$this->view->apas = $apa;
//        $this->view->title = $arch->title_en;
        parent::itemAction();
        
    }
    
    
    public function norouteAction()
    {
        // redirect to home page
        $this->_redirect('/');
    }
}