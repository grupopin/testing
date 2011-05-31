<?php
class ItemController extends Hw_Controller_Action {
  
  protected static $cache;

  public function indexAction(){



  }


  public function oeuvreAction(){
  	
  	$this->_helper->layout->setLayout('skins');
  	
  	
  }
  
  public function cntApaAction(){


    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    //$arr=$this->_kBase->countSearchObjectRange2("[apa]","[apa-[title-'']]","[apa-[year-1980]]","[apa-[title,apa,year]]","year",0,"year",1990);
    //print_r($arr);

    //$arr=$this->_kBase->searchObjectRange2("[apa]","[apa-[title-'']]","[apa-[year-1980]]","[apa-[id,title,apa,year]]","year",0,"year",1990);
    //print_r($arr);

  }


  public function getAction(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $id=$this->_getParam('id');
    $type=$this->_getParam('type');

    $id=$this->view->transId($id,false);


    $name=$this->_dataConfig['itemProp'][$type]['name'];
    $itemView=$this->_dataConfig['itemView'][$type]['list'];

    $langArr=$this->_session->langArr;
    //print_r($itemViewProps);



    $types=array_keys($this->_dataConfig['itemView']);


    if (!in_array($type,$types)) {
      print $this->view->l('Error type');
      return false;
    }

    if (is_array($this->_nsHwPages->allItems)){
      $allItems=$this->_nsHwPages->allItems;
      //print_r($allItemsOpts);

      //find current item position



      foreach($allItems as $n=>$item){
        if ($item[$itemView['id']]==$id){
          $curNum=$n;
          break;
        }
      }




      $prev=$allItems[$curNum-1][$itemView['id']];
      $prev=$this->view->transId($prev);
      $next=$allItems[$curNum+1][$itemView['id']];
      $next=$this->view->transId($next);

      $showItem=$allItems[$curNum];

      //print_r($prev);
      //print "cur:$n,left:".$curNum-$itemsOnPage/2;
      //print_r($showItems);
      //$this->_nsHwPages->setExpirationHops(3);
      //$this->view->showItem=$showItems;
      //print_r($showItems);
      //$this->view->idField=$itemViewProps['id'];
    }
    /*

    */

    //print "id:$id, type=$type";
    //print_r($types);
    //print_r($itemArr);





    $pad=true;
    if ($type=='gallery'){
      $pad=false;
      $imgPath=$showItem[$itemView['image_id']];
      $imgPath=str_replace('galery/','',$imgPath);
    }else{
      //$imgPath=$showItem[$itemView['image_id']];
      $imgPath=$showItem[$itemView['id']];
      
    }
    $imgPath=$this->view->workImage($type,'original',$imgPath,'list',true,'asc',1,$pad);//medium is too small!!!
    if (!$imgPath){
      //$imgPath="/images/items/someExamplePic.jpg";
      $imgPath="";
    }
    
   if ($showItem[$itemView['title']]) { 
	    $title=html_entity_decode($showItem[$itemView['title']],ENT_QUOTES,'UTF-8');
	    $titleStr=$title;
	} else {
	    $title=html_entity_decode($showItem[$itemView['title_default']],ENT_QUOTES,'UTF-8');
	    $titleStr=$title;
	}
    
    if ($showItem[$itemView['work_number']]) {
      $work_number = html_entity_decode("Work N&deg;&nbsp;",ENT_QUOTES,'UTF-8') .$showItem[$itemView['work_number']];
    } else {
      $work_number = "";
    }

    $response=array(
     'id'=>$id,
     'prev'=>$prev,
     'next'=>$next,
     'cur'=>$curNum+1,
     'title'=>$title,
     'titleStr'=>$titleStr,
     'workNumber'=>$work_number,
     'imgPath'=>$imgPath
    );


    $json = new Zend_Json();
    $responseStr = $json->encode($response);
    $this->getResponse()->setHeader("Content-Type","application/json, text/javascript");
    $this->getResponse()->setBody($responseStr);


  }


