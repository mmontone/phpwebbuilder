<?php
class Condition extends Expression {
	var $exp1;
	var $operation;
	var $exp2;
	var $evaluated_e1;
	var $evaluated_e2;

	function Condition($params=array()) {
		#@typecheck $params['operation']:String,$params['exp2']:Expression,$params['exp2']:Expression@#
		$this->exp1 =& $params['exp1'];
        $this->exp1->parent =& $this;
		$this->exp2 =& $params['exp2'];
        $this->exp2->parent =& $this;
		$this->operation = $params['operation'];
	}

    function setExp1(&$exp) {
    	$this->exp1 =& $exp;
        $this->exp1->parent =& $this;
    }

    function setExp2(&$exp) {
        $this->exp2 =& $exp;
        $this->exp2->parent =& $this;
    }

    function setOperation($operation) {
    	$this->operation = $operation;
    }

	function evaluateIn(&$report) {
		$this->evaluated_e1 = $this->exp1->evaluateIn($report);
		$this->evaluated_e2 = $this->exp2->evaluateIn($report);
	}

	function printString() {
		#@gencheck
        if ($this->evaluated_e1 == null) {
			print_backtrace($this->evaluated_e1 . ' ' . $this->operation . ' ' . $this->evaluated_e2 . ' has not been evaluated');
		}//@#
		return $this->evaluated_e1 . ' ' . $this->operation . ' ' . $this->evaluated_e2;
	}

    function addEvalExpression(&$exp) {
        $this->parent->addEvalExpression($exp);
    }
}

class EqualCondition extends Condition {
	function EqualCondition($params) {
		$params['operation'] = '=';
		parent::Condition($params);
	}
}

class Expression {
	var $parent;

    function Expression() {

	}
	function evaluateIn(&$report) {
		print_backtrace_and_exit('Subclass responsibility');
	}

	function printString() {
		print_backtrace_and_exit('Subclass responsibility');
	}
}

class NotExp extends Expression {
	var $exp;

    function NotExp(&$exp) {
    	$this->exp =& $exp;
        $this->exp->parent =& $this;
        parent::Expression();
    }

    function isEmpty() {
    	return false;
    }

    function evaluateIn(&$report) {
        return $this->exp->evaluateIn($report);
    }

    function printString() {
    	return 'NOT (' . $this->exp->printString() . ')';
    }
}

class AndExp extends Expression {
	var $exps;

	function AndExp($params=array(), $evaluate=true) {
		$this->exps = array();
        if ($evaluate) {
            $this->addExpression(new AndExp(array(), false));
        }

		foreach(array_keys($params) as $k) {
			$this->addExpression($params[$k]);
		}
		parent::Expression();
	}
	function addExpression(&$exp) {
		$this->exps[] =& $exp;
        $exp->parent =& $this;
	}

    function addEvalExpression(&$exp) {
        $evaluated_exp =& $this->exps[0];

        $evaluated_exp->addExpression($exp);
    }

    function cleanEvalExpressions() {
        $this->exps[0] =& new AndExp(array(), false);
    }

	function addExpressionUnique($index, &$exp) {
		$this->exps[$index] =& $exp;
        $exp->parent =& $this;
	}

	function evaluateIn(&$report) {
		foreach(array_keys($this->exps) as $e) {
			$this->exps[$e]->evaluateIn($report);
		}
    }

	function printString() {
	  $printed = array();

      foreach(array_keys($this->exps) as $e) {
	    $p = $this->exps[$e]->printString();
	    if ($p !== '') {
	      $printed[] = $p;
	    }
	  }

	  if (empty($printed)) {
	    return '';
	  }
	  if (count($printed) == 1) {
	    return $printed[0];
	  }

      return '(' . implode(') AND (', $printed) . ')';
	}

	function isEmpty() {
		return empty($this->exps);
	}
}

class OrExp extends Expression {
	var $exps;
    var $eval_exp = null;

	function OrExp($params=array()) {
		$this->exps = array();

        foreach(array_keys($params) as $k) {
			$this->addExpression($params[$k]);
		}

		parent::Expression();
	}
	function addExpression(&$exp) {
		$this->exps[] =& $exp;
        $exp->parent =& $this;
	}

    function addEvalExpression(&$exp) {
        if ($this->eval_exp == null) {
        	$this->eval_exp =& new AndExp;
        }

        $this->eval_exp->addExpression($exp);
    }

    function cleanEvalExpressions() {
        $n = null;
        $this->eval_exp =& $n;
    }

	function evaluateIn(&$report) {
		foreach(array_keys($this->exps) as $e) {
			$this->exps[$e]->evaluateIn($report);
		}
	}

	function printString() {
	  if ($this->eval_exp != null) {
	  	$s = $this->evalPrintString();
	  }
      else {
      	$s = $this->primPrintString();
      }

      return $s;
	}

    function primPrintString() {
      $printed = array();
      foreach(array_keys($this->exps) as $e) {
        $p = $this->exps[$e]->printString();
        if ($p !== '') {
          $printed[] = $p;
        }
      }

      if (empty($printed)) {
        return '';
      }
      if (count($printed) == 1) {
        return '';
      }

      return '(' . implode(') OR (', $printed) . ')';
    }

    function evalPrintString() {
    	$exp =& new AndExp;
        $exp->addExpression($this->eval_exp);
        $n = null;
        $this->eval_exp =& $n;
        $exp->addExpression($this);
        return $exp->printString();
    }


