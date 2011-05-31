<?php

class KbController extends Hw_Controller_Action{

  public function indexAction(){
    //why index action is always executed 3 times for any action below????

    //return $this->_kBase->assert("test(123)",$this->_session->user);
  }

  public function getAction(){
  	 print "123567<br>";
  	 //return true;
  	$hwImg=new Hw_Image();
  	if ($hwImg->visible('1234','apa')){
  		print "123!";
  	}
  	//    $type=$this->_getParam('type');
  	//    $text=$this->_getParam('text');
  	//    $termStr="findObject('$type', ObjNumber, '$text')";
  	//    //print $termStr."<br>";
  	//    $formStr="findObject\('$type', '([\d\w_-\s\.\\/]+)', '$text'\)";
  	//    //using regular expression
  	//    //$formStr="object\(([^,]+), ([^\(]+)\(([G_\d\, ]+)\)\), search_atom\(([\w\_\-]+), ([\w\_\-]+)\)";
  	//
  	//
  	//    $flds=array(1=>'ObjNumber');
  	//
  	//    $tm1=microtime(true);
  	//    $arr=$this->queryKb($termStr, $formStr, $flds, true);
  	//    $tm2=microtime(true);
  	//    print $tm2 - $tm1 . "<br>";
  	//    print_r($arr);
  	//
  	//    return true;
  }

  public function  getListAction(){

  }

  /**
   * this function gets full list of properties of objects:
   * object(Type, Id, Title, [PropList]). Prop list is in form of ['key'=>'val',....]
   *
   * @return unknown_type
   */
  public function getPropsAction(){
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();

    //$id=$this->_getParam('id');
    $type=$this->_getParam('type');
    $key=$this->_getParam('key');
    $search=$this->_getParam('search');
    $selected=$this->_getParam('selected');//[id,text_title_en]
    $to=(int)$this->_getParam('to');
    if ($to<=0) {
      $to=19;
    }
    $from=(int)$this->_getParam('from');
    if ($from<0){
      $from=0;
    }

    $selected="[".$selected."]";

    //$termStr="findObject('$type', ObjNumber, '$text')";


    $tm1=microtime(true);
    $res=$this->_kBase->selectOrderLimit($type, $key, $search, $selected, $from, $to);

    $tm2=microtime(true);
    $dT1=$tm2 - $tm1;
    //print "Query took:" . $dT1 . "<br>";

    //print $res;
    //die();
    $allArr=explode('@#*@#*', $res); //print $res;
    //print_r($allArr);
    $convStr=new Hw_Convert_Str();
    foreach($allArr as $j=>$string){
      $string=trim($string);
      if (empty($string)) continue;
      $propValues=explode('###', $string);
      //print_r($propValues);
      $parsedData=array();
      $parsedDataPair=array();
      $parsedDataFields=array();
      foreach($propValues as $l=>$line){
        $line=trim($line);
        $parsedData=array();
        if($line){
          $parsedData=explode("-", $line, 2);

          $parsedData[1]=$convStr->stripWrappingQuotes($parsedData[1]);
          $parsedDataPair[]=$parsedData;
          if($parsedDataFields[$parsedData[0]]){
            $parsedDataFields[$parsedData[0]].="<br>" . $parsedData[1];
          }else{
            $parsedDataFields[$parsedData[0]]=$parsedData[1];
          }
        }
      }
      //print_r($parsedDataPair);
      $data[]=$parsedDataPair;
      $dataFields[]=$parsedDataFields;
    }
    //we get to resulting arrays: data and dataFields. data is array of pairs [0]='id', [1]=>123
    // and dataFields is in form of key=>value. Values for the same keys are merged.
    print_r($dataFields);

    //$res=$this->_kBase->execJoinList($termStr);


    $tm3=microtime(true);
    $dT2=$tm3 - $tm2;
    //print "parsing took:" . $dT2 . "<br>";


  }

  public function checkTermAction(){
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();

    $trm="object(hl,'54','The brook, the river, the swam',[id-'54',title-'The brook, the river, the swam',text-'The brook, the river, the swamp, the riverside wetlands as they\\nare, the way god created them, must be sacred and inviolable to us.']).";
    $termArr=Hw_MyTrans::parseTerm($trm);
    print_r($termArr);

    $trm="link(hl,'54',text,'1.2.3.4')";
    $termArr=Hw_MyTrans::parseTerm($trm);
    print_r($termArr);

  }

