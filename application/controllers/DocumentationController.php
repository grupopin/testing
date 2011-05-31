<?php

class DocumentationController extends Hw_Controller_Action
{
 
  
  public function indexAction()
  {
    $this->_helper->layout->setLayout('skins');
  }
  public function showAction()
  {
    $this->_helper->layout->setLayout('skins');
    
    $type=$this->_getParam('type');
    $key=$this->_getParam('key');
    $quest_filter=$this->_getParam('quest_filter');
    $parentType=$this->_getParam('link_item');
    $parentId=$this->_getParam('link_item_id');
    $selectedKeys=$this->_getParam("selected_keys");
    $show_selected=(int)$this->_getParam("show_selected");
    $order_by=$this->_getParam("sidx",'id');
    $order_dir=$this->_getParam("sord",'asc');

    if (!$key){
      $key='title';
    }

    $keyField=$this->_fields[$key][strtolower($type)];
    $itemTitle=$this->_fields['title'][strtolower($type)];
    $itemIdField=$this->_fields['id'][strtolower($type)];

    if ($keyField!=$itemTitle && $keyField!=$itemIdField){
      $keyStr=",$keyField";
    }


    if (!$selectedKeys){
      $selectedKeys="id,$itemIdField,$itemTitle".$keyStr."contents_en,article_summary_en,doc_description_en"; //FIXME: hacer configurable 
    }


    $count=$this->_kBase->countObjects("[$parentType,$type]", $parentId, $key,$quest_filter,$selectedKeys);
    
    $params['defIndex']='id'; //default order field
    $params['defLimit']=500; //default limit
    $params['count']= $count;
    $resPaging=$this->jqPaging($params);

    $resArr=array();
    
    $resArrAll=$this->_kBase->selectFromKb("[$parentType,$type]", $parentId, $key,$quest_filter,$selectedKeys,$resPaging->start,$resPaging->limit, $order_by, $order_dir);
    //$this->view->items = $resArrAll;
    $this->view->itemArr = $resArrAll;
    $this->view->type = $type;
    $this->view->key = $key;
    $this->view->name = $this->_dataConfig['itemProp'][$type]['name'];
    $this->view->title = $this->_dataConfig['itemProp'][$type]['title'];
    $this->view->item_title = $this->_itemTitles[$type];
    $this->view->objectCategory=$this->_dataConfig['objectCategory'];
    $this->view->count=$count;
    $itemViewConfig=$this->_dataConfig['itemView'][$type]['list'];
    $this->view->itemViewConfig=$itemViewConfig;
    
  }

  public function filterAction()
  {
    $this->_helper->layout->setLayout('skins');
    
    
    $this->view->type = $this->_getParam('type');
    $this->view->subtype = $this->_getParam('subtype');
    $this->view->key = $this->_getParam('key');
    $this->view->title = $this->_dataConfig['itemProp'][$this->view->type]['title'];
    
    switch ($this->view->type) {
    	case 'text':
          if ($this->view->subtype=='hw') {
            $this->view->breadcrumb = "Hundertwasser texts and manifestos";
          } else {
            $this->view->breadcrumb = "Texts on Hundertwasser";
          }
    	break;
    	
    	case 'gallery':
    	  $this->view->breadcrumb = 'Image Gallery';
    	break;
    	default:
    	  $this->view->breadcrumb = $this->view->title;
    	break;
    }
  }
  
