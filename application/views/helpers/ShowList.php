<?php
class Zend_View_Helper_ShowList
{
  public $view;
  public function setView(Zend_View_Interface $view)
  {
    $this->view = $view;
  }

  public function showList($val,$header1,$header2='',$sep='<br>'){
    static $listId=0;
    $listId++;
    if (isset($val)){
      $arr=explode($sep,$val);
      $size=sizeof($arr);
      if ($size>0){
        //$style="height: 60px;overflow: auto;border:solid thin #ACC798;";
        //$style="height: 0px;overflow: auto"; Comentado por GC
        $style="display: none"; // Modificado por GC
      }
      ?>
<br>
<?php if ($size>0){?>
<div class='list-headers' style='cursor: pointer'
	id="list-header-<?=$listId?>"><span id='list-sign-<?=$listId?>'>+</span><span
	class="t1 list-headers"><?=$header1?></span><? if ($header2) print ": ".$header2;?></div>
<div id='list-size-<?=$listId?>' style='display:none;'><?=$size?></div>
<?php }else{?>
<div ><span	class="t1 list-headers"><?=$header1?></span>:<? if ($header2) print $header2;?></div>
<?php }?>
<ul class="ul1" id='list-body-<?=$listId?>' style="<?=$style?> ">
<?php

foreach($arr as $rn=>$row){
  if (trim($row)=="*"){
   $rowStr='<hr>';
  }elseif (trim($row)=="<p>") {
    $rowStr='';
  } else {
   $rowStr=html_entity_decode($row, ENT_QUOTES, "utf-8");
  }
  if ($rowStr) {
?>
	<li><?=$rowStr?></li>
<?php
  }
}
?>
</ul>
<?php
  }
}

}