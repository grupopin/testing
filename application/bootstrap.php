<?php
class Bootstrap

{
  protected $dbg;
  private function dArr( $arr) {
    if ($this->dbg) {
      print_r ( $arr );
    }

  }

  public function __construct( $configSection = 'live') {
    //$this->dbg=$GLOBALS['dbg'];
    //die(__LINE__.'Test');



    $GLOBALS ['startTime'] = microtime ( true );

    $rootDir = dirname ( dirname ( __FILE__ ) );
    define ( 'ROOT_DIR', $rootDir );

    $docRoot=$rootDir.'/public_html';
    define('DOCROOT',$docRoot);
    //die($docRoot);

    set_include_path ( get_include_path () . PATH_SEPARATOR . ROOT_DIR . '/library/' . PATH_SEPARATOR . ROOT_DIR . '/application/models/' . PATH_SEPARATOR . ROOT_DIR . '/application/' );
    //print get_include_path();
    //include 'Zend/Loader.php';


    /*
     * For ZF 1.7
     */
    /*
     include 'Zend/Loader.php';
     @spl_autoload_register(array('Zend_Loader', 'autoload'));
     */
    /**
     * for ZF ver 1.8
     */

    require_once 'Zend/Loader/Autoloader.php';

    $autoloader = Zend_Loader_Autoloader::getInstance ();
    $autoloader->registerNamespace ( 'Pl_' );
    $autoloader->registerNamespace ( 'Hw_' );
    $autoloader->setFallbackAutoloader ( true );
    //$autoloader->registerNamespace('ZendX_');


    //@spl_autoload_register(array('Zend_Loader', 'autoload'));

    $ip=$_SERVER['REMOTE_ADDR'];

    //FIXME: saco el control de ip
//    
//    if ($ip!='85.130.239.124'){
//      $dt=date('Y-m-d');
//      $tm=date('Y-m-d H:i:s');
//      $agent=$_SERVER['HTTP_USER_AGENT'];
//      $mail=new Hw_Mail();
//      $mail->debugSend("hw-archive.com, ip:$ip, time: $dt",$tm.':     '.$_SERVER['REQUEST_URI']."<br>".$agent);
//    } else {
//    	ini_set("display_errors",1);
//    	error_reporting(E_ALL^E_NOTICE);
//    }

    	ini_set("display_errors",1);
    	ini_set("default_charset","utf-8");
    	ini_set("upload_max_filesize","10M");
    	
    	error_reporting(E_ALL^E_NOTICE);
    	
    
    
    //setup Json encoder to internal. As external did not succeed to work on big text files
    Zend_Json::$useBuiltinEncoderDecoder = true;
    // Load configuration
    Zend_Registry::set ( 'configSection', $configSection );

    $cfgArr = new Zend_Config_Ini ( ROOT_DIR . '/application/config.ini', $configSection );
    $config = new Zend_Config ( $cfgArr->toArray () );

    Zend_Registry::set ( 'config', $config );

    // print_r($config['works']);


    $confArr = parse_ini_file ( ROOT_DIR . '/application/config.ini', true );
    $docs = $confArr ['hw_docs'];
    $works = $confArr ['hw_works'];
    //print_r($docs);
    //$works=array_keys($config->works);
    //print_r($works);
    Zend_Registry::set ( 'works', $works );

    if ($this->dbg) {
      //print_r($config);
      try {
        if (Zend_Registry::isRegistered ( 'configSection' )) {
          $co = Zend_Registry::get ( 'configSection' );
        } else {
          print "configSection is not registered, please check that zend.ze1_compatibility_mode is set to 0";
        }
      } catch ( Exception $e ) {
        print $e->getMessage ();

      }
    }
    //print_r(Zend_Registry::get('works'));

    $nsHw  = new Zend_Session_Namespace ( 'hw' );
    $nsHwPages = new Zend_Session_Namespace ('hwPages');

    /**** START Language definition ****/
    $lang = @$_REQUEST ['lang'];
    if (! $nsHw->lang) {
      $nsHw->lang = 'en';
    }
    //locale names
    if (in_array ( $lang, array ('en', 'de', 'he' ) )) {
      $nsHw->lang = $lang;
    }

    $lang2to3['he']='heb';
    $lang2to3['en']='eng';
    $lang2to3['de']='ger';

    $lang2to2['he']='he';
    $lang2to2['en']='en';
    $lang2to2['de']='ge';

    $lang2ToWord['en']='English';
    $lang2ToWord['he']='Hebrew';
    $lang2ToWord['de']='German';
    $lang2ToWord['fr']='French';


    //$nsTp->lang='he';
    //if(in_array($lang, array('en','he','ru'))){
    //  $nsTp->lang = $lang;
    //}
    if ($nsHw->lang == 'he') {
      $langArr ['lang'] = 'he';
      $langArr ['lang_alt'] = 'heb';
      $langArr ['lang_alt_short'] = 'he';
      $langArr ['lang_word'] = $lang2ToWord[$langArr ['lang']];
      $langArr ['dir'] = 'rtl';
      $langArr ['align_left'] = 'right';
      $langArr ['align_right'] = 'left';


      $langObj->lang = 'he';
      $langObj->dir = 'rtl';
      $langObj->lang_alt = $lang2to3['he'];
      $langObj->lang_alt_short = $lang2to2['he'];
      $langObj->lang_word=$langArr ['lang_word'];
      $langObj->align_left = 'right';
      $langObj->align_right = 'left';
    } else {
      $langArr ['lang'] = $nsHw->lang;
      if ($lang2to3[$nsHw->lang]){
        $langArr ['lang_alt'] = $lang2to3[$nsHw->lang];
      }else{
        $langArr ['lang_alt']= $nsHw->lang;
      }

      if ($lang2to2[$nsHw->lang]){
        $langArr ['lang_alt_short'] = $lang2to2[$nsHw->lang];
      }else{
        $langArr ['lang_alt_short']= $nsHw->lang;
      }

      $langArr ['lang_word'] = $lang2ToWord[$langArr ['lang']];

      $langArr ['dir'] = 'ltr';
      $langArr ['align_left'] = 'left';
      $langArr ['align_right'] = 'right';

      $langObj->lang = $nsHw->lang;
      $langObj->lang_alt = $langArr ['lang_alt'];
      $langObj->lang_alt_short = $langArr ['lang_alt_short'];
      $langObj->lang_word=$langArr ['lang_word'];
      $langObj->dir = 'ltr';
      $langObj->align_left = 'left';
      $langObj->align_right = 'right';
    }

    $nsHw->langArr = $langArr;
    $nsHw->langObj = $langObj;

    /* stop langauge definition*/



    Zend_Registry::set ( 'docs', $docs );
    Zend_Registry::set ( 'tables', array_merge ( $works, $docs ) );
    Zend_Registry::set ( 'titles', $confArr ['hw_titles'] );
    Zend_Registry::set ( 'hw_skins', $confArr ['hw_skins'] );
    Zend_Registry::set ( 'hw_dates', $confArr ['hw_dates'] );
    Zend_Registry::set ( 'hw_work', $confArr ['hw_work'] );




    $translationOptions = array ('scan' => null, 'disableNotices' => true, 'delimiter' => "@" );
    $transFileName = $config->translateDir . "/labels_{$nsHw->lang}.csv.txt";
    Zend_Registry::set ( 'transFileName', $transFileName );
    Zend_Locale::setLocale($nsHw->lang);
    $translate = new Zend_Translate ( 'csv', $transFileName, $nsHw->lang, $translationOptions );
    Zend_Registry::set ( 'translate', $translate );

    require_once("config.php");

    //Zend_Registry::set('lang','en');
    //Zend_Registry::set('confArr',$confArr);


    date_default_timezone_set ( $config->date_default_timezone );

    // configure database and store to the registery
    $db = Zend_Db::factory ( $config->db );
    Zend_Db_Table_Abstract::setDefaultAdapter ( $db );
    Zend_Registry::set ( 'db', $db );

    //if my ip, then connect to second/ontology KB which is on port 3100
    //		if ($_SERVER['REMOTE_ADDR']=='82.81.147.236'){
    //		  //print_r(Zend_Registry::get ( 'config' )->pl2->params);
    //		  $kBase = new Pl_Adapter ( Zend_Registry::get ( 'config' )->pl2->params );
    //		}else{
    $kBase = new Pl_Adapter ( Zend_Registry::get ( 'config' )->pl->params );
    //		}
    Zend_Registry::set ( 'kBase', $kBase );

    //session
    //require_once 'Zend/Session/Namespace.php';

    $logFile = Zend_Registry::get ( 'config' )->logFiles->error;
    if ($logFile){
     $writer=new Zend_Log_Writer_Stream($logFile);
     //$writer->setFormatter( '<li>' . Zend_Log_Formatter_Simple::DEFAULT_FORMAT . '</li>');
     $log = new Zend_Log ( $writer );
    }else{
     $log=new Zend_Log();
    }

    if (Zend_Registry::get ( 'config' )->fireDebug) {
    	$writer = new Zend_Log_Writer_Firebug ( );
    	//$writer->setFormatter();
    	$log->addWriter ( $writer );
    }
    //print_r($log);
    Zend_Registry::set('logger',$log);



    //print $nsHw->lang;
    if (! isset ( $nsHw->initialized )) {
      Zend_Session::regenerateId ();
      $nsHw->initialized = true;
      //print "New session<br>";
    } else {
      //print "Initialized<br>";
    }

  if (! isset ( $nsHwPages->initialized )) {
      Zend_Session::regenerateId ();
      $nsHwPages->initialized = true;
      //print "New session<br>";
    } else {
      //print "Initialized<br>";
    }
    Zend_Registry::set ( 'nsHw', $nsHw );
    Zend_registry::set ( 'nsHwPages',$nsHwPages );

  }

