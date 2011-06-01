<?php

$str="Printed by  Rotaprint Bürodruckmaschinen, Annagasse";

$arr=explode('  ',$str);
//$arr=mb_split("@  @",$str);
print_r($arr);

function hw_trim(&$str){
  $str=trim($str);
}

//array_walk($tmpArr,'hw_trim');

//print_r($tmpArr);


?>