  public function listAction(){

    $type=$this->_getParam('type');
    $from=$this->_getParam('page',0);
    $parent=$this->_getParam('parent','');
    $parent_id=$this->_getParam('parent_id','');

    $parent_id=$this->view->transId($parent_id,false);
    //die($parent_id);

    $langCode=$this->_session->langArr['lang_alt_short'];

    $itemViewProps=$this->_dataConfig['itemView'][$type]['list'];
    $viewFields=$itemViewProps['selected'].','.$itemViewProps['orderby'].','.$itemViewProps['image_id'];

    //print $viewFields;


    if (in_array($type,array('js','css'))) return false;
    if (!$parent) return false;

    $itemViewConfig=$this->_dataConfig['itemView'][$type]['list'];
    $order_by=$itemViewConfig['orderby'];
    $order_dir=$itemViewConfig['order'];
    $selectedFields=$itemViewConfig['selected'];
    $limit=5;
    //print $parent;
    switch($parent){
      case 'skin':
        //print 'skin:';
        $parentId=$this->_config->skin->{$parent_id};

        $pageAdapter=new Hw_KbPaginator();
        $pageAdapter->path="[$parent,$type]";
        $pageAdapter->parentId=$parentId;
        $pageAdapter->searchFields=$itemViewConfig['search'];
        $pageAdapter->searchString='';
        $pageAdapter->selectedFields=$selectedFields;
        $pageAdapter->order_by=$order_by;
        $pageAdapter->order_dir=$order_dir;

        $itemArr=new Zend_Paginator($pageAdapter);
        //$pager=Zend_Paginator::factory($itemArr);
        $itemArr->setCurrentPageNumber($from);
        $itemArr->setItemCountPerPage($limit);
        $count=$itemArr->getTotalItemCount();


        $allItemsOpts=array(
         'path'=>"[$parent,$type]",
         'parentId'=>$parentId,
         'searchFields'=>$itemViewConfig['search'],
         'searchString'=>'',
         'selectedFields'=>$selectedFields,
         'order_by'=>$order_by,
         'order_dir'=>$order_dir
        );

        $from2=0;$limit2=1000;
        $allItems=$this->_kBase->selectFromKb($allItemsOpts['path'],$allItemsOpts['parentId'],$allItemsOpts['searchFields'],$allItemsOpts['searchString'],$viewFields,$from2,$limit2,$allItemsOpts['order_by'],$allItemsOpts['order_dir']);
        $this->_nsHwPages->allItems=$allItems;

        $sData=$this->_kBase->getObjectById('skin',$parentId,"skin,title_$langCode,title_long_$langCode,descr_$langCode");
        $skinProps=$sData[0];

        $pathData[]=array($skinProps['title_long_'.$langCode]);
        $pathData[]=array($this->view->l($type));
        
       // var_dump($pathData);
	        
//        BREADCRUMB PATH
		$pathArr[]=array($this->view->l('Home'),'/');
	    $pathArr[]=array($this->view->l('The 5 Skins'),"/skin");
	    $pathArr[]=array($skinProps['title_long_'.$langCode]);
	    $this->view->pathArr=$pathArr;
	    
        $this->_nsHwPages->pathData=$pathData;
        //print_r($allItems);

        //$this->_nsHwPages->setExpirationHops(2);


        //
        if (sizeof($itemArr)>0){
          foreach($itemArr as $item){
            $idFld=$itemViewConfig['id'];
            $ids[]=$item[$idFld];

          }
          $idStr=implode('-',$ids);

          $this->view->relatedItems=$this->cntRelatedPerParent($type,$ids);
          $this->view->relLink=$this->getRelLinks($type,$ids);
          $this->view->buyItems=$this->getShopItems($type,$ids);
        }


        //$count=$this->_kBase->countObjects("[$parent,$type]", $parentId, $itemViewConfig['search'],'',$selectedFields);
        //$itemArr=$this->_kBase->selectFromKb("[$parent,$type]",$parentId,$itemViewConfig['search'],'',$selectedFields,$from,$limit,$order_by,$order_dir);

        //print $parentId."<br>";



        break;
      case 'mt':

        //find maintitle id as per parentId (which is like - the_general_mobilisation_of_the_eye)
        $mtArr=$this->_kBase->selectFromKb('mt','','mt','','mt,maintitle_name, maintitle_name_en');

        foreach($mtArr as $i=>$mtRow){
          $mtName=$mtRow['maintitle_name'];
          if (!$mtName){
            $mtName=$mtRow['maintitle_name_en'];
          }
          //print $mtName;
          $mtName=str_replace(array('/',' ','&','#'),'_',strtolower($mtName));
          if ($mtName==$parent_id){
            $parentId=$mtRow['mt'];
            break;
          }
        }
        //print $parentId."<br>";

        //show hl of mt related items, rather then related of mt!!!
        //i.e. find related items of childs of mt
        $pageAdapter=new Hw_KbPaginator();
        $pageAdapter->path="[mt,hl,text]";
        $pageAdapter->parentId=$parentId;
        $pageAdapter->searchFields='id';
        $pageAdapter->searchString='';
        $pageAdapter->selectedFields=$selectedFields;
        $pageAdapter->order_by=$order_by;
        $pageAdapter->order_dir=$order_dir;

        $itemArr=new Zend_Paginator($pageAdapter);
        //$pager=Zend_Paginator::factory($itemArr);
        $itemArr->setCurrentPageNumber($from);
        $itemArr->setItemCountPerPage($limit);
        $count=$itemArr->getTotalItemCount();

        if (sizeof($itemArr)){

          $ids=array();
          //$itemArr=$this->_kBase->selectFromKb("[mt,hl,text]",$parentId,'id','',$selectedFields);
          foreach($itemArr as $i=>$item){
            $ids[]=$item['txt'];
          }
          //print_r($ids);
          $this->view->relLink=$this->getRelLinks('mt,hl',$ids);
          $this->view->buyItems=$this->getShopItems('mt,hl',$ids);

        }
        
        //        BREADCRUMB PATH
        $sData=$this->_kBase->getObjectById('skin',$parentId,"skin,title_$langCode,title_long_$langCode,descr_$langCode");
        $skinProps=$sData[0];
		$pathArr[]=array($this->view->l('Home'),'/');
	    $pathArr[]=array($this->view->l('The 5 Skins'),"/skin");
	    $pathArr[]=array($skinProps['title_long_'.$langCode]);
	    $this->view->pathArr=$pathArr;




        //$count=$this->_kBase->countObjects("[mt,hl,text]",$parentId,'id','',$selectedFields);
        //$itemArr=$this->_kBase->selectFromKb("[mt,hl,text]",$parentId,'id','',$selectedFields);
        //print "\"[mt,hl,text]\",$parentId,'id','',$selectedFields";




        //print_r($relArr);

        break;
      default:
        //print $parent."-";
        //print $type.'-';
        //die();
        $parent_id=str_replace('-',',',$parent_id);
        //$parent_id=$this->cleanSelected($parent_id);
        //print "[$parent,$type], $parent_id <br>";


        $pageAdapter=new Hw_KbPaginator();
        $pageAdapter->path="[$parent,$type]";
        $pageAdapter->parentId=$parent_id;
        $pageAdapter->searchFields=$itemViewConfig['search'];
        $pageAdapter->searchString='';
        $pageAdapter->selectedFields=$selectedFields;
        $pageAdapter->order_by=$order_by;
        $pageAdapter->order_dir=$order_dir;

        /*
         * prepare session for item view page
         */
        $allItemsOpts=array(
         'path'=>"[$parent,$type]",
         'parentId'=>$parent_id,
         'searchFields'=>$itemViewConfig['search'],
         'searchString'=>'',
         'selectedFields'=>$selectedFields,
         'order_by'=>$order_by,
         'order_dir'=>$order_dir
        );

        $from2=0;$limit2=1000;
        $allItems=$this->_kBase->selectFromKb($allItemsOpts['path'],$allItemsOpts['parentId'],$allItemsOpts['searchFields'],$allItemsOpts['searchString'],$viewFields,$from2,$limit2,$allItemsOpts['order_by'],$allItemsOpts['order_dir']);
        $this->_nsHwPages->allItems=$allItems;
        $pathData[]=array($this->view->l($parent));
        $pathData[]=array($this->view->l($type));
        $this->_nsHwPages->pathData=$pathData;

      $mt = "";
      $skin="";
      $query = "link(".$type.",'".$id."',ObjPar,ObjParId).";
      
	    $parents = $this->_kBase->exec($query);
	    $json=new Zend_Json();
	    $parents_arr =$json->decode($parents);
	  	foreach($parents_arr as $parent){
			if ($parent[3]=="mt"){
				$g_query = "link(mt,'".$parent[4]."',ObjPar,ObjParId).";
	  			$g_parents = $this->_kBase->exec($g_query);
	  			$g_parents_arr =$json->decode($g_parents);
	  			foreach($g_parents_arr as $g_parent){
	  				if ($g_parent[3]=="skin"){
	  					$mt = $parent[4];
	  					$skin = $g_parent[4];
	  				}
	  			}
	  		}
	    }
	     $langCode=$this->_session->langArr['lang_alt_short'];
	      $sData=$this->_kBase->getObjectById('skin',$skin,"skin,title_$langCode,title_long_$langCode,descr_$langCode");
	      $skinProps=$sData[0];
        
//        BREADCRUMB PATH
		$pathArr[]=array($this->view->l('Home'),'/');
	    $pathArr[]=array($this->view->l('The 5 Skins'),"/skin");
	    $pathArr[]=array($skinProps['title_long_'.$langCode]);
	    $this->view->pathArr=$pathArr;
        
        //$this->_nsHwPages->setExpirationHops(2);


        $itemArr=new Zend_Paginator($pageAdapter);
        //$pager=Zend_Paginator::factory($itemArr);
        //print_r($itemArr);


        $itemArr->setCurrentPageNumber($from);
        $itemArr->setItemCountPerPage($limit);

        $count=$itemArr->getTotalItemCount();


        //$count=$this->_kBase->countObjects("[$parent,$type]", $parent_id, $itemViewConfig['search'],'',$selectedFields);
        //$itemArr=$this->_kBase->selectFromKb("[$parent,$type]",$parent_id,$itemViewConfig['search'],'',$selectedFields,$from,$limit,$order_by,$order_dir);
        if (sizeof($itemArr)){

          foreach($itemArr as $i=>$item){
            $ids[]=$item[$itemViewConfig['id']];
          }
          $idStr=implode('-',$ids);
          $this->view->relLink=$this->getRelLinks($type,$ids);
          $data=$this->cntRelatedPerParent($type,$ids);
          //print_r($data);
          $this->view->relatedItems=$data;
          $this->view->buyItems=$this->getShopItems($type,$ids);
        }


    }
    //print $parentId;


    //print_r($itemViewConfig);


    //print_r($allItems);

    //print "$parent, $parentId, $type,{$itemViewConfig['search']},'',$selectedFields";

    if ($count){

      //$allurl=$_SERVER['REQUEST_URI'];
      //$urlpath=parse_url($allurl,PHP_URL_PATH);

      //$this->view->allurl=urlencode($urlpath);

      $this->view->idStr=urlencode($this->view->transId($idStr));
      $this->view->itemViewConfig=$itemViewConfig;
      $this->view->objectCategory=$this->_dataConfig['objectCategory'];
      $this->view->itemProp=$this->_dataConfig['itemProp'];
      $this->view->type=$type;
      $this->view->itemArr=$itemArr;
      $this->view->count=$count;
      //      print $from.'<br>';
      //      print $count.'<br>';
      if ($from<1){
        $from1=1;
      }else{
        $from1=$from;
      }
      //      print $from1.'<br>';
      $this->view->fromStr=($from1-1)*$limit+1;
      $this->view->toStr=($count < $from1*$limit)?($count):($from1*$limit);
    }
    $this->_helper->layout->setLayout('skins');

  }

