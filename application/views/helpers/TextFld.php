<?php
class Zend_View_Helper_TextFldValue
{
	function textFldValue($fldName,$curLang,&$row)
	{
		$out='';
		$txt=$row[$fldName.'_'.$curLang];
		if ($txt){
			$out= $txt;
		}else{
			$out= $row[$fldName];
		}
		return $out;
	}
}
?>