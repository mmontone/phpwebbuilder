<?php

class JoinedPersistentCollection extends PersistentCollection{
	function JoinedPersistentCollection($dataType, $table, $field){
		parent::PersistentCollection ($dataType);
		$this->innerTable = $table;
		$this->innerField = $field;
		//$this->setCondition($table.'.'.$field, '=',$this->tableName().'.id');

        $e1 =& new ValueExpression($table.'.'.$field);
        $e2 =& new ValueExpression($this->tableName().'.id');
        $cond =& new Condition(array('exp1'=> &$e1,
                                     'operation'=>'=',
                                     'exp2' => &$e2));
        $cond->evaluateIn($this);

        $this->select_exp->addExpressionUnique($this->parseField($field), $cond);
        $n = null;
        $this->elements=& $n;
	}
	function tableNames() {
		$obj = new $this->dataType;
		return $obj->tableNames(). ', '.$this->innerTable;
	}
}
?>