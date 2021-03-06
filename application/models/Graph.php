<?php

class Graph extends HwDbTable
{
    protected $_name = 'graph';
    protected $_primary  = 'graph_id'; 
    /**
     * Fetch the latest $count places
     *
     * @param int $count
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchLatest($count = 5)
    {
        return $this->fetchAll(null,'graph_id DESC', $count);
    }
    
//    public function countSkinWorks($skin_id){
//        return parent::countSkinWorks($skin_id,$this->_name);
//    }
//    
// public function skinWorks($skin_id,$count,$offset){
//    return parent::skinWorks($skin_id,$count,$offset,$this->_name);
//   }
}