  public function searchAction()
  {
       $this->_helper->layout->setLayout('skins');
    
       $objectType = $this->_getParam('type');
       $subtype = $this->_getParam('subtype');
       $key = $this->_getParam('key');
       $title = $this->_getParam('title');
       $year = $this->_getParam('year');
       $keyword = $this->_getParam('keyword');
       $author = $this->_getParam('author');
       $photographer = $this->_getParam('photographer');
       
       $options = array(
            Zend_Db::ALLOW_SERIALIZATION => false
        );
       $params = array(
          'host'           => '127.0.0.1',
          'username'       => 'hwarch_gcasado',
          'password'       => 'yodo33',
          'dbname'         => 'hwarch_db',
          'options'        => $options
      );
      
       $this->db = Zend_Db::factory('Pdo_Mysql', $params);
       $this->db->getConnection();
       
       $sql = "SELECT * FROM kb_trans where term_active= 1 and term_arg1='{$objectType}' and term_head='object' ";
       $sql .= " and (created_by='1' || created_by='3' || created_by='2' ) and (operation='load' || operation='assert') ";
       $sql .= " and term_head!='' && term_head IS NOT NULL" ;
       
       if ($objectType=='text') { 
         if ($subtype=='hw') {
           $sql .=  " and term_list like '%text_author-\'Hundertwasser\',%'";
         } else {
           $sql .=  " and term_list not like '%text_author-\'Hundertwasser\',%'";
         }
       } 
         
       if ($title) {
         switch ($objectType) {
         	case 'text':
         	  $f1='text_title_en';
         	  $f2='_text_title_ge';
         	break;
         	case 'gallery':
         	  $f1='gallery_title_en';
         	  $f2='%gallery_title_ge';
         	break;
         	case 'audio':
         	  $f1='title_en';
         	  $f2='%title_ge';
         	break;
         	case 'video':
         	  $f1='title_en';
         	  $f2='%title_ge';
         	break;         	
         } 
         //echo $title;
         $title=str_replace("\'","%'",$title);
         //echo $title;
         $title_hmtl=htmlentities($title,ENT_QUOTES,'UTF-8');
         //echo $title_hmtl;
         
         $filter1 = " and term_list like '%{$f1}-\'%$title_hmtl%\',{$f2}%'";
         $sql = $sql . $filter1;
         //text_title_en-'ON FALSE ART', text_title_ge-'
       }
       
       //text_date-'1978-11-07', text_summary
       if ($year) {
         switch ($objectType) {
         	case 'text':
         	  $f1='text_date';
         	  $f2='_text_summary';
         	break;
         	case 'gallery':
         	  $f1='gallery_year';
         	  $f2='%gallery_year_circa';
         	break;
         	case 'audio':
         	  $f1='date_of_shot';
         	  $f2='%place_en';
         	break;         	
         	case 'video':
         	  $f1='date_of_shot%';
         	  $f2='%place_of_shot_en';
         	break;          	
         } 
         $filter2 = " and term_list like '%{$f1}-\'%$year%\',{$f2}%'";
         $sql = $sql . $filter2;
       }
              
       if ($keyword) {
         switch ($objectType) {
         	case 'text':
         	  $f1='text_keywords';
         	  $f2='_text_date';
         	break;
         	case 'gallery':
         	  $f1='gallery_keywords';
         	  $f2='%galery_place_ge';
         	break;
          	case 'audio':
         	  $f1='description_keywords%';
         	  $f2='%note_occasion_en';
         	break;
          	case 'video':
         	  $f1='descr_keywords%';
         	  $f2='%note_occasion_en';
         	break;         	
         } 
         $keyword_hmtl=htmlentities($keyword,ENT_QUOTES,'UTF-8');
         $filter3 = " and term_list like '%{$f1}-\'%$keyword_hmtl%\',{$f2}%'";
         $sql = $sql . $filter3;
       }
       
       if ($author) {
         switch ($objectType) {
         	case 'text':
         	  $f1='text_author';
         	  $f2='_text_orig_lang';
         	break;
          	case 'video':
         	  $f1='author_%';
         	  $f2='%director_en';
         	break;         	
         } 
         $author_hmtl=htmlentities($author,ENT_QUOTES,'UTF-8');
         $filter4 = " and term_list like '%{$f1}-\'%$author_hmtl%\',{$f2}%'";
         $sql = $sql . $filter4;
       }
       
       if ($photographer) {
         $photographer_hmtl=htmlentities($photographer,ENT_QUOTES,'UTF-8');
         $filter5 = " and term_list like '%gallery_photographer-\'%$photographer_hmtl%\',%gallery_keywords%'";
         $sql = $sql . $filter5;
       }
      
      $items = $this->db->fetchAll($sql);
      $itemArr=array();
            
      foreach ($items as $item) {
         $item = $this->_kBase->getObjectById($objectType,$item['term_arg2']);
         if ($item[0][$key]) {
           $itemArr[]=$item[0];
         }
      }
      
      //$this->view->items = $itemArr;
      $this->view->itemArr = $itemArr;
      $this->view->type = $objectType;
      $this->view->key = $key;
      $this->view->name = $this->_dataConfig['itemProp'][$objectType]['name'];
      $this->view->title = $this->_dataConfig['itemProp'][$objectType]['title'];
      $this->view->item_title = $this->_itemTitles[$objectType];
      //$this->view->title = "Text Search";
      $this->view->subtype = $subtype;
      $this->view->itemProp=$this->_dataConfig['itemProp'];
      $this->view->objectCategory=$this->_dataConfig['objectCategory'];
      $this->view->count=sizeof($itemArr);
      $itemViewConfig=$this->_dataConfig['itemView'][$objectType]['list'];
      $this->view->itemViewConfig=$itemViewConfig;
      
      switch ($objectType) {
    	case 'text':
          if ($this->view->subtype=='hw') {
            $this->view->breadcrumb = "Hundertwasser texts and manifestos";
          } else {
            $this->view->breadcrumb = "Texts on Hundertwasser";
          }
    	break;
    	
    	case 'gallery':
    	  $this->view->breadcrumb = 'Image Gallery';
    	break;
    	default:
    	  $this->view->breadcrumb = $this->view->title;
    	break;
    }
      
   }
    
}