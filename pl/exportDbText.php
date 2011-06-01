<?php

$dbName="hwarch_db";
$table="text";
$keyField="text_id";

$ver="1.03";
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

$fpOut=fopen("kbase/sql_{$table}_{$ver}.pl",'w+');

$dh=mysql_connect('localhost','hwarch','hwarch');
if (!$dh){
  exit("No access!");
}
mysql_select_db($dbName,$dh);
mysql_query("SET NAMES UTF8");

$qs="SELECT * FROM `$table` ORDER BY `$keyField`";
$q=mysql_query($qs);
while($row=mysql_fetch_assoc($q)){
  foreach($row as $fld=>$val){
    $fld=strtolower($fld);
    if ($fld==$keyField) continue;
    $val=tryToInt($val);
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

    $termArr[$fld].=$fld."(".$row[$keyField].','.$val.').'."\n";

  }
//break;
}
//print_r($row);

//print_r($termArr);
$fileName=__FILE__;
$cmt=<<<EOD
 /*
 This file is generated from $dbName,
 table: $table
 script: $fileName
 */
EOD;

fwrite($fpOut,$cmt."\n");
foreach($termArr as $line){
  fwrite($fpOut,$line."\n");
}

fclose($fpOut);


/*
 *
 * FUNCTIONS
 *
 *
 */
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