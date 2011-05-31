<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: DbSelect.php 14137 2009-02-21 23:25:39Z norm2782 $
 */

/**
 * @see Zend_Paginator_Adapter_Interface
 */
require_once 'Zend/Paginator/Adapter/Interface.php';

/**
 * @see Zend_Db
 */
require_once 'Zend/Db.php';

/**
 * @see Zend_Db_Select
 */
require_once 'Zend/Db/Select.php';

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Hw_PageKb implements Zend_Paginator_Adapter_Interface
{

  /**
   * Total item count
   *
   * @var integer
   */
  protected $_rowCount = null;
  /**
   * pl_conn - connector to Knowledge Base (Prolog) Query object
   * @var unknown_type
   */
  protected $_pl_conn;
  /**
   * connector to Prolog Adapter object
   * @var unknown_type
   */
  protected $_plAdapter;
  /**
   * Constructor.
   *
   * @param Zend_Db_Select $select The select query
   */
  public function __construct($method,$params){
    $this->pl_conn = new Pl_Query($method,$params);
    $this->_plAdapter=$this->pl_conn->_plAdapter;
  }

  /**
   * Sets the total row count, either directly or through a supplied
   * query.  Without setting this, {@link getPages()} selects the count
   * as a subquery (SELECT COUNT ... FROM (SELECT ...)).  While this
   * yields an accurate count even with queries containing clauses like
   * LIMIT, it can be slow in some circumstances.  For example, in MySQL,
   * subqueries are generally slow when using the InnoDB storage engine.
   * Users are therefore encouraged to profile their queries to find
   * the solution that best meets their needs.
   *
   * @param  Zend_Db_Select|integer $totalRowCount Total row count integer
   *                                               or query
   * @return Zend_Paginator_Adapter_DbSelect $this
   * @throws Zend_Paginator_Exception
   */
  public function setRowCount($rowCount){
    if ((int)$rowCount>=0){
      $this->_rowCount = $rowCount;
    } else {
      /**
       * @see Zend_Paginator_Exception
       */
      require_once 'Zend/Paginator/Exception.php';

      throw new Zend_Paginator_Exception('Invalid row count');
    }

    return $this;
  }

  /**
   * Returns an array of items for a page.
   *
   * @param  integer $offset Page offset
   * @param  integer $itemCountPerPage Number of items per page
   * @return array
   */
  public function getItems($offset, $itemCountPerPage){
   //print "$offset, $itemCountPerPage";
    $this->pl_conn->limit($itemCountPerPage, $offset);
    //print "getItems<br>";
    return $this->pl_conn->runQuery()->getResults();


  }

  /**
   * Returns the total number of rows in the result set.
   *
   * @return integer
   */
  public function count(){
    if ($this->_rowCount === null) {
      //print_r($this->pl_conn->_params);

      $params=$this->pl_conn->getParams();
      $rowCount=$this->_plAdapter->countObjects($params['parentType'], $params['parentId'],
                                                $params['type'], $params['key'],
                                                $params['search'], $params['selected']);

      //print $rowCount;
      $this->setRowCount($rowCount);
    }

    return $this->_rowCount;
  }
}
