<?php
class Pl_Adapter{

  protected $_connection;

  /**
   * User-provided configuration
   *
   * @var array
   */
  protected $_config=array();
  public $_dataArr=array();

  public function __construct( $config){
    /*
     * Verify that adapter parameters are in an array.
     */
    if(!is_array($config)){
      /*
       * Convert Zend_Config argument to a plain array.
       */
      if($config instanceof Zend_Config){
        $config=$config->toArray();
      }else{
        /**
         * @see Zend_Db_Exception
         */
        require_once 'Pl/Exception.php';
        throw new Pl_Exception('Adapter parameters must be in an array or a Zend_Config object');
      }
    }
    $this->_checkRequiredOptions($config);

    $this->_config=array_merge($this->_config, $config);

    //print_r($this->_config);


  }

  /**
   * Check for config options that are mandatory.
   * Throw exceptions if any are missing.
   *
   * @param array $config
   * @throws Zend_Db_Adapter_Exception
   */
  protected function _checkRequiredOptions(array $config){
    // we need at least a dbname
    if(!array_key_exists('host', $config)){
      require_once 'Pl/Exception.php';
      throw new Pl_Exception("Configuration array must have a key for 'dbname' that names the database instance");
    }

    if(!array_key_exists('port', $config)){
      /**
       * @see Zend_Db_Adapter_Exception
       */
      require_once 'Pl/Exception.php';
      throw new Pl_Exception("Configuration array must have a key for 'password' for login credentials");
    }

  }

  /**
   * Returns the underlying database connection object or resource.
   * If not presently connected, this initiates the connection.
   *
   * @return object|resource|null
   */
  public function getConnection(){
    $this->_connect();
    return $this->_connection;
  }

  /**
   * Returns the configuration variables in this adapter.
   *
   * @return array
   */
  public function getConfig(){
    return $this->_config;
  }

  protected function _connect(){
    //do not allow creation of connection if it already exists
    if($this->_connection){
      return false;
    }
    $this->_connection=curl_init();
    if(!$this->_connection){

      require_once 'Pl/Exception.php';
      throw new Pl_Exception("Curl error: Could not initiate curl session");
    }

    curl_setopt($this->_connection, CURLOPT_PORT, (integer)$this->_config['port']);
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host']);
    curl_setopt($this->_connection, CURLOPT_RETURNTRANSFER, 1);
    if($err=curl_error($this->_connection)){
      require_once 'Pl/Exception.php';
      throw new Pl_Exception("Curl error: " . curl_error($this->_connection));
    }
  }

  protected function _execute(){
    if(!$this->_connection){
      require_once 'Pl/Exception.php';
      throw new Pl_Exception("Curl error: Please call _connect() before execution");
    }
    $res=curl_exec($this->_connection);
    //to speedup curl transactions
    //$this->_close();
    return $res;
  }

  protected function _close(){
    if(!$this->_connection){
      require_once 'Pl/Exception.php';
      throw new Pl_Exception("Curl error: You can not close connection as it is not yet open/or already closed?.");
    }
    $res=curl_close($this->_connection);
  }

  public function assert( $termStr='', $user){
    //var_dump(debug_backtrace());
    //print "assert";
    if (!$termStr) return false;
    $this->_connect();
    $term1=urlencode($termStr);
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/assert?user={$user['id']}&term=" . $term1);

    $res=$this->_execute();

    if($res){
      //      $mail=new Hw_Mail();
      //      ob_start();
      //      debug_print_backtrace();
      //      $dbgStr=ob_get_clean();
      //      $mail->debugSend("assert",'res:'.$res."<br>".$termStr."<br><pre>".$dbgStr."</pre>");
      $newId=Hw_MyTrans::updateTerm($termStr, $user);
    }
    return $newId;
  }

  public function deleteText($id){
    $this->_connect();
    $id2=urlencode($id);
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/deleteText?id=$id2");
    return $res=$this->_execute();
  }

