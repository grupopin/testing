<?php
class Hw_GridConfig{
  public $id,
  $itemType="skin",
  $child=null,
  $childArr=array(),//put here same values like in $child, in same order
  $parent=null,
  $syncWith=null,
  $allItemsGrid=null,
  $allItemsGridId=null,
  $selectedItemsGrid=null,
  $subGrid=false,
  $selectedGridId=null,
  $hlEditor=false,
  $gridHeight=150,
  $gridWidth=370,
  $gridCaption='Item';
}


?>