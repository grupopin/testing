<?php
class LinkController extends Hw_Controller_Action {

  public function indexAction(){
    $this->includejQuery();
    //$cat_id = (int) $this->_request->getParam('cat_id');
    //$this->view->headScript()->appendFile("/js/tinymce/jscripts/tiny_mce/tiny_mce.js");
    //$this->view->headScript()->appendFile("/js/fckeditor/fckeditor.js");

    //    $this->view->headScript()->appendFile("/js/jquery.fck/jquery.MetaData.js");
    //    $this->view->headScript()->appendFile("/js/jquery.fck/jquery.form.js");
    //    $this->view->headScript()->appendFile("/js/jquery.fck/jquery.FCKEditor.js");

    $ip=$_SERVER['REMOTE_ADDR'];
    $sess=Zend_Registry::get("nsHw");
    if ($sess->login_from!=$ip){
      $sess->login_from=$ip;
      $mail=new Hw_Mail();
      $mail->debugSend("Logged: hw-linkage, user:{$this->_session->user['name']},  ip $ip", "On ".date("H:i:s d/m/Y")."<br>logged from:".$ip);
    }

  }



  public function allAction(){
    $this->includejQuery();
    //print "123";
    $ip=$_SERVER['REMOTE_ADDR'];
    $sess=Zend_Registry::get("nsHw");
    if ($sess->login_from!=$ip){
      $sess->login_from=$ip;
      $mail=new Hw_Mail();
      $mail->debugSend("hw-All, user:{$this->_session->user['name']},  ip $ip", "On ".date("H:i:s d/m/Y")."<br>logged from:".$ip);
    }

  }

  public function treeAction(){
    $this->includejQuery();

    $ip=$_SERVER['REMOTE_ADDR'];
    $sess=Zend_Registry::get("nsHw");
    if ($sess->login_tree_from!=$ip){
      $sess->login_tree_from=$ip;
      $mail=new Hw_Mail();
      $mail->debugSend("hw-linkage TREE, user:{$this->_session->user['name']},  ip $ip", "On ".date("H:i:s d/m/Y")."<br>logged from:".$ip);
    }
    $json=new Zend_Json();
    $term="skin(Id, Title)";
    //$trans=new Hw_Trans();
    //$termArr=$trans->parseTerm($term);
    //$termjson=$json->encode($termArr);

    $skinStr=$this->_kBase->exec($term);
    $skinArr=$json->decode($skinStr);
    foreach($skinArr as $row){
      $skinTreeArr[]=array('n'=>$row[1],'title'=>$row[2]);
    }
    $this->view->skinArr=$skinTreeArr;

  }


  public function jqAssertAction(){

    $this->_helper->viewRenderer->setNoRender();

    //$this->view->layout()->setLayout('layout_ajax');
    $this->_helper->layout->disableLayout();
    $type=$this->_getParam('type');
    $id=$this->_getParam("id");
    $link_item=$this->_getParam('link_item');
    $link_item_id=$this->_getParam('link_item_id');

    //print "$quest_filter";

    //search($qry='', $itemType='', $parentItemType=null, $parentItemId=null)

    $assertStr="link('$link_item','$id','$type','$link_item_id')";
    //$transLog=new Hw_Trans();
    //$transLog->updateTerm($assertStr);
    $resStr=$this->_kBase->assert($assertStr, $this->_session->user);
    $this->getResponse()->setHeader("Content-Type","text/plain");
    $this->getResponse()->setBody($assertStr);

  }
  /**
   * Synonim of jqAssertAction, but name of Request params differ
   * @return none
   */
  public function jqAssertLinkAction(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $child=$this->_getParam('child');
    $parent=$this->_getParam('parent');
    $child_id=$this->_getParam('child_id');
    $parent_id=$this->_getParam('parent_id');
    $term=$child.'_to_'.$parent."($child_id,$parent_id)";
    //$transLog=new Hw_Trans();
    //$transLog->updateTerm($term);
    //print $term;
    $resStr=$this->_kBase->assert($term,$this->_session->user);

    if (is_string($resStr)){
      $this->getResponse()->setHeader("Content-Type","text/plain");
      $this->getResponse()->setBody($resStr);
    }

  }
  /*
   * same as above. Hl parameter is added
   */
  public function jqAssertHlLinkAction(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    
    
    $child=$this->_getParam('child');
    $parent=$this->_getParam('parent');
    $child_id=$this->_getParam('child_id');
    $parent_id=$this->_getParam('parent_id');

    $hl=$this->_getParam('hl');


    //$val=str_replace(array("â€œ","â€�"),'"',$val);
    $val=htmlentities($hl, ENT_QUOTES,'UTF-8');
    $hl1=strip_tags($hl);
    $hl1=htmlentities(mb_substr($hl1,0,50),ENT_QUOTES,'UTF-8');

    //_kBase->ask("next_hl") - finds and reserves next avaibale highlight ID number from KB
    $nextId=(int)$this->_kBase->ask("next_hl");

    if ($nextId<=0){
      return false;
    }
    //$term1="object(hl,$nextId,'',[])";
    $term1="object(hl,'$nextId','$hl1',[id-'$nextId',title-'$hl1',text-'$val'])";
    $term2="link($child,'$child_id',hl,'$nextId')";
    $term3="link(hl,'$nextId',$parent,'$parent_id')";

    $str="nextId=$nextId<br>term1=$term1<br>term2=$term2<br>term3=$term3";
    //$transLog=new Hw_Trans();
    //$transLog->updateTerm($term);
    $res=$this->_kBase->createHl($nextId,$hl1,$val,$child,$child_id,$parent,$parent_id);
    //$resArr=$this->_kBase->assert3($term1,$term2,$term3,$this->_session->user);
    
    if ($nextId && $res=='Done'){
      Hw_MyTrans::updateTerm($term1, $this->_session->user);
      Hw_MyTrans::updateTerm($term2, $this->_session->user);
      Hw_MyTrans::updateTerm($term3, $this->_session->user);

      $this->getResponse()->setHeader("Content-Type","text/plain");
      $this->getResponse()->setBody($nextId);
      
    } else {
      $this->getResponse()->setHeader("Content-Type","text/plain");
      $this->getResponse()->setBody("Failded");
    }

  }

