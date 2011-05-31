<?php
class Zend_View_Helper_HlpFormatText{
	public $view;
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
	public function hlpFormatText($str,$limit=100,$end='...',$escape=true){
		$str=iconv('iso-8859-1','utf-8',$str);
		$len=mb_strlen($str);
		if ($len>$limit){
			$str2=mb_substr($str,0,$limit).$end;
				
		}else{
			$str2=$str;
		}
    if ($escape){
    	$str2=$this->view->escape($str2);
    }
		return $str2;
	}

}
?>