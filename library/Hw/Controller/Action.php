<?php

class Hw_Controller_Action extends Zend_Controller_Action{


  public $_kBase;
  public $_itemTitles;
  public $_itemIds;
  public $_fields;
  protected
   $_session,
   $_config,
   $_dataConfig,
   $_langCode,
   $_subGridFld,
   $_nsHwPages;

  public function __call( $method, $args){

    if('Action' == substr($method, -6)){
      $controller=$this->getRequest()->getControllerName();
      $url='/' . $controller . '/index';
      return $this->_redirect($url);
    }
    throw new Exception('Invalid method');

  }

  protected function strToArr($str,$sep=','){
    if (stristr($str,',')){
      $strArr=explode(',',$str);
      foreach($strArr as $strName){
        $strArr2[]=trim($strName);
      }
    }else{
      $strArr2[]=trim($str);
    }
    return $strArr2;
  }

  protected function getArrayParam($param='type',$types=array('apa','arch','hwg','jw','paint','tap')){
    $typeParam=trim($this->_getParam($param));
    if (!$typeParam) return false;
    $typeArr=explode(',',$typeParam);
    foreach($typeArr as $typVal){
      $typVal=trim($typVal);
      if ($typVal){
        $type[]=trim($typVal);
      }
    }
    $type=array_intersect($type,$types);
    $type=array_unique($type);
    return $type;
  }

  protected function wrapTermStr($str){
    if (preg_match("~^\d+$~",$str,$match)){
      return $str;
    }else{
      return "'$str'";
    }
  }

  private function checkLogin(){
    //$ns=Zend_Registry::get('nsHw');
    //$config=Zend_Registry::get('config');
    //print_r($config->be_admin_ps);
    //print $this->_getParam('ps');


    if(!is_array($this->_session->user)){
      $users=$this->_config->user->toArray();
      if($this->_getParam("ps")){
        //$found=false;
        foreach($users as $uid=>$uprops){
          if($uprops['ps'] == $this->_getParam('ps')){
            $this->_session->user=$uprops;
            $this->_session->user['id']=$uid;
            //$from=urldecode($this->_getParam("from"));
            return true;
          }
        }

      }else{
        //print "No access";
        return false;
      }
    }
    return true;
  }
  
	public function postDispatch(){
	 $layout=$this->_helper->layout->getLayout();
	 $lang=$this->_session->langObj->lang;
	 if (DEBUG_INFO==true){
	   $str="layout:/application/views/layouts/$lang/$layout.phtml";
	   fb($str,'debug info');
	 }
	}
  
  function init(){
    parent::init();
    $this->baseUrl=$this->_request->getBaseUrl();
    //for _kBase to be available for hinting, we assign it to Pl_Adapter here.
    //$this->_kBase=Zend_Registry::get("kBase");
    $this->_kBase=new Pl_Adapter ( Zend_Registry::get ( 'config' )->pl->params );
    $this->_itemTitles=Zend_Registry::get("itemTitles");
    $this->_itemIds=Zend_Registry::get("itemIds");
    $this->_fields=Zend_Registry::get("fields");
    $this->_session=Zend_Registry::get('nsHw');
    $this->_nsHwPages=Zend_Registry::get('nsHwPages');
    $this->_config=Zend_Registry::get('config');
    $this->_dataConfig=Zend_Registry::get('dataConfig');
    $this->_langCode=$this->_session->langArr['lang_alt_short'];
    $this->_subGridFld=Zend_Registry::get('subGridFld');

    //print $this->_config->admin_controller;
    $controller=$this->getRequest()->getControllerName();
    $adminControllers=explode(',', $this->_config->admin_controller);
    if(in_array($controller, $adminControllers)){
      if(!$this->checkLogin()){
        //print "No access";
        $this->_redirect("/auth/login/?from=" . urlencode($_SERVER['REQUEST_URI']));
        return false;
      }
    }

  }


