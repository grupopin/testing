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

require_once 'Pl/Adapter.php';
/**
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Hw_KbPaginator implements Zend_Paginator_Adapter_Interface
{

  /**
   * Total item count
   *
   * @var integer
   */
  const ROW_COUNT_COLUMN = 'zend_paginator_row_count';
  protected $_rowCount = null;
  public $_kBase,$path,$parentId,$searchFields,$searchString,$selectedFields,$order_by,$order_dir;
  public $strictSearch,$from_field,$from_val,$to_field,$to_val;
  public $dataFunction="selectFromKb",$counterFunction="countObjects";
  //for searchObject
  public $typeList,$whereList,$selectList;


  public function __construct(){
    //$this->_kBase=new Pl_Adapter ( Zend_Registry::get ( 'config' )->pl->params );
    if (!$this->_kBase){
      $this->_kBase=Zend_Registry::get('kBase');
    }
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
   if ($this->dataFunction=='searchObject'){
     $itemArr=$this->_kBase->searchObject($this->typeList,$this->whereList,$this->selectList,$offset,$itemCountPerPage,$this->order_by,$this->order_dir);
   }elseif($this->dataFunction=='searchObjectRange2'){
     
     $itemArr=$this->_kBase->searchObjectRange2($this->typeList,$this->whereList,$this->strictSearch,$this->selectList,$this->from_field,$this->from_val,$this->to_field,$this->to_val,$offset,$itemCountPerPage,$this->order_by,$this->order_dir);
     
   }else{
    $itemArr=$this->_kBase->{$this->dataFunction}($this->path,$this->parentId,$this->searchFields,$this->searchString,$this->selectedFields,$offset,$itemCountPerPage,$this->order_by,$this->order_dir);
   }
    //print "getItems<br>";
    return $itemArr;


  }

  /**
   * Returns the total number of rows in the result set.
   *
   * @return integer
   */
  public function count(){
    if ($this->_rowCount === null) {
      //print_r($this->pl_conn->_params);


      if ($this->counterFunction=="countSearchObject"){

        $countArr=$this->_kBase->countSearchObject($this->typeList,$this->whereList,$this->selectList);
        $key=key($countArr[0]);


        $rowCount=$countArr[0][$key];

        
        //print $rowCount;

      }elseif($this->counterFunction=="countSearchObjectRange2"){


        //print "countSearchObjectRange2:<br>";
        //$mem1=memory_get_usage( true);
        //print $mem1."<br>";
        $countArr=$this->_kBase->countSearchObjectRange2($this->typeList,$this->whereList,$this->strictSearch,$this->selectList,$this->from_field,$this->from_val,$this->to_field,$this->to_val);

        //$dbgArr=debug_backtrace(true);
        fb($countArr,hwGetPlace(__FILE__,__LINE__).'count()');
        
        $trace=debug_backtrace();
		$debugStr=hwGetBackTrace($trace);
        fb($debugStr,hwGetPlace(__FILE__,__LINE__).'count()-trace');
        //die();
        $key=@key($countArr[0]);
        //print $key;

        $rowCount=$countArr[0][$key];
      }else{
        $rowCount=$this->_kBase->{$this->counterFunction}($this->path,$this->parentId,$this->searchFields,$this->searchString,$this->selectedFields);
      }


      //print $rowCount;
      $this->setRowCount($rowCount);
    }

    return $this->_rowCount;
  }
}
