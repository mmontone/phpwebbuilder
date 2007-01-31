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
		$this->exp2 =& $params['exp2'];
		$this->operation = $params['operation'];
	}

	function evaluateIn(&$report) {
		$this->evaluated_e1 = $this->exp1->evaluateIn($report);
		$this->evaluated_e2 = $this->exp2->evaluateIn($report);
	}

	function printString() {
		if ($this->evaluated_e1 == null) print_backtrace_and_exit(print_r($this, true));
		return $this->evaluated_e1 . ' ' . $this->operation . ' ' . $this->evaluated_e2;
	}
}

class EqualCondition extends Condition {
	function EqualCondition($params) {
		$params['operation'] = '=';
		parent::Condition($params);
	}
}

class Expression {
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
        parent::Expression();
    }

    function evaluateIn(&$report) {

    }

    function printString() {
    	return 'NOT (' . $this->exp->printString() . ')';
    }
}

class AndExp extends Expression {
	var $exps;

	function AndExp() {
		$c =& new Condition(array('exp1' => new ValueExpression(1), 'operation' => '=', 'exp2' => new ValueExpression(1)));
		$n = null;
		$c->evaluateIn($n);
		$this->exps = array($c);

		parent::Expression();
	}
	function addExpression(&$exp) {
		$this->exps[] =& $exp;
	}

	function addExpressionUnique($index, &$exp) {
		$this->exps[$index] =& $exp;
	}

	function evaluateIn(&$report) {
		foreach(array_keys($this->exps) as $e) {
			$exp =& $this->exps[$e];
			$exp->evaluateIn($report);
		}
	}

	function printString() {
		$printed = array();
		foreach(array_keys($this->exps) as $e) {
			$exp =& $this->exps[$e];
			$printed[] = '(' . $exp->printString() . ')';
		}

		return implode(' AND ', $printed);
	}

	function isEmpty() {
		return empty($this->exps);
	}
}

class OrExp extends Expression {
	var $exps;

	function OrExp() {
		$c =& new Condition(array('exp1' => new ValueExpression(1), 'operation' => '=', 'exp2' => new ValueExpression(2)));
		$c->evaluateIn($n = null);
		$this->exps = array($c);

		parent::Expression();
	}
	function addExpression(&$exp) {
		$this->exps[] =& $exp;
	}
	function evaluateIn(&$report) {
		foreach(array_keys($this->exps) as $e) {
			$exp =& $this->exps[$e];
			$exp->evaluateIn($report);
		}
	}

	function printString() {
		$printed = array();
		foreach(array_keys($this->exps) as $e) {
			$exp =& $this->exps[$e];
			$printed[] = '(' . $exp->printString() . ')';
		}

		return implode(' OR ', $printed);
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
			$report->setCondition($otable. '.' . $index, '=', '`'.$obj->getTable() .'`'. '.id');
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

class ObjectPathExpression extends PathExpression {
	var $type;

	function ObjectPathExpression($path, $type='') {
		$this->type = $type;
		parent::PathExpression($path);
	}
	function evaluateIn(&$report) {
		$o =& $this->registerPath($report);
		$attr = $o->getTable() . '.id';
		return $attr;
	}
}

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

	}

	function printString() {
		return 'EXISTS (' . $this->query->selectsql() . ')';
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
        return $this->field . ' IN (' . $this->query->selectsql() . ')';
    }
}
?>