  public function assert3( $term1,$term2, $term3, $user){
    $this->_connect();
    //$term1url=urlencode($term1);
    //$term2url=urlencode($term2);
    //$term3url=urlencode($term3);

    //$urlStr="user={$user['id']}&term1=" . $term1url."&term2=".$term2url."&term3=".$term3url;

    $postFields=array(
      'user'=>$user['id'],
      'term1'=>$term1,
      'term2'=>$term2,
      'term3'=>$term3
    );

    curl_setopt($this->_connection, CURLOPT_POST, 1);
    curl_setopt($this->_connection, CURLOPT_POSTFIELDS,$postFields);
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/assert3");

    $res=$this->_execute();

    if($res){
      $mail=new Hw_Mail();
      $mail->debugSend("assert3",'res:'.$res."<br>".print_r($postFields,true));
      $newId1=Hw_MyTrans::updateTerm($term1, $user);
      $newId2=Hw_MyTrans::updateTerm($term2, $user);
      $newId3=Hw_MyTrans::updateTerm($term3, $user);
    }
    return array($newId1,$newId2, $newId3);
  }

  public function insertTerm($termStr,$id,$type,$user){
    $this->_connect();
    $postFields=array(
      'user'=>$user['id'],
      'term'=>$termStr,
      'id'=>$id,
      'type'=>$type,
    );

    curl_setopt($this->_connection, CURLOPT_POST, 1);
    curl_setopt($this->_connection, CURLOPT_POSTFIELDS,$postFields);
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/insertTerm");

    $res=$this->_execute();

    return $res;

  }

  public function updateTerm($termStr,$id,$type,$user){
    $this->_connect();
    $postFields=array(
      'user'=>$user['id'],
      'term'=>$termStr,
      'id'=>$id,
      'type'=>$type,
    );

    curl_setopt($this->_connection, CURLOPT_POST, 1);
    curl_setopt($this->_connection, CURLOPT_POSTFIELDS,$postFields);
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/updateTerm");

    $res=$this->_execute();

    return $res;

  }

  public function createHl($nextId,$hl1,$val,$child,$child_id,$parent,$parent_id){
    $this->_connect();
    $postFields=array(
   		'nextId'=>$nextId,
   		'hl_short'=>$hl1,
   		'hl_text'=>$val,
   		'child'=>$child,
      'child_id'=>$child_id,
   		'parent'=>$parent,
   		'parent_id'=>$parent_id
    );

    curl_setopt($this->_connection, CURLOPT_POST, 1);
    curl_setopt($this->_connection, CURLOPT_POSTFIELDS,$postFields);
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/createHl");
    $res=$this->_execute();
    return $res;

  }

  public function retract3( $term1,$term2, $term3, $user){
    $this->_connect();
    $term1url=urlencode($term1);
    $term2url=urlencode($term2);
    $term3url=urlencode($term3);

    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/retract3?user={$user['id']}&term1=" . $term1url."&term2=".$term2url."&term3=".$term3url);

    $res=$this->_execute();

    if($res){
      //$mail=new Hw_Mail();
      //$mail->debugSend("assert",'res:'.$res."<br>".$termStr);
      Hw_MyTrans::updateTerm($term1, $user, 'retract');
      Hw_MyTrans::updateTerm($term2, $user, 'retract');
      Hw_MyTrans::updateTerm($term3, $user, 'retract');
      return true;
    }
    return false;
  }

  public function assert4( $term1,$term2, $term3, $term4, $user){
    $this->_connect();
    //!!!$term1 is retract - WE DO NOT enter into DB retracts
    $term1url=urlencode($term1);
    $term2url=urlencode($term2);
    $term3url=urlencode($term3);
    $term4url=urlencode($term4);


    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/assert4?user={$user['id']}&term1=" . $term1url."&term2=".$term2url."&term3=".$term3url."&term4=$term4url");
    //$mail=new Hw_Mail();
    //$mail->debugSend("assert4","/assert4?term1=" . $term1url."&term2=".$term2url."&term3=".$term3url."&term4=$term4url");
    $res=$this->_execute();

    //$mail=new Hw_Mail();
    //$mail->debugSend("assert4, res:$res",$res);
    if($res){
      //$mail=new Hw_Mail();
      //$mail->debugSend("assert",'res:'.$res."<br>".$termStr);
      //!!!$term1 is retract - WE DO NOT enter into DB retracts
      $newId2=Hw_MyTrans::updateTerm($term2, $user);
      $newId3=Hw_MyTrans::updateTerm($term3, $user);
      $newId4=Hw_MyTrans::updateTerm($term4, $user);
    }
    return array($newId2,$newId3, $newId4);
  }



