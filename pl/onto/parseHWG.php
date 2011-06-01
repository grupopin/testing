<?php
require_once ("xmlToOntoParser.php");

$fileName="updates/011210/Graphics.xml";

class hwgParser extends xmlToOntoParser{



}

$fieldsYaml=<<<YAML
HWG:
    key: true
    term: hwg
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
$hwgParser=new hwgParser($fileName, "hwg", "HWG", $fieldsYaml);
$hwgParser->titleField="Title";
$hwgParser->parse();

//print_r($parsedArr);






