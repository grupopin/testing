<?php

class Audio extends HwDbTable 
{
    protected $_name = 'audio';
    protected $_primary  = 'audio_id'; 
    /**
     * Fetch the latest $count places
     *
     * @param int $count
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchLatest($count = 5)
    {
        return $this->fetchAll(null,'audio_id DESC', $count);
    }
    
//    public function countSkinDocs($skin_id){
//      return parent::countSkinDocs($skin_id,$this->_name);
//    }
//    
//    public function skinDocs($skin_id){
//    	return parent::skinDocs($skin_id,$this->_name);
//    }
}
