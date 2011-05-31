<?php
class Zend_View_Helper_Sess
{
  public $view;
  public function setView(Zend_View_Interface $view){
    $this->view = $view;
  }

  public function sess($var='',$fld=''){
   $sess = Zend_Registry::get('nsHw');
   if ($var){
     if ($fld){
       return $sess->$var[$fld];
     }else{
       return $sess->$var;
     }
   }else{
    return $sess;
   }
  }




}