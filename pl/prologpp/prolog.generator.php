<?php 

/* INTERFACES 
 * 
 * class PrologGenerator { 
 *   var $parser = null; 
 *   function PrologGenerator(&$parser); 
 * } 
 *  
 * class PrologCode extends PrologGenerator { 
 *   var $program = null; 
 *   var $query = null; 
 *   function PrologCode(&$parser); 
 *   function setProgram(&$program); 
 *   function setQuery(&$query); 
 *   function wimcode(); // PUBLIC 
 * } 
 *  
 * class PrologProgram extends PrologGenerator { 
 *   var $procedures = null; 
 *   function PrologProgram(&$parser); 
 *   function addClause(&$clause); 
 *   function code_P(); 
 * } 
 *  
 * class PrologProcedure extends PrologGenerator { 
 *   var $clauses = null; 
 *   function PrologProcedure(&$parser); 
 *   function addClause(&$clause); 
 *   function compareSignature(&$clause); 
 *   function code_PR(); 
 * } 
 *  
 * class PrologClause extends PrologGenerator { 
 *   var $head = null; 
 *   var $goals = null; 
 *   function PrologClause(&$parser); 
 *   function setHead(&$predicate); 
 *   function addGoals(&$goals); 
 *   function getClauseLabel($index, $ending = PG_LABEL_ENDING); 
 *   function pickVariables(&$vars, $local); 
 *   function code_C($btparam, $index); 
 * } 
 *  
 * class PrologPredicate extends PrologGenerator { 
 *   var $functor = ""; 
 *   var $terms = null; 
 *   function PrologPredicate(&$parser); 
 *   function setFunctor(&$functor, &$terms); 
 *   function compareSignature(&$predicate); 
 *   function pickVariables(&$vars, $local); 
 * } 
 *  
 * class PrologGoals extends PrologGenerator { 
 *   var $goals = null; 
 *   function PrologGoals(&$parser); 
 *   function addGoal(&$goal); 
 *   function pickVariables(&$vars, $local); 
 * } 
 *  
 * class PrologGoal extends PrologGenerator { 
 *   var $type = ""; 
 *   var $predicate = null; 
 *   function PrologGoal(&$parser); 
 *   function setCut(); 
 *   function setFail(); 
 *   function setPredicate(&$predicate); 
 *   function pickVariables(&$vars, $local); 
 *   function code_G(&$vars, $local); 
 * } 
 *  
 * class PrologTerms extends PrologGenerator { 
 *   var $terms = null; 
 *   function PrologTerms(&$parser); 
 *   function addTerm(&$term); 
 *   function pickVariables(&$vars, $local); 
 * } 
 *  
 * class PrologTerm extends PrologGenerator { 
 *   var $term = ""; 
 *   var $type = ""; 
 *   var $terms = null; 
 *   function PrologTerm(&$parser); 
 *   function setAtom(&$atom); 
 *   function setVariable(&$variable); 
 *   function setStructure(&$structure, &$terms); 
 *   function pickVariables(&$vars, $local); 
 *   function code_A(&$vars, $local); 
 *   function code_U(&$vars, $local); 
 * } 
 *  
 * class PrologQuery extends PrologGenerator { 
 *   var $goals = null; 
 *   function PrologQuery(&$parser); 
 *   function addGoals(&$goals); 
 *   function code_Q(); 
 * } 
 */ 



define("PG_TERMTYPE_ATOM", "atom"); 
define("PG_TERMTYPE_VARIABLE", "variable"); 
define("PG_TERMTYPE_STRUCTURE", "structure"); 

define("PG_GOALTYPE_CUT", "cut"); 
define("PG_GOALTYPE_FAIL", "fail"); 
define("PG_GOALTYPE_PREDICATE", "predicate"); 

