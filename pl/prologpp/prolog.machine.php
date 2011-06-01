<?php 

/* INTERFACES 
 * 
 * class PMAtom { // (PRIVATE CLASS NESTED IN PrologMachine) 
 *   var $NAME = ""; 
 *   function PMAtom($name); // CONSTRUCTOR 
 * } 
 * 
 * class PMRef { // (PRIVATE CLASS NESTED IN PrologMachine) 
 *   var $REF = ""; 
 *   var $NAME = ""; 
 *   var $ID = PM_VAR_IDPREFIX; 
 *   function PMRef($ref, $name, $id); // CONSTRUCTOR 
 * } 
 * 
 * class PMStruct { // (PRIVATE CLASS NESTED IN PrologMachine) 
 *   var $NAME = ""; 
 *   var $ARITY = 0; 
 *   function PMStruct($name, $arity); // CONSTRUCTOR 
 * } 
 * 
 * class PrologMachine { 
 *   var $PS = null; 
 *   var $PC = 0; 
 *   var $PA = null; 
 *   var $ST = null; 
 *   var $SP = 0; 
 *   var $FP = 0; 
 *   var $BTP = 0; 
 *   var $H = null; 
 *   var $HP = 0; 
 *   var $TR = null; 
 *   var $TP = 0; 
 *   var $mode = PM_MODE_READ; 
 *   var $id = 100; 
 *   var $results = ""; // PUBLIC 
 *   function PrologMachine(&$wimcode); // CONSTRUCTOR 
 *   function loadProgram(&$wimcode); 
 *   function execute(); // PUBLIC 
 *   function executeInstruction($instruction); 
 *   function newAtom($atom); 
 *   function newRef($ref, $name); 
 *   function newStruct($name, $arity, $hps); 
 *   function isAtom($a); 
 *   function isRef($a); 
 *   function isStruct($a); 
 *   function deref($a); 
 *   function trail($a); 
 *   function backtrack(); 
 *   function reset($tpu, $tpo); 
 *   function unify($a1, $a2); 
 *   function out($string); 
 *   function outResult(); 
 *   function outQueryVar($v); 
 *   function outStruct($s); 
 * } 
 */ 

/* EXAMPLE 
 * 
 * <?php 
 * 
 *   include_once("prolog.scanner.php"); 
 *   include_once("prolog.parser.php"); 
 *   include_once("prolog.machine.php"); 
 * 
 *   $prolog_source_code = "isequal(X, X). ?- isequal(a, b)."; 
 * 
 *   $scanner = &new PrologScanner($prolog_source_code); 
 *   $parser = &new PrologParser($scanner); 
 * 
 *   $wimcode = $parser->parse(); // compile inclusive! 
 * 
 *   if (!$parser->hasErrors()) { 
 * 
 *     $machine = &new PrologMachine($wimcode); 
 *     $results = $machine->execute(); 
 *     echo $results; 
 * 
 *   } 
 * 
 * ?> 
 * 
 * OUTPUT 
 * 
 *   No 
 * 
 */ 



include_once("prolog.generator.php"); // use constants, only 



define("PM_PC_EXIT", "program exit!"); 

define("PM_MODE_READ", "read"); 
define("PM_MODE_WRITE", "write"); 

define("PM_VAR_IDPREFIX", "_G"); 

define("PM_STACK_LIMIT", 2000); 
define("PM_HEAP_LIMIT", 1000); 

define("PM_OUT_ERROR_STACKOVERFLOW", "\n\nERROR: STACK OVERFLOW!\n\n"); 
define("PM_OUT_ERROR_HEAPOVERFLOW", "\n\nERROR: HEAP OVERFLOW!\n\n"); 

define("PM_OUT_YES", "Yes"); 
define("PM_OUT_NO", "No"); 
define("PM_OUT_EQUAL", " = "); 
define("PM_OUT_LPAREN", "("); 
define("PM_OUT_RPAREN", ")"); 
define("PM_OUT_COMMA", ", "); 
define("PM_OUT_NL", "\n"); 
define("PM_OUT_TERMINATOR", " ;\n\n"); 



class PMAtom { 
   
  var $NAME = ""; 
   
   
  function PMAtom($name) { 
    $this->NAME = $name; 
  } // end of PMAtom 
   
} // end of class PMAtom 



class PMRef { 
   