  public function updateObjectListProperty($type,$id,$key,$text,$user){
    $this->_connect();
    //!!!$term1 is retract - WE DO NOT enter into DB retracts
    //$textUrlVar=urlencode($text);
    if (!empty($text)){
      //$postFields="type=$type&id=$id&key=$key&val=$textUrlVar";
      $postFields=array(
        'type'=>$type,
        'id'=>$id,
        'key'=>$key,
        'val'=>$text,
        'user'=>$user['id']
      );
      //$mail=new Hw_Mail();
      //$mail->debugSend("update text",$text);
      curl_setopt($this->_connection, CURLOPT_POST, 1);
      curl_setopt($this->_connection, CURLOPT_POSTFIELDS,$postFields);
      curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/updateObjectListProperty");
    }
    $res=$this->_execute();
    return $res;
  }

  public function testPost($id){
    $this->_connect();
    //!!!$term1 is retract - WE DO NOT enter into DB retracts
    if ($id){
      $postFields="id=$id";
      curl_setopt($this->_connection, CURLOPT_POST, 1);
      curl_setopt($this->_connection, CURLOPT_POSTFIELDS  ,$postFields);
      curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/testPost");
    }
    $res=$this->_execute();
    return $res;
  }


  public function retract( $termStr, $user){
    $this->_connect();
    $term1=urlencode($termStr);
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/retract?term=" . $term1);

    $res=$this->_execute();
    if($res){
      Hw_MyTrans::updateTerm($termStr, $user,'retract');
    }
    return $res;
  }

  /**
   *
   * The idea is to build some easy way to generate prolog terms from php arrays...
   * @todo build queryBuilder for prolog
   * @param $qryArr
   * @return unknown_type
   */
  public function queryBuilder( $qryArr=array()){
    /*
     $term2="{$type}_to_{$link_item}(Id,X),{$this->_itemTitles[$type]}(Id, Title),search_atom(Title,'$quest_filter')";
     $frm2="{$type}_to_{$link_item}(%d, %d), {$this->_itemTitles[$type]}(%d, '%[^']'), search_atom('%[^']', '$quest_filter')";
     $fields2=array(
     0=>'Id',
     3=>'Title'
     );
     */
    $qry=array('columns'=>array('i#paint#id','s#paint#work_num'),'from'=>array('paint','paint_to_skin'),

    'where'=>array('work_num',$searchText));

  }

  public function ask($req,$qry=''){
    $this->_connect();
    if ($qry){
      $qryStr="?qry=".urlencode($qry);
    }
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/$req{$qryStr}");
    return $this->_execute();
  }

  public function question( $questStr){
    $this->_connect();
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/quest?goal=" . urlencode($questStr));
    return $this->_execute();
  }

  public function showList( $questStr){
    $this->_connect();
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/list?goal=" . urlencode($questStr));
    return $this->_execute();
  }

  public function skinSearch( $qry){
    $this->_connect();
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/skinSearch?qry=" . urlencode($qry));
    return $this->_execute();
  }

  public function graphSearch( $qry){
    $this->_connect();
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/graphSearch?qry=" . urlencode($qry));
    return $this->_execute();
  }

  public function search( $qry='', $itemType='', $parentItemType=null, $parentItemId=null){
    $this->_connect();
    $url=$this->_config['host'] . "/search?qry=" . urlencode($qry) . "&itemType=$itemType&parentItemType=$parentItemType&parentItemId=$parentItemId";
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    return $this->_execute();
  }

  public function searchParent( $qry='', $itemType='', $parentItemType=null, $childItemId=null){
    $this->_connect();
    $url=$this->_config['host'] . "/search_parent?qry=" . urlencode($qry) . "&itemType=$itemType&parentItemType=$parentItemType&childItemId=$childItemId";
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    return $this->_execute();
  }

  public function exec( $qry=''){
    $this->_connect();
    $url=$this->_config['host'] . "/exec?qry=" . urlencode($qry);
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    return $this->_execute();
  }

