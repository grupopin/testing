<?php
require_once ("xmlToOntoParser.php");

$fileName="updates/011210/APA.xml";

class apaParser extends xmlToOntoParser{

  protected function litMonographs( $str, $objName, $curId=0){
    $str2=trim($str);
    $arr=explode("/",$str2);
    array_walk($arr, 'hw_trim');
    $objProps=array('objekt'=>$arr[0],'storage'=>$arr[1]);
    $termStr=$this->addObject($objName, $objProps);
    return $termStr;
  }

}

$fieldsYaml=<<<YAML
APA:
    key: true
    term: apa
    first: true


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


YAML;
/*
$fieldsYaml.<<<YAML

Group_exhibitions:
    term: groupExhibitions

Coordinator:
    term: coordinator

One-man_exhibitions:
    term: oneManExhibitions

Literature-_Monographs:
    term: litMonographs

Literature-_Exhibition_catalogues:
    term: litExhibCatalogs

YAML;
*/
//$debug=true;//if true,- data will be sent to kb_trans2 table
$apaParser=new apaParser($fileName, "apa", "APA", $fieldsYaml);
$apaParser->titleField="Title";

$apaParser->parse();

//print_r($parsedArr);