  public function jqRetractHlLinkAction(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $child=$this->_getParam('child');
    //$parent=$this->_getParam('parent');
    $child_id=$this->_getParam('child_id');
    //$parent_id=$this->_getParam('parent_id');
    $hlId=$this->_getParam('hl_id');

    //_kBase->ask("next_hl") - finds and reserves next avaibale highlight ID number from KB

    if ($hlId<=0){
      return false;
    }

    //$term1="object(hl,$nextId,'',[])";
    $term1="object(hl,'$hlId',Title,List)";
    $term2="link($child,'$child_id',hl,'$hlId')";

    //define link to parent
    $kb=new KbTrans();
    $row=$kb->fetchRow("term_head='link' && term_arg1='hl' && term_arg2='$hlId' && term_active=1");
    if ($row->term_id>0){
      $term3="link(hl,'$hlId',{$row->term_arg3},'$row->term_arg4')";
    }
    //

    $str="hlId=$hlId<br>term1=$term1<br>term2=$term2<br>term3=$term3";
    //print $str;
    //print "<br>";
    //$mail=new Hw_Mail();
    //$mail->debugSend('retract3',$str);
    //$transLog=new Hw_Trans();
    //$transLog->updateTerm($term);
    //print $term;
    $resArr=$this->_kBase->retract3($term1,$term2,$term3,$this->_session->user);

    $resStr=$hlId;

    if ($resStr){
      $this->getResponse()->setHeader("Content-Type","text/plain");
      $this->getResponse()->setBody($resStr);
    }

  }


  public function saveTextAction(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $type=$this->_getParam('type');
    $id=$this->_getParam('id');
    $text_fld_name=$this->_getParam('text_fld_name');
    $text=$this->_getParam('text');

    //clean from extra div added in jqItem2Action
    //$cell="<div style='height:170px;overflow: auto; word-wrap:break-word;white-space:normal;'>".$cont."</div>";
    //divStr differs becuase in HTML it is generated differently
    //    $divStr='<div style="overflow: auto; height: 170px; white-space: normal;">';
    //    $html2=preg_replace('@'.$divStr.'@','',$text,-1,$count);
    //    if ($count){
    //      for($i=1;$i<=$count;$i++){
    //        $pos=mb_strripos($html2,"</div>");
    //        $len=strlen("</div>");
    //        $html2=substr_replace($html2,'',$pos,$len);
    //      }
    //    }

    $text=str_replace(array("\\n","\\r\\n"),"<br>",$text);

    if ($text){
      $text=stripslashes($text);
      $text=htmlentities($text, ENT_QUOTES, 'UTF-8');
    }
 
    if (!empty($text) && !empty($text_fld_name)){
      $res=$this->_kBase->updateObjectListProperty($type,$id,$text_fld_name,$text,$this->_session->user);
      //fb("check",'saveTextAction');
      $this->getResponse()->setHeader("Content-Type","text/plain");
      $this->getResponse()->setBody($res);
    }else{
      $sz=strlen($text);
      $this->getResponse()->setHeader("Content-Type","text/plain");
      $this->getResponse()->setBody("jqAssertHlLinkAction, error saving text"."text size is: " . $sz);    
    }
  }

  public function jqCheckLinkAction(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $child=$this->_getParam('child');
    $parent=$this->_getParam('parent');
    $child_id=$this->_getParam('child_id');
    $parent_id=$this->_getParam('parent_id');
    $term=$child.'_to_'.$parent."($child_id,$parent_id)";
    //print $term;
    $resStr=$this->_kBase->exec($term);
    $json=new Zend_Json();
    $arr=$json->decode($resStr);
    if (is_array($arr)){
      $this->getResponse()->setHeader("Content-Type","text/plain");
      $this->getResponse()->setBody("true");
    }

  }

  public function jqHlCheckAction(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $child=$this->_getParam('child');
    $parent=$this->_getParam('parent');
    $child_id=$this->_getParam('child_id');
    $parent_id=$this->_getParam('parent_id');
    $hl=$this->_getParam('hl');
    $term="{$child}_hl_to_$parent($child_id,$parent_id,'$hl')";
    $form="{$child}_hl_to_$parent(%d, %d, '%[^']')";
    $flds=array(
    0=>'Cid',
    1=>'Pid',
    2=>'Hl'
    );
    //print $term."<br>\n".$form."<br>\n";

    $arr=$this->queryKb($term,$form,$flds);
    //print "<pre>";print_r($arr);print "</pre>";
    $cont=html_entity_decode($arr[0]['Hl'],ENT_QUOTES,"utf-8");
    print $cont;
  }


  public function checkPredicateAction(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $termHead=$this->_getParam("term_head");
    $termBodyArr=$this->_getParam("body_arr");

    $filter=new Zend_Filter_PregReplace("@[\W]@","");
    $termHead=$filter->filter($termHead);

    $filtHtml=new Zend_Filter_HtmlEntities();

    $bodyStr='';
    //print_r($termBodyArr);
    if (is_array($termBodyArr)){
      $bodyStr="$termHead(";
      foreach($termBodyArr as $i=>$atom){
        if ($this->view->onlyIntString($atom)){
          $qt="";
        }else{
          $atom=$filtHtml->filter($atom);
          $qt="'";
        }
        if ($bodyStr=="$termHead("){
          $bodyStr.=$qt.$atom.$qt;
        }else{
          $bodyStr.=','.$qt.$atom.$qt;
        }

      }
      $bodyStr.=')';

    }
    $res='';
    if ($bodyStr){
      $res=$this->_kBase->check($bodyStr);
    }

    $this->getResponse()->setHeader("Content-Type","text/plain");
    $this->getResponse()->setBody($res);
  }


  public function showItemAction(){
    $type=$this->_getParam('type');
    $id=(int)$this->_getParam("id");
    $term="text_contents($id,Content)";
    $form="text_contents(%d, '%[^']')";
    $flds=array(
    0=>'Id',
    1=>'Content'
    );
    $arr=$this->queryKb($term,$form,$flds);
    $cont=html_entity_decode($arr[0]['Content'],ENT_QUOTES,"utf-8");
    $cont=str_replace(array(chr(194),chr(160)),' ',$cont);
    //$cont=strip_tags($cont);

    //    for($i=0;$i<strlen($cont);$i++){
    //      $chr=$cont{$i};
    //      print "$chr:".ord($chr)."<br>";
    //    }
    $this->view->contents=$cont;
  }




