<?php
//exit("This script will overwrite data in kb_cat table!");


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
$kBase=new Pl_Adapter ( $params );
//print_r($kBase);
//$types=array('apa','arch','hwg','paint','jw','tap');
$types=array('tap');
foreach($types as $type){
  if ($type!='apa'){
  $catField=$searchFields[$type]['field'];
  $idField=$searchFields[$type]['id'];
  print $type.':'.$catField."<br>\n";
  $res=$kBase->selectOrderLimit($type,$idField,'',"[$idField,$catField]",0,10000);
  $data=$kBase->parseKbData($res);
  //print_r($data);

  foreach($data as $i=>$row){
    //print $row[$catField];
    $catStr=$row[$catField];
//    if ($type=='tap'){
//
//      $catNames=array();
//      preg_match_all('/\\b[A-Z]+[\\w\/&\/;]+\\b\\s+\\b[A-Z]+[\\w\/&\/;]+\\b/',$catStr,$names);
//      $catNames=$names[0];
//      print_r($catNames);
//      $tmpArr=explode(',',$catStr);
//      foreach($tmpArr as $j=>$part){
//        $part=trim($part);
//        if (in_array($part,$catNames) || preg_match_all('/wooven|wool|warp|silk|yarn|mohair|linen/',$part,$matches)){
//          continue;
//        }
//
//        $catNames[]=$part;
//      }
//      $catNames=array_unique($catNames);
//      foreach($catNames as $cname){
//        if ($cname && !$cats[$type][$cname]){
//          $cats[$type][$cname]=1;
//        }
//      }
//
//    }else{
//      $catName=$catStr;
//    }
    $catName=$catStr;
    if ($catName && !$cats[$type][$catName]){
      $cats[$type][$catName]=1;
    }

  }
  ksort($cats[$type]);

  }else{
    $fp=fopen("/home/hwarch/domains/hw-archive.com/faust_import/apa_cats.html",'r');
    $l=0;

    while(($line=fgets($fp,10000))!==false){
      $line=trim($line);
      $l++;
      if ($line=='') continue;
      $matches=array();

      $catNameEn='';
      $catNameDe='';
      //$res=preg_match('/(?P<cat_eng>[.\\w \/\-\,\:]+)\\t+(?P<cat_ger>[\\w \/\-\,\:]+)/',$line,$matches);
      $matches=preg_split('/\\t+/',$line);
      //print_r($matches);
      if (preg_match("@[A-Z]{3,}@",$matches[0])){
        //this is category line
        $catParEn=mysql_escape_string($matches[0]);
        $catParDe=mysql_escape_string($matches[1]);
      }else{
        $catNameEn=mysql_escape_string($matches[0]);
        $catNameDe=mysql_escape_string($matches[1]);
      }
      $qs="INSERT IGNORE INTO kb_cat
       (cat_par_en,
        cat_name_en,
        cat_par_de,
        cat_name_de,
        cat_type
      )
      VALUES (
        '$catParEn',
        '$catNameEn',
        '$catParDe',
        '$catNameDe',
        'apa'
      )
      ";
      $db->query($qs);
      //print $qs."\n";
      //if ($l==4) die();
    }


  }

}

foreach($cats as $type=>$catArr){
  $cnt=0;
  foreach($catArr as $catName=>$catVal){
    $cnt++;
    $catNameStr=mysql_escape_string($catName);
    $qs="INSERT INTO kb_cat (cat_name_en, cat_type) VALUES ('$catNameStr','$type')";
    //print $qs."\n";
    //if ($cnt>4) die();
     $db->query($qs);

  }
  print "insert $cnt records\n";

}

//print_r($cats);



