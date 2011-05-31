<?php
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Front Controller plug in to set up the view with the Apa view helper
 * path and some useful request variables.
 *
 */
class Hw_Controller_Plugin_ViewSetup extends Zend_Controller_Plugin_Abstract
{
  /**
   * @var Zend_View
   */
  protected $_view;

  public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
  {
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
    $viewRenderer->init();

    $view = $viewRenderer->view;

    ZendX_JQuery::enableView($view);
    $view->jQuery()->enable();
    $this->_view = $view;
    // set up common variables for the view
    $viewRenderer->view->baseUrl = $request->getBaseUrl();
    $viewRenderer->view->module = $request->getModuleName();
    $ctrlName=$viewRenderer->view->controller = $request->getControllerName();
    $viewRenderer->view->action = $request->getActionName();

    // setup initial head place holders
    $view->headMeta()->setName('Content-Type', 'text/html;charset=utf-8');
    $view->headMeta()->setHttpEquiv("content-language","en");
    $view->headMeta()->setName("author","Galor Design - Web Development, Tel Aviv, 972 3 6204082");
    $view->headMeta()->setName("web_author","Unisol, 972 9 9567496");
    $view->headScript()->captureStart();
    ?>
if (screen.width<=800){
   document.write('<link	type="text/css" rel="stylesheet" href="/css/style_800.css" />');
 }else if(screen.width>1024 ){
   document.write('<link	type="text/css" rel="stylesheet" href="/css/style_1280.css" />');
 }
    <?php
    $view->headScript()->captureEnd();

    $view->headScript()->appendFile("/js/swfobject.js");
    $view->headScript()->appendFile("/js/scripts.js");

    $this->_config=Zend_Registry::get('config');
    //print $this->_config->admin_controller;

    $adminControllers=explode(',', $this->_config->admin_controller);
    if(in_array($ctrlName, $adminControllers)){
      $view->headLink()->appendStylesheet($view->baseUrl . '/css/site.css');
    }else{
      $view->headLink()->appendStylesheet($view->baseUrl . '/css/style_home.css');
      $view->headLink()->appendStylesheet($view->baseUrl . '/css/style_inside.css');

    }
    // add helper path to View/Helper directory within this library
    $prefix = 'Apa_View_Helper';
    $dir = dirname(__FILE__) . '/../../View/Helper';
    $view->addHelperPath($dir, $prefix);

    $sess = Zend_Registry::get ( 'nsHw' );
    $lang = $sess->lang;
    if ($lang=='en'){
     $sfx='';
    }else{
     $sfx="_$lang";
    }
    $view->addScriptPath(ROOT_DIR."/application/views/scripts$sfx");
    $view->addHelperPath(ROOT_DIR."/application/views/helpers");
  }


  public function postDispatch(Zend_Controller_Request_Abstract $request)
  {
    $this->_controllerCSSLink($request);

    if (!$request->isDispatched()) {
      return;
    }
    $view = $this->_view;

    if (count($view->headTitle()->getValue()) == 0) {
      $view->headTitle($view->title);
    }
    $view->headTitle()->setSeparator(' - ');
    $view->headTitle('Hundertwasser');
  }

  protected function _controllerCSSLink(Zend_Controller_Request_Http $request)
  {
    $view = $this->_view;
    $controller = $request->getControllerName();

    // ensure we don't add a link that already exists
    $currentLinks = array();
    foreach($this->_view->headLink() as $link)
    {
      if($link->rel == "stylesheet") {
        $currentLinks[] = $link->href;
      }
    }

    // screen
    $file = ROOT_DIR . '/public_html/css/' . $controller . '.css';
    if (file_exists($file)) {
      $url = $this->_view->baseUrl . '/css/' . $controller . '.css';
      if(!in_array($url, $currentLinks)) {
        $this->_view->headLink()->appendStylesheet($url , 'screen,projection');
      }
    }

    // print
    $file = ROOT_DIR . '/public_html/css/' . $controller . '.print.css';
    if (file_exists($file)) {
      $url = $this->_view->baseUrl . '/css/' . $controller . '.print.css';
      if(!in_array($url, $currentLinks)) {
        $this->_view->headLink()->appendStylesheet($url , 'print');
      }
    }
  }
}