  public function loadTermsAction(){
    //parses all the files of KB, objects.pl,..., and loads only freshest.
    $this->_helper->layout->disableLayout();
    $path="/home/hwarch/domains/hw-archive.com/pl/kbase/onto/current/";
    $fp0=fopen($path."kbase.pl",'w+');
    if ($handle = opendir($path)) {
      $files=array();
      while (false !== ($file = readdir($handle))) {
        //$files[$file] = preg_replace('/[^0-9]/', '', $file); # Timestamps may not be unique, file names are.
        if ($file!='.' && $file!='..'){
          if (pathinfo($file,PATHINFO_EXTENSION)=='pl'){
            $fp=fopen($path.$file,'r');
            $i=0;
            while(false!==($line=readfile($path.$file))){
              if (trim($line)==''){
                continue;
              }
              $i++;
              if (strpos($line,0,1)=='%'){
                $state="start";
                $startLine=$i;
                //$objects[]
              }
              if ($i==$startLine+1){
                $state="objectStart";
              }

              if (trim($line)=="])"){
                $state='ObjectEnd';
              }



              if ($state=='start'){
                $tmpArr=explode("###",$line,3);

                $user=substr_replace($tmpArr[0],'',0,1);
                $timeArr=preg_split("@[\s\-\:]{1}@ig",$tmpArr[1]);
                $time=mktime($timeArr[3],$timeArr[4],$timeArr[5],$timeArr[1],$timeArr[2],$timeArr[0],0);
                //unset($tmp)
                //$termHead=$tmpArr[2];
                //$objectType=$tmpArr[3];
                //$objectId=$tmpArr[4];
                $existingObjectTime=$objects[$tmpArr[2]];
                if (!empty($existingObjectTime) && $existingObjectTime>$time){
                  // a fresher object exist, ignore this one
                  $state1="ignore";
                }else{
                  $objects[$tmpArr[2]]=$time;
                  $state1='save';
                }
              }

              if (($state=='ObjectStart' || $state=='objectEnd') && $state1=='save'){
                fwrite($fp0,$line);
              }

            }
            fclose($fp);
          }
        }
      }
      closedir($handle);
      fclose($fp0);

    }

  }

  public function getTransAction(){
    $this->_helper->layout->disableLayout();
    $op=$this->_getParam('op','load');
    $obj=$this->_getParam('obj','');
    $obj=mysql_escape_string($obj);
    $permittedOps=array('load','assert','retract');
    if (!in_array($op,$permittedOps)){
      $op='load';
    }

    //$this->_helper->viewRenderer->setNoRender();
    //require_once("KbTrans.php");
    $trans=new KbTrans();
    //if($this->_session->user['id'] == 1){
    //$where="created_by='1'";
    //}else{
    $where="(created_by='1' || created_by='3' || created_by='2' )"; //Modificado para probar localhost
    //}
    $where.=" && (operation='load' || operation='assert')";
    if ($obj){
      $where.=" && term_arg1='$obj'";
    }
    //die($where);
    $this->view->trans=$arr=$trans->fetchAllTerms($where);
    //$sz=sizeof($arr);
    //die("sz:$sz");

    $this->view->heads=$trans->fetchTermHeads($where);



    //print_r($heads);


  }

  public function getTrans2Action(){
    $this->_helper->layout->disableLayout();
    $op=$this->_getParam('op','load');
    $obj=$this->_getParam('obj','');
    $obj=mysql_escape_string($obj);
    $permittedOps=array('load','assert','retract');
    if (!in_array($op,$permittedOps)){
      $op='load';
    }

    //$this->_helper->viewRenderer->setNoRender();
    //require_once("KbTrans.php");
    $trans=new KbTrans2();
    //if($this->_session->user['id'] == 1){
    //$where="created_by='1'";
    //}else{
    $where="(created_by='1' || created_by='3')";
    //}
    $where.=" && (operation='load' || operation='assert')";
    if ($obj){
      $where.=" && term_arg1='$obj'";
    }
    //die($where);
    $this->view->trans=$arr=$trans->fetchAllTerms($where,20000);
    //$sz=sizeof($arr);
    //die("sz:$sz");
    //print_r($arr[0]);
    $this->view->heads=$trans->fetchTermHeads($where);



    //print_r($heads);


  }


