<?php
class Hw_Image{
	
	public function visible($imgPath,$type){
		$kbImage=new KbImage();
		$sel=$kbImage->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->where("frontend_visible=?",1)->where("image_name=?",$imgPath)->where("object_type=?",$type)->columns("frontend_visible");
		$row=$kbImage->fetchRow($sel);
		if ($row){
			return true;
		}
		
	}

public function workImage($type,$size='small',$id,$mode='list',$series=false,$sort='asc',$showNum=1,$pad=true,$params=array()){

    if (!$mode){
      $mode='list';
    }

    $config=Zend_Registry::get('dataConfig');
    if ($mode == 'extended') {
       $itemConfig=$config['itemView'][$type]['list'];
    } else {
      $itemConfig=$config['itemView'][$type][$mode];
    }

    //find images with same $id
    $pref=$itemConfig['thumb']['prefix_'.$size];
    
    $path=DOCROOT.'/'.$itemConfig['thumb']['path_'.$size];
     
    $path=$this->cleanPath($path);
    $idStr=$this->IdToName($type,$id,$pad);
    
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
    try {

        $db = Zend_Db::factory('Pdo_Mysql', $params);
        $db->getConnection();
        $sql = "SELECT i.name archivo, p.name fotografo FROM objeto o inner join objeto_imagen oi on oi.objeto_id=o.id inner join imagen i on oi.imagen_id=i.id left join  photographer p on i.photographer_id = p.id where o.tipo='work' and o.subtipo='{$type}' and o.codigo='{$id}' order by oi.orden, i.id";
                
        $imagenes = $db->fetchAll($sql);
        
        foreach ($imagenes as $i=>$imagen){
          
          $image_path=$itemConfig['thumb']['path_'.$size]. $itemConfig['thumb']['prefix_'.$size];
           
          if ($mode=='extended') {
           $str[]= array ("file" => $image_path . $imagen['archivo'], "photographer" => $imagen['fotografo']);
          } else {
           $str[]=  $image_path . $imagen['archivo'];
          }
        }
        
    } catch (Zend_Db_Adapter_Exception $e) {
    
       print "perhaps a failed login credential, or perhaps the RDBMS is not running";
    
    } catch (Zend_Exception $e) {
    
       print "perhaps factory() failed to load the specified Adapter class";
    
    }
    
//        Comentado por GC porque ya esta ordenado por la consulta 
//        if (is_array($str)){
//        if ($sort=='asc'){
//          sort($str);
//        }elseif('desc'){
//          rsort($str);
//        }
//    }
   
    if ($showNum>0){
      $res=$str[$showNum-1];
    } else{
      $res=$str;
    }
    
    fb(print_r($res,true));
    return $res;
  }
	
	
public function workImage1($type,$size='small',$id,$mode='list',$series=false,$sort='asc',$showNum=1,$pad=true,$params=array()){

    if (!$mode){
      $mode='list';
    }

    $config=Zend_Registry::get('dataConfig');
    $itemConfig=$config['itemView'][$type][$mode];

    //find images with same $id
    $pref=$itemConfig['thumb']['prefix_'.$size];
    
    $path=DOCROOT.'/'.$itemConfig['thumb']['path_'.$size];
     
    $path=$this->cleanPath($path);
    $idStr=$this->IdToName($type,$id,$pad);
    
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
    try {

        $db = Zend_Db::factory('Pdo_Mysql', $params);
        $db->getConnection();
        $sql = "SELECT i.* FROM objeto o, objeto_imagen oi, imagen i where oi.objeto_id=o.id AND oi.imagen_id=i.id and o.tipo='work' and o.subtipo='{$type}' and o.codigo='{$id}' order by oi.orden, i.id";
        $imagenes = $db->fetchAll($sql);
        
        foreach ($imagenes as $i=>$imagen){
          //FIXME: agregar path segun configuracion
           $image_path=$itemConfig['thumb']['path_'.$size]. $itemConfig['thumb']['prefix_'.$size];
           $str[]= $image_path . $imagen['name'];
        }
        
    } catch (Zend_Db_Adapter_Exception $e) {
    
       print "perhaps a failed login credential, or perhaps the RDBMS is not running";
    
    } catch (Zend_Exception $e) {
    
       print "perhaps factory() failed to load the specified Adapter class";
    
    }
    
//        Comentado por GC porque ya esta ordenado por la consulta 
//        if (is_array($str)){
//        if ($sort=='asc'){
//          sort($str);
//        }elseif('desc'){
//          rsort($str);
//        }
//    }
   
    if ($showNum>0){
      $res=$str[$showNum-1];
    } else{
      $res=$str;
    }
    
    fb(print_r($res,true));
    return $res;
  }
  
