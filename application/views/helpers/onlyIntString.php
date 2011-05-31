<?php

class Zend_View_Helper_OnlyIntString
{
    protected $_baseUrl;

    function __construct()
    {
        $fc = Zend_Controller_Front::getInstance();
        $request = $fc->getRequest();
        $this->_baseUrl =  $request->getBaseUrl();
    }

    function onlyIntString()
    {

        return $res;
    }
}