  public function execJoin( $qry=''){
    $this->_connect();
    $url=$this->_config['host'] . "/exec_join?qry=" . urlencode($qry);
    //$url=$this->_config['host']."/exec_join?qry=".$qry;
    //print $url;
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    return $this->_execute();
  }

  /**
   *
   * @param $qry JSON
   * @return unknown_type
   */
  public function execJoinList( $qry=''){
    $this->_connect();
    //$url=$this->_config['host']."/exec_join?qry=".urlencode($qry);
    $qry=urlencode($qry);
    $url=$this->_config['host'] . "/exec_join_list?qry=$qry&rnd=" . mt_rand(10, 9999999);
    //$url=$this->_config['host']."/quest?goal=$qry&rnd=".mt_rand(10,9999999);
    //print $url."-".$qry1;
    //print $url;
    //curl_setopt($this->_connection, CURLOPT_PORT, 3000);
    //curl_setopt($this->_connection, CURLOPT_POST,1);
    //curl_setopt($this->_connection, CURLOPT_POSTFIELDS, array("goal"=>$qry));
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    return $this->_execute();
  }


  /**
   *
   * print first result

   * @param $type
   * @param $qry
   * @return unknown_type
   */
  public function printObjects( $type, $qry){

    //
    $this->_connect();
    //$url=$this->_config['host']."/exec_join?qry=".urlencode($qry);
    $qry=urlencode($qry);
    $type=urlencode($type);
    $url=$this->_config['host'] . "/print_objects?qry=$qry&type=$type&rnd=" . mt_rand(10, 9999999);

    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();

    return $res;
  }

  /**
   * prints all objects with said parameters

   * @param $type
   * @param $qry
   * @return unknown_type
   */
  public function selectOrderLimit( $type='', $key='id', $search='_all_', $selected='[id,title]',$from=0, $limit=20, $order_by='id', $order_dir='asc'  ){

    //
    $this->_connect();
    //$url=$this->_config['host']."/exec_join?qry=".urlencode($qry);
    //$search=urlencode($search);
    //$type=urlencode($type);
    //selectOrderLimit($type, $key, $search, $selected, $to, $from)

    $search=trim($search);
    $key=trim($key);
    if (!$search){
      $search="_all_";
    }
    if (empty($key)){
      $key="id";
    }

    $selected=urlencode($selected);

    $url=$this->_config['host'] . "/selectOrderLimit?type=$type&key=$key&selected=$selected&search=$search&from=$from&limit=$limit&order_by=$order_by&order_dir=$order_dir&rnd=" . mt_rand(10, 9999999);
    //print "<br>\n".$url."<br>\n";
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();

    return $res;
  }

  public function selectLinkedOrderLimit($path, $parentId, $key='id', $search='_all_', $selected='[id,title]',$from=0, $limit=20, $order_by='id', $order_dir='desc'){
    $this->_connect();
    //$url=$this->_config['host']."/exec_join?qry=".urlencode($qry);
    //$search=urlencode($search);
    //$type=urlencode($type);
    //selectOrderLimit($type, $key, $search, $selected, $to, $from)
    //$url=$this->_config['host'] . "/selectLinked1?parent_type=$parentType&parent_id=$parentId&type=$type&key=$key&selected=$selected&search=$search&from=$from&to=$to&rnd=" . mt_rand(10, 9999999);

    $search=trim($search);
    $key=trim($key);
    if (!$search){
      $search="_all_";
    }
    if (empty($key)){
      $key="id";
    }

    $selected=urlencode($selected);

    $parentId=urlencode($parentId);

    $url=$this->_config['host'] . "/selectLinked?path=$path&parent_id=$parentId&key=$key&selected=$selected&search=$search&from=$from&limit=$limit&order_by=$order_by&order_dir=$order_dir&rnd=" . mt_rand(10, 9999999);
    //print "<br>\n".$url."<br>\n";
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();
    //print $res;

    return $res;
  }


  public function countLinkedObjectsPl($path, $parentId, $key, $search, $selected){
    $this->_connect();
    $search=trim($search);
    $key=trim($key);
    if (!$search){
      $search="_all_";
    }
    if (empty($key)){
      $key="id";
    }

    $path=urlencode($path);
    $selected=urlencode($selected);
    $parentId=urlencode($parentId);
    $url=$this->_config['host'] . "/countLinkedObjects?path=$path&parent_id=$parentId&key=$key&selected=$selected&search=$search&rnd=" . mt_rand(10, 9999999);
    p($url,'countLinkedObjectsPl->url');
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();
    p($res,'countLinkedObjectsPl->res');

    return $res;
  }

