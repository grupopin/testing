<?php
#.htaccess is also used
#in case you need to open access to other then application files and folders, open access in .htaccess and close here
/*
$ips=array(
'91.113.251.186',//Archiv
'85.130.239.124',//me
'62.90.141.5',//server
'80.123.29.63',//Andrea - res
//'84.229.179.76',//hemy - res
'84.228.60.202',//hemy res
'79.179.37.182', //danny
'93.173.144.93',//Eran
'89.139.24.153',//Eran
'62.47.129.10', #Lorna 13/10/09
'62.47.151.113', ##Lorna 14/10/09
);
*/
//$ips=array('85.130.239.124','62.90.141.5');
////
//if (!in_array($_SERVER['REMOTE_ADDR'],$ips)){
//  print "your ip is ".$_SERVER['REMOTE_ADDR']."<br>";
//  exit("Site is on maintinance. Please send an email to admin");
//}

//ini_set('display_errors','On');
//error_reporting(E_ALL);
ini_set('zend.ze1_compatibility_mode',0);//set to make Registry working
// Set your Zend Framework library path(s) here - default is the master lib/ directory
//$lib = '/usr/local/lib/php';
//set_include_path(get_include_path() . PATH_SEPARATOR . $lib);
//print get_include_path();
//die();
$baseDir = dirname(dirname(__FILE__));
mb_internal_encoding("utf-8");

$debug=0;
$debugInfo=0;

define('DEBUG_INFO',$debugInfo);

$myIp=$_SERVER['REMOTE_ADDR'];
//$confSect='dev';
//die('Site is on maintenance');
if ($myIp=='85.130.239.124' && $debug){
  $confSect='dev';//with dev, error output is always to the screen, strange
  define('DEBUG',true);
}else{
  $confSect='general';
  define('DEBUG',false);
}


$BASE_PATH=dirname(__FILE__);
//print dirname(__FILE__)."<br>";
require_once ($BASE_PATH.'/../application/bootstrap.php');

// Specify your config section here

$configSection = getenv('ZF_CONFIG') ? getenv('ZF_CONFIG') : $confSect;
$bootstrap = new Bootstrap($configSection);
$bootstrap->runApp();









