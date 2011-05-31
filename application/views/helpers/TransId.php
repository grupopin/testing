<?php
class Zend_View_Helper_TransId{
	public $view;
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
	function transId($id,$encode=true)
	{
	  $arr1=array('/',' ');
	  $arr2=array('slash','space');
	  if ($encode){
	    $str=str_replace($arr1,$arr2,$id);
	  }else{
	    $str=str_replace($arr2,$arr1,$id);
	  }

		return $str;
	}
}
