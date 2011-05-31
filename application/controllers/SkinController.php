<?php

class SkinController extends Hw_Controller_Action
{
  protected $skinArr;
  protected $workNames;



  public function indexAction()
  {
    //        $id = (int)$this->_request->getParam('id');
    //        if ($id == 0) {
    //            $this->_redirect('/');
    //            return;
    //        }
    //$response=$this->getResponse();
    //$response->insert('right_column',$this->view->render('skin/skins_flash.phtml'));
    $this->view->title = 'Skins';
    $this->_helper->layout->setLayout('skins');



  }
  /*
   * list of maintitles for particular skin
   *
   */
  public function listMtAction(){
    //$this->_helper->layout->disableLayout();
    //$this->_helper->viewRenderer->setNoRender();

    $skins=$this->_config->skin;

    $id=$this->_getParam('id');
    $idstr=ucfirst($id);
    $skinId=$skins->{$id};
    $this->view->title=$this->view->l("Skin").':'.$this->view->l('Maintitles');
    $langCode=$this->_session->langArr['lang_alt_short'];
    //die($langCode);
    //get list of maintitles for the skin

    //    $idToText[1]='The First Skin';
    //    $idToText[2]='The Second Skin';
    //    $idToText[3]='The Third Skin';
    //    $idToText[4]='The Fourth Skin';
    //    $idToText[5]='The Fifth Skin';

    //	  $catStyle[0][2]="CatItem2_color2";
    //	  $catStyle[0][3]="CatItem3_color2";
    //	  $catStyle[0][4]="CatItem4_color2";

    $mtArr=array();

    //print 'skinId:'.$skinId."<br>";
    //print $idToText[$skinId]."<br>";
    $from=$this->_getParam('page',0);
    $limit=1000;
    $order_by='id';
    $order_dir='asc';
    $selectedFields="id,mt,maintitle_name_{$langCode},maintitle_summary_{$langCode}";
    $count=$this->_kBase->countObjects('[skin,mt]', $skinId, 'mt','',$selectedFields);
    if ($count){
    $mtArr=$this->_kBase->selectFromKb('[skin,mt]',$skinId,'mt','',$selectedFields,$from,$limit,$order_by,$order_dir);
    //print_r($mtArr);
    //$this->view->pageTitle=$this->view->l($idToText[$skinId]).': '.$this->view->l($idstr);
    $fromStr=$from+1;
    foreach($mtArr as $i=>$mtRow){
      if ($i>0){
        $style=$i+1;
      }else{
        $style='';
      }
      $mtArr[$i]['class_id']=$style;
    }

    $pager=Zend_Paginator::factory($mtArr);
    $pager->setCurrentPageNumber($from+1);
    $this->view->pager=$pager;

    }
    $toStr=$from+sizeof($mtArr);
    $this->view->countStr=$this->view->l('Main Titles')."($fromStr-$toStr ".$this->view->l('of')." $count)";
    //$this->view->mtArr=$mtArr;


    $sData=$this->_kBase->getObjectById('skin',$skinId,"skin,title_$langCode,title_long_$langCode,descr_$langCode");
    $this->view->skinProps=$skinProps=$sData[0];

    /*
     foreach($mtArr as $i=>$mtRow){
     $ids[]=$mtRow['mt'];
     }
     */

    $pathArr[]=array($this->view->l('Home'),'/');
    $pathArr[]=array($this->view->l('The 5 Skins'),"/skin");
    $pathArr[]=array($skinProps['title_long_'.$langCode]);
    $this->view->pathArr=$pathArr;
    $this->view->skinIcon=$this->_dataConfig['skinIcons'][$skinId];


    $this->view->skinName=$id;
    $this->view->langCode=$langCode;
    //$this->view->relLink=$this->getRelLinks('mt',$ids);
    $this->view->relLink=$this->getRelLinks('skin',array($skinId));

    $this->view->buyItems=$this->getShopItems('skin',array($skinId));
    //print_r($this->view->buyItems);
    //print_r($relLink);
    $this->_helper->layout->setLayout('skins');
  }



