<?php
class ImageController extends Hw_Controller_Action
{
  public function indexAction()
  {

  	$request = $this->getRequest();
    $form    = new Forms_Image();
	$form->addAll(array(
		'fold'=>$this->_getParam('fold'),
		'n'=>$this->_getParam('n')
	));

    if ($this->getRequest()->isPost()) {
	    if ($form->isValid($request->getPost())) {
      		if (!$form->image->receive()) {
             	print "Error receiving the file";
   			}
   			$data = $form->getValues();
//   			$imageIdFld=$this->_dataConfig['itemView'][$parentType][$mode]['image_id'];
//  			$pref=$this->_dataConfig['itemView'][$parentType][$mode]['thumb']['prefix_'.$size];
      		$file_info=array(
      		'path_small'=>DOCROOT.$this->_dataConfig['itemView'][$this->_getParam('fold')]["list"]["thumb"]["path_small"],
      		'path_medium'=>DOCROOT.$this->_dataConfig['itemView'][$this->_getParam('fold')]["list"]["thumb"]["path_medium"],
      		'path_original'=>DOCROOT.$this->_dataConfig['itemView'][$this->_getParam('fold')]["list"]["thumb"]["path_original"],
   			'prefix_small'=>$this->_dataConfig['itemView'][$this->_getParam('fold')]["list"]["thumb"]["prefix_small"],
   			'prefix_medium'=>$this->_dataConfig['itemView'][$this->_getParam('fold')]["list"]["thumb"]["prefix_medium"],
   			'prefix_original'=>$this->_dataConfig['itemView'][$this->_getParam('fold')]["list"]["thumb"]["prefix_original"]);  

      		
   			
   			$img = new Image(array('file_info'=>$file_info));
   			$img->setCode($data['image']);
   			$img->setSrc($form->getPath());
   			
   			$this->view->message = $img->save();
   			
//   			$output;
//   			$cmd = "ls -l ".$img->getSrc()."/".$img->getCode();
//   			exec($cmd,$output);
//   			var_dump($output);

	    }
    }
        
    $this->view->form = $form;
	
  }
  
  public function deleteAction(){
   	$request = $this->getRequest();
    $form    = new Forms_Delete_Image();
	$form->addAll(array(
		'fold'=>$this->_getParam('fold'),
		'n'=>$this->_getParam('n')
	));
	
      if ($this->getRequest()->isPost()) {
	    if ($form->isValid($request->getPost())) {

   			$data = $form->getValues();
      		$file_info=array(
      		'path_small'=>DOCROOT.$this->_dataConfig['itemView'][$this->_getParam('fold')]["list"]["thumb"]["path_small"],
      		'path_medium'=>DOCROOT.$this->_dataConfig['itemView'][$this->_getParam('fold')]["list"]["thumb"]["path_medium"],
      		'path_original'=>DOCROOT.$this->_dataConfig['itemView'][$this->_getParam('fold')]["list"]["thumb"]["path_original"],
   			'prefix_small'=>'sm_',
   			'prefix_medium'=>'med_',
   			'prefix_original'=>'orig_');  


   			$img = new Image(array('file_info'=>$file_info));
   			$img->setImage_name($this->_getParam('n'));
   			$img->setSrc($this->_getParam('fold'));
   			//var_dump($this->_getParam('fold'));
   			//var_dump($this->_dataConfig['itemView'][$this->_getParam('fold')]);
   			$this->view->message = $img->delete();
//   			$output;
//   			$cmd = "ls -l ".$img->getSrc()."/".$img->getCode();
//   			exec($cmd,$output);
//   			var_dump($output);

	    }
    }
     
	$this->view->form = $form;
		
  }

  public function thumbAction(){

    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();

    $src=$this->_getParam('src');
    $from=$this->_getParam('from','shop');
    $cat=$this->_getParam('cat');

    if ($from=='shop' && $cat){
      $path=$this->_subGridFld[$cat]['http_path'];
      require_once('Hw/Thumb/ThumbLib.inc.php');
      //$path=DOCROOT."/hwdb/thumbs/";
      $imgPath=$path.'/'.$src;
      $imgPath=preg_replace(array('/\/\\.\//','/\/+/'),'/',$imgPath);
      $image=PhpThumbFactory::create('http://'.$imgPath);
      $image->resize(100,100);
      $image->show();

    }

  }


}