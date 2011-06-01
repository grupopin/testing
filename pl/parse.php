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
$datPath="/home/hwarch/domains/hw-archive.com/public_html/hwdb/control/dat/15_01_2008";
$fileName=$datPath."/HWGNeu_neu.txt";
//$fileName=$datPath."/hwg.txt";
$fp=fopen($fileName,'r');
$fpOut=fopen('kbase/graph.pl','w+');
//print $fpOut;
//exit();

$pkName="graph_id";
$sameTermsTogether=true;
$termPrefix="graph_";
$fieldsYaml=<<<YAML
---
HWG:
    key: true
    term: hwg
    first: true
Work No:
    term: work_num
Hundertwasser comment:
    term: hundertwasser_comment
    alternate:
        - _ge
        - _en

Title:
    term: title
Title G:
    term: title_german
Title E:
    term: title_english
Title F:
    term: title_french
Title I:
    term: title_italian
Title J:
    term: title_japanese
One-man exhibitions:
    term: oneManExhibitions
Group exhibitions:
    term: groupExhibitions

Published / Place:
    term: published_place
Published / Year:
    term: published_year
Description / Portfolio:
    term: description_portfolio
Technique keyword:
    term: technique_keyword
Technique:
    term: tecnique
Sheet Height:
    term: sheet_height
Sheet Length:
    term: sheet_length
Image Heigth:
    term:  image_heigth
Image Lengh:
    term: image_length
Cut by:
    term: cut_by
Printed by:
    term: printed_by
Printed Place:
    term: printed_place
Printed Date:
    term: printed_date
Printed by / Place / Date:
    term: printedPlaceDate
Studio:
    term: studio
Coordinator:
    term: coordinator
Edition:
    term: edition
Variants:
    term: variants
Published by:
    term: published_By
Information:
    term: information
Literature: Monographs:
    term: litMonographs

Literature: Exhibition catalogues:
    term: literature_exhibition_catalogues

Reproductions / Art Prints
    term: reproductions_art_prints

Application
    term: application

YAML;


$fieldsArr=Spyc::YAMLLoadString($fieldsYaml);
$stackOfFields=array_keys($fieldsArr);
//print_r($fieldsArr);
//exit();

class parserEvents{
  protected $_curExbGroupId=0;
  protected $_galleryCount=0;
  protected $_galleryGroupCnt=0;
  protected $_galleryArray=array();
  protected $_authorCount=0;
  protected $_authorArr=array();

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


function printedPlaceDate($str, $curId){
  //Printed by / Place / Date  Robert Finger, Vienna, 1981
  global $fpOut, $parserData;
  $str=trim($str);
  if (!$str) return false;

  $tmpArr=explode(',',$str);
  array_walk($tmpArr,'hw_trim');

 $prnStr="printed($curId,'{$tmpArr[0]}','{$tmpArr[1]}','{$tmpArr[2]}').";
 outTerm($prnStr,'printed');


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
    $monoYear='""';
  }

  $monoStr.="literature_monographs($curId,$authId,'$monoName',$monoYear,'$monoPointers').";
  outTerm($monoStr,"literature_monographs");

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


  $oneManExhibTerm="group_exhibition($curId,$galId,{$parserData->getCurrentExbGroupId()},'$year1','$year2','$portfolio').".PARSE_NewLine;
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
    preg_match("@([^,\r\n]*) ?([\d]{4})[/-]?([\d]{0,4})@",$str,$match);
    if (!$match){
      $match[1]=mb_substr($str,0,$len);
    }
    $grId=$parserData->incCurExhibGroup();

