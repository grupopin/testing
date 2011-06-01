<?php

$dbName="hwarch_db";
$table="galery_utf8";
$targetTable="kb_trans_gallery";

$ver="0.01";
//$tidy=true;

/*******************************************/
//ini_set("display_errors","on");
//error_reporting(E_ALL);
$rootDir = dirname(dirname(__FILE__));
define('ROOT_DIR', $rootDir);

set_include_path(get_include_path()
. PATH_SEPARATOR . ROOT_DIR . '/library/'
. PATH_SEPARATOR . ROOT_DIR . '/application/models/'
);
//print get_include_path();
//include 'Zend/Loader.php';

//spl_autoload_register(array('Zend_Loader', 'autoload'));
/***********************************************************/

if ($tidy){
	require_once("Hw/Tidy.php");
}


$dh=mysql_connect('localhost','hwarch','hwarch');
if (!$dh){
  exit("No access!");
}
mysql_select_db($dbName,$dh);
mysql_query("SET NAMES UTF8");


$qs="select
  galery_id,
	galery_lang,
	galery_title_ge,
	galery_place_ge,
	galery_place_en,
	galery_image,
	galery_description_ge,
	galery_title_en,
	galery_description_en,
	galery_year,
	galery_year_circa,
	galery_photographer,
	galery_keywords,
	galery_copyright,
	thumb_path

	FROM galery_utf8 as g LEFT JOIN thumb as t ON g.galery_id=t.object_id ";

$q=mysql_query($qs);
while($res=mysql_fetch_assoc($q)){
  foreach($res as $fld=>$val){
    $row[$fld]=htmlentities($val, ENT_QUOTES,'UTF-8');
  }
  $qs1="INSERT INTO kb_trans_gallery (term_head, term_arg1, term_arg2,term_arg3,term_list) VALUES ('object','gallery',{$row['galery_id']},'{$row['galery_title_ge']}',
  \"[\nid-{$row['galery_id']},\n
  typeOfObject-'gallery',\n
  gallery-{$row['galery_id']},\n
  image_path-'{$row['thumb_path']}',\n
  gallery_title_en-'{$row['galery_title_en']}',\n
  gallery_title_ge-'{$row['galery_title_ge']}',\n
  gallery_description_ge-'{$row['galery_description_ge']}',\n
  gallery_description_en-'{$row['galery_description_en']}',\n
  gallery_year-'{$row['galery_year']}',\n
  gallery_year_circa-'{$row['galery_year_circa']}',\n
  gallery_photographer-'{$row['galery_photographer']}',\n
  gallery_keywords-'{$row['galery_keywords']}',\n
  galery_place_ge-'{$row['galery_place_ge']}',\n
  galery_place_en-'{$row['galery_place_en']}',\n
  gallery_copyright-'{$row['galery_copyright']}',\n
  galery_image-'{$row['galery_image']}'\n
  ]\")";
print $qs1."<br>";
  $q1=mysql_query($qs1);


}





