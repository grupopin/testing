<?php
//exit("This script will overwrite data in kb_wn table!");
//CONFIGURE!!!

$types=array('apa','arch','hwg','paint','jw','tap');
//$types=array('paint');
//$field='workNumber';
$field='workId';

/*******************************************/
ini_set("display_errors","on");
error_reporting(E_ALL^E_NOTICE);
$rootDir = dirname(dirname(dirname(__FILE__)));
define('ROOT_DIR', $rootDir);

set_include_path(get_include_path()
. PATH_SEPARATOR . ROOT_DIR . '/library/'
. PATH_SEPARATOR . ROOT_DIR . '/application/models/'
);

require_once 'Zend/Config/Ini.php';
$cfgArr = new Zend_Config_Ini ( ROOT_DIR . '/application/config.ini', 'live' );
$config = new Zend_Config ( $cfgArr->toArray () );

require_once('Zend/Db/Table/Abstract.php');

$db = Zend_Db::factory ( $config->db );
Zend_Db_Table_Abstract::setDefaultAdapter ( $db );

require_once('Pl/Adapter.php');

$params=$cfgArr->pl->params;
require_once(ROOT_DIR . '/application/searchConfig.php');
//print_r($params);
//die();
//print_r($db);
//die();
$kBase=new Pl_Adapter ( $params );
//print_r($kBase);

foreach($types as $type){

  //$catField=$searchFields[$type]['field'];
  $idField=$searchFields[$type][$field];

  $res=$kBase->selectOrderLimit($type,$idField,'',"[$idField]",0,10000);
  $data=$kBase->parseKbData($res);
  //print_r($data);

  foreach($data as $i=>$row){
    //print $row[$catField];
    $catStr=$row[$idField];

    $catName=$catStr;


    if ($catName && !$cats[$type][$catName]){
      $cats[$type][$catName]=1;
    }

  }
  ksort($cats[$type]);



}

foreach($cats as $type=>$catArr){
  $cnt=0;
  foreach($catArr as $catName=>$catVal){
    $cnt++;
    $catNameStr=mysql_escape_string($catName);
    $qs="INSERT IGNORE INTO kb_field (field_val,field_type,item_type) VALUES (?,?,?)";
    //print $qs."\n";
    //if ($cnt>4) die();
    $res=$db->query($qs,array($catNameStr,$field,$type));
    //print_r($res);
    //die();

  }

}

//print_r($cats);



