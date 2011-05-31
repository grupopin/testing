<?php
require_once 'Zend/Uri.php';

class Zend_View_Helper_ConstructUrl
{
	function constructUrl($varPath='', $varQry='', $uri=null)
	{
		    //$uri="http://hw.com/skin/earth/?#art1"; 
		//
		if (is_null($uri)){
			if ($_SERVER['QUERY_STRING']){
				$qryString='?'.$_SERVER['QUERY_STRING'];
			}
			$uri="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$qryString;
		}
		//print "uri:".$uri."<br>";
	  if (is_string($varPath)){
     parse_str($varPath,$arrPath); 
    }elseif(is_array($varPath)){
      $arrPath=$varPath; 
    }
    if (is_string($varQry)){
     parse_str($varQry,$arrQry);
    }elseif(is_array($varQry)){
     $arrQry=$varQry; 
    }

    $uriArr=parse_url($uri);
    if ($uriArr['path']{0}=='/') {
      $uriArr['path']=substr_replace($uriArr['path'],'',0,1);
    }
    $varArr=array();
    $varArr0=array();
    $varArr1=array();
    if ($uriArr['path']){ 
    $varArr=explode('/',$uriArr['path']);
    $iMax=sizeof($varArr);
    for($i=0;$i<$iMax;$i+=2){
      $varArr0[$varArr[$i]]=$varArr[$i+1];
    }
    }
    parse_str($uriArr['query'],$varArr1);

    $varArrPath=array_merge($varArr0,$arrPath);
    $iMax=sizeof($varArrPath);
    $newPath='';
    //print_r($varArrPath);
    
    foreach($varArrPath as $fld => $val){
    	if ($fld){
      $newPath.="$fld/".urlencode($val).'/';
    	}
    }
    //$newPath=implode('/',$varArrPath);


    $varArrQry=array_merge($varArr1,$arrQry);
    $newQry=http_build_query($varArrQry);

    //$newUri=$uriArr['scheme'].'://'.$uriArr['host'].'/'.$newPath.'?'.$newQry.$uriArr['fragment'];
    if ($uriArr['scheme']){
      $newUri.=$uriArr['scheme'].'://'; 
    }
    
    if ($uriArr['host']){
      $newUri.=$uriArr['host'].'/'; 
    }
    
    if ($newPath){
      $newUri.=$newPath; 
    }
    
    if ($newQry){
     $newUri.='?'.$newQry;
     
    }
    
    if ($uriArr['fragment']){
      if ($newQry){
        $newUri.="#".$uriArr['fragment'];
      }else{
        $newUri.="?#".$uriArr['fragment'];
      }
    }
		
		 
		return $newUri;
		
		
		
	}
}
?>