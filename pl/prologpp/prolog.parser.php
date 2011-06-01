
<?php 

/* Prolog-Grammatik 
 * 
 * Code ::= Program Query 
 * Program ::= [Clause]+ 
 * Clause ::= Predicate [":-" Goals]? "." 
 * Predicate ::= WORD ["(" Terms ")"]? 
 * Goals ::= Goal ["," Goal]* 
 * Goal ::= "!" | "fail" | Predicate 
 * Terms ::= Term ["," Term]* 
 * Term ::= WORD ["(" Terms ")"]? | VARIABLE 
 * Query ::= "?-" Goals "." 
 */ 

/* Prolog-Grammatik mit Scanner-Tokens 
 * 
 * Code ::= Program Query 
 * Program ::= [Clause]+ 
 * Clause ::= Predicate [PS_DIRECTIVE Goals]? PS_DOT 
 * Predicate ::= PS_WORD [PS_LPAREN Terms PS_RPAREN]? 
 * Goals ::= Goal [PS_COMMA Goal]* 
 * Goal ::= PS_CUT | PS_FAIL | Predicate 
 * Terms ::= Term [PS_COMMA Term]* 
 * Term ::= PS_WORD [PS_LPAREN Terms PS_RPAREN]? | PS_VARIABLE 
 * Query ::= PS_QUERY Goals PS_DOT 
 */ 

/* INTERFACE 
 * 
 * class PrologParser { 
 *   var $scanner = null; 
 *   var $wimcode = null; // PUBLIC 
 *   var $errors = null; // PUBLIC 
 *   var $warnings = null; // PUBLIC 
 *   function PrologParser(&$scanner); // CONSTRUCTOR 
 *   function addError($msg, $token = true, $line = true); // PUBLIC 
 *   function hasErrors(); // PUBLIC 
 *   function addWarning($msg, $token = true, $line = true); // PUBLIC 
 *   function hasWarnings(); // PUBLIC 
 *   function tryNextToken(); 
 *   function parse(); // PUBLIC // Code ::= Program Query 
 *   function gProgram();        // Program ::= [Clause]+ 
 *   function gClause();         // Clause ::= Predicate [":-" Goals]? "." 
 *   function gPredicate();      // Predicate ::= WORD ["(" Terms ")"]? 
 *   function gGoals();          // Goals ::= Goal ["," Goal]* 
 *   function gGoal();           // Goal ::= "!" | "fail" | Predicate 
 *   function gTerms();          // Terms ::= Term ["," Term]* 
 *   function gTerm();           // Term ::= WORD ["(" Terms ")"]? | VARIABLE 
 *   function gQuery();          // Query ::= "?=" Goals "." 
 * } 
 */ 

/* EXAMPLE 
 * 
 * <?php 
 * 
 *   include_once("prolog.scanner.php"); 
 *   include_once("prolog.parser.php"); 
 * 
 *   $prolog_source_code = "isequal(X, X). ?- isequal(a, b)."; 
 * 
 *   $scanner = &new PrologScanner($prolog_source_code); 
 *   $parser = &new PrologParser($scanner); 
 * 
 *   $wimcode = $parser->parse(); // compile inclusive! 
 * 
 *   if (!$parser->hasErrors()) { 
 *     echo $wimcode; 
 *   } 
 * 
 * ?> 
 * 
 * OUTPUT 
 *             init 
 *             pushenv 4 
 *             enter 
 *             putatom a 
 *             putatom b 
 *             call isequal/2 
 *             halt 
 *   
 *  isequal$1: pushenv 7 
 *             pusharg 1 
 *             uvar 7 
 *             pusharg 2 
 *             uref 7 
 *             popenv 
 */ 



include_once("prolog.scanner.php"); 
include_once("prolog.generator.php"); 



class PrologParser { 
   
  var $scanner = null;  // instance of prolog-scanner 
  var $wimcode = null;  // instance of the root of the code-generator-tree 
   
  var $errors = null;   // array contains error-messages, only 
  var $warnings = null; // array contains warning-messages, only 
   
   
  function PrologParser(&$scanner) { 
    $this->scanner = &$scanner; 
    $this->wimcode = &new PrologCode($this); 
    $this->errors = array(); 
    $this->warnings = array(); 
  } // end of PrologParser 
   
   
   