  public function getObjectByIdPl($type, $id, $selected){
    $this->_connect();


    $selected=urlencode($selected);
    $id=urlencode($id);
    $url=$this->_config['host'] . "/getObjectById?type=$type&id=$id&selected=$selected&rnd=" . mt_rand(10, 9999999);
    //print "<br>\n".$url."<br>\n";
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();
    //print $res;

    return $res;
  }

  public function getObjectTitleByIdPl($type, $id){
    $this->_connect();
    $url=$this->_config['host'] . "/getObjectTitleById?type=$type&id=$id&rnd=" . mt_rand(10, 9999999);
    //print "<br>\n".$url."<br>\n";
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();
    //print $res;

    return $res;
  }

  public function countObjectsPl($type, $key, $search, $selected){
    $this->_connect();
    $search=trim($search);
    $key=trim($key);
    if (!$search){
      $search="_all_";
    }
    if (empty($key)){
      $key="id";
    }

    $selected=urlencode($selected);
    $url=$this->_config['host'] . "/countObjects?type=$type&key=$key&selected=$selected&search=$search&rnd=" . mt_rand(10, 9999999);
    //print "<br>\n".$url."<br>\n";
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();
    //print $res;

    return $res;
  }

  //print_all_objects

  public function check( $qry=''){
    $this->_connect();
    $url=$this->_config['host'] . "/check?qry=" . urlencode($qry);
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    return $this->_execute();
  }


  public function countObjects($path, $parentId,  $key='id', $search, $selected){
    $selected=$this->cleanSelected($selected);

    if ($parentId>0){
      $parentId=$this->cleanSelected($parentId);
      //print $parentId."<br>";
      p($parentId,'countObjects:parentId');
      $res=$this->countLinkedObjectsPl($path, $parentId, $key, $search, $selected);
    }else{
      $tmpArr=explode(',',preg_replace("@\s+@","",$path));
      $type=$tmpArr[sizeof($tmpArr)-1];
      $type=str_replace("]",'',$type);
      $res=$this->countObjectsPl($type, $key, $search, $selected);
    }
    return $res;
  }


  public function countLinkedPerParent($path, $parentId,  $key='id', $search, $selected){

    $data=array();
    if ($parentId>0){
      $this->_connect();
      $selected=$this->cleanSelected($selected);
      $parentId=$this->cleanSelected($parentId);
      $path=$this->cleanSelected($path);
      //print $parentId."<br>";
      //$res=$this->countLinkedObjectsPl($path, $parentId, $key, $search, $selected);
      $url=$this->_config['host'] . "/countLinkedPerParent?path=$path&parent_id=$parentId&key=$key&selected=$selected&search=$search&rnd=" . mt_rand(10, 9999999);

      curl_setopt($this->_connection, CURLOPT_URL, $url);
      $res=$this->_execute();
      $data=$this->parseKbData($res);


    }
    return $data;
  }

  public function searchObject($types,$where,$selected,$from=0,$limit=5,$orderby='id',$orderdir='asc'){
    $types1=urlencode($types);
    $where1=urlencode($where);
    $selected1=urlencode($selected);

    $this->_connect();

    $url=$this->_config['host'] . "/searchObjectOrderLimit?types=$types1&search_fields=$where1&selected_fields=$selected1&from=$from&limit=$limit&orderby=$orderby&orderdir=$orderdir&rnd=" . mt_rand(10, 9999999);
    //print_r($url)."<br>";
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();
    $dataFields=$this->parseKbData($res);
    return $dataFields;

  }