  protected function includejQuery(){
    //$ver=mt_rand(0,100000);
    if(!$this->_session->lang){
      $lang='en';
    }else{
      $lang=$this->_session->lang;
    }
    $ver="0.17";
    //print "JQuery!!!";
    //print_r($this->view);

    //$jqGridVer='3.4.2';
    $jqGridVer='3.4.4';


    $this->view->JQuery()->addStylesheet("/css/smoothness/jquery-ui-1.7.custom.css");

    $this->view->jQuery()->setLocalPath("/js/jquery/jquery-1.3.2.min.js");
    $this->view->jQuery()->addJavascriptFile("/js/jquery/jquery-ui-1.7.custom.min.js");
    $this->view->JQuery()->addStylesheet("/css/autocomplete.css");
    $this->view->JQuery()->addJavascriptFile("/js/jquery/ui/development-bundle/external/bgiframe/jquery.bgiframe.min.js");
    $this->view->JQuery()->addJavascriptFile("/js/jquery/plugins/jquery.autocomplete.js");

    //$this->view->JQuery()->addStylesheet("/js/jquery/themes/ui.datepicker.css");
    //$this->view->JQuery()->addStylesheet("/css/jquery/jqModal.css");



    switch($jqGridVer){
      case '3.4.2':
        $this->view->JQuery()->addStylesheet("/js/jqgrid/themes/jqModal.css");

        ////$this->view->JQuery()->addStylesheet("/js/jqgrid/themes/basic/grid.css");
        $this->view->JQuery()->addStylesheet("/js/jqgrid/themes/sand/grid.css");
        ////$this->view->JQuery()->addStylesheet("/js/jqgrid/themes/coffee/grid.css");
        ////$this->view->JQuery()->addStylesheet("/js/jqgrid/themes/green/grid.css");
        $this->view->JQuery()->addJavascriptFile("/js/setlocale_{$lang}.js");
        $this->view->JQuery()->addJavascriptFile("/js/jqgrid/jquery.jqGrid.js?$ver");
        $this->view->JQuery()->addJavascriptFile("/js/jqgrid/js/jqModal.js");
        $this->view->JQuery()->addJavascriptFile("/js/jqgrid/js/jqDnR.js");
        $this->view->JQuery()->addJavascriptFile("/js/jqgrid/js/jquery.tablednd.js");
        break;
      case '3.4.4':

        $this->view->JQuery()->addStylesheet("/js/jqgrid-3.4.4/themes/jqModal.css");

        ////$this->view->JQuery()->addStylesheet("/js/jqgrid/themes/basic/grid.css");
        $this->view->JQuery()->addStylesheet("/js/jqgrid-3.4.4/themes/sand/grid.css");
        ////$this->view->JQuery()->addStylesheet("/js/jqgrid/themes/coffee/grid.css");
        ////$this->view->JQuery()->addStylesheet("/js/jqgrid/themes/green/grid.css");
        $this->view->JQuery()->addJavascriptFile("/js/setlocale_{$lang}.js");
        $this->view->JQuery()->addJavascriptFile("/js/jqgrid-3.4.4/jquery.jqGrid.js?$ver");
        $this->view->JQuery()->addJavascriptFile("/js/jqgrid-3.4.4/js/jqModal.js");
        $this->view->JQuery()->addJavascriptFile("/js/jqgrid-3.4.4/js/jqDnR.js");
        $this->view->JQuery()->addJavascriptFile("/js/jqgrid-3.4.4/js/jquery.tablednd.js");
        break;

      default:;

    }

    $this->view->JQuery()->addJavascriptFile("/js/jquery.fck/jquery.MetaData.js");
    $this->view->JQuery()->addJavascriptFile("/js/jquery.fck/jquery.form.js");
    $this->view->JQuery()->addJavascriptFile("/js/jquery.fck/jquery.FCKEditor.js");

    $this->view->JQuery()->addJavascriptFile("/js/jquery.plugins/dimensions_1.2/jquery.dimensions.pack.js");

    $this->view->JQuery()->addJavascriptFile("/js/jquery.plugins/jquery.rte1_2/jquery.rte.js");
    $this->view->JQuery()->addJavascriptFile("/js/jquery.plugins/jquery.rte1_2/jquery.rte.tb.js");
    $this->view->JQuery()->addStylesheet("/js/jquery.plugins/jquery.rte1_2/jquery.rte.css");

  }


