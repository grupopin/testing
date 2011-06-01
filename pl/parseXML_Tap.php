<?php
require_once('/home/hwarch/domains/hw-archive.com/library/spyc/spyc.php');
//this script parses the text file and converts it to prolog terms
//file is parsed line by line
//record is content between 3 newline chars
//at the beginning of each line we check if there is a token(field name)
//the content/value of the field is all the text before next token/field or end of record (3 new lines)
//if the same field name occures in the same record then it is treated as new clause for the same term.
//if in a field definition array a property 'alternate' is set then all fields are added with suffix '_en', '_ge' etc
//e.g.
//title  This is a test title
//title  This is an alternative title
//result:
//title_en('This is ...').
//title_ge('This is alt ...').
//terminalSymbols

//if field defintion has property: subterm, then it should parse the value of the field to another term, for example:
//Exhibitions: The Gallery of Sun, Sun Francisco, 1990/91
//we get:
//exhibitions('23',exhibition_place(Name,City,YearFrom,YearTo)).

define('PARSE_NewLine',"\r\n");
define('PARSE_NEW_RECORD',PARSE_NewLine.PARSE_NewLine.PARSE_NewLine);
define('PARSE_FIELD_DELIMITER',"  ");

mb_internal_encoding("UTF-8");

//array_walk_recursive($fieldsArr)
//$yaml = Spyc::YAMLDump($fields,4,60);
$xmlPath="/home/hwarch/domains/hw-archive.com/faust_import/110509";
$fileName=$xmlPath."/TAP_all.xml";
$xml=new XMLReader();
$xml->open($fileName);

$fpOut=fopen('kbase/xml_tap.pl','w+');
//print $fpOut;
//exit();

$pkName="tap_id";
$sameTermsTogether=true;
$termPrefix="tap_";
$fieldsYaml=<<<YAML
---
TAP:
    key: true
    term: tap
    first: true
Work_Number:
    term: work_num

Hundertwasser_comment:
    term: hundertwasser_comment
    alternate:
        - _ge
        - _en

Information:
    term: information
    alternate:
        - _ge
        - _en


One-man_exhibitions:
    term: oneManExhibitions

Group_exhibitions:
    term: groupExhibitions

Coordinator:
    term: coordinator

Literature-_Monographs:
    term: litMonographs

Literature-_Exhibition_catalogues:
    term: litExhibCatalogs

YAML;


$fieldsArr=Spyc::YAMLLoadString($fieldsYaml);
//$stackOfFields=array_keys($fieldsArr);
//print_r($fieldsArr);
//exit();

class parserEvents{
  protected $_curExbGroupId=0;
  protected $_galleryCount=0;
  protected $_galleryGroupCnt=0;
  protected $_galleryArray=array();
  protected $_authorCount=0;
  protected $_authorArr=array();
  protected $_galleryCatalogCount=0;
  protected $_galleryCatalog=array();

  public function setCurrentExbGroupId($val){
    $this->_curExbGroupId=$val;
  }

  public function incCurExhibGroup(){
    return $this->_curExbGroupId=$this->_curExbGroupId+1;
  }

  public  function getCurrentExbGroupId(){
    return $this->_curExbGroupId;
  }

  public function incGalleryCount(){
    return $this->_galleryCount=$this->_galleryCount+1;
  }

  public function addGallery($galName,&$new){
    if ($this->_galleryArray[$galName]){
      $new=false;
      return $this->_galleryArray[$galName];
    }else{
      $cnt=$this->incGalleryCount();
      $this->_galleryArray[$galName]=$cnt;
      $new=true;
      return $cnt;
    }

  }

  public  function getGalleryCount(){
    return $this->_galleryCount;
  }


  public function incAuthorCount(){
    return $this->_authorCount=$this->_authorCount+1;
  }

  public function getAuthorCount(){
    return $this->_authorCount;
  }

