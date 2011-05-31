<?php
class Zend_View_Helper_HlpShowImage{

	public $view;
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}

	public function hlpShowImage($type, $work_id, $limitPreviewWidth=407,$thm_size='original',$format='img'){
		$db=Zend_Registry::get('db');
		$type=strtolower($type);
		$idArr=preg_split('@[/\s]+@',$work_id);
		//print_r($idArr);
		$idArr[0]=str_pad($idArr[0],4,'0',STR_PAD_LEFT);
		$id2=$idArr[0].'_'.$idArr[1];
		$id3=$idArr[0].$idArr[1];
		$thmbImagePathArr=array();

		switch($type){
			case 'apa':
			case 'arch':
				$typ1=strtoupper($type);
				//$id2=str_replace($)
				$qs="SELECT image_id,image_path,image_name FROM image WHERE cat_name='{$type}' && image_name LIKE '{$typ1}_{$id2}%' OR image_name LIKE '{$typ1}_{$id3}' ORDER BY image_name";
				//print $qs."<br>";
				break;
			case 'paint':
				$workType='works';

        $idArr=preg_split('@[/\s]+@',$work_id);
        //print_r($idArr);
        $idArr[0]=str_pad($idArr[0],4,'0',STR_PAD_LEFT);
        $id2=$idArr[0].'_'.$idArr[1];
        $id3=$idArr[0].$idArr[1];
        $typ1='';
        $qs="SELECT image_id,image_path,image_name FROM image WHERE cat_name='$workType' && image_name LIKE '{$id2}%' OR image_name LIKE '{$id3}' ORDER BY image_name";
				//print $qs;
        break;
				
			case 'tap':
				$workType='tap';
				$idArr=preg_split('@[/\s]+@',$work_id);
				//print_r($idArr);
				$idArr[0]=str_pad($idArr[0],4,'0',STR_PAD_LEFT);
				$id2=$idArr[0].'_'.$idArr[1];
				$id3=$idArr[0].$idArr[1];
				$typ1='';
				$qs="SELECT image_id,image_path,image_name FROM image WHERE cat_name='$workType' && image_name LIKE '{$id2}%' OR image_name LIKE '{$id3}' ORDER BY image_name";
				
				break;

			case 'graph':
				$typ1="GR";
				//$id2=str_replace($)
				$workIdStr=str_pad($work_id,4,'0',STR_PAD_LEFT);
				$qs="SELECT image_id,image_path,image_name FROM image WHERE cat_name='hwg' &&  image_name LIKE '{$typ1}_{$id2}%' OR image_name LIKE '{$typ1}_{$id3}' OR image_name LIKE '{$workIdStr}%' ORDER BY image_name";
				//print $qs."<br>";
				break;

			default;
		}

		$q=$db->query($qs);
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
}
?>