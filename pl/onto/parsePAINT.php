<?php
require_once ("xmlToOntoParser.php");

$fileName="updates/221110/Paintings.xml";

class paintParser extends xmlToOntoParser{



}

$fieldsYaml=<<<YAML
---
Work_Number:
    key: true
    term: workNumber
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
One-man_exhibitions:
    term: oneManExhibitions

Group_exhibitions:
    term: groupExhibitions

Coordinator:
    term: coordinator

Literature-_Exhibition_catalogues:
    term: litExhibCatalogs

YAML;
*/
//$pkName[0]="Work_Number";
//$pkName[1]="JW";

$paintParser=new paintParser($fileName, "paint", "Work_Number", $fieldsYaml);
$paintParser->titleField="Title";
$paintParser->parse();

//$parsedArr=$paintParser->parsedRecords;
//print_r($parsedArr);






