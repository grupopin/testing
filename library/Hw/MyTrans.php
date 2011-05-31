<?php

class Hw_MyTrans{

  private static $_trans;
  private static $_registryClassName = 'Hw_MyTrans';


  public static function getInstance() {
    if(!self::$_trans){
      self::$_trans= new Hw_MyTrans();
    }
    return self::$_trans;
  }



  public static function setInstance(Hw_MyTrans $trans)
  {
    if (self::$_trans !== null) {
      require_once 'Zend/Exception.php';
      throw new Zend_Exception('Trans Log is already initialized');
    }
    self::setClassName(get_class($trans));
    self::$_trans = $trans;
  }

  /**
   * Initialize the default registry instance.
   *
   * @return void
   */
  protected static function init()
  {
    self::setInstance(new self::$_registryClassName());
  }

  /**
   * Set the class name to use for the default instance.
   * Does not affect the currently initialized instance, it only applies
   * for the next time you instantiate.
   *
   * @param string $registryClassName
   * @return void
   * @throws Zend_Exception if the registry is initialized or if the
   *   class name is not valid.
   */
  public static function setClassName($registryClassName = 'Hw_MyTrans')
  {
    if (self::$_trans !== null) {
      require_once 'Zend/Exception.php';
      throw new Zend_Exception('Trans Log is already initialized');
    }

    if (!is_string($registryClassName)) {
      require_once 'Zend/Exception.php';
      throw new Zend_Exception("Argument is not a class name");
    }

    /**
     * @see Zend_Loader
     */
    require_once 'Zend/Loader.php';
    Zend_Loader::loadClass($registryClassName);

    self::$_registryClassName = $registryClassName;
  }



  public static function parseTerm($term,$mode='assert'){
    //object(text,'1.2.3','Title text', [title_en-'Title in En', id-12,....])

    //ob_start();
    //debug_print_backtrace();
    //$str=ob_get_clean();
    //fb($str,'parseTerm');



    $goodTerm=false;
    if (strpos($term,'link')===0){
      $regEx='/^link\\((\'[^\'\\r\\n]*\'|[^,\\r\\n]*),(\'[^\'\\r\\n]*\'|[^,\\r\\n]*),(\'[^\'\\r\\n]*\'|[^,\\r\\n]*),(\'[^\'\\r\\n]*\'|[^\'\\r\\n]*)\\)\\.?$/m';
      $termHead='link';
      $goodTerm=true;
    }elseif(strpos($term,'object')===0){
      if ($mode=='assert'){
        $regEx='/^object\\((\'[^\'\\r\\n]*\'|[^,\\r\\n]*),(\'[^\'\\r\\n]*\'|[^,\\r\\n]*),(\'[^\'\\r\\n]*\'|[^,\\r\\n]*),(\\[[^\\r\\n]*\\])\\)\\.?$/m';
      }else{//retract
        $regEx='/^object\\((\'[^\'\\r\\n]*\'|[^,\\r\\n]*),(\'[^\'\\r\\n]*\'|[^,\\r\\n]*),(\'[^\'\\r\\n]*\'|[^,\\r\\n]*),(\'[^\'\\r\\n]*\'|[^\'\\r\\n]*)\\)\\.?$/m';
      }
      $termHead='object';
      $regEx2='/^object\\((\'[^\'\\r\\n]*\'|[^,\\r\\n]*),(\\[[^\\r\\n]*\\])\\)\\.?$/m';
      $goodTerm=true;
    }

    if (!$goodTerm) return false;

    //print $regEx;
    preg_match_all($regEx, $term,$matches,PREG_PATTERN_ORDER);

    //require_once "Hw_Mail.php";
    //$mail=new Hw_Mail();
    //$mail->debugSend("parseTerm",print_r($matches,true));

    if ($termHead=='object' && !$matches[1]){
      preg_match_all($regEx2, $term,$matches,PREG_PATTERN_ORDER);
      $obj2=true;
    }
    //print_r($matches);
    $arr['functor']=$termHead;
    $arr['body']=$matches[1][0].', '.$matches[2][0].', '.$matches[3][0].', '.$matches[4][0];
    $arr['arg1']=trim($matches[1][0],"'");
    if (!$obj2){
      $arr['arg2']=trim($matches[2][0],"'");
      $arr['arg3']=trim($matches[3][0],"'");
    }
    switch($termHead){
      case 'link':
        $arr['arg4']=trim($matches[4][0],"'");
        break;
      case 'object':
        if ($obj2){
          $arr['list']=$matches[2][0];
        }else{
          $arr['list']=$matches[4][0];
        }
        break;
      default:;
    }
    if ($obj2){
      $arr['arity']=2;
    }else{
      $arr['arity']=4;
    }

    return $arr;

  }