  public function autoYearAction(){
    //$this->_helper->viewRenderer->setNoRender();
    //$this->_helper->layout->disableLayout();

    //$type=$this->_getParam('type');

    //$type=array_intersect($type,array('apa','arch','hwg','jw','paint','tap','text','shop'));
    //$type=array_unique($type);

    $filter=mysql_escape_string(urldecode($_GET['q']));
    $ac=$this->_helper->autoComplete;

    $kbField=new KbField();
    $kbField->select()->distinct(true);
    $filter=mysql_escape_string($filter);

    $whereStr="field_type='year' && field_val LIKE '%$filter%'";
    //$mail=new Hw_Mail();
    //$mail->debugSend('where',$whereStr);
    $data=$kbField->fetchAll($whereStr,array('field_val'));
    $found=array();
    foreach($data as $row){
      if (!$found[$row['field_val']]){
        $found[$row['field_val']]=1;
        $resArr[]=stripslashes($row['field_val']).'|'.$row['id'];
      }
    }
    print $ac->sendAutoCompletion($resArr);


  }

  public function autoWorkNumberAction(){
    //$this->_helper->viewRenderer->setNoRender();
    //$this->_helper->layout->disableLayout();

    $type=$this->getArrayParam('type');
    //print_r($type);
    $limit=$this->_getParam('limit',10);

    if (!$type) return false;

    $filter=mysql_escape_string(urldecode($_GET['q']));
    $ac=$this->_helper->autoComplete;

    $kbField=new KbField();
    $filter=mysql_escape_string($filter);

    foreach($type as $typ){
      $catArr[]="'".$typ."'";
    }
    if ($catArr){
      $catStr=implode(',',$catArr);
    }
    $catWhere="item_type IN (".$catStr.")";

    $whereStr=$catWhere." && field_type='workNumber' && field_val LIKE '%$filter%'";
    //fb($whereStr,'whereStr');
    //print $whereStr;
    //$mail=new Hw_Mail();
    //$mail->debugSend('where',$whereStr);
    $data=$kbField->fetchAll($whereStr,array('field_val'),$limit);
    foreach($data as $row){
      $tmpArr=preg_split('/\\s+/',$row['field_val']);
      foreach($tmpArr as $wordVal){
        if (strpos($wordVal,$filter)===0){
          $resArr[]=stripslashes($row['field_val']).'|'.$row['id'];
        }
      }

    }
    print $ac->sendAutoCompletion($resArr);


  }
  