define("PG_OP_INIT", "init"); 
define("PG_OP_HALT", "halt"); 
define("PG_OP_PUSHENV", "pushenv"); 
define("PG_OP_PUSHARG", "pusharg"); 
define("PG_OP_SETBTP", "setbtp"); 
define("PG_OP_NEXTALT", "nextalt"); 
define("PG_OP_DELBTP", "delbtp"); 
define("PG_OP_POPENV", "popenv"); 
define("PG_OP_RESTORE", "restore"); 
define("PG_OP_ENTER", "enter"); 
define("PG_OP_CALL", "call"); 
define("PG_OP_UATOM", "uatom"); 
define("PG_OP_UVAR", "uvar"); 
define("PG_OP_UREF", "uref"); 
define("PG_OP_USTRUCT", "ustruct"); 
define("PG_OP_DOWN", "down"); 
define("PG_OP_BROTHER", "brother"); 
define("PG_OP_UP", "up"); 
define("PG_OP_PUTATOM", "putatom"); 
define("PG_OP_PUTVAR", "putvar"); 
define("PG_OP_PUTREF", "putref"); 
define("PG_OP_PUTSTRUCT", "putstruct"); 
define("PG_OP_CUT", "cut"); // cut-opcode! 
define("PG_OP_FAIL", "fail"); // fail-opcode! 

define("PG_BT_SINGLE", "single"); 
define("PG_BT_FIRST", "first"); 
define("PG_BT_MIDDLE", "middle"); 
define("PG_BT_LAST", "last"); 

define("PG_INSTRUCTION_ENDING", "\n"); 
define("PG_LABEL_ENDING", ": "); 
define("PG_LABEL_SEP", "\$"); 
define("PG_SIG_SEP", "/"); 

define("PG_ENV_I", "local pointer"); 
define("PG_ENV_ISVAR", "is variable"); 



class PrologGenerator { // abstract base class 
   
  var $parser = null; // errors and warnings will be added to the parser 
   
   
  function PrologGenerator(&$parser) { 
    $this->parser = &$parser; 
  } // end of PrologGenerator 
   
} // end of class PrologGenerator 



class PrologCode extends PrologGenerator { // the root of code-generator-tree 
   
  var $program = null; 
  var $query = null; 
   
   
  function PrologCode(&$parser) { 
    $this->PrologGenerator($parser); 
    _pg_output_init(); 
    $this->program = null; 
    $this->query = null; 
  } // end of PrologCode 
   
   
   
  function setProgram(&$program) { 
    $this->program = &$program; 
  } // end of setProgram 
   
   
   
  function setQuery(&$query) { 
    $this->query = &$query; 
  } // end of setQuery 
   
   
   
  /* wimcode p q = code_Q q; 
   *               code_P p 
   */ 
  function wimcode() { 
    $result = $this->query->code_Q(); 
    $result .= $this->program->code_P(); 
    return $result; 
  } // end of wimcode 
   
} // end of class PrologCode 



class PrologProgram extends PrologGenerator { 
   
  var $procedures = null; 
   
   
  function PrologProgram(&$parser) { 
    $this->PrologGenerator($parser); 
    $this->procedures = array(); 
  } // end of PrologProgram 
   
   
   
  function addClause(&$clause) { 
    if ($clause) { 
      for ($i = 0; $i < count($this->procedures); $i++) { 
        if ($this->procedures[$i]->compareSignature($clause)) { 
          $this->procedures[$i]->addClause($clause); 
          return; 
        } 
      } 
      // there is no procedure with same signature -> create new procedure 
      $procedure = &new PrologProcedure($this->parser); 
      $procedure->addClause($clause); 
      $this->procedures[] = &$procedure; 
    } 
  } // end of addClause 
   
   
   
  /* code_P p1,...,pn = code_PR p1; 
   *                    ... 
   *                    code_PR pn 
   */ 
  function code_P() { 
    $result = ""; 
    for ($i = 0; $i < count($this->procedures); $i++) { 
      $result .= $this->procedures[$i]->code_PR(); 
    } 
    return $result; 
  } // end of code_P 
   
} // end of class PrologProgram 



class PrologProcedure extends PrologGenerator { // not used directly in parser 
   
  var $clauses = null; 
   
   
  function PrologProcedure(&$parser) { 
    $this->PrologGenerator($parser); 
    $this->clauses = array(); 
  } // end of PrologProcedure 
   
   
   
  function addClause(&$clause) { 
    if ($clause) { 
      $this->clauses[] = &$clause; 
      _pg_output_update($clause->getClauseLabel(count($this->clauses) - 1)); 
    } 
  } // end of addClause 
   
   
   
  function compareSignature(&$clause) { 
    if ((!count($this->clauses)) && (!$this->clauses[0]->head) 
       && ((!$clause) && (!$clause->head))) { 
      return false; 
    } else { 
      if ($clause->head) { 
        return $clause->head->compareSignature($this->clauses[0]->head); 
      } else { 
        return false; 
      } 
    } 
  } // end of compareSignature 
   
   
   