  public function runApp() {
    $sess = Zend_Registry::get ( 'nsHw' );
    $lang = $sess->lang;
    
    
    //print $lang."<br>";
    // setup front controller
    $frontController = Zend_Controller_Front::getInstance ();
    $frontController->throwExceptions ( false );
    $frontController->setControllerDirectory ( ROOT_DIR . '/application/controllers' );

    $frontController->registerPlugin(new Hw_Controller_Plugin_ActionSetup());
    $frontController->registerPlugin(new Hw_Controller_Plugin_ViewSetup( ), 98 );

    Zend_Controller_Action_HelperBroker::addHelper(new ZendX_JQuery_Controller_Action_Helper_AutoComplete());

    // setup the layout
    Zend_Layout::startMvc ( array ('layoutPath' => ROOT_DIR . "/application/views/layouts/$lang" ) );

    // run!
    try {
      
      if (!$sess->landing) {
          $frontController->setDefaultAction("landing"); //agregado por GC
          $sess->landing=1;
      }      
      $frontController->dispatch ();
    } catch ( Exception $exception ) {
      // an exception has occurred after the ErrorController's postdispatch() has run
      if (Zend_Registry::get ( 'config' )->debug == 1) {
        $msg = $exception->getMessage ();
        $trace = $exception->getTraceAsString ();
        echo "<div>Error: $msg<p><pre>$trace</pre></p></div>";
      } else {
        try {
          $log=Zend_Registry::get('logger');

          $log->debug ( $exception->getMessage () . "\n" . $exception->getTraceAsString () . "\n-----------------------------" );
        } catch ( Exception $e ) {
          // can't log it - display error message
          $errMessage = $e->getMessage ();
          die ( "<p>An error occurred with logging an error!" . $errMessage );
        }
      }
    }
  }

}
function p($val,$lab){
  if (DEBUG==true){
    if (is_array($val)){
      print $lab."<br />";
      print_r($val);
      print "<br />";
    }elseif($val){

      print $lab.': '.$val.'<br />';
    }
  }
}
function fb($message, $label = null){
	if (!DEBUG && !DEBUG_INFO) return '';
  if($label != null){
    //$message = array($label,$message); Modified by GC
    $message = $label ." - ".$message; 
  }
  Zend_Registry::get('logger')->debug($message);
}