  /*
  public function workImage2($type,$size='small',$id,$mode='list',$series=false,$sort='asc',$showNum=1,$pad=true,$params=array()){


    if (!$mode){
      $mode='list';
    }
    
    $config=Zend_Registry::get('dataConfig');
    
    $itemConfig=$config['itemView'][$type][$mode];

    $pref=$itemConfig['thumb']['prefix_'.$size];
    //$pref='orig_JW_';
    
    $path = 'c:\proyectos\desarrollo\hw\public_html'.'/'.$itemConfig['thumb']['path_'.$size];
    
    //$path='c:\proyectos\desarrollo\hw\public_html'.'/hwdb/data/Images_jw/originals/';
    
    $path=$this->cleanPath($path);
    
    $idStr=$this->IdToName($type,$id,$pad);
    
    $dh=opendir($path);

    while(($file=readdir($dh))!==false){
    	
      if (in_array($file,array('.','..'))) continue;

      if (str_contains($file,$pref.$idStr, true)===1){
        if (!$series){
          $str=$itemConfig['thumb']['path_'.$size].$file;
          break;
        }else{

          //$str[]=$itemConfig['thumb']['path_'.$size].$file;
          //$str[]='/hwdb/data/Images_jw/originals/'.$file;
          $str[]= substr($file,strlen($pref));
        }
      
      }
	
    }
    
    if (is_array($str)){
        if ($sort=='asc'){
          sort($str);
        }elseif('desc'){
          rsort($str);
        }
    }
   
    if ($showNum>0){
      $res=$str[$showNum-1];
    } else{
      $res=$str;
    }
    
    return $res;
  }*/
  
	
  public function workImage2($type,$size='small',$id,$mode='list',$series=false,$sort='asc',$showNum=1,$pad=true,$params=array()){

//  	$args=func_get_args();
//  	fb(print_r($args,true));
    if (!$mode){
      $mode='list';
    }
    //var_dump($id);
    $config=Zend_Registry::get('dataConfig');
    $itemConfig=$config['itemView'][$type][$mode];
//     die($itemConfig);
    //print_r($itemConfig);
    //$path=$itemConfig['thumb']['path_'.$size];
    //find images with same $id
    $pref=$itemConfig['thumb']['prefix_'.$size];
//    die($pref);
    
    $path=DOCROOT.'/'.$itemConfig['thumb']['path_'.$size];
     
    $path=$this->cleanPath($path);
    
    //print $path;
    //print $id;
    $idStr=$this->IdToName($type,$id,$pad);
    
    //print $pref.$idStr."<br>";
    $dh=opendir($path);
    //var_dump($path);
    while(($file=readdir($dh))!==false){
    	
    	 //var_dump( $file,"<br/>");
      if (in_array($file,array('.','..'))) continue;
      //print $file."<br>";
//      $t = $pref.$idStr;
//      fb(print_r($type));fb(print_r($t));
//var_dump($file);
      if (str_contains($file,$pref.$idStr, true)===1){
        //print $file;
        //die();
        //print $str;
        if (!$series){
          $str=$itemConfig['thumb']['path_'.$size].$file;
          break;
        }else{

          //$str[]=$itemConfig['thumb']['path_'.$size].$file; Modificado por GC
          $str[]= substr($file,strlen($pref));
        }
        //var_dump($pref.$idStr);
      }
	
    }
    
   // var_dump($itemConfig,$size,$path);
    $fArr=array();
    
    //FIXME: ver porque no funciona este control de visibilidad y para que esta 
    if ($params['check_visibility']){
    	foreach($str as $fname){
    		$fArr[]=$this->fileNameToDbImageName($fname,$type,$mode,$size);
    	}
    	
    	if ($fArr){
    		//$fStr=implode(',',$fArr);
    		//foreach($fArr as $fRow){
    		  	
    		//}
    		foreach($fArr as $fVal){
    			if ($fStr){
    				$fStr.=",'".$fVal."'";
    			}else{
    				$fStr="'$fVal'";
    			}
    		}
    		
    	    $kbImg=new KbImage();
    	    //select images which are set unvisible
    	    $select=$kbImg->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->where("image_name in ($fStr) && frontend_visible=0");
    	    $sql=$select->assemble();
    	    $arr=$kbImg->fetchAll($select);
    	    $str1=array();
    	    $rows=array();
    	    while($row=$arr->current()){
    	    	$rows[]=$row['image_name'];
    	    	$arr->next();
    	    }
    	    $arrVis=array_diff($fArr,$rows);
    	    foreach($arrVis as $fName){
    	    	$str1[]=$itemConfig['thumb']['path_'.$size].$pref.$fName.'.jpg';
    	    }
    	    $str=$str1;
    	    
    	    //fb(print_r(array('path'=>$path,'pref'=>$pref,'rows'=>$rows,'str'=>$str,'fArr'=>$fArr),'image files list'));
    	
    	}
    	
    	
    	
    }
    

    if (is_array($str)){
        if ($sort=='asc'){
          sort($str);
        }elseif('desc'){
          rsort($str);
        }
    }
   

    if ($showNum>0){
      $res=$str[$showNum-1];
    } else{
      $res=$str;
    }
    fb(print_r($res,true));
    return $res;
  }
  