  var $REF = ""; 
  var $NAME = ""; 
  var $ID = PM_VAR_IDPREFIX; 
   
   
  function PMRef($ref, $name, $id) { 
    $this->REF = $ref; 
    $this->NAME = $name; 
    $this->ID = PM_VAR_IDPREFIX . $id; 
  } // end of PMRef 
   
} // end of class PMRef 



class PMStruct { 
   
  var $NAME = ""; 
  var $ARITY = 0; 
   
   
  function PMStruct($name, $arity) { 
    $this->NAME = $name; 
    $this->ARITY = $arity; 
  } // end of PMStruct 
   
} // end of class PMStruct 



class PrologMachine { 
   
  var $PS = null;           // program instructions 
  var $PC = 0;              // program counter 
  var $PA = null;           // addresses of labeled program instructions 
   
  var $ST = null;           // stack 
  var $SP = 0;              // stack pointer 
  var $FP = 0;              // frame pointer 
  var $BTP = 0;             // backtrack pointer 
   
  var $H = null;            // heap 
  var $HP = 0;              // heap pointer 
   
  var $TR = null;           // trail stack 
  var $TP = 0;              // trail pointer 
   
  var $mode = PM_MODE_READ; // unification mode (read or write) 
   
  var $id = 100;            // loop-id to create a var-id 
  var $results = "";        // output-string 
   
   
  function PrologMachine(&$wimcode) { 
    // program 
    $this->PS = array(); 
    $this->PC = 0; 
    $this->PA = array(); 
    $this->loadProgram($wimcode); 
    // stack 
    $this->ST = array(); 
    $this->SP = 0; 
    $this->FP = 0; 
    $this->BTP = 0; 
    // heap 
    $this->H = array(); 
    $this->HP = 0; 
    // trail 
    $this->TR = array(); 
    $this->TP = 0; 
    // mode 
    $this->mode = PM_MODE_READ; 
    // result 
    $this->id = 100; 
    $this->results = ""; 
  } // end of PrologMachine 
   
   
   
  function loadProgram(&$wimcode) { 
    $lines = explode(PG_INSTRUCTION_ENDING, $wimcode); 
    $address = 0; 
    foreach ($lines as $line) { 
      $line = trim($line); // remove all unnecessary white spaces 
      if ($line) { 
        // commandLine = label + instruction 
        $commandLine = explode(PG_LABEL_ENDING, $line); 
        switch (count($commandLine)) { 
          case 1: 
            $label = ""; 
            $instruction = trim($commandLine[0]); 
            break; 
          case 2: 
            $label = trim($commandLine[0]); 
            $instruction = trim($commandLine[1]); 
            break; 
        } 
        $this->PS[$address] = $instruction; 
        if ($label) { 
          // label = clause + number 
          list($clause, $number) = explode(PG_LABEL_SEP, $label); 
          $this->PA[$clause][$number] = $address; 
        } 
        $address++; 
      } // end of if 
    } 
  } // end of loadProgram 
   
   
   
  function execute() { 
    do { 
      $this->id++; 
       
      $instruction = explode(" ", $this->PS[$this->PC]); 
      $this->PC++; 
      $hasnext = $this->executeInstruction($instruction); 
       
    } while ($hasnext); 
     
    $this->out(PM_OUT_NO); // LAST RESULT IS "No"  ->  display("No") 
     
    return $this->results; 
  } // end of execute 
   
   
   
