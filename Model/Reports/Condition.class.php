<?php

function unify_types($t1, $t2){
	$a1=get_superclasses($t1);
	$a2=get_superclasses($t2);
	array_unshift($a1,$t1);
	array_unshift($a2,$t2);
	$arr = array_intersect($a1,$a2);
	if (count($arr)==0){
		print_backtrace_and_exit('Type error: '.$t1.' and '.$t2);
	} else {
		$type = array_shift($arr);
		//print_backtrace("$t1 and $t2 give $type");
		return $type;
	}
}

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
    function getExpressionType(&$report){
    	$t1 = $this->exp1->getExpressionType($report);
		$t2 = $this->exp2->getExpressionType($report);
		if($t1==$t2) return $t2;
		if($t1==null) $nt = $t2;
		else if($t2==null) $nt = $t1;
		else $nt = unify_types($t1, $t2);
		$this->exp2->setType($nt);
		$this->exp1->setType($nt);
		return $nt;
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
	function getExpressionType(){return null;}
	function setType($type){$this->type = $type;}
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
		$this->type = $class;
		parent::Expression();
	}

	function evaluateIn(&$report) {
		if ($this->type===null) {
			return $this->object->getId();
		} else {
			return $this->object->getIdOfClass($this->type);
		}
	}
}

class PathExpression extends Expression {
	var $path;

	function PathExpression($path) {
		$this->path = $path;
		parent::Expression();
	}

	function &getTargetVar(&$report) {
		$pp = explode('.', $this->path);
        $target = $pp[0];


        $target_var =& $report->getVar($target);
        if (!is_object($target_var)) {
            $target_var =& $report->getTargetVar();
        }
        else {
        	array_shift($pp);
        }
		$arr = array(&$target_var, $pp);
        return $arr;
	}

    function &registerPath(&$report) {
		$result =& $this->getTargetVar($report);
        $target_var =& $result[0];

        $pp = $result[1];
        $datatype = $target_var->class;
        $prefix = $target_var->prefix;
        $pre = substr($prefix,0);

		$o =& new $datatype(array(),false);

		foreach ($pp as $index) {
            #@gencheck
            if (!is_object($o->$index)) {
                print_backtrace_and_exit('The field ' . $index . ' does not exists in ' . $datatype . '(' . getClass($this) . ' with path: ' . $this->path . ')');
            }
            @#
            $class =& $o->$index->getDataType();
			$obj =& new $class(array(),false);

            $otable = $o->tableForFieldPrefixed($index, $pre);

            $pre .= $index ;

            //$report->addTables($obj->getTablesPrefixed($pre));
            $var = $report->getVar($pre);
            if ($var==null){
            	$report->defineVar($pre,$class);
	            $exp =& new EqualCondition(array('exp1' => new ValueExpression('`' . $otable. '`.`' . $index . '`'),
	                                             'exp2' => new ValueExpression('`' . $obj->getTablePrefixed($pre.'_') .'`.`id`')));

	            $exp->evaluateIn($report);
	            $this->parent->addEvalExpression($exp);
            }
			$pre .= '_';
			$o =& $obj;
		}
		$arr = array(&$o, $pre);
		return $arr;
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
        $result =& $this->getTargetVar($report);
        $target_var =& $result[0];
        $otable = $o[0]->tableForFieldPrefixed($this->attr, $o[1]);
		$attr = $otable . '.' . $this->attr;
		return '`' . str_replace('.','`.`',$attr) . '`';
	}
}

class ObjectPathExpression extends PathExpression {
	var $type;

	function ObjectPathExpression($path, $type='') {
		$this->type = $type;
		parent::PathExpression($path);
	}
	function evaluateIn(&$report) {
		$ret =& $this->registerPath($report);
		$o =& $ret[0];
		$type = $this->parent->getExpressionType($report);
		if ($type!=''){
			$o =& new $type(array(),false);
		}
		$attr = '`'.$o->getTablePrefixed($ret[1]) .'`.`id`';
        return $attr;
	}
	function getExpressionType(&$report){
		if ($this->type==''){
			$rp =& $this->registerPath($report);
			$this->type = getClass($rp[0]);
		}
		return $this->type;
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

            $and->addExpression(new EqualCondition(array('exp1' => new AttrPathExpression($this->path . '.' . $this->collection_field,'target_'),
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
		$this->query->parent =& $report;
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
		$this->query->parent =& $report;
    }

    function printString() {
        return $this->field . ' IN (' . $this->query->inSQL() . ')';
    }
}
?>