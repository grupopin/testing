<?php
class Zend_View_Helper_Path
{
  public $view;
  public function setView(Zend_View_Interface $view){
    $this->view = $view;
  }

  public function path($pathArr=array()){
    if ($pathArr){
      $len=sizeof($pathArr);

      foreach($pathArr as $i=>$el){
        if ($i==$len-1){
          $class="l4on";
        }else{
          $class="l4 b";
        }
        if ($el[1]){
          $links[]="<a href='{$el[1]}' class='$class'>{$el[0]}</a>";
        }else{
          $links[]="<span class='$class'>{$el[0]}</span>";
        }
      }
      if ($links){
        $linksHtml=implode("&nbsp;&gt;&nbsp;",$links);
      }
    }

    return $linksHtml;

  }




}