  function executeInstruction($instruction) { 
    $stop = false; 
     
    switch ($instruction[0]) { 
       
       
      // init 
      case PG_OP_INIT: 
         
        $this->FP = 1;             // in frame of query (the first frame) 
        $this->BTP = 1; 
        $this->TP = -1;            // empty trail stack 
        $this->HP = 0;             // empty heap stack 
        $this->ST[0] = PM_PC_EXIT; // pos. program address 
        $this->ST[1] = $this->FP; 
        $this->ST[2] = $this->BTP; 
        $this->ST[3] = $this->TP; 
        $this->ST[4] = $this->HP; 
        $this->ST[5] = PM_PC_EXIT; // neg. program address 
        $this->SP = 5; 
         
        break; // end of init 
       
       
      // halt 
      case PG_OP_HALT: 
         
        $this->outResult(); // display the CURRENT RESULT !!! 
         
        if ($this->BTP > $this->FP) { // maybe, there is another (next) result! 
          $this->backtrack(); 
        } else { 
          $stop = true; 
        } 
         
        break; // end of halt 
       
       
      // pushenv k 
      case PG_OP_PUSHENV: 
         
        $k = $instruction[1]; 
        for ($i = $this->SP + 1; $i <= $this->FP + $k; $i++) { 
          $this->ST[$i] = "debug.pushenv." . $k; // better debugging, only 
        } 
        $this->SP = $this->FP + $k; 
         
        break; // end of pushenv 
       
       
      // pusharg i 
      case PG_OP_PUSHARG: 
         
        $i = $instruction[1]; 
        $this->SP++; 
        $this->ST[$this->SP] = $this->ST[$this->FP + 4 + $i]; 
         
        break; // end of pusharg 
       
       
      // setbtp l 
      case PG_OP_SETBTP: 
         
        $l = $instruction[1]; 
        $this->ST[$this->FP + 1] = $this->BTP; 
        $this->ST[$this->FP + 2] = $this->TP; 
        $this->ST[$this->FP + 3] = $this->HP; 
        list($clause, $number) = explode(PG_LABEL_SEP, $l); 
        $this->ST[$this->FP + 4] = $this->PA[$clause][$number]; 
        $this->BTP = $this->FP; 
         
        break; // end of setbtp 
       
       
      // nextalt l 
      case PG_OP_NEXTALT: 
         
        $l = $instruction[1]; 
        list($clause, $number) = explode(PG_LABEL_SEP, $l); 
        $this->ST[$this->FP + 4] = $this->PA[$clause][$number]; 
         
        break; // end of nextalt 
       
       
      // delbtp 
      case PG_OP_DELBTP: 
         
        $this->BTP = $this->ST[$this->FP + 1]; 
         
        break; // end of delbtp 
       
       
      // popenv 
      case PG_OP_POPENV: 
         
        if ($this->FP > $this->BTP) { 
          $this->SP = $this->FP - 2; 
        } 
        $this->PC = $this->ST[$this->FP - 1]; 
        $this->FP = $this->ST[$this->FP]; 
         
        break; // end of popenv 
       
       
      // restore 
      case PG_OP_RESTORE: 
         
        $this->PC = $this->ST[$this->FP - 1]; 
        $this->FP = $this->ST[$this->FP]; 
         
        break; // end of restore 
       
       
      // enter 
      case PG_OP_ENTER: 
         
        $this->ST[$this->SP + 1] = "debug.enter.pos.prog.addr."; 
        $this->ST[$this->SP + 2] = $this->FP; 
        $this->ST[$this->SP + 3] = "debug.enter.btp"; 
        $this->ST[$this->SP + 4] = "debug.enter.tp"; 
        $this->ST[$this->SP + 5] = "debug.enter.hp"; 
        $this->ST[$this->SP + 6] = "debug.enter.neg.prog.addr."; 
        $this->SP = $this->SP + 6; 
         
        break; // end of enter 
       
       
      // call p/n 
      case PG_OP_CALL: 
         
        list($p, $n) = explode(PG_SIG_SEP, $instruction[1]); 
        $this->FP = $this->SP - ($n + 4); 
        $this->ST[$this->FP - 1] = $this->PC; 
        $this->PC = $this->PA[$p][1]; // procedure code (first clause) 
         
        break; // end of call 
       
       
      // uatom a 
      case PG_OP_UATOM: 
         
        $a = $instruction[1]; 
         
        switch ($this->mode) { 
           
          // read mode 
          case PM_MODE_READ: 
            $v = $this->deref($this->ST[$this->SP]); 
            $this->SP--; 
            // case (ATOM:a) 
            if (($this->isAtom($v)) && ($this->H[$v]->NAME == $a)) { 
              // do nothing 
              break; 
            } 
            // case (REF:-) 
            if (($this->isRef($v)) && ($v == $this->H[$v]->REF)) { 
              // bind ATOM:a to REF:v 
              $this->H[$v]->REF = $this->newAtom($a); 
              $this->trail($v); 
              break; 
            } 
            // else 
            $this->backtrack(); // fail! 
            break; 
           
          // write mode 
          case PM_MODE_WRITE: 
            $this->H[$this->ST[$this->SP]] = $this->newAtom($a); 
            $this->SP--; 
            break; 
        } 
         
        break; // end of uatom 
       
       
      // uvar i 
      case PG_OP_UVAR: 
         
        $i = $instruction[1]; 
         
        switch ($this->mode) { 
           
          // read mode 
          case PM_MODE_READ: 
            $this->ST[$this->FP + $i] = $this->deref($this->ST[$this->SP]); 
            break; 
           
          // write mode 
          case PM_MODE_WRITE: 
            $this->ST[$this->FP + $i] = $this->newRef($this->HP, ""); 
            $this->H[$this->ST[$this->SP]] = $this->ST[$this->FP + $i]; 
            break; 
        } 
        $this->SP--; 
         
        break; // end of uvar 
       
       
      // uref i 
      case PG_OP_UREF: 
         
        $i = $instruction[1]; 
         
        switch ($this->mode) { 
           
          // read mode 
          case PM_MODE_READ: 
            $this->unify($this->ST[$this->SP], $this->ST[$this->FP + $i]); 
            break; 
           
          // write mode 
          case PM_MODE_WRITE: 
            $this->H[$this->ST[$this->SP]] = $this->ST[$this->FP + $i]; 
            break; 
        } 
        $this->SP--; 
         
        break; // end of uref 
       
       
      // ustruct f/n 
      case PG_OP_USTRUCT: 
         
        list($f, $n) = explode(PG_SIG_SEP, $instruction[1]); 
        $nils = array(); 
        for ($i = 0; $i < $n; $i++) { 
          $nils[] = "debug.nil.pointer"; 
        } 
         
        switch ($this->mode) { 
           
          // read mode 
          case PM_MODE_READ: 
            $v = $this->deref($this->ST[$this->SP]); 
            // case (STRUCT:f/n) 
            if (($this->isStruct($v)) && ($this->H[$v]->NAME == $f) 
                && ($this->H[$v]->ARITY == $n)) { 
              $this->ST[$this->SP] = $v; 
              break; 
            } 
            // case (REF:-) 
            if (($this->isRef($v)) && ($v == $this->H[$v]->REF)) { 
              $this->ST[$this->SP] = $this->mode; 
              $this->SP++; 
              $hp = $this->newStruct($f, $n, $nils); // empty STRUCT! 
              $this->ST[$this->SP] = $hp; 
              // bind STRUCT:f/n to REF:v 
              $this->H[$v]->REF = $hp; 
              $this->trail($v); 
              $this->mode = PM_MODE_WRITE; // to fill the empty STRUCT 
              break; 
            } 
            // else 
            $this->backtrack(); // fail! 
            break; 
           
          // write mode 
          case PM_MODE_WRITE: 
            $hp = $this->newStruct($f, $n, $nils); // empty STRUCT! 
            $this->ST[$this->SP + 1] = $hp; 
            $this->H[$this->ST[$this->SP]] = $hp; 
            $this->ST[$this->SP] = $this->mode; 
            $this->SP++; 
            break; 
        } 
         
        break; // end of ustruct 
       
       
      // down 
      case PG_OP_DOWN: 
         
        switch ($this->mode) { 
           
          // read mode 
          case PM_MODE_READ: 
            $this->ST[$this->SP + 1] = $this->H[$this->ST[$this->SP] + 1]; 
            break; 
           
          // write mode 
          case PM_MODE_WRITE: 
            $this->ST[$this->SP + 1] = $this->ST[$this->SP] + 1; 
            break; 
        } 
        $this->SP++; 
         
        break; // end of down 
       
       
      // brother i 
      case PG_OP_BROTHER: 
         
        $i = $instruction[1]; 
         
        switch ($this->mode) { 
           
          // read mode 
          case PM_MODE_READ: 
            $this->ST[$this->SP + 1] = $this->H[$this->ST[$this->SP] + $i]; 
            break; 
           
          // write mode 
          case PM_MODE_WRITE: 
            $this->ST[$this->SP + 1] = $this->ST[$this->SP] + $i; 
            break; 
        } 
        $this->SP++; 
         
        break; // end of brother 
       
       
      // up 
      case PG_OP_UP: 
         
        $this->SP--; 
        if ($this->mode == PM_MODE_WRITE) { 
          $this->mode = $this->ST[$this->SP]; 
          $this->SP--; 
        } 
         
        break; // end of up 
       
       
      // putatom a 
      case PG_OP_PUTATOM: 
         
        $a = $instruction[1]; 
         
        $this->SP++; 
        $this->ST[$this->SP] = $this->newAtom($a); 
         
        break; // end of putatom 
       
       
      // putvar i v 
      case PG_OP_PUTVAR: 
         
        $i = $instruction[1]; 
        $v = $instruction[2]; // varname 
         
        $this->SP++; 
        $this->ST[$this->FP + $i] = $this->newRef($this->HP, $v); 
        $this->ST[$this->SP] = $this->ST[$this->FP + $i]; 
         
        break; // end of putvar 
       
       
      // putref i 
      case PG_OP_PUTREF: 
         
        $i = $instruction[1]; 
         
        $this->SP++; 
        $this->ST[$this->SP] = $this->ST[$this->FP + $i]; 
         
        break; // end of putref 
       
       
      // putstruct f/n 
      case PG_OP_PUTSTRUCT: 
         
        list($f, $n) = explode(PG_SIG_SEP, $instruction[1]); 
         
        $this->SP = $this->SP - $n +1; 
        $hps = array(); 
        for ($i = $this->SP; $i < $this->SP + $n; $i++) { 
          $hps[] = $this->ST[$i]; 
        } 
        $this->ST[$this->SP] = $this->newStruct($f, $n, $hps); 
         
        break; // end of putstruct 
       
       
      // cut 
      case PG_OP_CUT: 
         
        // cut-operation deletes backtrack pointer, only :-) 
        $this->BTP = $this->ST[$this->FP + 1]; 
         
        break; // end of cut 
       
       
      // fail 
      case PG_OP_FAIL: 
         
        // fail-operation is a backtrack, only :-) 
        $this->backtrack(); 
         
        break; // end of fail 
       
       
      default: // unknown instruction, but impossible 
        $stop = true; 
       
    } // end of switch($instruction[0]) 
     
    // check stack overflow 
    if ($this->SP >= PM_STACK_LIMIT) { 
      $stop = true; 
      $this->out(PM_OUT_ERROR_STACKOVERFLOW); 
    } 
     
    // check maximum of heap objects 
    if ($this->HP >= PM_HEAP_LIMIT) { 
      $stop = true; 
      $this->out(PM_OUT_ERROR_HEAPOVERFLOW); 
    } 
     
    return ((!$stop) & ($this->PC != $this->ST[5])); 
  } // end of executeInstruction 
   
   
   