    $galGroupTerm="group_gallery($grId,'Travelling','{$match[1]}','{$match[2]}','{$match[3]}').".PARSE_NewLine;
    outTerm($galGroupTerm,"group_gallery");

  }elseif($str=='*'){
    //$parserData->setCurrentExbGroupId(0);
  }else{

    if (mb_stristr($str,"Schindler")){
      // $ind=1;
    }
    //check how many comma separated parts are in sentence
    //
    if (mb_strpos(strrev($str),strrev("("))===0){
      $str=str_replace("(","",$str);
      $exhibYearAB="a";
    }elseif (mb_strpos(strrev($str),strrev("(a"))===0){
      $str=str_replace("(a","",$str);
      $exhibYearAB="a";
    }elseif (mb_strpos(strrev($str),strrev("(a,"))===0){
      $str=str_replace("(a,","",$str);
      $exhibYearAB="a,b";
    }elseif (mb_strpos(strrev($str),strrev("(a, "))===0){
      $str=str_replace("(a, ","",$str);
      $exhibYearAB="a,b";
    }elseif (mb_strpos(strrev($str),strrev("(a, b"))===0){
      $str=str_replace("(a, b","",$str);
      $exhibYearAB="a,b";
    }elseif (mb_strpos(strrev($str),strrev("(a, b)"))===0){
      $str=str_replace("(a, b","",$str);
      $exhibYearAB="a,b";
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


    $oneManExhibTerm="one_man_exhibition($curId,$galId,{$parserData->getCurrentExbGroupId()},'$year1','$year2','$portfolio','$exhibYearAB').".PARSE_NewLine;
    outTerm($oneManExhibTerm,"one_man_exhibition");

    //$parserData->addOneManExhibitions($oneManExhib);
  }
}

function genTerm($fldProps,$val,$currentKeyId,$index,&$termName){
  static $stack,$first=true;
  $val=htmlentities($val, ENT_QUOTES,'UTF-8');
  if (function_exists($fldProps['term'])){
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
    $termName=$fldProps['term'].$curSfx;
    $termStr=$termName."($currentKeyId,'$val').";

  }
  return $termStr;
  //print
}

function matchField($stackOfFields,$str,&$restOfStr=''){
  $splitStr=explode(PARSE_FIELD_DELIMITER,$str);
  //print_r($splitStr);
  foreach($stackOfFields as $fld){
    if ($splitStr[0]==$fld){
      //print "********\n";
      if (sizeof($splitStr)>2){
        unset($splitStr[0]);
        $restOfStr=implode(PARSE_FIELD_DELIMITER,$splitStr);
      }else{
        $restOfStr=$splitStr[1];
      }
      return $fld;
    }

  }
  return false;
}



//first we parse and make array of pointers to fields(multiline values), and records
$curPos=0;
$curRecord=0;
$i=0;
$prevFld='';
while($line = fgets($fp)){
  $i++;
  $line=trim($line);
  $line=iconv("iso-8859-1","utf-8",$line);
  $fldFound=matchField($stackOfFields,$line,$restStr);

  if ($fldFound){
    //print $fldFound." = ".$restStr."\n";
    $fldProps=$fieldsArr[$fldFound];
    if ($fldProps['key']){
      $curRecord++;
    }
    if (!is_array($parsedRecords[$curRecord][$fldFound])){
      $parsedRecords[$curRecord][$fldFound]=array();
    }
    $curIndex=array_push($parsedRecords[$curRecord][$fldFound],$restStr);
    //print 'found:'.$curIndex."\n";

    //break;
    //print $termStr."<br>";
  }else{
    //print "not found:".($curIndex-1)."\n";
    //if field label not found then add the line to previous field.
    if ($curRecord>0){
      $parsedRecords[$curRecord][$prevFld][$curIndex-1].=PARSE_NewLine.$line;
    }
  }
  if ($fldFound){
    $prevFld=$fldFound;
  }
  //if ($i>100) break;
}
//exit();
//print_r($parsedRecords);
//print sizeof($parsedRecords);
//exit();



foreach($parsedRecords as $recNum=>$record){
  $pkTerm=$pkName."($recNum).";
  outTerm($pkTerm,$pkName);

  foreach($record as $fld=>$valArr){
    foreach($valArr as $l=>$val){
      if ($val){
        $termStr=genTerm($fieldsArr[$fld],$val,$recNum,$l,$curTermName);

        if ($termStr){
          outTerm($termStr,$curTermName);
        }
      }
    }
  }




}

ksort($kbArr);
//print_r($kbArr);
if ($sameTermsTogether){
  foreach($kbArr as $trm=>$trmStr){
    fwrite($fpOut,$trmStr,mb_strlen($trmStr));
  }
}
fclose($fp);
fclose($fpOut);


//print_r($yaml);



?>