<?php
require_once("/home/hwarch/domains/hw-archive.com/library/Hw/Xml.php");
//$xmlFile=new XMLReader("/home/hwarch/domains/hw-archive.com/faust_import/HWG_all.xml");
print memory_get_usage()."\n";

$fileName="/home/hwarch/domains/hw-archive.com/faust_import/HWG_all.xml";
$xml=new XMLReader();
$xml->open($fileName);

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

          $count[$xml->localName]++;
          $xml->read();
          if ($xml->hasValue){
            //$fObject[$count['faustObjectNum']][$bufName][]=$xml->value;
            if ($fObject[$count['faustObjectNum']][$bufName]){
              //then convert to an array
              if (!is_array($fObject[$count['faustObjectNum']][$bufName])){
                $tmpVal=$fObject[$count['faustObjectNum']][$bufName];
                $fObject[$count['faustObjectNum']][$bufName]=array($tmpVal);

              }
              array_push($fObject[$count['faustObjectNum']][$bufName],$xml->value);

            }else{
              $fObject[$count['faustObjectNum']][$bufName]=$xml->value;
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
print_r ($count)."\n";
//print_r($fObject);

print memory_get_usage();
//print_r($arr);


?>