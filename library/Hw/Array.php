<?php
class Hw_Array{

  private $arr=array();

  public function setArray($arr){
    $this->arr=$arr;
  }

  public function getColumn($fld,$arr=array()){
    if ($arr){
      $arr1=$arr;
    }else{
      $arr1=$this->arr;
    }

    if ($arr1){
      foreach($this->arr as $i=>$row){
        $resArr[]=$row[$fld];
      }

      return $resArr;
    }else{
      return array();
    }



  }

}
?>