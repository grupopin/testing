<?php

class News extends Zend_Db_Table
{
    protected $_name = 'news';
    protected $_primary  = 'news_id'; 
    /**
     * Fetch the latest $count places
     *
     * @param int $count
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchLatest($count = 5)
    {
        return $this->fetchAll(null,'news_id DESC', $count);
    }
}
