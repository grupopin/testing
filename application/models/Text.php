<?php

class Text extends Zend_Db_Table
{
    protected $_name = 'text';
    protected $_primary  = 'text_id';
    /**
     * Fetch the latest $count places
     *
     * @param int $count
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchLatest($count = 5)
    {
        return $this->fetchAll(null,'text_id DESC', $count);
    }

//    public function countSkinDocs($skin_id){
//      return parent::countSkinDocs($skin_id,$this->_name);
//    }
//
//    public function skinDocs($skin_id){
//      return parent::skinDocs($skin_id,$this->_name);
//    }
//
//   public function skinWorks($skin_id,$count,$offset=0){
//    return parent::skinWorks($skin_id,$count,$offset,$this->_name);
//   }
//
//   public function countItemWorks($item_id){
//    return parent::countItemWorks($item_id,$this->_name);
//   }
}
