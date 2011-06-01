<?php

include_once("prolog.scanner.php");
$prolog_source_code = "graph_to_skin(14, 2), graph_title(14, 'LABYRINTHE POUR UNE EXPOSITION'), graph_author(14, 'Hundertwasser'), skin(2,
'Clothes'), search_atom('Clothes', clo), graph_edition(14, 'Edition of 50, unsigned and not numbered Probably 10 proofs').";
$scanner = &new PrologScanner($prolog_source_code);
do {
  $scanner->nextToken();
  echo $scanner->token . ": \"" . $scanner->tokenValue . "\"\n";
} while ($scanner->token != PS_EOF);


?>
