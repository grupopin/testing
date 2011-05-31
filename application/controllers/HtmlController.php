<?php
  class HtmlController extends Hw_Controller_Action 
{
    //private function __call(){
//    parent::__call();
//    print "123";$this->_request->getParam('action');
//    
//    
//    }
    
    public function indexAction()
    {
        

    }
    
    public function aboutAction(){
     
    
    }
    
    public function contactusAction(){
     
     
    
    }
    
    public function homeAction(){
    	
    }
    
    public function browseAction(){
        
    }
    public function helpAction(){
     
    
    }


    public function norouteAction()
    {
        // redirect to home page
        //print $this->_request->getParam('action');
        $this->_redirect('/');
    }
}
?>
