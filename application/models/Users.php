<?php

class Users extends Zend_Db_Table
{
    protected $_name = 'user';
    protected $_primary  = 'user_id'; 
    /**
     * Fetch the latest $count places
     *
     * @param int $count
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchLatest($count = 2)
    {
        return $this->fetchAll(null,'user_id DESC', $count);
    }
}