  /* code_PR c1,c2,...,cn-1,cn = code_C c1 first index(c1); 
   *                             code_C c2 middle index(c2); 
   *                             ... 
   *                             code_C cn-1 middle index(cn-1); 
   *                             code_C cn last index(cn) 
   * code_PR c                 = code_C c single index(c) 
   */ 
  function code_PR() { 
    $result = ""; 
    $count = count($this->clauses); 
    if ($count == 1) { 
      $result .= $this->clauses[0]->code_C(PG_BT_SINGLE, 0); 
    } elseif ($count > 1) { 
      $result .= $this->clauses[0]->code_C(PG_BT_FIRST, 0); 
      for ($i = 1; $i < ($count - 1); $i++) { 
        $result .= $this->clauses[$i]->code_C(PG_BT_MIDDLE, $i); 
      } 
      $result .= $this->clauses[$count - 1]->code_C(PG_BT_LAST, $count - 1); 
    } // else empty procedure, but impossible 
    return $result; 
  } // end of code_PR 
   
} // end of class PrologProcedure 



class PrologClause extends PrologGenerator { 
   
  var $head = null; 
  var $goals = null; 
   
   
  function PrologClause(&$parser) { 
    $this->PrologGenerator($parser); 
    $this->head = null; 
    $this->goals = null; 
  } // end of PrologClause 
   
   
   
  function setHead(&$predicate) { 
    $this->head = &$predicate; // 'null' forbidden 
  } // end of setHead 
   
   
   
  function addGoals(&$goals) { 
    $this->goals = &$goals; // 'null' is possible, but no failure 
  } // end of addGoals 
   
   
   
  function getClauseLabel($index, $ending = PG_LABEL_ENDING) { 
    return $this->head->functor . PG_LABEL_SEP . ($index + 1) . $ending; 
  } // end of getClauseLabel 
   
   
   
  function pickVariables(&$vars, $local) { 
    $this->head->pickVariables($vars, $local); 
    if ($this->goals) { 
      $this->goals->pickVariables($vars, $local); 
    } 
  } // end of pickVariables 
   
   
   
  /* code_C (c : p(t1,t2,...,tn) <- g1,...,gm) btparam index = 
   *   label(c): pushenv n + r + 4; 
   *             btinit btparam btnext(index); 
   *             pusharg 1; 
   *             code_U t1 (v,l); 
   *             pusharg 2; 
   *             code_U t2 (v,l); 
   *             ... 
   *             pusharg n; 
   *             code_U tn (v,l); 
   *             code_G g1 (v,l); 
   *             ... 
   *             code_G gm (v,l); 
   *             fin btparam 
   */ 
  function code_C($btparam, $index) { 
    $result = ""; 
    $local = count($this->head->terms->terms) + 4; 
    $this->pickVariables($vars, $local); 
    // check singleton variables 
    if (is_array($vars[PG_ENV_ISVAR])) { 
      foreach ($vars[PG_ENV_ISVAR] as $varname => $varcount) { 
        // if ((!$varname in ["_", "__", "___", ...]) && ($varcount == 1)) { ... 
        if ((!ereg("^_*\$", $varname)) && ($varcount == 1)) { 
          $this->parser->addWarning("Singleton variable \"" . $varname . "\"" 
          . " in clause \"" . $this->head->functor . "\".", false, false); 
        } 
      } 
    } 
    // label: pushenv n + r + 4 
    $label = $this->getClauseLabel($index, PG_LABEL_ENDING); 
    $envsize = count($this->head->terms->terms) + count($vars[PG_ENV_I]) + 4; 
    $result .= _pg_output_instruction(PG_OP_PUSHENV . " " . $envsize, $label); 
    // btinit btparam btnext 
    $btnext = $this->getClauseLabel($index + 1, ""); 
    switch ($btparam) { 
      case PG_BT_FIRST: 
        $result .= _pg_output_instruction(PG_OP_SETBTP . " " . $btnext); 
        break; 
      case PG_BT_MIDDLE: 
        $result .= _pg_output_instruction(PG_OP_NEXTALT . " " . $btnext); 
        break; 
      case PG_BT_LAST: 
        $result .= _pg_output_instruction(PG_OP_DELBTP); 
        break; 
      case PG_BT_SINGLE: 
        break; 
    } 
    // out head 
    if ($this->head->terms->terms) { 
      for ($i = 0; $i < count($this->head->terms->terms); $i++) { 
        $result .= _pg_output_instruction(PG_OP_PUSHARG . " " . ($i + 1)); 
        $result .= $this->head->terms->terms[$i]->code_U($vars, $local); 
      } 
    } 
    // out goals 
    if ($this->goals->goals) { 
      for ($i = 0; $i < count($this->goals->goals); $i++) { 
        $result .= $this->goals->goals[$i]->code_G($vars, $local); 
      } 
    } 
    // fin btparam 
    switch ($btparam) { 
      case PG_BT_LAST: 
      case PG_BT_SINGLE: 
        $result .= _pg_output_instruction(PG_OP_POPENV); 
        break; 
      case PG_BT_FIRST: 
      case PG_BT_MIDDLE: 
        $result .= _pg_output_instruction(PG_OP_RESTORE); 
        break; 
    } 
    return $result; 
  } // end of code_C 
   
} // end of class PrologClause 



