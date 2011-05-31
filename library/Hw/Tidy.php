<?php
/**
 * The class is built to run tidy from command line
 * The reason it was built is that merge-spans did not work properly in tidy.so. As per php site there is a bug.
 * where as tidy which was built from fresh csv is working fine as a standalone version
 * @author levan
 *
 */
class Hw_Tidy{

  private static $_tidy;
  private static $_tidyClassName = 'Hw_Tidy';

  public static function getInstance() {
    if(!self::$_tidy)
    self::$_tidy= new Hw_Tidy();
    return self::$_tidy;
  }

  public static function setInstance(Hw_Tidy $trans)
  {
    if (self::$_tidy !== null) {
      require_once 'Zend/Exception.php';
      throw new Zend_Exception('Tidy is already initialized');
    }

    self::setClassName(get_class($trans));
    self::$_tidy = $trans;
  }

  /**
   * Initialize the default registry instance.
   *
   * @return void
   */
  protected static function init()
  {
    self::setInstance(new self::$_tidyClassName());
  }

  /**
   * Set the class name to use for the default instance.
   * Does not affect the currently initialized instance, it only applies
   * for the next time you instantiate.
   *
   * @param string $registryClassName
   * @return void
   * @throws Zend_Exception if the registry is initialized or if the
   *   class name is not valid.
   */
  public static function setClassName($registryClassName = 'Hw_Tidy')
  {
    if (self::$_tidy !== null) {
      require_once 'Zend/Exception.php';
      throw new Zend_Exception('Tidy is already initialized');
    }

    if (!is_string($registryClassName)) {
      require_once 'Zend/Exception.php';
      throw new Zend_Exception("Argument is not a class name");
    }

    /**
     * @see Zend_Loader
     */
    require_once 'Zend/Loader.php';
    Zend_Loader::loadClass($tidyClassName);

    self::$_tidyClassName = $tidyClassName;
  }

  public static function runTidy($html, $options=null){
    if (is_null($options)){
      $options="-quiet --input-encoding utf8 --show-warnings No --wrap 100 --clean yes --word-2000 yes --merge-spans yes --merge-divs yes  ";
    }
    $rand=uniqid(md5(mt_rand()), true);
    $tempFile="c:/proyectos/desarrollo/hw/var/text_$rand.html";
    $new=false;
    
    for ($i=0; $i<3; $i++){
      if (!file_exists($tempFile)){
        $new=true;
        break;
      }else{
        $rand=uniqid(md5(mt_rand()), true);
        $tempFile="c:/proyectos/desarrollo/hw/var/text_$rand.html";
      }
    }
    if ($i==3 && $new==false){
      require_once 'Zend/Exception.php';
      throw new Zend_Exception("Could not create a unique file in folder /home/hwarch/var");
    }
    $fp=fopen($tempFile,'w+');
    fwrite($fp, $html, strlen($html));
    fclose($fp);

    $tidypath= 'c:\proyectos\desarrollo\hw\library\tidy.exe';

    //$command = escapeshellcmd("$tidypath $options $tempFile");
    $command = $tidypath. " ". $options. $tempFile;
    
    $result = exec($command, $response);
    //echo $command;\
    //print $command."<br>";
    //print implode("\n",$response);
    $responseStr=implode("\n",$response);

    //now get styles
    preg_match_all('/<style[^>]*>(?P<style>.*?)<\/style>/si',$responseStr,$styles);
    //print_r($styles);

    preg_match_all('/<body[^>]*>(?P<body>.*?)<\/body>/si',$responseStr,$body);
    //print_r($body);


    $styleStr=trim(implode("",$styles['style']));
    $bodyStr=trim(implode("",$body['body']));

    //$mail=new Hw_Mail();
    //$mail->debugSend("tidy",$styleStr."<br>".$bodyStr);

    if ($styleStr){
    	$styleStr='<style type="text/css">'.$styleStr."</style>\n";
    }
    $cleanHtml=$styleStr.$bodyStr;

    //unlink($tempFile);
    return $cleanHtml;


  }


}


?>