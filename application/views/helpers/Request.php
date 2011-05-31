<?php

class Zend_View_Helper_Request
{
    //protected $_action;
    protected $_request;
    
    function __construct()
    {
        $fc = Zend_Controller_Front::getInstance();
        $this->_request = $fc->getRequest();
        //$this->_action =  $request->getActionName();
    }
    
    function request($com)
    {
      $com=ucfirst($com);
      $method='get'.$com."Name";
      //print $method;
      $res=$this->_request->$method();
      //print $res;
      return $res;
    }
}