  public function autoWorkIdAction(){
    //$this->_helper->viewRenderer->setNoRender();
    //$this->_helper->layout->disableLayout();

    $type=$this->getArrayParam('type');
    //print_r($type);
    $limit=$this->_getParam('limit',10);

    if (!$type) return false;

    $filter=mysql_escape_string(urldecode($_GET['q']));
    $ac=$this->_helper->autoComplete;

    $kbField=new KbField();
    $filter=mysql_escape_string($filter);

    foreach($type as $typ){
      $catArr[]="'".$typ."'";
    }
    if ($catArr){
      $catStr=implode(',',$catArr);
    }
    $catWhere="item_type IN (".$catStr.")";

    $whereStr=$catWhere." && field_type='workId' && field_val LIKE '%$filter%'";
    fb($whereStr,'whereStr');
    //print $whereStr;
    //$mail=new Hw_Mail();
    //$mail->debugSend('where',$whereStr);
    require_once("Zend/Db/Table.php");
    $select=$kbField->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
    				->distinct(true)->where($whereStr)
    				->columns(array('field_val','id'))->group('field_val');
    
    $data=$kbField->fetchAll($select,array('field_val'),$limit);
    foreach($data as $row){
      $tmpArr=preg_split('/\\s+/',$row['field_val']);
      foreach($tmpArr as $wordVal){
        if (strpos($wordVal,$filter)===0){
           	
           $resArr[]=stripslashes($row['field_val']).'|'.$row['id'];
        }
      }

    }
    print $ac->sendAutoCompletion($resArr);


  }



  public function autoAction(){
    //$this->_helper->viewRenderer->setNoRender();
    //$this->_helper->layout->disableLayout();

  	$resArr=array();
    $type=$this->getArrayParam('type');


    $filter=mysql_escape_string(urldecode($_GET['q']));
    $ac=$this->_helper->autoComplete;

    $kbCat=new KbCat();
    $filter=mysql_escape_string($filter);
    if ($type){
      //
      foreach($type as $j=>$typ){
        $typeArr2[]="'$typ'";
      }
      $tempStr=implode(',',$typeArr2);

      $typeStr="cat_type IN ($tempStr) AND ";
    }
    $whereStr="$typeStr concat(cat_par_en,' ',cat_name_en) LIKE '%$filter%'";

    //die();
    //$mail=new Hw_Mail();
    //$mail->debugSend('where',$whereStr);
    $data=$kbCat->fetchAll($whereStr,array('cat_par_en','cat_name_en'));
    
    $i=0;
    foreach($data as $row){
    	$i++;
    	$catParEn='';
    	if ($row['cat_par_en']){
    		$catParEn=stripslashes($row['cat_par_en']).' > ';
    	}
    	$tmpArr=preg_split('/\\s+/',$row['cat_name_en']);
    	//print_r($tmpArr);print "<br>\n";
    	//check if array of found words is not subset of "ignore list"
    	foreach($tmpArr as $tmpVal){
    		if (stripos($tmpVal,$filter)===0){
    			$foundWords[$i][]=$tmpVal;
    		}
    	}
    	$interArr=array();
    	if ($foundWords[$i]){
    		$interArr=array_intersect($this->_dataConfig['searchIgnore'],$foundWords[$i]);
    		//var_dump($this->_dataConfig['searchIgnore']);
    		//var_dump($foundWords[$i]);
    		
    		//fb(array('inter'=>$interArr,'found'=>$foundWords[$i]));
    		$diff=array_diff($foundWords[$i],$interArr);
    		//print_r($diff);
    		if ($diff){
    			foreach($tmpArr as $tmpVal){
    				if (stripos($tmpVal,$filter)===0){
    					$resArr[]=$catParEn.stripslashes(str_replace(array("<br>"),array("/"),$row['cat_name_en'])).'|'.$row['cat_name_en'];
    					break;
    				}
    			}
    		}
    	}

    }
    //print_r($resArr);
    print $ac->sendAutoCompletion($resArr);
  }

