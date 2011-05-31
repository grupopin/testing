<?php
class Zend_View_Helper_Translate{
  public $view;
  public function setView(Zend_View_Interface $view)
  {
    $this->view = $view;
  }
  public function translate(){
    $label=@func_get_arg(0);
    //print $label;
    $locale=@func_get_arg(1);
    if (!$locale){
      $locale=null;
    }
    if ($label){
    $trans=Zend_Registry::get('translate');
      return $trans->_($label,$locale);
    }else{
      return '';
    }
 
  }

}