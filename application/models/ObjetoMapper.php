<?php

require_once("c:\proyectos\desarrollo\hw\application\models\DbTable\Objeto.php");

class Application_Model_ObjetoMapper
{


  protected $_dbTable;

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

          $this->setDbTable('Application_Model_DbTable_Objeto');

      }

      return $this->_dbTable;

  }



  public function save(Application_Model_Objeto $objeto)

  {

      $data = array(

          'tipo'   => $objeto->getTipo(),

          'subtipo' => $objeto->getSubtipo(),
		  
          'codigo' => $objeto->getCodigo(),
      	  
          'user' => $objeto->getUser(),

          'created' => date('Y-m-d H:i:s'),

      );



      if (null === ($id = $objeto->getId())) {

          unset($data['id']);
          
         $id= $this->getDbTable()->insert($data);
         
         return $id;
      

      } else {

          $this->getDbTable()->update($data, array('id = ?' => $id));

      }

  }



  public function find($id, Application_Model_Objeto $objeto)

  {

      $result = $this->getDbTable()->find($id);

      if (0 == count($result)) {

          return;

      }

      $row = $result->current();

      $objeto->setId($row->id)

                ->setTipo($row->tipo)

                ->setSubtipo($row->subtipo)

                ->setCodigo($row->codigo)
                
                ->setUser($row->user)

                ->setCreated($row->created);

  }

  public function findByCodigo(Application_Model_Objeto $objeto)
  {

      $select = $this->getDbTable()->select();
      $select->where("tipo = '{$objeto->getTipo()}' and subtipo= '{$objeto->getSubtipo()}' and codigo = '{$objeto->getCodigo()}'");
      $resultSet = $this->getDbTable()->fetchAll($select);

      if (0 == count($resultSet[0])) {

          return;

      }

      $row = $resultSet->current();

      $objeto->setId($row->id)

                ->setTipo($row->tipo)

                ->setSubtipo($row->subtipo)

                ->setCodigo($row->codigo)
                
                ->setUser($row->user)

                ->setCreated($row->created);

  }


  public function fetchAll()

  {

      $resultSet = $this->getDbTable()->fetchAll();

      $entries   = array();

      foreach ($resultSet as $row) {

          $entry = new Application_Model_Objeto();

          $entry->setId($row->id)

                ->setTipo($row->tipo)

                ->setSubtipo($row->subtipo)

                ->setCodigo($row->codigo)
                
                ->setUser($row->user)
                
                ->setCreated($row->created);

          $entries[] = $entry;

      }

      return $entries;

  }

}

