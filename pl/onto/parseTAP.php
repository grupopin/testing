<?php
require_once ("xmlToOntoParser.php");
$fileName="updates/011210/Tapestries.xml";

class tapParser extends xmlToOntoParser{



}

$fieldsYaml=<<<YAML
TAP:
    key: true
    term: tap
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

$tapParser=new tapParser($fileName, "tap", "TAP", $fieldsYaml);
$tapParser->titleField="Title";
$tapParser->parse();

//print_r($parsedArr);






