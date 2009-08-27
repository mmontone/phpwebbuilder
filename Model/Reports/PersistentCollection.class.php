<?
class PersistentCollection extends Report{
	/**
	 * A collection of persisted objects (of the same class)
	 */
	function PersistentCollection($dataType = "", $options=array()) {
		#@check Compiler::requiredClass($dataType)@#
		parent::Report($options);
		$this->setDataType($dataType);
	}
	function listen($class, &$listener){
		$class = strtolower($class);
		#@model_events_echo echo '<br/>'.$listener->printString().' listens to '.$class;@#
		if (@$_SESSION['PersistentCollectionListener'][$class]===null){
			$_SESSION['PersistentCollectionListener'][$class] =& new PWBObject();
		}
		$_SESSION['PersistentCollectionListener'][$class]->addInterestIn('change', $listener);
	}
	function changedClass($class){
		$class = strtolower($class);
		#@model_events_echo print_backtrace('triggering for '.$class);@#
		if (@$_SESSION['PersistentCollectionListener'][$class]===null){
			$_SESSION['PersistentCollectionListener'][$class] =& new PWBObject();
		}
		$_SESSION['PersistentCollectionListener'][$class]->triggerEvent('change', $n=null);
	}

	function addSetConditionExpression($field, &$exp){
		$this->select_exp->addExpressionUnique($this->parseField($field), $exp);
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
		$datatype = $this->getDataType();
		$obj =& PersistentObject::getMetaData($datatype);
		return $obj->getTables();
	}
	/**
	  * Returns the tables of the base class of the elements of the collection
	  */
	function tableName() {
		$datatype =& $this->getDataType();
		$obj =& PersistentObject::getMetaData($datatype);
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