  public function IdToName($type,$id,$pad){
  	//var_dump($type,$id,$pad);
  	if ($type=='gallery'){

      $id=str_replace('galery/','',$id);
      //print $id;
      $tmpArr=explode('.',$id);
      unset($tmpArr[count($tmpArr)-1]);
      $id=implode('.',$tmpArr);
    }
    $idStr=str_replace(array('/','-',' '),'_',$id);
    $idArr=explode('_',$idStr);
    //print_r($idArr);

    if ($pad){
      $idStr=str_pad($idArr[0],4,'0',STR_PAD_LEFT);
    }
    unset($idArr[0]);

    if ($idArr){
      $idStr2='_'.implode($idArr);
    }
    $idStr.=$idStr2;
    //var_dump($idStr);
    return $idStr;
  }
  
  public function fileNameToDbImageName($file,$type,$mode,$size){
  	$config=Zend_Registry::get('dataConfig');
    $itemConfig=$config['itemView'][$type][$mode];
    $pref=$itemConfig['thumb']['prefix_'.$size];
    
    $filename=pathinfo($file,PATHINFO_FILENAME);
    $str=str_replace($pref,'',$filename);
    return $str;
  	
  }

  public function cleanPath($path){
    //remove duplicate slashes
    $path1=preg_replace("@/+@",'/',$path);
    $path1=str_replace('http:/','http://',$path1);//fix http protocol
    return $path1;

  }
	
	
}
function str_contains($haystack, $needle, $ignoreCase = false) {
    if ($ignoreCase) {
        $haystack = strtolower($haystack);
        $needle   = strtolower($needle);
    }
    $needlePos = strpos($haystack, $needle);
    return ($needlePos === false ? false : ($needlePos+1));
}