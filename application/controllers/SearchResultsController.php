<?php

class SearchresultsController extends Hw_Controller_Action
{
	public function indexAction()
	{
		$this->workNames=array_keys(Zend_Registry::get('tables'));
		$docs=array_keys(Zend_Registry::get('docs'));
		$session=Zend_Registry::get('nsHw');
		//print_r($session);

    $lang=$session->lang;
		$session->referrer='SearchresultsController';
		$session->refUri=$_SERVER['REQUEST_URI'];
		$data=$session->data;
		$wkArr=array();
		//print_r($data);
		//die();
		$workType=$this->getRequest()->getParam('type');
		//print "workType:$workType";
		$titleArr=Zend_Registry::get('titles');
		$dateFieldArr=Zend_Registry::get('hw_dates');
		$workFldArr=Zend_Registry::get('hw_work');
		$this->view->titleArr=$titleArr;

		$this->view->lang=$lang;
		if (!in_array($workType,$this->workNames)){
			$workType='';
		}

		$typeFields=array(
		'apa'=>"apa_technique LIKE '%s' OR apa_technique_description LIKE '%s'",
		'arch'=>"technique LIKE '%s' OR technique_description LIKE '%s'",
		'graph'=>"technique LIKE '%s' OR technique_description LIKE '%s'",
		'paint'=>"technique LIKE '%s' OR technique_description LIKE '%s'",
		'tap'=>"tap_material LIKE '%s'"
		);

		$params=$this->getRequest()->getParams();
		//print_r($params);
		foreach($params as $parFld=>$parVal){
			if (substr($parFld,0,5)=='item_'){
				$itemId=(int)$parVal;
				$itemType=str_replace('item_','',$parFld);

				if (!in_array($itemType,$this->workNames)){
					$itemId='';
					$itemType='';
					continue;
				}else{
					break;
				}
			}
		}


		if ($itemId){
		 //print "itemId=$itemId&itemType=$itemType<br>";
		 $className=ucfirst($workType);
		 $workObjFinder = new $className();
		 $cntWorks=$workObjFinder->countItemWorks($itemId,$itemType);
		 //print_r($cntWorks);

		 require_once 'Pager.php';
		 $count=5;
		 //print "<br>work:".$work."<br>";
		 $offset=(int)$this->getRequest()->getParam('item',1);
		 if (!$offset){
		 	$offset=1;

		 }
		 //wkName=$itemType
		 //print "offset=$offset<br>";
		 //$wk=new Paint();
		 $num_products=$cntWorks[$workType];
		 $pager_options = array(
        'mode'       => 'Sliding',
        'append'     => false,
        'perPage'    => $count,
        'delta'      => $offset,    
        'urlVar'     => 'item',
        'path'       => "/searchresults/index/type/$workType/item_$itemType/$itemId",
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

        $wkArr=$workObjFinder->itemWorks($itemId,$count,$from-1,$itemType,2);
        if (!in_array($workType,$docs)){
        foreach($wkArr as $i=>$oWrk){

        	//print_r($workFldArr);
        	$img=$this->view->hlpShowImage($workType,$oWrk[$workFldArr[$workType]],'100','small','src');
        	//$otherWorks[$i]=$oWrk;
        	$wkArr[$i]['img']=$img;
        }
        }
        //print_r($wkArr);
        //die();
        $skinCnt=array();
        $this->view->work=$workType;
        $this->view->links=$pager->links;
        $this->view->from=$from;
        $this->view->to=$to;
        $this->view->total=$num_products;
        $this->view->skinCnt=$cntWorks;
        $this->view->itemType=$itemType;
        $this->view->itemId=$itemId;
        //$this->view->tables=$this->workNames;
         
        //die();
		}elseif($data){
			?>
<div style="color: white;"><?


//we need to filter workes by skins only
if (!$data['skin'] || $data['skin']['all']){
	$skins=array(1,2,3,4,5);
}else{
	$skins=array_values($data['skin']);
}

//print_r($skins);

//print_r($data);
$skinCnt=array();
$wkListDone=false;

foreach($this->workNames as $wkName){
	$whereStr=array();
	$wClassname=ucfirst($wkName);
	//print $wClassname;
	$wObj=new $wClassname();



	$whereArr=array();
	if ($data[$wkName] || $data['search_words'] || $itemId){

		if ($data['search_words']){
			$keyw=mysql_escape_string($data['search_words']);
			if (!in_array($wkName,array('audio','video'))){
				$title=$titleArr[$wkName].'_'.$lang;
			}else{
				$title=$titleArr[$wkName];
			}
			$whereArr[]="(`$title` LIKE '%$keyw%')";
		}

		if ($data['work']['keyword']){
			$keyw=mysql_escape_string($data['work']['keyword']);
			if (!in_array($wkName,array('audio','video')) ){
				$title=$titleArr[$wkName].'_'.$lang;
			}else{
				$title=$titleArr[$wkName];
			}
			$whereArr[]="(`$title` LIKE '%$keyw%')";
		}


	 if ($data['work']['num']){
	 	$keyw=mysql_escape_string($data['work']['num']);
	 	$whereArr[]="`work_num` LIKE '%$keyw%'";
	 }
	 if (!$data[$wkName]['all']){
	 	$tmpArr=array();
	 	foreach($data[$wkName] as $keyVal=>$on){
	 		$keyVal=mysql_escape_string($keyVal);
	 		$keyVal="%$keyVal%";
	 		$tmpArr[]=sprintf($typeFields[$wkName],$keyVal,$keyVal);
	 	}
	 	$whereArr[]='('.implode(' OR ',$tmpArr).')';
	 }
	  
	 $whereStr[$wkName]=implode(' AND ',$whereArr);
	  
	}
	//print_r($whereStr);
	//die();


	if (in_array($wkName,array_keys(Zend_Registry::get('docs')))){
		//print $wObj->countSkinDocs($skin_id);
		$skinCnt[$wkName]=$wObj->countSkinDocs($skins,null,$whereStr[$wkName]);
	}else{
		$skinCnt[$wkName]=$wObj->countSkinWorks($skins,null,$whereStr[$wkName]);
	}
	if (!$wkListDone && ($workType==$wkName)){

		require_once 'Pager.php';
		$count=5;
		//print "<br>work:".$work."<br>";
		$offset=(int)$this->getRequest()->getParam('item',1);
		if (!$offset){
			$offset=1;

		}

		//print "offset=$offset<br>";
		//$wk=new Paint();
		$num_products=$skinCnt[$wkName];
		$pager_options = array(
        'mode'       => 'Sliding',
        'append'     => false,
        'perPage'    => $count,
        'delta'      => $offset,    
        'urlVar'     => 'item',
        'path'       => "/searchresults/index/type/$workType/",
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
        if (in_array($wkName,$docs)){
        	$wkArr=$wObj->skinDocs($skins,$count,$from-1,null,$whereStr[$wkName]);
        }else{
        	$wkArr=$wObj->skinWorks($skins,$count,$from-1,null,array("$wkName.*","maintitle.maintitle_skin_id"),$whereStr[$wkName]);
        }
        if (!in_array($wkName,$docs))
        foreach($wkArr as $i=>$oWrk){

        	//        	if (method_exists($wObj,'showImage') && $workFldArr[$wkName]){
        	//        		$img=$wObj->showImage($oWrk[$workFldArr[$wkName]],'100','small','src');
        	//        		//$otherWorks[$i]=$oWrk;
        	//        		$wkArr[$i]['img']=$img;
        	//
        	//        	}
        	//print $wkName;
        	$img=$this->view->hlpShowImage($wkName,$oWrk[$workFldArr[$wkName]],'100','small','src');
        	//$otherWorks[$i]=$oWrk;
        	$wkArr[$i]['img']=$img;
	}

	$ses=Zend_Registry::get('nsHw');
	$ses->otherWorksType=$wkName;

	$ses->otherWorks=$wkArr;
	$this->view->work=$wkName;
	$this->view->links=$pager->links;
	$this->view->from=$from;
	$this->view->to=$to;
	$this->view->total=$num_products;
	$wkListDone=true;
}



}
//print_r($wkArr);
//die();




?></div>
<?
$this->view->skinCnt=$skinCnt;
}
$this->view->date_fld_name=$dateFieldArr[$this->view->work];
$this->view->tables=$this->workNames;
$this->view->wkArr=$wkArr;
//print_r($this->view->wkArr);


//$archsFinder = new Arch();
//$this->view->arch = $archFinder->fetchByArchId($id);
$this->_helper->layout->setLayout('search_results');
}




public function norouteAction()
{
	// redirect to home page
	$this->_redirect('/');
}
}