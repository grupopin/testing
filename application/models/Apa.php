<?php

class Apa extends HwDbTable
{
    protected $_name = 'apa';
    protected $_primary  = 'apa_id'; 
    /**
     * Fetch the latest $count places
     *
     * @param int $count
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchLatest($count = 5)
    {
        return $this->fetchAll(null,'apa_id DESC', $count);
    }
    

public function showImage($work_id, $limitPreviewWidth=407,$thm_size='original',$format='img'){
    $idArr=preg_split('@[/\s]+@',$work_id);
    //print_r($idArr);
    $idArr[0]=str_pad($idArr[0],4,'0',STR_PAD_LEFT);
    $id2=$idArr[0].'_'.$idArr[1];
    $id3=$idArr[0].$idArr[1];
    $typ1='APA';
    //$qs="SELECT image_id,image_path,image_name FROM image WHERE cat_name='works' && image_name LIKE '$id2%' OR image_name LIKE '$id3' ORDER BY image_name";
    $qs="SELECT image_id,image_path,image_name FROM image WHERE cat_name='{$type}' && image_name LIKE '{$typ1}_{$id2}%' OR image_name LIKE '{$typ1}_{$id3}' ORDER BY image_name";
    //print $qs;
    //die();
    $q=$this->_db->query($qs);
    //$q=db_query($qs);
     
    $row=$q->fetch();
    //print_r($row);
    $imgArr=array();
    switch($thm_size){
      case 'small':
          $imgArr['img']=str_replace(array('originals','orig'),array('small','sm'),$row['image_path']);
          break;
      case 'medium':
          $imgArr['img']=str_replace(array('originals','orig'),array('medium','med'),$row['image_path']);
          break;
      default:
       $imgArr['img']=$row['image_path'];
            
            
    }
    
    $imgArr['name']=$row['image_name'];
    $imgArr['image_id']=$row['image_id'];

    $thmbImagePath=$imgArr['img'];
    $thmbImageName=$imgArr['name'];
    $thmbImageId=$imgArr['image_id'];
     
    $imgProp=getimagesize("/home/hwarch/public_html/hwdb/data$thmbImagePath");
    //print $thmbImagePath."<br>";
    //print_r($imgProp);
    //print "<br>";
    $heightStr='';
    $widthStr='';
    if ($imgArr[0]>$limitPreviewWidth){
      $widthStr="width={$limitPreviewWidth}";
    }else{
      $widthStr="width={$imgProp[0]}";
    }
    $imagesStr='';
    if ($thmbImagePath){
    if ($format=='src'){
       $imagesStr="/hwdb/data$thmbImagePath";
    }else{
       $imagesStr="<img $widthStr $heightStr src='/hwdb/data$thmbImagePath' alt='{$thmbImageName}' title='{$thmbImageName}'/>";
    }
    }
    return $imagesStr;
  }
    
    public function searchWorks($seachData){
      	
      //$data['period']	
    	
    	
    }
    
//    public function countSkinWorks($skin_id){
//        return parent::countSkinWorks($skin_id,$this->_name);
//    }
//    
// public function skinWorks($skin_id,$count,$offset){
//    return parent::skinWorks($skin_id,$count,$offset,$this->_name);
//   }
//
//
//
//   public function countItemWorks($item_id){
//    return parent::countItemWorks($item_id,$this->_name);
//   }
//   
//   
//   public function itemWorks($item_id,$count=5,$offset=0,$work=null){
//   	if (!$work){
//   		$work1=$this->_name;
//   	}else{
//   		$work1=$work;
//   	}
//    return parent::itemWorks($item_id,$count,$offset,$work1);
//   }
//   
}