  //  public static function parseTerm($term){
  //    $leftBracketPos=strpos($term,"(");
  //    $rightBracketPos=strrpos($term,")");
  //    $termHead=substr($term,0,$leftBracketPos);
  //    $termBody=substr($term,$leftBracketPos+1,$rightBracketPos-$leftBracketPos-1);
  //
  //    //print $termHead."-".$termBody."<br>";
  //    $convStr=new Hw_Convert_Str();
  //    $termArr=$convStr->getcsv($termBody,",","'");
  //    $termArity=count($termArr);
  //    $arr['functor']=$termHead;
  //    $arr['body']=$termBody;
  //    $arr['args']=$termArr;
  //    $arr['arity']=$termArity;
  //
  //    return $arr;
  //
  //
  //  }

  public function updateDbTerm($type,$id,$title,$List,$user){
    $trans=new KbTrans();
    $row=$trans->fetchRow("term_head='object' && term_arg1='$type' && term_arg2='$id' && term_active='1'");
    $data['term_head']='object';
    $data['term_arity']=4;
    $data['term_arg1']=$type;
    $data['term_arg2']=$id;
    $data['term_arg3']=$title;
    $data['term_arg4']='';
    $data['term_list']=$List;
    $data['created_by']=$user['id'];
    $data['created_on']=date("Y-m-d H:i:s");
    $data['created_ip']=$_SERVER['REMOTE_ADDR'];
    $data['operation']='assert';
    $data['term_active']=1;
    $data['term_source']='backend';
    if ($row->term_id>=1){
      //$data=$row->toArray();
      $trans->update(array('term_active'=>0), "term_id='{$row->term_id}'");
    }
    $trans->insert($data);
    $lastId=$trans->getAdapter()->lastInsertId();
    return $lastId;

  }

  /**
   * adds/deletes(deactivates) a term to/from db. duplicates are allowed, active term is only one with term_active=1
   * @var $termStr string - representation of term
   * @var $user integer - Id of logged in user
   * @var $operation string  -load/assert/retract. 'load': terms are loaded along with srv.pl. assert/retract - terms are asserted or retracted when server is started or after it.
   * @return boolean
   */
  public static function updateTerm($termStr,$user,$operation='assert'){

    //require_once 'KbTrans.php';
    $trans=new KbTrans();

    $arr=self::parseTerm($termStr,$operation);
    //print_r($arr);

    //$arr['body']=mysql_escape_string($arr['body']);
    switch($arr['functor']){
      case 'link':
        $linkStr="{$arr['functor']},{$arr['arg1']},{$arr['arg2']},{$arr['arg3']},{$arr['arg4']}";
        $row=$trans->fetchRow("concat(term_head,',',term_arg1,',',term_arg2,',',term_arg3,',',term_arg4)='$linkStr' && term_active=1");
        break;
      case 'object':
        $objStr="{$arr['functor']},{$arr['arg1']},{$arr['arg2']}";
        $row=$trans->fetchRow("concat(term_head,',',term_arg1,',',term_arg2)='$objStr' && term_active=1");
        break;
    }

    //$mail=new Hw_Mail();
    //$mail->debugSend("updateTerm",print_r($arr,true).'<br>term_id='.$row->term_id);
    if ($operation=='assert'){
      if ($row->term_id<1){
        $data['term_head']=$arr['functor'];
        $data['term_arity']=$arr['arity'];
        $data['term_arg1']=$arr['arg1'];
        $data['term_arg2']=$arr['arg2'];
        $data['term_arg3']=$arr['arg3'];
        $data['term_arg4']=$arr['arg4'];
        $data['term_list']=$arr['list'];
        $data['created_by']=$user['id'];
        $data['created_on']=date("Y-m-d H:i:s");
        $data['created_ip']=$_SERVER['REMOTE_ADDR'];
        $data['operation']=$operation;

        $trans->insert($data);
        $lastId=$trans->getAdapter()->lastInsertId();
        return $lastId;
      }else{
        return false;
      }
    }elseif($operation=='retract'){
      if ($row->term_id>0){
        //deactivate the term
        $trans->update(array('term_active'=>0),"term_id='{$row->term_id}'");
      }
    }

  }

}


?>