function hwGetPlace($file=__FILE__,$line=__LINE__){
	if (!DEBUG) return '';
	//$file1=str_replace("/home/hwarch/domains/hw-archive.com/",'',$file);
	$file1=str_replace("c:\proyectos\desarrollo\hw\\",'',$file);
	return $file1.':'.$line.'-';
}

function hwGetBackTrace($trace,$level=2){
	if (!DEBUG) return '';
	for($i=0;$i<$level;$i++){
		$argsStr=print_r($trace[$i]['args'],true);
		$file=str_replace("/home/hwarch/domains/hw-archive.com",'',$trace[$i]['file']);
		$debugStr.="$file:{$trace[$i]['line']} function {$trace[$i]['class']}->{$trace[$i]['function']}($argsStr)\n";
	}
	return $debugStr;
}

function requireOnce($path){
  if (!Zend_Registry::get('config')->autoload){
    //simple magic for lazy people :-)
    //for path like - Zend_Form_Element_Text => Zend/Form/Element/Text.php
    if (strpos($path,"/")===false){
      $classPath=str_replace("_",'/',$path).".php";
    }else{
      $classPath=$path;
    }
    require_once($classPath);
  }
}

function addShopItemTitles(&$itemArr,$shopArr,$fldName){

  foreach($shopArr as $cat=>$catProps){
    $itemArr[$catProps['term_name']]=$fldName;
  }

}