  function newAtom($atom) { 
    array_push($this->H, new PMAtom($atom)); 
    $this->HP++; 
    return ($this->HP - 1); 
  } // end of newAtom 
   
   
   
  function newRef($ref, $name) { 
    if ($this->FP != 1) { // variables of query will be get a name, only 
      $name = ""; 
    } 
    array_push($this->H, new PMRef($ref, $name, $this->id)); 
    $this->HP++; 
    return ($this->HP - 1); 
  } // end of newRef 
   
   
   
  function newStruct($name, $arity, $hps) { 
    if (!is_array($hps)) { 
      $hps = array(); 
    } 
    $result = $this->HP; 
    array_push($this->H, new PMStruct($name, $arity)); 
    $this->HP++; 
    for ($i = 0; $i < count($hps); $i++) { 
      array_push($this->H, $hps[$i]); 
      $this->HP++; 
    } 
    return $result; 
  } // end of newStruct 
   
   
   
  function isAtom($a) { 
    return ((is_object($this->H[$a])) 
            && (strtolower(get_class($this->H[$a])) == "pmatom")); 
  } // end of isAtom 
   
   
   
  function isRef($a) { 
    return ((is_object($this->H[$a])) 
            && (strtolower(get_class($this->H[$a])) == "pmref")); 
  } // end of isRef 
   
   
   