  public function del109Action(){
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    //$id="'10.10.10.100'";
    //$res=$this->_kBase->deleteText($id);
    //$par='1.2.3.4,3.4.5.6';
    //$par1=$this->cleanSelected($par);
    //print $par1;
    $data=$this->cntRelatedPerParent("skin",array(1,2));
    print_r($data);
  }

  public function delAction(){
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    $id=$this->_getParam("id");
    $type=$this->_getParam("type");


    //replace _ to /. In HTML ID can not have  / symbol.
    $id=str_replace(array("id-","_",'-'),array('',"/",'.'),$id);


    if (!$this->_config->editable->$type){
      print $this->view->l('Operation is not permitted');
      return false;
    }

    //print $id.$type;

    if ($_SERVER['REMOTE_ADDR']!='85.130.239.124'){
      //return false; //Comentado por GC
    }
    
    switch($type){
      case 'text':
        //delete from KB DB
        $lidArr=array();
        $lidStr='';
        $trans=new KbTrans();
        if ($id){
          $now=date("Y-m-d H:i:s");
           $updWhere="term_head='object' && term_arg1='text' && term_arg2='$id' && term_active='1'";
           $resCount=$trans->update(array("term_active"=>'0','term_updated'=>$now),$updWhere);
           //--> $resCount is null
           $resCount=1;
           if ($resCount){
             //find linked highlights
             $fetchWhere1="term_head='link' && term_arg1='text' && term_arg2='$id' && term_arg3='hl' && term_active='1'";
             //print $fetchWhere1."<br>";

             $links=$trans->fetchAll($fetchWhere1);
             if ($links){
               foreach($links as $l=>$linkProp){
                 $lidArr[]=$linkProp['term_arg4'];
               }
               if ($lidArr){
                 $lidStr=implode(',',$lidArr);
                 if ($lidStr){
                   $hlWhere="term_head='object' && term_arg1='hl' && term_arg2 IN ($lidStr)";
                   $trans->update(array("term_active"=>'0','term_updated'=>$now), $hlWhere);

                   $hlLinksOutWhere="term_head='link' && term_arg1='hl' && term_arg2 IN ($lidStr)";
                   $trans->update(array("term_active"=>'0','term_updated'=>$now), $hlLinksOutWhere);

                   $hlLinksInWhere="term_head='link' && term_arg3='hl' && term_arg4 IN ($lidStr)";
                   $trans->update(array("term_active"=>'0','term_updated'=>$now), $hlLinksInWhere);
                 }
                 //print $hlWhere."<br>";
                 //print $hlLinksWhere."<br>";
               }
             }


             //disable outbound links
             $updWhere1="term_head='link' && term_arg1='text' && term_arg2='$id' && term_active='1'";
             //print $updWhere1."<br>";
             $resCount1=$trans->update(array("term_active"=>'0','term_updated'=>$now),$updWhere1);
             //disable inbound links
             $updWhere2="term_head='link' && term_arg3='text' && term_arg4='$id' && term_active='1'";
             //print $updWhere2."<br>";
             $resCount2=$trans->update(array("term_active"=>'0','term_updated'=>$now),$updWhere2);


             //$res=$this->_kBase->deleteText("'$id'"); //No funciona
             //return $res;
             
             $term="object('text','$id',Title,List)";
             $res=$this->_kBase->retract($term,$this->_session->user);
             
             if ($res){
              $this->getResponse()->setHeader("Content-Type","text/plain");
              $this->getResponse()->setBody($res);
             }

          }

        }
        //delete from Prolog KB
        break;
      case 'audio':
      case 'article':
      case 'gallery':
            
          $term="object('$type','$id',Title,List)";
          $res=$this->_kBase->retract($term,$this->_session->user);
             
          if ($res){
            $this->getResponse()->setHeader("Content-Type","text/plain");
            $this->getResponse()->setBody($res);
          }
        break;
      default:
          $res = "false";
          $this->getResponse()->setHeader("Content-Type","text/plain");
          $this->getResponse()->setBody($res);
        ;
    }
    //$mail=new Hw_Mail();
    //$mail->debugSend($id.$type,$updWhere);
  }
  
