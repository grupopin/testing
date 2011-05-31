<?php

class Zend_View_Helper_DisplayDate
{
    function displayDate($timestamp, $format='%d %B %Y')
    {
        return strftime($format, strtotime($timestamp));
    }
}