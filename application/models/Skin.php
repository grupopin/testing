<?php

class Skin extends Zend_Db_Table
{
    protected $_name = 'skin';
    protected $_primary  = 'skin_id';
    protected $skin_id;
    protected $skin_name; 
    /**
     * Fetch the latest $count places
     *
     * @param int $count
     * @return Zend_Db_Table_Rowset_Abstract
     */
    
    public function getSkinName($skin_id)
    {
        $arr=$this->find($skin_id)->toArray();
        return $arr[0]['skin_name'];
       
    }
}
