<?php
require_once('Hw/Thumb/ThumbLib.inc.php');
 
class Image {
	
	private $_image;
	//private $_dimension;
	private $_file_info;
	protected $_src;
	protected $_code;
	protected $_id;
    protected $_object_type;
    protected $_image_name;
    protected $_frontend_visible;
    protected $_caption;
    protected $_mapper;
	
    public function getId(){
    	return $this->_id;
    }
    public function setId($val){
    	$this->_id=$val;
    }
    public function getObject_type(){
    	return $this->_object_type;
    }
    public function setObject_type($val){
    	$this->_object_type=$val;
    }
    public function getImage_name(){
    	return $this->_image_name;
    }
    public function setImage_name($val){
    	$this->_image_name=$val;
    }
    public function getFrontend_visible(){
    	return $this->_frontend_visible;
    }
    public function setFrontend_visible($val){
    	$this->_frontend_visible=$val;
    }
    public function getCaption(){
    	return $this->_caption;
    }
    public function setCaption($val){
    	$this->_caption=$val;
    }
    
    
    
	public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
        
    }
	
	public function __set($name, $value) {
        $method = 'set' . $name;
        if (!method_exists($this, $method)) {
            throw new Exception('Invalid Image property');
        }
        $this->$method($value);
    }
	
    public function __get($name){
        $method = 'get' . $name;
        if (!method_exists($this, $method)) {
            throw new Exception('Invalid Image property');
        }
        return $this->$method();
    }
    
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    

    public function setSrc($text){
    	$this->_src=$text;
    }
    public function getSrc(){
    	return $this->_src;
    }
    
    public function setFile_info($text){
    	$this->_file_info=$text;
    }
    public function getFile_info(){
    	return $this->_file_info;
    }
    
    public function setCode($text){
    	$this->_code=$text;
    }
    public function getCode(){
    	return $this->_code;
    }
    
    public function save(){
        $matches = array();
        $suff=$this->_code;
        
        /* Anulado por GC 
    	if(preg_match("/([a-z]+)_([a-zA-Z]+)_/",$this->_file_info['prefix_original'],$matches)){
    		$reg = "/".$matches[2]."_(.*jpg)/";

    		preg_match($reg,$this->_code,$matches2);
    		$suff=$matches2[1];
    	}*/
    	
//
    	$path_orig = $this->_file_info['path_original'].$this->_file_info['prefix_original'].$suff;
		$path_small = $this->_file_info['path_small'].$this->_file_info['prefix_small'].$suff;
		$path_med   = $this->_file_info['path_medium'].$this->_file_info['prefix_medium'].$suff;

    	if( file_exists($path_orig)){

			exec('mv '.$path_orig.' '.DOCROOT.'/old_images/'.strftime('%Y-%m-%d_%T').'_'.$this->_file_info['prefix_original'].$suff);
			exec('mv '.$path_small.' '.DOCROOT.'/old_images/'.strftime('%Y-%m-%d_%T').'_'.$this->_file_info['prefix_small'].$suff);
			exec('mv '.$path_med.' '.DOCROOT.'/old_images/'.strftime('%Y-%m-%d_%T').'_'.$this->_file_info['prefix_medium'].$suff);
		}	
		try {
//echo("Source : ".$this->_src.'/'.$this->_code."<br/>");
        	$this->_image = PhpThumbFactory::create($this->_src.'/'.$this->_code);
//echo("Create at ".$this->_src.'/'.$path_orig."<br/>");
        	$this->_image->save($path_orig);
//echo "Created at ".$path_orig." ".(file_exists($path_orig)>0)."<br/>";
        	$this->_image->resizePercent(33);
//echo("Create medium at ".$this->_src.'/'.$path_med."<br/>");
        	$this->_image->save($path_med);
//echo "Create at ".$path_med." ".(file_exists($path_med)>0)."<br/>";
        	$this->_image->resizePercent(66);
//echo("Create small at ".$this->_src.'/'.$path_small."<br/>");
        	$this->_image->save($path_small);
//echo "Created at ".$path_small." ".(file_exists($path_small)>0)."<br/>";
        	
		}
        catch(Exception $e){
        	echo "Error, please report to admin ! ";
		     echo (var_export($e, TRUE));
		}
			
		return "<p>Done, please close this window and refresh the item.</p>";  	

    }
    
    public function delete(){
//    	echo $this->_image_name."<br>";
    	$post = ".jpg";
    	
    	if (strpos($this->_image_name,"sm")===0){
    		$this->_image_name = substr($this->_image_name,strpos($this->_image_name,"_")+1);	    		    	
	    	$path_orig = $this->_file_info['path_original'].$this->_file_info['prefix_original'].$this->_image_name.$post;	 
	    	$path_small = $this->_file_info['path_small'].$this->_file_info['prefix_small'].$this->_image_name.$post;	 
			$path_med   = $this->_file_info['path_medium'].$this->_file_info['prefix_medium'].$this->_image_name.$post;	 
	    		
    	}
    	else{
    		
	    	$path_orig = $this->_file_info['path_original'].$this->_image_name.$post;	 
	    	$path_small = $this->_file_info['path_small'].$this->_image_name.$post;	 
			$path_med   = $this->_file_info['path_medium'].$this->_image_name.$post;	 
    		
    	}
//    	
//echo $this->_image_name.$post."<br>";
//
//$output0 = $path_orig."<br>";
//exec('ls -l '.$path_orig.'*',$output);
//$output0 .= $path_small."<br>";
//exec('ls -l '.$path_small.'*',$output);
//$output0 .= $path_med."<br>";
//exec('ls -l '.$path_med.'*',$output);
//echo "<pre>Before : " .$output0. var_export($output, TRUE) . "</pre>\\n";
//		
    	
    	
		if( file_exists($path_orig)){

			
			exec('mv '.$path_orig.' '.DOCROOT.'/old_images/'.strftime('%Y-%m-%d_%T').'_'.$this->_file_info['prefix_original'].$this->_image_name.$post,$out0);
//echo '<pre>done :' . 'mv '.$path_orig.' '.DOCROOT.'/old_images/'.strftime('%Y-%m-%d_%T').'_'.$this->_file_info['prefix_original'].$this->_image_name.$post . '</pre>\\n';			
			exec('mv '.$path_small.' '.DOCROOT.'/old_images/'.strftime('%Y-%m-%d_%T').'_'.$this->_file_info['prefix_small'].$this->_image_name.$post,$out0);
			exec('mv '.$path_med.' '.DOCROOT.'/old_images/'.strftime('%Y-%m-%d_%T').'_'.$this->_file_info['prefix_medium'].$this->_image_name.$post,$out0);
//echo "<pre>mv :" . var_export($out0, TRUE) . "</pre>\\n";
//exec('ls -l '.$path_orig.'*',$output1);
//exec('ls -l '.$path_small.'*',$output1);
//exec('ls -l '.$path_med.'*',$output1);
//echo "<pre>" . var_export($output1, TRUE) . "</pre>\\n";			
			$kbImage=new KbImage();
			try{
				$kbImage->delete('kb_image','image_name='.$this->_image_name);
			}
		   	catch(Exception  $e){}
		}	
		
		return "<p>Done, please close this window and refresh the item.</p>";
    }
    
	public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new KbImage_Mapper());
        }
        return $this->_mapper;
    }

    public function saveInDB()
    {	
    	$this->getMapper()->save($this);
    }

	public function fetchAll()
    {
        return $this->getMapper()->fetchAll();
    }
    
	
	
}

