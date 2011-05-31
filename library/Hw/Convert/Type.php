<?php
class Hw_Convert_Type{




public function isInt($val){

  if (is_string($val)){
    $valInt=(int)$val;
    $str=(string)$valInt;
    if ($val==$str){
      $return = $valInt;
    }else{
      $return = $val;
    }
  }else{
    $return = $val;
  }
  if (is_int($return)) return true;
}


}
?>