class PrologPredicate extends PrologGenerator { 
   
  var $functor = ""; 
  var $terms = null; 
   
   
  function PrologPredicate(&$parser) { 
    $this->PrologGenerator($parser); 
    $this->functor = ""; 
    $this->terms = null; 
  } // end of PrologPredicate 
   
   
   
  function setFunctor(&$functor, &$terms) { 
    $this->functor = $functor; 
    $this->terms = &$terms; // 'null' is possible, but no failure! 
  } // end of setFunctor 
   
   
   
  function compareSignature(&$predicate) { 
    $result = false; 
    // check functor names und argument count 
    if ($predicate->functor == $this->functor) { 
      if (count($predicate->terms->terms) == count($this->terms->terms)) { 
        $result = true; 
      } else { 
        $this->parser->addError("The predicate" 
        . " \"" . $predicate->functor . "\"" 
        . " has bad signature (arguments).", false, false); 
        $result = false; 
      } 
    } 
    return $result; 
  } // end of compareSignature 
   
   
   
  function pickVariables(&$vars, $local) { 
    if ($this->terms) { 
      $this->terms->pickVariables($vars, $local); 
    } 
  } // end of pickVariables 
   
} // end of class PrologPredicate 



class PrologGoals extends PrologGenerator { 
   
  var $goals = null; 
   
   
  function PrologGoals(&$parser) { 
    $this->PrologGenerator($parser); 
    $this->goals = array(); 
  } // end of PrologGoals 
   
   
   
  function addGoal(&$goal) { 
    $this->goals[] = &$goal; 
  } // end of addGoal 
   
   
   
  function pickVariables(&$vars, $local) { 
    for ($i = 0; $i < count($this->goals); $i++) { 
      $this->goals[$i]->pickVariables($vars, $local); 
    } 
  } // end of pickVariables 
   
} // end of class PrologGoals 



class PrologGoal extends PrologGenerator { 
   
  var $type = ""; // cut | fail | predicate 
  var $predicate = null; // only needed, if type(goal) is predicate 
   
   
  function PrologGoal(&$parser) { 
    $this->PrologGenerator($parser); 
    $this->type = ""; 
    $this->predicate = null; 
  } // end of PrologGoal 
   
   
   
  function setCut() { 
    $this->type = PG_GOALTYPE_CUT; 
    $this->predicate = null; 
  } // end of setCut 
   
   
   
  function setFail() { 
    $this->type = PG_GOALTYPE_FAIL; 
    $this->predicate = null; 
  } // end of setFail 
   
   
   
  function setPredicate(&$predicate) { 
    $this->type = PG_GOALTYPE_PREDICATE; 
    $this->predicate = &$predicate; 
  } // end of setPredicate 
   
   
   
  function pickVariables(&$vars, $local) { 
    if ($this->predicate) { 
      $this->predicate->pickVariables($vars, $local); 
    } 
  } // end of pickVariables 
   
   
   