  public function notEmpty($item){
    if (!empty($item)){
      return true;
    }else{
      return false;
    }
  }
  public function objectListAction(){
    //$this->_helper->viewRenderer->setNoRender();
    //$this->_helper->layout->disableLayout();
    $tt0 = microtime(true);
    fb("Start","objectListAction");
    
    $t0=microtime(true);
    
    $this->_helper->layout->setLayout('skins');
    $workNumber=$this->_getParam("work_number");
    $workId=$this->_getParam("work_id");
    $typeParam=$this->_getParam('type');
    $objectType=$this->_getParam('object_type');

    $from=$this->_getParam('page',0);

    //work_number=10&key_word=Start+Typing&cat_id=&free_text=&year_from=&year_to=&year=
    $params['type']=$typeParam;
    $params['object_type']=$objectType;
    $params['work_number']=$workNumber;
    $params['work_id']=$workId;
    $params['key_word']=$this->_getParam('key_word');
    $params['cat_id']=(int)$this->_getParam('cat_id');
    $params['free_text']=trim($this->_getParam('free_text'));
    $params['year_from']=(int)$this->_getParam('year_from');
    $params['year_to']=(int)$this->_getParam('year_to');
    $params['year']=(int)$this->_getParam('year');
    if (@stristr($this->view->l('Start Typing'),$params['key_word'])){
      $params['key_word']='';
    }
    
    $keyWord=htmlentities($params['key_word'],ENT_QUOTES,'UTF-8');


    $params=array_filter($params,array($this,'notEmpty'));

    //print_r($params);
    //die();


    $whereArr=array();
    $selectArr=array();
    $whereList='';
    $selectList='';

    $itemViewConfig=$this->_dataConfig['itemView'];
    $itemWorkNumber=$this->_dataConfig['itemWorkNumber'];
    $itemTitles=$this->_dataConfig['itemTitles'];

    $workTypes=$this->_dataConfig['objectCategory']['work'];
    $itemIds=$this->_dataConfig['itemIds'];

    $this->view->objectCategory=$this->_dataConfig['objectCategory'];

    $this->view->itemProp=$this->_dataConfig['itemProp'];
    $searchFields=$this->_dataConfig['searchFields'];



    $limit=5;

    if (!in_array($objectType,$workTypes)){
      $objectType='';
    }


    if (is_array($typeParam)){
      $typeParam=array_intersect($typeParam,$workTypes);
    }elseif($typeParam){
      //filter type param, sanitation
      $typeParam=array_intersect(array($typeParam),$workTypes);

    }
    //print_r($typeParam);
    //die();
    if (!$typeParam) $typeParam=$workTypes;

    if (is_array($params)){
      $typeList="[".implode(',',$typeParam)."]";

        foreach($typeParam as $type){
          $strictSearchTermArr=array();
          //all $strictSearchTermArr items are searched with 'AND' condition, if we need to put OR ,- necessary to create several groupd for same type
          //[hwg-[workNumber-'154 A',year-1987]] - means find graphics which have workNumber=154 A AND year-1987.
          //[hwg-[workNumber-'154 A',year-1987],hwg-[workNumber-'154B']] - find graphics which is with (number 154 A && year=1987) OR (number 154B)
            
          if ($workNumber){
            $strictSearchTermArr[]="{$itemWorkNumber[$type]}-".$this->wrapTermStr($workNumber);
          }
          
        if ($workId){
            $strictSearchTermArr[]="{$itemIds[$type]}-".$this->wrapTermStr($workId);
          }
          
          if ($params['year']){
            $strictSearchTermArr[]="{$searchFields[$type]['year']}-{$params['year']}";
          }
          if ($keyWord){
          	$strictSearchTermArr[]="{$searchFields[$type]['field']}-'{$keyWord}'";
          }

          if ($params['year_from']){
            $fromFiled=$searchFields[$type]['year'];
            $fromValue=$params['year_from'];
          }else{
            //$fromFiled=$searchFields[$type]['year']; Comented by GC
            $fromFiled="id";
            $fromValue=0;
          }

          if ($params['year_to']){
            $toFiled=$searchFields[$type]['year'];
            $toValue=$params['year_to'];
          }else{
            //$toFiled=$searchFields[$type]['year']; Comented by GC
            $toFiled="id";
            $toValue=3000;
          }


          //free text fields
          $ftArr=array();
          $ftStr='';
          if ($params['free_text']){

          	$freeText=htmlentities($params['free_text'],ENT_QUOTES,'utf-8');
          	foreach($searchFields[$type]['free_text'] as $ftFld){
          		$ftArr[]="$type-[$ftFld-".$this->wrapTermStr($freeText)."]";
          	}
          	if ($ftArr){
          		$ftStr=implode(',',$ftArr);
          		$whereArr[$type]="$ftStr";
          	}
          }else{
          	$ftStr="{$itemTitles[$type]}-''";
          	$whereArr[$type]="$type-[$ftStr]";
          }


          $strictSearchTerm=implode(',',$strictSearchTermArr);

          $selectArr[$type]="$type-[id,{$itemIds[$type]},{$itemTitles[$type]},{$itemWorkNumber[$type]}]";
          $strictSearchArr[$type]="$type-[$strictSearchTerm]";

        }
        //print_r($selectArr);

        $whereList="[".implode(',',$whereArr)."]";
        $strictSearchStr="[".implode(',',$strictSearchArr)."]";
        $selectList="[".implode(',',$selectArr)."]";
        
        $t1=microtime(true);
        fb($t1-$t0,"whereList - Elapsed time: ");
        
      if ($whereList && $selectList){
        
        $t0=microtime(true);
        
        if (($whereList=="[jw-[title-'']]" and $strictSearchStr=='[jw-[]]') 
        or ($whereList =="[paint-[title-'']]" and $strictSearchStr=='[paint-[]]')
        or ($whereList =="[hwg-[title-'']]" and $strictSearchStr=='[hwg-[]]')
        or ($whereList =="[tap-[title-'']]" and $strictSearchStr=='[tap-[]]')
        or ($whereList =="[apa-[title-'']]" and $strictSearchStr=='[apa-[]]')
        or ($whereList =="[arch-[title-'']]" and $strictSearchStr=='[arch-[]]')) { //la consulta es simple
          
          switch ($type) {
           	case 'jw':
        	   $data[] = array("jw" => 276);
        	break;
          
        	case 'paint':
        	   $data[] = array("paint" => 801);
        	break;
        	
        	case 'hwg':
        	   $data[] = array("hwg" => 148);
        	break;
        	
        	case 'tap':
        	   $data[] = array("tap" => 69);
        	break;
        	
        	case 'apa':
        	   $data[] = array("apa" => 421);
        	break;
        	
        	case 'arch':
        	   $data[] = array("arch" => 353);
        	break;
        	
        	default:
        		;
        	break;
          }
          $consulta_simple=true;
          
        } else { 
          fb("countSearchObjectRange2"," Consulta compleja:" . $whereList . " - " . $strictSearchStr);
          $data = $this->_kBase->countSearchObjectRange2($typeList,$whereList,$strictSearchStr,$selectList,$fromFiled,$fromValue,$toFiled,$toValue);
          $consulta_simple=false;
        }
        
        
        $t1=microtime(true);
        fb($t1-$t0,"countSearchObjectRange2 - Elapsed time: ");
        
        //fb($data,HwGetPlace(__FILE__,__LINE__).':data=countSearchObjectRange2');
        //die();
      }

      if ($data){

        foreach($data as $i=>$row){
          $key=key($row);
          $countData[$key]=$row[$key];
        }

        reset($countData);
        if ($countData){
          $key=key($countData);
          if (sizeof($countData)==1 && $countData[$key]==1){
            //one item found only
       
            $objData = $this->_kBase->searchObjectRange2("[$key]","[{$whereArr[$key]}]","[".$strictSearchArr[$key]."]","[".$selectArr[$key]."]",$fromFiled,$fromValue,$toFiled,$toValue,0,1);
            $id = $objData[0][$itemIds[$key]];
            
            $this->_forward("view-$id",$key);

          }else{
            
            $t0=microtime(true);
            arsort($countData);

            if (!$objectType){
              $objectType=key($countData);
            }
            //$objectCount=$data[0][$objectType];
            
            unset($countData[$objectType]);
			$selectListArr=array();
			$selectListArr[]='id';
			$selectListArr[]=$itemIds[$objectType];
			$selectListArr[]=$itemViewConfig[$objectType]['list']['title'];
			$selectListArr[]=$itemViewConfig[$objectType]['list']['title_default'];
			$selectListArr[]=$itemWorkNumber[$objectType];
			$selectListArr[]=$itemViewConfig[$objectType]['list']['image_id'];
			$selectListArr[]='descriptionPortfolio';
			$selectListArr[]='descriptionCategoryKeyword';
			
			$selectListArr=array_unique($selectListArr);
			$selectListStr=implode(',',$selectListArr);
			
			//TODO: parace que siempre devuelve array[0]
            $this->view->otherItems=$this->getOtherLinks($countData); 
            $this->view->workNumber=$workNumber;
            $t1=microtime(true);
            fb($t1-$t0,"getOtherLinks - Elapsed time: ");
            
            $pageAdapter=new Hw_KbPaginator();
            $pageAdapter->_kBase=$this->_kBase;
            $pageAdapter->dataFunction="searchObjectRange2";
            $pageAdapter->counterFunction="countSearchObjectRange2";
            $pageAdapter->typeList="[$objectType]";
            $pageAdapter->whereList="[{$whereArr[$objectType]}]";
            $pageAdapter->strictSearch="[".$strictSearchArr[$objectType]."]";
            $pageAdapter->selectList="[$objectType-[$selectListStr]]";
            $pageAdapter->order_by=$searchFields[$objectType]['order_by'];
            $pageAdapter->order_dir=$searchFields[$objectType]['order_sort'];
            $pageAdapter->from_field=$fromFiled;
            $pageAdapter->from_val=$fromValue;
            $pageAdapter->to_field=$toFiled;
            $pageAdapter->to_val=$toValue;
            $from2=0;
            $limit2=1000;
            
            $t0 = microtime(true);
            
            if ($consulta_simple) { //Si la consulta es simple uso el cache
               $config = $this->_config;
               $CacheDir = $config->CacheDir;
               
               if ($cache === null) {
                 
                $frontendOptions = array(
           			'lifetime' => null, // cache lifetime forever
           			'automatic_serialization' => true
                );
                
                $backendOptions = array(
            		'cache_dir' => $CacheDir // Directory where to put the cache files
                );
                // getting a Zend_Cache_Core object
                $cache = Zend_Cache::factory('Core',
                                     'File',
                                      $frontendOptions,
                                      $backendOptions);              
              }
              
              $result = $cache->load($objectType);
              
              if ($result === false) {
                
                $allItems=$this->_kBase->searchObjectRange2($pageAdapter->typeList,$pageAdapter->whereList,$pageAdapter->strictSearch,
                     $pageAdapter->selectList,$pageAdapter->from_field,$pageAdapter->from_val,$pageAdapter->to_field,$pageAdapter->to_val=$toValue,
                     $from2,$limit2,$pageAdapter->order_by,$pageAdapter->order_dir);
                     
                if ($objectType=='arch') {
                  $allItems = $this->array_sort($allItems, 'id', SORT_ASC);
                }
                     
                $cache->save($allItems, $objectType, array($objectType));
                                              
              } else {
                
                $allItems = $result;
              }
              
              $itemArr = new Zend_Paginator(new Zend_Paginator_Adapter_Array($allItems));
              $itemArr->setCurrentPageNumber($from);
              $itemArr->setItemCountPerPage($limit);
              $count=$itemArr->getTotalItemCount();
              
              $t1 = microtime(true);
              fb($t1-$t0,"Consulta simple - Elapsed time: ");
            
            } else {//Si la consulta es compleja uso la base prolog

              $allItems=$this->_kBase->searchObjectRange2($pageAdapter->typeList,$pageAdapter->whereList,$pageAdapter->strictSearch,
              $pageAdapter->selectList,$pageAdapter->from_field,$pageAdapter->from_val,$pageAdapter->to_field,$pageAdapter->to_val,
              $from2,$limit2,$pageAdapter->order_by,$pageAdapter->order_dir);
              
              
              $itemArr=new Zend_Paginator($pageAdapter);
              //$pager=Zend_Paginator::factory($itemArr);
              $itemArr->setCurrentPageNumber($from);
              $itemArr->setItemCountPerPage($limit);
              $count=$itemArr->getTotalItemCount();
            
              $t1 = microtime(true);
              fb($t1-$t0,"Consulta compleja - Elapsed time: ");
            
            }
                                                        
            $this->_nsHwPages->allItems=$allItems;
            
            //$itemArr=$this->_kBase->searchObject("[$objectType]","[$objectType-[{$itemWorkNumber[$objectType]}-'$workNumber']]","[$objectType-[id,{$itemTitles[$objectType]},{$itemWorkNumber[$objectType]}]]");
            

            if (sizeof($itemArr)>0){
              foreach($itemArr as $i=>$item){
                $ids[]=$item[$itemViewConfig[$objectType]['list']['id']];
                //print_r($item);
              }
              $idStr=implode('-',$ids);
              
              $t0 = microtime(true);
              $this->view->relLink=$this->getRelLinks($objectType,$ids);
              $this->view->relatedItems=$this->cntRelatedPerParent($objectType,$ids);
              //$this->view->buyItems=$this->getShopItems($objectType,$ids); Comentado por GC
              $t1 = microtime(true);
              fb($t1-$t0,"getRelLinks - Elapsed time: ");
              
              $this->view->type=$typeParam;
              $this->view->object_type=$objectType;

              $this->view->itemViewConfig=$itemViewConfig[$objectType]['list'];
              $this->view->params=$params;
            }

            $this->view->idStr=urlencode($this->view->transId($idStr));

            $this->view->type=$objectType;
            $this->view->itemArr=$itemArr;
            $this->view->count=$count;
            if ($from<1){ 
              $from1=1; 
            }else{
              $from1=$from;
            }
            $this->view->fromStr=($from1-1)*$limit+1;
            $this->view->toStr=($count < $from1*$limit)?($count):($from1*$limit);


          }

        }
      }else{
        print "No matching data found.";
      }

      //$data=$this->_kBase->searchObject($typeList,$whereList,$selectList);
      //print_r($data);
    }
    /*
     $data=$this->_kBase->searchObject("[tap,apa,arch,paint,hwg,jw]","[tap-[workNumber,year-'19']]","[tap-[id,title,workNumber,year]]");
     print_r($data);


     $data=$this->_kBase->countSearchObject("[tap,apa]","[tap-[year-'19'],apa-[workNumber-'20']]","[tap-[id,title,workNumber,year],apa-[id,title,workNumber]]");
     print_r($data);
     */
    $tt1 = microtime(true);
    fb("END","objectListAction");
    fb($tt1-$tt0,"objectListAction - Elapsed time: ");

  }


