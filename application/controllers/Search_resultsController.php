<?php

class SearchresultsController extends Hw_Controller_Action
{
	public function indexAction()
	{
		if ($_POST['submit']){

			$data['work']=$_POST['work'];
			$data['period']=$_POST['period'];
			$data['skin']=$_POST['skin'];
			$data['name']=$_POST['name'];
			$data['paint']=$_POST['paint'];
			$data['arch']=$_POST['arch'];
			$data['text']=$_POST['text'];
			$data['graph']=$_POST['graph'];
			$data['tap']=$_POST['tap'];
			?>
<div style="color: white;"><?

if ($data['skin'] && !$data['skin']['all']){
	//we need to filter workes by skins only
	$skins=array_values($data['skin']);
	//print_r($skins);
	$whereStr['apa']="apa_technique LIKE '%offset%'";
  //print_r($skins);
	$skinCnt=array();
	$this->workNames=array_keys(Zend_Registry::get('tables'));
	foreach($this->workNames as $wkName){
		$wClassname=ucfirst($wkName);
		//print $wClassname;
		$wObj=new $wClassname();

		if (in_array($wkName,array_keys(Zend_Registry::get('docs')))){
			//print $wObj->countSkinDocs($skin_id);
			$skinCnt[$wkName]=$wObj->countSkinDocs($skins,null,$whereStr[$wkName]);
		}else{
			$skinCnt[$wkName]=$wObj->countSkinWorks($skins,null,$whereStr[$wkName]);
		}
	}
	//print_r($skinCnt);
	$this->view->skinCnt=$skinCnt;

	
}
?></div>
<?

}


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