  public function textAutoAction(){
  	///kb/text_auto?fld=text_type
  	$filter=mysql_escape_string(urldecode($this->_getParam('q')));
    $ac=$this->_helper->autoComplete;
  	$fld=$this->_getParam('fld','');
  	if (!in_array($fld,array('text_type','text_cat'))){
  		$fld='';
  	}
  	if (!$fld) return false;
  	
  	$resArr=array();
  	switch ($fld) {
  		case 'text_type':
  		  $qs="SELECT text_type_name FROM text_type ORDER BY text_type_name";
  		  $types=new TextType();
  		  $res=$types->fetchAll("text_type_name LIKE '$filter%'",'text_type_name');
  		  foreach($res as $row){
  		  	$resArr[]=stripslashes($row['text_type_name']);
  		  }	
  		break;
  		
  		case 'text_cat':
  		  $qs="SELECT text_cat_name FROM text_cat ORDER BY text_cat_name";
  		  $cats=new TextCat();
  		  $res=$cats->fetchAll("text_cat_name LIKE '$filter%'",'text_cat_name');
  		  foreach($res as $row){
  		  	$resArr[]=stripslashes($row['text_cat_name']);
  		  }	
  		break;
  		default:;
  	}
  	
  	print $ac->sendAutoCompletion($resArr);
  	
  }

