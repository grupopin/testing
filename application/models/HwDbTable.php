<?php
//require_once('Zend_Db.php');
require_once('Zend/Db/Table.php');
class HwDbTable extends Zend_Db_Table
{

  public function update($bind,$where){
    if (trim($where)==''){
      throw new Zend_Exception("In HwDbTable->update() you need to set \$where");

    }
    parent::update($bind,$where);
  }

  public function delete($bind,$where){
    if (trim($where)==''){
      throw new Zend_Exception("In HwDbTable->delete() you need to set \$where");

    }
    parent::delete($bind,$where);
  }



	public function countSkinWorks($skin_id,$workName=null,$whereStr=''){
		$counter=0;
		if (!$workName){
			$workName=$this->_name;
		}
	if (is_array($skin_id)){
    $skinStr=implode(',',$skin_id);
   }elseif(is_string($skin_id)){
    $skinStr=trim($skin_id);
   }elseif(is_int($skin_id)){
    $skinStr=$skin_id;
   }

		//SELECT COUNT(DISTINCT ra.apa_id) FROM maintitle as m LEFT JOIN rel_maintitle_text as r ON m.maintitle_id=r.maintitle_id
		//INNER JOIN rel_apa_text as ra ON r.text_id=ra.text_id ;
		foreach(array('text','video','audio') as $tab){
			$select=$this->select()->setIntegrityCheck(false);
			$select
			->from('maintitle',array("COUNT(DISTINCT rel_{$workName}_{$tab}.{$workName}_id) as {$workName}_count"),$this->_schema)
			->joinLeft("rel_maintitle_{$tab}","maintitle.maintitle_id=rel_maintitle_{$tab}.maintitle_id",array())
			->joinInner("rel_{$workName}_{$tab}","rel_maintitle_{$tab}.{$tab}_id=rel_{$workName}_{$tab}.{$tab}_id",array())
			->joinInner($workName, "rel_{$workName}_{$tab}.{$workName}_id={$workName}.{$workName}_id",array())
			->where('maintitle.maintitle_skin_id IN ( ? )',$skinStr);
			if ($whereStr){
				$select->where($whereStr);
			}
			//print $select."<br>";
			//die();//$arr=$this->fetchAll($select)->toArray();
			//$counter+=$arr[0]["{$workName}_count"];
			$selArr[]=$select->__toString();
		}
		$sel=implode(" UNION ",$selArr);
		//print $sel;
		//die();
		$stm=$this->_db->query($sel);
		//print_r($stm);
		$arr=$stm->fetchAll();
		foreach($arr as $row){
			$counter+=$row[$workName.'_count'];
		}

		return $counter;
	}

	public function countItemWorks($item_id,$workName=null){
		if (!$workName){
			$workName=$this->_name;
		}
		//print $workName."<br>";
		//$counter=0;
		//SELECT COUNT(DISTINCT ra.apa_id) FROM maintitle as m LEFT JOIN rel_maintitle_text as r ON m.maintitle_id=r.maintitle_id
		//INNER JOIN rel_apa_text as ra ON r.text_id=ra.text_id ;
		$tables=Zend_Registry::get('tables');
		//print_r($tables);
		$dbTabs=$this->_db->listTables();
		//print_r($dbTabs);
		foreach($tables as $tab){
			$arr[$tab]=0;
			$relTab="rel_{$tab}_{$workName}";
			if (!in_array($relTab,$dbTabs)){
				$relTab="rel_{$workName}_{$tab}";
			}
			if (!in_array($relTab,$dbTabs)){
				continue;
			}
			$select=$this->select()->setIntegrityCheck(false);
			$select->from($relTab,array("count(DISTINCT {$tab}.{$tab}_id) as cnt"))
			->joinInner($tab,"$relTab.{$tab}_id={$tab}.{$tab}_id",array())
			->where("$relTab.{$workName}_id= ?",$item_id);
			//print $select."<br>";
			$row=$this->fetchRow($select);
			$arr[$tab]=$row->cnt;
		}

		return $arr;
	}


