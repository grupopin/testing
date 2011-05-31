<?php

class TextsController extends Hw_Controller_Action
{
  public function indexAction()
  {


    //$archsFinder = new Arch();
    //        $this->view->arch = $archFinder->fetchByArchId($id);
    $this->view->title = $this->view->l('Skins:Text');
    $this->_helper->layout->setLayout('skins');

  }

  public function viewAction(){
    $this->includejQuery();
    $hl=(int)$this->_getParam('hl',0);//hl id
    $id=$this->_getParam('id',0);//text id
    $langCode=$this->_session->langArr['lang_alt_short'];

    //print "text:$id, hl:$hl<br>";

    $res=$this->_kBase->findParent('hl',$hl,'mt',"mt,maintitle_name_{$this->_langCode}");
    $mtProps=$res[0];


    $sData=$this->_kBase->findParent('mt',$mtProps['mt'],'skin',"skin,title_long_{$this->_langCode}");
    $this->view->skinProps=$skinProps=$sData[0];

    $res=$this->_kBase->getObjectById('text',$id,"txt,text_contents_{$this->_langCode},text_title_{$this->_langCode},text_occasion_{$this->_langCode},text_published_{$this->_langCode},text_summary_{$this->_langCode}");
    $text=$res[0];
    
	//removing 22 >>> in front of Highlights
    $text = preg_replace("/[0-9]+&amp;gt;&amp;gt;&amp;gt;/","",$text);
    //removing <<<22 in end of Highlights
    $text = preg_replace("/&amp;lt;&amp;lt;&amp;lt;[0-9]+/","",$text);
    //$res=$this->_kBase->getObjectById('mt',$id,"mt, maintitle_name_{$this->_langCode}");
    //print_r($mtProps);
    //print_r($res);
    
    
    $pathArr[]=array($this->view->l('Home'),'/');
    $pathArr[]=array($this->view->l('The 5 Skins'),"/skin");
    $pathArr[]=array($skinProps['title_long_'.$this->_langCode],'/'.$this->_dataConfig['skinNames'][$skinProps['skin']]);
    $pathArr[]=array($mtProps["maintitle_name_{$this->_langCode}"],'/highlights/'.$mtProps['mt']);
    //$pathArr[]=array($text["text_title_{$this->_langCode}"]);
    $this->view->pathArr=$pathArr;


    $this->view->hl_id=$hl;
    $this->view->text=$text;
    $ids[]=$id;
    $this->view->relLink=$this->getRelLinks('text',$ids);

    $this->view->buyItems=$this->getShopItems('text',$ids);

    $this->view->title = $this->view->l('Skins:Text');
    $this->view->id=$id;
    $this->_helper->layout->setLayout('skins');
  }

  public function itemAction(){

    parent::itemAction();


    //$this->_helper->layout->setLayout('skin_results');
  }


  public function norouteAction()
  {
    // redirect to home page
    $this->_redirect('/');
  }
}