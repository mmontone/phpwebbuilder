<?
class PersistentCollection extends Report{
	/**
	 * A collection of persisted objects (of the same class)
	 */
	function PersistentCollection($dataType = "") {
		$this->setDataType($dataType);
		parent::Collection();
	}

	function setCondition($field, $comparator, $value){
		//echo 'Persistent collection: Setting condition ' . $this->parseField($field) . $comparator . $value . '<br />';
		$this->conditions[$this->parseField($field)]=array($this->parseField($field),$comparator,$value);
		$n = null;
		$this->elements=& $n;
	}

	/**
	 * finds all similar objects (objects with same atributes set in same values)
	 * Returns a PersistentCollection
	 */
	function &findMatches(&$object) {
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
		$datatype =& $this->getDataType();
		$obj = new $datatype;
		return $obj->getTables();
	}
	/**
	  * Returns the tables of the base class of the elements of the collection
	  */
	function tableName() {
		$datatype =& $this->getDataType();
		$obj = new $datatype;
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
	function &makeElement($data){
		$dt = $this->getDataType();
		$obj =& new $dt;
		return $obj->loadFromRec($data);
	}
}

class CompositePersistentCollection extends CompositeReport {}

?>