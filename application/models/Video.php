<?php

class Video extends HwDbTable
{
    protected $_name = 'video';
    protected $_primary  = 'video_id'; 
    /**
     * Fetch the latest $count places
     *
     * @param int $count
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchLatest($count = 5)
    {
        return $this->fetchAll(null,'video_id DESC', $count);
    }
    
//    public function countSkinDocs($skin_id){
//    	return parent::countSkinDocs($skin_id,$this->_name);
//    }
//    
//    
//    public function skinDocs($skin_id){
//      return parent::skinDocs($skin_id,$this->_name);
//    }

}
