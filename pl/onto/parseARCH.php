<?php
require_once ("xmlToOntoParser.php");

//$fileName="110509/ARCH_all.xml";
//$fileName="updates/ARCH74.xml";
$fileName="updates/011210/Arch.xml";

class archParser extends xmlToOntoParser{



}

$fieldsYaml=<<<YAML
ARCH:
    key: true
    term: arch
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

One-man_exhibitions:
    term: oneManExhibitions

Literature-_Exhibition_catalogues:
    term: litExhibCatalogs

Coordinator:
    term: coordinator

YAML;
*/

$archParser=new archParser($fileName, "arch", "ARCH", $fieldsYaml);
$archParser->titleField="Title";
$archParser->parse();

//print_r($parsedArr);