  function isStruct($a) { 
    return ((is_object($this->H[$a])) 
            && (strtolower(get_class($this->H[$a])) == "pmstruct")); 
  } // end of isStruct 
   
   
   
  function deref($a) { 
    if ($this->isRef($a)) { 
      $b = $this->H[$a]->REF; 
      if ($a == $b) { 
        return $a; 
      } else { 
        return $this->deref($b); 
      } 
    } else { 
      return $a; 
    } 
  } // end of deref 
   
   
   
  function trail($a) { 
    if ($a < $this->ST[$this->BTP + 3]) { 
      $this->TP++; 
      $this->TR[$this->TP] = $a; 
    } 
  } // end of trail 
   
   
   
  function backtrack() { 
    $this->FP = $this->BTP; 
    $this->HP = $this->ST[$this->FP + 3]; 
    // delete old heap objects, but not needed 
    while ((count($this->H)) && (count($this->H) > $this->HP)) { 
      array_pop($this->H); 
    } 
    $this->reset($this->ST[$this->FP + 2], $this->TP); 
    $this->TP = $this->ST[$this->FP + 2]; 
    $this->PC = $this->ST[$this->FP + 4]; 
  } // end of backtrack 
   
   
   
  function reset($tpu, $tpo) { // used in backtrack, only 
    for ($i = $tpo; $i > $tpu; $i--) { 
      if ($this->isRef($this->TR[$i])) { 
        $this->H[$this->TR[$i]]->REF = $this->TR[$i]; 
      } 
    } 
  } // end of reset 
   
   
   
