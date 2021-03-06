<?php

function unify_types($t1, $t2){
	$t1 = strtolower($t1);
    $t2 = strtolower($t2);
    $a1=get_superclasses($t1);
	$a2=get_superclasses($t2);
	array_unshift($a1,$t1);
	array_unshift($a2,$t2);
	$arr = array_intersect($a1,$a2);
	if (count($arr)==0){
		print_backtrace_and_exit('Type error: '.$t1.' and '.$t2);
	} else {
		$type = array_shift($arr);
		//echo "Unifying $t1 and $t2 give $type<br/>";
		return $type;
	}
}

class Condition extends Expression {
	var $exp1;
	var $operation;
	var $exp2;
	var $evaluated_e1;
	var $evaluated_e2;
    var $eval_exps = array();

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
		$this->exp1->setParent($this);
		$this->exp2->setParent($this);
		$this->evaluated_e1 = $this->exp1->evaluateIn($report);
		$this->evaluated_e2 = $this->exp2->evaluateIn($report);
	}

	function printString() {
		#@gencheck
        if ($this->evaluated_e1 == null) {
			print_backtrace($this->evaluated_e1 . ' ' . $this->operation . ' ' . $this->evaluated_e2 . ' has not been evaluated');
		}//@#

        if (!empty($this->eval_exps)) {
        	$and =& new AndExp;
            foreach (array_keys($this->eval_exps) as $e) {
                $and->addExpression($this->eval_exps[$e]);
            }

            $me =& new Condition(array('exp1'=>$this->exp1, 'exp2'=>$this->exp2, 'operation'=>$this->operation));
            $me->evaluated_e1 =& $this->evaluated_e1;
            $me->evaluated_e2 =& $this->evaluated_e2;

            $and->addExpression($me);

            return $and->printString();

        }
        else {
		    return $this->evaluated_e1 . ' ' . $this->operation . ' ' . $this->evaluated_e2;
        }
	}

    function addEvalExpression(&$exp) {
        $this->eval_exps[] =& $exp;
    }

    function getExpressionType(&$report){
    	$t1 = $this->exp1->getExpressionType($report);
		$t2 = $this->exp2->getExpressionType($report);
		//var_dump("Unifying $t1 and $t2");
		if($t1==$t2) return $t2;
		if($t1==null) $nt = $t2;
		else if($t2==null) $nt = $t1;
		else $nt = unify_types($t1, $t2);
		$this->exp2->setType($nt);
		$this->exp1->setType($nt);
		return $nt;
    }
}

class SQLCondition extends Condition {
    var $sql;

    function SQLCondition($sql) {
    	$this->sql = $sql;
    }

    function evaluateIn(&$report) {

    }

