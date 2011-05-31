<?php
class Zend_View_Helper_WorkImage
{
  public $view;
  public function setView(Zend_View_Interface $view){
    $this->view = $view;
  }

  /**
   * Finds first image of particular id
   *
   * @param $type
   * @param $size
   * @param $id
   * @param $mode
   * @param $series
   * @return unknown_type
   */
  public function workImage($type,$size='small',$id,$mode='list',$series=false,$sort='asc',$showNum=1,$pad=true,$params=array()){

    $hwImage=new Hw_Image();
   
    return $hwImage->workImage($type,$size,$id,$mode,$series,$sort,$showNum,$pad,$params);
  }




}
?>