  public function getShopItems($parent,$ids=array()){
    //get 6 items
    $allItems=array();
    foreach($this->_dataConfig['shopCats'] as $cat){
      $pid=implode(',',$ids);
      $pid=$this->cleanSelected($pid);

      $res=$this->_kBase->selectLinkedOrderLimit("[$parent,{$cat['term_name']}]",$pid,'id','','[id,categoryid,descr,image_path]');
      $resArr=$this->_kBase->parseKbData($res);
      if ($resArr){
        foreach($resArr as $i=>$row){
          $allItems[]=$row;
        }
      }
      //if ($resArr){
      //  $allItems[$cat['term_name']][$pid]=$resArr;
      //}

    }
    return $allItems;
  }

  public function cntRelatedItems($path, $ids){
    $itemTypes=$this->_dataConfig['relatedItems'];
    foreach($itemTypes as $type=>$id){
      array_map(array($this->view,'transId'),$ids);
      $parent_id=implode(',',$ids);
      p("\"[$path,$type]\",$parent_id,$id,'',$id", 'cntRelatedItems');
      $cnt=$this->_kBase->countObjects("[$path,$type]",$parent_id,$id,'',$id);

      if ($cnt){
        $cntArr[$type]=$cnt;
      }


      //$count=$this->_kBase->countObjects("[$parent,$type]", $parentId, $itemViewConfig['search'],'',$selectedFields);

    }

    //asort($cntArr);
    return $cntArr;
  }

  public function cntRelatedPerParent($path,$ids){
    $itemTypes=$this->_dataConfig['relatedItems'];
    foreach($itemTypes as $type=>$id){
      array_map(array($this->view,'transId'),$ids);
      $parent_id=implode(',',$ids);
      p("\"[$path,$type]\",$parent_id,$id,'',$id", 'countLinkedPerParent');
      $data=$this->_kBase->countLinkedPerParent("$path,$type",$parent_id,$id,'',$id);
      if ($data){
        //print_r($data);
        foreach($data as $i=>$row){
          $k=key($row);
          $cntArr[$k][$type]=$row[$k];
        }
      }


      //$count=$this->_kBase->countObjects("[$parent,$type]", $parentId, $itemViewConfig['search'],'',$selectedFields);

    }


    if ($cntArr)
    foreach($cntArr as $j=>$arr){
      arsort($cntArr[$j]);
    }
    return $cntArr;
  }



  public function getOtherLinks($countRelatedItems){
    $relLink=$this->_dataConfig['itemProp'];


    $relLink['paint']['cnt']=$countRelatedItems['paint'];
    $relLink['jw']['cnt']=$countRelatedItems['jw'];
    $relLink['hwg']['cnt']=$countRelatedItems['hwg'];
    $relLink['tap']['cnt']=$countRelatedItems['tap'];
    $relLink['apa']['cnt']=$countRelatedItems['apa'];
    $relLink['arch']['cnt']=$countRelatedItems['arch'];


    foreach($relLink as $type=>$link){
      if ($link['cnt']<1){
        unset($relLink[$type]);
      }
    }


    usort($relLink,array($this,'cmp'));

    //print_r($relLink);
    return $relLink;
  }

  public function cmp($a,$b){
    if ($a['cnt']>$b['cnt']){
      $res=-1;
    }elseif($a['cnt']==$b['cnt']){
      $res=0;
    }else{
      $res=1;
    }
    return $res;
  }

  public function getRelLinks($path, $ids){
    $countRelatedItems=$this->cntRelatedItems($path,$ids);

    $relLink=$this->_dataConfig['itemProp'];

    //print_r( $countRelatedItems );

    $relLink['text']['cnt']=$countRelatedItems['text'];
    $relLink['paint']['cnt']=$countRelatedItems['paint'];
    $relLink['jw']['cnt']=$countRelatedItems['jw'];
    $relLink['hwg']['cnt']=$countRelatedItems['hwg'];
    $relLink['tap']['cnt']=$countRelatedItems['tap'];
    $relLink['apa']['cnt']=$countRelatedItems['apa'];
    $relLink['arch']['cnt']=$countRelatedItems['arch'];
    $relLink['video']['cnt']=$countRelatedItems['video'];
    $relLink['audio']['cnt']=$countRelatedItems['audio'];
    $relLink['gallery']['cnt']=$countRelatedItems['gallery'];
    $relLink['article']['cnt']=$countRelatedItems['article']; //Agregado por GC
    $relLink['document']['cnt']=$countRelatedItems['document']; //Agregado por GC
    $relLink['picture']['cnt']=$countRelatedItems['picture']; //Agregado por GC

    foreach($relLink as $type=>$link){
      if ($link['cnt']<1){
        unset($relLink[$type]);
      }
    }

    function cmp($a,$b){
      if ($a['cnt']>$b['cnt']){
        $res=-1;
      }elseif($a['cnt']==$b['cnt']){
        $res=0;
      }else{
        $res=1;
      }
      return $res;
    }

    usort($relLink,'cmp');

    //print_r($relLink);
    return $relLink;
  }