  public function listHlAction(){
    //$id - maintitle id
    $id=$this->_getParam('id');
    //print $id;


    $curPage=$this->_getParam('page',1);
    $limit=1000;
    $from=($curPage-1)*$limit;
    $order_by='id';
    $order_dir='asc';
    $selectedFields='id,text';
    $count=$this->_kBase->countObjects('[mt,hl]', $id,'id','',$selectedFields);

    $hlArr=$this->_kBase->selectFromKb('[mt,hl]', $id,'id','',$selectedFields,$from,$limit,$order_by,$order_dir);

    if ($count){

      $items=new Zend_Paginator(new Hw_PageKb('selectFromKb',
      array('parentType'=>'mt', 'parentId'=>$id,'type'=>'hl','key'=>'id',
                              'search'=>'','selected'=>$selectedFields,'from'=>$from,'limit'=>$limit,
                              'order_by'=>$order_by,'order_dir'=>$order_dir)));
      $items->setItemCountPerPage($limit);
      $items->setCurrentPageNumber($curPage);

      //$curItems=$items->getCurrentItems();


      //print_r($curItems);


      $txtArr=array();
      foreach($hlArr as $i=>$hlRow){
        $res=$this->_kBase->selectFromKb('[hl,text]',$hlRow['id'],'txt','','text_title_en,txt,text_id,text_date',0,1);
        //print $hlRow['id'].':'.$res[0]['text_title_en']."<br>";
        $txtArr[$res[0]['txt']][]=array('id'=>$hlRow['id'],'text'=>$hlRow['text']);
        if (empty($texts[$res[0]['txt']])){
          $texts[$res[0]['txt']]=$res[0];
        }
        //print_r($res);print "<br>";
        if ($hlRow['id']){
        $ids[]=$hlRow['id'];

        }
      }
      

      $this->view->idStr=implode('-',$ids);

      //print_r($texts);

      //$this->view->pager=$items;

//print_r($ids);
      $this->view->relLink=$this->getRelLinks('hl',$ids);

      $this->view->buyItems=$this->getShopItems('hl',$ids);


    }

    $res=$this->_kBase->getObjectById('mt',$id,"maintitle_name_{$this->_langCode}");
    //print_r($mtProps);
    //print_r($res);
    $mtProps=$res[0];


    $sData=$this->_kBase->findParent('mt',$id,'skin',"skin,title_long_{$this->_langCode}");
    $this->view->skinProps=$skinProps=$sData[0];


    $pathArr[]=array($this->view->l('Home'),'/');
    $pathArr[]=array($this->view->l('The 5 Skins'),"/skin");
    $pathArr[]=array($skinProps['title_long_'.$this->_langCode],'/'.$this->_dataConfig['skinNames'][$skinProps['skin']]);
    $mtName=$mtProps["maintitle_name_{$this->_langCode}"];
    $pathArr[]=array($mtName);
    $this->view->pathArr=$pathArr;

    $this->view->mtPathName=str_replace(array('/',' ','&','#'),'_',strtolower($mtName));

    $this->view->hlArr=$hlArr;
    $this->view->txtArr=$txtArr;
    $this->view->texts=$texts;
    $this->view->items=$items;
    $this->view->from=$from;
    $this->view->count=$count;


    $this->_helper->layout->setLayout('skins');

  }




  public function epidermisAction(){
    $this->_forward('browse',null,null,array('id'=>1));
  }
  public function clothesAction(){
    $this->_forward('browse',null,null,array('id'=>2));
  }
  public function housesAction(){
    $this->_forward('browse',null,null,array('id'=>3));
  }
  public function identityAction(){
    $this->_forward('browse',null,null,array('id'=>4));
  }
  public function earthAction(){
    $this->_forward('browse',null,null,array('id'=>5));
  }


