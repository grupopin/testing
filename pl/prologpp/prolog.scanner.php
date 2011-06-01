<?php 

/* INTERFACE 
 * 
 * class PrologScanner { 
 *   var $source = ""; 
 *   var $len = 0; 
 *   var $pos = 0; 
 *   var $line = 1; 
 *   var $token = PS_UNKNOWN; // PUBLIC 
 *   var $tokenValue = ""; // PUBLIC 
 *   function PrologScanner(&$source); // CONSTRUCTOR 
 *   function hasChar($count = 1); 
 *   function getChar($count = 1); 
 *   function backChar($count = 1); 
 *   function getInfo($token = true, $line = true); // PUBLIC 
 *   function nextToken(); // PUBLIC 
 *   function scanLineComment(); 
 *   function scanBlockComment(); 
 *   function scanDigitsAndLetters(); 
 * } 
 */ 

/* EXAMPLE 
 * 
 * <?php 
 * 
 *   include_once("prolog.scanner.php"); 
 * 
 *   $prolog_source_code = "isequal(X, X). ?- isequal(a, b)."; 
 * 
 *   $scanner = &new PrologScanner($prolog_source_code); 
 * 
 *   do { 
 * 
 *     $scanner->nextToken(); 
 *     echo $scanner->token . ": \"" . $scanner->tokenValue . "\"\n"; 
 * 
 *   } while ($scanner->token != PS_EOF); 
 * 
 * ?> 
 * 
 * OUTPUT 
 * 
 *   WORD: "isequal" 
 *   LPAREN: "(" 
 *   VARIABLE: "X" 
 *   COMMA: "," 
 *   VARIABLE: "X" 
 *   RPAREN: ")" 
 *   DOT: "." 
 *   QUERY: "?-" 
 *   WORD: "isequal" 
 *   LPAREN: "(" 
 *   WORD: "a" 
 *   COMMA: "," 
 *   WORD: "b" 
 *   RPAREN: ")" 
 *   DOT: "." 
 *   EOF: "" 
 * 
 */ 



define("PS_UNKNOWN", "UNKNOWN");        // unknown token! 
define("PS_EOF", "EOF");                // the end of file! 

define("PS_COMMENT", "COMMENT");        // e.g.: % ... | /* ... */ 
define("PS_WORD", "WORD");              // e.g.: lowercaseword | name 
define("PS_VARIABLE", "VARIABLE");      // e.g.: _var | X | Uppercaseword 

define("PS_QUERY", "QUERY");            // ?- 
define("PS_DIRECTIVE", "DIRECTIVE");    // :- 

define("PS_LPAREN", "LPAREN");          // ( 
define("PS_RPAREN", "RPAREN");          // ) 
define("PS_DOT", "DOT");                // . 
define("PS_COMMA", "COMMA");            // , 

define("PS_CUT", "CUT");                // ! 
define("PS_FAIL", "FAIL");              // fail 



class PrologScanner { 

  var $source = ""; 
  var $len = 0; 
  var $pos = 0; 
  var $line = 1; 
   
  var $token = PS_UNKNOWN; 
  var $tokenValue = ""; 
   
   
  function PrologScanner(&$source) { 
    $this->source = &$source; 
    $this->len = strlen($source); 
    $this->pos = 0; 
    $this->line = 1; 
    $this->token = PS_UNKNOWN; 
    $this->tokenValue = ""; 
  } // end of PrologScanner 
   
   
   
  function hasChar($count = 1) { 
    return ($this->pos + $count <= $this->len); 
  } // end of hasChar 
   
   
   
  function getChar($count = 1) { 
    $result = ""; 
    for ($i = 0; $i < $count; $i++) { 
      $result .= $this->source[$this->pos]; 
      $this->pos++; 
    } 
    $this->line += substr_count($result, "\n"); 
    return $result; 
  } // end of getChar 
   
   
   
  function backChar($count = 1) { 
    for ($i = 0; $i < $count; $i++) { 
      if ($this->pos > 0) { 
        $this->pos--; 
        if (($this->source[$this->pos] == "\n")) { 
          $this->line--; 
        } 
      } else { 
        break; 
      } 
    } 
  } // end of backChar 
   
   
   
