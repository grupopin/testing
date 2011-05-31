<?php
//same as view/helpers/L.php
class Hw_L {

  public function __(){
    $label=@func_get_arg(0);
    //print $label;
    $locale=@func_get_arg(1);
    if (!$locale){
      $locale=null;
    }
    if ($label){

    require_once('Zend/Registry.php');
    $trans=Zend_Registry::get('translate');
     //check if label does not exist in file, then add it to the file
     //
     if (!$trans->isTranslated($label,true,$locale)){
       $opts=$trans->getOptions();
       $fp=fopen(Zend_Registry::get('transFileName'),'ab');
       fputcsv($fp,array($label,$label),$opts['delimiter'],$opts['enclosure']);
       fclose($fp);
       require_once('Hw/Mail.php');
       $mail=new Hw_Mail("UTF-8");
       $mail->debugSend("new label for locale: $locale","There is a new label:<br>$label<br> for locale:$locale<br>file name: ".Zend_Registry::get('transFileName'));
     }
      return $trans->_($label,$locale);
    }else{
      return '';
    }

  }

}