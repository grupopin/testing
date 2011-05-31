<?php
class Zend_View_Helper_TextDiv
{
	public $view;
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
	function textDiv($fldName,$fldTitle,$row,$lang,$display='none',$style=null)
	{
		$str='';
		$txt=$row[$fldName.'_'.$lang];
		if ($txt){
			$out= $txt;
		}else{
			$out= $row[$fldName];
		}
		if ($out){
			if (is_null($style)){
				$style="vertical-align:top; overflow:auto; width:450px; height:130px; display:$display;";
			}
			$str="<div id='txtdiv_$fldName' style='$style'>";
			$str.="<font face=\"Verdana\" size=\"2\" color=\"#74C828\">".$fldTitle.'</font>:'.nl2br($this->view->hlpFormatText($out,512,'...',false));
			$str.="</div><br style=\"line-height: 5px;\">";
		}
		return $str;
	}
}
?>