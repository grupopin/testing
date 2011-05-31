<?php
require_once('HwDbTable.php');
class KbTrans2 extends HwDbTable
{
    protected $_name = 'kb_trans2';
    protected $_primary  = 'term_id';
   /**
     * Fetch the all terms and sort them by term_body+add terms dynamic declarations
     *
     * @param int $count
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchAllTerms($where='',$count = null){
      $wherStr='';
       if ($where){
         $wherStr=$where.' && ';
       }
       //print $wherStr.' term_head!="" && term_head IS NOT NULL && term_active=1';
       //die();
        return $this->fetchAll($wherStr.' term_head!="" && term_head IS NOT NULL && term_active=1',array('term_head','term_id'), $count);
    }

    public function fetchTermHeads($where=''){
      //$select=new Zend_Db_Select($this->getAdapter());
       $wherStr='';
       if ($where){
         $wherStr=$where.' && ';
       }

      $select=$this->select();
      //$select->from($this->_name);
      $select->group(array("term_head", "term_arity"))->where($wherStr.' term_head!="" && term_head IS NOT NULL && term_active=1')->order(array("term_head", "term_arity"));
      $arr=$this->fetchAll($select);
      return $arr;
    }




}