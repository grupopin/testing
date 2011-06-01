<?php

$dbName="hwarch_db";
$table="text";
$keyField="text_id";
$titleField="text_title_en";
$output='mysql';
//$output='file';
$debug=true;


$ver="1.03";
//$tidy=true;
$listJunctor="-"; //'=';

/*******************************************/
//ini_set("display_errors","on");
//error_reporting(E_ALL);
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
//print get_include_path();
//include 'Zend/Loader.php';

//spl_autoload_register(array('Zend_Loader', 'autoload'));
/***********************************************************/

if ($tidy){
  require_once("Hw/Tidy.php");
}

if ($output=='file'){
  $now=date("Y-m-d_His");
  $fpOut=fopen("/home/hwarch/domains/hw-archive.com/pl/kbase/onto/sql_{$table}_onto_{$now}.pl",'w+');
}else{
  if (!$debug){
    require_once 'KbTrans.php';
    $transTable=new KbTrans();
  }else{
    require_once 'KbTrans2.php';
    $transTable=new KbTrans2();
  }
}

$dh=mysql_connect('localhost','hwarch','hwarch');
if (!$dh){
  exit("No access!");
}
mysql_select_db($dbName,$dh);
mysql_query("SET NAMES UTF8");

//$tm1=microtime(true);
$qs="SELECT * FROM `$table` ORDER BY `TXT`";
$q=mysql_query($qs);
//while($row=mysql_fetch_assoc($q)){
//  $data[]=$row;
//}
//$tm2=microtime(true);
//
//$dT=$tm2-$tm1;
//
//die("time=$dT");

$txtArr=array();
while($row=mysql_fetch_assoc($q)){
  $data=array();
  $TXT=$row['TXT'];
  if (!$TXT){
    die("Critical error:record text_id={$row['text_id']}, TXT field is empty");
  }

  if (!trim($row[$titleField])){
    die("Critical error:record text_id={$row['text_id']}, $titleField field is empty");
  }
  if (array_search($TXT,$txtArr)===false){
    $txtArr[]=$TXT;
  }else{
    print "Warning: TXT: $TXT, text_id={$row['text_id']} is not unique\n";
  }
  $title=html_entity_decode($row[$titleField], ENT_QUOTES, "utf-8");
  $title=addslashes($title);

  if ($output=='file'){
    $now=date("Y-m-d H:i:s");
    $termStr="%Andrea###$now###object###text###{$row['TXT']}\n";
    fputs($fpOut,$termStr);
    $termStr="object(text,'{$row['TXT']}','$title',[\nid$listJunctor{$row['text_id']},\ntypeOfObject$listJunctor'TEXT',\n";
    fputs($fpOut,$termStr);
  }else{//mysql
    $data['term_head']='object';
    $data['term_arg1']='text';
    $data['term_arg2']=$row['TXT'];
    $data['term_arg3']=$title;
    $data['term_arity']=4;
    $data['term_list']="[\nid$listJunctor{$row['text_id']},\ntypeOfObject$listJunctor'TEXT',\n";
    $data['created_by']=2;//Archive/Andrea
    $data['created_on']=date("Y-m-d H:i:s");
    $data['created_ip']=($_SERVER['REMOTE_ADDR'])?($_SERVER['REMOTE_ADDR']):('localhost');
    $data['term_source']='arch';

  }

  $termArr=array();

  foreach($row as $fld=>$val){

    $fld=strtolower($fld);
    //if ($fld==$keyField) continue;
    $val=tryToInt($val);
    if (is_int($val) && $val<0){
      $val="$val";
    }
    if (is_string($val)){
      //$val=str_replace(array(chr(194),chr(160)),' ',$val);
      if (strpos($fld,"text_contents")===0 && $tidy){
        $val=Hw_Tidy::runTidy($val);
      }
      $val="'".htmlentities($val, ENT_QUOTES,'UTF-8',false)."'";
    }
    if (empty($val)){
      $val="''";
    }

    $termStr="";
    $prop=strToCamel($fld);
    //$val=wrapInQuotes($val);
    $termArr[]=$fld.$listJunctor.$val;

  }
  if ($output=='file'){
    if ($termArr){
      $termStr=implode(",\n",$termArr);
      fputs($fpOut,$termStr);
    }
    fputs($fpOut,"\n]).\n\n");
  }else{
    if ($termArr){
      $data['term_list'].=implode(",\n",$termArr);

    }
    $data['term_list'].="\n]";
    $transTable->insert($data);
  }
  //break;
}
//print_r($row);

if ($output=='file'){
  //print_r($termArr);
  $fileName=__FILE__;
  $cmt=<<<EOD
 /*
 This file is generated from $dbName,
 table: $table
 script: $fileName
 */
EOD;

  fclose($fpOut);
}


/*
 *
 * FUNCTIONS
 *
 *
 */




/**
 * warp string in single quotes. If they already exist, first remove them(it is faster to remove then search)
 * if $str is integer after single quotes are removed then do not wrap it. Integer does not need quotes in Pl.
 * @param $str
 * @return unknown_type
 */
function wrapInQuotes( $str){
  $str2=trim($str, "'");
  //print $str2."<br>";
  $str3=(int)$str2;
  //print $str3."<br>";
  if(strcmp($str3, $str2) === 0){
    return $str2;
  }elseif(is_string($str2) || empty($str2)){
    $str2="'" . $str2 . "'";
  }
  return $str2;
}

function upstr( &$str){
  $str=strtolower($str);
  $str=ucfirst($str);
}

function strToCamel( $str, $firstUpper=false){
  $arr=preg_split("@[\_\-\s]+@", $str);
  array_walk($arr, 'upstr');
  $newStr=implode('', $arr);
  if (!$firstUpper){
    $fletter=mb_substr($newStr,0,1);
    $fletter=strtolower($fletter);
    $newStr=substr_replace($newStr,$fletter,0,1);
  }
  return $newStr;
}
function tryToInt($val){

  if (is_string($val)){
    $valInt=(int)$val;
    $str=(string)$valInt;
    if ($val==$str){
      return $valInt;
    }else{
      return $val;
    }
  }else{
    return $val;
  }
}
?>