  public function browseAction()
  {
    $curURI=$_SERVER['REQUEST_URI'];
    $curURI=($curURI{strlen($curURI)-1}!='/')?($curURI.'/'):($curURI);
    $this->view->curURI=$curURI;
    //print $curURI;
    $session=Zend_Registry::get('nsHw');
    $session->referrer='SkinController';
    $session->refUri=$_SERVER['REQUEST_URI'];
    $this->skinArr=Zend_Registry::get('hw_skins');
    $this->workNames=array_keys(Zend_Registry::get('tables'));
    $docs=Zend_Registry::get('docs');
    $workFldArr=Zend_Registry::get('hw_work');
    $dateFieldArr=Zend_Registry::get('hw_dates');
    //print_r($this->getRequest()->getParams());

    $skin_id=(int)$this->getRequest()->getParam('id');
    $session->skin_id=$skin_id;
    $sk=new Skin();
    $skin_name=$sk->getSkinName($skin_id);
    $work=$this->getRequest()->getParam('work');
    if (!in_array($work,$this->workNames)){
      $work='';
    }

    require_once 'Pager.php';
    $hl=new RelMaintitleText();
    $skinCnt=array();
    foreach($this->workNames as $wkName){
      $wClassname=ucfirst($wkName);
      //print $wClassname;
      $wObj=new $wClassname();

      if (in_array($wkName,array_keys(Zend_Registry::get('docs')))){
        //print $wObj->countSkinDocs($skin_id);
        $skinCnt[$wkName]=$wObj->countSkinDocs($skin_id);
      }else{
        $skinCnt[$wkName]=$wObj->countSkinWorks($skin_id);
      }
    }
    //print_r($skinCnt);
    $this->view->skinCnt=$skinCnt;




    $offset=(int)$this->getRequest()->getParam('item',1);
    if (!$offset){
      $offset=1;

    }
    $count=5;
    $path="/skin/{$this->skinArr[$skin_id]}/";
    if ($work){
      $num_products=$skinCnt[$work];
      $path.="work/$work";
    }else{
      $num_products=$hl->countHL($skin_id);
    }
    $this->view->num_items=$num_products;

    $pager_options = array(
      'mode'       => 'Sliding',
			'append'     => false,
      'perPage'    => $count,
		  'delta'      => $offset,
      'urlVar'     => 'item',
			'path'       => $path,
			'fileName'   => 'item/%d',
      'totalItems' => $num_products,
		  'separator' => ',',
		'spacesBeforeSeparator'=>1,
		'spacesAfterSeparator'=>1,
		'currentPage'=>$offset,
		'curPageLinkClassName'=>'cur_page_link_class',
		'linkClass'=>'page_link_class'
		);

		$pager = Pager::factory($pager_options);

		//then we fetch the relevant records for the current page
		list($from, $to) = $pager->getOffsetByPageId($offset);
		//set the OFFSET and LIMIT clauses for the following query

		//print "<br>$from, $num_products<br>";
		if ($work){
		  $wkClassName=ucfirst($work);
		  $wk=new $wkClassName();
		  if (in_array($work,array_keys(Zend_Registry::get('docs')))){
		    $wkArr=$wk->skinDocs($skin_id,$pager_options['perPage'],$from - 1);
		  }else{
		    $wkArr=$wk->skinWorks($skin_id,$pager_options['perPage'],$from - 1);
		  }
		  $ses=Zend_Registry::get('nsHw');
		  $ses->otherWorksType=$work;
		  if (!in_array($work,$docs)){
		    foreach($wkArr as $i=>$oWrk){

		      $img=$this->view->hlpShowImage($work,$oWrk[$workFldArr[$work]],'100','small','src');
		      //$otherWorks[$i]=$oWrk;
		      $wkArr[$i]['img']=$img;
		    }
		  }
		  //print_r($wkArr);
		  $ses->otherWorks=$wkArr;
		}else{

		  $hlArr=$hl->getHL($skin_id,$pager_options['perPage'],$from - 1);
		  $ses=Zend_Registry::get('nsHw');
		  $ses->otherWorksType='hl';
		  $ses->otherWorks=$hlArr;
		}



		//Zend_Registry::get('nsItems')->set('wkArr',$wkArr);

		//show the links
		$this->view->links=$pager->links;
		$this->view->num_from=$from;
		$this->view->num_to=$to;

		//mail('levan@pelezol.co.il','skin_name',$skin_id.":".print_r($hlArr,true));
		$this->view->hlArr=$hlArr;
		$this->view->wkArr=$wkArr;

		$this->view->lang=$lang='en';
		$titleArr=Zend_Registry::get('titles');
		$this->view->work=$work;
		$this->view->date_fld_name=$dateFieldArr[$work];
		$this->view->titleArr=$titleArr;
		$this->_helper->layout->setLayout('skin_results');
  }

  public function norouteAction()
  {
    // redirect to home page
    $this->_redirect('/');
  }
}