  function addError($msg, $token = true, $line = true) { 
    $this->errors[] = $this->scanner->getInfo($token, $line) . $msg; 
  } // end of addError 
   
   
   
  function hasErrors() { 
    return (count($this->errors)); 
  } // end of hasErrors 
   
   
   
  function addWarning($msg, $token = true, $line = true) { 
    $this->warnings[] = $this->scanner->getInfo($token, $line) . $msg; 
  } // end of addWarning 
   
   
   
  function hasWarnings() { 
    return (count($this->warnings)); 
  } // end of hasWarnings 
   
   
   
  function tryNextToken() { 
    $this->scanner->nextToken(); 
     
    // skip any comments! 
    while ($this->scanner->token == PS_COMMENT) { 
      $this->scanner->nextToken(); 
    } 
     
    // catch first syntax error! 
    if ($this->scanner->token == PS_UNKNOWN) { 
      $this->addError("Unknown token."); 
    } 
     
    return (!$this->hasErrors()); // true, if no errors! 
  } // end of tryNextToken 
   
   
   
  // Code ::= Program Query 
  function parse() { // parse and compile: returns WimCodeString 
    if (!$this->tryNextToken()) return ""; // error occured! 
     
    $this->wimcode->setProgram($this->gProgram()); // parse the program 
    $this->wimcode->setQuery($this->gQuery()); // parse the query 
     
    if ($this->hasErrors()) return ""; // error occured! 
     
    $result = $this->wimcode->wimcode(); // COMPILE!!! 
     
    if ($this->hasErrors()) return ""; // error occured! 
    return $result; 
  } // end of parse 
   
   
   
  // Program ::= [Clause]+ 
  function gProgram() { 
    $result = &new PrologProgram($this); 
     
    while (true) { 
      if ($this->hasErrors()) return; // error occured! 
       
      $result->addClause($this->gClause()); // parse a clause 
       
      // check follow{Program} 
      if ($this->scanner->token == PS_QUERY) { 
        break; 
      } elseif ($this->scanner->token == PS_EOF) { 
        $this->addError("The end of file is reached," 
        . " but the query is missing.", false); 
        break; 
      } 
    } // end of while(true) 
     
    return $result; 
  } // end of gProgram 
   
   
   
  // Clause ::= Predicate [":-" Goals]? "." 
  function gClause() { 
    $result = &new PrologClause($this); 
     
    if ($this->hasErrors()) return; // error occured! 
     
    $result->setHead($this->gPredicate()); // parse the head of this clause 
     
    if ($this->scanner->token == PS_DIRECTIVE) { 
      if (!$this->tryNextToken()) return; // error occured! 
       
      $result->addGoals($this->gGoals()); // parse all goals of this clause 
       
      if ($this->hasErrors()) return; // error occured! 
    } 
     
    if ($this->scanner->token == PS_DOT) { 
      if (!$this->tryNextToken()) return; // error occured! 
    } else { 
      $this->addError("Bad clause - maybe, the terminator is missing.", false); 
    } 
    return $result; 
  } // end of gClause 
   
   
   
  // Predicate ::= WORD ["(" Terms ")"]? 
  function gPredicate() { 
    $result = &new PrologPredicate($this); 
     
    if ($this->hasErrors()) return; // error occured! 
     
    if ($this->scanner->token == PS_WORD) { 
      // WORD is a functor 
      $functor = $this->scanner->tokenValue; 
       
      if (!$this->tryNextToken()) return; // error occured! 
       
      if ($this->scanner->token == PS_LPAREN) { 
        if (!$this->tryNextToken()) return; // error occured! 
         
        // functor(term1, term2, ..., termN) 
        $result->setFunctor($functor, $this->gTerms()); // and parse the terms 
         
        if ($this->scanner->token == PS_RPAREN) { 
          if (!$this->tryNextToken()) return; // error occured! 
        } else { 
          $this->addError("Unclosed predicate happens. \")\" required."); 
        } 
      } else { 
        // only functor 
        $result->setFunctor($functor, $NULL); 
      } 
    } else { 
      $this->addError("Bad functor."); 
    } 
    return $result; 
  } // end of gPredicate 
   
   
   