	public function itemWorks($item_id,$count=5,$offset=0,$workName=null,$linkConnection=1){
		if (!$workName){
			$workName=$this->_name;
		}
		//print $workName;
		//$counter=0;
		//SELECT COUNT(DISTINCT ra.apa_id) FROM maintitle as m LEFT JOIN rel_maintitle_text as r ON m.maintitle_id=r.maintitle_id
		//INNER JOIN rel_apa_text as ra ON r.text_id=ra.text_id ;
		$tables=Zend_Registry::get('tables');
		$dbTabs=$this->_db->listTables();
		//   print_r($dbTabs);
		$tab=$this->_name;
		//print $tab;
		//die();

		$relTab="rel_{$tab}_{$workName}";
		if (!in_array($relTab,$dbTabs)){
			$relTab="rel_{$workName}_{$tab}";
		}
		if (!in_array($relTab,$dbTabs)){
			return false;
		}
		//die($relTab);
		if ($linkConnection==1){
		$select=$this->select()->setIntegrityCheck(false)
		->distinct()
		->from($relTab)
		->joinInner($tab,"$relTab.{$tab}_id={$tab}.{$tab}_id")
		->where("$relTab.{$tab}_id= ?",$item_id)
		->limit($count,$offset);
		}else{
			$select=$this->select()->setIntegrityCheck(false)
    ->distinct()
    ->from($relTab)
    ->joinInner($tab,"$relTab.{$tab}_id={$tab}.{$tab}_id")
    ->where("$relTab.{$workName}_id= ?",$item_id)
    ->limit($count,$offset);

		}
		//$selArr[]=$select->__toString();
		//print "itemWorks:".$select;


		//$sel=implode(" UNION ",$selArr)." LIMIT $count OFFSET $offset";
		//$stm=$this->_db->query($sel);
		$arr=$this->fetchAll($select)->toArray();
		return $arr;
	}

	public function countOneItemWorks($item_id,$workName=null){
		if (!$workName){
			$workName=$this->_name;
		}
		//$counter=0;
		//SELECT COUNT(DISTINCT ra.apa_id) FROM maintitle as m LEFT JOIN rel_maintitle_text as r ON m.maintitle_id=r.maintitle_id
		//INNER JOIN rel_apa_text as ra ON r.text_id=ra.text_id ;
		//$tables=Zend_Registry::get('tables');
		$dbTabs=$this->_db->listTables();
		//print_r($dbTabs);
		//die("test");
		$tab=$this->_name;
		//print $tab;
		//die();

		$relTab="rel_{$tab}_{$workName}";
		if (!in_array($relTab,$dbTabs)){
			$relTab="rel_{$workName}_{$tab}";
		}
		if (!in_array($relTab,$dbTabs)){
			return false;
		}
		//die($relTab);
		$select=$this->select()->setIntegrityCheck(false)
		->from($relTab,array("COUNT(DISTINCT {$workName}_id) as cnt"))
		->joinInner($tab,"$relTab.{$tab}_id={$tab}.{$tab}_id",array())
		->where("$relTab.{$tab}_id= ?",$item_id);
		//$selArr[]=$select->__toString();
		//print $select;
		//$sel=implode(" UNION ",$selArr)." LIMIT $count OFFSET $offset";
		//$stm=$this->_db->query($sel);
		$arr=$this->fetchRow($select);
		$cnt=$arr->cnt;
		//print "cnt=$cnt<br>";
		return $cnt;
	}