  /**
   * txtAction - view one text contents field from KB
   * @return unknown_type
   */
  public function txtAction(){
    $id=$this->_getParam("id");
    //$text=new Text();
    //$textRow=$text->fetchRow("text_id={$id}");
    //print_r($textRow);

    //$this->view->text_contents=Hw_Tidy::runTidy($textRow->text_contents);

    //print_r($this->_session->user);
    // simple query
    //$sql_query = "SELECT field1, field2 FROM table WHERE field3='val1' AND field4=5 OR ( field5='val2' AND field6>'22');";
    //
    //$query2tree = new dqml2tree($sql_query);
    //$sql_tree = $query2tree->make();
    //
    //print_r($sql_tree);
    //
    //return true;

    $type="text";
    $fldName="contents";
    $colName="Contents";
    $termStr="{$type}_$fldName($id,$colName)";
    $formStr="{$type}_$fldName(%d, '%[^']')";

    $flds=array(
    0=>'Id',
    1=>$colName
    );

    $arr=$this->queryKb($termStr,$formStr,$flds);
    //print_r($arr);
    $cont=html_entity_decode($arr[0]['Contents'],ENT_QUOTES,'utf-8');
    $cont=Hw_Tidy::runTidy($cont);
    $cont=str_replace("&nbsp;"," ",$cont);
    $this->view->text_contents=$cont;

  }

  public function jqItem2Action(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $id=$this->_getParam("id");
    //replace _ to /. In HTML ID can not have  / symbol.
    $id=str_replace(array("id-","_",'-','spc'),array('',"/",'.',' '),$id);

    //$rowid=(int)$this->_getParam("rowid");
    $type=$this->_getParam('type');
    $fld=$this->_getParam('fld');
    $cell_type=$this->_getParam('cell_type');
    $origen=$this->_getParam('origen');

    $subGridFld=$this->_subGridFld;

    $fldArr=$this->strToArr($fld);
    //print_r($fldArr);
    $typeArr=$this->strToArr($cell_type);
    //print_r($typeArr);
    if ($fld){
      $arr=$this->_kBase->getObjectById($type,$id,$fld);

      //$mail=new Hw_Mail();
      //$mail->debugSend('jqItem2',print_r($arr,true));

      $response->rows[0]['id'] = 1;
      $response->rows[0]['cell']=array();

      //print_r($data);
      $editable=$this->_config->editable->$type;
//var_dump($editable);
      $cnt=sizeof($fldArr);
      
      if ($cnt>1){

        //require_once('Hw/Thumb/ThumbLib.inc.php');

        //$str2=print_r($this->_config->editable->$type,true);

        $editLink='';
        if ($editable){
          $editLink="<tr><td colspan='2'><div><a onclick=\"return showBox(this);\" target=\"_blank\" href=\"/kb/edit?id=$id&type=$type\" class=\"thumb_image\">Edit</a></div></td></tr>";
        }
        
        $response->rows[0]['cell'][0]="<div style='height:200px;overflow:auto;'><table border='0' width='300'>".$editLink;
        foreach($fldArr as $i=>$fldTitle){

          $cont=$arr[0][$fldTitle];
          //print $cont;
          if (in_array($typeArr[$i],array('txt','str'))){
            $cont=html_entity_decode($cont,ENT_QUOTES, "utf-8");
            //$cont=Hw_Tidy::runTidy($cont);
            //$cont=str_replace($cont,'\n'," ");

          }
          if ($fldTitle){
            $fldTitleStr=$fldTitle;
          }else{
            $fldTitleStr='&nbsp;';
          }
          //$cont=strip_tags($cont);
          //print $cont;
          $idStr=str_replace(array('/','\\',':'),'-',$id);

          switch($typeArr[$i]){
            case 'txt':
              //add jquery bind
              $cell="<textarea id='textdiv_{$idStr}_$fldTitle' class='text-editors' style='height:210px;width:350px;overflow:auto;word-wrap:break-word;'>".$cont."</textarea>";
              break;
            case 'img':
              if ($subGridFld[$type]['http_path']){
                //$cell='external image';
                $imgPath=$subGridFld[$type]['http_path'].'/'.$cont;
                $imgPath=preg_replace(array('/\/\\.\//','/\/+/'),'/',$imgPath);


                //$path=DOCROOT."/hwdb/thumbs/";
                //$newFilePath=str_replace(array("//"),'/',$path.$imgPath);
//                $image=PhpThumbFactory::create($imgPath);
//                $image->resize(100,100);
//                ob_start();
//                $image->show();
//                $imgStr=ob_get_clean();

                //$cell="<a class='thumb_image' href='$imgPath' target='_blank' onclick='showBox(this);'><img border='0' class='thumb_small' id='img_{$idStr}_$fldTitle' src='$imgPath'></a>";
                $srcEnc=urlencode($cont);
                $cell="<a class='thumb_image' href='http://$imgPath' target='_blank' onclick='showBox(this);'><img border='0' class='thumb_small' id='img_{$idStr}_$fldTitle' width='120' height='120' src='/image/thumb/?cat=$type&from=shop&src=$srcEnc'></a>";
              }else{
                $imgSrc=str_replace("/","/thumb.",$cont);
                $cell="<a class='thumb_image' href='/hwdb/thumbs/$cont' target='_blank' onclick='showBox(this);'><img border='0' class='thumb_small' id='img_{$idStr}_$fldTitle' src='/hwdb/thumbs/$imgSrc'></a>";
              }
              break;
            case 'str':
              $cell="<div id='div_{$idStr}_$fldTitle' style='overflow: auto; word-wrap:break-word;white-space:normal;'>".$cont."</div>";
              break;

            default:
              $cell="<span style='white-space:normal;'>".$cont."</span>";
          }

          if (strlen($cont)>30){
            $response->rows[0]['cell'][0].="<tr><td colspan='2'><i><b>$fldTitleStr:</b></i><br>$cell</td></tr>";
          }elseif(strlen($cont)>0){
            $response->rows[0]['cell'][0].="<tr><td>$fldTitleStr</td><td>$cell</td></tr>";
          }
          //$response->rows[0]['cell'][0].='<tr><td></td></tr>';
        }
        $response->rows[0]['cell'][0].="</table></div>";
      }else{
        $editLink='';
        if ($editable){
          $editLink="<div><a onclick=\"return showBox(this);\" target=\"_blank\" href=\"/kb/edit?id=$id&type=$type\" class=\"thumb_image\">Edit</a></div>";
        }
        
        $cont=$arr[0][$fldArr[0]];
        //print $cont;
        if (in_array($typeArr[0],array('txt','str'))){
          $cont=html_entity_decode($cont, ENT_QUOTES, "utf-8");
          //$cont=Hw_Tidy::runTidy($cont);
        }
        $response->rows[0]['cell'][0]='';
        switch($typeArr[0]){

          case 'img':
            $cell="<img id='img_$id' $src='$cont'>";
            break;

          default:
            //$cell="<div id='hldiv_editor' style='height:170px;overflow: auto; word-wrap:break-word;white-space:normal;'>".$cont."</div>";
            $cell=$cont;
        }
        if ($origen) {//<>hl_editor Agregado por GC 
          $response->rows[0]['cell'][0]=$cell;
        } else {
          $response->rows[0]['cell'][0]=$editLink.$cell;
        }
      }

      $json = new Zend_Json();
      $responseStr = $json->encode($response);
      $this->getResponse()->setHeader("Content-Type","application/json, text/javascript");
      $this->getResponse()->setBody($responseStr);

    }
  }

