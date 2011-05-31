<?php


class Forms_Delete_Image extends Zend_Form
{
	protected $_filename;
	//protected $_path = '/home/hwarch/public_html/upload';
	protected $_path = 'c:\proyectos\desarrollo\hw\public_html\upload';
	
	public function getFilename(){
		return $this->_filename;
	}
	public function setFilename($txt){
		$this->_filename=$txt;
	}
	
	public function getPath(){
		return $this->_path;
	}
	public function setPath($txt){
		$this->_path=$txt;
	}
	
	public function init()
    {
    	// Set the method for the display form to POST
         
    }
    
    public function addAll($opts){
       	$this->setMethod('post');
		$this->setAttrib('enctype', 'multipart/form-data');

 		$element = new Zend_Form_Element_Text('fold');	
		$element->setRequired(true);
		$element->setValue($opts['fold']);
		$element->setOptions(array('readonly'=>'1'));
		$element->setLabel('Type :');
		$this->addElement($element,'fold');
		
 		
 		$element = new Zend_Form_Element_Text('n');	
		$element->setRequired(true);
		$element->setValue($opts['n']);
		$element->setOptions(array('readonly'=>'1'));
		$element->setLabel('Image name:');
		$this->addElement($element,'n');
 		
		
		$this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Delete file',
        ));
    	
    }
}
        
        

