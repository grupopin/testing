<?php
class Zend_View_Helper_TextDivLink
{
    function textDivLink($fldName,$fldTitle)
    {
    
     return "<a href=\"javascript:;\" onclick=\"switchDiv('$fldName');\">$fldTitle</a><br clear=all/>";
    	
    }
}
?>