  public function addAuthor($auth,&$new){
    if ($this->_authorArr[$auth]){
      $new=false;
      return $this->_authorArr[$auth];
    }else{
      $new=true;
      $cnt=$this->incAuthorCount();
      $this->_authorArr[$auth]=$cnt;
      return $cnt;
    }

  }


  public function incExhibCatalogCount(){
    return $this->_exhibCatalogCount=$this->_exhibCatalogCount+1;
  }

  public function getExhibCatalogCount(){
    return $this->_exhibCatalogCount;
  }

  public function addExhibCatalog($exhibCatal,&$new){
    if ($this->_exhibCatalog[$exhibCatal]){
      $new=false;
      return $this->_exhibCatalog[$exhibCatal];
    }else{
      $new=true;
      $cnt=$this->incExhibCatalogCount();
      $this->_exhibCatalog[$exhibCatal]=$cnt;
      return $cnt;
    }
  }

  public function incCoordinatorCount(){
    return $this->_coordinatorCount=$this->_coordinatorCount+1;
  }

  public function getCoordinatorCount(){
    return $this->_coordinatorCount;
  }

  public function addCoordinator($auth,&$new){
    if ($this->_coordinatorArr[$auth]){
      $new=false;
      return $this->_coordinatorArr[$auth];
    }else{
      $new=true;
      $cnt=$this->incCoordinatorCount();
      $this->_coordinatorArr[$auth]=$cnt;
      return $cnt;
    }

  }







}
$parserData=new parserEvents();



//first we parse and make array of pointers to fields(multiline values), and records
$count=array();
while($xml->read()){
  switch ($xml->nodeType){
    case XMLReader::ELEMENT:
      switch($xml->localName){
        case 'Werke':
          $count['works']++;
          break;
        case 'FAUST-Objekt':
          $count['faustObjectNum']++;

          break;
        default:
          $bufName=$xml->localName;

          $count[$bufName]++;
          $xml->read();
          if ($xml->hasValue){
            $val=trim($xml->value);
            $val=iconv("iso-8859-1","utf-8",$val);
            //$fObject[$count['faustObjectNum']][$bufName][]=$xml->value;
            if ($parsedRecords[$count['faustObjectNum']][$bufName]){
              //then convert to an array
              if (!is_array($parsedRecords[$count['faustObjectNum']][$bufName])){
                $tmpVal=$parsedRecords[$count['faustObjectNum']][$bufName];
                $parsedRecords[$count['faustObjectNum']][$bufName]=array($tmpVal);

              }
              array_push($parsedRecords[$count['faustObjectNum']][$bufName],$val);

            }else{
              $parsedRecords[$count['faustObjectNum']][$bufName]=$val;
            }
            //    array_push();


          }
      }
        case XMLReader::END_ELEMENT:
          switch($xml->localName){
            case 'Werke':
              $count['workEnd']++;
              break;
            case 'FAUST-Objekt':
              $count['faustObjectEnd']++;
              break;
            default:
              $count[$xml->localName.'_end']++;


          }
          break;



  }
}



ksort($count);
//print_r ($count)."\n";

//print_r($parsedRecords[68]);
//exit();
/**
 * This function makes attempt to convert a values like '12', '221' to integer 12, 221...
 * If it is not a integer in form of string or is string then it just return it
 * @param $val
 * @return unknown_type
 */
function tryToInt($val){

  if (is_string($val)){
    $valInt=(int)$val;
    $str=(string)$valInt;
    if ($val==$str){
      return $valInt;
    }else{
      return $val;
    }
  }else{
    return $val;
  }
}
//print is_integer(tryToInt('123a'));
//exit();


