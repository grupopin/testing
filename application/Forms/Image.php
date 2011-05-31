<?php


class Forms_Image extends Zend_Form
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

        $element = new Zend_Form_Element_File('image');
		$element->setLabel('Upload an image:');
		$element->setDestination($this->_path);
		$element->setRequired(true);
		$element->addValidator('NotEmpty');
		$element->addValidator('Extension', false, 'jpg,png,gif'); // only JPEG, PNG, and G
		//$element->addValidator('Count', false, 1);     // ensure only 1 file
		//$element->addValidator('Size', false, 102400); // limit to 100K
		$this->addElement($element);
		
        
		$element = new Zend_Form_Element_Hidden('fold');
		$element->setValue($opts['fold']);
		$this->addElement($element,'fold');
		
		$this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Upload file',
        ));
        
		$element = new Zend_Form_Element_Hidden('n');
		$element->setValue($opts['n']);
		$this->addElement($element,'n');
		
		//agregado por gc
		$element = new Zend_Form_Element_Hidden('id');
		$element->setValue($opts['id']);
		$this->addElement($element,'id');
		
		$element = new Zend_Form_Element_Hidden('objeto_id');
		$element->setValue($opts['objeto_id']);
		$this->addElement($element,'objeto_id');
		
    	
    }
}
        
        

