<?php

class Arch extends HwDbTable
{
    protected $_name = 'arch';
    protected $_primary  = 'arch_id';
    
	public function fetchByArchId($arch_id, $order = '', $count=null){
		$where = 'arch_id = ' . (int)$arch_id;
		
		return $this->fetchAll($where, $order, $count);
	}
	
	
//   public function countSkinWorks($skin_id){
//        return parent::countSkinWorks($skin_id,$this->_name);
//   }
//
// public function skinWorks($skin_id,$count,$offset){
//    return parent::skinWorks($skin_id,$count,$offset,$this->_name);
//   }
}
