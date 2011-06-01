<?php
error_reporting(E_ALL);
ini_set("display_errot",1);
mb_internal_encoding("utf-8");
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

$path="/home/hwarch/domains/hw-archive.com/pl/kbase/onto/current/";
$fp=fopen($path."objects.pl",'r');


//print_r($fp);

//$files[$file] = preg_replace('/[^0-9]/', '', $file); # Timestamps may not be unique, file names are.




$i=0;
while(false!==($line=fgets($fp))){
   //print $line."\n";
  if (trim($line)==''){
    continue;
  }

  $i++;
  print $i.',';
  if (mb_strpos($line,"%")===0){
    $data=array();
    //parse
    //%Levan###2009-6-28 8:40:38.5778###object###text###1.1.1.2###A LETTER FROM PARIS
    $state="start";
    //print $state;
    $startLine=$i;
    //print "start...\n";
    //$objects[]
    $tmpArr=explode("###",$line);
    if (!($tmpArr[2]=='object' && $tmpArr[3]=='text' && $tmpArr[4] && $tmpArr[5] && empty($tmpArr[6]))){
      die("Error: object format is wrong in Line ".$i."\n");
    }

    $user=substr_replace($tmpArr[0],'',0,1);
    $timeArr=preg_split("@[\s\-\:]{1}@i",$tmpArr[1]);
    //print_r($timeArr);

    $time=mktime($timeArr[3],$timeArr[4],round($timeArr[5]),$timeArr[1],$timeArr[2],$timeArr[0]);

    //overwriting old objects
    $data['term_head']=$tmpArr[2];
    $data['term_arity']=4;
    $data['term_arg1']=$tmpArr[3];
    $data['term_arg2']=$tmpArr[4];
    $data['term_arg3']=$tmpArr[5];
    $data['term_arg4']='';
    $data['term_list']='';
    $data['created_by']=$user;
    $data['created_on']=date("Y-m-d H:i:s");
    $data['created_ip']='localhost';
    $data['term_active']=1;



  }

  if ($i>3 && !$state){
    die("Something wrong, no state defined");
  }

  if ($state=='start' && $i==$startLine+1 && strpos($line,'[')===0){
    $state="objectStart";
    $data['term_list']=$line;
    $object[$tmpArr[4]]=$data;
    $data=array();

  }


  //print $state.'('.$state1.')';

  //          if ($i>20){
  //            break;
  //          }
}
fclose($fp);

//foreach($object as $fld=>$obj){
//  print $fld."\n";
//}

//now find same objects in KB_trans table and deactivate them, and new one insert into db and activate
require_once 'KbTrans.php';
$kb=new KbTrans();
$suc=true;
foreach($object as $objKey=>$objData){
  print "term_arg2='$objKey'\n";
  $cond="term_head='object' && term_arg1='text' && term_arg2='$objKey'";
  $row=$kb->fetchRow($cond);
  //continue;

  if ($row->term_id>0){
    $kb->update(array('term_active'=>0),$cond);
    $res=$kb->insert($objData);
    $suc=$suc&&$res;
  }
}
if ($suc){
 $fp1=fopen($path."objects.pl",'w+');
 //flock($fp1,LOCK_EX);
 //fwrite($fp1,"");
 //flock($fp1,LOCK_UN);
 fclose($fp1);
}



?>