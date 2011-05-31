<?php
class Hw_Convert_Date extends Zend_Date{




  public function setDateConvert($dateStr,$inFormat='%d/%d/%d %d:%d:%d',$inFormatVars=array('day','month','year', 'hour','minute','second')){

    list($dateArr[$inFormatVars[0]],
         $dateArr[$inFormatVars[1]],
         $dateArr[$inFormatVars[2]],
         $dateArr[$inFormatVars[3]],
         $dateArr[$inFormatVars[4]],
         $dateArr[$inFormatVars[5]]
         )=sscanf($dateStr,$inFormat);


    $this->set($dateArr);

    //$dmail=new Tp_Mail("UTF-8");
    //$dmail->debugSend('dateArr',print_r($dateArr,true));
  }


}
?>