<?php
/**
 * This calss is meant for storing dynamic predicates in file for further load by srv.pl
 * It is replaces by new class MyTrans.php which will be mysql driven.
 * @author levan
 *
 */
class Hw_Trans{

  protected $_transFile="/home/hwarch/domains/hw-archive.com/pl/kbase/trans.pl";
  protected $_fp=null;

  public function checkArity(){

  }

  public function parseTerm($term){
    $leftBracketPos=strpos($term,"(");
    $rightBracketPos=strrpos($term,")");
    $termHead=substr($term,0,$leftBracketPos);
    $termBody=substr($term,$leftBracketPos+1,$rightBracketPos-$leftBracketPos-1);

    //print $termHead."-".$termBody."<br>";
    $convStr=new Hw_Convert_Str();
    $termArr=$convStr->getcsv($termBody,",",'"');
    $termArity=count($termArr);
    $arr['functor']=$termHead;
    $arr['body']=$termBody;
    $arr['args']=$termArr;
    $arr['arity']=$termArity;

    return $arr;

  }
  public function updateTerm($term){
    if (substr($term,-1)!='.'){
      $term.=".";
    }

    $this->_fp=fopen($this->_transFile,"a+");
    //check if dynamic declaration exist for the same head and body arity
    //find what is the head name and arity
    //we will use funny way to do it
    //abc("1,234",2,3)

    $leftBracketPos=strpos($term,"(");
    $rightBracketPos=strrpos($term,")");
    $termHead=substr($term,0,$leftBracketPos);
    $termBody=substr($term,$leftBracketPos+1,$rightBracketPos-$leftBracketPos-1);

    //print $termHead."-".$termBody."<br>";
    $convStr=new Hw_Convert_Str();
    $termArr=$convStr->getcsv($termBody,",","'");
    $termArity=count($termArr);
    $dynamicDecl=":-dynamic $termHead/$termArity.";
    $fnd0=false;
    while($line=fgets($this->_fp)){
      $lineStr=trim($line);
      if ($lineStr==$dynamicDecl){
        $fnd0=true;
        break;
      }
    }
    if (!$fnd0){
      fputs($this->_fp,$dynamicDecl."\n");
    }


    $fnd=false;
    while($line=fgets($this->_fp)){
      $lineStr=trim($line);
      if ($lineStr==$term){
        $fnd=true;
        break;
      }
    }
    if (!$fnd){
      fputs($this->_fp,$term."\n");
    }
    fclose($this->_fp);
  }

  public function retractTerm($term){
    //just find and delete term


  }

}
?>