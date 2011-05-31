<?php

class SlideshowController extends Hw_Controller_Action
{
	public function indexAction()
	{
		
		
		$this->workNames=array_keys(Zend_Registry::get('tables'));
		$docs=array_keys(Zend_Registry::get('docs'));
		$session=Zend_Registry::get('nsHw');
		//print_r($session);
		$data=$session->data;
		$wkArr=array();
		//print_r($data);
		//die();
		$workType=$this->getRequest()->getParam('type');
		//print "workType:$workType";
		$titleArr=Zend_Registry::get('titles');
		$this->view->titleArr=$titleArr;
		$lang=$this->view->lang=Zend_Registry::get('lang');
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
		if ($data){
			//print_r($data);
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

$wkName=$workType;
$wClassname=ucfirst($wkName);
//print $wClassname;
$wObj=new $wClassname();

$whereArr=array();
if ($data[$wkName]){

	if ($data['work']['keyword']){
		$keyw=mysql_escape_string($data['work']['keyword']);
		if (!in_array($wkName,$docs)){
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



if (in_array($wkName,array_keys(Zend_Registry::get('docs')))){
	//print $wObj->countSkinDocs($skin_id);
	$skinCnt[$wkName]=$wObj->countSkinDocs($skins,null,$whereStr[$wkName]);
}else{
	$skinCnt[$wkName]=$wObj->countSkinWorks($skins,null,$whereStr[$wkName]);

	require_once 'Pager.php';
	$count=1;
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
        'path'       => "/slideshow/index/type/$workType/",
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
        $links=$pager->getLinks($offset);
        $wkArr=$wObj->skinWorks($skins,$count,$from-1,null,array("$wkName.*","maintitle.maintitle_skin_id"),$whereStr[$wkName]);
        $this->view->work=$wkName;
        $this->view->links=$pager->links;
        //print_r($links['linkTagsRaw']['next']['url']);
        $this->view->next=$links['linkTagsRaw']['next']['url'];
        $this->view->prev=$links['linkTagsRaw']['prev']['url'];
        $this->view->from=$from;
        $this->view->to=$to;
        $this->view->curPage=$offset;
        $this->view->total=$num_products;


}

//print_r($wkArr);
$this->view->wkArr=$wkArr;
//print_r($this->view->wkArr);
//$this->view->skinCnt=$skinCnt;



?></div>
<?

}


//$archsFinder = new Arch();
//$this->view->arch = $archFinder->fetchByArchId($id);
$this->_helper->layout->setLayout('slideshow');
}




public function norouteAction()
{
	// redirect to home page
	$this->_redirect('/');
}
}