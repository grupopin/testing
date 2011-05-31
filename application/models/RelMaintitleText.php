<?php

class RelMaintitleText extends Zend_Db_Table
{
    protected $_name = 'rel_maintitle_text';
    protected $_primary  = 'rel_maintitle_text_id';

   
    public function getHL($skin_id,$count, $offset)
    {
    	$select=$this->select()->setIntegrityCheck(false);
    
    	$select->from('rel_maintitle_text')
    	->where('maintitle.maintitle_skin_id= ?',$skin_id)
    	->joinLeft('maintitle','maintitle.maintitle_id=rel_maintitle_text.maintitle_id')
    	->joinLeft('skin','maintitle.maintitle_skin_id=skin.skin_id')
    	->joinLeft('text','text.text_id=rel_maintitle_text.text_id')
    	->order(array('maintitle.maintitle_sort_id','rel_maintitle_text.rel_priority'))
    	->limit($count,$offset);
    	//print $select."<br>";
    	
        $arr=$this->fetchAll($select)->toArray();
        return $arr;
       
    }
    
    public function countHL($skin_id){
    	     $select=$this->select()->setIntegrityCheck(false);
    
      $select->from('rel_maintitle_text', "COUNT(*) as cnt")
      ->where('maintitle.maintitle_skin_id= ?',$skin_id)
      ->joinLeft('maintitle','maintitle.maintitle_id=rel_maintitle_text.maintitle_id', array())
      ->joinLeft('skin','maintitle.maintitle_skin_id=skin.skin_id',array());
      
      //print $select."<br>";
      
        $arr=$this->fetchRow($select)->toArray();
        return $arr['cnt'];
    }
}