  public function searchObjectRange($types,$where,$selected,$from_field,$from_val,$to_field,$to_val,$from=0,$limit=5,$orderby='id',$orderdir='asc'){
    $types1=urlencode($types);
    $where1=urlencode($where);
    $selected1=urlencode($selected);
    $from_val1=urlencode($from_val);
    $to_val1=urlencode($to_val);

    $this->_connect();

    $url=$this->_config['host'] . "/searchObjectRange?types=$types1&search_fields=$where1&selected_fields=$selected1&from=$from&limit=$limit&orderby=$orderby&orderdir=$orderdir&from_field=$from_field&from_val=$from_val1&to_field=$to_field&to_val=$to_val1&rnd=" . mt_rand(10, 9999999);
    //print_r($url)."<br>";
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();
    $dataFields=$this->parseKbData($res);
    return $dataFields;

  }

public function searchObjectRange2($types,$where,$strict,$selected,$from_field,$from_val,$to_field,$to_val,$from=0,$limit=5,$orderby='id',$orderdir='asc'){
    $types1=urlencode($types);
    $where1=urlencode($where);
    $strict1=urlencode($strict);
    $selected1=urlencode($selected);
    $from_val1=urlencode($from_val);
    $to_val1=urlencode($to_val);

    $this->_connect();

    $url=$this->_config['host'] . "/searchObjectRange2?types=$types1&search_fields=$where1&strict_search=$strict1&selected_fields=$selected1&from=$from&limit=$limit&orderby=$orderby&orderdir=$orderdir&from_field=$from_field&from_val=$from_val1&to_field=$to_field&to_val=$to_val1&rnd=" . mt_rand(10, 9999999);
    //print_r($url)."<br>";
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();
    $dataFields=$this->parseKbData($res);
    return $dataFields;

  }


 public function countSearchObject($types,$where,$selected){
    $types1=urlencode($types);
    $where1=urlencode($where);
    $selected1=urlencode($selected);

    $this->_connect();

    $url=$this->_config['host'] . "/countSearchObject?types=$types1&search_fields=$where1&selected_fields=$selected1&rnd=" . mt_rand(10, 9999999);

    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();
    $dataFields=$this->parseKbData($res);
    return $dataFields;
 }

 public function countSearchObjectRange($types,$where,$selected,$from_field,$from_val,$to_field,$to_val){
    $types1=urlencode($types);
    $where1=urlencode($where);
    $selected1=urlencode($selected);
    $from_val1=urlencode($from_val);
    $to_val1=urlencode($to_val);

    $this->_connect();

    $url=$this->_config['host'] . "/countSearchObjectRange?types=$types1&search_fields=$where1&selected_fields=$selected1&from_field=$from_field1&from_val=$from_val1&to_field=$to_field&to_val=$to_val1&rnd=" . mt_rand(10, 9999999);

    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();
    $dataFields=$this->parseKbData($res);
    return $dataFields;
 }

public function countSearchObjectRange2($types,$where,$strict,$selected,$from_field,$from_val,$to_field,$to_val){

	//print "111";
	//die();
	//no need to do url encoding in case of post!
//    $types1=urlencode($types);
//    $where1=urlencode($where);
//    $strict1=urlencode($strict);
//    $selected1=urlencode($selected);
//    $from_val1=urlencode($from_val);
//    $to_val1=urlencode($to_val);
    
    

    $postFields=array(
      'types'=>$types,
      'search_fields'=>$where,
      'strict_search'=>$strict,
      'selected_fields'=>$selected,
      'from_field'=>$from_field,
      'from_val'=>$from_val,
      'to_field'=>$to_field,
      'to_val'=>$to_val,
      'rnd'=>mt_rand(10, 9999999)
    );
    
    $this->_connect();
    
    curl_setopt($this->_connection, CURLOPT_POST, 1);
    curl_setopt($this->_connection, CURLOPT_POSTFIELDS,$postFields);
    curl_setopt($this->_connection, CURLOPT_URL, $this->_config['host'] . "/countSearchObjectRange2");
    

    $res=$this->_execute();
    if (curl_error($this->_connection)) $output = "\n". curl_error($this->_connection);
    //print $res;
    fb($res,hwGetPlace(__FILE__,__LINE__).":res");
    $dataArr=$this->parseKbData($res);


    //print "In function<br>";
    //print_r($dataArr);
    //debug_zval_dump($dataArr);

    //$sess=Zend_Registry::get('nsHw');
    //$sess->countArr=$dataArr;
    //print_r($sess->countArr);

    //print_r($dataArr);
    //die();
    //$dataArr=array(array('apa'=>5),array('tap'=>5),array('arch'=>6),array('paint'=>7));

    if ($dataArr)
    foreach($dataArr as $row){
      $key=key($row);
      $dataArr1[$key]=$row[$key];
    }
    //Zend_Debug::dump($dataArr);
    //print_r($dataArr1);

    return $dataArr;
 }


