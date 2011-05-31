<?php
class Zend_View_Helper_L{
  public $view;
  public function setView(Zend_View_Interface $view)
  {
    $this->view = $view;
  }
  public function l(){
    $label=@func_get_arg(0);
    //print $label;
    $locale=@func_get_arg(1);
    if (!$locale){
      $locale=null;
    }
    if ($label){

    $trans=Zend_Registry::get('translate');
     //check if label does not exist in file, then add it to the file
     //
     if (!$trans->isTranslated($label,true,$locale)){
       $opts=$trans->getOptions();
       $fp=fopen(Zend_Registry::get('transFileName'),'ab');
       fputcsv($fp,array($label,$label),$opts['delimiter'],$opts['enclosure']);
       fclose($fp);
       //$mail=new Hw_Mail("UTF-8");
       //$mail->debugSend("new label for locale: $locale","There is a new label:<br>$label<br> for locale:$locale<br>file name: ".Zend_Registry::get('transFileName'));
       $str="There is a new label:($label) for locale:($locale) in file name: ".Zend_Registry::get('transFileName');
       fb($str);
     }
      return $trans->_($label,$locale);
    }else{
      return '';
    }

  }

}