<?php
require_once ("xmlToOntoParser.php");
$fileName="updates/011210/JW.xml";

class jwParser extends xmlToOntoParser{



}

$fieldsYaml=<<<YAML
---
JW:
    key: true
    term: jw
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

//$pkName[1]="JW";

$jwParser=new jwParser($fileName, "jw", "JW", $fieldsYaml);
$jwParser->titleField="Title";
$jwParser->parse();
//$parsedArr=$paintParser->parsedRecords;
//print_r($parsedArr);






