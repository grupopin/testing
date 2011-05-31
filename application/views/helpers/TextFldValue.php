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
		return iconv('iso-8859-1','utf-8',$out);
	}
}
?>