	public function skinWorks($skin_id,$count=0,$offset=0,$workName=null,$fields=array('*'),$whereStr=''){

		$tables=Zend_Registry::get('tables');
    $dbTabs=$this->_db->listTables();

	 if (!$workName){
	 	$workName=$this->_name;
	 }
	 if (is_array($skin_id)){
	 	$skinStr=implode(',',$skin_id);
	 }elseif(is_string($skin_id)){
	 	$skinStr=trim($skin_id);
	 }elseif(is_int($skin_id)){
	 	$skinStr=$skin_id;
	 }
		$arr=array();
		foreach(array('text','video','audio') as $tab){

		$relTab="rel_{$tab}_{$workName}";
    if (!in_array($relTab,$dbTabs)){
      $relTab="rel_{$workName}_{$tab}";
    }
    if (!in_array($relTab,$dbTabs)){
      continue;
    }
			$select=$this->select()->setIntegrityCheck(false);
			$select->distinct(true);

			$select
			->from('maintitle',array())
			->joinLeft("rel_maintitle_{$tab}","maintitle.maintitle_id=rel_maintitle_{$tab}.maintitle_id",array())
			->joinInner("$relTab","rel_maintitle_{$tab}.{$tab}_id=$relTab.{$tab}_id",array())
			->joinInner($workName, "$relTab.{$workName}_id={$workName}.{$workName}_id",$fields)
			->where('maintitle.maintitle_skin_id IN ( ? )',$skinStr);
			if ($whereStr){
			 $select->where($whereStr);
			}
			//->limit($count,$offset);
		//print $select."<br>";
		//die();
			//$arr=array_merge($arr,$this->fetchAll($select)->toArray());
			$selArr[]=$select->__toString();
		}
		if ($count || $offset){
			$limitStr=" LIMIT $count OFFSET $offset";
		}

		$sel=implode(" UNION ",$selArr).$limitStr;
		//print $sel;
		//$arr=$this->fetchAll($sel)->toArray();
		//print_r($arr);
		$db=Zend_Registry::get('db');
		//$ses=Zend_Registry::get('nsHw');
		//$ses->lang='en';
		//$stm=$db->query($sel);

		$arr=$db->fetchAll($sel);


		return $arr;
	}




	public function skinDocs($skin_id, $count=0,$offset=0, $tab=null,$whereStr=''){
		if (!$tab){
			$tab=$this->_name;
		}
	if (is_array($skin_id)){
    $skinStr=implode(',',$skin_id);
   }elseif(is_string($skin_id)){
    $skinStr=trim($skin_id);
   }elseif(is_int($skin_id)){
    $skinStr=$skin_id;
   }

		$select=$this->select()->setIntegrityCheck(false);
		$select
		->distinct(true)
		->from('maintitle', array())
		->joinLeft("rel_maintitle_{$tab}","maintitle.maintitle_id=rel_maintitle_{$tab}.maintitle_id",array())
		->joinInner($tab, "rel_maintitle_{$tab}.{$tab}_id={$tab}.{$tab}_id")
		->where('maintitle.maintitle_skin_id IN ( ? )',$skinStr);

		if ($whereStr){
			$select->where($whereStr);
		}
		if ($count){
		  $select->limit($count,$offset);
		}
//		print $select."<br>";
//		die();
		return $arr=$this->fetchAll($select)->toArray();
	}

	public function countSkinDocs($skin_id, $tab=null,$whereStr=''){
		if (!$tab){
			$tab=$this->_name;
		}
	if (is_array($skin_id)){
    $skinStr=implode(',',$skin_id);
   }elseif(is_string($skin_id)){
    $skinStr=trim($skin_id);
   }elseif(is_int($skin_id)){
    $skinStr=$skin_id;
   }

		//SELECt count(distinct text_id ) FROM maintitle as m LEFT JOIN rel_maintitle_text as r ON m.maintitle_id=r.maintitle_id
		//WHERE m.maintitle_skin_id=1;
		$select=$this->select()->setIntegrityCheck(false);
		$select
		->from('maintitle',array("COUNT(DISTINCT {$tab}.{$tab}_id ) as {$tab}_count"))
		->joinLeft("rel_maintitle_{$tab}","maintitle.maintitle_id=rel_maintitle_{$tab}.maintitle_id",array())
		->joinInner($tab, "rel_maintitle_{$tab}.{$tab}_id={$tab}.{$tab}_id", array())
		->where('maintitle.maintitle_skin_id IN ( ? )',$skinStr);
		//print $select."<br>";
  	if ($whereStr){
      $select->where($whereStr);
    }

		$arr=$this->fetchAll($select)->toArray();

		//print_r($arr);
		$counter=$arr[0]["{$tab}_count"];
		return $counter;
	}
}