foreach($parsedRecords as $recNum=>$record){
  //if ($recNum>1) break;
  $pkTerm=$pkName."($recNum).";
  $curTermName='';
  outTerm($pkTerm,$pkName);
  //print $recNum.", ";
  foreach($record as $fld=>$valArr){
    $first=true;
    //print $fld."::"; print count($valArr); print"**************\n";
    if (is_array($valArr)){
      //print '!!!array!!!\n';
      foreach($valArr as $l=>$val){
        if ($val){
          //print $fld."\n";
          $termStr=genTerm($fld,$val,$recNum,$l,$curTermName,$first);
          //print "==================term: $termStr==============\n";
          if ($termStr){
            outTerm($termStr,$curTermName);
          }
        }
      }
    }elseif(!empty($valArr)){
      //print $fld."\n";
      $termStr=genTerm($fld,$valArr,$recNum,$l,$curTermName,$first);
      if ($termStr){
        outTerm($termStr,$curTermName);
      }
    }
  }




}

ksort($kbArr);
//print_r($kbArr);
if ($sameTermsTogether){
  foreach($kbArr as $trm=>$trmStr){
    //print $trmStr."\n";
    fwrite($fpOut,$trmStr);
  }
}

fclose($fpOut);
$xml->close();

//print_r($yaml);

















function hw_trim(&$str){
  $str=trim($str);
}

function outTerm($termStr,$termName){
  global $sameTermsTogether, $kbArr, $fpOut, $termPrefix;
  $termStr2=$termPrefix.$termStr.PARSE_NewLine;

  if ($sameTermsTogether){
    $kbArr[$termName].=$termStr2;
  }else{
    fputs($fpOut,$termStr2, mb_strlen($termStr2));
  }
}


function coordinator($str, $curId){
  global $fpOut, $parserData;
  $str=trim($str);
  if (!$str) return false;
  $coordinator=$str;
  $usedIndex[]=0;
  $coordId=$parserData->addCoordinator($coordinator,$new);
  if ($new){
    $coordPersStr="coordinator_person($coordId,'$coordinator').";
    outTerm($coordPersStr,'coordinator_person');
  }
  $coordStr="coordinator($curId,$coordId).";
  outTerm($coordStr, 'coordinator');

}

function litMonographs($str,$curId){
  //Literature: Monographs
  //parse in form:
  //author,name, year, pointers
  //author is first field
  //year is year of issue
  //pointers are all page nums etc
  //name - rest of fields including name of catalog, sub.suthors, cities etc

  global $fpOut, $parserData;
  $str=trim($str);
  if (!$str) return false;
  $tmpArr=explode(',',$str);
  array_walk($tmpArr,'hw_trim');

  $author=$tmpArr[0];
  $usedIndex[]=0;

  $authId=$parserData->addAuthor($author,$new);
  if ($new){
    $authStr="author($authId,'$author').";
    outTerm($authStr,'author');

  }


  $len=sizeof($tmpArr);
  for($k=1;$k<$len;$k++){
    $el=$tmpArr[$k];
    if (preg_match("@^[\d]{4}$@",$el)){

      $monoYear=$el;
      $usedIndex[]=$k;
    }
    if (preg_match("@p. |pl. @i",$el)){
      $pointerArr[]=$el;
      $usedIndex[]=$k;
    }
  }
  if ($pointerArr){
    $monoPointers=implode(',',$pointerArr);
  }

  //gather all not used elements
  for($k=1;$k<$len;$k++){
    if (!in_array($k,$usedIndex)){
      $remArr[]=$tmpArr[$k];
    }
  }

  if ($remArr){
    $monoName=implode(',',$remArr);
  }

  if (!$monoYear){
    $monoYear="''";
  }

  $monoStr.="literature_monographs($curId,$authId,'$monoName',$monoYear,'$monoPointers').";
  outTerm($monoStr,"literature_monographs");

}

