<?php

class KbImage_Mapper 

{
	protected $_dbTable;
	
public function __construct(array $options = null)
    {
            $config = array(
			    'host'     => 'localhost',
			    'username' => 'hwarch',
			    'password' => 'hwarch',
			    'dbname'   => 'hwarch_db',
			);

			$db = Zend_Db::factory('PDO_MYSQL', $config);
			Zend_DB_Table_Abstract::setDefaultAdapter($db); 
    }
	
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
 
			
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('KbImage');
        }
        return $this->_dbTable;
    }
    
    public function save(Image  $im)
    {
        $data = array(
            'id'   => $im->getId(),
            'object_type' => $im->getObject_type(),
            'image_name' => $im->getImage_name(),
        	'frontend_visible' => $im->getFrontend_visible(),
        	'caption' => $im->getCaption()
        );
        $this->getDbTable()->update($data, array('name = ?' => $im->getName()));
        
    }
    
     public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
        	//var_dump($row);
            $entry = new Image();
            $entry->setId($row->id);
            $entry->setObject_type($row->object_type);
            $entry->setImage_name($row->image_name);
            $entry->setFrontend_visible($row->frontend_visible);
            $entry->setCaption($row->caption);
            $entries[] = $entry;
            //var_dump($entry);
        }
        return $entries;
    }
    
    public function find($id, Image $im){
    	$result = $this->getDbTable()->find($id);
    	if (0 == count($result)) {
    		return;
    	}
    	$row = $result->current();
        $entry = new Image();
        $entry->setId($row->id);
        $entry->setObject_type($row->object_type);
        $entry->setImage_name($row->image_name);
        $entry->setFrontend_visible($row->frontend_visible);
        $entry->setCaption($row->caption);    	
    } 
    

    
}