  /* code_G cut (v,l)          = cut 
   * code_G fail (v,l)         = fail 
   * code_G p(t1,...,tn) (v,l) = enter; 
   *                             code_A t1 (v,l); 
   *                             ... 
   *                             code_A tn (v,l); 
   *                             call p/n 
   */ 
  function code_G(&$vars, $local) { 
    $result = ""; 
    switch ($this->type) { 
      case PG_GOALTYPE_CUT: 
        $result = _pg_output_instruction(PG_OP_CUT); 
        break; 
      case PG_GOALTYPE_FAIL: 
        $result = _pg_output_instruction(PG_OP_FAIL); 
        break; 
      case PG_GOALTYPE_PREDICATE: 
        if ($this->predicate) { 
          $p = &$this->predicate; 
           
          $result = _pg_output_instruction(PG_OP_ENTER); // enter 
          if ($p->terms->terms) { 
            for ($i = 0; $i < count($p->terms->terms); $i++) { 
              $result .= $p->terms->terms[$i]->code_A($vars, $local); 
            } 
          } 
          $result .= _pg_output_instruction(PG_OP_CALL . " " . $p->functor 
                     . PG_SIG_SEP . count($p->terms->terms)); // call p/n 
        } 
        break; 
    } 
    return $result; 
  } // end of code_G 
   
} // end of class PrologGoal 



class PrologTerms extends PrologGenerator { 
   
  var $terms = null; 
   
   
  function PrologTerms(&$parser) { 
    $this->PrologGenerator($parser); 
    $this->terms = array(); 
  } // end of PrologTerms 
   
   
   
  function addTerm(&$term) { 
    $this->terms[] = &$term; // 'null' forbidden 
  } // end of addTerm 
   
   
   
  function pickVariables(&$vars, $local) { 
    for ($i = 0; $i < count($this->terms); $i++) { 
      $this->terms[$i]->pickVariables($vars, $local); 
    } 
  } // end of pickVariables 
   
} // end of class PrologTerms 



class PrologTerm extends PrologGenerator { 
   
  var $term = ""; 
  var $type = ""; // atom | variable | structure 
  var $terms = null; // only needed, if type(term) is a structure 
   
   
  function PrologTerm(&$parser) { 
    $this->PrologGenerator($parser); 
    $this->term = ""; 
    $this->type = ""; 
    $this->terms = null; 
  } // end of PrologTerm 
   
   
   
  function setAtom(&$atom) { 
    $this->term = $atom; 
    $this->type = PG_TERMTYPE_ATOM; 
    $this->terms = null; 
  } // end of setAtom 
   
   
   
  function setVariable(&$variable) { 
    $this->term = $variable; 
    $this->type = PG_TERMTYPE_VARIABLE; 
    $this->terms = null; 
  } // end of setVariable 
   
   
   
  function setStructure(&$structure, &$terms) { 
    $this->term = $structure; 
    $this->type = PG_TERMTYPE_STRUCTURE; 
    $this->terms = &$terms; // 'null' forbidden 
  } // end of setStructure 
   
   
   
  function pickVariables(&$vars, $local) { 
    if ($this->type == PG_TERMTYPE_VARIABLE) { 
      if (!$vars[PG_ENV_ISVAR][$this->term]) { 
        $vars[PG_ENV_I][$this->term] = $local + count($vars[PG_ENV_I]) + 1; 
      } 
      $vars[PG_ENV_ISVAR][$this->term]++; 
    } elseif ($this->type == PG_TERMTYPE_STRUCTURE) { 
      $this->terms->pickVariables($vars, $local); 
    } 
  } // end of pickVariables 
   
   
   
  /* code_A a (v,l)            = putatom a 
   * code_A X (v,l)            = putvar (v,l)(X) X 
   * code_A &X (v,l)           = putref (v,l)(X) 
   * code_A f(t1,...,tn) (v,l) = code_A t1 (v,l); 
   *                             ... 
   *                             code_A tn (v,l); 
   *                             putstruct f/n 
   */ 
  function code_A(&$vars, $local) { 
    $result = ""; 
    switch ($this->type) { 
      case PG_TERMTYPE_ATOM: 
        $result .= _pg_output_instruction(PG_OP_PUTATOM . " " . $this->term); 
        break; 
      case PG_TERMTYPE_VARIABLE: 
        if ($vars[PG_ENV_ISVAR][$this->term]) { 
          $result .= _pg_output_instruction(PG_OP_PUTVAR . " " 
                     . $vars[PG_ENV_I][$this->term] . " " . $this->term); 
        } else { 
          $result .= _pg_output_instruction(PG_OP_PUTREF . " " 
                     . $vars[PG_ENV_I][$this->term]); 
        } 
        $vars[PG_ENV_ISVAR][$this->term] = false; 
        break; 
      case PG_TERMTYPE_STRUCTURE: 
        for ($i = 0; $i < count($this->terms->terms); $i++) { 
          $result .= $this->terms->terms[$i]->code_A($vars, $local); 
        } 
        $result .= _pg_output_instruction(PG_OP_PUTSTRUCT . " " . $this->term 
                   . PG_SIG_SEP . count($this->terms->terms)); 
        break; 
    } 
    return $result; 
  } // end of code_A 
   
   
   