  public function jqItemAction(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $id=(int)$this->_getParam("id");
    $type=$this->_getParam('type');
    $cell_type=$this->_getParam('cell_type');
    $fld=$this->_getParam('fld');
    $fldArr=$this->strToArr($fld);
    //print_r($fldArr);
    $typeArr=$this->strToArr($cell_type);

    if ($fld){
      foreach($fldArr as $fldName){
        $fldTitle=ucfirst($fldName);

        $terms[]="{$type}_$fldName($id,$fldTitle)";
        $forms[]="{$type}_$fldName(%d, '%[^']')";

        if (sizeof($flds)==0){
          $flds=array(
          0=>'Id',
          1=>$fldTitle
          );
        }else{
          $flds[]=$fldTitle;
        }
      }
      if ($terms){
        $termStr=implode(',',$terms);
      }
      if ($forms){
        $formStr=implode(',',$forms);
      }
      $arr=$this->queryKb($termStr,$formStr,$flds);
      //print "\n".$termStr."\n".$formStr."\n";
      //print_r($arr);
      /*Json*/

      $response->rows[0]['id'] = $id;
      $response->rows[0]['cell']=array();

      foreach($flds as $i=>$fldTitle2){
        if ($i==0) continue;
        $i1=$i-1;
        $cont=$arr[0][$fldTitle2];
        //print $cont;
        if (in_array($typeArr[$i1],array('txt','str'))){

          $cont=html_entity_decode($cont, ENT_QUOTES, "utf-8");
          $cont=Hw_Tidy::runTidy($cont);
          $cont=str_replace("&nbsp;"," ",$cont);
          //$mail=new Hw_Mail();
          //$mail->debugSend("cont",$cont);

          //$cont=html_entity_decode($cont,ENT_QUOTES,"utf-8");
          //--cleaning of chr(194),chr(160) done during the export
          //$cont=str_replace(array(chr(194),chr(160)),' ',$cont);
          //$cont=preg_replace('/\\\\u00a0/si',' ',$cont);
          //$cont=preg_replace('@Ã‚@',' ',$cont);
          //$cont=preg_replace('/\\\\u00a0/si',' ',$cont);

          //$cont=
        }
        //$cont=strip_tags($cont);
        //print $cont;

        switch($typeArr[$i1]){
          case 'txt':
            //add jquery bind
            $response->rows[0]['cell'][]="<div id='textdiv_$id' class='text-editors' style='height:210px;width:350px;overflow:auto;word-wrap:break-word;'>".$cont."</div>";
            break;
          case 'img':
            $response->rows[0]['cell'][]="<img id='img_$id' $src='$cont'>";
            break;
          case 'str':
            $response->rows[0]['cell'][]="<div id='div_$id' style='height:200px;width:280px;overflow:auto;word-wrap:break-word;'>".$cont."</div>";
            break;

          default:
            $response->rows[0]['cell'][]=$cont;
        }
      }
      //print_r($response);

      //XML
      /*
       if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
       $this->getResponse()->setHeader("Content-type","application/xhtml+xml;charset=utf-8");
       } else {
       $this->getResponse()->setHeader("Content-type","text/xml;charset=utf-8");
       }
       $et = ">";

       $responseStr= "<?xml version='1.0' encoding='utf-8'?$et\n";
       $responseStr.= "<rows>\n";
       $responseStr.= "<row>\n";


       foreach($response->rows[0]['cell'] as $i=>$fldTitle2){
       //if ($i==0) continue;
       $cd1='';
       $cd2='';
       //$i1=$i-1;
       //print $i.$typeArr[$i];
       if (in_array($typeArr[$i],array('txt','str'))){
       $cd1="<![CDATA[";
       $cd2="]]>";
       }
       $val=$response->rows[0]['cell'][$i];
       //$mail=new Hw_Mail();
       //$mail->debugSend("text",$val);
       //$val=str_replace('\n','',$val);
       //$val=html_entity_decode($val);
       $responseStr.="<cell>".$cd1.$val.$cd2."</cell>\n";
       }
       $responseStr.="</row>\n";
       $responseStr.="</rows>\n";
       $this->getResponse()->setBody($responseStr);

       */
      // be sure to put text data in CDATA


      $json = new Zend_Json();
      $responseStr = $json->encode($response);
      $this->getResponse()->setHeader("Content-Type","application/json, text/javascript");
      $this->getResponse()->setBody($responseStr);






    }



  }

  public function jqDelAction(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $type=$this->_getParam('type');
    $link_item=$this->_getParam('link_item');
    $link_item_id=(int)$this->_getParam('link_item_id');
    $id=(int)$this->_getParam("id");

    $str="{$type}_to_{$link_item}($id,$link_item_id)";
    $this->_kBase->retract($str);
    //$mail=new Hw_Mail();
    //$mail->debugSend("retract",$str);
    $this->getResponse()->setHeader("Content-Type","text/plain");
    $this->getResponse()->setBody($str);



  }