  public function cleanSelected($selected){

    $selected1='';
    $selArr=array();
    $selectArr=explode(',',$selected);
    foreach($selectArr as $i=>$selVal){
      $selVal=trim($selVal);
      if (preg_match('/\\D+/',$selVal)){
        //if (is_string($selVal)){
        $selVal="'$selVal'";
      }
      if ($selVal){
        $selArr[]=$selVal;
      }
    }
    if ($selArr){
      $selected1=implode(',',$selArr);
    }

    //wrap into [  ] if not already wrapped
    if (strpos($selected1,'[')!==0){
      $selected1="[".$selected1;
    }

    if (strpos($selected1,']')!==(strlen($selected1)-1)){
      $selected1=$selected1."]";
    }

    return $selected1;

  }

  protected function jqPaging( $params){
    /*
     $params=array(
     'defIndex'=>'id', //default order field
     'defLimit'=>10, //default limit
     'qs'=>'' //default query string including 'where' conditions
     'count'=>$count,//
     )
     */

    extract($params);
    if(!$defIndex){
      $defIndex='id';
    }

    if($defLimit <= 0){
      $defLimit=10;
    }

    // get the requested page
    $page=$this->getRequest()->getParam('page');
    // get how many rows we want to have into the grid
    // rowNum parameter in the grid
    $limit=$this->getRequest()->getParam('rows');
    if(!$limit){
      $limit=$defLimit;
    }
    // get index row - i.e. user click to sort
    // at first time sortname parameter - after that the index from colModel
    $sidx=$this->getRequest()->getParam('sidx');
    // if we do not pass at first time index use the first column for the index
    if(!$sidx){
      $sidx=$defIndex;
    }
    // sorting order - at first time sortorder
    $sord=$this->getRequest()->getParam('sord');

    if($count > 0){
      $total_pages=ceil($count / $limit);
    }else{
      $total_pages=0;
    }
    // if for some reasons the requested page is greater than the total
    // set the requested page to total page
    if($page > $total_pages){
      $page=$total_pages;
    }
    // calculate the starting position of the rows
    $start=$limit * $page - $limit;
    // do not put $limit*($page - 1)
    // if for some reasons start position is negative set it to 0
    // typical case is that the user type 0 for the requested page
    if($start < 0){
      $start=0;
    }

    //    $resArr=array(
    //    'page'=>$page,
    //    'total'=>$total_pages,
    //    'records'=>$count,
    //    'qsGrid'=>$qsGrid,
    //    'sortFld'=>$sidx,
    //    'sortOrd'=>$sord,
    //    'start'=>$start,
    //    'limit'=>$limit
    //    );


    $resObj=new stdClass();
    $resObj->page=$page;
    $resObj->total=$total_pages;
    $resObj->records=$count;
    $resObj->qsGrid=$qsGrid;
    $resObj->sortFld=$sidx;
    $resObj->sortOrd=$sord;
    $resObj->start=$start;
    $resObj->limit=$limit;

    return $resObj;
  }