  function getInfo($token = true, $line = true) { 
    $result = ""; 
    if ($line) { 
      $result .= "[line:" . $this->line . "]"; 
    } 
    if ($token) { 
      $result .= "[token:" . $this->token . "=\"" . $this->tokenValue . "\"]"; 
    } 
    if ($result) { 
      return $result . " "; 
    } else { 
      return $result; 
    } 
  } // end of getInfo 
   
   
   
  function nextToken() { 
    $this->token = PS_UNKNOWN; 
    $this->tokenValue = ""; 
    while ($this->hasChar()) { 
      $c = $this->getChar(); 
      $this->tokenValue = $c; 
      switch ($c) { 
        case "\n": case "\r": case "\t": case "\v": case "\0": case " ": 
          // skip 
          break; 
        case "(": 
          $this->token = PS_LPAREN; 
          return; 
        case ")": 
          $this->token = PS_RPAREN; 
          return; 
        case ".": 
          $this->token = PS_DOT; 
          return; 
        case ",": 
          $this->token = PS_COMMA; 
          return; 
        case "!": 
          $this->token = PS_CUT; 
          return; 
        case "?": 
          if ($this->hasChar()) { 
            $c2 = $this->getChar(); 
            $this->tokenValue .= $c2; 
            if ($c2 == "-") { 
              $this->token = PS_QUERY; 
            } 
          } 
          return; 
        case ":": 
          if ($this->hasChar()) { 
            $c2 = $this->getChar(); 
            $this->tokenValue .= $c2; 
            if ($c2 == "-") { 
              $this->token = PS_DIRECTIVE; 
            } 
          } 
          return; 
        case "%": 
          $this->backChar(); 
          $this->token = PS_COMMENT; 
          $this->tokenValue = $this->scanLineComment(); 
          return; 
        case "/": 
          if ($this->hasChar()) { 
            $c2 = $this->getChar(); 
            if ($c2 == "*") { 
              $this->backChar(2); 
              $this->token = PS_COMMENT; 
              $this->tokenValue = $this->scanBlockComment(); 
            } else { 
              $this->tokenValue .= $c2; 
            } 
          } 
          return; 
        default: 
          if (ereg("[a-z]", $c)) { 
            $this->backChar(); 
            $this->token = PS_WORD; 
            $this->tokenValue = $this->scanDigitsAndLetters(); 
          } elseif (ereg("_|[A-Z]", $c)) { 
            $this->backChar(); 
            $this->token = PS_VARIABLE; 
            $this->tokenValue = $this->scanDigitsAndLetters(); 
          } 
          return; 
      } 
    } 
    $this->token = PS_EOF; 
    $this->tokenValue = ""; 
    return; 
  } // end of nextToken 
   
   
   
  function scanLineComment() { 
    $result = $this->getChar(); // '%' required 
    while ($this->hasChar()) { 
      $c = $this->getChar(); 
      switch ($c) { 
        case "\r": 
          // skip 
          break; 
        case "\n": 
          $this->backChar(); 
          return $result; 
        default: 
          $result .= $c; 
      } 
    } 
    return $result; 
  } // end of scanLineComment 
   
   
   
  function scanBlockComment() { 
    $result = $this->getChar(2); // '/*' required 
    while ($this->hasChar()) { 
      $c = $this->getChar(); 
      $result .= $c; 
      if ($c == "*") { 
        if ($this->hasChar()) { 
          $c2 = $this->getChar(); 
          $result .= $c2; 
          if ($c2 == "/") { 
            return $result; 
          } 
        } 
      } 
    } 
    $this->token = PS_UNKNOWN; // unclosed block comment! 
    return $result; 
  } // end of scanBlockComment 
   
   
   
  function scanDigitsAndLetters() { 
    $result = $this->getChar(); // '_' or letter or digit required 
    while ($this->hasChar()) { 
      $c = $this->getChar(); 
      if (ereg("_|[a-z]|[A-Z]|[0-9]", $c)) { 
        $result .= $c; 
      } else { 
        $this->backChar(); 
        break; 
      } 
    } 
    if ($result == "fail") { 
      $this->token = PS_FAIL; 
    } 
    return $result; 
  } // end of scanDigitsAndLetters 
   
} // end of class PrologScanner 

?>