function litExhibCatalogs($str,$curId){
  //Literature: Monographs
  //parse in form:
  //author,name, year, pointers
  //author is first field
  //year is year of issue
  //pointers are all page nums etc
  //name - rest of fields including name of catalog, sub.suthors, cities etc

  global $fpOut, $parserData;
  $str=trim($str);
  if (!$str) return false;
  $tmpArr=explode(',',$str);
  array_walk($tmpArr,'hw_trim');

  $exhibCatalog=$tmpArr[0];
  $usedIndex[]=0;

  $exhibCatalogId=$parserData->addExhibCatalog($exhibCatalog,$new);
  if ($new){
    $exhibCatalogStr="exhib_catalog($exhibCatalogId,'$exhibCatalog').";
    outTerm($exhibCatalogStr,'exhib_catalog');

  }


  $len=sizeof($tmpArr);
  for($k=1;$k<$len;$k++){
    $el=$tmpArr[$k];
    if (preg_match("@^[\d]{4}$@",$el)){

      $monoYear=$el;
      $usedIndex[]=$k;
    }
    if (preg_match("@p. |pl. @i",$el)){
      $pointerArr[]=$el;
      $usedIndex[]=$k;
    }
  }
  if ($pointerArr){
    $monoPointers=implode(',',$pointerArr);
  }

  //gather all not used elements
  for($k=1;$k<$len;$k++){
    if (!in_array($k,$usedIndex)){
      $remArr[]=$tmpArr[$k];
    }
  }

  if ($remArr){
    $monoName=implode(',',$remArr);
  }

  if (!$monoYear){
    $monoYear="''";
  }

  $monoStr.="literature_exhibition_catalogs($curId,$exhibCatalogId,'$monoName',$monoYear,'$monoPointers').";
  outTerm($monoStr,"literature_exhibition_catalogs");

}


function groupExhibitions($str,$curId,$index){
  global $fpOut, $parserData,$parsedRecords;
  $str=trim($str);
  if (!$str) return false;
  //@todo - add travelling exhibition parser

  //check how many comma separated parts are in sentence
  //
  $tmpArr=explode(",",$str);
  $cnt=count($tmpArr);
  $yearRegex="@(?P<year1>[\d]{2,4})?[-\/]?(?P<year2>[\d]{2,4})?[\s^\r\n]{1,4}(?P<portfolio>[\w\(\)]{2,4})?@i";
  switch($cnt){
    case 1:
      //print "Gallery with no , - error\n";
      $galleryName=trim($tmpArr[0]);
      //exit($str);
      break;
    case 2:
      $galleryName=trim($tmpArr[0]);
      preg_match($yearRegex,trim($tmpArr[1]),$yrMatch);
      $year1=$yrMatch['year1'];
      $year2=$yrMatch['year2'];
      if ($yrMatch['portfolio']){
        $portfolio='portfolio';
      }
      break;
    case 3:
      $galleryName=trim($tmpArr[0]);
      $galleryCity=trim($tmpArr[1]);
      preg_match($yearRegex,trim($tmpArr[2]),$yrMatch);
      $year1=$yrMatch['year1'];
      $year2=$yrMatch['year2'];
      if ($yrMatch['portfolio']){
        $portfolio='portfolio';
      }
      break;
    case 4:
      $galleryName=trim($tmpArr[0]);
      $galleryOrg=trim($tmpArr[1]);
      $galleryCity=trim($tmpArr[2]);
      preg_match($yearRegex,trim($tmpArr[3]),$yrMatch);
      $year1=$yrMatch['year1'];
      $year2=$yrMatch['year2'];
      if ($yrMatch['portfolio']){
        $portfolio='portfolio';
      }
      break;
    default:
      print __FUNCTION__."\n";
      print_r($parsedRecords[$curId]['Work No']);
      print_r($parsedRecords[$curId]['Group exhibitions']);
      print $str."\n";
      exit("record # $curId\n Line: $index\n. Number of data fields in gallery is $cnt. Please write the code for this event");

      //preg_match("@^(?P<galleryName>[^,\r\n]*), ?(?P<galleryCity>[^,\r\n]*)?, ?(?P<year1>[\d]{0,4})?[-\/]?(?P<year2>[\d]{0,4})?[\s^\r\n]{1,4}(?P<portfolio>[/(pf/)]{2,4})?$@i",$str,$match);
  }


  //$parserData->incGalleryCount();
  $galId=$parserData->addGallery($galleryName,$new);
  //print $galId.":$galleryName\n";
  if ($new){
    $galStr="gallery_name($galId,'$galleryName').";
    outTerm($galStr,"gallery_name");
  }

  $galTermStr="gallery($curId,$galId,'$galleryOrg','$galleryCity').";
  outTerm($galTermStr,"gallery");

  $year1=hwConvYear($year1);
  $year2=hwConvYear($year2);
  if (!$year2){
    $year2=$year1;
  }
  if (!$year1){
    $year1=0;
  }
  if (empty($year1)){
    $year1=0;$year2=0;
  }

  $oneManExhibTerm="group_exhibition($curId,$galId,{$parserData->getCurrentExbGroupId()},$year1,$year2,'$portfolio').";
  outTerm($oneManExhibTerm,"group_exhibition");
  //$parserData->addOneManExhibitions($oneManExhib);

}