  public function delLinkAction(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $type=$this->_getParam('type');
    $id=$this->_getParam('id');
    $parent_type=$this->_getParam('parent_type');
    $parent_id=$this->_getParam('parent_id');
    $term="link($type,'$id',$parent_type,'$parent_id')";
    $res=$this->_kBase->retract($term,$this->_session->user);


    if (stristr($res,'error')){
      $mail=new Hw_Mail();
      $mail->debugSend("delLink:error retracting link",$term. "<br>". $res);
      $resStr='Error';
    }else{
      $resStr='Done';
    }

    $this->getResponse()->setHeader("Content-Type","text/plain");
    $this->getResponse()->setBody($resStr);


  }


  /***
   * generates data for autocomplete field in tree.html
   */
  public function jqItemAutoAction(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $filter = mysql_escape_string(urldecode($_GET['q']));
    $item=$this->_getParam("item");
    if (in_array($item,array_keys($this->_itemTitles))){
      //print $filter;
      //$ac = $this->_helper->autoComplete;
      $term=$this->_itemTitles[$item]."(Id,Title),search_atom(Title,'$filter')";
      //print $term."<br>";
      $resStr=$this->_kBase->execJoin($term);
      //print $resStr;
      $json=new Zend_Json();
      $arr=$json->decode($resStr);
      $resArr=array();
      $this->getResponse()->setHeader("Content-Type","text/plain");
      if (is_array($arr))
      foreach($arr as $row){
        $str.= stripslashes($row[1][2]).'|'.$row[1][1]."\n";

      }
      $this->getResponse()->setBody($str);
    }

    // print $ac->sendAutoCompletion($resArr);


  }

