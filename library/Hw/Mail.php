<?php
class Hw_Mail extends Zend_Mail{

  public function debugSend($subj='',$html=''){
    $this->setSubject($subj);
    $this->setBodyHtml($html,'UTF-8');

    $this->addTo("levan.cheishvili@gmail.com","levan");
    $this->send();
  }

}
?>