  function unify($a1, $a2) { 
    $pdl = array(); // local pushdownlist 
    array_push($pdl, $a1); 
    array_push($pdl, $a2); 
     
    while (count($pdl) > 0) { 
      $d1 = $this->deref(array_pop($pdl)); 
      $d2 = $this->deref(array_pop($pdl)); 
       
      if ($d1 != $d2) { 
        if ($this->isRef($d1)) { 
          // bind *:d2 to REF:d1 
          $this->H[$d1]->REF = $d2; 
          $this->trail($d1); 
        } elseif ($this->isRef($d2)) { 
          // bind *:d1 to REF:d2 
          $this->H[$d2]->REF = $d1; 
          $this->trail($d2); 
        } elseif (($this->isStruct($d1)) & ($this->isStruct($d2))) { 
          if (($this->H[$d1]->NAME == $this->H[$d2]->NAME) 
              & ($this->H[$d1]->ARITY == $this->H[$d2]->ARITY)) { 
            for ($i = 1; $i <= $this->H[$d1]->ARITY; $i++) { 
              array_push($pdl, $this->H[$d1 + $i]); 
              array_push($pdl, $this->H[$d2 + $i]); 
            } 
          } else { 
            $this->backtrack(); // fail! 
            return; 
          } 
        } elseif (($this->isAtom($d1)) & ($this->isAtom($d2)) 
                  & ($this->H[$d1]->NAME == $this->H[$d2]->NAME)) { 
          // do nothing 
        } else { 
          $this->backtrack(); // fail! 
          return; 
        } 
      } // end of if($d1!=$d2) 
    } // end of while 
  } // end of unify 
   
   
   
  /* OUTPUT METHODS -> out the results of this machine */ 
   
  function out($string) { 
    $this->results .= $string; 
  } // end of out 
   
   
   
  function outResult() { // used in case of GP_OP_HALT, only 
    // fetch all query variables 
    $vars = array(); 
    for ($i = 0; $i < $this->HP; $i++) { 
      if (($this->isRef($i)) && ($this->H[$i]->NAME)) { 
        $vars[] = $i; 
      } 
    } 
    if (count($vars)) { // there are(is a) query variable(s) 
      for ($i = 0; $i < count($vars); $i++) { 
        if ($i > 0) { 
          $this->out(PM_OUT_NL); 
        } 
        $this->outQueryVar($vars[$i]); 
      } 
    } else { // there are no query variables ->  display("Yes") 
      $this->out(PM_OUT_YES); 
    } 
    $this->out(PM_OUT_TERMINATOR); 
  } // end of outResult 
   
   
   
  function outQueryVar($v) { // used in outResult, only 
    $this->out($this->H[$v]->NAME . PM_OUT_EQUAL); 
    $d = $this->deref($v); 
    if ($this->isRef($d)) { // d is (same or other) ref! 
      $this->out($this->H[$d]->ID); 
    } elseif ($this->isAtom($d)) { 
      $this->out($this->H[$d]->NAME); 
    } elseif ($this->isStruct($d)) { 
      $this->outStruct($d); 
    } // else unknown, but impossible 
  } // end of outQueryVar 
   
   
   
  function outStruct($s) { // used in outQueryVar and outStruct, only 
    $this->out($this->H[$s]->NAME); 
    $this->out(PM_OUT_LPAREN); 
    $n = $this->H[$s]->ARITY; 
    for ($i = 1; $i <= $n; $i++) { 
      if ($i > 1) { 
        $this->out(PM_OUT_COMMA); 
      } 
      $d = $this->deref($this->H[$s + $i]); 
      if ($this->isRef($d)) { 
        $this->out($this->H[$d]->ID); 
      } elseif ($this->isAtom($d)) { 
        $this->out($this->H[$d]->NAME); 
      } elseif ($this->isStruct($d)) { 
        $this->outStruct($d); 
      } // else unknown, but impossible 
    } 
    $this->out(PM_OUT_RPAREN); 
  } // end of outStruct 
   
} // end of class PrologMachine 

?>