 public function setData($arr){
   $this->_dataArr=$arr;
 }

 public function getData(){
   return $this->_dataArr;
 }

 public function testArray(){
   $data=$this->getData();
   return $data;
 }



  public function getObjectById($type, $id, $selected=''){
    $selected=$this->cleanSelected($selected);

    fb($selected,'selected');
    $res=$this->getObjectByIdPl($type,$id,$selected);

    fb($res,'res');
    //$mail=new Hw_Mail();
    //$mail->debugSend('getObject',$selected."<br>".$res);
    $data=$this->parseKbData($res);
    
    fb($data,'data');
    
    return $data;

  }

  public function getObjectTitleById($type, $id){
    $res=$this->getObjectTitleByIdPl($type,$id,$selected);

    //$mail=new Hw_Mail();
    //$mail->debugSend('getObject',$selected."<br>".$res);
    //$data=$this->parseKbData($res);
    return $res;

  }

  /**
   *
   *  memberchk(mtId=MtId, Search),
   memberchk(type=Type, Search),
   memberchk(from=From, Search),
   memberchk(limit=Limit, Search),
   memberchk(key=PropKey, Search),
   memberchk(search=PropSearch, Search),
   memberchk(selected=SelectedKeys, Search),
   * @param $mtId
   * @param $type
   * @param $selected
   * @return unknown_type
   */


  public function findMtgrandsons($mtId, $type, $key, $search, $selected, $from=0, $limit=20, $order_by='id', $order_dir='asc'){
    $selected=$this->cleanSelected($selected);

    $selected=urlencode($selected);
    $this->_connect();

    $url=$this->_config['host'] . "/findMtgrandsons?mtId=$mtId&type=$type&selected=$selected&rnd=" . mt_rand(10, 9999999);
    //print "<br>\n".$url."<br>\n";
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();
    $dataFields=$this->parseKbData($res);
    return $dataFields;
  }

  public function findParent($childType,$childId,$parentType,$selected){
    $selected=$this->cleanSelected($selected);

    $selected=urlencode($selected);
    $this->_connect();

    $url=$this->_config['host'] . "/findParent?childType=$childType&childId=$childId&parentType=$parentType&selected=$selected&rnd=" . mt_rand(10, 9999999);
    //print "<br>\n".$url."<br>\n";
    curl_setopt($this->_connection, CURLOPT_URL, $url);
    $res=$this->_execute();
    $dataFields=$this->parseKbData($res);
    return $dataFields;

  }

  /**
   *
   * @param $path
   * @param $parentId array() - list of parent ids [1,2,45,65], if one element then [2]
   * @param $key
   * @param $search
   * @param $selected
   * @param $from
   * @param $limit
   * @param $order_by
   * @param $order_dir
   * @return unknown_type
   */
  public function selectFromKb($path, $parentId, $key, $search, $selected, $from=0, $limit=20, $order_by='id', $order_dir='asc'){


    //$termStr="findObject('$type', ObjNumber, '$text')";
    $selected=$this->cleanSelected($selected);




    $tm1=microtime(true);
    if ($parentId>0){
      $parentId=$this->cleanSelected($parentId);
      $res=$this->selectLinkedOrderLimit($path, $parentId, $key, $search, $selected, $from, $limit, $order_by,$order_dir);
    }else{
      $tmpArr=explode(',',preg_replace("@\s+@","",$path));
      $type=$tmpArr[sizeof($tmpArr)-1];
      $type=str_replace("]",'',$type);
      //print $type;
      $res=$this->selectOrderLimit($type, $key, $search, $selected, $from, $limit, $order_by, $order_dir);
    }

    $tm2=microtime(true);
    $dT1=$tm2 - $tm1;
    //print "Query took:" . $dT1 . "<br>";

    //print $res;
    //die();
    $dataFields=$this->parseKbData($res);
    //we get to resulting arrays: data and dataFields. data is array of pairs [0]='id', [1]=>123
    // and dataFields is in form of key=>value. Values for the same keys are merged.
    //print_r($dataFields);

    //$res=$this->execJoinList($termStr);


    $tm3=microtime(true);
    $dT2=$tm3 - $tm2;
    //print "parsing took:" . $dT2 . "<br>";
    return $dataFields;
  }


