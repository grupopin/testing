<?php

class Zend_View_Helper_BaseUrl
{
    protected $_baseUrl;
    
    function __construct()
    {
        $fc = Zend_Controller_Front::getInstance();
        $request = $fc->getRequest();
        $this->_baseUrl =  $request->getBaseUrl();
    }
    
    function baseUrl()
    {
        return $this->_baseUrl;
    }
}