  // Goals ::= Goal ["," Goal]* 
  function gGoals() { 
    $result = &new PrologGoals($this); 
     
    if ($this->hasErrors()) return; // error occured! 
     
    $result->addGoal($this->gGoal()); // parse the first goal 
     
    while ($this->scanner->token == PS_COMMA) { 
      if (!$this->tryNextToken()) return; // error occured! 
       
      $result->addGoal($this->gGoal()); // parse next goal 
    } 
    return $result; 
  } // end of gGoals 
   
   
   
  // Goal ::= "!" | "fail" | Predicate 
  function gGoal() { 
    $result = &new PrologGoal($this); 
     
    if ($this->scanner->token == PS_CUT) { 
      if (!$this->tryNextToken()) return; // error occured! 
       
      $result->setCut(); // "cut" happens! 
       
    } elseif ($this->scanner->token == PS_FAIL) { 
      if (!$this->tryNextToken()) return; // error occured! 
       
      $result->setFail(); // "fail" happens! 
       
    } else { 
       
      $result->setPredicate($this->gPredicate()); // parse the predicate 
       
      if ($this->hasErrors()) return; // error occured! 
    } 
     
    return $result; 
  } // end of gGoal 
   
   
   
  // Terms ::= Term ["," Term]* 
  function gTerms() { 
    $result = &new PrologTerms($this); 
     
    if ($this->hasErrors()) return; // error occured! 
     
    $result->addTerm($this->gTerm()); // parse the first term 
     
    while ($this->scanner->token == PS_COMMA) { 
      if (!$this->tryNextToken()) return; // error occured! 
       
      $result->addTerm($this->gTerm()); // parse next term 
    } 
    return $result; 
  } // end of gTerms 
   
   
   
  // Term ::= WORD ["(" Terms ")"]? | VARIABLE 
  function gTerm() { 
    $result = &new PrologTerm($this); 
     
    if ($this->hasErrors()) return; // error occured! 
     
    if ($this->scanner->token == PS_WORD) { 
      // WORD is atom or structure 
      $atomOrStructure = $this->scanner->tokenValue; 
       
      if (!$this->tryNextToken()) return; // error occured! 
       
      if ($this->scanner->token == PS_LPAREN) { 
        if (!$this->tryNextToken()) return; // error occured! 
         
        // structure(term1, term2, ..., termN) 
        $result->setStructure($atomOrStructure, $this->gTerms()); 
         
        if ($this->scanner->token == PS_RPAREN) { 
          if (!$this->tryNextToken()) return; // error occured! 
        } else { 
          $this->addError("Unclosed term happens. \")\" required."); 
        } 
      } else { 
        // atom 
        $result->setAtom($atomOrStructure); 
      } 
    } elseif ($this->scanner->token == PS_VARIABLE) { 
      // variable 
      $result->setVariable($this->scanner->tokenValue); 
      if (!$this->tryNextToken()) return; // error occured! 
    } else { 
      $this->addError("Bad term."); 
    } 
    return $result; 
  } // end of gTerm 
   
   
   
  // Query ::= "?-" Goals "." 
  function gQuery() { 
    $result = &new PrologQuery($this); 
     
    if ($this->hasErrors()) return; // error occured! 
     
    if ($this->scanner->token == PS_QUERY) { 
      if (!$this->tryNextToken()) return; // error occured! 
       
      $result->addGoals($this->gGoals()); // parse all goals of the query 
       
      if ($this->scanner->token == PS_DOT) { 
        if (!$this->tryNextToken()) return; // error occured! 
        // check follow{Query} 
        if ($this->scanner->token != PS_EOF) { 
          $this->addWarning("The rest after the query will be not parsed."); 
        } 
      } else { 
        $this->addWarning("The query has no termination." 
        . " \".\" required.", false); 
      } 
    } else { 
      $this->addError("The end of file is reached," 
      . " but the query is missing.", false); 
    } 
    return $result; 
  } // end of gQuery 
   
} // end of class PrologParser 

?>
