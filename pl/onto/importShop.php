<?php
//exit('Before import, please check that category names in source table do not have symbols other than alfanum and space,/,(,)<br>\nPlease improve the script').
//$importTo='kb_trans';
$importTo='kb_cat';


//ini_set('display_errors','On');
//error_reporting(E_ALL);
ini_set('zend.ze1_compatibility_mode',0);//set to make Registry working
// Set your Zend Framework library path(s) here - default is the master lib/ directory
//$lib = '/usr/local/lib/php';
//set_include_path(get_include_path() . PATH_SEPARATOR . $lib);
//print get_include_path();
//die();
$baseDir = dirname(dirname(dirname(__FILE__)));
mb_internal_encoding("utf-8");

//$confSect='dev';
//die('Site is on maintenance');
$confSect='general';



//print dirname(__FILE__)."<br>";
require_once ($baseDir.'/application/bootstrap.php');

// Specify your config section here

$configSection = getenv('ZF_CONFIG') ? getenv('ZF_CONFIG') : $confSect;
$bootstrap = new Bootstrap($configSection);





$ver="0.01";
//$tidy=true;


if ($tidy){
  require_once("Hw/Tidy.php");
}



$dh=mysql_connect('localhost','da_admin','d3GjcxQt');
if (!$dh){
  exit("No access!");
}
$dbName="hwarch_db";
mysql_select_db($dbName,$dh);
mysql_query("SET NAMES UTF8");

/*
 $qs=<<<EOD
 INSERT INTO kb_trans (term_head, term_arg1, term_arg2,term_arg3,term_list,term_arity,created_on,created_ip, created_by)
 select 'object', convert(lower(replace(replace(replace(replace(cat.category,' ','_'),'/','_'),')','_'),'(','_')) using utf8) collate utf8_unicode_ci, c.productid, convert(p.descr using utf8) collate utf8_unicode_ci, concat("[\n",
 "category-'",c.categoryid,"',\n",
 "category_name-'",convert(cat.category using utf8) collate utf8_unicode_ci,"',\n",
 "id-'",c.productid,"',\n",
 "descr-'",convert(p.descr using utf8) collate utf8_unicode_ci,"',\n",
 "image_path-'",i.image_path,"'\n"
 "]\n"),
 4,now(),'localhost',1
 FROM hwarch_shop.xcart_products as p LEFT JOIN hwarch_shop.xcart_products_categories as c ON p.productid=c.productid
 LEFT JOIN hwarch_shop.xcart_categories as cat ON c.categoryid=cat.categoryid
 LEFT JOIN hwarch_shop.xcart_images_T as i ON p.productid=i.id ORDER BY cat.category;

 EOD;
 mysql_query($qs);
 */

$qs="select
  c.categoryid as categoryid, convert(cat.category using utf8) collate utf8_unicode_ci as cat_name, c.productid as productid, convert(p.descr using utf8) collate utf8_unicode_ci as descr,i.image_path as imagepath
	FROM hwarch_shop.xcart_products as p LEFT JOIN hwarch_shop.xcart_products_categories as c ON p.productid=c.productid
  LEFT JOIN hwarch_shop.xcart_categories as cat ON c.categoryid=cat.categoryid
  LEFT JOIN hwarch_shop.xcart_images_T as i ON p.productid=i.id ORDER BY cat.category";

$q=mysql_query($qs);
while($res=mysql_fetch_assoc($q)){
  foreach($res as $fld=>$val){
    if ($fld=='cat_name'){
      $row['objName']=strtolower(preg_replace('/[\\W]+/','_',$val));//replace all non word chars
    }

    $row[$fld]=htmlentities($val, ENT_QUOTES,'UTF-8');
  }
  if ($importTo=='kb_cat'){
     $qs1="INSERT INTO kb_cat (cat_name_en, cat_type) VALUES ('{$row['cat_name']}','shop')";

  }elseif($importTo=='kb_trans'){

    $qs1="INSERT INTO kb_trans (term_head, term_arg1, term_arg2,term_arg3,term_list,term_arity,created_on,created_ip, created_by)
  VALUES ('object','{$row['objName']}','{$row['productid']}','{$row['cat_name']}',
  \"[\nid-{$row['productid']},\n
  typeOfObject-'{$row['cat_name']}',\n
  categoryid-'{$row['categoryid']}',\n
  image_path-'{$row['imagepath']}',\n
  descr-'{$row['descr']}'\n
  ]\",4,NOW(),'localhost',1)";

  }
  //print $qs1."<br>";
  $q1=mysql_query($qs1);
  //die();
}

//die();


//$dataConfig=Zend_Registry::get('dataConfig');
//print_r($dataConfig);