  public function itemAction(){
    $id=(int)$this->_request->getParam('id');
    //print "id=$id<br>";
    $session=Zend_Registry::get('nsHw');
    $lang=$session->lang;
    //print "ref:".$session->referrer;
    $work=$this->getRequest()->getParam('work');
    $tables=Zend_Registry::get('tables');
    $docs=Zend_Registry::get('docs');
    $workFldArr=Zend_Registry::get('hw_work');
    $curCtrl=strtolower($this->getRequest()->getControllerName());
    $className=ucfirst($curCtrl);
    $titleArr=Zend_Registry::get('titles');
    if(!in_array($work, $tables)){
      $work='';
    }
    if($id == 0){
      $this->_redirect('/');
      return;
    }

    $workObjFinder=new $className();
    $workObj=$workObjFinder->fetchRow($curCtrl . "_id=" . $id);
    //print $curCtrl.'_id';
    if($workObj->{$curCtrl . '_id'} != $id){
      $this->_redirect('/');
      return;
    }
    //$this->view->apas = $apa;


    if($work){
      require_once 'Pager.php';
      $count=5;
      //print "<br>work:".$work."<br>";
      $offset=(int)$this->getRequest()->getParam('item', 1);
      if(!$offset){
        $offset=1;

      }

      //print "offset=$offset<br>";
      //$wk=new Paint();
      $num_products=$workObjFinder->countOneItemWorks($id);
      $pager_options=array('mode'=>'Sliding','append'=>false,'perPage'=>$count,'delta'=>$offset,'urlVar'=>'item','path'=>"/$curCtrl/item/id/$id/work/$work/",'fileName'=>'item/%d','totalItems'=>$num_products,'separator'=>',','spacesBeforeSeparator'=>1,'spacesAfterSeparator'=>1,'currentPage'=>$offset,'curPageLinkClassName'=>'cur_page_link_class','linkClass'=>'page_link_class');

      $pager=Pager::factory($pager_options);

      //then we fetch the relevant records for the current page
      list($from, $to)=$pager->getOffsetByPageId($offset);
      $wkArr=$workObjFinder->itemWorks($id, $count, $from - 1, $work);
      $this->view->links=$pager->links;
      //print_r($wkArr);
      $this->view->wkArr=$wkArr;
    }else{
      $fldname=$titleArr[$curCtrl] . '_' . $lang;
      $this->view->title=$workObj->$fldname;
      $row=$workObj->toArray();
      $this->view->wkRow=$row;

      if(!in_array($curCtrl, $docs)){
        $img=$this->view->hlpShowImage($curCtrl, $row[$workFldArr[$curCtrl]], '400', 'medium', 'src');
        $this->view->img=$img;
        $this->view->img_big=$this->view->hlpShowImage($curCtrl, $row[$workFldArr[$curCtrl]], 600, 'original', 'src');
      }

    }

    $this->view->lang=$lang;

    $this->view->work=$curCtrl;

    $this->view->titleArr=$titleArr;
    //this should be Related Works.....
    $cntWorks=$workObjFinder->countItemWorks($workObj->{$curCtrl . "_id"}, $curCtrl);
    //print_r($cntWorks);


    $this->view->skinCnt=$cntWorks;
    //find other works
    if(!$work){
      $session=Zend_Registry::get('nsHw');
      $skin_id=$session->skin_id;
      if(!$skin_id){
        $skin_id=$this->getRequest()->getParam('skin');
      }
      if(!$skin_id){
        $skin_id=1;
      }
      $otherWorks=$workObjFinder->skinWorks($skin_id, 6, 0, $curCtrl);
      foreach($otherWorks as $i=>$oWrk){
        $otherWorks[$i]['skin_id']=$skin_id;
        //				if (method_exists($workObjFinder,'showImage')){
        //				$otherWorks[$i]['img']=$workObjFinder->showImage($oWrk['work_num'],'100','small');
        //				}
        if(!in_array($curCtrl, $docs)){
          $otherWorks[$i]['img']=$this->view->hlpShowImage($curCtrl, $oWrk[$workFldArr[$curCtrl]], '100', 'small');
        }
      }
      //print_r($wkArr);
      $ses=Zend_Registry::get('nsHw');
      $this->view->otherWorks=$ses->otherWorks;
      //$this->view->otherWorks=$otherWorks;
      //print_r ($otherWorks);
    }
    $this->view->referrer=$session->referrer;
    $this->view->refUri=$session->refUri;
    $this->view->id=$id;
    $this->_helper->layout->setLayout('item');
  }

}