function oneManExhibitions($str,$curId,$index){
  global $fpOut, $parserData,$parsedRecords;
  $str=trim($str);
  if (!$str) return false;
  //@todo - add travelling exhibition parser
  //:
  //*
  //fputs($fpOut,$str."\n");
  $pos=mb_strpos($str,":");
  $len=mb_strlen($str)-1;
  $foundStr=mb_stristr($str,'Travelling');
  $foundTravelling=mb_stristr($str,'exhibition:');
  //print $pos.'-'.$len.'-'.$foundStr."\n";

  if (  (($pos == $len) && $foundStr) || $foundTravelling){
    //parse group of exhibition header
    //
    //print $str."\n";
    preg_match("@([^,\r\n\d]*) ?([\d]{4})[/-]?([\d]{0,4})@",$str,$match);
    if (!$match){
      $match[1]=mb_substr($str,0,$len);
    }
    $grId=$parserData->incCurExhibGroup();
    $year1=hwConvYear($match[2]);
    $year2=hwConvYear($match[3]);
    if (!$year2){
      $year2=$year1;
    }
    if (!$year1){
      $year1=0;
    }
    if (empty($year1)){
      $year1=0;$year2=0;
    }
    $galGroupTerm="group_gallery($grId,'Travelling','{$match[1]}',$year1,$year2).";
    outTerm($galGroupTerm,"group_gallery");

  }elseif($str=='*'){
    //$parserData->setCurrentExbGroupId(0);
  }else{

    if (mb_stristr($str,"Galleria La Medusa")){
      //$ind=1;
    }
    //check how many comma separated parts are in sentence
    //
//    if (mb_strpos(strrev($str),strrev("(a, b)"))===0){
//      $str=str_replace("(a, b)","",$str);
//      $exhibYearAB="a,b";
//    }
    $abFindRex="@\(([ab, ]+)\)@i";
    preg_match($abFindRex,$str,$match);
    if ($match[0]){
      $str=str_replace($match[0],"",$str);
      $exhibAB=$match[0];
    }

    $tmpArr=explode(",",$str);
    $cnt=count($tmpArr);
    $yearRegex="@(?P<year1>[\d]{2,4})?[-\/]?(?P<year2>[\d]{2,4})?[\s^\r\n]{1,4}(?P<portfolio>[\w\(\)]{2,4})?@i";
    switch($cnt){
      case 1:
        //print "Gallery with no , - error\n";
        $galleryName=trim($tmpArr[0]);
        //exit($str);
        break;
      case 2:
        $galleryName=trim($tmpArr[0]);
        preg_match($yearRegex,trim($tmpArr[1]),$yrMatch);
        $year1=$yrMatch['year1'];
        $year2=$yrMatch['year2'];
        if ($yrMatch['portfolio']){
          $portfolio='portfolio';
        }
        break;
      case 3:
        $galleryName=trim($tmpArr[0]);
        $galleryCity=trim($tmpArr[1]);
        preg_match($yearRegex,trim($tmpArr[2]),$yrMatch);
        $year1=$yrMatch['year1'];
        $year2=$yrMatch['year2'];
        if ($yrMatch['portfolio']){
          $portfolio='portfolio';
        }
        break;
      case 4:
        $galleryName=trim($tmpArr[0]);
        $galleryOrg=trim($tmpArr[1]);
        $galleryCity=trim($tmpArr[2]);
        preg_match($yearRegex,trim($tmpArr[3]),$yrMatch);
        $year1=$yrMatch['year1'];
        $year2=$yrMatch['year2'];
        if ($yrMatch['portfolio']){
          $portfolio='portfolio';
        }
        break;
      default:
        print __FUNCTION__."\n";
        print_r($parsedRecords[$curId]['Work No']);
        print_r($parsedRecords[$curId]['One-man exhibitions']);
        print $str."\n";
        exit("record # $curId\n Line $index\n Number of data fields in gallery is $cnt. Please write the code for this event");

        //preg_match("@^(?P<galleryName>[^,\r\n]*), ?(?P<galleryCity>[^,\r\n]*)?, ?(?P<year1>[\d]{0,4})?[-\/]?(?P<year2>[\d]{0,4})?[\s^\r\n]{1,4}(?P<portfolio>[/(pf/)]{2,4})?$@i",$str,$match);
    }

    if ($ind) {
      print_r($tmpArr);
      print $tmpArr[2];
      print "
      $str
      $galleryName
      $galleryOrg
      $galleryCity
      $year1
      $year2
      $portfolio
      $exhibAB
      ";
      exit();
    }

    //$parserData->incGalleryCount();
    $galId=$parserData->addGallery($galleryName,$new);
    //print $galId.":$galleryName\n";
    if ($new){
      $galStr="gallery_name($galId,'$galleryName').";
      outTerm($galStr,"gallery_name");

    }

    $galTermStr="gallery($curId,$galId,'$galleryOrg','$galleryCity').";
    outTerm($galTermStr,"gallery");

    $year1=hwConvYear($year1);
    $year2=hwConvYear($year2);

    //$year1Int=tryToInt($year1);
    //$year2Int=tryToInt($year2);
    if (empty($year2)){
      $year2=$year1;
    }
    if (empty($year1)){
      $year1=0;$year2=0;
    }
    $oneManExhibTerm="one_man_exhibition($curId,$galId,{$parserData->getCurrentExbGroupId()},$year1,$year2,'$portfolio','$exhibAB').";
    outTerm($oneManExhibTerm,"one_man_exhibition");

    //$parserData->addOneManExhibitions($oneManExhib);
  }
}