	function isEmpty() {
		return empty($this->exps);
	}
}

class ValueExpression extends Expression {
	var $value;

	function ValueExpression($value) {
		$this->value = $value;
		parent::Expression();
	}
	function evaluateIn(&$report) {
		return $this->value;
	}

	function printString() {
		return $this->value;
	}
}

class ObjectExpression extends Expression {
	var $object;

	function ObjectExpression(&$object,$class=null) {
		$this->object =& $object;
		$this->class = $class;
		parent::Expression();
	}

	function evaluateIn(&$report) {
		if ($this->class===null) {
			return $this->object->getId();
		} else {
			return $this->object->getIdOfClass($this->class);
		}
	}
}

class PathExpression extends Expression {
	var $path;

	function PathExpression($path) {
		$this->path = $path;
		parent::Expression();
	}

	function &registerPath(&$report) {
		$pp = explode('.', $this->path);
		$target = $pp[0];
		if (!isset($report->vars[$target])) {
			//print_backtrace($target . ' not defined');
			$datatype = $report->getDataType();
		}
		else {
			$datatype = $report->vars[$target];
			array_shift($pp);
		}

		$o =& new $datatype(array(),false);

		foreach ($pp as $index) {
            #@gencheck
            if (!is_object($o->$index)) {
                print_backtrace_and_exit('The field ' . $index . ' does not exists in ' . $datatype);
            }
            @#
            $class =& $o->$index->getDataType();
			$obj =& new $class(array(),false);

            $report->addTables($obj->getTables());
			$otable = $o->tableForField($index);
			//$report->setCondition($otable. '.' . $index, '=', '`'.$obj->getTable() .'`'. '.id');

            $exp =& new EqualCondition(array('exp1' => new ValueExpression('`' . $otable. '`.`' . $index . '`'),
                                             'exp2' => new ValueExpression('`' . $obj->getTable() .'`'. '.id')));

            $exp->evaluateIn($report);
            $this->parent->addEvalExpression($exp);


			$o =& $obj;
		}
		return $o;
	}
}

class AttrPathExpression extends PathExpression {
	var $attr;

	function AttrPathExpression($path, $attr) {
		$this->attr = $attr;
		parent::PathExpression($path);
	}

	function evaluateIn(&$report) {
		$o =& $this->registerPath($report);
		$otable = $o->tableForField($this->attr);
		$attr = $otable . '.' . $this->attr;
		return '`' . str_replace('.','`.`',$attr) . '`';
	}
}

class ObjectPathExpression extends AttrPathExpression {
	var $type;

	function ObjectPathExpression($path, $type='') {
		$this->type = $type;
		$path = explode('.',$path);
		if (count($path)>1) $att = array_pop($path);
		parent::AttrPathExpression(implode('.',$path), $att);
	}
	function evaluateIn(&$report) {
		if ($this->attr!=''){
			return parent::evaluateIn($report);
		}
		$o =& $this->registerPath($report);
		$type = $this->type;
		if ($type!=''){
			$o =& new $type(array(),false);
		}
		$attr = $o->getTable() . '.id';
		return $attr;
	}
}


/** Finds a collection, and defines $var to access it*/
class CollectionPathExpression extends PathExpression {
    var $collection_field;
    var $var;
    var $exp;

    function CollectionPathExpression($path, $collection_field, $var) {
        $this->collection_field = $collection_field;
        $this->var = $var;
        parent::PathExpression($path);
    }

    function evaluateIn(&$report) {
        $o =& $this->registerPath($report);

        $field = $this->collection_field;
        $col_field =& $o->$field;

        $type = $col_field->collection->getDataType();

        if ($col_field->direct) {
            $this->defineVar($this->var, $type);
        }
        else {
            $link_obj =& new $type;
            $table = $link_obj->getTable();
            $target_field = $link_obj->target;

            $target_obj =& new $target_field->datatype;
            $and =& new AndExp;

            $and->addExpression(new EqualCondition(array('exp1' => new AttrPathExpression($this->path . '.' . $this->collection_field,'target'),
                                                         'exp2' => new ValueExpression("`" . $target_obj->getTable() . '`.`id`'))));

            $and->addExpression(new EqualCondition(array('exp1' => new AttrPathExpression($this->path . '.' . $this->collection_field, 'owner'),
                                                         'exp2' => new ValueExpression("`". $o->getTable() . '`.`id`'))));
            $this->exp =& $and;

            $report->defineVar($this->var, $target_field->datatype);

            $this->exp->evaluateIn($report);
        }
    }

    function printString() {
        return $this->exp->printString();
    }
}

class ExistsExpression extends Expression {
	var $query;

	function ExistsExpression(&$query) {
		$this->query =& $query;
		parent::Expression();
	}

	function evaluateIn(&$report) {
		$report->registerAllVars($this->query);
	}

	function printString() {
		return 'EXISTS (' . $this->query->inSQL() . ')';
	}

    function isEmpty() {
    	return false;
    }
}

class InExpression extends Expression {
	var $query;
    var $field;

    function InExpression($field, &$query) {
        $this->query =& $query;
        $this->field = $field;
        parent::Expression();
    }

    function evaluateIn(&$report) {

    }

    function printString() {
        return $this->field . ' IN (' . $this->query->inSQL() . ')';
    }
}
?>