  public function editAction(){
    //edit KB object and create new version of an object. We do not overwrite it
    //$this->_helper->viewRenderer->setNoRender();
    //$this->_helper->layout->disableLayout();
    //ini_set("display_errors",1);
    //error_reporting(E_ALL^E_NOTICE);

    $id=$this->_getParam("id");
    //replace _ to /. In HTML ID can not have  / symbol.
    $id=str_replace(array("id-","_",'-'),array('',"/",'.'),$id);

    //$rowid=(int)$this->_getParam("rowid");
    $type=$this->_getParam('type');
    if (!$this->_config->editable->$type){
      print $this->view->l('Operation is not permitted');
      return false;
    }

    $subGridProps=Zend_Registry::get('subGridFld');
    $fldsDefArr=array();
    //print_r($subGridProps[$type]);
    if ($subGridProps[$type]['fields']['edit']){
      $fldsDefArr=$subGridProps[$type]['fields']['edit'];
    }elseif ($subGridProps[$type]['edit-field']){
      $flds=$subGridProps[$type]['edit-field'];
    }else{
      $flds=$subGridProps[$type]['field'];
    }
    $folderNameCommon=$subGridProps[$type]['folder-name'];
    $folderNames=$subGridProps[$type]['folder'];

    //print_r($folderNames);
    //print $folderNameCommon."<br>";
    //die();
    if (!$id){
    	//then it is an insert operation(new record)
    	fb("Insert<br>");
     if ($fldsInsertArr=$subGridProps[$type]['fields']['insert']){
     	fb($fldsInsertArr);
		foreach($fldsInsertArr as $fnam=>$fldDef){
		  $insFldsArr[]=$fnam; //Agregado por GC
		  $insFldTypes[$fnam]=$fldDef['type'];	
		}     	
     }else{	
      $insFlds=$subGridProps[$type]['insert-field'];
      $insFldsArr=$this->strToArr($insFlds);
      $insType=$subGridProps[$type]['insert-type'];
      $insTypeArr=$this->strToArr($insType);
      foreach($insFldsArr as $inx=>$fnam){
        $insFldTypes[$fnam]=$insTypeArr[$inx];
      }
     }
    }
    
    if ($fldsDefArr){
     $fldArr=array_keys($fldsDefArr);	
    }else{
     $fldArr=$this->strToArr($flds);
    }
    //print_r($fldArr);
    if ($fldEditArr=$subGridProps[$type]['fields']['edit']){
    	foreach($fldEditArr as $fnam=>$fldProp){
	      $fldTypes[$fnam]=$fldProp['type'];
	    }		
    }else{
	    $cell_type=$subGridProps[$type]['edit-type'];
	    $typeArr=$this->strToArr($cell_type);
	    foreach($fldArr as $inx=>$fnam){
	      $fldTypes[$fnam]=$typeArr[$inx];
	    }
    	
    }
    if (!$id){//insert mode
      $fldTypes=array_merge($insFldTypes,$fldTypes);
      //print_r($insFldTypes);
      $fldArr=array_merge($insFldsArr,$fldArr);
      //print_r($fldTypes);
    }
    
    fb($fldTypes,'fldTypes');

    $form=new Zend_Form();
    $form->setAction('/kb/edit')
    ->setMethod('post');
    //print $type.','.$id.','.$flds;
    //fetch all fields values

    if ($id){//i.e. update operation
      $arr=$this->_kBase->getObjectById($type,$id,'');
      
      $data=$arr[0];
      fb($data,"$type-$id:");
      foreach($data as $fname=>$fvalue){

        $data[$fname]=html_entity_decode($fvalue,ENT_QUOTES,'UTF-8');
        if ($fldTypes[$fname]=='ta'){
          $data[$fname]=strtr($data[$fname],array("<br />"=>"\n",'<br>'=>"\n"));
          //strtr($text, array("\n" => '<br />', "\r\n" =>'<br />'));
        }
      }
    }else{//i.e. insert operation

      foreach($fldArr as $inx=>$fnam){
        $data[$fnam]='';
      }
   
    }


    $i=0;
  
    $haArr=array();
    $els=array();
    foreach($data as $fldName=>$val){
      if (!in_array($fldName,$fldArr)) continue;

      $folderName=$this->getFolderName($folderNames,$fldName);
      //print "$fldName-$folderName<br>";
      switch($fldTypes[$fldName]){
        case 'str':
          $formEl=new Zend_Form_Element_Text($fldName);
          $formEl->setLabel($fldName);
          if ($fldName==$this->_itemIds[$type]) {
            //$formEl->setRequired(true); //Agregado por GC
            $formEl->setAttrib('style','background-color : yellow'); 
          }
          //$formEl->setValue($val);
          $els[$i]=$formEl;
          break;

        case 'img':
          $formEl=new Zend_Form_Element_Image($fldName.'-image');
          $formEl->setLabel($fldName);
          //$formEl->setValue($val);
          $imgPath="/hwdb/thumbs/".str_replace("/","/thumb.",$val);
          $formEl->setImageValue($imgPath);
          $els[$i]=$formEl;

          $i++;
          $formEl=new Zend_Form_Element_File($fldName);
          //$formEl->setValue($val);
          $formEl->setLabel($this->view->l('Upload an Image'));

          $formEl->setDestination(DOCROOT."/hwdb/thumbs/$folderName");
          $els[$i]=$formEl;

          break;

        case 'audio':
        case 'video':
        case 'article':
        case 'document':

          $formEl=new Zend_Form_Element_File($fldName);
          //$formEl->setValue($val);
          $label=$this->view->l('Upload '.$fldTypes[$fldName]);
          $label.='/'.$val.'/';
          $formEl->setLabel($label);

          $formEl->setDestination(DOCROOT."/hwdb/thumbs/$folderName");
          $els[$i]=$formEl;

          break;

        case 'ha'://htmlarea
          $formEl=new Zend_Form_Element_Textarea($fldName);
          $formEl->setLabel($fldName);
          //$formEl->setValue($val);
          $formEl->setAttrib('class','text-editors');
          $formEl->setAttrib('style','width:300px;height:200px'); 
          $els[$i]=$formEl;
          $haArr[]=$fldName;
          break;

        case 'ta'://textarea
          $formEl=new Zend_Form_Element_Textarea($fldName);
          $formEl->setLabel($fldName);
          //$formEl->setValue($val);
          $formEl->setAttrib('style','width:500px;height:100px;'); //Modify by GC
          $els[$i]=$formEl;
          break;

      }
      //$j++;
      $i++;
    }
    //print_r($haArr);
    $i++;
    //$id2=str_replace(".","-",$id);
    //die($id2);
    $formEl=new Zend_Form_Element_Hidden('id');
    //$formEl->setValue($id);
    $els[$i]=$formEl;

    $i++;
    $formEl=new Zend_Form_Element_Hidden('type');
    //$formEl->setValue($type);
    $els[$i]=$formEl;


    $i++;
    $formEl=new Zend_Form_Element_Submit('submit');
    $els[$i]=$formEl;

    $form->addElements($els);


    if ($this->_getParam('submit')){

      if ($form->isValid($_POST)){
        $values=$form->getValues();
        //print_r($_POST);

        $termList="";
        foreach($data as $f=>$v1){
          if (!in_array($f,$fldArr)){
            //values from $data, not from the forms
            $curVal=$v1;
          }else{
            //values from form
            $v=$values[$f];
            if ($fldTypes[$f]=='img' && $v){
              $folderName=$this->getFolderName($folderNames,$f);
              $imgPath=$folderName.'/'.$v;
              $thumbPath=$folderName.'/thumb.'.$v;
              //create thumbnail
              require_once('Hw/Thumb/ThumbLib.inc.php');
              $path=DOCROOT."/hwdb/thumbs/";
              $newFilePath=str_replace(array("//"),'/',$path.$imgPath);
              $image=PhpThumbFactory::create($newFilePath);
              $image->resize(100,100);
              $image->save($path.$thumbPath);
              $v=$imgPath;
            }

            if ($fldTypes[$f]=='ta'){
             $curVal=nl2br($v);
            }else{
             $curVal=$v;
            }

          }
          if (in_array($fldTypes[$f],array('img','audio','video','article','document'))&& !$v){
            //avoid empty image name
            $curVal=$v1;
            //continue;
          }
          if (!$id && $f==$this->_itemTitles[$type]){

            $objectTitle=htmlentities($curVal,ENT_QUOTES,'UTF-8');

          }

          if (!$id && $f==$this->_itemIds[$type]){
            $objectId=$curVal;

          }
          $conv=new Hw_Convert_Type();
          if ($conv->isInt($curVal)){
            $termVals[]="$f-$curVal";
          }else{
            $curVal=str_replace(array("\n","\r"),'',$curVal);
            if ($fldTypes[$f]=='ha'){
              //there is a bug, may be in fckeditor, it ads <div>s to the end of html
              //lets' remove it
              $curVal=str_replace("<div>&nbsp;</div>",'',$curVal);

            }
            $curVal=htmlentities($curVal,ENT_QUOTES,'UTF-8');
            $termVals[]="$f-'$curVal'";
            //if ($f=='maintitle_summary_en'){
            //  $mail=new Hw_Mail();
            //  $mail->debugSend('maintitle_summary_en',$curVal);
            //}
          }
        }
        if (is_array($termVals)){
          $termList="[\n".implode(",\n",$termVals)."\n]";
          //print $termList;
        }

        //exit();

        if ($id){
          $objectTitle=htmlentities($this->_kBase->getObjectTitleById($type,$id),ENT_QUOTES,'UTF-8');
          $objectId=$id;
          $mode='update';
        }else{
          $mode='insert';
        }

        $newDbId=-1;

        $objectTerm="object($type,'$objectId','$objectTitle',$termList).";

        if ($mode=='insert'){
          if ($objectId) {
            $res=$this->_kBase->insertTerm($objectTerm,$objectId,$type,$this->_session->user);
          } else {
            $res='goal_failed(user:reply)'; //Agregado por GC
            $mensaje = "Object ID is empty";
          }
          
        } else {
          $res=$this->_kBase->updateTerm($objectTerm,$objectId,$type,$this->_session->user);
        }
        if (stristr($res,'goal_failed(user:reply)')){
          $termCreated=false;
          print "Error asserting the term<br>" ;
          print $mensaje."<br>";
          print $objectTerm."<br>";
        }else{
          $termCreated=true;
          $newDbId=Hw_MyTrans::updateDbTerm($type,$objectId,$objectTitle,$termList,$this->_session->user);
          if ($newDbId>0){
            $this->_forward('saved',null,null,array('type'=>$type,'id'=>$objectId));
          }
        }


        //print "new id:".$newDbId;
        //print_r($termVals);

        //print_r($values);
        //print_r($_POST);


      } else {
        print "Form is not valid";
      }
    }else{
      //$this->render('edit-form');

      //$this->includejQuery();
      $data['id']=$id;
      $data['type']=$type;
      $form->setDefaults($data);


      $this->view->form=$form;
      $this->view->haArr=$haArr;


    }

  }

