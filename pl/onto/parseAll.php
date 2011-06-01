<?php
$files=array('exportDbText‬','parseAPA','parseARCH','parseHWG','parseJW','parsePAINT','parseTAP');
foreach($files as $file){
  system('php '.$file.'.php');
  sleep(5);
  print "\n\n\n";
}


//die("Will be implemented later");