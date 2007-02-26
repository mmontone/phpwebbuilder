<?
class PersistentCollection extends Report{
	/**
	 * A collection of persisted objects (of the same class)
	 */
	function PersistentCollection($dataType = "") {
		#@check Compiler::requiredClass($dataType)@#
		parent::Report();
		if ($dataType!=''){
			$this->setDataType($dataType);
		}
	}

	function setCondition($field, $comparator, $value){
		$e1=&new ValueExpression('`' . $this->parseField($field) . '`');
		$e2=&new ValueExpression($value);
		$cond =& new Condition(array('exp1'=> &$e1,
				'operation'=>$comparator,
				'exp2' => &$e2));
		$cond->evaluateIn($this);

		$this->select_exp->addExpressionUnique($this->parseField($field), $cond);
		$n = null;
		$this->elements=& $n;
	}

	/**
	 * finds all similar objects (objects with same atributes set in same values)
	 * Returns a PersistentCollection
	 */
	function &findMatches(&$object) {
		#@typecheck $object:PersistentObject@#
		$col =& $object->findMatches();
		$this->conditions = $col->conditions;
		return $this;
	}
	/**
	  * Returns the tables to be used
	  */
	/*
	function tableNames() {
		$datatype =& $this->getDataType();
		$obj = new $datatype;
		return $obj->tableNames();
	}*/

	function getTables() {
		if ($this->getDataType()!='PersistentObject'){
			$datatype = $this->getDataType();
			$obj = new $datatype(array(),false);
			return $obj->getTables();
		} else {
			parent::getTables();
		}

	}
	/**
	  * Returns the tables of the base class of the elements of the collection
	  */
	function tableName() {
		$datatype =& $this->getDataType();
		$obj = new $datatype(array(),false);
		return $obj->tableName();
	}
	/**
	  * Returns the object of the collection with the id
	  */
	function & getObj($id) {
		return PersistentObject::getWithId($this->getDataType(), $id);
	}
	/**
	  * Creates an element, and fills it from the record
	  */
	function &fillExtras($obj,$data){
	 	return $obj;
	}

}

class CompositePersistentCollection extends CompositeReport {}

?>