  private function getFolderName($folderNames,$fldName,$dest="/hwdb/thumbs/"){
    $destPath=DOCROOT.$dest;
    $res=$folderNames[$fldName];

    if ($res){
      if (!file_exists($destPath.'/'.$res)){
        mkdir($destPath.'/'.$res);
      }
    }
    return $res;
  }

  public function savedAction(){
    $this->view->id=$this->_getParam('id');
    $this->view->type=$this->_getParam('type');
  }



  public function listPredicates( $head_part=''){

    //remove duplicates
    //remove paint_to_ - 'broken' predicates with _ at the end


    /*		$str="text_to_";
     $arr=explode('_',$str);
     print_r($arr);

     return;
     */
    $termStr="current_predicate(AtomStr,Head),search_atom(AtomStr,'$head_part')";
    //$formStr="current_predicate(%[^'], %[^,]), search_atom(%[^'], $part)";
    //using regular expression
    $formStr="current_predicate\(([^,]+), ([^\(]+)\(([G_\d\, ]+)\)\), search_atom\(([\w\_\-]+), ([\w\_\-]+)\)";

    $flds=array(1=>'AtomStr',3=>'Body');

    $head=array();
    $body=array();
    $arr=$this->queryKb($termStr, $formStr, $flds, true);
    //print_r($arr);
    foreach($arr as $el){
      $termHead=$el['AtomStr'][0];
      $termBody=$el['Body'][0];
      $tmpArr=explode('_', $termHead);
      if($tmpArr[1] == 'to' || empty($tmpArr[sizeof($tmpArr) - 1])){
        continue;
      }
      if(array_search($termHead, $head) !== false){
        continue;
      }
      $head[]=$termHead;
      $body[]=$termBody;
      //print $termHead.">".$termBody."<br>";


    }
    return array('head'=>$head,'body'=>$body);
    //$cont=html_entity_decode($arr[0]['Contents'],ENT_QUOTES,'utf-8');
    //$cont=Hw_Tidy::runTidy($cont);
    //$cont=str_replace("&nbsp;"," ",$cont);
    //$this->view->text_contents=$cont;


  }