  public function jqListItemsAction(){
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $type=$this->_getParam('type');
    $quest_filter=$this->_getParam('quest_filter');

    $link_item_id=(int)$this->_getParam('item_id');
    //check all the links
    $itemsArr=$this->_itemTitles;

    $json=new Zend_Json();
    $resArr=array();
    $resArr1=array();
    $resArr2=array();
    $resArr3=array();
    $resArr4=array();
    foreach($itemsArr as $link_item=>$title_term){
      //print "***********************************<br>\n";
      //if ($type=='skin' && $link_item=='skin'){
      // print "$type, $link_item, $link_item_id<br>";

      //find all childs
      $term="{$link_item}_to_$type(Id,$link_item_id),{$title_term}(Id,Title)";
      //print $term."<br>\n";
      $resStr=$this->_kBase->execJoin($term);
      //print $resStr;
      //}else{
      //  continue;
      //}

      $resArr1=$json->decode($resStr);
      //print_r($resArr1);
      if (is_array($resArr1)){
        $resArr2=array();
        foreach($resArr1 as $i=>$row1){
          $resArr2[$i]['type']=$link_item;
          $resArr2[$i]['isLazy']='true';
          //$resArr1[$i]['isFolder']='true';
          $resArr2[$i]['id']=$row1[2][1];
          $resArr2[$i]['title']=$row1[2][2];
          $resArr2[$i]['tooltip']=$link_item;
          $resArr2[$i]['icon']="_hw_$link_item.png";
        }
        //print_r($resArr2);
        $resArr=array_merge($resArr,$resArr2);
      }
      if ($link_item!=$type){
        $term="{$type}_to_{$link_item}($link_item_id,Id),{$title_term}(Id,Title)";
        //print $term."<br>\n-----------------------\n";
        $resStr2=$this->_kBase->execJoin($term);
        $resArr3=$json->decode($resStr2);
        //print_r($resArr1);
        if (is_array($resArr3)){
          $resArr4=array();
          foreach($resArr3 as $i=>$row1){
            $resArr4[$i]['type']=$link_item;
            $resArr4[$i]['isLazy']='true';
            //$resArr1[$i]['isFolder']='true';
            $resArr4[$i]['id']=$row1[2][1];
            $resArr4[$i]['title']=$row1[2][2];
            $resArr4[$i]['tooltip']=$link_item;
            $resArr4[$i]['icon']="_hw_$link_item.png";
          }
          //print_r($resArr4);
          $resArr=array_merge($resArr,$resArr4);
        }

      }


      //print "***********************************<br>\n";
    }
    $resArrJson=$json->encode($resArr);
    //print $resArrJson;
    $this->getResponse()->setHeader("Content-Type","application/json, text/javascript");
    $this->getResponse()->setBody($resArrJson);

  }

  
  public function jqImageListAction(){
  	
  	$defLimit=10;
  	$this->_helper->viewRenderer->setNoRender();
  	$this->_helper->layout->disableLayout();
  	$type=$this->_getParam('type');
  	$key=$this->_getParam('key');
  	$quest_filter=$this->_getParam('quest_filter');
  	$parentType=$this->_getParam('link_item');
  	$parentId=$this->_getParam('link_item_id');
  	$selectedKeys=$this->_getParam("selected_keys");
  	$show_selected=(int)$this->_getParam("show_selected");
  	$order_by=$this->_getParam("sidx",'id');
  	$order_dir=$this->_getParam("sord",'asc');
  	
  	$mode='list';
  	$size='small';
  	
    //print_r($itemConfig);
    //$path=$itemConfig['thumb']['path_'.$size];
    //find images with same $id
    
  	
  	//fb('here');
  	$itemArr=$this->_kBase->getObjectById($parentType,$parentId);
  	$item=$itemArr[0];
  	//fb(print_r($item,true));
  	
// $itemView=array(
//  'paint'=>array(
//    'list'=>array(
//      'thumb'=>array(
//      		'path_small'  	
//$this->_dataConfig['itemView'][$parentType][$mode]['thumb']

  	$imageIdFld=$this->_dataConfig['itemView'][$parentType][$mode]['image_id'];
  	$pref=$this->_dataConfig['itemView'][$parentType][$mode]['thumb']['prefix_'.$size];
  	
  	$imageId=$item[$imageIdFld];
  	fb(array($pref,$imageId));
  	//die();
  	$page=$this->_getParam('page');
    // get how many rows we want to have into the grid
    // rowNum parameter in the grid
    $limit=$this->_getParam('rows');
    if(!$limit){
      $limit=$defLimit;
    }
    // get index row - i.e. user click to sort
    // at first time sortname parameter - after that the index from colModel

    // if we do not pass at first time index use the first column for the index
    // sorting order - at first time sortorder

  	$hwImage=new Hw_Image();
  	//public function workImage($type,$size='small',$id,$mode='list',$series=false,$sort='asc',$showNum=1,$pad=true)
  	//$images=$this->workImage($this->type,'original',$imgPath,'list',true,'asc',0,$pad)
  	//var_dump($parentType,$size,$imageId,$mode,true,$order_dir,0,true);
  	$list=$hwImage->workImage($parentType,$size,$imageId,$mode,true,$order_dir,0,true);
  	//var_dump($hwImage);
//  	var_dump($parentType,$size,$imageId,$mode,true,$order_dir);
//  	die();
  	//fb("List");
  	//fb($list);
  	if ($list){
  		$pager=new Zend_Paginator(new Zend_Paginator_Adapter_Array($list));
  		$pager->setItemCountPerPage($limit);
  		$pager->setPageRange(5);
  		$pager->setCurrentPageNumber($page);
  		$count=$pager->getTotalItemCount();
  			
  		$total=$pager->count();
  	}
  	$response=new stdClass();
  		
  	if ($count){
  		//fb(print_r("count > 0"));
  		$kbImage=new KbImage();
  		$imageIdStr=$hwImage->IdToName($parentType,$imageId,true);
//  		$sel=$kbImage->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->columns("frontend_visible")->where("object_type='$parentType' && image_name LIKE '$imageIdStr%'");
//  		$selStr=$sel->assemble();
//  		$kbImageArray=$kbImage->fetchAll($sel);
//		$sel = "SELECT `frontend_visible` FROM kb_image WHERE  object_type='".$parentType."' AND image_name LIKE '".$imageIdStr."%'";
//		$kbImageArray = $kbImage->fetchAll($sel);
//		while($row=$kbImageArray->current()){
//  			
//  			$kbImageRows[$pref.$row->image_name]=$row->toArray();
//  			$kbImageArray->next();
//  		}
//  		

  		foreach($pager as $i => $item){
  			//fb(print_r("pager"));
  			$imgArr=pathinfo($item);
  			$imgName=$imgArr['filename'];
  			
  			$imgId=str_replace($pref,'',$imgName);
  			
  			$imgChkName="img_{$parentType}_{$imgId}";
  			$imgChkId="img-{$parentType}-{$imgId}";
  			$btnId="btn-{$parentType}-{$imgId}";
  			$curData=array();
  			
//  			$kbImage->query("INSERT IGNORE INTO kb_image ('object_type','image_name') VALUES (?, ?)", array($parentType, $imgName));
  			
		  	$data= array(
		  		'object_type'=>$parentType,
		  		'image_name'=>$imgName,
		   	);
		   	try{
		   		$kbImage->insert($data);
		   	}
		   	catch(Exception  $e){}
		   	
		  	//fb(print_r('exception passed'));
  			//$imgChkChecked=
  			
		  	//$sel = "SELECT `frontend_visible` FROM kb_image WHERE  object_type='".$parentType."' AND image_name = '".$imgName."'";
			$sel=$kbImage->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->columns("frontend_visible")->where("object_type='".$parentType."' AND image_name = '".$imgName."'");
		  	$displayed = $kbImage->fetchAll($sel);
		
  			
  		    $imgChecked='';
  		    
  				if ($displayed[0]['frontend_visible']=="1" ){
  					 fb($curData,'curData');
	  				 $imgChecked='checked';
  				 	
  				}
  				

  			
  			$response->rows[$i]['id'] = $i;
  			$response->rows[$i]['cell']=array($i,"<img src='$item' align='left'><div>filename: $imgName</div><div>Visible:<input type='checkbox' class='image-visible-chk' name='$imgChkName' id='$imgChkId' $imgChecked/></div>
  			<button id='$btnId' onclick=\"$('#$imgChkId').trigger('click');\">Apply</button><br/>
  			<button id='upload_btn' onclick=\"window.open ('http://hwarch/image?n=$imgName&fold=".$this->_getParam('link_item')."&s=".$size."&i=".$imageId."','mywindow','menubar=0,resizable=0,width=350,height=250');\">Change this File</button><br/>
  			<button id='delete_btn' onclick=\"window.open ('http://hwarch/image/delete?n=$imgName&fold=".$this->_getParam('link_item')."&s=".$size."&i=".$imageId."','mywindow','menubar=0,resizable=0,width=350,height=250');\">Delete this File</button><br/>
  			<!-- ".$parentType."  ".$size."   ".$imageId."-->");
  			
  		}
  		
  		$temp = array(
  			'id'=>0,
  			'cell'=>array(0,"New picture<br/>
  			<button id='upload_btn' onclick=\"window.open ('http://hwarch/image?n=&fold=".$this->_getParam('link_item')."','mywindow','menubar=0,resizable=0,width=350,height=250');\">Upload a new File1</button>")
  		);
  		
  		array_unshift($response->rows,$temp);  		
  	}else{
  		$response->rows[0]['id'] = 0;
  		$response->rows[0]['cell']=array(0,"no picture<br/>
  			<button id='upload_btn' onclick=\"window.open ('http://hwarch/image?n=$imgName&fold=".$this->_getParam('link_item')."','mywindow','menubar=0,resizable=0,width=350,height=250');\">Upload a new File2</button>");
  	}
  	 
  	$response->page = $page;
  	$response->total = $total;//total number of pages
  	$response->records = $count;//total count of record
  	 
  	$json = new Zend_Json();
  	$responseStr = $json->encode($response);

  	$this->getResponse()->setHeader("Content-Type","application/json, text/javascript");
  	$this->getResponse()->setBody($responseStr);
  	 
  	
  	
  }

  /**
   * show objects linked to the parent
   * call:
   * /link/jq-list2/?link_item=TAP&link_item_id=4&type=APA&key=id&quest_filter=&selected_keys=id,title,workNumber
   * there should be asserted link tems:
   * assert(link('APA','40','TAP','4')). child=>parent
   * where APA, 40 are type and id of child and TAP, 4 are type and id of parent
   * @return array
   */
  public function jqList2Action(){
  	$this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $type=$this->_getParam('type');
    $key=$this->_getParam('key');
    $quest_filter=$this->_getParam('quest_filter');
    $parentType=$this->_getParam('link_item');
    $parentId=$this->_getParam('link_item_id');
    $selectedKeys=$this->_getParam("selected_keys");
    $show_selected=(int)$this->_getParam("show_selected");
    $order_by=$this->_getParam("sidx",'id');
    $order_dir=$this->_getParam("sord",'asc');
    //$limit=(int)$this->_getParam("rows");

    /*
     * set in bootstrap.php
     *  $fields=array(
     'workNumber'=>$itemWorkNumber,
     'id'=>$itemIds,
     'title'=>$itemTitles
     );
     */

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
      $selectedKeys="id,$itemIdField,$itemTitle".$keyStr;
    }


    if ($show_selected){
      $count=$this->_kBase->countObjects($type, 0, $key,$quest_filter,$selectedKeys);
    }else{
      $count=$this->_kBase->countObjects("[$parentType,$type]", $parentId, $key,$quest_filter,$selectedKeys);
    }
    
    //print "count:$count\n";
    $params['defIndex']='id'; //default order field
    $params['defLimit']=20; //default limit
    $params['count']=$count;//
    $resPaging=$this->jqPaging($params);
    //print_r($resPaging);

    $resArr=array();
    if ($show_selected){
      $resArr=$this->_kBase->selectFromKb("[$parentType,$type]", $parentId, $key,$quest_filter,$selectedKeys,$resPaging->start,$resPaging->limit, $order_by, $order_dir);
      $resArrAll=$this->_kBase->selectFromKb($type, 0,$key,$quest_filter,$selectedKeys,$resPaging->start,$resPaging->limit,$order_by, $order_dir);
    }else{
      $resArrAll=$this->_kBase->selectFromKb("[$parentType,$type]", $parentId, $key,$quest_filter,$selectedKeys,$resPaging->start,$resPaging->limit, $order_by, $order_dir);
    }
    
    $i=0;
    $selectedRows=array();
    if (is_array($resArrAll)){
      if (is_array($resArr)){
        foreach($resArr as $resRow){
          $selectedRows[]=$resRow[$itemIdField];
        }
      }

      //print_r($selectedRows);

       //$mail=new Hw_Mail();
       //$mail->debugSend('list2','<pre>'.print_r($resArrAll,true).'</pre>');
           //print_r($resArrAll);

      foreach($resArrAll as $r=>$rowArr){
        //$rowArr=$resArrAll[$r];
        //$id=$rowArr['id'];

        //if ($id){
        $itemId=$rowArr[$itemIdField];
        if (!$rowCache[$itemId]){
          $rowCache[$itemId]=true;
          $response->rows[$i]['id'] = $itemId;
          $style='overflow: auto; word-wrap:break-word;white-space:normal;';
          if (in_array($itemId,$selectedRows)){
            $style.="background-color: yellow;";
          }

          $title=trim($rowArr[$itemTitle]);
          if (is_string($title)){
            $title=html_entity_decode($title, ENT_QUOTES,'utf-8');
            //$strConv=new Hw_Convert_Str();
            //$title=$strConv->htmlDecode($title);
            //$title=html_entity_decode($title, ENT_QUOTES,'utf-8');
            //$title=Hw_Tidy::runTidy($title);
            //$title=str_replace("&nbsp;",' ',$title);
            //$title=str_replace('\200\\234\\','"',$title);
            $title=nl2br($title);
            //            $len=strlen($title);
            //            $strArr=array();
            //            for($l=0;$l<$len;$l++){
            //              $strArr[]=array($title{$l},ord($title{$l}));
            //            }
            //            $allStrArr[]=$strArr;
          }
          //$title='This is test This is testThis is testThis is testThis is testThis is testThis is testThis is testThis is testThis is test';
          $itemIdStr=html_entity_decode($itemId,ENT_QUOTES,'UTF-8');
          $response->rows[$i]['cell']=array($itemIdStr,"<span style='$style'>".$title."</span>");
          //$response->rows[$i]['cell']=array("-$id",$rowArr['title']);
          $i++;
        }
        //}
      }


    }

    /*    $mail=new Hw_Mail();
     $mail->debugSend('allStrArr',"<pre>".print_r($allStrArr,true).'</pre>');
     */



    $response->page = $resPaging->page;
    $response->total = $resPaging->total;//total number of pages
    $response->records = $resPaging->records;//total count of record

    $json = new Zend_Json();
    $responseStr = $json->encode($response);

    $this->getResponse()->setHeader("Content-Type","application/json, text/javascript");
    $this->getResponse()->setBody($responseStr);

    //print_r($resArrAll);
  }

