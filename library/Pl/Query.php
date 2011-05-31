<?php
class Pl_Query{
  public $_plAdapter;
  protected $_params;
  protected $_method;
  public $result;

  public function __construct($method = null, $params = null){
    $this->_plAdapter=new Pl_Adapter ( Zend_Registry::get ( 'config' )->pl->params );
    if (isset($method)){
       $this->setMethod($method);
    }
    if (isset($params)){
      $this->setParams($params);
    }
    return $this;
  }

  public function getMethod(){
    return $this->_method;
  }

  public function getParams(){
    return $this->_params;
  }

  public function setMethod($method){
    if (method_exists($this->_plAdapter,$method)){
      $this->_method=$method;
    }else{
      throw new Zend_Exception('Invalid method set in Pl_Query');
    }
    return $this;

  }

  public function setParams(array $params){
    if (is_array($params)){
      $this->_params=$params;
    }else{
      throw new Zend_Exception('param array is empty!');
    }
    return $this;
  }

  public function limit($itemCountPerPage, $offset){
    $this->_limit=$itemCountPerPage;
    $this->_from=$offset;
  }

  public function runQuery(){
    if (is_array($this->_params) && (is_string($this->_method))){
      if ($this->_limit>0){
        $this->_params['limit']=$this->_limit;
      }
      if ($this->_from>=0){
        $this->_params['from']=$this->_from;
      }
      $res=call_user_func_array(array($this->_plAdapter,$this->_method),$this->_params);
      $this->result=$res;
    }
    return $this;
  }

  public function getResults(){
    if (!empty($this->result)){
      return $this->result;
    }else{
      $this->runQuery();
      if ($this->result){
        return $this->result;
      }
    }
  }

  /*
   public function __call($method,$params){
   if (is_array($params) && (is_string($method))){
   $res=call_user_func_array(array($this->_plAdapter,$method),$params);
   $this->result=$res;
   }
   return $this;
   }
   */










}