<?php

/**
 * Front Controller plug in to set up the action stack.
 *
 */
class Hw_Controller_Plugin_ActionSetup extends Zend_Controller_Plugin_Abstract
{
  /*
   public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
   {
   $front = Zend_Controller_Front::getInstance();
   if (!$front->hasPlugin('Zend_Controller_Plugin_ActionStack')) {
   $actionStack = new Zend_Controller_Plugin_ActionStack();
   $front->registerPlugin($actionStack, 97);
   } else {
   $actionStack = $front->getPlugin('Zend_Controller_Plugin_ActionStack');
   }
   //die();

   //print 'controllerName:'.$request->getControllerName();
   //die();


   //        $menuAction = clone($request);
   //        $menuAction->setActionName('menu')
   //                ->setControllerName('index');
   //        $actionStack->pushStack($menuAction);
   //
   //        $advertAction = clone($request);
   //        $advertAction->setActionName('advert')
   //                ->setControllerName('index');
   //        $actionStack->pushStack($advertAction);
   }
   */
  public function preDispatch(Zend_Controller_Request_Abstract $request) {
    $ctrlName=$request->getControllerName();
    $actName=$request->getActionName();
//    var_dump($ctrlName,$actName);
//    die();
    //$front = Zend_Controller_Front::getInstance();

    //die($ctrlName);
    //    if (in_array($ctrlName, array('epidermis','clothes','houses','identity','earth'))){
    //      $this->_forward('list-mt','skin',null,array('id'=>$ctrlName));
    //    }elseif($ctrlName=='highlights' && (int)$actName>0){
    //      $this->_forward('list-hl','skin',null,array('id'=>$actName));
    //    }

    //print $ctrlName."<br>";
    if (!in_array($actName,array('js','css','images','hwdb'))){
      switch($ctrlName){

        case 'search':
          $this->_forward('search','item',null,array());
          break;
        case 'oeuvre':
          $this->_forward('oeuvre','item');	
          break;  

        case 'epidermis':
        case 'clothes':
        case 'houses':
        case 'identity':
        case 'earth':
          if (strpos($actName,'related_')===0){
            $tmpArr=explode('_',$actName);
            //print_r($tmpArr);
            $this->_forward('list','item',null,array('type'=>$tmpArr[1],'parent'=>'skin','parent_id'=>$ctrlName));
          }else{
            $this->_forward('list-mt','skin',null,array('id'=>$ctrlName));
          }
          break;
        case 'highlights':
          $this->_forward('list-hl','skin',null,array('id'=>$actName));
          break;

        case 'mt':
          //die('MT');

          //die($type);
          if ($related=$request->getParam('related')){

            //print_r($tmpArr);
            //print "'type'=>$related,'parent'=>'mt','parent_id'=>$actName";
            $this->_forward('list','item',null,array('type'=>$related,'parent'=>'mt','parent_id'=>$actName));
          }

          break;


        case 'text':
          $tmpArr=explode('-',$actName);
          if ($tmpArr[0]=='view'){
            $this->_forward('view','item',null, array('id'=>$tmpArr[1],'type'=>$ctrlName));
          }elseif($tmpArr[0]=='slides'){
            $this->_forward('slides','item',null, array('id'=>$tmpArr[1],'type'=>$ctrlName));
          }elseif(stristr($actName,'_related-')){
            $tmpArr2=explode('_',$actName);
            $tmpArr3=explode('-',$tmpArr2[1]);
            $type=$tmpArr3[1];
            $this->_forward('list','item',null, array('parent'=>$ctrlName,'parent_id'=>$tmpArr2[0],'type'=>$type));
          }else{
            $this->_forward('view','texts',null,array('id'=>$actName,'hl'=>$request->getParam('hl')));
          }
          break;

        case 'hl':
        case 'paint':
        case 'hwg':
        case 'tapestry':
        case 'apa':
        case 'arch':
        case 'tap':
        case 'jw':
        case 'video':
        case 'audio':
        case 'gallery':
        case 'article': //Agregado por GC
        case 'document': //Agregado por GC
        case 'picture': //Agregado por GC

          if ($actName=='index' || $actName=='list' || $actName==''){
            $this->_forward('list','item',null, array('type'=>$ctrlName));

          }else{
            $tmpArr=explode('-',$actName);
            if ($tmpArr[0]=='view'){
              $this->_forward('view','item',null, array('id'=>$tmpArr[1],'type'=>$ctrlName));
            }elseif($tmpArr[0]=='slides'){
              $this->_forward('slides','item',null, array('id'=>$tmpArr[1],'type'=>$ctrlName));
            }else{
              $tmpArr2=explode('_',$actName);
              $tmpArr3=explode('-',$tmpArr2[1]);
              $type=$tmpArr3[1];
              $this->_forward('list','item',null, array('parent'=>$ctrlName,'parent_id'=>$tmpArr2[0],'type'=>$type));
            }
          }
          break;

      }
    }

  }

  /**
   * duplicated from Zend_Controller_Action
   * @param $action
   * @param $controller
   * @param $module
   * @param $params
   * @return unknown_type
   */
  final protected function _forward($action, $controller = null, $module = null, array $params = null)
  {
    $request = $this->getRequest();

    if (null !== $params) {
      $request->setParams($params);
    }

    if (null !== $controller) {
      $request->setControllerName($controller);
      // Module should only be reset if controller has been specified
      if (null !== $module) {
        $request->setModuleName($module);
      }
    }

    $request->setActionName($action)
    ->setDispatched(false);
  }
  
  
  public function postDispatch(Zend_Controller_Request_Abstract $request){
  	if (DEBUG_INFO==true){
  	$request = $this->getRequest();
  	$act=$request->getActionName();
  	$ctrl1=$request->getControllerName();
  	$ctrl=ucfirst($ctrl1);
  	$filt=new Zend_Filter();
  	$filt->addFilter(new Zend_Filter_Word_DashToCamelCase());
  	$actStr=$filt->filter($act);
  	$uri = $request->getRequestUri();
  	
  	$sess = Zend_Registry::get ( 'nsHw' );
  	$lang = $sess->lang;
  	if ($lang=='en'){
     $sfx='';
    }else{
     $sfx="_$lang";
    }
    if (!isset($sess->last_urls) or !is_array($sess->last_urls)) {
        $sess->last_urls = array();
        
    }
    $history = $sess->last_urls; 
    
    if (substr($uri,0,13) != '/image/thumb/') {
        //array_unshift($history, $uri);
        //$history = array_slice($history, 0, 5);
        $history[] = $uri;
        if (sizeof($history)>5)  array_shift($history);
        $sess->last_urls = $history;
    }
    
  	
  	$str="$ctrl/$act\n
  	view file: application/views/scripts$sfx/$ctrl1/$act.phtml\n
  	controller file: application/controllers/{$ctrl}Controller.php/{$actStr}Action()";
  	fb($str,"Debug Info");
  	fb($uri, "Last url");
  	}
  	
  }
  
  
  
}