  public function getDataAction(){

    //$str="a2";
    //$str=wrapInQuotes($str);


    //$str=trim($str,"'");
    //print $str;
    //return true;


    $part=$this->_getParam("part");
    $terms=$this->listPredicates($part);
    $id=(int)$this->_getParam("id");

    //now fetch all terms which begin with $part_
    //print_r ( $terms );
    $max=sizeof($terms['head']);
    for($i=0; $i < $max; $i++){
      $headStr=$terms['head'][$i];
      print $headStr . "<br>";
      $qry="$headStr($id, Value)";
      $frm="$headStr($id, '%[^']')";
      $outFields=array(0=>'Id',1=>'Value');
      $dataArr[$headStr]=$this->queryKb($qry, $frm, $outFields);
      if($i == 2){
        break;
      }
    }
    print_r($dataArr);

  }

  public function addTermAction(){
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();

    $transDbAdapter=new Hw_MyTrans();
    $transDbAdapter->updateTerm("a(1,'Test')", $this->_session->user);

  }

  public function execAction(){

    //$arr=array('term'=>"skin(Id, Title)");
    //$json=new Zend_Json();
    //$qry=$json->encode($arr);
    //print $qry;
    $searchStr='e';
    $qry="graph_to_skin(Gid,Sid),graph_title(Gid,Title),graph_author(Gid, Author),skin(Sid,SkinName),search_atom(SkinName,'$searchStr')";
    $frm="graph_to_skin(%d, %d), graph_title(%d, '%[^']'), graph_author(%d, '%[^']'), skin(%d, '%[^']'), search_atom('%[^']', '$searchStr')";
    $outFields=array(0=>'Gid',1=>'Sid',3=>'Title',4=>'Author',7=>'SkinName');
    //$qry="skin_to_graph(Id,Gid)";
    $dataArr=$this->queryKb($qry, $frm, $outFields);
    //print "<pre>";
    //print_r($dataArr);
    //print "</pre>";
    //print $res;
    return $dataArr;

  }

  public function execJoinAction(){

    $res=$this->_kBase->execJoin("graph_title(Id,Title),graph_author(Id,Author)");
    print $res;
    return $res;

  }

  public function listAction(){

    $id=(int)$this->_getParam('id');
    if($id < 1)
    $id=6;

    //$res=$this->_kBase->showList("cut_by(X,Y)");
    //
    //$res=$this->_kBase->showList("coordinator('117',X),atom_number(X,Id),coordinator_person(Id,Title)");
    $res=$this->_kBase->showList("coordinator(X,'$id'),coordinator_person($id,Name), title(X,Title)");
    if($res){
      return $res;
    }else{
      return "Nothing found";
    }

  }

}

?>