    function printString() {
    	return $this->sql;
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
	var $type;
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
	function setParent(&$parent){
    	$this->parent =& $parent;
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

	function AndExp($params=array()) {
		$this->exps = array();

		foreach(array_keys($params) as $k) {
			$this->addExpression($params[$k]);
		}

		parent::Expression();
	}
	function addExpression(&$exp) {
		$this->exps[] =& $exp;
	}

    function addExpressionUnique($index, &$exp) {
		$this->exps[$index] =& $exp;
        $exp->parent =& $this;
	}

	function evaluateIn(&$report) {
		foreach(array_keys($this->exps) as $e) {
			$this->exps[$e]->setParent($this);
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

    function evaluateIn(&$report) {
		foreach(array_keys($this->exps) as $e) {
			$this->exps[$e]->setParent($this);
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
        return '';
      }

      return '(' . implode(') OR (', $printed) . ')';
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

class AggregateExpression extends Expression{
	function AggregateExpression(&$query){
		$this->query =& $query;
		parent::Expression();
	}

	function evaluateIn(&$report) {
		$this->query->parent =& $report;
		return $this->printString();
	}

	function printString() {
		return ' (' . $this->query->selectSQL() . ')';
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
        if ($target == '') {
            $t =& $report->getTargetVar();
            $arr = array(&$t, array());
            return $arr;
        }

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
        $datatype=$class = $target_var->class;
        $prefix = $target_var->prefix;
        $pre = $prefix;
		$o =& PersistentObjectMetaData::getMetaData($class);

		$keys = array_keys($pp);
        $lastkey = @$keys[count($pp) - 1];

        foreach ($pp as $index) {
            #@gencheck
            if ($o->getDataType($index)===null) {
                print_backtrace_and_exit('The field ' . $index . ' does not exists in ' . $class . '(' . getClass($this) . ' with path: ' . $this->path . ')');
            }
            //@#


            if ($index == $lastkey and $this->type != '') {
                $class = $this->type;
            }
            else {
                $class =& $o->getDataType($index);
            }

			$obj =& PersistentObjectMetaData::getMetaData($class);

            $target_obj =& PersistentObjectMetaData::getMetaData($o->getDataType($index));

            $otable = $o->tableForFieldPrefixed($index, $pre);

            $pre .= $index ;

            //$report->addTables($obj->getTablesPrefixed($pre));
            $var = $report->getVar($pre);
            if ($var==null){
            	$report->defineVar($pre, $class);
	            $exp =& new EqualCondition(array('exp1' => new ValueExpression('`' . $otable. '`.`' . $o->getColName($index) . '`'),
	                                             'exp2' => new ValueExpression('`' . $target_obj->getTablePrefixed($pre.'_') .'`.`id`')));

	            $exp->evaluateIn($report);
	            $this->parent->addEvalExpression($exp);
            }
			$pre .= '_';
			$o =& $obj;
		}

        if ($this->type == '') {
            $this->type = $class;
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
		$attr = $otable . '.' . $o[0]->getColName($this->attr);
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
		$this->ret =& $ret;
		$type = $this->parent->getExpressionType($report);
		if ($type!=''){
			$o =& PersistentObjectMetaData::getMetaData($type);
		}
		$attr = '`'.$o->getTablePrefixed($ret[1]) .'`.`id`';
        return $attr;
	}
	function getExpressionType(&$report){
		return $this->type?$this->type:getClass($this->ret[0]);
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
        $metadata_and_prefix = $this->registerPath($report);
        $metadata =& $metadata_and_prefix[0];
        $prefix =& $metadata_and_prefix[1];

        $colfield =& $metadata->getFieldNamed($this->collection_field);

        if ($colfield->isDirect()) {
        	return $this->evaluateDirectCollectionIn($colfield, $report, $metadata, $prefix);
        }
        else {
        	return $this->evaluateIndirectCollectionIn($colfield, $report, $metadata, $prefix);
        }
    }

    function evaluateDirectCollectionIn(&$colfield, &$report, &$metadata, $prefix) {
        $type = $colfield->getDataType();
        $report->defineVar($this->var, $type);

        $and =& new AndExp;

        $and->addExpression(new EqualCondition(array('exp1' => new AttrPathExpression($this->var, $colfield->getReverseField()),
                                                     'exp2' => new ValueExpression('`' . $metadata->getTablePrefixed($prefix) . '`.`id`'))));

        $this->exp =& $and;

        $this->exp->evaluateIn($report);
    }

    function evaluateIndirectCollectionIn(&$colfield, &$report, &$metadata, $prefix) {
        $joinVar = $prefix . $colfield->getName();

        $report->defineVar($joinVar, $colfield->getJoinDataType());
        $report->defineVar($this->var, $colfield->getDataType());

        $joinType = $colfield->getJoinDataType();

        $join_metadata =& PersistentObjectMetaData::getMetaData($joinType);

        $join_table = $join_metadata->getTablePrefixed($prefix);

        $target_field =& $join_metadata->getFieldNamed($colfield->getTargetField());

        $and =& new AndExp;

        $and->addExpression(new EqualCondition(array('exp1' => new AttrPathExpression($joinVar, $colfield->getReverseField()),
                                                     'exp2' => new ValueExpression('`' . $metadata->getTablePrefixed($prefix) . '`.`id`'))));

        $and->addExpression(new EqualCondition(array('exp1' => new AttrPathExpression($joinVar, $colfield->getTargetField()),
                                                     'exp2' => new ObjectPathExpression($this->var))));
        $this->exp =& $and;

        $this->exp->evaluateIn($report);
    }

    function setTargetVar(&$report) {
        $metadata_and_prefix = $this->registerPath($report);
        $metadata =& $metadata_and_prefix[0];
        $prefix =& $metadata_and_prefix[1];

        $colfield =& $metadata->getFieldNamed($this->collection_field);

        $report->setTargetVar($this->var, $colfield->getDataType());
    }

    function printString() {
        return $this->exp->printString();
    }
}

class ExistsExpression extends Expression {
	var $query;

	function ExistsExpression(&$query) {
		#@typecheck $query:SubReport@#
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
    var $attr;

    function InExpression($field, &$query) {
        $this->query =& $query;
        $this->field =& new ObjectPathExpression($field);
        $this->field->parent =& $this;

        parent::Expression();
    }

    function getExpressionType() {
        return '';
    }

    function evaluateIn(&$report) {
		$this->query->parent =& $report;
        $this->attr = $this->field->evaluateIn($report);
    }

    function printString() {
        return $this->attr . ' IN (' . $this->query->inSQL() . ')';
    }
}
?>