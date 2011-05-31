<?php
class Singleton{

}
class Hw_Acl{

  static private $userData;
  static private $instance;

  protected function __construct() {
    $self->userData = Zend_Registry::get ( 'nsHw' )->authUserData;
  }

  final private function __clone() {

  }

  static function getInstance() {
    if(!self::$instance)
    self::$instance= new Tp_Acl();
    return self::$instance;
  }

  static function getAdmins($str=true){
    $user=new User();
    $userArr=$user->fetchAll("role='admin'")->toArray();
    if($userArr){
      foreach($userArr as $row){
        $admins[]=$row['id'];
      }
    }
    if ($str){
      return implode(',',$admins);
    }else{
      return $admins;
    }
  }

  /**
   * @param $tableName - name of class of DB Table
   * @param $ownerFieldName - name of field : creator_id, owner_id, ...
   * @param $id - value of item id
   * @return boolean
   */
  static function checkItemPerm($tableName,$ownerFieldName,$id){
    if ($self->userData->role!='admin'){
      $tab=new $tableName();
      $res=$tab->fetchRow("$ownerFieldName='$id'");
      if ($res){
        return true;
      } else{
        return false;
      };
    }

  }

}

?>