  public function cmp_count($a,$b){
    $aKey=key($a);
    $bKey=key($b);
    if ($a[$aKey]>$b[$bKey]){
      $res=-1;
    }elseif($a[$aKey]==$b[$bKey]){
      $res=0;
    }else{
      $res=1;
    }
    return $res;
  }




  public function slidesAction(){
    $id=$this->_getParam('id');
    $type=$this->_getParam('type');

    $id=$this->view->transId($id,false);

    $itemViewProps=$this->_dataConfig['itemView'][$type]['list'];
    //print_r($itemViewProps);



    $types=array_keys($this->_dataConfig['itemView']);


    if (!in_array($type,$types)) {
      print $this->view->l('Error type');
      return false;
    }


    //print_r($this->_nsHwPages->pageAdapter);
    if (is_array($this->_nsHwPages->allItems)){
      $allItems=$this->_nsHwPages->allItems;
      //print_r($allItemsOpts);
      $itemsOnPage=6;//how many items to show on one page.

      //find current item position
      foreach($allItems as $n=>$item){
        if ($item[$itemViewProps['id']]==$id){
          $curNum=$n;
          break;
        }
      }

      $leftPos=$curNum-$itemsOnPage/2;
      if ($leftPos<0){
        $leftPos=0;
      }
      $showItems=array_slice($allItems,$leftPos,$itemsOnPage,true);
      $prev=$showItems[$curNum-1];
      $next=$showItems[$curNum+1];
      //print_r($prev);
      $this->view->prev=$prev[$itemViewProps['id']];
      $this->view->next=$next[$itemViewProps['id']];

      //print "cur:$n,left:".$curNum-$itemsOnPage/2;
      //print_r($showItems);


      //$this->_nsHwPages->setExpirationHops(3);
      $this->view->showItems=$showItems;
      $this->view->idField=$itemViewProps['id'];
    }
    /*

    */

    //print "id:$id, type=$type";
    //print_r($types);
    $itemArr=$this->_kBase->getObjectById($type,$id);
    //print_r($itemArr);

    $this->view->curNum=$curNum;
    $this->view->pathData=$this->_nsHwPages->pathData;
    $this->view->name=$this->_dataConfig['itemProp'][$type]['name'];
    $this->view->itemView=$this->_dataConfig['itemView'][$type]['list'];

    $this->view->type=$type;
    $this->view->id=$id;

    $this->view->relLink=$this->getRelLinks($type,array($id));
    $this->view->buyItems=$this->getShopItems($type,array($id));

    $this->view->langArr=$this->_session->langArr;

    $this->view->item=$itemArr[0];
    $this->view->count=sizeof($allItems);




    $this->_helper->layout->setLayout('skins');

  }