  /* code_U a (v,l)               = uatom a 
   * code_U X (v,l)               = uvar (v,l)(X) 
   * code_U &X (v,l)              = uref (v,l)(X) 
   * code_U f(t1,t2,...,tn) (v,l) = ustruct f/n; 
   *                                down; 
   *                                code_U t1 (v,l); 
   *                                brother 2; 
   *                                code_U t2 (v,l); 
   *                                ... 
   *                                brother n; 
   *                                code_U tn (v,l); 
   *                                up 
   */ 
  function code_U(&$vars, $local) { 
    $result = ""; 
    switch ($this->type) { 
      case PG_TERMTYPE_ATOM: 
        $result .= _pg_output_instruction(PG_OP_UATOM . " " . $this->term); 
        break; 
      case PG_TERMTYPE_VARIABLE: 
        if ($vars[PG_ENV_ISVAR][$this->term]) { 
          $result .= _pg_output_instruction(PG_OP_UVAR . " " 
                     . $vars[PG_ENV_I][$this->term]); 
        } else { 
          $result .= _pg_output_instruction(PG_OP_UREF . " " 
                     . $vars[PG_ENV_I][$this->term]); 
        } 
        $vars[PG_ENV_ISVAR][$this->term] = false; 
        break; 
      case PG_TERMTYPE_STRUCTURE: 
        $result .= _pg_output_instruction(PG_OP_USTRUCT . " " . $this->term 
                                   . PG_SIG_SEP . count($this->terms->terms)); 
        $result .= _pg_output_instruction(PG_OP_DOWN); 
        $result .= $this->terms->terms[0]->code_U($vars, $local); 
        for ($i = 1; $i < count($this->terms->terms); $i++) { 
          $result .= _pg_output_instruction(PG_OP_BROTHER . " " . ($i + 1)); 
          $result .= $this->terms->terms[$i]->code_U($vars, $local); 
        } 
        $result .= _pg_output_instruction(PG_OP_UP); 
        break; 
    } 
    return $result; 
  } // end of code_U 
   
} // end of class PrologTerm 



class PrologQuery extends PrologGenerator { 
   
  var $goals = null; 
   
   
  function PrologQuery(&$parser) { 
    $this->PrologGenerator($parser); 
    $this->goals = null; 
  } // end of PrologQuery 
   
   
   
  function addGoals(&$goals) { 
    $this->goals = &$goals; // 'null' forbidden 
  } // end of addGoals 
   
   
   
  /* code_Q g1,...,gn = init; 
   *                    pushenv r + 4; 
   *                    code_G g1 (v,l); 
   *                    ... 
   *                    code_G gn (v,l); 
   *                    halt 
   */ 
  function code_Q() { 
    $result = _pg_output_instruction(PG_OP_INIT); 
    $local = 4; 
    $this->goals->pickVariables($vars, $local); 
    $result .= _pg_output_instruction(PG_OP_PUSHENV . " " 
               . (count($vars[PG_ENV_I]) + 4)); 
    for ($i = 0; $i < count($this->goals->goals); $i++) { 
      $result .= $this->goals->goals[$i]->code_G($vars, $local); 
    } 
    $result .= _pg_output_instruction(PG_OP_HALT); 
    return $result; 
  } // end of code_Q 
   
} // end of class PrologQuery 



/* Global part for formatted output of WiM-Code (best indent). */ 

$_pg_output_indent = 0; // update in PrologProcedure 

function _pg_output_init() { 
  global $_pg_output_indent; 
  $_pg_output_indent = 0; 
} 

function _pg_output_update($label) { 
  global $_pg_output_indent; 
  $_pg_output_indent = max($_pg_output_indent, strlen($label)); 
} 

function _pg_output_instruction($string, $label = "") { 
  $result = ""; 
  if ($label) { 
    $result .= PG_INSTRUCTION_ENDING; 
  } 
  global $_pg_output_indent; 
  $result .= str_pad($label, $_pg_output_indent, " ") . $string; 
  return $result . PG_INSTRUCTION_ENDING; 
} 

?>