  public function cleanSelected($selected){

    $selected1='';
    $selArr=array();
    $selectArr=explode(',',$selected);
    foreach($selectArr as $i=>$selVal){
      $selVal=trim($selVal);
      if (preg_match('/\\D+/',$selVal)){
        //if (is_string($selVal)){
        $selVal="'$selVal'";
      }
      if ($selVal){
        $selArr[]=$selVal;
      }
    }
    if ($selArr){
      $selected1=implode(',',$selArr);
    }

    //wrap into [  ] if not already wrapped
    if (strpos($selected1,'[')!==0){
      $selected1="[".$selected1;
    }

    if (strpos($selected1,']')!==(strlen($selected1)-1)){
      $selected1=$selected1."]";
    }

    return $selected1;

  }

  public function parseKbData($res){
    //    if ($_SERVER['REMOTE_ADDR']=='85.130.239.124'){
    //      $dbg=1;
    //    }
    $allArr=explode('@#*@#*', $res); //print $res;
    //print_r($allArr);
    require_once("Hw/Convert/Str.php");
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
          $parsedData[1]=str_replace("\\r\\n","<br>",$parsedData[1]);
          //          if ($parsedData[0]=='text_title_en' && $dbg){
          //            requireOnce("Hw_Mail");
          //            $mail=new Hw_Mail();
          //            //$mail->addTo("info@pelezol.co.il","Info");
          //            $mail->debugSend("text_title_en",$parsedData[1]);
          //          }
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

    return $dataFields;


  }


  public function queryKb( $qry, $frm, $fields, $regex=false){
    /*
     * qry is compound term to query prolog database
     * frm is format to scan resulting strings from prolog. PLEASE TAKE CARE of spaces, commas and apostrophs
     * outFields - the fields which we want to extract from results
     $qry="graph_to_skin(Gid,Sid),graph_title(Gid,Title),graph_author(Gid, Author),skin(Sid,SkinName),search_atom(SkinName,'clo')";
     $frm="graph_to_skin(%d, %d), graph_title(%d, '%[^']'), graph_author(%d, '%[^']'), skin(%d, '%[^']'), search_atom('%[^']', 'clo')";
     $outFields=array(
     0=>'Gid',
     1=>'Sid',
     3=>'Title',
     4=>'Author',
     7=>'SkinName'
     );
     */
    //print "<pre>";
    //print_r($fields);
    //print "</pre>";
    $dataArr=array();
    $res=$this->execJoinList($qry);
    //print $res."<br>";
    //$mail=new Hw_Mail();
    //$mail->debugSend("list", $res);
    //fb($res);


    $arr=preg_split("@###\n@i", $res);
    //print "<pre>";
    //print_r($arr);
    //print "</pre>";
    //$mail=new Hw_Mail();
    //$mail->debugSend("split",print_r($arr,true));
    if(!$regex){
      $frm=str_replace(" ", "&nbsp;", $frm);
    }
    $i=0;
    foreach($arr as $str){
      $i++;
      $str=trim($str);
      if(empty($str)){
        continue;
      }
      if(!$regex){
        $str=str_replace(" ", "&nbsp;", $str);
        $resRow=sscanf($str, $frm);
      }else{
        preg_match_all('@' . $frm . '@', $str, $resRow);
        /*				if ($i == 1) {
         $mail = new Hw_Mail ( );
         $mail->debugSend ( "scan", print_r ( $resRow, true ) );
         }*/
      }
      //      if($i == 1){
      //        $mail=new Hw_Mail();
      //        $mail->debugSend("scan/regex=$regex", $str.$frm.print_r($resRow, true));
      //      }
      //print "<pre>";
      //print_r($resRow);
      //print "</pre>";
      //$resArr[]=$resRow;
      $data=array();
      foreach($resRow as $n=>$resVal){
        if($fields[$n]){
          $data[$fields[$n]]=$resVal;
        }
      }
      $dataArr[]=$data;

    }
    return $dataArr;
  }

}

?>