  public function viewAction(){
    
    $id=$this->_getParam('id');
    $type=$this->_getParam('type');
    $this->view->subtype = $this->_getParam('subtype');

    $id=$this->view->transId($id,false);

    $itemViewProps=$this->_dataConfig['itemView'][$type]['list'];
    //print_r($itemViewProps);
    

    // print $id;

    $types=array_keys($this->_dataConfig['itemView']);


    if (!in_array($type,$types)) {
      print $this->view->l('Error type');
      return false;
    }


    //print_r($this->_nsHwPages->pageAdapter);
    if (is_array($this->_nsHwPages->allItems)){
      $allItems=$this->_nsHwPages->allItems;
      //print_r($allItemsOpts);

      //find current item position

      $itemsOnPage=6;//how many items to show on one page.

      foreach($allItems as $n=>$item){
        if ($item[$itemViewProps['id']]==$id){
          $curNum=$n;
          break;
        }
      }

      $leftPos=$curNum-$itemsOnPage/2;
      if ($leftPos<0){
        $leftPos=0;
      }
      $showItems=array_slice($allItems,$leftPos,$itemsOnPage,true);
      $prev=$showItems[$curNum-1];
      $next=$showItems[$curNum+1];
      //print_r($prev);
      $this->view->prev=$prev;
      $this->view->next=$next;

      //print "cur:$n,left:".$curNum-$itemsOnPage/2;
      //print_r($showItems);
      
      $mt = "";
      $skin="";
      $query = "link(".$type.",'".$id."',ObjPar,ObjParId).";
      
	    $parents = $this->_kBase->exec($query);
	    $json=new Zend_Json();
	    $parents_arr =$json->decode($parents);
	  	foreach($parents_arr as $parent){
			if ($parent[3]=="mt"){
				$g_query = "link(mt,'".$parent[4]."',ObjPar,ObjParId).";
	  			$g_parents = $this->_kBase->exec($g_query);
	  			$g_parents_arr =$json->decode($g_parents);
	  			foreach($g_parents_arr as $g_parent){
	  				if ($g_parent[3]=="skin"){
	  					$mt = $parent[4];
	  					$skin = $g_parent[4];
	  				}
	  			}
	  		}
	    }
	     $langCode=$this->_session->langArr['lang_alt_short'];
	      $sData=$this->_kBase->getObjectById('skin',$skin,"skin,title_$langCode,title_long_$langCode,descr_$langCode");
	      $skinProps=$sData[0];
        
//        BREADCRUMB PATH
		$pathArr[]=array($this->view->l('Home'),'/');
	    $pathArr[]=array($this->view->l('The 5 Skins'),"/skin");
	    $pathArr[]=array($skinProps['title_long_'.$langCode]);
	    $this->view->pathArr=$pathArr;

      //$this->_nsHwPages->setExpirationHops(3);
      $this->view->showItems=$showItems;
      //print_r($showItems);
      $this->view->idField=$itemViewProps['id'];
    }

    $itemArr=$this->_kBase->getObjectById($type,$id);
        
    $this->view->name=$this->_dataConfig['itemProp'][$type]['name'];
    $this->view->title=$this->_dataConfig['itemProp'][$type]['title'];
    $this->view->itemView=$this->_dataConfig['itemView'][$type]['list'];
    $this->view->objectCategory=$this->_dataConfig['objectCategory'];

    $this->view->type=$type;
    $this->view->id=$id;

    
    $this->view->relLink=$this->getRelLinks($type,array($id));
    $this->view->buyItems=$this->getShopItems($type,array($id));

    $this->view->langArr=$this->_session->langArr;

    $this->view->item=$itemArr[0];

    $this->_helper->layout->setLayout('skins');
    

  }


  public function searchAction(){
    $this->_nsHwPages->allItems=array();
    $this->_helper->layout->setLayout('skins');

  }

  public function array_sort($array, $on, $order=SORT_ASC)
  {
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
  }

}