function hwConvYear($year){
  if ($year){
    if (strlen($year)<3){
      if ((int)$year>30){
        $year=1900+$year;
      }else{
        $year=2000+$year;
      }
    }

  }
  return $year;
}


function genTerm($fld,$val,$currentKeyId,$index,&$termName,&$first){
  global $fieldsArr;
  static $stack;
  $termName='';
  $fldProps=$fieldsArr[$fld];
  $val=tryToInt($val);
  if (is_string($val)){
    $val=htmlentities($val, ENT_QUOTES,'UTF-8');
  }

  if (is_array($fldProps)){
    if (function_exists($fldProps['term'])){
      //print "{$fldProps['term']}($val,$currentKeyId,$index)\n";
      $fldProps['term']($val,$currentKeyId,$index);

    }else{
      $curSfx='';
      if ($fldProps['alternate']){
        if ($first){
          $stack=$fldProps['alternate'];
          $first=false;
        }
        $curSfx=array_pop($stack);
      }
      if ($fldProps['term']){
        $termName=$fldProps['term'].$curSfx;
      }else{
        $termName=str_replace(array('-','_',' '),'_',strtolower($fld));
      }
    }
  }else{
    $termName=str_replace(array('-','_',' '),'_',strtolower($fld));
  }

  if ($termName){
    if (is_integer($val)){
      $termStr=$termName."($currentKeyId,$val).";
    }else{
      $termStr=$termName."($currentKeyId,'$val').";
    }

    //print $termStr."\n------------------------------------\n";
  }
  //if (stristr($termName,'exhib')){
  // print $termStr;
  // die();
  // }
  return $termStr;

  //print
}


?>