  public function jqListAction(){

    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->layout->disableLayout();
    $type=$this->_getParam('type');
    $quest_filter=$this->_getParam('quest_filter');
    $link_item=$this->_getParam('link_item');
    $link_item_id=(int)$this->_getParam('link_item_id');
    $show_selected=(int)$this->_getParam("show_selected");

    //$uri=$_SERVER['REQUEST_URI'];
    //$mail=new Hw_Mail();
    //$mail->debugSend("req_uri",$uri);

    //print "$quest_filter";

    //search($qry='', $itemType='', $parentItemType=null, $parentItemId=null)
    //print "search($quest_filter, $type, $link_item, $link_item_id)";
    //$link_item_id=2;
    if (strlen($quest_filter)>0){
      if ($link_item_id>0){
        $term2="{$type}_to_{$link_item}(Id,X),{$this->_itemTitles[$type]}(Id, Title),search_atom(Title,'$quest_filter')";
        $frm2="{$type}_to_{$link_item}(%d, %d), {$this->_itemTitles[$type]}(%d, '%[^']'), search_atom('%[^']', '$quest_filter')";
        $fields2=array(
        0=>'Id',
        3=>'Title'
        );
      }else{
        $term2="{$this->_itemTitles[$type]}(Id, Title),search_atom(Title,'$quest_filter')";
        $frm2="{$this->_itemTitles[$type]}(%d, '%[^']'), search_atom('%[^']', '$quest_filter')";
        $fields2=array(
        0=>'Id',
        1=>'Title'
        );
      }
    }else{
      if ($link_item_id>0){
        $term2="{$type}_to_{$link_item}(Id,$link_item_id),{$this->_itemTitles[$type]}(Id, Title)";
        $frm2="{$type}_to_{$link_item}(%d, %d), {$this->_itemTitles[$type]}(%d, '%[^']')";
        $fields2=array(
        0=>'Id',
        3=>'Title'
        );
      }else{
        $term2="{$this->_itemTitles[$type]}(Id, Title)";
        $frm2="{$this->_itemTitles[$type]}(%d, '%[^']')";
        $fields2=array(
        0=>'Id',
        1=>'Title'
        );

      }

    }

    $resArr=$this->queryKb($term2,$frm2,$fields2);
    //print "\n".$term2."\n$frm2\n";
    //print_r($resArr);
    //$resStr=$this->_kBase->search($quest_filter, $type, $link_item, $link_item_id);

    //show_selected>0 - show ALL ietms and show selected items with yellow
    if ($show_selected>0){
      if (strlen($quest_filter)>2){
        $term=$this->_itemTitles[$type]."(Id,Title),search_atom(Title,'$quest_filter')";
        $frm=$this->_itemTitles[$type]."(%d, '%[^']'), search_atom('%[^']', '$quest_filter')";
        $fields=array(
        0=>'Id',
        1=>'Title'
        );

      }else{
        $term=$this->_itemTitles[$type]."(Id,Title)";
        $frm=$this->_itemTitles[$type]."(%d, '%[^']')";
        $fields=array(
        0=>'Id',
        1=>'Title'
        );

      }
      //print $term;
      $resArrAll=$this->queryKb($term,$frm,$fields);
      //$resStrAll=$this->_kBase->search($quest_filter, $type,'',0);
      //print $resStrAll;
      //$resStr=$this->_kBase->search($quest_filter, $type, $link_item, $link_item_id);
    }
    //print $resStr;


    //print_r($resArrAll);
    //return true;

    //$resArr=preg_split("@\n+@",$resStr);
    //$show_selected=0;
    if ($show_selected>0){
      $i=0;
      $selectedRows=array();
      if (is_array($resArrAll)){
        if (is_array($resArr))
        foreach($resArr as $resRow){
          $selectedRows[]=$resRow['Id'];
        }

        //print_r($selectedRows);
        $count=count($resArrAll);
        $params['defIndex']='id'; //default order field
        $params['defLimit']=10; //default limit
        $params['count']=$count;//
        $resPaging=$this->jqPaging($params);
        for($r=$resPaging->start;$r<$resPaging->start+$resPaging->limit;$r++){
          $rowArr=$resArrAll[$r];
          $id=$rowArr['Id'];
          if ($id){
            if (!$rowCache[$id]){
              $rowCache[$id]=true;
              $response->rows[$i]['id'] = $id;
              $style='';
              if (in_array($id,$selectedRows)){
                $style="background-color: yellow;";
              }
              $title=trim($rowArr['Title']);
              if (is_string($title)){
                $title=html_entity_decode($title, ENT_QUOTES,'utf-8');
                //$title=html_entity_decode($title, ENT_QUOTES,'utf-8');
                $title=Hw_Tidy::runTidy($title);
                $title=str_replace("&nbsp;",' ',$title);
                $title=str_replace("\\n\\r","\n",$title);
                $title=nl2br($title);
              }
              $response->rows[$i]['cell']=array($id,"<span style='$style'>".$title."</span>");
              //$response->rows[$i]['cell']=array("-$id",$rowArr['title']);
              $i++;
            }
          }
        }


      }
    }else{
      $i=0;
      if (is_array($resArr)){
        $count=count($resArr);
        $params['defIndex']='id'; //default order field
        $params['defLimit']=10; //default limit
        $params['count']=$count;//
        $resPaging=$this->jqPaging($params);
        for($r=$resPaging->start;$r<$resPaging->start+$resPaging->limit;$r++){
          $rowArr=$resArr[$r];
          $id=$rowArr['Id'];
          if ($id){
            if (!$rowCache[$id]){
              $rowCache[$id]=true;
              $response->rows[$i]['id'] = $id;
              $title=trim($rowArr['Title']);
              if (is_string($title)){
                $title=html_entity_decode($title, ENT_QUOTES,'utf-8');
                //$title=html_entity_decode($title, ENT_QUOTES,'utf-8');
                $title=Hw_Tidy::runTidy($title);
                $title=str_replace("&nbsp;",' ',$title);
                $title=str_replace(array("\\n\\r","\\r\\n","\\n","\r","\n"),"<br>",$title);

                $title=nl2br($title);
                //$title=nl2br($title);
              }
              $response->rows[$i]['cell']=array($id,$title,'&nbsp;');
              $i++;
            }
          }
        }


        //print $json->encode($response);

      }
    }

    $response->page = $resPaging->page;
    $response->total = $resPaging->total;//total number of pages
    $response->records = $resPaging->records;//total count of record

    $json = new Zend_Json();
    $responseStr = $json->encode($response);

    //XML
    /*
     // set the header information
     if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
     $this->getResponse()->setHeader("Content-type","application/xhtml+xml;charset=utf-8");
     } else {
     $this->getResponse()->setHeader("Content-type","text/xml;charset=utf-8");
     }
     $et = ">";
     $responseStr.= "<?xml version='1.0' encoding='utf-8'?$et\n";

     $responseStr.= "<rows>\n";
     $responseStr.= "<page>".$resPaging->page."</page>\n";
     $responseStr.= "<total>".$resPaging->total."</total>\n";
     $responseStr.= "<records>".$resPaging->records."</records>\n";
     $rowsNum=sizeof($response->rows);
     //print_r($response->rows);
     for($r=0;$r<$rowsNum;$r++){
     $responseStr.="<row id='".$response->rows[$r]['id']."'>\n";
     $responseStr.="<cell>".$response->rows[$r]['cell'][0]."</cell>\n";
     $responseStr.="<cell><![CDATA[".$response->rows[$r]['cell'][1]."]]></cell>\n";
     $responseStr.="<cell><![CDATA[".$response->rows[$r]['cell'][2]."]]></cell>\n";
     $responseStr.="</row>\n";
     }
     $responseStr.="</rows>\n";
     */


    //$mail=new Hw_Mail();
    //$mail->debugSend('json',$responseStr);
    $this->getResponse()->setHeader("Content-Type","application/json, text/javascript");
    $this->getResponse()->setBody($responseStr);

  }




}
?>