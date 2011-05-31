<?php
//field - is name of field use for search by keywords
$searchFields=array(
'paint'=>array(
'id'=>'workNumber',
'workNumber'=>'workNumber',
'workId'=>'workNumber',
'field'=>'techniqueKeyword',
'year'=>'year',
'free_text'=>array('title','title'.$langArr['lang_word'],'finishedPlace','date','hundertwasser_comment_'.$langArr['lang_alt_short'],'information_'.$langArr['lang_alt_short']),
'order_by'=>'workNumber',
'order_sort'=>'asc'
),

'jw'=>array(
'id'=>'jw',
'workNumber'=>'workNumber',
'workId'=>'jw',
'field'=>'techniqueKeyword',
'year'=>'year',
'free_text'=>array('title','title'.$langArr['lang_word'],'finishedPlace','date','hundertwasser_comment_'.$langArr['lang_alt_short'],'information_'.$langArr['lang_alt_short']),
'order_by'=>'jw',
'order_sort'=>'asc'
),

'hwg'=>array(
'id'=>'hwg',
'field'=>'techniqueKeyword',
'workNumber'=>'workNumber',
'workId'=>'hwg',
'year'=>'publishedYear',
'free_text'=>array('title','title'.$langArr['lang_word'],'finishedPlace','date','hundertwasser_comment_'.$langArr['lang_alt_short'],'information_'.$langArr['lang_alt_short']),
'order_by'=>'hwg',
'order_sort'=>'asc'
),

'tap'=>array(
'id'=>'tap',
'field'=>'wovenBy',
'workNumber'=>'workNumber',
'workId'=>'tap',
'default'=>array(),
'year'=>'year',
'free_text'=>array('title','title'.$langArr['lang_word'],'finishedPlace','date','wovenBy','hundertwasser_comment_'.$langArr['lang_alt_short'],'information_'.$langArr['lang_alt_short']),
'order_by'=>'tap',
'order_sort'=>'asc'
),

 'apa'=>array(
 'id'=>'apa',
 'workNumber'=>'workNumber',
'workId'=>'apa',
 'field'=>'descriptionCategoryKeyword',
 'year'=>'year',
 'free_text'=>array('title','title'.$langArr['lang_word'],'finishedPlace','date','hundertwasser_comment_'.$langArr['lang_alt_short'],'information_'.$langArr['lang_alt_short']),
 'order_by'=>'apa',
 'order_sort'=>'asc'
 ),

 'arch'=>array(
 'id'=>'arch',
 'workNumber'=>'workNumber',
 'workId'=>'arch',
 'field'=>'descriptionCategoryKeyword',
 'year'=>'year',
 'free_text'=>array('title','title'.$langArr['lang_word'],'finishedPlace','date','hundertwasser_comment_'.$langArr['lang_alt_short'],'information_'.$langArr['lang_alt_short']),
 'order_by'=>'arch',
 'order_sort'=>'asc'
 ),
 
 'text'=>array(
 'id'=>'txt',
 'workNumber'=>'workNumber',
 'workId'=>'txt',
 'field'=>'text_keywords',
 'year'=>'year',
 'free_text'=>array('title','title'.$langArr['lang_word'],'finishedPlace','date','hundertwasser_comment_'.$langArr['lang_alt_short'],'information_'.$langArr['lang_alt_short']),
 'order_by'=>'txt',
 'order_sort'=>'asc'
 ),
 
 );